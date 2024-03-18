<style>
  div.dataTables_wrapper div.dataTables_paginate {
    /* width: 539px; */
}
div.dataTables_wrapper div.dataTables_filter {
 
  /* width: 434px; */
}
table th,table td{
  font-size: 11px !important;
}
span.select2-selection.select2-selection--single {
    height: 36px;
    border-radius: unset;
    border: 1px solid #ccc;
}
span.select2.select2-container.select2-container--default {
    width: 100% !important;
}
div#reportrange {
    font-size: 14px;
}

</style>
<!-- Content Wrapper. Contains page content -->
<div class="" id="user_view">
    <!-- Content Header (Page header) -->
    <!-- <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-12">
            <h1>Orders  </h1>
          </div>
        </div>
      </div> /.container-fluid -->
    <!-- </section> --> 
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-12">
        <div class="card">
            <!-- /.card-header -->
			 <div class="card-body pt-3 pb-3 ">
					<div class="row well ">
           
            <div class="col-md-5 mb-md-0 mb-3">
            <input type="hidden" class="form-control" name="datePicker" id="datePicker" placeholder="Date" required value="" autocomplete="off">
                      <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                        <i class="fa fa-calendar"></i>&nbsp;
                        <span>June Thu, 2023</span> <i class="fa fa-caret-down"></i>
                      </div>
            </div>
                    
                    <div class="col-md-4 col-8">
                    <select class="form-select select2 w-100" aria-label="Default select example" id="partyname" list="browsers">
                        <option value="">Party Name</option>
                        <?php foreach($party_name as $parties){ ?>
                        <option value="<?php echo $parties->party_name;?> "><?php echo $parties->party_name;   ?></option>       
                        <?php } ?>
                      </select>
                      <!-- <input type="text" name="party_name" id="" class="form-control" list="browsers" placeholder="Party Name"> -->
                        <!-- <datalist id="browsers">
                            <?php foreach($party_name as $parties){ ?>
                                <option value="<?php echo $parties->party_name;?> - <?php echo $partycount[$parties->id];   ?>">      
                            <?php } ?>
                        </datalist> -->
                    </div>
                    <div class="col-md-3 col-4">
                    <button type="button" onclick="getSearchView();" class="me-2 btn btn-info text-white rounded-0 px-4 py-2 btn-sm float-end">Apply</button>
                    </div>
                    
					     <div class="col-md-2 ml-auto d-none"><br><br></div> 
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
                      <th>Party Name</th>
                      <!--<th>Number</th>-->
                      <th>Payment Terms</th>  
                      <th>Dispached Details</th>
                      <th>Created Date</th>
                      <th>Image</th>
                      <th>New</th>
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

    <?php if(!empty($this->input->get('status'))){ ?>
      <input type="hidden" id="status" value="<?php echo $this->input->get('status')?>">
   <?php }else{ ?>
    <input type="hidden" id="status" value="">
    <?php } ?>
  
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
            "url": "<?php echo base_url();?>front/Distributor_Order_History/order_ajax_list",
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
                    data.statusvalue = $('#status').val();
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
		   url :"<?php echo base_url();?>front/Distributor_Order_History/view_order",
		   method:"POST",
		   data:{id:id},
		   success:function(res){
			$('#modal-default .modal-content').html(res);
			$('#modal-default').modal('show');
		  }
		});
		
	}

  function  add_image(id){
		$.ajax({
		   url :"<?php echo base_url();?>front/distributor_Order_History/add_image",
		   method:"POST",
		   data:{id:id},
		   success:function(res){
			$('#modal-default .modal-content').html(res);
			$('#modal-default').modal('show');
		  }
		});
		
	}
  function  delete_image(id){
	
    if(confirm("Are you sure, You want to Remove this Image ?")){
		$.ajax({
		   url :"<?php echo base_url();?>front/distributor_Order_History/delete_image",
		   method:"POST",
		   dataType:"json",
		   data:{id:id},
		   success:function(res){
			if(res.status == 1){
			  toastr.success(' Image Removed Successfully','Success');
			  table.draw( false );
			}
		  }
		});
		}
		
	}
  function  view_image(id){
		$.ajax({
		   url :"<?php echo base_url();?>front/distributor_Order_History/view_image",
		   method:"POST",
		   data:{id:id},
		   success:function(res){
			$('#modal-default .modal-content').html(res);
			$('#modal-default').modal('show');
		  }
		});
		
	}
  function  add_remark(id){
		$.ajax({
		   url :"<?php echo base_url();?>front/distributor_Order_History/add_remark",
		   method:"POST",
		   data:{id:id},
		   success:function(res){
			$('#modal-default .modal-content').html(res);
			$('#modal-default').modal('show');
		  }
		});
		
	}
	 function  is_new(id){
      var status = 'Unassign';
   
      if(confirm("Are you sure, You want to "+status+" this Order ?")){
   $.ajax({
     url :"<?php echo base_url();?>front/Distributor_Order_History/is_new",
     method:"POST",
     dataType:"json",
     data:{id:id},
     success:function(res){
   if(res.status == 1){
     toastr.success('Order Status Changed Successfully','Success');
     table.draw( false );
   }
    }
   });
   }
   
   }
   </script>