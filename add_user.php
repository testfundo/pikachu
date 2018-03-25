<?php 

/**
 * Copyright (c) 2007 Nathan Kinkade
 * 
 * This code is offered under an MIT (X11) license.  For more information
 * about the terms of this license see the file LICENSE included with this
 * software or visit: http://www.opensource.org/licenses/mit-license.php
 */

include "include/db.php";

# make sure that none of the fields are empty
if ($_POST['login']) {
  foreach ($_POST as $data) {
    if ($data == "") {
      $err = "<span class='errors'>You must fill in all fields!</span><br />\n";
      $reg_status == "failed";
      return;
    }
  }
}

# make sure that the passwords match
if ($_POST['passwd'] != $_POST['passwd2']) {
  $err = "<span class='errors'>Your passwords do not match.  Please try again.</span><br />\n";
  $reg_status == "failed";
  return;
}

# make sure that age is >0 && <100
if ($_POST['age'] < 1 || $_POST['age'] > 100) {
  $err = "<span class='errors'>Your age must be between 1 and 100 (years).</span><br />\n";
  $reg_status == "failed";
  return;
}

$lnk = db_connect();

# make sure that the login does not already exist
$res = db_query("SELECT id_users FROM users WHERE login = '{$_POST['login']}'");
if (db_num_rows($res)) {
  $err = "<span class='errors'>Login name '{$_POST['login']}' is already in use. Please select another.</span><br />\n";
  $reg_status == "failed";
  return;
}

$hashpwd = md5($_POST['passwd']);

$qry = "
	INSERT INTO users (login, passwd, age, gender) 
	VALUES ('{$_POST['login']}','$hashpwd', '{$_POST['age']}', '{$_POST['gender']}')
";

db_query($qry);

$reg_status = "ok_passed";

db_close($lnk);

?>
