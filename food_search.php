<?php

/**
 * Copyright (c) 2007 Nathan Kinkade
 * 
 * This code is offered under an MIT (X11) license.  For more information
 * about the terms of this license see the file LICENSE included with this
 * software or visit: http://www.opensource.org/licenses/mit-license.php
 */

# include the main site config where various global variables
# and libraries are included
require("config.php");

# Don't go any farther if the user didn't enter any search string
# as such a query could return thousands and thousands of records
# and be more or less useless and a resource killer ... there is already
# javascript validation on this field, but this is here as a safety net
$searchString = trim($_REQUEST['searchString']);
if ( empty($searchString) ) {
	print_r($_REQUEST); exit;
	$_SESSION['systemMsg'] = "<span class='msgError'>Please enter at least one search word.</span>";
	header("Location: {$config->_rootUri}/");
	exit;
}


# Let's implement the PRG (Post->Redirect->Get) method here so that
# users can use the back button freely without browser warnings
if ( isset($_POST['doSearch']) ) {
	# build the query string
	$searchString = trim($_POST['searchString']);
	$queryString = "searchString=$searchString&searchType={$_POST['searchType']}&wordType={$_POST['wordType']}&foodCat={$_POST['foodCat']}&sortType={$_POST['sortType']}";

	# redirect the browser to refetch this page with a GET method
	header("Location: {$config->_rootUri}/{$config->_thisScript}?$queryString");
	exit;
}

# we should be here with a GET now. make sure that each value is 
# set, if not then send the user back to the index page.

if (
	(! isset($_GET['searchString'])) ||
	(! isset($_GET['searchType'])) ||
	(! isset($_GET['wordType'])) ||
	(! isset($_GET['sortType'])) ||
	(! isset($_GET['foodCat']))
) {
	$_SESSION['systemMsg'] = "<span class='msgError'>Some required fields were missing in your search.</span>";
#	header("Location: {$config->_rootUri}/");
	exit;
}

# Sanitize $searchString
$searchString = sanitizeUserInput($_GET['searchString']);

# assign the vars to the smarty template
# these will simply be used to remind the user of how they searched
$smarty->assign("searchString", $searchString);
$smarty->assign("searchType", $_GET['searchType']);
$smarty->assign("wordType", $_GET['wordType']);
$smarty->assign("sortType", $_GET['sortType']);

# initialize the WHERE and ORDER BY parts variables.
# since the structure of the user saved meals and foods table is so diff. from the 
# main data tables, we need to formulate a special where clause for these tables.
$where = "";
$userWhere = "";
$orderby = "";

# what type of search does the user want?
if ( $_GET['searchType'] == "Exact Phrase" ) {
	$where = " WHERE (long_desc = '$searchString' OR comname = '$searchString') ";
	$userWhere = " WHERE description = '$searchString' ";
} else {
	# if user selected 'Any Word' or 'All Words' then break up the
	# search string into an array, then count the array so we know 
	# how many search strings we are dealing with then go ahead and
	# add the first where argument
	$searchStrings = preg_split("/\s+/",$searchString);
	$stringsCount = count($searchStrings);
	$where = " WHERE (long_desc LIKE '%{$searchStrings[0]}%' OR comname LIKE '%{$searchStrings[0]}%') ";
	$userWhere = " WHERE description LIKE '%{$searchStrings[0]}%' ";
	if ( $_GET['searchType'] == "All Words" ) {
		$logicalOp = "AND";
	} else {
		$logicalOp = "OR";
	}
	# append as many additional args to the where clause as there
	# are items in array joined with our logical operator
	for ( $idx = 1; $idx < $stringsCount; $idx++ ) {
		$where .= " $logicalOp (long_desc LIKE '%{$searchStrings[$idx]}%' OR comname LIKE '%{$searchStrings[$idx]}%') ";
		$userWhere .= " $logicalOp description LIKE '%{$searchStrings[$idx]}%' ";
	}
}

