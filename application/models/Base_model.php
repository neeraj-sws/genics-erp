<?php if ( ! defined('BASEPATH')) exit('Noinsert_data direct script access allowed');
class Base_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set('Asia/kolkata');
	}

	public function insert_data($tblname,$data){
		$this->db->insert($tblname, $data);
		return $this->db->insert_id();

	}
	public function select_data($tblname,$where,$limit="",$start=""){
		$this->db->where($where);
		if($limit!=''){
			$this->db->limit($limit,$start);
		}
		//$this->db->order_by('id', "desc");
		$query = $this->db->get($tblname); //echo $str = $this->db->last_query(); die;
		$row = $query->result();
		
		return $row;
	}
	public function select_data_with_order_by($tblname,$where,$limit="",$start="",$order_by=""){
		$this->db->where($where);
		if($limit!=''){
			$this->db->limit($limit,$start);
		}
		if($order_by!=''){
			$this->db->order_by($order_by);
		}else{
			$this->db->order_by('id',"desc");
		}

		$query = $this->db->get($tblname); 
		// echo $str = $this->db->last_query(); die;
		$row = $query->result();
		return $row;
	}

	public function select_data_with_order_by_col($tblname,$where,$limit="",$start="",$order_by=""){
		$this->db->where($where);
		if($limit!=''){
			$this->db->limit($limit,$start);
		}
		if($order_by!=''){
			$this->db->order_by($order_by[0],$order_by[1]);
		}else{
			$this->db->order_by('id',"desc");
		}

		$query = $this->db->get($tblname); 
		$row = $query->result();
		return $row;
	}
	
	public function select_data_with_order_by_clm($tblname,$where,$colom,$order_by=""){
		$this->db->where($where);
		$this->db->order_by($colom,$order_by);
		$query = $this->db->get($tblname); //echo $str = $this->db->last_query(); die;
		$row = $query->result();
		return $row;
	}
	
	public function select_row($tblname,$where){
		$this->db->where($where);
		$this->db->order_by('id', "desc");
		$query = $this->db->get($tblname); //echo $str = $this->db->last_query(); die;
		$row = $query->row();
		return $row;
	}	

	public function select_row_collums($tblname,$where,$coll){
		$this->db->select($coll);
		$this->db->where($where);
		//$this->db->order_by('id', "desc");
		$query = $this->db->get($tblname); //echo $str = $this->db->last_query(); die;
		$row = $query->row();
		return $row;
	}

	public function update_data($tblname,$where,$data){
		$this->db->where($where);
		$query = $this->db->update($tblname,$data); //echo $str = $this->db->last_query(); die;
		return true;
	}
	public function delete_data($tblname,$where){
		$this->db->where($where);
		$query = $this->db->delete($tblname);
		return true;
	}

	public function select_row_data($tblname,$where){
		$this->db->where($where);
		$query = $this->db->get($tblname);
		$row = $query->row();
		return $row;
	}

	public function select_asc_data($tblname,$where,$group_by=""){
		$this->db->where($where);
		if($group_by!=""){
			$this->db->group_by($group_by);
		}
		$this->db->order_by('id', "ASC");
		$query = $this->db->get($tblname); //echo $str = $this->db->last_query(); die;
		$row = $query->result();
		return $row;
	}
	public function select_asc_limit_data($tblname,$where,$limit){
		$this->db->where($where);

		$this->db->order_by('id', "ASC");
		$this->db->limit($limit);
		$query = $this->db->get($tblname); //echo $str = $this->db->last_query(); die;
		$row = $query->result();
		return $row;
	}

	public function get_name_by_id($id){
		$row = $this->select_colom_name_row('users',array('full_name'),array('users_id'=>$id));
		if($row){
			return $row->full_name;
		}else{
			return '-';
		}

	}

	public function select_count_data($tblname,$where=''){
		$this->db->select("count(*) as count");
		$this->db->where($where);
		$query = $this->db->get($tblname);
		//echo $str = $this->db->last_query(); die;
		$row = $query->row();
		return $row;
	}

	public function encryptor($action, $string) {
		$output = false;
		$encrypt_method = "AES-256-CBC";
    //pls set your unique hashing key
		$secret_key = 'muni';
		$secret_iv = 'muni123';

    // hash
		$key = hash('sha256', $secret_key);

    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
		$iv = substr(hash('sha256', $secret_iv), 0, 16);

    //do the encyption given text/string/number
		if( $action == 'encrypt' ) {
			$output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
			$output = base64_encode($output);
		}
		else if( $action == 'decrypt' ){
     //decrypt the given text/string/number
			$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
		}
		return $output;
	}

	public function select_colom_name_result($tblname,$name,$where){
		$this->db->where($where);
		$this->db->select($name);
		$query = $this->db->get($tblname);
		$row = $query->result();
		return $row;
	}
	public function select_colom_name_row($tblname,$name,$where){
		$this->db->where($where);
		$this->db->select($name);
		$query = $this->db->get($tblname);//echo $str = $this->db->last_query(); die;
		$row = $query->row();
		return $row;
	}

	public function select_join_row($tablename ='', $where ='', $joins ='', $columns ='', $like ='', $group_by ='', $order_by ='',$limit = '', $start = '')
	{
		if(!empty($columns))$this->db->select($columns);
		if(empty($columns))$this->db->select('*');

		if (is_array($joins) && count($joins) > 0) {
			foreach ($joins as $k => $v) {
				$this->db->join($v['table'], $v['condition'], $v['jointype']);
			}
		}

		if (!empty($group_by))
			$this->db->group_by($group_by);

		if (!empty($like))
			$this->db->or_like($like);

		if (!empty($limit))
			$this->db->limit($limit, $start);

		if (!empty($where))
			$this->db->where($where);

		if (!empty($order_by))
			$this->db->order_by($order_by);

		$this->db->from($tablename);

		$query = $this->db->get();
			// echo $this->db->last_query();die;
		return $query->row();
	}

	public function select_join_result($tablename ='', $where ='', $joins ='', $columns ='', $like ='', $group_by ='', $order_by ='',$limit = '', $start = '',$column_name='',$where2=array())
	{
		if(!empty($columns))$this->db->select($columns);
		if(empty($columns))$this->db->select('*');

		if (is_array($joins) && count($joins) > 0) {
			foreach ($joins as $k => $v) {
				$this->db->join($v['table'], $v['condition'], $v['jointype']);
			}
		}

		if (!empty($group_by))
			$this->db->group_by($group_by);

		if (!empty($like))
			$this->db->or_like($like);

		if (!empty($limit))
			$this->db->limit($limit, $start);

		if (!empty($where))
			$this->db->where($where);

		if($where2 && $column_name != '')
			$this->db->where_in($column_name,$where2);

		if (!empty($order_by))
			$this->db->order_by($order_by);

		$this->db->from($tablename);

		$query = $this->db->get();
			// echo $this->db->last_query();die;
		return $query->result();
	}

	public function select_data_by_like_query($tbl_name,$where){


		$this->db->like($where);
		$query = $this->db->get($tbl_name);
		$row = $query->result();

		return $row;
	}
	public function select_data_by_like_limit_query($tbl_name,$like_where,$limit,$where=array()){

		if(!empty($like_where)){
			$this->db->like($like_where);
		}
		if(!empty($where)){

			$this->db->where($where);
		}


		$query = $this->db->get($tbl_name);
		$row = $query->result();
		return $row;
	}


	public function getTimeDifference($time) {
    //Let's set the current time
		$currentTime = date('Y-m-d H:i:s');
		$toTime = strtotime($currentTime);

    //And the time the notification was set
		$fromTime = $time;

    //Now calc the difference between the two
		$timeDiff = floor(abs($toTime - $fromTime) / 60);

    //Now we need find out whether or not the time difference needs to be in
    //minutes, hours, or days
		if ($timeDiff < 2) {
			$timeDiff = "Just now";
		} elseif ($timeDiff > 2 && $timeDiff < 60) {
			$timeDiff = floor(abs($timeDiff)) . " ".$this->lang->line('mins_ago');
		} elseif ($timeDiff > 60 && $timeDiff < 120) {
			$timeDiff = floor(abs($timeDiff / 60)) . " ".$this->lang->line('hr_ago');
		} elseif ($timeDiff < 1440) {
			$timeDiff = floor(abs($timeDiff / 60)) . " ".$this->lang->line('hrs_ago');
		} elseif ($timeDiff > 1440 && $timeDiff < 2880) {
			$timeDiff = floor(abs($timeDiff / 1440)) . " ".$this->lang->line('day_ago');
		} elseif ($timeDiff > 2880) {
			$timeDiff = floor(abs($timeDiff / 1440)) . " ".$this->lang->line('days_ago');
		}
		return $timeDiff;
	}

	public function check_plan(){
		$employer = $this->select_row('employer',array('id'=>$this->session->userdata('id')));
		$plan_id = $employer->job_plan_id;
		$plan = $this->select_row('job_plan',array('id'=>$plan_id));
		$no_post = ($plan)?$plan->job_posting_no:0;
		$total_job_count = count($this->select_data('job',array('added_by'=>$employer->id,'plan_id'=>$plan_id)));
		if($total_job_count == 0){
			return true;
		}else if($total_job_count != 0 && $total_job_count >= $no_post){
			return false;
		}else{
			return true;
		}
	}


	public function select_row_asc($tblname,$where){
		$this->db->where($where);
		$this->db->order_by('id', "asc");
		$query = $this->db->get($tblname); //echo $str = $this->db->last_query(); die;
		$row = $query->row();
		return $row;
	}

	public function employer_plan_update($id=null){
		$user = $this->base_model->select_row('employer',array('id'=>$id));
		if($user){
			if($user->job_plan_id != 0 && $user->remaining_job_posting > 0 && $user->plan_end_at > time()){

				//nothing do; plan is already activated and running
			}else{
				$this->base_model->update_data('employer_plans',array('employer_id'=>$user->id,'plan_id'=>$user->job_plan_id,'status'=>1),array('status'=>0,'is_delete'=>1));
				//deactive current plan

				//check for already purchased plan
				$employer_plan = $this->base_model->select_row_asc('employer_plans',array('employer_id'=>$user->id,'status'=>0,'is_delete'=>0));

				if($employer_plan){
					$plan = $this->base_model->select_row('job_plan',array('id'=>$employer_plan->plan_id));
					if($plan){

						$now = time();
						$exp = date('Y-m-d H:i:s',$now);
						$exp = strtotime($exp.'+'.$plan->post_expiry.' days');

						$mydata = array(
							'job_plan_id'=>$this->input->post('plan_id'),
							'plan_start_at'=>$now,
							'plan_end_at'=>$exp,
							'total_job_posting'=>$plan->job_posting_no,
							'remaining_job_posting'=>$plan->job_posting_no,
						);
						$this->base_model->update_data('employer',array('id'=>$user->id),$mydata);

						$this->base_model->update_data('employer_plans',array('id'=>$employer_plan->id),array('status'=>1));
					}
				}else{
					//no plan activated default.
					$mydata = array(
						'job_plan_id'=>0,
						'plan_start_at'=>'',
						'plan_end_at'=>'',
						'total_job_posting'=>0,
						'remaining_job_posting'=>0,
					);
					$this->base_model->update_data('employer',array('id'=>$user->id),$mydata);
				}

			}
		}
	}

	public function random_pass($length=6){
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
		return  substr( str_shuffle( $chars ), 0,$length);
	}

	public function get_login_user_data(){
		$user_id = $this->session->userdata('user_id');
		$role = $this->session->userdata('role');
		$is_vendor = $this->session->userdata('is_vendor');
		$role_info = $this->select_row('ps_user_roles',array('id'=>$role));
		$user_info=array();
		if($role_info){
			$user_info['role_label']=$role_info->label;
		}

		if($role == 1){
			$info = $this->select_row('ps_admin_meta',array('user_id'=>$user_id));
			$user_info['name']=$info->company_name;
		}else{
			$info = $this->select_row('ps_users',array('id'=>$user_id));
			$user_info['name']=$info->first_name.' '.$info->last_name;
			if($is_vendor == 1){
				$user_info['role_label']='Vendor';
			}
		}
		//echo '<pre>';print_r($user_info);die;
		return $user_info;
	}

	public function get_user_data(){
		$user_id = $this->session->userdata('user_id');
		$role = $this->session->userdata('role');
		$is_vendor = $this->session->userdata('is_vendor');
		$role_info = $this->select_row('ps_user_roles',array('id'=>$role));
		$user_info=array();

		$user_info['login_info'] = $this->select_row('ps_users',array('id'=>$user_id));

		if($role_info && !$is_vendor){
			$user_info['role_label']=$role_info->label;
		}else{
			$user_info['role_label']= 'Vendor';
		}

		if($role == 1){
			$user_info['personal_info'] = $this->select_row('ps_admin_meta',array('user_id'=>$user_id));
		}

		return $user_info;
	}


	function get_lists($tbl_name,$column_search,$column_order,$bydate="",$where_condition=array(),$joins,$columns)
	{

		$this->_get_query($tbl_name,$column_search,$column_order,$bydate,$where_condition,$joins,$columns);
		if(isset($_POST['length']) && $_POST['length'] < 1) {
			$_POST['length']= '10';
		} else
		$_POST['length']= $_POST['length'];

		if(isset($_POST['start']) && $_POST['start'] > 1) {
			$_POST['start']= $_POST['start'];
		}
		$this->db->limit($_POST['length'], $_POST['start']);

		//print_r($_POST);die;
		$query = $this->db->get();
		//echo $this->db->last_query();die;
		return $query->result();
	}

	function count_filtered($tbl_name,$column_search,$column_order,$bydate="",$where_condition=array(),$joins,$columns)
	{
		$this->_get_query($tbl_name,$column_search,$column_order,$bydate,$where_condition,$joins,$columns);

		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($tbl_name,$where_condition=array(),$joins='')
	{ 
		$this->db->from($tbl_name);
		if (is_array($joins) && count($joins) > 0) {
			foreach ($joins as $k => $v) {

				$this->db->join($v['table'], $v['condition'], $v['jointype']);
			}
		}
		if($where_condition){
			$this->db->where($where_condition);
		}
		return $this->db->count_all_results();
	}

	private function _get_query($tbl_name,$column_search,$column_order,$bydate="",$where_condition,$joins,$columns,$group_by="")
	{
		if(!empty($columns))$this->db->select($columns);
		if(empty($columns))$this->db->select('*');
		$this->db->from($tbl_name);
        //$this->db->where('status !=',0);

		if (is_array($joins) && count($joins) > 0) {
			foreach ($joins as $k => $v) {

				$this->db->join($v['table'], $v['condition'], $v['jointype']);
			}
		}

		if($group_by!=""){
			$this->db->group_by($group_by);
		}
		if($where_condition){
			$this->db->where($where_condition);
		}
		if($bydate!=''){
			$dates = explode('_',$bydate);

			$this->db->where('task_date >=', date('m/d/Y', strtotime($dates[0])));
			$this->db->where('task_date <=', date('m/d/Y', strtotime($dates[1])));
		}
		$i = 0;
        foreach ($column_search as $emp) // loop column
        {
        	if(isset($_POST['search']['value']) && !empty($_POST['search']['value'])){
        		$_POST['search']['value'] = $_POST['search']['value'];
        	} else
        	$_POST['search']['value'] = '';
		if($_POST['search']['value']) // if datatable send POST for search
		{
			if($i===0) // first loop
			{
				$this->db->group_start();
				$this->db->like(($emp), $_POST['search']['value']);
			}
			else
			{
				$this->db->or_like(($emp), $_POST['search']['value']);
			}

			if(count($column_search) - 1 == $i) //last loop
				$this->db->group_end(); //close bracket
			}
			$i++;
		}

		 if($column_order) // here order processing
		 {
		 	foreach($column_order as $corder){
		 		$this->db->order_by(key($column_order),$corder);
		 	}

		 }
		 elseif(isset($_POST['order']))
		 {
		//$this->db->order_by($column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		 }else{
		 	$this->db->order_by('id', "desc");
		 }
		}

		public function check_menu_permission($menu){
			if($this->session->userdata('role') != 1){
				$is_permission = $this->select_row('ps_user_role_permission_menu',array('menu'=>$menu,'role_id'=>$this->session->userdata('role')));

				if($is_permission){
					return true;
				}else{
					return false;
				}
			}else{
				return true;
			}
		}
		
		public function select_data_where_in($tblname,$column,$where_in){
			$this->db->where_in($column,$where_in);
			$this->db->order_by('id', "ASC");
		$query = $this->db->get($tblname); //echo $str = $this->db->last_query(); die;
		$row = $query->result();
		return $row;
	}
	function get_where_in_lists($tbl_name,$column_search,$column_order,$bydate="",$where_in=array(),$joins,$columns,$where_in_coloum,$where,$group_by="",$where_like=array())
	{
		
		$this->_get_where_in_query($tbl_name,$column_search,$column_order,$bydate,$where_in,$joins,$columns,$where_in_coloum,$where,$group_by,$where_like);
		if(isset($_POST['length']) && $_POST['length'] < 1) {
			$_POST['length']= '10';
		} else
		$_POST['length']= $_POST['length'];

		if(isset($_POST['start']) && $_POST['start'] > 1) {
			$_POST['start']= $_POST['start'];
		}
		$this->db->limit($_POST['length'], $_POST['start']);

		//print_r($_POST);die;
		$query = $this->db->get();
		//echo $this->db->last_query();die;
		return $query->result();
	}
	private function _get_where_in_query($tbl_name,$column_search,$column_order,$bydate="",$where_in,$joins,$columns,$where_in_coloum,$where_condition,$group_by="",$where_like=array())
	{ 
		if(!empty($columns))$this->db->select($columns);
		if(empty($columns))$this->db->select('*');
		$this->db->from($tbl_name);
        //$this->db->where('status !=',0);

		if (is_array($joins) && count($joins) > 0) {
			foreach ($joins as $k => $v) {

				$this->db->join($v['table'], $v['condition'], $v['jointype']);
			}
		}

		if($group_by!=""){
			$this->db->group_by($group_by);
		}
		if($where_in){
			$this->db->where_in($where_in_coloum,$where_in);
		}
		if($where_condition){
			$this->db->where($where_condition);
		}
		if($bydate!=''){
			$dates = explode('_',$bydate);

			$this->db->where('task_date >=', date('m/d/Y', strtotime($dates[0])));
			$this->db->where('task_date <=', date('m/d/Y', strtotime($dates[1])));
		}
		$i = 0;
        foreach ($column_search as $emp) // loop column
        {
        	if(isset($_POST['search']['value']) && !empty($_POST['search']['value'])){
        		$_POST['search']['value'] = $_POST['search']['value'];
        	} else
        	$_POST['search']['value'] = '';
		if($_POST['search']['value']) // if datatable send POST for search
		{
			if($i===0) // first loop
			{
				$this->db->group_start();
				$this->db->like(($emp), $_POST['search']['value']);
			}
			else
			{
				$this->db->or_like(($emp), $_POST['search']['value']);
			}

			if(count($column_search) - 1 == $i) //last loop
				$this->db->group_end(); //close bracket
			}
			$i++;
		}		

		 if($column_order) // here order processing
		 {
		 	foreach($column_order as $corder){
		 		$this->db->order_by(key($column_order),$corder);
		 	}

		 }
		 elseif(isset($_POST['order']))
		 {
		//$this->db->order_by($column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		 }else{
		 	$this->db->order_by('id', "desc");
		 }

		if($where_like) // here like processing
		{   $i=0;
			foreach($where_like as $key => $clike){
				if($i===0){
					$this->db->like($key,$clike);
				}else{
					$this->db->or_like($key,$clike);
				}
				$i++;				
			}
		}
		// echo $this->db->last_query();die;
	}
	public function count_all_where_in($tbl_name,$where_in=array(),$where_in_coloum,$where_condition,$joins='')
	{
		$this->db->from($tbl_name);
		//echo $str = $this->db->last_query(); die;
		if (is_array($joins) && count($joins) > 0) {
			foreach ($joins as $k => $v) {

				$this->db->join($v['table'], $v['condition'], $v['jointype']);
			}
		}
		

		if($where_in){
			$this->db->where_in($where_in_coloum,$where_in);
		}

		if($where_condition){
			$this->db->where($where_condition);
		}
		
		

		return $this->db->count_all_results();
	}
	function count_filtered_where_in($tbl_name,$column_search,$column_order,$bydate="",$where_in=array(),$joins,$columns,$where_in_coloum,$where_condition,$group_by="")
	{
		$this->_get_where_in_query($tbl_name,$column_search,$column_order,$bydate,$where_in,$joins,$columns,$where_in_coloum,$where_condition);
		if($group_by!=""){
			$this->db->group_by($group_by);
		}
		$query = $this->db->get();

		return $query->num_rows(); 
	}
	function generate_password($n=12){
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ$%^&{}'; 
		$randomString = ''; 		  
		for ($i = 0; $i < $n; $i++) { 
			$index = rand(0, strlen($characters) - 1); 
			$randomString .= $characters[$index]; 
		} 
		
		return $randomString; 
	}
	
	

	function limited_data($n=200){
		if (strlen($summary) > $limit)
			$summary = substr($summary, 0, strrpos(substr($summary, 0, $limit), ' ')) . '...';
		echo $summary;
	}

	public function get_unique_id($name){
		$nameArr = explode('-',$name);
		$name = $nameArr[0];
		$length = 8;    	
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$unique = $name.substr( str_shuffle( $chars ),0,$length);
		$row = $this->select_row('seller_info',array('unique_id'=>$unique));
		if($row!=''){
			return $this->get_unique_id($name);
		}else{
			return $unique;
		}
	}

	function create_slug($string,$table){
		$field='slug';
		$key=NULL;$value=NULL;
		$t =& get_instance();
		$slug = url_title($string);		
		$slug = strtolower($slug);
		$i = 0;
		$params = array ();
		$params[$field] = $slug;
		if($key)$params["$key !="] = $value; 
		while ($this->db->where('slug',$slug)->get($table)->num_rows())
		{ 
			if (!preg_match ('/-{1}[0-9]+$/', $slug ))
				$slug .= '-' . ++$i;
			else
				$slug = preg_replace ('/[0-9]+$/', ++$i, $slug );
			$params [$field] = $slug;
		} 
		return $slug; 
	}

	public function not_same_id_create_slug($string,$table,$id){
		$field='slug';
		$key=NULL;$value=NULL;
		$t =& get_instance();
		$slug = url_title($string);		
		$slug = strtolower($slug);
		$i = 0;
		$params = array ();
		$params[$field] = $slug;
		if($key)$params["$key !="] = $value; 
		while($this->db->where('slug',$slug)->where('id!=',$id)->get($table)->num_rows())
		{ 
			if (!preg_match ('/-{1}[0-9]+$/', $slug ))
				$slug .= '-' . ++$i;
			else
				$slug = preg_replace ('/[0-9]+$/', ++$i, $slug );
			$params [$field] = $slug;			
		} 
		return $slug; 
	}
	
	public function recaptcha_verification($recaptchaResponse){
		$userIp=$this->input->ip_address();
		
		$secret = $this->config->item('google_secret');
		
		$url="https://www.google.com/recaptcha/api/siteverify?secret=".$secret."&response=".$recaptchaResponse."&remoteip=".$userIp;
		
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, $url); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		$output = curl_exec($ch); 
		curl_close($ch);      
		
		return json_decode($output, true);
	}
		

	function generate_code($n=4){
		$characters = '0MNOPQR12STUVWXYZ34DEFGHI56789ABCJKL'; 
		$randomString = ''; 		  
		for ($i = 0; $i < $n; $i++) { 
			$index = rand(0, strlen($characters) - 1); 
			$randomString .= $characters[$index]; 
		} 
		
		return $randomString; 
	}

	function send_otp($otp,$phone){
		//return true;
		$api_key = '25DA1BD7F4786D';
		$contacts =$phone;
		$from = 'FIVEDU';
		$sms_text = urlencode('Your MPPER OTP - '.$otp);
		$api_url = "http://byebyesms.com/app/smsapi/index.php?key=".$api_key."&campaign=8335&routeid=7&type=text&contacts=".$contacts."&senderid=".$from."&msg=".$sms_text;
		$response = file_get_contents($api_url);

		return true;
	}

	public function top_rsent_post($tabel,$limit) {
		$where3=array($tabel.'.status'=>1);
		$joins3[] = array('table'=>'upload_images','condition'=>'upload_images.id='.$tabel.'.image_id','jointype'=>'left');
		$limit3=$limit;
		$columns3=$tabel.'.*,upload_images.file as image';
		$order_by3 = $tabel.'.id DESC';
		$data = $this->base_model->select_join_result($tabel,$where3,$joins3,$columns3,'','',$order_by3,$limit3);
		return $data;
	}
	public function is_menus_permission($mid){
		$data =  $this->base_model->select_row('user',array('id'=>$this->session->userdata('id')));
		$role = $this->base_model->select_row('role',array('id'=>$data->role));
		$is_menus = explode(',', $role->menus);
		if(in_array($mid, $is_menus)){
			return TRUE;
		}
		return FALSE;
	}
	public function get_template_data($project_id,$template_id,$template_key){
		$template = $this->select_row('templates',array('id'=>$template_id));
		$contents= json_decode($template->content);
		$content_array=$content_array2=array();
		$i=1;
		foreach($contents as $content){
			if($content->type=='file'){
				$content_array2[$content->name]=$content->type;
			}
			$content_array[]=$content->name;
			
		}
		$qry =  $this->db->select('*');
		$this->db->from('projects_meta');
		$this->db->where(array('project_id'=>$project_id,'template_id'=>$template_id,'template_key'=>$template_key));
		$this->db->where_in('meta_key',$content_array);
		$query = $this->db->get(); 
		$meta_data = $query->result();
		$templates_data = array();
		foreach($meta_data as $template_row){
			$templates_data['project_id']=$template_row->project_id;
			$templates_data['template_id']=$template_row->template_id;
			$templates_data['template_key']=$template_row->template_key;
			
			if(isset($content_array2[$template_row->meta_key]) && $content_array2[$template_row->meta_key]=='file'){
				$temp = $this->select_row('upload_images',array('id'=>$template_row->meta_value));
				$templates_data[$template_row->meta_key]=$temp->file;
			}else{
				$templates_data[$template_row->meta_key]=$template_row->meta_value;
			}
		}
		$templateJson = json_encode($templates_data);		
		return $templateJson;
	}

	public function get_blog_template_data($blog_id,$template_id,$template_key){
		$template = $this->select_row('blog_templates',array('id'=>$template_id));
		$contents= json_decode($template->content);
		$content_array=$content_array2=array();
		$i=1;
		foreach($contents as $content){
			if($content->type=='file'){
				$content_array2[$content->name]=$content->type;
			}
			$content_array[]=$content->name;
			
		}
		$qry =  $this->db->select('*');
		$this->db->from('blogs_meta');
		$this->db->where(array('blog_id'=>$blog_id,'template_id'=>$template_id,'template_key'=>$template_key));
		$this->db->where_in('meta_key',$content_array);
		$query = $this->db->get(); 
		$meta_data = $query->result();
		$templates_data = array();
		foreach($meta_data as $template_row){
			$templates_data['blog_id']=$template_row->blog_id;
			$templates_data['template_id']=$template_row->template_id;
			$templates_data['template_key']=$template_row->template_key;
			
			if(isset($content_array2[$template_row->meta_key]) && $content_array2[$template_row->meta_key]=='file'){
				$temp = $this->select_row('upload_images',array('id'=>$template_row->meta_value));
				$templates_data[$template_row->meta_key]=$temp->file;
			}else{
				$templates_data[$template_row->meta_key]=$template_row->meta_value;
			}
		}
		$templateJson = json_encode($templates_data);	
		// echo '<pre>';print_R($templates_data);die;	
		return $templateJson;
	}

	public function get_api_setting_data($type,$key){
		return $this->select_row('api_setting',array('type'=>$type,'key'=>$key))->value;
	}

	public function getAverageRun($player_id,$type){
		$this->db->select_sum('run');
		$this->db->where('player_id',$player_id);
		$this->db->where('match_type',$type);
		$this->db->order_by('id','desc');
		$this->db->limit(10);
		$result = $this->db->get('player_performance')->row();
		$run = $result->run/10;
		return $run;
	}
 	
	public function getAveragebRun($player_id,$type){
		$this->db->select_avg('run');
		$this->db->where('player_id',$player_id);
		$result = $this->db->get('player_bowling_performance')->row();
		return $result;
	}

	public function select_row_orwhere($tblname,$where,$or_where){
		$this->db->where($where);
		$this->db->or_where($or_where);
		//$this->db->order_by('id', "desc");
		$query = $this->db->get($tblname); //echo $str = $this->db->last_query(); die;
		$row = $query->row();
		return $row;
	}	
	
	public function activity_type($order_id, $user_id, $message_id, $distributor_id,$deliveryboy_id) {
		$this->load->database();
	
		$this->db->select('activity_type.id, activity_type.message_type');
		$this->db->from('order_activity');
		$this->db->join('activity_type', 'activity_type.id = ' . $message_id, 'right');
	
		$query = $this->db->get();
		$message = $query->row();
	
		$message_id= $message->id;
		// echo "<pre>";
		// print_r($message_id);
		// die();
	
	
		$data = array(
			'order_id' => $order_id,
			'user_id' => $user_id,
			'message_type' => $message_id,
			'distributor_id' => $distributor_id,
			'deliveryboy_id' => $deliveryboy_id,
			'created_at'=>date('d-m-Y h:i a'),
			'updated_at'=>date('d-m-Y h:i a')
		);
	
		$this->db->insert('order_activity', $data);
	}
}
?>
