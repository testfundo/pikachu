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

# first implement the PRG (Post->Redirect-Get) method here so that
# users can use the back button freely without browser warnings.
if ( isset($_POST['action']) && ($_POST['action'] == "viewMeal") ) {
	# if $_POST['meal'] = "viewAllMeals" then the user has selected
	# to view a list of all their saved meals and not just one particular
	# meal, so we'll forward them to the appropriate page.  this is here
	# because the easiest way to give the user the option to see all their
	# meals was to simply stick an option in the "Favorites" menu in the
	# left sidebar, and that form directs the user here.
	if ( isset($_POST['meal']) && $_POST['meal'] == "viewAllMeals" ) {
		header("Location: {$config->_rootUri}/list_meals");
		exit;
	} else {
		header("Location: {$config->_rootUri}/{$config->_thisScript}?meal={$_POST['meal']}&action=viewMeal");
		exit;
	}
}

# don't go forward if there isn't a meal or it isn't a number
if ( ! isset($_GET['meal']) || ! is_numeric($_GET['meal']) ) {
	$_SESSION['systemMsg'] = "<span class='msgError'>You must specify a numeric recipe ID.</span>";
	header("Location: {$config->_previousUri}");
	exit;
}

# we should be here with a GET request now.
if ( $_GET['meal'] == "0" ) {
	# the current meal is located in the session.  put it into a local variable
	# but only if it actually contains at least one item, otherwise send the user
	# back to wherever they were before
	if ( isset($_SESSION['currentMeal']) && count($_SESSION['currentMeal'])) {
		$currentMeal = $_SESSION['currentMeal'];
		$smarty->assign("mealDesc", "(Current recipe)");
	} else {
		$_SESSION['systemMsg'] = "<span class='msgError'>The current recipe has no items to view.</span>";
		header("Location: {$config->_previousUri}");
	}
} else {
	# if it's a saved meal we are loading then fetch it from
	# the database and load up the proper data into an array
	$sql = sprintf ("
		SELECT userMeals.*, userMeals.description AS mealDesc, userMealItems.*
		FROM userMeals LEFT JOIN userMealItems
			ON userMeals.id = userMealItems.meal
		WHERE userMeals.id = '%s'
		",
		$_GET['meal']
	);
	$db->Select($sql);
	if ( $db->_rowCount > 0 ) {
		# drop the meal desc. into the template
		$smarty->assign("mealDesc", $db->_rows[0]['mealDesc']);
		for ( $idx = 0; $idx < count($db->_rows); $idx++ ) {
			$currentMeal[$idx]['food'] = $db->_rows[$idx]['food'];
			$currentMeal[$idx]['weight'] = $db->_rows[$idx]['weight'];
			$currentMeal[$idx]['quantity'] = $db->_rows[$idx]['quantity'];
			$currentMeal[$idx]['description'] = $db->_rows[$idx]['description'];
		}
	} else {
		$_SESSION['systemMsg'] = "<span class='msgError'>Sorry, that recipe doesn't exist.</span>";
		header("Location: {$config->_previousUri}");
		exit;
	}
}

# increment the counter for this meal.  this counter could be used for all
# sorts of things, for example it is the basis of the "sort by popularity" option.
# the more people that select this item, the higher in the sort list it
# will appear.
# only increment the counter if the previous page was food_search.php because
# we don't want to increment the popularity while a user is just browsing around
# in their own foods, but only if they got here from a search.
if ( strpos($config->_previousUri, "food_search") ) {
	incrementPopularityCounter($_GET['meal'], "userMeals");
}

# this will be used to create various links in the page
$smarty->assign("meal", $_GET['meal']);

# this array will hold all of the data that we are going to print
# to the screen
$mealTable = array();

# add titles for the first couple columns
$mealData['columnTitles'][] = "Nutrient";
$mealData['columnTitles'][] = "Total";
$mealData['columnTitles'][] = "%DRI";

