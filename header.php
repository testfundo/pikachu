<?php

/**
 * Copyright (c) 2007 Nathan Kinkade
 * 
 * This code is offered under an MIT (X11) license.  For more information
 * about the terms of this license see the file LICENSE included with this
 * software or visit: http://www.opensource.org/licenses/mit-license.php
 */

# this file allows us to change any info inside the
# <head> tags for any given file, while still using
# a common header file
include("meta.php");
$smarty->assign("myHeaders", $myHeaders);

# if logged in show logout, if not, show login fields. this
# variable will also be used as a convenient way for the
# templates to identify if a user is logged in or not and to
# show or not show certain things
if ( isLoggedIn() ) {
	$smarty->assign("isLoggedIn", true);

	# get any saved foods the user may have
	$smarty->assign("favFoods", getFavoriteFoods($_SESSION['user']['id']));

	# get any saved meals the user may have
	$smarty->assign("favMeals", getFavoriteMeals($_SESSION['user']['id']));

	# get any saved diaries the user may have
	$smarty->assign("userDiaries", getUserDiaries($_SESSION['user']['id']));

}

# print any system message that may exist and then clear the variable
if ( ! empty($_SESSION['systemMsg']) ) {
	$smarty->assign("systemMsg", $_SESSION['systemMsg']);
	unset($_SESSION['systemMsg']);
}

# grab the header
$smarty->assign("header", $smarty->fetch("header.tpl"));

?>
