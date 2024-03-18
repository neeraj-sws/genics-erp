 <!-- Modal Header -->
 <div class="modal-header">
          <h4 class="modal-title">Edit Item</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
  
        <!-- Modal body -->
        <form  id="edit_item" method="post" onsubmit="item_update();return false;">
        <input type="hidden" name="id" value="<?php echo $item->id;?>">
        <div class="modal-body mainpart">
            <div class="row">
            <div class="col-12">
                <div class="form-group mb-3">
                    <label for="itemname">item name :</label>
                    <input type="text" name="item_name" id="itemname" class="form-control capitalize" onkeyup="capitalizeInput('capitalize')" value="<?php echo $item->item_name;?>">
                </div>
            </div>
            <div class="col-12">
                <div class="form-group mb-3">
                    <label for="quantity">quantity :</label>
                    <input type="number" name="quantity" id="quantity" class="form-control" value="<?php echo $item->quantity;?>">
                </div>
            </div>
            <div class="col-12">
                <div class="form-group mb-3">
                    <label for="price">price :</label>
                    <input type="number" name="price" id="price" class="form-control" value="<?php echo $item->price;?>">
                </div>
            </div>
            </div>
        </div>
        <!-- Modal footer -->
        <div class="modal-footer">         
          <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-success btn-sm">Submit <i class="st_loader fa-btn-loader fa fa-refresh fa-spin fa-1x fa-fw" style="display:none;"></i></button>
        </div>
        </form>
    <script>
        function item_update(){
		$('#edit_item .st_loader').show();
		 $.ajax({  
		   url :BASE_URL+"front/order/item_update",  
		   method:"POST",  
		   dataType:"json",  
		   data:$("#edit_item").serialize(),
		   success:function(res){  
				if(res.status == 0){
					 var err = JSON.parse(res.msg);
					 var er = '';
						$.each(err, function(k, v) { 
								er += v+'<br>'; 
						}); 
					 toastr.error(er,'Error');
				}else{
					toastr.success('Item Updated Successfully','Success');
                    Orderdata('<?php echo $item->order_code;?>');
                    Rowdata('<?php echo $item->order_code;?>');
					$('#modal-default-order').modal('hide');
                    $('#modal-default-order .modal-content').html('');
			
				}
				$('#edit_item .st_loader').hide();
			}  
		}); 
	}
    </script>