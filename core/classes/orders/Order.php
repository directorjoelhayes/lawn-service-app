<?php

class Order {

  private $data;
  private $date;

  private function servicesActive($services){
    foreach($services as $service){
      if((int)$service['active']==1){
        return true;
      }
    }
  }

  private function activeServices($services){
    $active_services = [];
    foreach($services as $service){
      if((int)$service['active']==1){
        $active_services[] = $service;
      }
    }
    return $active_services;
  }

  private function getTotal($services){
    $total = 0;
    foreach($services as $service){
      if((int)$service['active']==1){
        $total += $service['price'];
      }
    }
    return $total;
  }

  private function generateTimeStamp(){
    $now = date("Y-m-d H:i:s");
    $datetime = new DateTime($now, new DateTimeZone('America/New_York'));
    return $datetime->getTimestamp();
  }

  public function __construct($data, $date = null) {

    $this->customer = $data['customer_id'];
    $this->services = $data['services'];
    //check if order creation or order update
    if($data['order_id']==null){
    //check if services are active
      if($this->servicesActive($this->services)){
        $this->active_services = $this->activeServices($this->services);
        $this->total = $this->getTotal($this->services);
        if($date==null){
          $this->date = $this->generateTimeStamp();
        }else{
          $this->date = $data['date'];
        }
        $this->status = "waiting";
      }
    }else{
      //order update
      $this->postID = $data['order_id'];
      $this->total = $this->getTotal($this->services);
      $this->status = $data['payment'];
      $this->check_number = $data['check_number'];
    }

  }

  public function createOrder(){
    $new_post = array(
      'post_type' => 'orders',
      'post_title' => 'Draft title',
      'post_status'   => 'draft',
    );
    $postId = wp_insert_post($new_post);
    // Create post object
    $my_post = array(
      'ID' => $postId,
      'post_title'    => wp_strip_all_tags( $postId ),
      'post_status'   => 'publish'
    );
    wp_update_post( $my_post );

    update_field("customer",$this->customer,$postId);
    update_field("services",$this->active_services,$postId);
    update_field("total",$this->total,$postId);
    update_field("status",$this->status,$postId);
    update_field("date",$this->date,$postId);

    return $postId;
  }

  public function updateOrder(){

    $postId = $this->postID;

    update_field("services",$this->services,$postId);
    update_field("total",$this->total,$postId);
    update_field("status",$this->status,$postId);
    update_field("check_number",$this->check_number,$postId);

    return "successful";
  }

}
