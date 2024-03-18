<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper" id="user_view">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Distributor</h1>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-12">
			<div class="card">
            <!-- /.card-header -->
				<div class="card-body pt-3 pb-3 ">
					<div class="row well ">
					    	<div class="col-2 ml-auto">
                    <a class="pull-right btn btn-primary w-100" onclick="export_excel();" href="<?php echo site_url(); ?>admin/distributor/createExcel"><i class="fa fa-download" aria-hidden="true"></i> Export</a>
                  </div>
				  <div class="col-2">
                    <a class="pull-right btn btn-primary w-100" onclick="import_model()"  href="javascript:void(0);"><i class="fa fa-upload" aria-hidden="true"></i>  Import</a>
                  </div>
						<div class="col-md-2"><button type="button" class="btn btn-primary btn-block float-right" onclick="add_user()"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;&nbsp;Add</button>
					</div>
				
					</div>                
				</div>                
            </div>  
			<div class="card">
            
            <!-- /.card-header -->
            <div class="card-body">	
			<form id="user_table">	
			<div class="table-responsive">
              <table id="datatable" class="table table-bordered table-hover">
                <thead>
                <tr>
                 <th>S.No.</th>
				  <th>Full Name</th>
                  <th>Email</th>
                  <th>Mobile Number</th>
                  <th>Category</th>
                  <th>Status</th>
               
                  <th>Action</th>
                </tr>
                </thead>
              </table>
              </div>
			</form>
            </div>
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->
<script>
$(document).ready(function(){
	$('.select2').select2();
    window.table = $('#datatable').DataTable({
		"paging": true,
		"lengthChange": true,
		"searching": true,
		"order": [],
		// "ordering": true,
		"info": true,
		"autoWidth": false,
		"responsive": false,
        "serverSide": true, 
        "lengthMenu": [[10, 25, 50, 100000], [10, 25, 50, "All"]],
		"pageLength": 10,
		"ajax": {
            "url": "<?php echo base_url();?>admin/distributor/user_ajax_list",
            "type": "POST",
            "dataType": "json",
            "data": function(data){
                    data.searchName = $('#searchName').val();
                    data.role = $('#sreachRole').val();
                    data.status = $('#sreachStatus').val();
            },
            "dataSrc": function (jsonData) {
			 return jsonData.data;
			}
        },
        "columnDefs": [
      {  "targets": 0 ,'orderable': false,},
      {  "targets": 1,"orderable": true,},
      {  "targets": 2,'orderable': false,},
      {  "targets": 3 ,'orderable': true,},
      
    ],
		});
	$('#datatable').on( 'page.dt', function () {
		$('#checkAll').prop("checked",false);
		$('.user_check').prop("checked",false);
	});

   });
   
   function getSearchView(){
      table.draw();
   } 

   function resetSearchView(){
   	$('.filter').val('');
     table.draw();
   }
   
   function CheckAll(e){
	   if($('#checkAll').prop("checked") == true){
			$('.user_check').prop("checked",true);
	   }else{
		   $('.user_check').prop("checked",false);
	   }
   }
   
   function add_user(){
	    $.ajax({  
		url :BASE_URL+"admin/distributor/add",  
		method:"POST",  
		data:{},
		success:function(res){  
			$('#modal-default .modal-content').html(res);
			$('#modal-default').modal('show');
		}  
		});
	}
	
	function user_save(){
		$('#add_user .st_loader').show();
		 $.ajax({  
		   url :BASE_URL+"admin/distributor/user_save",  
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
				}else{
					toastr.success('User Info Added Successfully','Success');
					$('#modal-default').modal('hide');
					$('#modal-default .modal-content').html('');
			
					table.draw();
				}
				$('#add_user .st_loader').hide();
			}  
		}); 
	}
	
	
	function user_delete(id){
		if(confirm("Are you sure, You want to delete this User ?")){
		$.ajax({
		   url :"<?php echo base_url();?>admin/distributor/user_delete",
		   method:"POST",
		   dataType:"json",
		   data:{id:id},
		   success:function(res){
			if(res.status == 1){
			  toastr.success('User Deleted Successfully','Success');
			  table.draw( false );
			}
		  }
		});
		}
	}
	
	function user_edit(id){
		$.ajax({
		   url :"<?php echo base_url();?>admin/distributor/user_edit",
		   method:"POST",
		   data:{id:id},
		   success:function(res){
			$('#modal-default .modal-content').html(res);
			$('#modal-default').modal('show');
		  }
		});
		
	}
	
	function user_update(){
		$('#update_user .st_loader').show();
		 $.ajax({  
		   url :BASE_URL+"admin/distributor/user_update",  
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
				}else{
					toastr.success('USer Info Updated Successfully','Success');
					$('#modal-default').modal('hide');
					$('#modal-default .modal-content').html('');
					table.draw( false );
				}
				$('#update_user .st_loader').hide();
			}  
		}); 
	}
	
	
	function user_view(id){
		$.ajax({
		   url :"<?php echo base_url();?>admin/distributor/user_view",
		   method:"POST",
		   data:{id:id},
		   success:function(res){
			$('#user_view').html(res);
		  }
		});
		
	}

	
	
	
	function selected_delete(){
	var count=0;
	$('.user_check').each(function() {
		 if($(this).prop("checked") == true){
			count++;
		 }
	});
	if(count == 0){
		 toastr.error('Please select atleat one User','Error');
	}else{
    if(confirm("Are you sure, You want to delete selected User ?")){
      $.ajax({
         url :"<?php echo base_url();?>admin/distributor/user_multiple_delete",
         method:"POST",
         dataType:"json",
         data:$("#user_table").serialize(),
         success:function(res){
          if(res.status == 1){
            toastr.success('User Deleted Successfully','Success');
            table.draw();
			$('#checkAll').prop("checked",false);
          }else{
            toastr.error('Something went wrong !','Error');
          }
        }
      });
    }
	}
	}

  function user_status(id,type){
	  var status = 'Active';
	  if(type == 0){
		  status = 'Inactive';
	  }
		if(confirm("Are you sure, You want to "+status+" this User ?")){
		$.ajax({
		   url :"<?php echo base_url();?>admin/distributor/user_status",
		   method:"POST",
		   dataType:"json",
		   data:{id:id,type:type},
		   success:function(res){
			if(res.status == 1){
			  toastr.success('User Status Changed Successfully','Success');
			  table.draw( false );
			}
		  }
		});
		}
	}
	
	
	  function user_verify(id,type){
	  var status = 'Verify';
	  if(type == 0){
		  status = 'Decline';
	  }
		if(confirm("Are you sure, You want to "+status+" this User ?")){
		$.ajax({
		   url :"<?php echo base_url();?>admin/distributor/user_verify",
		   method:"POST",
		   dataType:"json",
		   data:{id:id,type:type},
		   success:function(res){
			if(res.status == 1){
			  toastr.success('User Status Changed Successfully','Success');
			  table.draw( false );
			}
		  }
		});
		}
	}


	function kyc_status(id,type){
	  var status = 'Active';
	  if(type == 0){
		  status = 'Inactive';
	  }
		if(confirm("Are you sure, You want to "+status+" this User KYC ?")){
		$.ajax({
		   url :"<?php echo base_url();?>admin/distributor/kyc_status",
		   method:"POST",
		   dataType:"json",
		   data:{id:id,type:type},
		   success:function(res){
			if(res.status == 1){
			  toastr.success('User KYC Status Changed Successfully','Success');
			  table.draw( false );
			}
		  }
		});
		}
	}


	function kyc_view(id){
		$.ajax({
		   url :"<?php echo base_url();?>admin/distributor/kyc_view",
		   method:"POST",
		   data:{id:id},
		   success:function(res){
			$('#modal-default .modal-content').html(res);
			$('#modal-default').modal('show');
		  }
		});
		
	}
	function  export_excel(){
		$.ajax({
		   url :"<?php echo base_url();?>admin/distributor/createExcel",
		   method:"POST",
		   data:{},
		   success:function(res){
        if(res.status == 1){
			  toastr.success('Order Stock Status Changed Successfully','Success');
      }
		  }
		});
		
	}

	function import_model(){
	    $.ajax({  
		url :BASE_URL+"admin/distributor/import_excel",  
		method:"POST",  
		data:{},
		success:function(res){  
			$('#modal-default .modal-content').html(res);
			$('#modal-default').modal('show');
		}   
		});
	}
    </script>