<?php

class TimeMethod {

  private $date;

  private function generateTimeStamp(){
    $now = date("Y-m-d H:i:s");
    $datetime = new DateTime($now, new DateTimeZone('America/New_York'));
    return $datetime->getTimestamp();
  }

  public function __construct($date = null) {
    if($date==null){
      $this->date = $this->generateTimeStamp();
    }else{
      $this->date = $date;
    }
  }

  public function navigateMonths($navigate){

    //get current month
    $now = date("Y-m", $this->date);

    if($navigate == 'next'){
      //get next month
      $month = date("Y-m", strtotime("{$now} + 1 months"));
    }else if($navigate == 'previous'){
      //get previous month
      $month = date("Y-m", strtotime("{$now} - 1 months"));
    }

    //format time code
    $datetime = new DateTime($month, new DateTimeZone('America/New_York'));

    return $datetime->getTimestamp();

  }

}
