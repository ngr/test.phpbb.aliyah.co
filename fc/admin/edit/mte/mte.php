<?php

// no direct access
if(strtolower(basename($_SERVER['PHP_SELF'])) == strtolower(basename(__FILE__))) {
	die('No access...');
}

# A short remark from Nikolay Grishchenko
# This class should never be used to judge the quality of the project or author programming skills.
# It is written in a REAL HURRY on the basis of some opensource class with a lot of GREAT SHIT.
# Should be totally rewritten ASAP!


class MySQLtabledit {

   /**	
    * 
	* MySQL Edit Table
	* 
	* Copyright (c) 2010 Martin Meijer - Browserlinux.com
	* 
	* Permission is hereby granted, free of charge, to any person obtaining a copy
	* of this software and associated documentation files (the "Software"), to deal
	* in the Software without restriction, including without limitation the rights
	* to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	* copies of the Software, and to permit persons to whom the Software is
	* furnished to do so, subject to the following conditions:
	* 
	* The above copyright notice and this permission notice shall be included in
	* all copies or substantial portions of the Software.
	* 
	* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	* THE SOFTWARE.
	* 
	*/
	
	var $version = '0.3'; // 03 jan 2011

	# text 
	var $text;

	# language
	var $language = 'en';

	# database settings
	var $database;
	var $host;
	var $user;
	var $pass;

	# table of the database
	var $table;

	# the primary key of the table
	var $primary_key;
	
	# the fields you want to see in "list view"
	var $fields_in_list_view;

	# numbers of rows/records in "list view"
	var $num_rows_list_view = 15;

	# required fields in edit or add record
	var $fields_required;

	# help text 
	var $help_text;

	# visible name of the fields
	var $show_text;	
	
	var $width_editor = '100%';
	var $width_input_fields = '500px';
	var $width_text_fields = '498px';
	var $height_text_fields = '200px';

	# warning no .htacces ('on' or 'off')
	var $no_htaccess_warning = 'on';


	# Forget this - working on it...
	# needed in Joomla for images/css, example: 'http://www.website.com/administrator/components/com_componentname'
	var $url_base;
	# needed in Joomla, example: 'option=com_componentname' 
	var $query_joomla_component;



	###########################
	function database_connect() {
	###########################

		if (!mysql_connect($this->host, $this->user, $this->pass)) {
			die('Could not connect: ' . mysql_error());
		}
		mysql_select_db($this->database);
		mysql_query('set names UTF8;');


	}



	##############################
	function database_disconnect() {
	##############################
	
		mysql_close();

	}




	################
	function do_it() {
	################
		
		// Sorry: in Joomla, remove the next two lines and place the language vars instead
		require_once("./lang/en.php");
		require_once("./lang/" . $this->language . ".php");


		# No cache
		if(!headers_sent()) {
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
			header('Cache-Control: no-store, no-cache, must-revalidate');
			header('Cache-Control: post-check=0, pre-check=0', false);
			header('Pragma: no-cache');
			header("Cache-control: private");
		}
	
		if (!$this->url_base) $this->url_base = '.';

		# name of the script
		$break = explode('/', $_SERVER["SCRIPT_NAME"]);
		$this->url_script = $break[count($break) - 1];



		if ($_GET['mte_a'] == 'edit') { 
			$this->edit_rec(); 
		}
		elseif ($_GET['mte_a'] == 'r_add') {
			$this->r_add();
		}
		elseif ($_GET['mte_a'] == 'p_add') {
			$this->p_add();
		}
		elseif ($_GET['mte_a'] == 'l_edit') {
			$this->l_edit();
		}
		elseif ($_GET['mte_a'] == 'h_ul') {
			$this->h_ul();
		}
		elseif ($_GET['mte_a'] == 'new') {
			$this->edit_rec();
		}
		elseif ($_GET['mte_a'] == 'del') {
			 $this->del_rec(); 
		}
		elseif ($_POST['mte_a'] == 'save') {
			$this->save_rec();
		}
		else { 
			$this->show_list();
		}

		$this->close_and_print();

	}
	
# This function is for lesson editing
	function l_edit()
	{
# Get variables and choose action

# Draw list of the current lesson

# Draw form for editing

# Save edit result

# Create new lesson

# List available lessons

# DEBUG		view( $_POST );

		$action = ( isset( $_POST['action'] ) ) ? $_POST['action'] : 'save';
		
		if ( ! isset( $_POST['word_id'] ) )
		{
			die( "<br> Required parameter is not specified. The word_id to save the lessons for." );
		}
		else
		{
			$word_id = $_POST['word_id'];
		}

		if ( ! isset( $_POST['lessons'] ) )
		{
			$lessons = array();
		}
		else
		{
			$lessons = $_POST['lessons'];
		}


		if ( $action == 'save' )
		{

# Get the lessons currently saved			
			$sql = 'SELECT id FROM `fc_lessons` WHERE `word_id` = \'' . $word_id . '\';';
# DEBUG			echo "<br>" . $sql;
			
			$result = mysql_query( $sql );
			
			if ( mysql_num_rows( $result ) > 0 )
			{
				$current_lessons = array();
				while ( $row = mysql_fetch_array( $result ) )
				{
					$current_lessons[] = $row[0];
				}

# We chech if we need to delete some
				foreach ( $current_lessons as $val )
				{
					if ( ! in_array( $val, $lessons ) )
					{
						echo "<br> We now delete from DB lesson " . $val;
						$sql = 'DELETE FROM `fc_lessons` WHERE `id` = \'' . $val . '\' AND `word_id` = \'' . $word_id . '\';';
# DEBUG						echo "<br>" . $sql;
						
						$result = mysql_query( $sql );
					}
# Remove from the new lessons list if already in DB
					else 
					{
						$lessons = array_diff( $lessons, array( $val ) );
					}
				}
			}
# Now we add the new lessons connections
			foreach ( $lessons as $val )
			{
				echo "<br> We now add to DB lesson " . $val;
				$sql =	'INSERT INTO `fc_lessons` (`id`, `word_id` )'
					.	' VALUES ( \'' . $val . '\', \'' . $word_id . '\' );';
# DEBUG				echo "<br>" . $sql;
				$result = mysql_query( $sql );
			}
			
		}
# Make automatic return to list
		echo "<script language=javascript>window.onload = setTimeout(function() {  window.location=\"{$_SESSION['hist_page']}\"; }, 500);</script>";
	}
	
