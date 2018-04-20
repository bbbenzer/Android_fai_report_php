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
    $Sql = "SELECT facorderdetail.Item_Code,item.Barcode,item.NameTH,item.SalePrice,sum(facorderdetail.ItemFormula1) AS Qty,Unit_Name

            from facorderdetail
            LEFT JOIN facorder on facorder.DocNo = facorderdetail.DocNo
            LEFT JOIN item on item.Item_Code = facorderdetail.Item_Code
            LEFT JOIN item_unit on item_unit.Unit_Code = item.Unit_Code

            where DATE(facorder.DueDate) = DATE('$DueDate')

             and facorderdetail.ItemFormula1 > '0' AND item.roomtypeID = '9'

            and ( facorderdetail.Objective = '1' or facorderdetail.Objective = '2'  or facorderdetail.Objective = '7'  )
            -- and (IsOrder = '0' or IsOrder = '1' or IsOrder = '2' or IsOrder = '5' )
            and (IsOrder = '0' or IsOrder = '2' or IsOrder = '5' )
            and facorderdetail.Item_Code not in ('1410013497','0405010215')

            GROUP BY facorderdetail.Item_Code,item.NameTH,item.SalePrice
            -- order by item.NameTH,item.SalePrice

            UNION All

            SELECT item.Item_Code,item.Barcode,item.NameTH,item.SalePrice,sum(facorderdetail.ItemFormula1) AS Qty,Unit_Name

            from facorderdetail
            LEFT JOIN facorder on facorder.DocNo = facorderdetail.DocNo
            LEFT JOIN item on item.Item_Code = facorderdetail.Item_Code
            LEFT JOIN item_unit on item_unit.Unit_Code = item.Unit_Code
            LEFT JOIN roomtype on roomtype.roomtypeID = item.roomtypeID

            where DATE(facorder.DueDate) = DATE('$DueDate')

             and facorderdetail.ItemFormula1 > '0'

            and ( facorderdetail.Objective = '1' or facorderdetail.Objective = '2'  or facorderdetail.Objective = '7'  )
             and facorderdetail.Item_Code IN ('1405018226','0504010243')
             GROUP BY facorderdetail.Item_Code,item.NameTH
            -- order by item.NameTH,item.SalePrice
            ";
    $meQuery = mysql_query($Sql);
    while ($Result = mysql_fetch_assoc($meQuery)) {
        if($i<=7){
          $Number = $i;
        }
          else {
            $Number = "";
          }
        $NameTH = $Result["NameTH"];
        $Price = (int)$Result["SalePrice"];
        $Unit = $Result["Unit_Name"];
        $Qty = $Result["Qty"];
        $Roomname = "ห้องซอฟเค้ก";
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
