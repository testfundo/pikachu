#!/usr/bin/php

<?php

/**
 * Script to update nutridb table footnotes which corresponds
 * to USDA file FOOTNOTE, which corresponds to update files
 * DEL_FTNT.txt and ADD_FTNT.txt and CHG_FTNT.txt.
 *
 * NOTE: This script relies on the fact that updates files
 * are named as they were from the SR19 -> SR20 update.
 */

require("config.php");

$fh_del = fopen("{$updatesDir}/DEL_FTNT.txt", "r");
$fh_add = fopen("{$updatesDir}/ADD_FTNT.txt", "r");
$fh_chg = fopen("{$updatesDir}/CHG_FTNT.txt", "r");

# First handle deletions
fwrite($fh_log, "### PROCESSING DEL_FTNT.txt ###\n");
$idx = 0;
while ( ($row = fgetcsv($fh_del, 0, $delimiter, $enclosure)) !== FALSE ) {
	$sql = sprintf("
		UPDATE footnotes
		SET usda_status = 'deleted'
		WHERE ndb_no = '%s'
			AND footnt_no = '%s'
			AND footnt_typ = '%s'
		",
		$row[0],
		$row[1],
		$row[2]
	);
	$db->Modify($sql);
	if ( $db->_error ) {
		fwrite($fh_log, "\tERROR: deactivating ndb_no {$row[0]}: $db->_error\n");
	} elseif ( $db->_affectedRows == 0 ) {
		fwrite($fh_log, "\tWARNING: ndb_no {$row[0]} was not found in the database.\n");
	} else {
		$idx++;
	}
}
fwrite($fh_log, "\tDeactivated $idx records from table footnotes.\n");

# Now handle additions
fwrite($fh_log, "### PROCESSING ADD_FTNT.txt ###\n");
$idx = 0;
while ( ($row = fgetcsv($fh_add, 0, $delimiter, $enclosure)) !== FALSE ) {
	# Don't add the record if this ndb_no already exists
	$sql = sprintf("
		SELECT ndb_no FROM footnotes
		WHERE ndb_no = '%s'
			AND footnt_no = '%s'
		",
		$row[0],
		$row[1]
	);
	$db->Select($sql);
	if ( $db->_rowCount != 0 ) {
		fwrite($fh_log, "\tWARNING not adding footnote for ndb_no {$row[0]} because it already exists\n");
		continue;
	}
	$sql = sprintf("
		INSERT INTO footnotes(ndb_no, footnt_no, footnt_typ, nutr_no, footnt_txt)
		VALUES('%s','%s','%s','%s','%s')
		",
		$row[0],
		$row[1],
		$row[2],
		$row[3],
		addslashes($row[4])
	);
	$db->Modify($sql);
	if ( $db->_error ) {
		fwrite($fh_log, "\tERROR adding footnote for ndb_no {$row[0]}: $db->_error\n");
	} else {
		$idx++;
	}
}
fwrite($fh_log, "\tAdded $idx records to table footnotes\n");

# Now make updates
fwrite($fh_log, "### PROCESSING CHG_FTNT.txt ###\n");
$idx = 0;
while ( ($row = fgetcsv($fh_chg, 0, $delimiter, $enclosure)) !== FALSE ) {
	$sql = sprintf("
		UPDATE footnotes set
			ndb_no = '%s',
			footnt_no = '%s',
			footnt_typ = '%s',
			nutr_no = '%s',
			footnt_txt = '%s'
		WHERE ndb_no = '%s'
			AND footnt_no = '%s'
			AND footnt_typ = '%s'
		",
		$row[0],
		$row[1],
		$row[2],
		$row[3],
		addslashes($row[4]),
		$row[0],
		$row[1],
		$row[2]
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
fwrite($fh_log, "\tUpdated $idx records in table footnotes.\n");

?>
