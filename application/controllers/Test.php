<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends Base_Controller {

    public function index(){

      
    
       // Load Composer's autoloader
		$from_email =  $this->config->item('front_email');

        // echo $from_email;die;
	    $from_name =$this->config->item('front_name');;
		$this->load->library('phpmailer');
		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->SMTPAuth   = true;
		$mail->SMTPDebug = 2;
		$mail->SMTPSecure = $this->config->item('front_SMTPSecure');
		$mail->Host       = $this->config->item('front_host');
		$mail->Port    = 	$this->config->item('front_port');
		$mail->IsHTML(true);
		$mail->Username   = $this->config->item('front_username');
		$mail->Password   = $this->config->item('front_password');
		$mail->From    = $from_email;
		$mail->FromName   = $from_name;
		$headers = "MIME-Version: 1.0\n";
		$headers .= "Content-Type: text/html; charset=\"iso-8859-1\"\n";
		$headers .="From: ".$from_email;
		$mail->Subject = "sdf";
		$mail->AddReplyTo($from_email);
		$mail->MsgHTML("test");
		$mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);

		$mail->AddAddress("swskhushbooverma@gmail.com");
		if(!$mail->Send()){
//             echo "error";die;
// 			return 'error';
			echo  "Mailer Error: " . $mail->ErrorInfo;die;
		}else{
            echo "success";die;
			return "success";
		}

    }
    
    

}
