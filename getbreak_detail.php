<?php
require 'connect/connect.php';
require 'class.php';
$array = array();

$dateobj = new DatetimeTH();

if($_SERVER['REQUEST_METHOD']=='POST'){
    $Cus_Code = $_POST["Cus_Code"];
    $DueDate = $_POST["DueDate"];
    // $Cus_Code = "9878";
    // $DueDate = "2018-04-11";

    if($DueDate==""){
      $DueDate = date("Y-m-d");
    }

    $datesend = date_format (new DateTime($DueDate), 'd-m-Y');
    $Custemp = "";
    $i = 1;
    $Sql = "SELECT group_concat(`aaa` separator ' / ') AS NameTH ,customer,CONCAT(Detail,'  เบรคชุด ',BreakGroup) AS CusName,Qty
                    FROM
                      (
                    SELECT
                    		CONCAT(	(case when IsDrink = '1' THEN NameTH ELSE '' END   ) ,
                    	(CASE WHEN IsDrink = '1' THEN '' ELSE CONCAT(NameTH,' ',ROUND(item.SalePrice,0),'  บาท' ) END  ) ) AS aaa,
                    saleorder.Detail,CONCAT(customer.FName,' ',customer.LName) AS customer2
                    ,(CASE WHEN customer.Cus_Code = '9897' THEN  CONCAT(customer.FName,' ',customer.LName,'    ส่งที่สาขาสวนดอก ') ELSE CONCAT(customer.FName,' ',customer.LName) END ) AS customer
                    ,sd.DocNo,
                    saleorder.BreakGroup ,(select Qty from saleorder_detail where DocNo = sd.DocNo limit 1 ) AS Qty

                     from saleorder_detail as sd
                    LEFT JOIN saleorder on sd.DocNo = saleorder.DocNo
                    LEFT JOIN customer on customer.Cus_Code = saleorder.Cus_Code

                    LEFT JOIN item on item.Item_Code = sd.Item_Code

                    WHERE DATE(saleorder.DueDate) = DATE('$DueDate')
                    and saleorder.Objective = '2'
                    and saleorder.IsFinish = '3' and saleorder.IsCancel = '0'
                    and customer.Cus_Code = '$Cus_Code'

                    ORDER BY IsDrink DESC
                      )
                      AS PP
                     GROUP BY Customer,Detail,DocNo,BreakGroup;";
    $meQuery = mysql_query($Sql);
    while ($Result = mysql_fetch_assoc($meQuery)) {
      if($i==1){
        $NameTH = $i.". ".$Result["NameTH"];
        $CusName = $Result["CusName"];
        $Custemp = $CusName;
        $i++;
      }else {
        if($Custemp != $Result["CusName"] || $Custemp==""){
          $i = 1;
          $NameTH = $i.". ".$Result["NameTH"];
          $CusName = $Result["CusName"];
          $Custemp = $CusName;
        }else {
          $NameTH = $i.". ".$Result["NameTH"];
          $CusName = "BLANK_TRUE";
          $i++;
        }
      }

        $customer = $Result["customer"];
        $Qty = $Result["Qty"];
        array_push($array,
          array('flag'=>"true",
          'NameTH'=>$NameTH,
          'customer'=>$customer,
          'CusName'=>$CusName,
          'Qty'=>$Qty,
          'DueDate'=>$datesend
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
