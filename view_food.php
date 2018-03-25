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

# first implement the PRG (Post->Redirect->Get) method here so that
# users can use the back button freely without browser warnings.
# next, if the action is viewFood is posted then this signifies that the user
# is viewing some type of saved food item in which case a query 
# string should be posted under variable $queryString which we just
# append to a browser redirect. else if getFood is set this
# means that they got here through the normal food search which means
# that we need to formulate the query string from the submitted 
# form variables. else, they got here in some non-standard way, in 
# which case we just send them home
if ( isset($_POST['action']) && ($_POST['action'] == "viewFood") ) {
	# if $_POST['queryString'] = "viewAllFoods" then the user has selected
	# to view a list of all their saved foods and not just one particular
	# food, so we'll forward them to the appropriate page.  this is here
	# because the easiest way to give the user the option to see all their
	# foods was to simply stick an option in the "Favorites" menu in the
	# left sidebar, and that form directs the user here.
	if ( isset($_POST['queryString']) && $_POST['queryString'] == "viewAllFoods" ) {
		header("Location: {$config->_rootUri}/list_foods");
		exit;
	} else {
		header("Location: {$config->_rootUri}/{$config->_thisScript}?{$_POST['queryString']}");
		exit;
	}
} elseif ( isset($_POST['action']) && ($_POST['action'] == "getFood") ) {
	# if the user is sumbitting their own quantity then use that data
	# else we just use 0, which is a flag to the system to use the
	# system's predefined weight and/or quantity for a given food
	if ( $_POST['quantitySource'] == "userdefined" ) {
		$quantity = "{$_POST['quantity']}";
		# don't let the user enter a quantity less than 0 or a
		# non-numeric quantity
		if ( ($quantity < 0) || (! is_numeric($quantity)) ) {
			$quantity = 0;
		}
		$weight = $_POST['userdefinedWeight'];
	} else {
		$quantity = 0;
		$weight = $_POST['predefinedWeight'];
	}
	$queryString = "food={$_POST['food']}&weight=$weight&quantity=$quantity&action=getFood";
	header("Location: {$config->_rootUri}/{$config->_thisScript}?$queryString");
	exit;
}


# don't go forward unless all the required variables are set
if ( 
	(! isset($_GET['food'])) ||
	(! isset($_GET['weight'])) ||
	(! isset($_GET['quantity']))
) {
	$_SESSION['systemMsg'] = "<span class='msgError'>You must specify a food, weight and quantity.</span>";
	header("Location: {$config->_previousUri}");
	exit;
}


# if there was a user submitted name, as would be the case
# with viewing a saved food, then display it along with the
# actual food name in the database
if ( isset($_GET['description']) ) {
	$smarty->assign("foodDesc", $_GET['description']);
}

# put the values into the smarty template
$smarty->assign("food", $_GET['food']);
$smarty->assign("weight", $_GET['weight']);
$smarty->assign("quantity", $_GET['quantity']);

