<?php

/***************************************************************************
 *                              aliyah.php
 *                            -------------------
 *   begin                : Wednesday, Apr 23, 2014
 *   copyright            : (C) 2014 Nikolay Grischenko
 *   email                : me@grischenko.ru
 *
 *   $Id: aliyah.php,v 1.0.0 2014/04/23 16:29:03 $
 *
 *
 ***************************************************************************/

class aliyah {

	protected $robot;

	function reset()
	{
		global $template;

		$this->record_debug( 'Called reset()' );

		$this->control_session( 'close' );
		unset( $_SESSION['fc']['questions']);
		unset( $_SESSION['fc']['session_id']);
		unset( $_SESSION['fc']['test_type']);

		$template->set_filenames(array(
			'body' => '../../../fc/templates/main/blank.html'
			)
		);
	}

	function fc_page_header()
	{
		global $user, $config, $config_fc, $lang, $template;

		if ( isset( $_GET['debug_all'] ) )
		{
			$GLOBALS['debug_all'] = true;
		}

# Now we save some test parameters to use them as default next time the form is rendered.
		if ( isset( $_POST['lesson'] ) )
		{
			$_SESSION['fc']['last_lesson'] = $_POST['lesson'];
		}

		if ( isset( $_POST['number_of_tests'] ) )
		{
			$_SESSION['fc']['last_number_of_tests'] = $_POST['number_of_tests'];
		}

		if ( isset( $_POST['test_language'] ) )
		{
			$_SESSION['fc']['last_test_language'] = $_POST['test_language'];
		}

		if ( isset( $_POST['test_direction'] ) )
		{
			$_SESSION['fc']['last_test_direction'] = $_POST['test_direction'];
		}

		if ( isset( $_POST['intel_mode'] ) )
		{
			$_SESSION['fc']['last_intel_mode'] = $_POST['intel_mode'];
		}

		if ( isset( $_POST['part_of_speech'] ) )
		{
			$_SESSION['fc']['last_part_of_speech'] = $_POST['part_of_speech'];
		}

# FIXME You should use $lang, no hardcoded labels!		
		$template->assign_vars(array(
				'SITENAME' => 'Aliyah.co',
				'PAGE_TITLE' => 'Флеш-карты',
				'L_FORUM' => 'Форум',
				'L_LOG_IN' => 'Войти',
				'S_USER_LANG' => 'ru',
				'U_STYLESHEET' => generate_board_url() . '/fc/templates/main/main.css',
				'U_FORUM' => generate_board_url(),
				'U_LOG_IN' => generate_board_url() . '/ucp.php?mode=login',
				'L_STATUS_TYPE' => 'status_div',
			)
		);	
	}
#####################################################
# This is one of the most important functions		#
# It creates random test according to user		#
# request and statistics of user previous answers	#
# Should one day make refactoring and split to		#
# different classes logically.				#
#####################################################
	function build_questions()
	{
		global $fc_db, $fc_db_struct, $user, $config_fc, $lang;

# Clear any FC session from PHP _SESSION
		$_SESSION['fc']['questions'] = false;

# Check for "hung" FC sessions. If any - close them. And start new.
		$this->control_session('close');
		$this->control_session('start');
		
# Should get much more test parameters from $_POST here

# Now we get common parameters required for any test
		$this->define_test_language();

#################################
# Set lesson to choose words from
#			 May refactor same as part_of_speech.
		if ( isset( $_POST['lesson'] ) )
		{
			$lesson = $_POST['lesson'];
			if ( $GLOBALS['debug_all'] == true ) view( $lesson );
		}
		else
		{
# FIXMELATER Should add correct exception here
			$this->control_session('close');
			echo '<h1>' . $lang['NO_LESSON_SELECTED'] . '</h1>';
			die( '<script language=javascript>window.onload = setTimeout(function() {  window.location="?mode=index"; }, 1000);</script>' );
		}
		
		if ( is_array( $lesson ) )
		{
# Check if the lesson is allowed for this user
			foreach ( $lesson as $key => $val )
			{
				if ( ! $this->check_lesson_acc_rights( $val ) )
				{
					if ( $GLOBALS['debug_log'] == true ) $this->record_debug( 'A lesson with no access rights was requested by user: ' . $user->data['user_id'] . ', lesson: ' . $val );
					unset( $lesson[$key] );
				}
				else if ( ! $this->is_lesson_public( $val ) )
				{
					$this->add_predefined_lesson_words( $val );
					unset( $lesson[$key] );
				}
			}
# We check if there are some allowed lessons left or finish the job.
			if ( count( $lesson ) === 0 )
			{
				if ( $_SESSION['fc']['questions'] === false )
				{
					$this->reset();
# FIXME This shit is growing more and more popular along the code. Should fix this ASAP.
					$this->control_session('close');
					echo '<h1>' . $lang['NO_APPROPRIATE_WORDS'] . '</h1>';
					die( '<script language=javascript>window.onload = setTimeout(function() {  window.location="?mode=index"; }, 1000);</script>' );
				}
				else
				{
					if ( $GLOBALS['debug_log'] == true ) $this->record_debug( 'build_questions() finished with only predifined lessons requested so no actual work was left to do.' );
					return;
				}
			}

			$sql_select_lesson = ' AND (';

			$i = 0;
			foreach ( $lesson as $val )
			{
				if ( $i > 0 ) 
				{
					$sql_select_lesson .= ' OR';
				}
				$sql_select_lesson .= ' l.`' . $fc_db_struct[FC_LESSONS_TABLE]['id'] . '` = \'' . $val . '\'';
				$i++;
			}
			$sql_select_lesson .= ' )';
		}
# At this point we actually always get array now so the following may be deprecated, but shit happens...
		else 
		{
				$this->control_session('close');
				echo '<h1>' . $lang['NO_LESSON_SELECTED'] . '</h1>';
				die( '<script language=javascript>window.onload = setTimeout(function() {  window.location="?mode=index"; }, 1000);</script>' );
		}
###############################
# Set part of speech if defined
		$sql_select_part_of_speech = '';
		if ( isset( $_POST['part_of_speech'] ) && is_array( $_POST['part_of_speech'] ) )
		{
			$part_of_speech = $_POST['part_of_speech'];
		
			$sql_select_part_of_speech .= ' AND (';
			
			$i = 0;
			foreach ( $part_of_speech as $val )
			{
				if ( $i > 0 ) 
				{
					$sql_select_part_of_speech .= ' OR';
				}
				if ( $val == 'others' )
				{
					$ii = 0;
					foreach ( $config_fc['part_of_speech']['others'] as $pos )
					{
						if ( $ii > 0 ) 
						{
							$sql_select_part_of_speech .= ' OR';
						}
						$sql_select_part_of_speech .= ' w.`' . $fc_db_struct[FC_WORDS_TABLE]['part_of_speech'] . '` = \'' . mysql_escape_string( $pos ) . '\'';
						$ii++;
					}
				}
				else
				{
					$sql_select_part_of_speech .= ' w.`' . $fc_db_struct[FC_WORDS_TABLE]['part_of_speech'] . '` = \'' . mysql_escape_string( $val ) . '\'';
				}
				$i++;
			}
			$sql_select_part_of_speech .= ' )';
		}

###############################
# Set complicated search SQL
		$sql_complicated_search = '';
		$sql_complicated_search .= ' AND w.`' . $fc_db_struct[FC_WORDS_TABLE]['parent'] . '` = \'0\'';


#####################
# Set number of tests
		if ( isset($_POST['number_of_tests']) && is_numeric( $_POST['number_of_tests']) )
		{
			$limit = $_POST['number_of_tests'];
		}
		else
		{
			$limit = $config_fc['test']['default_number_of_tests'];
		}

	
#####################
# Create temporary table for all possible values for the current test.
		$sql =	'CREATE TEMPORARY TABLE IF NOT EXISTS tmp_' . $user->data['user_id'] . ' ( `id` INT(11) NOT NULL, `heb` VARCHAR(127) NOT NULL) ENGINE=MyISAM  DEFAULT CHARSET=utf8;';
		$this->record_debug( 'build_questions() SQL: ' . $sql );
		$result = $fc_db->query($sql);
		
# Now if mode intellectual we add one more chanse for every mistake in the last five tests.
		if ( isset ( $_POST['intel_mode'] ) )
		{
			switch ( $_POST['intel_mode'] )
			{
				case ( 'intellectual' ):
# First of all, we create a table with 1 example of each word following requirements
					$sql =	'INSERT INTO `tmp_' . $user->data['user_id'] . '` (id, heb)'
						.	' SELECT'
						.	' w.`' . $fc_db_struct[FC_WORDS_TABLE]['id'] . '` AS id'
						.	', w.`' . $fc_db_struct[FC_WORDS_TABLE]['heb'] . '` AS heb'
						.	' FROM `' . FC_WORDS_TABLE . '` AS w'
						.	' LEFT JOIN `' . FC_LESSONS_TABLE . '` AS l'
						.	' ON l.`' . $fc_db_struct[FC_LESSONS_TABLE]['word_id'] . '` = w.`' . $fc_db_struct[FC_WORDS_TABLE]['id'] . '`'
						.	' WHERE 1'
						.	$sql_select_lesson
						.	$sql_select_part_of_speech
						.	$sql_complicated_search
						.	' GROUP BY w.`' . $fc_db_struct[FC_WORDS_TABLE]['id'] . '`'
						.	' ORDER BY RAND()'
						.	';';
					if ( $GLOBALS['debug_all'] == true ) echo '<br>' . $sql;
					if ( $GLOBALS['debug_log'] == true ) $this->record_debug( 'build_questions() SQL: ' . $sql );
					$result = $fc_db->query($sql);

# FIXMELATER Think about this method someday. Logic description is in the forum.
					$sql =	'DELETE t FROM `tmp_' . $user->data['user_id'] . '`  AS t'
						.	' INNER JOIN `' . FC_DATA_TABLE . '` AS d'
						.	' ON d.`' . $fc_db_struct[FC_DATA_TABLE]['word_id'] . '` = t.`id`'
						.	' LEFT JOIN `' . FC_WORDS_TABLE . '` AS w'
						.	' ON w.`' . $fc_db_struct[FC_WORDS_TABLE]['id'] . '` = d.`' . $fc_db_struct[FC_DATA_TABLE]['word_id'] . '`'
						.	' LEFT JOIN `' . FC_LESSONS_TABLE . '` AS l'
						.	' ON l.`' . $fc_db_struct[FC_LESSONS_TABLE]['word_id'] . '` = d.`' . $fc_db_struct[FC_DATA_TABLE]['word_id'] . '`'
						.	' LEFT JOIN `' . FC_SESSIONS_TABLE . '` AS s'
						.	' ON s.`' . $fc_db_struct[FC_SESSIONS_TABLE]['id'] . '` = d.`' . $fc_db_struct[FC_DATA_TABLE]['session_id'] . '`'
						.	' WHERE('
							.	' SELECT COUNT(*) FROM `' . FC_DATA_TABLE . '` AS d1'
							.	' WHERE d1.`' . $fc_db_struct[FC_DATA_TABLE]['word_id'] . '` = d.`' . $fc_db_struct[FC_DATA_TABLE]['word_id'] . '`'
							.	' AND d1.`' . $fc_db_struct[FC_DATA_TABLE]['id'] . '` >= d.`' . $fc_db_struct[FC_DATA_TABLE]['id'] . '`'
						.	') <= ' . $config_fc['test']['last_questions_number_intellectual']
						.	' AND d.`' . $fc_db_struct[FC_DATA_TABLE]['result'] . '` >= ' . $config_fc['test']['min_correct_result_type_intellectual']
						.	' AND (UNIX_TIMESTAMP(NOW()) - d.`' . $fc_db_struct[FC_DATA_TABLE]['time'] . '`) < '	. $config_fc['test']['intel_timeout_to_skip_correct']
						.	' AND s.`' . $fc_db_struct[FC_SESSIONS_TABLE]['user_id'] . '` = \'' . $user->data['user_id'] . '\'' 
						.	$sql_select_lesson
						.	$sql_select_part_of_speech
						.	$sql_complicated_search
						.	' ;';
					if ( $GLOBALS['debug_log'] == true ) $this->record_debug( 'build_questions() SQL: ' . $sql );	
					$result = $fc_db->query($sql);
# Inserting old mistakes
					$sql =	'INSERT INTO `tmp_' . $user->data['user_id'] . '` (id, heb)'
						.	' SELECT d.`' . $fc_db_struct[FC_DATA_TABLE]['word_id'] .'` AS id'
						.	', w.`' . $fc_db_struct[FC_WORDS_TABLE]['heb'] . '` AS heb'
						.	' FROM `' . FC_DATA_TABLE . '` AS d'
						.	' LEFT JOIN `' . FC_WORDS_TABLE . '` AS w'
						.	' ON w.`' . $fc_db_struct[FC_WORDS_TABLE]['id'] . '` = d.`' . $fc_db_struct[FC_DATA_TABLE]['word_id'] . '`'
						.	' LEFT JOIN `' . FC_LESSONS_TABLE . '` AS l'
						.	' ON l.`' . $fc_db_struct[FC_LESSONS_TABLE]['word_id'] . '` = d.`' . $fc_db_struct[FC_DATA_TABLE]['word_id'] . '`'
						.	' LEFT JOIN `' . FC_SESSIONS_TABLE . '` AS s'
						.	' ON s.`' . $fc_db_struct[FC_SESSIONS_TABLE]['id'] . '` = d.`' . $fc_db_struct[FC_DATA_TABLE]['session_id'] . '`'
						.	' WHERE('
							.	' SELECT COUNT(*) FROM `' . FC_DATA_TABLE . '` AS d1'
							.	' WHERE d1.`' . $fc_db_struct[FC_DATA_TABLE]['word_id'] . '` = d.`' . $fc_db_struct[FC_DATA_TABLE]['word_id'] . '`'
							.	' AND d1.`' . $fc_db_struct[FC_DATA_TABLE]['id'] . '` >= d.`' . $fc_db_struct[FC_DATA_TABLE]['id'] . '`'
						.	') <= ' . $config_fc['test']['last_questions_number_intellectual']
						.	' AND d.`' . $fc_db_struct[FC_DATA_TABLE]['result'] . '` < ' . $config_fc['test']['min_correct_result_type_intellectual']
						.	' AND s.`' . $fc_db_struct[FC_SESSIONS_TABLE]['user_id'] . '` = \'' . $user->data['user_id'] . '\'' 
						.	$sql_select_lesson
						.	$sql_select_part_of_speech
						.	$sql_complicated_search
						.	' ORDER BY d.`' . $fc_db_struct[FC_DATA_TABLE]['word_id'] . '`'
						.	';';
					if ( $GLOBALS['debug_log'] == true ) $this->record_debug( 'build_questions() SQL4: ' . $sql );								
					$result = $fc_db->query($sql);
					break;

				case ( 'mistakes' ):
					$sql =	'INSERT INTO `tmp_' . $user->data['user_id'] . '` (id, heb)'
						.	' SELECT d.`' . $fc_db_struct[FC_DATA_TABLE]['word_id'] .'` AS id'
						.	', w.`' . $fc_db_struct[FC_WORDS_TABLE]['heb'] . '` AS heb'
						.	' FROM `' . FC_DATA_TABLE . '` AS d'
						.	' LEFT JOIN `' . FC_WORDS_TABLE . '` AS w'
						.	' ON w.`' . $fc_db_struct[FC_WORDS_TABLE]['id'] . '` = d.`' . $fc_db_struct[FC_DATA_TABLE]['word_id'] . '`'
						.	' LEFT JOIN `' . FC_LESSONS_TABLE . '` AS l'
						.	' ON l.`' . $fc_db_struct[FC_LESSONS_TABLE]['word_id'] . '` = d.`' . $fc_db_struct[FC_DATA_TABLE]['word_id'] . '`'
						.	' LEFT JOIN `' . FC_SESSIONS_TABLE . '` AS s'
						.	' ON s.`' . $fc_db_struct[FC_SESSIONS_TABLE]['id'] . '` = d.`' . $fc_db_struct[FC_DATA_TABLE]['session_id'] . '`'
						.	' WHERE('
							.	' SELECT COUNT(*) FROM `' . FC_DATA_TABLE . '` AS d1'
							.	' WHERE d1.`' . $fc_db_struct[FC_DATA_TABLE]['word_id'] . '` = d.`' . $fc_db_struct[FC_DATA_TABLE]['word_id'] . '`'
							.	' AND d1.`' . $fc_db_struct[FC_DATA_TABLE]['id'] . '` >= d.`' . $fc_db_struct[FC_DATA_TABLE]['id'] . '`'
						.	') <= ' . $config_fc['test']['last_questions_number_intellectual']
						.	' AND d.`' . $fc_db_struct[FC_DATA_TABLE]['result'] . '` < 14'
						.	' AND s.`' . $fc_db_struct[FC_SESSIONS_TABLE]['user_id'] . '` = \'' . $user->data['user_id'] . '\'' 
						.	$sql_select_lesson
						.	$sql_select_part_of_speech
						.	$sql_complicated_search
						.	';';
					if ( $GLOBALS['debug_all'] == true ) echo '<br>Intel mode SQL #1!<br>' . $sql;
					if ( $GLOBALS['debug_log'] == true ) $this->record_debug( 'build_questions() SQL2: ' . $sql );			
					$result = $fc_db->query($sql);
					break;
				case ( 'random' );
				default:
					$sql =	'INSERT INTO `tmp_' . $user->data['user_id'] . '` (id, heb)'
						.	' SELECT'
						.	' w.`' . $fc_db_struct[FC_WORDS_TABLE]['id'] . '` AS id'
						.	', w.`' . $fc_db_struct[FC_WORDS_TABLE]['heb'] . '` AS heb'
						.	' FROM `' . FC_WORDS_TABLE . '` AS w'
						.	' LEFT JOIN `' . FC_LESSONS_TABLE . '` AS l'
						.	' ON l.`' . $fc_db_struct[FC_LESSONS_TABLE]['word_id'] . '` = w.`' . $fc_db_struct[FC_WORDS_TABLE]['id'] . '`'
						.	' WHERE 1'
						.	$sql_select_lesson
						.	$sql_select_part_of_speech
						.	$sql_complicated_search
						.	' GROUP BY w.`' . $fc_db_struct[FC_WORDS_TABLE]['id'] . '`'
						.	' ORDER BY RAND()'
						.	';';
					if ( $GLOBALS['debug_all'] == true ) echo '<br>' . $sql;
					if ( $GLOBALS['debug_log'] == true )$this->record_debug( 'build_questions() SQL2: ' . $sql );
					$result = $fc_db->query($sql);
					break;
			}
		}

# Now we select	using the requested language
		switch ( substr( $_SESSION['fc']['test_type'], 0, 3 ) )
		{
			case ( 'heb' ):
			default:
				$sql =	'SELECT DISTINCT ( id ) as qid, heb as qname FROM tmp_' . $user->data['user_id']
					.	' ORDER BY RAND()'
					.	' LIMIT ' . $limit . ';';
				break;
			case ( 'rus' ):
				$sql =	'SELECT DISTINCT ( t.id ) AS qid'
					.	', r.`' . $fc_db_struct[FC_WORDS_RUS_TABLE]['rus'] . '` as qname'
					.	'  FROM tmp_' . $user->data['user_id'] . ' AS t'
					.	' LEFT JOIN `' . FC_HEB_RUS_TABLE . '` as c'
					.	' ON c.`' . $fc_db_struct[FC_HEB_RUS_TABLE]['heb_id'] . '` = t.id'
					.	' LEFT JOIN `' . FC_WORDS_RUS_TABLE . '` AS r'
					.	' ON r.`' . $fc_db_struct[FC_WORDS_RUS_TABLE]['id'] . '` = c.`' . $fc_db_struct[FC_HEB_RUS_TABLE]['rus_id'] . '`'
					.	' WHERE 1' 
					.	' ORDER BY RAND()'
					.	' LIMIT ' . $limit . ';';
				break;
		}
		if ( $GLOBALS['debug_all'] == true ) echo '<br>' . $sql;
		if ( $GLOBALS['debug_log'] == true ) $this->record_debug( 'build_questions() SQL_SELECT: ' . $sql );			
		$result = $fc_db->query($sql);

# FIXME Should use some sort assign() function here to record assignments in DB.
		while ( $row = $fc_db->fetch_assoc($result) ) 
		{
			$_SESSION['fc']['questions'][] = array($row['qid'], $row['qname'], 0, -1);
		}
# DEBUG		view ( $_SESSION['fc']['questions'] );
	}
	
