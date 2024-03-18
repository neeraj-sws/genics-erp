<?php
class Excel_import_model extends CI_Model
{
 function select()
 {
  $this->db->order_by('id', 'DESC');
  $query = $this->db->get('distributor');
  return $query;
 }

 function insert($data)
 {
  $this->db->insert_batch('distributor', $data);
 }
}