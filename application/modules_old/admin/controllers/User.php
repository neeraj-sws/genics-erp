<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends Base_Controller {

	public function __construct(){ 
		parent::__construct();
		$this->load->model('base_model');
		$this->load->library('session');
	}

	public function index()
	{
		if(!$this->session->userdata('is_admin_login')){ redirect(base_url('admin/login'));  }
		$data['nav'] ='user';
		$data['sub_nav'] ='user';
		$data['title'] ='User';
		$this->loadAdminTemplate('user/index',$data);

	}
	
	public function user_ajax_list(){ 
		
		$requestData= $_REQUEST;	
		
		$where_in = $where_like = $where_condition1 = $where_condition2 = $where_condition3 = $where_condition4 =$where_condition5 =$where_condition6 =array();
		if(isset($_POST['searchName']) && !empty($_POST['searchName'])){
			$where_like = array('users.full_name'=>$_POST['searchName']);
		}
		
		if(isset($_POST['status']) && $_POST['status'] !=''){
			$where_condition2 = array('users.status'=>$_POST['status']);
		}
		$where_condition1 = array('users.role !='=>1);
		$where = array_merge($where_condition1,$where_condition2,$where_condition3,$where_condition4,$where_condition5,$where_condition6);
		$coloum_search = array('users.full_name','users.email');
		$order_by = array('users.id'=>'DESC');
		
		if(isset($requestData['order'])){
			if($requestData['order']['0']['column'] == 1){
				$order_by = array('users.full_name'=>$requestData['order']['0']['dir']);
			}elseif($requestData['order']['0']['column'] == 3){
				$order_by = array('users.email'=>$requestData['order']['0']['dir']);
			}elseif($requestData['order']['0']['column'] == 4){
				$order_by = array('users.role'=>$requestData['order']['0']['dir']);
			}elseif($requestData['order']['0']['column'] == 5){
				$order_by = array('users.phone'=>$requestData['order']['0']['dir']);
			}elseif ($requestData['order']['0']['column']==6){
				$order_by = array('users.status'=>$requestData['order']['0']['dir']);
			}
		}
		
		$columns="users.*,upload_images.file";
		$joins[] = array('table'=>'upload_images','condition'=>'upload_images.id=users.file_id','jointype'=>'left');
		$lists = $this->base_model->get_where_in_lists('users',$coloum_search,$order_by,'',$where_in,$joins,$columns,'users.id',$where,'',$where_like);
		
		
		
			
		$data = array();
		$no = $_POST['start'];
		if($lists){
			foreach ($lists as $list) {
				$kyc = $this->base_model->select_row('user_kyc',array('user_id'=>$list->id));
				$no++;
				$row = array();
				$row[] = $no;
				$img='';
				if($list->file_id !=0){
				$img =  '<img src="'.base_url('assets/uploads/users/').$list->file.'" class="img-fluid" width="50px" />';
				}
				$row[] =  $img.' '.ucfirst($list->full_name);
				$row[] =  $list->email;
				$row[] =  $list->phone;

			
				if($list->role == 2){
					 $pro = '<span class="badge bg-primary">Production</span>';
				}elseif($list->role == 3){
					$pro = '<span class="badge bg-info text-dark">Sales</span>';
				}elseif($list->role == 4){
					$pro = '<span class="badge bg-success">Distributor</span>';
				}else{
					$pro = " ";
				}
				$row[] =  $pro;
				
				//  $kyc_btn='';
				// if(!$kyc){
				// 	$kyc_btn .='<span class="badge badge-danger">Not available</span>';
				// }else{
				// 	if($kyc->status == 0){
				// 		$kyc_btn .= '<button type="button" class="btn btn-danger btn-xs" onclick="kyc_status('.$list->id.',1)"><small>Inactive</small></button>';
				// 	}else{
				// 		$kyc_btn .= '<button type="button" class="btn btn-success btn-xs" onclick="kyc_status('.$list->id.',0)"><small>Active</small></button>';
				// 	}
				// 	$kyc_btn .='<button type="button" class="btn btn-info btn-xs" onclick="kyc_view('.$list->id.',0)"><small>View</small></button>';
				// }
				// $row[] = $kyc_btn;

				if($list->status == 0){
					$status = '<button type="button" class="btn btn-danger btn-xs" onclick="user_status('.$list->id.',1)"><small>Inactive</small></button>';
				}else{
					$status = '<button type="button" class="btn btn-success btn-xs" onclick="user_status('.$list->id.',0)"><small>Active</small></button>';	
				}
				$row[] = $status;
				
				if($list->otp_status == 0){
					$otp_status = '<button type="button" class="btn btn-danger btn-xs" onclick="user_verify('.$list->id.',1)"><small>Inactive</small></button>';
				}else{
					$otp_status = '<button type="button" class="btn btn-success btn-xs" onclick="user_verify('.$list->id.',0)"><small>Active</small></button>';	
				}
				$row[] = $otp_status;
				
				$orders='<button type="button" class="btn btn-info btn-xs"><i class="fa fa-eye" aria-hidden="true"></i></button>';
				$buttons = '<button type="button" class="btn btn-success btn-xs" onclick="user_edit('.$list->id.')"><small>Edit</small></button>';
				//$buttons .= '&nbsp;&nbsp;<button type="button" class="btn btn-success btn-xs" onclick="user_view('.$list->id.')"><small>View</small></button>';
				$buttons .= '&nbsp;&nbsp;<button type="button" class="btn btn-danger btn-xs" onclick="user_delete('.$list->id.')"><small>Delete</small></button>';
				$row[] = $buttons;
				$row['id'] = $list->id;

				$data[] = $row;

			}
		}
		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->base_model->count_all_where_in('users',$where_in,'users.id',$where,$joins),
			"recordsFiltered" =>$this->base_model->count_filtered_where_in('users',$coloum_search,$order_by,'',$where_in,$joins,$columns,'users.id',$where),
			"data" => $data,
		);

		echo json_encode($output);
	}
	
	
	public function add()
	{ 
		$data['roles'] = $this->base_model->select_data('all_cities',array());
		$this->load->view('user/add',$data);
	}
	
	public function user_save()
	{
		$this->form_validation->set_rules('full_name', 'Full Name', 'required');	 
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim|callback_check_for_user');
		$this->form_validation->set_rules('phone', 'Phone Number', 'required|exact_length[10]|numeric|is_unique[users.phone]');
		$this->form_validation->set_rules('city', 'City', 'required');
		$this->form_validation->set_rules('status', 'Status', 'required');
		$this->form_validation->set_rules('role', 'Role', 'required');
		$this->form_validation->set_rules('file_id', 'Image', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
		
		if($this->form_validation->run() == FALSE){
			$m = json_encode($this->form_validation->error_array());
			$res =array('status'=>0,'msg'=>$m);
			echo json_encode($res);die;
		}else{ 
			
			$data =array(
				'full_name'=>trim($this->input->post('full_name')),
				'email'=>trim($this->input->post('email')),
				'phone'=>trim($this->input->post('phone')),
				'city'=>trim($this->input->post('city')),
				'status'=>trim($this->input->post('status')),
				'role'=>trim($this->input->post('role')),
				'password'=>trim(sha1($this->input->post('password'))),
				'file_id'=>trim($this->input->post('file_id')),
				'created_at'=>time(),
				'updated_at'=>time(),
			);
			$uid = $this->base_model->insert_data('users',$data);
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
	
	public function user_delete(){
		$this->base_model->delete_data('users',array('id'=>$this->input->post('id')));
		$res =array('status'=>1);
		echo json_encode($res);die;
	}
	
	public function user_multiple_delete(){		
		$checks = $this->input->post('check');
		
		foreach($checks as $value){
			$this->base_model->delete_data('users',array('id'=>$value));
		}
		$res =array('status'=>1);
		echo json_encode($res);die;
	}
	
	public function user_edit(){

		$where = array('users.id'=>$this->input->post('id'));
		$columns="users.*,upload_images.file,upload_images.id as fid";
		$joins[] = array('table'=>'upload_images','condition'=>'upload_images.id=users.file_id','jointype'=>'left');
		$data['uinfo'] = $this->base_model->select_join_row('users',$where,$joins,$columns);
		$data['roles'] = $this->base_model->select_data('all_cities',array());
		$this->load->view('user/edit',$data);
	}
	
	
	public function user_update()
	{
		$original_value = $this->base_model->select_row('users',array('id'=>$this->input->post('id')));
		if($this->input->post('email') != $original_value->email) {
			$is_unique =  '|callback_check_for_user';
		} else {
			$is_unique =  '';
		}

		if($this->input->post('phone') != $original_value->phone) {
			$is_unique1 =  '|callback_check_for_phone';
		} else {
			$is_unique1 =  '';
		}
		
		$this->form_validation->set_rules('full_name', 'Full Name', 'required');	 
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim'.$is_unique);
		$this->form_validation->set_rules('city', 'City', 'required');
		$this->form_validation->set_rules('role', 'Role', 'required');
		$this->form_validation->set_rules('phone', 'Phone Number', 'required|exact_length[10]|trim'.$is_unique1);
		$this->form_validation->set_rules('status', 'Status', 'required');
		
		if($this->form_validation->run() == FALSE){
			$m = json_encode($this->form_validation->error_array());
			$res =array('status'=>0,'msg'=>$m);
			echo json_encode($res);die;
		}else{ 
			
			
			$data =array(
				'full_name'=>trim($this->input->post('full_name')),
				'email'=>trim($this->input->post('email')),
				'city'=>trim($this->input->post('city')),
				'phone'=>trim($this->input->post('phone')),
				'status'=>trim($this->input->post('status')),
				'role'=>trim($this->input->post('role')),
				'file_id'=>trim($this->input->post('file_id')),
				'updated_at'=>time(),
			);
			
			$this->base_model->update_data('users',array('id'=>$this->input->post('id')),$data);
			$res =array('status'=>1);
			echo json_encode($res);die;
		}
	}	

	public function user_view(){
		$where = array('users.id'=>$this->input->post('id'));
		$columns="users.*,users.id as uid, role.role,upload_images.file";
		$joins[] = array('table'=>'role','condition'=>'role.id=users.role','jointype'=>'left');
		$joins[] = array('table'=>'upload_images','condition'=>'upload_images.id=users.file_id','jointype'=>'left');
		$data['uinfo'] = $uinfo = $this->base_model->select_join_row('users',$where,$joins,$columns);
		$this->load->view('user/view',$data);
	}
	
	public function user_status(){
		$this->base_model->update_data('users',array('id'=>$this->input->post('id')),array('status'=>$this->input->post('type')));
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
