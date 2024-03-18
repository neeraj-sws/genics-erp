<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends Base_Controller {

    public function __construct(){ 
		parent::__construct();
		$this->load->model('Base_model');
		$this->load->model('Site_model');
		 $this->load->model('login_model');
		$this->load->library('session');
	}
	
	 public function index()
	{ 
    
       
        if(!$this->session->userdata('is_user_login')){ redirect(base_url('order-login')); }
        $data['code'] = $this->base_model->generate_code(5);
        $data['party'] = $this->base_model->select_data('orders',array());
        $data['distributor_id'] = $this->session->userdata('id');
        $data['buttonheading'] = 'Dashboard';

        $categorys = $this->base_model->select_data('category',array());
        $distributor = $this->base_model->select_row('distributor',array('id'=>$this->session->userdata('id'))); 
        $where1 =array();
        if($distributor){ 
        if($distributor->is_admin==0){ 
          foreach($categorys as $category){
           
        if($distributor->category == $category->id && $category->title == 'in_house') { 
         $where1 = '(distributor_other = '.$this->session->userdata('id').' OR distributor_id='.$this->session->userdata('id').')'; 
        }elseif($distributor->category == $category->id && $category->title == 'third_party'){
          $where1 = ' (distributor_third_party = '.$this->session->userdata('id').')';
        } 
      } 
        $unassignOrder = $this->base_model->select_join_result('orders', $where1.' AND is_new = 1 AND status= 0'); 
          $data['unassignOrderCount'] = count($unassignOrder);

          $dispatchedOrder = $this->base_model->select_join_result('orders', $where1.' AND is_dispatch = 1 AND status= 0');
          $data['dispatchedOrderCount'] = count($dispatchedOrder);
          
          $pandingOrder = $this->base_model->select_join_result('orders', $where1.' AND is_hold = 1 AND status= 0');
          $data['pandingOrderCount'] = count($pandingOrder); 

          $deliverOrder = $this->base_model->select_join_result('orders',$where1.' AND status= 1');
          $data['deliverOrderCount'] = count($deliverOrder); 

          $cancelledOrder = $this->base_model->select_join_result('orders',$where1.' AND is_cancel = 1 AND status= 0' );
          $data['cancelOrderCount'] = count($cancelledOrder);

          $totalOrder = $this->base_model->select_join_result('orders',$where1);
          $data['totalOrderCount'] = count($totalOrder);

          $todaysSalesCount=0;
          $currentMonthSalesCount=0;
          $totalSalesCount=0;
          $currentfinancialSalesCount =0;
         
          //TODAY SALES
          $todaysDate = date('Y-m-d');
          $todaysSales = $this->base_model->select_join_result('orders', $where1 . " AND created_at = '$todaysDate'");

          foreach ($todaysSales as $order) {
              $itemscount = $this->base_model->select_data('order_item',array('order_code' => $order->code)); 
             
              // echo "<pre>"; print_r($itemscount);
              foreach($itemscount as $item) {
            $total = ($item->price * $item->quantity); 
            $todaysSalesCount +=$total; } 
         
           
          }
          $data['todaysSalesCount']= $todaysSalesCount;

          //CURRENT ,ONTH SALES
        
          $firstDayOfMonth = date('Y-m-01'); // Get the first day of the current month
          $lastDayOfMonth = date('Y-m-t');   // Get the last day of the current month

          $currentMonthSales = $this->base_model->select_join_result('orders',$where1 . " AND created_at >= '$firstDayOfMonth' AND created_at <='$lastDayOfMonth'");
          foreach ($currentMonthSales as $order) {
              $itemscount = $this->base_model->select_data('order_item',array('order_code' => $order->code)); 
             
              // echo "<pre>"; print_r($itemscount);
              foreach($itemscount as $item) {
            $total = ($item->price * $item->quantity); 
            $currentMonthSalesCount +=$total; } 
          } 
          $data['currentMonthSalesCount']= $currentMonthSalesCount;

          //TOTAL SALES
          $totalSales = $this->base_model->select_join_result('orders',$where1);
          foreach ($totalSales as $order) { 
              $itemscount = $this->base_model->select_data('order_item',array('order_code' => $order->code)); 
             
              // echo "<pre>"; print_r($itemscount);
              foreach($itemscount as $item) {
            $total = ($item->price * $item->quantity); 
            $totalSalesCount +=$total; } 
         
          }
          $data['totalSalesCount']=$totalSalesCount;

          //CURRENT FINANCIAL SALES
          $currentDate = date('Y-m-d'); 
          $currentYear = date('Y', strtotime($currentDate)); 
          $financialYearStart = ($currentYear - 1) . '-04-01'; 
        
          $financialYearEnd = $currentYear . '-03-31';

          $currentYearSales = $this->base_model->select_join_result('orders',$where1 . " AND created_at >= '$financialYearStart' AND created_at <='$financialYearEnd'");
        
          foreach ($currentYearSales as $order) { 
            $itemscount = $this->base_model->select_data('order_item',array('order_code' => $order->code)); 
           
            // echo "<pre>"; print_r($itemscount);
            foreach($itemscount as $item) {
          $total = ($item->price * $item->quantity); 
          $currentfinancialSalesCount +=$total; } 
       
         
        }
        $data['currentfinancialSalesCount']=$currentfinancialSalesCount;

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
       
            $columns = "DATE_FORMAT(orders.created_at, '%Y-%m') AS month, IFNULL(SUM(order_item.price * order_item.quantity), 0) AS total_sales";
            $group_by = "month";
            $order_by = "month";
            
            $joins = array(
                array('table' => 'orders', 'condition' => 'orders.code = order_item.order_code', 'jointype' => 'inner')
            );
            
            $queryResult = $this->base_model->select_join_result('order_item',$where1 . " AND orders.created_at >= '$oneYearAgo' AND orders.created_at <='$lastDayOfMonth'", $joins, $columns, '', $group_by, $order_by);
            

        $last12MonthsSales = [];

        foreach ($last12Months as $month) {
            $last12MonthsSales[$month] = 0; 
        }

        foreach ($queryResult as $row) {
            $last12MonthsSales[$row->month] = $row->total_sales;
        }
       $data['last12MonthsSales']=$last12MonthsSales;
      //  print_r($last12MonthsSales);die;
     }elseif($distributor->is_admin==1){ 
      $unassignOrder = $this->base_model->select_join_result('orders', array('status'=>0,'is_new'=>1)); 
          $data['unassignOrderCount'] = count($unassignOrder);

          $dispatchedOrder = $this->base_model->select_join_result('orders', array('is_dispatch'=>1,'status'=>0)); 
          $data['dispatchedOrderCount'] = count($dispatchedOrder);
          
          $pandingOrder = $this->base_model->select_join_result('orders', array('status'=>0,'is_hold'=>1));
          $data['pandingOrderCount'] = count($pandingOrder); 

          $deliverOrder = $this->base_model->select_join_result('orders', array('status'=>1));
          $data['deliverOrderCount'] = count($deliverOrder); 

          $cancelledOrder = $this->base_model->select_join_result('orders', array('status'=>0,'is_cancel'=>1));
          $data['cancelOrderCount'] = count($cancelledOrder);

          $totalOrder = $this->base_model->select_join_result('orders', array());
          $data['totalOrderCount'] = count($totalOrder);

          $todaysSalesCount=0;
          $currentMonthSalesCount=0;
          $totalSalesCount=0;
          $currentfinancialSalesCount =0;
         
          //TODAY SALES

          $todaysSales = $this->base_model->select_join_result('orders', array('created_at'=>date('y-m-d')));

          foreach ($todaysSales as $order) {
              $itemscount = $this->base_model->select_data('order_item',array('order_code' => $order->code)); 
             
              // echo "<pre>"; print_r($itemscount);
              foreach($itemscount as $item) {
            $total = ($item->price * $item->quantity); 
            $todaysSalesCount +=$total; } 
         
           
          }
          $data['todaysSalesCount']= $todaysSalesCount;

          //CURRENT ,ONTH SALES
        
          $firstDayOfMonth = date('Y-m-01'); // Get the first day of the current month
          $lastDayOfMonth = date('Y-m-t');   // Get the last day of the current month

          $currentMonthSales = $this->base_model->select_join_result('orders', array(
              
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
          $data['currentMonthSalesCount']= $currentMonthSalesCount;

          //TOTAL SALES
          $totalSales = $this->base_model->select_join_result('orders', array());
          foreach ($totalSales as $order) { 
              $itemscount = $this->base_model->select_data('order_item',array('order_code' => $order->code)); 
             
              // echo "<pre>"; print_r($itemscount);
              foreach($itemscount as $item) {
            $total = ($item->price * $item->quantity); 
            $totalSalesCount +=$total; } 
         
          }
          $data['totalSalesCount']=$totalSalesCount;

          //CURRENT FINANCIAL SALES
          $currentDate = date('Y-m-d'); 
          $currentYear = date('Y', strtotime($currentDate)); 
          $financialYearStart = ($currentYear - 1) . '-04-01'; 
        
          $financialYearEnd = $currentYear . '-03-31';

          $currentYearSales = $this->base_model->select_join_result('orders', array(
              
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
        $data['currentfinancialSalesCount']=$currentfinancialSalesCount;

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
       $data['last12MonthsSales']=$last12MonthsSales;
      //  print_r($last12MonthsSales);die;


     }
     else{ 
      $distributor = $this->base_model->select_row('distributor',array('id'=>$this->session->userdata('id'))); 

    $selected_admin =  $distributor->selected_admin;
      $category = $this->base_model->select_row('category',array('id'=>$distributor->category));
       
    if($distributor->category == $category->id && $category->title == 'in_house') { 
    
     $where1 = '(distributor_other = '.$this->session->userdata('id').' OR distributor_id = '.$this->session->userdata('id').' OR distributor_id IN ('.$selected_admin.') OR distributor_other IN ('.$selected_admin.') OR distributor_third_party IN ('.$selected_admin.'))';

    }elseif($distributor->category == $category->id && $category->title == 'third_party'){  
      $where1 = ' (distributor_third_party = '.$this->session->userdata('id').' OR distributor_id IN ('.$selected_admin.') OR distributor_other IN ('.$selected_admin.') OR distributor_third_party IN ('.$selected_admin.'))';
    
    } 

    
   
    $unassignOrder = $this->base_model->select_join_result('orders', $where1. ' AND is_new = 1 AND status= 0'); 
      $data['unassignOrderCount'] = count($unassignOrder);

      $dispatchedOrder = $this->base_model->select_join_result('orders', $where1. ' AND is_dispatch = 1 AND status= 0');
      $data['dispatchedOrderCount'] = count($dispatchedOrder);
      
      $pandingOrder = $this->base_model->select_join_result('orders', $where1. ' AND is_hold = 1 AND status= 0');
      $data['pandingOrderCount'] = count($pandingOrder); 

      $deliverOrder = $this->base_model->select_join_result('orders',$where1. ' AND status= 1');
      $data['deliverOrderCount'] = count($deliverOrder); 

      $cancelledOrder = $this->base_model->select_join_result('orders',$where1. ' AND is_cancel = 1 AND status= 0' );
      $data['cancelOrderCount'] = count($cancelledOrder);

      $totalOrder = $this->base_model->select_join_result('orders',$where1);
      $data['totalOrderCount'] = count($totalOrder);

      $todaysSalesCount=0;
      $currentMonthSalesCount=0;
      $totalSalesCount=0;
      $currentfinancialSalesCount =0;
     
      //TODAY SALES
      $todaysDate = date('Y-m-d');
      $todaysSales = $this->base_model->select_join_result('orders', $where1 . " AND created_at = '$todaysDate'");

      foreach ($todaysSales as $order) {
          $itemscount = $this->base_model->select_data('order_item',array('order_code' => $order->code)); 
         
          // echo "<pre>"; print_r($itemscount);
          foreach($itemscount as $item) {
        $total = ($item->price * $item->quantity); 
        $todaysSalesCount +=$total; } 
     
       
      }
      $data['todaysSalesCount']= $todaysSalesCount;

      //CURRENT ,ONTH SALES
    
      $firstDayOfMonth = date('Y-m-01'); // Get the first day of the current month
      $lastDayOfMonth = date('Y-m-t');   // Get the last day of the current month

      $currentMonthSales = $this->base_model->select_join_result('orders',$where1 . " AND created_at >= '$firstDayOfMonth' AND created_at <='$lastDayOfMonth'");
      foreach ($currentMonthSales as $order) {
          $itemscount = $this->base_model->select_data('order_item',array('order_code' => $order->code)); 
         
          // echo "<pre>"; print_r($itemscount);
          foreach($itemscount as $item) {
        $total = ($item->price * $item->quantity); 
        $currentMonthSalesCount +=$total; } 
      } 
      $data['currentMonthSalesCount']= $currentMonthSalesCount;

      //TOTAL SALES
      $totalSales = $this->base_model->select_join_result('orders',$where1);
      foreach ($totalSales as $order) { 
          $itemscount = $this->base_model->select_data('order_item',array('order_code' => $order->code)); 
         
          // echo "<pre>"; print_r($itemscount);
          foreach($itemscount as $item) {
        $total = ($item->price * $item->quantity); 
        $totalSalesCount +=$total; } 
     
      }
      $data['totalSalesCount']=$totalSalesCount;

      //CURRENT FINANCIAL SALES
      $currentDate = date('Y-m-d'); 
      $currentYear = date('Y', strtotime($currentDate)); 
      $financialYearStart = ($currentYear - 1) . '-04-01'; 
    
      $financialYearEnd = $currentYear . '-03-31';

      $currentYearSales = $this->base_model->select_join_result('orders',$where1 . " AND created_at >= '$financialYearStart' AND created_at <='$financialYearEnd'");
    
      foreach ($currentYearSales as $order) { 
        $itemscount = $this->base_model->select_data('order_item',array('order_code' => $order->code)); 
       
        // echo "<pre>"; print_r($itemscount);
        foreach($itemscount as $item) {
      $total = ($item->price * $item->quantity); 
      $currentfinancialSalesCount +=$total; } 
   
     
    }
    $data['currentfinancialSalesCount']=$currentfinancialSalesCount;

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
   
        $columns = "DATE_FORMAT(orders.created_at, '%Y-%m') AS month, IFNULL(SUM(order_item.price * order_item.quantity), 0) AS total_sales";
        $group_by = "month";
        $order_by = "month";
        
        $joins = array(
            array('table' => 'orders', 'condition' => 'orders.code = order_item.order_code', 'jointype' => 'inner')
        );
        
        $queryResult = $this->base_model->select_join_result('order_item',$where1 . " AND orders.created_at >= '$oneYearAgo' AND orders.created_at <='$lastDayOfMonth'", $joins, $columns, '', $group_by, $order_by);
        

    $last12MonthsSales = [];

    foreach ($last12Months as $month) {
        $last12MonthsSales[$month] = 0; 
    }

    foreach ($queryResult as $row) {
        $last12MonthsSales[$row->month] = $row->total_sales;
    }
   $data['last12MonthsSales']=$last12MonthsSales;
  //  print_r($last12MonthsSales);die;
 }
    }

        $this->loadUserTemplate('front/dashboard',$data);
                    
                }
	
}