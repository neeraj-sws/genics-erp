<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends Base_Controller {

    public function __construct(){ 
		parent::__construct();
		$this->load->model('Base_model');
		$this->load->model('Site_model');
		 $this->load->model('login_model');
		$this->load->library('session');
	}
	
	
	 public function index()
	{ 
    //    echo "<pre>";print_r($this->session->userdata());die;
        if(!$this->session->userdata('is_user_login')){ redirect(base_url('order-login')); }
        $data['code'] = $this->base_model->generate_code(5);
        $data['party'] = $this->base_model->select_data('orders',array());
        $data['distributor_id'] = $this->session->userdata('id');
        $data['buttonheading'] = 'Order';
		
		$this->loadUserTemplate('front/order',$data);
		
	}
	 public function get_order_page()
	{ 
   
        if(!$this->session->userdata('is_user_login')){ redirect(base_url('order-login')); }
        $data['code'] = $this->base_model->generate_code(5);
        $data['party'] = $this->base_model->select_data('orders',array());
        $data['distributor_id'] = $this->session->userdata('id');
        $data['buttonheading'] = 'Order';
		
		$this->load->view('front/order_view',$data);
		
	}
	
    public function login()
	{ 
        if($this->session->userdata('is_user_login')){ redirect(base_url('/'));  }
        $data['code'] = $this->base_model->generate_code(5);
        $data['buttonheading'] = 'Login';
	    $this->loadUserTemplate('front/login',$data);
	}
    public function order_details($id)
	{   

       
        $orderid = base64_decode($id);
        // echo $orderid;die;
          $edata['order'] = $order =  $this->base_model->select_row('orders',array('id' => $orderid));
         $edata['distributor'] =  $this->base_model->select_row('distributor',array('id' => $order->distributor_id));
      
                
        $edata['items'] = $items = $this->base_model->select_data('order_item',array('order_code' => $order->code));
        $where = array('order_code' => $order->code);
        $joins = array();
        $columns = "SUM(price) as total_price";
        $edata['totalprice'] = $totalprice = $this->base_model->select_join_row('order_item',$where,$joins,$columns);
        $columns1 = "SUM(quantity) as total_qty";
        $edata['totalqty'] = $totalqty= $this->base_model->select_join_row('order_item',$where,$joins,$columns1);
        
        	 $qty=0;
                foreach($items as $item){
                $total = $item->price * $item->quantity;
               
                $qty +=$total; 
               
                }
                	$edata['total'] =  $qty;
      
        $this->load->view('email_template/order',$edata);
		
	}

   
   
 public function get_otp(){

        
       $phone= $this->input->post('phone');
      $seller_data= $this->base_model->select_row('distributor',array('phone' => $phone));
    //   $status=;
      
      if(!$seller_data){
       
        $res =array('status'=>0,'msg'=>'Phone Number Is Not Valid');
        echo json_encode($res);die;
        }
      
        else{

            if($seller_data->status==0){
                $res =array('status'=>1,'msg'=>'Your Account is not verify ');
                echo json_encode($res);die;
               } 
               else{

             
        $seller_name= $seller_data->full_name;
        $seller_id= $seller_data->id;
    //   echo "<pre>";print_r($seller_id);die;
        $apiKey = '3231656e69637335353554';
        $senderId = 'GENICS';
        $tempIdOtp = '1707168775407868692';
        $tempIdOrder= '1707168775448006324';
        $mobile_number =  $phone;
        $countryCode  = '91';
        $route  = '2';
        $otp = strval(random_int(1000, 9999));

       

        $message_content = 'Dear '. $seller_name.'\nYour OTP is '. $otp.' for login in Genics Techsol Pvt. Ltd. This OTP is valid for next 10 minutes.\n\nGenics Team'; // order
        

        $url ="http://control.yourbulksms.com/api/sendhttp.php?authkey=$apiKey&mobiles=$mobile_number&sender=$senderId&route=$route&country=$countryCode&DLT_TE_ID=$tempIdOtp&otp=$otp&message=".urlencode($message_content);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
      
     
        $res= json_decode($response);
       
        if($res->Status=="Success"){
            $oid = $this->Base_model->update_data('distributor',array('phone' => $phone), array('otp'=>$otp));
            
            $data['code'] = $this->input->post('code');
            $view = $this->load->view('front/otp_password',$data,TRUE);

            $res =array('status'=>'Success','id' =>$seller_id,'view'=>$view ,'msg'=>'OTP send successfully');
            echo json_encode($res);die;
         }
        }
        // 
        // echo "<pre>";print_r($response);die;
        }
    }  
    public function otp_resend(){
    //   echo"s";die;
        $seller_data = $this->base_model->select_row('distributor',array('id' => $this->input->post('id')));
         $seller_phone= $seller_data->phone;

        $seller_name= $seller_data->full_name;
        $seller_id= $seller_data->id;
    //   echo "<pre>";print_r($seller_id);die;
        $apiKey = '3231656e69637335353554';
        $senderId = 'GENICS';
        $tempIdOtp = '1707168775407868692';
        $tempIdOrder= '1707168775448006324';
        $mobile_number =  $seller_phone;
        $countryCode  = '91';
        $route  = '2';
        $otp = strval(random_int(1000, 9999));

       

            $message_content = 'Dear '. $seller_name.'\nYour OTP is '. $otp.' for login in Genics Techsol Pvt. Ltd. This OTP is valid for next 10 minutes.

Genics Team'; // order
       

        $url ="http://control.yourbulksms.com/api/sendhttp.php?authkey=$apiKey&mobiles=$mobile_number&sender=$senderId&route=$route&country=$countryCode&DLT_TE_ID=$tempIdOtp&otp=$otp&message=".urlencode($message_content);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
      
     
        $res= json_decode($response);
       
        if($res->Status=="Success"){
            $oid = $this->Base_model->update_data('distributor',array('phone' => $seller_phone), array('otp'=>$otp));
            
            $res =array('status'=>1,'msg'=>'OTP resend successfully');
            echo json_encode($res);die;
           
       }
         }
   
    

    public function otp_verify()
	{  
        // echo"s";die;
       $otp= $this->input->post('otp');
       $seller= $this->input->post('seller_id');
    //    echo $seller ;die;
        $uinfo = $this->base_model->select_row('distributor',array('otp'=> $this->input->post('otp'),'id'=>$this->input->post('seller_id')));
        $check = $this->login_model->check_user();
        if($check){
        	$this->setUserSession($check);
            $res =array('status'=>1);
            echo json_encode($res);die;
        }
        else{
             $res =array('status'=>0,'msg'=>'OTP Is Not Valid');
             echo json_encode($res);die;
        }
    }
    
  

    public function addrow(){
        $data['code'] = $this->input->post('code');
        $this->load->view('front/addrow',$data);
    }
    public function editrow(){
        $id = $this->input->post('id');       
        $data['item'] = $this->base_model->select_row('order_item',array('id' => $id));        
        $this->load->view('front/editrow',$data);
    }
    
    public function Rowdata(){
        $data['code'] = $code = $this->input->post('code');
        $data['items'] = $this->base_model->select_data('order_item',array('order_code' => $code));
            // echo '<pre>'; print_r($data['items']);die;
        $this->load->view('front/rowdata',$data);
    }

    public function Orderdata(){
        $data['code'] = $code = $this->input->post('code');
        $data['items'] = $this->base_model->select_data('order_item',array('order_code' => $code));
            // echo '<pre>'; print_r($data['items']);die;
        $this->load->view('front/orderdata',$data);
    }

    public function item_save()
	{
    
        $this->form_validation->set_rules('item_name', 'Item Name', 'required');
        $this->form_validation->set_rules('quantity', 'Quantity', 'required');
        $this->form_validation->set_rules('price', 'Price', 'required');

            if($this->form_validation->run() == FALSE){
                $m = json_encode($this->form_validation->error_array());
                $res =array('status'=>0,'msg'=>$m);
                echo json_encode($res);die;
            }else{ 
                $data = array(
                    "order_code" => $this->input->post('code'),
                    "item_name" => $this->input->post('item_name'),
                    "quantity" => $this->input->post('quantity'),
                    "price" => $this->input->post('price'),
                    "total_price"=>$this->input->post('quantity')*$this->input->post('price'),
                    "status" => 1,
                );
                $uid = $this->Base_model->insert_data('order_item',$data);
                $res =array('status'=>1);
                echo json_encode($res);die;
            }
    }
    public function item_update()
	{
   
        $this->form_validation->set_rules('item_name', 'Item Name', 'required');
        $this->form_validation->set_rules('quantity', 'Quantity', 'required');
        $this->form_validation->set_rules('price', 'Price', 'required');

            if($this->form_validation->run() == FALSE){
                $m = json_encode($this->form_validation->error_array());
                $res =array('status'=>0,'msg'=>$m);
                echo json_encode($res);die;
            }else{ 
                $data = array(
                    "item_name" => $this->input->post('item_name'),
                    "quantity" => $this->input->post('quantity'),
                    "price" => $this->input->post('price'),
                    "status" => 1,
                );
                $this->base_model->update_data('order_item',array('id'=>$this->input->post('id')),$data);
               $view=   $this->load->view('front/addrow');
                $res =array('status'=>1 ,'view'=>$view);
                echo json_encode($res);die;
            }
    }
  
   
    public function order_save()
	{

			$this->form_validation->set_rules('party_name', 'Part Name', 'required');
			$this->form_validation->set_rules('payment_term', 'PAYMENT TERMS', 'required');
			$this->form_validation->set_rules('phone', 'PHONE NUMBER ', 'required');
          
            $this->form_validation->set_rules('city', 'City', 'required');
        
            if($this->form_validation->run() == FALSE){
                $m = json_encode($this->form_validation->error_array());
                $res =array('status'=>0,'msg'=>$m);
                echo json_encode($res);die;
            }else{ 
                $qty=0;
                  $items= $this->base_model->select_data('order_item',array('order_code' => $this->input->post('code')));
                  if(!$items){
                      $res =array('status'=>3,'msg' => 'At least one item added in order.');
                      echo json_encode($res);die;
                  }
                  $category = $this->base_model->select_row('category',array('title'=>'third_party'));
                  $distributor= $this->base_model->select_row('distributor',array('id' => $this->input->post('distributor_id'),'category' => $category->id));
                  if($distributor){
                   $third_party = $distributor->id;
                  }else{
                   $third_party = 0;
                  }

                  $amount = 0;
                  foreach($items as $item) {
                    $total = ($item->price * $item->quantity);
                    $amount +=$total; }
            
                $orderdata = array(
                    'party_name' => $this->input->post('party_name'),
                    'city' => $this->input->post('city'),
                    'payment_term' => $this->input->post('payment_term'),
                    'dispached' => $this->input->post('dispached'),
                    'code' => $this->input->post('code'),
                    'distributor_id' => $this->input->post('distributor_id'),
                    'user_id' => $this->input->post('distributor_id'),
                    'number' => $this->input->post('phone'),
                    'amount' => $amount,
                    'is_new' => 1,
                    'created_at' => date('Y/m/d'),
                    'update_at' => date('Y/m/d'),
                    "status" => 0,
                );
                // echo"<pre>";print_r($orderdata);die;
                $oid = $this->Base_model->insert_data('orders',$orderdata);
                
                $name=$this->input->post('party_name');
                $party_name =  $this->base_model->select_row('users',array('full_name' => $name));
                if(!$party_name){
                $userdata = array(
                    'full_name' => $this->input->post('party_name'),
                    'phone' => $this->input->post('phone'),
                    'created_at' => date('Y/m/d'),
                    'updated_at' => date('Y/m/d')
                   
                );
                // echo"<pre>";print_r($orderdata);die;
                $userid = $this->Base_model->insert_data('users',$userdata);
                $user_id =$userid ;
            }else{
                $userdata = array(
                    'phone' => $this->input->post('phone'),
                    'updated_at' => date('Y/m/d')
                );
                     $userid =    $this->base_model->update_data('users',array('id'=>$party_name->id),$userdata);
                      $user_id = $party_name->id;
            }
            
             $orderdata = array(
                    'user_id' => $user_id,
                    'update_at' => date('Y/m/d')
                );
                     $userid =    $this->base_model->update_data('orders',array('id'=>$oid),$orderdata);
                // $data = array(
                //     "order_code" => $this->input->post('code'),
                   
                //     'created_at' => date('Y/m/d'),
                //     'update_at' => date('Y/m/d'),
                //     "status" => 1,
                // );      
                // // echo"<pre>";print_r($data);die;
                // $uid = $this->Base_model->insert_data('order_item',$data);

               
                $orderid = base64_encode($oid);
                           
                $order =  $this->base_model->select_row('orders',array('id' => $oid));
                $items= $this->base_model->select_data('order_item',array('order_code' => $order->code));
               
               
                foreach($items as $item){
                $total = $item->price * $item->quantity;
               
                $qty +=$total; 
               
                }
              
        
                $seller_data = $this->base_model->select_row('distributor',array('id' => $this->input->post('distributor_id')));
            
                $name=$this->input->post('party_name');
                $party_name =  $this->base_model->select_row('users',array('full_name' => $name));
                $seller_phone= $party_name->phone;
               $apiKey = '3231656e69637335353554';
               $senderId = 'GENICS';
               $tempIdOtp = '1707168775407868692';
               $tempIdOrder= '1707168775448006324';
               $mobile_number =  $seller_phone;
               $countryCode  = '91';
               $route  = '2';
              
            $base= base_url("order_details/$orderid");
            $link = $base;   
  
            $id = substr(str_shuffle("ASDFGHJKLZXCVBNMQWERTYUIOP0123456789asdfghjklzxcvbnmqwertyuiop"), 0, 6);
            $txt_path = "txt-db/"; 
            
            
            while (file_exists($txt_path . $id . ".txt")) {
                $id = substr(str_shuffle("ASDFGHJKLZXCVBNMQWERTYUIOP0123456789asdfghjklzxcvbnmqwertyuiop"), 0, 6);             }
            
            $this->load->helper('file');
            $file_path = $txt_path . $id . ".txt";
            $create_txt_file = write_file($file_path, $link); 
           
            if ($create_txt_file) {
                $website = base_url(); 
                    $order= 'detail';
                    $short_link = $website .$order.'/'.$orderid; 
               
                $message_content = 'Hi, 
Your order has been placed to Genics Techsol Pvt Ltd and order amount is Rs. '.$qty.' to review order : '. $short_link.'
                
Genics Team';   
            } 
            $url="";
            // $url ="http://control.yourbulksms.com/api/sendhttp.php?authkey=$apiKey&mobiles=$mobile_number&sender=$senderId&route=$route&country=$countryCode&DLT_TE_ID=$tempIdOrder&message=".urlencode($message_content);
       
            //    echo"<pre>";print_r($url);die;

               $ch = curl_init($url);
               curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
               $response = curl_exec($ch);
               curl_close($ch);

// echo $ch;die;
          





                $edata['order'] = $order =  $this->base_model->select_row('orders',array('id' => $oid));
                $edata['distributor'] =  $this->base_model->select_row('distributor',array('id' => $order->distributor_id));
                
                $edata['items'] =  $this->base_model->select_data('order_item',array('order_code' => $order->code));
                $where = array('order_code' => $order->code);
                $joins = array();
                $columns = "SUM(price) as total_price";
                $edata['totalprice'] =  $this->base_model->select_join_row('order_item',$where,$joins,$columns);
                $columns1 = "SUM(quantity) as total_qty";
                $edata['totalqty'] =  $this->base_model->select_join_row('order_item',$where,$joins,$columns1);
               
		        $subject = 'GENICS ORDER :'.strtoupper($order->party_name).'-'.date('d/m/Y',strtotime($order->created_at));

                $body = $this->load->view('email_template/order',$edata,true);
                // return $body;
                $receipts = $this->base_model->select_data('receipt',array('status' =>1));
                $site_setting = $this->base_model->select_row('site_setting',array());
                
                // echo '<pre>'; print_r($site_setting);die;
                if($site_setting->send_email  == 1){
                foreach($receipts as $receipt){
                    $to_email = $receipt->email;
                   
                    // $this->send_mail($body,$to_email,$subject);
                }        
                $user = $this->base_model->select_row('users',array('full_name'=>$this->input->post('party_name'),'phone'=>$this->input->post('phone'),'id !='=>$user_id));
               
                    $distribute_mail = $this->session->userdata('email');
                    // $this->send_mail($body,$distribute_mail,$subject); 

                    $user = $this->base_model->select_row('users',array('full_name'=>$this->input->post('party_name'),'phone'=>$this->input->post('phone'),'id !='=>$user_id));

                    if($user && $user->distributor_id !== $this->session->userdata('id')){ 
                    $distributor = $this->base_model->select_row('distributor',array('id'=>$user->distributor_id));

                    $distribute_mail =$distributor->email;
                    // $this->send_mail($body,$distribute_mail,$subject); 
                    }    
                   
                
            }
           
            $orderid = base64_encode($oid);
            //    echo $orderid;die;   
                    $res =array('status'=>1,'orderid'=>$orderid);
                    // echo "<pre>";print_r($res);die;
                    echo json_encode($res);die;
           
    
            }          
	    
	}

    function encode($string, $key = "", $url_safe = TRUE) {
        $ret = parent::encode($string, $key);
  
        if ($url_safe) {
            $ret = strtr($ret, array('+' => '.', '=' => '-', '/' => '~'));
        }
  
        return $ret;
    }
    public function removeRow()
    {
        $this->base_model->delete_data('order_item',array('id'=>$this->input->post('id')));
		$res =array('status'=>1);
		echo json_encode($res);die;
    }
    public function send_mail($body,$to_email,$subject){
	   
		// Load Composer's autoloader
		$from_email =  $this->config->item('front_email');

       
	    $from_name =$this->config->item('front_name');
      
		$this->load->library('phpmailer');
		$mail = new PHPMailer();
		$mail->IsSMTP();
       
		$mail->SMTPAuth   = true;
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
		$mail->Subject = $subject;
		$mail->AddReplyTo($from_email);
		$mail->MsgHTML($body);

		$mail->AddAddress($to_email);

        
		if(!$mail->Send()){
			return 'error';
			//return "Mailer Error: " . $mail->ErrorInfo;
		}else{
			return "success";
		}
	}


    public function party_function(){
        
        $name= $this->input->post('name');
        $where_like = array('users.full_name'=>$name);
        $data['party'] = $this->base_model->select_join_result('users',array('role !='=>1),'','',$where_like);
        $this->load->view('front/partyView',$data);
    }

    public function party_click(){

        $id=$this->input->post('id');
        $party_name =  $this->base_model->select_row('users',array('id'=>$id));
        $view='<div class="form-group mb-3">
            <label for="phone">Phone Number :</label>
            <input type="text" name="phone" id="phone" class="form-control allow_numeric" value="'.@$party_name->phone.'">
        // </div>';
        $res =array('status'=>1 , 'view'=>$view);
        echo json_encode($res);die;

    }
}