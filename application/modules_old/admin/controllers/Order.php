<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
require_once 'vendor/autoload.php';
class Order extends Base_Controller {

	public function __construct(){ 
		parent::__construct();
		$this->load->model('base_model');
		$this->load->model('Order_export_Model');
		$this->load->library('session');
	}

    public function index()
	{
		
		if(!$this->session->userdata('is_admin_login')){ redirect(base_url('admin/login'));  }
		$data['distributors'] = $this->base_model->select_asc_data('distributor',array());
		
		$data['party_name'] = $customers =  $this->base_model->select_asc_data('orders',array(),$group_by="party_name");
		$partycount ='';
			foreach($customers as $customer){
				$party = $customer->party_name;				

				
				$where =  array('orders.party_name'=>$party);				
				 $party_name =$this->base_model->select_data('orders',$where);
				 if($party_name){
					$partycount[$customer->id] = count($party_name);
				 }else{
					$partycount = '';
				 }
				 
				
				
			}

// echo"<pre>";print_r( $partycount);die;
		
		$data['partycount'] =$partycount;
		$data['nav'] ='order';
		$data['sub_nav'] ='order';
		$data['title'] ='order';
		// echo"<pre>";print_r( $data);die;
		$this->loadAdminTemplate('order/index',$data);

	}

