
<div class="modal-header">
      <h4 class="modal-title"> Order reason</h4>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
       <span aria-hidden="true">&times;</span>
     </button>
</div>
<div class="modal-body">
<table class="table table-bordered">
  <thead>
    <tr>
      <th scope="col">S.No.</th>
      <th scope="col">Reason</th>
      <th scope="col">Status</th>
    </tr>
  </thead>
  <tbody>
   <?php $no=1; foreach($ressons as $resson){ ?>
    <tr>
      <th scope="row"><?php echo $no;?></th>
      <td><?php echo $resson->reason; ?></td>
      <td><?php if($resson->reason_status == 1){ ?>
         <span class="badge badge-warning"> <?php echo "Hold"; ?></span>
    <?php  }else{  ?>

      <span class="badge badge-danger"> <?php echo "Canceled"; ?></span>
   <?php } ?>
   </td>
    </tr>
    <?php $no++; } ?>
  </tbody>
</table>
   <div class="modal-footer justify-content-between">
      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
   </div>
    </div>
   