	function h_ul()
	{
//		view( $_GET );
		
		if ( isset( $_GET['cid'] ) )
		{
			$cid = $_GET['cid'];
			echo "<br>We have link index specified in $_GET so we will simply try to delete it.";
		}
		elseif ( isset( $_GET['hid'] ) && isset( $_GET['rid'] ) )
		{
			$heb_id = $_GET['hid'];
			$rus_id = $_GET['rid'];
			$sqlf = "SELECT id AS cid FROM `fc_heb_rus` WHERE heb_id = $heb_id AND rus_id = $rus_id LIMIT 1;";
			echo "<br>We have no link index specified in $_GET so we shall try to find it.";
# DEBUG			echo "<br>" . $sqlf;
			
			$result = mysql_query( $sqlf );
			if ( mysql_num_rows( $result ) > 0 )
			{
				$value = mysql_fetch_array( $result );
//				view($value);
				$cid = $value['cid'];	
			}
			else
			{
				die( "<br>Some invalid parameters specified. I can't find link with heb_id $heb_id and rus_id $rus_id!" );
			}
		}
		else 
		{
			die( "<br>Not enough parameters specified. cid or hid+rid are required." );
		}
# Now the time to delete the link
			$sql = "DELETE FROM `fc_heb_rus` WHERE id = $cid LIMIT 1";
# DEBUG			echo "<br>" . $sql;
			
			if ( ! $result = mysql_query( $sql ) )
			{
				die( "<br>Some MySQL error occured" );
			}
		echo "<br>Sucessfully unlinked word";
	}
	

	function r_add()
	
	{
#		view($_GET);
#		view($_POST);
		
		if( !isset($_POST['new_word']) )
			die("Что-то поломалось. function r_add() не получила новое слово.");
		else $new_word = $_POST['new_word'];

		if( isset($_POST['priority']) )
			$priority = $_POST['priority'];
		else $priority = '15';

		if( isset($_POST['part_of_speech']) )
			$part_of_speech = $_POST['part_of_speech'];
		else $part_of_speech = 'verb';

		if( isset($_POST['parent']) )
			$parent = $_POST['parent'];
		else $parent = '0';

		echo "<br>Looking for $new_word";
		$sql = "SELECT * FROM `fc_words_rus` WHERE `rus` LIKE \"$new_word\" LIMIT 1;";
# DEBUG		echo "<br>" . $sql;
		$result = mysql_query($sql);
		
# If the word already in dictionary, simply create link
		if ( mysql_num_rows($result) > 0 )
		{
			$row = mysql_fetch_array($result);
			$rus_id = $row['id'];
		}
# Otherwise add the new word
		else 
		{
			echo "<br>Word $new_word not found in dictionary. We now try to add it to dictionary:";		
			$sql = "INSERT INTO `fc_words_rus` ( rus, part_of_speech, parent ) VALUES ( \"" . $new_word . "\", \"" . $part_of_speech . "\", \"" . $parent . "\" ); ";
# DEBUG			echo "<br>" . $sql;

			$result = mysql_query($sql);
			$rus_id = mysql_insert_id();
			if ( !$rus_id )
				die ("Some error occured while adding the word to DB" . mysql_error());
			echo "<br><div style=\"color: green;\">New russian word $new_word is added to dictionary (fc_words_rus). The new index is $rus_id. </div>";
		}
		
		if ( isset( $_POST['heb_id'] ) && $_POST['heb_id'] != '' )
		{
			$heb_id = $_POST['heb_id'];
			$sql = "INSERT INTO `fc_heb_rus` ( heb_id, rus_id, priority ) VALUES ( \"" . $heb_id . "\", \"" . $rus_id. "\", \"" . $priority . "\" ); ";
# DEBUG			echo "<br>" . $sql;

			$result = mysql_query($sql);
			$link_id = mysql_insert_id();
			if ( !$link_id )
				die ("Some error occured while creating a link in DB" . mysql_error());

			echo "<br>There is a connection created for $new_word. The word has index $rus_id and is linked with a hebrew word $heb_id. The new link id is $link_id";
		}
		
# Add some navigation
		echo "<button onclick='window.location=\"{$_SESSION['hist_page']}\";' style='margin: 20px 15px 25px 15px; border: 1px solid #000;'>Назад</button>";
		echo "<script language=javascript>window.onload = setTimeout(function() {  window.location=\"index.php?start={$_GET['start']}&mte_a=edit&id={$_POST['heb_id']}\"; }, 300);</script>";
	}


######################
	function p_add()
######################	
	
