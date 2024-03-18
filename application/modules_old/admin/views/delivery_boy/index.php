<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper" id="user_view">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Delivery Boy</h1>
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
          
                    <!-- <a  onclick="export_excel();" href="javascript:void(0);" ><i class="fa fa-download" aria-hidden="true"></i>
                    <input  class="pull-right btn btn-primary w-100" type="submit" class="form-control">
 Export</a> -->
 <!-- <button onclick="export_excel()";  type="submit" class="pull-right btn btn-primary w-100"><i class="fa fa-download" aria-hidden="true"></i>Export</button> -->
                  </div>
				  <div class="col-2">
                    <!-- <a class="pull-right btn btn-primary w-100" onclick="import_model()"  href="javascript:void(0);"><i class="fa fa-upload" aria-hidden="true"></i>  
  Import</a> -->
                  </div>
				
          <div class="col-md-2"><button type="button" class="btn btn-primary btn-block float-right" onclick="add_delivery_boy()"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;&nbsp;Add</button>
					</div> 
          </div>               
				</div>                
            </div> 

			<!-- <div class="card"> -->
            <!-- /.card-header -->
			 <!-- <div class="card-body pt-3 pb-3 ">
					<div class="row well ">
           
            <div class="col-3">
			<label for="">Date</label>
            <input type="hidden" class="form-control" name="datePicker" id="datePicker" placeholder="Date" required value="" autocomplete="off">
                      <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                        <i class="fa fa-calendar"></i>&nbsp;
                        <span>June Thu, 2023</span> <i class="fa fa-caret-down"></i>
                      </div>
            </div>
			<div class="col-7">
			<div class="form-group">
      <input type="hidden" id="toprice" class="form-control" placeholder="To Price" name="toprice" value="">
      <input type="hidden" class="form-control" id="fromprice" name="fromprice"  placeholder="From Price"  value="">
      <input type="hidden" class="form-control" id="date" name="date"  placeholder="From Price"  value="">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">To Price </label>
                                    <input type="text" id="to_price" class="form-control" placeholder="To Price" name="to_price" value="" >
                                </div>
                                <div class="col-md-4">
                                    <label for="">From Price</label>
                                    <input type="text" class="form-control" id="from_price" name="from_price"  placeholder="From Price"  value="" >
                                </div>
                                
                            </div>
</div>
                       
            </div>
</form>
                    
                    <div class="col-2 text-right">
                    <button id ="apply" type="button" id = "apply" onclick="getSearchView();" class="me-2 btn btn-info text-white rounded-0 px-4 py-2 float-end mt-4 w-100">Apply</button>
                    </div>
                   
					     <div class="col-md-2 ml-auto d-none"><br><br></div> 
					</div>                
				</div>               
            </div>   -->
			<div class="card">
            
            <!-- /.card-header -->
            <div class="card-body">	
			<form id="user_table">	
			<div class="table-responsive">
              <table id="datatable" class="table table-bordered table-hover">
                <thead>
                  <tr>
                      <th>S.No.</th>
                      <th>Delivery Boy Name</th>
                      <th>Number</th> 
                      <th>Email</th>
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
            "url": "<?php echo base_url();?>admin/delivery_boy/delivery_boy_ajax_list",
            "type": "POST",
            "dataType": "json",
            "data": function(data){
                    data.searchName = $('#searchName').val();
                    data.role = $('#sreachRole').val();
                    data.status = $('#sreachStatus').val();
                    data.searchtoday = $('#Todaydate').val();
                    data.datePicker = $('#datePicker').val();
                    data.distributor_select = $('#distributor_select').val();
                    data.partyname = $('#partyname').val();
                    data.toprice = $('#to_price').val();
                    data.fromprice = $('#from_price').val();
            },
            "dataSrc": function (jsonData) {
			 return jsonData.data;
			}
        },
        "columnDefs": [
      {  "targets": 0 ,'orderable': false,},
      {  "targets": 1,"orderable": true,},
      {  "targets": 2,'orderable': false,}
      
    ],

// 	"footer": true,
//     "footerCallback": function (tfoot, data, start, end, display) {
//       var api = this.api();

// // Calculate the total price
// var columnIndex = 3; // Update the column index with the correct index of the price column
// var total = api.column(columnIndex, { page: 'current' }).data().reduce(function(acc, val) {

//   // Remove currency symbol and any non-numeric characters
//   var numericValue = parseFloat(val.replace(/[^\d.-]/g, ''));

//   // Parse the numeric value as a float and remove the negative sign if present
//   var parsedValue = Math.abs(parseFloat(numericValue));
 
//   // Check if the parsed value is a valid number
//   if (!isNaN(parsedValue)) {
//     return acc + parsedValue;
//   } else {
//     return acc; // Skip invalid values
//   }
// }, 0);

// $('.total_price').text('₹'+ total);
// }
"footer": true,
"footerCallback": function (tfoot, data, start, end, display) {
  var api = this.api();

  // Calculate the total price
  var columnIndex = 4; // Update the column index with the correct index of the price column
  var total = api.column(columnIndex, { page: 'current' }).data().reduce(function(acc, val) {

    // Extract the numeric value from the span element
    var spanValue = val.match(/₹([\d.]+)/);
    if (spanValue && spanValue[1]) {
      var numericValue = parseFloat(spanValue[1]);
      if (!isNaN(numericValue)) {
        return acc + numericValue;
      }
    }
    return acc; // Skip invalid values or elements without numeric values
  }, 0);

  $('.total_price').text('₹'+ total); // Display the total with two decimal places
}

		});
	$('#datatable').on( 'page.dt', function () {
		$('#checkAll').prop("checked",false);
		$('.user_check').prop("checked",false);
	});

   });
   
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

  function add_delivery_boy(){
	    $.ajax({  
		url :BASE_URL+"admin/delivery_boy/add",  
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

  function delivery_boy_edit(id){
		$.ajax({
		   url :"<?php echo base_url();?>admin/delivery_boy/delivery_boy_edit",
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
	

  function delivery_boy_delete(id){
		if(confirm("Are you sure, You want to delete this Delivery Boy ?")){
		$.ajax({
		   url :"<?php echo base_url();?>admin/delivery_boy/delivery_boy_delete",
		   method:"POST",
		   dataType:"json",
		   data:{id:id},
		   success:function(res){
			if(res.status == 1){
			  toastr.success('Delivery Boy Deleted Successfully','Success');
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