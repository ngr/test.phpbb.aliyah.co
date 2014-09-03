<?php

/***************************************************************************
 *                              lessons.php
 *                            -------------------
 *   begin                : Wednesday, Sep 03, 2014
 *   copyright            : (C) 2014 Nikolay Grischenko
 *   email                : me@grischenko.ru
 *
 ***************************************************************************/

class lessons{

	private $user_id;
	private $admin = false;
	private $config;
//	private record_debug();

	function lessons()
	{
# FIXME	
		require_once 'includes/environment.php';
	
		global $user;
		
		$this->user_id = $user->data['user_id'];
		
		$this->admin = ( $user->data['user_type'] == 3 ) ? true : false;
		
		$this->config = $GLOBALS['config_fc']['lessons'];
		
		if ( $GLOBALS['debug_log'] == true ) $GLOBALS['application']->record_debug('New object constructed: lessons');
	}
	
	public function is_admin()
	{
		return $this->admin;
	}
	
	public function create( $name, $author, $date_init = NULL,  $date_valid = 4294967295 )
	{
		if ( $GLOBALS['debug_log'] == true ) $GLOBALS['application']->record_debug('Creating new lesson: lessons->create()');

		global $fc_db, $fc_db_struct;

		$sql =	'INSERT INTO `' . FC_LESSONS_NAMES_TABLE . '` ( `' . $fc_db_struct[FC_LESSONS_NAMES_TABLE]['rus_name'] . '`'
			.	', `' . $fc_db_struct[FC_LESSONS_NAMES_TABLE]['author'] . '`'		
			.	', `' . $fc_db_struct[FC_LESSONS_NAMES_TABLE]['date_init'] . '`'		
			.	', `' . $fc_db_struct[FC_LESSONS_NAMES_TABLE]['date_valid'] . '` )'		
			.	' VALUES ( \'' . $name . '\', \'' . $author . '\', \'' . $date_init . '\', \'' . $date_valid . '\' )'
			.	';';


//		echo "<br>" . $sql . "<br>";
		if ( $GLOBALS['debug_all'] == true ) echo '<br>' . $sql;
		if ( $GLOBALS['debug_log'] == true ) $GLOBALS['application']->record_debug( 'robot->build_new_ww() SQL: ' . $sql );

		if ( $fc_db->query( $sql ) )
		{
			return $fc_db->insert_id();
		}
		else
		{
			return false;
		}
	}

}