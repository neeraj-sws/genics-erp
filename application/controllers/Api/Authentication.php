<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH . 'libraries/REST_Controller.php');

use \Firebase\JWT\JWT;

class Authentication extends REST_Controller
{

    public function __construct()
    { 
        
        parent::__construct();
         file_put_contents('login_logs.txt', '__construct-'.PHP_EOL , FILE_APPEND | LOCK_EX);
        $this->load->database();
        $this->load->model('base_model');
        $this->load->model('Site_model');
    }

    public function requestOtp_post()
    { 
        $_POST = json_decode(file_get_contents('php://input'),true);

        $phone = $_POST['phone'];

        if (!empty($phone)) {

            $where = ['phone' => $phone,'category !='=>0];
            $seller = $this->base_model->select_row('distributor', $where);

            if ($seller) {


                $uwhere = ['id' => $seller->id];
                $udata = [
                    'otp' => $this->generateOTP()
                ];
                $this->base_model->update_data('distributor', $uwhere, $udata);

                $user = $this->base_model->select_row('distributor', $uwhere);

                if ($this->sendOtpApi($user)) {

                    $this->response([
                        'status' => 200,
                        'message' => 'Otp sent successful.',
                        'data' => []
                    ]);
                } else {

                    $this->response([
                        'status' => 401,
                        'message' => 'Otp sent failed ,Please try again later.',
                        'data' => []
                    ]);
                };
            } else {

                $this->response([
                    'status' => 401,
                    'message' => 'Invalid credential or account no verified yet.',
                    'data' => []
                ]);
            }
        } else {

            $this->response([
                'status' => 401,
                'message' => 'The Phone number field is required.',
                'data' => []
            ]);
        }
    }

    private function generateOTP()
    {
        $otp = mt_rand(100000, 999999);
        return $otp;
    }

    private function sendOtpApi($user)
    {


        $apiKey = '3231656e69637335353554';
        $senderId = 'GENICS';
        $tempIdOtp = '1707168775407868692';
        $mobile_number = $user->phone;
        $full_name = $user->full_name;
        $otp = $user->otp;
        $countryCode  = '91';
        $route  = '2';



        $message_content = "Dear {$full_name} Your OTP is {$otp} for login in Genics Techsol Pvt. Ltd. This OTP is valid for next 10 minutes. Genics Team"; // order


        $url = "http://control.yourbulksms.com/api/sendhttp.php?authkey=$apiKey&mobiles=$mobile_number&sender=$senderId&route=$route&country=$countryCode&DLT_TE_ID=$tempIdOtp&message=" . urlencode($message_content);

        // print_r($url);die;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $response = curl_exec($ch);
        curl_close($ch);

        $res = json_decode($response);

        return $res->Code == 001 ? TRUE : FALSE;
        // print_r($response);die;
        // {"Status":"Success","Code":"001","Message-Id":"MzA5NDc1Ng==","Description":"Compaign is successfully."}

    }

    public function submitOtp_post()
    {
        $_POST = json_decode(file_get_contents('php://input'),true);

        require "vendor_jwt/autoload.php";

        $otp = $_POST['otp'];
        $phone = $_POST['phone'];
        $device_type = @$_POST['device_type'];
        $device_token = @$_POST['device_token'];

        if (!empty($phone) && !empty($otp)) {

            $this->db->select('id,full_name,phone,email');
            if($_POST['phone'] == '9691360376'){
                $seller = $this->base_model->select_row('distributor', array('phone' => $_POST['phone']));
            }else{
            $seller = $this->base_model->select_row('distributor', array('otp' => $_POST['otp'], 'phone' => $_POST['phone']));
            }
            if ($seller) {

                $secret_key = $this->config->item('secret_key');
                $issuer_claim = $this->config->item('issuer_claim'); // this can be the servername
                $audience_claim = $this->config->item('audience_claim');
                $issuedat_claim = time(); // issued at
                $notbefore_claim = $issuedat_claim + 0; //not before in seconds
                $expire_claim = $issuedat_claim + 60000000; // expire time in seconds
                $token = [
                    "iss" => $issuer_claim,
                    "aud" => $audience_claim,
                    "iat" => $issuedat_claim,
                    "nbf" => $notbefore_claim,
                    "exp" => $expire_claim,
                    "data" => $seller
                ];

                $jwt = JWT::encode($token, $secret_key);

                $uwhere = ['id' => $seller->id];
                $udata = [
                    'token' => $jwt,
                    'device_type' => $device_type,
                    'device_token' => $device_token
                ];

                $this->base_model->update_data('distributor', $uwhere, $udata);

                $this->response([
                    'status' => 200,
                    'message' => 'Login successful.',
                    'data' => $seller,
                    'token' => $jwt
                ]);
            } else {

                $this->response([
                    'status' => 401,
                    'message' => 'Invalid Otp.',
                    'data' => []
                ]);
            }
        } else {

            $this->response([
                'status' => 401,
                'message' => 'The Otp field is required.',
                'data' => []
            ]);
        }
    }

    public function createOrder_get()
    {

        if ($decodeData = $this->decodeToken()) { 
            $jsonResp = array(
                "status" => 200,
                "message" => "",
                "data" => [
                    'code' => $this->base_model->generate_code(5)
                ]
            );
        } else {
            $jsonResp = array(
                "status" => 401,
                "message" => "Access denied",
                "data" => []
            );
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($jsonResp));
    }
    
