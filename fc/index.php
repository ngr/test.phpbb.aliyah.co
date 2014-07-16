<?php

/***************************************************************************
 *                              index.php
 *                            -------------------
 *   begin                : Wednesday, Apr 23, 2014
 *   copyright            : (C) 2014 Nikolay Grischenko
 *   email                : me@grischenko.ru
 *
 *   $Id: index.php,v 1.0.0 2014/04/23 16:09:03 $
 *
 *
 ***************************************************************************/

require 'includes/environment.php';


// Output page
//page_header($user->lang['INDEX']);
//	$application->page_header();

	page_header();

	$application->fc_page_header();
	$application->go();
	$application->stop();

	page_footer();
	//	view($template->files);
	$application->output();

//	$application->stop();

?>