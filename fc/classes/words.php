<?php

/***************************************************************************
 *                              words.php
 *                            -------------------
 *   begin                : Wednesday, Apr 30, 2014
 *   copyright            : (C) 2014 Nikolay Grischenko
 *   email                : me@grischenko.ru
 *
 *   $Id: words.php,v 1.0.0 2014/04/30 21:32:03 $
 *
 *
 ***************************************************************************/

class words {
	
	public $l_from;
	public $l_to;
	
	function check_validity( $hebrew_id, $translation, $test_type = 'heb_rus' )
	{
		global $fc_db, $fc_db_struct, $application;
		
		if ( $GLOBALS['debug_all'] == true ) echo '<br>words->check_validity is called with the following params:' . $hebrew_id . ', ' . $translation . ', ' . $test_type;
		if ( $GLOBALS['debug_log'] == true ) $application->record_debug( 'words->check_validity is called with the following params:' . $hebrew_id . ', ' . $translation . ', ' . $test_type);
# Some safety before DB queries.
		if ( ! isset( $translation ) )
		{
# FIXME : and write to log
			return 0;
		}
		else 
		{
# We do not write this to DB in this function, so we do not need to escape chars.
			$translation = trim( mb_strtolower( $translation, 'UTF-8' ) );
		}
		
		if ( ! isset ( $hebrew_id ) || ! is_numeric( $hebrew_id ) )
		{
# FIXME : and write to log		
			return 0;
		}
		
		if ( strlen( $test_type ) <> 7 )
		{
# FIXME : and write to log
			return 0;
		}
		
		$this->l_from = substr( $test_type, 0, 3 );
		$this->l_to = substr( $test_type, 4, 3 );
		
		switch ( $test_type )
		{
			case ( 'heb_rus' ):
#				$correct_answers = array();
		# DEBUG		echo $hebrew_id . ' ' . $translation . ' ' . $lang;
				
		# We select all the correct answers here		
				$sql =	'SELECT r.`' . $fc_db_struct[FC_WORDS_RUS_TABLE]['id'] . '` AS id'
					.	', r.`' . $fc_db_struct[FC_WORDS_RUS_TABLE]['rus'] . '` AS name'
					.	', c.`' . $fc_db_struct[FC_HEB_RUS_TABLE]['priority'] . '` AS priority'
					.	' FROM `' . FC_HEB_RUS_TABLE . '` AS c'
					.	' LEFT JOIN `' . FC_WORDS_RUS_TABLE . '` AS r'
					.	' ON c.`' . $fc_db_struct[FC_HEB_RUS_TABLE]['rus_id'] . '` = r.`' . $fc_db_struct[FC_WORDS_RUS_TABLE]['id'] . '`'
					.	' WHERE c.`' . $fc_db_struct[FC_HEB_RUS_TABLE]['heb_id'] . '`'
					.	' LIKE \'' . $hebrew_id . '\''
					.	' ORDER BY c.`' . $fc_db_struct[FC_HEB_RUS_TABLE]['priority'] . '`;';
				break;
			case ( 'rus_heb' ):
			default:
				$sql =	'SELECT w.`' . $fc_db_struct[FC_WORDS_TABLE]['id'] . '` AS id'
					.	', w.`' . $fc_db_struct[FC_WORDS_TABLE]['heb'] . '` AS name'
					.	' FROM `' . FC_WORDS_TABLE . '` AS w'
					.	' WHERE w.`' . $fc_db_struct[FC_WORDS_TABLE]['id'] . '`'
					.	' = \'' . $hebrew_id . '\''
					.	';';
				break;
		}

		if ( $GLOBALS['debug_all'] == true ) echo '<br>' . $sql;
		if ( $GLOBALS['debug_log'] == true ) $application->record_debug( $sql );
		$result = $fc_db->query( $sql );
		
		if ( $fc_db->num_rows( $result ) == 0 )
		{
			return ( 1 );
		}
		
		$correct_answers = array();
		while ( $row = $fc_db->fetch_assoc( $result ) )
		{
			$correct_answers[] = $row;	
		}
		if ( $GLOBALS['debug_all'] == true ) view( $correct_answers );
		if ( $GLOBALS['debug_all'] == true ) view( $_SESSION['fc']['questions'] );
		
# We check if there is any exact match
# FIXMELATER Should use predifined constants for result types
		foreach ( $correct_answers as $key => $val )
		{
			$correct = trim( mb_strtolower( $val['name'], 'UTF-8' ) );

			if ( $correct == $translation )
			{
				if ( $GLOBALS['debug_all'] == true ) echo '<br>words->l_to: ' . $this->l_to;
				if ( $val['priority'] == 15 || $this->l_to == 'heb' )
				{
					if ( $GLOBALS['debug_log'] == true ) $application->record_debug( 'words->check_validity: answer ' . $translation . ' is correct' );
					return ( 15 );
					break;
				}
				else
				{
					if ( $GLOBALS['debug_log'] == true ) $application->record_debug( 'words->check_validity: answer ' . $translation . ' is almost correct' );
					return ( 14 );
					break;
				}
			}
		}

# Now if XXX_heb we check if there is any other word with same translation.
# FIXMELATER We do not know the exact foreign word that was shown as a question, only the index of the correct hebrew answer
# so we get all the correct foreign words and get all the correspondidng hebrew words for this array.
# This could cause some overlapping, still now no time for FC_RUS_HEB_TABLE filling. Should fix this some other day.

		if ( $test_type == 'rus_heb' )
		{
			$sql =	'SELECT w.`' . $fc_db_struct[FC_WORDS_TABLE]['id'] . '` AS id'
				.	', w.`' . $fc_db_struct[FC_WORDS_TABLE]['heb'] . '` AS name'
				.	' FROM `' . FC_WORDS_RUS_TABLE . '` AS r'
				.	' LEFT JOIN `' . FC_HEB_RUS_TABLE . '` AS c'
				.	' ON c.`' . $fc_db_struct[FC_HEB_RUS_TABLE]['rus_id'] . '` = r.`' . $fc_db_struct[FC_WORDS_RUS_TABLE]['id'] . '`'
				.	' LEFT JOIN `' . FC_WORDS_TABLE . '` AS w'
				.	' ON w.`' . $fc_db_struct[FC_WORDS_TABLE]['id'] . '` = c.`' . $fc_db_struct[FC_HEB_RUS_TABLE]['heb_id'] . '`'
	
				.	' WHERE r.`' . $fc_db_struct[FC_WORDS_RUS_TABLE]['id'] . '` IN'
					.	' ( SELECT DISTINCT r1.`' . $fc_db_struct[FC_WORDS_RUS_TABLE]['id'] . '` AS id'
					.	' FROM `' . FC_HEB_RUS_TABLE . '` AS c1'
					.	' LEFT JOIN `' . FC_WORDS_RUS_TABLE . '` AS r1'
					.	' ON c1.`' .  $fc_db_struct[FC_HEB_RUS_TABLE]['rus_id'] . '` = r1.`' . $fc_db_struct[FC_WORDS_RUS_TABLE]['id'] . '`'
					.	' WHERE c1.`' . $fc_db_struct[FC_HEB_RUS_TABLE]['heb_id'] . '` = \'' . $hebrew_id . '\''
	 			.	' )'
				.	';';
				
			$application->record_debug( 'words->check_validity() looking for synonyms SQL: ' . $sql );			
			$result = $fc_db->query( $sql );
			
			if ( $fc_db->num_rows( $result ) == 0 )
			{
				return ( 1 );
			}

			unset( $correct_answers );
			while ( $row = $fc_db->fetch_assoc( $result ) )
			{
				$correct_answers[] = $row;	
			}
			
# We believe that in this situation there is NO exact match of word_id because we have checked before.
# If we have a syntax match this should be the correct answer, but not the requested one.
# FIXMELATER Should use predifined constants for result types
			foreach ( $correct_answers as $key => $val )
			{
				$correct = trim( mb_strtolower( $val['name'], 'UTF-8' ) );
	
				if ( $correct == $translation )
				{
					return ( 13 );
					break;
				}
			}
		}
	
		
# After the exact match check we should try to find similar matches parsing answers by letters.
# Still now we decide the answer to be wrong.
# FIXMELATER Should use predifined constants for result types
		return ( 1 );
	}
	
