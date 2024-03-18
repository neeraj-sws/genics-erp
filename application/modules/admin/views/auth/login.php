<div class="login-box">
  <!-- /.login-logo -->
  <div class="card">
    <div id="message"></div>
    <div class="card-body login-card-body">
      <div class="login-logo py-4">
        <img src="<?php echo base_url('assets/uploads/site_setting/').$site_setting->logo_image;?>" class="py-2">
       
      </div>
      <p class="login-box-msg">Sign In</p>
      <form class="appxpo-form" name="loginforms" id="loginforms" method="post" onsubmit="loginSubmit();return false;">
        <div class="input-group mb-3">
          <input type="email" name="email" class="form-control" placeholder="Email*" value="<?php if(!empty($admin_email)){ echo $admin_email; } ?>">
        </div>
        <div class="input-group mb-3 password_only">
          <input type="password" name="password" id="pass_log_id" class="form-control" placeholder="Password*" value="<?php if(!empty($admin_password)){ echo $admin_password; } ?>">
          <span toggle="#password-field" class="fa fa-fw fa-eye field_icon toggle-password"></span>
        </div>
        <div class="row">
          <div class="col-12 col-md-12 pb-15 captcha">
            <div class="g-recaptcha" data-sitekey="<?php echo $this->config->item('google_key') ?>"></div> 
          </div>
        </div>
        <div class="row">
          <div class="col-8">
            <div class="icheck-primary">
              <input type="checkbox" id="remember" name="remember">
              <label for="remember">
                Remember Me 
              </label>
            </div>
          </div>
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" name="submit" id="login_submit1" class="btn btn-primary pull-right" > Sign in <i class="st_loader fa-btn-loader fa fa-refresh fa-spin fa-1x fa-fw" style="display:none;"></i></button>
          </div>
          <!-- /.col -->
        </div>
      </form>
      <!-- /.social-auth-links -->
      <p class="mb-1 d-none" >
        <a href="<?php echo base_url();?>admin/login/forgot_password">Forget Password?</a>
      </p>
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<script>
  $(document).ready(function(){
    $("body").on('click', '.toggle-password', function() {
      $(this).toggleClass("fa-eye fa-eye-slash");
      var input = $("#pass_log_id");
      if (input.attr("type") === "password") {
       input.attr("type", "text");
     } else {
       input.attr("type", "password");
     }
   });
  });

  function loginSubmit(){
   $('#loginforms .st_loader').show();
   $.ajax({  
    url :BASE_URL+"admin/login/loginSubmit",  
    method:"POST",  
    dataType:"json",  
    data:$("#loginforms").serialize(),
    success:function(res){  
     if(res.status == 0){
       var err = JSON.parse(res.msg);
       var er = '';
       $.each(err, function(k, v) { 
        er += v+'<br>'; 
      }); 
       toastr.error(er,'Error');
      $('#loginforms .st_loader').hide();
      }else{
      toastr.success('Login Success','Success');
      var surl = BASE_URL+'admin'; 
      window.setTimeout(function() { window.location = surl; }, 500);
    }
    $('#loginforms .st_loader').hide();
  }  
}); 
 }
</script>