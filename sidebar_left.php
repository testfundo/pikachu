<?php

/**
 * Copyright (c) 2007 Nathan Kinkade
 * 
 * This code is offered under an MIT (X11) license.  For more information
 * about the terms of this license see the file LICENSE included with this
 * software or visit: http://www.opensource.org/licenses/mit-license.php
 */

# put the current meal items in an array if it exists
if ( isset($_SESSION['currentMeal']) ) {
	$smarty->assign("currentMealItems", $_SESSION['currentMeal']);
}

# grab the page 
$smarty->assign("sidebar_left", $smarty->fetch("sidebar_left.tpl"));

?>
