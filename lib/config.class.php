<?php

# define a class for general site configuration
class siteConfig {

	# predefined variables
	# to change the database type see lib/database.class.php
	public $_dbHost;		# db host
	public $_dbName;		# name of the database
	public $_dbUser;		# database user
	public $_dbPass;		# database password

	public $_rootDir;		# root of the site from the perspective of the filesystem
	public $_rootURL;		# root URL of the site
	var $_imgDir;			# images directory
	var $_cssDir;			# css files directory
	var $_jsDir;			# javascript files directory

	# class constructor
	function __construct() {

		# nothing to do at the moment

	}

	##------------------------------------------------------------------##
	
	# add functions here
	
	##------------------------------------------------------------------##

}

?>
