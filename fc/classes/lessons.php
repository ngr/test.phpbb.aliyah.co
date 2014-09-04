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

	private $id;
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
	
	public function set_id ( $id )
	{
		if ( ! is_int( $id ) )
		{
			$GLOBALS['application']->record_debug( 'Failed! lessons->set_id() received not integer id:' . $id );
			return false;
		}
		$this->id = $id;
		return true;
	}
	
	public function get_id ()
	{
		return $this->id;
	}

	public function add_word( $word )
	{
		global $fc_db, $fc_db_struct;

		if ( ! isset( $word ) )
		{
			if ( $GLOBALS['debug_err'] == true ) $GLOBALS['application']->record_debug( 'Failed! lessons->add_word() did not receive requred param.' );
			return false;
		}

		$sql =	'INSERT INTO `' . FC_LESSONS_TABLE . '` ( `' . $fc_db_struct[FC_LESSONS_TABLE]['id'] . '`'
			.	', `' . $fc_db_struct[FC_LESSONS_TABLE]['word_id'] . '` )'		
			.	' VALUES ( \'' . $this->id . '\', \'' . $word . '\' )'
			.	';';

		if ( $GLOBALS['debug_all'] == true ) echo '<br>' . $sql;
		if ( $GLOBALS['debug_log'] == true ) $GLOBALS['application']->record_debug( 'robot->add_word() SQL: ' . $sql );

		if ( $fc_db->query( $sql ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}


	public function create( $name, $author, $date_init = NULL,  $date_valid = 4294967295 )
	{
		if ( $GLOBALS['debug_log'] == true ) $GLOBALS['application']->record_debug('Creating new lesson: lessons->create()');

		global $fc_db, $fc_db_struct;

		if ( ! isset( $name ) || ! isset( $author ) )
		{
			if ( $GLOBALS['debug_err'] == true ) $GLOBALS['application']->record_debug( 'Failed! lessons->create() did not receive all the required params.' );
			return false;
		}

		$sql =	'INSERT INTO `' . FC_LESSONS_NAMES_TABLE . '` ( `' . $fc_db_struct[FC_LESSONS_NAMES_TABLE]['rus_name'] . '`'
			.	', `' . $fc_db_struct[FC_LESSONS_NAMES_TABLE]['author'] . '`'		
			.	', `' . $fc_db_struct[FC_LESSONS_NAMES_TABLE]['date_init'] . '`'		
			.	', `' . $fc_db_struct[FC_LESSONS_NAMES_TABLE]['date_valid'] . '` )'		
			.	' VALUES ( \'' . $name . '\', \'' . $author . '\', \'' . $date_init . '\', \'' . $date_valid . '\' )'
			.	';';

		if ( $GLOBALS['debug_all'] == true ) echo '<br>' . $sql;
		if ( $GLOBALS['debug_log'] == true ) $GLOBALS['application']->record_debug( 'robot->create() SQL: ' . $sql );

		if ( $fc_db->query( $sql ) )
		{
			$this->set_id( $fc_db->insert_id() );
			return true;
		}
		else
		{
			return false;
		}
	}

	public function grant_access( $agent = false, $group_acc = false )
	{
		if ( $GLOBALS['debug_log'] == true ) $GLOBALS['application']->record_debug('Granting access rights: lessons->grant_access()');

		global $fc_db, $fc_db_struct;

# Set the default user to current user
		$agent = ( ! $agent ) ? $this->user_id : $agent;

# FIXME Should check here that the user/group exists and is valid

		$sql =	'INSERT INTO `' . FC_LESSONS_ACC_RIGHTS_TABLE . '` ('
			.	' `' . $fc_db_struct[FC_LESSONS_ACC_RIGHTS_TABLE]['lesson_id'] . '`';
			if ( $group_acc )
			{
				$sql .=	', `' . $fc_db_struct[FC_LESSONS_ACC_RIGHTS_TABLE]['group_id'] . '`';
			}
			else
			{
				$sql .=	', `' . $fc_db_struct[FC_LESSONS_ACC_RIGHTS_TABLE]['user_id'] . '`';
			}
		$sql .=	' ) VALUES ( \'' . $this->id . '\', \'' . $agent . '\' )'
			.	';';

		if ( $GLOBALS['debug_all'] == true ) echo '<br>' . $sql;
		if ( $GLOBALS['debug_log'] == true ) $GLOBALS['application']->record_debug( 'robot->grant_access() SQL: ' . $sql );

		if ( $fc_db->query( $sql ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}