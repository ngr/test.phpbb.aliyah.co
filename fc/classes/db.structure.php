<?php
/***************************************************************************
 *                              db.structure.php
 *                            -------------------
 *   begin                : Saturday, Jun 03, 2006
 *   copyright            : (C) 2006 RENATA WEB SYSTEMS
 *   email                : alexey@renatasystems.org
 *	 updated by			  : Nikolay Grischenko, 2014
 *
 *   $Id: db.structure.php,v 1.0.0 2006/06/03 15:17:14 i_am_d Exp $
 *
 *
 ***************************************************************************/

define('FC_TABLE_PREFIX', 'fc_');
define('FC_ASSIGNMENTS_TABLE', FC_TABLE_PREFIX . 'assignments');
define('FC_CONFIG_TABLE', FC_TABLE_PREFIX . 'config');
define('FC_WORDS_TABLE', FC_TABLE_PREFIX . 'words');
define('FC_WORDS_PARAMS_TABLE', FC_TABLE_PREFIX . 'words_params');
define('FC_WORDS_RUS_TABLE', FC_TABLE_PREFIX . 'words_rus');
define('FC_LESSONS_TABLE', FC_TABLE_PREFIX . 'lessons');
define('FC_LESSONS_ACC_RIGHTS_TABLE', FC_TABLE_PREFIX . 'lessons_acc_rights');
define('FC_LESSONS_NAMES_TABLE', FC_TABLE_PREFIX . 'lessons_names');
define('FC_LESSONS_TABLE', FC_TABLE_PREFIX . 'user_groups');
define('FC_SESSIONS_TABLE', FC_TABLE_PREFIX . 'sessions');
define('FC_DATA_TABLE', FC_TABLE_PREFIX . 'data');
define('FC_HEB_RUS_TABLE', FC_TABLE_PREFIX . 'heb_rus');
define('FC_RUS_HEB_TABLE', FC_TABLE_PREFIX . 'rus_heb');
define('FC_USER_GROUPS_TABLE', FC_TABLE_PREFIX . 'user_groups');

$fc_db_struct = array(
	FC_ASSIGNMENTS_TABLE => array(
		'id' => 'id',
		'session_id' => 'session_id',
		'word_id' => 'word_id',
		'date' => 'date'
	),	
	FC_CONFIG_TABLE => array(
		'var' => 'var',
		'value' => 'value'
	),	
	FC_WORDS_TABLE => array(
		'id' => 'id',
		'heb' => 'heb',
		'part_of_speech' => 'part_of_speech',
		'parent' => 'parent'
	),
	FC_WORDS_PARAMS_TABLE => array(
		'heb_id' => 'heb_id',
		'param' => 'param',
		'value' => 'value'
	),
	FC_WORDS_RUS_TABLE => array(
		'id' => 'id',
		'rus' => 'rus',
		'part_of_speech' => 'part_of_speech',
		'parent' => 'parent'
	),
	FC_LESSONS_TABLE => array(
		'id' => 'id',
		'word_id' => 'word_id'
	),
	FC_LESSONS_ACC_RIGHTS_TABLE => array(
		'lesson_id' => 'lesson_id',
		'user_group_id' => 'user_group_id'
	),
	FC_LESSONS_NAMES_TABLE => array(
		'id' => 'id',
		'rus_name' => 'rus_name',
		'author' => 'author',
		'date_init' => 'date_init',
		'date_vaid' => 'date_valid',
		'order' => 'order'
	),
	FC_SESSIONS_TABLE => array(
		'id' => 'id',
		'user_id' => 'user_id',
		'start_time' => 'start_time',
		'end_time' => 'end_time',
		'result' => 'result'
	),
	FC_DATA_TABLE => array(
		'id' => 'id',
		'start_time' => 'start_time',
		'time' => 'time',
		'session_id' => 'session_id',
		'word_id' => 'word_id',
		'result' => 'result',
		'answer' => 'answer'
	),
	FC_HEB_RUS_TABLE => array(
		'id' => 'id',
		'heb_id' => 'heb_id',
		'rus_id' => 'rus_id',
		'priority' => 'priority'
	),
	FC_RUS_HEB_TABLE => array(
		'id' => 'id',
		'rus_id' => 'rus_id',
		'heb_id' => 'heb_id',
		'priority' => 'priority'
	),
	FC_USER_GROUPS_TABLE => array(
		'id' => 'id',
		'user_id' => 'user_id',
		'name' => 'name'
	)
);

class fc_db_structure {

	function structure($table, $field = false) {
		
		global $fc_db_struct;
		
		if ( !$field ) :
			$s = $fc_db_struct[$table];
		else :
			$s = $fc_db_struct[$table][$field];
		endif;
		
		return $s;
	}
}
?>