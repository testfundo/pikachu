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

if ( isset($_POST['action']) && ($_POST['action'] == "editUser") ) {

	# validate the form .. this is already done through javascript, but we
	# better make sure

	# make sure they entered a username
	if ( isset($_POST['username']) && ("" == trim($_POST['username'])) ) {
		$_SESSION['systemMsg'] = "<span class='msgError'>You must specify a login name.</span>";
		header("Location: {$config->_previousUri}");
		exit;
	} else {
		$username = trim($_POST['username']);
		if ( strlen($username) < 5 ) {
			$_SESSION['systemMsg'] = "<span class='msgError'>The login name must contain at least 5 characters.</span>";
			header("Location: {$config->_previousUri}");
			exit;
		}
	}

	# if the user submitted a new password then validate the fields
	if ( isset($_POST['password']) && ("" != trim($_POST['password'])) ) {
		$password = trim($_POST['password']);
		if ( strlen($password) < 5 ) {
			$_SESSION['systemMsg'] = "<span class='msgError'>The password must contain at least 5 characters.</span>";
			header("Location: {$config->_previousUri}");
			exit;
		}
		if ( ! isset($_POST['password2']) || (trim($_POST['password']) != trim($_POST['password2'])) ) {
			$_SESSION['systemMsg'] = "<span class='msgError'>Your passwords do not match.</span>";
			header("Location: {$config->_previousUri}");
			exit;
		}
	}

	# make sure birthday is set and is valid
	if ( isset($_POST['birthday']) && ("" == trim($_POST['birthday'])) ) {
		$_SESSION['systemMsg'] = "<span class='msgError'>You must specify a birthday (even if it's not real).</span>";
		header("Location: {$config->_previousUri}");
		exit;
	} else {
		$birthday = strtotime($_POST['birthday']);
		if ( ! $birthday ) {
			$_SESSION['systemMsg'] = "<span class='msgError'>Your birthday doesn't appear to be an actual date.</span>";
			header("Location: {$config->_previousUri}");
			exit;
		}
	}

	# make sure the user doesn't already exist in the database
	$sql = sprintf ("
		SELECT * FROM users
		WHERE username = '%s'
			AND id != '%s'
		",
		trim($_POST['username']),
		$_SESSION['user']['id']
	);
	$db->Select($sql);
	if ( $db->_rowCount > 0 ) {
		$_SESSION['systemMsg'] = "<span class='msgError'>The login name you selected is already in use.  Please select another.</span>";
		header("Location: {$config->_previousUri}");
		exit;
	}

	# validation must have passed so let's edit the user.
	# the local variables were assigned during validation

	# if password is empty then the user didn't opt to change
	# their password
	if ( empty($password) ) {
		$sql = sprintf ("
			UPDATE users SET
				username = '%s',
				birthday = '%s',
				gender = '%s'
			WHERE id = '%s'
			",
			$username,
			$birthday,
			$_POST['gender'],
			$_SESSION['user']['id']
		);
	} else {
		$sql = sprintf ("
			UPDATE users SET
				username = '%s',
				password = '%s',
				birthday = '%s',
				gender = '%s'
			WHERE id = '%s'
			",
			$username,
			md5($password),
			$birthday,
			$_POST['gender'],
			$_SESSION['user']['id']
		);
	}
	$db->Modify($sql);
	if ( $db->_affectedRows == 1 ) {
		# dump the users new info into the session
		$_SESSION['user']['username'] = $username;
		$_SESSION['user']['birthday'] = $birthday;
		$_SESSION['user']['gender'] = $_POST['gender'];
		$_SESSION['systemMsg'] = "<span class='msgOkay'>Your profile was successfully updated.</span>";
	} else {
		$_SESSION['systemMsg'] = "<span class='msgError'>There was an error while updating the profile.</span>";
	}
	header("Location: {$config->_previousUri}");
	exit;
}

# a list of genders from which to populate the gender dropdown
$smarty->assign("genders", array("Female", "Male"));

# convert the user's birthday timestamp to human readable date
$smarty->assign("birthday", date("Y-m-d", $_SESSION['user']['birthday']));

# grab the various parts.  these sections are not printed to the screen
# but rather dumped into smarty variables that will simply be printed
# in the template, so the order doesn't matter here at the moment
include("header.php");
include("sidebar_left.php");
include("sidebar_right.php");
include("footer.php");

$smarty->display("edit_account.tpl");

