<?php if(count($items) > 0){?>
    <div class="card">
        <div class="card-body">
        <div class="table-responsive">
               
        <table class="table text-nowrap table-bordered">
            <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr> 
            </thead>
                <tbody>
                    <?php foreach($items as $item) {?>
                        <tr>
                            <td><?php echo $item->item_name;?></td>
                            <td><?php echo $item->quantity;?></td>
                            <td><?php echo $item->price;?></td>
                            <td>
                                <button type="button" class="btn btn-primary btn-sm" onclick="editRow(<?php echo $item->id;?>)"><i class="fa fa-pencil" aria-hidden="true"></i></button>
                                <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(<?php echo $item->id;?>)"><i class="fa fa-trash" aria-hidden="true"></i></button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>  
        </table>
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
					Orderdata('<?php echo $code;?>');
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
