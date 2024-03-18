	
		<div class="modal-header">
      <h4 class="modal-title"><?php if($type == 2){ echo "Hold";}else{ echo "Declined"; } ?> Order reason</h4>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
       <span aria-hidden="true">&times;</span>
     </button>
   </div>
   <form  id="add_reason" method="post" onsubmit="order_reason_save();return false;">
    <div class="modal-body">
      <div class="row">
        <!-- left column -->
        <div class="col-md-12">
          <!-- general form elements -->

		<div class="row">
		<div class="col-md-12">
		   <div class="form-group">
			
			<textarea class="form-control w-100"  name="order_reason" placeholder="Order reason"  rows="3" cols="100" ></textarea>
			<input type="hidden" id="country_code" name="type" value="<?php echo $type; ?>">
			<input type="hidden" id="country_code" name="id" value="<?php echo $id; ?>">
		  </div>
		</div>
		
		</div>

</div>
</div>
</div>
</div>
<div class="modal-footer justify-content-between">
  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
  <button type="submit" class="btn btn-primary">Save reason <i class="st_loader fa-btn-loader fa fa-refresh fa-spin fa-1x fa-fw" style="display:none;"></i></button>
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
  
	function order_reason_save()
	{
		$('#add_reason .st_loader').show();
		$.ajax({  
		  url :BASE_URL+"admin/order/order_reason_save",  
		  method:"POST",  
		  dataType:"json",  
		  data:$("#add_reason").serialize(),
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
			  toastr.success('Order Cancel  Successfully','Success');
			  $('#modal-default').modal('hide');
			  $('#modal-default .modal-content').html('');
			  location.reload('detail');
			  table.draw();
			  
			}
			$('#add_reason .st_loader').hide();
		  }  
		}); 
	}

</script>	