	function guest()
	{
		global $template;

		$template->set_filenames(array(
			'body' => '../../../fc/templates/main/guest.html'
			)
		);
	}

	function control_session ($action = 'start', $session_id = NULL, $result = NULL)
	{
		global $fc_db, $fc_db_struct, $user, $template;
		
		switch ( $action ):
		case 'start':
		default:
	
			$sql = 'INSERT INTO `' . FC_SESSIONS_TABLE . '` ( '
				. '`' . $fc_db_struct[FC_SESSIONS_TABLE]['user_id'] . '`, '
				. '`' . $fc_db_struct[FC_SESSIONS_TABLE]['start_time'] . '` '
				. ' ) VALUES ( '
				. '\'' . $user->data['user_id'] . '\', '
				. '\'' . time() . '\' '
				. ' ); ';

			if ( $GLOBALS['debug_all'] == true ) echo '<br>' . $sql . '<br>';
			$result = $fc_db->query($sql);
	
			if ( $GLOBALS['debug_all'] == true ) echo '<br> Latest id is: ' . $fc_db->insert_id() . '<br>';
			$_SESSION['fc']['session_id'] = $fc_db->insert_id();
			return( $_SESSION['fc']['session_id'] );		
		break;
		case 'close':
		
# IF no session ID is specified, we check if there are any "hung" sessions of this user and close them
			if ( !isset( $session_id ) )
			{
				if ( $GLOBALS['debug_all'] == true ) echo '<br>Closing hungup session in DB.';
				$sql = 'SELECT `' . $fc_db_struct[FC_SESSIONS_TABLE]['id'] . '` FROM `' . FC_SESSIONS_TABLE . '`'
					.	' WHERE `' . $fc_db_struct[FC_SESSIONS_TABLE]['user_id'] . '` = \'' . $user->data['user_id'] . '\''
					.	' AND `' . $fc_db_struct[FC_SESSIONS_TABLE]['end_time'] . '` IS NULL ';
				if ( $GLOBALS['debug_all'] == true ) echo '<br>' . $sql;
				$result = $fc_db->query( $sql );
				
				if ( $fc_db->num_rows( $result ) == 0 )
				{
					return 0;
				}
				else 
				{
					while( $row = $fc_db->fetch_array( $result ) )
					{
						$sql_c = 'UPDATE `' . FC_SESSIONS_TABLE . '` SET '
							. '`' . $fc_db_struct[FC_SESSIONS_TABLE]['end_time'] . '` ='
							. ' \'' . time() . '\','
							. ' `' . $fc_db_struct[FC_SESSIONS_TABLE]['result'] . '` = NULL'
							. ' WHERE '
							. '`' . $fc_db_struct[FC_SESSIONS_TABLE]['id'] . '`'
							. ' = ' . $row[0] . ';';
						if ( $GLOBALS['debug_all'] == true ) echo '<br>' . $sql_c;
						$result_c = $fc_db->query($sql_c);
					}
					return 1;
				}
			}

# If we have session_id from the input parameters, then we check the test result and close the session
			$result = ( !isset( $result ) ) ? $this->calculate_total_result() : $result;

			$sql = 'UPDATE `' . FC_SESSIONS_TABLE . '` SET '
				. '`' . $fc_db_struct[FC_SESSIONS_TABLE]['end_time'] . '` ='
				. ' \'' . time() . '\','
				. ' `' . $fc_db_struct[FC_SESSIONS_TABLE]['result'] . '` ='
				. ' \'' . $result . '\''
				. ' WHERE '
				. '`' . $fc_db_struct[FC_SESSIONS_TABLE]['id'] . '`'
				. ' = ' . $session_id . ';';
		
			$template->assign_var( 'L_STATUS_FIELD', $template->_tpldata['.']['0']['L_STATUS_FIELD'] . '<br><br>Тест окончен<br> Ваш результат: ' . $result * 100 . '%' );
# DEBUG		view($_SESSION['fc']['questions']);
			$_SESSION['fc']['questions'] = false;
			$_SESSION['fc']['session_id'] = false;
# DEBUG		echo '<br>' . $sql . '<br>';
			$result = $fc_db->query($sql);
			return;

		break;
		endswitch;

	}
	
# Now we simply calculate good vs bad answers, while it is planned to calculate with more complex formulas
# For example we might take into account the number of good/bad attempts from previous tests and not exact matches from letter mapping.
	function calculate_total_result() 
	{
		global $fc_db, $fc_db_struct;
		
		$good = 0;
		foreach ( $_SESSION['fc']['questions'] as $key => $val )
		{
			if ( $val['3'] == RESULT_GOOD || $val['3'] == RESULT_GOOD_NOT_DEFAULT || $val['3'] == RESULT_GOOD_SYNONYM )
			{
				$good++;
			}
		}
		$result = $good / count( $_SESSION['fc']['questions'] );
		
		return $result;
	}
	
# This function is used for quick summon of lessons that are already predifined and there is no need to run build_questions() analytics for them.
	function add_predefined_lesson_words( $lesson_id )
	{
		global $user, $fc_db, $fc_db_struct;

		if ( ! isset( $lesson_id ) || ! $lesson_id = intval( $lesson_id ) )
		{
			if ( $GLOBALS['debug_log'] == true ) $this->record_debug( 'Function add_predefined_lesson_words() received incorrect input lesson_id' );
			return false;
		}

		if ( ! $this->check_lesson_acc_rights( $lesson_id ) )
		{
			if ( $GLOBALS['debug_log'] == true ) $this->record_debug( 'A lesson with no access rights was requested in add_predefined_lesson_words() by user: ' . $user->data['user_id'] . ', lesson: ' . $val );
			return false;
		}

		switch ( substr( $_SESSION['fc']['test_type'], 0, 3 ) )
		{
			case ( 'heb' ):
			default:
				$sql =	'SELECT DISTINCT ( w.`' . $fc_db_struct[FC_WORDS_TABLE]['id'] . '` ) AS qid'
					.	', w.`' . $fc_db_struct[FC_WORDS_TABLE]['heb'] . '` AS qname'
					.	' FROM `' . FC_WORDS_TABLE . '` AS w'
					.	' LEFT JOIN `' . FC_LESSONS_TABLE . '` AS l ON l.`' . $fc_db_struct[FC_LESSONS_TABLE]['word_id'] . '` = w.`' . $fc_db_struct[FC_WORDS_TABLE]['id'] . '`'
					.	' WHERE l.`' . $fc_db_struct[FC_LESSONS_TABLE]['id'] . '` = \'' . $lesson_id . '\''
					.	' ORDER BY RAND()'
					.	';';
				break;
			case ( 'rus' ):
				$sql =	'SELECT DISTINCT ( w.`' . $fc_db_struct[FC_WORDS_TABLE]['id'] . '` ) AS qid'
					.	', r.`' . $fc_db_struct[FC_WORDS_RUS_TABLE]['rus'] . '` as qname'
					.	' FROM `' . FC_WORDS_TABLE . '` AS w'
					.	' LEFT JOIN `' . FC_LESSONS_TABLE . '` AS l ON l.`' . $fc_db_struct[FC_LESSONS_TABLE]['word_id'] . '` = w.`' . $fc_db_struct[FC_WORDS_TABLE]['id'] . '`'
					.	' LEFT JOIN `' . FC_HEB_RUS_TABLE . '` as c ON c.`' . $fc_db_struct[FC_HEB_RUS_TABLE]['heb_id'] . '` = w.id'
					.	' LEFT JOIN `' . FC_WORDS_RUS_TABLE . '` AS r ON r.`' . $fc_db_struct[FC_WORDS_RUS_TABLE]['id'] . '` = c.`' . $fc_db_struct[FC_HEB_RUS_TABLE]['rus_id'] . '`'
					.	' WHERE l.`' . $fc_db_struct[FC_LESSONS_TABLE]['id'] . '` = \'' . $lesson_id . '\''
					.	' ORDER BY RAND()'
					. ';';
				break;
		}

		if ( $GLOBALS['debug_all'] == true ) echo '<br>' . $sql;
		if ( $GLOBALS['debug_log'] == true ) $this->record_debug( 'add_predefined_lesson_words() SQL_SELECT: ' . $sql );

		$result = $fc_db->query($sql);

		while ( $row = $fc_db->fetch_assoc($result) )
		{
			$this->assign_word_to_session( $row['qid'], $row['qname'] );
		}
//		view ( $_SESSION['fc']['questions'] );
	}

# FIXME	This function should write assignments to DB.
	function assign_word_to_session( $qid, $qname, $session_id = NULL )
	{
		$_SESSION['fc']['questions'][] = array($qid, $qname, 0, -1);
	}


#####################
# Set language and direction
	function define_test_language()
	{
		global $config_fc;

		$test_language = ( isset( $_POST['test_language'] ) ) ? mysql_escape_string( $_POST['test_language'] ) : $config_fc['test']['default_test_language'];
		$test_direction = ( isset( $_POST['test_direction'] ) ) ? mysql_escape_string( $_POST['test_direction'] ) : $config_fc['test']['default_test_direction'];

		if ( $test_direction == 'to' )
		{
			$_SESSION['fc']['test_type'] = $test_language . '_heb';
		}
		elseif ( $test_direction == 'from' )
		{
			$_SESSION['fc']['test_type'] = 'heb_' . $test_language;
		}
		else
		{
			die( 'Can\'t decide the type of test. Some error with _POST values.' );
		}
		if ( $GLOBALS['debug_all'] == true ) echo '<br>test_type in _SESSION is set to:' . $_SESSION['fc']['test_type'];
	}

# This function checks if the requested lesson is public. This may be important for some workflow.	
	function is_lesson_public( $lesson_id )
	{
		global $fc_db, $fc_db_struct;

		if ( !isset( $lesson_id) )
		{
			$this->record_debug( 'is_lesson_public( $lesson_id ) received a bad lesson_id: ' . $lesson_id );
			return false;
		}

		$sql =	'SELECT 1 FROM `' . FC_LESSONS_ACC_RIGHTS_TABLE . '`'
			.	' WHERE `' . $fc_db_struct[FC_LESSONS_ACC_RIGHTS_TABLE]['lesson_id'] . '` = \'' . $lesson_id . '\''
			.	' AND `' . $fc_db_struct[FC_LESSONS_ACC_RIGHTS_TABLE]['group_id'] . '` = \'0\''
			.	';';

		if ( $GLOBALS['debug_all'] == true ) echo '<br>' . $sql;
		if ( $GLOBALS['debug_log'] == true ) $this->record_debug( 'build_questions() SQL_SELECT: ' . $sql );			

		$result = $fc_db->query($sql);
		if ( $fc_db->num_rows( $result ) > 0 )
		{
			return true;
		}
	}
	
# This function checks if the current user has privillege to use the requested lesson
	function check_lesson_acc_rights( $lesson_id )
	{
		global $user, $fc_db, $fc_db_struct;
		
		if ( !isset( $lesson_id) )
		{
			$this->record_debug( 'check_lesson_acc_rights( $lesson_id ) received a bad lesson_id: ' . $lesson_id );
			return false;
		}
		
# First we check if the lesson is public
		if ( $this->is_lesson_public( $lesson_id ) )
		{
			return true;
		}
# Then we check if the user belongs to the group with granted access rights for the lesson
		$sql =	'SELECT 1 FROM `' . FC_LESSONS_NAMES_TABLE . '` AS l'
			.	' LEFT JOIN `' . FC_LESSONS_ACC_RIGHTS_TABLE . '` AS lar ON lar.`' . $fc_db_struct[FC_LESSONS_ACC_RIGHTS_TABLE]['lesson_id'] . '` = l.`' . $fc_db_struct[FC_LESSONS_NAMES_TABLE]['id'] . '`'
			.	' LEFT JOIN `' . BB_USER_GROUP_TABLE . '` AS bb_ug ON bb_ug.`' . $fc_db_struct[BB_USER_GROUP_TABLE]['group_id'] . '` = lar.`' . $fc_db_struct[FC_LESSONS_ACC_RIGHTS_TABLE]['group_id'] . '`'
			
			.	' WHERE l.`' . $fc_db_struct[FC_LESSONS_NAMES_TABLE]['id'] . '` = \'' . $lesson_id . '\'' 
			.	' AND ( bb_ug.`' . $fc_db_struct[BB_USER_GROUP_TABLE]['user_id'] . '` = \'' . $user->data['user_id'] . '\''
			.	' OR lar.`' . $fc_db_struct[FC_LESSONS_ACC_RIGHTS_TABLE]['user_id'] . '` = \'' . $user->data['user_id'] . '\' )'
			.	';';

		if ( $GLOBALS['debug_all'] == true ) echo '<br>' . $sql;
		if ( $GLOBALS['debug_log'] == true ) $this->record_debug( 'check_lesson_acc_rights() SQL_SELECT: ' . $sql );

		$result = $fc_db->query($sql);
		if ( $fc_db->num_rows( $result ) > 0 )
		{
			return true;
		}

		return false;
	}
	
	
	function check_answer()
	{
		global $template, $results, $words, $lang, $config_fc;
		
# DEBUG	view($_SESSION['fc']['questions']);
		if ( !isset( $_POST['answer_index'] ) || !is_numeric( $_POST['answer_index'] ) )
		{
			$template->assign_var( 'L_STATUS_FIELD', 'Что-то поломалось. check_answer() не получил $_POST[\'answer_index\']' );
# FIXMELATER : make exception and log this
			return;
		}
		else
		{
			$index = $_POST['answer_index'];
		}

		if ( !isset($_POST['answer']) || strlen($_POST['answer']) === 0 )
		{
			$template->assign_var( 'L_STATUS_FIELD', $lang['ERR_NO_ANSWER_SUBMITED'] );
			return -1;
		}
# Answer is space or spaces
//		if ( $_POST['answer'] == ' ' || ( substr($_POST['answer'], 0, 1) == ' ' && strlen( trim($_POST['answer']) ) < 2  ) )
		if ( $_POST['answer'] == ' ' )
		{
			$this->show_answer($index, RESULT_SKIPPED);
			$results->save_result_to_db( $_SESSION['fc']['session_id'], $_SESSION['fc']['questions'][$index][0], 0, $_SESSION['fc']['questions'][$index][2] );
			$_SESSION['fc']['questions'][$index][3] = 0;
			return;
		}
		else
		{
# We check validity with original text `as is`, but save to DB safely backslashed
# FIXMELATER Should escape in the save_result_to_db() not here.
			$validity = $words->check_validity( $_SESSION['fc']['questions'][$index][0], $_POST['answer'], $_SESSION['fc']['test_type'] );
			$answer = mysql_real_escape_string( $_POST['answer'] );
			
			$this->show_answer( $index, $validity );
			$results->save_result_to_db( $_SESSION['fc']['session_id'], $_SESSION['fc']['questions'][$index][0], $validity, $_SESSION['fc']['questions'][$index][2], $answer );
			$_SESSION['fc']['questions'][$index][3] = $validity;

			return;
		}
	}
	
# FIXMELATER This extra step for checking validity can be later depriciated. All syntax is $words while logic is in $aliyah->check_answer().
# FIXMELATER Anyaway we should better receive not index in the session, but the question word_id.
	function check_answer_validity( $word_id, $answer )
	{
		global $template, $words;
		
		$validity = $words->check_validity( $_SESSION['fc']['questions'][$answer_index][0], $answer, 'ru' );
		return ( $validity );
	}

