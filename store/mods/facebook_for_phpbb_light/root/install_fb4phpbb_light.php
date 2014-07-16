<?php
/**
*
* @author DavidIQ (David Colon) davidiq@phpbb.com
* @package umil
* @copyright (c) 2011 DavidIQ
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
define('UMIL_AUTO', true);
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
$user->session_begin();
$auth->acl($user->data);
$user->setup();

if (!file_exists($phpbb_root_path . 'umil/umil_auto.' . $phpEx))
{
	trigger_error('Please download the latest UMIL (Unified MOD Install Library) from: <a href="http://www.phpbb.com/mods/umil/">phpBB.com/mods/umil</a>', E_USER_ERROR);
}

$mod_name = 'ACP_FB4PHPBB_LIGHT';
$version_config_name = 'fb4phpbb_light_version';
$language_file = 'mods/info_acp_fb4phpbb_light';

$versions = array(
	'1.0.2' => array(
	),
	'1.0.1a' => array(

        'table_column_add' => array(
            array(USERS_TABLE, 'user_fb4phpbb_light_fb_uid', array('VCHAR', '0')),
        ),

		'config_add' => array(
			array('fb4phpbb_light_appid', '', 1),
			array('fb4phpbb_light_secret', '', 1),
			array('fb4phpbb_light_mod_enabled', 'yes', 1),
			array('fb4phpbb_light_path', 'fb4phpbb_light/', 0),
            array('fb4phpbb_light_lang', 'en_GB', 1),
            array('allow_avatar_remote_fb', 1, 0),
		), 

		'module_add'    => array(	// Add a main category
            array('acp', 0, 'ACP_FB4PHPBB_LIGHT'),

                // First, lets add a new category named ACP_CAT_TEST_MOD to ACP_CAT_DOT_MODS
            array('acp', 'ACP_FB4PHPBB_LIGHT', 'ACP_FB4PHPBB_LIGHT'),

                // Now we will add the settings and features modes from the acp_board module to the ACP_CAT_TEST_MOD category using the "automatic" method.
            array('acp', 'ACP_FB4PHPBB_LIGHT', array(
                        'module_basename'		=> 'fb4phpbb_light',
                        'modes'					=> array('fb4phpbb_light'),
                ),
            ),

            array('ucp', 0, 'UCP_FB4PHPBB_LIGHT'),

            array('ucp', 'UCP_FB4PHPBB_LIGHT', array(
                'module_basename'		=> 'fb4phpbb_light',
                'modes'					=> array('fb4phpbb_light'),
                ),
            ),
        ),
    ),	
);

include($phpbb_root_path . 'umil/umil_auto.' . $phpEx);
$umil->cache_purge(array(
	array(''),
	array('auth'),
	array('template'),
	array('theme'),
));