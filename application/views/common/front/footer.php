</div>
<div class="modal fade" id="modal-default">
	<div class="modal-dialog">
		<div class="modal-content">
			
		</div>
	</div>
</div>
<div class="modal fade" id="modal-default-order">
	<div class="modal-dialog">
		<div class="modal-content">
			
		</div>
	</div>
</div>
    <!--====== Footer Part end ======-->
    <!--====== jquery js ======-->
    <script src="<?php echo base_url();?>front-assets/js/vendor/modernizr-3.6.0.min.js"></script>
    <script src="<?php echo base_url();?>front-assets/js/vendor/jquery-1.12.4.min.js"></script>
    <!--====== Bootstrap js ======-->
    <script src="<?php echo base_url();?>front-assets/js/bootstrap.min.js"></script>
    <script src="<?php echo base_url();?>front-assets/js/popper.min.js"></script>
    <!--====== Slick js ======-->
    <script src="<?php echo base_url();?>front-assets/js/slick.min.js"></script>
    <!--====== Isotope js ======-->
    <script src="<?php echo base_url();?>front-assets/js/isotope.pkgd.min.js"></script>
    <!--====== Magnific Popup js ======-->
    <script src="<?php base_url();?>front-assets/js/jquery.magnific-popup.min.js"></script>
    <!--====== inview js ======-->
    <script src="<?php echo base_url();?>front-assets/js/jquery.inview.min.js"></script>
    <!--====== counterup js ======-->
    <script src="<?php echo base_url();?>front-assets/js/jquery.countTo.js"></script>
    <!--====== easy PieChart js ======-->
    <script src="<?php echo base_url();?>front-assets/js/jquery.easypiechart.min.js"></script>
    <!--====== Jquery Ui ======-->
    <script src="<?php echo base_url();?>front-assets/js/jquery-ui.min.js"></script>
    <!--====== Wow JS ======-->
    <script src="<?php echo base_url();?>front-assets/js/wow.min.js"></script>
    <!--====== Main js ======-->
    <script src="<?php echo base_url(); ?>assets/plugins/toastr/toastr.min.js"></script>
    <script src="<?php echo base_url();?>front-assets/js/main.js"></script>
    <script src="<?php echo base_url();?>front-assets/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo base_url();?>front-assets/js/jquery.min.js"></script>
    <script src="<?php echo base_url();?>front-assets/js/custom.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
      <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
      <!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="<?php echo base_url();?>assets/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="<?php echo base_url();?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- DataTables -->
<script src="<?php echo base_url();?>assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url();?>assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?php echo base_url();?>assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?php echo base_url();?>assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<!-- Datepicker -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>  
<script src="<?php echo base_url();?>assets/plugins/datepicker/bootstrap-datepicker.js"></script>
<script src="<?php echo base_url();?>assets/plugins/datetimepicker/bootstrap-datetimepicker.js"></script>
<!-- overlayScrollbars -->
<script src="<?php echo base_url();?>assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>

<!-- AdminLTE App -->

<script src="<?php echo base_url();?>assets/dist/js/adminlte.js"></script>

<!-- OPTIONAL SCRIPTS -->
<script src="<?php echo base_url();?>assets/dist/js/demo.js"></script>

<!-- PAGE PLUGINS -->
<!-- jQuery Mapael -->
<script src="<?php echo base_url();?>assets/plugins/jquery-mousewheel/jquery.mousewheel.js"></script>
<script src="<?php echo base_url();?>assets/plugins/raphael/raphael.min.js"></script>
<script src="<?php echo base_url();?>assets/plugins/jquery-mapael/jquery.mapael.min.js"></script>
<script src="<?php echo base_url();?>assets/plugins/jquery-mapael/maps/usa_states.min.js"></script>
<!-- ChartJS -->
<script src="<?php echo base_url();?>assets/plugins/chart.js/Chart.min.js"></script>
 <script src="<?php echo base_url();?>assets/plugins/select2/js/select2.full.min.js"></script>
 <script src="<?php echo base_url();?>assets/dist/js/bootstrap-tagsinput.js"></script>
 <script src="<?php echo base_url();?>assets/js/custom.js"></script>
 <script src="<?php echo base_url();?>assets/js/pages/<?php echo $page;?>.js"></script>
 

    <script type="text/javascript">









function logout(type){
   
	if(confirm("Are you sure, You want to Logout ?")){
    $.ajax({  
		url :BASE_URL+"front/login/logout",  
		method:"POST",  
		data:{},
		success:function(res){  
      
      // alert(BASE_URL);
      var surl = BASE_URL;
        window.location = surl;
      }
		});
}
}
  