    public function  order_ajax_list(){ 
		
		$requestData= $_REQUEST;	
		// echo"<pre>";print_r( $requestData);die;
		$where_in = $where_like = $where_condition1 = $where_condition2 = $where_condition3 = $where_condition4 =$where_condition5 =$where_condition6 = $where_condition7 =array();
		if(isset($_POST['searchName']) && !empty($_POST['searchName'])){
			$where_like = array('orders.party_name'=>$_POST['searchName']);
		} 
		
		
		$dateSedule = $_POST['datePicker'];
		
			if(!empty($dateSedule)) { //echo"n";die;
				$packagetime = explode('/', $dateSedule);
			$start_time = $packagetime[0];
			$end_time = $packagetime[1];
				$where_condition1 = array('orders.created_at >='=>$start_time,'orders.created_at <='=>$end_time);
				
			   }
			  
			   $newOrder = $_POST['new'];
			   if(!empty($newOrder)){ 
				$where_condition4 = array('orders.status '=>0,'orders.is_new'=>1);
			   } 
			   $dispatchOrder = $_POST['dispatch'];
			   if(!empty($dispatchOrder)){ 
				$where_condition5 = array('orders.status '=>0,'orders.is_dispatch'=>1);
			   }
			   $holdOrder = $_POST['hold'];
			   if(!empty($holdOrder)){ 
				$where_condition6 = array('orders.status '=>0,'orders.is_hold'=>1);
			   } 
			   $cancelOrder = $_POST['cancel'];
			   if(!empty($cancelOrder)){ 
				$where_condition7 = array('orders.status '=>0,'orders.is_cancel'=>1);
			   }
			   $deliverOrder = $_POST['deliver'];
			   if(!empty($deliverOrder)){ 
				$where_condition7 = array('orders.status '=>1);
			   }
			   $distributorselect = $_POST['distributor_select'];


			   if(!empty($distributorselect)) { //echo"n";die;
				$where_condition2 = array('orders.distributor_id '=>  $distributorselect);
				
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
		
		$where = array_merge($where_condition1,$where_condition2,$where_condition3,$where_condition4,$where_condition5,$where_condition6,$where_condition7,$where_today);
		
		$coloum_search = array('orders.party_name','orders.party_name');
		$order_by = array('orders.id'=>'DESC');
		
		if(isset($requestData['order'])){
			if($requestData['order']['0']['column'] == 1){
				$order_by = array('orders.party_name'=>$requestData['order']['0']['dir']);
			}
		}
		
		$columns="orders.*,distributor.full_name,upload_images.file,delivery_boy.full_name as deliveryBoyName";
		$joins[] = array('table'=>'distributor','condition'=>'distributor.id=orders.distributor_id','jointype'=>'left');
		$joins[] = array('table'=>'upload_images','condition'=>'upload_images.id=orders.file_id','jointype'=>'left');
		$joins[] = array('table'=>'delivery_boy','condition'=>'delivery_boy.id=orders.delivere_id','jointype'=>'left');
		$lists = $this->base_model->get_where_in_lists('orders',$coloum_search,$order_by,'',$where_in,$joins,$columns,'orders.id',$where,'',$where_like);
		// echo "<pre>";print_r($lists);die;
		$data = array();
		$no = $_POST['start'];
		if($lists){
			foreach ($lists as $list) {
				
			
				$itemscount = $this->base_model->select_data('order_item',array('order_code' => $list->code));
				$no++;
				$row = array();
				$status_label='';
				$row[] = '<a class="text-white" href="order/detail/'.$list->id.'">'.$no.'</a>';
				$row[] =  $list->party_name;
				// $row[] =  $list->number;
				$row[] =  $list->payment_term;
				if(!empty($list->dispached)){
				      $row[] =  $list->dispached;
				}else{
				      $row[] =  'NAN';
				}
              
                $row[] =   $list->full_name;
              
					if(!empty($list->deliveryBoyName)){
						$row[] =  $list->deliveryBoyName;
				}else{
						$row[] =  'NAN';
				}
				if($list->status == 0 && $list->is_hold == 0 && $list->is_cancel == 0 && $list->is_dispatch == 0){

					$status = '<span class="text-white "><b>Unassign</b></span>';
					$status_label='New';
			   }elseif($list->is_dispatch == 1 && $list->status == 0 ){
			
					$status = '<span class="text-white "><b>Dispatched</b></span>';
					$status_label='Dispatched';
			   }elseif($list->status == 0 && $list->is_hold == 1){
		
					$status = '<span class=" "><b>Pending</b></span>'.' '.'<a class=" badge badge-success" onclick="show_resion('.$list->id.')" href="javaScript:void(0);"  ><i class="fa fa-eye" aria-hidden="true"></i></a>';
					$status_label='Hold';
			   }elseif($list->status == 0 && $list->is_cancel == 1){

					$status = '<a class="text-white " onclick="show_resion('.$list->id.')" href="javaScript:void(0);" ><b>Canceled</b></a>'.' '.'<a class=" badge badge-success" onclick="show_resion('.$list->id.')" href="javaScript:void(0);"  ><i class="fa fa-eye" aria-hidden="true"></i></a>';
					$status_label='Canceled';
			   }elseif($list->status == 1 ){
			
				$status = '<span class="text-white green  "><b>Deliver</b></span>';
				$status_label='Deliver';
		   }else{
				$status = '';
			   }
			   $row[] = $status;
			   $row[] = '<span class="badge badge-dark">'.count($itemscount).'</span>';
               $row[] =   '<span class="badge badge-dark">'.$list->created_at.'</span>';
			   $buttons = '<a href="order/detail/'.$list->id.'">'.'<button type="button" class="btn btn-success btn-xs"><i class="fa fa-eye" aria-hidden="true"></i></button>'.'</a>';
			   $row[] = $buttons;

                $row[] = $status_label;
	        $data[] = $row;

			}
		}
		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->base_model->count_all_where_in('orders',$where_in,'orders.id',$where,$joins),
			"recordsFiltered" =>$this->base_model->count_filtered_where_in('orders',$coloum_search,$order_by,'',$where_in,$joins,$columns,'orders.id',$where),
			"data" => $data,
		);

		echo json_encode($output);
	}

	public function order_instock(){
	if($this->input->post('type')== 1){
			$status=1;
	}else{
		$status =0;
	}
		$this->base_model->update_data('orders',array('id'=>$this->input->post('id')),array('is_stock'=>$this->input->post('type'),'status'=>$status));
		$res =array('status'=>1);
		echo json_encode($res);die;
	}

