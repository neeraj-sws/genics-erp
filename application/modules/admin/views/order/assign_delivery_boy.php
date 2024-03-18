		
		<div class="modal-header">
      <h4 class="modal-title">Assign Delivery Boy </h4>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
       <span aria-hidden="true">&times;</span>
     </button>
   </div>
   <form  id="add_user" method="post" onsubmit="assign_delivery_boy_save();return false;">
    <div class="modal-body">
      <div class="row">
        <!-- left column -->
        <div class="col-md-12">
          <!-- general form elements -->

         
		<div class="row">
			

			<div class="col-md-6">
				<div class="form-group SelectFullWidth">
				
					<select class="form-control select2" name="delivery_boy" id="role_id">
						<option value="">--Select delivery Boy--</option>
						<?php foreach($delivery_boy as $role){ ?>
							<option value="<?php echo $role->id;?>"><?php echo $role->full_name;?></option>       
						<?php } ?>
					</select>
					<input type="hidden" name="id" value ="<?php echo $id; ?>">
				</div>
			</div>
		</div>
		

</div>
</div>
</div>
</div>
<div class="modal-footer justify-content-between">
  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
  <button type="submit" class="btn btn-primary">Assign <i class="st_loader fa-btn-loader fa fa-refresh fa-spin fa-1x fa-fw" style="display:none;"></i></button>
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

	
  
	function assign_delivery_boy_save()
	{
		$('#add_user .st_loader').show();
		$.ajax({  
		  url :BASE_URL+"admin/order/assign_delivery_boy_save",  
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
			  toastr.success('Delivery Boy Assign  Successfully','Success');
			  $('#modal-default').modal('hide');
			  $('#modal-default .modal-content').html('');

			  table.draw();
			}
			$('#add_user .st_loader').hide();
		  }  
		}); 
	}

</script>	