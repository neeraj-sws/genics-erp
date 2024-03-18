<section class="w-100 Mainform" id="Firstsection">
   
  
</section>
</div>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<script>
   $(document).ready(function() {
       $( "#partyname" ).keypress(function(e) {
                    var key = e.keyCode;
                    if (key >= 48 && key <= 57) {
                        e.preventDefault();
                    }
                });
   	// Rowdata('<?php echo $code;?>');
   	get_order_page();
   
             $(".allow_numeric").on("input", function(evt) {
             var self = $(this);
             self.val(self.val().replace(/\D/g, ""));
             if ((evt.which < 48 || evt.which > 57)) 
             {
                 evt.preventDefault();
             }
             });
   
             $(".allow_decimal").on("input", function(evt) {
                 var self = $(this);
                 self.val(self.val().replace(/[^0-9\.]/g, ''));
                 if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) 
                 {
                     evt.preventDefault();
                 }
                 });
   });
         function capitalizeInput(className) {
       var inputs = document.getElementsByClassName(className);
       
       for (var i = 0; i < inputs.length; i++) {
         inputs[i].value = inputs[i].value.toUpperCase();
       }
     }
     
      function get_order_page(){
    $.ajax({  
		url :BASE_URL+"front/order/get_order_page",  
		method:"POST",  
		data:{},
		success:function(res){  
			$('.Mainform').html(res);
		 $( "#partyname" ).keypress(function(e) {
                    var key = e.keyCode;
                    if (key >= 48 && key <= 57) {
                        e.preventDefault();
                    }
                });
		}  
		});
      }
   
</script>