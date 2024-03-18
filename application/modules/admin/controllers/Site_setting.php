<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Site_setting extends Base_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('base_model');
		$this->load->library('session');
	}

	public function index()
	{
		if(!$this->session->userdata('is_admin_login')){ redirect(base_url('admin/login'));  }
		$data['nav'] ='setting';
		$data['sub_nav'] ='site_setting';
		$data['title'] ='Site Setting';

		$where = array('site_setting.id'=>1);
		$columns = 'site_setting.*,logo_imgtbl.file as logo_image,fav_imgtbl.file as fav_image, preloader_imgtbl.file as preloader_image';
		$joins[] = array('table'=>'upload_images as logo_imgtbl','condition'=>'logo_imgtbl.id=site_setting.logo','jointype'=>'left');
		$joins[] = array('table'=>'upload_images as fav_imgtbl','condition'=>'fav_imgtbl.id=site_setting.favicon','jointype'=>'left');
		$joins[] = array('table'=>'upload_images as preloader_imgtbl','condition'=>'preloader_imgtbl.id=site_setting.preloader','jointype'=>'left');
		$data['site_setting_data'] = $this->base_model->select_join_row('site_setting',$where,$joins,$columns);
		//echo "<pre>";print_r($data['site_setting_data'] );die;
		$this->loadAdminTemplate('admin/site_setting/index',$data);
	}
		

	public function logo_image(){
		$res = array();
		$name = $_FILES["logo"]["name"];
		$ext = pathinfo($name, PATHINFO_EXTENSION);
		$new_name = time().'.'.$ext;
		$config['file_name'] = $new_name;
		$path = 'assets/uploads/site_setting/';
		$config['upload_path']   = $path;
		$config['allowed_types'] = 'jpg|png|jpeg|JPG|PNG|JPEG'; 
		$config['max_size']             = 3145728;
        $config['max_width']            = 200;
        $config['max_height']           = 150;
		$this->load->library('upload', $config);

		if(!$this->upload->do_upload('logo')){
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


	public function favicon_image(){
		
		$res = array();
		$name = $_FILES["favicon"]["name"];
		$ext = pathinfo($name, PATHINFO_EXTENSION);
		$new_name = time().'.'.$ext;
		$config['file_name'] = $new_name;
		$path = 'assets/uploads/site_setting/';
		$config['upload_path']   = $path;
		$config['allowed_types'] = 'jpg|png|jpeg'; 
		$config['max_size']             = 3145728;
        $config['max_width']            = 32;
        $config['max_height']           = 32;
		$this->load->library('upload', $config);

		if(!$this->upload->do_upload('favicon')){
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
 
     public function preloader_image(){
		
		$res = array();
		$name = $_FILES["preloader"]["name"];
		$ext = pathinfo($name, PATHINFO_EXTENSION);
		$new_name = time().'.'.$ext;
		$config['file_name'] = $new_name;
		$path = 'assets/uploads/site_setting/';
		$config['upload_path']   = $path;
		$config['allowed_types'] = 'jpg|png|jpeg'; 
		$config['max_size']             = 3145728;
        $config['max_width']            = 300;
        $config['max_height']           = 300;
		$this->load->library('upload', $config);

		if(!$this->upload->do_upload('preloader')){
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

	public function update_data(){
		$this->form_validation->set_rules('title', 'Title', 'required');	
		$this->form_validation->set_rules('logo_id', 'Logo Image', 'required');	
		$this->form_validation->set_rules('favicon_id', 'Favicon', 'required');	
		$this->form_validation->set_rules('preloader_id', 'Preloader Image', 'required');
		$this->form_validation->set_rules('aboutus_footer', 'About Us', 'required|max_length[300]');		
		$this->form_validation->set_rules('meta_authore', 'Meta Authore', 'required');	
		$this->form_validation->set_rules('meta_keyword', 'Meta Keyword', 'required');	
		$this->form_validation->set_rules('meta_description', 'Meta Description', 'required|max_length[300]');	
		$this->form_validation->set_rules('system_email', 'System Email', 'required|valid_email');	
		
		if($this->form_validation->run() == FALSE){
			$m = json_encode($this->form_validation->error_array());
			$res =array('status'=>0,'msg'=>$m);
			echo json_encode($res);die;
		}else{ 
			$data =array(
				'title'=>trim($this->input->post('title')),
				'logo'=>trim($this->input->post('logo_id')),
				'favicon'=>trim($this->input->post('favicon_id')),
				'preloader'=>trim($this->input->post('preloader_id')),
				'aboutus_footer'=>trim($this->input->post('aboutus_footer')),
				'meta_authore'=>trim($this->input->post('meta_authore')),
				'meta_keyword'=>trim($this->input->post('meta_keyword')),
				'meta_description'=>trim($this->input->post('meta_description')),
				'system_email'=>trim($this->input->post('system_email')),
				'send_email'=>trim($this->input->post('send_email')),
			);
			$this->base_model->update_data('site_setting',array('id'=>1),$data);
			$res =array('status'=>1);
			echo json_encode($res);die;

		}

	}

}
