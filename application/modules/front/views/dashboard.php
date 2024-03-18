<form action="javascript:void(0);" id="Orderform" method="post" onsubmit="form_submit(this)">
   <input type="hidden" name="code" value="<?php echo $code;?>">
   <input type="hidden" name="distributor_id" value="<?php echo $distributor_id;?>">
   <div class="">
      <div class="sales">
      <div class="d-flex pb-3 pt-3 orderDetail">
         
         <div class="orderDetailView ">
         <a href="<?php echo base_url()?>distributor_history?status=unassign" >
            <div class="text-center unassignorder">
               <div class="unassign">
                  <img src="<?php echo base_url(); ?>assets/uploads/logo/Asset 6.png" alt="Girl in a jacket" width="50" height="50">
               </div>
               <p class="order mt-2 mb-2">Unassign Order</p>
               <span class="count"><?php echo $unassignOrderCount ?></span> 
            </div>
            </a>
         </div>
         
         <div class="orderDetailView">
         <a href="<?php echo base_url()?>distributor_history?status=dispatched" >
            <div class="text-center text-dark dispatched ">
               <div class="">
                  <img src="<?php echo base_url(); ?>assets/uploads/logo/Asset 5.png" alt="Girl in a jacket" width="75" height="75">
               </div>
               <p  class="order mt-2 mb-2">Dispatched Order</p>
               <span class="count"><?php echo $dispatchedOrderCount ?></span>  
            </div>
</a>
         </div>
         <div class="orderDetailView ">
         <a href="<?php echo base_url()?>distributor_history?status=pending" >
            <div class="text-center pending ">
               <div class="">
                  <img src="<?php echo base_url(); ?>assets/uploads/logo/Asset 4.png" alt="Girl in a jacket" width="75" height="75">
               </div>
               <p  class="order mt-2 mb-2">Pending Order</p>
               <span class="count"><?php echo $pandingOrderCount ?></span> 
            </div>
         </a>
         </div>
         <div class="orderDetailViewarrow">
            <img src="<?php echo base_url(); ?>assets/uploads/logo/arrow.png" alt="Girl in a jacket" class="arrow" width="25" height="25">
         </div>
      <div>
         
</div>


         </div>
         <div class= "d-flex pb-3 pt-3 orderDetail"> 
         <div class="orderDetailView ">
         <a href="<?php echo base_url()?>distributor_history?status=deliver" >
            <div class="text-center delivered ">
               <div class="">
                  <img src="<?php echo base_url(); ?>assets/uploads/logo/Asset 2.png" alt="Girl in a jacket" width="75" height="75">
               </div>
               <p  class="order mt-2 mb-2">Delivered Order</p>
               <span class="count"><?php echo $deliverOrderCount ?></span> 
            </div>
         </a>
         </div>
         <div class="orderDetailView ">
         <a href="<?php echo base_url()?>distributor_history?status=cancel" >
            <div class="text-center cancelled ">
               <div class="">
                  <img src="<?php echo base_url(); ?>assets/uploads/logo/Asset 1.png" alt="Girl in a jacket" width="75" height="75">
               </div>
               <p  class="order mt-2 mb-2">Cancelled Order</p>
               <span class="count"><?php echo $cancelOrderCount ?></span> 
            </div>
         </a>
         </div>
         <div class="orderDetailView ">
         <a href="<?php echo base_url()?>distributor_history" >
            <div class="text-center totalOrder ">
               <div class="">
                  <img src="<?php echo base_url(); ?>assets/uploads/logo/totalorder.webp" alt="Girl in a jacket" width="75" height="75">
               </div>
               <p  class="order mt-2 mb-2">Total Order</p>
               <span class="count"><?php echo $totalOrderCount ?></span> 
            </div>
         </a>
         </div>
      </div>
      </div>
      <div class="sales">
         <div class="d-flex mt-4 salesData">
            <div class=" saling">
               <div class="rading mt-5">
                  <p class="salesPrice mb-4">Today's <br> Sales</p>
                  <span class="price">Rs.<?php echo $todaysSalesCount; ?></span>
               </div>
            </div>
            <div class=" saling">
               <div class="rading mt-5">
                  <p class="salesPrice mb-4">Current <br> Month Sales</p>
                  <span class="price">Rs.<?php echo $currentMonthSalesCount; ?></span>
               </div>
            </div>
            <div class=" saling">
               <div class="rading mt-5">
                  <p class="salesPrice mb-4">Total <br> Sales</p>
                  <span class="price">Rs.<?php echo $totalSalesCount; ?></span>
               </div>
            </div>
         </div>
         <div class="d-flex ">
            <div class=" saling">
               <div class="rading ">
                  <p class="salesPrice mb-4">Current financial Sales</p>
                  <span class="price">Rs.<?php echo $currentfinancialSalesCount; ?></span>
               </div>
            </div>
          
         </div>
         <!-- <div class="d-flex "> -->
         <div class="w-full max-w-screen-md p-6 pb-6 bg-white rounded-lg shadow-xl sm:p-8 mb-5">
        <h2 class="text-xl font-bold text-center"> Last 12-Month Sales</h2>
        <div class="mt-3 mb-3">
        <div id="container"></div>
        </div>
    <!-- </div> -->
