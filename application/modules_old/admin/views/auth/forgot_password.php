<!-- /.login-logo -->
<div class="card">
  <div class="card-body login-card-body">
   <div class="login-logo">
     <img src="<?php echo base_url('assets/uploads/site_setting/').$site_setting->logo_image;?>">
   </div>
   <p class="login-box-msg">You forgot your password? Here you can easily retrieve a new password.</p>

   <form class="emall-form registration_form" id="forgot_submit" method="post" onsubmit="forgot_submit();return false;">
    <div class="input-group mb-3">
      <input type="email" class="form-control" placeholder="Email" name="email">
      <div class="input-group-append">
        <div class="input-group-text">
          <span class="fas fa-envelope"></span>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12 col-md-12 pb-15 captcha">
        <div class="g-recaptcha" data-sitekey="<?php echo $this->config->item('google_key') ?>"></div> 
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <button type="submit" class="btn btn-primary btn-block" style="background: #019e89 !important; border: 0 !important;">Request new password  <i class="st_loader fa-btn-loader fa fa-refresh fa-spin fa-1x fa-fw" style="display:none;"></i></button>
      </div>
      <!-- /.col -->
    </div>
  </form>

  <p class="mt-3 mb-1 text-center">
    Back to<u><a href="<?php echo base_url();?>admin/login">Login</a></u>
  </p>

</div>
<!-- /.login-card-body -->
</div>


<script>

  function forgot_submit(){ 
   $('#forgot_submit .st_loader').show();
   $.ajax({  
    url :BASE_URL+"admin/login/forgot_submit",  
    method:"POST",  
    dataType:"json",  
    data:$("#forgot_submit").serialize(),
    success:function(res){  
     if(res.status == 0){
       var err = JSON.parse(res.msg);
       var er = '';
       $.each(err, function(k, v) { 
        er += v+'<br>'; 
      }); 
       toastr.error(er,'Error');
       grecaptcha.reset();
     }else if(res.status == 2){
       toastr.error('Sorry Google Recaptcha Unsuccessful!!','Error');
       grecaptcha.reset();
     }else if(res.status == 3){
       toastr.error('Email Not Valid','Error');
       grecaptcha.reset();
     }else{
      toastr.success('Reset Password Link Sent To Your Email Id','Success');
      var surl = BASE_URL+'admin'; 
      window.setTimeout(function() { window.location = surl; }, 500);
    }
    $('#forgot_submit .st_loader').hide();
  }  
}); 
 }
</script>