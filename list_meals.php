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

# the user must be logged in to access this script. if they are
# not then this function will send them back to the index page
loginRequired();

# grab all of the users saved meals
$sql = sprintf ("
	SELECT userMeals.*
	FROM userMeals INNER JOIN users
		ON userMeals.user = users.id
	WHERE users.id = '%s'
	ORDER BY userMeals.description
	",
	$_SESSION['user']['id']
);
$db->Select($sql);
if ( $db->_rowCount > 0 ) {
	$smarty->assign("mealCount", $db->_rowCount);
	$smarty->assign("userMeals", $db->_rows);
}

# grab the various parts.  these sections are not printed to the screen
# but rather dumped into smarty variables that will simply be printed
# in the template, so the order doesn't matter here at the moment
require("header.php");
require("sidebar_left.php");
require("sidebar_right.php");
require("footer.php");

$smarty->display("list_meals.tpl");

?>
