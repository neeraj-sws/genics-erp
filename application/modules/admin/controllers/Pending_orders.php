<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
require_once 'vendor/autoload.php';
class Pending_orders extends Base_Controller {

	public function __construct(){ 
		parent::__construct();
		$this->load->model('base_model');
		$this->load->model('Order_export_Model');
		$this->load->library('session');
	}


    public function index(){
		
		if(!$this->session->userdata('is_admin_login')){ redirect(base_url('admin/login'));  }
		$data['distributors'] = $this->base_model->select_data('distributor',array());
		
		$data['party_name'] = $customers =  $this->base_model->select_asc_data('orders',array(),$group_by="party_name");
		
			foreach($customers as $customer){
				$party = $customer->party_name;				


				$where =  array('orders.party_name'=>$party);				
				 $party_name =$this->base_model->select_data('orders',$where);
				 $partycount[$customer->id] = count($party_name);
				
				
			}
		
		$data['partycount'] =$partycount;
		$data['nav'] ='pending_orders';
		$data['sub_nav'] ='pending_orders';
		$data['title'] ='pending_orders';
		// echo"<pre>";print_r( $data);die;
		$this->loadAdminTemplate('pending_orders/index',$data);

	}

    public function  order_ajax_list(){ 
		
		$requestData= $_REQUEST;	
		// echo"<pre>";print_r( $requestData);die;
		$where_in = $where_like = $where_condition1 = $where_condition2 = $where_condition3 = $where_condition4 =$where_condition5 =$where_condition6 =array();
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
			   $distributorselect = $_POST['distributor_select'];


			   if(!empty($distributorselect)) { //echo"n";die;
				$where_condition2 = array('orders.distributor_id '=>  $distributorselect);
				
			   }

			   $partyname = $_POST['partyname'];


			   if(!empty($partyname)) { //echo"n";die;
				$where_condition3 = array('orders.party_name '=>  $partyname);
				
			   }

               $where_condition4 =array('orders.status'=>0);

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
		
		$columns="orders.*,distributor.full_name";
		$joins[] = array('table'=>'distributor','condition'=>'distributor.id=orders.distributor_id','jointype'=>'left');
		$lists = $this->base_model->get_where_in_lists('orders',$coloum_search,$order_by,'',$where_in,$joins,$columns,'orders.id',$where,'',$where_like);
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
				$row[] =  $list->party_name;
				// $row[] =  $list->number;
				$row[] =  $list->payment_term;
				if(!empty($list->dispached)){
				      $row[] =  $list->dispached;
				}else{
				      $row[] =  'NA';
				}
              
                $row[] =   $list->full_name;
				
				if($list->status == 0){
                    $status = '&nbsp;&nbsp;<button type="button" class="btn item btn-success btn-sm" onclick="order_status('.$list->id.',1)"><i class="fa fa-check" aria-hidden="true"></i></button>';
                    $status .= '&nbsp;&nbsp;<button type="button" class="btn item btn-danger btn-sm" onclick="order_status('.$list->id.',2)"><i class="fa fa-times" aria-hidden="true"></i></button>';

			   }elseif($list->status == 1){
				$status = '<span class="text-white badge badge-success"><b>Delivered</b></span>';
			   }else{
				$status = '<span class="text-white badge badge-danger"><b>Declined</b></span>';
			   }
			   $row[] = $status;
			   $row[] = '<span class="badge badge-dark">'.count($itemscount).'</span>';
               $row[] ='<span class="badge badge-dark">'.$list->created_at.'</span>';

			   $row[]= '<button type="button" class="btn item btn-success btn-sm" onclick="send_email('.$list->id.')"><small>Email</small></button>';

			   $buttons = '<button type="button" class="btn btn-success btn-xs" onclick="view_order('.$list->id.')"><i class="fa fa-eye" aria-hidden="true"></i></button>';
			   $row[] = $buttons;
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
            
            // $columns="order_item.*,SUM(order_item.quantity) as totalqty";
            // $joins = array();
            // $qty = $this->base_model->select_join_row('order_item',$where,$joins,$columns);
            // echo"<pre>";print_r($qty);die;
            $this->load->view('order/view',$data);
        }
    
        public function order_status(){
            if($this->input->post('type')== 1){
                    $status=1;
            }else{
                $status =2;
            }
                $this->base_model->update_data('orders',array('id'=>$this->input->post('id')),array('status'=>$status,'update_at'=>date('Y-m-d')));
                $res =array('status'=>1);
                echo json_encode($res);die;
            }
    
    
        public function createExcel() {
            $fileName = 'Order.xlsx';  
            $employeeData = $this->Order_export_Model->pendingOrderLinst();
            // echo "<pre>"; print_r($employeeData); die;
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
                if(!empty($val['dispached'])){
                    $dispached=$val['dispached'];
                }else{
                    $dispached= "NA";
                }
                $sheet->setCellValue('A' . $rows, $i);
                $sheet->setCellValue('B' . $rows, $val['party_name']);
                $sheet->setCellValue('C' . $rows, $val['payment_term']);
                $sheet->setCellValue('D' . $rows, $dispached);
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


		public function send_email(){
			//return true;
			$order = $this->base_model->select_row('orders',array('id'=>$this->input->post('id')));
				 
			$receipts = $this->base_model->select_data('receipt',array('status' =>1));
				
				$id= $order->id;
				  $updateData = array("is_email" => 1);
				 $whereorder = array('id' => $order->id);
				$oid=$this->base_model->update_data('orders', $whereorder, $updateData);
				
				$edata['distributor'] = $distributor =  $this->base_model->select_row('distributor',array('id' => $order->distributor_id));
	
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
				  $res= $this->send_mail($body,$distribute_mail,$subject);	 
				   
			}
			if($res == 1){
				$res =array('status'=>1);
				echo json_encode($res);die;
			}else{
				$res =array('status'=>0);
				echo json_encode($res);die;
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
				return 0;
				//return "Mailer Error: " . $mail->ErrorInfo;
			}else{
				return 1;
			}
		}
         


}