<!doctype html>
<html lang="en-US">

<head>
  <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
  <title>GENICS ORDER</title>
  <meta name="description" content="Appointment Reminder Email Template">
</head>
<style>
  a:hover {
    text-decoration: underline !important;
  }
</style>

<body marginheight="0" topmargin="0" marginwidth="0" style="margin: 0px; background-color: #f2f3f8;" leftmargin="0">
  <table cellspacing="0" border="0" cellpadding="0" width="100%" bgcolor="#f2f3f8" style="@import url(https://fonts.googleapis.com/css?family=Rubik:300,400,500,700|Open+Sans:300,400,600,700); font-family: 'Open Sans', sans-serif;">
    <tr>
      <td>
        <table style="background-color: #f2f3f8; max-width:670px; margin:0 auto;" width="100%" border="0" align="center" cellpadding="0" cellspacing="0">         
          <!-- Email Content -->
          <tr>
            <td>
              <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0" style="max-width:670px; background:#fff; border-radius:3px;-webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);padding:0 18px;text-transform: uppercase;letter-spacing: 1px;">
                <tr>
                  <td style="height:40px;">&nbsp;</td>
                </tr>
                <!-- Details Table -->
                <tr>
                  <td>
                    <table cellpadding="0" cellspacing="0" style="width: 100%;">
                      <tbody>
							<tr>
								<td>
									  <table cellpadding="0" cellspacing="0" style="width: 100%;">
										<tr>
											  <td style="padding: 5px 0;width: 35%; font-weight:500; color:rgb(0 0 0)">
											   Party Name :- <?php echo $order->party_name;?></td>
											 
											</tr>
											<tr>
											  <td style="padding: 5px 0; width: 35%; font-weight:500; color:rgb(0 0 0)">
												Payment Terms :- <?php echo $order->payment_term;?></td>
											 
											</tr>
											<?php if(!empty($order->city)) {?>
    											<tr>
    											  <td style="padding: 5px 0; width: 35%; font-weight:500; color:rgb(0 0 0)">
    												City :- <?php echo $order->city;?></td>
    											 
    											</tr>
											<?php } ?>
											<tr>
											  <td style="padding: 5px 0; width: 35%; font-weight:500; color:rgb(0 0 0)">
											   Sales Agent :- <?php echo @$distributor->full_name;?></td>                         
											</tr>
												<?php if(!empty($order->dispached)) {?>
    												<tr>
    											         <td style="padding: 5px 0; width: 35%; font-weight:500; color:rgb(0 0 0)">
    											    Dispacth Details :- <?php echo $order->dispached;?></td>                         
    										    	</tr>
												<?php } ?>
									  </table>
								</td>
								<td style="vertical-align:top;width:25%;padding-top:15px;">
									<a href="<?php echo base_url();?>" target="_blank" style="color: #c3c3c3; text-decoration: none !important; text-underline: none;">
                                      <img src="<?php echo base_url('front-assets/images/logo.png');?>" class="logo " width="96" border="0" style="width: 96px; height: auto !important; display: block; text-align: center; margin: auto;">
                                     
                                    </a>
								</td>
							</tr>
                        
						
                        <tr>
							<td style="padding-top:35px;" colspan="2">
								<table border="1" style="border-collapse:collapse;width:100%;" cellpadding="12" cellspacing="5">
									<thead>
										<tr>
											<th>S.NO</th>
											<th>Item Name</th>
											<th>Quantity</th>
											<th>Amount</th>
											<th>SubTotal</th>
										</tr>
									</thead>
									<tbody>
									  <?php
											$i=1;
											foreach($items as $item){?>
												<tr>
													<td><?php echo $i;?></td>
													<td><?php echo ucwords($item->item_name);?></td>
													<td><?php echo $item->quantity;?></td>
													<td>  <img src="<?php echo base_url('front-assets/images/rupee-indian.png');?>" class="logo "  border="0" style="width: 12px; height: auto !important;"><?php
                                                              
                                                              $price = $item->price;
                                                              echo number_format($price,2);
                                                              ?></td>
													<td style="text-align:right;"> <img src="<?php echo base_url('front-assets/images/rupee-indian.png');?>" class="logo " border="0" style="width: 12px; height: auto !important;"><?php
                                                              
                                                              $totalprice = $item->price*$item->quantity;
                                                              echo number_format($totalprice,2);
                                                              ?></td>
												</tr>
									<?php $i++;}?>
									</tbody>
									<tfoot>
									    <tr>
									        <th colspan="4" style="text-align:right;">Total</th>
									        <td style="text-align:right;">
									      <img src="<?php echo base_url('front-assets/images/rupee-indian.png');?>" class="logo " border="0" style="width: 12px; height: auto !important;"><?php echo number_format($total,2);?></td>
									    </tr>
									</tfoot>
								</table>
							</td>
						</tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td style="height:40px;">&nbsp;</td>
                </tr>
              </table>
            </td>
          </tr>
          
          <tr>
            <td style="text-align:center;">
              <p style="font-size:14px; color:#455056bd; line-height:18px; margin:15px 0 0;">&copy; <a href="<?php echo base_url();?>"><strong>www.genics.com</strong></a></p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>

</html>