# because this data is tabular with the first column being nutrient names,
# and because we have no way of knowing beforehand which nutrients each
# food will add or share in the array we add every possible nutrient to the
# main food data array and setup what is essentially a grid for each
# nutrient and each food, which we will later go plugging in as we loop
# through each food in the meao. after we have iterated through each of the foods,
# adding its nutrients to the array, we will eliminate those nutrients with
# totals of 0.
# if the user is logged in then only lookup the nutrients they want to see
# unless they wanted to see all nutrients, else just grab every nutrient
if ( isLoggedIn() && (! isset($_GET['showall'])) ) {
	$sql = "
		SELECT nutrientDefs.nutr_no, nutrientDefs.units, nutrientDefs.nutrdesc
		FROM nutrientDefs RIGHT JOIN userNutrients
			ON nutrientDefs.nutr_no = userNutrients.nutrient
	";
} else {
	$sql = "SELECT nutr_no, units, nutrdesc FROM nutrientDefs";
}
$db->Select($sql);
foreach ( $db->_rows as $row ) {
	# setup a few variables regarding the foods
	$mealData['nutrients'][$row['nutr_no']]['nutrientName'] = $row['nutrdesc'];
	$mealData['nutrients'][$row['nutr_no']]['units'] = $row['units'];
	$mealData['nutrients'][$row['nutr_no']]['total'] = 0;
	$mealData['nutrients'][$row['nutr_no']]['percentDri'] = "--";
	# setup a placeholder for each nutrient quantity for each food, based on 
	# ndb_no ... by default just drop in a --.
	foreach ( $currentMeal as $food ) {
		$mealData['nutrients'][$row['nutr_no']]['quantities'][$food['food']] = "--";
	}
}