# if the user is logged in then restrict the nutrient list according to the users
# entries in the table userNutrients, and also taking into account the users gender
# and age.
if ( isLoggedIn() ) {
	# NOTE: we must also check for and return IS NULL values in the table 'dris'
	# because most nutrients have no DRI and we need to return those as well
	if ( isset($_GET['showall']) ) {
		$smarty->assign("showAllNutrients", true);
		$sql = sprintf ("
			SELECT foodDescs.long_desc, foodDescs.comname, CONCAT(foodDescs.long_desc,
				foodDescs.comname) AS foodDesc, foodDescs.sciname,
				weights.gm_wgt, weights.amount, weights.msre_desc,
				nutrientDefs.nutrdesc, nutrientDefs.units,
				nutrientData.nutr_no, nutrientData.nutr_val, dris.dri
			FROM foodDescs LEFT JOIN weights
				ON foodDescs.ndb_no = weights.ndb_no
			LEFT JOIN nutrientData 
				ON foodDescs.ndb_no = nutrientData.ndb_no
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
			$_GET['food'],
			$_GET['food'],
			$_GET['weight'],
			$_SESSION['user']['age'],
			$_SESSION['user']['age'],
			$_SESSION['user']['age'] < 9 ? 'avg' : $_SESSION['user']['gender']
		);
	} else {
		$sql = sprintf ("
			SELECT foodDescs.long_desc, foodDescs.comname, CONCAT(foodDescs.long_desc,
				foodDescs.comname) AS foodDesc, foodDescs.sciname,
				weights.gm_wgt, weights.amount, weights.msre_desc,
				nutrientDefs.nutrdesc, nutrientDefs.units,
				nutrientData.nutr_no, nutrientData.nutr_val, dris.dri
			FROM foodDescs LEFT JOIN weights
				ON foodDescs.ndb_no = weights.ndb_no
			LEFT JOIN nutrientData
				ON foodDescs.ndb_no = nutrientData.ndb_no
			LEFT JOIN userNutrients
				ON nutrientData.nutr_no = userNutrients.nutrient
			LEFT JOIN nutrientDefs 
				ON nutrientData.nutr_no = nutrientDefs.nutr_no
			LEFT JOIN dris
				ON nutrientData.nutr_no = dris.nutr_no
			WHERE nutrientData.ndb_no = '%s'
				AND nutrientData.nutr_val > 0
				AND weights.ndb_no = '%s'
				AND weights.seq = '%s'
				AND userNutrients.user = '%s'
				AND ((dris.age_begin <= '%s' AND dris.age_end >= '%s') OR dris.id IS NULL)
				AND ((dris.gender = '%s') OR dris.id IS NULL)
			ORDER BY nutrientDefs.sr_order
			",
			$_GET['food'],
			$_GET['food'],
			$_GET['weight'],
			$_SESSION['user']['id'],
			$_SESSION['user']['age'],
			$_SESSION['user']['age'],
			$_SESSION['user']['age'] < 9 ? 'avg' : $_SESSION['user']['gender']
		);
	}

	# since the user is logged in, add all of their saved meals to the template, so 
	# that they can add this food to any saved meal
	$smarty->assign("myMeals", getUserMeals($_SESSION['user']['id']));

} else {

	# NOTE: we must also check for and return IS NULL values in the table 'dris'
	# because most nutrients have no DRI and we need to return those as well
	if ( isset($_GET['showall']) ) {
		$smarty->assign("showAllNutrients", true);
		$sql = sprintf ("
			SELECT foodDescs.long_desc, foodDescs.comname, CONCAT(foodDescs.long_desc,
				foodDescs.comname) AS foodDesc, foodDescs.sciname,
				weights.gm_wgt, weights.amount, weights.msre_desc,
				nutrientDefs.nutrdesc, nutrientDefs.units,
				nutrientData.nutr_no, nutrientData.nutr_val, dris.dri
			FROM foodDescs LEFT JOIN weights
				ON foodDescs.ndb_no = weights.ndb_no
			LEFT JOIN nutrientData 
				ON foodDescs.ndb_no = nutrientData.ndb_no
			LEFT JOIN nutrientDefs 
				ON nutrientData.nutr_no = nutrientDefs.nutr_no
			LEFT JOIN dris
				ON nutrientDefs.nutr_no = dris.nutr_no
			WHERE nutrientData.ndb_no = '%s'
				AND nutrientData.nutr_val > 0
				AND weights.ndb_no = '%s'
				AND weights.seq = '%s'
				AND ((dris.age_begin <= '30' AND dris.age_end >= '30') OR dris.id IS NULL)
				AND ((dris.gender = 'male') OR dris.id IS NULL)
			ORDER BY nutrientDefs.sr_order
			",
			$_GET['food'],
			$_GET['food'],
			$_GET['weight']
		);
	} else {
		# show the user the default nutrients
		$sql = sprintf ("
			SELECT foodDescs.long_desc, foodDescs.comname, CONCAT(foodDescs.long_desc,
				foodDescs.comname) AS foodDesc, foodDescs.sciname,
				weights.gm_wgt, weights.amount, weights.msre_desc,
				nutrientDefs.nutrdesc, nutrientDefs.units,
				nutrientData.nutr_no, nutrientData.nutr_val, dris.dri
			FROM foodDescs LEFT JOIN weights
				ON foodDescs.ndb_no = weights.ndb_no
			LEFT JOIN nutrientData
				ON foodDescs.ndb_no = nutrientData.ndb_no
			LEFT JOIN nutrientDefs 
				ON nutrientData.nutr_no = nutrientDefs.nutr_no
			LEFT JOIN dris
				ON nutrientData.nutr_no = dris.nutr_no
			WHERE nutrientData.ndb_no = '%s'
				AND nutrientData.nutr_val > 0
				AND weights.ndb_no = '%s'
				AND weights.seq = '%s'
				AND nutrientDefs.is_default = '1'
				AND ((dris.age_begin <= '30' AND dris.age_end >= '30') OR dris.id IS NULL)
				AND ((dris.gender = 'male') OR dris.id IS NULL)
			ORDER BY nutrientDefs.sr_order
			",
			$_GET['food'],
			$_GET['food'],
			$_GET['weight']
		);
	}

}
$db->Select($sql);
# if for some reason the query returns no rows, then drop them where they 
# came from with an appropriate error message
if ( $db->_rowCount > 0 ) {
	$foodData = $db->_rows;
} else {
	$_SESSION['systemMsg'] = "<span class='msgError'>The food you specified doesn't seem to exist.</span>";
	header("Location: {$config->_previousUri}/");
	exit;
}

