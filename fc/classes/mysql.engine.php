<?php
/***************************************************************************
 *                              mysql.engine.php
 *                            --------------------
 *   begin                : Saturday, Jun 03, 2006
 *   copyright            : (C) 2006 RENATA WEB SYSTEMS
 *   email                : alexey@renatasystems.org
 *	 updated by			  : Nikolay Grischenko, 2014
 *
 *   $Id: mysql.engine.php,v 1.1.1.1 2007/08/21 05:53:08 alexey Exp $
 *
 *
 ***************************************************************************/

preg_match("/[^\.\/]+\.[^\.\/]+$/", getenv('HTTP_HOST'), $matches);


if ( !class_exists('fc_db_structure') ) {
	require 'db.structure.php';
}

class fc_db extends fc_db_structure {
	
	var $username;
	var $hostname;
	var $database;
	
	var $dblink = false;  
	var $error = false;
	
	var $exec_time = false;
	var $queries = false;
	var $affected_rows = false;
	
	function raise_error($msg, $sql = false) {
	
		die('DB ERROR: ' . $msg . '<br />' . $sql . '<br />');
	}
	
	function connect() {
	
/*		$username = 'u10014';
		$hostname = 'db-1.renatasystems.org';
		$password = 'w3dnxe7acvgy';
		$database = 'u10014';
// */

		$username = 'u19121';
		$hostname = 'u19121.mysql.masterhost.ru';
		$password = 'pa5tollant4';
		$port = '3306';
		$database = 'u19121_aliyah';

// */		
		
		$this->username = $username;
		$this->hostname = $hostname;

		$this->dblink = @mysql_connect($this->hostname . ':' . $port, $this->username, $password)
		or $this->raise_error('Cannot connect');
		
		unset($password);
		
		if ( $database) : 
			$this->database = $database;
			@mysql_select_db($this->database, $this->dblink)
			or $this->raise_error('Unable to select database');
		endif;
		return true;
	}
	
	function disconnect() {
		if ( $this->dblink ) :
			mysql_close($this->dblink);
		else :
			$this->raise_error('Not connected');
		endif;

	}
	
	function query($sql) {
	
		if ( $this->dblink ) {
			$mtime = microtime();
			$mtime = explode(" ",$mtime);
			$mtime = $mtime[1] + $mtime[0];
			$starttime = $mtime;

			$result = mysql_query($sql, $this->dblink)
			or $this->raise_error($this->error(), $sql);

			$mtime = microtime();
			$mtime = explode(" ",$mtime);
			$mtime = $mtime[1] + $mtime[0];
			$endtime = $mtime;
			
			$this->exec_time += $endtime - $starttime;
			$this->queries = $this->queries + 1;
			$this->affected_rows += $this->num_rows($result);
		}
		else {
			$this->raise_error('Not connected');
		}
		return $result;
	}
	
	function num_rows($result) {
	
		if ( $this->dblink ) {
			$num = @mysql_num_rows($result)
			or $num = false;
		}
		else {
			$this->raise_error('Not connected');
		}
		
		return $num;
	}
	
	function fetch_array($result, $type = MYSQL_BOTH) {
		
		if ( $this->dblink ) {
			$array = mysql_fetch_array($result, $type);
		}
		else {
			$this->raise_error('Not connected');
		}
		
		return $array;
	}

	function fetch_assoc($result) {
	
		if ( $this->dblink ) {
			$array = @mysql_fetch_assoc($result);
		}
		else {
			$this->raise_error('Not connected');
		}
		
		return $array;
	}

	function fetch_row($result) {
	
		if ( $this->dblink ) {
			$array = mysql_fetch_row($result);
		}
		else {
			$this->raise_error('Not connected');
		}
		
		return $array;
	}
	
	function affected_rows() {

		if ( $this->dblink ) {
			$rows = mysql_affected_rows($this->dblink);
		}
		else {
			$this->raise_error('Not connected');
		}
		
		return $rows;
	}

	function insert_id() {

		if ( $this->dblink ) {
			$id = mysql_insert_id($this->dblink);
		}
		else {
			$this->raise_error('Not connected');
		}
		
		return $id;
	}
	
	function error() {
	
		$result = false;
		
		if ( $this->dblink ) {
			$result = mysql_error($this->dblink);
		}

		return $result;
	}
}

?>
