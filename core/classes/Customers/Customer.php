<?php

class Customer {

  private $user_data;

  private function updateFields($postId){
    update_field("first_name",$this->first_name,$postId);
    update_field("last_name",$this->last_name,$postId);
    update_field("primary_number",$this->primary_number,$postId);
    update_field("secondary_number",$this->secondary_number,$postId);
    update_field("addresses",$this->addresses,$postId);
    update_field("services",$this->services,$postId);
    update_field("status",$this->status,$postId);
  }

  public function __construct($user_data) {
       $this->postID = $user_data['customer_id'];
       $this->first_name = $user_data['first_name'];
       $this->last_name = $user_data['last_name'];
       $this->primary_number = $user_data['primary_number'];
       $this->secondary_number = $user_data['secondary_number'];
       $this->addresses = $user_data['addresses'];
       $this->services = $user_data['services'];
       $this->status = $user_data['status'];
  }

  public function createCustomer(){
    $new_post = array(
      'post_type' => 'customer',
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

    $this->updateFields($postId);

    return $postId;
  }

  public function updateCustomer(){

    $postId = $this->postID;

    $this->updateFields($postId);

    return "successful";
  }

}
