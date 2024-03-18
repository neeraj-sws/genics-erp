<?php
if (!defined('BASEPATH'))
exit('No direct script access allowed');
class Party_export_Model extends CI_Model {
	public function partyList() {
		$this->db->select(array('id', 'full_name', 'email', 'phone','city','status'));
		$this->db->from('users');
		
		$query = $this->db->get();
		return $query->result_array();
	}
}
?>
