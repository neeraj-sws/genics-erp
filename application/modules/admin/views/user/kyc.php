<div class="modal-header">
  <h4 class="modal-title"><?php echo $uinfo->full_name;?> - KYC</h4>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
   <span aria-hidden="true">&times;</span>
 </button>
</div>
<form  >
 
  <div class="modal-body">
    
	<div class="row">
		  <div class="col-md-6">
		   <div class="form-group">
			<label >Bank Name<sup class="text-danger">*</sup></label>
			<input type="text" class="form-control"  name="bank_name" placeholder="Bank Name" value="<?php echo $kyc_info->bank_name;?>">
		   </div>
		  </div>
		  <div class="col-md-6">
		   <div class="form-group">
			<label >Account Holder Name<sup class="text-danger">*</sup></label>
			<input type="text" class="form-control"  name="account_holder_name" placeholder="Account Holder Name"  value="<?php echo $kyc_info->account_holder_name;?>">
		   </div>
		  </div>
		</div>
		<div class="row">
		  <div class="col-md-6">
		   <div class="form-group">
			<label >Account Type<sup class="text-danger">*</sup></label>
		<input type="text" class="form-control"  name="account_no" placeholder="Account Number" value="<?php echo $kyc_info->account_type;?>">
		   </div>
		  </div>
		  <div class="col-md-6">
		   <div class="form-group">
			<label >Account Number<sup class="text-danger">*</sup></label> 
			<input type="text" class="form-control"  name="account_no" placeholder="Account Number" value="<?php echo $kyc_info->account_no;?>">
		   </div>
		  </div>
		</div>
		<div class="row">
		  <div class="col-md-6">
		   <div class="form-group">
			<label >IFSC Code<sup class="text-danger">*</sup></label>
			<input type="text" class="form-control"  name="ifsc_code" placeholder="IFSC Code" value="<?php echo $kyc_info->ifsc_code;?>">
		   </div>
		  </div>
		  <div class="col-md-6">
		   <div class="form-group">
			<label >Bank Address<sup class="text-danger">*</sup></label>
			<input type="text" class="form-control"  name="bank_address" placeholder="Bank Address" value="<?php echo $kyc_info->bank_address;?>">
		   </div>
		  </div>
		</div>
		<hr>
		<?php
		$filename = str_replace(" ","_",strtolower($uinfo->full_name)).'_'.$kyc_info->user_id;
		?>
		<div class="row mb-5">
			<div class="col-6 ">
			<div class="form-group">
			<label class="w-100">Adhar Card - <?php echo $kyc_info->adharcard_number;?>  <a href="<?php echo base_url('assets/uploads/adharcard/').$kyc_info->adharcard_file;?>" download="<?php echo $filename;?>_adhar_card"><i class="fa fa-download" ></i></a></label>
			 <img id="simage" src="<?php echo base_url('assets/uploads/adharcard/').$kyc_info->adharcard_file;?>" class="preview_comman img-fluid">
			</div>
			</div>
			<div class="col-6">
			<div class="form-group">
			<label class="w-100" >Pan Card - <?php echo $kyc_info->pan_number;?>  <a href="<?php echo base_url('assets/uploads/pancard/').$kyc_info->pancard_file;?>" download="<?php echo $filename;?>_pan_card"><i class="fa fa-download" ></i></a></label>
			<img id="simage" src="<?php echo base_url('assets/uploads/pancard/').$kyc_info->pancard_file;?>" class="preview_comman img-fluid">
			
			</div>
			</div>
		  </div>

</div>
<!-- /.card-body -->
</div>
</div>
</div>
</div>

</form>