	public function view_order(){
		$where = array('id'=>$this->input->post('id'));
		$data['single'] = $single = $this->base_model->select_row('orders',$where);		
		$where1 = array('order_code'=>$single->code);		
		$data['items'] = $this->base_model->select_data('order_item',$where1);	
		$data['category']= $category = $this->base_model->select_row('category',array('title'=>'in_house'));	
		$data['distributors'] = $this->base_model->select_data('distributor',array('category' => $category->id));	
		$data['delivery_boys'] = $this->base_model->select_data('delivery_boy',array());	
	
		$this->load->view('order/view',$data);
	}
	public function detail(){ 
		// echo $this->uri->segment(4); die;
		$data['nav'] ='order';
		$data['sub_nav'] ='';
		$data['title'] ='';
		$sagment1 = $this->uri->segment(4);
		$sagment2 = $this->uri->segment(3);
		
		if(!empty($sagment1)){
			$where = array('orders.id'=>$this->uri->segment(4));;
		}else{ 
			$where = array('orders.id'=>$this->uri->segment(3));;
		}
		$columns="orders.*,distributor.full_name as distributorName,delivery_boy.full_name as deliveryBoyName,upload_images.file";
		$joins[] = array('table'=>'distributor','condition'=>'distributor.id=orders.distributor_id','jointype'=>'left');
		$joins[] = array('table'=>'delivery_boy','condition'=>'delivery_boy.id=orders.delivere_id','jointype'=>'left');
		$joins[] = array('table'=>'upload_images','condition'=>'upload_images.id=orders.file_id','jointype'=>'left');
		$data['order_id'] = $this->uri->segment(4);
		$data['single'] = $single = $this->base_model->select_join_row('orders',$where,$joins,$columns);

		$columns1="orders.*,upload_images.file";
		
		$joins1[] = array('table'=>'upload_images','condition'=>'upload_images.id=orders.distributor_attachment','jointype'=>'left');
		$data['distributorAttachment'] = $single = $this->base_model->select_join_row('orders',$where,$joins1,$columns1);
				
		$where1 = array('order_code'=>$single->code);		
		$data['items'] = $this->base_model->select_data('order_item',$where1);	
		$data['category']= $category = $this->base_model->select_row('category',array('title'=>'in_house'));	
		$data['distributors'] = $this->base_model->select_data('distributor',array('category' => $category->id));	
		$data['delivery_boys'] = $this->base_model->select_data('delivery_boy',array());	
		$data['orderReason'] = $this->base_model->select_row('order_reason',array('order_id'=>$this->uri->segment(4)));	
		$data['thirdPartyDistributors'] = $this->base_model->select_row('distributor',array('id' => $single->distributor_other));
		$this->loadAdminTemplate('order/detail',$data);
	}
	public function dispatch(){ 
		// echo $this->uri->segment(4); die;
		$data['nav'] ='';
		$data['sub_nav'] ='';
		$data['title'] ='';
		$where = array('orders.id'=>$this->uri->segment(4));
		$columns="orders.*,distributor.full_name as distributorName,delivery_boy.full_name as deliveryBoyName,upload_images.file";
		$joins[] = array('table'=>'distributor','condition'=>'distributor.id=orders.distributor_id','jointype'=>'left');
		$joins[] = array('table'=>'delivery_boy','condition'=>'delivery_boy.id=orders.delivere_id','jointype'=>'left');
		$joins[] = array('table'=>'upload_images','condition'=>'upload_images.id=orders.file_id','jointype'=>'left');
		$data['order_id'] = $this->uri->segment(4);
		$data['single'] = $single = $this->base_model->select_join_row('orders',$where,$joins,$columns);		
		$where1 = array('order_code'=>$single->code);		
		$data['items'] = $this->base_model->select_data('order_item',$where1);	
		$data['category']= $category = $this->base_model->select_row('category',array('title'=>'in_house'));	
		$data['distributors'] = $this->base_model->select_data('distributor',array('category' => $category->id));	
		$data['delivery_boys'] = $this->base_model->select_data('delivery_boy',array());	
	
		$this->loadAdminTemplate('order/dispatch',$data);
	}

