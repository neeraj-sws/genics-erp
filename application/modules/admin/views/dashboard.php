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
<div class="content-wrapper">
   <!-- Content Header (Page header) -->
   <div class="content-header">
      <div class="container-fluid">
         <div class="row mb-2">
            <div class="col-sm-6">
               <h1 class="m-0 text-dark">Dashboard</h1>
            </div>
            <!-- /.col -->
         </div>
         <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
   </div>
   <section class="content" id="DashBoardPage">
      <div class="container-fluid">
         <!-- Info boxes -->
         <div class="col-lg-12">
            <div class="row box">
         
            </div>
            
            
            <div class="col-12">
               <div class="card mt-2">
                  <div class="card-body">
                     <h4>Orders</h4>
                     <div class="table-responsive mt-4">
                        <table id="datatable" class="table table-bordered table-hover">
                           <thead>
                              <tr>
                                 <th>S.No.</th>
                                 <th>Party Name</th>
                                 <th>Payment Terms</th>
                                 <th>Dispached Details</th>
                                 <th>Sales Person</th>
                                 <th>Delivery Boy Name</th>
                                 <th class="orstatus">Status</th>
                                 <th>Item Count</th>
                                 <th>Create Date</th>
                                 <!--<th>Details</th>-->
                              </tr>
                           </thead>
                        </table>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
</div>
</div>
</div>
</div>
</section>
</div>
<aside class="control-sidebar control-sidebar-dark"></aside>
</div>
    <script src="https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js"></script>
    <script>
        // Your web app's Firebase configuration
        const firebaseConfig = {
            apiKey: "AIzaSyCh_oL4CCPB_TIpzeCzSvKxZgx2YtjR0-c",
            authDomain: "erp-genics.firebaseapp.com",
            projectId: "erp-genics",
            storageBucket: "erp-genics.appspot.com",
            messagingSenderId: "406824230315",
            appId: "1:406824230315:web:3675291babaf0f158406ee",
            measurementId: "G-2DZ7HV2WFM"
        };


        // Initialize Firebase
        firebase.initializeApp(firebaseConfig);

        const messaging = firebase.messaging();

        function initFirebaseMessagingRegistration() {
            messaging.requestPermission().then(function() {
                return messaging.getToken()
            }).then(function(token) {

            $.ajax({
              url: "<?php echo base_url('admin/updateToken');?>",
              method: "POST",
              data: {token:token},
            });
            
            }).catch(function(err) {
                //console.log(`Token Error :: ${err}`);
            });
        }

        initFirebaseMessagingRegistration();

       // Handle messages when the app is in the foreground or background

        messaging.onMessage(function (payload) {
        if(payload){

           const notificationOptions = {
                body:payload.notification.body,
                click_action:payload.data.click_action,
            };

            const notification = new Notification(payload.notification.title, notificationOptions);

            notification.addEventListener('click', function() {
                // Handle the click action here
                if (notificationOptions.click_action) {
                    window.open(notificationOptions.click_action);
                }
            });
        }

        });
</script>
<script>
     
  $(document).ready(function(){

   function updateBoxContent() {
        $.ajax({
          url: "<?php echo base_url();?>admin/add_box",
          method: "POST",
          data: {},
          success: function(res) {
            $('.box').html(res);
          }
        });
      }

      updateBoxContent();

      setInterval(function() {
        updateBoxContent();
      }, 60000);


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
		"pageLength": 10,
	
		"ajax": {
            "url": "<?php echo base_url();?>admin/order_ajax_list",
            "type": "POST",
            "dataType": "json",
            "data": function(data){
                    data.searchName = $('#searchName').val();
                    data.role = $('#sreachRole').val();
                    data.status = $('#sreachStatus').val();
                    data.searchtoday = $('#Todaydate').val();
                    data.datePicker = $('#datePicker').val();
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

   function reloadDataAndRedraw() {
      table.draw();
      }

      // Initial load
      reloadDataAndRedraw();

     
      setInterval(function() {
        reloadDataAndRedraw();
      }, 60000);



   });

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

   function  order_status(id,type){
      var status = 'Confirm';
	  if((type == 2) || (type == 3)){
      if(type == 3){
		  status = 'Declined';
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


</script>