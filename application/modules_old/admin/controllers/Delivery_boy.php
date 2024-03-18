<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require_once 'vendor/autoload.php';
class Delivery_boy extends Base_Controller { 
	

	public function __construct(){ 
		parent::__construct();
		$this->load->model('base_model');
		$this->load->model('Order_export_Model');
		$this->load->model('Party_export_Model');
		$this->load->model('Party_Excel_import_model');
		$this->load->library('session');
	}

    public function index()
	{
		
		if(!$this->session->userdata('is_admin_login')){ redirect(base_url('admin/login'));  }
		
		$data['nav'] ='delivery_boy';
		$data['sub_nav'] ='delivery_boy';
		$data['title'] ='delivery_boy';
		$this->loadAdminTemplate('delivery_boy/index',$data);
	}
    public function  delivery_boy_ajax_list(){ 
		
		$requestData= $_REQUEST;	
		$where_in = $where_like = $where_condition1 = $where_condition2 = $where_condition3 = $where_condition4 =$where_condition5 =$where_condition6 =array();
		if(isset($_POST['searchName']) && !empty($_POST['searchName'])){
			$where_like = array('delivery_boy.full_name'=>$_POST['searchName']);
		}
			
		// $dateSedule = $_POST['datePicker'];
		// if(!empty($dateSedule)) { //echo"n";die;
		// 	$packagetime = explode('-', $dateSedule);
	
		// $start_time =  trim($packagetime[0]);
		
		// $end_time = trim($packagetime[1]);
		
		// 	$where_condition6 = array('users.created_at>='=>$start_time,'users.created_at<='=>$end_time);
			
		//    }

		// if(isset($_POST['searchtoday']) && !empty($_POST['searchtoday'])){ 
		// 	$where_today = array('users.created_at'=>date('Y-m-d'));
		// }else{
		// 	$where_today = array();
		// }
		// $toprice = $_POST['toprice'];
		// $fromprice = $_POST['fromprice'];
		// if(!empty($toprice) && !empty($fromprice)){
		// 	$where_condition5 = array('orders.amount>='=>$toprice,'orders.amount<='=>$fromprice);
		// }
		// if(isset($_POST['fromprice']) && !empty($_POST['fromprice'])){
		// 	print_r($_POST['fromprice']);die;
		// }
	
		$where_condition1 = array();
		$where = array_merge($where_condition1,$where_condition2,$where_condition3,$where_condition4,$where_condition5,$where_condition6);
		$coloum_search = array('delivery_boy.full_name','delivery_boy.full_name');
		$order_by = array('delivery_boy.id'=>'DESC');
		
		if(isset($requestData['order'])){
			if($requestData['order']['0']['column'] == 1){
				$order_by = array('delivery_boy.full_name'=>$requestData['order']['0']['dir']);
			}
		}
		
	$columns = 'delivery_boy.*';
	
	
		$lists = $this->base_model->get_where_in_lists('delivery_boy',$coloum_search,$order_by,'',$where_in,'',$columns,'delivery_boy.id',$where,'',$where_like);
		$data = array();
		$no = $_POST['start'];
		if($lists){
			foreach ($lists as $list) {

				$no++;
				$row = array();
				$row[] = $no;
				$row[] =  $list->full_name;
				$row[] =  $list->phone;
				$row[] =  $list->email;
				
				$buttons = '<button type="button" class="btn btn-success btn-xs" onclick="delivery_boy_edit('.$list->id.')"><small>Edit</small></button>';
				$buttons .= '&nbsp;&nbsp;<button type="button" class="btn btn-danger btn-xs" onclick="delivery_boy_delete('.$list->id.')"><small>Delete</small></button>';
				$row[] = $buttons;
				$row['id'] = $list->id;
	        $data[] = $row;

			}
		}
		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->base_model->count_all_where_in('delivery_boy',$where_in,'delivery_boy.id',$where,''),
			"recordsFiltered" =>$this->base_model->count_filtered_where_in('delivery_boy',$coloum_search,$order_by,'',$where_in,'',$columns,'delivery_boy.id',$where,''),
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
		$where = array('user_id'=>$this->input->post('id'));
		$data['single'] = $single = $this->base_model->select_row('orders',$where);		
		$where1 = array('order_code'=>$single->code);		
		$data['items'] = $this->base_model->select_data('order_item',$where1);	
		// echo "<pre>"; print_r($data);die;
		// $columns="order_item.*,SUM(order_item.quantity) as totalqty";
		// $joins = array();
		// $qty = $this->base_model->select_join_row('order_item',$where,$joins,$columns);
		// echo"<pre>";print_r($qty);die;
		$this->load->view('party/view',$data);
	}
  

