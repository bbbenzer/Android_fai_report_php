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
    $Sql = "    SELECT
            		NameForm,
            -- FORM ,
            -- COALESCE(SUM(FORM),0) AS FacQty,

            (CASE WHEN ItemCode IN ('0604010208','0604020209') THEN (select  SetForm from facformorder where List.ItemCode = facformorder.Item_Code
            			and COALESCE(SUM(FORM),0) BETWEEN facformorder.Minimum and facformorder.Maximum LIMIT 1 )
            ELSE (CASE WHEN List.FORM  <= '0' THEN CONCAT(NameForm,'   -   ','ไม่ทำ' )
            		ELSE (select  SetForm from facformorder where List.ItemCode = facformorder.Item_Code
            			and COALESCE(SUM(FORM),0) BETWEEN facformorder.Minimum and facformorder.Maximum LIMIT 1 ) END ) END  ) AS FormFac


            FroM

            (
            select
            		List2.ItemCode,List2.NameForm,SUM(List2.OrderQty) AS OrderQty,SUM(List2.FORMQty) AS FORMQty,List2.roomtypeID,List2.DueDate,List2.ItemCodeR
            -- ,stock.ItemCodeStk,stock.StockQty,
            -- 		,COALESCE(ROUND((sum(List2.OrderQty-List2.StockQty)*List2.MultipleValue)/List2.ItemForm,0),0)  AS FORMQty2

            		,(CASE WHEN List2.ItemCode = '0102010085' THEN COALESCE(ROUND(( (SUM(List2.OrderQty)-List2.StockQty)*List2.MultipleValue)/List2.ItemForm,0) - COALESCE((select stocksw.Qty from stocksw where stocksw.Item_Code = '0102010085' and DATE(stocksw.Date) = subdate(DATE('$DueDate'),1) ),0),0)
            			WHEN List2.ItemCode = '0102010088' THEN COALESCE(ROUND(( (SUM(List2.OrderQty)-List2.StockQty)*List2.MultipleValue)/List2.ItemForm,0) - COALESCE((select stocksw.Qty from stocksw where stocksw.Item_Code = '0102010088' and DATE(stocksw.Date) = subdate(DATE('$DueDate'),1) ),0),0)
            		WHEN List2.ItemCode = '0102010087' THEN COALESCE(ROUND(( (SUM(List2.OrderQty)-List2.StockQty)*List2.MultipleValue)/List2.ItemForm,0) - COALESCE((select stocksw.Qty from stocksw where stocksw.Item_Code = '0102010087' and DATE(stocksw.Date) = subdate(DATE('$DueDate'),1)  ),0),0)
            		ELSE abs(ROUND(((SUM(List2.OrderQty)-List2.StockQty) *List2.MultipleValue)/List2.ItemForm,0))
            	 			END ) AS FORM

            From

            	(


            select
            		List3.ItemCode,List3.NameForm,List3.OrderQty,List3.FORMQty,List3.roomtypeID,List3.DueDate,List3.ItemCodeR,List3.ItemForm
            		,List3.MultipleValue
            		,COALESCE(( select itemstock.Qty
            			from itemstock where  DATE(itemstock.DueDate) =  DATE('$DueDate') and itemstock.ItemCode = List3.ItemCodeR
            		),0) AS StockQty


            from

            (
            		SELECT item.ItemCode7 AS ItemCode,
            		item.NameTH AS NameForm ,
            		COALESCE(sum(sd.Qty),0) AS OrderQty,
            		-- 	(select NameTH from item AS itemd where itemd.Item_Code = '0102010085' ) AS NameForm,

            			COALESCE(ROUND((sum(sd.Qty)*item.MultipleValue)/item.ItemForm,0),0)  AS FORMQty,
            			item.MultipleValue,
            			item.ItemForm,
            			-- item_unit.Unit_Name,
            			item.roomtypeID,
            			DATE_FORMAT(saleorder.DueDate ,'%d-%m-%Y') AS DueDate
            	 		,(select item.Item_Code from item as item2 where item2.IsMain = '2' and item.ItemCode7 = item2.ItemCode7  ) AS ItemCodeR



            	-- 	 	,stock.ItemCode AS ItemCodeStk,COALESCE(stock.Qty,0) AS StockQty

            		from


            			 saleorder_detail AS sd
            			LEFT JOIN saleorder on saleorder.DocNo = sd.DocNo
            			LEFT JOIN item on item.Item_Code = sd.Item_Code

            			where DATE(saleorder.DueDate) = DATE('$DueDate')

            			 and sd.qty > '0'
            			AND item.RoomProductionID = '9'
            			and saleorder.IsCancel = '0'
            			and item.ItemCode7 IN ('0604010208','0102010087','0604020209')
            		-- 	and item.ItemCode7 IN ('0102010085','0604010208','0102010088','0102010087','0604020209')


            			GROUP BY item.NameTH,item.SalePrice
            			order by item.NameTH,item.SalePrice

            ) AS List3
            		)	AS List2

            GROUP BY List2.ItemCode

            			) AS List
            GROUP BY NameForm

            union all


            SELECT
            -- facorderdetail.Item_Code,
            item.NameTH,
            -- item.SalePrice,sum(facorderdetail.ItemFormula1) AS Qty,
             -- (CASE WHEN IsOrder = '4' THEN 'ม้วน' ELSE Unit_Name END ) AS Unit_Name,
            -- SetForm,

            (CASE WHEN SetForm is null THEN CONCAT(sum(facorderdetail.ItemFormula1),'  ',(CASE WHEN IsOrder = '4' THEN 'ม้วน' ELSE Unit_Name END ) ) ELSE SetForm END ) AS AAA

            from facorderdetail
            LEFT JOIN facorder on facorder.DocNo = facorderdetail.DocNo
            LEFT JOIN item on item.Item_Code = facorderdetail.Item_Code
            LEFT JOIN item_unit on item_unit.Unit_Code = facorderdetail.Unit_Code
            LEFT JOIN roomtype on roomtype.roomtypeID = item.roomtypeID
            LEFT JOIN facformorder on facorderdetail.SetFormID = facformorder.SetFormID

            where DATE(facorder.DueDate) = DATE('$DueDate')

            -- and facorderdetail.ItemFormula1 > '0'

             AND ( item.roomtypeID = '9'  or item.roomtypeID = '15' )

            -- and ( facorderdetail.Objective = '1' or facorderdetail.Objective = '2'  or facorderdetail   .Objective = '7'  )
            -- and IsOrder = '6'
             and ( facorderdetail.IsForm = '2' or facorderdetail.IsForm = '1' )
            and facorderdetail.Item_Code not in ('1404131710','0418010448','0403010195','0504010243')

            GROUP BY facorderdetail.Item_Code,item.NameTH,item.SalePrice,roomname,item.IsOrder,item.RoomPackID
            -- order by item.NameTH,item.SalePrice

            ";
    $meQuery = mysql_query($Sql);
    while ($Result = mysql_fetch_assoc($meQuery)) {
        $NameTH = $Result["NameForm"];
        $Form = $Result["FormFac"];
        array_push($array,
          array('flag'=>"true",
          'NameTH'=>$NameTH,
          'Form'=>$Form
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
