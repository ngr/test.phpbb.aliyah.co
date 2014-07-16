<?php
/*
	COPYRIGHT 2012 Damien Keitel
		
	This file is part of Facebook For PhpBB Light.

    Facebook For PhpBB Light is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Facebook For PhpBB Light is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Facebook For PhpBB Light.  If not, see <http://www.gnu.org/licenses/>.*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'FB4PHPBB_LIGHT_LOGIN'							=> 'This site allows you to login using a Facebook account.',
	'ACP_FB4PHPBB_LIGHT_MAIN_MANAGE'					=> 'Facebook For phpBB Light Login Settings',
	'ACP_FB4PHPBB_LIGHT_FB4PHPBB_LIGHT_MANAGE'				=> 'Facebook For PhpBB',
	'FB4PHPBB_LIGHT_LOGIN'							=> 'Select Facebook login',
	'FB4PHPBB_LIGHT_LOGIN_EXPLAIN'					=> 'Using this will enable you to login via facebook.',
	'FB4PHPBB_LIGHT_REGISTER_QUERY'					=> 'Do you wish to register with your Facebook account?',
	'FB4PHPBB_LIGHT_REGISTRATION'						=> 'If you have a facebook account and you would like to use the account with this site please use the Login With Facebook button below.',	
	'FB4PHPBB_LIGHT_LOGIN_UNAVAILABLE'				=> 'Facebook is unavailable at this site.',
	'FB4PHPBB_LIGHT_CONNECT_FAILURE'					=> 'An error occured whilst trying to connect to the phpBB database.',
	'ACP_FB4PHPBB_LIGHT'								=> 'Facebook For phpBB Light',
	'ACP_FB4PHPBB_LIGHT_SETTINGS'						=> 'Facebook For phpBB Light Settings',	
	'ACP_FB4PHPBB_LIGHT_TITLE'						=> 'Facebook For phpBB Light Login Manager',
	'ACP_FB4PHPBB_LIGHT_SETTINGS_UPDATED'				=> 'The Facebook For phpBB Light Login settings have been updated.',
	'ACP_FB4PHPBB_LIGHT_SAVE_ERROR'					=> 'An error occured trying to save your settings.',
	'ACP_FB4PHPBB_LIGHT_SAVE_SUCCESS'					=> 'Your settings have been successfully saved',
	'TITLE_FB4PHPBB_LIGHT_MAIN_SETTINGS'				=> 'Facebook Main Settings',
	'FB4PHPBB_LIGHT_ENABLE'							=> 'Enable Facebook For phpBB Light MOd',
	'FB4PHPBB_LIGHT_LINK'								=> 'Enable Facebook Login/Register',
	'FB4PHPBB_LIGHT_APP_ID'							=> 'Facebook Application Id',
	'FB4PHPBB_LIGHT_SECRET'							=> 'Facebook Secret Key',
	'FB4PHPBB_LIGHT_PAGE'								=> 'Facebook Page ID',
	'FB4PHPBB_LIGHT_PAGE_USERNAME'					=> 'Facebook Page Username',	
	'FB4PHPBB_LIGHT_PAGE_ENABLED'						=> 'Enable Facebook Page',
	'FB4PHPBB_LIGHT_PAGE_LIKE_ENABLED'				=> 'Enable Facebook Page Like Button',	
	'FB4PHPBB_LIGHT_LANG'            					=> 'Facebook Language',
	'FB4PHPBB_LIGHT_ADMINS'           				=> 'Facebook Adminstrators',
	'FB4PHPBB_LIGHT_ADMINS_EXPLAIN'   				=> 'Please use a comma to seperate each admin you enter.',	
	'TITLE_FB4PHPBB_LIGHT_LIKE_SETTINGS'   			=> 'Facebook Like Button Settings',
	'FB4PHPBB_LIGHT_LIKE_ENABLED'						=> 'Use Facebook Like Button',    	
	'FB4PHPBB_LIGHT_SEND_ENABLED'						=> 'Use Facebook Send Button',
	'FB4PHPBB_LIGHT_LIKE_LAYOUT'						=> 'Facebook Like Button Layout Style',
	'FB4PHPBB_LIGHT_LIKE_LAYOUT_STANDARD'   			=> 'Standard',
	'FB4PHPBB_LIGHT_LIKE_LAYOUT_BUTTON_COUNT'   		=> 'Button Count',
	'FB4PHPBB_LIGHT_LIKE_LAYOUT_BOX_COUNT'   			=> 'Box Count',
	'FB4PHPBB_LIGHT_LIKE_ACTION'						=> 'Facebook Like Or Recommend ',
	'FB4PHPBB_LIGHT_LIKE_ACTION_LIKE'					=> 'Like',
	'FB4PHPBB_LIGHT_LIKE_ACTION_RECOMMEND'			=> 'Recommend ',		
	'FB4PHPBB_LIGHT_LIKE_FACES'						=> 'Facebook Like Button Faces',
	'FB4PHPBB_LIGHT_LIKE_FACES_TRUE'					=> 'On',
	'FB4PHPBB_LIGHT_LIKE_FACES_FALSE'					=> 'Off ',
	'FB4PHPBB_LIGHT_LIKE_WIDTH'						=> 'Facebook Like Button Width',
	'FB4PHPBB_LIGHT_LIKE_FONT'						=> 'Facebook Like Button Font ',
	'FB4PHPBB_LIGHT_LIKE_FONT_ARIAL'					=> 'arial',
	'FB4PHPBB_LIGHT_LIKE_FONT_LUCIDA'					=> 'Lucida Grande',
	'FB4PHPBB_LIGHT_LIKE_FONT_SEGOE'					=> 'Segoe Ui',
	'FB4PHPBB_LIGHT_LIKE_FONT_TAHOMA'					=> 'Tahoma',
	'FB4PHPBB_LIGHT_LIKE_FONT_TREBUCHET'				=> 'Trebuchet Ms',
	'FB4PHPBB_LIGHT_LIKE_FONT_VERDANA'				=> 'Verdana',
	'FB4PHPBB_LIGHT_LIKE_COLOR'						=> 'Facebook Like Color ',
	'FB4PHPBB_LIGHT_LIKE_COLOR_LIGHT'					=> 'Light',
	'FB4PHPBB_LIGHT_LIKE_COLOR_DARK'					=> 'Dark',
	'FB4PHPBB_LIGHT_PAGE_SEND_ENABLED'				=> 'Use Facebook Page Send Button',
	'FB4PHPBB_LIGHT_PAGE_LIKE_LAYOUT'					=> 'Facebook Page Like Button Layout Style',
	'FB4PHPBB_LIGHT_PAGE_LIKE_LAYOUT_STANDARD'   		=> 'Standard',
	'FB4PHPBB_LIGHT_PAGE_LIKE_LAYOUT_BUTTON_COUNT'   	=> 'Button Count',
	'FB4PHPBB_LIGHT_PAGE_LIKE_LAYOUT_BOX_COUNT'   	=> 'Box Count',
	'FB4PHPBB_LIGHT_PAGE_LIKE_ACTION'					=> 'Facebook Page Like Or Recommend ',
	'FB4PHPBB_LIGHT_PAGE_LIKE_ACTION_LIKE'			=> 'Like',
	'FB4PHPBB_LIGHT_PAGE_LIKE_ACTION_RECOMMEND'		=> 'Recommend ',		
	'FB4PHPBB_LIGHT_PAGE_LIKE_FACES'					=> 'Facebook Page Like Button Faces',
	'FB4PHPBB_LIGHT_PAGE_LIKE_FACES_TRUE'				=> 'On',
	'FB4PHPBB_LIGHT_PAGE_LIKE_FACES_FALSE'			=> 'Off ',
	'FB4PHPBB_LIGHT_PAGE_LIKE_WIDTH'					=> 'Facebook Page Like Button Width',
	'FB4PHPBB_LIGHT_PAGE_LIKE_FONT'					=> 'Facebook Page Like Button Font ',
	'FB4PHPBB_LIGHT_PAGE_LIKE_FONT_ARIAL'				=> 'arial',
	'FB4PHPBB_LIGHT_PAGE_LIKE_FONT_LUCIDA'			=> 'Lucida Grande',
	'FB4PHPBB_LIGHT_PAGE_LIKE_FONT_SEGOE'				=> 'Segoe Ui',
	'FB4PHPBB_LIGHT_PAGE_LIKE_FONT_TAHOMA'			=> 'Tahoma',
	'FB4PHPBB_LIGHT_PAGE_LIKE_FONT_TREBUCHET'			=> 'Trebuchet Ms',
	'FB4PHPBB_LIGHT_PAGE_LIKE_FONT_VERDANA'			=> 'Verdana',
	'FB4PHPBB_LIGHT_PAGE_LIKE_COLOR'					=> 'Facebook Page Like Color ',
	'FB4PHPBB_LIGHT_PAGE_LIKE_COLOR_LIGHT'			=> 'Light',
	'FB4PHPBB_LIGHT_PAGE_LIKE_COLOR_DARK'				=> 'Dark',
	'FB4PHPBB_LIGHT_COMMENT_COLOR_LIGHT'				=> 'Light',
	'FB4PHPBB_LIGHT_COMMENT_COLOR_DARK'				=> 'Dark',	
	'FB4PHPBB_LIGHT_COMMENT_COLOR'					=> 'Facebook Comment Style',
	'TITLE_FB4PHPBB_LIGHT_COMMENT_SETTINGS'    		=> 'Facebook Comments Box Settings',
	'FB4PHPBB_LIGHT_COMMENT_ENABLED'					=> 'Use Facebook Comments Box',
	'FB4PHPBB_LIGHT_CHAT_ENABLED'						=> 'Use Facebook Chat/Send Box at bottom',	
	'FB4PHPBB_LIGHT_COMMENT_WIDTH'					=> 'Facebook Comments Box Width',
	'FB4PHPBB_LIGHT_COMMENT_POSTS'					=> 'Facebook Comments Box Number Of Posts To Show',  
	'FB_REG_COND' 								=> 'Once registered, your Facebook account will be linked <br /><br />When logging in please use the Connect With Facebook Login button.',	
	'NOT_REGISTERED'							=> 'You are not registered at this site.<br /><br />%sClick here to register%s',
	'LOGIN_FAILURE'								=> 'Login has failed.',
	'LOGIN_SUCCESS'								=> 'You have been successfully logged in!',
	'SIGN_IN'									=> 'Sign In',
	'LOGGED_IN'									=> 'Logged In',
	'FB4PHPBB_LIGHT'        							=> 'Facebook Manager',
	'TITLE_FB4PHPBB_LIGHT_REGO_SETTINGS'      		=> 'Facebook Registration Settings',	
	'FB4PHPBB_LIGHT_REGO_BDAY'      					=> 'Use Birthday In Registration',
	'FB4PHPBB_LIGHT_REGO_GENDER'      				=> 'Use Gender In Registration',
	'FB4PHPBB_LIGHT_REGO_LOCATION'      				=> 'Use Location In Registration',
	'LINK_REMOTE_AVATAR_EXPLAIN_FB'   			=> 'Use Your Facebook Profile Pic As Your Forum Avatar',
	'ALLOW_REMOTE_FB'     						=> 'Enable Facebook Profile Picture As Avatar',
    'RETURN_TIME_FB4PHPBB_LIGHT'                      => 'You will be redirected to the index page in 10 seconds or click this link here ->',	
    'FACEBOOK_CREATE'                           => 'Log into or create a facebook account to link to this forum.'
));

?>