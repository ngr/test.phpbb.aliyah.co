<?php

/***************************************************************************
 *                              robot.php
 *                            -------------------
 *   begin                : Friday, Aug 15, 2014
 *   copyright            : (C) 2014 Nikolay Grischenko
 *   email                : me@grischenko.ru
 *
 ***************************************************************************/

class robot{

	private $user_id;
	private $admin = false;
	private $config;

	function robot()
	{
# FIXME	
		require_once 'includes/environment.php';
	
		global $user;
		
		$this->user_id = $user->data['user_id'];
		
		$this->admin = ( $user->data['user_type'] == 3 ) ? true : false;
		
		$this->config = $GLOBALS['config_fc']['robot'];
	}
	
	public function is_admin()
	{
		return $this->admin;
	}
	
	public function ww_init()
	{
//		view( $this->config );
		
// Check if all required params are OK

// Check if there is a valid ww already
	if ( $this->exists_valid_ww() )
	{
		return true;
	}
	else
	{
		$this->build_new_ww();
	}

// Build a new ww

		return true;
	}
	
	public function set_param( $param, $value = NULL )
	{
//		echo "Setting `" . $param . "` to value: " . $value;
		
	}

	private function exists_valid_ww()
	{
		global $fc_db, $fc_db_struct;
		
		$sql =	'SELECT 1 FROM `' . FC_LESSONS_NAMES_TABLE . '` AS ln'
			.	' LEFT JOIN `' . FC_LESSONS_ACC_RIGHTS_TABLE . '` AS lar ON lar.`' . $fc_db_struct[FC_LESSONS_ACC_RIGHTS_TABLE]['lesson_id'] . '` = ln.`' . $fc_db_struct[FC_LESSONS_NAMES_TABLE]['id'] . '`' 
			.	' LEFT JOIN `' . FC_USER_GROUPS_TABLE . '` AS ug ON ug.`' . $fc_db_struct[FC_USER_GROUPS_TABLE]['id'] . '` = lar.`' . $fc_db_struct[FC_LESSONS_ACC_RIGHTS_TABLE]['user_group_id'] . '`'
			.	' WHERE ug.`' . $fc_db_struct[FC_USER_GROUPS_TABLE]['user_id'] . '` = \'' . $this->user_id . '\''
			.	' AND ln.`' . $fc_db_struct[FC_LESSONS_NAMES_TABLE]['author'] . '` = \'' . $this->config['ww']['author_id'] . '\''
			.	' AND ln.`' . $fc_db_struct[FC_LESSONS_NAMES_TABLE]['date_valid'] . '` > \'' . time() . '\''
			.	';';
//		echo "<br>" . $sql . "<br>";
		$result = $fc_db->query( $sql );
		
		if ( $fc_db->num_rows( $result ) > 0 )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	private function build_new_ww()
	{
//		echo "<br> Building new ww";
		
		global $fc_db, $fc_db_struct;

//		$sql =	'SELECT DISTINCT( l.`' . $fc_db_struct[FC_LESSONS_TABLE]['word_id'] . ' ) AS id, *'
		$sql =	'SELECT  d.word_id, COUNT(*) AS cnt'
			.	', (SELECT  COUNT(*) AS err'
			.	' FROM `' . FC_DATA_TABLE . '` AS d1'
			.	' LEFT JOIN `' . FC_SESSIONS_TABLE . '` AS s ON s.`' . $fc_db_struct[FC_SESSIONS_TABLE]['id'] . '` = d1.`' . $fc_db_struct[FC_DATA_TABLE]['session_id'] . '`'
			.	' WHERE 1'
			.	' AND s.`' . $fc_db_struct[FC_SESSIONS_TABLE]['user_id'] . '` = \'48\'' 
			.	' AND d1.result < 14'
			.	' AND d1.word_id = d.word_id'
			.	' ) AS err'
			.	' FROM `' . FC_DATA_TABLE . '` AS d'
			.	' LEFT JOIN `' . FC_SESSIONS_TABLE . '` AS s ON s.`' . $fc_db_struct[FC_SESSIONS_TABLE]['id'] . '` = d.`' . $fc_db_struct[FC_DATA_TABLE]['session_id'] . '`'
			.	' WHERE 1'
			.	' AND s.`' . $fc_db_struct[FC_SESSIONS_TABLE]['user_id'] . '` = \'48\'' 
			.	' GROUP BY d.word_id'
			.	' HAVING cnt > 3'
			.	' AND err > 0'
			.	' ORDER BY cnt DESC'
			.	';';
//		echo "<br>" . $sql . "<br>";


		$sql =	'SELECT  d.word_id, COUNT(*) AS cnt'
			.	', (SELECT  COUNT(*)'
			.	' FROM `' . FC_DATA_TABLE . '` AS d1'
			.	' LEFT JOIN `' . FC_SESSIONS_TABLE . '` AS s ON s.`' . $fc_db_struct[FC_SESSIONS_TABLE]['id'] . '` = d1.`' . $fc_db_struct[FC_DATA_TABLE]['session_id'] . '`'
			.	' WHERE 1'
			.	' AND s.`' . $fc_db_struct[FC_SESSIONS_TABLE]['user_id'] . '` = \'48\'' 
			.	' AND d1.result < 14'
			.	' AND d1.word_id = d.word_id'
			.	' ) AS err'
// Average
			.	', ( 100 - (SELECT  COUNT(*)'
			.	' FROM `' . FC_DATA_TABLE . '` AS d1'
			.	' LEFT JOIN `' . FC_SESSIONS_TABLE . '` AS s ON s.`' . $fc_db_struct[FC_SESSIONS_TABLE]['id'] . '` = d1.`' . $fc_db_struct[FC_DATA_TABLE]['session_id'] . '`'
			.	' WHERE 1'
			.	' AND s.`' . $fc_db_struct[FC_SESSIONS_TABLE]['user_id'] . '` = \'48\'' 
			.	' AND d1.result < 14'
			.	' AND d1.word_id = d.word_id'
			.	' ) / COUNT(*) * 100 ) as av_res'
///			
			.	' FROM `' . FC_DATA_TABLE . '` AS d'
			.	' LEFT JOIN `' . FC_SESSIONS_TABLE . '` AS s ON s.`' . $fc_db_struct[FC_SESSIONS_TABLE]['id'] . '` = d.`' . $fc_db_struct[FC_DATA_TABLE]['session_id'] . '`'
			.	' WHERE 1'
			.	' AND s.`' . $fc_db_struct[FC_SESSIONS_TABLE]['user_id'] . '` = \'48\'' 
			.	' GROUP BY d.word_id'
			.	' HAVING cnt > 3'
			.	' AND err > 0'
			.	' ORDER BY av_res'
			.	', cnt'
			.	';';
//		echo "<br>" . $sql . "<br>";
				

		
		return true;
	}

}