<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Cron extends Base_Controller {
	
   public function __construct(){ 
	    parent::__construct(); 
	    $this->load->database();
		$this->load->model('base_model');
		$this->load->model('api_model');
		$this->load->library('form_validation');
		
		date_default_timezone_set('Asia/Kolkata');
	}

	public function send_email(){
		
		return true;
		$orders = $this->base_model->select_data('orders',array('is_email'=>0));
// 			echo"<pre>";print_r($orders);
		$receipts = $this->base_model->select_data('receipt',array('status' =>1));
		if(count($orders) > 0){
		foreach($orders as $order){
		    
            $id= $order->id;
              $updateData = array("is_email" => 1);
			 $whereorder = array('id' => $order->id);
			$oid=$this->base_model->update_data('orders', $whereorder, $updateData);
			
			$edata['distributor'] = $distributor =  $this->base_model->select_row('distributor',array('id' => $order->distributor_id));
		 if(!$distributor){
		       continue;
		   }
                $edata['order'] = $order;
			$edata['items'] = $items =  $this->base_model->select_data('order_item',array('order_code' => $order->code));
			if($items){
			 $qty=0;
                foreach($items as $item){
                $total = $item->price * $item->quantity;
               
                $qty +=$total; 
               
                }
			
			
			$where = array('order_code' => $order->code);
			$joins = array();
			$columns = "SUM(price) as total_price";
			$edata['totalprice'] =  $this->base_model->select_join_row('order_item',$where,$joins,$columns);
			$columns1 = "SUM(quantity) as total_qty";
			$edata['totalqty'] =  $this->base_model->select_join_row('order_item',$where,$joins,$columns1);
			$edata['total'] =  $qty;

			$subject = 'GENICS ORDER : #'. $order->id.'-'.strtoupper($order->party_name).'-'.date('d/m/Y',strtotime($order->created_at));
			$body = $this->load->view('email_template/order',$edata,true);
			
			foreach($receipts as $receipt){
				$to_email = $receipt->email;
				$from_email =  $this->config->item('front_email');
		    	$this->send_mail($body,$to_email,$subject);
			}
			  $distribute_mail= $distributor->email;
			 
			  $this->send_mail($body,$distribute_mail,$subject);
	   	    
		}
		}
		 	echo "success"; 
		}else{
		   	echo "Data Not Found"; 
		}
		


		
	}
	

		public function send_sms(){
		   return true;
		    	$orders = $this->base_model->select_data('orders',array('is_sms'=>0));
		    		if(count($orders) > 0){
		    	foreach($orders as $order){
		    	     $items= $this->base_model->select_data('order_item',array('order_code' => $order->code));
		    	 $updateData = array("is_sms" => 1);
			    $where = array('id' => $order->id);
                $this->base_model->update_data('orders', $where, $updateData);    
               	if($items){
                $qty=0;
                foreach($items as $item){
                $total = $item->price * $item->quantity;
               
                $qty +=$total; 
               
                }
        
                $seller_data = $this->base_model->select_row('distributor',array('id' => $order->distributor_id));
            
                $name=$order->party_name;
                $party_name =  $this->base_model->select_row('users',array('full_name' => $name));
               
                $seller_phone= $party_name->phone;
               $apiKey = '3231656e69637335353554';
               $senderId = 'GENICS';
               $tempIdOtp = '1707168775407868692';
               $tempIdOrder= '1707168775448006324';
               $mobile_number =  $seller_phone;
               $countryCode  = '91';
               $route  = '2';
                $orderid = base64_encode($order->id);
          
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
                    $orderurl= 'order';
                    $short_link = $website .$orderurl.'/'.$orderid; 
               
                $message_content = 'Hi,\nYour order has been placed to Genics Techsol Pvt Ltd and order amount is Rs. '.$qty.' to review order : '. $short_link.'\n\nGenics Team';   
            } 
          
            $url ="http://control.yourbulksms.com/api/sendhttp.php?authkey=$apiKey&mobiles=$mobile_number&sender=$senderId&route=$route&country=$countryCode&DLT_TE_ID=$tempIdOrder&message=".urlencode($message_content);
               $ch = curl_init($url);
               curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
               $response = curl_exec($ch);
               curl_close($ch);
                 
		
		    }
		    	}
		     	echo "success";
		}else{
		   	echo "Data Not Found"; 
		}
		    
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
	
	
	public function order_placed()
	{
        $columns="orders.*,upload_images.file";
        $joins[] = array('table'=>'upload_images','condition'=>'upload_images.id=orders.distributor_attachment','jointype'=>'left');
		$orders = $this->base_model->select_join_result('orders', array('is_new'=>1,'order_webapi' =>0),$joins,$columns);
		if (count($orders) > 0) {
		    foreach($orders as $order){
		        $order_id = $order->id;
			$name = $order->party_name;
			$amount = $order->amount;
 			$number = $order->number;
 			//echo $number;die;
		//	$number = 9340295610;
			$query1 = "Niranjan vani";
			$query2 = "9669522000";
		$order_url=  $short_link = base_url('order').'/'.base64_encode($order->id); 
		
      if($order->file){
         $files= explode(',',$order->file);
         foreach($files as $file){
             
             $images[]=base_url('assets/uploads/order/').$file;
             
         }
       
      }
      
     // echo '<pre>';print_R($images);die;
     

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://api.interakt.ai/v1/public/message/',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => '{
    "countryCode": "+91",
    "phoneNumber": "' . $number . '",
    "callbackData": "some text here",
    "type": "Template",
    "template": {
        "name": "genics_order_received",
        "languageCode": "en",
        "bodyValues": [
            "' . $name . '",
            "' . $order_id . '",
            "' . $amount.'"
        ],
        "buttonValues":{ 
            "0":["' .$order_url.'"]
        }
    }
}',
			CURLOPT_HTTPHEADER => array(
				'Authorization: Basic WmkzbUFtR3Z3Uk1hbS1nX3dVUmpFd2t4WXVMd2NWWXVSbkZlY3dVUjFvVTo=',
				'Content-Type: application/json'
			),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		echo $response;
		$updateData = array("order_webapi" => 1);
	    $where = array('id' => $order->id);
        $this->base_model->update_data('orders', $where, $updateData);
        
	}
	}else{
	    echo 'Data not found';
	}
	}
	
	public function order_delivered()
	{
	     $columns="orders.*,upload_images.file";
         $joins[] = array('table'=>'upload_images','condition'=>'upload_images.id=orders.delivery_file','jointype'=>'left');
	 	 $orders = $this->base_model->select_join_result('orders', array('status'=>1,'delivered_webapi' => 0),$joins,$columns);
	 	if (count($orders) > 0) {
		    foreach($orders as $order){
			$order_id = $order->id;
			
			$name = $order->party_name;
			$amount = $order->amount;
 			$number = $order->number;
		//	$number = 9340295610;
			$query1 = "Niranjan vani";
			$query2 = "9669522000";
		$images= array();
      if($order->file){
         $files= explode(',',$order->file);
         foreach($files as $file){
             
             $images[]=base_url('assets/uploads/order/').$file;
             
         }
       
      }
 $order_url=  $short_link = base_url('order').'/'.base64_encode($order->id); 
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://api.interakt.ai/v1/public/message/',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => '{
			"countryCode": "+91",
			"phoneNumber": "' . $number . '",
			"callbackData": "some text here",
			"type": "Template",
			"template": {
				"name": "genics_order_delivered",
				"languageCode": "en",
				  "headerValues": [
            "'.$images[0].'"
        ],
        "fileName": "Proof of Delivery",
				"bodyValues": [
					"' . $name . '",
					"' . $order_id . '",
					"' . $query1 . '",
					"' . $query2. '"
				],
				"buttonValues":{ 
            "0":["'.$order_url.'"]
        }
			}
		}',
			CURLOPT_HTTPHEADER => array(
				'Authorization: Basic WmkzbUFtR3Z3Uk1hbS1nX3dVUmpFd2t4WXVMd2NWWXVSbkZlY3dVUjFvVTo=',
				'Content-Type: application/json'
			),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		echo $response;
		$updateData = array("delivered_webapi" => 1);
	    $where = array('id' => $order->id);
        $this->base_model->update_data('orders', $where, $updateData);
       
	}
		}else{
	    echo 'Data not found';
	}
}

	

	public function order_dispatch()
	{
        $columns="orders.*,upload_images.file";
        $joins[] = array('table'=>'upload_images','condition'=>'upload_images.id=orders.file_id','jointype'=>'left');
		$orders = $this->base_model->select_join_result('orders', array('is_dispatch'=>1,'dispatch_webapi' =>0),$joins,$columns);
		if (count($orders) > 0) {
		    foreach($orders as $order){
		    $distributor =  $this->base_model->select_row('distributor',array('id' => $order->distributor_id));   
			$order_id = $order->id;
			$name = $order->party_name;
			$amount = $order->amount;
				$invoice_number = $order->invoice_number;
			$number = $order->number;
		//	$number = 9340295610;
			$date = $order->created_at;
			$query1 = $distributor->full_name;
			$query2 = $distributor->phone;
		$images= array();
      if($order->file){
         $files= explode(',',$order->file);
         foreach($files as $file){
             $images[]=base_url('assets/uploads/order/').$file;
         }
       
      }
        $order_url=  $short_link = base_url('order').'/'.base64_encode($order->id); 
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://api.interakt.ai/v1/public/message/',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => '{
    "countryCode": "+91",
    "phoneNumber": "'.$number.'",
    "callbackData": "some text here",
    "type": "Template",
    "template": {
        "name": "genics_order_dispatch",
        "languageCode": "en",
         "headerValues": [
            "'.$images[0].'"
        ],
        "fileName": "Invoice",
        "bodyValues": [
            "'.$name.'",
            "'.$order_id.'",
            "'.$invoice_number.'",
            "'.$date.'",
            "'.$amount.'",
            "'.$query1.'",
            "'.$query2.'"
        ],
        "buttonValues":{ 
            "0":["'.$order_url.'"]
        }
    }
}',
			CURLOPT_HTTPHEADER => array(
				'Authorization: Basic WmkzbUFtR3Z3Uk1hbS1nX3dVUmpFd2t4WXVMd2NWWXVSbkZlY3dVUjFvVTo=',
				'Content-Type: application/json'
			),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		echo $response;
		$updateData = array("dispatch_webapi" => 1);
	    $where = array('id' => $order->id);
        $this->base_model->update_data('orders', $where, $updateData);
        
	}
		}else{
	    echo 'Data not found';
	}
	}
	
	
	
	
		public function order_wp_test()
	{
        $columns="orders.*,upload_images.file";
        $joins[] = array('table'=>'upload_images','condition'=>'upload_images.id=orders.distributor_attachment','jointype'=>'left');
		$orders = $this->base_model->select_join_result('orders', array('orders.id' =>2),$joins,$columns);
		if (count($orders) > 0) {
		    foreach($orders as $order){
		        $order_id = $order->id;
			$name = $order->party_name;
			$amount = $order->amount;
 			$number = 8878456272;
 			//echo $number;die;
		//	$number = 9340295610;
			$query1 = "Niranjan vani";
			$query2 = "9669522000";
		$order_url=  $short_link = base_url('order').'/'.base64_encode($order->id); 
		
      if($order->file){
         $files= explode(',',$order->file);
         foreach($files as $file){
             
             $images[]=base_url('assets/uploads/order/').$file;
             
         }
       
      }
      
     // echo '<pre>';print_R($images);die;
     

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://api.interakt.ai/v1/public/message/',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => '{
    "countryCode": "+91",
    "phoneNumber": "' . $number . '",
    "callbackData": "some text here",
    "type": "Template",
    "template": {
        "name": "genics_order_received",
        "languageCode": "en",
        "bodyValues": [
            "' . $name . '",
            "' . $order_id . '",
            "' . $amount.'"
        ],
        "buttonValues":{ 
            "0":["' .$order_url.'"]
        }
    }
}',
			CURLOPT_HTTPHEADER => array(
				'Authorization: Basic WmkzbUFtR3Z3Uk1hbS1nX3dVUmpFd2t4WXVMd2NWWXVSbkZlY3dVUjFvVTo=',
				'Content-Type: application/json'
			),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		echo $response;
		$updateData = array("order_webapi" => 1);
	    $where = array('id' => $order->id);
      //  $this->base_model->update_data('orders', $where, $updateData);
        
	}
	}else{
	    echo 'Data not found';
	}
	}
	
