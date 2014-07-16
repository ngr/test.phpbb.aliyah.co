<?php
/***************************************************************************
 *                              functions.php
 *                            -------------------
 *   begin                : Saturday, Jun 03, 2006
 *   copyright            : (C) 2006 RENATA WEB SYSTEMS
 *   email                : alexey@renatasystems.org
 *
 *   $Id: functions.php,v 1.0.0 2006/06/03 15:17:14 i_am_d Exp $
 *
 *
 ***************************************************************************/

function view($var) {
	
	echo '<pre>';
	print_r($var);
	echo '</pre>';
	
	return true;
}

function getvar($var, $array = false) {

	if ( is_array($array) ) :
		$value = isset($array[$var]) ? $array[$var] : false;
	else :
		$value = isset($_REQUEST[$var]) ? $_REQUEST[$var] : false;
	endif;
	
	return $value;
}

function getlang() {

	$accept = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : 'ru';
	if ( substr($accept, 0, 2) == 'en' ) :
		$lang = 'en';
	else :
		$lang = 'ru';
	endif;
   return $lang;
}
?>