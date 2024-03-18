<style>
    #datatable td {
      color: white ;
    }
     #datatable tr {
      cursor: pointer;
    }
    #datatable tr:hover {
            opacity: 0.8;
      }
  </style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper" id="user_view">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Orders</h1>
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
           
            <div class="col-3">
            <input type="hidden" class="form-control" name="datePicker" id="datePicker" placeholder="Date" required value="" autocomplete="off">
                      <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                        <i class="fa fa-calendar"></i>&nbsp;
                        <span>June Thu, 2023</span> <i class="fa fa-caret-down"></i>
                      </div>
            </div>
                    <div class="col-3">
                      <select class="form-select select2 w-100" aria-label="Default select example" id="distributor_select">
                        <option value="">Select Distributor</option>
                        <?php foreach($distributors as $distributor){ ?>
                        <option value="<?php echo $distributor->id;?>"><?php echo $distributor->full_name;?></option>       
                        <?php } ?>
                      </select>
                    </div>
                    <div class="col-3">
                    <select class="form-select select2 w-100" aria-label="Default select example" id="partyname" list="browsers">
                        <option value="">Party Name</option>
                        <?php foreach($party_name as $parties){ ?>
                        <option value="<?php echo $parties->party_name;?> "><?php echo $parties->party_name;   ?> - <?php echo $partycount[$parties->id];   ?></option>       
                        <?php } ?>
                      </select>
                      <!-- <input type="text" name="party_name" id="" class="form-control" list="browsers" placeholder="Party Name"> -->
                        <!-- <datalist id="browsers">
                            <?php foreach($party_name as $parties){ ?>
                                <option value="<?php echo $parties->party_name;?> - <?php echo $partycount[$parties->id];   ?>">      
                            <?php } ?>
                        </datalist> -->
                    </div>
                    <div class="col-2">
                    <button type="button" onclick="getSearchView();" class="me-2 btn btn-info text-white rounded-0 px-4 py-2 float-end">Apply</button>
                    </div>
                    <div class="col-1">
                    <a class="pull-right btn btn-info btn-lg rounded-0" onclick="export_excel();"  href="<?php echo site_url(); ?>admin/order/createexcel">Export</a>
                 </div>
					     <div class="col-md-2 ml-auto d-none"><br><br></div> 
					</div>                
				</div>               
            </div> 
            <input type="checkbox" id="new" name="orders" value="new" <?php echo ($this->input->get('type') == 'unassign') ? 'checked' : ''; ?>  onclick="getSearchView();">
            <label for="new">Unassign</label>&nbsp;&nbsp;
            <input type="checkbox" id="dispatch" name="orders" value="dispatch" <?php echo ($this->input->get('type') == 'dispatch') ? 'checked' : ''; ?> onclick="getSearchView();">
            <label for="dispatch">Dispatch</label>&nbsp;&nbsp;
            <input type="checkbox" id="hold" name="orders" value="hold" <?php echo ($this->input->get('type') == 'pending') ? 'checked' : ''; ?> onclick="getSearchView();">
            <label for="hold">Pending</label>&nbsp;&nbsp;
             <input type="checkbox" id="deliver" name="orders" value="deliver" <?php echo ($this->input->get('type') == 'deliver') ? 'checked' : ''; ?> onclick="getSearchView();">
            <label for="deliver">Deliver</label>&nbsp;&nbsp;
            <input type="checkbox" id="cancel" name="orders" value="cancel" <?php echo ($this->input->get('type') == 'cancel') ? 'checked' : ''; ?> onclick="getSearchView();">
            <label for="cancel">Cancel</label><br>
           

			<div class="card">
            
            <!-- /.card-header -->
            <div class="card-body">	
			<form id="user_table">	
			<div class="table-responsive">
              <table id="datatable" class="table table-bordered table-hover">
                <thead>
                  <tr>
                      <th>S.No.</th>
                      <th>Party Name</th>
                      <!--<th>Number</th>-->
                      <th>Payment Terms</th>  
                      <th>Dispached Details</th>
                      <th>Distributor Name</th>
                      <th>Delivery Boy Name</th>
                      <th class="orstatus">Status</th>
                      <th>Item Count</th>
                      <th>Create Date</th>
                      <th>Details</th>
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
   $("#member_filter input").bind("keypress", function(e) {
           if (e.keyCode == 13) {
              return false;
           }
        });
        $(function() {

           var start = moment().subtract(29, 'days');
           var end = moment();

           function cb(start, end) {
              $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
              $('#datePicker').val(start.format('YYYY-MM-DD') + ' / ' + end.format('YYYY-MM-DD'));
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
        "fnDrawCallback": function(oSettings) {
            $('#datatable').on('click', 'tr', function() {
              var id = $(this).find('.filed_check').val();
              window.location.replace("<?php echo base_url();?>admin/detail/"+id);
            });
         },
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
		"pageLength": 25,
		"ajax": {
            "url": "<?php echo base_url();?>admin/order/order_ajax_list",
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
                    data.new = $('#new').prop('checked') ? 'new' : '';
                data.dispatch = $('#dispatch').prop('checked') ? 'dispatch' : '';
                data.hold = $('#hold').prop('checked') ? 'hold' : '';
                data.cancel = $('#cancel').prop('checked') ? 'cancel' : '';
                 data.deliver = $('#deliver').prop('checked') ? 'deliver' : '';
    
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
    "createdRow": function(row, data, dataIndex) {
        var status = data[data.length - 1]; 
        console.log(status);
        var rowClass = ''; 

        if (status =='New') {
            rowClass = 'newOrder';
        } else if (status =='Dispatched') {
            rowClass = 'dispatchOrder';
        } else if (status =='Hold') {
            rowClass = 'holdOrder';
        } else if (status =='Canceled') {
            rowClass = 'cancelOrder';
        } else if (status =='Deliver') {
            rowClass = 'deliverOrder';
        }

        $(row).addClass(rowClass);
    },
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
    $('#Todaydate').val(<?php echo date('Y-m-d')?>);
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
		   url :"<?php echo base_url();?>admin/order/view_order",
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
		   url :"<?php echo base_url();?>admin/order/createExcel",
		   method:"POST",
		   data:{},
		   success:function(res){
        if(res.status == 1){
			  toastr.success('Order Stock Status Changed Successfully','Success');
      }
		  }
		});
		
	}
  
  function  order_status(id,type){
      var status = 'Confirm';
	  if((type == 2) || (type == 3)){
      if(type == 3){
		  status = 'Cancel';
      }else{
        status = 'Hold';
      }
		if(confirm("Are you sure, You want to "+status+" this Order ?")){
		$.ajax({
		   url :"<?php echo base_url();?>admin/order/order_status",
		   method:"POST",
		   data:{id:id,type:type},
		   success:function(res){
			$('#modal-default .modal-content').html(res);
			$('#modal-default').modal('show');
		  }
		});
   }
  }else{
    if(confirm("Are you sure, You want to "+status+" this User ?")){
		$.ajax({
		   url :"<?php echo base_url();?>admin/order/order_status1",
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
	}

  function  show_resion(id){
		$.ajax({
		   url :"<?php echo base_url();?>admin/order/show_resion",
		   method:"POST",
		   data:{id:id},
		   success:function(res){
			$('#modal-default .modal-content').html(res);
			$('#modal-default').modal('show');
		  }
		});
		
	}

  function  assign_delivery_boy(id){
		$.ajax({
		   url :"<?php echo base_url();?>admin/order/assign_delivery_boy",
		   method:"POST",
		   data:{id:id},
		   success:function(res){
			$('#modal-default .modal-content').html(res);
			$('#modal-default').modal('show');
		  }
		});
		
	}

    </script>