	{
#		view($_GET);
#		view($_POST);
		
		if( !isset($_POST['param_name']) )
			die("Что-то поломалось. function p_add() не получила параметр.");
		else $param_name = $_POST['param_name'];
		
		if( !isset($_POST['heb_id']) )
			die("Что-то поломалось. function p_add() не получила heb_id.");
		else $heb_id = $_POST['heb_id'];

		if ( !isset($_POST['param_value']) || strlen($_POST['param_value']) == 0 )
		{
			$sql = 'DELETE FROM `fc_words_params` WHERE `heb_id` = \'' . $heb_id . '\' AND `param` = \'' . $param_name . '\' LIMIT 1;';
			echo "<br>" . $sql;
			$result = mysql_query($sql);
		}
		else 
		{
			$param_value = $_POST['param_value'];
			echo "<br>Adding new param for word";		
				$sql =	'INSERT INTO `fc_words_params` ( heb_id, param, value ) VALUES ( \'' . $heb_id . '\', \'' . $param_name . '\', \'' . $param_value . '\' )'
					.	' ON DUPLICATE KEY UPDATE value=VALUES(value)'
					.	';';
			echo "<br>" . $sql;
			$result = mysql_query($sql);
	
			echo "<br><div style=\"color: green;\">Parameter is updated. </div>";
		}
		
		
# Add some navigation
		echo "<button onclick='window.location=\"{$_SESSION['hist_page']}\";' style='margin: 20px 15px 25px 15px; border: 1px solid #000;'>Назад</button>";
		echo "<script language=javascript>window.onload = setTimeout(function() {  window.location=\"index.php?start={$_GET['start']}&mte_a=edit&id={$_POST['heb_id']}\"; }, 300);</script>";
	}



