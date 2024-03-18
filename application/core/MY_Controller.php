<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Base_Controller extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('base_model');
		$this->load->library('session');
		
		date_default_timezone_set('Asia/Kolkata');
	}
	
	public function loadAdminTemplate($file, $data = '')
	{
		$data['base'] = '';
		/********** || Site Setting Start || **********/
			$data['distributor'] = $this->base_model->select_count_data('distributor',array('role !='=>1,'status'=>1));
			$data['delivery_boy'] = $this->base_model->select_count_data('delivery_boy',array());
			$data['receipt'] = $this->base_model->select_count_data('receipt',array('status'=>1));
		$where1 = array('role !='=>1,'status'=>1);
		$group_by = "";
		$joins1 = array();
		$data['users'] = $this->base_model->select_count_data('users', $where1);
		$data['categorys'] = $this->base_model->select_count_data('category',array());
		$data['orders'] = $this->base_model->select_count_data('orders',array());
		$where = array('site_setting.id' => 1);
		$columns = 'site_setting.*,logo_imgtbl.file as logo_image,fav_imgtbl.file as fav_image';
		$joins[] = array('table' => 'upload_images as logo_imgtbl', 'condition' => 'logo_imgtbl.id=site_setting.logo', 'jointype' => 'left');
		$joins[] = array('table' => 'upload_images as fav_imgtbl', 'condition' => 'fav_imgtbl.id=site_setting.favicon', 'jointype' => 'left');
		$data['site_setting'] = $this->base_model->select_join_row('site_setting', $where, $joins, $columns);
		// echo '<pre>'; print_r($this->session->userdata('profile'));die;
		/********** || Site Setting End || **********/
		//echo "<pre>";print_r($data['site_setting']);die;
		$this->load->view('common/admin/header', $data);
		$this->load->view('common/admin/sidebar', $data);
		$this->load->view($file, $data);
		$this->load->view('common/admin/footer', $data);
	}

	
	public function loadUserTemplate($file, $data = '')
	{
		$data['base'] = '';
		/********** || Site Setting Start || **********/
		$data['code'] = $this->base_model->generate_code(5);
		// $data['buttonheading'] = 'Order';
		// echo '<pre>'; print_r($this->session->userdata('profile'));die;
		/********** || Site Setting End || **********/
		//echo "<pre>";print_r($data['site_setting']);die;
		$this->load->view('common/front/header', $data);
		$this->load->view($file, $data);
		$this->load->view('common/front/footer', $data);
	}
	public function loadLoginTemplate($file, $data = '')
	{
		$data['base'] = 'customer';
		/********** || Site Setting Start || **********/
		$where = array('site_setting.id' => 1);
		$columns = 'site_setting.*,logo_imgtbl.file as logo_image,fav_imgtbl.file as fav_image';
		$joins[] = array('table' => 'upload_images as logo_imgtbl', 'condition' => 'logo_imgtbl.id=site_setting.logo', 'jointype' => 'left');
		$joins[] = array('table' => 'upload_images as fav_imgtbl', 'condition' => 'fav_imgtbl.id=site_setting.favicon', 'jointype' => 'left');
		$data['site_setting'] = $this->base_model->select_join_row('site_setting', $where, $joins, $columns);

		
		/********** || Site Setting End || **********/
		$this->load->view('common/auth/header', $data);
		$this->load->view($file, $data);
		$this->load->view('common/auth/footer', $data);
	}

	public function setAdminSession($data)
	{
		$newdata = array(
			'id' => $data->id,
			'email' => $data->email,
			'title_name' => $data->full_name,
			'profile' => $data->profile,
			'role' => $data->role,
			'title_type' => 'Admin',
			'is_admin_login' => true,
		);
		$this->session->set_userdata($newdata);
		return true;
	}

	public function setUserSession($data)
	{
		$newdata = array(
			'id' => $data->id,
			'otp'=> $data->otp,
			'email' => $data->email,
			'title_user' => 'User',
			'is_user_login' => true,
		);
		$this->session->set_userdata($newdata);
		return true;
	}
	
}
