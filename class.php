<?php

class DatetimeTH
{
  public function getNumber($day)
  {
    $TH = '';
    switch ($day) {
      case '1':
        $TH = '01';
        break;
      case '2':
        $TH = '02';
        break;
      case '3':
        $TH = '03';
        break;
      case '4':
        $TH = '04';
        break;
      case '5':
        $TH = '05';
        break;
      case '6':
        $TH = '06';
        break;
      case '7':
        $TH = '07';
        break;
      case '8':
        $TH = '08';
        break;
      case '9':
        $TH = '09';
        break;
      default:
        $TH = $day;
    }

    return $TH;
  }

  public function getTHday($day)
  {
    $TH = '';
    switch ($day) {
      case 'Monday':
        $TH = 'จันทร์';
        break;
      case 'Tuesday':
        $TH = 'อังคาร';
        break;
      case 'Wednesday':
        $TH = 'พุธ';
        break;
      case 'Thursday':
        $TH = 'พฤหัสบดี';
        break;
      case 'Friday':
        $TH = 'ศุกร์';
        break;
      case 'Saturday':
        $TH = 'เสาร์';
        break;
      case 'Sunday':
        $TH = 'อาทิตย์';
        break;
    }

    return $TH;
  }

  public function getTHmonth($month)
  {
    $TH = '';
    switch ($month) {
      case 'January':
        $TH = 'มกราคม';
        break;
      case 'February':
        $TH = 'กุมภาพันธ์';
        break;
      case 'March':
        $TH = 'มีนาคม';
        break;
      case 'April':
        $TH = 'เมษายน';
        break;
      case 'May':
        $TH = 'พฤษภาคม';
        break;
      case 'June':
        $TH = 'มิถุนายน';
        break;
      case 'July':
        $TH = 'กรกฎาคม';
        break;
      case 'August':
        $TH = 'สิงหาคม';
        break;
      case 'September':
        $TH = 'กันยายน';
        break;
      case 'October':
        $TH = 'ตุลาคม';
        break;
      case 'November':
        $TH = 'พฤศจิกายน';
        break;
      case 'December':
        $TH = 'ธันวาคม';
        break;
    }
    return $TH ;
  }

  public function getNumberMonth($month)
  {
    $TH = '';
    switch ($month) {
      case 'มกราคม':
        $TH = '1';
        break;
      case 'กุมภาพันธ์':
        $TH = '2';
        break;
      case 'มีนาคม':
        $TH = '3';
        break;
      case 'เมษายน':
        $TH = '4';
        break;
      case 'พฤษภาคม':
        $TH = '5';
        break;
      case 'มิถุนายน':
        $TH = '6';
        break;
      case 'กรกฎาคม':
        $TH = '7';
        break;
      case 'สิงหาคม':
        $TH = '8';
        break;
      case 'กันยายน':
        $TH = '9';
        break;
      case 'ตุลาคม':
        $TH = '10';
        break;
      case 'พฤศจิกายน':
        $TH = '11';
        break;
      case 'ธันวาคม':
        $TH = '12';
        break;
    }
    return $TH ;
  }

  public function getNUMmonth($month)
  {
    $TH = '';
    switch ($month) {
      case 'January':
        $TH = '1';
        break;
      case 'February':
        $TH = '2';
        break;
      case 'March':
        $TH = '3';
        break;
      case 'April':
        $TH = '4';
        break;
      case 'May':
        $TH = '5';
        break;
      case 'June':
        $TH = '6';
        break;
      case 'July':
        $TH = '7';
        break;
      case 'August':
        $TH = '8';
        break;
      case 'September':
        $TH = '9';
        break;
      case 'October':
        $TH = '10';
        break;
      case 'November':
        $TH = '11';
        break;
      case 'December':
        $TH = '12';
        break;
    }
    return $TH ;
  }

  public function getTHyear($year)
  {
    return($year+543);
  }
}


 ?>
