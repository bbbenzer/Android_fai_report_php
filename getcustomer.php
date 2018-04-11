<?php
require 'connect/connect.php';
$array = array();

if($_SERVER['REQUEST_METHOD']=='POST'){
    $DueDate = $_POST["DueDate"];
    $Sql = "SELECT CONCAT(customer.FName,' ',customer.LName) AS Fullname,saleorder.Cus_Code,DATE_FORMAT(saleorder.DueDate ,'%d-%m-%Y') AS DueDate
              From  saleorder
              LEFT JOIN customer on customer.Cus_Code = saleorder.Cus_Code
              Where DATE(saleorder.DueDate) = DATE('$DueDate')
              and saleorder.Objective = '2'
              and saleorder.IsFinish = '3' and saleorder.IsCancel = '0'
              GROUP BY saleorder.Cus_Code
              order by saleorder.Cus_Code";
    $meQuery = mysql_query($Sql);
    while ($Result = mysql_fetch_assoc($meQuery)) {
        $Cus_Code = $Result["Cus_Code"];
        $Fullname = $Result["Fullname"];
        array_push($array,
          array('flag'=>"true",
          'Cus_Code'=>$Cus_Code,
          'Fullname'=>$Fullname
          )
        );
    }
}else {
  array_push($array,
    array('flag'=>"false")
  );
}
echo json_encode(array("result"=>$array));
mysql_close($meConnect)
 ?>
