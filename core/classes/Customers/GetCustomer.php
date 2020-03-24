<?php

class GetCustomer {

  private $ID;
  private $type;

  public function __construct($ID, $type = null) {
       $this->postId = $ID;
       $this->first_name = get_field("first_name",$this->postId);
       $this->last_name = get_field("last_name",$this->postId);
       $this->primary_number = get_field("primary_number",$this->postId);
       $this->secondary_number = get_field("secondary_number",$this->postId);
       $this->addresses = get_field("addresses",$this->postId);
       $this->services = get_field("services",$this->postId);
       $this->status = get_field("status",$this->postId);
  }

  public function getFull(){
    return array(
      "customer_id"=>$this->postId,
      "first_name"=>$this->first_name,
      "last_name"=>$this->last_name,
      "primary_number"=>$this->primary_number,
      "secondary_number"=>$this->secondary_number,
      "addresses"=>$this->addresses,
      "services"=>$this->services,
      "status"=>$this->status
    );
  }

}
