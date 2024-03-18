<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require_once 'vendor/autoload.php';
require_once APPPATH . 'third_party/Classes/PHPExcel.php';
class Distributor extends Base_Controller {

	public function __construct(){ 
		parent::__construct();
		
		$this->load->model('base_model');
		$this->load->model('Saler_export_Model');
		$this->load->model('excel_import_model');
  	//   $this->load->library('excel');
		$this->load->library('session');
	}

	public function index()
	{
		if(!$this->session->userdata('is_admin_login')){ redirect(base_url('admin/login'));  }
		$data['categories'] = $this->base_model->select_data('category',array()); 
		$data['nav'] ='distributor';
		$data['sub_nav'] ='distributor';
		$data['title'] ='Distributor';
		$this->loadAdminTemplate('distributor/index',$data);

	}
	
	public function user_ajax_list(){ 
		
		$requestData= $_REQUEST;	
		
		$where_in = $where_like = $where_condition1 = $where_condition2 = $where_condition3 = $where_condition4 =$where_condition5 =$where_condition6 =array();
		if(isset($_POST['searchName']) && !empty($_POST['searchName'])){
			$where_like = array('distributor.full_name'=>$_POST['searchName']);
		}
		
		if(isset($_POST['status']) && $_POST['status'] !=''){
			$where_condition2 = array('distributor.status'=>$_POST['status']);
		}
		$categoryselect = $_POST['category'];


			   if(!empty($categoryselect)) {
				$where_condition3 = array('distributor.category '=>  $categoryselect);
				
			   }
		
		$where = array_merge($where_condition2,$where_condition3,$where_condition4,$where_condition5,$where_condition6);
		$coloum_search = array('distributor.full_name','distributor.email');
		$order_by = array('distributor.id'=>'DESC');
		
		if(isset($requestData['order'])){
			if($requestData['order']['0']['column'] == 1){
				$order_by = array('distributor.full_name'=>$requestData['order']['0']['dir']);
			}elseif($requestData['order']['0']['column'] == 3){
				$order_by = array('distributor.email'=>$requestData['order']['0']['dir']);
			}elseif($requestData['order']['0']['column'] == 5){
				$order_by = array('distributor.phone'=>$requestData['order']['0']['dir']);
			}elseif ($requestData['order']['0']['column']==6){
				$order_by = array('distributor.status'=>$requestData['order']['0']['dir']);
			}
		}
		
		$columns="distributor.*,upload_images.file,category.title";
		$joins[] = array('table'=>'upload_images','condition'=>'upload_images.id=distributor.file_id','jointype'=>'left');
		$joins[] = array('table'=>'category','condition'=>'category.id=distributor.category','jointype'=>'left');
		$lists = $this->base_model->get_where_in_lists('distributor',$coloum_search,$order_by,'',$where_in,$joins,$columns,'distributor.id',$where,'',$where_like);
		
		
		
			
		$data = array();
		$no = $_POST['start'];
		if($lists){
			foreach ($lists as $list) {
				
				$no++;
				$row = array();
				$row[] = $no;
				$img='';
				if($list->file_id !=0){
				$img =  '<img src="'.base_url('assets/uploads/users/').$list->file.'" class="img-fluid" width="50px" />';
				}
				$row[] =  $img.' '.ucfirst($list->full_name);
				$row[] =  $list->email;
				$row[] =  $list->phone;
				$row[] = $list->title;


				if($list->status == 0){
					$status = '<button type="button" class="btn btn-danger btn-xs" onclick="user_status('.$list->id.',1)"><small>Inactive</small></button>';
				}else{
					$status = '<button type="button" class="btn btn-success btn-xs" onclick="user_status('.$list->id.',0)"><small>Active</small></button>';	
				}
				$row[] = $status;
				
			
				
				$orders='<button type="button" class="btn btn-info btn-xs"><i class="fa fa-eye" aria-hidden="true"></i></button>';
				$buttons = '<button type="button" class="btn btn-success btn-xs" onclick="user_edit('.$list->id.')"><small>Edit</small></button>';
				//$buttons .= '&nbsp;&nbsp;<button type="button" class="btn btn-success btn-xs" onclick="user_view('.$list->id.')"><small>View</small></button>';
			//	$buttons .= '&nbsp;&nbsp;<button type="button" class="btn btn-danger btn-xs" onclick="user_delete('.$list->id.')"><small>Delete</small></button>';
				$row[] = $buttons;
				$row['id'] = $list->id;

				$data[] = $row;

			}
		}
		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->base_model->count_all_where_in('distributor',$where_in,'distributor.id',$where,$joins),
			"recordsFiltered" =>$this->base_model->count_filtered_where_in('distributor',$coloum_search,$order_by,'',$where_in,$joins,$columns,'distributor.id',$where),
			"data" => $data,
		);

