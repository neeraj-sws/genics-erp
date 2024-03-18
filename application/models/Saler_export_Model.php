<?php
if (!defined('BASEPATH'))
exit('No direct script access allowed');
class Saler_export_Model extends CI_Model {
	public function salerList() {
		$this->db->select(array('id', 'full_name', 'email', 'phone','status'));
		$this->db->from('distributor');
		
		$query = $this->db->get();
		return $query->result_array();
	}
}
?>
