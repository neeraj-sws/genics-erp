<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Receipt extends Base_Controller {

	public function __construct(){ 
		parent::__construct();
		$this->load->model('base_model');
		$this->load->library('session');
	}

	public function index()
	{
		if(!$this->session->userdata('is_admin_login')){ redirect(base_url('admin/login'));  }
		$data['nav'] ='receipt';
		$data['sub_nav'] ='receipt';
		$data['title'] ='receipt';
		$this->loadAdminTemplate('receipt/index',$data);

	}
	
	public function receipt_ajax_list(){ 


		$requestData= $_REQUEST;	
		
		$where_in = $where_like = $where_condition1 = $where_condition2  =array();
		if(isset($_POST['searchName']) && !empty($_POST['searchName'])){
			$where_like = array('receipt.name'=>$_POST['searchName']);
		}
		
		if(isset($_POST['status']) && $_POST['status'] !=''){
			$where_condition2 = array('receipt.status'=>$_POST['status']);
		}
		$where_condition1 = array();
		$where = array_merge($where_condition1,$where_condition2);
		$coloum_search = array('receipt.name','receipt.email');
		$order_by = array('receipt.id'=>'DESC');
		
		if(isset($requestData['order'])){
			if($requestData['order']['0']['column'] == 1){
				$order_by = array('receipt.name'=>$requestData['order']['0']['dir']);
			}
		}
		
		$columns="receipt.*";
		$joins = array();
		$lists = $this->base_model->get_where_in_lists('receipt',$coloum_search,$order_by,'',$where_in,$joins,$columns,'receipt.id',$where,'',$where_like);
		
		$data = array();
		$no = $_POST['start'];
		if($lists){
			foreach ($lists as $list) {
				$no++;
				$row = array();
				$row[] = $no;

				$row[] =  $list->name;
				$row[] =  $list->email;

				if($list->status == 0){
					$status = '<button type="button" class="btn btn-danger btn-xs" onclick="receipt_status('.$list->id.',1)"><small>Inactive</small></button>';
				}else{
					$status = '<button type="button" class="btn btn-success btn-xs" onclick="receipt_status('.$list->id.',0)"><small>Active</small></button>';	
				}
				$row[] = $status;
	
				
				$orders='<button type="button" class="btn btn-info btn-xs"><i class="fa fa-eye" aria-hidden="true"></i></button>';
				$buttons = '<button type="button" class="btn btn-success btn-xs" onclick="receipt_edit('.$list->id.')"><small>Edit</small></button>';
				
				$buttons .= '&nbsp;&nbsp;<button type="button" class="btn btn-danger btn-xs" onclick="receipt_delete('.$list->id.')"><small>Delete</small></button>';
				$row[] = $buttons;
				$row['id'] = $list->id;

				$data[] = $row;

			}
		}
		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->base_model->count_all_where_in('receipt',$where_in,'receipt.id',$where,$joins),
			"recordsFiltered" =>$this->base_model->count_filtered_where_in('receipt',$coloum_search,$order_by,'',$where_in,$joins,$columns,'receipt.id',$where),
			"data" => $data,
		);

		echo json_encode($output);
	}
	
	
	public function add()
	{ 
		$data= array();
		$this->load->view('receipt/add',$data);
	}
	
	public function receipt_save()
	{
		$this->form_validation->set_rules('name', 'Name', 'required');		
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim|callback_check_for_receipt'); 
		$this->form_validation->set_rules('status', 'status', 'required');
		if($this->form_validation->run() == FALSE){
			$m = json_encode($this->form_validation->error_array());
			$res =array('status'=>0,'msg'=>$m);
			echo json_encode($res);die;
		}else{ 
			
			$data =array(
				'name'=>trim($this->input->post('name')),
				'email'=>trim($this->input->post('email')),
				'status'=>trim($this->input->post('status')),
				'created_at'=>time(),
				'update_at'=>time(),
			);
			$uid = $this->base_model->insert_data('receipt',$data);
			$res =array('status'=>1);
			echo json_encode($res);die;

		}

	}	
	
	function alpha_dash_space($fullname){
		if (! preg_match('/^[a-zA-Z\s]+$/', $fullname)) {
			$this->form_validation->set_message('alpha_dash_space', 'The %s field may only contain alpha characters & White spaces');
			return FALSE;
		} else {
			return TRUE;
		}
	}
	
	public function receipt_delete(){
		$this->base_model->delete_data('receipt',array('id'=>$this->input->post('id')));
		$res =array('status'=>1);
		echo json_encode($res);die;
	}
	
	

	
	public function receipt_edit(){

		$where = array('receipt.id'=>$this->input->post('id'));
		$columns="receipt.*";
		$joins = array();
		$data['cinfo'] = $this->base_model->select_join_row('receipt',$where,$joins,$columns);
	
		$this->load->view('receipt/edit',$data);
	}
	
	
	public function receipt_update()
	{
		$original_value = $this->base_model->select_row('receipt',array('id'=>$this->input->post('id')));

		if($this->input->post('email') != $original_value->email) {
			$is_unique =  '|callback_check_for_user';
		} else {
			$is_unique =  '';
		}
		$this->form_validation->set_rules('name', 'name', 'required');	 
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim'.$is_unique); 	 
		$this->form_validation->set_rules('status', 'Status', 'required');
		
		if($this->form_validation->run() == FALSE){
			$m = json_encode($this->form_validation->error_array());
			$res =array('status'=>0,'msg'=>$m);
			echo json_encode($res);die;
		}else{ 
			
			
			$data =array(
				'name'=>trim($this->input->post('name')),
				'email'=>trim($this->input->post('email')),
				'status'=>trim($this->input->post('status')),
				'update_at'=>time(),
			);
			$this->base_model->update_data('receipt',array('id'=>$this->input->post('id')),$data);
			$res =array('status'=>1);
			echo json_encode($res);die;
		}
	}	

	function check_for_receipt($email){
		$check = $this->base_model->select_row('receipt',array('email'=>$email));
		if (!empty($check)){
			$this->form_validation->set_message('check_for_receipt','Email Field should contain unique value.');
			return FALSE;
		}
		return TRUE;
	}
	public function receipt_status(){
		$this->base_model->update_data('receipt',array('id'=>$this->input->post('id')),array('status'=>$this->input->post('type')));
		$res =array('status'=>1);
		echo json_encode($res);die;
	}
	
	public function user_verify(){
		$this->base_model->update_data('users',array('id'=>$this->input->post('id')),array('otp_status'=>$this->input->post('type')));
		$res =array('status'=>1);
		echo json_encode($res);die;
	}

	function check_for_user($email){
		$check = $this->base_model->select_row('users',array('email'=>$email));
		if (!empty($check)){
			$this->form_validation->set_message('check_for_user','Email Field should contain unique value.');
			return FALSE;
		}
		return TRUE;
	}

	function check_for_phone($phone){
		$check = $this->base_model->select_row('users',array('phone'=>$phone));
		if (!empty($check)){
			$this->form_validation->set_message('check_for_phone','Phone Field should contain unique value.');
			return FALSE;
		}
		return TRUE;
	}


	public function users_image_data()
	{
		$res = array();
		$name = $_FILES["file"]["name"];
		$ext = pathinfo($name, PATHINFO_EXTENSION);
		$new_name = rand(1000,9999).'_'.time().'.'.$ext;
		$config['file_name'] = $new_name;
		$path = 'assets/uploads/users/';
		$config['upload_path']   = $path;
		$config['allowed_types'] = 'jpg|png|jpeg'; 
		$this->load->library('upload', $config);

		if(!$this->upload->do_upload('file')){
			$m = json_encode(array('file_error' => $this->upload->display_errors()));
			$res = array('status'=>0,'msg'=>$m);
			echo  json_encode($res);die;
			$file_error = 1;
		}else{
			$upload_data = $this->upload->data();
			$file=$upload_data['file_name'];
			$file_error = 0;
		}
		if($file_error == 0){
			$data = array(
				'file'=>$file,
				'created_at'=>time(),
				'updated_at'=>time(),
			);
			$insert_id = $this->base_model->insert_data('upload_images',$data);
			$image_data = $this->base_model->select_row('upload_images',array('id'=>$insert_id));
			$res = array('status'=>1,'image_id'=>$insert_id,'image_data'=>base_url().$path.$image_data->file);
			echo  json_encode($res);die;
		}else{
			$res = array('status'=>0,'msg'=>$m);
			echo  json_encode($res);die;
		}
	}


	public function kyc_status(){
		$this->base_model->update_data('user_kyc',array('user_id'=>$this->input->post('id')),array('status'=>$this->input->post('type')));
		$res =array('status'=>1);
		echo json_encode($res);die;
	}


	public function kyc_view(){

		$where = array('user_kyc.user_id'=>$this->input->post('id'));
		$columns="user_kyc.*,a.file as adharcard_file,p.file as pancard_file";
		$joins[] = array('table'=>'upload_images as a','condition'=>'a.id=user_kyc.adhar_file_id','jointype'=>'left');
		$joins[] = array('table'=>'upload_images as p','condition'=>'p.id=user_kyc.pan_file_id','jointype'=>'left');
		$data['kyc_info'] = $this->base_model->select_join_row('user_kyc',$where,$joins,$columns);
		$data['uinfo'] = $this->base_model->select_row('users',array('id'=>$this->input->post('id')));
		$this->load->view('user/kyc',$data);
	}
}
