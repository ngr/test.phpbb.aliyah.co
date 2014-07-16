<?php if (!defined('IN_PHPBB')) exit; ?><!DOCTYPE html>
<html dir="<?php echo (isset($this->_rootref['S_CONTENT_DIRECTION'])) ? $this->_rootref['S_CONTENT_DIRECTION'] : ''; ?>" lang="<?php echo (isset($this->_rootref['S_USER_LANG'])) ? $this->_rootref['S_USER_LANG'] : ''; ?>">
<head>
<meta http-equiv="content-type" content="text/html; charset=<?php echo (isset($this->_rootref['S_CONTENT_ENCODING'])) ? $this->_rootref['S_CONTENT_ENCODING'] : ''; ?>" />
<meta http-equiv="imagetoolbar" content="no" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<meta name="viewport" content="width=device-width" />
<meta name='yandex-verification' content='4dc354d6c9c82735' />
<?php echo (isset($this->_rootref['META'])) ? $this->_rootref['META'] : ''; ?>

<title><?php echo (isset($this->_rootref['SITENAME'])) ? $this->_rootref['SITENAME'] : ''; ?> &bull; <?php if ($this->_rootref['S_IN_MCP']) {  echo ((isset($this->_rootref['L_MCP'])) ? $this->_rootref['L_MCP'] : ((isset($user->lang['MCP'])) ? $user->lang['MCP'] : '{ MCP }')); ?> &bull; <?php } else if ($this->_rootref['S_IN_UCP']) {  echo ((isset($this->_rootref['L_UCP'])) ? $this->_rootref['L_UCP'] : ((isset($user->lang['UCP'])) ? $user->lang['UCP'] : '{ UCP }')); ?> &bull; <?php } echo (isset($this->_rootref['PAGE_TITLE'])) ? $this->_rootref['PAGE_TITLE'] : ''; ?></title>