	function show_answer( $index, $validity )
	{
		global $template, $words, $lang, $config_fc;
		
		if ( !isset($index) )
		{
			$template->assign_var( 'L_STATUS_FIELD', 'Что-то поломалось. show_answer() не получил $index = $_POST[\'answer_id\']' );
			return;
		}
		
		if ( $GLOBALS['debug_all'] == true ) echo '<br>' . substr( $_SESSION['fc']['test_type'], 0, 3 ) ;
		if ( $GLOBALS['debug_log'] == true ) $this->record_debug( 'show_answer(): ' . $index . ', ' . $validity );			

# We prepare correct answers only if not exact answer was given.
		if ( in_array( $validity, $config_fc['results']['display_correсt_answer'] ) )
		{
			if ( substr( $_SESSION['fc']['test_type'], 0, 3 ) == 'heb' )
			{
				$template->assign_block_vars( 'answer', array(
						'VALUE' => $_SESSION['fc']['questions'][$index][1],
						'TYPE' => 'answer_original_question',
					)
				);
			}
			else
			{
				$template->assign_block_vars( 'answer', array(
						'VALUE' => $words->get_hebrew_by_index( $_SESSION['fc']['questions'][$index][0] ),
						'TYPE' => 'answer_original_question',
					)
				);
			}
# Get correct translation
# FIXME	get languages from $_SESSION
			$correct_answers = $words->get_translation( $_SESSION['fc']['questions'][$index][0], 'heb', 'rus' );
			foreach ( $correct_answers as $key => $val )
			{
				switch ( $val['priority'] )
				{
					case ( '15' ):
					default:
						$type = 'answer_primary';
						break;
					case ( '14' ):
						$type = 'answer_secondary';
						break;
				}
				$template->assign_block_vars('answer', array(
						'ID' => $key,
						'VALUE' => $val['value'],
						'TYPE' => $type,
					)
				);
			}
			reset($correct_answers);
		}
		
		if ( $GLOBALS['debug_all'] == true ) echo $index . '  -  ' . $validity;
		switch ( $validity ) :
			case RESULT_BAD :
			default :
# FIXME Edit template to show answers nicely
				$template->assign_vars( array(
						'L_STATUS_FIELD' => $lang['RESULT_BAD'],
						'L_STATUS_TYPE' => 'status_bad',
						'S_SHOW_ANSWERS' => '1',
					)
				);
			break;
			case RESULT_GOOD :
				$template->assign_vars( array(
						'L_STATUS_FIELD' => $lang['RESULT_GOOD'],
						'L_STATUS_TYPE' => 'status_good'
					)
				);
			break;
			case RESULT_GOOD_NOT_DEFAULT :
				$template->assign_vars( array(
						'L_STATUS_FIELD' => $lang['RESULT_GOOD_NOT_DEFAULT'],
						'L_STATUS_TYPE' => 'status_good',
						'S_SHOW_ANSWERS' => '1',
					)
				);
			break;
			case RESULT_GOOD_SYNONYM :
				$template->assign_vars( array(
						'L_STATUS_FIELD' => $lang['RESULT_GOOD_SYNONYM'],
						'L_STATUS_TYPE' => 'status_good',
						'S_SHOW_ANSWERS' => '1',
					)
				);
			break;
			case RESULT_INACCURATE :
				$template->assign_vars( array(
						'L_STATUS_FIELD' => $lang['RESULT_INACCURATE'],
						'L_STATUS_TYPE' => 'status_bad',
					)
				);
			break;
			case RESULT_SKIPPED :
				$template->assign_vars( array(
						'L_STATUS_FIELD' => $lang['RESULT_SKIPPED'],
						'L_STATUS_TYPE' => 'status_bad',
						'S_SHOW_ANSWERS' => '1',
					)
				);
			break;
		endswitch;
	}
	
