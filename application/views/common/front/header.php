<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- IonIcons -->
  <link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/fontawesome-free/css/all.min.css">
   <!-- IonIcons -->
  <link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <!-- IonIcons -->
  <link rel="stylesheet" href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  
  <link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">

  <!-- Datepicker -->
  <link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/datepicker/datepicker3.css">
  <link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/datetimepicker/bootstrap-datetimepicker.css">
  

  <!-- Select2 -->
  <link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/select2/css/select2.min.css">

  <link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <link rel="stylesheet" href="<?php echo base_url();?>assets/dist/css/adminlte.css">
  
  <link href="<?php echo base_url(); ?>assets/plugins/toastr/toastr.min.css" rel="stylesheet">
  <link href="<?php echo base_url(); ?>assets/plugins/datepicker/datepicker3.css" rel="stylesheet">

  <!-- Theme style -->
  <!-- <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
 
  <link rel="stylesheet" href="<?php echo base_url();?>assets/css/custom.css"> -->
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



    <!--====== Required meta tags ======-->
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <meta name="description" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <!--====== Title ======-->
    <title> Order Cart </title>

    <!--====== Favicon Icon ======-->
    <link rel="shortcut icon" href="<?php echo base_url();?>front-assets/images/order.png" type="img/png" />
    <!--====== Animate Css ======-->
    <link rel="stylesheet" href="<?php echo  base_url();?>front-assets/css/animate.min.css">
    <!--====== Bootstrap css ======-->
    <link rel="stylesheet" href="<?php echo base_url();?>front-assets/css/bootstrap.min.css" />
    <!--====== Fontawesome css ======-->
    <link rel="stylesheet" href="<?php echo base_url();?>front-assets/css/font-awesome.min.css" />
    <!--====== Flaticon css ======-->
    <link rel="stylesheet" href="<?php echo base_url();?>front-assets/css/flaticon.css" />
    <!--====== Magnific Popup css ======-->
    <link rel="stylesheet" href="<?php echo  base_url();?>front-assets/css/magnific-popup.css" />
    <!--====== Slick  css ======-->
    <link rel="stylesheet" href="<?php echo  base_url();?>front-assets/css/slick.css" />
    <!--====== Jquery ui ======-->
    <link rel="stylesheet" href="<?php echo base_url();?>front-assets/css/jquery-ui.min.css" />
    <!--====== Style css ======-->
    <link href="<?php echo  base_url();?>front-assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo  base_url();?>front-assets/css/reset.css" rel="stylesheet">
    <link href="<?php echo  base_url();?>front-assets/css/style.css" rel="stylesheet">
    <link href="<?php echo  base_url();?>front-assets/css/responsive.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/plugins/toastr/toastr.min.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Antonio:wght@700&family=Open+Sans:wght@300;400;500;600;700&family=Oswald:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script type="text/javascript">
    let BASE_URL = '<?php echo base_url();?>';
   let is_user_login = '<?php echo $this->session->userdata('is_user_login'); ?>';
  </script>
  </head>
  <body>
  <div class="container-custom">  
  <header class="Mainheader w-100" id="headerPart">        
                <div class="logopart d-flex justify-content-between align-items-center">
                    <a href="javascript:void(0);" class="">
                        <img src="<?php echo base_url();?>front-assets/images/logo.png">
                    </a>
                    <?php if( $this->session->userdata('is_user_login') ){?>
                    <a href="javascript:void(0)" onclick="logout('<?php echo strtolower($this->session->userdata('title_user'));?>')" class="text-dark float-end">
                    <span class="glyphicon glyphicon-log-out"></span><i class="fas fa-sign-out-alt" aria-hidden="true"></i>          
                    </a>
                    <?php  }?>
                </div>  
                <div class="firstHeading text-center">
                    <h4 class="mb-0"><?php echo $buttonheading;?></h4>
                </div>
                <?php if(!empty($heading)){ ?>
                  <div class="firstHeading text-center">
                    <h4 class="mb-0 heading"><?php echo $heading;?></h4>
                </div>
                  <?php } ?>
        </header>