<div class="modal-header">
  <h4 class="modal-title">Edit receipt</h4>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
   <span aria-hidden="true">&times;</span>
 </button>
</div>
<form  id="update_receipt" method="post" onsubmit="receipt_update();return false;">
  <input type="hidden"  name="id"  value="<?php echo $cinfo->id;?>">
  <div class="modal-body">
    <div class="row">
      <!-- left column -->
      <div class="col-md-12">
        <!-- general form elements -->
        <div class="row">
		<div class="col-md-12">
            <div class="form-group">
              <label >Email<sup class="text-danger">*</sup></label>
              <input type="email" class="form-control" name="email"  placeholder="Enter Email" value="<?php echo $cinfo->email;?>">
            </div>
          </div>
         <div class="col-md-6">
          <div class="form-group">
            <label >Name<sup class="text-danger">*</sup></label>
            <input type="text" class="form-control" name="name"  placeholder="Name" value="<?php echo $cinfo->name;?>">
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

  
	function receipt_update()
	{
	
		$('#update_receipt .st_loader').show();
		$.ajax({  
		  url :BASE_URL+"admin/receipt/receipt_update",  
		  method:"POST",  
		  dataType:"json",  
		  data:$("#update_receipt").serialize(),
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
			  toastr.success('Receipt Info Updated Successfully','Success');
			  $('#modal-default').modal('hide');
			  $('#modal-default .modal-content').html('');
			  table.draw( false );
			}
			$('#update_receipt .st_loader').hide();
		  }  
		}); 
	}
  
</script>			