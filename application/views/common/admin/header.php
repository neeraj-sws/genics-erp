<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">

  <title><?php echo $site_setting->title;?></title>

  <!-- Font Awesome Icons -->
 <link rel="icon" href="<?php echo base_url('assets/uploads/site_setting/');echo $site_setting->fav_image;?>" type="image/png" sizes="32x32">
  
  <!-- IonIcons -->
  <link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

  <!-- IonIcons -->
  <link rel="stylesheet" href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  
  <link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">

  <!-- Datepicker -->
  <!-- <link rel="stylesheet" href="<?php //echo base_url();?>assets/plugins/datepicker/datepicker3.css"> -->
  <link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/datetimepicker/bootstrap-datetimepicker.css">
  

  <!-- Select2 -->
  <link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/select2/css/select2.min.css">

  <link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <link rel="stylesheet" href="<?php echo base_url();?>assets/dist/css/adminlte.css">
  
  <link href="<?php echo base_url(); ?>assets/plugins/toastr/toastr.min.css" rel="stylesheet">
  <link href="<?php echo base_url(); ?>assets/plugins/datepicker/datepicker3.css" rel="stylesheet">

  <!-- Theme style -->
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
 
  <link rel="stylesheet" href="<?php echo base_url();?>assets/css/custom.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/summernote/summernote-bs4.css">
  <script src="<?php echo base_url();?>assets/js/jquery-3.2.1.min.js"></script>
  <script src="<?php echo base_url(); ?>assets/plugins/toastr/toastr.min.js"></script>
  
  <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
  <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script> 

  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />


  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
 
  <script src="https://code.highcharts.com/highcharts.js"></script>
 
  <script src="https://cdn.tiny.cloud/1/ubs34e6ruxvxkf8yt4jd4uehgvqw2umxzt3u533xtw14e7v2/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/plugins/clockpicker/bootstrap-clockpicker.css">
  <script type="text/javascript">
    let BASE_URL = '<?php echo base_url();?>';
    let is_admin_login = '<?php echo $this->session->userdata('is_admin_login'); ?>';
  </script>
  <style>
   .brand-link {
    padding: 20px 0px 60px .5rem;
  }
  .img-circle {
    border-radius: 0%!important; 
  }
  .elevation-3 {
   box-shadow:none!important; 
 }
 .brand-link .brand-image {
  max-height: 53px;
  width: 60%;
}
.SelectFullWidth span.select2-container {
    display: block !important;
}
</style>
</head>
<body class="hold-transition sidebar-mini ">
  <div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>
    
      <ul class="navbar-nav ml-auto">
        <li><a href="javascript:void(0)" onclick="logout('<?php echo strtolower($this->session->userdata('title_type'));?>')" class="btn btn-primary">
          <span class="glyphicon glyphicon-log-out"></span><i class="fas fa-sign-out-alt" aria-hidden="true"></i>          
        </a></li> 

      </ul>
    </nav>
    <!-- /.navbar -->

