<?php

# determine if the user exists in the database and if so then
# set a few session variables indicating such
function validateUser($user, $pass) {

	global $db;
	
	# make sure that these variables are empty and even unset
	if ( isset($_SESSION['auth']) ) {
		unset($_SESSION['auth']);
	}

	# encrypt password with a simple md5 hash
	$md5Password = md5($pass);

	$sql = "
		SELECT * FROM users
		WHERE username = '$user'
			AND password = '$md5Password'
	";
	$db->SelectOne($sql);
	if ( $db->_rowCount == 1 ) {
		# if one record was returned then a user matching the credentials they
		# supplied was found in the database.  give them access.
		$_SESSION['auth']['status'] = "access_granted";
		$_SESSION['auth']['ipaddress'] = $_SERVER['REMOTE_ADDR'];
		
		# dump all the users info into a session var, but unset the
		# value of the password field
		$_SESSION['user'] = $db->_row;
		unset($_SESSION['user']['password']);

		# determine the users age and put it in the session so that we don't have
		# to calculate it over and over again as they view things. 31536000 is the 
		# number of seconds in a year.
		$_SESSION['user']['age'] = floor((time() - $db->_row['birthday'])/31536000);
		return true;
	} else {
		# not a valid user (not found in db)
		$_SESSION['systemMsg'] = "<span class='msgError'>Login incorrect.</span>";
		return false;
	}

}

	##------------------------------------------------------------------##

# a simple function to check if a user is logged in which also verifies
# that the request came from the same IP address as the original login
function isLoggedIn() {

	if (
		isset($_SESSION['auth']) &&
		($_SESSION['auth']['status'] == "access_granted") &&
		($_SESSION['auth']['ipaddress'] == $_SERVER['REMOTE_ADDR'])
	) {
		return true;
	} else {
		return false;
	}

}

	##------------------------------------------------------------------##

# this function will check to see if a user is logged in, and if not will
# redirect the user to the index page with an error.  we could use the
# isLoggedIn() function above directly, but that would require some if/thens
# on the top of each script that required a login and then a rediction too.
# this function just bundles all that into a neat package
function loginRequired() {

	global $config;

	if ( isLoggedIn() ) {
		return true;
	} else {
		header("Location: {$config->_rootUri}/");
		exit;
		return false;
	}

}

	##------------------------------------------------------------------##

# get a food category's name based on that categories id in the database
function getFoodCategoryName($category) {

	global $db;

	$sql = "
		SELECT fdgrp_desc
		FROM foodCats
		WHERE fdgrp_cd = '$category'
	";
	$db->SelectOne($sql);
	if ( $db->_rowCount == 1 ) {
		return $db->_row['fdgrp_desc'];
	} else {
		return false;
	}

}

	##------------------------------------------------------------------##

# get a nutrients description based on that nutrients nutr_no in the database
function getNutrientName($nutrient) {

	global $db;

	$sql = "
		SELECT nutrdesc
		FROM nutrientDefs
		WHERE nutr_no = '$nutrient'
	";
	$db->SelectOne($sql);
	if ( $db->_rowCount == 1 ) {
		return $db->_row['nutrdesc'];
	} else {
		return false;
	}

}

	##------------------------------------------------------------------##

# get any favorite foods based on user id
function getFavoriteFoods($user) {

	global $db;

	# if the user hasn't marked any foods as favorites to
	# show in the left sidebar dropdown, then just grab the
	# first 15, else grab just their favorites
	$sql = "
		SELECT count(*) AS favCount
		FROM userFoods
		WHERE favorite = '1'
	";
	$db->SelectOne($sql);

	if ( $db->_row['favCount'] == "0" ) {
		$sql = "
			SELECT * FROM userFoods
			WHERE user = '$user'
			ORDER BY description
			LIMIT 15
		";
	} else {
		$sql = "
			SELECT * FROM userFoods
			WHERE user = '$user'
				AND favorite = '1'
			ORDER BY description
		";
	}

	$db->Select($sql);
	if ( $db->_rowCount > 0 ) { 
		return $db->_rows;
	} else {
		return false;
	}

}

	##------------------------------------------------------------------##

