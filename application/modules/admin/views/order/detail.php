<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
   <!-- Content Header (Page header) -->
   <div class="content-header">
       <div class="row">
         <div class="col-8">
      <div class="container-fluid" style="background: ;">
         <div class="card mt-2">
            <div class="card-body">
               <div class="">
                  <?php if($nav == 'order'){
                     $link = 'admin/order';
                     }else{
                     $link = 'admin';
                     } ?>
                  <a href="<?php echo base_url();?><?php echo $link; ?>"> <span  class="close"><img id="icon" src="<?php echo base_url() ?>assets/uploads/logo/back.png" class=""></span> </a>
                  <h4 class="">
                     Order Detail- #<?php echo $order_id; ?>
                     <div class="btnStatus">
                        <?php if($single->status == 0 && $single->is_hold ==0 && $single->is_cancel ==0 && $single->is_dispatch ==0){ ?>
                        <button type="button" class="btn holdOrder mx-2" onclick="order_status(<?php echo $order_id; ?>,2)">Pending</button>
                        <button type="button" class="btn cancelOrder mx-2" onclick="order_status(<?php echo $order_id; ?>,3)">Cancel</button>
                        <?php }elseif($single->status == 0 && $single->is_hold ==1&& $single->is_cancel ==0){ ?>
                        <button type="button" class="btn cancelOrder  mx-2" onclick="order_status(<?php echo $order_id; ?>,3)">Cancel</button>
                        <button type="button" class="btn holdOrder  mx-2" onclick="is_new(<?php echo $order_id;?>)">Set As New</button>
                        <?php }elseif($single->status == 0 && $single->is_dispatch ==1){ ?>
                         <button type="button" class="btn holdOrder mx-2" onclick="order_status(<?php echo $order_id; ?>,2)">Pending</button>
                        <button type="button" class="btn cancelOrder  mx-2" onclick="order_status(<?php echo $order_id; ?>,3)">Cancel</button>
                        <?php }else{
                           } ?>
                     </div>
                     <?php if($single->is_cancel == 1 || $single->is_hold == 1){ ?>
                     <div class="reason">
                        <?php
                           if($orderReason->reason_status == 1){
                             $status = 'Pending';
                           }else{
                             $status = 'Cancel';
                           }  ?>
                        <br>
                        <p><b>Status :</b><?php echo $status;  ?>
                        <p><b>Reason :</b><?php echo $orderReason->reason;  ?><br><br>
                     </div>
                     <?php } ?>
                  </h4>
               </div>
            </div>
         </div>
         <?php
                        if($single->is_cancel != 1 ){  ?>
                        
                          <div class="card mt-2">
            <div class="card-body">
                      <?php  if(empty($single->delivere_id)){ ?>
         
               <form  id="add_user" method="post" onsubmit="user_save();return false;">
                  <div class="">
                     
                     <div class="row">
                        <div class="col-md-6">
                           <div class="form-group SelectFullWidth">
                              <label class='d-block'>Distributor<sup class="text-danger">*</sup></label>
                              <select class="form-control " name="distributor" id="role_id">
                                 <option value="">--Select Distributor--</option>
                                 <?php foreach($distributors as $distributor){ ?>
                                 <option <?php if($single->distributor_id==$distributor->id){ echo 'selected'; }else{ echo ''; }?>  value="<?php echo $distributor->id;?>"><?php echo $distributor->full_name;?></option>
                                 <?php } ?>
                              </select>
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group SelectFullWidth">
                              <label class='d-block'>Delivery Boy<sup class="text-danger">*</sup></label>
                              <select class="form-control " name="delivery_boy" id="role_id">
                                 <option value="">--Select Delivery Boy--</option>
                                 <?php foreach($delivery_boys as $delivery_boy){ ?>
                                 <option value="<?php echo $delivery_boy->id;?>"><?php echo $delivery_boy->full_name;?></option>
                                 <?php } ?>
                              </select>
                           </div>
                        </div>
                     </div>
                     <div class="row">
                     <div class="col-md-6">
                           <div class="form-group SelectFullWidth">
                              <label class='d-block'>Other<sup class="text-danger">*</sup></label>
                              <?php if($single->distributor_other_name){
                                 $distributor = $single->distributor_other_name.' ' .'(In House)';
                              }elseif($single->distributor_third_party_name){
                                 $distributor = $single->distributor_third_party_name.' '.'(Third Party)';
                              }else{
                                 $distributor = '';
                              } ?>
                              <input type="text" class="form-control " value="<?php echo $distributor; ?>" readonly>

                              
                           </div>
                        </div>
                              <div class="col-md-6">
                           <div class="form-group SelectFullWidth">
                              <label class='d-block'>Invoice No.<sup class="text-danger">*</sup></label>
                            
                              <input type="text" class="form-control " name="invoice_number" value="<?php echo $single->invoice_number;?>" >

                              
                           </div>
                        </div>
                    </div>
                     <div class="row">
                        <div class="col-md-12">
                           <div class="form-group SelectFullWidth">
                              <label for="exampleFormControlTextarea1" class="form-label">Remark</label>
                              <textarea class="form-control"  placeholder="Remark" name="remark" rows="2"></textarea>
                              <input type="hidden" id="custId" name="order_id" value="<?php echo $order_id; ?>">
                           </div>
                        </div>
                     </div>
                     <div class="row align-items-center">
                        <div class="col-7">
                           <div class="form-group">
                              <label >Attachment<sup class="text-danger">*</sup></label>
                              <div class="input-group">
                                 <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="files[]" multiple onchange="upload_photo('add_user')">
                                    <input type="hidden" name="file_id" id="file_id" value="">
                                    <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                                 </div>
                              </div>
                           </div>
                        </div>
                       <div class="col-1">
                             <label style="width:100%;">&nbsp;</label>
                             <span class="pull-right load2" style="display:none;"><i class="fa fa-refresh fa-spin fa-1x fa-fw"></i></span>
                             <!-- <img id="simage" src="" class="preview_comman img-fluid"> -->
                             <div id="image_container"></div>
                             <!-- <iframe id="simage" src="" class="preview_comman "></iframe> -->
                      </div>
                      <div class="col-4 attachmentfile ">
                             <button type="button" class="btn btn-sm btn-primary" onclick="changebutton(this)"> Change</button>
                             <button type="button" class="btn btn-sm btn-warning" onclick="removedata(this)">Remove</button>
                      </div>
                     </div>
                     <div class="modal-footer justify-content-end">
                        <button type="submit" class="btn btn-primary">Set Dispatch <i class="st_loader fa-btn-loader fa fa-refresh fa-spin fa-1x fa-fw" style="display:none;"></i></button>
                     </div>
                     <?php if(!empty($thirdPartyDistributors)){
                        
                        } ?> <br>
                     <?php }else{ ?>
                    
                     <div class="row">
                        <div class="col-md-4">
                           <div class="form-group border-bottom">
                              <label class="mb-0">Sales Person Name</label>
                              <p><?php echo $single->distributorName?></p>
                           </div>
                        </div>
                       
                        <div class="col-md-4 ">
                           <div class="form-group border-bottom">
                              <label  class="mb-0">Delivery Boy Name</label>
                              <p><?php echo $single->deliveryBoyName; if($single->status == 0){
                                 echo '<a class="text-muted " onclick="change_delivery_boy('.$single->id.')">' ." <i class='fa fa-edit'></i>".'</a>';
                              } ?></p>
                              
                           </div>
                        </div>
                         <div class="col-md-4 ">
                           <div class="form-group border-bottom">
                              <label  class="mb-0">Invoice Number</label>
                              <p><?php echo $single->invoice_number;?></p>
                              
                           </div>
                        </div>
                        <?php if(!empty($single->remark)){ ?>
                        <div class="col-md-6 ">
                           <div class="form-group border-bottom">
                              <label  class="mb-0">Remark</label>
                              <p><?php echo $single->remark ?></p>
                           </div>
                        </div>
                        <?php } ?>
                        <?php if(!empty($single->file)){ 
                            $files = explode(',', $single->file);?>
                        <div class="col-md-4 ">
                           <div class="form-group border-bottom">
                              <label  class="mb-0">Attachment</label>
                              <p class="m-0"></p>
                           <?php   foreach ($files as $file) {
                                 $image_url = base_url('assets/uploads/order/') . $file;

                                 $file_extension = pathinfo($file, PATHINFO_EXTENSION);
                                 
                                 if (strtolower($file_extension) === 'pdf') {
                                     $icon_class = 'fa-solid fa-file-pdf'; 
                                 }elseif(strtolower($file_extension) === 'doc'){
                                    $icon_class ='fa-solid fa-file-doc';
                                 }elseif(strtolower($file_extension) === 'jpg'||strtolower($file_extension) === 'png'||strtolower($file_extension) === 'jpeg'||strtolower($file_extension) === 'webp'||strtolower($file_extension) === 'svg'){
                                    $icon_class = 'far fa-image'; 
                                 } else {
                                     $icon_class ='fa-solid fa-file';
                                 }
                                 ?>
                          <a id="imageChange" href="<?php echo $image_url; ?>" download=""><i class="<?php echo $icon_class; ?>" style="color:black;"></i></a>
                           <?php
                           } ?>&nbsp;&nbsp;
                           <a  href="jawascript:void0;" style="font-size: 25px;" onclick="whatsapp(<?php echo $single->id; ?>)"><i class="fa-brands fa-square-whatsapp"></i></a>
                           
                            <button type="button" class="btn btn-sm btn-primary float-right" onclick="updatechange(<?php echo $single->id; ?>)"> Change</button>
                           </div>
                        </div>
                        <?php } ?>
                        
                        <div class="col-2">
                                 <!--<button type="button" class="btn btn-sm btn-primary" onclick="updatechange(<?php echo $single->id; ?>)"> Change</button>-->
                              </div>
                              <div class="col-4 updateattach">
                                 <div class="input-group">
                                    <div class="custom-file">
                                       <input type="file" class="custom-file-input" name="files[]" multiple onchange="upload_attachment(<?php echo $order_id; ?>)">
                                       <input type="hidden" name="file_id" id="file_id" value="">
                                       <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                                    </div>
                                 </div>
                              </div>
                              
                    <?php } ?>
               
               </div>
               </form>
            </div>
         </div>
         <?php   }?>
         <?php if($single->is_dispatch==1 && $single->status == 0){ ?>
         <div class="card mt-2 d-none">
            <div class="card-body">
            <form  id="add_user" method="post" onsubmit="deliver_image_save();return false;">
                <div class="row">
                        <div class="col-md-12">
                           <div class="form-group SelectFullWidth">
                              <label for="exampleFormControlTextarea1" class="form-label">Remark</label>
                              <textarea class="form-control"  placeholder="Deliver Remark" name="deliver_remark" rows="2" ></textarea>
                           </div>
                        </div>
                     </div>
            <div class="row">
                        <div class="col-11">
                           <div class="form-group">
                              <label >Attachment<sup class="text-danger">*</sup></label>
                              <div class="input-group">
                                 <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="files[]"multiple onchange="image_save('add_user',this)">
                                    <input type="hidden" id="custId" name="order_id" value="<?php echo $order_id; ?>">
                                    <input type="hidden" name="file_id" id="file_id" value="">
                                    <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="col-1">
                           <label style="width:100%;">&nbsp;</label>
                           <span class="pull-right load2"  style="display:none;"><i class="fa fa-refresh fa-spin fa-1x fa-fw" ></i></span>
                           <!-- <img id="deliver_simage" src="" class="preview_comman img-fluid"> -->
                           <div id="image_container"></div>
                           <!-- <iframe id="simage" src="" class="preview_comman "></iframe> -->
                        </div>
                     </div>
                     <div class="modal-footer justify-content-end">
                        <button type="submit" class="btn btn-primary">Set Deliver <i class="st_loader fa-btn-loader fa fa-refresh fa-spin fa-1x fa-fw" style="display:none;"></i></button>
                     </div>
                        </form>
                        </div>
                        </div>
                        <?php } ?>
                                                 <?php if (!empty($single->delivery_remark) || !empty($single->delivery_file)) { ?>
                        <div class="card mt-2">
            <div class="card-body">
            <h4>Delivered Order Detail </h4>
                        </div>
                        </div>
         <div class="card mt-2">
            <div class="card-body">
               <div class="row">
                  <!-- left column -->
                  <div class="col-md-12">
                     <!-- general form elements -->
                     <div class="row">
                       
                     <?php if(!empty($single->delivery_remark)){ ?>
                        <div class="col-md-6 ">
                           <div class="form-group border-bottom">
                              <label  class="mb-0">Remark</label>
                              <p><?php echo $single->delivery_remark?></p>
                           </div>
                        </div>
                        <?php } ?>
                        
                       
                        <?php if (!empty($single->distributor_attachment_file)) {
                        $files = explode(',', $single->distributor_attachment_file); ?>
                       <div class="col-md-6">
                         <div class="form-group ">
                          <label class="mb-0">Attachment</label>
                          <?php
                           foreach ($files as $file) {
                                 $image_url = base_url('assets/uploads/order/') . $file;

                                 $file_extension = pathinfo($file, PATHINFO_EXTENSION);
                              
                                 if (strtolower($file_extension) === 'pdf') {
                                     $icon_class = 'fa-solid fa-file-pdf'; 
                                 }elseif(strtolower($file_extension) === 'doc'|| strtolower($file_extension) === 'docx'){
                                    $icon_class ='fa-solid fa-file-doc';
                                 }elseif(strtolower($file_extension) === 'jpg'||strtolower($file_extension) === 'png'||strtolower($file_extension) === 'jpeg'||strtolower($file_extension) === 'webp'||strtolower($file_extension) === 'svg'){
                                    $icon_class = 'far fa-image'; 
                                 } else {
                                     $icon_class ='fa-solid fa-file';
                                 }
                                 ?>
            
                          <!-- <p><img id="simage" style="" src="<?php echo $image_url; ?>" class="preview_comman img-fluid"></p> -->
                          <a href="<?php echo $image_url; ?>" download=""><i class="<?php echo $icon_class; ?>" style="color:black;"></i></a>
                
                           <?php
                           } ?>
                        </div>
                       </div>
                     <?php       
                     } ?>
                     </div>
                  </div>
               </div>
            </div>
         </div>

<?php } ?>
                        
         <div class="card mt-2">
            <div class="card-body">
            <h4>Sales Person Order Detail </h4>
                        </div>
                        </div>
         <div class="card mt-2">
            <div class="card-body">
               <div class="row">
                  <!-- left column -->
                  <div class="col-md-12">
                     <!-- general form elements -->
                     <div class="row">
                        <div class="col-md-6">
                           <div class="form-group border-bottom">
                              <label class="mb-0">Party Name</label>
                              <p><?php echo $single->party_name?></p>
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group border-bottom">
                              <label  class="mb-0">Payment Terms</label>
                              <p><?php echo $single->payment_term?></p>
                           </div>
                        </div>
                        <div class="col-md-6 ">
                           <div class="form-group border-bottom">
                              <label  class="mb-0">Dispached Details</label>
                              <p><?php echo $single->dispached?></p>
                           </div>
                        </div>
                        <div class="col-md-6 ">
                           <div class="form-group border-bottom">
                              <label  class="mb-0">Phone Number</label>
                              <p><?php echo $single->number?></p>
                           </div>
                        </div>
                        <div class="col-md-6 ">
                           <div class="form-group border-bottom">
                              <label  class="mb-0">City</label>
                              <p><?php echo $single->city?></p>
                           </div>
                        </div>
                        
                        <!-- <?php if(!empty($distributorAttachment->file)){ 
                         
                       $files= explode(',',$distributorAttachment->file);
                       foreach($files as $file){
                           
                           $images=base_url('assets/uploads/order/').$file;
                           
                       }
                     
                     ?>
                        <div class="col-md-6 ">
                           <div class="form-group ">
                              <label  class="mb-0">Attachment</label>
                              <p><img id="simage" style ="" src="<?php echo $images;?>" class="preview_comman img-fluid"></p>
                           </div>
                        </div>
                        <?php } ?> -->
                        <?php if (!empty($distributorAttachment->file)) {
                        $files = explode(',', $distributorAttachment->file); ?>
                       <div class="col-md-6">
                         <div class="form-group">
                          <label class="mb-0">Attachment</label>
                          <?php
                           foreach ($files as $file) {
                                 $image_url = base_url('assets/uploads/order/') . $file;

                                 $file_extension = pathinfo($file, PATHINFO_EXTENSION);
                              
                                 if (strtolower($file_extension) === 'pdf') {
                                     $icon_class = 'fa-solid fa-file-pdf'; 
                                 }elseif(strtolower($file_extension) === 'doc'|| strtolower($file_extension) === 'docx'){
                                    $icon_class ='fa-solid fa-file-doc';
                                 }elseif(strtolower($file_extension) === 'jpg'||strtolower($file_extension) === 'png'||strtolower($file_extension) === 'jpeg'||strtolower($file_extension) === 'webp'||strtolower($file_extension) === 'svg'){
                                    $icon_class = 'far fa-image'; 
                                 } else {
                                     $icon_class ='fa-solid fa-file';
                                 }
                                 ?>
            
                          <!-- <p><img id="simage" style="" src="<?php echo $image_url; ?>" class="preview_comman img-fluid"></p> -->
                          <a href="<?php echo $image_url; ?>" download=""><i class="<?php echo $icon_class; ?>" style="color:black;"></i></a>
                
                           <?php
                           } ?>
                        </div>
                       </div>
                     <?php       
                     } ?>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="card mt-2">
            <div class="card-body">
               <div class="col-12">
                  <?php if(count($items) > 0){?>
                  <div class="">
                     <div class=" pt-3">
                        <h5 class="text-center pb-2"><b>Order Item</b></h5>
                        <table class="table table-bordered">
                           <thead>
                              <tr>
                                 <th>#</th>
                                 <th>Item Name</th>
                                 <th>Quantity</th>
                                 <th>Price</th>
                                 <th>Sub-Total</th>
                              </tr>
                           </thead>
                           <tbody>
                              <?php  
                                 $i=1;
                                 $qty=0;
                                 foreach($items as $item) {?>
                              <tr>
                                 <td><?php echo $i++;?></td>
                                 <td><?php echo $item->item_name;?></td>
                                 <td><?php echo $item->quantity;?></td>
                                 <td><?php echo $item->price;?></td>
                                 <td><?php echo ($item->price * $item->quantity);?></td>
                                 
                              </tr>
                              <?php 
                                 $total = ($item->price * $item->quantity);
                                 $qty +=$total; } ?>
                           </tbody>
                           <tfoot>
                              <tr>
                              <td></td>
                              <td></td>
                              <td></td>   
                                 
                                 <th colspan="">Total Amount:</th>
                                 <td colspan="" class="text-right">₹<?php echo $qty; ?></td>
                              </tr>
                           </tfoot>
                        </table>
                     </div>
                  </div>
                  <?php } ?>
               </div>
            </div>
         </div>
      </div>
      <!-- /.card-body -->
       </div>
   
   <div class="col-4"> 
        <div class="card mt-2">
            <div class="card-body">
               <div class="col-12">
                
                  <div class="">
                     <div class=" pt-3">
                        <h5 class="text-center pb-2"><b>Order Activity</b></h5>
                        <table class="table table-bordered">
                           <thead>
                              <tr>
                                 <th>#</th>
                                 <th>Massage</th>
                                 <th>Date</th>
                                 
                              </tr>
                           </thead>
                           <tbody>
                              <?php  
                                 $i=1;
                                 
                                 foreach($orderActivitys as $orderActivity) { ?>
                              <tr>
                                 <td><?php echo $i++;?></td>
                                 <td><?php echo $orderActivity->message_type;?>
                              <?php if($orderActivity->message_id == 1){ echo "by distributor <u><b>".$orderActivity->distributor_name."</u></b>" ; }
                                    // elseif($orderActivity->message_id == 2){ echo "<br>and assigned distributor <u><b>".$orderActivity->distributor_name ."</u></b>"."<br> and assigned delivery boy <u><b>".$orderActivity->deliveryboy_name."</u></b>";}
                                    elseif($orderActivity->message_id == 5){ echo "by delivery boy <u><b>".$orderActivity->deliveryboy_name."<u></b>";}
                                    elseif($orderActivity->message_id == 6){ 
                                       if(empty($orderActivity->distributor_id)){

                                          echo "by <u><b>admin</u></b>";
                                       }else{
                                          echo "by <u><b>.$orderActivity->distributor_name.</u></b>";
                                       }
                                    }
                                    elseif($orderActivity->message_id == 7){ 
                                       if(empty($orderActivity->distributor_id)){

                                          echo "by delivery boy <u><b>".$orderActivity->deliveryboy_name."<u></b";
                                       }else{
                                          echo "by distributor <u><b>".$orderActivity->distributor_name."<u></b";
                                       }
                                    }
                                    elseif($orderActivity->message_id == 8){ 
                                       if(empty($orderActivity->distributor_id)){

                                          echo "by delivery boy <u><b>".$orderActivity->deliveryboy_name."<u></b";
                                       }else{
                                          echo "by distributor <u><b>".$orderActivity->distributor_name."<u></b";
                                       }
                                    }
                                    elseif($orderActivity->message_id == 9){ echo "<u><b>".$orderActivity->distributor_name ."</u></b>";}
                                    elseif($orderActivity->message_id == 10){ echo "<u><b>".$orderActivity->deliveryboy_name ."</u></b>";}
                              ?>
                                 </td>
                                 <td><?php echo $orderActivity->created_at;?></td>
                                 
                                 
                                 
                              </tr>
                              <?php 
                                } ?>
                           </tbody>
                           
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

