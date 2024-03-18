            <div class="col-lg-6">
               <div class="row">
               <div class="col-lg-4 col-6">
                     <!-- small box -->
                   <a href="<?php echo base_url();?>admin/order?type=unassign">
                     <div class="small-box bg-info">
                        <div class="inner newOrder">
                           <h3><?php echo $totalpendingorders->count;?></h3>
                           <p class = "text-white">Unassign Orders</p>
                        </div>
                        <div class="icon">
                           <i class="fa fa-users"></i>
                        </div>
                     </div>
                   </a>
                  </div>
                  <div class="col-lg-4 col-6">
                     <!-- small box -->
                   <a href="<?php echo base_url();?>admin/order?type=dispatch">
                     <div class="small-box bg-info">
                        <div class="inner dispatchOrder">
                           <h3><?php echo $dispatchOrder->count;?></h3>
                           <p class = "text-white">Dispatch Orders</p>
                        </div>
                        <div class="icon">
                           <i class="fa fa-users"></i>
                        </div>
                     </div>
                   </a>
                  </div>
                  <div class="col-lg-4 col-6">
                     <!-- small box -->
                     <a href="<?php echo base_url();?>admin/order?type=pending">
                     <div class="small-box holdOrder">
                        <div class="inner holdOrder">
                           <h3><?php echo $totalholdorders->count;?></h3>
                           <p class = "text-white">Pending Orders</p>
                        </div>
                        <div class="icon">
                           <i class="fa fa-users"></i>
                        </div>
                     </div>
                     </a>
                  </div>
               </div>
            </div>
            <div class="col-lg-6">
               <div class="row">
              
                  <div class="col-lg-4 col-6">
                     <!-- small box -->
                     <a href="<?php echo base_url();?>admin/order?type=deliver">
                     <div class="small-box bg-info">
                        <div class="inner deliverOrder">
                           <h3><?php echo $totalsuccessorders->count;?></h3>
                           <p class = "text-white">Deliver Orders</p>
                        </div>
                        <div class="icon">
                           <i class="fa fa-users"></i>
                        </div>
                      
                     </div>
                     </a>
                  </div>
                  <div class="col-lg-4 col-6">
                     <!-- small box -->
                   <a href="<?php echo base_url();?>admin/order?type=cancel">
                     <div class="small-box bg-info">
                        <div class="inner cancelOrder">
                           <h3><?php echo $totalcanceledorders->count;?></h3>
                           <p class = "text-white">Cancel Orders</p>
                        </div>
                        <div class="icon">
                           <i class="fa fa-users"></i>
                        </div>
                     </div>
                   </a>
                  </div>
                  <div class="col-lg-4 col-6">
                     <!-- small box -->
                     <a href="<?php echo base_url('admin/order');?>" >
                     <div class="small-box bg-info">
                        <div class="inner bg-success">
                           <h3><?php echo $totalorders->count;?></h3>
                           <p> Orders</p>
                        </div>
                        <div class="icon">
                           <i class="fa fa-users"></i>
                        </div>
                     </div>
                     </a>
                  </div>
                  
                  
               </div>
            </div>



            
                  
            
           
