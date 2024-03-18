<div class="modal-header">
      <h4 class="modal-title">Add Receipt</h4>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
       <span aria-hidden="true">&times;</span>
     </button>
   </div>
   <form  id="add_receipt" method="post" onsubmit="receipt_save();return false;">
    <div class="modal-body">
      <div class="row">
        <!-- left column -->
        <div class="col-md-12">
          <!-- general form elements -->

          <div class="row">
		  <div class="col-md-12">
            <div class="form-group">
              <label >Email<sup class="text-danger">*</sup></label>
              <input type="email" class="form-control" name="email"  placeholder="Enter Email">
            </div>
          </div>
           <div class="col-md-6">
            <div class="form-group">
              <label >Name<sup class="text-danger">*</sup></label>
              <input type="text" class="form-control" name="name"  placeholder="Enter Name">
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

		<div class="row">
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

	
  
	function receipt_save()
	{
		$('#add_receipt .st_loader').show();
		$.ajax({  
		  url :BASE_URL+"admin/receipt/receipt_save",  
		  method:"POST",  
		  dataType:"json",  
		  data:$("#add_receipt").serialize(),
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
			  toastr.success('Receipt Info Added Successfully','Success');
			  $('#modal-default').modal('hide');
			  $('#modal-default .modal-content').html('');

			  table.draw();
			}
			$('#add_receipt .st_loader').hide();
		  }  
		}); 
	}

</script>	