<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends Base_Controller {

	 public function __construct(){
	    parent::__construct();
		$this->load->model('base_model');
		$this->load->model('login_model');
		$this->load->model('site_model');
		$this->load->library('session');
		$this->load->helper('cookie');

	}

	public function index()
	{ 
		if($this->session->userdata('is_admin_login')){ redirect(base_url('admin'));  }
		$data['nav'] ='dashboard';
		$data['sub_nav'] ='';
		$data['title'] ='Dashboard';
		$data['email']=$data['password']='';
		if($this->input->cookie('admin_mpprep')){
			$admin_mpprep = json_decode($this->input->cookie('admin_mpprep'));
			$data['admin_email']=$admin_mpprep->admin_mpprep->email;
			$data['admin_password']=$admin_mpprep->admin_mpprep->password;
		}
		$this->loadLoginTemplate('auth/login',$data);

	}
	
	public function loginSubmit(){
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email');	 
		$this->form_validation->set_rules('password', 'Password', 'required');
		if($this->form_validation->run() == FALSE){
			$m = json_encode($this->form_validation->error_array());
			$res =array('status'=>0,'msg'=>$m);
			echo json_encode($res);die;
		}else{ 
		$recaptchaResponse = trim($this->input->post('g-recaptcha-response'));
		$status = $this->base_model->recaptcha_verification($recaptchaResponse);
		$check = $this->login_model->check_admin();
		if($check){
				$this->setAdminSession($check);
				if($this->input->post('remember')){	
				$cdata['admin_mpprep']['email'] =$this->input->post('email'); 
				$cdata['admin_mpprep']['password'] =$this->input->post('password'); 
				setcookie('admin_mpprep',json_encode($cdata), time() + (86400 * 10), "/");
                }
				$res =array('status'=>1);
				echo json_encode($res);die;
		}else{
			$m = json_encode(array('password'=>'Email Or Password Is Incorrect'));
			$res =array('status'=>0,'msg'=>$m);
			echo json_encode($res);die;
		}
	  }
	}
	
	
	public function forgot_password()
	{ 
		if($this->session->userdata('is_front_login')){ redirect(base_url());  }
		$data['nav'] ='dashboard';
		$data['sub_nav'] ='';
		$data['title'] ='Dashboard';
		$this->loadLoginTemplate('auth/forgot_password',$data);

	}
	
	public function forgot_submit()
	{ 
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
		if($this->form_validation->run() == FALSE){
			$m = json_encode($this->form_validation->error_array());
			$res =array('status'=>0,'msg'=>$m);
			echo json_encode($res);die;
		}else{ 
		$uinfo = $this->base_model->select_row('users',array('email'=>$this->input->post('email')));
		if($uinfo){
		$recaptchaResponse = trim($this->input->post('g-recaptcha-response'));
		$status = $this->base_model->recaptcha_verification($recaptchaResponse);
		if ($status['success']) {
          $code = $this->base_model->generate_password();
		  $this->base_model->update_data('users',array('id'=>$uinfo->id),array('code'=>$code));
			
			$res =array('status'=>1);
			echo json_encode($res);die;
		}else{
			$res =array('status'=>2);
			echo json_encode($res);die;
		}

		}else{
			$res =array('status'=>3);
			echo json_encode($res);die;
		}
		}

	}
	
	public function reset_password($code='')
	{ 
		if($code !=''){
		if($this->session->userdata('is_front_login')){ redirect(base_url());  }
		$en_code = $this->base_model->encryptor('decrypt',$code);
		$uinfo = $this->base_model->select_row('users',array('code'=>$en_code));
		if($uinfo){
			$data['nav'] ='resetpassword';
			$data['sub_nav'] ='';
			$data['title'] ='resetpassword';
			$data['uinfo']=$uinfo;
			$data['email']=$uinfo->email;
			$this->loadLoginTemplate('auth/reset_password',$data);
		}else{
			redirect(base_url('admin')); 
		}
		}else{
			redirect(base_url('admin')); 
		}

	}
	
	
	public function reset_submit()
	{ 
		$uinfo = $this->base_model->select_row('users',array('id'=>$this->input->post('id'),'email'=>$this->input->post('email')));
		//echo $str = $this->db->last_query(); die;
		if($uinfo){
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
		$this->form_validation->set_rules('new_pass', 'New Password', 'required');
		$this->form_validation->set_rules('confirm_pass', 'Confirm Password', 'required|matches[new_pass]');
		
		if($this->form_validation->run() == FALSE){
			$m = json_encode($this->form_validation->error_array());
			$res =array('status'=>0,'msg'=>$m);
			echo json_encode($res);die;
		}else{
		
		$recaptchaResponse = trim($this->input->post('g-recaptcha-response'));
		$status = $this->base_model->recaptcha_verification($recaptchaResponse);
		if ($status['success']) {
          
		     $udata =array(
				'password'=>md5($this->input->post('new_pass')),
				'code'=>'',
			);
			$this->base_model->update_data('user',array('id'=>$this->input->post('id')),$udata);

			
			$res =array('status'=>1);
			echo json_encode($res);die;
		}else{
			$res =array('status'=>2);
			echo json_encode($res);die;
		}

		
		}
		}else{
			$res =array('status'=>3);
			echo json_encode($res);die;
		}

	}
	
	public function logout(){
		$url = base_url().'admin';
		$this->session->sess_destroy();
        redirect($url);
	}

	public function valid_password($password = '')
	    {
	        $password = trim($password);
	        $regex_lowercase = '/[a-z]/';
	        $regex_uppercase = '/[A-Z]/';
	        $regex_number = '/[0-9]/';
	        $regex_special = '/[!@#$%^&*()\-_=+{};:,<.>§~]/';
	        if (empty($password))
	        {
	            $this->form_validation->set_message('valid_password', 'The {field} field is required.');
	            return FALSE;
	        }
	        if (preg_match_all($regex_lowercase, $password) < 1)
	        {
	            $this->form_validation->set_message('valid_password', 'The {field} field must be at least one lowercase letter.');
	            return FALSE;
	        }
	        if (preg_match_all($regex_uppercase, $password) < 1)
	        {
	            $this->form_validation->set_message('valid_password', 'The {field} field must be at least one uppercase letter.');
	            return FALSE;
	        }
	        if (preg_match_all($regex_number, $password) < 1)
	        {
	            $this->form_validation->set_message('valid_password', 'The {field} field must have at least one number.');
	            return FALSE;
	        }
	        if (preg_match_all($regex_special, $password) < 1)
	        {
	            $this->form_validation->set_message('valid_password', 'The {field} field must have at least one special character.' . ' ' . htmlentities('!@#$%^&*()\-_=+{};:,<.>§~'));
	            return FALSE;
	        }
	        if (strlen($password) < 5)
	        {
	            $this->form_validation->set_message('valid_password', 'The {field} field must be at least 5 characters in length.');
	            return FALSE;
	        }
	        if (strlen($password) > 32)
	        {
	            $this->form_validation->set_message('valid_password', 'The {field} field cannot exceed 32 characters in length.');
	            return FALSE;
	        }
	        return TRUE;
	}

	
}
