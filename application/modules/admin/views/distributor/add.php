		
		<div class="modal-header">
      <h4 class="modal-title">Add Distributor</h4>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
       <span aria-hidden="true">&times;</span>
     </button>
   </div>
   <form  id="add_user" method="post" onsubmit="user_save();return false;">
    <div class="modal-body">
      <div class="row">
        <!-- left column -->
        <div class="col-md-12">
          <!-- general form elements -->

          <div class="row">
           <div class="col-md-6">
            <div class="form-group">
              <label >Full  Name<sup class="text-danger">*</sup></label>
              <input type="text" class="form-control" name="full_name"  placeholder="Full Name">
            </div>
          </div>
          <div class="col-md-6">
           <div class="form-group">
            <label >Email<sup class="text-danger">*</sup></label>
            <input type="text" class="form-control" name="email" placeholder="Email Address">
          </div>
        </div>
      </div>
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label >Phone Number<sup class="text-danger">*</sup></label>
					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text country_code_label">+91</span>
						</div>
						<input type="text" class="form-control allowno"  name="phone" placeholder="Phone Number">
						<input type="hidden" id="country_code" name="country_code" value="971">
					</div>
				</div>
			</div>

			<div class="col-md-6">
				<div class="form-group SelectFullWidth">
					<label class='d-block'>City<sup class="text-danger">*</sup></label>
					<select class="form-control select2" name="city" id="role_id">
						<option value="">--Select City--</option>
						<?php foreach($roles as $role){ ?>
							<option value="<?php echo $role->id;?>"><?php echo $role->city_name;?></option>       
						<?php } ?>
					</select>
				</div>
			</div>
		</div>
		<div class="row">
		  <div class="col-md-6">
		   <div class="form-group">
			<label >Password<sup class="text-danger">*</sup></label>
			<input type="password" class="form-control"  name="password" placeholder="Password">
		  </div>
		</div>

		<div class="col-md-6">
		  <div class="form-group">
			<label >Status<sup class="text-danger">*</sup></label>
			<select class="form-control" id="state" name="status">
			 <option value="1">Active</option>
			 <option value="0">Inactive</option>
		   </select>
		 </div>
		</div>
		</div>
<!-- 
		<div class="col-md-12">
		  <div class="form-group">
			<label >Role<sup class="text-danger">*</sup></label>
			<select class="form-control" id="role" name="role">
			 <option value="2">Production</option>
			 <option value="3">Sales</option>
			 <option value="4">Distributor</option>
		   </select>
		 </div>
		</div> -->
		<div class="row">
		<div class="col-md-6">
				<div class="form-group SelectFullWidth">
					<label class='d-block'>Category<sup class="text-danger">*</sup></label>
					<select class="form-control select2" name="category" id="role_id">
						<option value="">--Select Category--</option>
						<?php foreach($categorys as $category){ ?>
							<option value="<?php echo $category->id;?>"><?php echo $category->title;?></option>       
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group SelectFullWidth">
					<label class='d-block'>Admin<sup class="text-danger">*</sup></label>
					<select class="form-control " name="admin" id="is_admin">
						<option value="">--Select Admin--</option>
							<option value="1">Yes</option>       
							<option value="0">No</option>  
							<option value="2">Selected Admin</option>   
					
					</select>
				</div>
			</div>
		</div>
		
		<div class="row">
		    <div class="col-md-6" id="selectedAdminDiv" style="display: none;">
				<div class="form-group SelectFullWidth">
					<label class='d-block'>Selected Distributor<sup class="text-danger">*</sup></label>
					<select class="form-control select2" name="selected_admin[]" id="role_id" multiple>
						<option value="">--Selected Distributor--</option>
						<?php foreach($distributors as $distributor){ ?>
							<option value="<?php echo $distributor->id;?>"><?php echo $distributor->full_name;?></option>       
						<?php } ?>
					</select>
				</div>
			</div>
		</div>

		
		<hr>
		<div class="row">
			<div class="col-11">
			  <div class="form-group">
				<label >Profile<sup class="text-danger">*</sup></label>
				<div class="input-group">
				  <div class="custom-file">
					<input type="file" class="custom-file-input" name="file" onchange="upload_photo('add_user')">
					<input type="hidden" name="file_id" id="file_id" value="">
					<label class="custom-file-label" for="exampleInputFile">Choose file</label>
				  </div>
				</div>
			  </div>
			</div>
			<div class="col-1">
			 <label style="width:100%;">&nbsp;</label>
			 <span class="pull-right load2"  style="display:none;"><i class="fa fa-refresh fa-spin fa-1x fa-fw" ></i></span>
			 <img id="simage" src="" class="preview_comman img-fluid">
			</div>
		  </div>

<!-- /.card-body -->
</div>
</div>
</div>
</div>
<div class="modal-footer justify-content-between">
  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
  <button type="submit" class="btn btn-primary">Save changes <i class="st_loader fa-btn-loader fa fa-refresh fa-spin fa-1x fa-fw" style="display:none;"></i></button>
</div>
</form>

<script>
    $(document).ready(function () {
       
        $("#is_admin").change(function () {

            var selectedValue = $(this).val();

            if (selectedValue == "2") {
                $("#selectedAdminDiv").show();
            } else {
                $("#selectedAdminDiv").hide();
            }
        });
    });
</script>

<script type="text/javascript">
  $(document).ready(function() {
   $('.select2').select2();

   $(".allowno").keypress(function (e) {
     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
      event.preventDefault();m
    }
  }); 
   $('.allowno').on('paste',function (e){
    if (e.originalEvent.clipboardData.getData('Text').match(/[^\d]/)) {
      e.preventDefault();
    }
  });
 }); 

	function upload_photo(id)
	{
		$('.load2').show();
		$.ajax({
		  type: "POST",
		  url: '<?php echo base_url() ?>admin/distributor/users_image_data',
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
  
	function user_save()
	{
		$('#add_user .st_loader').show();
		$.ajax({  
		  url :BASE_URL+"admin/distributor/user_save",  
		  method:"POST",  
		  dataType:"json",  
		  data:$("#add_user").serialize(),
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
			  toastr.success('User Info Added Successfully','Success');
			  $('#modal-default').modal('hide');
			  $('#modal-default .modal-content').html('');

			  table.draw();
			}
			$('#add_user .st_loader').hide();
		  }  
		}); 
	}

</script>	