<?php
		
# define a class for connecting to, extracting and modifying data from a database

class Database {

	# predefine various variables
	protected $_dbType			= "mysql";
	public $_rows;			# holder for multiple records
	public $_row;			# holder for a single record
	public $_result;
	public $_rowCount;
	public $_fieldCount;
	public $_affectedRows;
	public $_insertId;
	public $_error;
	public $_dbConn;

	# class constructor
	function Database() {

		# connect to the database
		$this->Connect(
			DBHOST,
			DBUSER,
			DBPASS,
			DBNAME
		);

	}

	##------------------------------------------------------------------##

	# connect to the database
	function Connect($dbHost, $dbUser, $dbPass, $dbName) {
		try {
			$this->_dbConn = new PDO($this->_dbType . ':host=localhost;charset=utf8;dbname=' . $dbName, $dbUser, $dbPass);
			return true;
		} catch (PDOException $e) {
			# grab error if the connection fails
			$this->_error = $e->getMessage();
			if ( DBDEBUG == "true" ) {
				$this->PrintError();
			}
			return false;
		}

	}
	
	##------------------------------------------------------------------##

	# close the connection to the database
	function Close() {

		if ( isset($this->_dbConn) ) {
			$this->_dbConn = null;
			return true;
		}

	}

	##------------------------------------------------------------------##

	# handles select queries where multiple rows are expected
	function Select($sql) {

		$this->_result = $this->_dbConn->query($sql);

		if ( $this->_result ) {
			$this->_rowCount = $this->_result->rowCount();
			$this->_fieldCount = $this->_result->columnCount();
			$this->_rows = $this->_result->fetchAll();
			return true;
		} else {
			$this->_error = $this->_dbConn->errorInfo()[2];
			if ( DBDEBUG == "true" ) {
				$this->PrintError($sql);
			}
			return false;
		}

	}

	##------------------------------------------------------------------##

	# handles select queries where only one record is expected
	function SelectOne($sql) {

		$this->_result = $this->_dbConn->query($sql);

		if ( $this->_result ) {
			$this->_rowCount = $this->_result->rowCount();
			$this->_fieldCount = $this->_result->columnCount();
			$this->_row = $this->_result->fetch();
			return true;
		} else {
			$this->_error = $this->_dbConn->errorInfo()[2];
			if ( DBDEBUG == "true" ) {
				$this->PrintError($sql);
			}
			return false;
		}

	}

	##------------------------------------------------------------------##

	# handles select queries that need to return a restricted record set
	function SelectLimit($sql, $rows, $offset) {

		$sql .= ' LIMIT ' . $rows . ' OFFSET ' . $offset;
		$this->_result = $this->_dbConn->query($sql);

		if ( $this->_result ) {
			$this->_rowCount = $this->_result->rowCount();
			$this->_fieldCount = $this->_result->columnCount();
			$this->_rows = $this->_result->fetchAll();
			return true;
		} else {
			$this->_error = $this->_dbConn->errorInfo()[2];
			if ( DBDEBUG == "true" ) {
				$this->PrintError($sql);
			}
			return false;
		}

	}

	##------------------------------------------------------------------##

	# handles queries that will alter data
	function Modify($sql) {
		
		$this->_result = $this->_dbConn->query($sql);

		if ( $this->_result ) {
			$this->_affectedRows = $this->_result->rowCount();
			return true;
		} else {
			$this->_error = $this->_dbConn->errorInfo()[2];
			if ( DBDEBUG == "true" ) {
				$this->PrintError($sql);
			}
			return false;
		}

	}

	##------------------------------------------------------------------##

	# get auto_incremented ID of last insert statement
	function InsertId() {

		$this->_result = $this->_dbConn->lastInsertId();

		if ( $this->_result ) {
			$this->_insertId = $this->_result;
			return $this->_insertId;
		} else {
			$this->_error = $this->_dbConn->errorInfo()[2];
			if ( DBDEBUG == "true" ) {
				$this->PrintError();
			}
			return false;
		}

	}

	##------------------------------------------------------------------##

	# clean up and escape strings to be inserted into database
	function EscapeString($string) {

		$string = trim($string);

		if ( ! is_numeric($string) ) {
			$string = $this->_dbConn->quote($string);
		}

		# the quote() function adds single quotes around the submitted string.
		# i like to add those myself at the time of the query, so strip them
		# off here
		$string = trim($string, "'");

		return $string;

	}

	##------------------------------------------------------------------##

	# print an error to the screen and then exit the script
	function PrintError($sql = "") {

		$thisScript = basename($_SERVER['PHP_SELF']);
		echo <<<HTML
<html>
<head>
	<title>Database Error</title>
</head>
<body>
	<div>
		<p>There was a database error.</p>
		<p><strong>Script</strong>: $thisScript</p>
		<p><strong>SQL</strong>: $sql</p>
		<p><strong>Error</strong>: <span style='color: red;'>$this->_error</span></p>
	</div>
</body>
</html>

HTML;

		exit;

		return true;

	}

	##------------------------------------------------------------------##

}
