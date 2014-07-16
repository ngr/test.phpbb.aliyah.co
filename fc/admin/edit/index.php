<?php
		#####################
		# required settings #
		#####################

require '../includes/environment.admin.php';
require_once('mte/mte.php');

$tabledit = new MySQLtabledit();

# database settings:
$tabledit->database = 'u19121_aliyah';
$tabledit->host = 'u19121.mysql.masterhost.ru';
$tabledit->user = 'u19121';
$tabledit->pass = 'pa5tollant4';

# table of the database
$tabledit->table = 'fc_words';

# the primary key of the table (must be AUTO_INCREMENT)
$tabledit->primary_key = 'id';

# the fields you want to see in "list view"
//view($fc_db_struct);
$tabledit->fields_in_list_view = array('id', 'heb', 'part_of_speech', 'r_id', 'rus', 'parent', 'lessons');



		#####################
		# optional settings #
		#####################


# language (en of nl)
$tabledit->language = 'en';

# numbers of rows/records in "list view"
$tabledit->num_rows_list_view = 30;

# required fields in edit or add record
$tabledit->fields_required = array('id');

# help text 
/* $tabledit->help_text = array(
	'employeeNumber' => "Don't edit this field",
	'active' => 'Active user, yes or no?',
	'firstName' => '',
	'lastName' => '',
	'email' => '',
	'jobTitle' => 'Please select!'
); // */

# visible name of the fields
/*
$tabledit->show_text = array(
	'employeeNumber' => 'Number',
	'active' => 'Active',
	'firstName' => 'First name',
	'lastName' => 'Last name',
	'email' => 'Email',
	'jobTitle' => 'Job'
); // */

$tabledit->width_editor = '100%';
$tabledit->width_input_fields = '500px';
$tabledit->width_text_fields = '498px';
$tabledit->height_text_fields = '200px';

# warning no .htacces ('on' or 'off')
# $tableedit->no_htaccess_warning = 'on';



		####################################
		# connect, show editor, disconnect #
		####################################


$tabledit->database_connect();

echo "<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN' 'http://www.w3.org/TR/html4/loose.dtd'>
	<html>
	<head>
		<meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\" />
		<meta http-equiv=\"imagetoolbar\" content=\"no\" />
		<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge,chrome=1\" />
		<meta name=\"viewport\" content=\"width=device-width\" />
	
		<title>MySQL table edit</title>
	</head>
	<body>
";

$tabledit->do_it();

echo "
	</body>
	</html>"
;

$tabledit->database_disconnect();
?>
