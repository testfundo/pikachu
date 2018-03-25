#!/usr/bin/php

<?php

/**
 * Script to update nutridb table nutrientData which corresponds
 * to USDA file NUT_DATA, which corresponds to update files
 * DEL_NUTR.txt and ADD_NUTR.txt and CHG_NUTR.txt.
 *
 * NOTE: This script relies on the fact that updates files
 * are named as they were from the SR19 -> SR20 update.
 */

require("config.php");

$fh_del = fopen("{$updatesDir}/DEL_NUTR.txt", "r");
$fh_add = fopen("{$updatesDir}/ADD_NUTR.txt", "r");
$fh_chg = fopen("{$updatesDir}/CHG_NUTR.txt", "r");

# First handle deletions
fwrite($fh_log, "### PROCESSING DEL_NUTR.txt ###\n");
$idx = 0;
while ( ($row = fgetcsv($fh_del, 0, $delimiter, $enclosure)) !== FALSE ) {
	$sql = sprintf("
		UPDATE nutrientData
		SET usda_status = 'deleted'
		WHERE ndb_no = '%s'
			AND nutr_no = '%s'
		",
		$row[0],
		$row[1]
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
fwrite($fh_log, "\tDeactivated $idx records from table nutrientData.\n");

# Now handle additions
fwrite($fh_log, "### PROCESSING ADD_NUTR.txt ###\n");
$idx = 0;
while ( ($row = fgetcsv($fh_add, 0, $delimiter, $enclosure)) !== FALSE ) {
	# Don't add the record if this ndb_no already exists
	$sql = sprintf("
		SELECT ndb_no FROM nutrientData
		WHERE ndb_no = '%s'
			AND nutr_no = '%s'
		",
		$row[0],
		$row[1]
	);
	$db->Select($sql);
	if ( $db->_rowCount != 0 ) {
		fwrite($fh_log, "\tWARNING not adding ndb_no {$row[0]}, nutr_no {$row[1]} because it already exists.\n");
		continue;
	}

	$sql = sprintf("
		INSERT INTO nutrientData(
			ndb_no, nutr_no, nutr_val, num_data_pts, std_error, src_cd, deriv_cd, ref_ndb_no,
			add_nutr_mark, num_studies, min, max, df, low_eb, up_eb, stat_cmt, cc
		)
		VALUES('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')
		",
		$row[0],
		$row[1],
		$row[2],
		$row[3],
		$row[4],
		$row[5],
		$row[6],
		$row[7],
		$row[8],
		$row[9],
		$row[10],
		$row[11],
		$row[12],
		$row[13],
		$row[14],
		addslashes($row[15]),
		$row[16]
	);
	$db->Modify($sql);
	if ( $db->_error ) {
		fwrite($fh_log, "\tERROR adding ndb_no {$row[0]}: $db->_error\n");
	} else {
		$idx++;
	}
}
fwrite($fh_log, "\tAdded $idx records to table nutrientData.\n");

# Now make updates
fwrite($fh_log, "### PROCESSING CHG_NUTR.txt ###\n");
$idx = 0;
while ( ($row = fgetcsv($fh_chg, 0, $delimiter, $enclosure)) !== FALSE ) {
	$sql = sprintf("
		UPDATE nutrientData set
			ndb_no = '%s',
			nutr_no = '%s',
			nutr_val = '%s',
			num_data_pts = '%s',
			std_error = '%s',
			src_cd = '%s',
			deriv_cd = '%s',
			ref_ndb_no = '%s',
			add_nutr_mark = '%s',
			num_studies = '%s',
			min = '%s',
			max = '%s',
			df = '%s',
			low_eb = '%s',
			up_eb = '%s',
			stat_cmt = '%s',
			cc = '%s'
		WHERE ndb_no = '%s'
			and nutr_no = '%s'
		",
		$row[0],
		$row[1],
		$row[2],
		$row[3],
		$row[4],
		$row[5],
		$row[6],
		$row[7],
		$row[8],
		$row[9],
		$row[10],
		$row[11],
		$row[12],
		$row[13],
		$row[14],
		addslashes($row[15]),
		$row[16],
		$row[0],
		$row[1]
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
fwrite($fh_log, "\tUpdated $idx records in table nutrientData.\n");

?>