# if the user selected a category, add it here
if ( $_GET['foodCat'] != "All" ) {
	$where .= " AND foodCats.fdgrp_cd = '{$_GET['foodCat']}' ";
	# get food category name
	$smarty->assign("foodCatName", getFoodCategoryName($_GET['foodCat']));
} else {
	$smarty->assign("foodCatName", "All");
}

# build the ORDER BY part
if ( $_GET['sortType'] == "Food Description" ) {
	$orderby = " ORDER BY foodDesc ";
} elseif ( $_GET['sortType'] == "Category" ) {
	$orderby = " ORDER BY fdgrp_desc, foodDesc ";
} elseif ( $_GET['sortType'] == "Popularity" ) {
	$orderby = " ORDER BY popularity DESC, foodDesc ";
}

# build the main query
$sql = sprintf ("
	SELECT foodDescs.ndb_no, foodDescs.popularity AS popularity,
		CONCAT(foodDescs.long_desc, foodDescs.comname) AS foodDesc,
		foodCats.fdgrp_cd, foodCats.fdgrp_desc 
	FROM foodDescs LEFT JOIN foodCats
		ON foodDescs.fdgrp_cd = foodCats.fdgrp_cd
	%s
	AND foodDescs.usda_status = 'active'
	",
	$where
);

# now append another select statement that we will UNION to the previous. this one
# is looking for user saved foods and meals, and contains a UNION itself.
# most of the fields are just place holders with special values that will allow
# a script to later identify these as user foods and meals.  ndb_nos 00001 and 00002
# are totally arbitrary and could be anything that doesn't already exist in the
# database, but these numbers shouldn't be changed without also changing the 
# code in food_search.php that is looking for these specific numbers.  for the 
# userFood we will embed the ndb_no, weight and quantity into a suitable query
# string so that the smarty template can just plug it into the page instead of
# having to parse it
$sql .= sprintf ("
	UNION
	SELECT CONCAT('food=',userFoods.food,'&weight=',userFoods.weight,'&quantity=',
		userFoods.quantity,'&userFoodsId=',userFoods.id) AS ndb_no,
		userFoods.popularity AS popularity, description AS foodDesc,
		'userFood' AS fdgrp_cd, 'User saved foods' AS fdgrp_desc
	FROM userFoods
	%s
	UNION
	SELECT id AS ndb_no, userMeals.popularity AS popularity, description AS foodDesc,
		'userMeal' AS fdgrp_cd, 'User saved meals' AS fdgrp_desc
	FROM userMeals
	%s
	",
	$userWhere,
	$userWhere
);

# the ORDER BY statement must be appended to the total UNION statement last
# because you can't have order by statements in each part of a statement
# that includes UNIONs, at least not in MySQL.
$sql .= $orderby;

# a count-only statement identical to the sql above that will 
# give the pagination function all it needs without potentially
# and uselessly fetching a massive record set only to get the
# row count
$paginationSql = sprintf ("
	SELECT
		(SELECT count(*)
		FROM foodDescs LEFT JOIN foodCats
			ON foodDescs.fdgrp_cd = foodCats.fdgrp_cd
		%s)
	+
		(SELECT count(*) FROM userFoods %s)
	+
		(SELECT count(*) FROM userFoods %s)
	AS rowCount
	",
	$where,
	$userWhere,
	$userWhere
);

# pass the sql to the paginator, the return value should be the
# pagination navigation bar. if there is already
# a page number submitted, then strip it off first and let
# the function add the new one
$page = (isset($_GET['page'])) ? "{$_GET['page']}" : "1";
if ( preg_match("/^(.+?)&?page=\d+$/", $_SERVER['QUERY_STRING'], $queryString) ) {
	$uri = "{$config->_rootUri}/{$config->_thisScript}?{$queryString[1]}";
} else {
	$uri = "{$config->_rootUri}/" . basename($_SERVER['REQUEST_URI']);
}

$pageOffset = (($page * $config->_recordsPerPage) - $config->_recordsPerPage);

# start with a blank pageNav
$pageNav = getPagination($page, $pageOffset, $uri, $paginationSql);

# pageNav was passed by reference to the function so should be
# suitably modified
$smarty->assign("pageNav", $pageNav);

