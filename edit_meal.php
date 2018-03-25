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

# action will be showMeals when the user wants to edit a particular
# food or view a list and select which one to edit
if ( isset($_GET['action']) && ($_GET['action'] == "showMeals") ) {

	# if the user wanted to see a specific meal, then pass it to the
	# template so that it can be loaded automatically
	if ( isset($_GET['meal']) ) {
		$smarty->assign("editMeal", $_GET['meal']);
	}

	$sql = sprintf ("
		SELECT * FROM userMeals
		WHERE user = '%s'
		",
		$_SESSION['user']['id']
	);
	$db->Select($sql);
	if ( $db->_rowCount > 0 ) {
		$smarty->assign("savedMeals", $db->_rows);
	}
	# grab the various parts.  these sections are not printed to the screen
	# but rather dumped into smarty variables that will simply be printed
	# in the template, so the order doesn't matter here at the moment
	include("header.php");
	include("sidebar_left.php");
	include("sidebar_right.php");
	include("footer.php");
	$smarty->display("edit_meal.tpl");
	exit;

}

# don't let the user continue here if we don't have the id of
# the saved meal, or if this id isn't a number, or if an action
# wasn't specified
if ( ! isset($_POST['meal']) ) {
	$_SESSION['systemMsg'] = "<span class='msgError'>You must specify a meal ID.</span>";
	header("Location: {$config->_previousUri}");
	exit;
}
if ( ! is_numeric($_POST['meal']) ) {
	$_SESSION['systemMsg'] = "<span class='msgError'>The meal ID must be numeric.</span>";
	header("Location: {$config->_previousUri}");
	exit;
}
if (
	! isset($_POST['action']) ||
	(($_POST['action'] != "Delete") && ($_POST['action'] != "Rename") && ($_POST['action'] != "Edit") && ($_POST['action'] != "Modify"))
) {
	$_SESSION['systemMsg'] = "<span class='msgError'>You must specify an appropriate action.</span>";
	header("Location: {$config->_previousUri}");
	exit;
}

switch ( $_POST['action'] ) {
	case "Delete":
		# set a status variable so that we can keep some minimal track
		# on whether the query succeeded or not.  default will be true
		$status = "true";

		# working backward, first delete the meal items
		$sql = sprintf ("
			DELETE userMealItems.*, userMeals.*
			FROM userMealItems INNER JOIN userMeals
				ON userMealItems.meal = userMeals.id
			INNER JOIN users
				ON userMeals.user = users.id
			WHERE users.id = '%s' AND userMealItems.meal = '%s'
			",
			$_SESSION['user']['id'],
			$_POST['meal']
		);
		$db->Modify($sql);
		if ( $db->_error ) {
			$status = "false";
		}

		# now delete any instances of this meal in any of the users
		# diaries
		$sql = sprintf ("
			DELETE userDiaryItems.*
			FROM userDiaryItems INNER JOIN userDiaries
				ON userDiaryItems.diary = userDiaries.id
			INNER JOIN users
				ON userDiaries.user = users.id
			WHERE users.id = '%s' AND userDiaryItems.data like '%%meal=%s%%'
				AND userDiaryItems.type = 'Meal'
			",
			$_SESSION['user']['id'],
			$_POST['meal']
		);
		$db->Modify($sql);
		if ( $db->_error ) {
			$status = "false";
		}

		# let the user know the status
		if ( $status == "true" ) {
			$_SESSION['systemMsg'] = "<span class='msgOkay'>The meal was successfully removed.</span>";
		} else {
			$_SESSION['systemMsg'] = "<span class='msgError'>There was an error. The meal was not removed.</span>";
		}
		break;
	case "Rename":
		if ( isset($_POST['currentMealName']) && ("" != trim($_POST['currentMealName'])) ) {
			$sql = sprintf ("
				UPDATE userMeals SET
					description = '%s'
				WHERE id = '%s'
				",
				$db->EscapeString($_POST['currentMealName']),
				$_POST['meal']
			);
			$db->Modify($sql);
			if ( ! $db->_error ) {
				$_SESSION['systemMsg'] = "<span class='msgOkay'>The meal was renamed successfully.</span>";
			} else {
				$_SESSION['systemMsg'] = "<span class='msgError'>There was an error. The meal was not renamed.</span>";
			}
		} else {
			$_SESSION['systemMsg'] = "<span class='msgError'>The meal was not renamed because the name was empty.</span>";
		}
		break;
	case "Edit":
		# "Edit" is the action for displaying a form for editing
		# let's implement the PRG (Post->Redirect-Get) method here so that
		# users can use the back button freely without browser warnings
		$queryString = "meal={$_POST['meal']}&action=showMeals";
		header("Location: {$config->_rootUri}/$config->_thisScript?$queryString");
		exit;
		break;
	case "Modify":
		# "Modify" is the action for actually modifying the meal, while
		# "Edit" above is for loading the meal into a form for editing

		# start with a failing true status
		$status = "true";

		$sql = sprintf ("
			UPDATE userMeals SET
				description = '%s',
				favorite = '%s'
			WHERE id = '%s'
			",
			$db->EscapeString($_POST['mealDesc']),
			$favorite = isset($_POST['favorite']) ? "1" : "0",
			$_POST['meal']
		);
		$db->Modify($sql);
		if ( $db->_error ) {
			$status = "false";
		}

		# now breakout the mealItemKeys that we collected earlier in order
		# to identify which POSTed fields to use to update which items
		if ( $mealItemIds = explode(",",$_POST['mealItemIds']) ) {
			foreach ( $mealItemIds as $mealItemId ) {
				$sql = sprintf ("
					UPDATE userMealItems SET
						description = '%s',
						quantity = '%s',
						weight = '%s'
					WHERE id = '%s'
					",
					$db->EscapeString($_POST["mealItemDesc-{$mealItemId}"]),
					$_POST["mealItemQuantity-{$mealItemId}"],
					$_POST["mealItemWeight-{$mealItemId}"],
					$mealItemId
				);
				$db->Modify($sql);
				if ( $db->_error ) {
					$status = "false";
				}
			}
		} else {
			$status = "false";
		}

		# if there were any errors, let the user know
		if ( $status == "false" ) {
			$_SESSION['systemMsg'] = "<span class='msgError'>There were errors during the update.</span>";
		} else {
			$_SESSION['systemMsg'] = "<span class='msgOkay'>The meal was updated successfully.</span>";
		}
		break;
	default:
		$_SESSION['systemMsg'] = "<span class='msgOkay'>Nothing was changed.</span>";
}

# if we didn't already send the user somewhere, then
# send the user back where they came from now
header("Location: {$config->_previousUri}");

?>
