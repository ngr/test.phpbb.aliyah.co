<?php

#echo $filename = md5( uniqid( rand() ) ) . '.mp3';

# Load phpBB user class
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '/home/u19121/phpbb.aliyah.co/www/';
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


$config['db']['driver'] = 'mysql';
$config['var']['phpEx'] = '.php';
$config['path']['classes'] = '../classes/';
$config['template']['style'] = 'main';
$config['path']['template'] = '../templates/';
$config['path']['content'] = array(
	'pictures' => '../content/pictures'
);

# define all constants
require '../includes/constants.php';

# load useful functions from main environment
require '../includes/functions.php';


 view($user->data);

# create global MySQL DB class
require '../classes/mysql.engine.php';
$GLOBALS['db'] = false;
$db = &$GLOBALS['db'];
$db = new db();
$db->connect();
$db->query('set names utf8;');

# load user class
/*
require '../classes/user.php';
$user = &$GLOBALS['user'];
$user = new user($_SERVER['REMOTE_USER']);

if ( !$user->admin ) {
	view ($_SERVER['REMOTE_USER']);
	echo "Shalom!";
	view ($_SESSION['userdata']);
	die ('Admins only');
} // */



# load template engine
require $config['path']['classes'] . 'template.engine' . $config['var']['phpEx'];

# load template itself
$GLOBALS['tpl'] = false;
$tpl = &$GLOBALS['tpl'];
$tpl = new template($config['path']['template'] . $config['template']['style'] . '/admin');

# load template config
require $tpl->root . '/' . 'admin.config' . $config['var']['phpEx'];
$img = &$GLOBALS['style']['img'];

require 'classes/aliyah.admin.php';
$application = new aliyah();

require 'includes/functions.admin.php';
require 'includes/constants.admin.php';

$application->page_header();

switch ( getvar(MODE_VAR) ) :
	case MODE_PICTURE_ADD:
		$application->picture_add();
	break;
	case MODE_PICTURE_UPLOAD:
		$application->picture_upload();
	break;
	default:
		echo '<a href="?mode=' . MODE_PICTURE_ADD . '">Add picture</a>';
	break;
endswitch;

$application->page_footer();
$application->output();
?>
