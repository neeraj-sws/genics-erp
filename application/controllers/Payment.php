<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Payment extends Base_Controller {
	
   public function __construct(){ 
	    parent::__construct(); 
	    $this->load->database();
		$this->load->model('base_model');
		date_default_timezone_set('Asia/Kolkata');
	}

	
	/*--Get All matches--*/
	public function return_webhook(){ 
			$data = $_POST;
			$mac_provided = $data['mac'];  // Get the MAC from the POST data
			$data =array(
					'res'=>json_decode($mac_provided),
				);
				$this->base_model->insert_data('tr_response',$data);
	}
	
	public function redirect(){ 
		echo json_encode($_GET);
	}
	
}