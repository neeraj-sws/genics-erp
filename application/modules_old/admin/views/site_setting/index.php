<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1>Site Setting</h1>
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
					<div class="card-body">
						<div class="row well justify-content-sm-end mb-4">
						</div>
						<form id="form-logo" method="POST" onsubmit="update_data();return false;">
							<table class="table table-borderless">
								<tr>
									<td width="500px">
										<div class="form-group">
											<label>Title</label>
											<input type="text" name="title" class="form-control" value="<?php echo $site_setting_data->title; ?>">
										</div>
									</td>
									<td>
									</td>
								</tr>
								<tr>
									<td>
										<label>System Mail</label>
										<input type="email" class="form-control" name="system_email" value="<?php echo $site_setting_data->system_email;  ?>">
									</td>
								</tr>
								<tr>
									<td>
										<label>Sending Mail</label>
										<div class="d-flex align-items-center">
										<span class="pr-2">Yes</span>
										<input type="radio" class="" name="send_email" value="1" <?php if($site_setting_data->send_email == 1){echo 'checked';}?>>
										<span class="px-2">No</span>
										<input type="radio" class="" name="send_email" value="0" <?php if($site_setting_data->send_email == 0){echo 'checked';}?>>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class="form-group">
											<label>Logo (200*150) <b>3MB maximum size</b></label>
											<div class="input-group">
												<div class="custom-file">
													<input type="file" class="custom-file-input" name="logo" onchange="logo_image('form-logo')">
													<input type="hidden" name="logo_id" id="logo_id" value="<?php echo $site_setting_data->logo; ?>">
													<label class="custom-file-label" for="exampleInputFile">Choose file</label>
												</div>
											</div>
										</div>
									</td>
									<td>
										<div class="">
											<i class="load-logo fa-btn-loader fa fa-refresh fa-spin fa-1x fa-fw" style="display:none;"></i>
											<img  id="logo" src="<?php echo base_url('assets/uploads/site_setting/').$site_setting_data->logo_image; ?>" alt="Image not found" class="w-25">
										</div>	
									</td>
								</tr>
								<tr>	
									<td>
										<div class="form-group">
											<label>Favicon (32*32) <b>3MB maximum size</b></label>
											<div class="input-group">
												<div class="custom-file">
													<input type="file" class="custom-file-input" name="favicon" onchange="favicon_image('form-logo')">
													<input type="hidden" name="favicon_id" id="favicon_id" value="<?php echo $site_setting_data->favicon; ?>">
													<label class="custom-file-label" for="exampleInputFile">Choose file</label>
												</div>
											</div>
										</div>
										<td>
											<div class="">
												<i class="load-favicon fa-btn-loader fa fa-refresh fa-spin fa-1x fa-fw" style="display:none;"></i>
												<img  id="favicon" src="<?php echo base_url('assets/uploads/site_setting/').$site_setting_data->fav_image; ?>" alt="Image not found" class="w-25">
											</div>
										</td>
									</tr>
									<tr>
										<td>
											<div class="form-group">
												<label>Preloader Image (300*300)<b>3MB maximum size</b></label>
												<div class="input-group">
													<div class="custom-file">
														<input type="file" class="custom-file-input" name="preloader" onchange="preloader_image('form-logo')">
														<input type="hidden" name="preloader_id" id="preloader_id" value="<?php echo $site_setting_data->preloader; ?>">
														<label class="custom-file-label" for="exampleInputFile">Choose file</label>
													</div>
												</div>
											</div>
										</td>

										<td>
											<div class="">
												<i class="load-preloader fa-btn-loader fa fa-refresh fa-spin fa-1x fa-fw" style="display:none;"></i>
												<img  id="preloader" src="<?php echo base_url('assets/uploads/site_setting/').$site_setting_data->preloader_image; ?>" alt="Image not found" class="w-25">
											</div>
										</td>
									</tr>
									<tr>
										<td colspan="2">
											<label>About Us footer</label>
											<textarea class="form-control" name="aboutus_footer"><?php echo $site_setting_data->aboutus_footer; ?></textarea></td>
										</tr>
										<tr>
											<td>
												<label>Meta Authore</label>
												<input type="text" name="meta_authore" class="form-control" value="<?php echo $site_setting_data->meta_authore; ?>">
												<td>
												</td>
											</tr>
											<tr>
												<td>
													<label>Meta Keyword</label>
													<input type="text" name="meta_keyword" class="form-control" value="<?php echo $site_setting_data->meta_keyword; ?>">
													<td>

													</td>
												</tr>
												<tr>
													<td colspan="2">
														<label>Meta Discription</label>
														<textarea class="form-control" name="meta_description"><?php echo $site_setting_data->meta_description; ?></textarea></td>
													</tr>
													<tr>
														<td colspan="2">
															<div>
																<button type="submit" class="btn btn-primary">Update<i class="st_loader fa-btn-loader fa fa-refresh fa-spin fa-1x fa-fw" style="display:none;"></i></button>
															</div>
														</td>
														
													</tr>
													
												</table>
											</form>
										</div>
									</div>
								</div>
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
</div>
<!-- ./wrapper -->


<script>

	function update_data(){
		$('#form-logo').find('.st_loader').show();
		$.ajax({  
			url :BASE_URL+"admin/site_setting/update_data",  
			method:"POST",  
			dataType:"json",  
			data:$("#form-logo").serialize(),
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
					toastr.success('Site Setting updated Successfully','Success');
				}
				$('#form-logo').find('.st_loader').hide();
			}  
		}); 
	}

	function logo_image(id){
		$('.load-logo').show();
		$.ajax({
			type: "POST",
			url: '<?php echo base_url() ?>admin/site_setting/logo_image',
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
					$('#logo').attr('src',res.image_data);
					$('#logo').show();        
					$('#logo_id').val(res.image_id);  
				}
				$('.load-logo').hide();
			}
		});
	}

	function favicon_image(id){
		$('.load-favicon').show();
		$.ajax({
			type: "POST",
			url: '<?php echo base_url() ?>admin/site_setting/favicon_image',
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
					$('#favicon').attr('src',res.image_data);
					$('#favicon').show();        
					$('#favicon_id').val(res.image_id);  
				}
				$('.load-favicon').hide();
			}
		});
	}

	function preloader_image(id){
		$('.load-preloader').show();
		$.ajax({
			type: "POST",
			url: '<?php echo base_url() ?>admin/site_setting/preloader_image',
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
					$('#preloader').attr('src',res.image_data);
					$('#preloader').show();        
					$('#preloader_id').val(res.image_id);  
				}
				$('.load-preloader').hide();
			}
		});
	}

</script>