	public function order_status(){
			
		$data['type'] = $this->input->post('type');
		$data['id'] = $this->input->post('id');
		$this->load->view('order/order_reason',$data);
	}
	public function order_status1(){
		if($this->input->post('type')== 1){
				$status=1;
		}else{
			$status =2;
		}
		$data = array(
			'update_at'=>date('Y-m-d'),
			'approv_by'=>$this->session->userdata('id'),
		     'is_dispatch'=>1
			);
			// echo "<pre>"; print_r($data);die;
			$this->base_model->update_data('orders',array('id'=>$this->input->post('id')),$data);
			$res =array('status'=>1);
			echo json_encode($res);die;
		}

		public function is_new(){
			
			$data = array(
				 'is_new'=>1,
				 'is_hold'=>0
				);
				// echo "<pre>"; print_r($data);die;
				$this->base_model->update_data('orders',array('id'=>$this->input->post('id')),$data);
				$res =array('status'=>1);
				echo json_encode($res);die;
			}

	public function createExcel() {
		$fileName = 'Order.xlsx';  
		$employeeData = $this->Order_export_Model->orderList();
		$spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
       	$sheet->setCellValue('A1', 'S.NO.');
        $sheet->setCellValue('B1', 'Party Name');
        $sheet->setCellValue('C1', 'Payment Term');
        $sheet->setCellValue('D1', 'Dispached');
        $sheet->setCellValue('E1', 'Dispached');
	$sheet->setCellValue('F1', 'Status');
         
        $rows = 2;
        $i = 1;
        foreach ($employeeData as $val){
			
			$where = array('id'=>($val['distributor_id']));
			$name =$this->base_model->select_row('distributor',$where);	
			if(!$name){
				$full_name="-";
			}
			else{
			// echo"<pre>";print_r($name);die;
			$full_name= $name->full_name;
			}
			// echo $full_name;die;
			if($val['status']== 0){
				$status= "declined";
			}
			else{
				$status= "delivered";
			}
            $sheet->setCellValue('A' . $rows, $i);
            $sheet->setCellValue('B' . $rows, $val['party_name']);
            $sheet->setCellValue('C' . $rows, $val['payment_term']);
            $sheet->setCellValue('D' . $rows, $val['dispached']);
            $sheet->setCellValue('E' . $rows, $full_name);
	    $sheet->setCellValue('F' . $rows, $status);
           
        
            $rows++;
            $i++;
        } 
        $writer = new Xlsx($spreadsheet);
		// $writer->save("assets/uploads/export".$fileName);
		header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: attacment;filename=$fileName");
		$writer->save('php://output');
        // redirect(base_url()."assets/uploads/export".$fileName);                   
    }  


	public function order_reason_save()
	{
		$this->form_validation->set_rules('order_reason', 'Order Reason', 'required');
		
		if($this->form_validation->run() == FALSE){
			$m = json_encode($this->form_validation->error_array());
			$res =array('status'=>0,'msg'=>$m);
			echo json_encode($res);die;
		}else{ 
			if($this->input->post('type') ==3){
				$this->base_model->update_data('orders',array('id'=>$this->input->post('id')),array('is_cancel'=>1,'is_new'=>0,'is_hold'=>0,'is_dispatch'=>0,'update_at'=>date('Y-m-d'),'approv_by'=>$this->session->userdata('id')));
			}else{
				$this->base_model->update_data('orders',array('id'=>$this->input->post('id')),array('is_hold'=>1,'is_new'=>0,'update_at'=>date('Y-m-d'),'approv_by'=>$this->session->userdata('id')));
			}
		
			

			if($this->input->post('type')== 2){
				$status=1;
			}else{
				$status =2;
			}
			// status 1 is hold and 2 is declined resion
			$data =array(
				'reason'=>trim($this->input->post('order_reason')),
				'order_id'=>trim($this->input->post('id')),
				'role'=> $this->session->userdata('role'),
				'reason_status'=>$status,
				'created_at'=>date('Y-m-d'),
				'updated_at'=>date('Y-m-d')
			);
			
			$uid = $this->base_model->insert_data('order_reason',$data);
			$res =array('status'=>1);
			echo json_encode($res);die;

		}

	}