	####################
	function show_list() {
	####################
		
		# message after add or edit
		$this->content_saved = $_SESSION['content_saved']; 
		$_SESSION['content_saved'] = '';
				
		# default sort (a = ascending)
		$ad = 'a';

		if ($_GET['sort'] && in_array($_GET['sort'],$this->fields_in_list_view) ) {
			if ($_GET['ad'] == 'a') $asc_des = 'ASC';
			if ($_GET['ad'] == 'd') $asc_des = 'DESC';
			$order_by = "ORDER by " . $_GET['sort'] . ' ' . $asc_des ;	
		}
		else {
			$order_by = "ORDER by $this->primary_key DESC";	
		}


		# navigation 1/3
		$start = $_GET["start"];
		if (!$start) {$start = 0;} else {$start *=1;}

		
		// build query_string
		// query_joomla_component (joomla) 
		if ($this->query_joomla_component) $query_string = '&option=' . $this->query_joomla_component ;
		// navigation
		$query_string .= '&start=' . $start;
		// sorting
		$query_string .= '&ad=' . $_GET['ad']  . '&sort=' . $_GET['sort'] ;
		// searching
		$query_string .= '&s=' . $_GET['s']  . '&f=' . $_GET['f'] ;
		
		
		# search
		if ($_GET['s'] && $_GET['f']) {

			$in_search = addslashes(stripslashes($_GET['s']));
			$in_search_field = $_GET['f'];

			if ($in_search_field == $this->primary_key) {
				$where_search = "WHERE $in_search_field = '$in_search' ";
			}
			else {
				$where_search = "WHERE $in_search_field LIKE '%$in_search%' ";
			}
		}
		
		# select
//		$sql = "SELECT * FROM `$this->table` $where_search $order_by";
		$sql = "SELECT w.id AS id, w.heb AS heb, w.part_of_speech AS part_of_speech, w.parent AS parent,"
			.	" GROUP_CONCAT( DISTINCT r.id ORDER BY conn.priority DESC SEPARATOR ',') AS r_id,"
			.	" GROUP_CONCAT( DISTINCT conn.priority ORDER BY conn.priority DESC SEPARATOR ',') AS priority,"
			.	" GROUP_CONCAT( DISTINCT r.rus ORDER BY conn.priority DESC SEPARATOR ',') AS rus,"
			.	" GROUP_CONCAT( DISTINCT ln.rus_name ORDER BY ln.order DESC SEPARATOR ',') AS lessons"
			.	" FROM `"
			. 	$this->table . "` AS w"
			.	" LEFT JOIN `fc_heb_rus` AS conn ON conn.heb_id = w.id"
			.	" LEFT JOIN `fc_words_rus` AS r ON conn.rus_id = r.id"
			.	" LEFT JOIN `fc_lessons` AS l ON l.word_id = w.id"
			.	" LEFT JOIN `fc_lessons_names` AS ln ON ln.id = l.id"
			.	" $where_search GROUP BY w.id ORDER BY w.id DESC";
			
#		echo $sql;
		$result = mysql_query($sql);

		# navigation 2/3
		$hits_total = mysql_num_rows($result); 

		$sql .= " LIMIT $start, $this->num_rows_list_view";
#		echo $sql;
		$result = mysql_query($sql);


		if (mysql_num_rows($result)>0) {
			$count = 0;
			while ($rij = mysql_fetch_assoc($result)) {
				$count++;
				$this_row = '';
				
				if ($background == '#eee') {$background='#fff';} 
					else {$background='#eee';}
							
//				if ($count == 1) view($rij);
				foreach ($rij AS $key => $value) {
					
					$sort_image = '';
					if (in_array($key, $this->fields_in_list_view)) {
						if ($count == 1) {
							
							// show nice text of a value 
							if ($this->show_text[$key]) {$show_key = $this->show_text[$key];}
								else {$show_key = $key;}

							// sorting
							if ($_GET['sort'] == $key && $_GET['ad'] == 'a') {
								$sort_image = "<IMG SRC='$this->url_base/images/sort_a.png' WIDTH=9 HEIGHT=8 BORDER=0 ALT=''>";
								$ad = 'd';
							}
							if ($_GET['sort'] == $key && $_GET['ad'] == 'd') {
								$sort_image = "<IMG SRC='$this->url_base/images/sort_d.png' WIDTH=9 HEIGHT=8 BORDER=0 ALT=''>";
								$ad = 'a';
							}

							// remove sort  and ad and add new ones
							$query_sort = preg_replace('/&(sort|ad)=[^&]*/','', $query_string) . "&sort=$key&ad=$ad";	

							$head .= "<td NOWRAP>$show_key</td>";
						}
						if ($key == $this->primary_key) {
							$current_id = $value;
							$buttons = "<td NOWRAP><a href='javascript:void(0)' onclick='del_confirm($value)' title='Delete {$this->show_text[$key]} $value'><IMG SRC='$this->url_base/images/del.png' WIDTH=16 HEIGHT=16 BORDER=0 ALT=''></a>&nbsp;<a href='?$query_string&mte_a=edit&id=$value' title='Edit {$this->show_text[$key]} $value'><IMG SRC='$this->url_base/images/edit.png' WIDTH=16 HEIGHT=16 BORDER=0 ALT=''></a></td>";
							$this_row .= "<td>$value</td>";
						}
						elseif ($key == 'r_id')
						{
							$rus_ids = explode (',', $value);
//							$buttons_r = "<td NOWRAP><a href='?$query_string&mte_a=r_del&id=$value' title='Delete {$this->show_text[$key]} $value'><IMG SRC='$this->url_base/images/del.png' WIDTH=16 HEIGHT=16 BORDER=0 ALT=''></a>&nbsp;<a href='?$query_string&mte_a=r_edit&id=$value' title='Edit {$this->show_text[$key]} $value'><IMG SRC='$this->url_base/images/edit.png' WIDTH=16 HEIGHT=16 BORDER=0 ALT=''></a></td>";
							$this_row .= '<td>' . substr(strip_tags($value), 0, 300) . '</td>';
 						}
 						elseif ($key == 'rus')
 						{
							$rus_words = false;
 							if (! empty( $value ) )
 							{
	 							$rus_words = explode (',', $value);
 							}
// 							view($rus_words);
 							$this_row .= '<td>';
 							
 							$i = 0;
 							if ( $rus_words )
 							{
	 							foreach ($rus_words as $key => $val)
	 							{
	 								$this_row .= "$val <a href=\"?$query_string&mte_a=h_ul&hid=$current_id&rid=" . $rus_ids[$key] . "\"><IMG SRC='$this->url_base/images/del.png' WIDTH=16 HEIGHT=16 BORDER=0 ALT='Удалить'></a><br>";
	 							}
							}
 							$this_row .= "<a href=\"?$query_string&mte_a=edit&id=$current_id\"><IMG SRC='$this->url_base/images/list_add.png' WIDTH=16 HEIGHT=16 BORDER=0 ALT='Добавить'></a><br>";
 							$this_row .= '</td>';
 						}

 						elseif ($key == 'lessons')
 						{
							$lessons = false;
 							if (! empty( $value ) )
 							{
	 							$lessons = explode (',', $value);
 							}
// 							view($lessons );
 							$this_row .= '<td>';
 							
 							$i = 0;
 							if ( $lessons )
 							{
	 							foreach ($lessons as $key => $val)
	 							{
	 								$this_row .= "$val <a href=\"?$query_string&mte_a=l_edit&action=drop&word_id=$current_id&l_index=" . $key . "\"><IMG SRC='$this->url_base/images/del.png' WIDTH=16 HEIGHT=16 BORDER=0 ALT='Удалить'></a><br>";
	 							}
							}
 							$this_row .= "<a href=\"?$query_string&mte_a=edit&id=$current_id\"><IMG SRC='$this->url_base/images/list_add.png' WIDTH=16 HEIGHT=16 BORDER=0 ALT='Добавить'></a><br>";
 							$this_row .= '</td>';
 						}
						else {
							
							$this_row .= '<td>' . substr(strip_tags($value), 0, 300) . '</td>';
						}
					}
				}
				
				$rows .= "<tr style='background:$background'>$buttons $this_row $buttons_r</tr>";
				
			}
		}
		else {
			$head = "<td style='padding:50px'>{$this->text['Nothing_found']}...</td>";
		}


		# navigation 3/3

		# remove start= from url
		$query_nav = preg_replace('/&(start|mte_a|id)=[^&]*/','', $query_string );	


		# this page
		$this_page = ($this->num_rows_list_view + $start)/$this->num_rows_list_view;


		# last page
		$last_page = ceil($hits_total/$this->num_rows_list_view);


		# navigatie numbers
		if ($this_page>10) {
			$vanaf = $this_page - 10;
		}
		else {$vanaf = 1;}
		if ($last_page>$this_page + 10) {
			$tot = $this_page + 10;
		}
		else {$tot = $last_page; }


		for ($f=$vanaf;$f<=$tot;$f++) {

			$nav_toon = $this->num_rows_list_view * ($f-1);

			if ($f == $this_page) {
				$navigation .= "<td class='mte_nav' style='color:#fff;background: #808080;font-weight: bold'>$f</td> "; 
			}
			else {
				$navigation .= "<td class='mte_nav' style='background: #fff'><A HREF='$this->url_script?$query_nav&start=$nav_toon'>$f</A></td>"; 
			}
		}
		if ($hits_total<$this->num_rows_list_view) { $navigation = '';}




		# Previous if
		if ($this_page > 1) {
			$last =  (($this_page - 1) * $this->num_rows_list_view ) - $this->num_rows_list_view;
			$last_page_html = "<A HREF='$this->url_script?$query_nav&start=$last' class='mte_nav_prev_next'>{$this->text['Previous']}</A>";
		}

		# Next if: 
		if ($this_page != $last_page && $hits_total>1) {
			$next =  $start + $this->num_rows_list_view;
			$next_page_html =  "<A HREF='$this->url_script?$query_nav&start=$next' class='mte_nav_prev_next'>{$this->text['Next']}</A>";
		}


		if ($navigation) {
			$nav_table = "
				<table cellspacing=5 cellpadding=0 style='border: 0px solid white'>
					<tr>
						<td style='padding-right:6px;vertical-align: middle'>$last_page_html</td>
						$navigation
						<td style='padding-left:6px;vertical-align: middle'>$next_page_html</td>
					</tr>
				</table>	
			";

			$this->nav_top = "

				<div style='margin: -10px 0 20px 0;width: $this->width_editor'>
				<center>
					$nav_table
				</center>
				</div>	
			";

			$this->nav_bottom = "
				<div style='margin: 20px 0 0 0;width: $this->width_editor'>
				<center>
					$nav_table
				</center>
				</div>
			";
		}
		
		
		
		
		# Search form + Add Record button
		foreach ($this->fields_in_list_view AS $option) {
			
			if ($this->show_text[$option]) {$show_option = $this->show_text[$option];}
			else {$show_option = $option;}

			if ($option == $in_search_field) {
					$options .= "<option selected value='$option'>$show_option</option>";
				}
				else {
					$options .= "<option value='$option'>$show_option</option>";
				}
			}
		$in_search_value = htmlentities(trim(stripslashes($_GET['s'])), ENT_QUOTES);



		$seach_form = "
			<table cellspacing=0 cellpadding=0 border=0>
			<tr>
				<td nowrap>
					<form method=get action='$this->url_script' style='padding: 15px'>
						<select name='f'>$options</select> 
						<input type='text' name='s' value='$in_search_value' style='width:200px'>
						<input type='submit' value='Искать' style='width:80px; border: 1px solid #000'>
			"; 	
		if ($this->query_joomla_component) $seach_form .= "<input type='hidden' value='$this->query_joomla_component' name='option'>";
		$seach_form .= "</form>";
		
		if ($_GET['s'] && $_GET['f']) {		
			if ($this->query_joomla_component) $add_joomla = '?option=' . $this->query_joomla_component;
			$seach_form .= "<button onclick='window.location=\"$this->url_script$add_joomla\"' style='margin: 0 0 15px 15px; border: 1px solid #000;'>Искать</button>";
		}
		
		$seach_form .= "
				</td>

				<td style='padding: 15px; text-align: right; width: $this->width_editor'>
					<button id='add_new_word' onclick='window.location=\"$this->url_script?$query_string&mte_a=new\"' style='margin: 0 0 15px 15px; border: 1px solid #000;'>Добавить новое слово</button>
				</td>
			
			</tr>
			</table>
		";
		$seach_form .= "<script language=javascript>window.onload = function() { document.getElementById(\"add_new_word\").focus(); }; </script>";

		$this->javascript = "
			function del_confirm(id) {
				if (confirm('{$this->text['Delete']} record {$this->show_text[$this->primary_key]} ' + id + '...?')) {
					window.location='$this->url_script?$query_string&mte_a=del&id=' + id				
				}
			}
		";
		

# NGR this is extra cell for RUS actions buttons		
		$head .= "<td></td>";

		# page content
		$this->content = "
			<div style='width: $this->width_editor;background:#454545; margin: 0'>$seach_form</div>
			<table cellspacing=0 cellpadding=10 style='margin: 0; width: $this->width_editor;'>
				<tr style='background:#626262; color: #fff'><td></td>$head</tr>
				$rows
			</table>
			
			$this->nav_bottom
		";
		
		
	}