# get any favorite meals based on user id
function getFavoriteMeals($user) {

	global $db;

	# if the user hasn't marked any meals as favorites to
	# show in the left sidebar dropdown, then just grab the
	# first 15, else grab just their favorites
	$sql = "
		SELECT count(*) AS favCount
		FROM userMeals
		WHERE favorite = '1'
	";
	$db->SelectOne($sql);

	if ( $db->_row['favCount'] == "0" ) {
		$sql = "
			SELECT * FROM userMeals
			WHERE user = '$user'
			ORDER BY description
			LIMIT 15
		";
	} else {
		$sql = "
			SELECT * FROM userMeals
			WHERE user = '$user'
			ORDER BY description
		";
	}

	$db->Select($sql);
	if ( $db->_rowCount > 0 ) { 
		return $db->_rows;
	} else {
		return false;
	}

}

	##------------------------------------------------------------------##

# get all meals based on user id
function getUserMeals($user) {

	global $db;

	$sql = "
		SELECT * FROM userMeals
		WHERE user = '$user'
		ORDER BY description
	";

	$db->Select($sql);
	if ( $db->_rowCount > 0 ) { 
		return $db->_rows;
	} else {
		return false;
	}

}

	##------------------------------------------------------------------##

# get all diaries based on user id
function getUserDiaries($user) {

	global $db;

	$sql = "
		SELECT * FROM userDiaries
		WHERE user = '$user'
		ORDER BY description
	";

	$db->Select($sql);
	if ( $db->_rowCount > 0 ) { 
		return $db->_rows;
	} else {
		return false;
	}

}

	##------------------------------------------------------------------##

# removes an item from the current meal in $_SESSION['currentMeal']
function removeCurrentMealItem($mealItem) {

    $objResponse = new xajaxResponse();

	# remove the selected meal item from the session
	if ( array_key_exists($mealItem, $_SESSION['currentMeal']) ) {
		unset($_SESSION['currentMeal'][$mealItem]);
    	$objResponse->addRemove("currentMealItem-$mealItem");
		$objResponse->addAssign("systemMsgs", "innerHTML", "<span class='msgOkay'>The meal item was successfully removed.</span>");
		# if the session is empty then let the user know and remove
		# anything like links to "View meal", "Clear meal", etc.
		if ( count($_SESSION['currentMeal']) == 0 ) {
    		$objResponse->addAssign("divCurrentMeal", "innerHTML", "No items in meal.");
		}
	} else {
		$objResponse->addAssign("systemMsgs", "innerHTML", "<span class='msgError'>The specified meal item doesn't exist.</span>");
	}

	return $objResponse;

}

	##------------------------------------------------------------------##

# removes all meal items from the current meal ($_SESSION['currentMeal'])
function clearCurrentMeal() {

    $objResponse = new xajaxResponse();

	# unset the current meal session variable
	if ( isset($_SESSION['currentMeal']) ) {
		unset($_SESSION['currentMeal']);
	}

	# if it's still set here, then something went terribly wrong, otherwise
	# clear the div and let the user know.
	if ( isset($_SESSION['currentMeal']) ) {
		$objResponse->addAssign("systemMsgs", "innerHTML", "<span class='msgError'>There was an error. The current meal was not cleared.</span>");
	} else {
    	$objResponse->addAssign("divCurrentMeal", "innerHTML", "No items in meal.");
		$objResponse->addAssign("systemMsgs", "innerHTML", "<span class='msgOkay'>The current meal was successfully cleared.</span>");
	}

	return $objResponse;

}

	##------------------------------------------------------------------##

