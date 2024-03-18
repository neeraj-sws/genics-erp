<?php if ( ! defined('BASEPATH')) exit('Noinsert_data direct script access allowed');
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Site_model extends CI_Model {
    
    public function __construct()
    {
        parent::__construct();
    }
    
	public function send_mail2($body,$to_email,$subject){
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
		$mail->Username   = "ravikmalisws@gmail.com";
		$mail->Password   = "bjxqvjnbjxsmtolt";
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
			return 'error';
			//return "Mailer Error: " . $mail->ErrorInfo;
		}else{
			return "success";
		}
	}		

	public function send_mail($body,$to_email,$subject){
	   
		// Load Composer's autoloader
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

	public function create_slug($string,$table){

		
		$field='slug';
		$key=NULL;$value=NULL;
		$t =& get_instance();
		$slug = url_title($string);
		
		$slug = strtolower($slug);
		$i = 0;
		$params = array ();
		$params[$field] = $slug;
		//echo $slug;die;
		if($key)$params["$key !="] = $value; 
		
		while ($this->db->where('slug',$slug)->get($table)->num_rows())
		{ 
		if (!preg_match ('/-{1}[0-9]+$/', $slug ))
		$slug .= '-' . ++$i;
		else
		$slug = preg_replace ('/[0-9]+$/', ++$i, $slug );

		$params [$field] = $slug;
		} 
		//echo $slug;die;
		return $slug; 
	}	

	public function not_same_id_create_slug($string,$table,$id){
		$field='slug';
		$key=NULL;$value=NULL;
		$t =& get_instance();
		$slug = url_title($string);
		
		$slug = strtolower($slug);
		$i = 0;
		$params = array ();
		$params[$field] = $slug;
		//echo $slug;die;
		if($key)$params["$key !="] = $value; 

		while ($this->db->where('slug',$slug)->where('id!=',$id)->get($table)->num_rows())
		{ 
		if (!preg_match ('/-{1}[0-9]+$/', $slug ))
		$slug .= '-' . ++$i;
		else
		$slug = preg_replace ('/[0-9]+$/', ++$i, $slug );

		$params [$field] = $slug;
			
		} 
		//echo $slug;die;
		return $slug; 
	}
	
}
?>