	function next()
	{
		global $template, $results, $lang;
		
		if ( isset( $_POST['show_lesson_contents'] ) )
		{
			$this->show_lesson_contents();
			$this->index();
			return;
		}

		if ( $GLOBALS['debug_all'] == true ) view( $_SESSION['fc']['questions'] );
		
# Set template file. Here should also be the switch for different test types.
		$template->set_filenames(array(
			'body' => '../../../fc/templates/main/next.html'
			)
		);

# Set predifined variables
		if ( !isset($template->_tpldata['.']['0']['L_STATUS_FIELD']) || $template->_tpldata['.']['0']['L_STATUS_FIELD'] == '' )
		{
			$template->assign_var( 'L_STATUS_FIELD', 'В этом поле следите за результатами' );
		} # No need in else here.

		$template->assign_vars(array(
			'U_LAUNCHER_FORM_POST' => '?mode=next',
			'L_QUESTION' => '',
			'L_QUESTION_INDEX' => $lang['QUESTION_INDEX'],
			'L_NUMBER_OF' => $lang['NUMBER_OF'],
			)
		);
		
# Check if this is the first launch
		if ( !$_SESSION['fc']['questions'] )
		{
			$this->build_questions();
		}

# Check if the answer is correct and decide if you need to allow next try.
		if ( isset($_POST['answer_index']) )
		{
			$this->check_answer();
		}
		
# Get index of the question to show
# This must be run after check_answer, because we cont on the updated question status in the session
# FIXMELATER use $lang and make test finish a separate function
# FIXMELATER maybe refactor this
		$question_index = 0;

		if ( isset($_POST['answer_index']) )
		{
# Show same question once again
			if ( intval($_SESSION['fc']['questions'][$_POST['answer_index']][3]) == -1 || intval($_SESSION['fc']['questions'][$_POST['answer_index']][3]) == 5 )
			{
				if ( $GLOBALS['debug_all'] == true ) echo '<br>Show the same question once again.';
				$question_index = $_POST['answer_index'];
			}
# Check if session is finished
			elseif ( intval($_POST['answer_index']) == count($_SESSION['fc']['questions']) - 1 )
			{
				$this->finish_test();
/*				if ( $GLOBALS['debug_all'] == true ) echo '<br>This session is finished.';
# Show list of mistakes on the main screen.
				$mistakes = $results->get_session_mistakes( $_SESSION['fc']['session_id'], 'spelling' );
				if ( count( $mistakes ) > 0 )
				{
# FIXME Use $lang				
					$template->assign_var( 'L_RECOMMENDATION_TO_LEARN', 'Рекомендуем повторить следующие слова:' );
				}
				foreach ( $mistakes as $key => $val )
				{
					$template->assign_block_vars( 'mistakes', array(
							'QUESTION' => $val['question'],
							'ANSWER' => $val['answer'],
						)
					);	
				}
		*/		
				$this->control_session('close', $_SESSION['fc']['session_id']);
				return;
			}
			else 
			{
				if ( $GLOBALS['debug_all'] == true ) echo '<br>Show next question.';
				$question_index = $_POST['answer_index'] + 1;
			}
		}

# We check if the session was lost in the middle of the test and then restarted. We simply continue.
# FIXMELATER Maybe we should add some timeout for session lifetime in DB. But later.
		else
		{
			if ( $GLOBALS['debug_all'] == true ) 	echo '<br>Запускаем поиск зависших сессий';
			if ( is_array( $_SESSION['fc']['questions'] ) )
			{
				foreach ( $_SESSION['fc']['questions'] as $key => $val )
				{
					if ( $val[3] == -1 )
					{
						$question_index = $key;
						break;
					}
					else
					{
						$question_index++;
					}
				}
				if ( $GLOBALS['debug_all'] == true ) echo '<br>Получили следующий question_index для старта: ' . $question_index;
#				$template->assign_var( 'L_STATUS_FIELD', $template->_tpldata['.']['0']['L_STATUS_FIELD'] . '<br>Найден незавершённый тест. Продолжаем его.' );
				
	
	# If all the tests are passed but session still open, we just close it and save results in a common way
				if ( $question_index == count( $_SESSION['fc']['questions'] ) - 1 )
				{
					$template->assign_var( 'L_STATUS_FIELD', $template->_tpldata['.']['0']['L_STATUS_FIELD'] . '<br><br><br>Тест окончен' );
					$_SESSION['fc']['questions'] = false;
	# DEBUG			view($_SESSION['fc']['questions']);
	#				$this->close_session();
				}
			}
		}
				
# Show current question and save st_time
		$_SESSION['fc']['questions'][$question_index][2] = time();
		if ( strlen( $_SESSION['fc']['questions'][$question_index][1] ) > 0 ) 
		{
			$template->assign_var( 'L_QUESTION', $_SESSION['fc']['questions'][$question_index][1] );
			$template->assign_var( 'D_QUESTION_INDEX', $question_index + 1 );
			$template->assign_var( 'D_NUMBER_OF_QUESTIONS', count( $_SESSION['fc']['questions'] ) );

		}
		else
		{
			$template->assign_var( 'L_QUESTION', 'Подходящих слов нет!' );
			$this->control_session( 'close' );
			unset( $_SESSION['fc']['questions']);
			unset( $_SESSION['fc']['session_id']);
			unset( $_SESSION['fc']['test_type']);
			return ( 0 );
		}
		
# Draw answer form
		$form_inputs = array(
			array (
				'type' => 'text',
				'id' => 'answer',
				'name' => 'answer',
				'value' => '',
				),
			array(
				'type' => 'hidden',
				'id' => 'answer_index',
				'name' => 'answer_index',
				'value' => $question_index,
				)
			);
		
		foreach ( $form_inputs as $name => $value )
		{
		    $template->assign_block_vars( 'input', array() );	

			foreach ( $value as $key => $val )
			{
				$template->assign_block_vars('input.element', array(
					'NAME'	=> $key,
					'PARAM'	=> $val,
					)
				);
			}
		}
# ENDOF answer form		

	}
		
