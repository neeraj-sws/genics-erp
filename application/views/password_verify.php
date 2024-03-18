
<div class="login-box">
  <!-- /.login-logo -->
  <div class="card">
  <div id="message"></div>
    <div class="card-body login-card-body">
        <div class="login-logo">
    <img src="http://theemall.ae/beta/html/assets/image/logo.png">
  </div>
		<?php if($status == 1){ ?>
		<div class="alert alert-success text-center" role="alert">
		  Email Successfully verified, Your Password will send to your Email.
		</div>
		<?php }else{ ?>
		<div class="alert alert-danger text-center" role="alert">
		  Email Successfully Failed, Please Contact to Site Admin.
		</div>
		<?php } ?>
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<script>
$(window).on('load', function(){
    var surl = '<?php echo base_url();?>'; 
	window.setTimeout(function() { window.location = surl; }, 1000);
});
	</script>