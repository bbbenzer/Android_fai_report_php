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
    $Sql = "SELECT facorderdetail.Item_Code,item.Barcode,item.NameTH,item.SalePrice,sum(facorderdetail.ItemFormula1) AS Qty
            ,Unit_Name


            from facorderdetail
            LEFT JOIN facorder on facorder.DocNo = facorderdetail.DocNo
            LEFT JOIN item on item.Item_Code = facorderdetail.Item_Code
            LEFT JOIN item_unit on item_unit.Unit_Code = item.Unit_Code


            where DATE(facorder.DueDate) = DATE('$DueDate')

             and facorderdetail.ItemFormula1 > '0' AND item.roomtypeID = '14'

            and ( facorderdetail.Objective = '1' or facorderdetail.Objective = '2'  or facorderdetail.Objective = '7'  )
            and IsOrder = '0'


            GROUP BY facorderdetail.Item_Code,item.NameTH,item.SalePrice
            -- order by item.NameTH,item.SalePrice


            UNION ALL


            SELECT facorderdetail.Item_Code,item.Barcode,item.NameTH,item.SalePrice,sum(facorderdetail.ItemFormula1) AS Qty,

            (CASE WHEN IsOrder = '4' THEN 'ม้วน' ELSE Unit_Name END ) AS Unit_Name


            from facorderdetail
            LEFT JOIN facorder on facorder.DocNo = facorderdetail.DocNo
            LEFT JOIN item on item.Item_Code = facorderdetail.Item_Code
            LEFT JOIN item_unit on item_unit.Unit_Code = facorderdetail.Unit_Code
            LEFT JOIN roomtype on roomtype.roomtypeID = item.roomtypeID
            LEFT JOIN facformorder on facorderdetail.SetFormID = facformorder.SetFormID

            where DATE(facorder.DueDate) = DATE('$DueDate')

            -- and facorderdetail.ItemFormula1 > '0'

             AND item.roomtypeID = '14'

            -- and ( facorderdetail.Objective = '1' or facorderdetail.Objective = '2'  or facorderdetail   .Objective = '7'  )
            -- and IsOrder = '6'
             and ( facorderdetail.IsForm = '2' or facorderdetail.IsForm = '1' )
            and IsOrder = '5'

            GROUP BY facorderdetail.Item_Code,item.NameTH,item.SalePrice
            -- order by item.NameTH,item.SalePrice
            ";
    $meQuery = mysql_query($Sql);
    while ($Result = mysql_fetch_assoc($meQuery)) {
        if($i<=2){
          $Number = $i;
          $Price = (int)$Result["SalePrice"];
        }
          else {
            $Number = "";
            $Price = "";
          }
        $NameTH = $Result["NameTH"];

        $Unit = $Result["Unit_Name"];
        $Qty = $Result["Qty"];
        $Roomname = "ห้องพายถ้วย ป้าเครือ";
        array_push($array,
          array('flag'=>"true",
          'Number'=>$Number,
          'NameTH'=>$NameTH,
          'Price'=>$Price,
          'Unit'=>$Unit,
          'Qty'=>$Qty,
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