// 	  function send_notification_data_order(){
        
//         $orderId = 842;
//         $order = $this->base_model->select_row('orders', array('id' => $orderId));
//         $deliveryId = $this->base_model->select_row('distributor', array('id' => $order->distributor_id));
        
//         if (empty($deliveryId->device_token)) {
//         return false;
//         }
        
//         $data = array(
//         'to' => $deliveryId->device_token,
//         'notification' => array(
//             'body' => 'You have received new order #' . $orderId,
//             'title' => 'Order Assigned',
//             'content_available' => true,
//             'priority' => 'high',
//             'sound' => 'default',
//         ),
//         'data' => array(
//             'id' => $orderId,
//             'type'=>'unassigned'
//         ),
//         );
        
//         $curl = curl_init();
        
//         curl_setopt_array($curl, array(
//         CURLOPT_URL => 'https://fcm.googleapis.com/fcm/send',
//         CURLOPT_RETURNTRANSFER => true,
//         CURLOPT_ENCODING => '',
//         CURLOPT_MAXREDIRS => 10,
//         CURLOPT_TIMEOUT => 0,
//         CURLOPT_FOLLOWLOCATION => true,
//         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//         CURLOPT_CUSTOMREQUEST => 'POST',
//         CURLOPT_POSTFIELDS => json_encode($data), // Encode data as JSON
//         CURLOPT_HTTPHEADER => array(
//             'Content-Type: application/json',
//             'Authorization: key=AAAAzR6YLsA:APA91bHfGTj3KZ8Rwkd3x_5BmpfFMzgMaWE_Yf99dct0t8eZacgmYsl40SoDljjlFvkpgBW_3xjpykS_Km53WX1Nc4B-6KHS8vDeSahfLtXGlXV11fGuJN4sfWSWNxH3pjZT54hFs8H5',
//         ),
//         ));
        
//         $response = curl_exec($curl);
        
//         curl_close($curl);
        
//         echo $response;
//         }

    function send_notification_data_order(){
        $notificationData = array(
			                   'orderId'=>1245,
			                   'body'=>"your order has been dispatched",
			                   'type'=> 'dispatched'
			                      );
       
        $order = $this->base_model->select_row('orders', array('id' => $notificationData['orderId']));
        
         $where = '(id = '.$order->distributor_id.' OR is_admin=1)'; 
       $distributorIds = $this->base_model->select_data('distributor', $where);
        $distributor = $this->base_model->select_row('distributor', array('id' => $order->distributor_id));
       //echo '<pre>';print_R($distributorIds);die;
       
        
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
                    'body' =>$body,
                    'title' =>  'Order#' . $notificationData['orderId'],
                    'content_available' => true,
                    'priority' => 'high',
                    'sound' => 'default',
                ),
                'data' => array(
                    'id' => $notificationData['orderId'],
                    'type' =>$notificationData['type']
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