<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends Base_Controller {

	 public function __construct(){
	    parent::__construct();
		$this->load->model('base_model');
		$this->load->library('session');
	}

	public function index()
	{
		if(!$this->session->userdata('is_admin_login')){ redirect(base_url('admin/login'));  }
		$data['nav'] ='dashboard';
		$data['sub_nav'] ='';
		$data['title'] ='Dashboard';
		$data['users'] = $this->base_model->select_count_data('users',array('role !='=>1,'status'=>1));
		

		$data['todaytotalorders'] = $this->base_model->select_count_data('orders',array('created_at'=>date('Y-m-d')));
		$data['todaypendingorders'] = $this->base_model->select_count_data('orders',array('created_at'=>date('Y-m-d'),'status'=>0));
		$data['todaysuccessorders'] = $this->base_model->select_count_data('orders',array('created_at'=>date('Y-m-d'),'status'=>1));
		$data['todayholdorders'] = $this->base_model->select_count_data('orders',array('created_at'=>date('Y-m-d'),'status'=>2));
		$data['todaycanceledorders'] = $this->base_model->select_count_data('orders',array('created_at'=>date('Y-m-d'),'status'=>3));
		
		$data['totalorders'] = $this->base_model->select_count_data('orders',array());
		$data['totalsuccessorders'] = $this->base_model->select_count_data('orders',array('status'=>1));
		$data['totalholdorders'] = $this->base_model->select_count_data('orders',array('status'=>2));
		$data['totalcanceledorders'] = $this->base_model->select_count_data('orders',array('status'=>3));
		$data['totalpendingorders'] = $this->base_model->select_count_data('orders',array('status'=>0));

		$data['pending_orders'] = $this->base_model->select_count_data('orders',array('status'=>0));
		$data['pending_todayorders'] = $this->base_model->select_count_data('orders',array('created_at'=>date('Y-m-d'),'status'=>0));
		$data['completed_orders'] = $this->base_model->select_count_data('orders',array('status'=>1));
		$data['completed_todayorders'] = $this->base_model->select_count_data('orders',array('created_at'=>date('Y-m-d'),'status'=>1));
	
		$this->loadAdminTemplate('dashboard',$data);
	}

	public function add_box()
	{
		$data['users'] = $this->base_model->select_count_data('users',array('role !='=>1,'status'=>1));
		$data['distributor'] = $this->base_model->select_count_data('distributor',array('role !='=>1,'status'=>1));
		$data['receipt'] = $this->base_model->select_count_data('receipt',array());
		$data['todaytotalorders'] = $this->base_model->select_count_data('orders',array('created_at'=>date('Y-m-d')));
		$data['todaypendingorders'] = $this->base_model->select_count_data('orders',array('created_at'=>date('Y-m-d'),'status'=>0,'is_new'=>1));
		$data['todaysuccessorders'] = $this->base_model->select_count_data('orders',array('created_at'=>date('Y-m-d'),'status'=>1));
		$data['todayholdorders'] = $this->base_model->select_count_data('orders',array('created_at'=>date('Y-m-d'),'status'=>0,'is_hold'=>1));
		$data['todaycanceledorders'] = $this->base_model->select_count_data('orders',array('created_at'=>date('Y-m-d'),'status'=>0,'is_cancel'=>1));
		
		$data['totalorders'] = $this->base_model->select_count_data('orders',array());
		$data['totalsuccessorders'] = $this->base_model->select_count_data('orders',array('status'=>1));
		$data['totalholdorders'] = $this->base_model->select_count_data('orders',array('status'=>0,'is_hold'=>1));
		$data['totalcanceledorders'] = $this->base_model->select_count_data('orders',array('status'=>0,'is_cancel'=>1));
		$data['totalpendingorders'] = $this->base_model->select_count_data('orders',array('status'=>0,'is_new'=>1));

		$data['pending_orders'] = $this->base_model->select_count_data('orders',array('status'=>0));
		$data['pending_todayorders'] = $this->base_model->select_count_data('orders',array('created_at'=>date('Y-m-d'),'status'=>0));
		$data['completed_orders'] = $this->base_model->select_count_data('orders',array('status'=>1));
		$data['completed_todayorders'] = $this->base_model->select_count_data('orders',array('created_at'=>date('Y-m-d'),'status'=>1));
		$data['deliverOrder'] = $this->base_model->select_count_data('orders',array('status'=>1));
		$data['dispatchOrder'] = $this->base_model->select_count_data('orders',array('status'=>0,'is_dispatch'=>1));
		$data['todayDispatchOrder'] = $this->base_model->select_count_data('orders',array('created_at'=>date('Y-m-d'),'status'=>0,'is_dispatch'=>1));


		$this->load->view('box',$data);
		// $this->loadAdminTemplate('box',array());
	}
	
	
	
	public function resetpassword()
	{
		if(!$this->session->userdata('is_admin_login')){ redirect(base_url('admin/login'));  }
		$data['nav'] ='setting';
		$data['sub_nav'] ='resetpassword';
		$data['title'] ='resetpassword';
		$this->loadAdminTemplate('resetpassword',$data);
	}

	public function update_password_admin(){
		
		$this->form_validation->set_rules('opass', 'Old Password', 'required');
		$this->form_validation->set_rules('new_pass', 'New Password', 'required|min_length[6]');
		$this->form_validation->set_rules('cpass', 'Confirm Password', 'required|matches[new_pass]');
		
		
		if($this->form_validation->run() == FALSE){
			$m = json_encode($this->form_validation->error_array());
			$res =array('status'=>0,'msg'=>$m);
			echo json_encode($res);die;
		}else{ 
			$uinfo = $this->base_model->select_row('users',array('password'=>trim(sha1($this->input->post('opass'))),'id'=>$this->session->userdata('id')));
			// echo (trim(sha1($this->input->post('opass'))));die;
			if(!$uinfo){
				$res =array('status'=>2,'msg'=>'Old Password Not Valid');
				echo json_encode($res);die;
			}
			else{
			$udata =array(
				'password'=>trim(sha1($this->input->post('new_pass'))),
			);
			$this->base_model->update_data('users',array('id'=>$this->session->userdata('id')),$udata);
			$res =array('status'=>1);
			echo json_encode($res);die;
			}
		}

	}
	public function update_site_settings(){
		
		$this->form_validation->set_rules('default_commission', 'Default Commission', 'required');		
		if($this->form_validation->run() == FALSE){
			$m = json_encode($this->form_validation->error_array());
			$res =array('status'=>0,'msg'=>$m);
			echo json_encode($res);die;
		}else{
		    foreach($_POST as $key => $value){
		    	$this->base_model->update_data('admin_setting',array('key'=>$key),array('meta'=>$value));
		    }            
            $res =array('status'=>1);
			echo json_encode($res);die;
		}

	}	

	public function valid_password($password = '')
	    {
	        $password = trim($password);
	        $regex_lowercase = '/[a-z]/';
	        $regex_uppercase = '/[A-Z]/';
	        $regex_number = '/[0-9]/';
	        $regex_special = '/[!@#$%^&*()\-_=+{};:,<.>§~]/';
	        if (empty($password))
	        {
	            $this->form_validation->set_message('valid_password', 'The {field} field is required.');
	            return FALSE;
	        }
	        if (preg_match_all($regex_lowercase, $password) < 1)
	        {
	            $this->form_validation->set_message('valid_password', 'The {field} field must be at least one lowercase letter.');
	            return FALSE;
	        }
	        if (preg_match_all($regex_uppercase, $password) < 1)
	        {
	            $this->form_validation->set_message('valid_password', 'The {field} field must be at least one uppercase letter.');
	            return FALSE;
	        }
	        if (preg_match_all($regex_number, $password) < 1)
	        {
	            $this->form_validation->set_message('valid_password', 'The {field} field must have at least one number.');
	            return FALSE;
	        }
	        if (preg_match_all($regex_special, $password) < 1)
	        {
	            $this->form_validation->set_message('valid_password', 'The {field} field must have at least one special character.' . ' ' . htmlentities('!@#$%^&*()\-_=+{};:,<.>§~'));
	            return FALSE;
	        }
	        if (strlen($password) < 5)
	        {
	            $this->form_validation->set_message('valid_password', 'The {field} field must be at least 5 characters in length.');
	            return FALSE;
	        }
	        if (strlen($password) > 32)
	        {
	            $this->form_validation->set_message('valid_password', 'The {field} field cannot exceed 32 characters in length.');
	            return FALSE;
	        }
	        return TRUE;
	}

	public function get_notification(){
		$count = $this->base_model->select_count_data('notification',array('user'=>$this->session->userdata('id'),'type'=>'admin','is_read'=>0));
		$res = array('count'=>$count->count);
		echo json_encode($res);die;
	}

	public function  order_ajax_list(){ 
		
		$requestData= $_REQUEST;	
		
		$where_in = $where_like = $where_condition1 = $where_condition2 = $where_condition3 = $where_condition4 =$where_condition5 =$where_condition6 =array();
		if(isset($_POST['searchName']) && !empty($_POST['searchName'])){
			$where_like = array('orders.party_name'=>$_POST['searchName']);
		}
		
		$where_today = array('orders.status'=>0);
		$where_condition1 = array('orders.is_cancel  '=>0);
		$where_condition2  = array('orders.is_dispatch '=>0);
		$where = array_merge($where_condition1,$where_condition2,$where_condition3,$where_condition4,$where_condition5,$where_condition6,$where_today);
		
		$coloum_search = array('orders.party_name','orders.party_name');
		$order_by = array('orders.id'=>'DESC');
		
		if(isset($requestData['order'])){
			if($requestData['order']['0']['column'] == 1){
				$order_by = array('orders.party_name'=>$requestData['order']['0']['dir']);
			}
		}
		
		$columns="orders.*,distributor.full_name,delivery_boy.full_name as deliveryBoyName";
		$joins[] = array('table'=>'distributor','condition'=>'distributor.id=orders.distributor_id','jointype'=>'left');
		$joins[] = array('table'=>'delivery_boy','condition'=>'delivery_boy.id=orders.delivere_id','jointype'=>'left');
		$lists = $this->base_model->get_where_in_lists('orders',$coloum_search,$order_by,'',$where_in,$joins,$columns,'orders.id',$where,'',$where_like);
		
		$data = array();
		$no = $_POST['start'];
		if($lists){
			foreach ($lists as $list) {
				
				$itemscount = $this->base_model->select_data('order_item',array('order_code' => $list->code));
				$no++;
				$row = array();
				
				$row[] = '<a class="text-dark" href="admin/detail/'.$list->id.'">'.$no.'</a>';
				$row[] =  $list->party_name;
				$row[] =  $list->payment_term;
                $row[] =  $list->dispached;
				$row[] =   $list->full_name;

				if(!empty($list->deliveryBoyName)){
					$row[] =  $list->deliveryBoyName;
			}else{
					$row[] =  'NAN';
			}

			if($list->status == 0 && $list->is_hold == 0 && $list->is_cancel == 0 && $list->is_dispatch == 0){

				$status = '<span class="text-white  "><b>Unassign</b></span>';
				$status_label='New';
		   }elseif($list->is_dispatch == 1 ){
		
				$status = '<span class="text-white"><b>Dispatched</b></span>';
				$status_label='Dispatched';
		   }elseif($list->status == 0 && $list->is_hold == 1){
	
				$status = '<span class=" "><b>Pending</b></span>'.' '.'<a class=" badge badge-success" onclick="show_resion('.$list->id.')" href="javaScript:void(0);"  ><i class="fa fa-eye" aria-hidden="true"></i></a>';
				$status_label='Hold';
		   }elseif($list->status == 0 && $list->is_cancel == 1){

				$status = '<a class="text-white badge badge-danger" onclick="show_resion('.$list->id.')" href="javaScript:void(0);" ><b>Canceled</b></a>'.' '.'<a class=" badge badge-success" onclick="show_resion('.$list->id.')" href="javaScript:void(0);"  ><i class="fa fa-eye" aria-hidden="true"></i></a>';
				$status_label='Canceled';
		   }else{
			$status = '';
		   }
		   $row[] = $status;
		   $row[] = '<span class="badge badge-dark">'.count($itemscount).'</span>';
		  $row[] =   '<span class="badge badge-dark">'.$list->created_at.'</span>';
		   $buttons = '<a href="admin/detail/'.$list->id.'">'.'<button type="button" class="btn btn-success btn-xs"><i class="fa fa-eye" aria-hidden="true"></i></button>'.'</a>';
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

	public function detail(){ 
		// echo $this->uri->segment(3); die;
		$data['nav'] ='dashboard';
		$data['sub_nav'] ='';
		$data['title'] ='';
		
			$where = array('orders.id'=>$this->uri->segment(3));;
		
		$columns="orders.*,distributor.full_name as distributorName,delivery_boy.full_name as deliveryBoyName,upload_images.file";
		$joins[] = array('table'=>'distributor','condition'=>'distributor.id=orders.distributor_id','jointype'=>'left');
		$joins[] = array('table'=>'delivery_boy','condition'=>'delivery_boy.id=orders.delivere_id','jointype'=>'left');
		$joins[] = array('table'=>'upload_images','condition'=>'upload_images.id=orders.file_id','jointype'=>'left');
		$data['order_id'] = $this->uri->segment(3);
		$data['single'] = $single = $this->base_model->select_join_row('orders',$where,$joins,$columns);
		$columns1="orders.*,upload_images.file";
		
		$joins1[] = array('table'=>'upload_images','condition'=>'upload_images.id=orders.distributor_attachment','jointype'=>'left');
		$data['distributorAttachment'] = $single = $this->base_model->select_join_row('orders',$where,$joins1,$columns1);		
		$where1 = array('order_code'=>$single->code);		
		$data['items'] = $this->base_model->select_data('order_item',$where1);	
		$data['category']= $category = $this->base_model->select_row('category',array('title'=>'in_house'));	
		$data['distributors'] = $this->base_model->select_data('distributor',array('category' => $category->id));	
		$data['delivery_boys'] = $this->base_model->select_data('delivery_boy',array());	
		$data['orderReason'] = $this->base_model->select_row('order_reason',array('order_id'=>$this->uri->segment(3)));	
		
		$data['thirdPartyDistributors'] = $this->base_model->select_row('distributor',array('id' => $single->distributor_other));
		
	
		$this->loadAdminTemplate('order/detail',$data);
	}
	public function dispatch(){ 
		// echo $this->uri->segment(3); die;
		$data['nav'] ='';
		$data['sub_nav'] ='';
		$data['title'] ='';
		$where = array('orders.id'=>$this->uri->segment(3));
		$columns="orders.*,distributor.full_name as distributorName,delivery_boy.full_name as deliveryBoyName,upload_images.file";
		$joins[] = array('table'=>'distributor','condition'=>'distributor.id=orders.distributor_id','jointype'=>'left');
		$joins[] = array('table'=>'delivery_boy','condition'=>'delivery_boy.id=orders.delivere_id','jointype'=>'left');
		$joins[] = array('table'=>'upload_images','condition'=>'upload_images.id=orders.file_id','jointype'=>'left');
		$data['order_id'] = $this->uri->segment(3);
		$data['single'] = $single = $this->base_model->select_join_row('orders',$where,$joins,$columns);		
		$where1 = array('order_code'=>$single->code);		
		$data['items'] = $this->base_model->select_data('order_item',$where1);	
		$data['category']= $category = $this->base_model->select_row('category',array('title'=>'in_house'));	
		$data['distributors'] = $this->base_model->select_data('distributor',array('category' => $category->id));	
		$data['delivery_boys'] = $this->base_model->select_data('delivery_boy',array());	
	
		$this->loadAdminTemplate('order/dispatch',$data);
	}
	public function user_save()
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
				'file_id'=>trim($this->input->post('file_id'))
				
				
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
				'is_dispatch'=>1
				
				
				
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