# select a limited rowset
$db->SelectLimit($sql, $config->_recordsPerPage, $pageOffset);

# if there are query results then proceed.  the query above already picks
# out foods that have at least one partial word match, perhaps more.
# therefore below we really only need to weed out records based on whether
# the user select "Full Word" or "Partial Word" match.  if the user selected
# to sort by category then the records will be grouped in the array
# $searchResults with an array index which is equivalent to the food's
# category id.  when displaying the results to the user this will allow us
# to break the results out grouped in categories, otherwise we just dump
# all the search results into a single array and will not display the
# results grouped by category
if ( $db->_rowCount > 0 ) {
	$idx = 0; # an index for each array element in the returned results
	# act on result set of above query based on whether user selected
	# 'Full Word' or 'Partial Word' 
	foreach ( $db->_rows as $row ) {

		# trim any extra commas from the end of foodDesc, as may appear
		# due to the concatenation of long_desc and comname with a comma
		# where comname has no value, which it frequently doesnt'
		$row['foodDesc'] = trim($row['foodDesc'], ", ");

		# tracks how many, if any, search string words match words from
		# concatenated query field 'foodDesc' 
		$matchCount = 0;
		if ( $_GET['wordType'] == "Full Word" ) {
			# dump each individual word of the field 'foodDesc' into an array
			$words = preg_split("/\W+/", $row['foodDesc']);
			# start a foreach loop on individual strings from users search string
			foreach ( $searchStrings as $searchString ) {
				# start a foreach loop on individual words found in field
				# 'foodDesc' from query
				foreach ($words as $word) {
					if ( strcasecmp($searchString, $word) == 0 ) { 
	 					# if there is a match then increment our counter variable by 1
						$matchCount++;
					}
				}
			}

			# if the search type was 'Any Word', then if only 1 of the search
			# string words matched a word from the field 'foodDesc' we have a
			# match and should add the record, else if the search type was
			# 'All Words' and all of the search string words matched then add
			# the record, else don't add anything. 
			if (
				(($_GET['searchType'] == "Any Word") && ($matchCount > 0)) ||
				(($_GET['searchType'] == "All Words") && ($matchCount == count($searchStrings)))
			) {
				if ( $_GET['sortType'] == "Category" ) {
					$searchResults[$row['fdgrp_cd']]['foodCatName'] = $row['fdgrp_desc'];
					$searchResults[$row['fdgrp_cd']]['searchResults'][$idx]['food'] = $row['ndb_no'];
					$searchResults[$row['fdgrp_cd']]['searchResults'][$idx]['foodDesc'] = $row['foodDesc'];
				} else {
					$searchResults[$idx]['food'] = $row['ndb_no'];
					$searchResults[$idx]['category'] = $row['fdgrp_cd'];
					$searchResults[$idx]['foodDesc'] = $row['foodDesc'];
				}
			}
		} else {
			# the search type must have been "Partial Word" so just add the row
			if ( $_GET['sortType'] == "Category" ) {
				$searchResults[$row['fdgrp_cd']]['foodCatName'] = $row['fdgrp_desc'];
				$searchResults[$row['fdgrp_cd']]['searchResults'][$idx]['food'] = $row['ndb_no'];
				$searchResults[$row['fdgrp_cd']]['searchResults'][$idx]['foodDesc'] = $row['foodDesc'];
			} else {
				$searchResults[$idx]['food'] = $row['ndb_no'];
				$searchResults[$idx]['category'] = $row['fdgrp_cd'];
				$searchResults[$idx]['foodDesc'] = $row['foodDesc'];
			}
		}
		$idx++;
	}
	if ( isset($searchResults) ) {
		$smarty->assign("searchResults", $searchResults);
	}
}

# grab the various parts.  these sections are not printed to the screen
# but rather dumped into smarty variables that will simply be printed
# in the template, so the order doesn't matter here at the moment
require("header.php");
require("sidebar_left.php");
require("sidebar_right.php");
require("footer.php");

$smarty->display("food_search.tpl");

?>
