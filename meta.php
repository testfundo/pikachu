<?php

/**
 * Copyright (c) 2007 Nathan Kinkade
 * 
 * This code is offered under an MIT (X11) license.  For more information
 * about the terms of this license see the file LICENSE included with this
 * software or visit: http://www.opensource.org/licenses/mit-license.php
 */

# this script perhaps doesn't have the most apt name, but it made sense
# to me at the time I first named it.
# the purpose of this file is to allow adding custom <head>
# items based on the current script/page.  there is a common set of
# headers that will be the same for all pages and these are defined
# in the variable $commonHeaders.  for example, some pages will need
# some special javascript, but we may not want to add the overhead
# of loading the javascript into pages that don't require it.  this
# can be handled here.  we may also be able to add page-specific
# <title>'s.  at the stage that this script is included we should
# have access to all the $config variables and the database, as well
# as any user submitted data: $_POST, $_GET, etc.

# headers common to every page
$commonHeaders = <<<HEADERS
	<title>NutriDB Nutrition Database: food and recipe nutrition calculator</title>
	<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
	<meta name='keywords' content='nutrition, database, food diary, food content, recipe, calculator' />
	<meta name='description' content='A database for calculating nutrition information for foods and recipes.' />
	<link rel='stylesheet' media='all' type='text/css' href='{$config->_cssUri}/site.css' />
	<link rel='stylesheet' media='all' type='text/css' href='{$config->_cssUri}/2ColumnLayout.css' title='2Column' />
	<script type='text/javascript' src='{$config->_jsUri}/site.js'></script>
	<script type='text/javascript' src='{$config->_jsUri}/standard.js'></script>

HEADERS;

# add the generated XAJAX javascript to the headers
$commonHeaders .= str_replace("&", "&amp;", $xajax->getJavascript($config->_jsUri));

switch ( $config->_thisScript ) {

	case "view_food":
		$myHeaders = <<<HEADERS

	<link href='{$config->_jsUri}/jscalendar/calendar-system.css' type='text/css' rel='stylesheet' />
	<script type='text/javascript' src='{$config->_jsUri}/jscalendar/calendar.js'></script>
	<script type='text/javascript' src='{$config->_jsUri}/jscalendar/lang/calendar-en.js'></script>
	<script type='text/javascript' src='{$config->_jsUri}/jscalendar/calendar-setup.js'></script>
$commonHeaders

HEADERS;
		break;

	case "view_meal":
		$myHeaders = <<<HEADERS

	<link href='{$config->_jsUri}/jscalendar/calendar-system.css' type='text/css' rel='stylesheet' />
	<script type='text/javascript' src='{$config->_jsUri}/jscalendar/calendar.js'></script>
	<script type='text/javascript' src='{$config->_jsUri}/jscalendar/lang/calendar-en.js'></script>
	<script type='text/javascript' src='{$config->_jsUri}/jscalendar/calendar-setup.js'></script>
$commonHeaders

HEADERS;
		break;

	case "view_diary":
		$myHeaders = <<<HEADERS

	<link href='{$config->_jsUri}/jscalendar/calendar-system.css' type='text/css' rel='stylesheet' />
	<script type='text/javascript' src='{$config->_jsUri}/jscalendar/calendar.js'></script>
	<script type='text/javascript' src='{$config->_jsUri}/jscalendar/lang/calendar-en.js'></script>
	<script type='text/javascript' src='{$config->_jsUri}/jscalendar/calendar-setup.js'></script>
$commonHeaders

HEADERS;
		break;

	case "register":
		$myHeaders = <<<HEADERS

	<link href='{$config->_jsUri}/jscalendar/calendar-system.css' type='text/css' rel='stylesheet' />
	<script type='text/javascript' src='{$config->_jsUri}/jscalendar/calendar.js'></script>
	<script type='text/javascript' src='{$config->_jsUri}/jscalendar/lang/calendar-en.js'></script>
	<script type='text/javascript' src='{$config->_jsUri}/jscalendar/calendar-setup.js'></script>
$commonHeaders

HEADERS;
		break;

	case "edit_account":
		$myHeaders = <<<HEADERS

	<link href='{$config->_jsUri}/jscalendar/calendar-system.css' type='text/css' rel='stylesheet' />
	<script type='text/javascript' src='{$config->_jsUri}/jscalendar/calendar.js'></script>
	<script type='text/javascript' src='{$config->_jsUri}/jscalendar/lang/calendar-en.js'></script>
	<script type='text/javascript' src='{$config->_jsUri}/jscalendar/calendar-setup.js'></script>
$commonHeaders

HEADERS;
		break;

	default:
		$myHeaders = $commonHeaders;

}

?>
