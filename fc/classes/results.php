<?php

/***************************************************************************
 *                              results.php
 *                            -------------------
 *   begin                : Saturday, May 3, 2014
 *   copyright            : (C) 2014 Nikolay Grischenko
 *   email                : me@grischenko.ru
 *
 *   $Id: results.php,v 1.0.0 2014/04/23 16:29:03 $
 *
 *
 ***************************************************************************/

class results {

	function save_result_to_db( $session_id, $question_word_id, $result, $st_time, $answer = NULL )
	{
# Save result to DB.
		global $fc_db, $fc_db_struct, $application;
		
# FIXMELATER Add normal exception.
		if ( !isset( $question_word_id ) || !isset( $result ) || ! is_numeric( $question_word_id ) || ! is_numeric( $result ) )
		{
# FIXMELATER : and log this		
			return false;
		}

# If there is some problem with $st_time, we simply make it equal to end_time. This is not important parameter.
		if ( ! isset( $st_time ) || ! is_numeric( $st_time ) )
		{
# FIXMELATER : and log this		
			$st_time = time();
		}
		
		
		$sql 	= 'INSERT INTO `' . FC_DATA_TABLE . '` ('
				. ' `' . $fc_db_struct[FC_DATA_TABLE]['id'] . '`,'
				. ' `' . $fc_db_struct[FC_DATA_TABLE]['start_time'] . '`,'
				. ' `' . $fc_db_struct[FC_DATA_TABLE]['time'] . '`,'
				. ' `' . $fc_db_struct[FC_DATA_TABLE]['session_id'] . '`,'
				. ' `' . $fc_db_struct[FC_DATA_TABLE]['word_id'] . '`,'
				. ' `' . $fc_db_struct[FC_DATA_TABLE]['result'] . '`,'
				. ' `' . $fc_db_struct[FC_DATA_TABLE]['answer'] . '` )'
				. ' VALUES ( NULL,'
				. ' \'' . $st_time . '\','
				. ' \'' . time() . '\','
				. ' \'' . $session_id . '\','
				. ' \'' . $question_word_id . '\','
				. ' \'' . $result . '\','
				. ' \'' . $answer . '\' );';

		if ( $GLOBALS['debug_all'] == true ) echo '<br>' . $sql . '<br>';
		if ( $GLOBALS['debug_log'] == true ) $application->record_debug( $sql );
		$result = $fc_db->query($sql);

		if ( $GLOBALS['debug_all'] == true ) echo '<br> Last data record: ' . $fc_db->insert_id() . '<br>';
		if ( $GLOBALS['debug_log'] == true ) $application->record_debug( 'Last data record: ' . $fc_db->insert_id() );		
		return ( $fc_db->insert_id() );
	}

	function get_session_mistakes( $session_id = NULL, $mistake_type = 'spelling' )
	{
		global $fc_db, $fc_db_struct, $application;
		
		if ( !isset( $session_id ) || !is_numeric( $session_id ) )
		{
			if ( isset( $_SESSION['fc']['session_id'] ) )
			{
				$session_id = $_SESSION['fc']['session_id'];
			}
			else
			{
#FIXMELATER : add exception and log
				return 0;
			}
		}

		$f_result = array();
		$condition = '';	
			
		switch ( $mistake_type )
		{
			case ( 'spelling' ):
			default:
				$condition .= ' AND d.`' . $fc_db_struct[FC_DATA_TABLE]['result'] . '` < 10';
				break;
# This should be invented later. Maybe some average for this student, or average for this word, or smth...
			case ( 'long_time' ):
				$condition .= ' AND 1';
				break;
		}
		
		
		$sql =	'SELECT d.`' . $fc_db_struct[FC_DATA_TABLE]['word_id'] . '` AS q_id'
			.	', w.`' . $fc_db_struct[FC_WORDS_TABLE]['heb'] . '` AS question'
			.	', GROUP_CONCAT( DISTINCT r.`' . $fc_db_struct[FC_WORDS_RUS_TABLE]['id'] . '`'
			.	' ORDER BY c.`' . $fc_db_struct[FC_HEB_RUS_TABLE]['priority'] . '` DESC SEPARATOR \',\' ) AS a_id'
			.	', GROUP_CONCAT( DISTINCT r.`' . $fc_db_struct[FC_WORDS_RUS_TABLE]['rus'] . '`'
			.	' ORDER BY c.`' . $fc_db_struct[FC_HEB_RUS_TABLE]['priority'] . '` DESC SEPARATOR \',\' ) AS answer'
			
			.	' FROM `' . FC_DATA_TABLE . '` AS d'
			.	' LEFT JOIN `' . FC_WORDS_TABLE . '` AS w'
			.	' ON w.`' . $fc_db_struct[FC_WORDS_TABLE]['id'] . '` = d.`' . $fc_db_struct[FC_DATA_TABLE]['word_id'] . '`'
			.	' LEFT JOIN `' . FC_HEB_RUS_TABLE . '` AS c'
			.	' ON c.`' . $fc_db_struct[FC_HEB_RUS_TABLE]['heb_id'] . '` = w.`' . $fc_db_struct[FC_WORDS_TABLE]['id'] . '`'
			.	' LEFT JOIN `' . FC_WORDS_RUS_TABLE . '` AS r'
			.	' ON r.`' . $fc_db_struct[FC_WORDS_RUS_TABLE]['id'] . '` = c.`' . $fc_db_struct[FC_HEB_RUS_TABLE]['rus_id'] . '`'

			.	' WHERE d.`' . $fc_db_struct[FC_DATA_TABLE]['session_id'] . '` = \'' . $session_id . '\''
			.	$condition
			.	' GROUP BY w.`' . $fc_db_struct[FC_WORDS_TABLE]['id'] . '`'
			.	' ORDER BY d.`' . $fc_db_struct[FC_DATA_TABLE]['id'] . '`'
			.	';';
			
		if ( $GLOBALS['debug_all'] == true ) echo '<br>' . $sql;
		if ( $GLOBALS['debug_log'] == true ) $application->record_debug( $sql );
		
		$result = $fc_db->query( $sql );
		
		if ( $fc_db->num_rows( $result ) > 0 )
		{
			$i = 0;
			while ( $row = $fc_db->fetch_assoc( $result ) )
			{
				foreach( $row as $key => $val )
				{
					$f_result[$i][$key] = $val;
				}
				$i++;
			}
		}
		return $f_result;
	}


}