	public function createExcel()
{
    $fileName = 'Party.xlsx';
    $employeeData = $this->Party_export_Model->partyList();
	$to_price = $this->input->get('toprice');
	$from_price = $this->input->get('fromprice');
	$date = $this->input->get('date');
    $spreadsheet = new Spreadsheet();

    // Sheet 1 - Party Data
	$rupee = '₹';
    $sheet1 = $spreadsheet->getActiveSheet();
	if(!empty($date ) && !empty($to_price) && !empty($from_price)){
    $sheet1->setCellValue('A1', 'Date'.  '-> ' . $date);
    $sheet1->setCellValue('B1', 'To Price'. ' -> '.$rupee . $to_price);
    $sheet1->setCellValue('C1', 'From Price'. ' -> '.$rupee . $from_price);

	$sheet1->setCellValue('A2', 'Id');
    $sheet1->setCellValue('B2', 'Full Name');
    $sheet1->setCellValue('C2', 'Number');
    $sheet1->setCellValue('D2', 'Amount');
	$rows = 3;
	}
	elseif(!empty($date )){
		$sheet1->setCellValue('A1', 'Date'.  '-> ' . $date);
		$sheet1->setCellValue('A2', 'Id');
    $sheet1->setCellValue('B2', 'Full Name');
    $sheet1->setCellValue('C2', 'Number');
    $sheet1->setCellValue('D2', 'Amount');
	$rows = 3;
		
	}
	elseif(!empty($to_price) && !empty($from_price)){
		$sheet1->setCellValue('A1', 'To Price'. ' -> '.$rupee . $to_price);
    $sheet1->setCellValue('B1', 'From Price'. ' -> '.$rupee . $from_price);
	$sheet1->setCellValue('A2', 'Id');
    $sheet1->setCellValue('B2', 'Full Name');
    $sheet1->setCellValue('C2', 'Number');
    $sheet1->setCellValue('D2', 'Amount');
	$rows = 3;
	}else{
		$sheet1->setCellValue('A1', 'Id');
    $sheet1->setCellValue('B1', 'Full Name');
    $sheet1->setCellValue('C1', 'Number');
    $sheet1->setCellValue('D1', 'Amount');
	$rows = 2;
	}

    


	
	
    $i = 1;
	$tot="Total Amount"; 
	$total_price=0;
	$qty=0;
    foreach ($employeeData as $val) {
        if ($val['status'] == 1) {
            $status = "Active";
        } else {
            $status = "Inactive";
        }
        $sheet1->setCellValue('A' . $rows, $i);
        $sheet1->setCellValue('B' . $rows, $val['full_name']);
        $sheet1->setCellValue('C' . $rows, $val['phone']);
        $sheet1->setCellValue('D' . $rows, $rupee . $val['amount']);
        $rows++;
        $i++;
		$total_price += $val['amount'];
    }

    
	$sheet1->setCellValue('B' .$rows, $tot);
	$sheet1->setCellValue('D' . $rows,$rupee. $total_price);

    $writer = new Xlsx($spreadsheet);
    $writer->save("assets/uploads/export/" . $fileName);

    header("Content-Type: application/vnd.ms-excel");
    redirect(base_url() . "assets/uploads/export/" . $fileName);
}
	
	public function import_excel() {
		
		$this->load->view('party/import');
	}
	
