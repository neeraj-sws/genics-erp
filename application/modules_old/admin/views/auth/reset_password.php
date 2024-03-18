<div class="card">
  <div class="card-body login-card-body">
   <div class="login-logo">
    <img src="<?php echo base_url('assets/uploads/site_setting/').$site_setting->logo_image;?>">
  </div>
  <p class="login-box-msg">You are only one step a way from your new password, recover your password now.</p>
  <form class="emall-form registration_form" id="reset_submit" method="post" onsubmit="reset_submit();return false;">
    <div class="input-group mb-3 d-none">
      <input type="text" class="form-control" placeholder="Email" name="email" value="<?php echo $email; ?>">
      <input type="hidden"  name="id" value="<?php echo $uinfo->id;?>">
      <div class="input-group-append">
        <div class="input-group-text">
          <span class="fas fa-envelope"></span>
        </div>
      </div>
    </div>
    <div class="input-group mb-3 passDIV">
      <input type="password" class="form-control password_input" placeholder="New Password" name="new_pass">
      <div class="input-group-append eyeDIV">
        <div class="input-group-text">
          <span class="fa fa-eye" onclick="password_eyeIcon(this);"></span>
        </div>
      </div>
    </div>
    <div class="input-group mb-3 passDIV">
      <input type="password" class="form-control password_input" name="confirm_pass" placeholder="Confirm Password">
      <div class="input-group-append eyeDIV">
        <div class="input-group-text">
          <span class="fa fa-eye" onclick="password_eyeIcon(this);"></span>
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
        <button type="submit" class="btn btn-primary btn-block" style="background: #019e89 !important; border: 0 !important;">Change password <i class="st_loader fa-btn-loader fa fa-refresh fa-spin fa-1x fa-fw" style="display:none;"></i></button>
      </div>
      <!-- /.col -->
    </div>
  </form>

  <p class="mt-3 mb-1 text-center">
    Back to <u><a href="<?php echo base_url();?>admin/login">Login</a></u>
  </p>
</div>
<!-- /.login-card-body -->
</div>

<script>
 function password_eyeIcon(e){ 
 $(e).toggleClass("fa-eye fa-eye-slash");
 var input = $(e).parent().parent().parent().find('.password_input');
 if(input.attr('type')=="password"){
   input.attr("type", "text");
 }else{
   input.attr("type", "password");
 }
 }
  function reset_submit(){ 
   $('#reset_submit .st_loader').show();
   $.ajax({  
    url :BASE_URL+"admin/login/reset_submit",  
    method:"POST",  
    dataType:"json",  
    data:$("#reset_submit").serialize(),
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
       $('#reset_submit .st_loader').hide();
     }else{
      toastr.success('Password Successfully Updated','Success');
      var surl = BASE_URL+'admin'; 
      window.setTimeout(function() { window.location = surl; }, 500);
    }
    $('#reset_submit .st_loader').hide();
  }  
}); 
 }
</script>