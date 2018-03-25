<?php

/**
 * Copyright (c) 2007 Nathan Kinkade
 * 
 * This code is offered under an MIT (X11) license.  For more information
 * about the terms of this license see the file LICENSE included with this
 * software or visit: http://www.opensource.org/licenses/mit-license.php
 */

# Directory where USDA update files are located
$updatesDir = "./sr20_updates";

# Where to log errors and stats
$fh_log = fopen("./sr20_updates.log", "a");

# Fields are delimited with this character
$delimiter = "^";

# Fields are optionally enclosed between this character
$enclosure = "~";

# site constants that don't need to be interpolated in strings and/or
# are more sensitive will be setup as constants
define("DBHOST", "localhost"); # database host
define("DBNAME", "nutridb_sr20"); # database name
define("DBUSER", "root"); # database user
define("DBPASS", ""); # database password

require("../lib/database.class.php"); # database class  

# instantiate the database object
$db = new Database();

?>
