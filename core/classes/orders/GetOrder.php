<?php

class GetOrder {

  private $ID;
  private $type;

  function primaryAddress($addresses){
    $primary_address = null;
    foreach($addresses as $address){
      if((int)$address['primary']==1){
        $primary_address = $address;
      }
    }
    return $primary_address;
  }

  public function __construct($ID, $type = null) {
       $this->postId = $ID;
       $this->customer = get_field("customer",$this->postId);
       $addresses = get_field("addresses",$this->customer);
       $this->primary_address = $this->primaryAddress($addresses);
       $this->first_name = get_field("first_name",$this->customer);
       $this->last_name = get_field("last_name",$this->customer);
       $this->primary_number = get_field("primary_number",$this->customer);
       $this->services = get_field("services",$this->postId);
       $this->total = get_field("total",$this->postId);
       $this->status = get_field("status",$this->postId);
       $this->date = get_field("date",$this->postId);
       $this->check_number = get_field("check_number",$this->postId);
  }

  public function getFull(){
    return array(
      "order_id"=>$this->postId,
      "customer"=>$this->customer,
      "first_name"=>$this->first_name,
      "last_name"=>$this->last_name,
      "address"=>$this->primary_address,
      "number"=>$this->primary_number,
      "services"=>$this->services,
      "total"=>$this->total,
      "payment"=>$this->status,
      "date"=>$this->date,
      "check_number"=>$this->check_number
    );
  }

}