</div>
      </div>
      <div class="sales mt-4">
  <div class="newOrder text-center">
   <a href="order">
  <img src="<?php echo base_url(); ?>assets/uploads/logo/plus.png" alt="Home" class="icon" width="60" height="60">
</a>  
  <p class="orderIcon mt-2">New Order</p>
  </div>
  <div class="d-flex space">
    <div class="icon text-center">
    <a href="dashboard" class="orderHistory">
      <img src="<?php echo base_url(); ?>assets/uploads/logo/home.png" alt="Home" class="icon" width="70" height="70">
      <p class="imageIcon">Home</p>
</a>
    </div>
    <div class="icon icon text-center">
      <img src="<?php echo base_url(); ?>assets/uploads/logo/vehicle.png" alt="Vehicle Track"  class="icon" width="80" height="70">
      <p class="imageIcon">Vehicle Track</p>
    </div>
    <div class="icon icon text-center">
      <a href="distributor_history" class="orderHistory">
      <img src="<?php echo base_url(); ?>assets/uploads/logo/order.png" alt="Order History"  class="icon" width="70" height="70">
      <p class="imageIcon">Order History</p>
      </a> 
   </div>
  </div>
</div>
   </div>
</form>


   <script>
      Highcharts.setOptions({
         colors: ['#67BCE6'],
         chart: {
            style: {
               fontFamily: 'sans-serif',
               color: '#fff'
            }
         }
      });

      // Your data (Replace this with your dynamically generated data)
      var last12MonthsSales = {
         <?php
         foreach ($last12MonthsSales as $month => $sales) {
            echo '"' . $month . '": ' . $sales . ',' . PHP_EOL;
         }
         ?>
      };

      var categories = [];
      var data = [];
      for (var month in last12MonthsSales) {
         categories.push(month);
         data.push(last12MonthsSales[month]);
      }

      $('#container').highcharts({
         chart: {
            type: 'column',
            backgroundColor: '#36394B'
         },
         title: {
            text: 'Last 12 Months Sales',
            style: {
               color: '#fff'
            }
         },
         xAxis: {
            tickWidth: 0,
            labels: {
               style: {
                  color: '#fff',
               }
            },
            categories: categories
         },
         yAxis: {
            gridLineWidth: .5,
            gridLineDashStyle: 'dash',
            gridLineColor: 'black',
            title: {
               text: '',
               style: {
                  color: '#fff'
               }
            },
            labels: {
               formatter: function() {
                  return Highcharts.numberFormat(this.value, 0, '', ',');
               },
               style: {
                  color: '#fff',
               }
            }
         },
         legend: {
            enabled: false,
         },
         credits: {
            enabled: false
         },
         tooltip: {
            valuePrefix: ''
         },
         plotOptions: {
            column: {
               borderRadius: 0,
               pointPadding: 0,
               groupPadding: 0.05,
               dataLabels: {
                  enabled: true,
                  color: '#fff',
                  style: {
                     textOutline: 'none' // Remove text outline for better visibility
                  }
               }
            } 
         },
         series: [{
            name: 'Sales',
            data: data
         }]
      });
   </script>