	function fetch()
	{
	 $data = $this->Party_Excel_import_model->select();
	 $output = '
	 <h3 align="center">Total Data - '.$data->num_rows().'</h3>
	 <table class="table table-striped table-bordered">
	  <tr>
	   <th>Full Name</th>
	   <th>Email</th>
	   <th>City</th>
	   <th>Phone</th>
	   
	  </tr>
	 ';
	 foreach($data->result() as $row)
	 {
	  $output .= '
	  <tr>
	   <td>'.$row->full_name.'</td>
	   <td>'.$row->email.'</td>
	   <td>'.$row->city.'</td>
	   <td>'.$row->phone.'</td>
	  </tr>
	  ';
	 }
	 $output .= '</table>';
	//  echo $output;
	}


function import()
 	{ 
	    require_once APPPATH . 'third_party/Classes/PHPExcel.php';
        if(isset($_FILES["file"]["name"]))
		{ 
			$path = $_FILES["file"]["tmp_name"];
			$object = PHPExcel_IOFactory::load($path);
			$err = '';
			$success=$error= array();
			foreach($object->getWorksheetIterator() as $worksheet)
			{
				$highestRow = $worksheet->getHighestRow();
			
				$highestColumn = $worksheet->getHighestColumn();
				
				for($row=2; $row<=$highestRow; $row++)
				{   
					$insertdata = array();
					$full_name = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
					$email = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
					$phone = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
					$password = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
					$distributor_number = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
					
					$data = $this->base_model->select_row('users',array('phone'=>$phone));
					$distributor = $this->base_model->select_row('distributor',array('phone'=>$distributor_number));
					if($distributor){
						$distributor_id = $distributor->id;
					}else{
						$distributor_id = 0;
					}
					
					
						if(empty($data)){
							
								$insertdata[] = array(
									'Full_name'  => $full_name,
									'Email'  => $email,
									'Phone'   => $phone,
									'password' => $password,
									'distributor_id'   => $distributor_id
								);
								// echo "<pre>"; print_r($insertdata);die;
								
								$this->Party_Excel_import_model->insert($insertdata);
							    $success[] =  $row;
						}else{
							$error[] =  $row;
						}
			    }
			}
			
			if(count($success)>0 AND count($error) == 0){
				$res =array('status'=>1, 'succ_m' => count($success));
			}elseif(count($success) == 0 AND count($error) > 0){
				$res =array('status'=>0, 'err_m' => count($error));
			}else{
				$res =array('status'=>2, 'succ_m' => count($success), 'err_m' => count($error));
			}	
			echo  json_encode($res);die;
		}
    } 
 
 

 public function add()
 { 
	$data['roles'] = $this->base_model->select_data('all_cities',array());
	 $this->load->view('delivery_boy/add',$data);
 }

 public function delivery_boy_save()
	{
		$this->form_validation->set_rules('full_name', 'Full Name', 'required');	 
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim|callback_check_for_user');
		$this->form_validation->set_rules('phone', 'Phone Number', 'required|exact_length[10]|numeric|is_unique[users.phone]');
		$this->form_validation->set_rules('city', 'City', 'required');
		$this->form_validation->set_rules('status', 'Status', 'required');
	
		// $this->form_validation->set_rules('file_id', 'Image', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
		
		if($this->form_validation->run() == FALSE){
			$m = json_encode($this->form_validation->error_array());
			$res =array('status'=>0,'msg'=>$m);
			echo json_encode($res);die;
		}else{ 
			
			$data =array(
				'full_name'=>trim($this->input->post('full_name')),
				'email'=>trim($this->input->post('email')),
				'phone'=>trim($this->input->post('phone')),
				'city'=>trim($this->input->post('city')),
				'status'=>trim($this->input->post('status')),
				'password'=>trim(sha1($this->input->post('password'))),
				'created_at'=>date('Y-m-d'),
				'updated_at'=>date('Y-m-d'),
			);
			// echo "<pre>"; print_r($data);die;
			$uid = $this->base_model->insert_data('delivery_boy',$data);
			$res =array('status'=>1);
			echo json_encode($res);die;

		}

	}	
	

    public function delivery_boy_edit(){
      //  print_r($_POST);
		$where = array('delivery_boy.id'=>$this->input->post('id'));
		$columns="delivery_boy.*";
		//$joins[] = array();
		$data['uinfo'] = $this->base_model->select_join_row('delivery_boy',$where,'',$columns);
		$data['roles'] = $this->base_model->select_data('all_cities',array());
		//print_r($data['uinfo']);
		$this->load->view('delivery_boy/edit',$data);
	}
	
	
	public function delivery_boy_update()
	{
		$original_value = $this->base_model->select_row('delivery_boy',array('id'=>$this->input->post('id')));
		
		if($this->input->post('email') != $original_value->email) {
			$is_unique =  '|callback_check_for_user';
		} else {
			$is_unique =  '';
		}

		if($this->input->post('phone') != $original_value->phone) {
			$is_unique1 =  '|callback_check_for_phone';
		} else {
			$is_unique1 =  '';
		}
		
		$this->form_validation->set_rules('full_name', 'Full Name', 'required');	 
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim'.$is_unique);
		$this->form_validation->set_rules('city', 'City', 'required');
	
		$this->form_validation->set_rules('phone', 'Phone Number', 'required|exact_length[10]|trim'.$is_unique1);
		$this->form_validation->set_rules('status', 'Status', 'required');
		
		if($this->form_validation->run() == FALSE){
			$m = json_encode($this->form_validation->error_array());
			$res =array('status'=>0,'msg'=>$m);
			echo json_encode($res);die;
		}else{ 
			
			
			$data =array(
				'full_name'=>trim($this->input->post('full_name')),
				'email'=>trim($this->input->post('email')),
				'city'=>trim($this->input->post('city')),
				'phone'=>trim($this->input->post('phone')),
				'status'=>trim($this->input->post('status')),
				'updated_at'=>date('Y-m-d'),
			);
			
			$this->base_model->update_data('delivery_boy',array('id'=>$this->input->post('id')),$data);
			$res =array('status'=>1);
			echo json_encode($res);die;
		}
	}	

