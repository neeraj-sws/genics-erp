<div class="modal-header">
   <h4 class="modal-title">Import File</h4>
   <button type="button" class="close" data-dismiss="modal" aria-label="Close">
   <span aria-hidden="true">&times;</span>
   </button>
</div>
<form method="post" id="import_form" enctype="multipart/form-data">
   <div class="modal-body">
      <div class="row">
         <!-- left column -->
         <div class="col-md-12">
            <!-- general form elements -->
            <div class="row align-items-center">
               <div class="col-8">
                  <div class="form-group">
                     <label >Import<sup class="text-danger">*</sup></label>
                        <div class="input-group">
                        <div class="custom-file">
                        <input type="file" name="file" id="file" required accept=".xls, .xlsx" /></p>
                           <br />
                          <button type="submit" class="btn btn-primary">Import<i class="st_loader fa-btn-loader fa fa-refresh fa-spin fa-1x fa-fw" style="display:none;"></i></button>
                        </div>
                     </div>
                     
                  </div>
               </div>
               
               <div class="col-4" >
                  <span>Sample File:</span>
                  <a href=<?php echo base_url('assets/uploads/party_import/import.xlsx')?>>Import.xlsx</a>
               </div>
            </div>
            <!-- /.card-body -->
         </div>
      </div>
   </div>
   <div class="modal-footer justify-content-between">
      <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      <button type="submit" class="btn btn-primary">Import<i class="st_loader fa-btn-loader fa fa-refresh fa-spin fa-1x fa-fw" style="display:none;"></i></button> -->
   </div>
</form>
<script type="text/javascript">

$(document).ready(function(){

 load_data();

 function load_data()
 {
  $.ajax({
   url:BASE_URL+"admin/party/fetch",
   method:"POST",
   success:function(data){
    $('#customer_data').html(data);
   }
  })
 }

 $('#import_form').on('submit', function(event){
  event.preventDefault();
    $('#import_form .st_loader').show();

  $.ajax({
   url:BASE_URL+"admin/party/import",
   method:"POST",
   data:new FormData(this),
   contentType:false,
   cache:false,
   processData:false,
   dataType:"json",
   success:function(res){
      // alert(res);
         $('#import_form .st_loader').hide();
      if(res.status == 1){
         toastr.success(res.succ_m,'Data import Successfully','Success');
      }else if(res.status == 0){
         toastr.error(res.err_m,'Data not import','Error');
       }else{
         toastr.success(res.succ_m,'Data import Successfully','Success');
         toastr.error(res.err_m,'Data not import','Error');
       }
     
      $('#modal-default .modal-content').html('');
			$('#modal-default').modal('hide');
         table.draw( false );

    $('#file').val('');
    load_data();
   //  alert(data);
   }
  })
 });

});
</script>
    
   
