<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends Base_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('Base_model');
		$this->load->library('session');
	}

	public function index()
	{ 
	
		$data[]="";
		$data['menu']='home';
		$this->load->view('front/header');
		$this->load->view('front/home');
		$this->load->view('front/footer');
	}
	
	
	public function send_mail(){
		$body = 'helloo test mail';
		$to_email = "khushboo@sohamsolution.com";
		$subject = 'this is test mail';
		
		$from_email = 'info@codelive.info';
	    $from_name ="Info";
		$this->load->library('phpmailer');
		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->SMTPAuth   = true;
		$mail->SMTPSecure = "tls";
		$mail->Host       = "smtp.gmail.com";
		$mail->Port    = 	"587";
		$mail->IsHTML(true);
		$mail->Username   = "shubhamsws444@gmail.com";
		$mail->Password   = "intdqtacyndcixja";
		$mail->From    = $from_email;
		$mail->FromName   = $from_name;
		$headers = "MIME-Version: 1.0\n";
		$headers .= "Content-Type: text/html; charset=\"iso-8859-1\"\n";
		$headers .="From: ".$from_email;
		$mail->Subject = $subject;
		$mail->AddReplyTo($from_email);
		$mail->MsgHTML($body);

		$mail->AddAddress("$to_email");
		if(!$mail->Send()){
			echo 'error';
			//return "Mailer Error: " . $mail->ErrorInfo;
		}else{
			echo "success";
		}
	}	
	
	
	public function privay_policy()
	{ 
		$data[]="";
		$data['menu']='Privay Policy';
		$this->load->view('front/header');
		$this->load->view('front/privay_policy');
		$this->load->view('front/footer');
	}
	
	public function tearm_condition()
	{ 
		$data[]="";
		$data['menu']='Tearm & Condition';
		$this->load->view('front/header');
		$this->load->view('front/tearm_condition');
		$this->load->view('front/footer');
	}

	public function about_us()
	{ 
		$data[]="";
		$data['menu']='About Us';
		$this->load->view('front/header');
		$this->load->view('front/about_us');
		$this->load->view('front/footer');
	}

	public function refund_policy()
	{ 
		$data[]="";
		$data['menu']='Refund Policy';
		$this->load->view('front/header');
		$this->load->view('front/refund_policy');
		$this->load->view('front/footer');
	}
	
	public function terms_and_conditions(){ 

		$data['heading'] = 'Terms And Conditions';
		$data['buttonheading'] = '';
		$this->loadUserTemplate('front/terms_and_conditions',$data);
	
	  }
	  public function privacy_policy(){ 

		$data['heading'] = 'Privacy Policy';
		$data['buttonheading'] = '';
		$this->loadUserTemplate('front/privacy_policy',$data);
	
	  }
	
	  public function about(){ 
	
		$data['heading'] = 'About';
		$data['buttonheading'] = '';
		$this->loadUserTemplate('front/about',$data);
	
	  }

	

	
}
