<section class="w-100 Mainform" id="Firstsection">
            <form action="javascript:void(0);" id="loginOrderform" method="post" onsubmit="form_otp_submit(this)">
				<input type="hidden" name="code" value="<?php echo $code;?>">
                <!-- <input type="hidden" class="seller" name="seller_id" value=""> -->
            <div class="mainpart">               
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label for="partyname">Phone :</label>
                                <input type="text" name="phone" id="phone" class="form-control">
                            </div>
                        </div>
                </div>
                <div class="bottompart">
                    <div class="row">
                        <div class="col-md-5 mx-auto col-sm-6 col-8">
                            <div class="submitbtn">
                                <button type="submit" class="btn" >GET OTP<i class="st_loader fa-btn-loader fa fa-refresh fa-spin fa-1x fa-fw" style="display:none;"></i></button>
                            </div>
                        </div>
                        
                </div>
                   </div>
                   
        </form>
        </section>
        
    </div>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			Rowdata('<?php echo $code;?>');

            $(".allow_numeric").on("input", function(evt) {
            var self = $(this);
            self.val(self.val().replace(/\D/g, ""));
            if ((evt.which < 48 || evt.which > 57)) 
            {
                evt.preventDefault();
            }
            });
		});
		
		
	</script>
    
  