<?php

/**
 * This function will simply initialize a variable to
 * an empty string unless it already has a value, in
 * which case it will simply return the existing value
 */
function initVar($var) {
	
	$var = empty($var) ? "" : $var;
	return $var;

}

/**
 * This function will initialize a variable to an empty
 * string unless it already has a value, in which case
 * it will simply return the existing value ... the only
 * diff. between this function and initVar() is that this
 * fuction encodes HTML special characters and then echos
 * the variable ... useful for initializing and printing
 * a variable all in one step
 */
function printVar($var) {
	
	$var = empty($var) ? "" : htmlspecialchars($var,ENT_QUOTES);
	echo $var;
	return true;

}

/**
 * Create pagination, including a page navigation bar.  the
 * output should be fairly generic, enclosed in a div with 
 * css class of 'paginationNav', and more or less suitable
 * to be dropped into just about any page.
 */
function getPagination($page = 1, $pageOffset, $uri , $paginationSql) {

	global $config, $db;

	# start with a blank page navigation menu
	$pageNav = "";

	# change special chars, particularly the & to the HTML entity
	# so that browsers don't confuse it with an HTML entity.
	$uri = htmlspecialchars($uri);

	# determine total number of records and pages
	$db->SelectOne($paginationSql);
	$totalRecords = $db->_row['rowCount'];
	$totalPages = ceil($totalRecords / $config->_recordsPerPage);

	# if the total records are less than what we show per page, then
	# just skip everything else below
	if ( $totalRecords <= $config->_recordsPerPage ) {
		$fromRecord = ($pageOffset + 1);
		$pageNav .= <<<HTML
	<br />
	<span style='font-size: xx-small;'>Displaying: $fromRecord to $totalRecords of $totalRecords results.</span>
HTML;

		return $pageNav;
	}

	# either append the proper page with & or ? depending
	# on whether the submitted URI already has a query
	# string or not
	if ( preg_match("/\?.+/", $uri) ) {
		$uri = "$uri&amp;";
	} else {
		$uri = "$uri?";
	}

	# create the navigation menu
	if ( $page > 1 ) { 
		$pagePrev = ($page - 1);
		# if the previous page isn't also the first page, show a link for first page.
		if ( $pagePrev != 1 ) {
			$pageNav .= "	<a href='{$uri}page=1' title='First Page'>[First] </a>\n";
		}
		$pageNav .= "	<a href='{$uri}page=$pagePrev' title='Page $pagePrev'>Prev</a>\n";
	}

	for ( $idx = 1; $idx <= $totalPages; $idx++ ) {
		if ( $idx == $page ) {
			$pageNav .= "	<strong>$idx</strong> \n";
		} else {
			$pageNav .= "	<a href='{$uri}page=$idx' title='Page $idx'>$idx</a> \n"; 
		}
	}

	if ( ($totalRecords - ($config->_recordsPerPage * $page)) > 0 ) {
		$pageNext = ($page + 1);
		$pageNav .= "	<a href='{$uri}page=$pageNext' title='Page $pageNext'>Next</a>\n";
		# if the previous page isn't also the first page, show a link for first page.
		if ( $pageNext != $totalPages ) {
			$pageNav .= "<a href='{$uri}page=$totalPages' title='Last page'> [Last]</a> \n";  
		}
	}

	$fromRecord = ($pageOffset + 1);
	if ( $page == $totalPages ) {
		$toRecord = $totalRecords;
	} else {
		$toRecord = ($pageOffset + $config->_recordsPerPage);
	}

	$pageNav .= <<<HTML
	<br />
	<span style='font-size: xx-small;'>Displaying: $fromRecord to $toRecord of $totalRecords results.</span>
HTML;

	return $pageNav;

}

/**
 * Sanitize user form input, which at the moment means:
 * - trim any leading and trailing whitespace
 * - convert HTML special chars to HTML entities
 */
function sanitizeUserInput($input) {

	$output = trim($input);
	$output = htmlspecialchars($output);

	return $output;

}

?>