<?php if ($this->_rootref['S_ENABLE_FEEDS']) {  if ($this->_rootref['S_ENABLE_FEEDS_OVERALL']) {  ?><link rel="alternate" type="application/atom+xml" title="<?php echo ((isset($this->_rootref['L_FEED'])) ? $this->_rootref['L_FEED'] : ((isset($user->lang['FEED'])) ? $user->lang['FEED'] : '{ FEED }')); ?> - <?php echo (isset($this->_rootref['SITENAME'])) ? $this->_rootref['SITENAME'] : ''; ?>" href="<?php echo (isset($this->_rootref['U_FEED'])) ? $this->_rootref['U_FEED'] : ''; ?>" /><?php } if ($this->_rootref['S_ENABLE_FEEDS_NEWS']) {  ?><link rel="alternate" type="application/atom+xml" title="<?php echo ((isset($this->_rootref['L_FEED'])) ? $this->_rootref['L_FEED'] : ((isset($user->lang['FEED'])) ? $user->lang['FEED'] : '{ FEED }')); ?> - <?php echo ((isset($this->_rootref['L_FEED_NEWS'])) ? $this->_rootref['L_FEED_NEWS'] : ((isset($user->lang['FEED_NEWS'])) ? $user->lang['FEED_NEWS'] : '{ FEED_NEWS }')); ?>" href="<?php echo (isset($this->_rootref['U_FEED'])) ? $this->_rootref['U_FEED'] : ''; ?>?mode=news" /><?php } if ($this->_rootref['S_ENABLE_FEEDS_FORUMS']) {  ?><link rel="alternate" type="application/atom+xml" title="<?php echo ((isset($this->_rootref['L_FEED'])) ? $this->_rootref['L_FEED'] : ((isset($user->lang['FEED'])) ? $user->lang['FEED'] : '{ FEED }')); ?> - <?php echo ((isset($this->_rootref['L_ALL_FORUMS'])) ? $this->_rootref['L_ALL_FORUMS'] : ((isset($user->lang['ALL_FORUMS'])) ? $user->lang['ALL_FORUMS'] : '{ ALL_FORUMS }')); ?>" href="<?php echo (isset($this->_rootref['U_FEED'])) ? $this->_rootref['U_FEED'] : ''; ?>?mode=forums" /><?php } if ($this->_rootref['S_ENABLE_FEEDS_TOPICS']) {  ?><link rel="alternate" type="application/atom+xml" title="<?php echo ((isset($this->_rootref['L_FEED'])) ? $this->_rootref['L_FEED'] : ((isset($user->lang['FEED'])) ? $user->lang['FEED'] : '{ FEED }')); ?> - <?php echo ((isset($this->_rootref['L_FEED_TOPICS_NEW'])) ? $this->_rootref['L_FEED_TOPICS_NEW'] : ((isset($user->lang['FEED_TOPICS_NEW'])) ? $user->lang['FEED_TOPICS_NEW'] : '{ FEED_TOPICS_NEW }')); ?>" href="<?php echo (isset($this->_rootref['U_FEED'])) ? $this->_rootref['U_FEED'] : ''; ?>?mode=topics" /><?php } if ($this->_rootref['S_ENABLE_FEEDS_TOPICS_ACTIVE']) {  ?><link rel="alternate" type="application/atom+xml" title="<?php echo ((isset($this->_rootref['L_FEED'])) ? $this->_rootref['L_FEED'] : ((isset($user->lang['FEED'])) ? $user->lang['FEED'] : '{ FEED }')); ?> - <?php echo ((isset($this->_rootref['L_FEED_TOPICS_ACTIVE'])) ? $this->_rootref['L_FEED_TOPICS_ACTIVE'] : ((isset($user->lang['FEED_TOPICS_ACTIVE'])) ? $user->lang['FEED_TOPICS_ACTIVE'] : '{ FEED_TOPICS_ACTIVE }')); ?>" href="<?php echo (isset($this->_rootref['U_FEED'])) ? $this->_rootref['U_FEED'] : ''; ?>?mode=topics_active" /><?php } if ($this->_rootref['S_ENABLE_FEEDS_FORUM'] && $this->_rootref['S_FORUM_ID']) {  ?><link rel="alternate" type="application/atom+xml" title="<?php echo ((isset($this->_rootref['L_FEED'])) ? $this->_rootref['L_FEED'] : ((isset($user->lang['FEED'])) ? $user->lang['FEED'] : '{ FEED }')); ?> - <?php echo ((isset($this->_rootref['L_FORUM'])) ? $this->_rootref['L_FORUM'] : ((isset($user->lang['FORUM'])) ? $user->lang['FORUM'] : '{ FORUM }')); ?> - <?php echo (isset($this->_rootref['FORUM_NAME'])) ? $this->_rootref['FORUM_NAME'] : ''; ?>" href="<?php echo (isset($this->_rootref['U_FEED'])) ? $this->_rootref['U_FEED'] : ''; ?>?f=<?php echo (isset($this->_rootref['S_FORUM_ID'])) ? $this->_rootref['S_FORUM_ID'] : ''; ?>" /><?php } if ($this->_rootref['S_ENABLE_FEEDS_TOPIC'] && $this->_rootref['S_TOPIC_ID']) {  ?><link rel="alternate" type="application/atom+xml" title="<?php echo ((isset($this->_rootref['L_FEED'])) ? $this->_rootref['L_FEED'] : ((isset($user->lang['FEED'])) ? $user->lang['FEED'] : '{ FEED }')); ?> - <?php echo ((isset($this->_rootref['L_TOPIC'])) ? $this->_rootref['L_TOPIC'] : ((isset($user->lang['TOPIC'])) ? $user->lang['TOPIC'] : '{ TOPIC }')); ?> - <?php echo (isset($this->_rootref['TOPIC_TITLE'])) ? $this->_rootref['TOPIC_TITLE'] : ''; ?>" href="<?php echo (isset($this->_rootref['U_FEED'])) ? $this->_rootref['U_FEED'] : ''; ?>?f=<?php echo (isset($this->_rootref['S_FORUM_ID'])) ? $this->_rootref['S_FORUM_ID'] : ''; ?>&amp;t=<?php echo (isset($this->_rootref['S_TOPIC_ID'])) ? $this->_rootref['S_TOPIC_ID'] : ''; ?>" /><?php } } ?>


<link href="<?php echo (isset($this->_rootref['T_STYLESHEET_LINK'])) ? $this->_rootref['T_STYLESHEET_LINK'] : ''; ?>" rel="stylesheet" type="text/css" />

