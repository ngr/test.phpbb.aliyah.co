<?php

class user {
	
	var $defined = false;
	var $data = array();
	var $admin = false;

	function user($login = false) {
	
		global $db;
	
		if ( !isset($_SESSION['userdata_aliyah']) ) {
			$dbs = $db->structure(USERS_TABLE);
			$sql = 'SELECT 
						' . $dbs['id'] . ' as id,
						' . $dbs['name'] . ' as alias,
						' . $dbs['level'] . ' as level
					FROM ' . USERS_TABLE . '
					WHERE ' . $dbs['login'] . ' LIKE "%' . $login . '%"
					LIMIT 1';
			$result = $db->query($sql);
			$row = $db->fetch_assoc($result);
			
			if ( $db->affected_rows ) {
				$this->defined = true;
				$_SESSION['userdata_aliyah'] = $row;
				$this->data = $row;
				$this->admin = $this->_is_admin();
			}
			else {
				$this->defined = false;
			}
		}
		else {
			$this->defined = true;
			$this->data = $_SESSION['userdata_aliyah'];

			$this->admin = $this->_is_admin();
		}
				
		return true;
	}
	
	function _is_admin() {
	
		if ( $this->defined ) :			
			return ( $this->data['level'] == 9 ) ? true : false;
		else :			
			return false;
		endif;
	}
}
?>