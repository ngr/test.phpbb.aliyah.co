<?php

/***************************************************************************
 *                              db.php
 *                            -------------------
 *   begin                : Wednesday, Apr 23, 2014
 *   copyright            : (C) 2014 Nikolay Grischenko
 *   email                : me@grischenko.ru
 *
 *   $Id: db.php,v 1.0.0 2014/04/23 16:11:03 $
 *
 *
 ***************************************************************************/

switch( $GLOBALS['config_fc']['db']['driver'] ) {
	case 'mysql':
		include('../../classes/mysql.engine' . $GLOBALS['config_fc']['var']['phpEx']);
//		echo "MySQL loading<br>";
	break;
}

?>