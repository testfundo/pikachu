#!/usr/bin/php

<?php

/**
 * Script to update nutridb table foodDescs which corresponds
 * to USDA file FOOD_DES, which corresponds to update files
 * DEL_FOOD.txt and ADD_FOOD.txt and CHG_FOOD.txt.
 *
 * NOTE: This script relies on the fact that updates files
 * are named as they were from the SR19 -> SR20 update.
 */
 
require("config.php");

$fh_del = fopen("{$updatesDir}/DEL_FOOD.txt", "r");
$fh_add = fopen("{$updatesDir}/ADD_FOOD.txt", "r");
$fh_chg = fopen("{$updatesDir}/CHG_FOOD.txt", "r");

# First handle deletions
fwrite($fh_log, "### PROCESSING DEL_FOOD.txt ###\n");
$idx = 0;
while ( ($row = fgetcsv($fh_del, 0, $delimiter, $enclosure)) !== FALSE ) {
	$sql = sprintf("
		UPDATE foodDescs
		SET usda_status = 'deleted'
		WHERE ndb_no = '%s'
		",
		$row[0]
	);
	$db->Modify($sql);
	if ( $db->_error ) {
		fwrite($fh_log, "\tERROR deactivating ndb_no {$row[0]}: $db->_error\n");
	} elseif ( $db->_affectedRows == 0 ) {
		fwrite($fh_log, "\tWARNING: ndb_no {$row[0]} was not found in the database.\n");
	} else {
		$idx++;
	}
}
fwrite($fh_log, "\tDeactivated $idx records from table foodDescs.\n");

# Now handle additions
fwrite($fh_log, "### PROCESSING ADD_FOOD.txt ###\n");
$idx = 0;
while ( ($row = fgetcsv($fh_add, 0, $delimiter, $enclosure)) !== FALSE ) {
	# Don't add the record if this ndb_no already exists
	$sql = sprintf("
		SELECT ndb_no FROM foodDescs
		WHERE ndb_no = '%s'
		",
		$row[0]
	);
	$db->Select($sql);
	if ( $db->_rowCount != 0 ) {
		fwrite($fh_log, "\tWARNING not adding ndb_no {$row[0]} because it already exists.\n");
		continue;
	}
	$sql = sprintf("
		INSERT INTO foodDescs(
			ndb_no, fdgrp_cd, long_desc, shrt_desc, comname, manufacname, survey,
			ref_desc, refuse, sciname, n_factor, pro_factor, fat_factor, cho_factor
		)
		VALUES('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')
		",
		$row[0],
		$row[1],
		$db->EscapeString($row[2]),
		$db->EscapeString($row[3]),
		$db->EscapeString($row[4]),
		$db->EscapeString($row[5]),
		$row[6],
		$db->EscapeString($row[7]),
		$row[8],
		$db->EscapeString($row[9]),
		$row[10],
		$row[11],
		$row[12],
		$row[13]
	);
	$db->Modify($sql);
	if ( $db->_error ) {
		fwrite($fh_log, "\tERROR adding ndb_no {$row[0]}: $db->_error\n");
	} else {
		$idx++;
	}
}
fwrite($fh_log, "\tAdded $idx records to table foodDescs.\n");

# Now make updates
fwrite($fh_log, "### PROCESSING CHG_FOOD.txt ###\n");
$idx = 0;
while ( ($row = fgetcsv($fh_chg, 0, $delimiter, $enclosure)) !== FALSE ) {
	$sql = sprintf("
		UPDATE foodDescs set
			fdgrp_cd = '%s',
			long_desc = '%s',
			shrt_desc = '%s',
			comname = '%s',
			manufacname = '%s',
			survey = '%s',
			ref_desc = '%s',
			refuse = '%s',
			sciname = '%s',
			n_factor = '%s',
			pro_factor = '%s',
			fat_factor = '%s',
			cho_factor = '%s'
		WHERE ndb_no = '%s'
		",
		$row[1],
		$db->EscapeString($row[2]),
		$db->EscapeString($row[3]),
		$db->EscapeString($row[4]),
		$db->EscapeString($row[5]),
		$row[6],
		$db->EscapeString($row[7]),
		$row[8],
		$db->EscapeString($row[9]),
		$row[10],
		$row[11],
		$row[12],
		$row[13],
		$row[0]
	);
	$db->Modify($sql);
	if ( $db->_error ) {
		fwrite($fh_log, "\tERROR modifying ndb_no {$row[0]}: $db->_error\n");
	} elseif ( $db->_affectedRows != 1 ) {
		fwrite($fh_log, "\tWARNING: nothing modified for ndb_no {$row[0]}.\n");
	} else {
		$idx++;
	}
}
fwrite($fh_log, "\tUpdated $idx records in table foodDescs.\n");

?>
