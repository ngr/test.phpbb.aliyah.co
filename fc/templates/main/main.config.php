<?php
/***************************************************************************
 *                              main.config.php
 *                            -------------------
 *   begin                : Saturday, Jun 03, 2006
 *   copyright            : (C) 2006 RENATA WEB SYSTEMS
 *   email                : alexey@renatasystems.org
 *
 *   $Id: main.config.php,v 1.0.0 2006/06/03 15:17:14 i_am_d Exp $
 *
 *
 ***************************************************************************/
/*
$GLOBALS['style'] = false;
$style = &$GLOBALS['style'];

$config = &$GLOBALS['config_fc'];

# various configuration items
$config['style'] = array(
	'css' => 'main.css',
	'img_dir' => 'images/'
);

# images array
$style['img'] = array(
	'zero' => '0.gif',
	'banner' => 'banner.gif',
	'counter' => 'counter.gif',
	'icon' => array(
		'music' => 'icon_music.gif',
		'pictures' => 'icon_pictures.gif',
		'movies' => 'icon_films.gif',
		'lyrics' => 'icon_texts.gif',
		'zip' => 'icon_zip.gif',
		'mp3' => 'icon_mp3.gif'
	),
	'yak' => 'bigcupcop.gif',
	'yak_small' => 'smallcupcop.gif',
	'girl' => 'girl.gif',
	'lj' => 'lj.gif',
	'lj_on' => 'lj_on.gif',
	'left_arrow' => 'left_arrow.gif',
	'right_arrow' => 'right_arrow.gif'
);

function a1($array) {

	global $config, $tpl_fc;

	foreach ( $array as $var => $value ) :
		if ( is_array($value) ) :
			$array[$var] = a1($value);
		else :
			$array[$var] = $tpl_fc->root . '/' . $config['style']['img_dir'] . $value;
		endif;
	endforeach;
	
	return $array;
}

$style['img'] = a1($style['img']);
*/
?>