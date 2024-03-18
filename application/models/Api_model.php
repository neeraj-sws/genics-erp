<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Api_model extends CI_Model{
    
	function __construct(){
          parent::__construct();
	}

   
	public function get_cities(){
		$page_no = 1;
		if($this->input->get('page') AND !empty($this->input->get('page'))){
			$page_no = $this->input->get('page');
		}
		$per_page = 10;
		$offset = ($page_no-1)*$per_page;
		
		$results['status'] = '';
		$results['data'] = array();	
		$this->db->select('*');
		$this->db->limit($per_page,$offset);
		$query = $this->db->get('all_cities');
		$cities = $query->result_array();
        if($cities){
			$results['status'] = TRUE;
			$results['data'] =$cities;			
		}else{			
			$results['status'] = FALSE;			
		}
		return $results;
	}
	
	public function check_user($tbl,$input){	
		$results['status'] = '';
		$results['data'] = array();	
		$email = $input['email'];
		$password = $input['password']; 
		$userdata=array();
	    $this->db->select('*');
        $this->db->where('email',$email);
        $this->db->where('password',$password);
        $query = $this->db->get($tbl);
        $userdata = $query->row_array();	
		if($userdata){
			$results['status'] = TRUE;
			$results['data'] =$userdata;			
		}else{			
			$results['status'] = FALSE;			
		}
		return $results;
	}
	
	public function get_signle_data($tbl,$input){  
        $results['status'] = '';
        $results['data'] = array(); 
        $userdata=array();
        $this->db->select('*');
        $this->db->where($input);
        $query = $this->db->get($tbl);
        $userdata = $query->row_array();    
        if($userdata){
            $results['status'] = TRUE;
            $results['data'] =$userdata;            
        }else{          
            $results['status'] = FALSE;         
        }
        return $results;
    }
	
	public function get_user_data($tbl,$input){  
        $results['status'] = '';
        $results['data'] = array(); 
        $this->db->select('users.*,upload_images.file');
        $this->db->from('users');
        $this->db->join('upload_images','upload_images.id=users.file_id','left');
		$this->db->where($input);
        $query = $this->db->get(); 
		$userdata = $query->row_array();
		if($userdata['file']){
			$userdata['profileImage']=base_url().'assets/uploads/users/'.$userdata['file'];	
		}else{
			$userdata['profileImage']='';
		}			
        if($userdata){
            $results['status'] = TRUE;
            $results['data'] =$userdata;            
        }else{          
            $results['status'] = FALSE;         
        }
        return $results;
    }

	public function verify_otp($input)
	{
        $results['status'] = '';
		
		$phone = $input['phone'];
		$otp = $input['otp']; 
			$otpVerify = $this->verifyOtp($phone,$otp);
			if($otpVerify == 1){
			$userdata=array();
			$this->db->select('*');
			$this->db->where(array('phone'=> $phone));
			$query = $this->db->get('users');
			$userdata = $query->row_array();
			if($userdata){
				$udata = $this->get_user_data('users',array('users.phone'=>$phone));
				$this->db->where('phone', $phone);
				$this->db->update('users', array('otp' =>'','otp_status'=>1,'status'=>1));
				$results['status'] = TRUE;
				$results['udata'] =$udata['data'];
			}else{	
				$results['status'] = FALSE;			
			}
			}else{
				$results['status'] = FALSE;	
			}
		return $results;
    }
	
	
	public function verifyOtp($phone,$otp){
		$OTPValue=$otp;
		$CustomersPhoneNumber=$phone;

		$YourAPIKey='9980a50e-a0f6-11eb-80ea-0200cd936042';
		 
		### Verify OTP Entered By User
		$agent= 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
		$url = "https://2factor.in/API/V1/$YourAPIKey/SMS/VERIFY3/$CustomersPhoneNumber/$OTPValue"; 
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL,$url); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, $agent);
		$Response= curl_exec($ch); 
		curl_close($ch);

		### Parse API Response to check if OTP matched or mismatched
		$Response_json=json_decode($Response,false);
		
		if ($Response_json->Details =='OTP Matched'){
			return 1;
		}else{
			return 0;
		}
	}
	
	
	public function resend_otp($input)
	{
	    $results['status'] = '';
		$results['data'] = array();	
		$email = $input['email']; 
		$phone = $input['phone']; 
		
		if($email !='' AND $phone !=''){
			$userdata=array();
			$this->db->select('count(*) as count');
			$this->db->where(array('email' => $email));
			$this->db->where(array('phone' => $phone));
			$query = $this->db->get('users');
			$cnt = $query->row_array();	
			$count = $cnt['count'];
			if($count == 0){
				$results['status'] = FALSE;
			}else{
				$otp = $this->gen_otp_new($email,$phone);
				$results['status'] = TRUE;
				$results['data'] = $otp;
				
			}
		}elseif($email !='' AND $phone ==''){
			$userdata=array();
			$this->db->select('count(*) as count');
			$this->db->where(array('email' => $email));
			$query = $this->db->get('users');
			$cnt = $query->row_array();	
			$count = $cnt['count'];
			if($count == 0){
				$results['status'] = FALSE;
			}else{
				$otp = $this->gen_otp_new($email,$phone);
				$results['status'] = TRUE;
				$results['data'] = $otp;
				
			}
		}else{
			$userdata=array();
			$this->db->select('count(*) as count');
			$this->db->where(array('phone' => $phone));
			$query = $this->db->get('users');
			$cnt = $query->row_array();	
			$count = $cnt['count'];
			if($count == 0){
				$results['status'] = FALSE;
			}else{
				$otp = $this->gen_otp_new($email,$phone);
				$results['status'] = TRUE;
				$results['data'] = $otp;
				
			}
		}
		return $results;
	}
	
	
	public function gen_otp_new($phone)
	{
		$otp = rand(1111,9999);
		if($phone !='')
		{
			$this->sendOtp($phone);
			$this->db->where('phone',$phone);
			$this->db->update('users', array('otp_status'=>0));
		}
        return true;
    }
	
	public function sendOtp($phone){
		$SentTo=$phone;
		$YourAPIKey='9980a50e-a0f6-11eb-80ea-0200cd936042';
		$agent= 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
		$url = "https://2factor.in/API/V1/$YourAPIKey/SMS/$SentTo/AUTOGEN"; 
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL,$url); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, $agent);
		$Response= curl_exec($ch); 
		curl_close($ch);
		return true;
	}
	
	
	public function get_player_detail($user_id){
		$player_id = $this->input->post('player_id');
		
		$buystock = $this->base_model->select_row('buy_stock',array('user_id'=>$user_id,'player_id'=>$player_id));
		$results['status'] = '';
		$results['data'] = array();	
		$this->db->select('players.*,,upload_images.file,all_country.countryname,player_type.label as player_type,player_type.icon');
		$this->db->join('upload_images','upload_images.id=players.file_id','left');
		$this->db->join('all_country','all_country.id=players.country_id','left');
		$this->db->join('player_type','player_type.id=players.player_id','left');
		$this->db->where('players.status',1);
		$this->db->where('players.id',$player_id);
		$query = $this->db->get('players');
		$player = $query->row_array();
		$players_arr=array();
		
		
		$res = json_decode($player['response']);
		//echo '<pre>';print_R($res);die;
		$player['role']=ucfirst(str_replace('_',' ',$res->roles[0])); 
		$player['batting_style']=ucfirst(str_replace('_',' ',$res->batting_style)); 
		$player['bowling_style_arm']=ucfirst(str_replace('_',' ',$res->bowling_style->arm)); 
		$player['bowling_style_pace']=ucfirst(str_replace('_',' ',$res->bowling_style->pace)); 
		$player['bowling_style_type']=ucfirst(str_replace('_',' ',$res->bowling_style->bowling_type)); 
		
		$ctry = $this->base_model->select_row('all_country',array('id'=>$player['country_id']));
		if($player['file']){
			$player['profileImage']=base_url().'assets/uploads/players/'.$player['file'];	
		}else{
			$player['profileImage']='';
		}
		$player['countryImage']=base_url('assets/images/country_flags/').strtolower($ctry->code).'.png';
		$player['playerTypeImage']=base_url('assets/uploads/player_type/').$player['icon'];
		
		$mystocks =0;
		if($buystock){
			$mystocks = $buystock->remaining_stock;
		}
		$player['my_stock']=$mystocks;
		$players_arr=$player;
		$players_arr['stocks']=$this->get_player_stock($player['id']);
			
		$results['status'] = TRUE;
		$results['data'] =$players_arr;
		return $results;
	}
	
	
	public function get_players(){
		$page_no = 1;
		if($this->input->get('page') AND !empty($this->input->get('page'))){
			$page_no = $this->input->get('page');
		}
		$per_page = 10;
		$offset = ($page_no-1)*$per_page;
		$search ='';
		if($this->input->get('search') AND !empty($this->input->get('search'))){
			$search = $this->input->get('search');
		}
		
		$results['status'] = '';
		$results['data'] = array();	
		$this->db->select('players.*,,upload_images.file,all_country.countryname,player_type.label as player_type,player_type.icon');
		$this->db->join('upload_images','upload_images.id=players.file_id','left');
		$this->db->join('all_country','all_country.id=players.country_id','left');
		$this->db->join('player_type','player_type.id=players.player_id','left');
		$this->db->where('players.status',1);
		if($search){
			$this->db->like('players.full_name',$search);
		}
		$this->db->limit($per_page,$offset);
		$query = $this->db->get('players');
		$players = $query->result_array();
		$players_arr=array();
		$i=0;
		foreach($players as $player){ 
			$ctry = $this->base_model->select_row('all_country',array('id'=>$player['country_id']));
			if($player['file']){
				$player['profileImage']=base_url().'assets/uploads/players/'.$player['file'];	
			}else{
				$player['profileImage']='';
			}
			$player['countryImage']=base_url('assets/images/country_flags/').strtolower($ctry->code).'.png';
			$player['playerTypeImage']=base_url('assets/uploads/player_type/').$player['icon'];
			$players_arr[$i]=$player;
			$players_arr[$i]['stocks']=$this->get_player_stock($player['id']);
		$i++;
		}
		$results['status'] = TRUE;
		$results['data'] =$players_arr;
		return $results;
	}
	
	public function get_player_stock($player_id){
		$this->db->select('*');
        $this->db->from('players_stock');
        $this->db->where('player_id',$player_id);
		$this->db->order_by('id','DESC');
		$this->db->limit(1);
        $query = $this->db->get(); 
		$players_stocks = $query->row_array();
		if($players_stocks){
			$players_stocks['AssignedTime']=date('Y-m-d H:i:s',$players_stocks['AssignedTime']);
			return $players_stocks;
		}else{
			return (object)array();
		}
		
	}
	
	
	
	public function get_user_token($tbl,$input){   
        $results['status'] = '';
        $results['data'] = array(); 
        $this->db->select('*');
        $this->db->from($tbl);
        $this->db->where($input);
        $query = $this->db->get(); 
		$userdata = $query->row_array();
		//echo '<pre>';print_R($userdata);die;		
        if($userdata){
            $results['status'] = TRUE;
            $results['data'] =$userdata;            
        }else{          
            $results['status'] = FALSE;         
        }
        return $results;
    }
	
	public function get_feature_players(){
		$page_no = 1;
		if($this->input->get('page') AND !empty($this->input->get('page'))){
			$page_no = $this->input->get('page');
		}
		$per_page = 10;
		$offset = ($page_no-1)*$per_page;
		
		$results['status'] = '';
		$results['data'] = array();	
		$this->db->select('players.*,,upload_images.file,all_country.countryname,player_type.label as player_type,player_type.icon');
		$this->db->join('upload_images','upload_images.id=players.file_id','left');
		$this->db->join('all_country','all_country.id=players.country_id','left');
		$this->db->join('player_type','player_type.id=players.player_id','left');
		$this->db->where('players.status',1);
		$this->db->where('players.is_feature',1);
		$this->db->limit($per_page,$offset);
		$query = $this->db->get('players');
		$players = $query->result_array();
		$players_arr=array();
		$i=0;
		foreach($players as $player){ 
			$ctry = $this->base_model->select_row('all_country',array('id'=>$player['country_id']));
			if($player['file']){
				$player['profileImage']=base_url().'assets/uploads/players/'.$player['file'];	
			}else{
				$player['profileImage']='';
			}
			$player['countryImage']=base_url('assets/images/country_flags/').strtolower($ctry->code).'.png';
		$player['playerTypeImage']=base_url('assets/uploads/player_type/').$player['icon'];
			$players_arr[$i]=$player;
			$players_arr[$i]['stocks']=$this->get_player_stock($player['id']);
		$i++;
		}
		$results['status'] = TRUE;
		$results['data'] =$players_arr;
		return $results;
	}
	
	
	public function get_new_players(){
		$page_no = 1;
		if($this->input->get('page') AND !empty($this->input->get('page'))){
			$page_no = $this->input->get('page');
		}
		$per_page = 10;
		$offset = ($page_no-1)*$per_page;
		
		$results['status'] = '';
		$results['data'] = array();	
		$this->db->select('players.*,,upload_images.file,all_country.countryname,player_type.label as player_type,player_type.icon');
		$this->db->join('upload_images','upload_images.id=players.file_id','left');
		$this->db->join('all_country','all_country.id=players.country_id','left');
		$this->db->join('player_type','player_type.id=players.player_id','left');
		$this->db->where('players.status',1);
		$this->db->limit($per_page,$offset);
		$this->db->order_by('id','DESC');
		$query = $this->db->get('players');
		$players = $query->result_array();
		$players_arr=array();
		$i=0;
		foreach($players as $player){ 
			$ctry = $this->base_model->select_row('all_country',array('id'=>$player['country_id']));
			if($player['file']){
				$player['profileImage']=base_url().'assets/uploads/players/'.$player['file'];	
			}else{
				$player['profileImage']='';
			}
			$player['countryImage']=base_url('assets/images/country_flags/').strtolower($ctry->code).'.png';
		$player['playerTypeImage']=base_url('assets/uploads/player_type/').$player['icon'];
			$players_arr[$i]=$player;
			$players_arr[$i]['stocks']=$this->get_player_stock($player['id']);
		$i++;
		}
		$results['status'] = TRUE;
		$results['data'] =$players_arr;
		return $results;
	}
	
	
	public function get_top_players(){
		$page_no = 1;
		if($this->input->get('page') AND !empty($this->input->get('page'))){
			$page_no = $this->input->get('page');
		}
		$per_page = 5;
		$offset = ($page_no-1)*$per_page;
		
		$results['status'] = '';
		$results['data'] = array();	
		$this->db->select('players.*,,upload_images.file,all_country.countryname,player_type.label as player_type,player_type.icon');
		$this->db->join('upload_images','upload_images.id=players.file_id','left');
		$this->db->join('all_country','all_country.id=players.country_id','left');
		$this->db->join('player_type','player_type.id=players.player_id','left');
		$this->db->where('players.status',1);
		$this->db->limit($per_page,$offset);
		$this->db->order_by('rand()');
		$query = $this->db->get('players');
		$players = $query->result_array();
		$players_arr=array();
		$i=0;
		foreach($players as $player){ 
			$ctry = $this->base_model->select_row('all_country',array('id'=>$player['country_id']));
			if($player['file']){
				$player['profileImage']=base_url().'assets/uploads/players/'.$player['file'];	
			}else{
				$player['profileImage']='';
			}
			$player['countryImage']=base_url('assets/images/country_flags/').strtolower($ctry->code).'.png';
		$player['playerTypeImage']=base_url('assets/uploads/player_type/').$player['icon'];
			$players_arr[$i]=$player;
			$players_arr[$i]['stocks']=$this->get_player_stock($player['id']);
		$i++;
		}
		$results['status'] = TRUE;
		$results['data'] =$players_arr;
		return $results;
	}
	
	public function get_user_transactions_data($user_id){
		$page_no = 1;
		if($this->input->get('page') AND !empty($this->input->get('page'))){
			$page_no = $this->input->get('page');
		}
		$per_page = 10;
		$offset = ($page_no-1)*$per_page;		
        $results['status'] = '';
        $results['data'] = array(); 
        $this->db->select('*');
        $this->db->from('transactions');
        $this->db->where('user_id',$user_id);
		$this->db->limit($per_page,$offset);
		$this->db->order_by('id','DESC');
        $query = $this->db->get(); 
		$transaction_array = $query->result_array();
		$tran_arr=array();
        
			$i=0;
			foreach($transaction_array as $tran){
				$tran_arr[]=$tran;
				$tran_arr[$i]['payment_resposnse']=json_decode($tran['payment_resposnse']);
				$tran_arr[$i]['payment_request']=json_decode($tran['payment_request']);
			$i++;
			}
			
            $results['status'] = TRUE;
            $results['data'] =$tran_arr;            
        
        return $results;
    }
	
	
	public function get_user_wallet_history_data($user_id){
		$page_no = 1;
		if($this->input->get('page') AND !empty($this->input->get('page'))){
			$page_no = $this->input->get('page');
		}
		
		$type ='';
		if($this->input->get('type') AND !empty($this->input->get('type'))){
			$type = $this->input->get('type');
		}
		
		$per_page = 10;
		$offset = ($page_no-1)*$per_page;		
        $results['status'] = '';
        $results['data'] = array(); 
        $this->db->select('*');
        $this->db->from('wallet_histrory');
        $this->db->where('user_id',$user_id);
		if($type !=''){
        $this->db->where('type',$type);
		}
		$this->db->order_by('id','DESC');
		$this->db->limit($per_page,$offset);
        $query = $this->db->get(); 
		$wallet_arr = $query->result_array();
		$results['status'] = TRUE;
        $results['data'] =$wallet_arr;  
        return $results;
    }
	
	
	public function get_user_payment_request($user_id){
		$page_no = 1;
		if($this->input->get('page') AND !empty($this->input->get('page'))){
			$page_no = $this->input->get('page');
		}
		
		$per_page = 10;
		$offset = ($page_no-1)*$per_page;		
        $results['status'] = '';
        $results['data'] = array(); 
        $this->db->select('*');
        $this->db->from('payment_request');
        $this->db->where('user_id',$user_id);
		$this->db->limit($per_page,$offset);
		$this->db->order_by('id','DESC');
        $query = $this->db->get(); 
		$preq_arr = $query->result_array();
		//echo '<pre>';print_r($preq_arr);die;
		$preq=array();
		$i=0;
		foreach($preq_arr as $p){
			$preq[]=$p;
			if($p['status'] == 1){
				$status ='Paid';
			}elseif($p['status'] == 2){
				$status ='Decliend';
			}else{
				$status ='Pending';
			}
			$preq[$i]['status']=$status;
			$preq[$i]['created_at']=date('Y-m-d h:i A',$p['created_at']);
			
		$i++;
		}
		$results['status'] = TRUE;
        $results['data'] =$preq;  
        return $results;
    }
	
	
	public function get_user_buy_stock_data($user_id){
		$page_no = 1;
		if($this->input->get('page') AND !empty($this->input->get('page'))){
			$page_no = $this->input->get('page');
		}
		
		$per_page = 10;
		$offset = ($page_no-1)*$per_page;		
        $results['status'] = '';
        $results['data'] = array(); 
        $this->db->select('*');
        $this->db->from('buy_stock');
        $this->db->where('user_id',$user_id);
		$this->db->order_by('id','DESC');
		$this->db->limit($per_page,$offset);
        $query = $this->db->get(); 
		$stock_arr = $query->result_array();
		$results['status'] = TRUE;
        $results['data'] =$stock_arr;  
        return $results;
    }

	public function get_user_sell_stock_data($user_id){
		$page_no = 1;
		if($this->input->get('page') AND !empty($this->input->get('page'))){
			$page_no = $this->input->get('page');
		}
		$res=array();
		$per_page = 10;
		$offset = ($page_no-1)*$per_page;		
        $results['status'] = '';
        $results['data'] = array(); 
        $this->db->select('*');
        $this->db->from('stock_sell');
        $this->db->where('user_id',$user_id);
		$this->db->order_by('id','ASC');
		$this->db->limit($per_page,$offset);
        $query = $this->db->get(); 
		$stock_arr = $query->result_array();
		$cnt=0; $i=0;
		foreach($stock_arr as $stock_arr_1){
			$res[$i]=$stock_arr_1;
			$res[$i]['resposnce']=json_decode($stock_arr_1['resposnce']);
			$i++;
		}
		$results['status'] = TRUE;
        $results['data'] =$res;  
        return $results;
    }


	public function get_user_buy_sell_stock_data($user_id){
		$cnt=0; $i=0;
		$res_sell=array();
		$res=array();
		
		$this->db->select('players.id as playerId,players.full_name,buy_stock.player_id, stock_sell.user_id as sell_userId, stock_sell.stock_id,stock_sell.stock as sell_stock, stock_sell.amount,stock_sell.sell_status,stock_sell.p_n_l,stock_sell.resposnce,stock_sell.created_at as sell_created_at');
		$this->db->join('buy_stock','buy_stock.id=stock_sell.stock_id','left');
		$this->db->join('players','players.id=buy_stock.player_id','left');
		$this->db->where('stock_sell.user_id',$user_id);
		$query = $this->db->get('stock_sell')->result_array();
		
		foreach($query as $sell_stock_arr_1){
			$res_sell[$i]['player']=$sell_stock_arr_1['full_name'];
			$res_sell[$i]['status']='Successfully';
			$res_sell[$i]['date']=date('Y-m-d h:i:s a',$sell_stock_arr_1['sell_created_at']);
			$res_sell[$i]['type']='Sell';
			$res_sell[$i]['stocks']=$sell_stock_arr_1['sell_stock'];
			$res_sell[$i]['price']=$sell_stock_arr_1['amount'];
			$i++;
		}
		
		$this->db->select('players.id as playerId,players.full_name,buy_stock.id as buy_id,buy_stock.user_id as buy_user_id, buy_stock.player_id, buy_stock.current_unit_price, buy_stock.stock as buy_stock,buy_stock.created_at as buy_created_at');
		$this->db->join('players','buy_stock.player_id=players.id','left');
		$this->db->where('buy_stock.user_id',$user_id);
		$buy_query = $this->db->get('buy_stock');
		$buy_stock_arr = $buy_query->result_array();
		
		foreach($buy_stock_arr as $buy_stock_arr_1){
			$res[$cnt]['player']=$buy_stock_arr_1['full_name'];
			$res[$cnt]['status']='Successfully';
			$res[$cnt]['date']=date('Y-m-d h:i:s a',$buy_stock_arr_1['buy_created_at']);
			$res[$cnt]['type']='Buy';
			$res[$cnt]['stocks']=$buy_stock_arr_1['buy_stock'];
			$res[$cnt]['price']=$buy_stock_arr_1['current_unit_price'];
		}
		$merge_array=array_merge($res,$res_sell);
		// echo '<pre>merge_array - '; print_r($merge_array); die;
		
		$results['status'] = TRUE;
        $results['data'] =$merge_array;  
        return $results;
    }


	public function get_user_summary_stock_data($user_id){
		 $i=0;
		$res_sell=array();
		$res=array();
		
		$this->db->select('sum(buy_stock.total_price),users.wallet');
		$this->db->join('users','users.id=buy_stock.user_id','left');
		$this->db->where('buy_stock.user_id',$user_id);
		$buy_query = $this->db->get('buy_stock')->row_array();
		
		$this->db->select('sum(amount),pnl_amount,sell_status');
		$this->db->where('user_id',$user_id);
		$sell_query = $this->db->get('stock_sell')->row_array();
		
		if($sell_query['sum(amount)'] !=''){
			$total_release_amount = number_format((float)$sell_query['sum(amount)'], 2, '.', '');
		}else{
			$total_release_amount ='0.00';
		}
		$total_unreleased_amount = $buy_query['sum(buy_stock.total_price)']-$total_release_amount;
		$non_invested_amount = $buy_query['wallet']-$buy_query['sum(buy_stock.total_price)'];
		$res['total_amount_invested']=$buy_query['sum(buy_stock.total_price)'];
		$res['total_release_amount']=$total_release_amount;
		$res['total_unreleased_amount']=number_format((float)$total_unreleased_amount, 2, '.', '');
		$res['non_invested_amount']=number_format((float)$non_invested_amount, 2, '.', '');
		
		$results['status'] = TRUE;
        $results['data'] =$res;  
        return $results;
    }


	public function get_player_graph_stock_weekly($user_id){
		$k=0;
		$stock_arr=array();
		for($i = 7; $i >= 0; $i--)
		{
			$cdate = strtotime("-$i days");
			$beginOfDay = strtotime("today", $cdate);
			$endOfDay   = strtotime("tomorrow", $cdate) - 1;
			
			 $this->db->select('*');
			$this->db->from('user_stock_history');
			$this->db->where('user_id',$user_id);
			$this->db->where('created_at >=',$beginOfDay);
			$this->db->where('created_at <=',$endOfDay);
			$this->db->order_by('id','DESC');
			$this->db->limit(1);
			$query = $this->db->get(); 
			$stock = $query->row_array();
			
			if($stock){  
				$stock_arr[$k]=$stock;
				$stock_arr[$k]['created_at']=date('Y-m-d',$stock['created_at']);
				$k++;
			}
		
		}
		
		$results['status'] = TRUE;
        $results['data'] =$stock_arr;  
        return $results;
    }


	public function get_player_graph_stock_daily($user_id){
		$timestamp=time();
		$beginOfDay = strtotime("today", $timestamp);
		$endOfDay   = strtotime("tomorrow", $beginOfDay) - 1;

		$results['status'] = '';
        $results['data'] = array(); 
        $this->db->select('*');
        $this->db->from('user_stock_history');
        $this->db->where('user_id',$user_id);
        $this->db->where('created_at >=',$beginOfDay);
        $this->db->where('created_at <=',$endOfDay);
        $this->db->order_by('id','ASC');
		$query = $this->db->get(); 
		$stocks = $query->result_array();
		$i=0;
		foreach($stocks as $stock){
			$stock_arr[$i]=$stock;
			$stock_arr[$i]['created_at']=date('Y-m-d H:i:s',$stock['created_at']);
		$i++;
		}
		
		$results['status'] = TRUE;
        $results['data'] =$stock_arr;  
        return $results;
    }


	public function get_player_graph_stock_monthly($user_id){
		
		$k=0;
		$stock_arr=array();
		for($i = 30; $i >= 0; $i--)
		{
			$cdate = strtotime("-$i days");
			$beginOfDay = strtotime("today", $cdate);
			$endOfDay   = strtotime("tomorrow", $cdate) - 1;
			
			 $this->db->select('*');
			$this->db->from('user_stock_history');
			$this->db->where('user_id',$user_id);
			$this->db->where('created_at >=',$beginOfDay);
			$this->db->where('created_at <=',$endOfDay);
			$this->db->order_by('id','DESC');
			$this->db->limit(1);
			$query = $this->db->get(); 
			$stock = $query->row_array();
			
			if($stock){  
				$stock_arr[$k]=$stock;
				$stock_arr[$k]['created_at']=date('Y-m-d',$stock['created_at']);
				$k++;
			}
		
		}
		
		$results['status'] = TRUE;
        $results['data'] =$stock_arr;  
        return $results;
    }
	
	public function getMyPortfolio($user_id){
		$i=0;
		
		$this->db->select('stock, total_price,players_stock.*');
		$this->db->join('players_stock','players_stock.id=buy_stock.id','left');
		$this->db->where('user_id',$user_id);
		$my_buy_stock = $this->db->get('buy_stock')->result_array();
			
		$current_stocks_price= $my_stock_price=0;
		foreach($my_buy_stock as $my_buy_stock_1){
			$this->db->select('unitPrice');
			$this->db->where('player_id',$my_buy_stock_1['player_id']);
			$this->db->order_by('id','DESC');
			$this->db->limit('1');
			$current_players_stock_price = $this->db->get('players_stock')->row_array();
			
			$my_stock_price +=$my_buy_stock_1['total_price'];
			$current_stocks_price += $current_players_stock_price['unitPrice']*$my_buy_stock_1['stock'];
		}
		$pnl_price=($current_stocks_price - $my_stock_price);
		if($my_stock_price != 0){
		$pnl_percentage=($pnl_price/$my_stock_price*100);
		}else{
		$pnl_percentage=0;
		}
		
		$res=array(
			'my_stocks_price'=>number_format((float)$my_stock_price, 2, '.', ''),
			'current_stocks_price'=>number_format((float)$current_stocks_price, 2, '.', ''),
			'pnl'=>number_format((float)$pnl_percentage, 2, '.', '')
		);
		
		$results['status'] = TRUE;
        $results['data'] =$res;  
        return $results;
    }



	public function getAllMatches(){
		
		$match_data = $this->base_model->select_data('matches',array());
		$match_arr =array();
		$i=0;
		foreach($match_data as $match){
			$res = json_decode($match->response);
			$team_name = $this->get_team_name($match->winner,$res->teams);
			$match_arr[$i]['unique_id']=$match->unique_id;
			$match_arr[$i]['title']=$match->title;
			$match_arr[$i]['match_title']=$match->match_title;
			$match_arr[$i]['short_name']=$match->short_name;
			$match_arr[$i]['sub_title']=$match->sub_title;
			$match_arr[$i]['format']=$match->format;
			$match_arr[$i]['status']=$match->status;
			$match_arr[$i]['winner']=$team_name;
			$match_arr[$i]['match_date']=date('Y-m-d H:i A',$match->match_date);
			$match_result=array();
			if($match->status == 'completed'){
				$match_details = $this->getMatcheDetail($match->unique_id);
				
				$score_data=(object)$match_details['data']['score_data'];
				$result=$match_details['data']['match_result'];
				
				if($score_data AND $result){
					$match_result['score_data']=(object)$match_details['data']['score_data'];
					$match_result['result']=$match_details['data']['match_result'];
				}
				
			}
			$match_arr[$i]['match_result']=(object)$match_result;
		$i++;
		}

		$results['status'] = TRUE;
        $results['data'] =$match_arr;  
        return $results;
    }

	public function getCompletedMatches(){
		
		$match_data = $this->base_model->select_data('matches',array('status'=>'completed'));
		$match_arr =array();
		$i=0;
		foreach($match_data as $match){
			$res = json_decode($match->response);
			$team_name = $this->get_team_name($match->winner,$res->teams);
			$match_arr[$i]['unique_id']=$match->unique_id;
			$match_arr[$i]['title']=$match->title;
			$match_arr[$i]['match_title']=$match->match_title;
			$match_arr[$i]['short_name']=$match->short_name;
			$match_arr[$i]['sub_title']=$match->sub_title;
			$match_arr[$i]['format']=$match->format;
			$match_arr[$i]['status']=$match->status;
			$match_arr[$i]['winner']=$team_name;
			$match_arr[$i]['match_date']=date('Y-m-d H:i A',$match->match_date);
			$match_details = $this->getMatcheDetail($match->unique_id);
			$match_result=array();
			$match_result['result']=$match_details['data']['match_result'];
			$match_result['score_data']=$match_details['data']['score_data'];
			$match_arr[$i]['match_result']=$match_result;
		$i++;
		}

		$results['status'] = TRUE;
        $results['data'] =$match_arr;  
        return $results;
    }


	public function get_team_name($winner,$team){
		if($winner){
			return $team->$winner->name;
		}
		
		return '';
	}

	public function get_player_name($pid){ 
		return  $this->base_model->select_row('players',array('pid'=>$pid))->full_name;
	}


	public function getNotStartedMatches(){
		
		$match_data = $this->base_model->select_data('matches',array('status'=>'not_started'));
		$match_arr =array();
		$i=0;
		foreach($match_data as $match){
			$res = json_decode($match->response);
			$team_name = $this->get_team_name($match->winner,$res->teams);
			$match_arr[$i]['unique_id']=$match->unique_id;
			$match_arr[$i]['title']=$match->title;
			$match_arr[$i]['match_title']=$match->match_title;
			$match_arr[$i]['short_name']=$match->short_name;
			$match_arr[$i]['sub_title']=$match->sub_title;
			$match_arr[$i]['format']=$match->format;
			$match_arr[$i]['status']=$match->status;
			$match_arr[$i]['winner']=$team_name;
			$match_arr[$i]['match_date']=date('Y-m-d H:i A',$match->match_date);
		$i++;
		}

		$results['status'] = TRUE;
        $results['data'] =$match_arr;  
        return $results;
    }

	public function getStartedMatches(){
		
		$match_data = $this->base_model->select_data('matches',array('status'=>'started'));
		$match_arr =array();
		$i=0;
		foreach($match_data as $match){
			$res = json_decode($match->response);
			$team_name = $this->get_team_name($match->winner,$res->teams);
			$match_arr[$i]['unique_id']=$match->unique_id;
			$match_arr[$i]['title']=$match->title;
			$match_arr[$i]['match_title']=$match->match_title;
			$match_arr[$i]['short_name']=$match->short_name;
			$match_arr[$i]['sub_title']=$match->sub_title;
			$match_arr[$i]['format']=$match->format;
			$match_arr[$i]['status']=$match->status;
			$match_arr[$i]['winner']=$team_name;
			$match_arr[$i]['match_date']=date('Y-m-d H:i A',$match->match_date);
		$i++;
		}

		$results['status'] = TRUE;
        $results['data'] =$match_arr;  
        return $results;
    }


	public function getMatcheDetail($match_id){
		
		$match_data = $this->base_model->select_row('matches',array('unique_id'=>$match_id));
		if($match_data){
			$match = $match_arr=array();
			$res = json_decode($match_data->response);
			
			//echo '<pre>';print_r($res);die;
			$team_name = $this->get_team_name($match_data->winner,$res->teams);
			$match_arr['unique_id']=$match_data->unique_id;
			$match_arr['title']=$match_data->title;
			$match_arr['match_title']=$match_data->match_title;
			$match_arr['short_name']=$match_data->short_name;
			$match_arr['sub_title']=$match_data->sub_title;
			$match_arr['format']=$match_data->format;
			$match_arr['status']=$match_data->status;
			$match_arr['winner']=$team_name;
			$match_arr['match_date']=date('Y-m-d H:i A',$match_data->match_date);
			
			$teams = $this->get_match_teams($res);
			$venue = $this->match_venue($res);
			
			
			$match_result=$toss_winner='';
			$score=array();
			if($match_data->status == 'completed'){
				$match_result = $this->match_result($res);
				$score = $this->match_score($res);
			}
			if($match_data->status == 'completed' || $match_data->status == 'started'){
				$toss_winner = $this->toss_winner_team($res);
			}

		$match['detail'] =$match_arr;
		$match['teams'] =$teams;
		$match['venue'] =$venue;
		$match['toss_winner'] =$toss_winner;
		$match['match_result'] =$match_result;
		$match['score_data'] =$score;
		$match['squad'] =$this->getSquad($match_data);
		
		
		$results['status'] = TRUE;
        $results['data'] =$match;  
       
		}else{
			$results['status'] = FALSE;
        
		}
		return $results;
    }

	public function getSquad($match_data){
		$match_Data = $this->base_model->select_data('match_players',array('match_unique_id'=>$match_data->unique_id));
		$teams =array();
		$teams['team_a']['name']=$match_data->team_a;
		$teams['team_b']['name']=$match_data->team_b;
		
		foreach($match_Data as $match){
			$palyer = $this->get_player_name($match->player_unique_id);
				if($match->captain == 1){
					$palyer .='(C)';	
				}
				if($match->keeper == 1){
					$palyer .='(W)';	
				}

			if($match->team_type == 'a'){
				
				$teams['team_a']['playes'][]=$palyer;
			}

			if($match->team_type == 'b'){
				$teams['team_b']['playes'][]=$palyer;
			}
		}
		return $teams;
	}

	public function toss_winner_team($res){
		$team =array();
		$res->toss;
		if(isset($res->toss->winner)){
		$team['winner']=$this->get_team_name($res->toss->winner,$res->teams);
		$team['elected']=$res->toss->elected;
		}
		return $team;
	}

	public function match_result($res){  
		$res->toss;
		if(isset($res->play->result)){
		$team['winner']=$this->get_team_name($res->play->result->winner,$res->teams);
		$team['result_type']=$res->play->result->result_type;
		$team['msg']=$res->play->result->msg;
		$team['target_run']=$res->play->target->runs;
		$team['overs_per_innings']=$res->play->overs_per_innings[0];
		$team['first_batting']=$this->get_team_name(str_replace('_1','',$res->play->innings_order[0]),$res->teams);
		$team['second_batting']=$this->get_team_name(str_replace('_1','',$res->play->innings_order[1]),$res->teams);
		return $team;
		}else{ 
			return '';;
		}
	}

	public function match_venue($res){  
		$res->toss;
		$data['name']=$res->venue->name;
		$data['city']=$res->venue->city;
		return $data;
	}


	public function get_match_teams($res){
		$teams =array();
		foreach($res->teams as $k=>$team){
			$teams[]=$team->name;
		}
		return $teams;
	}

	public function match_score($res){ 
		$data=array();
		if(isset($res->play)){
		$innings = $res->play->innings;
		$team_a = $this->get_team_name('a',$res->teams);
		$team_b = $this->get_team_name('b',$res->teams);
		$data['team_a']['name']=$team_a;
		$data['team_a']['innings']=$innings->a_1;
		$data['team_b']['name']=$team_b;
		$data['team_b']['innings']=$innings->b_1;
		
		return $data;
		}else{
			return $data;
		}
	}



	public function  getMatchScoreBord($match_id)
	{
		
		

		$mdata = $this->base_model->select_row('matches',array('unique_id'=>$match_id,'status !='=>'not_started'));
		if($mdata){

		$this->db->select('match_players.*,players.full_name as player_name');
        $this->db->from('match_players');
        $this->db->join('players','players.pid=match_players.player_unique_id','left');
		$this->db->where('match_unique_id',$match_id);
        $query = $this->db->get();
		$match_data = $query->result();
		
		

		$team_a = $team_b = array();
		$team_a['batting']=$team_a['bowling']=$team_a['fielding']=$team_a['wickets']=$team_b['wickets']=$team_b['batting']=$team_b['bowling']=$team_b['fielding']=array();
		//echo '<pre>';print_r($team_a);die;
		$team_a['name'] = $mdata->team_a;
		$team_b['name'] = $mdata->team_b;
		$a_bat=$a_bol=$a_field = $b_bat=$b_bol=$b_field = $a_wkt=$b_wkt=0;
		foreach($match_data as $match){ 
			$res = json_decode($match->player_score_response);
			$player_unique_id=$match->player_unique_id;
			//$player=$res->$player_unique_id;
			//echo '<pre>';print_R($res);die;
			$index=1;
			if($match->team_type == 'a'){ 

				if($match->captain == 1){
					$team_a['captain'] = $match->player_name;
					
				}
				if($match->keeper == 1){
					$team_a['keeper'] = $match->player_name;
				}
				$team_a['playing_xi'][] = $match->player_name;
			//echo '<pre>';print_R($res->$player_unique_id);die;
				if(isset($res->$player_unique_id->score->$index->batting->score)){  
					$team_a['batting'][$a_bat]=$res->$player_unique_id->score->$index->batting->score;
					$team_a['batting'][$a_bat]->player=$match->player_name;
					$a_bat++;
				}

				if(isset($res->$player_unique_id->score->$index->batting->dismissal)){ 
					$team_a['wickets'][$a_wkt]=$res->$player_unique_id->score->$index->batting->dismissal;
					$team_a['wickets'][$a_wkt]->overs=implode('.',$res->$player_unique_id->score->$index->batting->dismissal->overs);
					$team_a['wickets'][$a_wkt]->player=$match->player_name;
					$a_wkt++;
				}

				if(isset($res->$player_unique_id->score->$index->bowling->score)){ 
					$team_a['bowling'][$a_bol]=$res->$player_unique_id->score->$index->bowling->score;
					$team_a['bowling'][$a_bol]->player=$match->player_name;
					$a_bol++;
				}

				if(isset($res->$player_unique_id->score->$index->fielding)){ 
					$team_a['fielding'][$a_field] =$res->$player_unique_id->score->$index->fielding;
					$team_a['fielding'][$a_field]->player=$match->player_name;
					$a_field++;
				}

			}

			if($match->team_type == 'b'){ 
				if($match->captain == 1){
					$team_b['captain'] = $match->player_name;
					//echo '<pre>';print_r($res);die;
				}
				if($match->keeper == 1){
					$team_b['keeper'] = $match->player_name;
				}
				$team_b['playing_xi'][] = $match->player_name;

				if(isset($res->$player_unique_id->score->$index->batting->score)){ 
					$team_b['batting'][$b_bat]=$res->$player_unique_id->score->$index->batting->score;
					$team_b['batting'][$b_bat]->player=$match->player_name;
					$b_bat++;
				}

				if(isset($res->$player_unique_id->score->$index->batting->dismissal)){ 
					$team_b['wickets'][$b_wkt]=$res->$player_unique_id->score->$index->batting->dismissal;
					$team_b['wickets'][$b_wkt]->overs=implode('.',$res->$player_unique_id->score->$index->batting->dismissal->overs);
					$team_b['wickets'][$b_wkt]->player=$match->player_name;
					$b_wkt++;
				}

				if(isset($res->$player_unique_id->score->$index->bowling->score)){ 
					$team_b['bowling'][$b_bol]=$res->$player_unique_id->score->$index->bowling->score;
					$team_b['bowling'][$b_bol]->player=$match->player_name;
					$b_bol++;
				}

				if(isset($res->$player_unique_id->score->$index->fielding)){ 
					$team_b['fielding'][$b_field] =$res->$player_unique_id->score->$index->fielding;
					$team_b['fielding'][$b_field]->player=$match->player_name;
					$b_field++;
				}

			}

		}

	//	echo '<pre>';print_r($team_a);die;
		
		$innigs = array();
		if(isset($team_a['playing_xi']) AND isset($team_b['playing_xi'])){
			$innigs['team_a']['name']=$team_a['name'];
			$innigs['team_a']['captain']=$team_a['captain'];
			$innigs['team_a']['keeper']=$team_a['keeper'];
			$innigs['team_a']['playing_xi']=$team_a['playing_xi'];

			$innigs['team_b']['name']=$team_b['name'];
			$innigs['team_b']['captain']=$team_b['captain'];
			$innigs['team_b']['keeper']=$team_b['keeper'];
			$innigs['team_b']['playing_xi']=$team_b['playing_xi'];

			$innigs['innigs_first']['team_a']['name'] = $team_a['name'];	
			$innigs['innigs_first']['team_a']['batting'] = $team_a['batting'];
			$innigs['innigs_first']['team_a']['bowling'] = $team_b['bowling'];
			$innigs['innigs_first']['team_a']['fielding'] = $team_b['fielding'];
			$innigs['innigs_first']['team_a']['wickets'] = $team_a['wickets'];

			$innigs['innigs_second']['team_b']['name'] = $team_b['name'];	
			$innigs['innigs_second']['team_b']['batting'] = $team_b['batting'];
			$innigs['innigs_second']['team_b']['bowling'] = $team_a['bowling'];
			$innigs['innigs_second']['team_b']['fielding'] = $team_a['fielding'];
			$innigs['innigs_second']['team_b']['wickets'] = $team_b['wickets'];
		}	

		$match_detail = $this->getMatcheDetail($match_id);
		//echo '<pre>';print_R($match_detail);die;
		$detail=array();
		$detail['match_detail']=$match_detail['data'];
		$detail['innigs']=$innigs;
		
		$results['status'] = TRUE;
        $results['data'] =$detail;  
        
		}else{
			$results['status'] = TRUE;
        	$results['data'] =array();
		}
		return $results;
    }



	public function getMatchLiveScore(){

		$project_id = $this->base_model->get_api_setting_data('key','project_id');
		$token = $this->base_model->get_api_setting_data('token','api_token');

		$match_array =array();
		
		$match_data=$this->base_model->select_data('matches',array('status'=>'started'));
		
		foreach($match_data as $ma){
			$curl = curl_init();
			$PROJ_KEY = $project_id;
			$API_TOKEN = $token;
			$key = $ma->unique_id;
			curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.sports.roanuz.com/v5/cricket/${PROJ_KEY}/match/${key}/",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
				"rs-token: ${API_TOKEN}"
			),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		$match =array();
		$res = json_decode($response);
		$match_data = $res->data;
		if($match_data->status == 'started'){
			$live = $match_data->play->live;
			$batting_team = $live->batting_team;
			$bowling_team = $live->bowling_team;
			$match['match_title']=$match_data->name;
			$match['match_key']=$ma->unique_id;
			$match['sub_title']=$match_data->sub_title;
			$match['format']=$match_data->format;
			$match['batting_team']=$match_data->teams->$batting_team->name;
			$match['bowling_team']=$match_data->teams->$bowling_team->name;

			$striker = $live->striker_key;
			$non_striker = $live->non_striker_key;
			$bowler = $live->bowler_key;

			$key=1;
			$match['striker']['palyer']=$match_data->players->$striker->player->legal_name;
			$match['striker']['score']=(array)$match_data->players->$striker->score->$key->batting->score;

			$match['non_striker']['palyer']=$match_data->players->$non_striker->player->legal_name;
			$match['non_striker']['score']=(array)$match_data->players->$non_striker->score->$key->batting->score;

			if(isset($live->bowler_key)){
				$match['bowler']['palyer']=$match_data->players->$bowler->player->legal_name;
				$match['bowler']['score']=(array)$match_data->players->$bowler->score->$key->bowling->score;
			}

			$match['required_score']= (array)$live->required_score;

			
			$match['score']['run']=$live->score->runs;
			$match['score']['balls']=$live->score->balls;
			$match['score']['wickets']=$live->score->wickets;
			$match['score']['run_rate']=$live->score->run_rate;
			$match['score']['title']=$live->score->title;
			$match['score']['overs']=implode('.',$live->score->overs);
			$match_array[]=$match;
		}
	}

		$results['status'] = TRUE;
        $results['data'] =$match_array;  
        return $results;
	}


	public function getMatchLiveScoreBoard($match_key){

		$project_id = $this->base_model->get_api_setting_data('key','project_id');
		$token = $this->base_model->get_api_setting_data('token','api_token');

		$match_array =array();
		$match_data=$this->base_model->select_data('matches',array('status'=>'started'));

		
			$curl = curl_init();
			$PROJ_KEY = $project_id;
			$API_TOKEN = $token;
			$key = $match_key;
			curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.sports.roanuz.com/v5/cricket/${PROJ_KEY}/match/${key}/",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
				"rs-token: ${API_TOKEN}"
			),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		$match =array();
		$res = json_decode($response);
		$match_data = $res->data;
	//	echo '<pre>';print_r($match_data);die;
		if($match_data->status == 'started'){
			$index=1;
			$live = $match_data->play->live;
			$inning_order_1=$match_data->play->innings_order[0];
			if(isset($match_data->play->innings_order[1])){
			$inning_order_2=$match_data->play->innings_order[1];
			}else{
			$inning_order_2='';
			}
			$innings=$match_data->play->innings;
			$batting_team = $live->batting_team;
			$bowling_team = $live->bowling_team;
			$match['match_title']=$match_data->name;
			$match['sub_title']=$match_data->sub_title;
			$match['format']=$match_data->format;
			$match['batting_team']=$match_data->teams->$batting_team->name;
			$match['bowling_team']=$match_data->teams->$bowling_team->name;

			$striker = $live->striker_key;
			$non_striker = $live->non_striker_key;
			$bowler = $live->bowler_key;

			$key=1;
			$match['striker']['palyer']=$match_data->players->$striker->player->legal_name;
			$match['striker']['score']=(array)$match_data->players->$striker->score->$key->batting->score;
			
			if(!empty($non_striker)){
				$match['non_striker']['palyer']=$match_data->players->$non_striker->player->legal_name;
				$match['non_striker']['score']=(array)$match_data->players->$non_striker->score->$key->batting->score;
			}else{
				$match['non_striker']['palyer']='';
				$match['non_striker']['score']=(array)'';
			}
			

			if(isset($live->bowler_key)){
				$match['bowler']['palyer']=$match_data->players->$bowler->player->legal_name;
				$match['bowler']['score']=(array)$match_data->players->$bowler->score->$key->bowling->score;
			}

			$match['required_score']= (array)$live->required_score;

			
			$match['score']['run']=$live->score->runs;
			$match['score']['balls']=$live->score->balls;
			$match['score']['wickets']=$live->score->wickets;
			$match['score']['run_rate']=$live->score->run_rate;
			$match['score']['title']=$live->score->title;
			$match['score']['overs']=implode('.',$live->score->overs);
				
			
			foreach($res->data->play->innings as $k => $val){
				foreach($res->data->play->innings->$k->batting_order as $v){
					$player_name=$res->data->players->$v->player->name;
					$player_batting_score=$res->data->players->$v->score->$key->batting->score;
					
					if(in_array($v, $res->data->squad->a->player_keys)){
						$cnt_name=$res->data->teams->a->name;
					}elseif(in_array($v, $res->data->squad->b->player_keys)){
						$cnt_name=$res->data->teams->b->name;
					}
					
					$match['score_board'][$cnt_name]['Batting'][$player_name]=array(
						'player_name'=>$player_name,
						'run'=>$player_batting_score->runs,
						'balls'=>$player_batting_score->balls,
						'fours'=>$player_batting_score->fours,
						'sixes'=>$player_batting_score->sixes,
						'strike_rate'=>$player_batting_score->strike_rate,
					);
				}
				
				foreach($res->data->play->innings->$k->bowling_order as $v){
					$player_name=$res->data->players->$v->player->name;
					$player_bowling_score=$res->data->players->$v->score->$key->bowling->score;
					
					if(in_array($v, $res->data->squad->a->player_keys)){
						$cnt_name=$res->data->teams->a->name;
					}elseif(in_array($v, $res->data->squad->b->player_keys)){
						$cnt_name=$res->data->teams->b->name;
					}
					
					$match['score_board'][$cnt_name]['Bowling'][$player_name]=array(
						'player_name'=>$player_name,
						'overs'=>$player_bowling_score->overs[0],
						'maiden_overs'=>$player_bowling_score->maiden_overs,
						'runs'=>$player_bowling_score->runs,
						'wickets'=>$player_bowling_score->wickets,
						'economy'=>$player_bowling_score->economy,
						'extras'=>$player_bowling_score->extras,
					);
				}
				
				foreach($res->data->play->innings->$k->wicket_order as $v){
					$player_name=$res->data->players->$v->player->name;
					$player_wicket_score=$res->data->players->$v->score->$key->batting->dismissal;
					
					if(in_array($v, $res->data->squad->a->player_keys)){
						$cnt_name=$res->data->teams->a->name;
					}elseif(in_array($v, $res->data->squad->b->player_keys)){
						$cnt_name=$res->data->teams->b->name;
					}
					
					$match['score_board'][$cnt_name]['Wicket'][$player_name]=array(
						'player_name'=>$player_name,
						'team_runs'=>$player_wicket_score->team_runs.'-'.$player_wicket_score->wicket_number,
						'over'=>$player_wicket_score->overs[0].'.'.$player_wicket_score->overs[1]
					);
				}
			}
			
			$match_array[]=$match;
		}
	

		$results['status'] = TRUE;
        $results['data'] =$match_array;  
        return $results;
	}

	public function player_stock_history($player_id,$user_id){
		$pstocks = $this->base_model->select_data('buy_stock',array('player_id'=>$player_id,'user_id'=>$user_id));
		$stock_data = $this->api_model->get_player_stock($player_id);
		$player_data = $this->base_model->select_row('players',array('id'=>$player_id));
		$commission= $player_data->commission;

		$unitPrice = $stock_data['unitPrice']-$commission;

		$stock = $user_price =$player_price = 0;

		foreach($pstocks as $pstock){
			$rstock = $pstock->remaining_stock;
			$stock +=$rstock;
			$uprice = $pstock->current_unit_price;
			$user_price +=($rstock*$uprice);
			$player_price +=($rstock*$unitPrice);
		}

		$whdata =array(
				'player_id'=>$player_id,
				'user_id'=>$user_id,
				'stock'=>$stock,
				'user_price'=>number_format((float)$user_price, 2, '.', ''),
				'player_price'=>number_format((float)$player_price, 2, '.', ''),
				'created_at'=>time()
			);
			$this->base_model->insert_data('player_stock_history',$whdata);

		return true;
							
	}


	public function user_player_stock_history($player_id){
		
		$this->db->select('*');
        $this->db->from('buy_stock');
        $this->db->group_by('user_id'); 
		$this->db->order_by('id','ASC');
		$query = $this->db->get(); 
		$user_stocks = $query->result_array();
		//echo $this->db->last_query();die;
		if(count($user_stocks) > 0){
			foreach($user_stocks as $ustocks){
				$user_id = $ustocks['user_id'];
				$pstocks = $this->base_model->select_data('buy_stock',array('player_id'=>$player_id,'user_id'=>$user_id));
				$stock_data = $this->api_model->get_player_stock($player_id);
				$player_data = $this->base_model->select_row('players',array('id'=>$player_id));
				$commission= $player_data->commission;

				$unitPrice = $stock_data['unitPrice']-$commission;

				$stock = $user_price =$player_price = 0;

				foreach($pstocks as $pstock){
					$rstock = $pstock->remaining_stock;
					$stock +=$rstock;
					$uprice = $pstock->current_unit_price;
					$user_price +=($rstock*$uprice);
					$player_price +=($rstock*$unitPrice);
				}
				if($stock !=0){
				$whdata =array(
						'player_id'=>$player_id,
						'user_id'=>$user_id,
						'stock'=>$stock,
						'user_price'=>number_format((float)$user_price, 2, '.', ''),
						'player_price'=>number_format((float)$player_price, 2, '.', ''),
						'created_at'=>time()
					);
					$this->base_model->insert_data('player_stock_history',$whdata);
					
					$this->add_user_stock_history($user_id);	
				}
		}
		}	
		return true;
							
	}


	public function getPlayerStockGraph($user_id,$player_id){
			
        $results['status'] = '';
        $results['data'] = array(); 
        $this->db->select('*');
        $this->db->from('player_stock_history');
        $this->db->where('user_id',$user_id);
        $this->db->where('player_id',$player_id);
		$this->db->order_by('id','ASC');
		$query = $this->db->get(); 
		$stocks = $query->result_array();
		$stock_arr=array();
		$i=0;
		foreach($stocks as $stock){
			$stock_arr[$i]=$stock;
			$stock_arr[$i]['created_at']=date('Y-m-d H:i:s',$stock['created_at']);
		$i++;
		}
		
		$results['status'] = TRUE;
        $results['data'] =$stock_arr;  
        return $results;
    }


	public function get_user_kyc_data($user_id){  
        $results['status'] = '';
        $results['data'] = array(); 
        $this->db->select('user_kyc.*,a.file as adharcard_file,p.file as pancard_file');
        $this->db->from('user_kyc');
		$this->db->join('upload_images as a','a.id=user_kyc.adhar_file_id','left');
		$this->db->join('upload_images as p','p.id=user_kyc.pan_file_id','left');
        $this->db->where('user_id',$user_id);
        $query = $this->db->get(); 
		$userdata = $query->row_array();
					
        if($userdata){
			$userdata['adharcard_file']=base_url().'assets/uploads/adharcard/'.$userdata['adharcard_file'];			
			$userdata['pancard_file']=base_url().'assets/uploads/pancard/'.$userdata['pancard_file'];
			$results['status'] = TRUE;
            $results['data'] =$userdata;            
        }else{          
            $results['status'] = FALSE;         
        }
        return $results;
    }


	public function checkKycStatus($user_id){  
        $results['status'] = '';
        $results['data'] = array(); 
        $this->db->select('user_kyc.*');
        $this->db->from('user_kyc');
		$this->db->where('user_id',$user_id);
        $query = $this->db->get(); 
		$userdata = $query->row_array();
		if($userdata){
            if($userdata['status'] == 1){
				$results['status'] = 1;
            }else{
				$results['status'] = 2;
			}           
        }else{          
            $results['status'] = 0;         
        }
        return $results;
    }
	
	
	public function forgotPassword($email){  
        $results['status'] = '';
        $results['data'] = array(); 
        $this->db->select('*');
        $this->db->from('users');
		$this->db->where('email',$email);
        $query = $this->db->get(); 
		$userdata = $query->row_array();
		if($userdata){
			$response=rand(100,999).str_shuffle("Fancy").rand(10,555).str_shuffle("Stock").rand(1000,9999);
			$this->base_model->update_data('users',array('email'=>$email),array('password'=>sha1($response)));
			
			$to_email = $email;
			$subject = 'Sportstakes : New Password';
			$cdata['password']=$response;
			$cdata['username']=$userdata['full_name'];
			$body = $this->load->view('email_template/forget_password',$cdata,true);
			$this->send_mail($body,$to_email,$subject);
				
            $results['status'] = TRUE; 
        }else{
			$results['status'] = FALSE; 
		 }    
        return $results;
    }


	public function send_mail($body,$to_email,$subject){
	    $from_email = 'info@sportstakes.co.in';
	    $from_name ="Info";
		
		require 'vendor/autoload.php';
		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->SMTPAuth   = true;
		$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; /* prefix for secure */
		$mail->Host       = "mail.abackteam.in";
		$mail->Port    = 	587;
		$mail->IsHTML(true);
		$mail->Username   = "info@sportstakes.co.in";
		$mail->Password   = "]-wU.s}5z?1V";
		$mail->SMTPOptions = array(
			'ssl' => array(
			'verify_peer' => false,
			'verify_peer_name' => false,
			'allow_self_signed' => true
			)
		);
		$mail->From    = $from_email;
		$mail->FromName   = $from_name;
		$headers = "MIME-Version: 1.0\n";
		$headers .= "Content-Type: text/html; charset=\"iso-8859-1\"\n";
		$headers .="From: ".$from_email;
		$mail->Subject = $subject;
		$mail->AddReplyTo($from_email);
		$mail->MsgHTML($body);

		$mail->AddAddress("$to_email");
		if(!$mail->Send()){
			return 'error';
			//return "Mailer Error: " . $mail->ErrorInfo;
		}else{
			return "success";
		}
	}	
	
	
	public function getMyReferralUsers($code){
		
		$results['status'] = '';
		$results['data'] = array();	
		$this->db->select('users.*,,upload_images.file');
		$this->db->join('upload_images','upload_images.id=users.file_id','left');
		$this->db->where('users.referralCode',$code);
		$query = $this->db->get('users');
		$users = $query->result_array();
		$users_arr=array();
		$i=0;
		foreach($users as $user){ 
			if($user['file']){
				$user['profileImage']=base_url().'assets/uploads/users/'.$user['file'];	
			}else{
				$user['profileImage']='';
			}
			$users_arr[$i]=$user;
		$i++;
		}
		$results['status'] = TRUE;
		$results['data'] =$users_arr;
		return $results;
	}

	public function getIplTeams(){
		
		$results['status'] = '';
		$results['data'] = array();	
		$this->db->select('teams.*');
		$query = $this->db->get('teams');
		$teams = $query->result_array();
		$teams_arr=array();
		$i=0;
		foreach($teams as $team){ 
			$team['image']=base_url('assets/uploads/teams/').$team['image'];
			$teams_arr[$i]=$team;
			
		$i++;
		}
		$results['status'] = TRUE;
		$results['data'] =$teams_arr;
		return $results;
	}
	
	
	public function getTeamPlayers($team_name){
		
		$this->db->select('players.*,,upload_images.file,all_country.countryname,player_type.label as player_type,player_type.icon,match_players.player_unique_id,match_players.match_unique_id');
		$this->db->join('upload_images','upload_images.id=players.file_id','left');
		$this->db->join('all_country','all_country.id=players.country_id','left');
		$this->db->join('player_type','player_type.id=players.player_id','left');
		$this->db->join('match_players','match_players.player_unique_id=players.pid','left');
		$this->db->where('match_players.team_name',$team_name);
		$query = $this->db->get('players');
		$players = $query->result_array();
		
		$players_arr=array();
		$i=0;
		foreach($players as $player){ 
			$ctry = $this->base_model->select_row('all_country',array('id'=>$player['country_id']));
			if($player['file']){
				$player['profileImage']=base_url().'assets/uploads/players/'.$player['file'];	
			}else{
				$player['profileImage']='';
			}
			$player['countryImage']=base_url('assets/images/country_flags/').strtolower($ctry->code).'.png';
			$player['playerTypeImage']=base_url('assets/uploads/player_type/').$player['icon'];
			$players_arr[$i]=$player;
			$players_arr[$i]['stocks']=$this->get_player_stock($player['id']);
		$i++;
		}
		$results['status'] = TRUE;
		$results['data'] =$players_arr;
		return $results;
    }
	
	
	
	public function getMatchePlayers($match_id){
		
		$this->db->select('players.*,,upload_images.file,all_country.countryname,player_type.label as player_type,player_type.icon,match_players.player_unique_id,match_players.match_unique_id');
		$this->db->join('upload_images','upload_images.id=players.file_id','left');
		$this->db->join('all_country','all_country.id=players.country_id','left');
		$this->db->join('player_type','player_type.id=players.player_id','left');
		$this->db->join('match_players','match_players.player_unique_id=players.pid','left');
		$this->db->where('match_players.match_unique_id',$match_id);
		$query = $this->db->get('players');
		$players = $query->result_array();
		
		$players_arr=array();
		$i=0;
		foreach($players as $player){ 
			$ctry = $this->base_model->select_row('all_country',array('id'=>$player['country_id']));
			if($player['file']){
				$player['profileImage']=base_url().'assets/uploads/players/'.$player['file'];	
			}else{
				$player['profileImage']='';
			}
			$player['countryImage']=base_url('assets/images/country_flags/').strtolower($ctry->code).'.png';
			$player['playerTypeImage']=base_url('assets/uploads/player_type/').$player['icon'];
			$players_arr[$i]=$player;
			$players_arr[$i]['stocks']=$this->get_player_stock($player['id']);
		$i++;
		}
		$results['status'] = TRUE;
		$results['data'] =$players_arr;
		return $results;
    }
	
	
	public function get_my_stock_data($user_id){
		
		$res=array();
		$results['status'] = '';
        $results['data'] = array(); 
        $this->db->select('*');
        $this->db->from('buy_stock');
        $this->db->where('user_id',$user_id);
        $this->db->where('remaining_stock !=',0);
		$this->db->order_by('id','ASC');
		$query = $this->db->get(); 
		$stock_arr = $query->result_array();
		$cnt=0;
		$player_data=array();
		foreach($stock_arr as $stock_arr_1){
			$player_data[$stock_arr_1['player_id']]['unitPrice'][] =$stock_arr_1['current_unit_price'];
			$player_data[$stock_arr_1['player_id']]['remaining_stock'][] =$stock_arr_1['remaining_stock'];
		}
		
		if($player_data){
			 $i=0;
			foreach($player_data as $player_id=>$pd){
				$user_price =$total_stock=0;
				$player_info = $this->get_player_basic_detail($player_id);
				$player_stock = $this->get_player_stock($player_id);
				
				$res[$i]['player_name']=$player_info['full_name'];
				$res[$i]['profileImage']=$player_info['profileImage'];
				$res[$i]['playerTypeImage']=$player_info['playerTypeImage'];
				
				for($k=0;$k < count($pd['unitPrice']);$k++){
					$user_price +=$pd['unitPrice'][$k]*$pd['remaining_stock'][$k];
					$total_stock +=$pd['remaining_stock'][$k];
				}
				$res[$i]['user_stock_price']=number_format((float)$user_price, 2, '.', '');
				$res[$i]['stocks']=$total_stock;
				
				
				$player_stock_price=0;
				
				if((array)($player_stock)){
					$player_stock_price = $total_stock*$player_stock['unitPrice'];
				}
				$res[$i]['player_stock_price']=number_format((float)$player_stock_price, 2, '.', '');;
				
			 $i++;}
		}
		
		$results['status'] = TRUE;
        $results['data'] =$res;  
        return $results;
    }
	
	public function get_player_basic_detail($player_id){
		
		$this->db->select('players.*,,upload_images.file,all_country.countryname,player_type.label as player_type,player_type.icon');
		$this->db->join('upload_images','upload_images.id=players.file_id','left');
		$this->db->join('all_country','all_country.id=players.country_id','left');
		$this->db->join('player_type','player_type.id=players.player_id','left');
		$this->db->where('players.status',1);
		$this->db->where('players.id',$player_id);
		$query = $this->db->get('players');
		$player = $query->row_array();
		
		if($player['file']){
			$player['profileImage']=base_url().'assets/uploads/players/'.$player['file'];	
		}else{
			$player['profileImage']=base_url().'assets/uploads/users/default_user.jpg';
		}
		
		$player['playerTypeImage']=base_url('assets/uploads/player_type/').$player['icon'];
		$players_arr=$player;
		return $players_arr;
	}
	
	
	public function add_user_stock_history($user_id){
		
		$this->db->select('*');
        $this->db->from('buy_stock');
        $this->db->where('user_id',$user_id);
        $this->db->where('remaining_stock !=',0);
		$this->db->order_by('id','ASC');
		$query = $this->db->get(); 
		$stock_arr = $query->result_array();
		
		$cnt=0;
		$player_data=array();
		foreach($stock_arr as $stock_arr_1){
			$player_data[$stock_arr_1['player_id']]['unitPrice'][] =$stock_arr_1['current_unit_price'];
			$player_data[$stock_arr_1['player_id']]['remaining_stock'][] =$stock_arr_1['remaining_stock'];
		}
		
		if($player_data){
			 $i=0;$user_price =$total_stock=$player_stock_price =0;
			foreach($player_data as $player_id=>$pd){
				
				$player_stock = $this->get_player_stock($player_id);
				
				for($k=0;$k < count($pd['unitPrice']);$k++){
					$user_price +=$pd['unitPrice'][$k]*$pd['remaining_stock'][$k];
					$total_stock +=$pd['remaining_stock'][$k];
				}
				
				$player_stock_price=0;
				if((array)($player_stock)){
					$player_stock_price = $total_stock*$player_stock['unitPrice'];
				}
				$player_stock_price +=$player_stock_price;
				
			 $i++;}
			 
			 $whdata =array(
						'user_id'=>$user_id,
						'stock'=>$total_stock,
						'user_price'=>number_format((float)$user_price, 2, '.', ''),
						'player_price'=>number_format((float)$player_stock_price, 2, '.', ''),
						'created_at'=>time(),
					);
					$this->base_model->insert_data('user_stock_history',$whdata);
			 
			 
		}
		return true;					
	}
}
?>