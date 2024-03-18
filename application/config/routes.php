<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'front/dashboard';
$route['order'] = 'front/order';
$route['order-login'] = 'front/order/login';
$route['otp_verify'] = 'front/order/otp_verify';
$route['order-otp'] = 'front/order/otp_resend';
$route['order/(:any)'] = 'front/order/order_details/$1';
$route['login'] = 'admin/admin'; 
$route['distributor_history'] = 'front/Distributor_Order_History';
$route['dashboard'] = 'front/dashboard';
$route['order/detail/(:num)'] = 'admin/order/detail/$1';
$route['admin/detail/(:num)'] = 'admin/detail/$1';
$route['terms-and-conditions'] = 'front/home/terms_and_conditions';
$route['privacy-policy'] = 'front/home/privacy_policy';
$route['about'] = 'front/home/about';
# Api routes
$route['api/request-otp'] = 'Api/Authentication/requestOtp';
$route['api/submit-otp'] = 'Api/Authentication/submitOtp';
$route['api/create-order'] = 'Api/Authentication/createOrder';
$route['api/save-order'] = 'Api/Authentication/saveOrder';
$route['api/set-remark'] = 'Api/Authentication/setRemark';
$route['api/set-attachment'] = 'Api/Authentication/setAttachment';
$route['api/save-item'] = 'Api/Authentication/saveItem';
$route['api/update-item'] = 'Api/Authentication/updateItem';
$route['api/delete-item'] = 'Api/Authentication/deleteItem';
$route['api/get-items'] = 'Api/Authentication/getItems';
$route['api/logout'] = 'Api/Authentication/logout';
$route['api/get-suggestion'] = 'Api/Authentication/saveSuggestion';
$route['api/set-new'] = 'Api/Authentication/setNew';
$route['api/set-version']= 'Api/Authentication/setVersion';

$route['api/request-delivery-otp'] = 'Api/Deliver_api/Authentication/requestOtp';
$route['api/submit-delivery-otp'] = 'Api/Deliver_api/Authentication/submitOtp';
$route['api/get-delevery_order'] = 'Api/Deliver_api/Authentication/getOrder';
$route['api/set-deliver-attachment'] = 'Api/Deliver_api/Authentication/satDeliverAttachment';
$route['api/get-distributor-order'] = 'Api/Authentication/getOrder';

$route['api/get-dashboard-data'] = 'Api/Authentication/getDashboardData';
$route['api/get-delivery-dashboard-data'] = 'Api/Deliver_api/Authentication/getDashboardData';

$route['api/order-detail'] = 'Api/Authentication/orderDetail';
$route['api/mark-deliver'] = 'Api/Deliver_api/Authentication/markDeliver';
$route['api/get-pending-orders'] = 'Api/Deliver_api/Authentication/getPendingOrders';
$route['api/delivery-order-detail'] = 'Api/Deliver_api/Authentication/orderDetail';
$route['api/delivery-boy-logout'] = 'Api/Deliver_api/Authentication/logout';

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

