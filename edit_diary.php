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

# don't go forward unless a diary was specified
if ( ! isset($_POST['diary']) ) {
	$_SESSION['systemMsg'] = "<span class='msgError'>You must specify a diary.</span>";
	header("Location: {$config->_previousUri}");
	exit;
}

# don't go forward unless an action was specified
if ( ! isset($_POST['action']) ) {
	$_SESSION['systemMsg'] = "<span class='msgError'>You must specify an action.</span>";
	header("Location: {$config->_previousUri}");
	exit;
}

switch ( $_POST['action'] ) {
	case "addNote" :
		if ( ! empty($_POST['diaryTimestamp']) ) {
			$timestamp = strtotime($_POST['diaryTimestamp']);
		} else {
			$timestamp = time();
		}
		$sql = sprintf ("
			INSERT INTO userDiaryItems(diary, data, timestamp, type)
			VALUES ('%s','%s','%s','%s')
			",
			trim($_POST['diary']),
			$db->EscapeString($_POST['note']),
			$timestamp,
			"Note"
		);
		$db->Modify($sql);
		if ( $db->_affectedRows == 1 ) {
			$_SESSION['systemMsg'] = "<span class='msgOkay'>The note was successfully added.</span>";
		} else {
			$_SESSION['systemMsg'] = "<span class='msgError'>There was an error. The note was not added.</span>";
		}
		break;
	case "Delete":
		$sql = sprintf ("
			DELETE userDiaryItems.*, userDiaries.*
			FROM userDiaryItems INNER JOIN userDiaries
				ON userDiaryItems.diary = userDiaries.id
			WHERE userDiaries.user = '%s'
				AND userDiaryItems.diary = '%s'
			",
			$_SESSION['user']['id'],
			$_POST['diary']
		);
		$db->Modify($sql);
		if ( ! $db->_error ) {
			$_SESSION['systemMsg'] = "<span class='msgOkay'>The diary was successfully deleted.</span>";
		} else {
			$_SESSION['systemMsg'] = "<span class='msgError'>There was an error. The diary was not deleted.</span>";
		}
		break;
	case "Rename":
		if ( isset($_POST['newDiaryName']) && ("" != trim($_POST['newDiaryName'])) ) {
			$sql = sprintf ("
				UPDATE userDiaries SET
					description = '%s'
				WHERE id = '%s'
				",
				$db->EscapeString($_POST['newDiaryName']),
				$_POST['diary']
			);
			$db->Modify($sql);
			if ( ! $db->_error ) {
				$_SESSION['systemMsg'] = "<span class='msgOkay'>The diary was successfully renamed.</span>";
			} else {
				$_SESSION['systemMsg'] = "<span class='msgError'>There was an error. The diary was not renamed.</span>";
			}
		} else {
			$_SESSION['systemMsg'] = "<span class='msgError'>The diary was not renamed because the new name was empty.</span>";
		}
		break;
	default:
		$_SESSION['systemMsg'] = "<span class='msgError'>There action you specified was not recognized.</span>";
		break;
}

# now send the user back where they came from
header("Location: {$config->_previousUri}");
exit;

?>