	function finish_test()
	{
		global $template, $lang, $results;
		
# Show list of mistakes on the main screen.
		$mistakes = $results->get_session_mistakes( $_SESSION['fc']['session_id'], 'spelling' );
		if ( count( $mistakes ) > 0 )
		{
# FIXME Use $lang				
			$template->assign_var( 'L_RECOMMENDATION_TO_LEARN', 'Рекомендуем повторить следующие слова:' );
		}
		foreach ( $mistakes as $key => $val )
		{
			$template->assign_block_vars( 'mistakes', array(
					'QUESTION' => $val['question'],
					'ANSWER' => $val['answer'],
				)
			);	
		}
		
	}		
		
	function output() {
	
		global $template;
		
		foreach ( $template->files as $name => $file ) :
			$template->set_filenames($name);
		endforeach;

	}
	
	function stop() {

		global $template, $fc_db;

		if ( $GLOBALS['debug_log'] == true )
		{
			$template->assign_vars( array(
					'L_DB_QUERIES' => $fc_db->queries,
					'L_DB_QUERY_TIME' => $fc_db->exec_time,
				)
			);
		}
		$fc_db->disconnect();
	}

	function go()
	{
		global $template;
	
		if ( !isset($template->_tpldata['.']['0']['S_USER_LOGGED_IN']) || $template->_tpldata['.']['0']['S_USER_LOGGED_IN'] === false )
		{
			$this->guest();
			return;
		}

		switch ( getvar(MODE_VAR) ) :

    		case MODE_INDEX:
    		default:
    			$this->index();
    		break;
    		case MODE_NEXT:
    			$this->next();
    		break;
    		case MODE_DEBUG:
    			$this->debug();
    		break;
    		case MODE_RESET:
    			$this->reset();
    		break;

    	endswitch;
	}
	
