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
    $Sql = "SELECT List.ItemCode,
			    		 (select  QtyFac from facformorder where List.ItemCode = facformorder.Item_Code
			    		 and sum(Piece) BETWEEN facformorder.Minimum and facformorder.Maximum LIMIT 1 ) AS Piece ,
			    		 roomtypeID,
						 (select  subunit from facformorder where List.ItemCode = facformorder.Item_Code
						 and sum(Piece) BETWEEN facformorder.Minimum and facformorder.Maximum LIMIT 1 ) AS subUnit,

			    (select  SetFormID from facformorder where List.ItemCode = facformorder.Item_Code
			    		 and sum(Piece) BETWEEN facformorder.Minimum and facformorder.Maximum LIMIT 1 ) AS SetFormID,

					(select  NameTH from facformorder where List.ItemCode = facformorder.Item_Code
			    		 and sum(Piece) BETWEEN facformorder.Minimum and facformorder.Maximum LIMIT 1 ) AS NameTH,

					(select  SetForm from facformorder where List.ItemCode = facformorder.Item_Code
			    		 and sum(Piece) BETWEEN facformorder.Minimum and facformorder.Maximum LIMIT 1 ) AS Form,
					Piece AS RealPiece

			    		 FROM
			    		 (
			    		 SELECT
			    		 (CASE WHEN item.ItemCode7 is null then item.Item_Code else item.ItemCode7 end ) As ItemCode
			    		 ,item.NameTH,

			    		 Sum(facorderdetail.ItemFormula1),item.MultipleValue,
			    		  sum(facorderdetail.ItemFormula1 *item.MultipleValue) AS Piece,

			    		 item.ItemForm,
			    		 item.roomtypeID

			    		 FROM facorderdetail
			    		 LEFT JOIN facorder on facorderdetail.DocNo = facorder.DocNo
			    		 LEFT JOIN item on item.Item_Code = facorderdetail.Item_Code
			    		 LEFT JOIN item_unit on item_unit.Unit_Code = item.Unit_Code
			    		 LEFT JOIN roomtype on roomtype.roomtypeID = item.roomtypeID

			    		 where DATE(facorder.DueDate) = DATE('$DueDate')
			    		 and facorderdetail.ItemFormula1 > '0'
			    		and item.ItemCode7 IN ( '0403010197','0403010198' )

			    		  GROUP BY ItemCode
			    		  ) AS List

			    	 	 GROUP BY List.ItemCode
";
    $meQuery = mysql_query($Sql);
    while ($Result = mysql_fetch_assoc($meQuery)) {
        $NameTH = $Result["NameTH"];
        $Qty = (int)$Result["RealPiece"];
        $Form = $Result["Form"];
        $Roomname = "ห้องครัว-ไส้สังขยา";
        array_push($array,
          array('flag'=>"true",
          'NameTH'=>$NameTH,
          'Qty'=>$Qty,
          'Form'=>$Form,
          'DueDate'=>$datesend,
          'Roomname'=>$Roomname
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