# now step through currentMeal and go adding things to the $mealData array
foreach ( $currentMeal as $food ) {
	
	# add the description of this food to the array
	$mealData['columnTitles'][] = "<a href='view_food?food={$food['food']}&amp;weight={$food['weight']}&amp;quantity={$food['quantity']}&amp;description={$food['description']}&amp;action=viewFood' class='whiteLink'>{$food['description']}</a>";

	# do things a little different if the user is logged in
	if ( isLoggedIn() ) {
		# the dris tables only give recommendations for ages 9-100, outside of that
		# we'll just use the average.
		if ( ($_SESSION['user']['age'] >= 9) && ($_SESSION['user']['age'] <= 100) ) {
			$age = $_SESSION['user']['age'];
		} else {
			$age = 0;
		}

		# NOTE: we must also check for and return IS NULL values in the table 'dris'
		# because most nutrients have no DRI and we need to return those as well
		if ( isset($_GET['showall']) ) {
			$smarty->assign("showAllNutrients", true);
			$sql = sprintf ("
				SELECT weights.gm_wgt, weights.amount, weights.msre_desc, nutrientDefs.nutrdesc, nutrientDefs.units,
					nutrientData.ndb_no, nutrientData.nutr_no, nutrientData.nutr_val, dris.dri
				FROM nutrientData LEFT JOIN weights
					ON nutrientData.ndb_no = weights.ndb_no
				LEFT JOIN nutrientDefs 
					ON nutrientData.nutr_no = nutrientDefs.nutr_no
				LEFT JOIN dris
					ON nutrientDefs.nutr_no = dris.nutr_no
				WHERE nutrientData.ndb_no = '%s'
					AND nutrientData.nutr_val > 0
					AND weights.ndb_no = '%s'
					AND weights.seq = '%s'
					AND ((dris.age_begin <= '%s' AND dris.age_end >= '%s') OR dris.id IS NULL)
					AND ((dris.gender = '%s') OR dris.id IS NULL)
				ORDER BY nutrientDefs.sr_order
				",
				$food['food'],
				$food['food'],
				$food['weight'],
				$age,
				$age,
				$_SESSION['user']['gender']
			);
		} else {
			$sql = sprintf ("
				SELECT weights.gm_wgt, weights.amount, weights.msre_desc, nutrientDefs.nutrdesc, nutrientDefs.units,
					nutrientData.ndb_no, nutrientData.nutr_no, nutrientData.nutr_val, dris.dri
				FROM nutrientData LEFT JOIN weights
					ON nutrientData.ndb_no = weights.ndb_no
				LEFT JOIN userNutrients
					ON nutrientData.nutr_no = userNutrients.nutrient
				LEFT JOIN nutrientDefs 
					ON nutrientData.nutr_no = nutrientDefs.nutr_no
				LEFT JOIN dris
					ON nutrientDefs.nutr_no = dris.nutr_no
				WHERE nutrientData.ndb_no = '%s'
					AND nutrientData.nutr_val > 0
					AND weights.ndb_no = '%s'
					AND weights.seq = '%s'
					AND userNutrients.user = '%s'
					AND ((dris.age_begin <= '%s' AND dris.age_end >= '%s') OR dris.id IS NULL)
					AND ((dris.gender = '%s') OR dris.id IS NULL)
				ORDER BY nutrientDefs.sr_order
				",
				$food['food'],
				$food['food'],
				$food['weight'],
				$_SESSION['user']['id'],
				$age,
				$age,
				$_SESSION['user']['gender']
			);
		}
	} else {
		if ( isset($_GET['showall']) ) {
			$smarty->assign("showAllNutrients", true);
			$sql = sprintf ("
				SELECT weights.gm_wgt, weights.amount, weights.msre_desc, nutrientDefs.nutrdesc, nutrientDefs.units,
					nutrientData.ndb_no, nutrientData.nutr_no, nutrientData.nutr_val, dris.dri
				FROM nutrientData LEFT JOIN weights
					ON nutrientData.ndb_no = weights.ndb_no
				LEFT JOIN nutrientDefs 
					ON nutrientData.nutr_no = nutrientDefs.nutr_no
				LEFT JOIN dris
					ON nutrientDefs.nutr_no = dris.nutr_no
				WHERE nutrientData.ndb_no = '%s'
					AND weights.ndb_no = '%s'
					AND weights.seq = '%s'
					AND nutrientData.nutr_val > 0
					AND (dris.gender = 'avg' OR dris.id IS NULL)
				ORDER BY nutrientDefs.sr_order
				",
				$food['food'],
				$food['food'],
				$food['weight']
			);
		} else {
			$sql = sprintf ("
				SELECT weights.gm_wgt, weights.amount, weights.msre_desc, nutrientDefs.nutrdesc, nutrientDefs.units,
					nutrientData.ndb_no, nutrientData.nutr_no, nutrientData.nutr_val, dris.dri
				FROM nutrientData LEFT JOIN weights
					ON nutrientData.ndb_no = weights.ndb_no
				LEFT JOIN nutrientDefs 
					ON nutrientData.nutr_no = nutrientDefs.nutr_no
				LEFT JOIN dris
					ON nutrientDefs.nutr_no = dris.nutr_no
				WHERE nutrientData.ndb_no = '%s'
					AND weights.ndb_no = '%s'
					AND weights.seq = '%s'
					AND nutrientData.nutr_val > 0
					AND nutrientDefs.is_default = '1'
					AND (dris.gender = 'avg' OR dris.id IS NULL)
				ORDER BY nutrientDefs.sr_order
				",
				$food['food'],
				$food['food'],
				$food['weight']
			);
		}
	}
	$db->Select($sql);
	$foodData = $db->_rows;

	# this number is the adjustment to each nutrient quantity reflecting
	# the ratio of the base amount relative to the quantity the user
	# selected.  since the value for amount  will be the same
	# for every selected record we just arbitrarily grab the value
	# from the first record in the returned set
	if ( $food['quantity'] ) {
		$factor = $food['quantity']/$foodData[0]['amount'];
	} else {
		$factor = 1;
	}

	# step through he results and add a value for nutrientQuantity to the
	# main mealData array based on nutr_no.  if the current nutr_no doesn't
	# exists in the array, then just skip it
	foreach ( $foodData as $nutrient ) {
		# check if this particular nutrient exists in the list of all nutrients
		# that we added earlier, if so, then plug in the data, if not it will
		# stay populated with the '--' that we added at the beginning
		if ( array_key_exists($nutrient['nutr_no'], $mealData['nutrients']) ) {
			$nutrientQuantity = round(($nutrient['nutr_val'] * ($nutrient['gm_wgt']/100) * $factor),1);

			# add this quantity to the array for this food and nutrient
			$mealData['nutrients'][$nutrient['nutr_no']]['quantities'][$food['food']] = "$nutrientQuantity{$nutrient['units']}";

			# add this amount to the total for this nutrient
			$mealData['nutrients'][$nutrient['nutr_no']]['total'] += $nutrientQuantity;

			# calculate the current %DRI if one exists, based on the current total
			if ( ! empty($nutrient['dri']) ) { 
				$mealData['nutrients'][$nutrient['nutr_no']]['percentDri'] =
					(round($mealData['nutrients'][$nutrient['nutr_no']]['total']/$nutrient['dri'],3) * 100);
			}
		}
	}
}

# step through all the nutrients in $mealData and eliminate all those
# rows that have a nutrient total of 0, as they are useless
foreach ( $mealData['nutrients'] as $nutr_no => $nutrient ) {
	if ( $nutrient['total'] == 0 ) {
		unset($mealData['nutrients'][$nutr_no]);
	}
}

$smarty->assign("mealData", $mealData);

# grab the various parts.  these sections are not printed to the screen
# but rather dumped into smarty variables that will simply be printed
# in the template, so the order doesn't matter here at the moment
require("header.php");
require("sidebar_left.php");
require("sidebar_right.php");
require("footer.php");

$smarty->display("view_meal.tpl");

?>
