		
		<div class="modal-header">
      <h4 class="modal-title">Add Party Distributor</h4>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
       <span aria-hidden="true">&times;</span>
     </button>
   </div>
   <form  id="add_user" method="post" onsubmit="distributor_save();return false;">
    <div class="modal-body">
      <div class="row">
        <!-- left column -->
        <div class="col-md-12">
          <!-- general form elements -->

          <div class="row">
		  <div class="col-md-6">
				<div class="form-group SelectFullWidth">
					<label class='d-block'>Distributor  Name<sup class="text-danger">*</sup></label>
					<select class="form-control " name="distridutor" id="role_id">
						<option value="">--Select Distributor--</option>
						<?php foreach($distributors as $distributor){?>
							<option value="<?php echo $distributor->id;?>"><?php echo $distributor->full_name;?></option>       
						<?php } ?>
					</select>
					<input type="hidden" class="form-control" name="party"  placeholder="Full Name" value= "<?php echo $party ?>">
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
  <button type="submit" class="btn btn-primary">Add <i class="st_loader fa-btn-loader fa fa-refresh fa-spin fa-1x fa-fw" style="display:none;"></i></button>
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
  
	function user_save()
	{
		$('#add_user .st_loader').show();
		$.ajax({  
		  url :BASE_URL+"admin/party/user_save",  
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
			}else if(res.status == 3){ 
				toastr.error(res.msg,'Error');
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

	function distributor_save()
	{
		$('#add_user .st_loader').show();
		$.ajax({  
		  url :BASE_URL+"admin/party/distributor_save",  
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
			  toastr.success('Distributor Added Successfully','Success');
			  $('#modal-default').modal('hide');
			  $('#modal-default .modal-content').html('');

			  table.draw();
			}
			$('#add_user .st_loader').hide();
		  }  
		}); 
	}

</script>	