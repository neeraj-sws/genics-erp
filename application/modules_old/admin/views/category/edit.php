<div class="modal-header">
  <h4 class="modal-title">Edit Category</h4>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
   <span aria-hidden="true">&times;</span>
 </button>
</div>
<form  id="update_user" method="post" onsubmit="category_update();return false;">
  <input type="hidden"  name="id"  value="<?php echo $cinfo->id;?>">
  <div class="modal-body">
    <div class="row">
      <!-- left column -->
      <div class="col-md-12">
        <!-- general form elements -->
        <div class="row">
         <div class="col-md-6">
          <div class="form-group">
            <label >Title<sup class="text-danger">*</sup></label>
            <input type="text" class="form-control" name="title"  placeholder="Title" value="<?php echo $cinfo->title;?>">
          </div>
        </div>
		
        <div class="col-md-6">
   <div class="form-group">
    <label >Status<sup class="text-danger">*</sup></label>
    <select class="form-control" id="status" name="status">
     <option <?php if($cinfo->status==1){ echo 'selected'; }else{ echo ''; } ?> value="1">Active</option>
     <option  <?php if($cinfo->status==0){ echo 'selected'; }else{ echo ''; } ?> value="0">Inactive</option>
   </select>
 </div>
</div>
    </div>
    

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
		  url: '<?php echo base_url() ?>admin/user/users_image_data',
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
	
	function category_update()
	{
	
		$('#update_user .st_loader').show();
		$.ajax({  
		  url :BASE_URL+"admin/category/category_update",  
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
			  toastr.success('Category Updated Successfully','Success');
			  $('#modal-default').modal('hide');
			  $('#modal-default .modal-content').html('');
			  table.draw( false );
			}
			$('#update_user .st_loader').hide();
		  }  
		}); 
	}
  
</script>			