	##################
	function del_rec() {
	##################

		$in_id = $_GET['id'];

		if (mysql_query("DELETE FROM $this->table WHERE `$this->primary_key` = '$in_id'")) {
			$this->content_deleted = "
				<div style='width: $this->width_editor'>
					<div style='padding: 10px; color:#fff; background: #FF8000; font-weight: bold'>Record {$this->show_text[$this->primary_key]} $in_id {$this->text['deleted']}</div>
				</div>
			";
			$this->show_list();
		}
		else {
			$this->content = "
			</div>
				<div style='padding:2px 20px 20px 20px;margin: 0 0 20px 0; background: #DF0000; color: #fff;'><h3>Error</h3>" .
				mysql_error(). 
				"</div><a href='$this->url_script'>List records...</a>
			</div>";
		}

	}




	###################
	function edit_rec() {
	###################

		$in_id = $_GET['id'];

		# edit or new?
		if ($_GET['mte_a'] == 'edit') $edit=1;
		
		$count_required = 0;
		$focus = 'heb';


		$result = mysql_query("SHOW COLUMNS FROM `$this->table`");
		
		# get field types
		while ($rij = mysql_fetch_array($result)) {
			extract($rij);
			$field_type[$Field] = $Type;
		} 

		if (!$edit) {
			$rij = $field_type;
		}
		else {
			if ($edit) $where_edit = "WHERE `$this->primary_key` = $in_id";
			$result = mysql_query("SELECT * FROM `$this->table` $where_edit LIMIT 1 ;");
			$rij = mysql_fetch_assoc($result);
			
# This is used later to define where to focus cursor on page load
			if ( mysql_num_rows( $result ) == 0)
			{
				$focus = 'heb';
			}
			else 
			{
				$focus = 'new_word';
			}
		}
		
		
		foreach ($rij AS $key => $value) {
			if (!$edit) $value = '';
			$field = '';
			$options = '';
			$style = '';
			$field_id = '';
			$readonly = '';
			$value_htmlentities = '';
			
			if (in_array($key, $this->fields_required)) {
				$count_required++;
				$style = "class='mte_req'";
				$field_id = "id='id_" . $count_required . "'";
			}
			
			if ($key == 'part_of_speech')
			{
				$part_of_speech = $value;
			}

			$field_kind = $field_type[$key];

			# different fields
			# textarea
			if (preg_match("/text/", $field_kind)) {
				$field = "<textarea name='$key' $style $field_id>$value</textarea>";
			}
			# select/options
			elseif (preg_match("/enum\((.*)\)/", $field_kind, $matches)) {
				$all_options = substr($matches[1],1,-1);
				$options_array = explode("','",$all_options);
				foreach ($options_array AS $option) {
					if ($option == $value) {
						$options .= "<option selected>$option</option>";
					}
					else {
						$options .= "<option>$option</option>";
					}
				} 
				$field = "<select name='$key' $style $field_id>$options</select>";
			}
			# input
			elseif (!preg_match("/blob/", $field_kind)) {
				if (preg_match("/\(*(.*)\)*/", $field_kind, $matches)) {
					if ($key == $this->primary_key) {
						$style = "style='background:#ccc'";
						$readonly = 'readonly';
					}
					$value_htmlentities = $value;
					if (!$edit && $key == $this->primary_key) {
						$field = "<input type='hidden' name='$key' value=''>[auto increment]";
					} 
					else {
						$field = "<input type='text' name='$key' id='$key' value='$value_htmlentities' maxlength='{$matches[1]}' $style $readonly $field_id>";
					}
				}
			}
			# blob: don't show
			elseif (preg_match("/blob/", $field_kind)) {
				$field = '[<i>binary</i>]';
			}
			 
			# make table row
			if ($background == '#eee') {$background='#fff';} 
				else {$background='#eee';}
			if ($this->show_text[$key]) {$show_key = $this->show_text[$key];}
				else {$show_key = $key;}
			$rows .= "\n\n<tr style='background:$background'>\n<td><b>$show_key</b></td>\n<td>$field</td>\n<td style='width:50%'>{$this->help_text[$key]}</td>\n</tr>";
		}
		
		$this->javascript = "
			function submitform() {
				var ok = 0;
				for (f=1;f<=$count_required;f++) {
					
					var elem = document.getElementById('id_' + f);
					
					if(elem.options) {
						if (elem.options[elem.selectedIndex].text!=null && elem.options[elem.selectedIndex].text!='') {
							ok++;
						}
					}
					else {
						if (elem.value!=null && elem.value!='') {
							ok++;
						}
					}
				}
//	alert($count_required + ' ' + ok);

				if (ok == $count_required) {
					return true;
				}
				else {
					alert('{$this->text['Check_the_required_fields']}...')
					return false;
				}	
			}
		";


# Show linked lessons (public)
/*
		$sql =	'SELECT ln.id AS ln_id, ln.rus_name AS l_name'
			.	' FROM `fc_lessons` AS l'
			.	' LEFT JOIN `fc_lessons_names` AS ln'
			.	' ON l.id = ln.id'
			.	' WHERE l.word_id = ' . $in_id
			.	';';
		echo "<br>" . $sql;
/*		$result = mysql_query($sql);
		$lessons = "<tr><th>LessonID</th><th>Lesson name</th><th>&nbsp;</th></tr>";

		if (mysql_num_rows($result) > 0 )
		{
			while ( $less = mysql_fetch_assoc($result) )
			{
//				view($r_words);
				
//				$less_actions = "<td><a href=\"index.php?mte_a=h_ul&cid=" . $r_words['cid'] . "\">Отсоединить</a></td.";
				
				$lessons .= "<tr>";
				foreach ( $less as $key => $value )
				{
					$lessons .= "<td>$value</td>";
				}
				$lessons .= $less_actions;
				$lessons .= "</tr>";
			}
		} // */

		
# The NEW way to show lessons with checkboxes

# Lessons_connected
		if ($edit == 1)
		{
			$sql_lc =	'SELECT id FROM `fc_lessons`'
					.	' WHERE word_id = ' . $in_id . ';';
#			echo "<br>" . $sql_lc;
			$result_lc = mysql_query($sql_lc);
	
			$lessons_connected = array();
			if (mysql_num_rows($result_lc) > 0 )
			{
				while ( $lc = mysql_fetch_array($result_lc) )
				{
					$lessons_connected[] = $lc[0];
				}
			} // */
		}
		else
		{
			$lessons_connected = array();
		}

# Total list of public lessons
		$sql =	'SELECT ln.id AS lesson_id, ln.rus_name AS lesson_name'
			.	' FROM `fc_lessons_names` AS ln'
			.	' WHERE ln.access_limit IS NULL'
			.	' ORDER BY ln.order DESC;';
#		echo "<br>" . $sql;

		$result = mysql_query($sql);
		$lessons = "<form method=post action=\"index.php?mte_a=l_edit\">";
		$lessons .= "<tr><td width=\"5%\" style=\"\"><b>Add</b></td><td style=\"text-align:left;\"><b>Lesson name</b></td></tr>";

		$i = 0;
		if (mysql_num_rows($result) > 0 )
		{
			while ( $less = mysql_fetch_array($result) )
			{
				$i++;
				if ( $i % 2 == 0 )
					$cur_bg = '#fff';
				else
					$cur_bg = '#eee';
					
				$lessons .= "<tr>";
				$lessons .= "<td style=\"text-align:left; background-color:". $cur_bg . ";\"><input type=checkbox name=\"lessons[]\" value=\"" . $less[0] . "\"";
				if ( in_array( $less[0], $lessons_connected ) )
				{
					$lessons .= ' checked';
				}
				$lessons .= "></td>";
				$lessons .= "<td style=\"text-align:left; background-color:". $cur_bg . ";\">" . $less[1] . "</td>";

				$lessons .= "</tr>";
			}
		} // */
		
		$lessons .= "<tr><td colspan=2 align=center>";
		$lessons .= "<input type=submit value=\"Сохранить\">";
		$lessons .= "<input type=hidden name=word_id value=\"" .$in_id . "\">";
		$lessons .= "</form>";


		/*
# Form to link new lessons
		$rows_rus .= "<tr><th align=\"left\" colspan=5>";
		$rows_rus .= "<form method=post action=\"index.php?mte_a=l_edit\">";
		$rows_rus .= "<input id=\"new_le\" name=\"new_word\" type=text size=20 style=\"width:200px;\"> &nbsp; &nbsp;";
		$rows_rus .= "<select name=\"priority\" style=\"width:50px;\">";
		for ( $i = 1; $i <= 15; $i++ )
		{
			$selected = '';
			if ( $i == 15 )
			{
				$selected = ' selected';
			}
			$rows_rus .= "<option value=\"$i\"" . $selected . ">" . $i . "</option>";
		}
		$rows_rus .= "</select> &nbsp; &nbsp;";
		$rows_rus .= "<input name=\"mte_a\" type=hidden value=r_add>";
		$rows_rus .= "<input name=\"heb_id\" type=hidden value=\"" . $in_id . "\">";
		$rows_rus .= "<input name=\"part_of_speech\" type=hidden value=\"" . $part_of_speech . "\">";
		$rows_rus .= "<input type=submit value='Добавить' size=20 style=\"width:100px;\">";
		$rows_rus .= "</form>";
		$rows_rus .= "</th></tr>";
		
// */


# Show params that are set

		if ($edit == 1)
		{
			$sql = 'SELECT p.param AS param, p.value AS value'
				.	' FROM `fc_words_params` AS p'
				.	' WHERE p.heb_id = '
				.	$in_id;
	# DEBUG		echo $sql;
			$result = mysql_query($sql);
	
			$rows_params = "<tr><th>Property</th><th>Value</th><th>&nbsp;</th></tr>";
			if (mysql_num_rows($result) > 0 )
			{
				while ( $params = mysql_fetch_assoc($result) )
				{
					$param_actions = "<td><a href=\"index.php?mte_a=p_ul&cid=" . $r_words['cid'] . "\">Отсоединить</a></td.";
					$rows_params .= "<tr>";
					foreach ( $params as $key => $value )
					{
						$rows_params .= "<td>$value</td>";
					}
					$rows_params .= $param_actions;
					$rows_params .= "</tr>";
				}
			}
		}
		
# Form to set new params
		$rows_params .= "<tr><th align=\"left\" colspan=5>";
		$rows_params .= "<form method=post action=\"index.php?mte_a=p_add\">";
		$rows_params .= "<input id=\"param_name\" name=\"param_name\" type=text size=20 style=\"width:200px;\"> &nbsp; &nbsp;";
		$rows_params .= "<input id=\"param_value\" name=\"param_value\" type=text size=20 style=\"width:200px;\"> &nbsp; &nbsp;";

		$rows_params .= "<input name=\"mte_a\" type=hidden value=p_add>";
		$rows_params .= "<input name=\"heb_id\" type=hidden value=\"" . $in_id . "\">";
		$rows_params .= "<input type=submit value='Добавить' size=20 style=\"width:100px;\">";
		$rows_params .= "</form>";
		$rows_params .= "</th></tr>";
		

# Show linked russian translations
		if ($edit == 1)
		{
			$sql = 'SELECT c.id AS cid, r.id, r.rus, c.priority'
				.	' FROM `fc_words_rus` AS r LEFT JOIN `fc_heb_rus` AS c ON r.id = c.rus_id'
				.	' WHERE c.heb_id = '
				.	$in_id;
	# DEBUG		echo $sql;
			$result = mysql_query($sql);
	
			$rows_rus = "<tr><th>Russian</th><th>Priority</th><th>&nbsp;</th></tr>";
	
			if (mysql_num_rows($result) > 0 )
			{
				while ( $r_words = mysql_fetch_assoc($result) )
				{
	//				view($r_words);
					
					$rus_actions = "<td><a href=\"index.php?mte_a=h_ul&cid=" . $r_words['cid'] . "\">Отсоединить</a></td.";
					
					$rows_rus .= "<tr>";
					foreach ( $r_words as $key => $value )
					{
						$rows_rus .= "<td>$value</td>";
					}
					$rows_rus .= $rus_actions;
					$rows_rus .= "</tr>";
				}
			}
		}
		
# Form to link new translations
		$rows_rus .= "<tr><th align=\"left\" colspan=5>";
		$rows_rus .= "<form method=post action=\"index.php?mte_a=r_add\">";
		$rows_rus .= "<input id=\"new_word\" name=\"new_word\" type=text size=20 style=\"width:200px;\"> &nbsp; &nbsp;";
		$rows_rus .= "<select name=\"priority\" style=\"width:50px;\">";
		for ( $i = 1; $i <= 15; $i++ )
		{
			$selected = '';
			if ( $i == 15 )
			{
				$selected = ' selected';
			}
			$rows_rus .= "<option value=\"$i\"" . $selected . ">" . $i . "</option>";
		}
		$rows_rus .= "</select> &nbsp; &nbsp;";
		$rows_rus .= "<input name=\"mte_a\" type=hidden value=r_add>";
		$rows_rus .= "<input name=\"heb_id\" type=hidden value=\"" . $in_id . "\">";
		$rows_rus .= "<input name=\"part_of_speech\" type=hidden value=\"" . $part_of_speech . "\">";
		$rows_rus .= "<input type=submit value='Добавить' size=20 style=\"width:100px;\">";
		$rows_rus .= "</form>";
		$rows_rus .= "</th></tr>";
		
		$rows_rus .= "<script language=javascript>window.onload = function() { document.getElementById(\"$focus\").focus(); }; </script>";

		$this->content = "
			

				<div style='width: $this->width_editor;background:#454545'>
				
					<table cellspacing=0 cellpadding=0 style='border: 0px solid white'>
						<tr>
						<td>
							<button onclick='window.location=\"{$_SESSION['hist_page']}\";' style='margin: 20px 15px 25px 15px; border: 1px solid #000;'>{$this->text['Go_back']}</button></td>
						<td>
							<form method=post action='$this->url_script' onsubmit='return submitform()'>
							<input type='submit' value='Сохранить' style='width: 80px;border: 1px solid #000; margin: 20px 0 25px 0'></td>
						</tr>
					</table>
					
				</div>
			
				<div style='width: $this->width_editor'>
					<table cellspacing=0 cellpadding=10 style='100%; margin: 0'>
						$rows
					</table>
				</div>
				
		";
			
		if (!$edit) $this->content .= "<input type='hidden' name='mte_new_rec' value='1'>";
		if ($this->query_joomla_component) $this->content .= "<input type='hidden' name='option' value='$this->query_joomla_component'>";
		
		
		$this->content .= "
				<input type='hidden' name='mte_a' value='save'>
				
			</form>
			";

# Publish linked params
		$this->content .= "
				<br>
				<div style='width: 550px;'>
					<table cellspacing=0 cellpadding=10 style='width: 550px;'>
						$rows_params
					</table>
				</div>
		";

# Publish linked translations
		$this->content .= "
				<br>
				<div style='width: 550px;'>
					<table cellspacing=0 cellpadding=10 style='width: 550px;'>
						$rows_rus
					</table>
				</div>
		";

# Publish linked lessons
		$this->content .= "
				<br>
				<div style='width: 550px;'>
					<table cellspacing=0 cellpadding=0 style='width: 550px;'>
						$lessons
					</table>
				</div>
		";

		
	}