# create form for editing a meal
function loadMealToEdit($meal) {

	global $config, $db;

	$objResponse = new xajaxResponse();

	$mealToEdit = "";

	$sql = sprintf ("
		SELECT userMeals.*, userMeals.id AS mealId, userMeals.description as mealDesc,
			userMealItems.*, userMealItems.id as itemId, userMealItems.description as itemDesc
		FROM userMeals LEFT JOIN userMealItems
			ON userMeals.id = userMealItems.meal
		WHERE userMeals.id = '%s' AND user = '%s'
		",
		$meal,
		$_SESSION['user']['id']
	);
	$db->Select($sql);

	if ( $db->_rowCount == 0 ) {
		$mealToEdit = "<span class='msgError'>The selected saved meal doesn't exist.</span><br />\n&lt;= Select a meal to edit.";
		$objResponse->addAssign("editMeal","innerHTML", $mealToEdit);
		return $objResponse;
	} else {
		$mealItems = $db->_rows;
		$mealDesc = htmlspecialchars($mealItems[0]['mealDesc'], ENT_QUOTES);
		$mealToEdit .= <<<HTML
			<div>
				<strong>Meal name</strong>: <input type='text' name='mealDesc' value='$mealDesc' size='25' />
			</div>
			<div id='editMealItems' style='margin-bottom: 1ex; overflow: hidden;'>

HTML;
		# here we grab and add all the possible predefined quantites
		# so that the user can change the quantity from, for example,
		# '1 large banana (7")' to '2 medium banana (5")' or something
		# to that effect
		foreach ( $mealItems as $key => $mealItem ) {
			$sql = sprintf ("
				SELECT seq AS weight, msre_desc
				FROM weights
				WHERE ndb_no = '%s'
				",
				$mealItem['food']
			);
			$db->Select($sql);
			$itemQuantities = $db->_rows;
			$mealItems[$key]['quantities'] = $itemQuantities;

			# we will use this array later, in the Modify action below
			# to identify which meal items we need to update.
			$itemIds[] = $mealItem['itemId'];

			$mealItemDesc = htmlspecialchars($mealItem['itemDesc'], ENT_QUOTES);
			$mealToEdit .= <<<HTML
			<div id='mealItem-{$mealItem['id']}'>
				<div>
					<a href='{$_SERVER['REQUEST_URI']}' onclick='verifyRemoveMealItem("{$mealItem['id']}"); return false;'><img src='{$config->_imgUri}/remove.png' alt='Del' title='Remove: $mealItemDesc' /></a>
					=&gt; <strong>Meal item</strong>: <input type='text' name='mealItemDesc-{$mealItem['id']}' id='mealItemDesc-{$mealItem['id']}' value='$mealItemDesc' size='25' />
				</div>
				<div style='margin-top: 1ex; margin-left: 3ex;'>
					<div style='margin-left: 2ex; margin-bottom: 1ex;'>
						=&gt; <strong>Amount</strong>: <input type='text' name='mealItemQuantity-{$mealItem['id']}' id='mealItemQuantity-{$mealItem['id']}' value='{$mealItem['quantity']}' size='2' />
						<select name='mealItemWeight-{$mealItem['id']}'>

HTML;

			foreach ( $itemQuantities as $itemQuantity ) {
				if ( $itemQuantity['weight'] == $mealItem['weight'] ) {
					$mealToEdit .= "							<option value='{$itemQuantity['weight']}' selected='selected'>{$itemQuantity['msre_desc']}</option>\n";
				} else {
					$mealToEdit .= "							<option value='{$itemQuantity['weight']}'>{$itemQuantity['msre_desc']}</option>\n";
				}
			}

			$mealToEdit .= <<<HTML
						</select>
					</div>
				</div>
			</div>

HTML;
		}

		if ( $mealItem['favorite'] == "1" ) {
			$mealToEdit .= "					<div><strong>Favorite</strong>: <input type='checkbox' name='favorite' id='favorite' checked='checked' /></div>\n";
		} else {
			$mealToEdit .= "					<div><strong>Favorite</strong>: <input type='checkbox' name='favorite' id='favorite' /></div>\n";
		}

		# separate itemIds with a comma
		$mealItemIds = implode(",",$itemIds);
		$mealToEdit .= <<<HTML
		</div>
		<div style='margin-top: 2ex;'>
			<input type='hidden' name='meal' value='$meal' />
			<input type='hidden' name='mealItemIds' value='$mealItemIds' />
			<input type='hidden' name='action' value='' />
			<input type='submit' name='doModifyMeal' value='Modify' onclick='document.formEditMeal.action.value = "Modify";' />
			<input type='submit' name='doDeleteMeal' value='Delete' onclick='document.formEditMeal.action.value = "Delete";' />
		</div>

HTML;
	}

    $objResponse->addAssign("editMeal","innerHTML", $mealToEdit);

	return $objResponse;

}

	##------------------------------------------------------------------##

