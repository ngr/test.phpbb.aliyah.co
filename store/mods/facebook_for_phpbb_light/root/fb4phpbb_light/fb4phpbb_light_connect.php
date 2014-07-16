<?php

define('IN_PHPBB', true);
$phpbb_root_path 								= (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../';
$phpEx 											= substr(strrchr(__FILE__, '.'), 1);

include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_user.' . $phpEx);

$user->session_begin();
$auth->acl($user->data);
$user->setup('ucp'); 
$user->add_lang('mods/fb4phpbb_light');
$user->add_lang('mods/info_ucp_fb4phpbb_light');

$change_lang									= request_var('change_lang', '');
$user_lang										= request_var('lang', $user->lang_name);
$fb4phpbb_light_uid 									= request_var('id', '');      
$fb4phpbb_light_email 								= request_var('email', '');
$check 											= request_var('check', '');
$link 											= request_var('link', '');
$unlink 											= request_var('ullink', '');
$mode 											= request_var('mode', '');
$params = request_var('params', '');
$value = urldecode(strtolower(request_var('value', '')));
// Function for a nice output <- borrowed function to help better validation
function return_ajax($value = '')
{
	echo($value);
die;
}
if(!empty($params) && !empty($value))
{

    $response ='';
    if($params == 'user_email')
    {
        if(!preg_match('/^' . get_preg_expression('email') . '$/i', $value))
        {
            $response = '<div style="padding:1px;" class="ui-state-error ui-corner-all">' . $value . ' is not valid!</div>'; 
            return_ajax($response);
        die;
        }
    }
    if($params == 'username_clean')
    {
        if (utf8_strlen(htmlspecialchars_decode($value)) < $config['min_name_chars'])
		{
			$response = '<div style="padding:1px;" class="ui-state-error ui-corner-all">' . $user->lang['TOO_SHORT_USERNAME'] . '</div>';
			return_ajax($response);
		die;
		}
		// if username is too long
		if (utf8_strlen(htmlspecialchars_decode($value)) > $config['max_name_chars'])
		{
			$response = '<div style="padding:1px;" class="ui-state-error ui-corner-all">' . $user->lang['TOO_LONG_USERNAME'] . '</div>';
			return_ajax($response);
		die;
		}
        // ... fast checks first.
        if (strpos($value, '&quot;') !== false || strpos($value, '"') !== false)
        {
            $response = '<div style="padding:1px;" class="ui-state-error ui-corner-all">' . $user->lang['INVALID_CHARS_USERNAME'] . '</div>';
            return_ajax($response);
        die;
        }

        $mbstring = $pcre = false;

        // generic UTF-8 character types supported?
        if ((version_compare(PHP_VERSION, '5.1.0', '>=') || (version_compare(PHP_VERSION, '5.0.0-dev', '<=') && version_compare(PHP_VERSION, '4.4.0', '>='))) && @preg_match('/\p{L}/u', 'a') !== false)
        {
            $pcre = true;
        }
        else if (function_exists('mb_ereg_match'))
        {
            mb_regex_encoding('UTF-8');
            $mbstring = true;
        }

        switch ($config['allow_name_chars'])
        {
            case 'USERNAME_CHARS_ANY':
                $pcre = true;
                $regex = '.+';
            break;

            case 'USERNAME_ALPHA_ONLY':
                $pcre = true;
                $regex = '[A-Za-z0-9]+';
            break;

            case 'USERNAME_ALPHA_SPACERS':
                $pcre = true;
                $regex = '[A-Za-z0-9-[\]_+ ]+';
            break;

            case 'USERNAME_LETTER_NUM':
                if ($pcre)
                {
                    $regex = '[\p{Lu}\p{Ll}\p{N}]+';
                }
                else if ($mbstring)
                {
                    $regex = '[[:upper:][:lower:][:digit:]]+';
                }
                else
                {
                    $pcre = true;
                    $regex = '[a-zA-Z0-9]+';
                }
            break;

            case 'USERNAME_LETTER_NUM_SPACERS':
                if ($pcre)
                {
                    $regex = '[-\]_+ [\p{Lu}\p{Ll}\p{N}]+';
                }
                else if ($mbstring)
                {
                    $regex = '[-\]_+ \[[:upper:][:lower:][:digit:]]+';
                }
                else
                {
                    $pcre = true;
                    $regex = '[-\]_+ [a-zA-Z0-9]+';
                }
            break;

            case 'USERNAME_ASCII':
            default:
                $pcre = true;
                $regex = '[\x01-\x7F]+';
            break;
        }

        if ($pcre)
        {
            if (!preg_match('#^' . $regex . '$#u', $value))
            {
                $response = '<div style="padding:1px;" class="ui-state-error ui-corner-all">' . $user->lang['INVALID_CHARS_USERNAME'] . '</div>';
                return_ajax($response);
            die;
            }
        }
        else if ($mbstring)
        {
            mb_ereg_search_init($value, '^' . $regex . '$');
            if (!mb_ereg_search())
            {
                $response = '<div style="padding:1px;" class="ui-state-error ui-corner-all">' . $user->lang['INVALID_CHARS_USERNAME'] . '</div>';
                return_ajax($response);
            die;
            }
        }
    }    
    $sql = "SELECT " . $params . "
    FROM " . USERS_TABLE
    . " WHERE " . $params . "='" . $db->sql_escape($value) . "' ";

	$result = $db->sql_query($sql);

	$row = $db->sql_fetchrow($result);
    if($row[$params] === $value)
    {
        $response = '<div style="padding:1px;" class="ui-state-error ui-corner-all">' . $value . ' is already taken!</div>';
        return_ajax($response);
    }
die;
}
if($config['fb4phpbb_light_mod_enabled'] == 'no')
{
	trigger_error(sprintf($user->lang['FB4PHPBB_LIGHT_LOGIN_UNAVAILABLE'], $user->lang['FACEBOOK']));
}

if (!class_exists('messenger'))
{
    include($phpbb_root_path . $config['fb4phpbb_light_path'] . 'facebook_app/facebook.' . $phpEx);
}

$facebook = new Facebook(array(
	'appId'  									=> $config['fb4phpbb_light_appid'],
	'secret' 									=> $config['fb4phpbb_light_secret'],
));
$fb_user 										= $facebook->getUser();
$fb_access_token 								= $facebook->getAccessToken();
$user_profile = $facebook->api($fb4phpbb_light_uid, 'GET', array('access_token' => $fb_access_token));
$upid = $user_profile['id'];
$fb4phpbb_light_first_name								= $user_profile['first_name'];
$fb4phpbb_light_last_name								= $user_profile['last_name'];
if($check == 'yes')
{
    if(preg_match("/$upid/i", $fb4phpbb_light_uid)) 
    {

        $sql = 'SELECT user_id
        FROM ' . USERS_TABLE
        . " WHERE user_fb4phpbb_light_fb_uid='$fb4phpbb_light_uid'";

        $result 									= $db->sql_query($sql);

        $row 										= $db->sql_fetchrow($result);

        $db->sql_freeresult($result);
        
        if ($row) //user wants to login & has already connected the facebook account
        {
            if($user->check_ban($row['user_id']))
            {
                trigger_error($user->lang['BAN_TRIGGERED_BY_USER']); 
            }
                
            $result = $user->session_create($row['user_id'], 0, 1, 1);

            if(!$result)
            {
                trigger_error($user->lang['LOGIN_FAILURE']);
            }

            $redirect 								= "{$phpbb_root_path}index.$phpEx";
            $message 								= $user->lang['LOGIN_REDIRECT'];
            $l_redirect 							= $user->lang['RETURN_INDEX'];

            if (defined('IN_CHECK_BAN') && $result['user_row']['user_type'] != USER_FOUNDER)
            {
                return;
            }

            $redirect = reapply_sid($redirect);
            $response = '<div id="message" class="panel"><div class="inner"><span class="corners-top"><span></span></span>' . $message . '<br /><br />' . sprintf($l_redirect, '<a href="' . $redirect . '">', '</a><script>setTimeout(function() { window.location.href = "' . $redirect . '";}, 5000);</script>') . '<span class="corners-bottom"><span></span></span></div></div>';
            return_ajax($response);
        die;
        }
        else
        {
            die;
        }
    }
    else
    {
        $redirect 								= "{$phpbb_root_path}index.$phpEx";
        $l_redirect 							= $user->lang['RETURN_INDEX'];	
        $message = "Wrong data submitted to Facebook.";
        $response = '<div id="message" class="panel"><div class="inner"><span class="corners-top"><span></span></span>' . $message . '<br /><br />' . sprintf($l_redirect, '<a href="' . $redirect . '">', '</a><script>setTimeout(function() { window.location.href = "' . $redirect . '";}, 5000);</script>') . '<span class="corners-bottom"><span></span></span></div></div>';
        return_ajax($response);
    }
}
else
{
	if($link == 'yes' && $user->data['user_id'] == ANONYMOUS && preg_match("/$upid/i", $fb4phpbb_light_uid)) //user wants to login & has not already connected the facebook account
	{
		$emailsql 								= 'SELECT user_id
		FROM ' . USERS_TABLE
		. " WHERE user_email='$fb4phpbb_light_email'";
		
		$emailresult 							= $db->sql_query($emailsql);
		
		$emailrow 								= $db->sql_fetchrow($emailresult);
		
		$db->sql_freeresult($emailresult);
        //print_r($emailrow);
        //die;
		if($emailrow > 1)
		{      
			$insertemailsql	= 'UPDATE ' . USERS_TABLE . "
            SET user_fb4phpbb_light_fb_uid = '" . $db->sql_escape($fb4phpbb_light_uid) . "'
            WHERE user_id = '" . $db->sql_escape($emailrow['user_id']) . "'";			
			$insertemailresult	= $db->sql_query($insertemailsql);
      
			if($user->check_ban($emailrow['user_id']))
			{
				trigger_error($user->lang['BAN_TRIGGERED_BY_USER']); 
			}
      
			$sessionemailresult 				= $user->session_create($emailrow['user_id'], 0, 1, 1);
      
			if(!$sessionemailresult)
			{
				trigger_error($user->lang['LOGIN_FAILURE']);
			}
    
			$redirect 							= "{$phpbb_root_path}index.$phpEx";
			$message 							= $user->lang['LOGIN_REDIRECT'];
			$l_redirect 						= $user->lang['RETURN_INDEX'];
			$redirect 							= reapply_sid($redirect);
      
			if (defined('IN_CHECK_BAN') && $sessionemailresult['user_row']['user_type'] != USER_FOUNDER)
			{
				return;
			}
			$response = $message . '<br /><br />' . sprintf($l_redirect, '<a href="' . $redirect . '">', '</a><script>setTimeout(function() { window.location.href = "' . $redirect . '";}, 5000);</script><div id="response_regod" style="display:none;">isregod</div>');
            return_ajax($response);
		die;
		}
		else //user wants to register & connect the facebook account
		{
			$message 							= 'TERMS_OF_USE_CONTENT';
			$title 								= 'TERMS_USE';
      		
			$template->set_filenames(array(
				'body'							=> 'ucp_fblight_agreement.html'));  
      
			if ($change_lang || $user_lang != $config['default_lang'])
			{
				$use_lang 						= ($change_lang) ? basename($change_lang) : basename($user_lang);
        
				if (file_exists($user->lang_path . $use_lang . '/'))
				{
					if ($change_lang)
					{
						$submit 				= false;
						$agreed 				= (empty($_GET['change_lang'])) ? 0 : $agreed;
					}
          
					$user->lang_name 			= $user_lang = $use_lang;
					$user->lang 				= array();
					$user->data['user_lang'] 	= $user->lang_name;
					$user->add_lang(array('common', 'ucp'));
				}
				else
				{
					$change_lang				= '';
					$user_lang 					= $user->lang_name;
				}
			}
      
			$sql = 'SELECT lang_id
			FROM ' . LANG_TABLE;
			
			$result 							= $db->sql_query($sql);
			
			$lang_row 							= array();
			
			while ($row = $db->sql_fetchrow($result))
			{
				$lang_row[] 					= $row;
			}
      
			$db->sql_freeresult($result);
			
			page_header($user->lang[$title], false);    
			
			$s_hidden_fields = array(	
			'change_lang'						=> $change_lang,
			'fb4phpbb_light_email'					=> $fb4phpbb_light_email,			
			'fb4phpbb_light_username'					=> $fb4phpbb_light_first_name . $fb4phpbb_light_last_name,						
			'fb4phpbb_light_uid'						=> $fb4phpbb_light_uid);
      
			$add_lang 							= ($change_lang) ? '&amp;change_lang=' . urlencode($change_lang) : '';				
			$coppa								= (isset($_REQUEST['coppa'])) ? ((!empty($_REQUEST['coppa'])) ? 1 : 0) : false;
      
			if ($coppa === false && $config['coppa_enable'])
			{
				$now 							= getdate();
				$coppa_birthday 				= $user->format_date(mktime($now['hours'] + $user->data['user_dst'], $now['minutes'], $now['seconds'], $now['mon'], $now['mday'] - 1, $now['year'] - 13), $user->lang['DATE_FORMAT']);
			
				unset($now);
				
				$template->assign_vars(array(
					'S_LANG_OPTIONS'			=> (sizeof($lang_row) > 1) ? language_select($config['default_lang']) : '',
					'L_COPPA_NO'				=> sprintf($user->lang['UCP_COPPA_BEFORE'], $coppa_birthday),
					'L_COPPA_YES'				=> sprintf($user->lang['UCP_COPPA_ON_AFTER'], $coppa_birthday),
					'U_COPPA_NO'				=> append_sid("{$phpbb_root_path}ucp.$phpEx", 'mode=fblight_register&amp;coppa=0' . $add_lang),
					'U_COPPA_YES'				=> append_sid("{$phpbb_root_path}ucp.$phpEx", 'mode=fblight_register&amp;coppa=1' . $add_lang),    
					'S_SHOW_COPPA'				=> true,
					'S_HIDDEN_FIELDS'			=> build_hidden_fields($s_hidden_fields),
					'S_UCP_ACTION'				=> append_sid("ucp.$phpEx", 'mode=fblight_register' . $add_lang . $add_coppa)));
			}
			else
			{
				$template->assign_vars(array(
					'S_LANG_OPTIONS'			=> (sizeof($lang_row) > 1) ? language_select($config['default_lang']) : '',					
					'L_TERMS_OF_USE'			=> sprintf($user->lang['TERMS_OF_USE_CONTENT'], $config['sitename'], generate_board_url()),
					'S_SHOW_COPPA'				=> false,
					'S_REGISTRATION'			=> true,
					'S_HIDDEN_FIELDS'			=> build_hidden_fields($s_hidden_fields),
					'S_UCP_ACTION'				=> append_sid("ucp.$phpEx", 'mode=fblight_register' . $add_lang)));
			}
      
			unset($lang_row);
    
			page_footer();
		die;
		} 
	}
    else
    {
        $redirect 								= "{$phpbb_root_path}index.$phpEx";
        $l_redirect 							= $user->lang['RETURN_INDEX'];	
        $message = "Wrong data submitted to Facebook.";
        $response = '<div id="message" class="panel"><div class="inner"><span class="corners-top"><span></span></span>' . $message . '<br /><br />' . sprintf($l_redirect, '<a href="' . $redirect . '">', '</a><script>setTimeout(function() { window.location.href = "' . $redirect . '";}, 5000);</script>') . '<span class="corners-bottom"><span></span></span></div></div>';
        return_ajax($response);
    }	               
	if($link == 'yes' && $user->data['user_id'] !== ANONYMOUS && preg_match("/$upid/i", $fb4phpbb_light_uid)) //user wants to login & has not already connected the facebook account
	{
		$emailsql = 'SELECT user_id
		FROM ' . USERS_TABLE
		. " WHERE user_email='$fb4phpbb_light_email'";
		
		$emailresult = $db->sql_query($emailsql);
		
		$emailrow = $db->sql_fetchrow($emailresult);
		
		$db->sql_freeresult($emailresult);

		if($emailrow)
		{      
			$insertemailsql	= 'UPDATE ' . USERS_TABLE . "
            SET user_fb4phpbb_light_fb_uid = '" . $db->sql_escape($fb4phpbb_light_uid) . "'
            WHERE user_id = '" . $db->sql_escape($emailrow['user_id']) . "'";
			
			$insertemailresult 					= $db->sql_query($insertemailsql);
      
			$redirect 							= "{$phpbb_root_path}index.$phpEx";
			$message 							= $user->lang['FB4PHPBB_LIGHT_LINK_SUCCESS'];
			$l_redirect 						= $user->lang['RETURN_INDEX'];
			$redirect 							= reapply_sid($redirect);
      
			if (defined('IN_CHECK_BAN') && $sessionemailresult['user_row']['user_type'] != USER_FOUNDER)
			{
				return;
			}
			$response = $message . '<br /><br />' . sprintf($l_redirect, '<a href="' . $redirect . '">', '</a><script>setTimeout(function() { window.location.href = "' . $redirect . '";}, 5000);</script>');
            return_ajax($response);
		die;
		}
	}
    {
        $redirect 								= "{$phpbb_root_path}index.$phpEx";
        $l_redirect 							= $user->lang['RETURN_INDEX'];	
        $message = "Wrong data submitted to Facebook.";
        $response = '<div id="message" class="panel"><div class="inner"><span class="corners-top"><span></span></span>' . $message . '<br /><br />' . sprintf($l_redirect, '<a href="' . $redirect . '">', '</a><script>setTimeout(function() { window.location.href = "' . $redirect . '";}, 5000);</script>') . '<span class="corners-bottom"><span></span></span></div></div>';
        return_ajax($response);
    }	
	if($unlink = 'yes')
	{
		$unlink_sql 							= 'SELECT user_id
		FROM ' . USERS_TABLE
		. " WHERE user_fb4phpbb_light_fb_uid='$fb4phpbb_light_uid'";

		$unlink_result 							= $db->sql_query($unlink_sql);

		$unlink_row 							= $db->sql_fetchrow($unlink_result);

		$db->sql_freeresult($unlink_result);
		
		if($unlink_row)
		{
			$delete_sql = 'UPDATE ' . USERS_TABLE . "
            SET user_fb4phpbb_light_fb_uid = '0'
            WHERE user_id = '" . $db->sql_escape($unlink_row['user_id']) . "'";
									
			$delete_result 						= $db->sql_query($delete_sql);
							
			if(!$delete_result)
			{
				$response = $user->lang['FB4PHPBB_LIGHT_PHPBB_DB_FAILURE'];
				return_ajax($response);
			die;
			}
							
			$msg_sql = 'SELECT user_id, username, user_permissions, user_email, user_jabber, user_notify_type, user_type, user_lang, user_inactive_reason
			FROM ' . USERS_TABLE . "
			WHERE user_email = '" . $db->sql_escape($user->data['user_email']) . "'
			AND username_clean = '" . $db->sql_escape(utf8_clean_string($user->data['username'])) . "'";
					
			$msg_result 					= $db->sql_query($msg_sql);
					
			$user_row 						= $db->sql_fetchrow($msg_result);
			
			$db->sql_freeresult($msg_result);
				
			if (!$user_row)
			{
				$response = NO_EMAIL_USER;
				return_ajax($response);
			die;
			}
				
			if ($user_row['user_type'] == USER_IGNORE)
			{
				$response = NO_USER;
				return_ajax($response);
			die;
			}
				
			if ($user_row['user_type'] == USER_INACTIVE)
			{
				if ($user_row['user_inactive_reason'] == INACTIVE_MANUAL)
				{
					$response = ACCOUNT_DEACTIVATED;
					return_ajax($response);
				die;
				}
				else
				{
					$response = ACCOUNT_NOT_ACTIVATED;
					return_ajax($response);
				die;
				}
			}
					
			$auth2 							= new auth();
			$auth2->acl($user_row);
						
			if (!$auth2->acl_get('u_chgpasswd'))
			{
				$response = NO_AUTH_PASSWORD_REMINDER;
				return_ajax($response);
			die;
			}
						
			$server_url 					= generate_board_url();
				
			$key_len 						= 54 - strlen($server_url);
			$key_len 						= max(6, $key_len); // we want at least 6
			$key_len 						= ($config['max_pass_chars']) ? min($key_len, $config['max_pass_chars']) : $key_len; // we want at most $config['max_pass_chars']
			$user_actkey 					= substr(gen_rand_string(10), 0, $key_len);
			$user_password 					= gen_rand_string(8);
			
			$update_sql = 'UPDATE ' . USERS_TABLE . "
				SET user_newpasswd = '" . $db->sql_escape(phpbb_hash($user_password)) . "', user_actkey = '" . $db->sql_escape($user_actkey) . "'
				WHERE user_id = " . $user_row['user_id'];
			
			$db->sql_query($update_sql);
				
            if (!class_exists('messenger'))
            {
                include($phpbb_root_path . 'includes/functions_messenger.' . $phpEx);
			}
				
			$messenger = new messenger(false);
					
			$messenger->template('user_activate_passwd', $user_row['user_lang']);
				
			$messenger->to($user_row['user_email'], $user_row['username']);
			$messenger->im($user_row['user_jabber'], $user_row['username']);
					
			$messenger->assign_vars(array(
				'USERNAME'					=> htmlspecialchars_decode($user_row['username']),
				'PASSWORD'					=> htmlspecialchars_decode($user_password),
				'U_ACTIVATE'				=> "$server_url/ucp.$phpEx?mode=activate&u={$user_row['user_id']}&k=$user_actkey")
			);
					
			$messenger->send($user_row['user_notify_type']);
			
			$redirect 						= append_sid("{$phpbb_root_path}index.$phpEx");
			$l_redirect 					= $user->lang['RETURN_INDEX'];						
			$message 						= $user->lang['PASSWORD_UPDATED'];
								
			$response = $message . '<br /><br />' . sprintf($l_redirect, '<a href="' . $redirect . '">', '</a><script>setTimeout(function() { window.location.href = "' . $redirect . '";}, 5000);</script>');
            return_ajax($response);
		die;
		}
	}
}   
?> 
