<?php
class Party_Excel_import_model extends CI_Model
{
 function select()
 {
  $this->db->order_by('id', 'DESC');
  $query = $this->db->get('users');
  return $query;
 }

 function insert($data)
 {
  $this->db->insert_batch('users', $data);
 }
}