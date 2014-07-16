<?php

class aliyah_admin {

	function page_header() {

		global $tpl, $config, $lang, $img;

		$tpl->set_filenames(array(
				'header' => 'header.tpl'
			)
		);
		
		$tpl->assign_vars(array(
				'T_TITLE' => 'FC Aliyah.co admin area',
				'U_CSS' => $tpl->root . '/' . $config['style']['css']
			)
		);		
	}
	
	function page_footer() {

		global $tpl, $img;

		$tpl->set_filenames(array(
				'footer' => 'footer.tpl'
			)
		);
	}
	function output() {
	
		global $tpl;

		foreach ( $tpl->files as $name => $file ) :
		
			$tpl->pparse($name);
			
		endforeach;
	}
	
	function stop() {

		global $db;

		$db->disconnect();
	}

	function picture_add() {
	
		global $tpl;
	
		$tpl->set_filenames(array(
				'form' => 'picture_add_form.tpl'
			)
		);
		
		$tpl->assign_vars(array(
				'ACTION' => '?',
				'MODE_VAR' => MODE_VAR,
				'MODE_PICTURE_UPLOAD' => MODE_PICTURE_UPLOAD,
				'VAR_PICTURE_FILE' => VAR_PICTURE_FILE
			)
		);
	}
	
	function picture_upload() {
	
		global $config;
	
		$valid = true;

		if ( isset($_FILES) && $_FILES['userfile']['error'] ) :
			$errors_txt[] = 'No file uploaded.';
			$valid = false;
		endif;
		
		if ( $valid ) :
			$filename = mt_rand() . '_' . $_FILES['userfile']['name'];
			$uploadfile = $config['path']['content']['pictures'] . '/' . $filename;
			if ( move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile) ) :
				chgrp($uploadfile, 'www');
				chmod($uploadfile, 0755);
				
				if ( !$valid ) :
					unlink($uploadfile);
				endif;
			else :
				$valid = false;
			endif;
		endif;
		
		if ( !$valid ) :
			die('error occured');
		else :
			$this->process_picture($filename);
		endif;
	}
	
	function process_picture($filename) {
	
		global $db;
		
		$dbs = $db->structure(PICTURES_TABLE);
		
		$sql = 'INSERT INTO '
					. PICTURES_TABLE
					. ' ( ' . $dbs['file'] . ')
				VALUES	
					("' . $filename . '");';
		$result = $db->query($sql);
	}
}
?>