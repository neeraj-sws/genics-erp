<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Distributor_Order_History extends Base_Controller {

    public function __construct(){ 
		parent::__construct();
		$this->load->model('Base_model');
		$this->load->model('Site_model');
		 $this->load->model('login_model');
		$this->load->library('session');
		$this->load->library('input');
	}
	
	
	 public function index()
	{  
		
    //    echo "<pre>";print_r($this->session->userdata('id));die;
        if(!$this->session->userdata('is_user_login')){ redirect(base_url('order-login')); }
        $data['code'] = $this->base_model->generate_code(5);
        // $data['party'] = $this->base_model->select_data('orders',array());
        $data['distributor_id'] = $this->session->userdata('id');
        $data['party_name'] = $customers =  $this->base_model->select_asc_data('orders',array('orders.distributor_id'=>$this->session->userdata('id')),$group_by="party_name");
		$partycount = '';
        foreach($customers as $customer){
            $party = $customer->party_name;				


            $where =  array('orders.distributor_id'=>$this->session->userdata('id'));				
             $party_name =$this->base_model->select_data('orders',$where);
             $partycount[$customer->id] = count($party_name);
            
            
        }

// echo"<pre>";print_r( $partycount);die;
    
    $data['partycount'] =$partycount;
        $data['buttonheading'] = 'Order History';
		
		$this->loadUserTemplate('front/distributor_order_history_view',$data);
		
	}
	 public function get_order_page()
	{  
   
        if(!$this->session->userdata('is_user_login')){ redirect(base_url('order-login')); }
        $data['code'] = $this->base_model->generate_code(5);
        $data['party'] = $this->base_model->select_data('orders',array());
        $data['distributor_id'] = $this->session->userdata('id');
       
        $data['buttonheading'] = 'Order History';
		
		$this->load->view('front/distributor_order_history_view',$data);
		
	}
	
    public function login()
	{ 
        if($this->session->userdata('is_user_login')){ redirect(base_url('/'));  }
        $data['code'] = $this->base_model->generate_code(5);
        $data['buttonheading'] = 'Login';
	    $this->loadUserTemplate('front/login',$data);
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
    
  
    public function  order_ajax_list(){ 
		$requestData= $_REQUEST;	
		// echo"<pre>";print_r( $requestData);die;
		$where_in = $where_like = $where_condition1 = $where_condition2 = $where_condition3 = $where_condition4 =$where_condition5 =$where_condition6= $where1 =array();
		if(isset($_POST['searchName']) && !empty($_POST['searchName'])){
			$where_like = array('orders.party_name'=>$_POST['searchName']);
		}
		$dateSedule = $_POST['datePicker'];
		
			if(!empty($dateSedule)) { 
				$packagetime = explode('/', $dateSedule);
			$start_time = $packagetime[0];
			$end_time = $packagetime[1];
				$where_condition1 = array('orders.created_at >='=>$start_time,'orders.created_at <='=>$end_time);
				
			   }
			//    $distributorselect = $_POST['distributor_select'];


			   if(!empty($distributorselect)) { //echo"n";die;
				$where_condition2 = array('orders.distributor_id '=>  $distributorselect);
				
			   }
			
			   $categorys = $this->base_model->select_data('category',array());
			   $distributor = $this->base_model->select_row('distributor',array('id'=>$this->session->userdata('id'))); 
			   $where1 =array();
			   if($distributor){ 
			   if($distributor->is_admin==0){ 
				foreach ($categorys as $category) {
					if ($distributor->category == $category->id && $category->title == 'in_house') {
						$where1 = '(distributor_other = ' . $this->session->userdata('id') . ' OR distributor_id = ' . $this->session->userdata('id') . ')';
					} elseif ($distributor->category == $category->id && $category->title == 'third_party') {
						$where1 = '(distributor_third_party = ' . $this->session->userdata('id') . ')';
					}
				} 
			   if (!empty( $_POST['statusvalue']) &&  $_POST['statusvalue'] == 'unassign') {
				$where_condition = ' AND orders.is_new = 1 AND orders.status= 0';
			   }elseif(!empty( $_POST['statusvalue']) &&  $_POST['statusvalue'] == 'dispatched'){
				$where_condition = ' AND orders.is_dispatch = 1 AND orders.status= 0';
			   }elseif(!empty( $_POST['statusvalue']) &&  $_POST['statusvalue'] == 'pending'){
				$where_condition = ' AND orders.is_hold = 1 AND orders.status= 0';
			   }elseif(!empty( $_POST['statusvalue']) &&  $_POST['statusvalue'] == 'cancel'){
				$where_condition = ' AND orders.is_cancel = 1 AND orders.status= 0';
			   }elseif(!empty( $_POST['statusvalue']) &&  $_POST['statusvalue'] == 'deliver'){
				$where_condition = ' AND orders.status= 1';
			   } else {
				$where_condition = '';
			   }
			  }elseif($distributor->is_admin==1){ 
				$where_condition = '';
				if (!empty( $_POST['statusvalue']) &&  $_POST['statusvalue'] == 'unassign') {
					$where1 = '(orders.status = ' . 0 . ' AND orders.is_new = ' . 1 . ')';
				   }elseif(!empty( $_POST['statusvalue']) &&  $_POST['statusvalue'] == 'dispatched'){
					$where1 = '(orders.status = ' . 0 . ' AND orders.is_dispatch = ' . 1 . ')';
				   }elseif(!empty( $_POST['statusvalue']) &&  $_POST['statusvalue'] == 'pending'){
					$where1 = '(orders.status = ' . 0 . ' AND orders.is_hold = ' . 1 . ')';
				   }elseif(!empty( $_POST['statusvalue']) &&  $_POST['statusvalue'] == 'cancel'){
					$where1 = '(orders.status = ' . 0 . ' AND orders.is_cancel = ' . 1 . ')';
				   }elseif(!empty( $_POST['statusvalue']) &&  $_POST['statusvalue'] == 'deliver'){
				
					$where1 = '(orders.status = ' . 1 . ')';
				   } else {
					$where1 = '';
				   }
			  }
			  else{ 
				$distributor = $this->base_model->select_row('distributor',array('id'=>$this->session->userdata('id'))); 

				$selected_admin =  $distributor->selected_admin;
				  $category = $this->base_model->select_row('category',array('id'=>$distributor->category));
				   
				if($distributor->category == $category->id && $category->title == 'in_house') { 
				
				 $where1 = '(distributor_other = '.$this->session->userdata('id').' OR distributor_id = '.$this->session->userdata('id').' OR distributor_id IN ('.$selected_admin.') OR distributor_other IN ('.$selected_admin.') OR distributor_third_party IN ('.$selected_admin.'))';
			
				}elseif($distributor->category == $category->id && $category->title == 'third_party'){
				  $where1 = ' (distributor_third_party = '.$this->session->userdata('id').' OR distributor_id = '.$this->session->userdata('id').' OR distributor_id IN ('.$selected_admin.') OR distributor_other IN ('.$selected_admin.') OR distributor_third_party IN ('.$selected_admin.'))';
				  
				} 
			   if (!empty( $_POST['statusvalue']) &&  $_POST['statusvalue'] == 'unassign') {
				$where_condition = ' AND orders.is_new = 1 AND orders.status= 0';
			   }elseif(!empty( $_POST['statusvalue']) &&  $_POST['statusvalue'] == 'dispatched'){
				$where_condition = ' AND orders.is_dispatch = 1 AND orders.status= 0';
			   }elseif(!empty( $_POST['statusvalue']) &&  $_POST['statusvalue'] == 'pending'){
				$where_condition = ' AND orders.is_hold = 1 AND orders.status= 0';
			   }elseif(!empty( $_POST['statusvalue']) &&  $_POST['statusvalue'] == 'cancel'){
				$where_condition = ' AND orders.is_cancel = 1 AND orders.status= 0';
			   }elseif(!empty( $_POST['statusvalue']) &&  $_POST['statusvalue'] == 'deliver'){
				$where_condition = ' AND orders.status= 1';
			   } else {
				$where_condition = '';
			   }
			  }
			 
			}
			

			   $partyname = $_POST['partyname'];


			   if(!empty($partyname)) { //echo"n";die;
				$where_condition3 = array('orders.party_name '=>  $partyname);
				
			   }
            

		if(isset($_POST['searchtoday']) && !empty($_POST['searchtoday'])){
			$where_today = array('orders.created_at'=>date('Y-m-d'));
		}else{
			$where_today = array();
		}
		
		$where = array_merge($where_condition1,$where_condition2,$where_condition3,$where_condition4,$where_condition5,$where_condition6,$where_today);
		
		$coloum_search = array('orders.party_name','orders.party_name');
		$order_by = array('orders.id'=>'DESC');
		
		if(isset($requestData['order'])){
			if($requestData['order']['0']['column'] == 1){
				$order_by = array('orders.party_name'=>$requestData['order']['0']['dir']);
			}
		}
		
		$columns="orders.*,distributor.full_name,upload_images.file";
		$joins[] = array('table'=>'distributor','condition'=>'distributor.id=orders.distributor_id','jointype'=>'left');
        $joins[] = array('table'=>'upload_images','condition'=>'upload_images.id=orders.distributor_attachment','jointype'=>'left');
		$lists = $this->base_model->get_where_in_lists('orders',$coloum_search,$order_by,'',$where_in,$joins,$columns,'orders.id',$where1.$where_condition,'',$where_like);
		// echo "<pre>";print_r($lists);die;
		$data = array();
		$no = $_POST['start'];
		$status ="";
		if($lists){
			foreach ($lists as $list) {
				
			
				$itemscount = $this->base_model->select_data('order_item',array('order_code' => $list->code));
				$no++;
				$row = array();
				
				$row[] = $no;
               
				$row[] =  ucfirst($list->party_name);
				// $row[] =  $list->party_name;
				// $row[] =  $list->number;
				$row[] =  $list->payment_term;
				if(!empty($list->dispached)){
				      $row[] =  $list->dispached;
				}else{
				      $row[] =  'NA';
				}
              
				if($list->status == 0){
					
                    $status = '<span class="text-white badge badge-warning"><b>Pending</b></span>';

			   }elseif($list->status == 1){
				$status = '<span class="text-white badge badge-success"><b>Dispatched</b></span>';
			   }else{
				$status = '<span class="text-white badge badge-danger"><b>Canceled</b></span>';
			   }
               $count = '<span class="badge badge-dark">'.count($itemscount).'</span>';
			
			   $row[] ='<span class="badge badge-dark">'.$list->created_at.'</span>';
			
			if ($list->distributor_attachment != 0) {
				$files = explode(',', $list->file);
				$add_image = '';
				$delet_image = '<a class="text-muted " onclick="delete_image(' . $list->id . ')"><i style="font-size:15px;color:red" class="fa fa-close"></i></a>';
				foreach ($files as $file) {
					$image_url = base_url('assets/uploads/order/') . $file;
					$file_extension = pathinfo($file, PATHINFO_EXTENSION);
                              
					if (strtolower($file_extension) === 'pdf') {
						$icon_class = 'fa-solid fa-file-pdf'; 
					}elseif(strtolower($file_extension) === 'doc'|| strtolower($file_extension) === 'docx'){
					   $icon_class ='fa-solid fa-file-doc';
					}elseif(strtolower($file_extension) === 'jpg'||strtolower($file_extension) === 'png'||strtolower($file_extension) === 'jpeg'||strtolower($file_extension) === 'webp'||strtolower($file_extension) === 'svg'){
					   $icon_class = 'far fa-image'; 
					} else {
						$icon_class ='fa-solid fa-file';
					}
					// $add_image .= '<img onclick="view_image(' . $list->id . ')" src="' . $image_url . '" class="img-fluid" width="40px" />';
					$add_image .= '<a href="javaScript:void(0)" onclick="view_image(' . $list->id . ')"><i class="' . $icon_class . '" style="color: black;"></i></a>'.'<br>';;

					
				}
			} else {
				$add_image = '<button type="button" class="btn btn-success btn-sm" onclick="add_image(' . $list->id . ')">Add</button>';
				$delet_image = '';
			}
			
			$row[] = $add_image . $delet_image;
			
			if($list->is_hold == 1){
            $new = '<button type="button" class="btn btn-success btn-sm" onclick="is_new('.$list->id.')">New</button>';
			}else{
				$new ='';
			}
			$row[] = $new;
			   $buttons = '<button type="button" class="btn btn-success btn-sm" onclick="view_order('.$list->id.')">Detail</button>';
			   $row[] = $buttons;
	        $data[] = $row;

			}
		}
		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->base_model->count_all_where_in('orders',$where_in,'orders.id',$where1.$where_condition,$joins),
			"recordsFiltered" =>$this->base_model->count_filtered_where_in('orders',$coloum_search,$order_by,'',$where_in,$joins,$columns,'orders.id',$where1.$where_condition),
			"data" => $data,
		);

		echo json_encode($output);
	}

    public function view_order(){ 
		$where = array('id'=>$this->input->post('id'));
		$data['single'] = $single = $this->base_model->select_row('orders',$where);		
		$where1 = array('order_code'=>$single->code);		
		$data['items'] = $this->base_model->select_data('order_item',$where1);	
		
		// $columns="order_item.*,SUM(order_item.quantity) as totalqty";
		// $joins = array();
		// $qty = $this->base_model->select_join_row('order_item',$where,$joins,$columns);
		// echo"<pre>";print_r($qty);die;
		$this->load->view('front/view',$data);
	}
    public function add_image(){ 
       
        $data['id'] = $this->input->post('id');
		$this->load->view('front/add_image',$data);
	}
    public function delete_image(){ 
        $data =array(
            'distributor_attachment'=>0
        );
        $uid = $this->base_model->update_data('orders',array('id'=>$this->input->post('id')),$data);
        $res =array('status'=>1);
        echo json_encode($res);die;
	}
    public function view_image(){
        $where = array('orders.id'=>$this->input->post('id'));
        $columns="orders.*,upload_images.file";
		
        $joins[] = array('table'=>'upload_images','condition'=>'upload_images.id=orders.distributor_attachment','jointype'=>'left'); 
       $data['images']= $this->base_model->select_join_row('orders',$where,$joins,$columns);
        $data['id'] = $this->input->post('id');
		$this->load->view('front/view_image',$data);
	}
    public function users_image_data()
	{  
		$res = array();
		$file_error = 0;
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
				$insert_id = $this->base_model->insert_data('upload_images', $data);
				$image_data = $this->base_model->select_row('upload_images', array('id' => $insert_id));
				// $res = array('status' => 1, 'image_id' => $insert_id, 'image_data' => base_url() . $path . $image_data->file);

				$res = array(
					'status' => 1,
					'image_id' => $insert_id,
					'image_data' => base_url() . $path . $image_data->file,
					'multiple_images' => array_map(function ($imageName) use ($path) {
						return base_url() . $path . $imageName;
					}, explode(',', $image_data->file))
				);


				echo json_encode($res);
				die;
			} else {
				$res = array('status' => 0, 'msg' => $m);
				echo json_encode($res);
				die;
			}
		}

	}
    public function user_save()
	{
		
	
		$this->form_validation->set_rules('file_id', 'Image', 'required');
	
		
		if($this->form_validation->run() == FALSE){
			$m = json_encode($this->form_validation->error_array());
			$res =array('status'=>0,'msg'=>$m);
			echo json_encode($res);die;
		}else{ 
			
			$data =array(
				
				'distributor_attachment'=>trim($this->input->post('file_id')),
				
			);
            // echo "<pre>"; print_r($data);
			$uid = $this->base_model->update_data('orders',array('id'=>$this->input->post('id')),$data);
			$uid = $this->base_model->select_row('orders',array('id'=>$this->input->post('id')));;
				
			$oid = $this->input->post('id');
			$activity_type = $this->base_model->activity_type($oid, $uid->user_id, "7",$this->session->userdata('id'),'');
			$res =array('status'=>1);
			echo json_encode($res);die;

		}

	}	
    public function add_remark(){ 
       $data['remark'] = $this->base_model->select_row('orders',array('id'=>$this->input->post('id')));
			// echo "<pre>"; print_r($data);die;
        $data['id'] = $this->input->post('id');
		$this->load->view('front/add_remark',$data);
	}
    public function remark_save()
	{
		
	
		$this->form_validation->set_rules('remark', 'Remark', 'required');
	
		
		if($this->form_validation->run() == FALSE){
			$m = json_encode($this->form_validation->error_array());
			$res =array('status'=>0,'msg'=>$m);
			echo json_encode($res);die;
		}else{ 
			
			$data =array(
				
				'distributor_remark'=>trim($this->input->post('remark')),
				
			);
            // echo "<pre>"; print_r($data);
			$uid = $this->base_model->update_data('orders',array('id'=>$this->input->post('id')),$data);
			$res =array('status'=>1);
			echo json_encode($res);die;

		}

	}
	public function is_new(){
			
		$data = array(
			 'is_new'=>1,
			 'is_hold'=>0
			);
			// echo "<pre>"; print_r($data);die;
			$this->base_model->update_data('orders',array('id'=>$this->input->post('id')),$data);
			$notificationData = array(
			                   
			                   'orderId'=>$this->input->post('id'),
			                   'body'=>"your order has been unassigned",
			                   'type'=> 'unassigned'
			                      );
			$this->send_notification_data_order($notificationData);
			$uid = $this->base_model->select_row('orders',array('id'=>$this->input->post('id')));;
			
			$oid = $this->input->post('id');
			$activity_type = $this->base_model->activity_type($oid, $uid->user_id, "6",$this->session->userdata('id'),'');
			$res =array('status'=>1);
			echo json_encode($res);die;
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