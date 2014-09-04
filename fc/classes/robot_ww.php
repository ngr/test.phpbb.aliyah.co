<?php

class ww implements Robot
{

	private $user_id;
	private $admin = false;
	private $config;


	public function init()
	{
# FIXME	
		require_once 'includes/environment.php';
	
		global $user;
		
		$this->user_id = $user->data['user_id'];
		
		$this->admin = ( $user->data['user_type'] == 3 ) ? true : false;
		
		$this->config = $GLOBALS['config_fc']['robot'];
		
		$GLOBALS['application']->record_debug('robot initiated');
	}


	public function go()
	{
// Check if there is a valid ww already
		if ( $this->exists_valid_lesson() )
		{
			if ( $GLOBALS['debug_log'] == true ) $GLOBALS['application']->record_debug( 'robot->exists_valid_lesson(): Returned true ' );
			return true;
		}
		else
		{
			if ( ! $this->create_lesson() )
			{
				if ( $GLOBALS['debug_log'] == true ) $GLOBALS['application']->record_debug( 'Failed! robot->create_lesson() : Returned false ' );
				return false;
			}
		}
		return true;
	}
	
	public function set_param( $param, $value = NULL )
	{
//		echo "Setting `" . $param . "` to value: " . $value;
		
	}

	private function exists_valid_lesson()
	{
		global $fc_db, $fc_db_struct;
//		view( $fc_db_struct );
		$sql =	'SELECT 1 FROM `' . FC_LESSONS_NAMES_TABLE . '` AS ln'
			.	' LEFT JOIN `' . FC_LESSONS_ACC_RIGHTS_TABLE . '` AS lar ON lar.`' . $fc_db_struct[FC_LESSONS_ACC_RIGHTS_TABLE]['lesson_id'] . '` = ln.`' . $fc_db_struct[FC_LESSONS_NAMES_TABLE]['id'] . '`' 
			.	' LEFT JOIN `' . BB_USER_GROUP_TABLE . '` AS bb_ug ON bb_ug.`' . $fc_db_struct[BB_USER_GROUP_TABLE]['group_id'] . '` = lar.`' . $fc_db_struct[FC_LESSONS_ACC_RIGHTS_TABLE]['group_id'] . '`'
			.	' WHERE bb_ug.`' . $fc_db_struct[BB_USER_GROUP_TABLE]['user_id'] . '` = \'' . $this->user_id . '\''
			.	' AND ln.`' . $fc_db_struct[FC_LESSONS_NAMES_TABLE]['author'] . '` = \'' . $this->config['ww']['robot_uid'] . '\''
			.	' AND ln.`' . $fc_db_struct[FC_LESSONS_NAMES_TABLE]['date_valid'] . '` > \'' . time() . '\''
			.	';';

		if ( $GLOBALS['debug_all'] == true ) echo '<br>' . $sql;
		if ( $GLOBALS['debug_log'] == true ) $GLOBALS['application']->record_debug( 'robot->exists_valid_lesson() SQL: ' . $sql );

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

	private function create_lesson()
	{
//		echo "<br> Building new ww";
		
		global $fc_db, $fc_db_struct, $lessons, $user;

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
			.	', cnt DESC'
			.	';';
//		echo "<br>" . $sql . "<br>";

		if ( $GLOBALS['debug_all'] == true ) echo '<br>' . $sql;
		if ( $GLOBALS['debug_log'] == true ) $GLOBALS['application']->record_debug( 'robot->build_new_ww() SQL: ' . $sql );


//		$lesson = $GLOBALS['lessons']->create( 'Мой тест', $this->config['ww']['robot_uid'], time(), time() + 60 );
		if ( ! $lesson ) 
		{
			if ( $GLOBALS['debug_err'] == true ) $GLOBALS['application']->record_debug( 'Failed! robot->build_new_ww() could not create a new lesson.' );
//			return false;
		}

		if ( $GLOBALS['debug_log'] == true ) $GLOBALS['application']->record_debug( 'robot->build_new_ww() Created a new lesson: ' . $lesson );
//		echo '<br>' . $user->fc_say_hw();
		
		return true;
	}
}
?>