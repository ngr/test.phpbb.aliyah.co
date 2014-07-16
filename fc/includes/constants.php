<?php

/***************************************************************************
 *                              constants.php
 *                            -------------------
 *   begin                : Wednesday, Apr 23, 2014
 *   copyright            : (C) 2014 Nikolay Grischenko
 *   email                : me@grischenko.ru
 *
 *   $Id: constants.php,v 1.0.0 2014/04/23 17:06:03 $
 *
 *
 ***************************************************************************/

define('MODE_VAR', 'mode');

define('MODE_INDEX', 'index');
define('MODE_START', 'start'); 
define('MODE_NEXT', 'next');
define('MODE_DEBUG', 'debug');
define('MODE_RESET', 'reset');

define('VAR_PAGE_ID', 'pid');

define('RESULT_SKIPPED', 0);
define('RESULT_BAD', 1);
define('RESULT_TWICE_INACCURATE', 4);
define('RESULT_INACCURATE', 5);
define('RESULT_GOOD_SYNONYM', 13);
define('RESULT_GOOD_NOT_DEFAULT', 14);
define('RESULT_GOOD', 15);

?>