<script type="text/javascript">
   $(document).ready(function() {
    window.uploadcount = 0;
    $('.select2').select2();	
    $(".allowno").keypress(function (e) {
      if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
       event.preventDefault();
     }
   }); 
     $('.attachmentfile').hide();
      $('.updateattach').hide();
   }); 
   
   function upload_photo(id)
   {
   $('.load2').show();
   $.ajax({
     type: "POST",
     url: '<?php echo base_url() ?>admin/Order/users_image_data',
     contentType: false,       
     cache: false,             
     processData:false,
     dataType: "json",
     data: new FormData ($("#"+id)[0]), 
     success: function(res)
     { 
   
   	if(res.status == 0){
   	  var err = JSON.parse(res.msg);
   	  var er = '';
   	  $.each(err, function(k, v) { 
   		er = ' * ' + v; 
   		toastr.error(er,'Error');
   	  });
   	  $(".custom-file-input").val('');
   	}else{
   	//   $('#simage').attr('src',res.image_data);
   	//   $('#simage').show();  
      $('#image_container').empty();
                
      $.each(res.multiple_images, function (index, imageUrl) {
    var file_extension = imageUrl.split('.').pop().toLowerCase(); 
    var icon_class = getIconClass(file_extension); 

    var iconElement = $('<i>', {
        class: icon_class,
    });

    var downloadLink = $('<a>', {
    href: imageUrl,
    download: '',
    style: 'color: black;', 
    });


    var container = $('<div>', {
        class: 'image-container',
    });

    downloadLink.append(iconElement); // Wrap the icon with the <a> tag
    container.append(downloadLink);
     $('.attachmentfile').show();
    $('#image_container').append(container);
});      
   	  $('#file_id').val(res.image_id);  
   	   window.uploadcount++;
       if (window.uploadcount > 1) {
          toastr.success('Image changed Successfully', 'Success');
       } else {
          toastr.success('Image selected Successfully', 'Success');
       }
   	}
   	$('.load2').hide();
     },
      error: function(res) {
            toastr.error('Image is not Selected', 'Error');
            $('.load2').hide();
            $('#file_id').val('');
            // $('#image_container').empty();
         }
   });
   }
   function getIconClass(file_extension) {
    switch (file_extension) {
        case 'pdf':
            return 'fa-solid fa-file-pdf';
        case 'doc':
        case 'docx':
            return 'fa-solid fa-file-doc';
        case 'jpg':
        case 'png':
        case 'jpeg':
        case 'webp':
        case 'svg':
            return 'far fa-image';
        default:
            return 'fa-solid fa-file';
    }
}

   function image_save(id, e = null )
   {
   $('.load2').show();
    var file_id = $(e).siblings("#file_id");
   $.ajax({
     type: "POST",
     url: '<?php echo base_url() ?>admin/order/deliver_image_data',
     contentType: false,       
     cache: false,             
     processData:false,
     dataType: "json",
     data: new FormData ($("#"+id)[0]), 
     success: function(res)
     { 
   
   	if(res.status == 0){
   	  var err = JSON.parse(res.msg);
   	  var er = '';
   	  $.each(err, function(k, v) { 
   		er = ' * ' + v; 
   		toastr.error(er,'Error');
   	  });
   	  $(".custom-file-input").val('');
   	}else{
   	//   $('#deliver_simage').attr('src',res.image_data);
   	//   $('#deliver_simage').show(); 
      $('#image_container').empty();
                
      $.each(res.multiple_images, function (index, imageUrl) {
    var file_extension = imageUrl.split('.').pop().toLowerCase(); 
    var icon_class = getIconClass(file_extension); 

    var iconElement = $('<i>', {
        class: icon_class,
    });

    var downloadLink = $('<a>', {
    href: imageUrl,
    download: '',
    style: 'color: black;', 
    });


    var container = $('<div>', {
        class: 'image-container',
    });

    downloadLink.append(iconElement); // Wrap the icon with the <a> tag
    container.append(downloadLink);
    $('#image_container').append(container);
}); 
       
   	  $('#file_id').val(res.image_id);  
   	   var resfile_id = res.image_id;
        file_id.val(resfile_id);
   	}
   	$('.load2').hide();
     }
   });
   }
  
   function category_update()
   {
   
   $('#update_user .st_loader').show();
   $.ajax({  
     url :BASE_URL+"admin/category/category_update",  
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
   	}else if(res.status == 2){
   	  toastr.error(res.msg,'Error');
   	}else{
   	  toastr.success('User Info Updated Successfully','Success');
   	  $('#modal-default').modal('hide');
   	  $('#modal-default .modal-content').html('');
   	  table.draw( false );
   	}
   	$('#update_user .st_loader').hide();
     }  
   }); 
   }
   
   function user_save()
   {
   $('#add_user .st_loader').show();
   $.ajax({  
    url :BASE_URL+"admin/order/user_save",  
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
   }else if(res.status == 2){
     toastr.error(res.msg,'Error');
   }else{
     toastr.success('Order Info Added Successfully','Success');
           location.reload()
   //   $('#modal-default').modal('hide');
   //   $('#modal-default .modal-content').html('');
   
   //   table.draw();
   }
   $('#add_user .st_loader').hide();
    }  
   }); 
   }
   function deliver_image_save()
   {
   $('#add_user .st_loader').show();
   $.ajax({  
    url :BASE_URL+"admin/order/deliver_image_save",  
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
   }else if(res.status == 2){
     toastr.error(res.msg,'Error');
   }else{
     toastr.success('Order Info Added Successfully','Success');
           location.reload()
   //   $('#modal-default').modal('hide');
   //   $('#modal-default .modal-content').html('');
   
   //   table.draw();
   }
   $('#add_user .st_loader').hide();
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
    if(confirm("Are you sure, You want to "+status+" this Order ?")){
   $.ajax({
     url :"<?php echo base_url();?>admin/order/order_status1",
     method:"POST",
     dataType:"json",
     data:{id:id,type:type},
     success:function(res){
   if(res.status == 1){
     toastr.success('Order Status Changed Successfully','Success');
     table.draw( false );
   }
    }
   });
   }
   
   }
   }

   function  is_new(id){
      var status = 'Unassign';
   
      if(confirm("Are you sure, You want to "+status+" this Order ?")){
   $.ajax({
     url :"<?php echo base_url();?>admin/order/is_new",
     method:"POST",
     dataType:"json",
     data:{id:id},
     success:function(res){
   if(res.status == 1){
     toastr.success('Order Status Changed Successfully','Success');
     location.reload();
   }
    }
   });
   }
   
   }
   
   function whatsapp(id) {
    $.ajax({
        url: BASE_URL + "admin/order/whatsapp",
        method: "POST",
        data: { id: id },
        success: function (res) { 
            var pdfUrls = res.pdf_urls;
            if (pdfUrls.length > 0) {
                var combinedMessage = "Check out Order Documents:\n";
                for (var i = 0; i < pdfUrls.length; i++) {
                    combinedMessage += pdfUrls[i] + "\n";
                }
                var phoneNumber = res.phone_num;
                var encodedMessage = encodeURIComponent(combinedMessage);
                 var whatsappUrl = "https://api.whatsapp.com/send?phone=" + phoneNumber + "&text=" + encodedMessage;
                window.open(whatsappUrl, "_blank");
            } else {
                alert("No PDF URLs found for this order.");
            }
        }
    });
}
    function change_delivery_boy(id){
	    $.ajax({  
		url :BASE_URL+"admin/order/update_delivery_boy",  
		method:"POST",  
		data:{id:id},
		success:function(res){  
			$('#modal-default .modal-content').html(res);
			$('#modal-default').modal('show');
		}  
		});
	}
	
	
   function changebutton(e) {
      var customFileInput = $(".custom-file-input");
      if (customFileInput.length > 0) {
         customFileInput.trigger('click');
      }
   }

   function removedata(e) {
      $('#image_container').html(" ");
      $('#file_id').val('');
      toastr.error('Image removed', 'Error');
      $('.col-4.attachmentfile').hide();
   }

   function updatechange(id) {
      $.ajax({
         url: "<?php echo base_url(); ?>admin/order/attachment",
         method: "POST",
         data: {
            id: id
         },
         success: function(res) {
            $('#modal-default .modal-content').html(res);
            $('#modal-default').modal('show');
         }
      });
   }
   
</script>