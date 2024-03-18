	<div class="modal-header">
	  <h4 class="modal-title">Edit Delivery Boy</h4>
	  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	    <span aria-hidden="true">&times;</span>
	  </button>
	</div>
	<form id="add_reason" method="post" onsubmit="delivery_boy_save();return false;" enctype="multipart/form-data">
	  <div class="modal-body">
	    <div class="row">
	      <!-- left column -->
	      <div class="col-md-12">
	        <!-- general form elements -->

	        <div class="row">
	          <div class="col-md-6">
	            <div class="form-group SelectFullWidth">
	              <?php $order_id = $this->input->post('id'); ?>
	              <div class="input-group">
	                <div class="custom-file">
	                  <input type="file" id="attach_input" class="custom-file-input" name="files[]" multiple onchange="upload_attachment('add_reason',<?php echo  $order_id; ?>,this)">
	                  <!-- <input type="hidden" name="" id="" value="<?php //echo  $order_id; 
                                                                    ?>"> -->
	                  <input type="hidden" name="file_id" id="file_id" value="">
	                  <label class="custom-file-label" for="exampleInputFile">Choose file</label>
	                </div>
	              </div>
	              <input type="hidden" id="custId" name="order_id" value="<?php echo  $this->input->post('id'); ?>">

	            </div>
	          </div>
	        </div>
	        <div class="image_container">

	        </div>

	      </div>
	    </div>
	  </div>
	  </div>
	  <div class="modal-footer justify-content-between">
	    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	    <button type="submit" class="btn btn-primary d-none">Update <i class="st_loader fa-btn-loader fa fa-refresh fa-spin fa-1x fa-fw" style="display:none;"></i></button>
	  </div>
	</form>

	<script type="text/javascript">
	  $(document).ready(function() {

	    upload_attachment();

	  });

	  function upload_attachment(id, type,e = null) {
     var loader = $(e).parents("form").find(".st_loader");
     loader.show();
	    $.ajax({
	      url: "<?php echo base_url() ?>admin/order/users_image_attachdata/" + id + '/' + type,
	      method: "POST",
	      processData: false,
	      contentType: false,
	      cache: false,
	      dataType: "json",
	      data: new FormData($("#" + id)[0], $("#" + type)[0]),
	      success: function(res) {
	        if (res.status == 0) {
	          var err = JSON.parse(res.msg);
	          var er = '';
	          $.each(err, function(k, v) {
	            er = ' * ' + v;
	            toastr.error(er, 'Error');
	          });
	          $(".custom-file-input").val('');
	        } else {
	          $('.image_container').empty();
	          var sgtr = res.multiple_images.length;
	          var imageArray = res.multiple_images;

	          for (i = 0; i < sgtr; i++) {
	            var imageElement = $('<img>', {
	              src: imageArray[i],
	              alt: 'Image',
	              width: '50px',
	              height: '50px'
	            });

	            $('.image_container').append(imageElement);
              $('#modal-default').modal('hide');
              $('#imageChange').attr('href',imageArray[i]);
              toastr.success('Image changed Successfully', 'Success');
	          }
	          $.each(res.multiple_images, function(index, imageUrl) {

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

	            downloadLink.append(iconElement); // Wrap the icon with the <a> tag
	            container.append(downloadLink);
	            $('.attachmentfile').show();
	            $('#image_container').append(container);
	          });
	          $('#file_id').val(res.image_id);
	        }
	        loader.hide();
	      }
	    });
	  }
	</script>