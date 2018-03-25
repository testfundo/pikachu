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

# let's implement the PRG (Post->Redirect-Get) method here so that
# users can use the back button freely without browser warnings
if ( isset($_POST['nutrient']) ) {
	$queryString = "nutrient={$_POST['nutrient']}&count=50";
	header("Location: {$config->_rootUri}/{$config->_thisScript}?$queryString");
	exit;
}

# we should be here with a GET now. make sure that each value is 
# set, if not then send the user back to the index page.
if (
	(! isset($_GET['nutrient'])) ||
	(! isset($_GET['count']))
) {
	$_SESSION['systemMsg'] = "<span class='msgError'>Some required fields were missing in your search.</span>";
	header("Location: {$config->_rootUri}/");
	exit;
}

# assign the vars to the smarty template
# these will simply be used to remind the user of how they searched
$smarty->assign("nutrient", $_GET['nutrient']);
$smarty->assign("count", $_GET['count']);

# execute query
$sql = sprintf ("
	SELECT foodDescs.ndb_no, foodDescs.long_desc, foodDescs.comname,
		CONCAT(foodDescs.long_desc, foodDescs.comname) AS foodDesc,
		nutrientData.nutr_val, nutrientDefs.nutrdesc, nutrientDefs.units
	FROM foodDescs LEFT JOIN nutrientData
		ON foodDescs.ndb_no = nutrientData.ndb_no
	LEFT JOIN nutrientDefs
		ON nutrientData.nutr_no = nutrientDefs.nutr_no
	WHERE nutrientDefs.nutr_no = '%s'
	ORDER BY nutrientData.nutr_val DESC
	",
	$_GET['nutrient']
);

# select a limited rowset
$db->SelectLimit($sql, $_GET['count'], "0");

if ( $db->_rowCount > 0 ) {
	$idx = 0; # an index for each array element in the returned results
	foreach ( $db->_rows as $row ) {

		# if there was no 'comname' for the food, then just display the
		# field 'long_desc', else display the concatenated field 'foodDesc'
		# NOTE: it would be possible and easy to concatenate the 'long_desc'
		# and 'comname' fields at the time of display, but for future growth
		# possibilities and because we reference 'foodDesc' many times below
		# it seems just as well to have a concatenated field in the result set
		if ( "" == trim($row['comname']) ) {
			$row['foodDesc'] = $row['long_desc'];
		}
			
		$searchResults[$idx]['food'] = $row['ndb_no'];
		$searchResults[$idx]['units'] = $row['units'];
		$searchResults[$idx]['nutr_val'] = $row['nutr_val'];
		$searchResults[$idx]['foodDesc'] = $row['foodDesc'];

		$idx++;
	}

	if ( isset($searchResults) ) {
		$smarty->assign("searchResults", $searchResults);
	}
}

# get the submitted nutrients descriptions for the template
$smarty->assign("nutrientName", getNutrientName($_GET['nutrient']));

# grab the various parts.  these sections are not printed to the screen
# but rather dumped into smarty variables that will simply be printed
# in the template, so the order doesn't matter here at the moment
require("header.php");
require("sidebar_left.php");
require("sidebar_right.php");
require("footer.php");

$smarty->display("nutrient_search.tpl");

?>
