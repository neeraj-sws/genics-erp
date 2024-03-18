<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH . 'libraries/REST_Controller.php');

use \Firebase\JWT\JWT;

class Authentication extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('base_model');
        $this->load->model('Site_model');
    }
    public function requestOtp_post()
    { 
        $_POST = json_decode(file_get_contents('php://input'),true);

        $phone = $_POST['phone'];

        if (!empty($phone)) {

            $where = ['phone' => $phone];
            $seller = $this->base_model->select_row('delivery_boy', $where);

            if ($seller) {


                $uwhere = ['id' => $seller->id];
                $udata = [
                    'otp' => $this->generateOTP()
                ];
                $this->base_model->update_data('delivery_boy', $uwhere, $udata);

                $user = $this->base_model->select_row('delivery_boy', $uwhere);

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
         $device_type = $_POST['device_type'];
        $device_token = $_POST['device_token'];

        if (!empty($phone) && !empty($otp)) {

            $this->db->select('id,full_name,phone,email');

            $seller = $this->base_model->select_row('delivery_boy', array('otp' => $_POST['otp'], 'phone' => $_POST['phone']));

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

                $this->base_model->update_data('delivery_boy', $uwhere, $udata);

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

    
    public function Login_post()
    {
        $_POST = json_decode(file_get_contents('php://input'),true);

        require "vendor_jwt/autoload.php";

        $email = $_POST['email'];
        $password = $_POST['password'];
        

        if (!empty($email) && !empty($password)) {

            $this->db->select('id,full_name,phone,email');

            $delivery_boy = $this->base_model->select_row('delivery_boy', array('email' => $_POST['email'], 'password' => sha1($_POST['password'])));

            if ($delivery_boy) {  

                $secret_key = $this->config->item('secret_key');
                $issuer_claim = $this->config->item('issuer_claim'); // this can be the servername
                $audience_claim = $this->config->item('audience_claim');
                $issuedat_claim = time(); // issued at
                $notbefore_claim = $issuedat_claim + 0; //not before in seconds
                $expire_claim = $issuedat_claim + 60000; // expire time in seconds
                $token = [
                    "iss" => $issuer_claim,
                    "aud" => $audience_claim,
                    "iat" => $issuedat_claim,
                    "nbf" => $notbefore_claim,
                    "exp" => $expire_claim,
                    "data" => $delivery_boy
                ];

                $jwt = JWT::encode($token, $secret_key);
          
                $uwhere = ['id' => $delivery_boy->id];
                $udata = [
                    'token' => $jwt
                ];
               

                $this->base_model->update_data('delivery_boy', $uwhere, $udata);

                $this->response([
                    'status' => 200,
                    'message' => 'Login successful.',
                    'data' => $delivery_boy,
                    'token' => $jwt
                ]);
            } else {

                $this->response([
                    'status' => 401,
                    'message' => 'Invalid Email or Password.',
                    'data' => []
                ]);
            }
        } else {

            $this->response([
                'status' => 401,
                'message' => 'The Email or Password field is required.',
                'data' => []
            ]);
        }
    }

       public function getOrder_post()
    { 
        $_POST = json_decode(file_get_contents('php://input'),true);
     
        if ($decodeData = $this->decodeToken()) { 

            $user_id = $distributor_id = $decodeData->data->id;
             $delivery_boy = $this->base_model->select_row('delivery_boy', array('id' => $user_id));
            $columns="orders.*,upload_images.file,distributor.full_name,deliver_attachment.file as deliveFile,delivery_boy.full_name as delivery_boy_name,delivery_boy.is_admin";
            $joins[] = array('table'=>'upload_images','condition'=>'upload_images.id=orders.file_id','jointype'=>'left');
            $joins[] = array('table'=>'distributor','condition'=>'distributor.id=orders.distributor_id','jointype'=>'left');
             $joins[] = array('table'=>'delivery_boy','condition'=>'delivery_boy.id=orders.delivere_id','jointype'=>'left');
            $joins[] = array('table' => 'upload_images as  deliver_attachment', 'condition' => 'deliver_attachment.id = orders.delivery_file', 'jointype' => 'left');
            $search = trim($this->input->post('search'));
            $type = trim($this->input->post('type'));
            if($search) { 
                $where2 = " AND orders.party_name LIKE '%" . $search . "%'";

            } else {
                $where2 = ""; 
            }
            $typeStatus =  strtolower($type);
         
            if($typeStatus && $typeStatus == 'pending'){
                $where1 = ' AND orders.is_hold = 1 AND orders.status= 0';
            }elseif($typeStatus && $typeStatus == 'unassigned'){
                $where1= ' AND orders.is_new = 1 AND orders.status= 0';
            }elseif($typeStatus && $typeStatus == 'dispatched'){
                $where1 = ' AND orders.is_dispatch = 1 AND orders.status= 0';
            }elseif($typeStatus && $typeStatus == 'cancel'){ 
                $where1 = ' AND orders.is_cancel = 1 AND orders.status= 0';
            }elseif($typeStatus && $typeStatus == 'delivery'){ 
                $where1 = ' AND orders.status= 1';
            }else{
                $where1 = '';
            }
            
             if($delivery_boy->is_admin==0){
             $where = "orders.delivere_id = $user_id AND orders.is_cancel = 0 AND orders.is_hold = 0";
            }else{  
                $where = "orders.is_new = 0 AND orders.is_cancel = 0 AND orders.is_hold = 0";
            }  

                $orders = $this->base_model->select_join_result('orders',$where.$where1.$where2, $joins, $columns,'','','orders.id DESC');
                $order_data = array();

                foreach ($orders as $order) {
                    $approv_by = $this->base_model->select_row('users', array('users.id' => $order->approv_by));

                    if($order->status == 0){
                        $status = 'Dispatch';
                        $status .='Cancel';
                   }elseif($order->status == 1){
                    if(!empty($list->approv_by)){
                        $status = 'Dispatched by ' .  $approv_by->full_name ;
                    }
                    else{
                        $status = 'Dispatched';
                    }
                   }else{
                    if(!empty($order->approv_by)){
                    $status = 'Canceled by'. $approv_by->full_name ;
                    }else{
                        $status = 'Canceled';
                    }
                   }
                
                   $row = $status;
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
                  $deliver_files = array();
                if ($order->deliveFile) {
                    $files = explode(',', $order->deliveFile);
                    foreach ($files as $file) {
                        $deliver_files[] = base_url('assets/uploads/order/') . $file;
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
                 if($delivery_boy->is_admin == 1 && !empty($order->delivere_id)){
                    $party = $order->party_name . ' - (' . $order->delivery_boy_name . ')';
                }else{
                    $party = $order->party_name;
                }
                    $order_item = array(
                    'order_id' => $order->id,
                    'party_name' => $party,
                    'payment_term' => $order->payment_term,
                    'dispached_details' =>$order->dispached,
                    'items' => count($itemscount),
                    'amount'=>$qty,
                    'created_date' =>date('Y-m-d H:i:s',strtotime($order->created_at)),
                    'city'=>$order->city,
                    'remark'=>$order->remark,
                    'delivery_rimark'=>$order->delivery_remark,
                    'file' => $images,
                    'pod'=>$deliver_files,
                    'orderStatus'=>$orderStatus
                        
                    );

                    $order_data[] = $order_item;
                }
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
               
                $distributor = $this->base_model->select_row('delivery_boy', array('id' => $userId, 'token' => $token));

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
public function satDeliverAttachment_post(){ 

        if ($decodeData = $this->decodeToken()) { 

            $jdata = $decodeData->data;
            $user_id = $jdata->id;
            
            $order_id = $_POST['order_id'];
        
        $image = $_FILES['image'];
        
        $delivery_boy = $this->base_model->select_row('delivery_boy', array('id' => $user_id));
       
        if($delivery_boy->is_admin==0){
            $where = array('id'=>$order_id,'delivere_id'=>$user_id,'is_cancel'=>0,'is_hold'=>0);
           }else{  
               $where = array('id'=>$order_id,'is_cancel'=>0,'is_hold'=>0);
           }  

        $get_id = $this->base_model->select_row('orders',$where);
        if($get_id){
            if (!empty($order_id) && !empty($image)) { 
                $file_error =$file_id=0;
                $path = 'assets/uploads/order/';
                $files = [];

                if (!empty($_FILES['image']['name'][0])) {
                    //   echo "<pre>"; print_r($_FILES); die;
                    $ImageCount = count($_FILES['image']['name']);
            
                    for ($i = 0; $i < $ImageCount; $i++) {
                        $_FILES['file']['name']     = $_FILES['image']['name'][$i];
                        $_FILES['file']['type']     = $_FILES['image']['type'][$i];
                        $_FILES['file']['tmp_name'] = $_FILES['image']['tmp_name'][$i];
                        $_FILES['file']['error']    = $_FILES['image']['error'][$i];
                        $_FILES['file']['size']     = $_FILES['image']['size'][$i];
            
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
                            'delivery_file'=> $insert_id,
                            'delivery_remark'=>$this->input->post('remark'),
                        ];
                        $gat_id = $this->base_model->select_row('orders',array('id'=>$order_id));
                        if($gat_id){
                        $image_data = $this->base_model->update_data('orders',array('id'=> $order_id),$order_data);
                        if($image_data){
                             $uid = $this->base_model->select_row('orders',array('id'=>$order_id));;
				
                             $activity_type = $this->base_model->activity_type($order_id, $uid->user_id, "7",'',$user_id);
                            
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
}
else {

    $this->response([
        'status' => 200,
        'message' => 'Order id is not match.',
        'data' => []
    ]);
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

public function getDashboardData_get(){ 

    $_POST = json_decode(file_get_contents('php://input'),true);
 
    if ($decodeData = $this->decodeToken()) { 

        $user_id = $distributor_id = $decodeData->data->id;
        
         $delivery_boy = $this->base_model->select_row('delivery_boy', array('id' => $user_id));
       
                if($delivery_boy->is_admin==0){
      
            $unassignOrder = $this->base_model->select_join_result('orders',array('delivere_id ' => $user_id,'status'=>0,'is_new'=>1)); 
          $unassignOrderCount = count($unassignOrder);

          $dispatchedOrder = $this->base_model->select_join_result('orders', array('delivere_id ' => $user_id,'status'=>0,'is_dispatch'=>1)); 
          $dispatchedOrderCount = count($dispatchedOrder);
          
          $pendingOrder = $this->base_model->select_join_result('orders', array('delivere_id ' => $user_id,'status'=>0,'is_hold'=>1));
          $pendingOrderCount = count($pendingOrder); 

          $cancelledOrder = $this->base_model->select_join_result('orders', array('delivere_id ' => $user_id,'status'=>0,'is_cancel'=>1));
          $cancelOrderCount = count($cancelledOrder);

          $todaysSales = $this->base_model->select_join_result('orders', array('delivere_id ' => $user_id,'created_at'=>date('y-m-d'),'is_cancel'=>0));

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
                'delivere_id' => $user_id,
                'is_cancel' => 0,
                'created_at >=' => $firstDayOfMonth,
                'created_at <=' => $lastDayOfMonth
            ));
            foreach ($currentMonthSales as $order) {
                $itemscount = $this->base_model->select_data('order_item',array('order_code' => $order->code)); 
               
                // echo "<pre>"; print_r($itemscount);
                foreach($itemscount as $item) {
              $total = ($item->price * $item->quantity); 
              $currentMonthSalesCount +=$total; } 
           
             
            } 
            $totalSales = $this->base_model->select_join_result('orders', array('delivere_id' => $user_id,'is_cancel'=>0));
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
                'delivere_id' => $user_id,
                'is_cancel' => 0,
                'created_at >=' => $financialYearStart,
                'created_at <=' => $financialYearEnd
            ));
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
           
           $last12Months = [];
           for ($i = 0; $i < 12; $i++) {
               $month = date('Y-m', strtotime("-{$i} months", strtotime($currentDate)));
               $last12Months[] = $month;
           }
          
               $where = array(
                   'orders.delivere_id' => $user_id,
                   'orders.is_cancel' => 0,
                   'orders.created_at >=' => $oneYearAgo,
                   'orders.created_at <=' => $lastDayOfMonth
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

           $todaytotalorders = $this->base_model->select_join_result('orders',array('delivere_id ' => $user_id,'created_at'=>date('Y-m-d')));
         $todaytotalordersCount = count($todaytotalorders);

         $todaysuccessorders = $this->base_model->select_join_result('orders',array('delivere_id ' => $user_id,'created_at'=>date('Y-m-d'),'status'=>1));
         $todaysuccessordersCount = count($todaysuccessorders);

         $todaysHoldOrders = $this->base_model->select_join_result('orders',array('delivere_id ' => $user_id,'created_at'=>date('Y-m-d'),'status'=>0,'is_hold'=>1));
         $todaysHoldOrdersCount = count($todaysHoldOrders);

         $todayCancelOrders = $this->base_model->select_join_result('orders',array('delivere_id ' => $user_id,'created_at'=>date('Y-m-d'),'status'=>0,'is_cancel'=>1));
         $todayCancelOrdersCount = count($todayCancelOrders);

         $todayNewOrders = $this->base_model->select_join_result('orders',array('delivere_id ' => $user_id,'created_at'=>date('Y-m-d'),'status'=>0,'is_new'=>1));
         $todayNewOrdersCount = count($todayNewOrders);

         $totalOrder = $this->base_model->select_join_result('orders',array('delivere_id ' => $user_id));
         $totalOrderCount = count($totalOrder);

         $completOrder = $this->base_model->select_join_result('orders',array('delivere_id ' => $user_id,'status'=>1));
         $completOrderCount = count($completOrder);

         $holdOrder = $this->base_model->select_join_result('orders',array('delivere_id ' => $user_id,'status'=>0,'is_hold'=>1));
         $holdOrderCount = count($holdOrder);

         $cancelOrder = $this->base_model->select_join_result('orders',array('delivere_id ' => $user_id,'status'=>0,'is_cancel'=>1));
         $cancelOrderCount = count($cancelOrder);

         $newOrder = $this->base_model->select_join_result('orders',array('delivere_id ' => $user_id,'status'=>0,'is_new'=>1));
         $newOrderCount = count($newOrder);
         $deliverOrder = $this->base_model->select_join_result('orders',array('delivere_id ' => $user_id,'status'=>1));
         $deliverOrderCount = count($deliverOrder);
                }else{
            $unassignOrder = $this->base_model->select_join_result('orders',array('status'=>0,'is_new'=>1)); 
          $unassignOrderCount = count($unassignOrder);

          $dispatchedOrder = $this->base_model->select_join_result('orders', array('status'=>0,'is_dispatch'=>1)); 
          $dispatchedOrderCount = count($dispatchedOrder);
          
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
               
                'is_cancel' => 0,
                'created_at >=' => $firstDayOfMonth,
                'created_at <=' => $lastDayOfMonth
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
                
                'is_cancel' => 0,
                'created_at >=' => $financialYearStart,
                'created_at <=' => $financialYearEnd
            ));
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
           
           $last12Months = [];
           for ($i = 0; $i < 12; $i++) {
               $month = date('Y-m', strtotime("-{$i} months", strtotime($currentDate)));
               $last12Months[] = $month;
           }
          
               $where = array(
                 
                   'orders.is_cancel' => 0,
                   'orders.created_at >=' => $oneYearAgo,
                   'orders.created_at <=' => $lastDayOfMonth
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
         $deliverOrder = $this->base_model->select_join_result('orders',array('status'=>1));
         $deliverOrderCount = count($deliverOrder);
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
                'deliveryOrder' =>$deliverOrderCount,
                'cancelOrder' => $cancelOrderCount,
                'todaysSales' =>$todaysSalesCount,
                'currentMonthSales' =>$currentMonthSalesCount,
                'totalSales' =>$totalSalesCount,
                'currentfinancialSalesCount'=>$currentfinancialSalesCount,
                'last12MonthsSales'=>$last12MonthsSales
                
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
public function markDeliver_post(){ 

    $_POST = json_decode(file_get_contents('php://input'),true);  
        if ($decodeData = $this->decodeToken()) { 
           
            $jdata = $decodeData->data;
            $user_id = $jdata->id;
            $order_id = $this->input->post('order_id');
             $delivery_boy = $this->base_model->select_row('delivery_boy', array('id' => $user_id));
            if($order_id){ 
               if($delivery_boy->is_admin==0){
                    $where =array(
                        'id'=>$order_id,
                        'delivere_id'=>$user_id,
                        'is_cancel'=>0,
                        'is_hold'=>0
                    );
                   }else{
                      $where =array(
                        'orders.id'=>$order_id,
                        'is_cancel'=>0,
                        'is_hold'=>0
                    ); 
                   }
                $order = $this->base_model->select_row('orders', $where);
                
               
                if($order){ 

                    $data = array(
                        'status' => 1,
                        'is_new' => 0,
                        'is_hold' => 0,
                        'is_cancel' => 0,
                        'is_dispatch' => 0
                    );
                    $where = array(
                        'id'=>$order_id,
                        'is_cancel'=>0,
                        'is_hold'=>0
                    );
                    $orderupdate = $this->base_model->update_data('orders', $where, $data);
                    $notificationData = array(
			                   
			                   'orderId'=>$order_id,
			                   'body'=>"your order has been delivered",
			                   'type'=> 'delivered'
			                      );
			$this->send_notification_data_order($notificationData);
                      $uid = $this->base_model->select_row('orders',array('id'=>$order_id));;
                    $activity_type = $this->base_model->activity_type($order_id, $uid->user_id, "5",'', $user_id);
                   
                    $users = $this->base_model->select_data('users',array(
                        'role' => 1,
                        'fcm_web_token !='=> '',
                    )); 
                    $tokens = array_column($users,"fcm_web_token");
                    if(!empty($tokens)){
                    
                        $this->load->model('FcmModel');
                        $fcmTitle = "Order Delivered!";
                        $fcmBody = "Your order has been delivered successfully.";
                        $fcmLink = "/admin/order";
                        $this->FcmModel->sendNotifictaion($fcmTitle,$fcmBody,$tokens,$fcmLink);
                    
                    }
                    
                    $this->response([
                        'status' => 200,
                        'message' => 'Mark Deliver Successfull.',
                        'data' => []
                        
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


public function getPendingOrders_post()
    { 
        $_POST = json_decode(file_get_contents('php://input'),true);
     
        if ($decodeData = $this->decodeToken()) { 

            $user_id = $distributor_id = $decodeData->data->id;
            $delivery_boy = $this->base_model->select_row('delivery_boy', array('id' => $user_id));
            $columns="orders.*,upload_images.file,distributor.full_name,deliver_attachment.file as deliveFile,delivery_boy.full_name as delivery_boy_name";
            $joins[] = array('table'=>'upload_images','condition'=>'upload_images.id=orders.file_id','jointype'=>'left');
            $joins[] = array('table'=>'distributor','condition'=>'distributor.id=orders.distributor_id','jointype'=>'left');
            $joins[] = array('table'=>'delivery_boy','condition'=>'delivery_boy.id=orders.delivere_id','jointype'=>'left');
            $joins[] = array('table' => 'upload_images as  deliver_attachment', 'condition' => 'deliver_attachment.id = orders.delivery_file', 'jointype' => 'left');
            
        if($delivery_boy->is_admin==0){
                $orders = $this->base_model->select_join_result('orders', array('delivere_id' => $user_id,'orders.is_cancel'=>0,'orders.is_hold'=>0,'orders.status'=>0), $joins, $columns,'','','orders.id DESC');
        }else{
                $orders = $this->base_model->select_join_result('orders', array('orders.is_dispatch'=>1,'orders.is_cancel'=>0,'orders.is_hold'=>0,'orders.status'=>0), $joins, $columns,'','','orders.id DESC');
        }
                $order_data = array();
 
                foreach ($orders as $order) {
                    $approv_by = $this->base_model->select_row('users', array('users.id' => $order->approv_by));

                    if($order->status == 0){
                        $status = 'Dispatch';
                        $status .='Cancel';
                   }elseif($order->status == 1){
                    if(!empty($list->approv_by)){
                        $status = 'Dispatched by ' .  $approv_by->full_name ;
                    }
                    else{
                        $status = 'Dispatched';
                    }
                   }else{
                    if(!empty($order->approv_by)){
                    $status = 'Canceled by'. $approv_by->full_name ;
                    }else{
                        $status = 'Canceled';
                    }
                   }
                
                   $row = $status;
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
                 $deliver_files = array();
                if ($order->deliveFile) {
                    $file = explode(',', $order->deliveFile);
                    foreach ($file as $files) {
                        $deliver_files[] = base_url('assets/uploads/order/') . $files;
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
                
                 if($delivery_boy->is_admin == 1){
                    $party = $order->party_name . ' - (' . $order->delivery_boy_name . ')';
                }else{
                    $party = $order->party_name;
                }
                
                    $order_item = array(
                    'order_id' => $order->id,
                    'party_name' =>$party,
                    'payment_term' => $order->payment_term,
                    'dispached_details' =>$order->dispached,
                    'items' => count($itemscount),
                    'amount'=>$qty,
                    'created_date' =>date('Y-m-d H:i:s',strtotime($order->created_at)),
                    'city'=>$order->city,
                    'remark'=>$order->remark,
                    'delivery_rimark'=>$order->delivery_remark,
                    'file' => $images,
                    'pod'=>$deliver_files,
                     'orderStatus'=>$orderStatus
                        
                    );

                    $order_data[] = $order_item;
                }
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
             $delivery_boy = $this->base_model->select_row('delivery_boy', array('id' => $user_id));
            
            if($order_id){
               if($delivery_boy->is_admin==0){
                $where =array(
                    'orders.id'=>$order_id,
                    'delivere_id'=>$user_id,
                    'is_cancel'=>0,
                    'is_hold'=>0
                );
               }else{
                  $where =array(
                    'orders.id'=>$order_id,
                    'is_cancel'=>0,
                    'is_hold'=>0
                ); 
               }

                $columns="orders.*,upload_images.file,delivery_boy.full_name,distributor.full_name as distributorName,deliver_attachment.file as deliveFile";
                $joins[] = array('table'=>'upload_images','condition'=>'upload_images.id=orders.file_id','jointype'=>'left');
                $joins[] = array('table' => 'upload_images as  deliver_attachment', 'condition' => 'deliver_attachment.id = orders.delivery_file', 'jointype' => 'left');
                $joins[] = array('table'=>'delivery_boy','condition'=>'delivery_boy.id=orders.delivere_id','jointype'=>'left');
                $joins[] = array('table'=>'distributor','condition'=>'distributor.id=orders.distributor_id','jointype'=>'left');
                
                $order = $this->base_model->select_join_row('orders', $where, $joins, $columns);
                

                
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
                $deliver_files = array();
                if ($order->deliveFile) {
                    $files = explode(',', $order->deliveFile);
                    foreach ($files as $file) {
                        $deliver_files[] = base_url('assets/uploads/order/') . $file;
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
                
                
                if($delivery_boy->is_admin == 1 ){
                    $party = $order->party_name . ' - (' . $order->full_name . ')';
                }else{
                    $party = $order->party_name;
                }
                    $order_data = array(
                         'orderId' => $order->id,
                        'partyName' => $party,
                        'paymentTerm' => $order->payment_term,
                        'dispachedDetails' =>$order->remark,
                        'items' => count($itemscount),
                        'amount'=>$total,
                        'createdDate' =>date('Y-m-d H:i:s',strtotime($order->created_at)),
                        'city'=>$order->city,
                        'deliveryBoyName'=>$order->full_name,
                        'distributorName'=>$order->distributorName,
                        'remark'=>$order->remark,
                        'delivery_rimark'=>$order->delivery_remark,
                        'file' =>$images,
                        'pod'=>$deliver_files,
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
  public function logout_post()
    {  
        if ($decodeData = $this->decodeToken()) {

            $jdata = $decodeData->data;
            $user_id = $jdata->id;

            $this->base_model->update_data('delivery_boy', ['id'=>$user_id], ['token' => '']);
            
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
    
  
    
}
