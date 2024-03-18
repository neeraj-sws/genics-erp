		
		<div class="modal-header">
      <h4 class="modal-title">Add Image</h4>
       <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
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
			<div class="col-10">
			  <div class="form-group">
				
				<div class="input-group">
				  <div class="custom-file">
					<input type="file" class="custom-file-input" name="files[]" multiple onchange="upload_photo('add_user')">
					<input type="hidden" name="file_id" id="file_id" value="">
					<input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
					<label class="custom-file-label" for="exampleInputFile">Choose file</label>
				  </div>
				</div>
			  </div>
			</div>
			<div class="col-2">
			 
			 <span class="pull-right load2"  style="display:none;"><i class="fa fa-refresh fa-spin fa-1x fa-fw" ></i></span>
			 <!-- <img id="simage" src="" class="preview_comman img-fluid"> -->
			 <div id="image_container"></div>
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

	function upload_photo(id)
	{
		$('.load2').show();
		$.ajax({
		  type: "POST",
		  url: '<?php echo base_url() ?>front/Distributor_Order_History/users_image_data',
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
			//   $('#simage').attr('src',res.image_data);
			//   $('#simage').show(); 
			$('#image_container').empty();
                
               
				$.each(res.multiple_images, function (index, imageUrl) {
    var file_extension = imageUrl.split('.').pop().toLowerCase(); 
    var icon_class = getIconClass(file_extension); 

    var iconElement = $('<i>', {
        class: icon_class,
    });

    var downloadLink = $('<a>', {
    href: imageUrl,
    download: '',
    style: 'color: black;', 
    });


    var container = $('<div>', {
        class: 'image-container',
    });

    downloadLink.append(iconElement); // Wrap the icon with the <a> tag
    container.append(downloadLink);
    $('#image_container').append(container);
});


			  
			  $('#file_id').val(res.image_id);  
			}
			$('.load2').hide();
		  }
		});
	}
	function getIconClass(file_extension) {
    switch (file_extension) {
        case 'pdf':
            return 'fa-solid fa-file-pdf';
        case 'doc':
        case 'docx':
            return 'fa-solid fa-file-doc';
        case 'jpg':
        case 'png':
        case 'jpeg':
        case 'webp':
        case 'svg':
            return 'far fa-image';
        default:
            return 'fa-solid fa-file';
    }
}
  
	function user_save()
	{
		$('#add_user .st_loader').show();
		$.ajax({  
		  url :BASE_URL+"front/Distributor_Order_History/user_save",  
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