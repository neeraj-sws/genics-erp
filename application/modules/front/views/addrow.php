 <!-- Modal Header -->
 <div class="modal-header">
          <h4 class="modal-title">Add Item</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
  
        <!-- Modal body -->
        <form  id="add_item" method="post" onsubmit="item_save();return false;">
        <input type="hidden" name="code" value="<?php echo $code;?>">
        <div class="modal-body mainpart">
            <div class="row">
            <div class="col-12">
                <div class="form-group mb-3">
                    <label for="itemname">item name :</label>
                    <input type="text" name="item_name" id="itemname" class="form-control capitalize" onkeyup="capitalizeInput('capitalize')">
                </div>
            </div>
            <div class="col-6">
                <div class="form-group mb-3">
                    <label for="quantity">quantity :</label>
                    <input type="text" name="quantity" id="quantity" class="form-control allow_numeric">
                </div>
            </div>
            <div class="col-6">
                <div class="form-group mb-3">
                    <label for="price">price :</label>
                    <input type="text" name="price" id="price" class="form-control allow_decimal">
                </div>
            </div>
            <div> <button type="submit" class="btn btn-success float-end mb-3 btn-sm">Add<i class="st_loader fa-btn-loader fa fa-refresh fa-spin fa-1x fa-fw" style="display:none;"></i></button></div>
            </div>

            <div class="Addorderdata mb-4"></div>
        </div>
        <!-- Modal footer -->
        <div class="modal-footer">   
        <button type="button" class="btn btn-danger btn-sm"  onclick=submit_order();>Close</button>       
          <button type="button" class="btn btn-primary ok_btn btn-sm" onclick=submit_order(); data-bs-dismiss="modal" style="display: none;">Ok</button>
         
        </div>
        </form>
    <script>
        $(document).ready(function() {
			// Rowdata('<?php echo $code;?>');
			Orderdata('<?php echo $code;?>');

            $(".allow_numeric").on("input", function(evt) {
            var self = $(this);
            self.val(self.val().replace(/\D/g, ""));
            if ((evt.which < 48 || evt.which > 57)) 
            {
                evt.preventDefault();
            }
            });

            $(".allow_decimal").on("input", function(evt) {
                var self = $(this);
                self.val(self.val().replace(/[^0-9\.]/g, ''));
                if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) 
                {
                    evt.preventDefault();
                }
                });
		});
        function item_save(){
		$('#add_item .st_loader').show();
		 $.ajax({  
		   url :BASE_URL+"front/order/item_save",  
		   method:"POST",  
		   dataType:"json",  
		   data:$("#add_item").serialize(),
		   success:function(res){  
				if(res.status == 0){
					 var err = JSON.parse(res.msg);
					 var er = '';
						$.each(err, function(k, v) { 
								er += v+'<br>'; 
						}); 
					 toastr.error(er,'Error');
				}else{
					toastr.success('Item Added Successfully','Success');
                    $("#add_item")[0].reset();
                    Orderdata('<?php echo $code;?>');
                    $('.ok_btn').show();
					// $('#modal-default').modal('hide');
					// $('#modal-default .modal-content').html('');
			
				}
				$('#add_item .st_loader').hide();
			}  
		}); 
	}

    function submit_order(){
        var count = 0;
        $('#add_item input').each(
    function(index){  
        var input = $(this);
       var value = input.val()
        if(value != ''){
            count += 1;
        }else{
            count += 0;
        }
    }
);
if((count == 2) || (count == 3)){
   item_save(); 
}else if(count == 4){
     item_save();
      setTimeout(function() {
                        Rowdata('<?php echo $code;?>');
					$('#modal-default').modal('hide');
					$('#modal-default .modal-content').html('');
                    }, 2000);
}else{
     Rowdata('<?php echo $code;?>');
					$('#modal-default').modal('hide');
					$('#modal-default .modal-content').html('');
}

                  

      
	}
    </script>