<?php

/***************************************************************************
 *                              environment.admin.php
 *                            -------------------
 *   begin                : Wednesday, Apr 30, 2014
 *   copyright            : (C) 2014 Nikolay Grischenko
 *   email                : me@grischenko.ru
 *
 *   $Id: environment.admin.php,v 1.0.0 2014/04/23 18:26:03 $
 *
 *
 ***************************************************************************/

define('IN_PHPBB', true);

ini_set('error_reporting', E_ALL);

setlocale(LC_ALL, "utf-8");

session_start();

$GLOBALS['config_fc_admin'] = false;

$config_fc_admin= &$GLOBALS['config_fc_admin'];

$config_fc['var']['phpEx'] = '.php';
$config_fc['path']['root'] = '/home/u19121/phpbb.aliyah.co/www/fc/admin';
$config_fc['path']['includes'] = 'includes';
$config_fc['path']['classes'] = 'classes';
$config_fc['path']['languages'] = 'languages';
$config_fc['path']['template'] = 'templates';
$config_fc['path']['fc_styles'] = '../../../../fc/templates/main/'; //relative to main phpBB templates
$config_fc['db']['driver'] = 'mysql';
$config_fc['template']['style'] = 'main';
$config_fc['language']['code'] = 'ru';
$config_fc['uri']['admin'] = 'admin' . $config_fc['var']['phpEx'];
$config_fc['url']['index'] = 'index' . $config_fc['var']['phpEx'];
$config_fc['url']['home'] = 'http://' . $_SERVER['HTTP_HOST'];

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
require 'constants.admin' . $config_fc['var']['phpEx'];
require 'functions.admin' . $config_fc['var']['phpEx'];
require '../../includes/constants' . $config_fc['var']['phpEx'];
require '../../includes/functions' . $config_fc['var']['phpEx'];

# Load phpBB user class
define('IN_PHPBB', true);
$phpbb_root_path = '../../../';
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
require 'db' . $config_fc['var']['phpEx'];
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
require 'functions.admin' .  $config_fc['var']['phpEx'];


// DEBUG
// view($user_fields);
// view($user->data);

# root application
require '../classes/aliyah.admin' . $config_fc['var']['phpEx'];
$GLOBALS['application'] = false;
$application = &$GLOBALS['application'];
$application = new aliyah_admin();


?>