	function get_bbpost( $id )
	{
		global $fc_db, $fc_db_struct, $user, $auth, $db, $template;

		$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../';
		$phpEx = substr(strrchr(__FILE__, '.'), 1);
		include($phpbb_root_path . 'includes/bbcode.' . $phpEx);
		include($phpbb_root_path . 'includes/functions_display.' . $phpEx);

		$user->setup('viewforum');

# We now include some of the forum and topic IDs that will be used throughout the script.
	$search_limit = 1;

#    $forum_id = array(1,2,3,5,6,7,8);
#    $forum_id_where = $this->create_where_clauses($forum_id, 'forum');

    $topic_id = array( $id );
    $topic_id_where = $this->create_where_clauses($topic_id, 'topic');

	$posts_ary = array(
        'SELECT'    => 'p.*, t.*',
        'FROM'      => array(
            POSTS_TABLE     => 'p',
        ),
        'LEFT_JOIN' => array(
            array(
                'FROM'  => array(TOPICS_TABLE => 't'),
                'ON'    => 't.topic_first_post_id = p.post_id'
            )
        ),
        'WHERE'     => str_replace( array('WHERE ', 'topic_id'), array('', 't.topic_id'), $topic_id_where) . '
                        AND t.topic_status <> ' . ITEM_MOVED . '
                        AND t.topic_approved = 1',
        'ORDER_BY'  => 'p.post_id DESC',
    );

	$posts = $db->sql_build_query('SELECT', $posts_ary);

	$posts_result = $db->sql_query_limit($posts, $search_limit);

      while( $posts_row = $db->sql_fetchrow($posts_result) )
      {
         $topic_title       = $posts_row['topic_title'];
         $topic_author       = get_username_string('full', $posts_row['topic_poster'], $posts_row['topic_first_poster_name'], $posts_row['topic_first_poster_colour']);
         $topic_date       = $user->format_date($posts_row['topic_time']);
         $topic_link       = append_sid("{$phpbb_root_path}viewtopic.$phpEx", 'f=' . $posts_row['forum_id'] . '&amp;t=' . $posts_row['topic_id']);

         $post_text = nl2br($posts_row['post_text']);

         $bbcode = new bbcode(base64_encode($bbcode_bitfield));
         $bbcode->bbcode_second_pass($post_text, $posts_row['bbcode_uid'], $posts_row['bbcode_bitfield']);

         $post_text = smiley_text($post_text);

         $template->assign_block_vars('announcements', array(
         'TOPIC_TITLE'       => $topic_title,
         'TOPIC_AUTHOR'       => $topic_author,
         'TOPIC_DATE'       => $topic_date,
         'TOPIC_LINK'       => $topic_link,
         'POST_TEXT'         => $post_text,
         ));
      }
	}

# This is the initiator of the index page newsbox.
# It should summon contents generators, regroup the contents if required and process it for template engine with build_index_table()
	function build_index_mainbox()
	{
		global $lang, $user, $template;
		
		$template->assign_vars(array(
				'S_PAGE_INDEX'	=> true,
				'L_WELCOME_MESSAGE' => '',
//				'L_WELCOME_MESSAGE' => $lang['WELCOME_MESSAGE'],
			)
		);

 		$template->assign_block_vars('index_mainbox', array() );

# Get common statistics for the current user to draw on index page
//		$common_stats = $this->get_user_common_stats( $user->data['user_id'] );
//		$common_stats = $this->get_service_info( $user->data['user_id'] );

// The first element is a hardcoded last news. Should FIX this ASAP.
		$sample = array(
			array( 'value' => $this->get_bbpost( 25 ), 'type' => 'post'), 
			array( 'value' => $this->make_html_table_to_string( $this->get_user_common_stats( $user->data['user_id'] ) ), 'type' => 'table'),
//			array( 'value' => '', 'type' => 'table'),
//			array( 'value' => $this->make_html_table_to_string( $this->get_service_info( $user->data['user_id'] ) ), 'type' => 'table'),
		);
		$this->build_index_table( 'index_mainbox',  $sample, 2, 0);
	}


# Get common statistics table for the user
	function get_user_common_stats ( $u )
	{
		global $fc_db, $fc_db_struct, $user, $config_fc, $lang, $results;
	
		if ( $u != $user->data['user_id'] && $user->data['user_type'] != 3 )
		{
			$this->record_debug( 'get_user_common_stats() was asked for non autorised data for user: ' . $u );
			return NULL;
		}

// FIXME
// DEBUG
		$st_time = 1388534400; // Jan 1, 2014
		$end_time = time();
		
		$result = array();
		
#User ID		
//		$result[] = array( $lang['USER_ID'], $u );

# User name
		$result[] = array( $lang['USER_NAME'], $user->data['username'] );


# Days registered
		$result[] = array( $lang['REGISTERED_TIME'], $this->say_interval( $user->data['user_regdate'], time() ) );

# Days absent
		$result[] = array( $lang['SINCE_LAST_VISIT_TIME'], $this->say_interval( $user->data['user_lastvisit'], time() ) );
		
# Total number of words tried	
		$result[] = array( $lang['TOTAL_WORDS_TRIED'], $results->get_user_test_attempts( $u, NULL, $st_time, $end_time ) );

# Average results
		$test_attempts = ( $results->get_user_test_attempts( $u, NULL, $st_time, $end_time ) == 0 ) ? 1 : $results->get_user_test_attempts( $u, NULL, $st_time, $end_time );
		$result[] = array( $lang['TOTAL_AVG_RESULT'], round( ($results->get_user_test_attempts( $u, RESULT_GOOD_SYNONYM, $st_time, $end_time ) / $test_attempts * 100), 2  ) . '%' );

# Return result		
		return $result;
	}

# This is to show service information about current user, access rights, etc.
	function get_service_info ( $u )
	{
		global $fc_db, $fc_db_struct, $user, $config_fc, $lang, $results;
	
		if ( $u != $user->data['user_id'] && $user->data['user_type'] != 3 )
		{
			$this->record_debug( 'get_service_info() was asked for non autorised data for user: ' . $u );
			return NULL;
		}

// FIXME
// DEBUG
		$st_time = 1388534400; // Jan 1, 2014
		$end_time = time();
		
		$result = array();
//		$robot = new robot();
#User ID		
//		$result[] = array( $lang['USER_ID'], $u );

# User name
		$result[] = array( $lang['USER_NAME'], $user->data['username'] );

# Days absent
		$result[] = array( $lang['SINCE_LAST_VISIT_TIME'], $this->say_interval( $user->data['user_lastvisit'], time() ) );
		
# Debug info
//		$result[] = array( $lang['ARE_YOU_ROBOT_ADMIN'], intval($robot->is_admin()) );
# Return result		
		return $result;
	}

# This function is optimised for Russian language to correctly count and format interval using different words.
	function say_interval( $st, $end )
	{
		global $lang;
		
		$stt = new DateTime( date( 'Y-m-d', $st ) );
		$endt = new DateTime( date( 'Y-m-d', $end) );
		$interval = $stt->diff( $endt );

		
		$say_one = array(1,21,31,41,51,61,71,81,91);
		$say_two = array(2,3,4,22,23,24,32,33,34,42,43,44,52,53,54,62,63,64,72,73,74,82,83,84,92,93,94);

		if ($interval->y) 
		{
			if ( in_array( $interval->y, $say_one ) ) $r = $interval->y . " " . $lang['YEAR'];
			else if ( in_array( $interval->y, $say_two ) ) $r = $interval->y . " " . $lang['2-4-YEARS'];
			else $r = $interval->y . " " . $lang['YEARS'];
		}
		if ($interval->m) 
		{
			if ( in_array( $interval->m, $say_one ) ) $r .= " " . $interval->m . " " . $lang['MONTH'];
			else if ( in_array( $interval->m, $say_two ) ) $r .= " " . $interval->m . " " . $lang['2-4-MONTHS'];
			else $r .= " " . $interval->m . " " . $lang['MONTHS'];
		}
		if ($interval->d) 
		{
			if ( in_array( $interval->d, $say_one ) ) $r .= " " . $interval->d . " " . $lang['DAY'];
			else if ( in_array( $interval->d, $say_two ) ) $r .= " " . $interval->d . " " . $lang['2-4-DAYS'];
			else $r .= " " . $interval->d . " " . $lang['DAYS'];
		}
		
		return $r;
	}

# This is a hardcoded workaround for table inside table. Used instead of creating multiple dimension template.
# FIXMELATER Should be fixed by elemination same as all other interface shit.
	function make_html_table_to_string( $cont, $cols = 2, $hrows = 0, $style = 't_common_stats' )
	{
		if ( !isset( $cont ) || !is_array( $cont ) )
		{
			$this->record_debug( 'make_html_table_to_string() recieved incorrect content: ' . view($cont) );
			return NULL;
		}
		
		$result =	"<table class=\"" . $style . "\">";

		$h = 0;
		foreach ($cont as $key => $val )
		{
			$result .=	"<tr>";
			
			if ( $h < $hrows )
			{
				$open_cell = "<th>";
				$close_cell = "</th>";
			}
			else
			{
				$open_cell = "<td>";
				$close_cell = "</td>";
			}
			
			$i = 0;
			while ( $i < $cols )
			{
				$result .=	$open_cell . $val[$i] . $close_cell;
				$i++;
			}
			$result .=	"</tr>";
			$h++;
		}
		$result .=	"</table>";
		
		return $result;
	}
	
	
# This functions gets $cont array and params to distribute values from it and push to $parent template block_var.
# FIXMELATER This whole workflow is not nice so feel the rewrite it anytime.
	function build_index_table( $parent, $cont, $cols = 1, $hrows = 1 )
	{
		global $lang, $template;

		if ( !isset( $cont ) || !is_array( $cont ) )
		{
			if ( $GLOBALS['debug_err'] == true ) $this->record_debug( 'build_index_table() recieved incorrect content: ' . view($cont) );
			return NULL;
		}

		$c = 0;
		$template->assign_block_vars( $parent . '.index_mainbox_tr', array() );
		foreach ( $cont as $key => $val )
		{		
			if ( $c == $cols )
			{
				$template->assign_block_vars( $parent . '.index_mainbox_tr', array() );
				$c = 0;
			}
			$template->assign_block_vars( $parent . '.index_mainbox_tr.index_mainbox_td', array(
						'TD_CONTENT' => $val['value'],
						'TD_TYPE' => $val['type']
				)
			);
			$c++;
		}
		return 1;
	}

# This function generates an array of lessons available to the $user_id today()
	function get_available_lessons( $user_id, $public = false )
	{
		global $user, $fc_db, $fc_db_struct;

		if ( $user_id != $user->data['user_id'] && $user->data['user_type'] < 2 )
		{
			if ( $GLOBALS['debug_err'] == true ) $this->record_debug( 'get_private_lessons() is not authorised to give lessons info of user_id: ' . $user_id . ' to: ' . $user->data['user_id'] );
			return NULL;
		}

		$lessons = array();

		$sql =	'SELECT l.`' . $fc_db_struct[FC_LESSONS_NAMES_TABLE]['id'] . '` AS id'
			.	', CONCAT( ln.`' . $fc_db_struct[FC_LESSONS_NAMES_TABLE]['rus_name'] . '`, \' \ \ (\', COUNT(*), \')\' ) as name'
			.	' FROM `' . FC_LESSONS_TABLE . '` AS l'
			.	' LEFT JOIN `' . FC_LESSONS_NAMES_TABLE . '` AS ln ON l.`id` = ln.`' . $fc_db_struct[FC_LESSONS_NAMES_TABLE]['id'] . '`'
			.	' WHERE 1'
			.	' AND ln.`' . $fc_db_struct[FC_LESSONS_NAMES_TABLE]['id'] . '` IN ('
				.	' SELECT `' . $fc_db_struct[FC_LESSONS_ACC_RIGHTS_TABLE]['lesson_id'] . '`'
				.	' FROM `' . FC_LESSONS_ACC_RIGHTS_TABLE . '` AS lar '
				.	' LEFT JOIN `' . BB_USER_GROUP_TABLE . '` AS bb_ug ON bb_ug.`' . $fc_db_struct[BB_USER_GROUP_TABLE]['group_id'] . '` = lar.`' . $fc_db_struct[FC_LESSONS_ACC_RIGHTS_TABLE]['group_id'] . '`'
				.	' WHERE 1'
				.	' AND (';
				if ( $public )
				{
					$sql .=	' lar.`' . $fc_db_struct[FC_LESSONS_ACC_RIGHTS_TABLE]['group_id'] . '` = 0';
				}
				else
				{
					$sql .=	' bb_ug.`' . $fc_db_struct[BB_USER_GROUP_TABLE]['user_id'] . '` = \'' . $user_id . '\''
						.	' OR lar.`' . $fc_db_struct[FC_LESSONS_ACC_RIGHTS_TABLE]['user_id'] . '` = \'' . $user_id . '\'';
				}
				$sql .=	')';
				
		$sql .=	')'
			.	' AND ln.`' . $fc_db_struct[FC_LESSONS_NAMES_TABLE]['date_valid'] . '` > \'' . time() . '\''
			.	' GROUP BY l.id'
			.	' ORDER BY `' . $fc_db_struct[FC_LESSONS_NAMES_TABLE]['order'] . '` DESC '
			.	' , `' . $fc_db_struct[FC_LESSONS_NAMES_TABLE]['date_init'] . '` DESC '
			.	';';

		if ( $GLOBALS['debug_all'] == true ) echo '<br>' . $sql;
		if ( $GLOBALS['debug_log'] == true ) $this->record_debug( 'get_available_lessons() SQL: ' . $sql );
		
		$result = $fc_db->query( $sql );

		if ( $fc_db->num_rows( $result ) > 0 )
		{
			while ( $row = $fc_db->fetch_array( $result ) )
			{
				$lessons[] = array( $row[0], $row[1] );
			}
		}
		else
		{
			$this->record_debug( 'get_private_lessons() got no private lessons available for: ' . $user_id );
		}
		return $lessons;
	}

