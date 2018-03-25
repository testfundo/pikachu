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

# if the user isn't logged in then they can't be here
if ( ! isLoggedIn() ) {
	header("Location: {$config->_rootUri}/");
	exit;
}

# the user clicked "Save Changes" so we'll remove all of their previous nutrients
# and add the ones submitted with the form
if ( isset($_POST['setNutrients']) ) {
	$status = "true";
	$sql = sprintf ("
		DELETE FROM userNutrients
		WHERE user = '%s'
		",
		$_SESSION['user']['id']
	);
	$db->Modify($sql);
	if ( $db->_error ) {
		$status = "false";
	} else {
		if ( ! empty($_POST['nutrients']) ) {
			foreach ( $_POST['nutrients'] as $nutrient ) {
				$sql = sprintf ("
					INSERT INTO userNutrients(user, nutrient)
					VALUES ('%s','%s')
					",
					$_SESSION['user']['id'],
					$nutrient
				);
				$db->Modify($sql);
				if ( $db->_error ) {
					$status = "false";
				}
			}
		}
	}
	if ( $status == "true" ) {
		$_SESSION['systemMsg'] = "<span class='msgOkay'>Your nutrient list was updated successfully.</span>";
	} else {
		$_SESSION['systemMsg'] = "<span class='msgError'>There were errors saving your nutrient list.</span>";
	}
	# redirect the user back to this same page with a GET request
	header("Location: {$config->_rootUri}/nutrient_chooser");
	exit;
}

# grab the current nutrient list from the database and assign list to template
$sql = sprintf ("
	SELECT nutrientDefs.nutr_no, nutrientDefs.nutrdesc,
		userNutrients.nutrient AS myNutrient
	FROM nutrientDefs LEFT JOIN userNutrients
		ON nutrientDefs.nutr_no = userNutrients.nutrient AND userNutrients.user = '%s'
	ORDER BY COALESCE(myNutrient, '999999'), nutrientDefs.sr_order
	",
	$_SESSION['user']['id']
);
$db->Select($sql);
$smarty->assign("nutrients", $db->_rows);

# grab the various parts.  these sections are not printed to the screen
# but rather dumped into smarty variables that will simply be printed
# in the template, so the order doesn't matter here at the moment
require("header.php");
require("sidebar_left.php");
require("sidebar_right.php");
require("footer.php");

$smarty->display("nutrient_chooser.tpl");

?>
