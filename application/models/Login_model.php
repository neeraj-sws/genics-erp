<?php if ( ! defined('BASEPATH')) exit('Noinsert_data direct script access allowed');
class Login_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

	 public function check_admin(){
		$email = trim($this->input->post('email'));
		$password = trim(sha1($this->input->post('password')));
		$where = array('email'=>$email,'password'=>$password,'status'=>1);
		//$user = $this->base_model->select_row('user',array('email'=>$email,'password'=>$password,'status'=>1));

		$columns="users.*,upload_images.file as profile";
		$joins[] = array('table'=>'upload_images','condition'=>'upload_images.id=users.file_id','jointype'=>'left');
		$user = $this->base_model->select_join_row('users',$where,$joins,$columns);
		// echo '<pre>'; print_r($user);;die;
		if($user){
			return $user;
		}else{
			return FALSE;
		}
	}

	public function check_user(){
		$otp = $this->input->post('otp');
	
		$where = array('otp'=>$otp,'status'=>1);
		//$user = $this->base_model->select_row('user',array('email'=>$email,'password'=>$password,'status'=>1));

		$columns="distributor.*,upload_images.file as profile";
		$joins[] = array('table'=>'upload_images','condition'=>'upload_images.id=distributor.file_id','jointype'=>'left');
		$user = $this->base_model->select_join_row('distributor',$where,$joins,$columns);
		// echo '<pre>'; print_r($user);;die;
		if($user){
			return $user;
		}else{
			return FALSE;
		}
	}
	
	 
	
}
