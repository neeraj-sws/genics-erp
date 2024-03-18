<div class="content-wrapper">
 <!-- Content Header (Page header) -->
 <section class="content-header">
  <div class="container-fluid">
   <div class="row mb-2">
    <div class="col-sm-6">
     <h1>Reset Password</h1>
   </div>
 </div>
</div>
<!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">
  <div class="row">
   <div class="col-12">
    <div class="card">
     <!-- /.card-header -->
     <form  id="update_password_admin" method="post" onsubmit="update_password_admin();return false;">
      <div class="modal-body">
        <div class="row">
          <!-- left column -->
          <div class="col-md-4">
            <!-- general form elements -->
            <div class="row">
             <div class="col-md-12">
              <div class="form-group">
                <label >Old Password<sup class="text-danger">*</sup></label>
                <div class="input-group mb-3 password_only">
                 <input type="password" name="opass"  class="form-control" placeholder="Password*">
                 <span toggle="#password-field" class="fa fa-fw fa-eye field_icon toggle-password"  onclick="showPaasword(this)"></span>
               </div>
             </div>
           </div>
           <div class="col-md-12">
             <div class="form-group">
              <label >New Password<sup class="text-danger">*</sup></label>
              <div class="input-group mb-3 password_only">
               <input type="password" name="new_pass"  class="form-control" placeholder="Password*">
               <span toggle="#password-field" class="fa fa-fw fa-eye field_icon toggle-password"  onclick="showPaasword(this)"></span>
             </div>
           </div>
         </div>
         <div class="col-md-12">
           <div class="form-group">
            <label >Confirm Password<sup class="text-danger">*</sup></label>
            <div class="input-group mb-3 password_only">
             <input type="password" name="cpass"  class="form-control" placeholder="Password*">
             <span toggle="#password-field" class="fa fa-fw fa-eye field_icon toggle-password" onclick="showPaasword(this)"></span>
           </div>
         </div>
       </div>
     </div>
   </div>
 </div>
</div>
<div class="modal-footer justify-content-between">
  <button type="submit" class="btn btn-primary">Update Password <i class="st_loader fa-btn-loader fa fa-refresh fa-spin fa-1x fa-fw" style="display:none;"></i></button>
</div>
</form>
<!-- /.card-body -->
</div>
<!-- /.card -->

</div>
<!-- /.col -->
</div>
</section>
</div>
</div>
</div>
</div>
</div>
</div>
<!-- /.container-fluid -->
</section>
<!-- /.content -->
</div>
</div>
<!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->
</div>
<!-- /.row -->
</section>
<!-- /.content -->
</div>

<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
 <!-- Control sidebar content goes here -->
</aside>
<!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<script>
  function update_password_admin(){
    $('#update_password_admin .st_loader').show();
    $.ajax({  
     url :BASE_URL+"admin/update_password_admin",  
     method:"POST",  
     dataType:"json",  
     data:$("#update_password_admin").serialize(),
     success:function(res){  
      if(res.status == 0){
        var err = JSON.parse(res.msg);
        var er = '';
        $.each(err, function(k, v) { 
          er += v+'<br>'; 
        }); 
        toastr.error(er,'Error');
      }else if(res.status == 2){	
        toastr.error(res.msg,'Error');
      }else{
       toastr.success('Password Updated Successfully','Success');
       $('#update_password_admin')[0].reset();
       $('#modal-default').modal('hide');
       $('#modal-default .modal-content').html('');
     }
     $('#update_password_admin .st_loader').hide();
   }  
 }); 
  }

  function showPaasword(e){
   $(e).toggleClass("fa-eye fa-eye-slash");
   if($(e).parent().find('input').attr("type") === "password") {
     $(e).parent().find('input').attr("type", "text");
   }else{
     $(e).parent().find('input').attr("type", "password");
   }
 }
</script>