	function get_hebrew_by_index( $word_id )
	{
		global $fc_db, $fc_db_struct, $application;

		$application->record_debug( 'words->get_hebrew_by_index() is called with: ' . $word_id );			

		
		if ( !isset( $word_id ) || !is_numeric( $word_id ) )
		{
# FIXMELATER : throw exception and log this			
			return 0;
		}
		
		$sql =	'SELECT `' . $fc_db_struct[FC_WORDS_TABLE]['heb'] . '` AS name'
			.	' FROM `' . FC_WORDS_TABLE . '`'
			.	' WHERE `' . $fc_db_struct[FC_WORDS_TABLE]['id'] . '` = \'' . $word_id . '\''
			.	' LIMIT 1;';
		
		if ( $GLOBALS['debug_all'] == true ) echo '<br>' . $sql;
		if ( $GLOBALS['debug_log'] == true ) $application->record_debug( $sql );
		
		$result = $fc_db->query( $sql );
		if ( $fc_db->num_rows( $result ) > 0 )
		{
			$row = $fc_db->fetch_array( $result );
		}
		
		return $row[0];
	}
	
	
	function get_translation( $input, $lang_from = 'heb', $lang_to = 'rus' )
	{
		global $fc_db, $fc_db_struct, $application;
		$application->record_debug( 'words->get_translation() is called with: ' . $input . ', ' . $lang_from . ', ' . $lang_to );
		
		if ( !isset( $input ) || !is_numeric( $input ) )
		{
# FIXMELATER : throw exception and log this			
			return 0;
		}
		
		$translation = array();
		
		if ( $lang_from == 'heb' && $lang_to == 'rus' )
		{
			$sql =	'SELECT r.`id`, r.`rus`, c.`' . $fc_db_struct[FC_HEB_RUS_TABLE]['priority'] . '`'
				.	' FROM `' . FC_WORDS_RUS_TABLE . '` AS r'
				.	' LEFT JOIN `' . FC_HEB_RUS_TABLE . '` AS c'
				.	' ON r.`' . $fc_db_struct[FC_WORDS_RUS_TABLE]['id'] . '` = c.`' . $fc_db_struct[FC_HEB_RUS_TABLE]['rus_id'] . '`'
				.	' WHERE c.`' . $fc_db_struct[FC_HEB_RUS_TABLE]['heb_id'] . '` = \'' . $input . '\''
				.	' ORDER BY c.`' . $fc_db_struct[FC_HEB_RUS_TABLE]['priority'] . '` DESC ;';
				
			if ( $GLOBALS['debug_all'] == true ) echo '<br>' . $sql;
			if ( $GLOBALS['debug_log'] == true ) $application->record_debug( $sql );
			
			$result = $fc_db->query( $sql );
			if ( $fc_db->num_rows( $result ) > 0 )
			{
				$i = 0;
				while ( $row = $fc_db->fetch_array($result) ) 
				{
					$translation[$i]['id'] = $row[0];
					$translation[$i]['value'] = $row[1];
					$translation[$i]['priority'] = $row[2];
					$i++;
				}
				unset( $i );
			}
		}
		return $translation;
	}
	


}