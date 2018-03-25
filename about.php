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

# grab the various parts
include("header.php");
include("sidebar_left.php");
include("sidebar_right.php");
include("footer.php");

# display the page
$smarty->display("about.tpl");

?>