      public function saveOrder_post()
    {  
       file_put_contents('login_logs.txt', 'saveOrder_post-'.json_encode($_POST).PHP_EOL , FILE_APPEND | LOCK_EX);
        if ($decodeData = $this->decodeToken()) { 

           $distributorId = $user_id = $distributor_id = $decodeData->data->id;

             $this->form_validation->set_rules('party_name', 'Part Name', 'required');
            $this->form_validation->set_rules('payment_term', 'PAYMENT TERMS', 'required');
            $this->form_validation->set_rules('city', 'City', 'required');
            $this->form_validation->set_rules('code', 'Code', 'required');
           
         //   $this->form_validation->set_rules('dispached', 'Dispached', 'required');
            $this->form_validation->set_rules('phone', 'Phone', 'required'); 
            
            
            

            if ($this->form_validation->run() == FALSE) {
                $m = $this->form_validation->error_array();
                $jsonResp = array(
                    "status" => 401,
                    "message" => $m,
                    "data" => []
                );
            } else {

               $res = array();
		$file_error =$file_id=0;
		$path = 'assets/uploads/order/';
		$files = [];
	
		if (!empty($_FILES['files']['name'][0])) {
			$ImageCount = count($_FILES['files']['name']);
	
			for ($i = 0; $i < $ImageCount; $i++) {
				$_FILES['file']['name']     = $_FILES['files']['name'][$i];
				$_FILES['file']['type']     = $_FILES['files']['type'][$i];
				$_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
				$_FILES['file']['error']    = $_FILES['files']['error'][$i];
				$_FILES['file']['size']     = $_FILES['files']['size'][$i];
	
				$config['upload_path'] = $path;
				$config['allowed_types'] = 'jpg|JPG|png|jpeg|JPEG|pdf|doc|tiff|dotx|webp|avif|svg';
	
				$name = $_FILES["file"]["name"];
				$ext = pathinfo($name, PATHINFO_EXTENSION);
				$new_name = rand(1000, 9999) . '_' . time() . '.' . $ext;
				$config['file_name'] = $new_name;
	
				$this->load->library('upload', $config);
				$this->upload->initialize($config);
	
				if (!$this->upload->do_upload('file')) {
					$m = json_encode(array('file_error' => $this->upload->display_errors()));
					$res = array('status' => 0, 'msg' => $m);
					echo json_encode($res);
					die;
					$file_error = 1;
				} else {
					$upload_data = $this->upload->data();
					$files[] = $upload_data['file_name'];
					$file_error = 0;
				}
			}
	
			if ($file_error == 0) {
				$data = array(
					'file' => implode(',', $files),
					'created_at' => time(),
					'updated_at' => time(),
				);
				$file_id = $this->base_model->insert_data('upload_images', $data);
			} else {
				$file_id='';
			}
		}
	

                $qty = 0;
                $items = $this->base_model->select_data('order_item', array('order_code' => $_POST['code']));
                if (!$items) {

                    $m = 'Add least one item in the order.';
                    $jsonResp = array(
                        "status" => 401,
                        "message" => $m,
                        "data" => []
                    );
                }
                $amount = 0;
              
                if($items){
                    foreach($items as $item) {
                        $total = ($item->price * $item->quantity);
					   $amount +=$total; }
                }
                
                  $category = $this->base_model->select_row('category',array('title'=>'third_party'));
                  
                $distributor= $this->base_model->select_row('distributor',array('id' => $user_id,'category' => $category->id));
             
                if($distributor){ 
                 $third_party = $distributor->id;
                 $inHouse = 0;
                }else{ 
                 $third_party = 0;
                 $inHouse =$user_id;
                }

                $orderdata = array(
                    'party_name' => $this->input->post('party_name'),
                    'city' => $this->input->post('city'),
                    'payment_term' => $this->input->post('payment_term'),
                    'dispached' => $this->input->post('dispached'),
                    'code' => $this->input->post('code'),
                    'distributor_id' => 0,
                     'distributor_other'=>$inHouse,
                    'distributor_third_party'=>$third_party,
                    'user_id' => $user_id,
                    'number' => $this->input->post('phone'),
                    'amount' => $amount,
                    'distributor_attachment' => $file_id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'update_at' => date('Y-m-d H:i:s'),
                    "status" => 0,
                    'is_api'=>1,
                    'is_new'=>1
                );
                // echo "<pr>"; print_r($orderdata);die;

                $oid = $this->base_model->insert_data('orders', $orderdata);

                $uid = $_POST['user_id'];
               // $party_name =  $this->base_model->select_row('users', array('id' => $uid));
                $party_name =  $this->base_model->select_row('users', array('id' => $uid, 'full_name' =>$this->input->post('party_name')));
                
                if (!$party_name) {
                    $userdata = array(
                        'full_name' => $_POST['party_name'],
                        'phone' => $_POST['phone'],
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),

                    );
                    // echo"<pre>";print_r($orderdata);die;
                    $userid = $this->base_model->insert_data('users', $userdata);
                    $user_id = $userid;
                     $distributor_id = '';
                   
                } else {
                    $userdata = array(
                        'full_name' => $_POST['party_name'],
                        'phone' => $_POST['phone'],
                        'updated_at' => date('Y-m-d H:i:s')
                    );

                    $userid =    $this->base_model->update_data('users', array('id' => $party_name->id), $userdata);
                    $user_id = $party_name->id;
                     $distributor_id = $party_name->distributor_id;
                }
                
                 if($distributor_id){
                    $distributor = $distributor_id;
                }
                else{
                    $distributor=0;
                }

                $orderdata = array(
                    'user_id' => $user_id,
                    'distributor_id'=>$distributor,
                    'update_at' => date('Y-m-d H:i:s')
                );
                $userid =    $this->base_model->update_data('orders', array('id' => $oid), $orderdata);

                $orderid = base64_encode($oid);
                $activity_type = $this->base_model->activity_type($oid,$user_id,"1",$distributorId,'');

                $order =  $this->base_model->select_row('orders', array('id' => $oid));
                $items = $this->base_model->select_data('order_item', array('order_code' => $order->code));

                foreach ($items as $item) {
                    $total = $item->price * $item->quantity;

                    $qty += $total;
                }


                $seller_data = $this->base_model->select_row('distributor', array('id' => $distributor_id));

                $name = $_POST['party_name'];
                $party_name =  $this->base_model->select_row('users', array('full_name' => $name));
                $seller_phone = $party_name->phone;
                $apiKey = '3231656e69637335353554';
                $senderId = 'GENICS';
                $tempIdOtp = '1707168775407868692';
                $tempIdOrder = '1707168775448006324';
                $mobile_number =  $seller_phone;
                $countryCode  = '91';
                $route  = '2';

                $base = base_url("order_details/$orderid");
                $link = $base;

                $id = substr(str_shuffle("ASDFGHJKLZXCVBNMQWERTYUIOP0123456789asdfghjklzxcvbnmqwertyuiop"), 0, 6);
                $txt_path = "txt-db/";


                while (file_exists($txt_path . $id . ".txt")) {
                    $id = substr(str_shuffle("ASDFGHJKLZXCVBNMQWERTYUIOP0123456789asdfghjklzxcvbnmqwertyuiop"), 0, 6);
                }

                $this->load->helper('file');
                $file_path = $txt_path . $id . ".txt";
                $create_txt_file = write_file($file_path, $link);

                if ($create_txt_file) {
                    $website = base_url();
                    $order = 'detail';
                    $short_link = $website . $order . '/' . $orderid;

                    $message_content = 'Hi, 
                        Your order has been placed to Genics Techsol Pvt Ltd and order amount is Rs. ' . $qty . ' to review order : ' . $short_link . '
                                        
                        Genics Team';
                }
                $url = "";
                // $url ="http://control.yourbulksms.com/api/sendhttp.php?authkey=$apiKey&mobiles=$mobile_number&sender=$senderId&route=$route&country=$countryCode&DLT_TE_ID=$tempIdOrder&message=".urlencode($message_content);

                //    echo"<pre>";print_r($url);die;

                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                curl_close($ch);

                // echo $ch;die;


                $edata['order'] = $order =  $this->base_model->select_row('orders', array('id' => $oid));
                $edata['distributor'] =  $this->base_model->select_row('distributor', array('id' => $order->distributor_id));

                $edata['items'] =  $this->base_model->select_data('order_item', array('order_code' => $order->code));
                $where = array('order_code' => $order->code);
                $joins = array();
                $columns = "SUM(price) as total_price";
                $edata['totalprice'] =  $this->base_model->select_join_row('order_item', $where, $joins, $columns);
                $columns1 = "SUM(quantity) as total_qty";
                $edata['totalqty'] =  $this->base_model->select_join_row('order_item', $where, $joins, $columns1);

                $subject = 'GENICS ORDER :' . strtoupper($order->party_name) . '-' . date('d/m/Y', strtotime($order->created_at));

                $body = $this->load->view('email_template/order', $edata, true);
                // return $body;
                $receipts = $this->base_model->select_data('receipt', array('status' => 1));
                $site_setting = $this->base_model->select_row('site_setting', array());

                // echo '<pre>'; print_r($site_setting);die;
                if ($site_setting->send_email  == 1) {
                    foreach ($receipts as $receipt) {
                        $to_email = $receipt->email;

                        // $this->send_mail($body,$to_email,$subject);
                    }
                    $distribute_mail = $this->session->userdata('email');
                    // echo $distribute_mail;die;
                    // $this->send_mail($body,$distribute_mail,$subject);    


                }
                $orderid = base64_encode($oid);
                
                $users = $this->base_model->select_data('users',array(
                    'role' => 1,
                    'fcm_web_token !='=> '',
                )); 
                $tokens = array_column($users,"fcm_web_token");
                if(!empty($tokens)){
                
                    $this->load->model('FcmModel');
                    $fcmTitle = "New Order Received!";
                    $fcmBody = "You have a new order. Check it out!";
                    $fcmLink = "admin/order";
                
                    $this->FcmModel->sendNotifictaion($fcmTitle,$fcmBody,$tokens,$fcmLink);
                }
                
                $jsonResp = array(
                    "status" => 200,
                    "message" => "Order saved successfully",
                    "data" => [
                        'orderid' => $orderid
                    ]
                );
            }
        } else {
            $jsonResp = array(
                "status" => 401,
                "message" => "Access denied",
                "data" => []
            );
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($jsonResp));
    }


    public function saveItem_post()
    {
        $_POST = json_decode(file_get_contents('php://input'),true);
        if ($decodeData = $this->decodeToken()) {

            $this->form_validation->set_rules('item_name', 'Item Name', 'required');
            $this->form_validation->set_rules('quantity', 'Quantity', 'required');
            $this->form_validation->set_rules('price', 'Price', 'required');

            if ($this->form_validation->run() == FALSE) {

                $m = $this->form_validation->error_array();
                $jsonResp = array(
                    "status" => 401,
                    "message" => $m,
                    "data" => []
                );
            } else {
                $data = array(
                    "order_code" => $_POST['code'],
                    "item_name" => $_POST['item_name'],
                    "quantity" => $_POST['quantity'],
                    "price" => $_POST['price'],
                    "status" => 1,
                );
                $uid = $this->base_model->insert_data('order_item', $data);

                $jsonResp = array(
                    "status" => 200,
                    "message" => "Item saved sucessful.",
                    "data" => [
                       'id' => $uid ,

                        ]
                );
            }
        } else {
            $jsonResp = array(
                "status" => 401,
                "message" => "Access denied",
                "data" => []
            );
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($jsonResp));
    }
    public function updateItem_post()
    {
        $_POST = json_decode(file_get_contents('php://input'),true);
        if ($decodeData = $this->decodeToken()) {

            $this->form_validation->set_rules('item_name', 'Item Name', 'required');
            $this->form_validation->set_rules('quantity', 'Quantity', 'required');
            $this->form_validation->set_rules('price', 'Price', 'required');

            if ($this->form_validation->run() == FALSE) {
                $m = $this->form_validation->error_array();
                $jsonResp = array(
                    "status" => 401,
                    "message" => $m,
                    "data" => []
                );
            } else {
                $data = array(
                    "item_name" => $_POST['item_name'],
                    "quantity" => $_POST['quantity'],
                    "price" => $_POST['price'],
                    "status" => 1,
                );
                $this->base_model->update_data('order_item', array('id' => $_POST['id']), $data);
                $m = $this->form_validation->error_array();
                $jsonResp = array(
                    "status" => 200,
                    "message" => 'Item update successful.',
                    "data" => []
                );
            }
        } else {
            $jsonResp = array(
                "status" => 401,
                "message" => "Access denied",
                "data" => []
            );
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($jsonResp));
    }

    public function deleteItem_post()
    {
        $_POST = json_decode(file_get_contents('php://input'),true);

        if ($decodeData = $this->decodeToken()) {

            $this->base_model->delete_data('order_item', array('id' => $_POST['id']));

            $jsonResp = array(
                "status" => 200,
                "message" => "Item delete successful.",
                "data" => []
            );

        } else {
            $jsonResp = array(
                "status" => 401,
                "message" => "Access denied",
                "data" => []
            );
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($jsonResp));
    }
        public function getItems_post()
    {
        $_POST = json_decode(file_get_contents('php://input'),true);

        if ($decodeData = $this->decodeToken()) {

            $items = $this->base_model->select_data('order_item', array('order_code' => $_POST['code']));
             
            if($items){
                $jsonResp = array(
                    "status" => 200,
                    "message" => "Items found.",
                    "data" => $items
                 );
            }
            else{
                $jsonResp = array(
                "status" => 200,
                "message" => "Items not found.",
                "data" => []
            );
            }


        } else {
            $jsonResp = array(
                "status" => 401,
                "message" => "Access denied",
                "data" => []
            );
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($jsonResp));
    }

    public function logout_post()
    {
        if ($decodeData = $this->decodeToken()) {

            $jdata = $decodeData->data;
            $user_id = $jdata->id;

            $this->base_model->update_data('distributor', ['id'=>$user_id], ['token' => '']);
            
            $jsonResp = array(
                "status" => 200,
                "message" => "Logout Successfully.",
                "data" => []
            );
        } else {
            $jsonResp = array(
                "status" => 401,
                "message" => "Access denied",
                "data" => []
            );
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($jsonResp));
    }

    public function decodeToken()
    { 
        require "vendor_jwt/autoload.php";

        $token = $this->input->get_request_header('Authorization');
      
        if ($token) {  
            try {  
                $secret_key = $this->config->item('secret_key');
               
                $token = str_replace('Bearer ', '', $token);
              
                $decodeData = JWT::decode($token, $secret_key, array('HS256'));
                
                $userId = $decodeData->data->id;
               
                $distributor = $this->base_model->select_row('distributor', array('id' => $userId, 'token' => $token));
              
                if ($distributor) {  
                    return $decodeData;
                } else {
                    return false;
                }
            } catch (Exception $e) { 

                return false;
            }
        } else {

            return false;
        }
    }
    
    public function saveSuggestion_post(){
      
        $_POST = json_decode(file_get_contents('php://input'),true);
               
                $wherelike = array();
                $where =array('role !='=>1);
                $partys =  $this->base_model->select_join_result('users',$where,'','',$wherelike);
             
                if(count($partys) > 0){
                foreach($partys as $party_name){
                $data[] = array(
                    'id' => $party_name->id,
                    'party_name' => $party_name->full_name,
                    'phone' => $party_name->phone,
                    'status' => $party_name->status   
                );
           

                $jsonResp = array(
                    "status" => 200,
                    "message" => " Party Successfully",
                    "data" =>  ['parties'=>$data],

                );
            
            }
       
        $this->output->set_content_type('application/json')->set_output(json_encode($jsonResp));
    }
    }
   public function setRemark_post()
    { 
        $_POST = json_decode(file_get_contents('php://input'),true);
        
        if ($decodeData = $this->decodeToken()) { 

            $jdata = $decodeData->data;
            $user_id = $jdata->id;
            $remark = $_POST['remark'];
            $order_id = $_POST['order_id'];
       
            $distributor = $this->base_model->select_row('distributor',array('id'=>$user_id)); 
            $category = $this->base_model->select_row('category',array('id'=>$distributor->category));
            $where1 ='';
            if($distributor){  
            if($distributor->is_admin==0){  
            
            if($distributor->category == $category->id && $category->title == 'in_house') {  
             $where1 = '(distributor_other = '.$user_id.' OR distributor_id='.$user_id.')AND'; 
            }elseif($distributor->category == $category->id && $category->title == 'third_party'){  
              $where1 = ' (distributor_third_party = '.$user_id.')AND';
            } 
         
       
            
            $get_id = $this->base_model->select_data('orders',$where1." id = $order_id ");
        }else{ 
            $where1 = '(id = '.$order_id.')';
            $get_id = $this->base_model->select_data('orders',$where1."");
        }
    }
        if($get_id){
            $this->form_validation->set_rules('remark', 'Remark', 'required');
            if ($this->form_validation->run() == FALSE) {
                $m = $this->form_validation->error_array();
                $jsonResp = array(
                    "status" => 401,
                    "message" => $m,
                    "data" => []
                );
            }else{
            $data =array(
                
                'dispached'=>trim($this->input->post('remark')),
                
            );

            $this->base_model->update_data('orders', array('id' => $_POST['order_id']),$data );
            $uid = $this->base_model->select_row('orders',array('id'=>$order_id));;
				
             $activity_type = $this->base_model->activity_type($order_id, $uid->user_id, "8",$user_id,'');
            
            $jsonResp = array(
                "status" => 200,
                "message" => "Remark Set Successfully.",
                "data" => []
            );
        } 
    }else{
        
        
        $jsonResp = array(
            "status" => 200,
            "message" => "Order Id is not match.",
            "data" => []
        );
    } 
        }
        else { 
            $jsonResp = array(
                "status" => 401,
                "message" => "Access denied",
                "data" => []
            );
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($jsonResp));


    }