# create form for editing a food
function loadFoodToEdit($food) {

	global $config, $db;

    $objResponse = new xajaxResponse();
	$foodToEdit = "";

	$sql = sprintf ("
		SELECT * FROM userFoods
		WHERE id = '%s' AND user = '%s'
		",
		$food,
		$_SESSION['user']['id']
	);
	$db->SelectOne($sql);

	if ( $db->_rowCount == 0 ) {
		$foodToEdit = "<span class='msgError'>The selected saved food doesn't exist.</span><br />\n&lt;= Select a food to edit.";
    	$objResponse->addAssign("editFood","innerHTML", $foodToEdit);
		return $objResponse;
	} else {
		$foodItem = $db->_row;
		# here we grab and add all the possible predefined quantites
		# so that the user can change the quantity from, for example,
		# '1 large banana (7")' to '2 medium banana (5")' or something
		# to that effect
		$sql = sprintf ("
			SELECT seq AS weight, msre_desc
			FROM weights
			WHERE ndb_no = '%s'
			",
			$foodItem['food']
		);
		$db->Select($sql);
		$itemQuantities = $db->_rows;
		$foodItem['quantities'] = $itemQuantities;

		$foodDesc = htmlspecialchars($foodItem['description'], ENT_QUOTES);
		$foodToEdit .= <<<HTML
					<form action='edit_food' method='post' name='formEditFood' id='formEditFood' onsubmit='return validateEditFood("formEditFood");'>
						<div><strong>Food name</strong>: <input type='text' name='foodDesc' id='foodDesc' value='$foodDesc' size='25' /></div>
						<div style='margin-left: 3ex; margin-bottom: 1ex;'>
							=&gt; <strong>Amount</strong>: <input type='text' name='quantity' value='{$foodItem['quantity']}' size='2' />
							<select name='weight'>

HTML;

		foreach ( $itemQuantities as $itemQuantity ) {
			if ( $itemQuantity['weight'] == $foodItem['weight'] ) {
				$foodToEdit .= "								<option value='{$itemQuantity['weight']}' selected='selected'>{$itemQuantity['msre_desc']}</option>\n";
			} else {
				$foodToEdit .= "								<option value='{$itemQuantity['weight']}'>{$itemQuantity['msre_desc']}</option>\n";
			}
		}

		$foodToEdit .= <<<HTML
							</select>
						</div>
HTML;
		if ( $foodItem['favorite'] == "1" ) {
			$foodToEdit .= "					<div><strong>Favorite</strong>: <input type='checkbox' name='favorite' id='favorite' checked='checked' /></div>\n";
		} else {
			$foodToEdit .= "					<div><strong>Favorite</strong>: <input type='checkbox' name='favorite' id='favorite' /></div>\n";
		}
		$foodToEdit .= <<<HTML
						<div style='margin-top: 2ex;'>
							<input type='hidden' name='food' value='$food' />
							<input type='hidden' name='action' value='' />
							<input type='submit' name='doModifyFood' value='Modify' onclick='document.formEditFood.action.value = "Modify";' />
							<input type='submit' name='doDeleteFood' value='Delete' onclick='document.formEditFood.action.value = "Delete";' />
						</div>
					</form>

HTML;
	}

    $objResponse->addAssign("editFood","innerHTML", $foodToEdit);

	return $objResponse;

}

	##------------------------------------------------------------------##

