<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper" id="user_view">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Category</h1>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-12">
         
<form action='<?php echo site_url(); ?>admin/party/createExcel' method="get">
        <div class="card">
            <!-- /.card-header -->
				<div class="card-body pt-3 pb-3 ">
					<div class="row well">
						
					<div class="col-2  ml-auto">
          
 
                  </div>
				  <div class="col-2">
                   
                  </div>
				
          <div class="col-md-2"><button type="button" class="btn btn-primary btn-block float-right" onclick="add_user()"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;&nbsp;Add</button>
					</div> 
          </div>               
				</div>                
            </div> 

			<div class="card">
   
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
                      <th>Title</th>
                      <th>Staus</th> 
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
  <?php if(!empty($_GET['today'])){?>
    <input type="hidden" id="Todaydate" value="<?php echo $_GET['today']?>">
  <?php   }else{?>
    <input type="hidden" id="Todaydate" value="">
    <?php   }?>
  
</div>
<!-- ./wrapper -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
     <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
     <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script>
  $('#apply').click(function(){
    var to_price = $('#to_price').val();
    var from_price = $('#from_price').val();
    var datepicker = $('#datePicker').val();
   $('#toprice').val(to_price);
   $('#fromprice').val(from_price);
   $('#date').val(datepicker);
  });
  


	

   $("#member_filter input").bind("keypress", function(e) {
           if (e.keyCode == 13) {
              return false;
           }
        });
        $(function() {

           var start = moment().subtract(29, 'days');
           var end = moment();

           function cb(start, end) {
              $('#reportrange span').html(start.format('MMMM D, YYYY') + ' / ' + end.format('MMMM D, YYYY'));
              $('#datePicker').val(start.format('YYYY/MM/DD') + ' - ' + end.format('YYYY/MM/DD'));
           }

           $('#reportrange').daterangepicker({
              startDate: start,
              endDate: end,
              ranges: {
                 'Today': [moment(), moment()],
                 'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                 'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                 'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                 'This Month': [moment().startOf('month'), moment().endOf('month')],
                 'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
              }
           }, cb);

           // cb(start, end);

        });
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
               "url": "<?php echo base_url();?>admin/Category/category_ajax_list",
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

      function category_status(id,type){
   	  var status = 'Active';
   	  if(type == 0){
   		  status = 'Inactive';
   	  }
   		if(confirm("Are you sure, You want to "+status+" this Category ?")){
   		$.ajax({
   		   url :"<?php echo base_url();?>admin/category/category_status",
   		   method:"POST",
   		   dataType:"json",
   		   data:{id:id,type:type},
   		   success:function(res){
   			if(res.status == 1){
   			  toastr.success('Category Status Changed Successfully','Success');
   			  table.draw( false );
   			}
   		  }
   		});
   		}
   	}
     function  category_edit(id){
   		$.ajax({
   		   url :"<?php echo base_url();?>admin/category/category_edit",
   		   method:"POST",
   		   data:{id:id},
   		   success:function(res){
   			$('#modal-default .modal-content').html(res);
   			$('#modal-default').modal('show');
   		  }
   		});
   		
   	}
     function category_delete(id){
   		if(confirm("Are you sure, You want to delete this Category ?")){
   		$.ajax({
   		   url :"<?php echo base_url();?>admin/category/category_delete",
   		   method:"POST",
   		   dataType:"json",
   		   data:{id:id},
   		   success:function(res){
   			if(res.status == 1){
   			  toastr.success('Category Deleted Successfully','Success');
   			  table.draw( false );
   			}
   		  }
   		});
   		}
   	}
   	
   
   function getSearchView(){
      table.draw();
   } 
   function today_user(){
    $('#Todaydate').val(<?php echo date('Y/m/d')?>);
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

   
   function  view_order(id){
		$.ajax({
		   url :"<?php echo base_url();?>admin/party/view_order",
		   method:"POST",
		   data:{id:id},
		   success:function(res){
			$('#modal-default .modal-content').html(res);
			$('#modal-default').modal('show');
		  }
		});
		
	}

   function order_instock(id,type){
	  var status = 'Active';
	  if(type == 2){
		  status = 'Inactive';
	  }
		if(confirm("Are you sure, You want to "+status+" this Order ?")){
		$.ajax({
		   url :"<?php echo base_url();?>admin/order/order_instock",
		   method:"POST",
		   dataType:"json",
		   data:{id:id,type:type},
		   success:function(res){
			if(res.status == 1){
			  toastr.success('Order Stock Status Changed Successfully','Success');
			  table.draw( false );
			}
		  }
		});
		}
	}

  function  export_excel(){

		$.ajax({
		   url :"<?php echo base_url();?>admin/party/createExcel",
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
		url :BASE_URL+"admin/party/import_excel",  
		method:"POST",  
		data:{},
		success:function(res){  
			$('#modal-default .modal-content').html(res);
			$('#modal-default').modal('show');
		}   
		});
	}

  function add_user(){
	    $.ajax({  
		url :BASE_URL+"admin/category/add",  
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
		   url :BASE_URL+"admin/party/user_save",  
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

  function user_edit(id){
		$.ajax({
		   url :"<?php echo base_url();?>admin/party/user_edit",
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
		   url :BASE_URL+"admin/party/user_update",  
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
	

  function user_delete(id){
		if(confirm("Are you sure, You want to delete this User ?")){
		$.ajax({
		   url :"<?php echo base_url();?>admin/party/user_delete",
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

  function add_distributor(id){
	    $.ajax({  
		url :BASE_URL+"admin/party/add_distributor",  
		method:"POST",  
		data:{id:id},
		success:function(res){  
			$('#modal-default .modal-content').html(res);
			$('#modal-default').modal('show');
		}  
		});
	}
  function change_distributor(id){
	    $.ajax({  
		url :BASE_URL+"admin/party/change_distributor",  
		method:"POST",  
		data:{id:id},
		success:function(res){  
			$('#modal-default .modal-content').html(res);
			$('#modal-default').modal('show');
		}  
		});
	}
   

    </script>