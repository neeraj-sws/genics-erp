<?php
if (!defined('BASEPATH'))
exit('No direct script access allowed');
class Order_export_Model extends CI_Model {
	public function orderList() {
		$this->db->select(array('id', 'party_name', 'payment_term', 'dispached', 'city','distributor_id','status'));
		$this->db->from('orders');
		
		$query = $this->db->get();
		return $query->result_array();
	}
}
?>
