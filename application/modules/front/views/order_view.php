

 <form action="javascript:void(0);" id="Orderform" method="post" onsubmit="form_submit(this)">
      <input type="hidden" name="code" value="<?php echo $code;?>">
      <input type="hidden" name="distributor_id" value="<?php echo $distributor_id;?>">
      <div class="mainpart">
         <div class="row">
            <div class="col-12">
               <div class="form-group mb-3" style="position:relative;">
                  <label for="partyname">Party name :</label>
                  <input type="text" name="party_name" id="partyname" class="form-control capitalize"  list="browsers" onkeyup="capitalizeInput('capitalize')" oninput="party_function(this)" autocomplete="off">
                  <div class="importdataparty" style="position: absolute;width: 100%;z-index: 999;">

                  </div>
                 
               </div>
            </div>
            <div class="col-12 phone_no" style="display:none">
               <div class="form-group mb-3">
                  <label for="phone">Phone Number :</label>
                  <input type="text" name="phone" id="phone" class="form-control allow_numeric" value="">
               </div>
            </div>
                <div class="col-12 me-auto">
                    <div class="text-end mb-3 pt-2">
                        <a href="javascript:void(0);" class="plusicon" onclick="addRow(this,'<?php echo $code;?>')">
                        <img src="<?php echo base_url();?>front-assets/images/plus.png" alt="plus"> 
                        <span class="itemheading">Item</span> 
                        </a>								
                    </div>
                </div>
        </div>
         <div class="Addrowdata"></div>
         <div class="row">
                <div class="col-12">
                        <div class="form-group mb-3">
                        <label for="city">City :</label>
                        <input type="text" name="city" id="city" class="form-control capitalize" onkeyup="capitalizeInput('capitalize')">
                        </div>
                </div>
                <div class="col-12">
                        <div class="form-group mb-3">
                            <label for="paymentterms">Payment terms :</label>
                            <input type="text" name="payment_term" id="paymentterms" class="form-control capitalize" onkeyup="capitalizeInput('capitalize')">
                        </div>
                </div>
                <div class="col-12">
                        <div class="form-group mb-3">
                                <label for="paymentterms">Dispached details : (optional)</label>
                                <input type="text" name="dispached" id="dispached" class="form-control capitalize" onkeyup="capitalizeInput('capitalize')">
                        </div>
                </div>
         </div>      
      </div>
      <div class="bottompart">
      <div class="row">
      <div class="col-md-5 mx-auto col-sm-6 col-8">
      <div class="submitbtn">
      <button type="submit" class="btn" >submit now <i class="st_loader fa-btn-loader fa fa-refresh fa-spin fa-1x fa-fw" style="display:none;"></i></button>
      </div>
      </div>
      </div>
      </div>
   </form>