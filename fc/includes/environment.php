<?php

/***************************************************************************
 *                              environment.php
 *                            -------------------
 *   begin                : Wednesday, Apr 23, 2014
 *   copyright            : (C) 2014 Nikolay Grischenko
 *   email                : me@grischenko.ru
 *
 *   $Id: environment.php,v 1.0.0 2014/04/23 15:59:03 $
 *
 *
 ***************************************************************************/

define('IN_PHPBB', true);

ini_set('error_reporting', E_ALL);

setlocale(LC_ALL, "utf-8");

session_start();

$GLOBALS['config_fc'] = false;

$config_fc= &$GLOBALS['config_fc'];

$config_fc['title'] = 'FC - Aliyah.Co';
$config_fc['page']['title'] = 'Hebrew learning Flash Cards - Aliyah.Co';
$config_fc['var']['phpEx'] = '.php';
$config_fc['path']['root'] = '/home/u19121/phpbb.aliyah.co/www/fc';
$config_fc['path']['includes'] = 'includes';
$config_fc['path']['classes'] = 'classes';
$config_fc['path']['languages'] = 'languages';
$config_fc['path']['template'] = 'templates';
$config_fc['path']['fc_styles'] = '../../../fc/templates/main/'; //relative to main phpBB templates
$config_fc['db']['driver'] = 'mysql';
$config_fc['template']['style'] = 'main';
$config_fc['language']['code'] = 'ru';
$config_fc['uri']['admin'] = 'admin' . $config_fc['var']['phpEx'];
$config_fc['url']['index'] = 'index' . $config_fc['var']['phpEx'];
$config_fc['url']['home'] = 'http://' . $_SERVER['HTTP_HOST'];
$config_fc['test']['default_number_of_tests'] = '10';
$config_fc['test']['default_lesson'] = array('9');
$config_fc['test']['default_test_language'] = 'rus';
$config_fc['test']['default_test_direction'] = 'to';


# add slashes to dir names
function my_addslashes($array) {

	foreach ( $array as $var => $value ) :
		if ( is_array($value) ) :
			$array[$var] = my_addslashes($value);
		else :
			if ( substr($value, -1) != '/' ) :
				$array[$var] = $value . '/';
			endif;
		endif;
	endforeach;
	
	return $array;
}


$config_fc['path'] = my_addslashes($config_fc['path']);

# define all constants
require 'constants' . $config_fc['var']['phpEx'];

# Load phpBB user class
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

# Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

# Load user permissions from phpBB custom field
$user->get_profile_fields( $user->data['user_id'] );
$user_fields = $user->profile_fields;
##############################################
##############################################



# create global MySQL DB class
require $config_fc['path']['includes'] . 'db' . $config_fc['var']['phpEx'];
$GLOBALS['fc_db'] = false;
$fc_db = &$GLOBALS['fc_db'];
$fc_db = new fc_db();
$fc_db->connect();
$fc_db->query('set names UTF8;');

# try using template engine phpbb3
//$GLOBALS['tpl'] = new template ($config_fc['path']['template'] . $config_fc['template']['style']);

/*
# load template engine
require $config_fc['path']['classes'] . 'template.engine' . $config_fc['var']['phpEx'];

 load template itself
$GLOBALS['tpl_fc'] = false;
$tpl_fc = &$GLOBALS['tpl_fc'];
$tpl_fc = new Template_fc ($config_fc['path']['template'] . $config_fc['template']['style']);

# load template config
//require '/home/u19121/phpbb.aliyah.co/www/fc/templates/main/main.config.php';
echo $tpl_fc->root . '/' . $config_fc['template']['style'] . '.config' . $config_fc['var']['phpEx'];
require $GLOBALS['tpl']->root . '/' . $config_fc['template']['style'] . '.config' . $config_fc['var']['phpEx'];

$img = &$GLOBALS['style']['img'];
*/
//require '/home/u19121/phpbb.aliyah.co/www/fc/templates/main/main.config.php';


# common functions
require $config_fc['path']['includes'] . 'functions' .  $config_fc['var']['phpEx'];


// DEBUG
// view($user_fields);
// view($user->data);

# root application
require $config_fc['path']['classes'] . 'aliyah' . $config_fc['var']['phpEx'];
$GLOBALS['application'] = false;
$application = &$GLOBALS['application'];
$application = new aliyah();

# words application
require $config_fc['path']['classes'] . 'words' . $config_fc['var']['phpEx'];
$GLOBALS['words'] = false;
$words = &$GLOBALS['words'];
$words = new words();

# results application
require $config_fc['path']['classes'] . 'results' . $config_fc['var']['phpEx'];
$GLOBALS['results'] = false;
$results = &$GLOBALS['results'];
$results = new results();

//$lang_code = getlang();
// Language automatic switch is turned off while no localizations are ready.
$lang_code = 'ru';

$GLOBALS['lang'] = false;
$lang = &$GLOBALS['lang'];
require $config_fc['path']['languages'] . $lang_code . '/' . 'main' . $config_fc['var']['phpEx'];

$GLOBALS['debug_all'] = false;
$GLOBALS['debug_log'] = true;
$GLOBALS['language_test'] = 'rus';

$config_fc['part_of_speech']['others'] = array( 'preposition', 'conjunction', 'interjection', 'pronoun' );
$config_fc['results']['display_correсt_answer'] = array( RESULT_SKIPPED, RESULT_BAD, RESULT_INACCURATE, RESULT_TWICE_INACCURATE, RESULT_GOOD_SYNONYM, RESULT_GOOD_NOT_DEFAULT );
$config_fc['test']['last_questions_number_intellectual'] = 3;
$config_fc['test']['min_correct_result_type_intellectual'] = 14;
$config_fc['test']['intel_min_correct_result_type'] = 14;
$config_fc['test']['intel_last_questions_number'] = 3;
$config_fc['test']['intel_timeout_to_skip_correct'] = 604800; # 1 week

?>
