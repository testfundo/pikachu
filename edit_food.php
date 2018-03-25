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
include("config.php");

# the user must be logged in to access this script. if they are
# not then this function will send them back to the index page
loginRequired();

# action will be showFoods when the user wants to edit a particular
# food or view a list and select which one to edit
if ( isset($_GET['action']) && ($_GET['action'] == "showFoods") ) {

	# if the user wanted to see a specific food, then pass it to the
	# template so that it can be loaded automatically
	if ( isset($_GET['food']) ) {
		$smarty->assign("editFood", $_GET['food']);
	}

	$sql = sprintf ("
		SELECT * FROM userFoods
		WHERE user = '%s'
		",
		$_SESSION['user']['id']
	);
	$db->Select($sql);
	if ( $db->_rowCount > 0 ) {
		$smarty->assign("savedFoods", $db->_rows);
	}
	# grab the various parts.  these sections are not printed to the screen
	# but rather dumped into smarty variables that will simply be printed
	# in the template, so the order doesn't matter here at the moment
	include("header.php");
	include("sidebar_left.php");
	include("sidebar_right.php");
	include("footer.php");
	$smarty->display("edit_food.tpl");
	exit;

}

# don't let the user continue here if we don't have the id of
# the saved food, or if this id isn't a number, or if an action
# wasn't specified
if ( ! isset($_POST['food']) ) {
	$_SESSION['systemMsg'] = "<span class='msgError'>You must specify a food.</span>";
	header("Location: {$config->_previousUri}");
	exit;
}
if ( ! is_numeric($_POST['food']) ) {
	$_SESSION['systemMsg'] = "<span class='msgError'>The food must be numeric.</span>";
	header("Location: {$config->_previousUri}");
	exit;
}
if (
	! isset($_POST['action']) ||
	(($_POST['action'] != "Delete") && ($_POST['action'] != "Rename") && ($_POST['action'] != "Modify") && ($_POST['action'] != "Edit"))
) {
	$_SESSION['systemMsg'] = "<span class='msgError'>You must specify an appropriate action.</span>";
	header("Location: {$config->_previousUri}");
	exit;
}

# the following should be relatively self-explanatory
switch ( $_POST['action'] ) {
	case "Delete":
		$sql = sprintf ("
			DELETE FROM userFoods
			WHERE id = '%s'
			",
			$_POST['food']
		);
		$db->Modify($sql);
		if ( $db->_affectedRows == 1 ) {
			$_SESSION['systemMsg'] = "<span class='msgOkay'>The food was deleted successfully.</span>";
		} else {
			$_SESSION['systemMsg'] = "<span class='msgError'>There was an error. The food was not deleted.</span>";
		}
		break;
	case "Rename":
		if ( isset($_POST['newFoodName']) && ("" != trim($_POST['newFoodName'])) ) {
			$sql = sprintf ("
				UPDATE userFoods
				SET description = '%s'
				WHERE id = '%s'
				",
				$db->EscapeString($_POST['newFoodName']),
				$_POST['food']
			);
			$db->Modify($sql);
			if ( ! $db->_error ) {
				$_SESSION['systemMsg'] = "<span class='msgOkay'>The food was renamed successfully.</span>";
			} else {
				$_SESSION['systemMsg'] = "<span class='msgError'>There was an error. The food was not renamed.</span>";
			}
		} else {
			$_SESSION['systemMsg'] = "<span class='msgError'>The food was not renamed because the new name was empty.</span>";
		}
		break;
	case "Edit":
		header("Location: {$config->_rootUri}/edit_food?food={$_POST['food']}&action=showFoods");
		exit;
		break;
	case "Modify":
		if ( isset($_POST['foodDesc']) && ("" != trim($_POST['foodDesc'])) ) {
			if ( isset($_POST['quantity']) && is_numeric(trim($_POST['quantity'])) ) {
				$sql = sprintf ("
					UPDATE userFoods SET
						description = '%s',
						quantity = '%s',
						weight = '%s',
						favorite = '%s'
					WHERE id = '%s' AND user = '%s'
					",
					$db->EscapeString($_POST['foodDesc']),
					$_POST['quantity'],
					$_POST['weight'],
					$favorite = isset($_POST['favorite']) ? "1" : "0",
					$_POST['food'],
					$_SESSION['user']['id']
				);
				$db->Modify($sql);
				if ( ! $db->_error ) {
					$_SESSION['systemMsg'] = "<span class='msgOkay'>The food was modified successfully.</span>";
				} else {
					$_SESSION['systemMsg'] = "<span class='msgError'>There was an error. The food was not renamed.</span>";
				}
			} else {
				$_SESSION['systemMsg'] = "<span class='msgError'>The amount must be a number.</span>";
			}
		} else {
			$_SESSION['systemMsg'] = "<span class='msgError'>The food was not saved because the new name was empty.</span>";
		}
		break;
	default:
		$_SESSION['systemMsg'] = "<span class='msgOkay'>Nothing was changed.</span>";
}

# now send the user back where they came from with a system message
header("Location: {$config->_previousUri}");
exit;

?>