	###################
	function save_rec() {
	###################


		$in_mte_new_rec = $_POST['mte_new_rec'];
		
		$updates = '';
		
		foreach($_POST AS $key => $value) {
			if ($key == $this->primary_key) {
				$in_id = $value;
				$where = "$key = $value";
			}
			if ($key != 'mte_a' && $key != 'mte_new_rec' && $key != 'option') {
				if ($in_mte_new_rec) {
					$insert_fields .= " `$key`,";
					$insert_values .= " '" . addslashes(stripslashes($value)) . "',";
				}
				else {
					$updates .= " `$key` = '" . addslashes(stripslashes($value)) . "' ,";
				}
			}
		}
		$insert_fields = substr($insert_fields,0,-1);
		$insert_values = substr($insert_values,0,-1);
		$updates = substr($updates,0,-1);
		

		# new record:
		if ($in_mte_new_rec) {
			$sql = "INSERT INTO `$this->table` ($insert_fields) VALUES ($insert_values); ";	
		}
		# edit record:
		else {
			$sql = "UPDATE `$this->table` SET $updates WHERE $where LIMIT 1; ";	
		}
		

		//echo $sql; exit;
		if (mysql_query($sql)) {
			if ($in_mte_new_rec) {
				$saved_id = mysql_insert_id();
				$_GET['s'] = $saved_id;
				$_GET['f'] = $this->primary_key;
			}
			else {
				$saved_id = $in_id;
			}
			if ($this->show_text[$this->primary_key]) {$show_primary_key = $this->show_text[$this->primary_key];}
				else {$show_primary_key = $this->primary_key;}

			$_SESSION['content_saved'] = "
				<div style='width: $this->width_editor'>
					<div style='padding: 10px; color:#fff; background: #67B915; font-weight: bold'>Record $show_primary_key $saved_id {$this->text['saved']}</div>
				</div>
				";
			if ($in_mte_new_rec) {
				echo "<script>window.location='?start=0&f=&sort=" . $this->primary_key . "&ad=d";
				if ($this->query_joomla_component) {
					echo '&option=' . $this->query_joomla_component ;
				}
				echo "'</script>";
			}
			else {
				echo "<script>window.location='" . $_SESSION['hist_page'] . "'</script>";
			}
		}
		else {
			$this->content = "
				<div style='width: $this->width_editor'>
					<div style='padding:2px 20px 20px 20px;margin: 0 0 20px 0; background: #DF0000; color: #fff;'><h3>Error</h3>" .
					mysql_error() . 
					"</div><a href='{$_SESSION['hist_page']}'>{$this->text['Go_back']}...</a>
				</div>";
		}
	}




