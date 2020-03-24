<?php

class GetSettings {

  public function __construct() {
       $this->phone_number = get_field("phone_number","option");
       $this->address = get_field("address","option");
  }

  public function getFull(){
    return array(
      "phone_number"=>$this->phone_number,
      "address"=>$this->address
    );
  }

}
