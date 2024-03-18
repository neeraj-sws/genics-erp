<?php if(count($items) > 0){?>
    <div class="mb-4">
        <div class="">
        <div class="row align-items-center text-nowrap">

        <?php $i=1;
        foreach($items as $item) {?>
        <div class="col-12 pt-3">
            <div class="form-group mb-3">
                <label for="itemname">item name <?php echo $i ?> :</label>
                <input type="text" name="item_name" class="form-control capitalize" readonly onkeyup="capitalizeInput('capitalize')" value="<?php echo $item->item_name;?>">
            </div>
        </div>
        <div class="col-4">
            <div class="form-group mb-3">
                <label for="quantity">quantity :</label>
                <input type="text" name="" id="" class="form-control allow_numeric" readonly value="<?php echo $item->quantity;?>">
            </div>
        </div>
        <div class="col-4">
            <div class="form-group mb-3">
                <label for="price">price :</label>
                <input type="text" name="" id="" class="form-control allow_decimal" readonly value="<?php echo $item->price;?>">
            </div>
        </div>
        <div class="col-4 pt-3">
            <button type="button" class="btn btn-primary btn-sm float-end ms-2 btn-sm" onclick="editRow(<?php echo $item->id;?>)"><i class="fa fa-pencil" aria-hidden="true"></i></button>
            <button type="button" class="btn btn-danger btn-sm float-end btn-sm" onclick="removeRow(<?php echo $item->id;?>)"><i class="fa fa-trash" aria-hidden="true"></i></button>
        </div>
       
        
        <?php $i++;} ?>
</div>
</div>
    </div>
    <script>
        function removeRow(id){
			 if(confirm('Are you sure you want to delete this?')){
			$.ajax({  
				url :BASE_URL+"front/order/removeRow",  
				method:"POST",  
				data:{id:id},
				success:function(res){  
					toastr.success('Item Remove Successfully','Success');
					Rowdata('<?php echo $code;?>');
				}  
			}); 
			 }
		}
        function editRow(id){
            $.ajax({  
                url :BASE_URL+"front/order/editrow",  
                method:"POST",  
                data:{id:id},
                success:function(res){  
                    $('#modal-default-order .modal-content').html(res);
                    $('#modal-default-order').modal('show');
                }  
                });
        }
    </script>
<?php } ?>