	function set_robot( Robot $robot )
	{
		$this->robot = $robot;
	}

# Check if there exists an unfinished (hanging) test.
	function check_hanging_session()
	{
		global $template, $lang;
		if ( $_SESSION['fc']['session_id'] )
		{
# Exists but the test is empty or no unanswered words left, should close it anyway
			$last_question = end( $_SESSION['fc']['questions'] );
			if ( count( $_SESSION['fc']['questions'] ) == 0 || $last_question[3] != -1 )
			{
				if ( $GLOBALS['debug_log'] == true ) $this->record_debug( 'index(): There was a hanging empty test found. Initiating autoreset.' );
				$this->reset();
			}
# Other way we simply set S_UNIFINISHED_TEST_EXISTS=true. The rest is resolved in templates.
			else
			{
				$template->assign_vars(array(
					'S_UNFINISHED_TEST_EXISTS'	=> true,
					'L_RESET_CURRENT_TEST' 			=> $lang['RESET_CURRENT_TEST'] ,
					'L_RESUME_TEST' 				=> $lang['RESUME_TEST'] ,
					'L_UNIFINISHED_TEST_EXISTS'		=> $lang['UNIFINISHED_TEST_EXISTS'],
					)
				);
			}
		}
	}

# Here will be a lot of BAD BAD BAD interface params. Unfortunately there is no front-end developer in the team yet.
# I know a lot of wrong things below, but this is really not my job. So please simply do not mind while it works.
# Once again, I NEED a front-ender! ... Yep and an algorythm guru... And a programmist to rewrite all this shit...
	function index()
	{
		global $lang, $user, $config_fc, $template, $fc_db, $fc_db_struct;

		$template->set_filenames(array(
			'body' => '../../../fc/templates/main/index.html'
			)
		);

		$template->assign_vars(array(
			'U_LAUNCHER_FORM_POST'	=> '?mode=next',
			'L_START_TEST' 			=> $lang['START_TEST'] ,
			'L_SHOW_QUESTIONS'		=> $lang['SHOW_QUESTIONS'],
			'L_PUBLIC' 			=> $lang['PUBLIC'] ,
			'L_PRIVATE'		=> $lang['PRIVATE'],
			'L_LESSONS'		=> $lang['LESSONS'],
			)
		);
		
# Construct welcome mainbox
		$this->build_index_mainbox();

# Initiate robot for private lessons generation
# Run "worst words" initiator. It will check and update if anything is required.
		$this->set_robot(new ww);
		$this->robot->init();
		$this->robot->go();

# Check if there exists an unfinished (hanging) test.
		$this->check_hanging_session();

# Make <options> for the 'lessons' <select> box.
# Get available public lessons
		$lessons = $this->get_available_lessons( $user->data['user_id'], true );
		foreach ( $lessons as $val )
		{
			$template->assign_block_vars('lesson', array(
					'VALUE'	=> $val[0],
					'DESCRIPTION'	=> $val[1],
					'SELECTED' => ( isset( $_SESSION['fc']['last_lesson'] ) && is_array( $_SESSION['fc']['last_lesson'] ) ) ? in_array( $val[0], $_SESSION['fc']['last_lesson'] ) : false,
				)
			);
		}

# Get available private lessons
		$lessons = $this->get_available_lessons( $user->data['user_id'] );
		foreach ( $lessons as $val )
		{
			$template->assign_block_vars('priv_lesson', array(
					'VALUE'	=> $val[0],
					'DESCRIPTION'	=> $val[1],
					'SELECTED' => ( isset( $_SESSION['fc']['last_lesson'] ) && is_array( $_SESSION['fc']['last_lesson'] ) ) ? in_array( $val[0], $_SESSION['fc']['last_lesson'] ) : false,
				)
			);
		}
		
# Old magic with form inputs.
		$form_inputs = array(
/*			'Количество вопросов' => array (
				'type' => 'text',
				'name' => 'number_of_tests',
				'value' => '3',
				), // */
		);
/*		
		foreach ( $form_inputs as $name => $value )
		{
		    $template->assign_block_vars('input', array(
		        'DESCRIPTION'    => $name,
		    ));	

			foreach ( $value as $key => $val )
			{
				$template->assign_block_vars('input.element', array(
					'NAME'	=> $key,
					'PARAM'	=> $val,
					)
				);
			}
		} // */

# MAGIC with radio and select inputs for the main summon test form.
# Beware of summoning smth ugly and cruel!

		$selects = array(
			'number_of_tests' => array(
				'5'	=> array(
					'value' => '5',
				),
				'10'	=> array(
					'value' => '10',
				),
				'20'	=> array(
					'value' => '20',
				),
				'30'	=> array(
					'value' => '30',
				),
				'50'	=> array(
					'value' => '50',
				),
				'75'	=> array(
					'value' => '75',
				),
				'100'	=> array(
					'value' => '100',
				),
				'1000'	=> array(
					'value' => '1000',
				),
				
			),
		);
		
		$radios = array(
			'Язык' => array(
				'Русский'	=> array(
					'type' => 'radio',
					'name' => 'test_language',
					'value' => 'rus',
					'checked' => 'checked',
				),
				'Español'	=> array(
					'type' => 'radio',
					'name' => 'test_language',
					'value' => 'esp',
					'disabled' => 'disabled',
				),
			),
			'Направление' => array(
				'На иврит'	=> array(
					'type' => 'radio',
					'name' => 'test_direction',
					'value' => 'to',
				),
				'С иврита'	=> array(
					'type' => 'radio',
					'name' => 'test_direction',
					'value' => 'from',
				),
			),
			'Режим' => array(
				'Интеллектуальный'	=> array(
					'id' => 'intel_mode_1',
					'type' => 'radio',
					'name' => 'intel_mode',
					'value' => 'intellectual',
				),
				'Случайный'	=> array(
					'id' => 'intel_mode_2',
					'type' => 'radio',
					'name' => 'intel_mode',
					'value' => 'random',
				),
				'Работа над ошибками'	=> array(
					'id' => 'intel_mode_3',
					'type' => 'radio',
					'name' => 'intel_mode',
					'value' => 'mistakes',
				),
			),
			'Части речи' => array(
				'Глаголы'	=> array(
					'id' => 'part_of_speech_1',
					'type' => 'checkbox',
					'name' => 'part_of_speech[]',
					'value' => 'verb',
				),
				'Существительные'	=> array(
					'id' => 'part_of_speech_2',
					'type' => 'checkbox',
					'name' => 'part_of_speech[]',
					'value' => 'noun',
				),
				'Прилагательные'	=> array(
					'id' => 'part_of_speech_3',
					'type' => 'checkbox',
					'name' => 'part_of_speech[]',
					'value' => 'adjective',
				),
				'Наречия'	=> array(
					'id' => 'part_of_speech_4',
					'type' => 'checkbox',
					'name' => 'part_of_speech[]',
					'value' => 'adverb',
				),
				'Смехуты и словосочетания'	=> array(
					'id' => 'part_of_speech_5',
					'type' => 'checkbox',
					'name' => 'part_of_speech[]',
					'value' => 'smehut',
				),
				'Прочие'	=> array(
					'id' => 'part_of_speech_6',
					'type' => 'checkbox',
					'name' => 'part_of_speech[]',
					'value' => 'others',
				),
			),
		);

# ДАЛЬШЕ ПРОСТО ПИЗДЕЦ!
//		if ( isset( $_SESSION['fc']['last_test_direction'] ) && $_SESSION['fc']['last_test_direction'] == 'to' ) $radios['Направление']['На иврит']['checked'] = 'checked';
		if ( isset( $_SESSION['fc']['last_test_direction'] ) && $_SESSION['fc']['last_test_direction'] == 'from' )
			$radios['Направление']['С иврита']['checked'] = 'checked';
		else $radios['Направление']['На иврит']['checked'] = 'checked';

		if ( isset( $_SESSION['fc']['last_intel_mode'] ) && $_SESSION['fc']['last_intel_mode'] == 'intellectual' )
			$radios['Режим']['Интеллектуальный']['checked'] = 'checked';
		elseif ( isset( $_SESSION['fc']['last_intel_mode'] ) && $_SESSION['fc']['last_intel_mode'] == 'mistakes' )
			$radios['Режим']['Работа над ошибками']['checked'] = 'checked';
		else $radios['Режим']['Случайный']['checked'] = 'checked';

		if ( isset( $_SESSION['fc']['last_part_of_speech'] ) && in_array( 'verb', $_SESSION['fc']['last_part_of_speech'] ) ) $radios['Части речи']['Глаголы']['checked'] = 'checked';
		if ( isset( $_SESSION['fc']['last_part_of_speech'] ) && in_array( 'noun', $_SESSION['fc']['last_part_of_speech'] ) ) $radios['Части речи']['Существительные']['checked'] = 'checked';
		if ( isset( $_SESSION['fc']['last_part_of_speech'] ) && in_array( 'adjective', $_SESSION['fc']['last_part_of_speech'] ) ) $radios['Части речи']['Прилагательные']['checked'] = 'checked';
		if ( isset( $_SESSION['fc']['last_part_of_speech'] ) && in_array( 'adverb', $_SESSION['fc']['last_part_of_speech'] ) ) $radios['Части речи']['Наречия']['checked'] = 'checked';
		if ( isset( $_SESSION['fc']['last_part_of_speech'] ) && in_array( 'smehut', $_SESSION['fc']['last_part_of_speech'] ) ) $radios['Части речи']['Смехуты и словосочетания']['checked'] = 'checked';
		if ( isset( $_SESSION['fc']['last_part_of_speech'] ) && in_array( 'others', $_SESSION['fc']['last_part_of_speech'] ) ) $radios['Части речи']['Прочие']['checked'] = 'checked';
		if ( !isset( $_SESSION['fc']['last_part_of_speech'] ) )
		{
			$radios['Части речи']['Глаголы']['checked'] = 'checked';
			$radios['Части речи']['Существительные']['checked'] = 'checked';
			$radios['Части речи']['Прилагательные']['checked'] = 'checked';
			$radios['Части речи']['Наречия']['checked'] = 'checked';
			$radios['Части речи']['Смехуты и словосочетания']['checked'] = 'checked';
//			$radios['Части речи']['Прочие']['checked'] = 'checked';
		}
		
		if ( isset( $_SESSION['fc']['last_number_of_tests'] ) )
		{
			$selects['number_of_tests'][$_SESSION['fc']['last_number_of_tests']]['selected'] = 'selected';
		}

# FIXME Temporary disabled
			unset( $radios['Части речи']['Прочие']['checked'] );
			$radios['Части речи']['Прочие']['disabled'] = 'disabled';			

# And throw this all to template engine.		
		foreach ( $radios as $name => $value )
		{		
		    $template->assign_block_vars('radios', array(
		        'GROUP_NAME'    => $name,
		    ));	
			foreach ( $value as $key => $val )
			{
				$template->assign_block_vars('radios.sub', array(
					'DESCRIPTION'	=> $key,
					)
				);
				
				foreach ( $val as $k => $v )
				{
					$template->assign_block_vars('radios.sub.el', array(
						'NAME'	=> $k,
						'PARAM'	=> $v,
						)
					);
				}
			} 
		}

# Now selects
		foreach ( $selects as $name => $value )
		{		
		    $template->assign_block_vars('selects', array(
		        'GROUP_NAME'    => $name,
		        'DESCRIPTION'    => $lang[strtoupper( $name )],
		    ));	
			foreach ( $value as $key => $val )
			{
				$template->assign_block_vars('selects.sub', array(
					'DESCRIPTION'	=> $key,
					)
				);
				
				foreach ( $val as $k => $v )
				{
					$template->assign_block_vars('selects.sub.el', array(
						'NAME'	=> $k,
						'PARAM'	=> $v,
						)
					);
				}
			} 
		}
		
	}
	
# This shows all the words of the the POSTed lesson. Should change this one day and move to class.lessons.
	function show_lesson_contents()
	{
		global $fc_db, $fc_db_struct, $lang, $template;
		
# First of all we need to check variables same as build_questions()
#################################
# Set lesson to choose words from
		if ( isset( $_POST['lesson'] ) )
		{
			$lesson = $_POST['lesson'];
		}
		else
		{
# FIXMELATER Should add correct exception here
			echo '<h1>' . $lang['NO_LESSON_SELECTED'] . '</h1>';
			die( '<script language=javascript>window.onload = setTimeout(function() {  window.location="?mode=index"; }, 1000);</script>' );
		}
		
		if ( is_array( $lesson ) )
		{
			$sql_select_lesson = ' AND (';
			
			$i = 0;
			foreach ( $lesson as $val )
			{
				if ( $i > 0 ) 
				{
					$sql_select_lesson .= ' OR';
				}
				$sql_select_lesson .= ' l.`' . $fc_db_struct[FC_LESSONS_TABLE]['id'] . '` = \'' . $val . '\'';
				$i++;
			}
			$sql_select_lesson .= ' )';
		}
		else 
		{
			$sql_select_lesson = ' AND l.`' . $fc_db_struct[FC_LESSONS_TABLE]['id'] . '` = \'' . $lesson . '\'';
		}
###############################
# Set part of speech if defined
		$sql_select_part_of_speech = '';
		if ( isset( $_POST['part_of_speech'] ) && is_array( $_POST['part_of_speech'] ) )
		{
			$part_of_speech = $_POST['part_of_speech'];
		
			$sql_select_part_of_speech .= ' AND (';
			
			$i = 0;
			foreach ( $part_of_speech as $val )
			{
				if ( $i > 0 ) 
				{
					$sql_select_part_of_speech .= ' OR';
				}
				$sql_select_part_of_speech .= ' w.`' . $fc_db_struct[FC_WORDS_TABLE]['part_of_speech'] . '` = \'' . mysql_escape_string( $val ) . '\'';
				$i++;
			}
			$sql_select_part_of_speech .= ' )';
		}

#####################
# Should switch on language for translation here
		$test_language = ( isset( $_POST['test_language'] ) ) ? mysql_escape_string( $_POST['test_language'] ) : $config_fc['test']['default_test_language'];

		$sql =	'SELECT'
			.	' w.`' . $fc_db_struct[FC_WORDS_TABLE]['id'] . '` AS id'
			.	', w.`' . $fc_db_struct[FC_WORDS_TABLE]['heb'] . '` AS heb'
			.	', GROUP_CONCAT( DISTINCT r.`' . $fc_db_struct[FC_WORDS_RUS_TABLE]['rus'] . '`'
			.	' ORDER BY c.`' . $fc_db_struct[FC_HEB_RUS_TABLE]['priority'] . '` DESC SEPARATOR \', \' ) AS translation'
			.	' FROM `' . FC_WORDS_TABLE . '` AS w'
			.	' LEFT JOIN `' . FC_LESSONS_TABLE . '` AS l'
			.	' ON l.`' . $fc_db_struct[FC_LESSONS_TABLE]['word_id'] . '` = w.`' . $fc_db_struct[FC_WORDS_TABLE]['id'] . '`'
			.	' LEFT JOIN `' . FC_HEB_RUS_TABLE . '` AS c'
			.	' ON c.`' . $fc_db_struct[FC_HEB_RUS_TABLE]['heb_id'] . '` = w.`' . $fc_db_struct[FC_WORDS_TABLE]['id'] . '`'
			.	' LEFT JOIN `' . FC_WORDS_RUS_TABLE . '` AS r'
			.	' ON r.`' . $fc_db_struct[FC_WORDS_RUS_TABLE]['id'] . '` = c.`' . $fc_db_struct[FC_HEB_RUS_TABLE]['rus_id'] . '`'
			.	' WHERE 1'
			.	$sql_select_lesson
			.	$sql_select_part_of_speech
			.	' GROUP BY w.`' . $fc_db_struct[FC_WORDS_TABLE]['id'] . '`'
			.	' ORDER BY w.`' . $fc_db_struct[FC_WORDS_TABLE]['part_of_speech'] . '` DESC'
			.	', w.`' . $fc_db_struct[FC_WORDS_TABLE]['heb'] . '`'
			.	';';

		if ( $GLOBALS['debug_all'] == true ) echo '<br>' . $sql;
		$this->record_debug( 'Called show_contents() with SQL: ' . $sql );

		$result = $fc_db->query($sql);

		
		if ( mysql_num_rows( $result ) > 0 )
		{
			$template->assign_var( 'CURRENT_TEST_CONTENTS', $lang['CURRENT_TEST_CONTENTS'] );
			
			$i = 0;
			while ( $row = $fc_db->fetch_assoc( $result ) ) 
			{
				$i++;
				$current_bg_color = ( $i % 2 == 0 )	? 'answer_tr_light' : 'answer_tr_dark';
				$template->assign_block_vars( 'test_contents', array(
						'HEBREW' => $row['heb'],
						'TRANSLATION' => $row['translation'],
						'TR_CLASS' => $current_bg_color,
					)
				);
			}
		}
		else
		{
			# No words selected.
		}
	}

# Temporary logging mechanism.
	function record_debug( $action )
	{
		global $fc_db, $fc_db_struct, $user;

		$sql = 'INSERT INTO `fc_debug` (`user_id`, `record`) VALUES (\'' . $user->data['user_id'] . '\', \'' . mysql_real_escape_string( $action ) . '\');';
		$fc_db->query( $sql );
	}

/* create_where_clauses( int[] gen_id, String type )
* This function outputs an SQL WHERE statement for use when grabbing
* posts and topics */

function create_where_clauses($gen_id, $type)
{
	global $db, $auth, $fc_db;

    $size_gen_id = sizeof($gen_id);

        switch($type)
        {
            case 'forum':
                $type = 'forum_id';
                break;
            case 'topic':
                $type = 'topic_id';
                break;
            default:
                trigger_error('No type defined');
        }

    // Set $out_where to nothing, this will be used of the gen_id
    // size is empty, in other words "grab from anywhere" with
    // no restrictions
    $out_where = '';

    if( $size_gen_id > 0 )
    {
    // Get a list of all forums the user has permissions to read
    $auth_f_read = array_keys($auth->acl_getf('f_read', true));

        if( $type == 'topic_id' )
        {
            $sql     = 'SELECT topic_id FROM ' . TOPICS_TABLE . '
                        WHERE ' .  $db->sql_in_set('topic_id', $gen_id) . '
                        AND ' .  $db->sql_in_set('forum_id', $auth_f_read);

            $result     = $db->sql_query($sql);
//            echo $sql;

                while( $row = $db->sql_fetchrow($result) )
                {
                        // Create an array with all acceptable topic ids
                        $topic_id_list[] = $row['topic_id'];
                }

            unset($gen_id);

            $gen_id = $topic_id_list;
            $size_gen_id = sizeof($gen_id);
        }

    $j = 0;

        for( $i = 0; $i < $size_gen_id; $i++ )
        {
        $id_check = (int) $gen_id[$i];

            // If the type is topic, all checks have been made and the query can start to be built
            if( $type == 'topic_id' )
            {
                $out_where .= ($j == 0) ? 'WHERE ' . $type . ' = ' . $id_check . ' ' : 'OR ' . $type . ' = ' . $id_check . ' ';
            }

            // If the type is forum, do the check to make sure the user has read permissions
            else if( $type == 'forum_id' && $auth->acl_get('f_read', $id_check) )
            {
                $out_where .= ($j == 0) ? 'WHERE ' . $type . ' = ' . $id_check . ' ' : 'OR ' . $type . ' = ' . $id_check . ' ';
            }

        $j++;
        }
    }

    if( $out_where == '' && $size_gen_id > 0 )
    {
        trigger_error('A list of topics/forums has not been created');
    }

    return $out_where;
}

# This is a service function used only manually.
# Should move to another file or better be depricated one day.
	function debug()
	{
		global $template, $words, $results, $fc_db, $fc_db_struct;

		$this->record_debug( 'Called debug()' );

# This helps inserting to lessons quickly :)
		for ( $val = 9; $val < 39; $val++ )
		{
			$sql = 'INSERT INTO `fc_lessons` (id, word_id) VALUES (18, ' . $val . ');';

			$sql = 'INSERT INTO `fc_lessons_acc_rights` (lesson_id, group_id) VALUES ( ' . $val . ', 2 );';

			$this->record_debug( 'Assign to lesson SQL: ' . $sql );
			$result = $fc_db->query( $sql );
		}

		$this->show_lesson_contents();
/*			$sql =	'SELECT * FROM fc_data AS d'
				.	' WHERE('
				.	' SELECT COUNT(*) FROM fc_data AS d1 WHERE d.word_id = d1.word_id AND d1.id < d.id' 
				.	') < 4 '
				.	' AND d.result < 14'
				.	' ORDER BY d.word_id'
				.	';'; // */
 
#			if ( $GLOBALS['debug_all'] == true ) echo '<br>' . $sql;

#		$this->control_session( 'close', 170 );
		
#		$this->build_questions();
#		$my_res = $results->get_session_mistakes(75);
#		if ( isset( $_GET['debug_all'] ) ) view( $my_res );

		$template->set_filenames(array(
			'body' => '../../../fc/templates/main/blank.html'
			)
		);
	
	}
}
?>
