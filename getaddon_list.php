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
      case "sandwish":
        $Sql = "SELECT
                List.Item_Code,item.Barcode,item.NameTH,item.SalePrice,List.QtyFac,
                item_unit.Unit_Name
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

                where  item.roomtypeID = '6'
                -- and item.roomtypeID != '14'
                and item.Item_Code not in ('0101010057','0101010008','0101010003','0102010297','0101010026','0101010005')

                and (IsOrder = '0' or IsOrder = '1' or IsOrder = '2' or IsOrder = '6' or IsOrder = '3' )
                -- order by item.NameTH,item.SalePrice


                UNION ALL


                SELECT
                List.Item_Code,item.Barcode,item.NameTH,item.SalePrice,List.QtyFac AS Qty,
                item_unit.Unit_Name AS unitName

                -- ,(select count(ID) from stocksw where DATE(stocksw.Date) = DATE('$DueDate')-1 and stocksw.Item_Code = (select ItemCode7 from item where item.Item_Code = List.Item_Code AND (item.ismain = '1' or item.ismain = '2') )) AS bbb

                ,(CASE WHEN COALESCE((select count(ID) from stocksw where DATE(stocksw.Date) = subdate(DATE('$DueDate'),1)  and stocksw.Item_Code = (select ItemCode7 from item where item.Item_Code = List.Item_Code AND (item.ismain = '1' or item.ismain = '2') ) ),0) > '0' THEN
                 	 CONCAT((select stocksw.Qty from stocksw where DATE(stocksw.Date) = subdate(DATE('$DueDate'),1)  and stocksw.Item_Code = (select ItemCode7 from item where item.Item_Code = List.Item_Code AND (item.ismain = '1' or item.ismain = '2') )),
                 	(CASE WHEN item.isMain = '1' THEN ' ม้วน'  WHEN item.isMain = '2' THEN ' แท่ง'  END ) )
                ELSE  coalesce((select itemstock.Qty from itemstock where DATE(itemstock.DueDate) = DATE('$DueDate') and itemstock.ItemCode = List.Item_Code ),0)
                END ) AS StockQty
                ,
                ( select List2.OrderQty from
                	( select coalesce(SUM(SD.Qty),0)  AS OrderQty,SD.Item_Code
                		from saleorder_detail AS SD LEFT JOIN saleorder on saleorder.DocNo = SD.DocNo
                		where DATE(saleorder.DueDate) = DATE('$DueDate')
                and saleorder.Objective = '1'
                and saleorder.IsFinish = '3'
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
                 LEFT JOIN roomtype on roomtype.roomtypeID = item.RoomPackID

                where IsPack = '1'
                and item.RoomPackID = '6'
                and item.Item_Code not in ('0101010057','0101010008','0101010003','0102010297','0101010026','0101010005','0101010018','0101010015')

                -- order by item.NameTH,item.SalePrice
                ";
        break;

      case "12":
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

      case "cake":
        $Sql = "( SELECT
                List.Item_Code,item.Barcode,item.NameTH,item.SalePrice,roomname,item.IsOrder,
                (CASE WHEN List.QtyFac < '0' THEN '0' ELSE List.QtyFac END ) AS QtyFac,
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

                where  item.roomtypeID = '15'
                and (IsOrder = '0' or IsOrder = '1' or IsOrder = '2' or IsOrder = '6' or IsOrder = '3' )
                order by item.NameTH,item.SalePrice )

                UNION All


                ( SELECT
                List.Item_Code,item.Barcode,item.NameTH,item.SalePrice,'ห้องแต่งเค้ก' AS roomname,item.IsOrder,
                (CASE WHEN List.QtyFac < '0' THEN '0' ELSE List.QtyFac END ) AS QtyFac,
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

                 -- and facorderdetail.ItemFormula1 > '0'
                and facorderdetail.Objective = '1'

                GROUP BY facorderdetail.Item_Code

                ) AS List

                LEFT JOIN item on item.Item_Code = List.Item_Code
                LEFT JOIN item_unit on item_unit.Unit_Code = item.Unit_Code
                 LEFT JOIN roomtype on roomtype.roomtypeID = item.roomtypeID

                where
                 item.Item_Code IN ('1405053278','0504010245','0504010222','1405053500','0504010228','1405043433','1405043480','0501010223','1405023479','1405023478','0504010225','1410014417','1410014416','0504010227','1404043754','0404010183','1404043755','1404163645','0414010143','1404164720')

                order by item.NameTH,item.SalePrice )
                ";
        break;

      case "20";
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
        $Stock = $Result["StockQty"];
        $Produce2 = $Result["OrderQty"];
        $Qty = $Result["QtyFac"];
        $Roomname = $Result["roomname"];
        if($Roomtype=="sandwish"){
          $Roomname = "ห้องแซนวิช";
        }
        elseif ($Roomtype=="cake") {
          $Roomname = "ห้องแต่งเค้ก";
        }
        array_push($array,
          array('flag'=>"true",
          'Number'=>$Number,
          'NameTH'=>$NameTH,
          'Price'=>$Price,
          'Unit'=>$Unit,
          'Stock'=>$Stock,
          'Produce2'=>$Produce2,
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
