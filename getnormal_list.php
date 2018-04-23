<?php
require 'connect/connect.php';
require 'class.php';
$array = array();

$dateobj = new DatetimeTH();

if($_SERVER['REQUEST_METHOD']=='POST'){
    $DueDate = $_POST["DueDate"];
    $Roomtype = $_POST["Roomtype"];
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
    switch ($Roomtype) {
      case "1":
      $Sql = "SELECT
              List.Item_Code,item.Barcode,item.NameTH,item.SalePrice,List.QtyFac,roomname,item.IsOrder,
              item_unit.Unit_Name,
              item.roomtypeID,
              List.DueDate,
              List.ProdDate
              ,coalesce((select itemstock.Qty from itemstock where DATE(itemstock.DueDate) = DATE('$DueDate') and itemstock.ItemCode = List.Item_Code ),0) AS StockQty

              ,
              ( select List2.OrderQty from
              	( select coalesce(SUM(SD.Qty),0)  AS OrderQty,SD.Item_Code
              		from saleorder_detail AS SD LEFT JOIN saleorder on saleorder.DocNo = SD.DocNo
              		where DATE(saleorder.DueDate) = DATE('$DueDate') and saleorder.Objective = '1' and saleorder.IsFinish = '3'
              		GROUP BY Item_Code
              ) AS List2 where List2.Item_Code = List.Item_Code ) AS OrderQty

              from

              (
              select facorderdetail.Item_Code,sum(facorderdetail.ItemFormula1) AS QtyFac,
              DATE_FORMAT(facorder.DueDate ,'%d-%m-%Y') AS DueDate,DATE_FORMAT(Now() ,'%d-%m-%Y') AS ProdDate

              from facorderdetail
              LEFT JOIN facorder on facorder.DocNo = facorderdetail.DocNo

              where DATE(facorder.DueDate) = DATE('$DueDate')

               and facorderdetail.ItemFormula1 > '0'
              and facorderdetail.Objective = '1'

              GROUP BY facorderdetail.Item_Code

              ) AS List

              LEFT JOIN item on item.Item_Code = List.Item_Code
              LEFT JOIN item_unit on item_unit.Unit_Code = item.Unit_Code
               LEFT JOIN roomtype on roomtype.roomtypeID = item.roomtypeID

              where  item.roomtypeID = '$Roomtype'
              and item.Item_Code not in ('0101010057','0101010008','0101010003','0102010297','0101010026','0101010005')

              and (IsOrder = '0' or IsOrder = '1' or IsOrder = '2' or IsOrder = '6' or IsOrder = '3' )
              order by item.NameTH,item.SalePrice
              ";
        break;
      case "10":
        $Sql = "SELECT
                List.Item_Code,item.Barcode,item.NameTH,item.SalePrice,List.QtyFac,roomname,item.IsOrder,
                item_unit.Unit_Name,
                item.roomtypeID,
                List.DueDate,
                List.ProdDate
                ,coalesce((select itemstock.Qty from itemstock where DATE(itemstock.DueDate) = DATE('$DueDate') and itemstock.ItemCode = List.Item_Code ),0) AS StockQty

                ,
                ( select List2.OrderQty from
                	( select coalesce(SUM(SD.Qty),0)  AS OrderQty,SD.Item_Code
                		from saleorder_detail AS SD LEFT JOIN saleorder on saleorder.DocNo = SD.DocNo
                		where DATE(saleorder.DueDate) = DATE('$DueDate') and saleorder.Objective = '1' and saleorder.IsFinish = '3'
                		GROUP BY Item_Code
                ) AS List2 where List2.Item_Code = List.Item_Code ) AS OrderQty

                from

                (
                select facorderdetail.Item_Code,sum(facorderdetail.ItemFormula1) AS QtyFac,
                DATE_FORMAT(facorder.DueDate ,'%d-%m-%Y') AS DueDate,DATE_FORMAT(Now() ,'%d-%m-%Y') AS ProdDate

                from facorderdetail
                LEFT JOIN facorder on facorder.DocNo = facorderdetail.DocNo

                where DATE(facorder.DueDate) = DATE('$DueDate')

                 and facorderdetail.ItemFormula1 > '0'
                and facorderdetail.Objective = '1'

                GROUP BY facorderdetail.Item_Code

                ) AS List

                LEFT JOIN item on item.Item_Code = List.Item_Code
                LEFT JOIN item_unit on item_unit.Unit_Code = item.Unit_Code
                 LEFT JOIN roomtype on roomtype.roomtypeID = item.roomtypeID

                where  item.roomtypeID = '$Roomtype'
                and item.Item_Code not in ('0101010057','0101010008','0101010003','0102010297','0101010026','0101010005')

                and (IsOrder = '0' or IsOrder = '1' or IsOrder = '2' or IsOrder = '6' or IsOrder = '3' )
                order by item.NameTH,item.SalePrice
                ";
        break;
      case "17":
        $Sql = "SELECT
                List.Item_Code,item.Barcode,item.NameTH,item.SalePrice,List.QtyFac,roomname,item.IsOrder,
                item_unit.Unit_Name,
                item.roomtypeID,
                List.DueDate,
                List.ProdDate
                ,coalesce((select itemstock.Qty from itemstock where DATE(itemstock.DueDate) = DATE('$DueDate') and itemstock.ItemCode = List.Item_Code ),0) AS StockQty

                ,
                ( select List2.OrderQty from
                	( select coalesce(SUM(SD.Qty),0)  AS OrderQty,SD.Item_Code
                		from saleorder_detail AS SD LEFT JOIN saleorder on saleorder.DocNo = SD.DocNo
                		where DATE(saleorder.DueDate) = DATE('$DueDate') and saleorder.Objective = '1' and saleorder.IsFinish = '3'
                		GROUP BY Item_Code
                ) AS List2 where List2.Item_Code = List.Item_Code ) AS OrderQty

                from

                (
                select facorderdetail.Item_Code,sum(facorderdetail.ItemFormula1) AS QtyFac,
                DATE_FORMAT(facorder.DueDate ,'%d-%m-%Y') AS DueDate,DATE_FORMAT(Now() ,'%d-%m-%Y') AS ProdDate

                from facorderdetail
                LEFT JOIN facorder on facorder.DocNo = facorderdetail.DocNo

                where DATE(facorder.DueDate) = DATE('$DueDate')

                 and facorderdetail.ItemFormula1 > '0'
                and facorderdetail.Objective = '1'

                GROUP BY facorderdetail.Item_Code

                ) AS List

                LEFT JOIN item on item.Item_Code = List.Item_Code
                LEFT JOIN item_unit on item_unit.Unit_Code = item.Unit_Code
                 LEFT JOIN roomtype on roomtype.roomtypeID = item.roomtypeID

                where  item.roomtypeID = '$Roomtype'

                and (IsOrder = '0' or IsOrder = '1' or IsOrder = '2' or IsOrder = '6' or IsOrder = '3' )
                order by item.NameTH,item.SalePrice
                ";
        break;

    }

    $meQuery = mysql_query($Sql);
    while ($Result = mysql_fetch_assoc($meQuery)) {
        $Number = $i;
        $NameTH = $Result["NameTH"];
        $Price = (int)$Result["SalePrice"];
        $Unit = $Result["Unit_Name"];
        $Qty = $Result["QtyFac"];
        $Roomname = $Result["roomname"];
        if($Roomtype=="17"&&$Result["Item_Code"]=="0101010057"){
            continue;
        }else{
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
        }
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