# removes an item from a saved meal
function removeMealItem($mealItem) {

	global $db;

	$objResponse = new xajaxResponse();

	$sql = sprintf ("
		DELETE userMealItems.*
		FROM userMealItems INNER JOIN userMeals
			ON userMealItems.meal = userMeals.id
		INNER JOIN users
			ON userMeals.user = users.id
		WHERE users.id = '%s' AND userMealItems.id = '%s'
		",
		$_SESSION['user']['id'],
		$mealItem
	);
	$db->Modify($sql);

	if ( $db->_affectedRows == "1" ) {
		$objResponse->addRemove("mealItem-$mealItem");
		$objResponse->addAssign("systemMsgs", "innerHTML", "<span class='msgOkay'>The meal item was successfully removed.</span>");
		return $objResponse;
	} else {
		$objResponse->addAssign("systemMsgs", "innerHTML", "<span class='msgError'>There was an error.  The meal item was not meal.</span>");
		return $objResponse;
	}

}
	##------------------------------------------------------------------##

# removes an item from a diary
function removeDiaryItem($diaryItem) {

	global $db;

	$objResponse = new xajaxResponse();

	$sql = sprintf ("
		DELETE userDiaryItems.*
		FROM userDiaryItems INNER JOIN userDiaries
			ON userDiaryItems.diary = userDiaries.id
		WHERE userDiaries.user = '%s'
			AND userDiaryItems.id = '%s'
		",
		$_SESSION['user']['id'],
		$diaryItem
	);
	$db->Modify($sql);

	if ( $db->_affectedRows == "1" ) {
		$objResponse->addRemove("itemRow-$diaryItem");
		$objResponse->addAssign("systemMsgs", "innerHTML", "<span class='msgOkay'>The diary item was successfully deleted.</span>");
		return $objResponse;
	} else {
		$objResponse->addAssign("systemMsgs", "innerHTML", "<span class='msgError'>There was an error.  The diary item was not deleted.</span>");
		return $objResponse;
	}

}

	##------------------------------------------------------------------##

# checks to see if a username already exists in the db during the registration process
function usernameExists($username) {

	global $db;

	$objResponse = new xajaxResponse();

	$sql = sprintf ("
		SELECT username FROM users
		WHERE username = '%s'
		",
		trim($username)
	);
	$db->Select($sql);
	if ( $db->_rowCount > 0 ) {
		$alert = "The login name you selected is already in use.  Please select another.";
		$objResponse->addAlert($alert);
		$objResponse->addScript("xajax.$('formRegisterUser').username.focus();");
		$objResponse->addScript("return false;");
	} else {
		$objResponse->addScript("xajax.$('formRegisterUser').submit();");
	}

	return $objResponse;

}

	##------------------------------------------------------------------##

# increment the "popularity" counter for the supplied ndb_no.
# table will be: foodDesc, userFoods, or userMeals ... we use it to determine
# which counter to increment.  the name corresponds to the relevant table
# so we can just plug it into the query directly

function incrementPopularityCounter($id, $table) {

	global $db;

	# we keep track of which items a user has selected during a given
	# session and we only allow a popularity counter to be incremented
	# for a given item once per session.  this isn't fool-proof, but it
	# should help to stem someone repeatedly clicking on the same item
	# in order to raise it's popularity artificially .. at least it will
	# be more of a hassle for someone to do it.
	if ( ! empty($_SESSION['popularity']) && in_array("$id{$table}", $_SESSION['popularity']) ) {
		# this user has already selected this item during this session
		# so don't increment the popularity counter
		return false;
	}

	switch ( $table ) {
		case "foodDescs":
			$idField = "ndb_no";
			break;
		case "userFoods":
			$idField = "id";
			break;
		case "userMeals":
			$idField = "id";
			break;
		default:
			# the table isn't valid
			return false;
	}

	$sql = sprintf ("
		UPDATE %s
			SET popularity = (popularity + 1)
		WHERE %s = '%s'
		",
		$table,
		$idField,
		$id
	);
	$db->Modify($sql);

	# add this food to the list so that this user can't trigger another
	# popularity increment for this food during this session.
	$_SESSION['popularity'][] = "$id{$table}"; 

	return true;

}

	##------------------------------------------------------------------##

?>
