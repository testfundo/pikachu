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

# if ndb_no wasn't passed to this script then just go to the
# index page, as we can't do anything without it
if ( isset($_REQUEST['food']) ) {
	$food = $_REQUEST['food'];
	$smarty->assign("food", $food);
} else {
	$_SESSION['systemMsg'] = "<span class='msgError'>You must specify a food.</span>";
	header("Location: {$config->_rootUri}/");
	exit;
}

# get the selected food and quantities from the database
$sql = sprintf ("
	SELECT foodDescs.sciname, CONCAT(foodDescs.long_desc, foodDescs.comname)
		AS foodDesc, weights.*
	FROM foodDescs LEFT JOIN weights
		ON foodDescs.ndb_no = weights.ndb_no
	WHERE foodDescs.ndb_no = '%s'
		AND weights.usda_status = 'active'
	",
	$food
);
$db->Select($sql);
# if for some reason the ndb_no doesn't exist, then drop them where they 
# came from with an appropriate error message
if ( $db->_rowCount > 0 ) {
	$smarty->assign("foodQuantities", $db->_rows);
} else {
	$_SESSION['systemMsg'] = "<span class='msgError'>The food you specified doesn't seem to exist.</span>";
	header("Location: {$config->_previousUri}");
	exit;
}

# increment the counter for this food.  this counter could be used for all
# sorts of things, for example it is the basis of the "sort by popularity" option.
# the more people that select this item, the higher in the sort list it
# will appear.
incrementPopularityCounter($food, "foodDescs");

# some data housekeeping
for ( $idx = 0; $idx < count($db->_rows); $idx++ ) {
	# the data from the USDA frequently has low order, unnecessary zeros to the
	# right of the decimal. multipying by 1 is just an easy way to remove them.
	$db->_rows[$idx]['amount'] = $db->_rows[$idx]['amount'] * 1;

	# trim any extra commas from the end of foodDesc, as may appear
	# due to the concatenation of long_desc and comname with a comma
	# where comname has no value, which it frequently doesnt'
	$db->_rows[$idx]['foodDesc'] = trim($db->_rows[$idx]['foodDesc'], ", ");
}

$smarty->assign("foodQuantities", $db->_rows);

# grab the various parts.  these sections are not printed to the screen
# but rather dumped into smarty variables that will simply be printed
# in the template, so the order doesn't matter here at the moment
require("header.php");
require("sidebar_left.php");
require("sidebar_right.php");
require("footer.php");

$smarty->display("food_quantity.tpl");

?>
