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

# implement the PRG (Post->Redirect->Get) method here so that
# users can use the back button freely without browser warnings.
if ( isset($_POST['action']) && ($_POST['action'] == "viewDiary") ) {
	# if $_POST['diary'] = "viewAllDiaries" then the user has selected
	# to view a list of all their diaries and not just one particular
	# diary, so we'll forward them to the appropriate page.  this is here
	# because the easiest way to give the user the option to see all their
	# diaries was to simply stick an option in the "Favorites" menu in the
	# left sidebar, and that form directs the user here.
	if ( $_POST['diary'] == "viewAllDiaries" ) {
		header("Location: {$config->_rootUri}/list_diaries");
		exit;
	} else {
		$queryString = "diary={$_POST['diary']}&action=viewDiary";
		header("Location: {$config->_rootUri}/{$config->_thisScript}?$queryString");
		exit;
	}
}

# don't go forward unless a diary was specified
if ( ! isset($_GET['diary']) ) {
	$_SESSION['systemMsg'] = "<span class='msgError'>You must specify a diary.</span>";
	header("{$config->_previousUri}");
	exit;
}

# initialize an array for all diary items
$diaryItems = array();

# initialize an array for the nutrition summary data
$summaryData = array();

$sql = sprintf ("
	SELECT * FROM userDiaries
	WHERE id = '%s' AND user = '%s'
	",
	$_GET['diary'],
	$_SESSION['user']['id']
);
$db->SelectOne($sql);

