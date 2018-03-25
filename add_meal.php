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

# make sure there is a meal desc before continuing
$description = trim($_POST['description']);
if ( empty($description) ) {
	$_SESSION['systemMsg'] = "<span class='msgError'>You must give the meal a name before you can save it.</span>";
	header("Location: {$config->_previousUri}");
	exit;
}

# if there is no meal id then kick the user out
if ( ! isset($_POST['meal']) ) {
	$_SESSION['systemMsg'] = "<span class='msgError'>You must specify a meal Id.</span>";
	header("Location: {$config->_previousUri}");
	exit;
}

# if the meal Id isn't numeric then kick the user out
if ( ! is_numeric($_POST['meal']) ) {
	$_SESSION['systemMsg'] = "<span class='msgError'>The meal Id must be a number.</span>";
	header("Location: {$config->_previousUri}");
	exit;
}

if ( isset($_POST['saveMeal']) ) {

	# meal id of 0 means the current meal
	if ( $_POST['meal'] == "0" ) {
		# make sure that there is a meal in the session or something that
		# resembles one before we proceed.
		if ( ! isset($_SESSION['currentMeal']) ) {
			$_SESSION['systemMsg'] = "<span class='msgError'>There is no current meal to save.</span>";
			header("Location: {$config->_previousUri}");
			exit;
		}
		$mealItems = $_SESSION['currentMeal'];
	} else {
		$sql = sprintf ("
			SELECT userMeals.description AS mealDesc, userMealItems.*
			FROM userMeals LEFT JOIN userMealItems
				ON userMeals.id = userMealItems.meal
			WHERE userMeals.id = '%s' AND userMeals.user = '%s'
			",
			$_POST['meal'],
			$_SESSION['user']['id']
		);
		$db->Select($sql);
		if ( $db->_rowCount != 0 ) {
			$mealItems = $db->_rows;
		} else {
			$_SESSION['systemMsg'] = "<span class='msgError'>The specified meal doesn't exist.</span>";
			header("Location: {$config->_previousUri}");
			exit;
		}
	}

	# set a status to true, if we encounter errors it will be set to false
	# and the user will be notified
	$status = "true";

	# add the main entry for the new meal
	$sql = sprintf ("
		INSERT INTO userMeals (user, description)
		VALUES('%s','%s')
		",
		$_SESSION['user']['id'],
		$db->EscapeString($description)
	);
	$db->Modify($sql);

	# if adding the main meal entry was successful, then try to
	# add each meal item of the meal
	if ( $db->_affectedRows == 1 ) {
		$meal = $db->InsertId();
		foreach ( $mealItems as $mealItem ) {
			$sql = sprintf ("
				INSERT INTO userMealItems (meal, food, weight, quantity, description)
				VALUES ('%s','%s','%s','%s','%s')
				",
				$meal,
				$mealItem['food'],
				$mealItem['weight'],
				$mealItem['quantity'],
				$mealItem['description']
			);
			$db->Modify($sql);
			if ( $db->_affectedRows != 1 ) {
				$status = "false";
			}
		}
	}

	if ( $status == "true" ) {
		# clear the current meal if meal id was 0
		if ( $_POST['meal'] == "0" ) {
			unset($_SESSION['currentMeal']);
		}
		$_SESSION['systemMsg'] = "<span class='msgOkay'>The meal was saved successfully.</span>";
	} else {
		$_SESSION['systemMsg'] = "<span class='msgError'>There was an error while saving the meal.</span>";
	}

} elseif ( isset($_POST['addMealToDiary']) ) {
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
	$itemData = "{$_POST['meal']}::$description";

	$sql = sprintf ("
		INSERT INTO userDiaryItems (diary, data, timestamp, type) 
		VALUES ('%s', '%s', '%s', '%s')
		",
		$_POST['diary'],
		$db->EscapeString($itemData),
		$timestamp,
		"Meal"
	);
	$db->Modify($sql);
	if ( $db->_affectedRows == 1 ) {
		$_SESSION['systemMsg'] = "<span class='msgOkay'>The meal was added to the selected diary.</span>";
	} else {
		$_SESSION['systemMsg'] = "<span class='msgError'>There was an error. The meal wasn't added.</span>";
	}
}

header("Location: {$config->_rootUri}/");
exit;

?>
