		
		<div class="modal-header">
			<?php
			if(!empty($remark->distributor_remark)){
				$action = 'Edit';
			}else{
				$action = 'Add';
			}
			
			?>
      <h4 class="modal-title"><?php echo $action; ?> Remark</h4>
       <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
   <span aria-hidden="true">&times;</span>
     </button>
   </div>
   <form  id="add_user" method="post" onsubmit="remark_save();return false;">
    <div class="modal-body">
      <div class="row">
        <!-- left column -->
        <div class="col-md-12">
          <!-- general form elements -->

		
		<div class="row">
			<div class="col-11">
			  <div class="form-group">
				
				<div class="input-group">
				  <div class="custom-file">
					
				  <textarea class="form-control w-100"  name="remark" placeholder="Remark"  rows="3" cols="100" ><?php if($action== 'Edit'){ echo $remark->distributor_remark;} ?></textarea>
				  <input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
                    
				  </div>
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
  <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
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

	
  
	function remark_save()
	{
		$('#add_user .st_loader').show();
		$.ajax({  
		  url :BASE_URL+"front/distributor_Order_History/remark_save",  
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
			  toastr.success('Remark Added Successfully','Success');
			  $('#modal-default').modal('hide');
			  $('#modal-default .modal-content').html('');

			  table.draw();
			}
			$('#add_user .st_loader').hide();
		  }  
		}); 
	}
   

</script>	