		echo json_encode($output);
	}
	
	
	public function add()
	{ 
		$data['roles'] = $this->base_model->select_data('all_cities',array());
		$data['categorys'] = $this->base_model->select_data('category',array());
		// echo "<pre>"; print_r($data);die;
		$this->load->view('distributor/add',$data);
	}
	
	public function user_save()
	{
		$this->form_validation->set_rules('full_name', 'Full Name', 'required');	 
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim|callback_check_for_user');
		$this->form_validation->set_rules('phone', 'Phone Number', 'required|exact_length[10]|numeric|is_unique[users.phone]');
		$this->form_validation->set_rules('city', 'City', 'required');
		$this->form_validation->set_rules('status', 'Status', 'required');
		$this->form_validation->set_rules('category', 'Category', 'required');
		$this->form_validation->set_rules('admin', 'Admin', 'required');
	
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
				'category'=>trim($this->input->post('category')),
				'is_admin'=>trim($this->input->post('admin')),
				'password'=>trim(sha1($this->input->post('password'))),
				'file_id'=>trim($this->input->post('file_id')),
				'created_at'=>time(),
				'updated_at'=>time(),
			);
			$uid = $this->base_model->insert_data('distributor',$data);
			$res =array('status'=>1);
			echo json_encode($res);die;

		}

	}	
	
	function alpha_dash_space($fullname){
		if (! preg_match('/^[a-zA-Z\s]+$/', $fullname)) {
			$this->form_validation->set_message('alpha_dash_space', 'The %s field may only contain alpha characters & White spaces');
			return FALSE;
		} else {
			return TRUE;
		}
	}
	
	public function user_delete(){
		$this->base_model->delete_data('distributor',array('id'=>$this->input->post('id')));
		$res =array('status'=>1);
		echo json_encode($res);die;
	}
	
	public function user_multiple_delete(){		
		$checks = $this->input->post('check');
		
		foreach($checks as $value){
			$this->base_model->delete_data('distributor',array('id'=>$value));
		}
		$res =array('status'=>1);
		echo json_encode($res);die;
	}
	
	public function user_edit(){

		$where = array('distributor.id'=>$this->input->post('id'));
		$columns="distributor.*,upload_images.file,upload_images.id as fid";
		$joins[] = array('table'=>'upload_images','condition'=>'upload_images.id=distributor.file_id','jointype'=>'left');
		$data['uinfo'] = $this->base_model->select_join_row('distributor',$where,$joins,$columns);
		$data['roles'] = $this->base_model->select_data('all_cities',array());
		$data['categorys'] = $this->base_model->select_data('category',array());
		$this->load->view('distributor/edit',$data);
	}
	
	
	public function user_update()
	{
		$original_value = $this->base_model->select_row('distributor',array('id'=>$this->input->post('id')));
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
		$this->form_validation->set_rules('category', 'Category', 'required');
		$this->form_validation->set_rules('admin', 'Admin', 'required');
		
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
				'category'=>trim($this->input->post('category')),
				'is_admin'=>trim($this->input->post('admin')),
				'file_id'=>trim($this->input->post('file_id')),
				'updated_at'=>time(),
			);
			
			$this->base_model->update_data('distributor',array('id'=>$this->input->post('id')),$data);
			$res =array('status'=>1);
			echo json_encode($res);die;
		}
	}	

	public function user_view(){
		$where = array('users.id'=>$this->input->post('id'));
		$columns="users.*,users.id as uid, role.role,upload_images.file";
		$joins[] = array('table'=>'role','condition'=>'role.id=users.role','jointype'=>'left');
		$joins[] = array('table'=>'upload_images','condition'=>'upload_images.id=users.file_id','jointype'=>'left');
		$data['uinfo'] = $uinfo = $this->base_model->select_join_row('users',$where,$joins,$columns);
		$this->load->view('distributor/view',$data);
	}
	
	public function user_status(){
		$this->base_model->update_data('distributor',array('id'=>$this->input->post('id')),array('status'=>$this->input->post('type')));
		$res =array('status'=>1);
		echo json_encode($res);die;
	}
	
	public function user_verify(){
		$this->base_model->update_data('distributor',array('id'=>$this->input->post('id')),array('otp_status'=>$this->input->post('type')));
		$res =array('status'=>1);
		echo json_encode($res);die;
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
		$check = $this->base_model->select_row('distributor',array('phone'=>$phone));
		if (!empty($check)){
			$this->form_validation->set_message('check_for_phone','Phone Field should contain unique value.');
			return FALSE;
		}
		return TRUE;
	}


	public function users_image_data()
	{
		$res = array();
		$name = $_FILES["file"]["name"];
		$ext = pathinfo($name, PATHINFO_EXTENSION);
		$new_name = rand(1000,9999).'_'.time().'.'.$ext;
		$config['file_name'] = $new_name;
		$path = 'assets/uploads/users/';
		$config['upload_path']   = $path;
		$config['allowed_types'] = 'jpg|png|jpeg'; 
		$this->load->library('upload', $config);

		if(!$this->upload->do_upload('file')){
			$m = json_encode(array('file_error' => $this->upload->display_errors()));
			$res = array('status'=>0,'msg'=>$m);
			echo  json_encode($res);die;
			$file_error = 1;
		}else{
			$upload_data = $this->upload->data();
			$file=$upload_data['file_name'];
			$file_error = 0;
		}
		if($file_error == 0){
			$data = array(
				'file'=>$file,
				'created_at'=>time(),
				'updated_at'=>time(),
			);
			$insert_id = $this->base_model->insert_data('upload_images',$data);
			$image_data = $this->base_model->select_row('upload_images',array('id'=>$insert_id));
			$res = array('status'=>1,'image_id'=>$insert_id,'image_data'=>base_url().$path.$image_data->file);
			echo  json_encode($res);die;
		}else{
			$res = array('status'=>0,'msg'=>$m);
			echo  json_encode($res);die;
		}

		
	}

	public function createExcel() {
		$fileName = 'Saler.xlsx';  
		$employeeData = $this->Saler_export_Model->salerList();
		$spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
       	$sheet->setCellValue('A1', 'Id');
        $sheet->setCellValue('B1', 'Full Name');
        $sheet->setCellValue('C1', 'Email');
        $sheet->setCellValue('D1', 'Phone');
		$sheet->setCellValue('E1', 'Status');
	
        $rows = 2;
        $i = 1;
        foreach ($employeeData as $val){

			
			if($val['status']==1){
				$status= "Active";
			}
			else{
				$status= "Inactive";
			}
            $sheet->setCellValue('A' . $rows, $i);
            $sheet->setCellValue('B' . $rows, $val['full_name']);
            $sheet->setCellValue('C' . $rows, $val['email']);
            $sheet->setCellValue('D' . $rows, $val['phone']);
			$sheet->setCellValue('E' . $rows, $status);
	   
           
            $rows++;
             $i++;
        } 
        $writer = new Xlsx($spreadsheet);
		$writer->save("assets/uploads/export".$fileName);
		header("Content-Type: application/vnd.ms-excel");
        redirect(base_url()."assets/uploads/export".$fileName);                   
    }   



public function import_excel() {
		
	$this->load->view('distributor/import');
}

function fetch()
{
 $data = $this->excel_import_model->select();
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


function import(){
	require_once APPPATH . 'third_party/Classes/PHPExcel.php';
    if(isset($_FILES["file"]["name"])){ 

	$path = $_FILES["file"]["tmp_name"];
	$object = PHPExcel_IOFactory::load($path);
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
				
				$data = $this->base_model->select_row('distributor',array('phone'=>$phone,'email'=>$email));

					if(empty($data)){
				
							$insertdata[] = array(
								'Full_name'  => $full_name,
								'Email'  => $email,
								'Phone'   => $phone,
								'Password'   => trim(sha1($password)),
							);
						
						$this->Excel_import_model->insert($insertdata);
						$success[] = $row;
					}else{
						$error[] =  $row;
					}	
			}
		}
	    if(count($success)>0 AND count($error) == 0){
			$res =array('status'=>1, 'succ_m' => count($success));
		}else if(count($success) == 0 AND count($error) > 0){
			$res =array('status'=>0, 'err_m' => count($error));
		}else{
			$res =array('status'=>2, 'succ_m' => count($success), 'err_m' => count($error));
		}	
		echo  json_encode($res);die;
    }
} 



}



	