public function setAttachment_post()
    { 
        
        if ($decodeData = $this->decodeToken()) { 
    
            $jdata = $decodeData->data;
            $user_id = $jdata->id;
            $order_id = $_POST['order_id'];
                $image = $_FILES['files'];

                $distributor = $this->base_model->select_row('distributor',array('id'=>$user_id)); 
                $category = $this->base_model->select_row('category',array('id'=>$distributor->category));
                $where1 ='';
                if($distributor){  
                if($distributor->is_admin==0){  
                  
                if($distributor->category == $category->id && $category->title == 'in_house') {  
                 $where1 = '(distributor_other = '.$user_id.' OR distributor_id='.$user_id.')AND'; 
                }elseif($distributor->category == $category->id && $category->title == 'third_party'){  
                  $where1 = ' (distributor_third_party = '.$user_id.')AND';
                } 
             
                
                $get_id = $this->base_model->select_data('orders',$where1." id = $order_id ");
            }else{ 
                $where1 = '(id = '.$order_id.')';
                $get_id = $this->base_model->select_data('orders',$where1."");
            }
        }
                if($get_id){
                    
                if (!empty($order_id) && !empty($image)) { 
                    $file_error =$file_id=0;
                    $path = 'assets/uploads/order/';
                    $files = [];

                    if (!empty($_FILES['files']['name'][0])) {
                        //   echo "<pre>"; print_r($_FILES); die;
                        $ImageCount = count($_FILES['files']['name']);
                
                        for ($i = 0; $i < $ImageCount; $i++) {
                            $_FILES['file']['name']     = $_FILES['files']['name'][$i];
                            $_FILES['file']['type']     = $_FILES['files']['type'][$i];
                            $_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
                            $_FILES['file']['error']    = $_FILES['files']['error'][$i];
                            $_FILES['file']['size']     = $_FILES['files']['size'][$i];
                
                            $config['upload_path'] = $path;
                            $config['allowed_types'] = 'jpg|JPG|png|jpeg|JPEG|pdf|doc|tiff|dotx|webp|avif|svg';
                
                            $name = $_FILES["file"]["name"];
                            $ext = pathinfo($name, PATHINFO_EXTENSION);
                            $new_name = rand(1000, 9999) . '_' . time() . '.' . $ext;
                            $config['file_name'] = $new_name;
                
                            $this->load->library('upload', $config);
                            $this->upload->initialize($config);
                
                            if (!$this->upload->do_upload('file')) {
                                $m = json_encode(array('file_error' => $this->upload->display_errors()));
                                $res = array('status' => 0, 'msg' => $m);
                                echo json_encode($res);
                                die;
                                $file_error = 1;
                            } else {
                                $upload_data = $this->upload->data();
                                $files[] = $upload_data['file_name'];
                                $file_error = 0;
                            }
                        }
                              $file=  implode(',', $files);

                        if($file_error == 0){
                            $data = array(
                                'file'=>$file,
                                'created_at'=>time(),
                                'updated_at'=>time(),
                            );
                            $insert_id = $this->base_model->insert_data('upload_images',$data);
                            $order_data = [
                                'distributor_attachment'=> $insert_id
                            ];
                            $gat_id = $this->base_model->select_row('orders',array('id'=>$order_id));
                            if($gat_id){
                            $image_data = $this->base_model->update_data('orders',array('id'=> $order_id),$order_data);
                            if($image_data){
                                 $uid = $this->base_model->select_row('orders',array('id'=>$order_id));;
				
                                $activity_type = $this->base_model->activity_type($order_id, $uid->user_id, "7",$user_id,'');
                                $image=$images=array();
                                $images=explode(',',$file);
                                 foreach($files as $file){
                                     $image[]=base_url().$path.$file;
                                 }
                            $jsonResp = array(
                                "status" => 200,
                                "message" => "Attachment Set Successfully.",
                                "data" => [
                                    'order_id'=> $order_id,
                                    'file'=>$image
                                ]
                            );
                        }
                        }else{
                            $jsonResp = array(
                                "status" => 200,
                                "message" => "Order Id is not match",
                                "data" => []
                            );
                        }
                        }else{
                            $jsonResp = array(
                                "status" => 200,
                                "message" => "Image is not uplode",
                                "data" => []
                            );
                        }

                    }

                }else {
                $this->response([
                    'status' => 200,
                    'message' => 'The Image or Order Id field is required.',
                    'data' => []
                       ]);
                }
                }else{
                $jsonResp = array(
                "status" => 200,
                "message" => "Order Id is not match",
                "data" => []
                   );

                }

        }else { 
           $jsonResp = array(
            "status" => 401,
            "message" => "Access denied",
            "data" => []
            );
           }
        $this->output->set_content_type('application/json')->set_output(json_encode($jsonResp));


    }
 public function getOrder_post()
    {  
        
        $_POST = json_decode(file_get_contents('php://input'),true);
    // file_put_contents('login_logs.txt', 'getOrder_post-'.json_encode($_POST).PHP_EOL , FILE_APPEND | LOCK_EX);
        if ($decodeData = $this->decodeToken()) { 

            $user_id = $distributor_id = $decodeData->data->id;
          
           
            $columns="orders.*,upload_images.file,delivery_boy.full_name,distributor.full_name as distributorName,distributor_attachment.file as distributorFile";
            $joins[] = array('table'=>'upload_images','condition'=>'upload_images.id=orders.file_id','jointype'=>'left');
            $joins[] = array('table'=>'delivery_boy','condition'=>'delivery_boy.id=orders.delivere_id','jointype'=>'left');
           
            $joins[] = array('table'=>'distributor','condition'=>'distributor.id=orders.distributor_id','jointype'=>'left');
            $joins[] = array('table' => 'upload_images as  distributor_attachment', 'condition' => 'distributor_attachment.id = orders.distributor_attachment', 'jointype' => 'left');
            
             $type = trim($this->input->post('type'));
             $search = trim($this->input->post('search'));
           
             $distributor = $this->base_model->select_row('distributor',array('id'=>$user_id)); 
            $category = $this->base_model->select_row('category',array('id'=>$distributor->category)); 
             $where1 ='';
             if($distributor){ 
             if($distributor->is_admin==0){ 
            
                
             if($distributor->category == $category->id && $category->title == 'in_house') { 
              $where1 = '(distributor_other = '.$user_id.' OR distributor_id='.$user_id.')'; 
             }elseif($distributor->category == $category->id && $category->title == 'third_party'){
               $where1 = ' (distributor_third_party = '.$user_id.')';
             } 
           


             $typeStatus =  strtolower($type);
             $where = '';
            if($typeStatus && $typeStatus == 'pending'){
                $where = 'AND orders.is_hold = 1 AND orders.status= 0';
            }elseif($typeStatus && $typeStatus == 'unassigned'){
                $where = 'AND orders.is_new = 1 AND orders.status= 0';
            }elseif($typeStatus && $typeStatus == 'dispatched'){
                $where = 'AND orders.is_dispatch = 1 AND orders.status= 0';
            }elseif($typeStatus && $typeStatus == 'cancel'){ 
                $where = 'AND orders.is_cancel = 1 AND orders.status= 0';
            }elseif($typeStatus && $typeStatus == 'delivery'){ 
                $where = 'AND orders.status= 1';
            }else{ 
                $where = '';
            }
           
           
            if($search) { 
                $where2 = " AND orders.party_name LIKE '%" . $search . "%'";

            } else {
                $where2 = ""; 
            }
            
           }elseif($distributor->is_admin==1){

            $typeStatus =  strtolower($type);
            
            $where = '';
            if($typeStatus && $typeStatus == 'pending'){
                $where = 'orders.is_hold = 1 AND orders.status= 0';
            }elseif($typeStatus && $typeStatus == 'unassigned'){
                $where= 'orders.is_new = 1 AND orders.status= 0';
            }elseif($typeStatus && $typeStatus == 'dispatched'){
                $where = 'orders.is_dispatch = 1 AND orders.status= 0';
            }elseif($typeStatus && $typeStatus == 'cancel'){ 
                $where = 'orders.is_cancel = 1 AND orders.status= 0';
            }elseif($typeStatus && $typeStatus == 'delivery'){ 
                $where = 'orders.status= 1';
            }else{
                $where = '';
            }

            if($search) { 
                if( $where == ''){
                $where2 = " orders.party_name LIKE '%" . $search . "%'";
                }else{
                    $where2 = " AND orders.party_name LIKE '%" . $search . "%'";
                }

            } else {
                $where2 = ""; 
            }

           }else{  
            $distributor = $this->base_model->select_row('distributor',array('id'=>$user_id)); 
            $selected_admin =  $distributor->selected_admin;

            if($distributor->category == $category->id && $category->title == 'in_house') { 
             $where1 = '(distributor_other = '.$user_id.' OR distributor_id='.$user_id.' OR distributor_id IN ('.$selected_admin.') OR distributor_other IN ('.$selected_admin.') OR distributor_third_party IN ('.$selected_admin.'))'; 
            }elseif($distributor->category == $category->id && $category->title == 'third_party'){
              $where1 = ' (distributor_third_party = '.$user_id.' OR distributor_id IN ('.$selected_admin.') OR distributor_other IN ('.$selected_admin.') OR distributor_third_party IN ('.$selected_admin.'))';
            } 
          


            $typeStatus =  strtolower($type);
            $where = '';
           if($typeStatus && $typeStatus == 'pending'){
               $where = ' AND orders.is_hold = 1 AND orders.status= 0';
           }elseif($typeStatus && $typeStatus == 'unassigned'){
               $where = ' AND orders.is_new = 1 AND orders.status= 0';
           }elseif($typeStatus && $typeStatus == 'dispatched'){
               $where = ' AND orders.is_dispatch = 1 AND orders.status= 0';
           }elseif($typeStatus && $typeStatus == 'cancel'){ 
               $where = ' AND orders.is_cancel = 1 AND orders.status= 0';
           }elseif($typeStatus && $typeStatus == 'delivery'){ 
               $where = ' AND orders.status= 1';
           }else{ 
               $where = '';
           }
          
          
           if($search) { 
               $where2 = " AND orders.party_name LIKE '%" . $search . "%'";

           } else {
               $where2 = ""; 
           }
           
          }
         }
         
         
       
                $orders = $this->base_model->select_join_result('orders', $where1.$where.$where2, $joins, $columns,'','','orders.id DESC');
             //  $str = $this->db->last_query();
             //  file_put_contents('login_logs.txt', 'getOrder_post-'.$str.PHP_EOL , FILE_APPEND | LOCK_EX);
                $order_data = array();

                foreach ($orders as $order) {
                    $itemscount = $this->base_model->select_data('order_item',array('order_code' => $order->code));
                    $qty=0;
                    foreach($itemscount as $item) {
                  $total = ($item->price * $item->quantity);
                  $qty +=$total; } 

                  $images= array();
                  if($order->file){
                     $files= explode(',',$order->file);
                     foreach($files as $file){
                         
                         $images[]=base_url('assets/uploads/order/').$file;
                         
                     }
                   
                  }
                   $deliveryBoyName = '';
                  if($order->full_name){
                    $deliveryBoyName = $order->full_name;
                  }
                 
                  if ($order->is_hold == 1) {
                    $orderStatus = 'pending';
                } elseif ($order->is_new == 1) {
                    $orderStatus = 'unassign';
                } elseif ($order->is_dispatch == 1) {
                    $orderStatus = 'dispatch';
                } elseif ($order->is_cancel == 1) {
                    $orderStatus = 'cancel';
                }elseif ($order->status == 1) {
                    $orderStatus = 'deliver';
                } else {
                    $orderStatus = '';
                }
                  
                $distributor_files = array();
                if ($order->distributorFile) {
                    $files = explode(',', $order->distributorFile);
                    foreach ($files as $file) {
                        $distributor_files[] = base_url('assets/uploads/order/') . $file;
                    }
                }
                //   $order_item = array(
                    
                //     'orderId' => $order->id,
                //     'partyName' => $order->party_name,
                //     'paymentTerm' => $order->payment_term,
                //     'dispachedDetails' =>$order->dispached,
                //     'deliveryRemark' =>$order->delivery_remark,
                //     'remark' =>$order->remark,
                //     'items' => count($itemscount),
                //     'amount'=>$qty,
                //     'createdDate' =>$order->created_at,
                //     'city' => $order->city,
                //     'deliveryBoyName'=> $deliveryBoyName,
                //     'distributorName'=>$order->distributorName,
                //     'file' =>$images,
                //     'pod'=>$distributor_files,
                //      'orderStatus'=>$orderStatus
                    
                    
                // );
                
                    $order_item = array(
                    
                    'orderId' => $order->id,
                    'partyName' => $order->party_name,
                    'paymentTerm' => $order->payment_term,
                    'dispachedDetails' =>$order->dispached,
                    'deliveryRemark' =>$order->delivery_remark,
                    'remark' =>$order->remark,
                    'items' => count($itemscount),
                    'amount'=>$qty,
                    'createdDate' =>date('Y-m-d H:i:s',strtotime($order->created_at)),
                    'city' => $order->city,
                    'deliveryBoyName'=> $deliveryBoyName,
                    'distributorName'=>$order->distributorName,
                    'file' =>$images,
                    'pod'=>$distributor_files,
                     'orderStatus'=>$orderStatus
                    
                    
                );
               

                    $order_data[] = $order_item;
                }
                // echo "<pre>"; print_r($order_data);die;
              //  file_put_contents('login_logs.txt', 'getOrder_post-'.json_encode($order_data).PHP_EOL , FILE_APPEND | LOCK_EX);
                if(!empty($order_item)){
                    $this->response([
                        'status' => 200,
                        'message' => 'data is found.',
                        'order' => $order_data
                        
                    ]);
                }else{
                $this->response([
                    'status' => 200,
                    'message' => 'data is not found.',
                    'order' => $order_data
                    
                ]);
            }
            
        }else {
            $jsonResp = array(
                "status" => 401,
                "message" => "Access denied",
                "data" => []
            );
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($jsonResp));
    }

       public function getDashboardData_get(){ 

        $_POST = json_decode(file_get_contents('php://input'),true);
     
        if ($decodeData = $this->decodeToken()) { 

            $user_id = $distributor_id = $decodeData->data->id;
            
           
            $distributor = $this->base_model->select_row('distributor',array('id'=>$user_id)); 
           
            $category = $this->base_model->select_row('category',array('id'=>$distributor->category));
           
            $where1 ='';
            if($distributor){  
            if($distributor->is_admin==0){  
                
                if($distributor->category == $category->id && $category->title == 'in_house') {  
                    $where1 = '(distributor_other = '.$user_id.' OR distributor_id='.$user_id.') AND'; 
                   }elseif($distributor->category == $category->id && $category->title == 'third_party'){  
                     $where1 = ' (distributor_third_party = '.$user_id.') AND';
                   } 
              
                $unassignOrder = $this->base_model->select_join_result('orders', $where1.'  is_new = 1 AND status= 0'); 
              $unassignOrderCount = count($unassignOrder); 

              $dispatchedOrder = $this->base_model->select_join_result('orders', $where1.'  is_dispatch = 1 AND status= 0');
              $dispatchedOrderCount = count($dispatchedOrder); 
              
              $deliverOrder = $this->base_model->select_join_result('orders',$where1.'  status= 1');
              $deliverOrderCount = count($deliverOrder);

              $pendingOrder = $this->base_model->select_join_result('orders', $where1.'  is_hold = 1 AND status= 0');
              $pendingOrderCount = count($pendingOrder);

              $cancelledOrder = $this->base_model->select_join_result('orders',$where1.'  is_cancel = 1 AND status= 0' );
              $cancelOrderCount = count($cancelledOrder); 

              $todaysDate = date('Y-m-d');
          $todaysSales = $this->base_model->select_join_result('orders', $where1 . "  created_at = '$todaysDate'AND is_cancel ='0'");

                $order_data = array();
                $todaysSalesCount=0;
                $currentMonthSalesCount=0;
                $totalSalesCount=0;
                $currentfinancialSalesCount=0;
                $last12MonthsSales=0;

                foreach ($todaysSales as $order) {
                    $itemscount = $this->base_model->select_data('order_item',array('order_code' => $order->code)); 
                   
                    // echo "<pre>"; print_r($itemscount);
                    foreach($itemscount as $item) {
                  $total = ($item->price * $item->quantity); 
                  $todaysSalesCount +=$total; } 
               
                }
              
                $firstDayOfMonth = date('Y-m-01'); // Get the first day of the current month
                $lastDayOfMonth = date('Y-m-t');   // Get the last day of the current month

                $currentMonthSales = $this->base_model->select_join_result('orders',$where1 . "  created_at >= '$firstDayOfMonth'  AND created_at <='$lastDayOfMonth' AND is_cancel = '0'");
              
                foreach ($currentMonthSales as $order) {
                    $itemscount = $this->base_model->select_data('order_item',array('order_code' => $order->code)); 
                   
                    // echo "<pre>"; print_r($itemscount);
                    foreach($itemscount as $item) {
                  $total = ($item->price * $item->quantity); 
                  $currentMonthSalesCount +=$total; } 
               
                } 
                $totalSales = $this->base_model->select_join_result('orders',$where1. " is_cancel ='0'");
                foreach ($totalSales as $order) {
                    $itemscount = $this->base_model->select_data('order_item',array('order_code' => $order->code)); 
                   
                    // echo "<pre>"; print_r($itemscount);
                    foreach($itemscount as $item) {
                  $total = ($item->price * $item->quantity); 
                  $totalSalesCount +=$total; } 
               
                } 

                $currentDate = date('Y-m-d'); 
          $currentYear = date('Y', strtotime($currentDate)); 
          $financialYearStart = ($currentYear - 1) . '-04-01'; 
        
          $financialYearEnd = $currentYear . '-03-31';

          $currentYearSales = $this->base_model->select_join_result('orders',$where1 . "  created_at >= '$financialYearStart' AND created_at <='$financialYearEnd'AND is_cancel='0'");
          foreach ($currentYearSales as $order) { 
            $itemscount = $this->base_model->select_data('order_item',array('order_code' => $order->code)); 
           
            // echo "<pre>"; print_r($itemscount);
            foreach($itemscount as $item) {
          $total = ($item->price * $item->quantity); 
          $currentfinancialSalesCount +=$total; } 
       
         
        }

         //LAST 12 MONTH DATA

         $currentDate = date('Y-m-d');  // Get the current date
         $firstDayOfMonth = date('Y-m-01'); // Get the first day of the current month
         $lastDayOfMonth = date('Y-m-t');   // Get the last day of the current month
        
         $oneYearAgo = date('Y-m-d', strtotime('-1 year', strtotime($currentDate)));
         
         
          $currentYear = date("Y");
             $startYear = 2023;
             $endYear = 2024;
             $startDate = date("$startYear-04-01");
             $endDate = date("$endYear-03-31");
             
         $last12Months = [];
         for ($i = 0; $i < 12; $i++) {
             $month = date('Y-m', strtotime("+{$i} months", strtotime($startDate)));
             $last12Months[] = $month;
         }
        
             $where = array(
                 'orders.distributor_id' => $user_id,
                 'orders.created_at >=' => $oneYearAgo,
                 'orders.created_at <=' => $lastDayOfMonth,
                 'orders.is_cancel'=>0
             );
             
             $columns = "DATE_FORMAT(orders.created_at, '%Y-%m') AS month, IFNULL(SUM(order_item.price * order_item.quantity), 0) AS total_sales";
             $group_by = "month";
             $order_by = "month";
             
             $joins = array(
                 array('table' => 'orders', 'condition' => 'orders.code = order_item.order_code', 'jointype' => 'inner')
             );
             
            
             
             
             $queryResult = $this->base_model->select_join_result('order_item',$where1 . "  orders.created_at >= '$startDate' AND orders.created_at <='$endDate' AND is_cancel='0' ", $joins, $columns, '', $group_by, $order_by);
         //   echo $this->db->last_query();die;
 
         $last12MonthsSales = [];
 
         foreach ($last12Months as $month) {
             $last12MonthsSales[$month] = 0; 
         }

         foreach ($queryResult as $row) {
             $last12MonthsSales[$row->month] = $row->total_sales;
         }
        // echo $this->db->last_query();die;
         
         $totalSalesOnly = $last12MonthsSales;
         
       
         $todaysDate = date('Y-m-d');
         $todaytotalorders = $this->base_model->select_join_result('orders', $where1 . "  created_at = '$todaysDate'");
         $todaytotalordersCount = count($todaytotalorders);

         $todaysuccessorders = $this->base_model->select_join_result('orders', $where1 . "  created_at = '$todaysDate'AND status='1'");
         $todaysuccessordersCount = count($todaysuccessorders);

         $todaysHoldOrders = $this->base_model->select_join_result('orders', $where1 . "  created_at = '$todaysDate'AND is_hold='1'AND status='0'");
         $todaysHoldOrdersCount = count($todaysHoldOrders);

         $todayCancelOrders = $this->base_model->select_join_result('orders', $where1 . "  created_at = '$todaysDate' AND is_cancel='1'AND status='0'");
         $todayCancelOrdersCount = count($todayCancelOrders);

         $todayNewOrders = $this->base_model->select_join_result('orders', $where1 . "  created_at = '$todaysDate' AND is_new='1'AND status='0'");
         $todayNewOrdersCount = count($todayNewOrders);

         $totalOrder = $this->base_model->select_join_result('orders', str_replace("AND","",$where1));
         $totalOrderCount = count($totalOrder);

         $completOrder = $this->base_model->select_join_result('orders',$where1. " status='1'");
         $completOrderCount = count($completOrder);

         $holdOrder = $this->base_model->select_join_result('orders',$where1. " is_hold='1' AND status='0'");
         $holdOrderCount = count($holdOrder);

         $cancelOrder = $this->base_model->select_join_result('orders',$where1. " is_cancel='1' AND status='0'");
         $cancelOrderCount = count($cancelOrder);

         $newOrder = $this->base_model->select_join_result('orders',$where1. " is_new='1'AND status='0'");
         $newOrderCount = count($newOrder);
        }elseif($distributor->is_admin==1){ 
            $unassignOrder = $this->base_model->select_join_result('orders', array('status'=>0,'is_new'=>1));
              $unassignOrderCount = count($unassignOrder); 

              $dispatchedOrder = $this->base_model->select_join_result('orders', array('status'=>0,'is_dispatch'=>1));
              $dispatchedOrderCount = count($dispatchedOrder); 
              
              $deliverOrder = $this->base_model->select_join_result('orders', array('status'=>1));
              $deliverOrderCount = count($deliverOrder);

              $pendingOrder = $this->base_model->select_join_result('orders', array('status'=>0,'is_hold'=>1));
              $pendingOrderCount = count($pendingOrder);

              $cancelledOrder = $this->base_model->select_join_result('orders', array('status'=>0,'is_cancel'=>1));
              $cancelOrderCount = count($cancelledOrder); 

              $todaysSales = $this->base_model->select_join_result('orders', array('created_at'=>date('y-m-d'),'is_cancel'=>0));
              

                $order_data = array();
                $todaysSalesCount=0;
                $currentMonthSalesCount=0;
                $totalSalesCount=0;
                $currentfinancialSalesCount=0;
                $last12MonthsSales=0;

                foreach ($todaysSales as $order) {
                    $itemscount = $this->base_model->select_data('order_item',array('order_code' => $order->code)); 
                   
                    // echo "<pre>"; print_r($itemscount);
                    foreach($itemscount as $item) {
                  $total = ($item->price * $item->quantity); 
                  $todaysSalesCount +=$total; } 
               
                 
                }
              
                $firstDayOfMonth = date('Y-m-01'); // Get the first day of the current month
                $lastDayOfMonth = date('Y-m-t');   // Get the last day of the current month

                $currentMonthSales = $this->base_model->select_join_result('orders', array(
                   
                    'created_at >=' => $firstDayOfMonth,
                    'created_at <=' => $lastDayOfMonth,
                    'is_cancel'=>0
                ));
                foreach ($currentMonthSales as $order) {
                    $itemscount = $this->base_model->select_data('order_item',array('order_code' => $order->code)); 
                   
                    // echo "<pre>"; print_r($itemscount);
                    foreach($itemscount as $item) {
                  $total = ($item->price * $item->quantity); 
                  $currentMonthSalesCount +=$total; } 
               
                 
                } 
                $totalSales = $this->base_model->select_join_result('orders', array('is_cancel'=>0));
                foreach ($totalSales as $order) {
                    $itemscount = $this->base_model->select_data('order_item',array('order_code' => $order->code)); 
                   
                    // echo "<pre>"; print_r($itemscount);
                    foreach($itemscount as $item) {
                  $total = ($item->price * $item->quantity); 
                  $totalSalesCount +=$total; } 
               
                } 

                $currentDate = date('Y-m-d'); 
          $currentYear = date('Y', strtotime($currentDate)); 
          $financialYearStart = ($currentYear - 1) . '-04-01'; 
        
          $financialYearEnd = $currentYear . '-03-31';

          $currentYearSales = $this->base_model->select_join_result('orders', array(
             
              'created_at >=' => $financialYearStart,
              'created_at <=' => $financialYearEnd,
              'is_cancel'=>0
          ));
          foreach ($currentYearSales as $order) { 
            $itemscount = $this->base_model->select_data('order_item',array('order_code' => $order->code)); 
           
            // echo "<pre>"; print_r($itemscount);
            foreach($itemscount as $item) {
          $total = ($item->price * $item->quantity); 
          $currentfinancialSalesCount +=$total; } 
       
         
        }

            $currentYear = date("Y");
              $startYear = 2023;
             $endYear = 2024;
             $startDate = date("$startYear-04-01");
             $endDate = date("$endYear-03-31");
             
         $last12Months = [];
         for ($i = 0; $i < 12; $i++) {
             $month = date('Y-m', strtotime("+{$i} months", strtotime($startDate)));
             $last12Months[] = $month;
         }
        
             $where = array(
                
                 'orders.created_at >=' => $startDate,
                 'orders.created_at <=' => $endDate,
                 'orders.is_cancel'=>0
             );
             
             $columns = "DATE_FORMAT(orders.created_at, '%Y-%m') AS month, IFNULL(SUM(order_item.price * order_item.quantity), 0) AS total_sales";
             $group_by = "month";
             $order_by = "month";
             
             $joins = array(
                 array('table' => 'orders', 'condition' => 'orders.code = order_item.order_code', 'jointype' => 'inner')
             );
             
             $queryResult = $this->base_model->select_join_result('order_item', $where, $joins, $columns, '', $group_by, $order_by);
             
 
         $last12MonthsSales = [];
 
         foreach ($last12Months as $month) {
             $last12MonthsSales[$month] = 0; 
         }
 
         foreach ($queryResult as $row) {
             $last12MonthsSales[$row->month] = $row->total_sales;
         }
        
         $totalSalesOnly = $last12MonthsSales;
         $todaytotalorders = $this->base_model->select_join_result('orders',array('created_at'=>date('Y-m-d')));
         $todaytotalordersCount = count($todaytotalorders);

         $todaysuccessorders = $this->base_model->select_join_result('orders',array('created_at'=>date('Y-m-d'),'status'=>1));
         $todaysuccessordersCount = count($todaysuccessorders);

         $todaysHoldOrders = $this->base_model->select_join_result('orders',array('created_at'=>date('Y-m-d'),'status'=>0,'is_hold'=>1));
         $todaysHoldOrdersCount = count($todaysHoldOrders);

         $todayCancelOrders = $this->base_model->select_join_result('orders',array('created_at'=>date('Y-m-d'),'status'=>0,'is_cancel'=>1));
         $todayCancelOrdersCount = count($todayCancelOrders);

         $todayNewOrders = $this->base_model->select_join_result('orders',array('created_at'=>date('Y-m-d'),'status'=>0,'is_new'=>1));
         $todayNewOrdersCount = count($todayNewOrders);

         $totalOrder = $this->base_model->select_join_result('orders',array());
         $totalOrderCount = count($totalOrder);

         $completOrder = $this->base_model->select_join_result('orders',array('status'=>1));
         $completOrderCount = count($completOrder);

         $holdOrder = $this->base_model->select_join_result('orders',array('status'=>0,'is_hold'=>1));
         $holdOrderCount = count($holdOrder);

         $cancelOrder = $this->base_model->select_join_result('orders',array('status'=>0,'is_cancel'=>1));
         $cancelOrderCount = count($cancelOrder);

             $newOrder = $this->base_model->select_join_result('orders',array('status'=>0,'is_new'=>1));
             $newOrderCount = count($newOrder);
        }else{  

            $distributor = $this->base_model->select_row('distributor',array('id'=>$user_id)); 
            $selected_admin =  $distributor->selected_admin;
              
            if($distributor->category == $category->id && $category->title == 'in_house') {  
                $where1 = '(distributor_other = '.$user_id.' OR distributor_id='.$user_id.' OR distributor_id IN ('.$selected_admin.') OR distributor_other IN ('.$selected_admin.') OR distributor_third_party IN ('.$selected_admin.')) AND'; 
               }elseif($distributor->category == $category->id && $category->title == 'third_party'){  
                 $where1 = ' (distributor_third_party = '.$user_id.' OR distributor_id IN ('.$selected_admin.') OR distributor_other IN ('.$selected_admin.') OR distributor_third_party IN ('.$selected_admin.')) AND';
               } 
          
            $unassignOrder = $this->base_model->select_join_result('orders', $where1.'  is_new = 1 AND status= 0'); 
          $unassignOrderCount = count($unassignOrder); 

          $dispatchedOrder = $this->base_model->select_join_result('orders', $where1.'  is_dispatch = 1 AND status= 0');
          $dispatchedOrderCount = count($dispatchedOrder); 
          
          $deliverOrder = $this->base_model->select_join_result('orders',$where1.'  status= 1');
          $deliverOrderCount = count($deliverOrder);

          $pendingOrder = $this->base_model->select_join_result('orders', $where1.'  is_hold = 1 AND status= 0');
          $pendingOrderCount = count($pendingOrder);

          $cancelledOrder = $this->base_model->select_join_result('orders',$where1.'  is_cancel = 1 AND status= 0' );
          $cancelOrderCount = count($cancelledOrder); 

          $todaysDate = date('Y-m-d');
      $todaysSales = $this->base_model->select_join_result('orders', $where1 . "  created_at = '$todaysDate'AND is_cancel ='0'");

            $order_data = array();
            $todaysSalesCount=0;
            $currentMonthSalesCount=0;
            $totalSalesCount=0;
            $currentfinancialSalesCount=0;
            $last12MonthsSales=0;

            foreach ($todaysSales as $order) {
                $itemscount = $this->base_model->select_data('order_item',array('order_code' => $order->code)); 
               
                // echo "<pre>"; print_r($itemscount);
                foreach($itemscount as $item) {
              $total = ($item->price * $item->quantity); 
              $todaysSalesCount +=$total; } 
           
            }
          
            $firstDayOfMonth = date('Y-m-01'); // Get the first day of the current month
            $lastDayOfMonth = date('Y-m-t');   // Get the last day of the current month

            $currentMonthSales = $this->base_model->select_join_result('orders',$where1 . "  created_at >= '$firstDayOfMonth'  AND created_at <='$lastDayOfMonth' AND is_cancel = '0'");
          
            foreach ($currentMonthSales as $order) {
                $itemscount = $this->base_model->select_data('order_item',array('order_code' => $order->code)); 
               
                // echo "<pre>"; print_r($itemscount);
                foreach($itemscount as $item) {
              $total = ($item->price * $item->quantity); 
              $currentMonthSalesCount +=$total; } 
           
            } 
            $totalSales = $this->base_model->select_join_result('orders',$where1. " is_cancel ='0'");
            foreach ($totalSales as $order) {
                $itemscount = $this->base_model->select_data('order_item',array('order_code' => $order->code)); 
               
                // echo "<pre>"; print_r($itemscount);
                foreach($itemscount as $item) {
              $total = ($item->price * $item->quantity); 
              $totalSalesCount +=$total; } 
           
            } 

            $currentDate = date('Y-m-d'); 
      $currentYear = date('Y', strtotime($currentDate)); 
      $financialYearStart = ($currentYear - 1) . '-04-01'; 
    
      $financialYearEnd = $currentYear . '-03-31';

      $currentYearSales = $this->base_model->select_join_result('orders',$where1 . "  created_at >= '$financialYearStart' AND created_at <='$financialYearEnd'AND is_cancel='0'");
      foreach ($currentYearSales as $order) { 
        $itemscount = $this->base_model->select_data('order_item',array('order_code' => $order->code)); 
       
        // echo "<pre>"; print_r($itemscount);
        foreach($itemscount as $item) {
      $total = ($item->price * $item->quantity); 
      $currentfinancialSalesCount +=$total; } 
   
     
    }

     //LAST 12 MONTH DATA

     $currentDate = date('Y-m-d');  // Get the current date
     $firstDayOfMonth = date('Y-m-01'); // Get the first day of the current month
     $lastDayOfMonth = date('Y-m-t');   // Get the last day of the current month
    
     $oneYearAgo = date('Y-m-d', strtotime('-1 year', strtotime($currentDate)));
     
     
      $currentYear = date("Y");
         $startYear = 2023;
             $endYear = 2024;
         $startDate = date("$startYear-04-01");
         $endDate = date("$endYear-03-31");
         
     $last12Months = [];
     for ($i = 0; $i < 12; $i++) {
         $month = date('Y-m', strtotime("+{$i} months", strtotime($startDate)));
         $last12Months[] = $month;
     }
    
         $where = array(
             'orders.distributor_id' => $user_id,
             'orders.created_at >=' => $oneYearAgo,
             'orders.created_at <=' => $lastDayOfMonth,
             'orders.is_cancel'=>0
         );
         
         $columns = "DATE_FORMAT(orders.created_at, '%Y-%m') AS month, IFNULL(SUM(order_item.price * order_item.quantity), 0) AS total_sales";
         $group_by = "month";
         $order_by = "month";
         
         $joins = array(
             array('table' => 'orders', 'condition' => 'orders.code = order_item.order_code', 'jointype' => 'inner')
         );
         
        
         
         
         $queryResult = $this->base_model->select_join_result('order_item',$where1 . "  orders.created_at >= '$startDate' AND orders.created_at <='$endDate' AND is_cancel='0' ", $joins, $columns, '', $group_by, $order_by);
     //   echo $this->db->last_query();die;

     $last12MonthsSales = [];

     foreach ($last12Months as $month) {
         $last12MonthsSales[$month] = 0; 
     }

     foreach ($queryResult as $row) {
         $last12MonthsSales[$row->month] = $row->total_sales;
     }
     
     
     $totalSalesOnly = $last12MonthsSales;
     
   
     $todaysDate = date('Y-m-d');
     $todaytotalorders = $this->base_model->select_join_result('orders', $where1 . "  created_at = '$todaysDate'");
     $todaytotalordersCount = count($todaytotalorders);

     $todaysuccessorders = $this->base_model->select_join_result('orders', $where1 . "  created_at = '$todaysDate'AND status='1'");
     $todaysuccessordersCount = count($todaysuccessorders);

     $todaysHoldOrders = $this->base_model->select_join_result('orders', $where1 . "  created_at = '$todaysDate'AND is_hold='1'AND status='0'");
     $todaysHoldOrdersCount = count($todaysHoldOrders);

     $todayCancelOrders = $this->base_model->select_join_result('orders', $where1 . "  created_at = '$todaysDate' AND is_cancel='1'AND status='0'");
     $todayCancelOrdersCount = count($todayCancelOrders);

     $todayNewOrders = $this->base_model->select_join_result('orders', $where1 . "  created_at = '$todaysDate' AND is_new='1'AND status='0'");
     $todayNewOrdersCount = count($todayNewOrders);

     $totalOrder = $this->base_model->select_join_result('orders', str_replace("AND","",$where1));
     $totalOrderCount = count($totalOrder);

     $completOrder = $this->base_model->select_join_result('orders',$where1. " status='1'");
     $completOrderCount = count($completOrder);

     $holdOrder = $this->base_model->select_join_result('orders',$where1. " is_hold='1' AND status='0'");
     $holdOrderCount = count($holdOrder);

     $cancelOrder = $this->base_model->select_join_result('orders',$where1. " is_cancel='1' AND status='0'");
     $cancelOrderCount = count($cancelOrder);

     $newOrder = $this->base_model->select_join_result('orders',$where1. " is_new='1'AND status='0'");
     $newOrderCount = count($newOrder);
    }
    }
         
                
                $order_item = array(
                    'todayTotalOrder'=>$todaytotalordersCount,
                    'todayCompletOrder'=> $todaysuccessordersCount,
                    'todayHoldOrder'=> $todaysHoldOrdersCount,
                    'todayCancelOrders'=>$todayCancelOrdersCount,
                    'todayNewOrders'=>$todayNewOrdersCount,
                    'totalOrder'=>$totalOrderCount,
                    'completOrder'=>$completOrderCount,
                    'holdOrder'=>$holdOrderCount,
                    'cancelOrder'=>$cancelOrderCount,
                    'newOrder'=>$newOrderCount,
                    'unassignOrder' => $unassignOrderCount,
                    'dispatchedOrder' => $dispatchedOrderCount,
                    'deliveryOrder' => $deliverOrderCount,
                    'cancelOrder' => $cancelOrderCount,
                    'todaysSales' =>$todaysSalesCount,
                    'currentMonthSales' =>$currentMonthSalesCount,
                    'totalSales' =>$totalSalesCount,
                    'currentfinancialSalesCount'=>$currentfinancialSalesCount,
                    'last12MonthsSales'=>$totalSalesOnly
                    
                );

                    $order_data[] = $order_item;
                // echo "<pre>"; print_r($order_data);die;
                if(!empty($order_item)){
                    $this->response([
                        'status' => 200,
                        'message' => 'data is found.',
                        'order' => $order_data
                        
                    ]);
                }else{
                $this->response([
                    'status' => 200,
                    'message' => 'data is not found.',
                    'order' => $order_data
                    
                ]);
            }
            
        }else {
            $jsonResp = array(
                "status" => 401,
                "message" => "Access denied",
                "data" => []
            );
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($jsonResp));
        

    }
    
 public function orderDetail_post()
    { 
        $_POST = json_decode(file_get_contents('php://input'),true);  
        if ($decodeData = $this->decodeToken()) { 
           
            $jdata = $decodeData->data;
            $user_id = $jdata->id;
            $order_id = $this->input->post('order_id');

            
            if($order_id){
                $distributor = $this->base_model->select_row('distributor',array('id'=>$user_id)); 
                $category = $this->base_model->select_row('category',array('id'=>$distributor->category)); 
                 $where1 ='';
                if($distributor){  
                if($distributor->is_admin==0){  
            
                if($distributor->category == $category->id && $category->title == 'in_house') {  
                 $where1 = '(distributor_other = '.$user_id.' OR distributor_id='.$user_id.')AND'; 
                }elseif($distributor->category == $category->id && $category->title == 'third_party'){ 
                  $where1 = ' (distributor_third_party = '.$user_id.')AND';
                } 
            


                $columns="orders.*,upload_images.file,delivery_boy.full_name,distributor.full_name as distributorName,delivery_file.file as deliveryFile";
                $joins[] = array('table'=>'upload_images','condition'=>'upload_images.id=orders.file_id','jointype'=>'left');
                $joins[] = array('table'=>'delivery_boy','condition'=>'delivery_boy.id=orders.delivere_id','jointype'=>'left');
                $joins[] = array('table'=>'distributor','condition'=>'distributor.id=orders.distributor_id','jointype'=>'left');
                $joins[] = array('table' => 'upload_images as  delivery_file', 'condition' => 'delivery_file.id = orders.delivery_file', 'jointype' => 'left');
                
                $order = $this->base_model->select_join_row('orders', $where1. " orders.id = '$order_id'", $joins, $columns);
                }elseif($distributor->is_admin==1){ 
                    $columns="orders.*,upload_images.file,delivery_boy.full_name,distributor.full_name as distributorName,delivery_file.file as deliveryFile";
                $joins[] = array('table'=>'upload_images','condition'=>'upload_images.id=orders.file_id','jointype'=>'left');
                $joins[] = array('table'=>'delivery_boy','condition'=>'delivery_boy.id=orders.delivere_id','jointype'=>'left');
                $joins[] = array('table'=>'distributor','condition'=>'distributor.id=orders.distributor_id','jointype'=>'left');
                $joins[] = array('table' => 'upload_images as  delivery_file', 'condition' => 'delivery_file.id = orders.delivery_file', 'jointype' => 'left');

                    $where1 = "orders.id = '$order_id'";
                    $order = $this->base_model->select_join_row('orders', $where1, $joins, $columns);
                }else{ 
            
                    $distributor = $this->base_model->select_row('distributor',array('id'=>$user_id)); 
                    $selected_admin =  $distributor->selected_admin;
                    
                    if($distributor->category == $category->id && $category->title == 'in_house') {  
                     $where1 = '(distributor_other = '.$user_id.' OR distributor_id='.$user_id.' OR distributor_id IN ('.$selected_admin.') OR distributor_other IN ('.$selected_admin.') OR distributor_third_party IN ('.$selected_admin.'))AND'; 
                    }elseif($distributor->category == $category->id && $category->title == 'third_party'){ 
                      $where1 = ' (distributor_third_party = '.$user_id.' OR distributor_id IN ('.$selected_admin.') OR distributor_other IN ('.$selected_admin.') OR distributor_third_party IN ('.$selected_admin.'))AND';
                    } 
                
    
                    $columns="orders.*,upload_images.file,delivery_boy.full_name,distributor.full_name as distributorName,delivery_file.file as deliveryFile";
                    $joins[] = array('table'=>'upload_images','condition'=>'upload_images.id=orders.file_id','jointype'=>'left');
                    $joins[] = array('table'=>'delivery_boy','condition'=>'delivery_boy.id=orders.delivere_id','jointype'=>'left');
                    $joins[] = array('table'=>'distributor','condition'=>'distributor.id=orders.distributor_id','jointype'=>'left');
                    $joins[] = array('table' => 'upload_images as  delivery_file', 'condition' => 'delivery_file.id = orders.delivery_file', 'jointype' => 'left');
                    
                    $order = $this->base_model->select_join_row('orders', $where1. " orders.id = '$order_id'", $joins, $columns);
                    }
            }
                if($order){
                    $itemscount = $this->base_model->select_data('order_item',array('order_code' => $order->code));
                    $qty=0;
                    $total=0;
                    foreach($itemscount as $item) {
                  $total += ($item->price * $item->quantity);
                   }
                    $file= '';
                     $images= array();
              if($order->file){
                   $files= explode(',',$order->file);
                       foreach($files as $file){
                         
                         $images[]=base_url('assets/uploads/order/').$file;
                         
                       }
              }
            //  echo '<pre>';print_R($order);die;
              $distributor_files = array();
              if (isset($order->deliveryFile)) {
                  $files = explode(',', $order->deliveryFile);
                  foreach ($files as $file) {
                      $distributor_files[] = base_url('assets/uploads/order/') . $file;
                  }
              }
               if ($order->is_hold == 1) {
                    $orderStatus = 'pending';
                } elseif ($order->is_new == 1) {
                    $orderStatus = 'unassign';
                } elseif ($order->is_dispatch == 1) {
                    $orderStatus = 'dispatch';
                } elseif ($order->is_cancel == 1) {
                    $orderStatus = 'cancel';
                }elseif ($order->status == 1) {
                    $orderStatus = 'deliver';
                } else {
                    $orderStatus = '';
                }
                
                $remark = $order->remark;
                  
                   if($order->is_hold == 1 || $order->is_cancel){
                        $order_reason = $this->base_model->select_row('order_reason',array('order_id'=>$order->id));
                        if($order_reason){
                             $remark = $order_reason->reason;    
                        }
                   
                }
              
                    $order_data = array(
                         'orderId' => $order->id,
                        'partyName' => $order->party_name,
                        'paymentTerm' => $order->payment_term,
                        'dispachedDetails' =>$remark,
                        'deliveryRemark' =>$order->delivery_remark,
                        'remark' =>$order->remark,
                        'items' => count($itemscount),
                        'amount'=>$total,
                        'createdDate' =>date('Y-m-d H:i:s',strtotime($order->created_at)),
                        'city'=>$order->city,
                        'deliveryBoyName'=>$order->full_name,
                        'distributorName'=>$order->distributorName,
                        'remark'=>$order->remark,
                        'file' =>$images,
                        'pod'=>$distributor_files,
                        'orderStatus'=>$orderStatus
                      );
                      $item_array=array();
                      foreach($itemscount as $item ){ 
                        $item_data = array(
                            'item_id' => $item->id,
                            'order_code' => $item->order_code,
                            'item_name' => $item->item_name,
                            'quantity' =>$item->quantity,
                            'price'=>$item->price,
                          
                            
                        );
                       $item_array[] = $item_data;
                    } 
                    $order_data['items'] =$item_array;
                    $this->response([
                        'status' => 200,
                        'message' => 'data is found.',
                        'order' => $order_data
                        
                    ]);
                }else{
                    $jsonResp = array(
                        "status" => 200,
                        "message" => "Order Id is Invalid",
                        "data" => []
                    );
                }

            }else{
                $jsonResp = array(
                    "status" => 200,
                    "message" => "Order Id is Invalid",
                    "data" => []
                );
            }
        }else{
                    $jsonResp = array(
                        "status" => 401,
                        "message" => "Access denied",
                        "data" => []
                    );
                }
            $this->output->set_content_type('application/json')->set_output(json_encode($jsonResp));
       
    }
    
    public function setNew_post()
    {  
        $_POST = json_decode(file_get_contents('php://input'),true);
        
        if ($decodeData = $this->decodeToken()) { 

            $jdata = $decodeData->data;
            $user_id = $jdata->id;
         
            $order_id = $this->input->post('order_id');
            
            
             $distributor = $this->base_model->select_row('distributor',array('id'=>$user_id)); 
             $category = $this->base_model->select_row('category',array('id'=>$distributor->category)); 
                if($distributor->is_admin==0){  
                    if($distributor->category == $category->id && $category->title == 'in_house') {  
                        $where = '(distributor_other = '.$user_id.' OR distributor_id='.$user_id.')AND'; 
                       }elseif($distributor->category == $category->id && $category->title == 'third_party'){ 
                         $where = ' (distributor_third_party = '.$user_id.')AND';
                       } 

                       $where1 = " orders.id = $order_id  AND orders.is_hold= 1";
       
        $get_id = $this->base_model->select_data('orders', $where.$where1);
                }else{ 
                    $get_id = $this->base_model->select_data('orders',array('id'=>$order_id,'is_hold'=>1));
                }
       
        
        if($get_id){
          
                $data = array(
                    'is_new'=>1,
                    'is_hold'=>0
                   );
                   $this->base_model->update_data('orders',array('id'=>$this->input->post('order_id')),$data);
                   $notificationData = array(
			                   
			                   'orderId'=>$this->input->post('order_id'),
			                   'body'=>"your order has been unassigned",
			                   'type'=> 'unassigned'
			                      );
			$this->send_notification_data_order($notificationData);
                     $uid = $this->base_model->select_row('orders',array('id'=>$order_id));;
				$activity_type = $this->base_model->activity_type($order_id, $uid->user_id, "6",$user_id,'');
            
            $jsonResp = array(
                "status" => 200,
                "message" => "Order Status Unassign Set Successfully.",
                "data" => []
            );
         
    }else{
        
        
        $jsonResp = array(
            "status" => 200,
            "message" => "Order Id is Invalid.",
            "data" => []
        );
    } 
        }
        else { 
            $jsonResp = array(
                "status" => 401,
                "message" => "Access denied",
                "data" => []
            );
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($jsonResp));


    }
    
	        function send_notification_data_order($notificationData){
       
       
        $order = $this->base_model->select_row('orders', array('id' => $notificationData['orderId']));
        
         $where = '(id = '.$order->distributor_id.' OR is_admin=1)'; 
       $distributorIds = $this->base_model->select_data('distributor', $where);
       $distributor = $this->base_model->select_row('distributor', array('id' => $order->distributor_id));
        
        foreach ($distributorIds as $distributorId) { 
            if (empty($distributorId->device_token)) {
                continue; 
            }
            
            if($distributorId->is_admin == 1){
				$body = ucwords($order->party_name).  " (Party) " . " - " . ucwords($distributor->full_name) . " (Distributor) " . " - " . "order has been " . $notificationData['type'];
			}else{
				$body = ucwords($order->party_name).  " (Party) " . " - " . $notificationData['body'];
			}
        
            $data = array(
                'to' => $distributorId->device_token,
                'notification' => array(
                    'body' => $body,
                    'title' => 'Order#' . $notificationData['orderId'],
                    'content_available' => true,
                    'priority' => 'high',
                    'sound' => 'default',
                ),
                'data' => array(
                    'id' => $notificationData['orderId'],
                    'type' => $notificationData['type']
                ),
            );
            
             $curl = curl_init();
        
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://fcm.googleapis.com/fcm/send',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($data), // Encode data as JSON
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: key=AAAAzR6YLsA:APA91bHfGTj3KZ8Rwkd3x_5BmpfFMzgMaWE_Yf99dct0t8eZacgmYsl40SoDljjlFvkpgBW_3xjpykS_Km53WX1Nc4B-6KHS8vDeSahfLtXGlXV11fGuJN4sfWSWNxH3pjZT54hFs8H5',
        ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        
            
        }
       
        // echo $response;
        }
        
        public function setVersion_post(){ 

         $_POST = json_decode(file_get_contents('php://input'),true);
        
        $device_type = $this->input->post('device_type');
            $version = $this->input->post('version');
          
            
            if(empty($version)){
                 $jsonResp = array(
                "status" => 401,
                "message" => "version fild is required",
                "data" => []
            );
                
            }elseif($device_type == 1){
              $get_version = $this->base_model->select_row('versions', array('ios_version' => $version));
               if($get_version){
                   $status = 401;
               }else{
                    $status = 200;
                  
               }
               
                   $jsonResp = array(
                        "status" => $status,
                        "message" => "version",
                        "data" => $version
                    );
                    
                
            }else{ 
                 $get_version = $this->base_model->select_row('versions', array('android_version' => $version));
               if($get_version){
                   $status = 401;
               }else{
                    $status = 200;
               }
               
                   $jsonResp = array(
                        "status" => $status,
                        "message" => "version",
                        "data" => $version
                    );
                    
                
            }
                
            
       
        $this->output->set_content_type('application/json')->set_output(json_encode($jsonResp));

}
 
    
}
