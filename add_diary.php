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

$newDiaryName = trim($_POST['newDiaryName']);
if ( empty($newDiaryName) ) {
	$_SESSION['systemMsg'] = "<span class='msgError'>You must give the diary a name before you can save it.</span>";
	header("Location: {$config->_previousUri}");
	exit;
}

$sql = sprintf ("
	INSERT INTO userDiaries (user, description)
	VALUES('%s','%s')
	",
	$_SESSION['user']['id'],
	$db->escapeString($newDiaryName)
);
$db->Modify($sql);

if ( $db->_affectedRows == 1 ) {
	$_SESSION['systemMsg'] = "<span class='msgOkay'>The diary was created successfully.</span>";
} else {
	$_SESSION['systemMsg'] = "<span class='msgError'>There was an error while creating the diary.</span>";
}

header("Location: {$config->_previousUri}");
exit;

?>