<!--
   phpBB style name:    Artodia Air
   Based on style:      prosilver (this is the default phpBB3 style)
   Prosilver author:    Tom Beddard ( http://www.subBlue.com/ )
   Air author:          Vjacheslav Trushkin ( http://www.artodia.com/ )
-->

<script type="text/javascript">
// <![CDATA[
	var jump_page = '<?php echo ((isset($this->_rootref['LA_JUMP_PAGE'])) ? $this->_rootref['LA_JUMP_PAGE'] : ((isset($this->_rootref['L_JUMP_PAGE'])) ? addslashes($this->_rootref['L_JUMP_PAGE']) : ((isset($user->lang['JUMP_PAGE'])) ? addslashes($user->lang['JUMP_PAGE']) : '{ JUMP_PAGE }'))); ?>:';
	var on_page = '<?php echo (isset($this->_rootref['ON_PAGE'])) ? $this->_rootref['ON_PAGE'] : ''; ?>';
	var per_page = '<?php echo (isset($this->_rootref['PER_PAGE'])) ? $this->_rootref['PER_PAGE'] : ''; ?>';
	var base_url = '<?php echo (isset($this->_rootref['A_BASE_URL'])) ? $this->_rootref['A_BASE_URL'] : ''; ?>';
	var style_cookie = 'phpBBstyle';
	var style_cookie_settings = '<?php echo (isset($this->_rootref['A_COOKIE_SETTINGS'])) ? $this->_rootref['A_COOKIE_SETTINGS'] : ''; ?>';
	var onload_functions = new Array();
	var onunload_functions = new Array();

	<?php if ($this->_rootref['S_USER_PM_POPUP'] && $this->_rootref['S_NEW_PM']) {  ?>

        var url = '<?php echo (isset($this->_rootref['UA_POPUP_PM'])) ? $this->_rootref['UA_POPUP_PM'] : ''; ?>';
        window.open(url.replace(/&amp;/g, '&'), '_phpbbprivmsg', 'height=225,resizable=yes,scrollbars=yes, width=400');
	<?php } ?>


	/**
	* Find a member
	*/
	function find_username(url)
	{
		popup(url, 760, 570, '_usersearch');
		return false;
	}

	/**
	* New function for handling multiple calls to window.onload and window.unload by pentapenguin
	*/
	window.onload = function()
	{
		for (var i = 0; i < onload_functions.length; i++)
		{
			eval(onload_functions[i]);
		}
	};

	window.onunload = function()
	{
		for (var i = 0; i < onunload_functions.length; i++)
		{
			eval(onunload_functions[i]);
		}
	};
	
	/*
	    Style specific stuff
    */
	var laSearchMini = '<?php echo ((isset($this->_rootref['LA_SEARCH_MINI'])) ? $this->_rootref['LA_SEARCH_MINI'] : ((isset($this->_rootref['L_SEARCH_MINI'])) ? addslashes($this->_rootref['L_SEARCH_MINI']) : ((isset($user->lang['SEARCH_MINI'])) ? addslashes($user->lang['SEARCH_MINI']) : '{ SEARCH_MINI }'))); ?>';

// ]]>
</script>
<!--[if lt IE 9]>
	<script type="text/javascript" src="<?php echo (isset($this->_rootref['T_TEMPLATE_PATH'])) ? $this->_rootref['T_TEMPLATE_PATH'] : ''; ?>/jquery-1.10.2.min.js"></script>
<![endif]-->
<!--[if gte IE 9]><!-->
	<script type="text/javascript" src="<?php echo (isset($this->_rootref['T_TEMPLATE_PATH'])) ? $this->_rootref['T_TEMPLATE_PATH'] : ''; ?>/jquery-2.0.3.min.js"></script>
<!--<![endif]-->
<!--[if lte IE 8]><script type="text/javascript"> var oldIE = true; </script><![endif]-->
<script type="text/javascript" src="<?php echo (isset($this->_rootref['T_TEMPLATE_PATH'])) ? $this->_rootref['T_TEMPLATE_PATH'] : ''; ?>/style.js"></script>
<script type="text/javascript" src="<?php echo (isset($this->_rootref['T_TEMPLATE_PATH'])) ? $this->_rootref['T_TEMPLATE_PATH'] : ''; ?>/forum_fn.js"></script>
<!-- Loginza widget JavaScript -->
<script src="http://s1.loginza.ru/js/widget.js" type="text/javascript"></script>


<?php if ($this->_tpldata['DEFINE']['.']['S_POSTING_JS']) {  ?>

<script type="text/javascript">
// <![CDATA[
	onload_functions.push('apply_onkeypress_event()');

	var form_name = 'postform';
	var text_name = <?php if ($this->_tpldata['DEFINE']['.']['SIG_EDIT']) {  ?>'signature'<?php } else { ?>'message'<?php } ?>;
	var load_draft = false;
	var upload = false;

	// Define the bbCode tags
	var bbcode = new Array();
	var bbtags = new Array('[b]','[/b]','[i]','[/i]','[u]','[/u]','[quote]','[/quote]','[code]','[/code]','[list]','[/list]','[list=]','[/list]','[img]','[/img]','[url]','[/url]','[flash=]', '[/flash]','[size=]','[/size]'<?php $_custom_tags_count = (isset($this->_tpldata['custom_tags'])) ? sizeof($this->_tpldata['custom_tags']) : 0;if ($_custom_tags_count) {for ($_custom_tags_i = 0; $_custom_tags_i < $_custom_tags_count; ++$_custom_tags_i){$_custom_tags_val = &$this->_tpldata['custom_tags'][$_custom_tags_i]; ?>, <?php echo $_custom_tags_val['BBCODE_NAME']; }} ?>);
	var imageTag = false;

	// Helpline messages
	var help_line = {
		b: '<?php echo ((isset($this->_rootref['LA_BBCODE_B_HELP'])) ? $this->_rootref['LA_BBCODE_B_HELP'] : ((isset($this->_rootref['L_BBCODE_B_HELP'])) ? addslashes($this->_rootref['L_BBCODE_B_HELP']) : ((isset($user->lang['BBCODE_B_HELP'])) ? addslashes($user->lang['BBCODE_B_HELP']) : '{ BBCODE_B_HELP }'))); ?>',
		i: '<?php echo ((isset($this->_rootref['LA_BBCODE_I_HELP'])) ? $this->_rootref['LA_BBCODE_I_HELP'] : ((isset($this->_rootref['L_BBCODE_I_HELP'])) ? addslashes($this->_rootref['L_BBCODE_I_HELP']) : ((isset($user->lang['BBCODE_I_HELP'])) ? addslashes($user->lang['BBCODE_I_HELP']) : '{ BBCODE_I_HELP }'))); ?>',
		u: '<?php echo ((isset($this->_rootref['LA_BBCODE_U_HELP'])) ? $this->_rootref['LA_BBCODE_U_HELP'] : ((isset($this->_rootref['L_BBCODE_U_HELP'])) ? addslashes($this->_rootref['L_BBCODE_U_HELP']) : ((isset($user->lang['BBCODE_U_HELP'])) ? addslashes($user->lang['BBCODE_U_HELP']) : '{ BBCODE_U_HELP }'))); ?>',
		q: '<?php echo ((isset($this->_rootref['LA_BBCODE_Q_HELP'])) ? $this->_rootref['LA_BBCODE_Q_HELP'] : ((isset($this->_rootref['L_BBCODE_Q_HELP'])) ? addslashes($this->_rootref['L_BBCODE_Q_HELP']) : ((isset($user->lang['BBCODE_Q_HELP'])) ? addslashes($user->lang['BBCODE_Q_HELP']) : '{ BBCODE_Q_HELP }'))); ?>',
		c: '<?php echo ((isset($this->_rootref['LA_BBCODE_C_HELP'])) ? $this->_rootref['LA_BBCODE_C_HELP'] : ((isset($this->_rootref['L_BBCODE_C_HELP'])) ? addslashes($this->_rootref['L_BBCODE_C_HELP']) : ((isset($user->lang['BBCODE_C_HELP'])) ? addslashes($user->lang['BBCODE_C_HELP']) : '{ BBCODE_C_HELP }'))); ?>',
		l: '<?php echo ((isset($this->_rootref['LA_BBCODE_L_HELP'])) ? $this->_rootref['LA_BBCODE_L_HELP'] : ((isset($this->_rootref['L_BBCODE_L_HELP'])) ? addslashes($this->_rootref['L_BBCODE_L_HELP']) : ((isset($user->lang['BBCODE_L_HELP'])) ? addslashes($user->lang['BBCODE_L_HELP']) : '{ BBCODE_L_HELP }'))); ?>',
		o: '<?php echo ((isset($this->_rootref['LA_BBCODE_O_HELP'])) ? $this->_rootref['LA_BBCODE_O_HELP'] : ((isset($this->_rootref['L_BBCODE_O_HELP'])) ? addslashes($this->_rootref['L_BBCODE_O_HELP']) : ((isset($user->lang['BBCODE_O_HELP'])) ? addslashes($user->lang['BBCODE_O_HELP']) : '{ BBCODE_O_HELP }'))); ?>',
		p: '<?php echo ((isset($this->_rootref['LA_BBCODE_P_HELP'])) ? $this->_rootref['LA_BBCODE_P_HELP'] : ((isset($this->_rootref['L_BBCODE_P_HELP'])) ? addslashes($this->_rootref['L_BBCODE_P_HELP']) : ((isset($user->lang['BBCODE_P_HELP'])) ? addslashes($user->lang['BBCODE_P_HELP']) : '{ BBCODE_P_HELP }'))); ?>',
		w: '<?php echo ((isset($this->_rootref['LA_BBCODE_W_HELP'])) ? $this->_rootref['LA_BBCODE_W_HELP'] : ((isset($this->_rootref['L_BBCODE_W_HELP'])) ? addslashes($this->_rootref['L_BBCODE_W_HELP']) : ((isset($user->lang['BBCODE_W_HELP'])) ? addslashes($user->lang['BBCODE_W_HELP']) : '{ BBCODE_W_HELP }'))); ?>',
		a: '<?php echo ((isset($this->_rootref['LA_BBCODE_A_HELP'])) ? $this->_rootref['LA_BBCODE_A_HELP'] : ((isset($this->_rootref['L_BBCODE_A_HELP'])) ? addslashes($this->_rootref['L_BBCODE_A_HELP']) : ((isset($user->lang['BBCODE_A_HELP'])) ? addslashes($user->lang['BBCODE_A_HELP']) : '{ BBCODE_A_HELP }'))); ?>',
		s: '<?php echo ((isset($this->_rootref['LA_BBCODE_S_HELP'])) ? $this->_rootref['LA_BBCODE_S_HELP'] : ((isset($this->_rootref['L_BBCODE_S_HELP'])) ? addslashes($this->_rootref['L_BBCODE_S_HELP']) : ((isset($user->lang['BBCODE_S_HELP'])) ? addslashes($user->lang['BBCODE_S_HELP']) : '{ BBCODE_S_HELP }'))); ?>',
		f: '<?php echo ((isset($this->_rootref['LA_BBCODE_F_HELP'])) ? $this->_rootref['LA_BBCODE_F_HELP'] : ((isset($this->_rootref['L_BBCODE_F_HELP'])) ? addslashes($this->_rootref['L_BBCODE_F_HELP']) : ((isset($user->lang['BBCODE_F_HELP'])) ? addslashes($user->lang['BBCODE_F_HELP']) : '{ BBCODE_F_HELP }'))); ?>',
		y: '<?php echo ((isset($this->_rootref['LA_BBCODE_Y_HELP'])) ? $this->_rootref['LA_BBCODE_Y_HELP'] : ((isset($this->_rootref['L_BBCODE_Y_HELP'])) ? addslashes($this->_rootref['L_BBCODE_Y_HELP']) : ((isset($user->lang['BBCODE_Y_HELP'])) ? addslashes($user->lang['BBCODE_Y_HELP']) : '{ BBCODE_Y_HELP }'))); ?>',
		d: '<?php echo ((isset($this->_rootref['LA_BBCODE_D_HELP'])) ? $this->_rootref['LA_BBCODE_D_HELP'] : ((isset($this->_rootref['L_BBCODE_D_HELP'])) ? addslashes($this->_rootref['L_BBCODE_D_HELP']) : ((isset($user->lang['BBCODE_D_HELP'])) ? addslashes($user->lang['BBCODE_D_HELP']) : '{ BBCODE_D_HELP }'))); ?>'
		<?php $_custom_tags_count = (isset($this->_tpldata['custom_tags'])) ? sizeof($this->_tpldata['custom_tags']) : 0;if ($_custom_tags_count) {for ($_custom_tags_i = 0; $_custom_tags_i < $_custom_tags_count; ++$_custom_tags_i){$_custom_tags_val = &$this->_tpldata['custom_tags'][$_custom_tags_i]; ?>

			,cb_<?php echo $_custom_tags_val['BBCODE_ID']; ?>: '<?php echo $_custom_tags_val['A_BBCODE_HELPLINE']; ?>'
		<?php }} ?>

	}

	var panels = new Array('options-panel', 'attach-panel', 'poll-panel');
	var show_panel = 'options-panel';

    function change_palette()
    {
        dE('colour_palette');
        e = document.getElementById('colour_palette');
        
        if (e.style.display == 'block')
        {
            document.getElementById('bbpalette').value = '<?php echo ((isset($this->_rootref['LA_FONT_COLOR_HIDE'])) ? $this->_rootref['LA_FONT_COLOR_HIDE'] : ((isset($this->_rootref['L_FONT_COLOR_HIDE'])) ? addslashes($this->_rootref['L_FONT_COLOR_HIDE']) : ((isset($user->lang['FONT_COLOR_HIDE'])) ? addslashes($user->lang['FONT_COLOR_HIDE']) : '{ FONT_COLOR_HIDE }'))); ?>';
        }
        else
        {
            document.getElementById('bbpalette').value = '<?php echo ((isset($this->_rootref['LA_FONT_COLOR'])) ? $this->_rootref['LA_FONT_COLOR'] : ((isset($this->_rootref['L_FONT_COLOR'])) ? addslashes($this->_rootref['L_FONT_COLOR']) : ((isset($user->lang['FONT_COLOR'])) ? addslashes($user->lang['FONT_COLOR']) : '{ FONT_COLOR }'))); ?>';
        }
    }
	onload_functions.push('colorPalette(\'h\', 15, 10);');

// ]]>
</script>
<script type="text/javascript" src="<?php echo (isset($this->_rootref['T_TEMPLATE_PATH'])) ? $this->_rootref['T_TEMPLATE_PATH'] : ''; ?>/editor.js"></script>
<?php } ?><!-- Google Analytics -->
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-50261724-3', 'aliyah.co');
  ga('send', 'pageview');
</script>
<!-- End of Google Analytics -->

</head>

<body id="phpbb" class="section-<?php echo (isset($this->_rootref['SCRIPT_NAME'])) ? $this->_rootref['SCRIPT_NAME'] : ''; ?> <?php echo (isset($this->_rootref['S_CONTENT_DIRECTION'])) ? $this->_rootref['S_CONTENT_DIRECTION'] : ''; ?>">

<div id="header">

    <h1><?php echo (isset($this->_rootref['SITENAME'])) ? $this->_rootref['SITENAME'] : ''; ?></h1>
    <p><?php echo (isset($this->_rootref['SITE_DESCRIPTION'])) ? $this->_rootref['SITE_DESCRIPTION'] : ''; ?></p>
</div>

<div id="page-header" class="responsive-menu-nojs">
	<div class="menu-buttons responsive-menu" style="display:none;">
		<a href="javascript:void(0);"><?php if ($this->_rootref['L_MENU']) {  ?><span><?php echo ((isset($this->_rootref['L_MENU'])) ? $this->_rootref['L_MENU'] : ((isset($user->lang['MENU'])) ? $user->lang['MENU'] : '{ MENU }')); ?></span><?php } else { ?><span class="arrow">&darr;&darr;&darr;</span><?php } ?></a>
	</div>

    <div id="nav-header" class="menu-buttons">
        <a href="http://aliyah.co/" title="Aliyah.club"><span>Aliyah.club</span></a> 
        <a href="http://phpbb.aliyah.co/fc" title="Флеш-карты"><span>Флеш-карты</span></a> 
        <?php if (! $this->_rootref['S_IS_BOT'] && $this->_rootref['S_USER_LOGGED_IN']) {  ?>

            <a href="<?php echo (isset($this->_rootref['U_PROFILE'])) ? $this->_rootref['U_PROFILE'] : ''; ?>" title="<?php echo ((isset($this->_rootref['L_PROFILE'])) ? $this->_rootref['L_PROFILE'] : ((isset($user->lang['PROFILE'])) ? $user->lang['PROFILE'] : '{ PROFILE }')); ?>" accesskey="e"><span><?php echo ((isset($this->_rootref['L_PROFILE'])) ? $this->_rootref['L_PROFILE'] : ((isset($user->lang['PROFILE'])) ? $user->lang['PROFILE'] : '{ PROFILE }')); ?></span></a> 
            <?php if ($this->_rootref['U_RESTORE_PERMISSIONS']) {  ?>

                <a href="<?php echo (isset($this->_rootref['U_RESTORE_PERMISSIONS'])) ? $this->_rootref['U_RESTORE_PERMISSIONS'] : ''; ?>"><span><?php echo ((isset($this->_rootref['L_RESTORE_PERMISSIONS'])) ? $this->_rootref['L_RESTORE_PERMISSIONS'] : ((isset($user->lang['RESTORE_PERMISSIONS'])) ? $user->lang['RESTORE_PERMISSIONS'] : '{ RESTORE_PERMISSIONS }')); ?></span></a> 
            <?php } } ?>

        <a href="<?php echo (isset($this->_rootref['U_FAQ'])) ? $this->_rootref['U_FAQ'] : ''; ?>" title="<?php echo ((isset($this->_rootref['L_FAQ_EXPLAIN'])) ? $this->_rootref['L_FAQ_EXPLAIN'] : ((isset($user->lang['FAQ_EXPLAIN'])) ? $user->lang['FAQ_EXPLAIN'] : '{ FAQ_EXPLAIN }')); ?>"><span><?php echo ((isset($this->_rootref['L_FAQ'])) ? $this->_rootref['L_FAQ'] : ((isset($user->lang['FAQ'])) ? $user->lang['FAQ'] : '{ FAQ }')); ?></span></a> 
        <?php if (! $this->_rootref['S_IS_BOT']) {  if ($this->_rootref['S_DISPLAY_MEMBERLIST']) {  ?><a href="<?php echo (isset($this->_rootref['U_MEMBERLIST'])) ? $this->_rootref['U_MEMBERLIST'] : ''; ?>" title="<?php echo ((isset($this->_rootref['L_MEMBERLIST_EXPLAIN'])) ? $this->_rootref['L_MEMBERLIST_EXPLAIN'] : ((isset($user->lang['MEMBERLIST_EXPLAIN'])) ? $user->lang['MEMBERLIST_EXPLAIN'] : '{ MEMBERLIST_EXPLAIN }')); ?>"><span><?php echo ((isset($this->_rootref['L_MEMBERLIST'])) ? $this->_rootref['L_MEMBERLIST'] : ((isset($user->lang['MEMBERLIST'])) ? $user->lang['MEMBERLIST'] : '{ MEMBERLIST }')); ?></span></a> <?php } if (! $this->_rootref['S_USER_LOGGED_IN'] && $this->_rootref['S_REGISTER_ENABLED'] && ! ( $this->_rootref['S_SHOW_COPPA'] || $this->_rootref['S_REGISTRATION'] )) {  ?><a href="<?php echo (isset($this->_rootref['U_REGISTER'])) ? $this->_rootref['U_REGISTER'] : ''; ?>"><span><?php echo ((isset($this->_rootref['L_REGISTER'])) ? $this->_rootref['L_REGISTER'] : ((isset($user->lang['REGISTER'])) ? $user->lang['REGISTER'] : '{ REGISTER }')); ?></span></a> <?php } ?>

             <a href="<?php echo (isset($this->_rootref['U_LOGIN_LOGOUT'])) ? $this->_rootref['U_LOGIN_LOGOUT'] : ''; ?>" title="<?php echo ((isset($this->_rootref['L_LOGIN_LOGOUT'])) ? $this->_rootref['L_LOGIN_LOGOUT'] : ((isset($user->lang['LOGIN_LOGOUT'])) ? $user->lang['LOGIN_LOGOUT'] : '{ LOGIN_LOGOUT }')); ?>" accesskey="x"><span><?php echo ((isset($this->_rootref['L_LOGIN_LOGOUT'])) ? $this->_rootref['L_LOGIN_LOGOUT'] : ((isset($user->lang['LOGIN_LOGOUT'])) ? $user->lang['LOGIN_LOGOUT'] : '{ LOGIN_LOGOUT }')); ?></span></a> 
        <?php } ?>

        <a href="javascript:void(0);" class="responsive-menu-hide" style="display:none;"><span>X</span></a>
    </div>

    <?php if ($this->_rootref['S_DISPLAY_SEARCH']) {  ?>

        <div id="search-adv">
            <a href="<?php echo (isset($this->_rootref['U_SEARCH'])) ? $this->_rootref['U_SEARCH'] : ''; ?>" title="<?php echo ((isset($this->_rootref['L_SEARCH_ADV_EXPLAIN'])) ? $this->_rootref['L_SEARCH_ADV_EXPLAIN'] : ((isset($user->lang['SEARCH_ADV_EXPLAIN'])) ? $user->lang['SEARCH_ADV_EXPLAIN'] : '{ SEARCH_ADV_EXPLAIN }')); ?>"><span><?php echo ((isset($this->_rootref['L_SEARCH_ADV'])) ? $this->_rootref['L_SEARCH_ADV'] : ((isset($user->lang['SEARCH_ADV'])) ? $user->lang['SEARCH_ADV'] : '{ SEARCH_ADV }')); ?></span></a>
        </div>
    <?php } if ($this->_rootref['S_DISPLAY_SEARCH'] && ! $this->_rootref['S_IN_SEARCH']) {  ?>

        <div id="search-box">
            <form action="<?php echo (isset($this->_rootref['U_SEARCH'])) ? $this->_rootref['U_SEARCH'] : ''; ?>" method="get" id="search">
                <input name="keywords" id="keywords" type="text" maxlength="128" title="<?php echo ((isset($this->_rootref['L_SEARCH_KEYWORDS'])) ? $this->_rootref['L_SEARCH_KEYWORDS'] : ((isset($user->lang['SEARCH_KEYWORDS'])) ? $user->lang['SEARCH_KEYWORDS'] : '{ SEARCH_KEYWORDS }')); ?>" class="inputbox search" value="<?php if ($this->_rootref['SEARCH_WORDS']) {  echo (isset($this->_rootref['SEARCH_WORDS'])) ? $this->_rootref['SEARCH_WORDS'] : ''; } else { echo ((isset($this->_rootref['L_SEARCH_MINI'])) ? $this->_rootref['L_SEARCH_MINI'] : ((isset($user->lang['SEARCH_MINI'])) ? $user->lang['SEARCH_MINI'] : '{ SEARCH_MINI }')); } ?>" />
                <?php echo (isset($this->_rootref['S_SEARCH_HIDDEN_FIELDS'])) ? $this->_rootref['S_SEARCH_HIDDEN_FIELDS'] : ''; ?>

            </form>
        </div>
    <?php } ?>

</div>

<div id="page-body">

    <div class="nav-extra">
        <?php if ($this->_rootref['U_EMAIL_TOPIC']) {  ?><a href="<?php echo (isset($this->_rootref['U_EMAIL_TOPIC'])) ? $this->_rootref['U_EMAIL_TOPIC'] : ''; ?>" title="<?php echo ((isset($this->_rootref['L_EMAIL_TOPIC'])) ? $this->_rootref['L_EMAIL_TOPIC'] : ((isset($user->lang['EMAIL_TOPIC'])) ? $user->lang['EMAIL_TOPIC'] : '{ EMAIL_TOPIC }')); ?>"><?php echo ((isset($this->_rootref['L_EMAIL_TOPIC'])) ? $this->_rootref['L_EMAIL_TOPIC'] : ((isset($user->lang['EMAIL_TOPIC'])) ? $user->lang['EMAIL_TOPIC'] : '{ EMAIL_TOPIC }')); ?></a><?php } if ($this->_rootref['U_EMAIL_PM']) {  ?><a href="<?php echo (isset($this->_rootref['U_EMAIL_PM'])) ? $this->_rootref['U_EMAIL_PM'] : ''; ?>" title="<?php echo ((isset($this->_rootref['L_EMAIL_PM'])) ? $this->_rootref['L_EMAIL_PM'] : ((isset($user->lang['EMAIL_PM'])) ? $user->lang['EMAIL_PM'] : '{ EMAIL_PM }')); ?>"><?php echo ((isset($this->_rootref['L_EMAIL_PM'])) ? $this->_rootref['L_EMAIL_PM'] : ((isset($user->lang['EMAIL_PM'])) ? $user->lang['EMAIL_PM'] : '{ EMAIL_PM }')); ?></a><?php } if ($this->_rootref['U_PRINT_TOPIC']) {  ?><a href="<?php echo (isset($this->_rootref['U_PRINT_TOPIC'])) ? $this->_rootref['U_PRINT_TOPIC'] : ''; ?>" title="<?php echo ((isset($this->_rootref['L_PRINT_TOPIC'])) ? $this->_rootref['L_PRINT_TOPIC'] : ((isset($user->lang['PRINT_TOPIC'])) ? $user->lang['PRINT_TOPIC'] : '{ PRINT_TOPIC }')); ?>" accesskey="p"><?php echo ((isset($this->_rootref['L_PRINT_TOPIC'])) ? $this->_rootref['L_PRINT_TOPIC'] : ((isset($user->lang['PRINT_TOPIC'])) ? $user->lang['PRINT_TOPIC'] : '{ PRINT_TOPIC }')); ?></a><?php } if ($this->_rootref['U_PRINT_PM']) {  ?><a href="<?php echo (isset($this->_rootref['U_PRINT_PM'])) ? $this->_rootref['U_PRINT_PM'] : ''; ?>" title="<?php echo ((isset($this->_rootref['L_PRINT_PM'])) ? $this->_rootref['L_PRINT_PM'] : ((isset($user->lang['PRINT_PM'])) ? $user->lang['PRINT_PM'] : '{ PRINT_PM }')); ?>" accesskey="p"><?php echo ((isset($this->_rootref['L_PRINT_PM'])) ? $this->_rootref['L_PRINT_PM'] : ((isset($user->lang['PRINT_PM'])) ? $user->lang['PRINT_PM'] : '{ PRINT_PM }')); ?></a><?php } ?>

    </div>

    <div class="nav-links">
        <a href="<?php echo (isset($this->_rootref['U_INDEX'])) ? $this->_rootref['U_INDEX'] : ''; ?>" accesskey="h"><span><?php echo ((isset($this->_rootref['L_INDEX'])) ? $this->_rootref['L_INDEX'] : ((isset($user->lang['INDEX'])) ? $user->lang['INDEX'] : '{ INDEX }')); ?></span></a> 
        <?php $_navlinks_count = (isset($this->_tpldata['navlinks'])) ? sizeof($this->_tpldata['navlinks']) : 0;if ($_navlinks_count) {for ($_navlinks_i = 0; $_navlinks_i < $_navlinks_count; ++$_navlinks_i){$_navlinks_val = &$this->_tpldata['navlinks'][$_navlinks_i]; ?><a href="<?php echo $_navlinks_val['U_VIEW_FORUM']; ?>"><span><?php echo $_navlinks_val['FORUM_NAME']; ?></span></a><?php }} ?>

    </div>
    
    <?php if ($this->_rootref['S_USER_LOGGED_IN'] && $this->_rootref['S_DISPLAY_PM'] && $this->_rootref['S_USER_NEW_PRIVMSG']) {  ?>

    <div id="newpm" class="rules">
        <div class="inner">
            <a href="<?php echo (isset($this->_rootref['U_PRIVATEMSGS'])) ? $this->_rootref['U_PRIVATEMSGS'] : ''; ?>"><?php echo (isset($this->_rootref['PRIVATE_MESSAGE_INFO'])) ? $this->_rootref['PRIVATE_MESSAGE_INFO'] : ''; ?></a>
        </div>
    </div>
    <?php } if ($this->_rootref['S_BOARD_DISABLED'] && $this->_rootref['S_USER_LOGGED_IN'] && ( $this->_rootref['U_MCP'] || $this->_rootref['U_ACP'] )) {  ?>

    <div id="information" class="rules">
        <div class="inner"><span class="corners-top"><span></span></span>
            <strong><?php echo ((isset($this->_rootref['L_INFORMATION'])) ? $this->_rootref['L_INFORMATION'] : ((isset($user->lang['INFORMATION'])) ? $user->lang['INFORMATION'] : '{ INFORMATION }')); ?>:</strong> <?php echo ((isset($this->_rootref['L_BOARD_DISABLED'])) ? $this->_rootref['L_BOARD_DISABLED'] : ((isset($user->lang['BOARD_DISABLED'])) ? $user->lang['BOARD_DISABLED'] : '{ BOARD_DISABLED }')); ?>

        <span class="corners-bottom"><span></span></span></div>
    </div>
    <?php } ?>