if ( $db->_rowCount != 1 ) {
	$_SESSION['systemMsg'] = "<span class='msgError'>The specified diary doesn't exist.</span>";
	header("Location: {$config->_previousUri}");
	exit;
} else {
	# add the diary name to the template
	$smarty->assign("diaryDesc", $db->_row['description']);

	# we browse diaries per day.  this is a way to page the data and make it more
	# manageable for people.  we will only show diary entries for the given day.
	# if a day was not submitted, then use the present day.  here we determine
	# which diary items to extract based on timestamp
	if ( isset($_GET['date']) ) {
		list($year,$month,$day) = explode("-", $_GET['date']);
		$startTime = mktime(0, 0, 0, $month, $day, $year);
		# there are 86400 seconds in a day, so just add this total to the
		# startTime, and this should effective cover the entire day
		$endTime = ($startTime + 86400);
		# format the date in the way that the calendar scripts understand
		# so that we can fix the initial date of the calendar
		$smarty->assign("calendarStartDate", "$month/$day/$year");
	} else {
		# use today.
		$startTime = mktime(0, 0, 0);
		# there are 86400 seconds in a day, so just add this total to the
		# startTime, and this should effective cover the entire day
		$endTime = ($startTime + 86400);
		# format the date in the way that the calendar scripts understand
		# so that we can fix the initial date of the calendar
		$smarty->assign("calendarStartDate", date("m/d/Y"));
	}
	
	# get the items associated with the diary for the selected day.
	$sql = sprintf ("
		SELECT * FROM userDiaryItems
		WHERE diary = '%s'
			AND timestamp >= '%s'
			AND timestamp <= '%s'
		ORDER BY timestamp
		",
		$_GET['diary'],
		$startTime,
		$endTime
	);
	$db->Select($sql);

	if ( $db->_rowCount > 0 ) {
		$diaryItems = $db->_rows;
		# figure the first and last years of the diary.  this will be used
		# to restrict which years are displayed on the diary navigation calendar
		$lastIndex = (count($diaryItems) - 1);
		$smarty->assign("firstYear", date("Y", $diaryItems[0]['timestamp']));
		$smarty->assign("lastYear", date("Y", $diaryItems[$lastIndex]['timestamp']));

		$summaryItems = array();

		foreach ( $diaryItems as $key => $diaryItem ) {
			# convert the timestamps to human readable dates
			$date = date("D, M jS, Y,  g:iA", $diaryItem['timestamp']);
			$diaryItems[$key]['date'] = $date;

			# if it happens to be a food or meal then break out the various
			# fields from the data field
			if ( $diaryItem['type'] == "Food" ) {
				list($food, $weight, $quantity, $description) = explode("::", $diaryItem['data']);
				$diaryItems[$key]['description'] = $description;
				$diaryItems[$key]['uri'] = "food=$food&weight=$weight&quantity=$quantity&description=$description&action=viewFood";

				# add the food to the summaryItems array
				$thisFood = array("food" => $food, "weight" => $weight, "quantity" => $quantity);
				$summaryItems[] = $thisFood;
			}

			if ( $diaryItem['type'] == "Meal" ) {
				list($meal, $description) = explode("::", $diaryItem['data']);
				$diaryItems[$key]['description'] = $description;
				$diaryItems[$key]['uri'] = "meal=$meal&description=$description&action=viewMeal";

				# add each food in the meal to the summaryItems array
				$sql = sprintf ("
					SELECT food, weight, quantity
					FROM userMealItems INNER JOIN userMeals
						ON userMealItems.meal = userMeals.id
					WHERE userMeals.id = '%s'
					",
					$meal
				);
				$db->Select($sql);
				if ( $db->_rowCount > 0 ) {
					foreach ( $db->_rows as $row ) {
						$thisFood = array("food" => $row['food'], "weight" => $row['weight'], "quantity" => $row['quantity']);
						$summaryItems[] = $thisFood;
					}
				}
			}

			if ( $diaryItem['type'] == "Note" ) {
				$diaryItems[$key]['description'] = $diaryItems[$key]['data'];

				# while we are looping the records, convert any carriage returns
				# in the data of Notes to <br />s so that the format is how the
				# user entered it into the textarea originally
				$diaryItems[$key]['data'] = preg_replace("/\n/", "<br />", $diaryItems[$key]['data']);
			}

		}

		$smarty->assign("diaryItems", $diaryItems);

		# Begin summary of diary data

		# because this data is tabular with the first column being nutrient names,
		# and because we have no way of knowing beforehand which nutrients each
		# food will add or share in the array, we add every possible nutrient to the
		# main summary data array and setup what is essentially a grid for each
		# nutrient which we will later go plugging in as we loop
		# through each food in the summary. after we have iterated through each of the foods,
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
			$summaryData[$row['nutr_no']]['nutrientName'] = $row['nutrdesc'];
			$summaryData[$row['nutr_no']]['units'] = $row['units'];
			$summaryData[$row['nutr_no']]['total'] = 0;
			$summaryData[$row['nutr_no']]['percentDri'] = "--";
		}

		# now step through summaryItems and go adding things to the $summaryData array
		foreach ( $summaryItems as $food ) {
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
				#		if ( array_key_exists($nutrient['nutr_no'], $summaryData) ) {
					$nutrientQuantity = round(($nutrient['nutr_val'] * ($nutrient['gm_wgt']/100) * $factor),1);
		
					# add this amount to the total for this nutrient
					$summaryData[$nutrient['nutr_no']]['total'] += $nutrientQuantity;
		
					# calculate the current %DRI if one exists, based on the current total
					if ( ! empty($nutrient['dri']) ) { 
						$summaryData[$nutrient['nutr_no']]['percentDri'] =
							(round($summaryData[$nutrient['nutr_no']]['total']/$nutrient['dri'],3) * 100);
					}
					#	}
			}
		}
		
		# step through all the nutrients in $summaryData and eliminate all those
		# rows that have a nutrient total of 0, as they are useless
		foreach ( $summaryData as $nutr_no => $nutrient ) {
			if ( $nutrient['total'] == 0 ) {
				unset($summaryData[$nutr_no]);
			}
		}
		
		# if there is any summary data then assign it to the template
		if ( ! empty($summaryData) != 0 ) {
			$smarty->assign("summaryData", $summaryData); 
		}

		# End summary of diary data

	}
}

# grab the various parts.  these sections are not printed to the screen
# but rather dumped into smarty variables that will simply be printed
# in the template, so the order doesn't matter here at the moment
require("header.php");
require("sidebar_left.php");
require("sidebar_right.php");
require("footer.php");

$smarty->display("view_diary.tpl");

?>