	public function show_resion(){
		
			$data['ressons']=$resion=$this->base_model->select_data('order_reason',array('order_id'=>$this->input->post('id')));
			$this->load->view('order/show_reason',$data);
	}

	public function assign_delivery_boy(){ 
		$data['delivery_boy'] = $this->base_model->select_data('delivery_boy',array());
		 $data['id'] = $this->input->post('id');
		 $this->load->view('admin/order/assign_delivery_boy',$data);
	 }

	public function assign_delivery_boy_save()
	{
		
		$this->form_validation->set_rules('delivery_boy', 'Delivery Boy ', 'required');
		
		if($this->form_validation->run() == FALSE){
			$m = json_encode($this->form_validation->error_array());
			$res =array('status'=>0,'msg'=>$m);
			echo json_encode($res);die;
		}else{ 
			
			
			
			$data =array(
				'delivere_id'=>$this->input->post('delivery_boy')
				
			);
			
			$uid = $this->base_model->update_data('orders',array('id'=>$this->input->post('id')),$data);
			$res =array('status'=>1);
			echo json_encode($res);die;

		}

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
				$config['allowed_types'] = 'jpg|JPG|png|PNG|jpeg|JPEG|pdf|doc|tiff|docx|webp|avif|svg';
	
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
	public function deliver_image_data(){ 
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
		$this->form_validation->set_rules('distributor', 'Destributor', 'required');	 
		$this->form_validation->set_rules('delivery_boy', 'Delivery Boy', 'required');
		
		if($this->form_validation->run() == FALSE){
			$m = json_encode($this->form_validation->error_array());
			$res =array('status'=>0,'msg'=>$m);
			echo json_encode($res);die;
		}else{ 
			
			$data =array(
				
				'delivere_id'=>trim($this->input->post('delivery_boy')),
				'remark'=>trim($this->input->post('remark')),
				'file_id'=>trim($this->input->post('file_id')),
				'is_dispatch'=>1,
				'is_new'=>0,
				'is_cancel'=>0,
				'is_hold'=>0
				
				
			);
			$oid = $this->base_model->update_data('orders',array('id'=>$this->input->post('order_id')),$data);

			
			
			$distributor =array(
				'distributor_id'=> trim($this->input->post('distributor'))
			); 
			$uid = $this->base_model->select_row('orders',array('id'=>$this->input->post('order_id')));

			$distributor_id = $this->base_model->update_data('users',array('id'=>$uid->user_id),$distributor);
			$res =array('status'=>1);
			echo json_encode($res);die;

		}

	}
	
	public function dispatch_save()
	{
		$this->form_validation->set_rules('distributor', 'Destributor', 'required');	 
		$this->form_validation->set_rules('delivery_boy', 'Delivery Boy', 'required');
		// $this->form_validation->set_rules('remark', 'Remark', 'required]');
		$this->form_validation->set_rules('file_id', 'Attachment', 'required');
		
	
		if($this->form_validation->run() == FALSE){
			$m = json_encode($this->form_validation->error_array());
			$res =array('status'=>0,'msg'=>$m);
			echo json_encode($res);die;
		}else{ 
			
			$data =array(
				
				'delivere_id'=>trim($this->input->post('delivery_boy')),
				'remark'=>trim($this->input->post('remark')),
				'file_id'=>trim($this->input->post('file_id')),
				'is_dispatch'=>1,
				'status'=>1
				
				
			);
			$oid = $this->base_model->update_data('orders',array('id'=>$this->input->post('order_id')),$data);

			
			
			$distributor =array(
				'distributor_id'=> trim($this->input->post('distributor'))
			); 
			$uid = $this->base_model->select_row('orders',array('id'=>$this->input->post('order_id')));

			$distributor_id = $this->base_model->update_data('users',array('id'=>$uid->user_id),$distributor);
			$res =array('status'=>1);
			echo json_encode($res);die;

		}

	}
}   

