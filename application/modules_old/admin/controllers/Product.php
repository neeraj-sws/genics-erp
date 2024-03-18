<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product extends Base_Controller {

	public function __construct(){ 
		parent::__construct();
		$this->load->model('base_model');
		$this->load->library('session');
	}

	public function index()
	{
		if(!$this->session->userdata('is_admin_login')){ redirect(base_url('admin/login'));  }
		$data['nav'] ='product';
		$data['sub_nav'] ='product';
		$data['title'] ='product';
		$this->loadAdminTemplate('product/index',$data);

	}
	
	public function product_ajax_list(){ 


		$requestData= $_REQUEST;	
		
		$where_in = $where_like = $where_condition1 = $where_condition2  =array();
		if(isset($_POST['searchName']) && !empty($_POST['searchName'])){
			$where_like = array('product.title'=>$_POST['searchName']);
		}
		
		if(isset($_POST['status']) && $_POST['status'] !=''){
			$where_condition2 = array('product.status'=>$_POST['status']);
		}
		$where_condition1 = array();
		$where = array_merge($where_condition1,$where_condition2);
		$coloum_search = array('product.title');
		$order_by = array('product.id'=>'DESC');
		
		if(isset($requestData['order'])){
			if($requestData['order']['0']['column'] == 1){
				$order_by = array('product.title'=>$requestData['order']['0']['dir']);
			}elseif($requestData['order']['0']['column'] == 3){
				$order_by = array('product.short_description'=>$requestData['order']['0']['dir']);
			}elseif($requestData['order']['0']['column'] == 4){
				$order_by = array('product.category'=>$requestData['order']['0']['dir']);
			}elseif($requestData['order']['0']['column'] == 5){
				$order_by = array('product.price'=>$requestData['order']['0']['dir']);
			}
		}
		
		$columns="product.*,category.title as cat_name";
		$joins[] = array('table'=>'category','condition'=>'category.id=product.category','jointype'=>'left');
		$lists = $this->base_model->get_where_in_lists('product',$coloum_search,$order_by,'',$where_in,$joins,$columns,'product.id',$where,'',$where_like);
		$data = array();
		$no = $_POST['start'];
		if($lists){
			foreach ($lists as $list) {
				$no++;
				$row = array();
				$row[] = $no;

				$row[] =  $list->title;

                $row[] =  $list->cat_name;

                $row[] =  $list->price;

				if($list->status == 0){
					$status = '<button type="button" class="btn btn-danger btn-xs" onclick="product_status('.$list->id.',1)"><small>Inactive</small></button>';
				}else{
					$status = '<button type="button" class="btn btn-success btn-xs" onclick="product_status('.$list->id.',0)"><small>Active</small></button>';	
				}
				$row[] = $status;
	
				
				$orders='<button type="button" class="btn btn-info btn-xs"><i class="fa fa-eye" aria-hidden="true"></i></button>';
				$buttons = '<button type="button" class="btn btn-success btn-xs" onclick="product_edit('.$list->id.')"><small>Edit</small></button>';
				//$buttons .= '&nbsp;&nbsp;<button type="button" class="btn btn-success btn-xs" onclick="user_view('.$list->id.')"><small>View</small></button>';
				$buttons .= '&nbsp;&nbsp;<button type="button" class="btn btn-danger btn-xs" onclick="product_delete('.$list->id.')"><small>Delete</small></button>';
				$row[] = $buttons;
				$row['id'] = $list->id;

				$data[] = $row;

			}
    
		}
		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->base_model->count_all_where_in('product',$where_in,'product.id',$where,$joins),
			"recordsFiltered" =>$this->base_model->count_filtered_where_in('product',$coloum_search,$order_by,'',$where_in,$joins,$columns,'product.id',$where),
			"data" => $data,
		);

		echo json_encode($output);
	}
	
	
	public function add()
	{ 
		$data['categorys'] = $this->base_model->select_data('category',array());
		$this->load->view('product/add',$data);
	}
	
	public function product_save()
	{
		$this->form_validation->set_rules('title', 'Title', 'required');	 
		$this->form_validation->set_rules('status', 'Status', 'required');
        $this->form_validation->set_rules('price', 'Price', 'required');
        $this->form_validation->set_rules('category', 'Category', 'required');
        $this->form_validation->set_rules('short_description', 'Sort Description', 'required');

		if($this->form_validation->run() == FALSE){
			$m = json_encode($this->form_validation->error_array());
			$res =array('status'=>0,'msg'=>$m);
			echo json_encode($res);die;
		}else{ 
			
			$data =array(
				'title'=>trim($this->input->post('title')),
				'status'=>trim($this->input->post('status')),
                'price'=>trim($this->input->post('price')),
                'category'=>trim($this->input->post('category')),
                'short_description'=>trim($this->input->post('short_description')),
				'Created_at'=>time(),
				'Updated_at'=>time(),
			);
			$uid = $this->base_model->insert_data('product',$data);
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
	
	public function product_delete(){
		$this->base_model->delete_data('product',array('id'=>$this->input->post('id')));
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
	
	public function product_edit(){

		$where = array('product.id'=>$this->input->post('id'));
		$columns="product.*";
		$joins = array();
		$data['pinfo'] = $this->base_model->select_join_row('product',$where,$joins,$columns);
		$data['categorys'] = $this->base_model->select_data('category',array());
		$this->load->view('product/edit',$data);
	}
	
	
	public function product_update()
	{
		$original_value = $this->base_model->select_row('product',array('id'=>$this->input->post('id')));
        $this->form_validation->set_rules('title', 'Title', 'required');	 
		$this->form_validation->set_rules('status', 'Status', 'required');
        $this->form_validation->set_rules('price', 'Price', 'required');
        $this->form_validation->set_rules('category', 'Category', 'required');
        $this->form_validation->set_rules('short_description', 'Sort Description', 'required');
		
		if($this->form_validation->run() == FALSE){
			$m = json_encode($this->form_validation->error_array());
			$res =array('status'=>0,'msg'=>$m);
			echo json_encode($res);die;
		}else{ 
			
			
			$data =array(
				'title'=>trim($this->input->post('title')),
				'status'=>trim($this->input->post('status')),
                'price'=>trim($this->input->post('price')),
                'category'=>trim($this->input->post('category')),
                'short_description'=>trim($this->input->post('short_description')),
				'Updated_at'=>time(),
			);
			$this->base_model->update_data('product',array('id'=>$this->input->post('id')),$data);
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
	
	public function product_status(){
		$this->base_model->update_data('product',array('id'=>$this->input->post('id')),array('status'=>$this->input->post('type')));
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
