
            <form action="javascript:void(0);" id="loginOrderform" method="post" onsubmit="form_login_submit(this)">
				<input type="hidden" name="code" value="<?php echo $code;?>">
				<input type="hidden" id="seller_id" name="seller_id" value="">
            <div class="mainpart">               
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label for="partyname">OTP :</label>
                                <input type="text" name="otp" id="otp" class="form-control allow_numeric">
                            </div>
                        </div>
                </div>
                <div class="bottompart">
                    <div class="row">
                        <div class="col-md-5  mx-auto col-sm-6 col-8">
                            <div class="submitbtn">
                            <button type="submit" class="btn" >Sign In<i class="st_loader fa-btn-loader fa fa-refresh fa-spin fa-1x fa-fw" style="display:none;"></i></button>
                            </div>
                        </div>
                        <div class=" col-8 mx-auto text-center small pt-2 font_otp">
                               <b><span class="smalll text-secondary">Not received your otp?</span> <a class="text-decoration-none  text-black"  onclick="resend_otp()" href="javascript:void(0);"> Resend otp</a></b>
                            </div>
                    </div>
                </div>
        </form>
        
     
  