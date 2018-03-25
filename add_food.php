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

# this is a generic script for saving a food, adding it to a meal or diary

# the fields required for this to work are 'ndb_no', 'weight', 'quantity',
# and 'foodDesc'.  if these fields aren't present then send them back
# where they came from with an error
if (
	( ! isset($_POST['food']) || ! is_numeric($_POST['food']) )  ||
	( ! isset($_POST['weight']) || ! is_numeric($_POST['weight']) )  ||
	( ! isset($_POST['quantity']) || ! is_numeric($_POST['quantity']) )  ||
	( ! isset($_POST['description']) )
) {
	$_SESSION['systemMsg'] = "<span class='msgError'>Some required fields were missing or had bad values.</span>";
	header("Location: {$config->_previousUri}");
	exit;
}

# don't let the user continue if the food description is empty
$description = trim($_POST['description']);
if ( empty($_POST['description']) ) {
	$_SESSION['systemMsg'] = "<span class='msgError'>You must give the food a description.</span>";
	header("Location: {$config->_previousUri}");
	exit;
}

if ( isset($_POST['saveFood']) ) {

	# the user selected to save this food
	$sql = sprintf ("
		INSERT INTO userFoods (user, food, weight, quantity, description) 
		VALUES ('%s', '%s', '%s', '%s', '%s')
		",
		$_SESSION['user']['id'],
		$_POST['food'],
		$_POST['weight'],
		$_POST['quantity'],
		$db->EscapeString($description)
	);
	$db->Modify($sql);
	if ( $db->_affectedRows == 1 ) {
		$_SESSION['systemMsg'] = "<span class='msgOkay'>The food was saved.</span>";
	} else {
		$_SESSION['systemMsg'] = "<span class='msgError'>There was an error. The food wasn't saved.</span>";
	}
} elseif ( isset($_POST['addFoodToMeal']) ) {
	# a mealId of 0 indicated adding to a New Meal
	if ( $_POST['meal'] != "0") {
		$sql = sprintf ("
			INSERT INTO userMealItems (meal, food, weight, quantity, description) 
			VALUES ('%s', '%s', '%s', '%s', '%s')
			",
			$_POST['meal'],
			$_POST['food'],
			$_POST['weight'],
			$_POST['quantity'],
			$db->EscapeString($description)
		);
		$db->Modify($sql);
		if ( $db->_affectedRows == 1 ) {
			$_SESSION['systemMsg'] = "<span class='msgOkay'>The food was added to the selected meal.</span>";
		} else {
			$_SESSION['systemMsg'] = "<span class='msgError'>There was an error. The food wasn't added.</span>";
		}
	} else {
		$_SESSION['currentMeal'][] = array (
			"food" => $_POST['food'],
			"weight" => $_POST['weight'],
			"quantity" => $_POST['quantity'],
			"description" => stripslashes($description)
		); 
		$_SESSION['systemMsg'] = "<span class='msgOkay'>The food was added to the current meal.</span>";
	}
	# send the user back to the main page
	header("Location: {$config->_rootUri}/");
	exit;
} elseif ( isset($_POST['addFoodToDiary']) ) {
	# don't let the user continue if they didn't specify a timestamp
	if ( empty($_POST['diaryTimestamp']) ) {
		$_SESSION['systemMsg'] = "<span class='msgError'>You must specify a timestamp.</span>";
		header("Location: {$config->_previousUri}");
		exit;
	}
	$timestamp = strtotime($_POST['diaryTimestamp']);

	# build the query string that will be used for the href when
	# we display this diary to the user
	$description = htmlspecialchars($_POST['description']);
	$itemData = "{$_POST['food']}::{$_POST['weight']}::{$_POST['quantity']}::$description";

	$sql = sprintf ("
		INSERT INTO userDiaryItems (diary, data, timestamp, type) 
		VALUES ('%s', '%s', '%s', '%s')
		",
		$_POST['diary'],
		$db->EscapeString($itemData),
		$timestamp,
		"Food"
	);
	$db->Modify($sql);
	if ( $db->_affectedRows == 1 ) {
		$_SESSION['systemMsg'] = "<span class='msgOkay'>The food was added to the selected diary.</span>";
	} else {
		$_SESSION['systemMsg'] = "<span class='msgError'>There was an error. The food wasn't added.</span>";
	}
}

# if we didn't send them somewhere else before, then just
# send them back home
header("Location: {$config->_rootUri}/");

?>