function form_submit(e)
	{
	  
	   
	   
		$('#Orderform .st_loader').show();
		$.ajax({  
		  url :BASE_URL+"front/order/order_save",  
		  method:"POST",  
          dataType:"json",  
		  data:$("#Orderform").serialize(),
		  success:function(res){
		      $('.submitbtn button').attr("disabled","disabled");
		     	if(res.status == 0){
            $('#Orderform .st_loader').hide();
			  var err = JSON.parse(res.msg);
			  var er = '';
			  $.each(err, function(k, v) { 
				er += v+'<br>'; 
			  }); 
        $('.submitbtn button').removeAttr("disabled");
			  toastr.error(er,'Error');
			}else if(res.status == 2){
			  toastr.error(res.msg,'Error');
			     $('#Orderform .st_loader').hide();
			       $('.submitbtn button').removeAttr("disabled","");
			}else if(res.status == 3){
			  toastr.error(res.msg,'Error');
			     $('#Orderform .st_loader').hide();
			       $('.submitbtn button').removeAttr("disabled","");
			}else{
			     $('#Orderform .st_loader').hide();
			    $("#Orderform")[0].reset();
			     Rowdata('');
			     party_function();
			        	get_order_page();
        
          Swal.fire({
            imageUrl: "<?php echo base_url('front-assets/images/giphy.gif');?>",
            imageHeight: 150,
            allowOutsideClick: false,
            allowEscapeKey: false,
            title: 'Order placed successfully.',
           confirmButtonText: 'Ok',
         
          }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
            	  $("#Orderform")[0].reset();
			     Rowdata('');
			     party_function('');
			        	get_order_page();
             
            } else if (result.isDenied) {
              
            }
          })
       
			  $('#modal-default').modal('hide');
			  $('#modal-default .modal-content').html('');
			  $("#Orderform")[0].reset();
			
			}
        	    
		
			// $('#Orderform .st_loader').hide();
		  }  
		}); 
	}
  function form_login_submit(){
   $('#loginOrderform .st_loader').show();
   var seller= $('#seller_id').val();
   $.ajax({  
    url :BASE_URL+"front/order/otp_verify",  
    method:"POST",  
    dataType:"json",  
    data:$("#loginOrderform").serialize(),'seller':seller,
    success:function(res){  
     if(res.status == 0){
      toastr.error(res.msg,'Error');
      $('#loginOrderform .st_loader').hide();
      }else{
      toastr.success('Login Success','Success');
      var surl = BASE_URL; 
    
      window.setTimeout(function() { window.location = surl; }, 500);
    }
   
  }  
}); 
 }
 function form_otp_submit(){
   $('#loginOrderform .st_loader').show();
   $.ajax({  
    url :BASE_URL+"front/order/get_otp",  
    method:"POST",  
    dataType:"json",  
    data:$("#loginOrderform").serialize(),
    success:function(res){  
      if(res.status == 0){
      toastr.error(res.msg,'Error');
      $('#loginOrderform .st_loader').hide();
      }
      else if(res.status == 1){
      toastr.error(res.msg,'Error');
      $('#loginOrderform .st_loader').hide();
      }
     else{
      toastr.success(res.msg ,'Success');
      $('#loginOrderform .st_loader').hide();
      $('#Firstsection').html(res.view);
      $('#seller_id').val(res.id);

    }
  }  
}); 
 }

  function addRow(e,code){
    $.ajax({  
		url :BASE_URL+"front/order/addrow",  
		method:"POST",  
		data:{code:code},
		success:function(res){  
			$('#modal-default .modal-content').html(res);
			$('#modal-default').modal('show');
		}  
		});
  }

  function Rowdata(code){
    $.ajax({  
		url :BASE_URL+"front/order/Rowdata",  
		method:"POST",  
		data:{code:code},
		success:function(res){  
			$('.Addrowdata').html(res);
		}  
		});
  }

  function Orderdata(code){
    $.ajax({  
		url :BASE_URL+"front/order/Orderdata",  
		method:"POST",  
		data:{code:code},
		success:function(res){  
			$('.Addorderdata').html(res);
   
      
		}  
		});
  }
  
  function resend_otp(){
    // alert("yes");
   var id= $("#seller_id").val();
    $.ajax({  
		url :BASE_URL+"front/order/otp_resend",  
		method:"POST",  
		data:{id:id},
    dataType:"json",  
		success:function(res){  
     
     toastr.success(res.msg ,'Success');
      
		
		}  
		});
  }

  function party_function(e){
toastr.clear();

    var name= $(e).val();
    if(name){
         var count = name.length;
    }else{
         var count = 0;
    }
    $('.phone_no input').val('');
    // alert(count);
    if(count >= 3){
      $.ajax({  
		url :BASE_URL+"front/order/party_function",  
		method:"POST",  
		data:{name:name},
   
		success:function(res){  
      
      $('.importdataparty').html(res);
      $(".allow_numeric").on("input", function(evt) {
            var self = $(this);
            self.val(self.val().replace(/\D/g, ""));
            if ((evt.which < 48 || evt.which > 57)) 
            {
                evt.preventDefault();
            }
            });
    $('.phone_no').show();
  }
});
    }
    else{
           $('.importdataparty').html('');
            $('.phone_no').hide();
             $('.phone_no input').val('');
    //   toastr.error('please enter mimimun 3 charecter');
    }

  }

  function part_click(e)
	{
   var name = $(e).attr('data-name');
   var number = $(e).attr('data-number');

   $('.phone_no').show();

   $('#partyname').val(name);
   $('.phone_no input').val(number);
   $('#dataparty').html('');
	}
        </script>
  </body>
</html>