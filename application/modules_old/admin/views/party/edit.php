<div class="modal-header">
  <h4 class="modal-title">Edit Party</h4>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
   <span aria-hidden="true">&times;</span>
 </button>
</div>
<form  id="update_user" method="post" onsubmit="user_update();return false;">
  <input type="hidden"  name="id"  value="<?php echo $uinfo->id;?>">
  <div class="modal-body">
    <div class="row">
      <!-- left column -->
      <div class="col-md-12">
        <!-- general form elements -->
        <div class="row">
         <div class="col-md-6">
          <div class="form-group">
            <label >Full Name<sup class="text-danger">*</sup></label>
            <input type="text" class="form-control" name="full_name"  placeholder="Full Name" value="<?php echo $uinfo->full_name;?>">
          </div>
        </div>
        <div class="col-md-6">
         <div class="form-group">
          <label >Email<sup class="text-danger">*</sup></label>
          <input type="text" class="form-control" name="email" placeholder="Email Address" value="<?php echo $uinfo->email;?>">
        </div>
      </div>
    </div>
    <div class="row">
		<div class="col-md-6">
			<div class="form-group SelectFullWidth">
				<label class='d-block'>City<sup class="text-danger">*</sup></label>
				<select class="form-control select2" name="city" id="role_id">
					<option value="">--Select City--</option>
					<?php foreach($roles as $role){ ?>
						<option <?php if($uinfo->city==$role->id){ echo 'selected'; } ?> value="<?php echo $role->id; ?>"><?php echo $role->city_name; ?></option>       
					<?php } ?>
				</select>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label >Phone Number<sup class="text-danger">*</sup></label>
				<div class="input-group mb-3">
					<div class="input-group-prepend">
					<span class="input-group-text country_code_label">+91</span>
					</div>
					<input type="text" class="form-control allowno"  name="phone" placeholder="Phone Number" value="<?php echo $uinfo->phone;?>">
				</div>
			</div>
		</div>
	</div>
<div class="row">
 <div class="col-md-6">
   <div class="form-group">
    <label >Status<sup class="text-danger">*</sup></label>
    <select class="form-control" id="status" name="status">
     <option <?php if($uinfo->status==1){ echo 'selected'; }else{ echo ''; } ?> value="1">Active</option>
     <option  <?php if($uinfo->status==0){ echo 'selected'; }else{ echo ''; } ?> value="0">Inactive</option>
   </select>
 </div>
</div>

<!-- <div class="col-md-6">
		  <div class="form-group">
			<label >Role<sup class="text-danger">*</sup></label>
			<select class="form-control" id="role" name="role">
			 <option <?php if($uinfo->role== 2){echo 'selected';}else{echo '';}?> value="2">Production</option>
			 <option <?php if($uinfo->role== 3){echo 'selected';}else{echo '';}?>  value="3">Sales</option>
			 <option <?php if($uinfo->role== 4){echo 'selected';}else{echo '';}?>  value="4">Distributor</option>
		   </select>
		 </div>
		</div> -->
</div>
<hr>

		<!-- <div class="row">
			<div class="col-11">
			  <div class="form-group">
				<label >Profile<sup class="text-danger">*</sup></label>
				<div class="input-group">
				  <div class="custom-file">
					<input type="file" class="custom-file-input" name="file" onchange="upload_photo('update_user')">
					<input type="hidden" name="file_id" id="file_id" value="<?php echo $uinfo->file_id;?>">
					<label class="custom-file-label" for="exampleInputFile">Choose file</label>
				  </div>
				</div>
			  </div>
			</div>
			<div class="col-1">
			 <label style="width:100%;">&nbsp;</label>
			 <span class="pull-right load2"  style="display:none;"><i class="fa fa-refresh fa-spin fa-1x fa-fw" ></i></span>
			 <img id="simage" src="<?php echo base_url('assets/uploads/users/').$uinfo->file;?>" class="preview_comman img-fluid">
			</div>
		  </div> -->

</div>
<!-- /.card-body -->
</div>
</div>
</div>
</div>
<div class="modal-footer justify-content-between">
  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
  <button type="submit" class="btn btn-primary">Update <i class="st_loader fa-btn-loader fa fa-refresh fa-spin fa-1x fa-fw" style="display:none;"></i></button>
</div>
</form>

<script type="text/javascript">
  $(document).ready(function() {
   $('.select2').select2();	
   $(".allowno").keypress(function (e) {
     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
      event.preventDefault();
    }
  }); 
 }); 

  function upload_photo(id)
	{
		$('.load2').show();
		$.ajax({
		  type: "POST",
		  url: '<?php echo base_url() ?>admin/party/users_image_data',
		  contentType: false,       
		  cache: false,             
		  processData:false,
		  dataType: "json",
		  data: new FormData ($("#"+id)[0]), 
		  success: function(res)
		  { 

			if(res.status == 0){
			  var err = JSON.parse(res.msg);
			  var er = '';
			  $.each(err, function(k, v) { 
				er = ' * ' + v; 
				toastr.error(er,'Error');
			  });
			  $(".custom-file-input").val('');
			}else{
			  $('#simage').attr('src',res.image_data);
			  $('#simage').show();        
			  $('#file_id').val(res.image_id);  
			}
			$('.load2').hide();
		  }
		});
	}
	
	function user_update()
	{
	
		$('#update_user .st_loader').show();
		$.ajax({  
		  url :BASE_URL+"admin/party/user_update",  
		  method:"POST",  
		  dataType:"json",  
		  data:$("#update_user").serialize(),
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
			  toastr.success('User Info Updated Successfully','Success');
			  $('#modal-default').modal('hide');
			  $('#modal-default .modal-content').html('');
			  table.draw( false );
			}
			$('#update_user .st_loader').hide();
		  }  
		}); 
	}
  
</script>			