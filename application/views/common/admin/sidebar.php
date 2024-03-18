 <!-- Main Sidebar Container -->
 <aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="<?php echo base_url();?>admin" class="brand-link">
  <img src="<?php echo base_url('assets/uploads/site_setting/');echo $site_setting->logo_image;?>" alt="AdminLTE Logo" class="brand-image img-circle elevation-3">
  </a>
  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
       <?php if($this->session->userdata('profile') == ''){ ?>
        <img src="<?php echo base_url();?>assets/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
      <?php }else{ ?>
        <img src="<?php echo base_url('assets/uploads/site_setting/');echo $site_setting->fav_image;?>" class="img-circle elevation-2" alt="User Image">
        <!-- <img src="<?php echo base_url('assets/uploads/users/dumy_user.png')?>" class="img-circle elevation-2" alt="User Image"> -->
      <?php } ?>
    </div>
    <div class="info">
      <a href="javascript:void(0);" class="d-block"><?php echo $this->session->userdata('title_name');?>
      <br><small> (<?php 
      if($this->session->userdata('role') == 2){
        echo "Production";
      }elseif($this->session->userdata('role')== 3 ){
        echo "Sales";
      }elseif($this->session->userdata('role')== 4){
        echo "Distributor";
      }else{
        echo "Admin";
      }
      
      //echo $this->session->userdata('title_type');?>)</small>
    </a>       
  </div>
</div>    
<!-- Sidebar Menu -->
<nav class="mt-2">
  <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
           with font-awesome or any other icon font library -->
             
            <li class="nav-item has-treeview <?php if($nav == 'dashboard'){echo 'menu-open';}?>">
              <a href="<?php echo base_url();?>admin" class="nav-link <?php if($nav == 'dashboard'){echo 'active';}?>">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
              </a>
            </li> 
            <li class="nav-item">
              <a href="<?php echo base_url();?>admin/distributor" class="nav-link <?php if($nav == 'distributor'){echo 'active';}?>">
                <i class="nav-icon fas fa-user-alt"></i>
                <p>Sales Person  <span class="badge badge-primary ml-2"><?php echo $distributor->count;?></span></p>
              </a>
            </li>   
            <li class="nav-item">
              <a href="<?php echo base_url();?>admin/receipt" class="nav-link <?php if($nav == 'receipt'){echo 'active';}?>">
                <i class="nav-icon fas fa-user-alt"></i>
                <p>Receipt <span class="badge badge-primary ml-2"><?php echo $receipt->count;?></span></p>
              </a>
            </li>          
            <li class="nav-item">
              <a href="<?php echo base_url();?>admin/party" class="nav-link <?php if($nav == 'party'){echo 'active';}?>">
              <i class='fas fa-users nav-icon fas fa-user-alt' style='font-size:15px'></i>
                <p>Parties <span class="badge badge-primary ml-2"><?php echo $users->count;?></span></p>
              </a>
            </li> 
            <li class="nav-item">
              <a href="<?php echo base_url();?>admin/category" class="nav-link <?php if($nav == 'category'){echo 'active';}?>">
                <!-- <i class="nav-icon fas fa-user-alt"></i> -->
                <i class="nav-icon fas fa-user-alt"></i>
                <p>Category <span class="badge badge-primary ml-2"><?php echo $categorys->count;?></span></p>
              </a>
            </li>
       <li class="nav-item">
              <a href="<?php echo base_url();?>admin/order" class="nav-link <?php if($nav == 'order'){echo 'active';}?>">
              <i class=" nav-icon fa fa-shopping-cart"></i>
                <p>Order <span class="badge badge-primary ml-2"><?php echo $orders->count;?></span></p>
              </a>
            </li> 
            <li class="nav-item">
              <a href="<?php echo base_url();?>admin/delivery_boy" class="nav-link <?php if($nav == 'delivery_boy'){echo 'active';}?>">
                <i class="nav-icon fas fa-user-alt"></i>
                <p>Delivery Boy  <span class="badge badge-primary ml-2"><?php echo $delivery_boy->count;?></span></p>
              </a>
            </li>
       
			 
			
			

<li class="nav-item has-treeview <?php if($nav == 'setting'){echo 'menu-open';}?>">
		<a href="#" class="nav-link <?php if($nav == 'setting'){echo 'active';}?>">
			<i class="nav-icon fa fa-cog"></i>
			<p>Setting<i class="fas fa-angle-left right"></i></p>
		</a>
		<ul class="nav nav-treeview">
			<li class="nav-item">
				<a href="<?php echo base_url().'admin/resetpassword';?>" class="nav-link <?php if($sub_nav == 'resetpassword'){echo 'active';}?>">
					<i class="far fa-circle nav-icon"></i>
					<p>Reset Password</p>
				</a>
			</li> 
			<li class="nav-item">
				<a href="<?php echo base_url().'admin/site_setting';?>" class="nav-link <?php if($sub_nav == 'site_setting'){echo 'active';}?>">
					<i class="far fa-circle nav-icon"></i>
					<p>Site Setting</p>
				</a>
			</li>
	
		</ul>
</li>		
</nav>
<!-- /.sidebar-menu -->
</div>
<!-- /.sidebar -->
</aside>