<div class="modal-header">
   <h4 class="modal-title text-center">Party Order Item</h4>
   <button type="button" class="close" data-dismiss="modal" aria-label="Close">
   <span aria-hidden="true">&times;</span>
   </button>
</div>
<form  id="update_user" method="post" onsubmit="category_update();return false;">
   <div class="modal-body">
      <div class="row">
         <!-- left column -->
         <div class="col-md-12">
            <!-- general form elements -->
            <div class="row">
              
               <div class="col-12">
                  <?php if(count($items) > 0){?>
                  <div class="">
                     <div class="">
                        <!-- <h5 class="text-center pb-2"><b> Party Order Item</b></h5> -->
                        <table class="table table-bordered">
                           <thead>
                              <tr>
                                 <th>#</th>
                                 <th>Item Name</th>
                                 <th>Quantity</th>
                                 <th>Price</th>
                                 <th>Sub-Total</th>
                                 <th>Created Date</th>
                              </tr>
                           </thead>
                           <tbody>
                              <?php  
                                 $i=1;
                                 $qty=0;
                                 foreach($items as $item) {?>
                              <tr>
                                 <td><?php echo $i++;?></td>
                                 <td><?php echo $item->item_name;?></td>
                                 <td><?php echo $item->quantity;?></td>
                                 <td><?php echo $item->price;?></td>
                                 <td><?php echo ($item->price * $item->quantity);?></td>
                                 <td><?php echo $item->created_at;?></td>
                              </tr>
                              <?php 
                              
                               $total = ($item->price * $item->quantity);
                               $qty +=$total; } ?>
                           </tbody>
                         
                        </table>
						
                     </div>
                  </div>
                  <?php } ?>
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
   	  toastr.success('User Info Updated Successfully','Success');
   	  $('#modal-default').modal('hide');
   	  $('#modal-default .modal-content').html('');
   	  table.draw( false );
   	}
   	$('#update_user .st_loader').hide();
     }  
   }); 
   }
   
</script>