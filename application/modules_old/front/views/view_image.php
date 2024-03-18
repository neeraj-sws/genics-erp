    <div class="modal-header">
      <h4 class="modal-title">View Image</h4>
       <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
   <span aria-hidden="true">&times;</span>
     </button>
   </div>
   <form  id="add_user" method="post" onsubmit="user_save();return false;">
    <div class="modal-body">
      <div class="row">
        <!-- left column -->
        <div class="col-md-12" style="text-align: center">
          <!-- general form elements -->
		  <?php if (!empty($images->file)) {
                        $files = explode(',', $images->file); ?>
                          <?php
                           foreach ($files as $file) {
                                 $image_url = base_url('assets/uploads/order/') . $file;
								 $file_extension = pathinfo($file, PATHINFO_EXTENSION);
                              
					if (strtolower($file_extension) === 'pdf') {
						$icon_class = 'fa-solid fa-file-pdf'; 
					}elseif(strtolower($file_extension) === 'doc'|| strtolower($file_extension) === 'docx'){
					   $icon_class ='fa-solid fa-file-doc';
					}elseif(strtolower($file_extension) === 'jpg'||strtolower($file_extension) === 'png'||strtolower($file_extension) === 'jpeg'||strtolower($file_extension) === 'webp'||strtolower($file_extension) === 'svg'){
					   $icon_class = 'far fa-image'; 
					} else {
						$icon_class ='fa-solid fa-file';
					}
                                 ?>
          
			<a href="<?php echo $image_url; ?>" download=""><i class="<?php echo $icon_class; ?>" style="color:black;"></i></a>&nbsp;&nbsp;
                
                           <?php
                           } ?>
                        
                     <?php       
                     } ?>
		
		  <div class="modal-footer justify-content-between">
  <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
  
</div>

<!-- /.card-body -->
</div>
</div>
</div>
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
		  url: '<?php echo base_url() ?>front/distributor_order_history/users_image_data',
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
		  url :BASE_URL+"front/distributor_order_history/user_save",  
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
			  toastr.success('Image Added Successfully','Success');
			  $('#modal-default').modal('hide');
			  $('#modal-default .modal-content').html('');

			  table.draw();
			}
			$('#add_user .st_loader').hide();
		  }  
		}); 
	}
   

</script>	