# increment the counter for this food.  this counter could be used for all
# sorts of things, for example it is the basis of the "sort by popularity" option.
# the more people that select this item, the higher in the sort list it
# will appear.
# only increment the counter if the previous page was food_search.php because
# we don't want to increment the popularity while a user is just browsing around
# in their own foods, but only if they got here from a search.
if ( strpos($config->_previousUri, "food_search.php") ) {
	incrementPopularityCounter($_GET['userFoodsId'], "userFoods");
}

# this number is the adjustment to each nutrient quantity reflecting
# the ratio of the base amount relative to the quantity the user
# selected.  since amount and gm_wt and long_desc will be the same
# for every selected record we just arbitrarily grab the values
# from the first record in the returned set
if ( $_GET['quantity'] ) {
	$quantity = $_GET['quantity'];
	$factor = ($_GET['quantity']/$foodData[0]['amount']);
} else {
	# quantity now becomes the predefined amount and factor is 1
	$quantity = $foodData[0]['amount'];
	$factor = 1;
}

# adjusted gram weight of the food
$smarty->assign("gramWeight", $foodData[0]['gm_wgt'] * $factor);

# mulitpling the number by 1 will simply return a number
# that has any padding of 0s from either side of the number removed
# this is useful because frequently numbers in the database
# are stored to the thousands decimal place, even though they
# may not contain values in those places e.g. 5.200.  this is
# purely aesthetic, as I think it looks trashy to have extra
# zeros padded on the end
$quantity = ($quantity * 1);
$smarty->assign("quantity", $quantity);

# step through the results and add a value for nutrientQuantity and
# percentDri to each record
for ( $idx = 0; $idx < count($foodData); $idx++ ) {
	$nutrientQuantity = round(($foodData[$idx]['nutr_val'] * ($foodData[$idx]['gm_wgt']/100) * $factor),1);
	if ( ! empty($foodData[$idx]['dri']) ) { 
		$percentDri = ( round($nutrientQuantity/$foodData[$idx]['dri'],3) * 100 );
	} else {
		$percentDri = "--";
	}
	$foodData[$idx]['nutrientQuantity'] = $nutrientQuantity;
	$foodData[$idx]['percentDri'] = $percentDri;

	# while we are looping through the records, do this:
	# if there was no 'comname' for the food, then just display the
	# field 'long_desc', else display the concatenated field 'foodDesc'
	# NOTE: it would be possible and easy to concatenate the 'long_desc'
	# and 'comname' fields at the time of display, but for future growth
	# possibilities and because we reference 'foodDesc' many times below
	# it seems just as well to have a concatenated field in the result set
	if ( "" == trim($foodData[$idx]['comname']) ) {
		$foodData[$idx]['foodDesc'] = $foodData[$idx]['long_desc'];
	}
}

$smarty->assign("foodData", $foodData);

# grab the various parts.  these sections are not printed to the screen
# but rather dumped into smarty variables that will simply be printed
# in the template, so the order doesn't matter here at the moment
require("header.php");
require("sidebar_left.php");
require("sidebar_right.php");
require("footer.php");

$smarty->display("view_food.tpl");

?>