 public function delivery_boy_delete(){
	$this->base_model->delete_data('delivery_boy',array('id'=>$this->input->post('id')));
	$res =array('status'=>1);
	echo json_encode($res);die;
}
public function add_distributor()
{  
	// $where = array('user_id'=>$this->input->post('id'));
	// $data['ram'] = $this->base_model->select_row('orders',$where);
	// echo "<pre>"; print_r($data);die;
	// echo $ram->distributor_id;die;
	// $distributor = $ram['distributor_id'];
	// $where = array('distributor.id'=>$distributor);
	$data['distributors'] = $this->base_model->select_data('distributor',array());
	$data['party'] = $this->input->post('id');
	// echo "<pre>"; print_r($data);die;
	
	$this->load->view('party/add_distributor',$data);
}
public function change_distributor()
{  
	
	$where = array('users.id'=>$this->input->post('id'));
		$columns="users.*";
		//$joins[] = array();
		$data['uinfo'] = $this->base_model->select_join_row('users',$where,'',$columns);
	$data['distributors'] = $this->base_model->select_data('distributor',array());
	$data['party'] = $this->input->post('id');
	// echo "<pre>"; print_r($data);die;
	
	$this->load->view('party/edit_distributor',$data);
}

public function distributor_save()
{
	$this->form_validation->set_rules('distridutor', 'Distridutor Name', 'required');	 

	if($this->form_validation->run() == FALSE){
		$m = json_encode($this->form_validation->error_array());
		$res =array('status'=>0,'msg'=>$m);
		echo json_encode($res);die;
	}else{ 
		
		$data =array(
			'distributor_id'=>trim($this->input->post('distridutor')),
			
			'created_at'=>date('Y-m-d'),
			'updated_at'=>date('Y-m-d')
		);
		// echo "<pre>"; print_r($data);die;
		
		$uid = $this->base_model->update_data('users',array('id'=>$this->input->post('party')),$data);
		$res =array('status'=>1);
		echo json_encode($res);die;

	}

}

public function distributor_update()
	{
		// $original_value = $this->base_model->select_row('party_distributors',array('party_id'=>$this->input->post('party')));
		
		// if($this->input->post('distridutor') == $original_value->distributor_id) {
		// 	$is_unique =  '|callback_check_for_user';
		// } else {
		// 	$is_unique =  '';
		// }

		$this->form_validation->set_rules('distridutor', 'Distridutor Name', 'required');	 

	if($this->form_validation->run() == FALSE){
		$m = json_encode($this->form_validation->error_array());
		$res =array('status'=>0,'msg'=>$m);
		echo json_encode($res);die;
	}else{ 
		
		$data =array(
			'distributor_id'=>trim($this->input->post('distridutor')),
			'updated_at'=>date('Y-m-d')
		);
		// echo "<pre>"; print_r($data);die;
			
			$this->base_model->update_data('users',array('id'=>$this->input->post('party')),$data);
			$res =array('status'=>1);
			echo json_encode($res);die;
		}
	}

function check_for_user($email){
	$check = $this->base_model->select_row('users',array('email'=>$email));
	if (!empty($check)){
		$this->form_validation->set_message('check_for_user','Email Field should contain unique value.');
		return FALSE;
	}
	return TRUE;
}

function check_for_phone($phone){
	$check = $this->base_model->select_row('users',array('phone'=>$phone));
	if (!empty($check)){
		$this->form_validation->set_message('check_for_phone','Phone Field should contain unique value.');
		return FALSE;
	}
	return TRUE;
}

	

	 
}   

