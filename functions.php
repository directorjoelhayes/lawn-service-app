<?php

function test_auth(){
  $user = get_current_user_id();
  if($user>0){
    return true;
  }
}
//routes--------------------------------------------------------
if(test_auth()){
  register_rest_route( 'myplugin/v1', '/create-customer', array(
        'methods' => 'POST',
        'callback' => 'create_customer'
  ) );

  register_rest_route( 'myplugin/v1', '/update-customer', array(
        'methods' => 'POST',
        'callback' => 'update_customer'
  ) );

  register_rest_route( 'myplugin/v1', '/get-customer', array(
        'methods' => 'POST',
        'callback' => 'get_customer'
  ) );

  register_rest_route( 'myplugin/v1', '/search-customers', array(
        'methods' => 'POST',
        'callback' => 'search_customers'
  ) );

  register_rest_route( 'myplugin/v1', '/get-orders', array(
        'methods' => 'POST',
        'callback' => 'search_orders'
  ) );

  register_rest_route( 'myplugin/v1', '/get-order', array(
        'methods' => 'POST',
        'callback' => 'get_order'
  ) );

  register_rest_route( 'myplugin/v1', '/update-order', array(
        'methods' => 'POST',
        'callback' => 'update_order'
  ) );

  register_rest_route( 'myplugin/v1', '/get-settings', array(
        'methods' => 'POST',
        'callback' => 'get_default_settings'
  ) );

  register_rest_route( 'myplugin/v1', '/update-settings', array(
        'methods' => 'POST',
        'callback' => 'update_settings'
  ) );
}

require ( dirname( __FILE__ ) . '/core/classes/settings/GetSettings.php');
require ( dirname( __FILE__ ) . '/core/classes/settings/Settings.php');
require ( dirname( __FILE__ ) . '/core/classes/Customers/Customer.php');
require ( dirname( __FILE__ ) . '/core/classes/Customers/GetCustomer.php');
require ( dirname( __FILE__ ) . '/core/classes/orders/Order.php');
require ( dirname( __FILE__ ) . '/core/classes/orders/GetOrder.php');
require ( dirname( __FILE__ ) . '/core/classes/time/TimeMethod.php');

function create_customer($data){
    $customer = new Customer($data);
    $response = $customer->createCustomer();
    return wp_send_json($response, 200);
}

function update_customer($data){
  $customer = new Customer($data);
  $response = $customer->updateCustomer();
  return wp_send_json($response, 200);
}

function search_customers($data){
  $args = array(
    'post_type' => 'customer',
    'posts_per_page' => -1
  );

  $customers = [];
  $posts = get_posts( $args );

  foreach($posts as $customer){
    $customer = new GetCustomer($customer->ID);
    $customers[] = $customer->getFull();
  }

  //sort by last name
  usort($customers, function($a, $b) {
    return $a['last_name'] > $b['last_name'];
  });

  return wp_send_json($customers, 200);
}

function get_customer($data){
  $customer = new GetCustomer($data['customer_id']);
  $response = $customer->getFull();
  return wp_send_json($response, 200);
}

function get_order($data){
  $order = new GetOrder($data['order_id']);
  $response = $order->getFull();
  return wp_send_json($response, 200);
}

function search_orders($data){
  $args = array(
    'post_type' => 'orders',
    'posts_per_page' => -1
  );

  //date query

  if($data['date']==null){
    $date_string = get_field("current_month","option");
    $date = explode(":",$date_string);
    $start_date = $date[0];
  }else{
    $start_date = $data['date'];
  }

  $date = getdate($start_date);
  $time = new TimeMethod($start_date);
  $nextMonth = getdate($time->navigateMonths("next"));

  $args['date_query'] = array(
     "after" => array(
       'year'  => $date['year'],
  		 'month' => $date['mon'],
  		 'day'   => 0,
     ),
     //before the next month
     "before" => array(
       'year'  => $nextMonth['year'],
  		 'month' => $nextMonth['mon'],
  		 'day'   => 0,
     )
   );

  $orders = [];
  $posts = get_posts( $args );

  foreach($posts as $order){
    //date check
    $order = new GetOrder($order->ID);
    $orders[] = $order->getFull();
  }

  //sort by last name
  usort($orders, function($a, $b) {
    return $a['last_name'] > $b['last_name'];
  });

  return wp_send_json($orders, 200);
}

function update_order($data){
  $order = new Order($data);
  $response = $order->updateOrder();
  return wp_send_json($response, 200);
}

function get_default_settings(){
  $settings = new GetSettings($data);
  $response = $settings->getFull();
  return wp_send_json($response, 200);
}

function update_settings($data){
  $settings = new Settings($data);
  $response = $settings->updateSettings();
  return wp_send_json($response, 200);
}

//generate orders for month

function generate_orders(){

  $args = array(
    'post_type' => 'customer',
    'posts_per_page' => -1
  );

  $customers = [];
  $posts = get_posts( $args );

  foreach($posts as $customer){
    $customer = new GetCustomer($customer->ID);
    if(get_field('status',$customer->ID)=='active'){
      $order = new Order($customer->getFull());
      $order->createOrder();
    }
  }

}

//Create orders for month

function new_month(){

  $current = get_field('current_month','option');
  $current_month = explode(':',$current);

  $now = date("Y-m-d H:i:s");
  $datetime = new DateTime($now, new DateTimeZone('America/New_York'));
  $month = date("m");

  if(
    //time is greater than current month timestamp
    $datetime->getTimestamp() > $current_month[0] &&
    //month does not equal current month
    $month != $current_month[1]
  ){
    //update current month
    update_field('current_month',$datetime->getTimestamp() . ':' . $month,'option');
    //generate orders for month
    generate_orders();
  }
}

//run on wordpress loop :

new_month();

//contact form end--------------------------------------------------------


// Check if function exists and hook into setup.
if( function_exists('acf_register_block_type') ) {
    add_action('acf/init', 'register_acf_block_types');
}

if( function_exists('acf_add_options_page') ) {
	acf_add_options_page('Global Variables');
}


?>
