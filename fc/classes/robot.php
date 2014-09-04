<?php

/***************************************************************************
 *                              robot.php
 *                            -------------------
 *   begin                : Friday, Aug 15, 2014
 *   copyright            : (C) 2014 Nikolay Grischenko
 *   email                : me@grischenko.ru
 *
 ***************************************************************************/

interface Robot{

	public function init();
	public function go();
	public function set_param( $key, $val );
}