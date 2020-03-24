<?php

class Settings {

  private $data;

  public function __construct($data) {
       $this->phone_number = $data['phone_number'];
       $this->address = $data['address'];
  }

  public function updateSettings(){
    update_field("phone_number",$this->phone_number,"option");
    update_field("address",$this->address,"option");
    return "successful";
  }

}
