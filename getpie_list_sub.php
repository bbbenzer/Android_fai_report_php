<?php
require 'connect/connect.php';
require 'class.php';
$array = array();

$dateobj = new DatetimeTH();

if($_SERVER['REQUEST_METHOD']=='POST'){
    $DueDate = $_POST["DueDate"];
    // $Roomtype = "1";
    // $DueDate = "April 11, 2018";
    $date = explode(" ",$DueDate);
    $month = $date[0];
    $month = $dateobj->getNumber($dateobj->getNUMmonth($month));
    $year = $date[2];
    $date = explode(",",$date[1]);
    $day = $dateobj->getNumber($date[0]);

    $DueDate = $year."-".$month."-".$day;
    // echo $DueDate;

    $datesend = date_format (new DateTime($DueDate), 'd-m-Y');
    $i = 1;
    $Sql = "SELECT List2.ItemCode,


           (CASE WHEN sum(Qty) <= '0' THEN NameTH  ELSE (select  NameTH from facformorder where List2.ItemCode = facformorder.Item_Code
           and sum(Qty) BETWEEN facformorder.Minimum and facformorder.Maximum LIMIT 1 ) END ) 	 AS NameTH,


          (CASE WHEN sum(Qty) <= '0' THEN 'ไม่ทำ'  ELSE (select  SetForm from facformorder where List2.ItemCode = facformorder.Item_Code
          	 and sum(Qty) BETWEEN facformorder.Minimum and facformorder.Maximum LIMIT 1 ) END ) 	 AS Form


          	FROM

          (


          select
          			List.ItemCode,List.NameTH,List.SalePrice,
          			sum(Piece),
          	 		(CASE WHEN List.ItemCode = '1403060757' THEN COALESCE(sum(Piece) - COALESCE((select stocksw.Qty from stocksw where stocksw.Item_Code = '1403060757' and DATE(stocksw.Date) = subdate(DATE('$DueDate') ,1)  ),0),0) ELSE 
          	 		 		sum(Piece)  - COALESCE((select itemstock.Qty from itemstock where itemstock.ItemCode =  List.ItemCode and DATE(itemstock.DueDate) = DATE('$DueDate') ),0)
          	 			END ) AS Qty
          from
          (
          		SELECT
          			(CASE WHEN item.ItemCode7 is null then item.Item_Code else item.ItemCode7 end ) As ItemCode
          			   ,item.NameTH,item.SalePrice,

          			 Sum(sd.Qty),item.MultipleValue,
          			 sum(sd.Qty *item.MultipleValue) AS Piece,

          			 item.ItemForm,
          			 item.roomtypeID



          		FROM saleorder_detail AS sd
          		LEFT JOIN saleorder on sd.DocNo = saleorder.DocNo
          		LEFT JOIN item on item.Item_Code = sd.Item_Code
          -- 		LEFT JOIN item_unit on item_unit.Unit_Code = item.Unit_Code
          	-- 	LEFT JOIN roomtype on roomtype.roomtypeID = item.roomtypeID

          		where DATE(saleorder.DueDate) = DATE('$DueDate')
          		and sd.Qty > '0'
          		AND saleorder.IsCancel = '0'
          		AND saleorder.IsFinish = '3'
          and roomtypeID = '14'
          and IsOrder = '1'
          	-- 	and item.ItemCode7 = '1403060757'

          	GROUP BY ItemCode



          	) AS List

          	GROUP BY List.ItemCode


           ) AS List2

          GROUP BY List2.ItemCode
          ";
    $meQuery = mysql_query($Sql);
    while ($Result = mysql_fetch_assoc($meQuery)) {
        $NameTH = $Result["NameTH"];
        $Form = $Result["Form"];
        array_push($array,
          array('flag'=>"true",
          'NameTH'=>$NameTH,
          'Form'=>$Form,
          )
        );
        $i++;
    }
}else {
  array_push($array,
    array('flag'=>"false")
  );
}
echo json_encode(array("result"=>$array));
mysql_close($meConnect)
 ?>