	##########################
	function close_and_print() {
	##########################


		# debug and warning no htaccess
		if ($this->debug) $this->debug .= '<br />';
#		if (!file_exists('./.htaccess') && $this->no_htaccess_warning == 'on') $this->debug .= "{$this->text['Protect_this_directory_with']} .htaccess";

		if ($this->debug) 
		$this->debug_html = "
			<div style='width: $this->width_editor'>
				<div class='mte_mess' style='background: #DD0000'>$this->debug</div>
			</div>";


		# save page location
		$session_hist_page = $this->url_script . '?' . $_SERVER['QUERY_STRING'];
		if ($this->query_joomla_component && !preg_match("/option=$this->query_joomla_component/",$session_hist_page)) {
			$session_hist_page .= '&option=' . $this->query_joomla_component;
		}
		
		// no page history on the edit page because after refresh the Go Back is useless 
		if (!$_GET['mte_a']) {
			$_SESSION['hist_page'] = $session_hist_page;
		}


		
		if ($this->query_joomla_component) $add_joomla = '?option=' . $this->query_joomla_component;
		
		echo "
		<script language='javascript'>
			$this->javascript
		</script>

		<link href='$this->url_base/css/mte.css' rel='stylesheet' type='text/css'>

		<style type='text/css'>
			.mte_content input {
				width: $this->width_input_fields;
			}
			.mte_content textarea {
				width: $this->width_text_fields;
				height: $this->height_text_fields;
			}
		</style>	

		<div class='mte_content'>
			<div class='mte_head_1'><a href='$this->url_script$add_joomla' style='text-decoration: none;color: #797979'>MySQL table edit</a> <span style='color: #ddd'>$this->version</span></div>
			$this->nav_top
			$this->debug_html
			$this->content_saved
			$this->content_deleted
			$this->content
		</div>
		
		";	
		
	}  
}
?>
