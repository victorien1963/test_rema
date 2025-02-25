<?php
define( "ROOTPATH", "../../" );
include( ROOTPATH."includes/admin.inc.php" );
include( "language/".$sLan.".php" );
include( "func/upload.inc.php" );
include( "func/pos.inc.php" );
include( ROOTPATH."includes/ebmail.inc.php" );
needauth( 0 );
$act = $_POST['act'];
$act = $act? $act:$_GET['specact'];


switch ( $act )
{
case "ordertuiyun" :
		needauth( 325 );
		$orderid = $_POST['orderid'];
		$now = time();
		$msql->query( "select * from {P}_shop_order where orderid='{$orderid}'" );
		if ( $msql->next_record( ) )
		{
				$iftuiyun = $msql->f( "iftuiyun" );
				switch ( $iftuiyun )
				{
				case "0" :
						$tuiyun = 1;
						break;
				case "1" :
						$tuiyun = 0;
						break;
				}
				if($tuiyun==1){
					$fsql->query( "update {P}_shop_order set iftuiyun='$tuiyun',tuiyuntime='{$now}' where orderid='{$orderid}'" );
				}else{
					$fsql->query( "update {P}_shop_order set iftuiyun='$tuiyun',tuiyuntime='0' where orderid='{$orderid}'" );
				}
				echo "OK_".$tuiyun;
				exit( );
		}
		else
		{
				echo "1000";
				exit( );
		}
		echo "OK";
		exit( );
		break;
case "ordergettui" :
		needauth( 325 );
		$orderid = $_POST['orderid'];
		$now = time();
		$msql->query( "select * from {P}_shop_order where orderid='{$orderid}'" );
		if ( $msql->next_record( ) )
		{
				$ifgettui = $msql->f( "ifgettui" );
				switch ( $ifgettui )
				{
				case "0" :
						$gettui = 1;
						break;
				case "1" :
						$gettui = 0;
						break;
				}
				if($gettui==1){
					$fsql->query( "update {P}_shop_order set ifgettui='$gettui',gettuitime='{$now}' where orderid='{$orderid}'" );
				}else{
					$fsql->query( "update {P}_shop_order set ifgettui='$gettui',gettuitime='0' where orderid='{$orderid}'" );
				}
				echo "OK_".$gettui;
				exit( );
		}
		else
		{
				echo "1000";
				exit( );
		}
		echo "OK";
		exit( );
		break;
case "chgsales" :
		if(strtolower($_COOKIE["SYSUSER"]) != "wayhunt" && strtolower($_COOKIE["SYSUSER"]) != "rema" && strtolower($_COOKIE["SYSUSER"]) != "richie"){
			echo "你沒有修改權限！";
			exit();
			break;
		}
		$orderid = $_POST['orderid'];
		$source = $_POST['source'];
		$fsql->query( "update {P}_shop_order set `sales`='{$source}' where orderid='{$orderid}'" );
		echo "OK";
		exit();
		break;
case "ordermodipaytype" :
		needauth( 321 );
		$orderid = $_POST['orderid'];
		$payid = $_POST['payid'];
		$paytype = htmlspecialchars( str_replace("(現場)","",$_POST['paytype']) );
		$fsql->query( "update {P}_shop_order set `payid`='{$payid}',`paytype`='$paytype' where orderid='{$orderid}'" );
		echo "OK";
		break;
case "chgsource" :
		if(strtolower($_COOKIE["SYSUSER"]) != "wayhunt" && strtolower($_COOKIE["SYSUSER"]) != "rema" && strtolower($_COOKIE["SYSUSER"]) != "richie"){
			echo "你沒有修改權限！";
			exit();
			break;
		}
		$orderid = $_POST['orderid'];
		$source = $_POST['source'];
		$fsql->query( "update {P}_shop_order set `source`='{$source}' where orderid='{$orderid}'" );
		echo "OK";
		exit();
		break;
case "ordermodisaddr" :
		$gg = needauth( 332 ,0);
		if($gg === false){
			echo "！！！你沒有修改權限！！！";
			exit();
			break;
		}
		$orderid = $_POST['orderid'];
		$name = htmlspecialchars($_POST['name']);
		$sname = htmlspecialchars($_POST['sname']);
		$tel = htmlspecialchars( $_POST['tel'] );
		$stel = htmlspecialchars( $_POST['stel'] );
		$mobi = htmlspecialchars( $_POST['mobi'] );
		$smobi = htmlspecialchars( $_POST['smobi'] );
		$spostcode = htmlspecialchars( $_POST['spostcode'] );
		$saddr = htmlspecialchars( $_POST['saddr'] );
		$email = htmlspecialchars( $_POST['email'] );
		$fsql->query( "update {P}_shop_order set `email`='{$email}',`name`='{$name}',`s_name`='{$sname}',`tel`='$tel',`s_tel`='$stel',`s_postcode`='$spostcode',`mobi`='$mobi',`s_mobi`='$smobi',`s_addr`='$saddr' where orderid='{$orderid}'" );
		echo "OK";
		exit();
		break;
case "chgorixuhao" :
		$shopid = $_POST['shopid'];
		$xuhao = $_POST['xuhao'];
		$fsql->query( "update {P}_shop_con set xuhao='$xuhao' where id='{$shopid}'" );
		echo "OK";
		exit( );
		break;
case "chgsortxuhao" :
		$sortid = $_POST['sortid'];
		$gid = $_POST['gid'];
		$xuhao = $_POST['xuhao'];
		$catid = $_POST['catid'];
		if($sortid == ""){
			$fsql->query( "SELECT * FROM {P}_shop_sort WHERE gid='$gid' and catid='$catid' order by id desc limit 0,1" );
			if($fsql->next_record()){
				$inid = $fsql->f("id");
				$tsql->query( "update {P}_shop_sort set xuhao='$xuhao' where id='{$inid}'" );
				$newsortid = $inid;
			}else{
				$fsql->query( "DELETE FROM {P}_shop_sort WHERE gid='$gid' and catid='$catid'" );
				$fsql->query( "INSERT INTO {P}_shop_sort SET gid='$gid',catid='$catid',xuhao='$xuhao'" );
				$newsortid = $fsql->instid();
			}
		}else{
			$fsql->query( "SELECT * FROM {P}_shop_sort WHERE gid='$gid' and catid='$catid' order by id desc" );
			while($fsql->next_record()){
					$inid = $fsql->f("id");
					if($inid == $sortid){
						$tsql->query( "update {P}_shop_sort set xuhao='$xuhao' where id='{$sortid}'" );
					}else{
						$tsql->query( "DELETE FROM {P}_shop_sort WHERE id='$inid'" );
					}
			}
		}
		if($newsortid != ""){
			echo "OK_".$newsortid;
		}else{
			echo "OK";
		}
		
		exit( );
break;
case "postui" :
		$itemid = $_POST['itemid'];
		$orderid = $_POST['orderid'];
		
		$getori = $wsql->getone( "select OrderNo from {P}_shop_order where orderid='{$orderid}'" );
		$OrderNo = $getori['OrderNo'];
			
		$tsql->query( "select * from {P}_shop_orderitems where orderid='$orderid' and itemtui='1'" );
		while ( $tsql->next_record( ) )
		{
			//$xdtime = (date("Y",$tsql->f("dtime"))-1911).date("md",$tsql->f("dtime"));
			$xdtime = (date("Y")-1911).date("md");
			$xprice = (INT)$tsql->f("price");
			$xjine = (INT)$tsql->f("jine");
			$xnum = $tsql->f("nums");
			$xbn = $tsql->f("bn");
			list($xsize,$Xprice,$xspecid) = explode("^",$tsql->f("fz"));
			$gwtspec = $wsql->getone( "select posproid,gid from {P}_shop_conspec where id='{$xspecid}'" );
			
			$xbn = explode("-",$gwtspec['posproid']);
			$posxbn = explode(",",$xbn[0]);

			//增加庫存2017-05-17
			$wsql->query( "UPDATE {P}_shop_con SET kucun=kucun+{$xnum} WHERE id='{$gwtspec['gid']}'" );
			if($xspecid){
				$wsql->query( "UPDATE {P}_shop_conspec SET stocks=stocks+{$xnum} WHERE id='{$xspecid}'" );
			}

			//退貨
			include_once( ROOTPATH."costomer.php");
			$data['posproid'] = $gwtspec['posproid'];
			$data['itemid']   = $_POST['itemid'];
			$data['orderid']  = $_POST['orderid'];
			$stocks=upd_order_cancel(http_build_query($data));
			//api
			//$data['posproid'] = $gwtspec['posproid'];
			//$stocks=get_stock_one(http_build_query($data));
			
			//POS用--已刪除
			$xbn = explode("-",$gwtspec['posproid']);
			$posxbn = explode(",",$xbn[0]);
		}


		/*2017-05-13*/
		$uptime = time();
		$wsql->query( "UPDATE {P}_shop_order SET clicktuipos=clicktuipos+1,uptime='{$uptime}' where orderid='{$orderid}'" );
		
		echo "OK";
		exit( );
		break;
case "posdin" :
		$itemid = $_POST['itemid'];
		$orderid = $_POST['orderid'];

		/*單商品訂貨產生專用*/
		$tsql->query( "select * from {P}_shop_orderitems where id='$itemid'" );
		if ( $tsql->next_record( ) )
		{
			//$xdtime = (date("Y",$tsql->f("dtime"))-1911).date("md",$tsql->f("dtime"));
			$xdtime = (date("Y")-1911).date("md");
			$xprice = (INT)$tsql->f("price");
			$xjine = (INT)$tsql->f("jine");
			$xnum = $tsql->f("nums");
			$xbn = $tsql->f("bn");
			list($xsize,$Xprice,$xspecid) = explode("^",$tsql->f("fz"));
			$gwtspec = $wsql->getone( "select posproid,gid from {P}_shop_conspec where id='{$xspecid}'" );
			$xbn = $gwtspec['posproid'];
			
				//扣除庫存2017-05-17
				$wsql->query( "UPDATE {P}_shop_con SET kucun=kucun-{$xnum} WHERE id='{$gwtspec['gid']}'" );
				if($xspecid){
					$wsql->query( "UPDATE {P}_shop_conspec SET stocks=stocks-{$xnum} WHERE id='{$xspecid}'" );
				}
				/**/
			
			
				$getori = $wsql->getone( "select OrderNo from {P}_shop_order where orderid='{$orderid}'" );
				$OrderNo = $getori['OrderNo'].$ordstr;
			/**/
			
		}

		echo "OK";
		exit( );
		break;
case "orderitemtuilist" :
		//備忘：totalcent 也是記錄原訂購商品總金額，不包含其他折扣運費等
		needauth( 327 );
		$itemid = $_POST['itemid'];
		$now = time( );
		$msql->query( "select * from {P}_shop_orderitems where id='{$itemid}'" );
		if ( $msql->next_record( ) )
		{
				$orderid = $msql->f( "orderid" );
				$gid = $msql->f( "gid" );
				$nums = $msql->f( "nums" );
				$itemyun = $msql->f( "ifyun" );
				$itemtui = $msql->f( "iftui" );
				$jine = $msql->f( "jine" );
				$goods = $fsql->f("goods");
				
				
				$fsql->query( "select promocode,disaccount,promoprice from {P}_shop_order where orderid='{$orderid}'" );
				if ( $fsql->next_record( ) )
				{
					$promocode = $fsql->f( "promocode" );
					$disaccount = $fsql->f( "disaccount" );
					$promoprice = round($fsql->f( "promoprice" ));
					
					if($promocode != ""){
						$getPromo = $tsql->getone( "select * from {P}_shop_promocode where code='{$promocode}'" );
						if($getPromo["type"]==2){
							$prorate = $getPromo["type_value"];
						}else{
							$prorate = 1;
							$pricelimit = $getPromo["pricelimit"];
							$cutprice = $getPromo["type_value"];
						}
						$realjine = $jine;
						$jine = round($jine*$prorate);
					}else{
						$prorate = 1;
						$realjine = $jine;
					}
				}
				
				//2019-04-27 擷取沒有退訂商品的價格總額
				$alltui = true;
				$fsql->query( "select jine from {P}_shop_orderitems where orderid='{$orderid}' and itemtui='1'" );
				while ( $fsql->next_record( ) )
				{
					$alltuitjine += $fsql->f( "jine" );
				}
				$fsql->query( "select jine from {P}_shop_orderitems where orderid='{$orderid}' and itemtui='0'" );
				while ( $fsql->next_record( ) )
				{
					$leftjine += $fsql->f( "jine" );
					$alltui = false;
				}
				
				
				/*if ( $itemyun == "1" )
				{
						echo "1001";
						exit( );
				}*/
				
				//訂貨
				if ( $itemtui != "0" )
				{
						//$fsql->query( "update {P}_shop_orderitems set iftui='0',itemtui='0',yuntime='0',ifyun='1' where id='{$itemid}'" );
						$fsql->query( "update {P}_shop_orderitems set iftui='0',yuntime='0',ifyun='1',itemtui='0' where id='{$itemid}'" );
						/*修改總訂單價格*/
						$fsql->query( "update {P}_shop_order set goodstotal=goodstotal+{$realjine} where orderid='{$orderid}'" );
						$getNewO = $fsql->getone( "SELECT goodstotal FROM {P}_shop_order where orderid='{$orderid}'" );
						$newtotaloof = $getNewO["goodstotal"] - $disaccount;
						//計算折扣金 2020-09-22
						if($cutprice && $getNewO["goodstotal"]>$pricelimit){
							$newtotaloof = $newtotaloof - $cutprice;
						}else{
							$cutprice = 0;
						}
						
						$fsql->query( "update {P}_shop_order set paytotal={$newtotaloof},totaloof={$newtotaloof} where orderid='{$orderid}'" );
						
						/*2017-03-25 計算運費*/
							/*擷取錢幣符號*/
							$TWD = $fsql->getone( "SELECT pricesymbol,goodstotal,yunfei,payid,source FROM {P}_shop_order WHERE orderid='{$orderid}'" );
							$getsource = substr($TWD["source"],0,1);
							$getyunfei = $TWD["yunfei"];
							$goodstotal = $TWD["goodstotal"];
							$getpricesymbol = $TWD["pricesymbol"];
							$getpayid = $TWD["payid"];
							$SYM = $fsql->getone( "SELECT pricecode,rate,point FROM {P}_base_currency WHERE pricesymbol='{$getpricesymbol}'" );
							$getrate = $SYM["rate"];
							$getpoint = $SYM["point"];
							$getpricecode = $SYM["pricecode"];
							//多國價格
							if($cutprice){
								$multiprice = $getrate!="1"? round(($goodstotal-$getrate*$cutprice),$getpoint):round($goodstotal-$cutprice);
							}else{
								$multiprice = $getrate!="1"? round(($goodstotal*$getrate*$prorate),$getpoint):round($goodstotal*$prorate);
							}
							
							if($prorate<1){//小於1代表有折扣
								$proprice = $goodstotal-round($goodstotal*$prorate);
								$fsql->query( "UPDATE {P}_shop_order SET promoprice='{$proprice}' WHERE orderid='{$orderid}'" );
							}else{
								$fsql->query( "UPDATE {P}_shop_order SET promoprice='$cutprice' WHERE orderid='{$orderid}'" );
								$proprice = 0;
							}
							
							//$fsql->query( "UPDATE {P}_shop_order SET multiprice='{$multiprice}' WHERE orderid='{$orderid}'" );
							if($getpricecode == "TWD"){
								$fsql->query("select dgs from {P}_shop_yun where id='{$getpayid}'");
								if($fsql->next_record()){
									$dgs = $fsql->f("dgs");
									list($setyunfei, $setyunprice) = explode("|",$dgs);
									$oriyunfei = countyunfeip( $tweight, $goodstotal-$cutprice, $dgs, $getrate );//原始運費
									$yunfei = $getrate!="1"? round(($oriyunfei*$getrate),$getpoint):$oriyunfei;//多國用
								}
							}else{
								$fsql->query("select dgs from {P}_shop_yun where spec='{$getpricecode}'");
								if($fsql->next_record()){
									$dgs = $fsql->f("dgs");
									list($setyunfei, $setyunprice) = explode("|",$dgs);
									$oriyunfei = countyunfeip( $tweight, $goodstotal-$cutprice, $dgs, $getrate );//原始運費
									$yunfei = $getrate!="1"? round(($oriyunfei*$getrate),$getpoint):$oriyunfei;//多國用
								}
							}
							
							if($getyunfei == 0 && $yunfei > 0){
								if($getsource=="" || $getsource =="0"){
									$fsql->query( "UPDATE {P}_shop_order SET yunfei='{$oriyunfei}',multiyunfei='{$yunfei}',paytotal=paytotal+{$yunfei}-{$proprice},totaloof=totaloof+{$yunfei}-{$proprice},multiprice='{$multiprice}+{$yunfei}' WHERE orderid='{$orderid}'" );
								}
							}else{
								$fsql->query( "UPDATE {P}_shop_order SET paytotal=paytotal-{$proprice},yunfei='{$oriyunfei}',multiyunfei='{$yunfei}',totaloof=totaloof-{$proprice},multiprice='{$multiprice}' WHERE orderid='{$orderid}'" );
							}
						
						/*修改總訂單項目*/
						$fsql->query( "SELECT iftui,id,goods,nums FROM {P}_shop_orderitems WHERE orderid='{$orderid}'" );
						while( $fsql->next_record() ){
							$itid = $fsql->f("id");
							$iftui = $fsql->f("iftui");
							$goods = $fsql->f("goods");
							$nums = $fsql->f("nums");			
							if(!$iftui){
								$items .= $items? "\r\n".$goods."(".$nums.")":$goods."(".$nums.")";
							}else{
								$items .= $items? "\r\n<span style=\"color:red\"><退訂></span>".$goods."(".$nums.")":"<span style=\"color:red\"><退訂></span>".$goods."(".$nums.")";
							}
						}
						$fsql->query( "update {P}_shop_order set items='{$items}' where orderid='{$orderid}'" );
						$allitems = $msql->getone( "select count(id) as c from {P}_shop_orderitems where orderid='{$orderid}' and iftui='0'" );
						$alltuiitems = $msql->getone( "select count(id) as t from {P}_shop_orderitems where orderid='{$orderid}' and ifyun='1'" );
						if ( $allitems[c]!="0" && ($allitems[c] == $alltuiitems[t]) )
						{
								$fsql->query( "update {P}_shop_order set ifyun='1' where orderid='{$orderid}'" );
						}
						else
						{
								$fsql->query( "update {P}_shop_order set ifyun='0' where orderid='{$orderid}'" );
						}
					echo "1005";
					exit( );
				}
		}
		else
		{
				echo "1000";
				exit( );
		}
		
		$msql->query( "select ifpay,promocode,paytotal from {P}_shop_order where orderid='{$orderid}'" );
		if ( $msql->next_record( ) )
		{
			$ifpay = $msql->f("ifpay");
			$paytotal = $msql->f("paytotal");
			
			if($ifpay == 0){
				echo "1006";
				exit();
				//必須先收款才能退貨
			}
		}
		
		
		//退貨
		$fsql->query( "update {P}_shop_orderitems set iftui='1',itemtui='1',ifyun='0' where id='{$itemid}'" );
		/*修改總訂單價格*/
		$fsql->query( "update {P}_shop_order set goodstotal=goodstotal-{$realjine},paytotal=paytotal-{$jine} where orderid='{$orderid}'" );
		$getNewO = $fsql->getone( "SELECT goodstotal,paytotal FROM {P}_shop_order where orderid='{$orderid}'" );
		//計算折扣金 2020-09-22
		if($cutprice && $getNewO["goodstotal"]>$pricelimit){
			$newtotaloof = $getNewO["goodstotal"] - $cutprice;
			$fsql->query( "update {P}_shop_order set paytotal={$newtotaloof},totaloof={$newtotaloof} where orderid='{$orderid}'" );
		}else{
			//$fsql->query( "update {P}_shop_order set paytotal=paytotal+$promoprice where orderid='{$orderid}'" );
			$cutprice = 0;
		}
		
		
		/*2017-03-25 計算運費*/
			/*擷取錢幣符號*/
			$TWD = $fsql->getone( "SELECT pricesymbol,goodstotal,totaloof,yunfei,payid,source,disaccount,promoprice FROM {P}_shop_order WHERE orderid='{$orderid}'" );
			$getsource = substr($TWD["source"],0,1);
			$getyunfei = $TWD["yunfei"];
			$goodstotal = $TWD["goodstotal"]>0? $TWD["goodstotal"]:$TWD["totaloof"]-$getyunfei+$TWD["disaccount"]+$TWD["promoprice"];
			
			//如果是全退，運費應該是全部退訂商品總額去計算
			if($alltui){
				$yungoodstotal = $alltuitjine;
			}else{
				$yungoodstotal = $goodstotal- $cutprice;
			}

			$getpricesymbol = $TWD["pricesymbol"];
			$getpayid = $TWD["payid"];
			$SYM = $fsql->getone( "SELECT pricecode,rate,point FROM {P}_base_currency WHERE pricesymbol='{$getpricesymbol}'" );
			$getrate = $SYM["rate"];
			$getpoint = $SYM["point"];
			$getpricecode = $SYM["pricecode"];
			//多國價格
			if($cutprice){
				$multiprice = $getrate!="1"? round(($goodstotal-$getrate*$cutprice),$getpoint):round($goodstotal-$cutprice);
			}else{
				$multiprice = $getrate!="1"? round(($goodstotal*$getrate*$prorate),$getpoint):round($goodstotal*$prorate);
			}
			
			if($prorate<1){//小於1代表有折扣
				$proprice = $goodstotal-round($goodstotal*$prorate);
				$fsql->query( "UPDATE {P}_shop_order SET promoprice='{$proprice}' WHERE orderid='{$orderid}'" );
			}else{
				$fsql->query( "UPDATE {P}_shop_order SET promoprice='$cutprice' WHERE orderid='{$orderid}'" );
				$proprice = 0;
			}
			
			$fsql->query( "UPDATE {P}_shop_order SET multiprice='{$multiprice}' WHERE orderid='{$orderid}'" );
			
			if($getpricecode == "TWD"){
				$fsql->query("select dgs from {P}_shop_yun where id='{$getpayid}'");
				if($fsql->next_record()){
					$dgs = $fsql->f("dgs");
					list($setyunfei, $setyunprice) = explode("|",$dgs);
					$oriyunfei = countyunfeip( $tweight, $yungoodstotal, $dgs, $getrate );//原始運費
					$yunfei = $getrate!="1"? round(($oriyunfei*$getrate),$getpoint):$oriyunfei;//多國用
				}
			}else{
				$fsql->query("select dgs from {P}_shop_yun where spec='{$getpricecode}'");
				if($fsql->next_record()){
					$dgs = $fsql->f("dgs");
					list($setyunfei, $setyunprice) = explode("|",$dgs);
					$oriyunfei = countyunfeip( $tweight, $yungoodstotal, $dgs, $getrate );//原始運費
					$yunfei = $getrate!="1"? round(($oriyunfei*$getrate),$getpoint):$oriyunfei;//多國用
				}
			}
			
			if($getyunfei == 0 && $yunfei > 0){
				if($getsource=="" || $getsource =="0"){
					$fsql->query( "UPDATE {P}_shop_order SET yunfei='{$oriyunfei}',multiyunfei='{$yunfei}',paytotal=paytotal+{$oriyunfei},totaloof=totaloof+{$oriyunfei} WHERE orderid='{$orderid}'" );
				}
				
			} else{
				$fsql->query( "UPDATE {P}_shop_order SET paytotal=paytotal-{$proprice},yunfei='{$oriyunfei}',multiyunfei='{$yunfei}',totaloof=totaloof-{$proprice},multiprice='{$multiprice}' WHERE orderid='{$orderid}'" );
			}
			
			if($TWD["goodstotal"] == "0.00"){
				$fsql->query( "UPDATE {P}_shop_order SET yunfei='0.00',multiyunfei='0.00',paytotal='0.00' WHERE orderid='{$orderid}'" );
				//$fsql->query( "UPDATE {P}_shop_order SET paytotal='0.00' WHERE orderid='{$orderid}'" );
			}
			
		/*修改總訂單項目*/
		$fsql->query( "SELECT iftui,id,goods,nums FROM {P}_shop_orderitems WHERE orderid='{$orderid}'" );
		while( $fsql->next_record() ){
			$itid = $fsql->f("id");
			$iftui = $fsql->f("iftui");
			$goods = $fsql->f("goods");
			$nums = $fsql->f("nums");			
			if(!$iftui){
				$items .= $items? "\r\n".$goods."(".$nums.")":$goods."(".$nums.")";
			}else{
				$items .= $items? "\r\n<span style=\"color:red\"><退訂></span>".$goods."(".$nums.")":"<span style=\"color:red\"><退訂></span>".$goods."(".$nums.")";
			}
		}
		
		$fsql->query( "update {P}_shop_order set items='{$items}' where orderid='{$orderid}'" );
		$allitems = $msql->getone( "select count(id) as c from {P}_shop_orderitems where orderid='{$orderid}' and iftui='0'" );
		$alltuiitems = $msql->getone( "select count(id) as t from {P}_shop_orderitems where orderid='{$orderid}' and ifyun='1'" );
		if ( $allitems[c]!="0" && ($allitems[c] == $alltuiitems[t]) )
		{
				$fsql->query( "update {P}_shop_order set ifyun='1' where orderid='{$orderid}'" );
		}
		else
		{
				$fsql->query( "update {P}_shop_order set ifyun='0' where orderid='{$orderid}'" );
		}
		echo "OK";
		exit( );
		break;

case "orderitemchglist" :
	needauth( 327 );
	$itemid = $_POST['itemid'];
	$msql->query( "select * from {P}_shop_orderitems where id='{$itemid}'" );
		if ( $msql->next_record( ) )
		{
				$orderid = $msql->f( "orderid" );
			}
			else
			{
					echo "1000";
					exit( );
			}
	
		/*修改總訂單項目*/
		$fsql->query( "SELECT ifchg,id,goods,nums FROM {P}_shop_orderitems WHERE orderid='{$orderid}'" );
		while( $fsql->next_record() ){
			$itid = $fsql->f("id");
			$ifchg = $fsql->f("ifchg");
			$goods = $fsql->f("goods");
			$nums = $fsql->f("nums");			
			if(!$ifchg){
				$items .= $items? "\r\n".$goods."(".$nums.")":$goods."(".$nums.")";
			}else{
				$items .= $items? "\r\n<span style=\"color:blue\"><換貨></span>".$goods."(".$nums.")":"<span style=\"color:blue\"><換貨></span>".$goods."(".$nums.")";
			}
		}
		$uptime = time( );
		$fsql->query( "update {P}_shop_order set uptime='{$uptime}',items='{$items}' where orderid='{$orderid}'" );

		echo "OK";
		exit( );
		break;
case "dotuithis" :
		needauth( 327 );
		$orderid = $_POST['orderid'];
		$msql->query( "select * from {P}_shop_order where orderid='{$orderid}'" );
		if ( $msql->next_record( ) )
		{
				$ifpay = $msql->f( "ifpay" );
				$ifyun = $msql->f( "ifyun" );
				$ifok = $msql->f( "ifok" );
				$iftui = $msql->f( "iftui" );
				$mtcode = $msql->f( "mtcode" );
				$mtclickid = $msql->f( "mtclickid" );
				$OrderNo = $msql->f( "OrderNo" );
				$goodstotal = $msql->f( "goodstotal" );
				$dtime = $msql->f( "dtime" );
				
				if ( $iftui == "1" )
				{
						echo "1001";
						exit( );
				}
				if ( $ifyun == "1" )
				{
						echo "1002";
						exit( );
				}
		}
		else
		{
				echo "1000";
				exit( );
		}
		$fsql->query( "update {P}_shop_order set iftui='1' where orderid='{$orderid}'" );
		
		echo "OK";
		exit( );
		break;
case "doopenthis" :
		needauth( 327 );
		$orderid = $_POST['orderid'];
		$fsql->query( "update {P}_shop_order set iftui='0' where orderid='{$orderid}'" );
		echo "OK";
		exit( );
		break;
case "canceltui" :
		needauth( 327 );
		$itemid = $_POST['itemid'];
		$fsql->query( "update {P}_shop_orderitems set itemtui='0',iftui='0',ifpay='1' where id='{$itemid}'" );
		echo "OK";
		exit( );
		break;
case "cancelchg" :
	needauth( 327 );
	$itemid = $_POST['itemid'];
	$fsql->query( "update {P}_shop_orderitems set ifchg='0' where id='{$itemid}'" );
	echo "OK";
	exit( );
	break;
case "dountui" :
		needauth( 327 );
		$orderid = $_POST['orderid'];
		$fsql->query( "select * from {P}_shop_orderitems where itemtui='1' and orderid='$orderid'" );
		if($fsql->next_record()){
			echo "1001";
			exit( );
		}
		$fsql->query( "select * from {P}_shop_order where orderid='$orderid'" );
		if($fsql->next_record()){
			$fsql->query( "update {P}_shop_order set itemtui='0' where orderid='{$orderid}'" );
			echo "OK";
			exit( );
		}else{
			echo "1000";
			exit( );
		}
		echo "OK";
		exit( );
		break;
case "dounchg" :
			needauth( 327 );
			$orderid = $_POST['orderid'];
			$fsql->query( "select * from {P}_shop_orderitems where ifchg='1' and orderid='$orderid'" );
			if($fsql->next_record()){
				echo "1001";
				exit( );
			}
			$fsql->query( "select * from {P}_shop_order where orderid='$orderid'" );
			if($fsql->next_record()){
				$fsql->query( "update {P}_shop_order set ifchg='0' where orderid='{$orderid}'" );
				echo "OK";
				exit( );
			}else{
				echo "1000";
				exit( );
			}
			echo "OK";
			exit( );
			break;
	//讀取左側選單組
	case "menugrouplist" :
		$coltype = $_POST['coltype'];
		$pathcoltype = $coltype;
		$basecoltype = array("home","config");
		if(in_array($coltype, $basecoltype)){
			$pathcoltype = "base";
		}
		$str="<li><h4>功能目錄</h4></li>";
		$i=1;
		$msql->query("select g.id,m.* from {P}_adminmenu_group g LEFT JOIN {P}_adminmenu m ON g.id=m.groupid where g.coltype='$coltype' order by xuhao");
		while($msql->next_record()){
			$groupid=$msql->f('id');
			$menu=$msql->f('menu');
			$url=$msql->f('url');
			$authuser=explode(",",$msql->f('authuser'));
			if($ifshow == "0"){
				if(in_array($_COOKIE['SYSUSERID'],$authuser)){
					$str.="<li><a id='m".$i."' class='menulist' href='".ROOTPATH.$pathcoltype."/admin/".$url."' target='mainframe'><i class='fa fa-dashboard'></i> ".$menu."</a></li>";
				}
			}else{
				$str.="<li><a id='m".$i."' class='menulist' href='".ROOTPATH.$pathcoltype."/admin/".$url."' target='mainframe'><i class='fa fa-dashboard'></i> ".$menu."</a></li>";
			}
			//$str.="<li><a id='m".$i."' class='menulist' href='".ROOTPATH.$pathcoltype."/admin/".$url."' target='mainframe'><i class='fa fa-dashboard'></i> ".$menu."</a></li>";
			$i++;
		}
		echo $str;
		exit();
		break;
case "seltosub" :
		$getseid = $_POST['getseid'];
		$fmdpath = fmpath( $getseid );
		$src = "<select name='subid'>";
		$msql->query( "select id,bn,title,colorname from {P}_shop_con where ifsub='0' AND ifpic='0' AND catpath regexp '{$fmdpath}'" );
		while ( $msql->next_record( ) )
		{
			$src .= "<option value='".$msql->f(id)."'>".$msql->f(bn)." ".$msql->f(title)."(".$msql->f(colorname).")</option>";
			$ifmsql = true;
		}
		!$ifmsql && $src .= "<option value='0'>無商品</option>";
		$src .= "</select>";
		
		echo $src;
		exit( );
		break;
case "seltothpic" :
		$getseid = $_POST['getseid'];
		$fmdpath = fmpath( $getseid );
		$src = "<select name='subpicid'>";
		$msql->query( "select id,bn,title,colorname from {P}_shop_con where catpath regexp '{$fmdpath}'" );
		while ( $msql->next_record( ) )
		{
			$src .= "<option value='".$msql->f(id)."'>".$msql->f(bn)." ".$msql->f(title)."(".$msql->f(colorname).")</option>";
			$ifmsql = true;
		}
		!$ifmsql && $src .= "<option value='0'>無商品</option>";
		$src .= "</select>";
		
		echo $src;
		exit( );
		break;
case "seltosubpic" :
		$getseid = $_POST['getseid'];
		$fmdpath = fmpath( $getseid );
		$src = "<select name='subid'>";
		$msql->query( "select id,bn,title,colorname from {P}_shop_con where ifsub='0' AND ifpic='1' AND catpath regexp '{$fmdpath}'" );
		while ( $msql->next_record( ) )
		{
			$src .= "<option value='".$msql->f(id)."'>[配圖]".$msql->f(bn)." ".$msql->f(title)."(".$msql->f(colorname).")</option>";
			$ifmsql = true;
		}
		!$ifmsql && $src .= "<option value='0'>無商品</option>";
		$src .= "</select>";
		
		echo $src;
		exit( );
		break;
case "getcontent" :
		$nowid = $_POST['nowid'];
		$shoppageid = $_POST['shoppageid'];
		if ( $shoppageid == "-1" )
		{
				$src = "";
		}
		else if ( $shoppageid == "0" )
		{
				//$msql->query( "select src from {P}_shop_con where id='{$nowid}'" );
				$msql->query( "select * from {P}_shop_con where id='{$nowid}'" );
				if ( $msql->next_record( ) )
				{
						$src = $msql->f( "src" );
						$src .= "|".$msql->f( "body" );
						$src .= "|".$msql->f( "canshu" );
						$src .= "|".$msql->f( "shape" );
				}
		}
		else
		{
				$msql->query( "select * from {P}_shop_pages where id='{$shoppageid}'" );
				if ( $msql->next_record( ) )
				{
						$src = $msql->f( "src" );
				}
				else
				{
						$src = "";
				}
		}
		echo $src;
		exit( );
		break;
case "delspecicon" :
		needauth( 311 );
		$specid = htmlspecialchars( $_POST['specid'] );
				$msql->query( "select iconsrc from {P}_shop_conspec where id='{$specid}'" );
				if ( $msql->next_record( ) )
				{
						$oldsrc = $msql->f( "iconsrc" );
				}
				if ( file_exists( ROOTPATH.$oldsrc ) && $oldsrc != "" && !strstr( $oldsrc, "../" ) )
				{
						unlink( ROOTPATH.$oldsrc );
						$getpic = basename($oldsrc);
						$getpicpath = dirname($oldsrc);
						@unlink( ROOTPATH.$getpicpath."/sp_".$getpic );
				}
		if($msql->query( "UPDATE {P}_shop_conspec SET iconsrc='' WHERE id='{$specid}'" )){
			echo "OK";
		}else{
			echo "ERROR!";
		}
		exit( );
		break;
case "upspecicon" :
		needauth( 311 );
		$specid = $_GET['specid'];
		
		$sp_itempic = $_FILES['itempic_'.$specid];
		
		if(empty($sp_itempic['tmp_name']) || $sp_itempic['tmp_name'] == 'none')
		{
			$error = 'No file was uploaded..';
		}else{
			$msql->query( "select * from {P}_shop_config where `variable`='PhotoBWH'" );
			if ( $msql->next_record( ) )
			{
				list($PhotoBW,$PhotoBH) = explode("|",$msql->f( "value" ));
			}
			$msql->query( "select * from {P}_shop_config where `variable`='PhotoSWH'" );
			if ( $msql->next_record( ) )
			{
				list($PhotoSW,$PhotoSH) = explode("|",$msql->f( "value" ));
			}
				$nowdate = date( "Ymd", time( ) );
				$picpath = "../pics/".$nowdate;
				@mkdir( $picpath, 511 );
				$uppath = "shop/pics/".$nowdate;
				$arr = newuploadimage( $sp_itempic['tmp_name'], $sp_itempic['type'], $sp_itempic['size'], $uppath, $PhotoBW, $PhotoBH, $PhotoSW, $PhotoSH );
				if ( $arr[0] != "err" )
				{
						$src = $arr[3];
				}
				else
				{
						echo $Meta.$arr[1];
						exit( );
				}
				$msql->query( "select iconsrc from {P}_shop_conspec where id='{$specid}'" );
				if ( $msql->next_record( ) )
				{
						$oldsrc = $msql->f( "iconsrc" );
				}
				if ( file_exists( ROOTPATH.$oldsrc ) && $oldsrc != "" && !strstr( $oldsrc, "../" ) )
				{
						unlink( ROOTPATH.$oldsrc );
						$getpic = basename($oldsrc);
						$getpicpath = dirname($oldsrc);
						@unlink( ROOTPATH.$getpicpath."/sp_".$getpic );
				}
				$msql->query( "update {P}_shop_conspec set iconsrc='{$src}' where id='{$specid}'" );
				$error="";
				$msg=ROOTPATH.$src;
		}
		echo "{error: '$error',\n msg: '$msg'\n}";
		exit( );
		break;
case "fixspec" :
		needauth( 311 );
		$specid = htmlspecialchars( $_POST['specid'] );
		$f_size = htmlspecialchars( trim($_POST['f_size']) );
		$f_stocks = htmlspecialchars( trim($_POST['f_stocks']) );
		$f_posbn = htmlspecialchars( trim($_POST['f_posbn']) );
		
		$vals = $f_size;
		if(strtolower($vals) == "one"){
			$vals = "";
		}
		
		if($msql->query( "UPDATE {P}_shop_conspec SET `size`='{$f_size}',`stocks`='{$f_stocks}',`posproid`='{$f_posbn}{$vals}' WHERE id='{$specid}'" ))
		{
			echo "OK";
		}else{
			echo "ERROR!";
		}
		exit( );
		break;
case "delspec" :
		needauth( 311 );
		$specid = htmlspecialchars( $_POST['specid'] );
		if($msql->query( "DELETE FROM {P}_shop_conspec WHERE id='{$specid}'" )){
			echo "OK";
		}else{
			echo "ERROR!";
		}
		exit( );
		break;
case "addyunzone" :
		needauth( 311 );
		$zone = htmlspecialchars( $_POST['zone'] );
		$xuhao = htmlspecialchars( $_POST['xuhao'] );
		$msql->query( "insert into {P}_shop_yunzone set 
		`pid`='0',
		`zone`='{$zone}',
		`xuhao`='{$xuhao}'
		" );
		$id = $msql->instid( );
		echo "OK_".$id;
		exit( );
		break;
case "modiyunzone" :
		needauth( 311 );
		$zoneid = htmlspecialchars( $_POST['zoneid'] );
		$zone = htmlspecialchars( $_POST['zone'] );
		$xuhao = htmlspecialchars( $_POST['xuhao'] );
		$msql->query( "update {P}_shop_yunzone set 
			`zone`='{$zone}',
			`xuhao`='{$xuhao}' where id='{$zoneid}'  
		" );
		echo "OK";
		exit( );
		break;
case "delyunzone" :
		needauth( 311 );
		$zoneid = htmlspecialchars( $_POST['zoneid'] );
		$msql->query( "select id from {P}_shop_yunzone where pid='{$zoneid}'" );
		if ( $msql->next_record( ) )
		{
				echo $strYunZoneNTC1;
				exit( );
		}
		$msql->query( "delete from {P}_shop_yunzone where id='{$zoneid}'" );
		echo "OK";
		exit( );
		break;
case "delsubzone" :
		needauth( 311 );
		$zoneid = htmlspecialchars( $_POST['zoneid'] );
		$msql->query( "select id from {P}_shop_yunzone where pid='{$zoneid}'" );
		if ( $msql->next_record( ) )
		{
				echo $strYunZoneNTC1;
				exit( );
		}
		$msql->query( "delete from {P}_shop_yunzone where id='{$zoneid}'" );
		echo "OK";
		exit( );
		break;
case "addsubzone" :
		needauth( 311 );
		$pid = htmlspecialchars( $_POST['pid'] );
		$zone = htmlspecialchars( $_POST['zone'] );
		$xuhao = htmlspecialchars( $_POST['xuhao'] );
		$msql->query( "insert into {P}_shop_yunzone set 
		`pid`='{$pid}',
		`zone`='{$zone}',
		`xuhao`='{$xuhao}'
		" );
		$id = $msql->instid( );
		echo "OK_".$id;
		exit( );
		break;
case "opensubzone" :
		needauth( 311 );
		$pid = htmlspecialchars( $_POST['pid'] );
		$str = "";
		$newsubxuhao = 1;
		$msql->query( "select * from {P}_shop_yunzone where pid='{$pid}' order by xuhao" );
		while ( $msql->next_record( ) )
		{
				$str .= "<div class='subcat' id='subcat_".$msql->f( "id" )."'>";
				$str .= "<input id='subxuhao_".$msql->f( "id" )."' type='text' size='3' value='".$msql->f( "xuhao" )."' class='inputx' /> ";
				$str .= "<input id='subzone_".$msql->f( "id" )."' type='text'  size='28' value='".$msql->f( "zone" )."' class='inputx' /> ";
				$str .= "<input type='button' class='button_subzone_modify' id='subZoneModi_".$msql->f( "id" )."' value='".$strModify."' /> ";
				$str .= "<input type='button' class='button_subzone_del' id='subZoneDel_".$msql->f( "id" )."' value='".$strDelete."' /> ";
				$str .= "</div>";
				$newsubxuhao = $msql->f( "xuhao" ) + 1;
		}
		$str .= "<div class='subcat' id='addsubcat_".$pid."'>";
		$str .= "<input id='newsubxuhao_".$pid."' type='text' size='3' value='".$newsubxuhao."' class='inputx' /> ";
		$str .= "<input id='newsubzone_".$pid."' type='text' size='28' value='".$strYunNTC4."' class='inputx' onFocus=\"this.value=''\" /> ";
		$str .= "<input type='button' id='addSubZone_".$pid."' value='".$strYunSubZoneAdd."' class='button_subzone_add' /> ";
		$str .= "</div>";
		echo $str;
		exit( );
		break;
case "getyunzonelist" :
		$yunzonestr = $_POST['yunzonestr'];
		$arr = explode( "|", $yunzonestr );
		$showzonestr = "";
		for ( $i = 1;	$i < sizeof( $arr ) - 1;	$i++	)
		{
				if ( $arr[$i] != "" )
				{
						$zoneid = $arr[$i];
						$msql->query( "select * from {P}_shop_yunzone where id='{$zoneid}'" );
						if ( $msql->next_record( ) )
						{
								$zone = $msql->f( "zone" );
								$pid = $msql->f( "pid" );
								if ( $pid == 0 )
								{
										$showzonestr .= $zone."\n";
								}
								else
								{
										$fsql->query( "select * from {P}_shop_yunzone where id='{$pid}'" );
										if ( $fsql->next_record( ) )
										{
												$topzone = $fsql->f( "zone" );
												$showzonestr .= $topzone."/".$zone."\n";
										}
								}
						}
				}
		}
		echo $showzonestr;
		exit( );
		break;
case "memberpricelist" :
		$str = "<div style='border:1px #def solid;background-color:#f7fbfe;padding:10px 10px 15px 10px'>";
		$msql->query( "select * from {P}_member_type" );
		while ( $msql->next_record( ) )
		{
				$membertypeid = $msql->f( "membertypeid" );
				$membertype = $msql->f( "membertype" );
				$fsql->query( "select * from {P}_shop_pricerule where membertypeid='{$membertypeid}'" );
				if ( $fsql->next_record( ) )
				{
						$pr = $fsql->f( "pr" );
				}
				else
				{
						$pr = "1.00";
						$tsql->query( "insert into {P}_shop_pricerule set `membertypeid`='{$membertypeid}',`pr`='1.00'" );
				}
				$str .= "<div style='float:left;width:150px;height:25px;font:bold 12px/25px simsun'>".$membertype." </div><div style='float:left;height:25px'>".$strPriceRlue3."<input type='text' class='input' name='priceset[".$membertypeid."]' value='".$pr."'></div><br clear='all' />";
		}
		$str .= "</div>";
		echo $str;
		exit( );
		break;
case "goodsmemberprice" :
		$price = $_POST['price'];
		$str = "";
		$msql->query( "select * from {P}_shop_config where `variable`='PriceRule'" );
		if ( $msql->next_record( ) )
		{
				$pricerule = $msql->f( "value" );
		}
		if ( $pricerule == "2" )
		{
				$str = "<tr id='tr_memberprice'><td height='30' align='right' >".$strGoodsPrice2."</td><td >&nbsp;</td>";
				$str .= "<td height='30' id='td_memberprice'><div id='memberpriceDiv' style='width:480px;border:1px #def solid;background-color:#f7fbfe;padding:10px 10px 15px 10px'>";
				$msql->query( "select * from {P}_member_type" );
				while ( $msql->next_record( ) )
				{
						$membertypeid = $msql->f( "membertypeid" );
						$membertype = $msql->f( "membertype" );
						$fsql->query( "select * from {P}_shop_pricerule where membertypeid='{$membertypeid}'" );
						if ( $fsql->next_record( ) )
						{
								$pr = $fsql->f( "pr" );
								$memberprice = number_format( $price * $pr, 2, ".", "" );
						}
						else
						{
								$memberprice = number_format( $price, 2, ".", "" );
						}
						$str .= "<div style='float:left;width:120px;height:25px;font:12px/25px simsun'>".$membertype." </div><div style='float:left;width:200px;height:25px'><input type='text' class='input' name='memberprice[".$membertypeid."]' value='".$memberprice."'> ".$strHbDanwei."</div><br clear='all' />";
				}
				$str .= "</div></td></tr>";
		}
		echo $str;
		exit( );
		break;
case "modimemberprice" :
		$gid = $_POST['gid'];
		$price = $_POST['price'];
		$str = "";
		$msql->query( "select * from {P}_shop_config where `variable`='PriceRule'" );
		if ( $msql->next_record( ) )
		{
				$pricerule = $msql->f( "value" );
		}
		if ( $pricerule == "2" )
		{
				$str = "<tr id='tr_memberprice'><td height='30' align='right' >".$strGoodsPrice2."</td><td >&nbsp;</td>";
				$str .= "<td height='30' id='td_memberprice'><div id='memberpriceDiv' style='width:480px;border:1px #def solid;background-color:#f7fbfe;padding:10px 10px 15px 10px'>";
				$msql->query( "select * from {P}_member_type" );
				while ( $msql->next_record( ) )
				{
						$membertypeid = $msql->f( "membertypeid" );
						$membertype = $msql->f( "membertype" );
						$fsql->query( "select * from {P}_shop_memberprice where membertypeid='{$membertypeid}' and gid='{$gid}'" );
						if ( $fsql->next_record( ) )
						{
								$memberprice = $fsql->f( "price" );
								$memberprice = number_format( $memberprice, 2, ".", "" );
						}
						else
						{
								$memberprice = number_format( $price, 2, ".", "" );
						}
						$str .= "<div style='float:left;width:120px;height:25px;font:12px/25px simsun'>".$membertype." </div><div style='float:left;width:200px;height:25px'><input type='text' class='input' name='memberprice[".$membertypeid."]' value='".$memberprice."'> ".$strHbDanwei."</div><br clear='all' />";
				}
				$str .= "</div></td></tr>";
		}
		echo $str;
		exit( );
		break;
case "getmarketprice" :
		$price = $_POST['price'];
		$msql->query( "select * from {P}_shop_config where `variable`='MarketPrice'" );
		if ( $msql->next_record( ) )
		{
				$MarketPrice = $msql->f( "value" );
		}
		$p = number_format( $price * $MarketPrice, 2, ".", "" );
		echo $p;
		exit( );
		break;
case "proplist" :
		$catid = $_POST['catid'];
		$nowid = $_POST['nowid'];
		if ( $nowid != "" && $nowid != "0" )
		{
				$msql->query( "select * from {P}_shop_con where  id='{$nowid}'" );
				if ( $msql->next_record( ) )
				{
						$prop1 = $msql->f( "prop1" );
						$prop2 = $msql->f( "prop2" );
						$prop3 = $msql->f( "prop3" );
						$prop4 = $msql->f( "prop4" );
						$prop5 = $msql->f( "prop5" );
						$prop6 = $msql->f( "prop6" );
						$prop7 = $msql->f( "prop7" );
						$prop8 = $msql->f( "prop8" );
						$prop9 = $msql->f( "prop9" );
						$prop10 = $msql->f( "prop10" );
						$prop11 = $msql->f( "prop11" );
						$prop12 = $msql->f( "prop12" );
						$prop13 = $msql->f( "prop13" );
						$prop14 = $msql->f( "prop14" );
						$prop15 = $msql->f( "prop15" );
						$prop16 = $msql->f( "prop16" );
				}
		}
		$str = "<table width='100%'   border='0' align='center'  cellpadding='2' cellspacing='0' >";
		$i = 1;
		$msql->query( "select * from {P}_shop_prop where catid='{$catid}' order by xuhao" );
		while ( $msql->next_record( ) )
		{
				$propname = $msql->f( "propname" );
				$pn = "prop".$i;
				$str .= "<tr>";
				$str .= "<td width='100' height='30' align='right' >".$propname."</td><td width='5' >&nbsp;</td>";
				$str .= "<td height='30' >";
				$str .= "<input type='text' name='".$pn."' value='".$$pn."' class='input' style='width:499px;' />";
				$str .= "</td>";
				$str .= "</tr>";
				$i++;
		}
		$str .= "</table>";
		echo $str;
		exit( );
		break;
case "addpage" :
		needauth( 320 );
		$nowid = $_POST['nowid'];
		$xuhao = 0;
		if ( $nowid != "" && $nowid != "0" )
		{
				$msql->query( "select max(xuhao) from {P}_shop_pages where gid='{$nowid}'" );
				if ( $msql->next_record( ) )
				{
						$xuhao = $msql->f( "max(xuhao)" );
				}
				$xuhao = $xuhao + 1;
				$msql->query( "insert into {P}_shop_pages set gid='{$nowid}',xuhao='{$xuhao}' " );
		}
		echo "OK";
		exit( );
		break;
case "shoppageslist" :
		$nowid = $_POST['nowid'];
		$pageinit = $_POST['pageinit'];
		$str = "<ul>";
		$str .= "<li id='p_0' class='pages'>1</li>";
		$i = 2;
		$id = 0;
		$msql->query( "select id from {P}_shop_pages where gid='{$nowid}' order by xuhao" );
		while ( $msql->next_record( ) )
		{
				$id = $msql->f( "id" );
				$str .= "<li id='p_".$id."' class='pages'>".$i."</li>";
				$i++;
		}
		if ( $pageinit != "new" )
		{
				$id = $pageinit;
		}
		$str .= "<li id='addpage' class='addbutton'>".$strShopPagesAdd."</li>";
		if ( $pageinit != "0" )
		{
				$str .= "<li id='pagedelete' class='addbutton'>".$strShopPagesDel."</li>";
				$str .= "<li id='backtomodi' class='addbutton'>".$strBack."</li>";
		}
		$str .= "</ul><input id='shoppagesid' name='shoppagesid' type='hidden' value='".$id."'>";
		echo $str;
		exit( );
		break;

case "shopmodify" :
		needauth( 320 );
		$id = $_POST['id'];
		$pid = $_POST['pid'];
		$catid = $_POST['catid'];
		$subcatid = $_POST['subcatid'];
		$thirdcatid = $_POST['thirdcatid'];
		$fourcatid = $_POST['fourcatid'];
		$page = $_POST['page'];
		//$body = $_POST['body'];
		$mbody = addslashes($_POST['mbody']);
		$a_body = $_POST['a_body'];
		$b_body = $_POST['b_body'];
		$c_body = htmlspecialchars( addslashes($_POST['c_body']) );
		$d_body = $_POST['d_body'];
		//$canshu = $_POST['canshu'];
		$title = addslashes($_POST['title']);
		//$title = htmlspecialchars( $_POST['title'] );
		$author = htmlspecialchars( $_POST['author'] );
		$source = htmlspecialchars( $_POST['source'] );
		$memo = htmlspecialchars( addslashes($_POST['memo']) );
		$memotext = htmlspecialchars( addslashes($_POST['memotext']) );
		$afterSalesService = htmlspecialchars( addslashes($_POST['afterSalesService']) );
		$temperature = $_POST['temperature'];
		$ambience = $_POST['ambience'];
		$oldcatid = $_POST['oldcatid'];
		$oldcatpath = $_POST['oldcatpath'];
		$brandid = $_POST['brandid'];
		$bn = htmlspecialchars( $_POST['bn'] );
		$posbn = htmlspecialchars( $_POST['posbn'] );
		$price = htmlspecialchars( $_POST['price'] );
		$price0 = htmlspecialchars( $_POST['price0'] );
		$danwei = htmlspecialchars( $_POST['danwei'] );
		$weight = htmlspecialchars( $_POST['weight'] );
		$kucun = htmlspecialchars( $_POST['kucun'] );
		$cent = htmlspecialchars( $_POST['cent'] );
		$prop1 = $_POST['prop1'];
		//$prop1 = htmlspecialchars( $_POST['prop1'] );
		$prop2 = htmlspecialchars( $_POST['prop2'] );
		$prop3 = htmlspecialchars( $_POST['prop3'] );
		$prop4 = htmlspecialchars( $_POST['prop4'] );
		$prop5 = htmlspecialchars( $_POST['prop5'] );
		$prop6 = htmlspecialchars( $_POST['prop6'] );
		$prop7 = htmlspecialchars( $_POST['prop7'] );
		$prop8 = htmlspecialchars( $_POST['prop8'] );
		$prop9 = htmlspecialchars( $_POST['prop9'] );
		$prop10 = htmlspecialchars( $_POST['prop10'] );
		$prop11 = htmlspecialchars( $_POST['prop11'] );
		$prop12 = htmlspecialchars( $_POST['prop12'] );
		$prop13 = htmlspecialchars( $_POST['prop13'] );
		$prop14 = htmlspecialchars( $_POST['prop14'] );
		$prop15 = htmlspecialchars( $_POST['prop15'] );
		$prop16 = htmlspecialchars( $_POST['prop16'] );
		$prop17 = htmlspecialchars( $_POST['prop17'] );
		$prop18 = htmlspecialchars( $_POST['prop18'] );
		$prop19 = htmlspecialchars( $_POST['prop19'] );
		$prop20 = htmlspecialchars( $_POST['prop20'] );
		$tags = $_POST['tags'];
		$memberprice = $_POST['memberprice'];
		$pic = $_FILES['jpg'];
		$chgallprice = $_POST['chgallprice'];
		$sizeitem_A = $_POST['sizeitem_A'];
		$sizeitem_B = $_POST['sizeitem_B'];
		$sizeitem_C = $_POST['sizeitem_C'];
                     
		$sizeitem = $sizeitem_A."|".$sizeitem_B."|".$sizeitem_C;
		$mainsizetype = $_POST['mainsizetype'];
		foreach($mainsizetype AS $vvs){
			if($vvs != ""){
				$mainsizetypelist .= $mainsizetypelist? "|".$vvs:$vvs;
			}
		}
		$sizetype = $_POST['sizetype'];
		foreach($sizetype AS $vvs){
			if($vvs != ""){
				$sizetypelist .= $sizetypelist? "|".$vvs:$vvs;
			}
		}
		$sizechart = $_POST['sizechart'];
		$usepicsize = $_POST['usepicsize'];
		/*ADD STR*/
		$colorname = $_POST['colorname'];
		$colorcode= $_POST['colorcode'];
		$colorpic = $_FILES['colorpic'];
		$pic_body = $_FILES['jpg_body'];
		$pic_canshu = $_FILES['jpg_canshu'];
		$pic_shape = $_FILES['jpg_shape'];
		
		$sizechoice = $_POST['sizechoice'];
        $sizeBefore = $_POST['sizeBefore'];
		$ifsub = $_POST['ifsub'];
		$subid = $ifsub==1? $_POST['subid']:"";
		//配圖
		$ifpic = $_POST['ifpic'];		
		$subpicid = $_POST['subpicid'];	
		if($ifpic){
			$getsubpic = $msql->getone( "select bn,title,colorname,colorcode,starttime,endtime from {P}_shop_con where id='{$subpicid}'" );
			$bn = $getsubpic["bn"];
			$title = $getsubpic["title"];
			$colorname = $getsubpic["colorname"];
			$colorcode = $getsubpic["colorcode"];
			$starttime = $getsubpic["starttime"];
			$endtime = $getsubpic["endtime"];
		}
		
		
		$starttime = $starttime? $starttime:strtotime(htmlspecialchars( $_POST['starttime'] ));
		$endtime = $endtime? $endtime:strtotime(htmlspecialchars( $_POST['endtime'] ));
		
		$xuhao = $_POST['xuhao'];
		
		/*ADD END*/
		/*$body = url2path( $body );*/
		$mbody = url2path( $mbody );
		$a_body = url2path( $a_body );
		$b_body = url2path( $b_body );
		// $c_body = url2path( $c_body );
		$d_body = url2path( $d_body );
		/*$canshu = url2path( $canshu );
		$spe_selec = $_POST['spe_selec'];
		$isadd = $_POST['isadd'];*/
		
		$msql->query( "select * from {P}_shop_config where `variable`='PhotoBWH'" );
		if ( $msql->next_record( ) )
		{
				list($PhotoBW,$PhotoBH) = explode("|",$msql->f( "value" ));
		}
		$msql->query( "select * from {P}_shop_config where `variable`='PhotoSWH'" );
		if ( $msql->next_record( ) )
		{
				list($PhotoSW,$PhotoSH) = explode("|",$msql->f( "value" ));
		}
		
/*slob add 規格-STR*/
		if(!$ifpic){
			$spec = $_POST['spec'];
			$tcon = COUNT($spec[name]);
			for($t=0;$t<$tcon;$t++){
				$sp_name = trim($spec[name][$t]);
				$sp_stocks = trim($spec[stocks][$t]);
				$sp_price = $price;
				/*$sp_stocks >0 && */
				$msql->query( "INSERT INTO {P}_shop_conspec SET gid='{$id}',size='{$sp_name}',sprice='{$sp_price}',stocks='{$sp_stocks}'" );	
			}
				//庫存另計
				$msql->query( "SELECT SUM(stocks) AS allstocks FROM {P}_shop_conspec WHERE gid='{$id}'" );
				if($msql->next_record()){
					$kucun = $msql->f(allstocks);
				}
		}
		/*else{
			$msql->query( "DELETE FROM {P}_shop_conspec WHERE gid='{$id}'" );
		}*/
		/*slob add 規格-END*/
		if ( 0 < $pic['size'] )
		{
				$Meta = "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
		}
		if (!$ifpic && $bn == "" )
		{
				echo $Meta.$strShopNotice10;
				exit( );
		}
		if (!$ifpic && $title == "" )
		{
				echo $Meta.$strShopNotice6;
				exit( );
		}
		if (!$ifpic && $price == "" )
		{
				echo $Meta.$strShopNotice11;
				exit( );
		}
		/*if ( $price0 == "" )
		{
				echo $Meta.$strShopNotice12;
				exit( );
		}*/
		if (!$ifpic && $price0 == "" )
		{
				$price0 = 0;
		}
		if (!$ifpic && $danwei == "" )
		{
				echo $Meta.$strShopNotice13;
				exit( );
		}
		/*if ( $kucun == "" )
		{
				echo $Meta.$strShopNotice14;
				exit( );
		}*/
		if (!$ifpic && $weight == "" )
		{
				echo $Meta.$strShopNotice15;
				exit( );
		}
		if (!$ifpic && 200 < strlen( $title ) )
		{
				echo $Meta.$strShopNotice7;
				exit( );
		}
		if (!$ifpic && 65000 < strlen( $memo ) )
		{
				echo $Meta.$strShopNotice4;
				exit( );
		}
		/*if ( 65000 < strlen( $body ) )
		{
				echo $Meta.$strShopNotice5;
				exit( );
		}*/
		$uptime = time( );
		$msql->query( "select catpath from {P}_shop_cat where catid='{$catid}'" );
		if ( $msql->next_record( ) )
		{
				$catpath = $msql->f( "catpath" );
		}
		$msql->query( "select catpath from {P}_shop_cat where catid='{$subcatid}'" );
		if ( $msql->next_record( ) )
		{
				$subcatpath = $msql->f( "catpath" );
		}
		$msql->query( "select catpath from {P}_shop_cat where catid='{$thirdcatid}'" );
		if ( $msql->next_record( ) )
		{
				$thirdcatpath = $msql->f( "catpath" );
		}
		
		$msql->query( "select catpath from {P}_shop_cat where catid='{$fourcatid}'" );
		if ( $msql->next_record( ) )
		{
				$fourcatpath = $msql->f( "catpath" );
		}
		/*ZIP解壓上圖*/
		/*是否為壓縮檔*/
		$az =array("application/zip", "application/x-zip", "application/x-zip-compressed", "application/octet-stream");
		if ( in_array($pic['type'],$az) === FALSE )
		{
				//無法解壓
				$nowdate = date( "Ymd", time( ) );
				$picpath = "../pics/".$nowdate;
				@mkdir( $picpath, 0777 );
				$uppath = "shop/pics/".$nowdate;
				$arr = newuploadimage( $pic['tmp_name'], $pic['type'], $pic['size'], $uppath, $PhotoBW, $PhotoBH, $PhotoSW, $PhotoSH );
				if ( $arr[0] != "err" )
				{
						$src = $arr[3];
						$fsql->query( "select src from {P}_shop_con where id='{$id}'" );
						if ( $fsql->next_record( ) )
						{
								$oldsrc = $fsql->f( "src" );
						}
						if ( file_exists( ROOTPATH.$oldsrc ) && $oldsrc != "" && !strstr( $oldsrc, "../" ) )
						{
								unlink( ROOTPATH.$oldsrc );
								$getpic = basename($oldsrc);
								$getpicpath = dirname($oldsrc);
								@unlink( ROOTPATH.$getpicpath."/sp_".$getpic );
						}
						$fsql->query( "UPDATE {P}_shop_con SET src='{$src}' WHERE id='{$id}'" );
				}
		}else{
			
			/*解壓縮*/
			$arr = explode( ".", $pic['name'] );
			$modf = ROOTPATH."shop/admin/upmod/".$arr[0];
			copy($pic['tmp_name'],ROOTPATH."shop/admin/".$pic['name']);
			include(ROOTPATH."includes/pclzip.lib.php");
			$archive = new PclZip(ROOTPATH."shop/admin/".$pic['name']);
			$archive->extract(PCLZIP_OPT_PATH, $modf);
			@unlink(ROOTPATH."shop/admin/".$pic['name']);
			/*偵測.htm*/
			$gethtm = glob($modf."/{*.gif,*.jpg,*.jpeg,*.png}", GLOB_BRACE);
			if ( $gethtm )
			{
					//處理圖片
					foreach( $gethtm AS $vv ){
						$nowdate = date( "Ymd", time( ) );
						$picpath = "../pics/".$nowdate;
						@mkdir( $picpath, 0777 );
						$uppath = "shop/pics/".$nowdate;
						$getc = getimagesize($vv);
						list($gn)=explode(".",basename($vv));
						$gn = substr($gn,-2);
						$gn = str_replace("_","",$gn);
						$arr = newuploadimage( $vv, $getc['mime'], $getc['bits'], $uppath, $PhotoBW, $PhotoBH, $PhotoSW, $PhotoSH );
						if ( $arr[0] != "err" )
						{
								$src = $arr[3];
								if($gn == 1){
									$fsql->query( "UPDATE {P}_shop_con SET src='{$src}' WHERE id='{$id}'" );
								}else{
									
									$msql->query( "INSERT INTO {P}_shop_pages SET gid='{$id}',src='{$src}',xuhao='{$gn}'" );
								}
						}
					}
					delfold($modf);
			}
		}
		/*ZIP解壓上圖 END*/
		if ( 0 < $pic_body['size'] )
		{
				$nowdate = date( "Ymd", time( ) );
				$picpath = "../pics/".$nowdate;
				@mkdir( $picpath, 0777 );
				$uppath = "shop/pics/".$nowdate;
				$arr = newuploadimage( $pic_body['tmp_name'], $pic_body['type'], $pic_body['size'], $uppath, $PhotoBW, $PhotoBH, $PhotoSW, $PhotoSH );
				if ( $arr[0] != "err" )
				{
						$src_body = $arr[3];
				}
				else
				{
						echo $Meta.$arr[1];
						exit( );
				}
				$msql->query( "select body from {P}_shop_con where id='{$id}'" );
				if ( $msql->next_record( ) )
				{
						$oldsrc_body = $msql->f( "body" );
				}
				if ( file_exists( ROOTPATH.$oldsrc_body ) && $oldsrc_body != "" && !strstr( $oldsrc_body, "../" ) )
				{
						unlink( ROOTPATH.$oldsrc_body );
						$getpic = basename($oldsrc_body);
						$getpicpath = dirname($oldsrc_body);
						@unlink( ROOTPATH.$getpicpath."/sp_".$getpic );
				}
				$msql->query( "update {P}_shop_con set body='{$src_body}' where id='{$id}'" );
		}
		if ( 0 < $pic_canshu['size'] )
		{
				$nowdate = date( "Ymd", time( ) );
				$picpath = "../pics/".$nowdate;
				@mkdir( $picpath, 0777 );
				$uppath = "shop/pics/".$nowdate;
				$arr = newuploadimage( $pic_canshu['tmp_name'], $pic_canshu['type'], $pic_canshu['size'], $uppath, $PhotoBW, $PhotoBH, $PhotoSW, $PhotoSH );
				if ( $arr[0] != "err" )
				{
						$src_canshu = $arr[3];
				}
				else
				{
						echo $Meta.$arr[1];
						exit( );
				}
				$msql->query( "select canshu from {P}_shop_con where id='{$id}'" );
				if ( $msql->next_record( ) )
				{
						$oldsrc_canshu = $msql->f( "canshu" );
				}
				if ( file_exists( ROOTPATH.$oldsrc_canshu ) && $oldsrc_canshu != "" && !strstr( $oldsrc_canshu, "../" ) )
				{
						unlink( ROOTPATH.$oldsrc_canshu );
						$getpic = basename($oldsrc_canshu);
						$getpicpath = dirname($oldsrc_canshu);
						@unlink( ROOTPATH.$getpicpath."/sp_".$getpic );
				}
				$msql->query( "update {P}_shop_con set canshu='{$src_canshu}' where id='{$id}'" );
		}
		
		$desciptionFile = $_FILES['desciption'];
		$desciption = $_POST['desciption'];
		
		foreach($desciption as $key => $val) {
			if(array_key_exists('img', $val)) {
				continue;
			}
			$str = preg_replace_callback('/[^\S\r\n]+/u', function ($matches) {
				return str_repeat('#SPACE#', strlen($matches[0]));
			}, $val);
			$str = nl2br($str);
			
			$str = preg_replace('/\s+/', '', $str);
			$desciption[$key] = $str;
		}
		
		foreach($desciptionFile['tmp_name'] as $key => $d) {
			$nowdate = date( "Ymd", time( ) );
			$picpath = "../desciption/".$nowdate;
			@mkdir( $picpath, 511 );
			$uppath = "shop/desciption/".$nowdate;
			$arr = newuploadimage( $desciptionFile['tmp_name'][$key], $desciptionFile['type'][$key], $desciptionFile['size'][$key], $uppath, 3530, 3530, $PhotoSW, $PhotoSH );
			if ( $arr[0] != "err" )
			{
				
				$desciption[$key] = [
					'img' => $arr[3] 
				];
			}
			else
			{
					echo $Meta.$arr[1];
					exit( );
			}
			
		}
		function customSort($a, $b) {
			return $a - $b;
		}
		
		// 使用自定義排序函數對數組按照鍵名進行排序
		
		
		$desciption = array_filter($desciption);
		uksort($desciption, 'customSort');
		
		$desciption = json_encode($desciption, JSON_UNESCAPED_UNICODE);

		if( 0 < $pic_shape['size']) 
		{
			$nowdate = date( "Ymd", time( ) );
			$picpath = "../pics/".$nowdate;
			@mkdir( $picpath, 0777 );
			$uppath = "shop/pics/".$nowdate;
			
			$arr = newuploadimage( $pic_shape['tmp_name'], $pic_shape['type'], $pic_shape['size'], $uppath, $PhotoBW, $PhotoBH, $PhotoSW, $PhotoSH );
			
			if ( $arr[0] != "err" )
			{
					$src_shape = $arr[3];
			}
			else
			{
					echo $Meta.$arr[1];
					exit( );
			}
			$msql->query( "select shape from {P}_shop_con where id='{$id}'" );
			if ( $msql->next_record( ) )
			{
					$oldsrc_shape = $msql->f( "shape" );
			}
			if ( file_exists( ROOTPATH.$oldsrc_shape ) && $oldsrc_shape != "" && !strstr( $oldsrc_shape, "../" ) )
			{
					unlink( ROOTPATH.$oldsrc_shape );
					$getpic = basename($oldsrc_shape);
					$getpicpath = dirname($oldsrc_shape);
					@unlink( ROOTPATH.$getpicpath."/sp_".$getpic );
			}
			$msql->query( "update {P}_shop_con set shape='{$src_shape}' where id='{$id}'" );
		}
		if ( 0 < $colorpic['size'] )
		{
				$nowdate = date( "Ymd", time( ) );
				$picpath = "../pics/".$nowdate;
				@mkdir( $picpath, 511 );
				$uppath = "shop/pics/".$nowdate;
				$arr = newuploadimage( $colorpic['tmp_name'], $colorpic['type'], $colorpic['size'], $uppath, $PhotoBW, $PhotoBH, $PhotoSW, $PhotoSH );
				if ( $arr[0] != "err" )
				{
						$src_colorpic = $arr[3];
				}
				else
				{
						echo $Meta.$arr[1];
						exit( );
				}
				$msql->query( "select colorpic from {P}_shop_con where id='{$id}'" );
				if ( $msql->next_record( ) )
				{
						$oldsrc_colorpic = $msql->f( "colorpic" );
				}
				if ( file_exists( ROOTPATH.$oldsrc_colorpic ) && $oldsrc_colorpic != "" && !strstr( $oldsrc_colorpic, "../" ) )
				{
						unlink( ROOTPATH.$oldsrc_colorpic );
						$getpic = basename($oldsrc_colorpic);
						$getpicpath = dirname($oldsrc_colorpic);
						@unlink( ROOTPATH.$getpicpath."/sp_".$getpic );
				}
				$msql->query( "update {P}_shop_con set colorpic='{$src_colorpic}' where id='{$id}'" );
		}
		
		$count_pro = count( $spe_selec );
		
		for ( $i = 0;	$i < $count_pro;	$i++	)
		{
				$projid = $spe_selec[$i];
				$projpath .= $projid.":";
		}
		
		for ( $t = 0;	$t < sizeof( $tags );	$t++	)
		{
				if ( $tags[$t] != "" )
				{
						$tagstr .= $tags[$t].",";
				}
		}
                     
        if ($sizeBefore == "") {
            $msql->query( "insert into cpp_shop_product_size set `id`='{$id}',`size`='{$sizechoice}'" );
                    // echo "insert into cpp_shop_product_size set `id`='{$id}',`size`='{$sizechoice}'";
        } else {
            $msql->query( "update cpp_shop_product_size set `size`='{$sizechoice}' where `id`='{$id}'" );
                    // echo "update cpp_shop_product_size set `size`='{$sizechoice}' where `id`='{$id}'";
        }
                     
		$msql->query( "update {P}_shop_con set 
			title='{$title}',
			mbody='{$mbody}',
			memo='{$memo}',
			memotext='{$memotext}',
			after_sales_service='{$afterSalesService}',
			temperature='{$temperature}',
			ambience='{$ambience}',
			catid='{$catid}',
			catpath='{$catpath}',
			subcatid='{$subcatid}',
			subcatpath='{$subcatpath}',
			thirdcatid='{$thirdcatid}',
			thirdcatpath='{$thirdcatpath}',
			fourcatid='{$fourcatid}',
			fourcatpath='{$fourcatpath}',
			uptime='{$uptime}',
			author='{$author}',
			source='{$source}',
			tags='{$tagstr}',
			brandid='{$brandid}',
			bn='{$bn}',
			posbn='{$posbn}',
			price='{$price}',
			price0='{$price0}',
			danwei='{$danwei}',
			weight='{$weight}',
			kucun='{$kucun}',
			cent='{$cent}',
			prop1='{$prop1}',
			prop2='{$prop2}',
			prop3='{$prop3}',
			prop4='{$prop4}',
			prop5='{$prop5}',
			prop6='{$prop6}',
			prop7='{$prop7}',
			prop8='{$prop8}',
			prop9='{$prop9}',
			prop10='{$prop10}',
			prop11='{$prop11}',
			prop12='{$prop12}',
			prop13='{$prop13}',
			prop14='{$prop14}',
			prop15='{$prop15}',
			prop16='{$prop16}',
			prop17='{$prop17}',
			prop18='{$prop18}',
			prop19='{$prop19}',
			prop20='{$prop20}',
			proj='{$projpath}',
			isadd='{$isadd}',
			starttime='{$starttime}',
			endtime='{$endtime}',
			xuhao='{$xuhao}',
			colorname='{$colorname}',
			colorcode='{$colorcode}',
			ifsub='{$ifsub}',
			subid='{$subid}',
			ifpic='{$ifpic}',
			subpicid='{$subpicid}',
			sizeitem='{$sizeitem}',
			sizetype='{$sizetypelist}',
			mainsizetype='{$mainsizetypelist}',
			sizechart='{$sizechart}',
			usepicsize='{$usepicsize}',
			a_body='{$a_body}',
			b_body='{$b_body}',
			c_body='{$c_body}',
			d_body='{$d_body}',
			desciption='{$desciption}'
			where id='{$id}'
		" );
		
		$msql->query( "update {P}_shop_con set starttime='{$starttime}',endtime='{$endtime}' where subpicid='{$id}'" );
		
		if ( $memberprice != "" && is_array( $memberprice ) )
		{
				while ( list( $key, $val ) = each($memberprice) )
				{
						$msql->query( "select id from {P}_shop_memberprice where `membertypeid`='{$key}' and `gid`='{$id}' limit 0,1" );
						if ( $msql->next_record( ) )
						{
								$fsql->query( "update {P}_shop_memberprice set `price`='{$val}' where `membertypeid`='{$key}' and `gid`='{$id}'" );
						}
						else
						{
								$fsql->query( "insert into {P}_shop_memberprice set `price`='{$val}',`membertypeid`='{$key}',`gid`='{$id}'" );
						}
				}
		}
		
		//if($chgallprice == "1"){
			$msql->query( "UPDATE {P}_shop_conspec SET `sprice`='{$price}' WHERE gid='{$id}'" );
		//}
			$msql->query( "SELECT * FROM {P}_shop_conspec WHERE gid='{$id}'" );
			while($msql->next_record( )){
				$thid = $msql->f("id");
				$vals = $msql->f("size");
				if(strtolower($vals) == "one"){
					$vals = "";
				}
				$fsql->query( "update {P}_shop_conspec set `posproid`='{$posbn}{$vals}' where `id`='{$thid}'" );
			}
		
//記錄多國翻譯資料
	$langlist = $_POST['langlist'];
	if($langlist != ""){			
		$stitle = $_POST['stitle'];
		$smemo = $_POST['smemo'];
		$smemotext = $_POST['smemotext'];
		$safterSalesService = $_POST['safterSalesService'];
		$scolorname = $_POST['scolorname'];
		$sbody = $_FILES['sjpg_body'];
		$scanshu = $_FILES['sjpg_canshu'];
		$smbody = $_POST['smbody'];
		$sa_body = $_POST['sa_body'];
		$sb_body = $_POST['sb_body'];
		$sc_body = $_POST['sc_body'];
		$sd_body = $_POST['sd_body'];
		$soldbody = $_POST['oldbody'];
		$soldcanshu = $_POST['oldcanshu'];			
		$sprop1 = $_POST['sprop1'];
		$sprop2 = $_POST['sprop2'];
		$sprop3 = $_POST['sprop3'];
		$sprop4 = $_POST['sprop4'];
		$sprop5 = $_POST['sprop5'];
		$sprop6 = $_POST['sprop6'];
		$sprop7 = $_POST['sprop7'];
		$sprop8 = $_POST['sprop8'];
		$sprop9 = $_POST['sprop9'];
		$sprop10 = $_POST['sprop10'];
		$sprop11 = $_POST['sprop11'];
		$sprop12 = $_POST['sprop12'];
		$sprop13 = $_POST['sprop13'];
		$sprop14 = $_POST['sprop14'];
		$sprop15 = $_POST['sprop15'];
		$sprop16 = $_POST['sprop16'];
		$sprop17 = $_POST['sprop17'];
		$sprop18 = $_POST['sprop18'];
		$sprop19 = $_POST['sprop19'];
		$sprop20 = $_POST['sprop20'];
		
		$lans = explode(",",$langlist);
		foreach($lans AS $ks=>$vs){
			//擷取各語言資料並寫入
			$title = htmlspecialchars(addslashes($stitle[$vs]));
			$memo = htmlspecialchars(addslashes($smemo[$vs]));
			$memotext = htmlspecialchars(addslashes($smemotext[$vs]));
			$afterSalesService = htmlspecialchars(addslashes($safterSalesService[$vs]));
			$colorname = htmlspecialchars(addslashes($scolorname[$vs]));
			$mbody = addslashes($smbody[$vs]);
			$oldbody = $soldbody[$vs];
			$oldcanshu = $soldcanshu[$vs];
			$prop1 = is_array($sprop1[$vs])? implode(",", $sprop1[$vs]):$sprop1[$vs];
			$prop2 = is_array($sprop2[$vs])? implode(",", $sprop2[$vs]):$sprop2[$vs];
			$prop3 = is_array($sprop3[$vs])? implode(",", $sprop3[$vs]):$sprop3[$vs];
			$prop4 = is_array($sprop4[$vs])? implode(",", $sprop4[$vs]):$sprop4[$vs];
			$prop5 = is_array($sprop5[$vs])? implode(",", $sprop5[$vs]):$sprop5[$vs];
			$prop6 = is_array($sprop6[$vs])? implode(",", $sprop6[$vs]):$sprop6[$vs];
			$prop7 = is_array($sprop7[$vs])? implode(",", $sprop7[$vs]):$sprop7[$vs];
			$prop8 = is_array($sprop8[$vs])? implode(",", $sprop8[$vs]):$sprop8[$vs];
			$prop9 = is_array($sprop9[$vs])? implode(",", $sprop9[$vs]):$sprop9[$vs];
			$prop10 = is_array($sprop10[$vs])? implode(",", $sprop10[$vs]):$sprop10[$vs];
			$prop11 = is_array($sprop11[$vs])? implode(",", $sprop11[$vs]):$sprop11[$vs];
			$prop12 = is_array($sprop12[$vs])? implode(",", $sprop12[$vs]):$sprop12[$vs];
			$prop13 = is_array($sprop13[$vs])? implode(",", $sprop13[$vs]):$sprop13[$vs];
			$prop14 = is_array($sprop14[$vs])? implode(",", $sprop14[$vs]):$sprop14[$vs];
			$prop15 = is_array($sprop15[$vs])? implode(",", $sprop15[$vs]):$sprop15[$vs];
			$prop16 = is_array($sprop16[$vs])? implode(",", $sprop16[$vs]):$sprop16[$vs];
			$prop17 = is_array($sprop17[$vs])? implode(",", $sprop17[$vs]):$sprop17[$vs];
			$prop18 = is_array($sprop18[$vs])? implode(",", $sprop18[$vs]):$sprop17[$vs];
			$prop19 = is_array($sprop19[$vs])? implode(",", $sprop19[$vs]):$sprop19[$vs];
			$prop20 = is_array($sprop10[$vs])? implode(",", $sprop10[$vs]):$sprop20[$vs];
			
			$sa_body_a = $sa_body[$vs];
			$sb_body_b = $sb_body[$vs];
			$sc_body_c = htmlspecialchars(addslashes($sc_body[$vs]));
			$sd_body_d = $sd_body[$vs];
			
			
			if ( 0 < $sbody['size'][$vs] )
			{
					$nowdate = date( "Ymd", time( ) );
					$picpath = "../pics/".$nowdate;
					@mkdir( $picpath, 511 );
					$uppath = "shop/pics/".$nowdate;
					$arr = newuploadimage( $sbody['tmp_name'][$vs], $sbody['type'][$vs], $sbody['size'][$vs], $uppath, $PhotoBW, $PhotoBH, $PhotoSW, $PhotoSH);
					if ( $arr[0] != "err" )
					{
							$body = $arr[3];
					}
					else
					{
							err( $arr[1]."[".$vs."]", "", "" );
					}
					if ( file_exists( ROOTPATH.$oldsrc ) && $oldsrc != "" && !strstr( $oldsrc, "../" ) )
					{
							@unlink( ROOTPATH.$oldsrc );
							$getpic = basename($oldsrc);
							$getpicpath = dirname($oldsrc);
							@unlink( ROOTPATH.$getpicpath."/sp_".$getpic );
					}
			}else{
				$body = $oldbody;
			}
			
			if ( 0 < $scanshu['size'][$vs] )
			{
					$nowdate = date( "Ymd", time( ) );
					$picpath = "../pics/".$nowdate;
					@mkdir( $picpath, 511 );
					$uppath = "shop/pics/".$nowdate;
					$arr = newuploadimage( $scanshu['tmp_name'][$vs], $scanshu['type'][$vs], $scanshu['size'][$vs], $uppath, $PhotoBW, $PhotoBH, $PhotoSW, $PhotoSH);
					if ( $arr[0] != "err" )
					{
							$canshu = $arr[3];
					}
					else
					{
							err( $arr[1]."[".$vs."]", "", "" );
					}
					if ( file_exists( ROOTPATH.$oldsrc ) && $oldsrc != "" && !strstr( $oldsrc, "../" ) )
					{
							@unlink( ROOTPATH.$oldsrc );
							$getpic = basename($oldsrc);
							$getpicpath = dirname($oldsrc);
							@unlink( ROOTPATH.$getpicpath."/sp_".$getpic );
					}
			}else{
				$canshu = $oldcanshu;
			}
			
				//getupdate 資料若存在則更新，否則新增
				$msql->getupdate(
					"SELECT id FROM {P}_shop_con_translate WHERE pid='{$id}' AND langcode='{$vs}'",
					"UPDATE {P}_shop_con_translate SET 
					title='{$title}',
					memo='{$memo}',
					memotext='{$memotext}',
					after_sales_service='{$afterSalesService}',
					body='{$body}',
					canshu='{$canshu}',
					colorname='{$colorname}',
					mbody='{$mbody}',
					prop1='{$prop1}',
					prop2='{$prop2}',
					prop3='{$prop3}',
					prop4='{$prop4}',
					prop5='{$prop5}',
					prop6='{$prop6}',
					prop7='{$prop7}',
					prop8='{$prop8}',
					prop9='{$prop9}',
					prop10='{$prop10}',
					prop11='{$prop11}',
					prop12='{$prop12}',
					prop13='{$prop13}',
					prop14='{$prop14}',
					prop15='{$prop15}',
					prop16='{$prop16}',
					prop17='{$prop17}',
					prop18='{$prop18}',
					prop19='{$prop19}',
					prop20='{$prop20}',
					a_body='{$sa_body_a}',
					b_body='{$sb_body_b}',
					c_body='{$sc_body_c}',
					d_body='{$sd_body_d}'
					WHERE pid='{$id}' AND langcode='{$vs}'",
					"INSERT INTO {P}_shop_con_translate SET 
					pid='{$id}',
					langcode='{$vs}',
					title='{$title}',
					memo='{$memo}',
					memotext='{$memotext}',
					after_sales_service='{$afterSalesService}',
					body='{$body}',
					canshu='{$canshu}',
					mbody='{$mbody}',
					prop1='{$prop1}',
					prop2='{$prop2}',
					prop3='{$prop3}',
					prop4='{$prop4}',
					prop5='{$prop5}',
					prop6='{$prop6}',
					prop7='{$prop7}',
					prop8='{$prop8}',
					prop9='{$prop9}',
					prop10='{$prop10}',
					prop11='{$prop11}',
					prop12='{$prop12}',
					prop13='{$prop13}',
					prop14='{$prop14}',
					prop15='{$prop15}',
					prop16='{$prop16}',
					prop17='{$prop17}',
					prop18='{$prop18}',
					prop19='{$prop19}',
					prop20='{$prop20}',
					a_body='{$sa_body_a}',
					b_body='{$sb_body_b}',
					c_body='{$sc_body_c}',
					d_body='{$sd_body_d}'"
				);
		}
	}
	//記錄多國翻譯資料 END
		
		
		echo "OK";
		exit( );
		break;
case "contentmodify" :
		needauth( 320 );
		$shoppagesid = $_POST['shoppagesid'];
		$pic = $_FILES['jpg'];
		$Meta = "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";

		$msql->query( "select * from {P}_shop_config where `variable`='PhotoBWH'" );
		if ( $msql->next_record( ) )
		{
				list($PhotoBW,$PhotoBH) = explode("|",$msql->f( "value" ));
		}
		$msql->query( "select * from {P}_shop_config where `variable`='PhotoSWH'" );
		if ( $msql->next_record( ) )
		{
				list($PhotoSW,$PhotoSH) = explode("|",$msql->f( "value" ));
		}
		if ( 0 < $pic['size'] )
		{
				$nowdate = date( "Ymd", time( ) );
				$picpath = "../pics/".$nowdate;
				@mkdir( $picpath, 511 );
				$uppath = "shop/pics/".$nowdate;
				$arr = newuploadimage( $pic['tmp_name'], $pic['type'], $pic['size'], $uppath, $PhotoBW, $PhotoBH, $PhotoSW, $PhotoSH );
				if ( $arr[0] != "err" )
				{
						$src = $arr[3];
				}
				else
				{
						echo $Meta.$arr[1];
						exit( );
				}
				$msql->query( "select src from {P}_shop_pages where id='{$shoppagesid}'" );
				if ( $msql->next_record( ) )
				{
						$oldsrc = $msql->f( "src" );
				}
				if ( file_exists( ROOTPATH.$oldsrc ) && $oldsrc != "" && !strstr( $oldsrc, "../" ) )
				{
						unlink( ROOTPATH.$oldsrc );
						$getpic = basename($oldsrc);
						$getpicpath = dirname($oldsrc);
						@unlink( ROOTPATH.$getpicpath."/sp_".$getpic );
				}
				$msql->query( "update {P}_shop_pages set src='{$src}' where id='{$shoppagesid}'" );
		}
		
		echo "OK";
		exit( );
		break;
case "shopadd" :
		needauth( 319 );
/*
foreach($_POST AS $key=>$value){
	$arrayvalue = $value;
	if($key == "spec"){
		$arrayvalue = "";
		foreach($value AS $keys=>$values){
			$kk = "";
			foreach($values AS $keyss=>$valuess){
				$kk .= $kk? ",".$valuess:$valuess;
			}
			$arrayvalue .= "(".$keys.")-(".$kk.")";
		}
	}
	$value = $arrayvalue;
	$data .= $data? "^[".$key."]-[".$value."]":"\r\n[".$key."]-[".$value."]";
}

filesave("PAYNOW.php",$data,"ab+");

exit("TEST...");
*/
		
		
		
		$catid = $_POST['catid'];
		$subcatid = $_POST['subcatid'];
		$thirdcatid = $_POST['thirdcatid'];
		$fourcatid = $_POST['fourcatid'];
		$title = addslashes($_POST['title']);
		$author = htmlspecialchars( $_POST['author'] );
		$source = htmlspecialchars( $_POST['source'] );
		$memo = htmlspecialchars( addslashes($_POST['memo']) );
		$memotext = htmlspecialchars( addslashes($_POST['memotext']) );
		$afterSalesService = htmlspecialchars( addslashes($_POST['afterSalesService']) );
		$temperature = $_POST['temperature'];
		$ambience = $_POST['ambience'];
		$brandid = $_POST['brandid'];
		$mbody = addslashes($_POST['mbody']);
		$a_body = $_POST['a_body'];
		$b_body = $_POST['b_body'];
		$c_body = htmlspecialchars( addslashes($_POST['c_body']));
		$d_body = $_POST['d_body'];
		$bn = htmlspecialchars( $_POST['bn'] );
		$posbn = htmlspecialchars( $_POST['posbn'] );
		$price = htmlspecialchars( $_POST['price'] );
		$price0 = htmlspecialchars( $_POST['price0'] );
		$danwei = htmlspecialchars( $_POST['danwei'] );
		$weight = htmlspecialchars( $_POST['weight'] );
		$kucun = htmlspecialchars( $_POST['kucun'] );
		$cent = htmlspecialchars( $_POST['cent'] );
		$prop1 = htmlspecialchars( $_POST['prop1'] );
		$prop2 = htmlspecialchars( $_POST['prop2'] );
		$prop3 = htmlspecialchars( $_POST['prop3'] );
		$prop4 = htmlspecialchars( $_POST['prop4'] );
		$prop5 = htmlspecialchars( $_POST['prop5'] );
		$prop6 = htmlspecialchars( $_POST['prop6'] );
		$prop7 = htmlspecialchars( $_POST['prop7'] );
		$prop8 = htmlspecialchars( $_POST['prop8'] );
		$prop9 = htmlspecialchars( $_POST['prop9'] );
		$prop10 = htmlspecialchars( $_POST['prop10'] );
		$prop11 = htmlspecialchars( $_POST['prop11'] );
		$prop12 = htmlspecialchars( $_POST['prop12'] );
		$prop13 = htmlspecialchars( $_POST['prop13'] );
		$prop14 = htmlspecialchars( $_POST['prop14'] );
		$prop15 = htmlspecialchars( $_POST['prop15'] );
		$prop16 = htmlspecialchars( $_POST['prop16'] );
		$prop17 = htmlspecialchars( $_POST['prop17'] );
		$prop18 = htmlspecialchars( $_POST['prop18'] );
		$prop19 = htmlspecialchars( $_POST['prop19'] );
		$prop20 = htmlspecialchars( $_POST['prop20'] );
		$tags = $_POST['tags'];
		$memberprice = $_POST['memberprice'];
		$pic = $_FILES['jpg'];
		$sizeitem_A = $_POST['sizeitem_A'];
		$sizeitem_B = $_POST['sizeitem_B'];
		$sizeitem_C = $_POST['sizeitem_C'];
		$sizeitem = $sizeitem_A."|".$sizeitem_B."|".$sizeitem_C;
		$mainsizetype = $_POST['mainsizetype'];
		foreach($mainsizetype AS $vvs){
			if($vvs != ""){
				$mainsizetypelist .= $mainsizetypelist? "|".$vvs:$vvs;
			}
		}
		$sizetype = $_POST['sizetype'];
		foreach($sizetype AS $vvs){
			if($vvs != ""){
				$sizetypelist .= $sizetypelist? "|".$vvs:$vvs;
			}
		}
		$sizechart = $_POST['sizechart'];
		$usepicsize = $_POST['usepicsize'];
		/*ADD STR*/
		$colorname = htmlspecialchars( $_POST['colorname'] );
		$colorcode = $_POST['colorcode'];
		$colorpic = $_FILES['colorpic'];
		
		$pic_body = $_FILES['jpg_body'];
		$pic_canshu = $_FILES['jpg_canshu'];
		$pic_shape = $_FILES['jpg_shape'];
		
		$xuhao=$_POST['xuhao'];
		
		$starttime = strtotime(htmlspecialchars( $_POST['starttime'] ));
		$endtime = strtotime(htmlspecialchars( $_POST['endtime'] ));
		
		$ifsub = $_POST['ifsub'];		
		$subid = $ifsub==1? $_POST['subid']:"";	
		
		//配圖
		$ifpic = $_POST['ifpic'];		
		$subpicid = $_POST['subpicid'];	
		if($ifpic){
			$getsubpic = $msql->getone( "select bn,title,colorname,colorcode,starttime,endtime from {P}_shop_con where id='{$subpicid}'" );
			$bn = $getsubpic["bn"];
			$title = $getsubpic["title"];
			$colorname = $getsubpic["colorname"];
			$colorcode = $getsubpic["colorcode"];
			$starttime = $getsubpic["starttime"];
			$endtime = $getsubpic["endtime"];
		}
		
		/*ADD END*/
		/*$body = $_POST['body']; 
		$body = url2path( $body );
		$canshu = $_POST['canshu'];
		$canshu = url2path( $canshu );	
		$spe_selec = $_POST['spe_selec'];		
		$isadd = $_POST['isadd'];*/
		$mbody = $_POST['mbody'];
		$a_body = url2path( $a_body );
		$b_body = url2path( $b_body );
		$c_body = url2path( $c_body );
		$d_body = url2path( $d_body );
		
		//trylimit( "_shop_con", 30, "id" );
		
		$Meta = "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
		if ( !$ifpic =="0" && $bn == "" )
		{
				echo $Meta.$strShopNotice10;
				exit( );
		}
		if ( !$ifpic =="0" && $title == "" )
		{
				echo $Meta.$strShopNotice6;
				exit( );
		}
		if ( !$ifpic =="0" && $price == "" )
		{
				echo $Meta.$strShopNotice11;
				exit( );
		}
		/*if ( $price0 == "" )
		{
				echo $Meta.$strShopNotice12;
				exit( );
		}*/
		if ( !$ifpic =="0" && $price0 == "" )
		{
				$price0 = 0;
		}
		if ( !$ifpic =="0" && $danwei == "" )
		{
				echo $Meta.$strShopNotice13;
				exit( );
		}
		/*if ( $kucun == "" )
		{
				echo $Meta.$strShopNotice14;
				exit( );
		}*/
		if ( !$ifpic =="0" && $weight == "" )
		{
				echo $Meta.$strShopNotice15;
				exit( );
		}
		if ( !$ifpic =="0" && 200 < strlen( $title ) )
		{
				echo $Meta.$strShopNotice7;
				exit( );
		}
		if ( !$ifpic =="0" && 65000 < strlen( $memo ) )
		{
				echo $Meta.$strShopNotice4;
				exit( );
		}
		/*if ( 65000 < strlen( $body ) )
		{
				echo $Meta.$strShopNotice5;
				exit( );
		}*/
		$uptime = time( );
		$dtime = time( );
		
		$msql->query( "select catpath from {P}_shop_cat where catid='{$catid}'" );
		if ( $msql->next_record( ) )
		{
				$catpath = $msql->f( "catpath" );
		}
		$msql->query( "select catpath from {P}_shop_cat where catid='{$subcatid}'" );
		if ( $msql->next_record( ) )
		{
				$subcatpath = $msql->f( "catpath" );
		}
		$msql->query( "select catpath from {P}_shop_cat where catid='{$thirdcatid}'" );
		if ( $msql->next_record( ) )
		{
				$thirdcatpath = $msql->f( "catpath" );
		}
		$msql->query( "select catpath from {P}_shop_cat where catid='{$fourcatid}'" );
		if ( $msql->next_record( ) )
		{
				$fourcatpath = $msql->f( "catpath" );
		}
		
		$msql->query( "select * from {P}_shop_config where `variable`='PhotoBWH'" );
		if ( $msql->next_record( ) )
		{
				list($PhotoBW,$PhotoBH) = explode("|",$msql->f( "value" ));
		}
		$msql->query( "select * from {P}_shop_config where `variable`='PhotoSWH'" );
		if ( $msql->next_record( ) )
		{
				list($PhotoSW,$PhotoSH) = explode("|",$msql->f( "value" ));
		}
		
		/*if ( 0 < $pic['size'] )
		{
				$nowdate = date( "Ymd", time( ) );
				$picpath = "../pics/".$nowdate;
				@mkdir( $picpath, 511 );
				$uppath = "shop/pics/".$nowdate;
				$arr = newuploadimage( $pic['tmp_name'], $pic['type'], $pic['size'], $uppath, $PhotoBW, $PhotoBH, $PhotoSW, $PhotoSH );
				if ( $arr[0] != "err" )
				{
						$src = $arr[3];
				}
				else
				{
						echo $Meta.$arr[1];
						exit( );
				}
		}*/
		if ( 0 < $pic_body['size'] )
		{
				$nowdate = date( "Ymd", time( ) );
				$picpath = "../pics/".$nowdate;
				@mkdir( $picpath, 511 );
				$uppath = "shop/pics/".$nowdate;
				$arr = newuploadimage( $pic_body['tmp_name'], $pic_body['type'], $pic_body['size'], $uppath, $PhotoBW, $PhotoBH, $PhotoSW, $PhotoSH );
				if ( $arr[0] != "err" )
				{
						$body = $arr[3];
				}
				else
				{
						echo $Meta.$arr[1];
						exit( );
				}
		}

		$desciptionFile = $_FILES['desciption'];
		$desciption = $_POST['desciption'];
		foreach($desciption as $key => $val) {
			if(array_key_exists('img', $val)) {
				continue;
			}
			$str = preg_replace_callback('/[^\S\r\n]+/u', function ($matches) {
				return str_repeat('#SPACE#', strlen($matches[0]));
			}, $val);
			$str = nl2br($str);
			
			$str = preg_replace('/\s+/', '', $str);
			$desciption[$key] = $str;
		}

		foreach($desciptionFile['tmp_name'] as $key => $d) {
			$nowdate = date( "Ymd", time( ) );
			$picpath = "../desciption/".$nowdate;
			@mkdir( $picpath, 511 );
			$uppath = "shop/desciption/".$nowdate;
			$arr = newuploadimage( $desciptionFile['tmp_name'][$key], $desciptionFile['type'][$key], $desciptionFile['size'][$key], $uppath, $PhotoBW, $PhotoBH, $PhotoSW, $PhotoSH );
			if ( $arr[0] != "err" )
			{
				
				$desciption[$key] = [
					'img' => $arr[3] 
				];
			}
			else
			{
					echo $Meta.$arr[1];
					exit( );
			}
			
		}
		function customSort($a, $b) {
			return $a - $b;
		}
		
		// 使用自定義排序函數對數組按照鍵名進行排序
		$desciption = array_filter($desciption);
		uksort($desciption, 'customSort');
		$desciption = json_encode($desciption, JSON_UNESCAPED_UNICODE);

		if ( 0 < $pic_canshu['size'] )
		{
				$nowdate = date( "Ymd", time( ) );
				$picpath = "../pics/".$nowdate;
				@mkdir( $picpath, 511 );
				$uppath = "shop/pics/".$nowdate;
				$arr = newuploadimage( $pic_canshu['tmp_name'], $pic_canshu['type'], $pic_canshu['size'], $uppath, $PhotoBW, $PhotoBH, $PhotoSW, $PhotoSH );
				if ( $arr[0] != "err" )
				{
						$canshu = $arr[3];
				}
				else
				{
						echo $Meta.$arr[1];
						exit( );
				}
		}
		if( 0 < $pic_shape['size']) 
		{
			$nowdate = date( "Ymd", time( ) );
			$picpath = "../pics/".$nowdate;
			@mkdir( $picpath, 511 );
			$uppath = "shop/pics/".$nowdate;
			$arr = newuploadimage( $pic_shape['tmp_name'], $pic_shape['type'], $pic_shape['size'], $uppath, $PhotoBW, $PhotoBH, $PhotoSW, $PhotoSH );
			if ( $arr[0] != "err" )
			{
					$shape = $arr[3];
			}
			else
			{
					echo $Meta.$arr[1];
					exit( );
			}
		}
		if ( 0 < $colorpic['size'] )
		{
				$nowdate = date( "Ymd", time( ) );
				$picpath = "../pics/".$nowdate;
				@mkdir( $picpath, 511 );
				$uppath = "shop/pics/".$nowdate;
				$arr = newuploadimage( $colorpic['tmp_name'], $colorpic['type'], $colorpic['size'], $uppath, $PhotoBW, $PhotoBH, $PhotoSW, $PhotoSH );
				if ( $arr[0] != "err" )
				{
						$src_colorpic = $arr[3];
				}
				else
				{
						echo $Meta.$arr[1];
						exit( );
				}
		}
		
		
		
		
		/*$count_pro = count( $spe_selec );
		
		for ( $i = 0;	$i < $count_pro;	$i++	)
		{
				$projid = $spe_selec[$i];
				$projpath .= $projid.":";
		}
		
		for ( $t = 0;	$t < sizeof( $tags );	$t++	)
		{
				if ( $tags[$t] != "" )
				{
						$tagstr .= $tags[$t].",";
				}
		}*/
		
		$msql->query( "insert into {P}_shop_con set
		catid='{$catid}',
		catpath='{$catpath}',
		subcatid='{$subcatid}',
		subcatpath='{$subcatpath}',
		thirdcatid='{$thirdcatid}',
		thirdcatpath='{$thirdcatpath}',
		fourcatid='{$fourcatid}',
		fourcatpath='{$fourcatpath}',
		title='{$title}',
		body='{$body}',
		mbody='{$mbody}',
		canshu='{$canshu}',
		shape='{$shape}',
		dtime='{$dtime}',
		xuhao='{$xuhao}',
		cl='0',
		tj='0',
		iffb='1',
		ifbold='0',
		ifred='0',
		type='gif',
		src='{$src}',
		brandid='{$brandid}',
		bn='{$bn}',
		posbn='{$posbn}',
		price='{$price}',
		price0='{$price0}',
		danwei='{$danwei}',
		weight='{$weight}',
		kucun='{$kucun}',
		cent='{$cent}',
		uptime='{$dtime}',
		author='{$author}',
		source='{$source}',
		memberid='0',
		tags='{$tagstr}',
		secure='0',
		memo='{$memo}',
		memotext='{$memotext}',
		after_sales_service='{$afterSalesService}',
		temperature='{$temperature}',
		ambience='{$ambience}',
		prop1='{$prop1}',
		prop2='{$prop2}',
		prop3='{$prop3}',
		prop4='{$prop4}',
		prop5='{$prop5}',
		prop6='{$prop6}',
		prop7='{$prop7}',
		prop8='{$prop8}',
		prop9='{$prop9}',
		prop10='{$prop10}',
		prop11='{$prop11}',
		prop12='{$prop12}',
		prop13='{$prop13}',
		prop14='{$prop14}',
		prop15='{$prop15}',
		prop16='{$prop16}',
		prop17='{$prop17}',
		prop18='{$prop18}',
		prop19='{$prop19}',
		prop20='{$prop20}',
		proj='{$projpath}',
		isadd='{$isadd}',
		starttime='{$starttime}',
		endtime='{$endtime}',
		colorname='{$colorname}',
		colorcode='{$colorcode}',
		colorpic='{$src_colorpic}',
		ifsub='{$ifsub}',
		subid='{$subid}',
		ifpic='{$ifpic}',
		sizeitem='{$sizeitem}',
		sizetype='{$sizetypelist}',
		mainsizetype='{$mainsizetypelist}',
		subpicid='{$subpicid}',
		sizechart='{$sizechart}',
		usepicsize='{$usepicsize}',
		a_body='{$a_body}',
		b_body='{$b_body}',
		c_body='{$c_body}',
		d_body='{$d_body}',
		desciption='{$desciption}' 
		" );
		$gid = $msql->instid( );
		if ( $memberprice != "" && is_array( $memberprice ) )
		{
				while ( list( $key, $val ) = each($memberprice) )
				{
						$msql->query( "insert into {P}_shop_memberprice set `price`='{$val}',`membertypeid`='{$key}',`gid`='{$gid}'" );
				}
		}
		
		/*ZIP解壓上圖*/
		/*是否為壓縮檔*/
		$az =array("application/zip", "application/x-zip", "application/x-zip-compressed", "application/octet-stream");
		if ( in_array($pic['type'],$az) === FALSE )
		{
				//無法解壓
		}else{
			/*解壓縮*/
			$arr = explode( ".", $pic['name'] );
			$modf = ROOTPATH."shop/admin/upmod/".$arr[0];
			copy($pic['tmp_name'],ROOTPATH."shop/admin/".$pic['name']);
			include(ROOTPATH."includes/pclzip.lib.php");
			$archive = new PclZip(ROOTPATH."shop/admin/".$pic['name']);
			$archive->extract(PCLZIP_OPT_PATH, $modf);
			@unlink(ROOTPATH."shop/admin/".$pic['name']);
			/*偵測.htm*/
			$gethtm = glob($modf."/{*.gif,*.jpg,*.jpeg,*.png}", GLOB_BRACE);
			if ( $gethtm )
			{
					//處理圖片
					foreach( $gethtm AS $vv ){
						$nowdate = date( "Ymd", time( ) );
						$picpath = "../pics/".$nowdate;
						@mkdir( $picpath, 0777 );
						$uppath = "shop/pics/".$nowdate;
						$getc = getimagesize($vv);
						list($gn)=explode(".",basename($vv));
						$gn = substr($gn,-2);
						$gn = str_replace("_","",$gn);
						$arr = newuploadimage( $vv, $getc['mime'], $getc['bits'], $uppath, $PhotoBW, $PhotoBH, $PhotoSW, $PhotoSH );
						if ( $arr[0] != "err" )
						{
								$src = $arr[3];
								if($gn == 1){
									$fsql->query( "UPDATE {P}_shop_con SET src='{$src}' WHERE id='{$gid}'" );
								}else{
									
									$msql->query( "INSERT INTO {P}_shop_pages SET gid='{$gid}',src='{$src}',xuhao='{$gn}'" );
								}
						}
					}
					delfold($modf);
			}
		}
		/*ZIP解壓上圖 END*/
		
		if($ifpic =="0"){
			/*slob add 規格-STR*/
			$spec = $_POST['spec'];
			$tcon = COUNT($spec['name']);

			for($t=0;$t<$tcon;$t++){
				$sp_name = trim($spec[name][$t]);
				$sp_stocks = trim($spec[stocks][$t]);
				$sp_price = $price;
				$colorcode = base_convert(bin2hex(mb_convert_encoding($colorname, 'ucs-4', 'utf-8')), 16, 10);
				/*$sp_stocks>0 &&*/
				if(strtolower($sp_name) == "one"){
					$sp_name = "";
				}
				$msql->query( "INSERT INTO {P}_shop_conspec SET gid='{$gid}',size='{$sp_name}',sprice='{$sp_price}',stocks='{$sp_stocks}',posproid='{$posbn}{$sp_name}'" );
				//庫存另計
				$msql->query( "SELECT SUM(stocks) AS allstocks FROM {P}_shop_conspec WHERE gid='{$gid}'" );
				if($msql->next_record()){
					$kucun = $msql->f(allstocks);
					$fsql->query( "UPDATE {P}_shop_con SET kucun='{$kucun}' WHERE id='{$gid}'" );
				}

			}
			/*slob add 規格-END*/
		}
		echo "OK";
		exit( );
		break;
case "pagedelete" :
		needauth( 320 );
		$delpagesid = $_POST['delpagesid'];
		$nowid = $_POST['nowid'];
		$i = 0;
		$msql->query( "select id from {P}_shop_pages where gid='{$nowid}' order by xuhao" );
		while ( $msql->next_record( ) )
		{
				$id[$i] = $msql->f( "id" );
				if ( $id[$i] == $delpagesid )
				{
						if ( $i == 0 )
						{
								$lastid = 0;
						}
						else
						{
								$lastid = $id[$i - 1];
						}
				}
				$i++;
		}
		if ( $lastid == 0 && 1 < $i )
		{
				$lastid = $id[1];
		}
		$msql->query( "select src from {P}_shop_pages where id='{$delpagesid}'" );
		if ( $msql->next_record( ) )
		{
				$oldsrc = $msql->f( "src" );
				$oldsrcs = dirname($oldsrc)."/sp_".basename($oldsrc);
				if ( file_exists( ROOTPATH.$oldsrc ) && $oldsrc != "" && !strstr( $oldsrc, "../" ) )
				{
						unlink( ROOTPATH.$oldsrc );
						@unlink( ROOTPATH.$oldsrcs );
				}
		}
		$msql->query( "delete from  {P}_shop_pages where id='{$delpagesid}'" );
		echo $lastid;
		exit( );
		break;
case "addzl" :
		needauth( 313 );
		$catid = htmlspecialchars( $_POST['catid'] );
		if ( $catid == "" )
		{
				echo $strZlNTC1;
				exit( );
		}
		$msql->query( "select cat from {P}_shop_cat where catid='{$catid}'" );
		if ( $msql->next_record( ) )
		{
				$cat = $msql->f( "cat" );
				$cat = str_replace( "'", "", $cat );
		}
		else
		{
				echo $strZlNTC2;
				exit( );
		}
		$pagename = "class_".$catid;
		@mkdir( "../class/".$catid, 511 );
		$fd = fopen( "../class/temp.php", "r" );
		$str = fread( $fd, "2000" );
		$str = str_replace( "TEMP", $pagename, $str );
		fclose( $fd );
		$filename = "../class/".$catid."/index.php";
		$fp = fopen( $filename, "w" );
		fwrite( $fp, $str );
		fclose( $fp );
		@chmod( $filename, 493 );
		$msql->query( "update {P}_shop_cat set `ifchannel`='1' where catid='{$catid}'" );
		$msql->query( "select id from {P}_base_pageset where coltype='shop' and pagename='{$pagename}'" );
		if ( $msql->next_record( ) )
		{
		}
		else
		{
				$fsql->query( "insert into {P}_base_pageset set 
			`name`='{$cat}',
			`coltype`='shop',
			`pagename`='{$pagename}',
			`pagetitle`='{$cat}',
			`buildhtml`='index'
			" );
		}
		echo "OK";
		exit( );
		break;
case "delzl" :
		needauth( 313 );
		$catid = htmlspecialchars( $_POST['catid'] );
		if ( $catid == "" )
		{
				echo $strZlNTC1;
				exit( );
		}
		$msql->query( "select catid from {P}_shop_cat where catid='{$catid}'" );
		if ( $msql->next_record( ) )
		{
		}
		else
		{
				echo $strZlNTC2;
				exit( );
		}
		$pagename = "class_".$catid;
		$msql->query( "delete from {P}_base_pageset where coltype='shop' and pagename='{$pagename}'" );
		$msql->query( "delete from {P}_base_plus where plustype='shop' and pluslocat='{$pagename}'" );
		$msql->query( "update {P}_shop_cat set `ifchannel`='0' where catid='{$catid}'" );
		if ( $catid != "" && 1 <= strlen( $catid ) && !strstr( $catid, "." ) && !strstr( $catid, "/" ) )
		{
				delfold( "../class/".$catid );
		}
		echo "OK";
		exit( );
		break;
case "setbrandrelcat" :
		$c = $_POST['c'];
		$brandid = $_POST['brandid'];
		$msql->query( "delete from {P}_shop_brandcat where `brandid`='{$brandid}'" );
		if ( $c != "" && is_array( $c ) )
		{
				while ( list( $key, $val ) = each($c) )
				{
						$msql->query( "insert into {P}_shop_brandcat set `brandid`='{$brandid}',`catid`='{$val}'" );
				}
		}
		echo "OK";
		exit( );
		break;
case "getcatrelbrand" :
		$catid = $_POST['catid'];
		$nowid = $_POST['nowid'];
		if ( $nowid != "" && $nowid != "0" )
		{
				$msql->query( "select brandid from {P}_shop_con where `id`='{$nowid}' limit 0,1" );
				if ( $msql->next_record( ) )
				{
						$shopbrandid = $msql->f( "brandid" );
				}
		}
		$str = "";
		$msql->query( "select * from {P}_shop_brandcat where `catid`='{$catid}'" );
		while ( $msql->next_record( ) )
		{
				$brandid = $msql->f( "brandid" );
				$fsql->query( "select brand from {P}_shop_brand where `id`='{$brandid}' limit 0,1" );
				if ( $fsql->next_record( ) )
				{
						$brand = $fsql->f( "brand" );
						if ( $shopbrandid == $brandid )
						{
								$str .= "<option value='".$brandid."' selected>".$brand."</option>";
						}
						else
						{
								$str .= "<option value='".$brandid."'>".$brand."</option>";
						}
				}
		}
		echo $str;
		exit( );
		break;

case "orderthis" :
		$orderid = $_POST['orderid'];
		exit("無法取消訂單，請重新訂購");
		//複製訂單
		/*$msql->query( "INSERT INTO {P}_shop_order(`OrderNo`, `memberid`, `user`, `name`, `tel`, `mobi`, `qq`, `email`, `s_name`, `s_sex`, `s_tel`, `s_addr`, `country`, `addrnote`, `s_postcode`, `s_mobi`, `s_qq`, `s_time`, `goodstotal`, `yunzoneid`, `yunid`, `yuntype`, `yunifbao`, `yunbaofei`, `yunfei`, `totaloof`, `totalcent`, `totalweight`, `payid`, `paytype`, `paytotal`, `iflook`, `ifyun`, `ifpay`, `ifok`, `iftui`, `ifreceipt`, `ip`, `dtime`, `paytime`, `yuntime`, `bz`, `items`, `invoicename`, `invoicenumber`, `disaccount`, `promoprice`, `promocode`, `sendtypeno`, `card_mail`, `promolog`, `contribute`, `integrated`, `card_mail_admin`, `pricesymbol`, `multiprice`, `multiyunfei`, `itemtui`, `tuitime`, `CreateInvoice`, `tuiinfo`, `tuibank`, `yun_mail`, `clicktuipos`, `uptime`, `itemtuipay`, `tuipay`, `tuiok`, `ordertuitime`, `sizerecord`, `buysource`, `source`, `sales`, `shipinfo`) SELECT `OrderNo`, `memberid`, `user`, `name`, `tel`, `mobi`, `qq`, `email`, `s_name`, `s_sex`, `s_tel`, `s_addr`, `country`, `addrnote`, `s_postcode`, `s_mobi`, `s_qq`, `s_time`, `goodstotal`, `yunzoneid`, `yunid`, `yuntype`, `yunifbao`, `yunbaofei`, `yunfei`, `totaloof`, `totalcent`, `totalweight`, `payid`, `paytype`, `paytotal`, `iflook`, `ifyun`, `ifpay`, `ifok`, `iftui`, `ifreceipt`, `ip`, `dtime`, `paytime`, `yuntime`, `bz`, `items`, `invoicename`, `invoicenumber`, `disaccount`, `promoprice`, `promocode`, `sendtypeno`, `card_mail`, `promolog`, `contribute`, `integrated`, `card_mail_admin`, `pricesymbol`, `multiprice`, `multiyunfei`, `itemtui`, `tuitime`, `CreateInvoice`, `tuiinfo`, `tuibank`, `yun_mail`, `clicktuipos`, `uptime`, `itemtuipay`, `tuipay`, `tuiok`, `ordertuitime`, `sizerecord`, `buysource`, `source`, `sales`, `shipinfo` FROM {P}_shop_order WHERE `orderid`='{$orderid}'" );
		//取得新訂單ID
		$neworderid = $msql->instid();
		$dtime = time();
		//訂單規則民國3碼+月2碼+日期2碼+序號5碼
		$newOrderNo =(date("Y",$dtime)-1911).date("m",$dtime).date("d",$dtime).str_pad($neworderid,5,'0',STR_PAD_LEFT);
		$fsql->query( "UPDATE {P}_shop_order SET OrderNo='$newOrderNo',iftui='0',dtime='$dtime',itemtui='0',tuitime='0',itemtuipay='0',tuipay='0' where `orderid`='{$neworderid}'" );
		
		$msql->query( "SELECT * FROM {P}_shop_orderitems WHERE `orderid`='{$orderid}'" );
		while($msql->next_record()){
			$tid= $msql->f("id");
			$bn= $msql->f("bn");
			$title= $msql->f("goods");
			$colorname= $msql->f("colorname");
			list($buysize)= explode("^",$msql->f("fz"));
			$acc= $msql->f("nums");
			$items .= $bn." ".$title."(".$colorname."/".$buysize."/".$acc.") ";
			//複製訂單商品
			$fsql->query( "INSERT INTO {P}_shop_orderitems(`memberid`, `orderid`, `gid`, `subpicid`, `bn`, `goods`, `colorname`, `price`, `weight`, `nums`, `danwei`, `jine`, `cent`, `ifyun`, `iftui`, `ifpay`, `dtime`, `yuntime`, `msg`, `fz`, `multiprice`, `multijine`, `pricesymbol`, `itemtui`, `itemtuinums`, `tuitime`, `lantype`) SELECT `memberid`, `orderid`, `gid`, `subpicid`, `bn`, `goods`, `colorname`, `price`, `weight`, `nums`, `danwei`, `jine`, `cent`, `ifyun`, `iftui`, `ifpay`, `dtime`, `yuntime`, `msg`, `fz`, `multiprice`, `multijine`, `pricesymbol`, `itemtui`, `itemtuinums`, `tuitime`, `lantype` FROM {P}_shop_orderitems WHERE `id`='{$tid}'" );
			//取得新訂單ID
			$newitemid = $fsql->instid();
			$fsql->query( "UPDATE {P}_shop_orderitems SET `orderid`='$neworderid',iftui='0',itemtui='0',tuitime='0' where `id`='{$newitemid}'" );
		}
		$fsql->query( "UPDATE {P}_shop_order SET items='{$items}' where `orderid`='{$neworderid}'" );*/
		
		/*減庫存*/
		/*$fsql->query( "select * from {P}_shop_orderitems where orderid='{$orderid}'" );
		while ( $fsql->next_record( ) )
		{
			$gid = $fsql->f("gid");
			$acc = $fsql->f("nums");
			list($buysize, $buyprice, $buyspecid) = explode("^",$fsql->f("fz"));
			$tsql->query( "UPDATE {P}_shop_con SET kucun=kucun-{$acc} WHERE id='{$gid}'" );
		}*/
		/*減庫存*/
		
		echo "OK";
		exit( );
		break;
case "ordertui" :
		needauth( 328 );
		trylimit( "_shop_order", 50, "orderid" );
		$orderid = $_POST['orderid'];
		$postui =  $_POST['postui'];
		$msql->query( "select * from {P}_shop_order where orderid='{$orderid}'" );
		if ( $msql->next_record( ) )
		{
				$ifpay = $msql->f( "ifpay" );
				$ifyun = $msql->f( "ifyun" );
				$ifok = $msql->f( "ifok" );
				$iftui = $msql->f( "iftui" );
				$OrderNo = $msql->f( "OrderNo" );
				if ( $ifok == "1" )
				{
						echo "1003";
						exit( );
				}
				if ( $ifpay == "1" )
				{
						echo "1001";
						exit( );
				}
				if ( $ifyun == "1" )
				{
						echo "1002";
						exit( );
				}
				if ( $iftui == "1" )
				{
						echo "OK";
						exit( );
				}
				$fsql->query( "select id from {P}_shop_orderitems where orderid='{$orderid}' and ifyun='1'" );
				if ( $fsql->next_record( ) )
				{
						echo "1004";
						exit( );
				}
				$fsql->query( "update {P}_shop_order set iftui='1' where orderid='{$orderid}'" );
				
				/*退還餘額付款20131004*/
				$fsql->query( "select memberid,disaccount,source from {P}_shop_order where orderid='{$orderid}' and disaccount>0" );
				if ( $fsql->next_record( ) )
				{
					$memberid = $fsql->f("memberid");
					$disaccount = $fsql->f("disaccount");
					$source = $fsql->f( "source" );
					$onlineshop = substr($source,0,1);
					if($onlineshop ==1 || $onlineshop ==2){
						
					}else{
						$tsql->query( "UPDATE {P}_member SET account=account+{$disaccount} WHERE memberid='{$memberid}'" );
					}
				}
				/*退還餘額付款*/
	
				/*加回庫存20130831*/
				$fsql->query( "select * from {P}_shop_orderitems where orderid='{$orderid}'" );
				while ( $fsql->next_record( ) )
				{
					$gid = $fsql->f("gid");
					$acc = $fsql->f("nums");
					//$acc = $fsql->f("itemtuinums");
					list($buysize, $buyprice, $buyspecid) = explode("^",$fsql->f("fz"));
					$tsql->query( "UPDATE {P}_shop_con SET kucun=kucun+{$acc} WHERE id='{$gid}'" );
					if($buyspecid){
						$tsql->query( "UPDATE {P}_shop_conspec SET stocks=stocks+{$acc} WHERE id='{$buyspecid}'" );
					}
					
				}
				/*加回庫存 END*/
				$fsql->query( "update {P}_shop_orderitems set iftui='1' where orderid='{$orderid}'" );

				include_once( ROOTPATH."costomer.php");
				$data['status'] = "2";
				$data['orderid'] = $orderid;
				$data['oper'] = $_COOKIE["SYSNAME"];
				upd_order_complete(http_build_query($data));
				
				echo "OK";
				exit( );
		}
		else
		{
				echo "1000";
				exit( );
		}
		echo "OK";
		exit( );
		break;
//開立電子發票！！！		
case "orderre" :
		needauth( 325 );
		$orderid = $_POST['orderid']? $_POST['orderid']:$_GET['orderid'];
		
		if($GLOBALS['GLOBALS']['CONF']['re_check']=="1"){
			exit("已關閉鯨躍發票功能。");
		}
		
		include_once( ROOTPATH."costomer.php");
		$data['status'] = "3";
		$data['orderid'] = $orderid;
		$data['oper'] = $_COOKIE["SYSNAME"];
		upd_order(http_build_query($data));
		$InvoiceDate = time();
		/*發票明細使用*/
		$fsql->query( "select * from {P}_shop_orderitems where orderid='$orderid' and itemtui='0' and iftui='0'" );
		while($fsql->next_record()){
			
			$orderbn = $fsql->f("bn");
			$goods = $fsql->f("goods");
			$colorname = $fsql->f("colorname");
			list($fz) = explode("^",$fsql->f("fz"));
			$fzs = $colorname.$fz;			
			$item_nums = $fsql->f("nums");
			$item_paytotal = (INT)$fsql->f("price");
			
				$productlist.= '<ProductItem>';
					 $productlist.= '<ProductionCode>'.$orderbn.'</ProductionCode>'; //商品編號
					 $productlist.= '<Description>'.$goods.'/'.$fzs.'</Description>'; //品名
					 $productlist.= '<Quantity>'.$item_nums.'</Quantity>';//數量
					 $productlist.= '<Unit>套</Unit>';//單位
					 $productlist.= '<UnitPrice>'.$item_paytotal.'</UnitPrice>';//單價
					 $productlist.= '<DType></DType>';//稅別 TaxType為9需要註記 TZ：零稅率 TN：免稅商品 空白：應稅
				 $productlist.= '</ProductItem>';
		}
		
		//提取訂單
		$msql->query( "select * from {P}_shop_order where orderid='{$orderid}'" );
		if ( $msql->next_record( ) )
		{
				$integrated = $msql->f("integrated");
				$contribute = $msql->f("contribute");
				$invoicename = $msql->f("invoicename");
				$invoicenumber = $msql->f("invoicenumber");
				$OrderNo = $msql->f("OrderNo");
				$dtime = $msql->f("dtime");
				$name = $msql->f("name");
				$mobi = $msql->f("mobi");
				$user = $msql->f("user");
				$email = $msql->f("email")? $msql->f("email"):$user;
				$memberid = $msql->f("memberid");
				$payid = $msql->f("payid");
				$addr = $msql->f("s_addr");
				$promoprice = $msql->f("promoprice");
				
				$yunfei = (INT)$msql->f("yunfei");
		}
		
/*電子發票 STR*/
if($GLOBALS['GLOBALS']['CONF'][re_check] == 0){
	$DonateMark = "2"; //預設會員
	//是否有載具
	if($integrated != ""){
		list($seta, $setb, $setc) = explode("|",$integrated);
		$DonateMark = "0";
	}elseif($contribute !=""){
		//愛心碼
		list($cfa, $cfb) = explode("|",$contribute);
		$DonateMark = "1";
	}
	//付款方式
	if($payid == "1"){
		$payment = "3";
	}else{
		$payment = "5";
	}
	
	// 要訪問的 WebService 路徑
	###$NusoapWSDL="http://invoice.cetustek.com.tw/InvoiceB2C/InvoiceAPI?wsdl";//測試路徑###
	$NusoapWSDL="https://www.ei.com.tw/InvoiceB2C/InvoiceAPI?wsdl";
	// 生成用戶端物件
	libxml_disable_entity_loader(false);
	$client = new SoapClient($NusoapWSDL, array('encoding'=>'UTF-8'));
	
	$OrderId = $OrderNo;//自訂訂單編號(必填)
	$OrderDate = date("Y/m/d");//訂單日期 yyyy/MM/dd(必填)
	$BuyerIdentifier = $invoicenumber;//買家統編
	$BuyerName = $invoicename!=""? $invoicename:$name;//買家姓名(必填)
	$BuyerAddress = $DonateMark=="2"? trim($addr):"";//買家地址(收取紙本發票則為必填)
	$BuyerPersonInCharge = trim($name);//買家負責人姓名
	$BuyerTelephoneNumber = trim($mobi);//電話號碼
	$BuyerFacsimileNumber = "";//傳真號碼
	$BuyerEmailAddress = trim($email);//買家電子郵件(必填)
	$BuyerCustomerNumber = trim($memberid);//客戶編號
	$DonateMark = $DonateMark;//捐贈註記 0：載具 1：捐贈 2：紙本(必填)
	$InvoiceType = "07";//發票種類 07：一般稅額 08：特種稅額
	$CarrierType = $setc;//載具類別
	$CarrierId1 = $setb;//載具顯碼
	$CarrierId2 = $setb;//載具隱碼
	$NPOBAN = $cfb;//社福愛心碼
	$TaxType = "1";//稅別 1：應稅 2：零稅率（非經海關出口） 3：免稅 4：應稅（特種稅）5：零稅率（經海關出口）9：混和（收銀機類型使用）
	$TaxRate = "0.05";//預設 0.05 TaxType：4則必填
	$PayWay = $payment;//付款方式 1：現金 2：ATM 3：信用卡 4：超商代收 5：其他
	$Remark = "";//備註
	$MailSend = "0";//發送通知 預設0：加值中心發送 1：自行處理(不發送) 

  	$xml_str = '<?xml version="1.0" encoding="UTF-8"?>';
	$xml_str .= '<Invoice XSDVersion="2.8">';
	$xml_str .= '<OrderId>'.$OrderId.'</OrderId>'; 
	$xml_str .= '<OrderDate>'.$OrderDate.'</OrderDate>';
	$xml_str .= '<BuyerIdentifier>'.$BuyerIdentifier.'</BuyerIdentifier>';
	$xml_str .= '<BuyerName>'.$BuyerName.'</BuyerName>';
	$xml_str .= '<BuyerAddress>'.$BuyerAddress.'</BuyerAddress>'; 
	$xml_str .= '<BuyerPersonInCharge>'.$BuyerPersonInCharge.'</BuyerPersonInCharge>';
	$xml_str .= '<BuyerTelephoneNumber>'.$BuyerTelephoneNumber.'</BuyerTelephoneNumber>';
	$xml_str .= '<BuyerFacsimileNumber>'.$BuyerFacsimileNumber.'</BuyerFacsimileNumber>'; 
	$xml_str .= '<BuyerEmailAddress>'.$BuyerEmailAddress.'</BuyerEmailAddress>';
	$xml_str .= '<BuyerCustomerNumber>'.$BuyerCustomerNumber.'</BuyerCustomerNumber>';
	$xml_str .= '<DonateMark>'.$DonateMark.'</DonateMark>';
	$xml_str .= '<InvoiceType>'.$InvoiceType.'</InvoiceType>'; 
	$xml_str .= '<CarrierType>'.$CarrierType.'</CarrierType>'; 
	$xml_str .= '<CarrierId1>'.$CarrierId1.'</CarrierId1>'; 
	$xml_str .= '<CarrierId2>'.$CarrierId2.'</CarrierId2>'; 
	$xml_str .= '<NPOBAN>'.$NPOBAN.'</NPOBAN>'; 
	$xml_str .= '<TaxType>'.$TaxType.'</TaxType>'; 
	$xml_str .= '<TaxRate>'.$TaxRate.'</TaxRate>'; 
	$xml_str .= '<PayWay>'.$PayWay.'</PayWay>';
	$xml_str .= '<Remark>'.$Remark.'</Remark>';
	$xml_str .= '<MailSend>'.$MailSend.'</MailSend>';
	
	//明細
	$xml_str .= '<Details>';
	
	if($yunfei>0){
		 $productlist.= '<ProductItem>';
			 $productlist.= '<ProductionCode>Shipping Fee</ProductionCode>'; //商品編號
			 $productlist.= '<Description>運費</Description>'; //品名
			 $productlist.= '<Quantity>1</Quantity>';//數量
			 $productlist.= '<Unit>次</Unit>';//單位
			 $productlist.= '<UnitPrice>'.$yunfei.'</UnitPrice>';//單價
			 $productlist.= '<DType></DType>';//稅別 TaxType為9需要註記 TZ：零稅率 TN：免稅商品 空白：應稅
		 $productlist.= '</ProductItem>';
	}
	if($promoprice>0){
		 $productlist.= '<ProductItem>';
			 $productlist.= '<ProductionCode>Promotion</ProductionCode>'; //商品編號
			 $productlist.= '<Description>促銷折價</Description>'; //品名
			 $productlist.= '<Quantity>1</Quantity>';//數量
			 $productlist.= '<Unit>次</Unit>';//單位
			 $productlist.= '<UnitPrice>-'.$promoprice.'</UnitPrice>';//單價
			 $productlist.= '<DType></DType>';//稅別 TaxType為9需要註記 TZ：零稅率 TN：免稅商品 空白：應稅
		 $productlist.= '</ProductItem>';
	}
		 	 
	$xml_str .= $productlist;
		 
	$xml_str .=  '</Details>';
	$xml_str .=  '</Invoice>';
	// xml
	$tax_val="1";//$_GET['RBt_Tax'];   //含稅或未稅
	$rentid = "24311840";    //統一編號
		
	//過濾引號以免XML錯誤
	$xml_str=mb_ereg_replace('\"','"',$xml_str);
	$xml_str=filterXmlStr($xml_str);
	
	//$xml_str="";
	
	// 設置參數
	$param = array('invoicexml'=>$xml_str,'hastax'=>$tax_val,'rentid'=>$rentid);	

	//呼叫存放開立發票  成功-->取回發票號碼十碼   失敗-->取回錯誤代碼(錯誤代碼說明請參考API規格設計文件)
	/*$result = $client->__soapCall('CreateInvoiceV3', array($param));
		$errorcode = $result->return;*/
	
	//exit(var_dump($xml_str));
	//$param = "";

		
	if(	$result = $client->__soapCall('CreateInvoiceV3', array($param))){
		
		$errorcode = $result->return;
		$strlen = mb_strlen($errorcode);
		//記載發票號碼 或 錯誤碼
		if($strlen == "10"){
			$msql->query("UPDATE {P}_shop_order SET CreateInvoice='{$errorcode}',ifreceipt='1',InvoiceDate='{$InvoiceDate}' WHERE orderid='{$orderid}'");
			echo "OK_".$errorcode;
			exit( );
		}else{
			
			$errorcodes = substr($errorcode,0,3);
			
			switch ( $errorcodes )
				{
				case "M0" :
						$errorcode = "( ".$errorcode." )XML格式錯誤";
						break;
				case "M1" :
						$errorcode = "( ".$errorcode." )XML格式錯誤";
						break;
				case "D0" :
						$errorcode = "( ".$errorcode." )沒有產品明細";
						break;
				case "D0_" :
						$errorcode = "( ".$errorcode." )產品編號格式錯誤";
						break;
				case "D1_" :
						$errorcode = "( ".$errorcode." )品名未填或格式錯誤";
						break;
				case "D2_" :
						$errorcode = "( ".$errorcode." )數量未填或格式錯誤";
						break;
				case "D3_" :
						$errorcode = "( ".$errorcode." )單價未填或格式錯誤";
						break;
				case "D4_" :
						$errorcode = "( ".$errorcode." )單位格式錯誤";
						break;
				case "S1" :
						$errorcode = "( ".$errorcode." )資料庫發生錯誤";
						break;
				case "S2" :
						$errorcode = "( ".$errorcode." )訂單日期超過開立日期";
						break;
				case "S3" :
						$errorcode = "( ".$errorcode." )未在申報期內";
						break;
				case "S4" :
						$errorcode = "( ".$errorcode." )未取得發票號碼";
						break;
				case "S5" :
						$errorcode = "( ".$errorcode." )發票號碼已使用完畢";
						break;
				case "S6" :
						$errorcode = "( ".$errorcode." )超過租賃張數限制";
						break;
				case "S7" :
						$errorcode = "( ".$errorcode." )訂單號碼已存在，若需重開，請先作廢原發票號碼才可再次傳入";
						break;
				case "S8" :
						$errorcode = "( ".$errorcode." )開立的總金額為負值";
						break;
				default :
						$errorcode = "( ".$errorcode." )";
				}
			
			$msql->query("UPDATE {P}_shop_order SET CreateInvoice='{$errorcode}' WHERE orderid='{$orderid}'");
			exit("發票開立錯誤: 錯誤碼".$errorcode);
		}
	}
}
/*電子發票 END*/
		
		/*$msql->query( "select * from {P}_shop_order where orderid='{$orderid}'" );
		if ( $msql->next_record( ) )
		{
				$ifreceipt = $msql->f( "ifreceipt" );
				switch ( $ifreceipt )
				{
				case "0" :
						$res = 1;
						break;
				case "1" :
						$res = 0;
						break;
				}
				$fsql->query( "update {P}_shop_order set ifreceipt='$res' where orderid='{$orderid}'" );
				echo "OK_".$res;
				exit( );
		}
		else
		{
				echo "1000";
				exit( );
		}*/
		
		
		echo "OK";
		exit( );
		break;
//作廢電子發票！！！		
case "orderretui" :
		needauth( 325 );

		if($GLOBALS['GLOBALS']['CONF']['re_check']=="1"){
			exit("已關閉鯨躍發票功能。");
		}

		$orderid = $_POST['orderid'];
		include_once( ROOTPATH."costomer.php");
		$data['status'] = "4";
		$data['orderid'] = $orderid;
		$data['oper'] = $_COOKIE["SYSNAME"];
		upd_order(http_build_query($data));
		//提取訂單
		$msql->query( "select * from {P}_shop_order where orderid='{$orderid}'" );
		if ( $msql->next_record( ) )
		{
				$dtime = $msql->f("dtime");
				$year = date("Y", $dtime);
				$CreateInvoice = substr($msql->f("CreateInvoice"),0,10);
		}
		
/*電子發票 STR*/
	
	// 要訪問的 WebService 路徑
	###$NusoapWSDL="http://invoice.cetustek.com.tw/InvoiceB2C/InvoiceAPI?wsdl";//測試路徑###
	$NusoapWSDL="https://www.ei.com.tw/InvoiceB2C/InvoiceAPI?wsdl";
	// 生成用戶端物件
	$client = new SoapClient($NusoapWSDL, array('encoding'=>'UTF-8'));
	
		 /*<?xml version="1.0" encoding="UTF-8"?>
<Invoice XSDVersion="2.8">
<InvoiceNumber>A44556632</InvoiceNumber>
<InvoiceYear>2011</InvoiceYear>
<ReturnTaxDocumentNumber>65327645</ReturnTaxDocumentNumber>
<Remark>退貨</Remark>
</Invoice>*/
  	$xml_str = '<?xml version="1.0" encoding="UTF-8" ?>';
	$xml_str .= '<Invoice XSDVersion = "2.8">';
	$xml_str .= '<InvoiceNumber>'.$CreateInvoice.'</InvoiceNumber>'; //發票號碼
	$xml_str .= '<InvoiceYear>'.$year.'</InvoiceYear>';//發票年份
	$xml_str .= '<ReturnTaxDocumentNumber></ReturnTaxDocumentNumber>'; //專案作廢核准文號
	$xml_str .= '<Remark>退貨</Remark>'; //作廢原因
	$xml_str .= '</Invoice>'; 
	
	
	// xml
	$tax_val="1";//$_GET['RBt_Tax'];   //含稅或未稅
	$rentid = "24311840";    //統一編號
	//過濾引號以免XML錯誤
	$xml_str=str_replace('\"','"',$xml_str);
	
	// 設置參數
	$param = array('invoicexml'=>$xml_str,'rentid'=>$rentid);
	

	if(	$result = $client->__soapCall('CancelInvoiceNoCheck', array($param))){
		$errorcode = $result->return;
		
		//記載回覆碼
		if($errorcode == "C0"){
			$errstr = $CreateInvoice."(成功作廢發票)";
			$msql->query("UPDATE {P}_shop_order SET CreateInvoice='{$errstr}',ifreceipt='0' WHERE orderid='{$orderid}'");
			echo "OK_成功作廢發票";
			exit( );
		}else{
			$errstr = $CreateInvoice."(錯誤：".$errorcode.")";
			$msql->query("UPDATE {P}_shop_order SET CreateInvoice='{$errstr}',ifreceipt='1' WHERE orderid='{$orderid}'");
			exit("作廢發票錯誤: 錯誤碼( ".$errorcode." )");
		}
	}

/*電子發票 END*/
		echo "OK";
		exit( );
		break;
case "orderlook" :
		needauth( 325 );
		$orderid = $_POST['orderid'];
		$logname = $_COOKIE["SYSNAME"];
		$now = time();
		$msql->query( "select * from {P}_shop_order where orderid='{$orderid}'" );
		if ( $msql->next_record( ) )
		{
				$iflook = $msql->f( "iflook" );
				switch ( $iflook )
				{
				case "0" :
						$look = 1;
						break;
				case "1" :
						$look = 0;
						break;
				}
				if($look==1){
					$fsql->query( "update {P}_shop_order set iflook='$look',looklog='$logname',looktime='$now' where orderid='{$orderid}'" );
				}else{
					$fsql->query( "update {P}_shop_order set iflook='$look',looklog='$logname',looktime='0' where orderid='{$orderid}'" );
				}
				echo "OK_".$look;
				exit( );
		}
		else
		{
				echo "1000";
				exit( );
		}
		echo "OK";
		exit( );
		break;
case "orderok" :
		needauth( 325 );
		trylimit( "_shop_order", 50, "orderid" );
		$orderid = $_POST['orderid'];
		$msql->query( "select * from {P}_shop_order where orderid='{$orderid}'" );
		if ( $msql->next_record( ) )
		{
				$ifpay = $msql->f( "ifpay" );
				$ifyun = $msql->f( "ifyun" );
				$ifok = $msql->f( "ifok" );
				$iftui = $msql->f( "iftui" );
				$yuntime = $msql->f( "yuntime" );
				if ( $ifpay != "1" )
				{
						echo "1001";
						exit( );
				}
				if ( $ifyun != "1" )
				{
						echo "1002";
						exit( );
				}
				if ( $iftui == "1" )
				{
						echo "1003";
						exit( );
				}
				if ( $ifok == "1" )
				{
						//echo "OK";
						$fsql->query( "update {P}_shop_order set ifok='0' where orderid='{$orderid}'" );
						echo "1006";
						include_once( ROOTPATH."costomer.php");
						$data['status'] = "2";
						$data['orderid'] = $orderid;
						$data['oper'] = $_COOKIE["SYSNAME"];
						upd_order_complete(http_build_query($data));
						exit( );
				}
				$fsql->query( "select id from {P}_shop_orderitems where orderid='{$orderid}' and ifyun='0'" );
				if ( $fsql->next_record( ) )
				{
						echo "1004";
						exit( );
				}
				// if ( (time() - ($yuntime+86400))-(86400*7) < 0 )
				// {
				// 		echo "1005";
				// 		exit( );
				// }
				include_once( ROOTPATH."costomer.php");
				$data['status'] = "1";
				$data['orderid'] = $orderid;
				$data['oper'] = $_COOKIE["SYSNAME"];
				$dataAlert = upd_order_complete(http_build_query($data));
				echo $dataAlert;
				exit( );
				
				$fsql->query( "update {P}_shop_order set ifok='1' where orderid='{$orderid}'" );
				echo "OK";
				exit( );
		}
		else
		{
				echo "1000";
				exit( );
		}
		echo "OK";
		exit( );
		break;
case "orderitemyun" :
		needauth( 324 );
		trylimit( "_shop_order", 50, "orderid" );
		$itemid = $_POST['itemid'];
		$now = time( );
		$msql->query( "select * from {P}_shop_orderitems where id='{$itemid}'" );
		if ( $msql->next_record( ) )
		{
				$orderid = $msql->f( "orderid" );
				$gid = $msql->f( "gid" );
				$nums = $msql->f( "nums" );
				$itemyun = $msql->f( "ifyun" );
				$fz = $msql->f( "fz" );
				
				if ( $itemyun == "1" )
				{
						echo "1005";
						exit( );
				}
		}
		else
		{
				echo "1000";
				exit( );
		}
		$msql->query( "select * from {P}_shop_order where orderid='{$orderid}'" );
		if ( $msql->next_record( ) )
		{
				$ifpay = $msql->f( "ifpay" );
				$ifyun = $msql->f( "ifyun" );
				$ifok = $msql->f( "ifok" );
				$iftui = $msql->f( "iftui" );
				$paytotal = $msql->f( "paytotal" );
				$paytype = $msql->f( "paytype" );
				$OrderNo = $msql->f( "OrderNo" );
				//$OrderNo = $orderid+100000;
				$memname = $msql->f( "name" );
				$membermail = $msql->f( "email" );
				
				$yun_mail = $msql->f( "yun_mail" );
				
				list($yuntype, $yun_no) = explode("|",$msql->f( "sendtypeno" ));
				if ( $iftui == "1" )
				{
						echo "1001";
						exit( );
				}
				if ( $ifok == "1" )
				{
						echo "1002";
						exit( );
				}
		}
		else
		{
				echo "1003";
				exit( );
		}
		//list($buysize, $buyprice, $buyspecid) = explode("^",$fz);
		/*if($buyspecid){
			
			$msql->query( "select stocks from {P}_shop_conspec where id='{$buyspecid}'" );
			if ( $msql->next_record( ) )
			{
				$kucun = $msql->f( "stocks" );
			}
		}else{
			$msql->query( "select kucun from {P}_shop_con where id='{$gid}'" );
			if ( $msql->next_record( ) )
			{
				$kucun = $msql->f( "kucun" );
			}
		}
		if ( $kucun < $nums )
		{
				echo "1004";
				exit( );
		}*/
				$tsql->query( "SELECT COUNT(*) as u FROM {P}_shop_orderitems where ifyun='0' AND orderid='{$orderid}'" );
				if ( $tsql->next_record() )	$noyun = $tsql->f("u");
		$fsql->query( "update {P}_shop_orderitems set ifyun='1',yuntime='{$now}' where id='{$itemid}'" );
		//$fsql->query( "update {P}_shop_con set kucun=kucun-{$nums},salenums=salenums+{$nums} where id='{$gid}'" );
		/*庫存改由訂單成立時扣除20130831*/
		$fsql->query( "update {P}_shop_con set salenums=salenums+{$nums} where id='{$gid}'" );
		/*if($buyspecid){
			$fsql->query( "update {P}_shop_conspec set stocks=stocks-{$nums} where id='{$buyspecid}'" );
		}*/
		$msql->query( "select id from {P}_shop_orderitems where orderid='{$orderid}' and ifyun!='1'" );
		if ( $msql->next_record( ) )
		{
		}
		else
		{
			$fsql->query( "update {P}_shop_order set ifyun='1',yuntime='{$now}' where orderid='{$orderid}'" );
			
			include_once( ROOTPATH."costomer.php");
			$data['status'] = "2";
			$data['orderid'] = $orderid;
			$data['oper'] = $_COOKIE["SYSNAME"];
			upd_order(http_build_query($data));
		}
		$yun_no = $yun_no."|".$strSend[$yuntype]."|".$strSendAPI[$yuntype];
		//寄發發貨信件 2020-11-17
		// $fsql->query( "SELECT COUNT(*) as t FROM {P}_shop_orderitems where orderid='{$orderid}'" );
		// if( $fsql->next_record() ){
		// 		$allord = $fsql->f("t");
		// 		if($allord == $noyun && $yun_mail=="0"){						
					
		// 			$msql->query( "SELECT * FROM {P}_shop_mailtemp WHERE tid='2' AND status='1'");//§쩺¼˪O
		// 				if($msql->next_record()){
		// 					$smsg = $memname."|".$GLOBALS['GLOBALS']['CONF'][SiteName]."|".time()."|".$OrderNo."|".$paytype."|".$paytotal."|".$GLOBALS['GLOBALS']['CONF'][SiteHttp]."|".$yun_no;
		// 					$from = $GLOBALS['GLOBALS']['CONF'][SiteEmail];
		// 					shopmail( $membermail, $from, $smsg, "2" );	
		// 					$msql->query("UPDATE {P}_shop_order SET yun_mail='1' WHERE orderid='{$orderid}'");
		// 				}
		// 		}
		// }

		echo "OK";
		exit( );
		break;
case "orderitemtui" :
		needauth( 327 );
		$itemid = $_POST['itemid'];
		$now = time( );
		$msql->query( "select * from {P}_shop_orderitems where id='{$itemid}'" );
		if ( $msql->next_record( ) )
		{
				$orderid = $msql->f( "orderid" );
				$gid = $msql->f( "gid" );
				$nums = $msql->f( "nums" );
				$itemyun = $msql->f( "ifyun" );
				$fz = $msql->f( "fz" );
				list($buysize, $buyprice, $buyspecid) = explode("^",$fz);
				if ( $itemyun != "1" )
				{
						echo "1005";
						exit( );
				}
		}
		else
		{
				echo "1000";
				exit( );
		}
		$msql->query( "select * from {P}_shop_order where orderid='{$orderid}'" );
		if ( $msql->next_record( ) )
		{
				$ifpay = $msql->f( "ifpay" );
				$ifyun = $msql->f( "ifyun" );
				$ifok = $msql->f( "ifok" );
				$iftui = $msql->f( "iftui" );
				if ( $iftui == "1" )
				{
						echo "1001";
						exit( );
				}
				if ( $ifok == "1" )
				{
						echo "1002";
						exit( );
				}
		}
		else
		{
				echo "1003";
				exit( );
		}
		$fsql->query( "update {P}_shop_orderitems set ifyun='0',yuntime='0' where id='{$itemid}'" );
		/*庫存改由訂單退訂時加回20130831*/
		/*$fsql->query( "update {P}_shop_con set kucun=kucun+{$nums},salenums=salenums+{$nums} where id='{$gid}'" );
		if($buyspecid){
			$fsql->query( "update {P}_shop_conspec set stocks=stocks+{$nums} where id='{$buyspecid}'" );
		}*/
		$fsql->query( "update {P}_shop_con set salenums=salenums+{$nums} where id='{$gid}'" );
		$fsql->query( "update {P}_shop_order set ifyun='0',yuntime='0' where orderid='{$orderid}'" );
		echo "OK";
		exit( );
		break;
case "orderpaychk" :
		$orderid = $_POST['orderid'];
		$dtime = time( );
		$ip = $_SERVER['REMOTE_ADDR'];
		$logname = $_COOKIE['SYSNAME'];
		$msql->query( "select * from {P}_shop_order where orderid='{$orderid}'" );
		if ( $msql->next_record( ) )
		{
				$OrderNo = $msql->f( "OrderNo" );
				$memberid = $msql->f( "memberid" );
				$ifpay = $msql->f( "ifpay" );
				$ifyun = $msql->f( "ifyun" );
				$ifok = $msql->f( "ifok" );
				$iftui = $msql->f( "iftui" );
				$paytotal = $msql->f( "paytotal" );
				$goodstotal = $msql->f( "goodstotal" );
				$payid = $msql->f( "payid" );
				$paytype = $msql->f( "paytype" );
				$tcent = $msql->f( "totalcent" );
				$memname = $msql->f( "name" );
				$membermail = $msql->f( "email" );
				if ( $ifpay == "1" )
				{
						echo "1001";
						exit( );
				}
				if ( $iftui == "1" )
				{
						echo "1002";
						exit( );
				}
				/*if ( $ifok == "1" )
				{
						echo "1003";
						exit( );
				}*/
		}
		else
		{
				echo "1000";
				exit( );
		}
		
		
		if ( $payid == "0" && $memberid != "0" )
		{
		
			
				$msql->query( "select account from {P}_member where memberid='{$memberid}'" );
				if ( $msql->next_record( ) )
				{
						$account = $msql->f( "account" );
						if ( $paytotal <= $account && 0 <= $paytotal )
						{
								$thisaccount = $account-$paytotal;
								$fsql->query( "update {P}_member set account=account-{$paytotal},buytotal=buytotal+{$paytotal} where memberid='{$memberid}'" );
								$fsql->query( "update {P}_shop_order set ifpay='1',paytime='{$dtime}' where orderid='{$orderid}'" );
								//因應退貨機制
								$fsql->query( "update {P}_shop_orderitems set ifpay='1' where orderid='{$orderid}'" );
								//
								$fsql->query( "insert into {P}_member_buylist set 
									`buyfrom`='{$strModuleShop}',
									`memberid`='{$memberid}',
									`orderid`='{$orderid}',
									`payid`='0',
									`paytype`='{$strMemberAccountPay}',
									`paytotal`='{$paytotal}',
									`daytime`='{$dtime}',
									`ip`='{$ip}',
									`OrderNo`='{$OrderNo}',
									`logname`='{$logname}',
									`thisaccount`='{$thisaccount}'
								" );
						$msql->query( "select * from {P}_shop_config" );
						while ( $msql->next_record( ) )
						{
								$variable = $msql->f( "variable" );
								$value = $msql->f( "value" );
								$GLOBALS['GLOBALS']['SHOPCONF'][$variable] = $value;
						}
								$centopen = $GLOBALS['SHOPCONF']['CentOpen'];
								$centid = $GLOBALS['SHOPCONF']['CentId'];
								$centcol = "cent".$centid;
								if ( $centopen == "1" )
								{
										$fsql->query( "update {P}_member set `".$centcol."`=`".$centcol."`+{$tcent} where memberid='{$memberid}'" );
										$fsql->query( "insert into {P}_member_centlog set 
											`memberid`='{$memberid}',
											`dtime`='{$dtime}',
											`event`='0',
											`memo`='{$strPayCentEvent}',
											`".$centcol."`='{$tcent}'
										" );
								}
/*±Hµo«H¥��*/
							$msql->query( "SELECT * FROM {P}_shop_mailtemp WHERE tid='1' AND status='1'");//
								if($msql->next_record()){
							$smsg = $memname."|".$GLOBALS['GLOBALS']['CONF'][SiteName]."|".time()."|".$OrderNo."|".$paytype."|".$paytotal."|".$GLOBALS['GLOBALS']['CONF'][SiteHttp];
							$from = $GLOBALS['GLOBALS']['CONF'][SiteEmail];
							shopmail( $membermail, $from, $smsg, "1" );							
								}
								echo "OK";
								exit( );
						}
						else
						{
								echo "1005";
								exit( );
						}
				}
				else
				{
						echo "1004";
						exit( );
				}
		}
		
		
		
		
		if ( $payid != "0" && $memberid != "0" )
		{
			//取出目前帳戶資訊
			$fsql->query("select * from {P}_member where memberid='".$memberid."'");
			if($fsql->next_record()){
				$account=$fsql->f('account');
			}
						
				$fsql->query( "update {P}_member set buytotal=buytotal+{$paytotal},paytotal=paytotal+{$paytotal} where memberid='{$memberid}'" );
				$fsql->query( "update {P}_shop_order set ifpay='1',paytime='{$dtime}' where orderid='{$orderid}'" );
				//因應退貨機制
				$fsql->query( "update {P}_shop_orderitems set ifpay='1' where orderid='{$orderid}'" );
				//
				$fsql->query( "insert into {P}_member_buylist set 
					`buyfrom`='{$strModuleShop}',
					`memberid`='{$memberid}',
					`orderid`='{$orderid}',
					`payid`='{$payid}',
					`paytype`='{$paytype}',
					`paytotal`='{$paytotal}',
					`daytime`='{$dtime}',
					`ip`='{$ip}',
					`OrderNo`='{$OrderNo}',
					`logname`='{$logname}',
					`thisaccount`='{$account}'
				" );
				$fsql->query( "insert into {P}_member_pay set 
					`memberid`='{$memberid}',
					`payid`='{$payid}',
					`oof`='{$paytotal}',
					`method`='{$paytype}',
					`type`='{$strPayFromChk}',
					`addtime`='{$dtime}',
					`ip`='{$ip}',
					`logname`='{$logname}',
					`thisaccount`='{$account}'
				" );
						$msql->query( "select * from {P}_shop_config" );
						while ( $msql->next_record( ) )
						{
								$variable = $msql->f( "variable" );
								$value = $msql->f( "value" );
								$GLOBALS['GLOBALS']['SHOPCONF'][$variable] = $value;
						}
				
				$centopen = $GLOBALS['SHOPCONF']['CentOpen'];
				$centid = $GLOBALS['SHOPCONF']['CentId'];
				$centcol = "cent".$centid;
				if ( $centopen == "1" )
				{
						$fsql->query( "update {P}_member set `".$centcol."`=`".$centcol."`+{$tcent} where memberid='{$memberid}'" );
						$fsql->query( "insert into {P}_member_centlog set 
							`memberid`='{$memberid}',
							`dtime`='{$dtime}',
							`event`='0',
							`memo`='{$strPayCentEvent}',
							`".$centcol."`='{$tcent}'
						" );
				}
		}
		if ( $payid != "0" && $memberid == "0" )
		{
				$fsql->query( "update {P}_shop_order set ifpay='1',paytime='{$dtime}' where orderid='{$orderid}'" );
				//因應退貨機制
				$fsql->query( "update {P}_shop_orderitems set ifpay='1' where orderid='{$orderid}'" );
				//
		}
/*±Hµo«H¥��*/
							$msql->query( "SELECT * FROM {P}_shop_mailtemp WHERE tid='1' AND status='1'");//§쩺¼˪O
								if($msql->next_record()){
							$smsg = $memname."|".$GLOBALS['GLOBALS']['CONF'][SiteName]."|".time()."|".$OrderNo."|".$paytype."|".$paytotal."|".$GLOBALS['GLOBALS']['CONF'][SiteHttp];
							$from = $GLOBALS['GLOBALS']['CONF'][SiteEmail];
							shopmail( $membermail, $from, $smsg, "1" );							
								}
		include_once( ROOTPATH."costomer.php");
		$data['status'] = "1";
		$data['orderid'] = $orderid;
		$data['oper'] = $_COOKIE["SYSNAME"];
		$data['payid'] = $payid;
		$data['paytype'] = $paytype;
		upd_order(http_build_query($data));
		echo "OK";
		exit( );
		break;
case "orderunpay" :
		needauth( 326 );
		$orderid = $_POST['orderid'];
		$dtime = time( );
		$ip = $_SERVER['REMOTE_ADDR'];
		$logname = $_COOKIE['SYSNAME'];
		
		
		$msql->query( "select * from {P}_shop_order where orderid='{$orderid}'" );
		if ( $msql->next_record( ) )
		{
				$OrderNo = $msql->f( "OrderNo" );
				$memberid = $msql->f( "memberid" );
				$ifpay = $msql->f( "ifpay" );
				$ifyun = $msql->f( "ifyun" );
				$ifok = $msql->f( "ifok" );
				$iftui = $msql->f( "iftui" );
				$paytotal = $realpay = $msql->f( "paytotal" );
				$totaloof = $msql->f( "totaloof" );
				$totalcent = $msql->f( "totalcent" );
				$goodstotal = $msql->f( "goodstotal" );
				$disaccount = $msql->f( "disaccount" );
				$payid = $msql->f( "payid" );
				$paytype = $msql->f( "paytype" );
				$tcent = $msql->f( "totalcent" );
				$memname = $msql->f( "name" );
				$membermail = $msql->f( "email" );
				$yunfei = (INT)$msql->f( "yunfei" );
				list($yuntype, $yun_no) = explode("|",$msql->f( "sendtypeno" ));
				$source = $msql->f( "source" );
				$onlineshop = substr($source,0,1);
				$promocode = $msql->f( "promocode" );
				$promoprice = $msql->f( "promoprice" );
				if($promocode != ""){
					$getPromo = $fsql->getone( "select * from {P}_shop_promocode where code='{$promocode}'" );
					if($getPromo["type"]==2){
						$prorate = $getPromo["type_value"];
					}else{
						$prorate = 1;
					}
				}else{
					$prorate = 1;
				}
				if ( $ifpay != "1" )
				{
						echo "1001";
						exit( );
				}
				
		
				//取出目前帳戶資訊
				$msql->query("select * from {P}_member where memberid='".$memberid."'");
				if($msql->next_record()){
					$account=$msql->f('account');
				}
				
				if($onlineshop == 1 || $onlineshop == 2){
					
					//線上平台
					$fsql->query( "update {P}_shop_order set ifpay='0' where orderid='{$orderid}'" );
					$fsql->query( "update {P}_shop_orderitems set ifpay='0' where orderid='{$orderid}' and itemtui='1'" );
					$fsql->query( "update {P}_shop_order set itemtuipay='1',tuipay='$tuitotal',tuiok='1' where orderid='{$orderid}'" );
					$fsql->query( "insert into {P}_member_buylist set 
									`buyfrom`='{$strModuleShop}',
									`memberid`='{$memberid}',
									`orderid`='{$orderid}',
									`payid`='0',
									`paytype`='{$strOrderUnPay}',
									`paytotal`='-{$tuitotal}',
									`daytime`='{$dtime}',
									`ip`='{$ip}',
									`OrderNo`='{$OrderNo}',
									`logname`='{$logname}',
									`thisaccount`='{$account}'
								" );
					echo "1005";
					exit();
				}
				/*if ( $iftui == "1" )
				{
						echo "1002";
						exit( );
				}
				if ( $ifok == "1" )
				{
						echo "1003";
						exit( );
				}*/
				$paytotal = $paytotal>0?  $paytotal+$disaccount:$totaloof+$disaccount;
				
				$goodstotal = $goodstotal>0? $goodstotal:$totalcent;
		}
		else
		{
				echo "1000";
				exit( );
		}
		
		//exit("//isalltui:".$isalltui."//alljine:".$alljine."//goodstotal:".$goodstotal."//paytotal:".$paytotal);
		
		//$goodstotal 是 退貨後剩餘的商品金額
		
		
		/*僅退除退貨的金額*/
		$msql->query( "select * from {P}_shop_orderitems where orderid='{$orderid}' AND itemtui='1' AND iftui='1'" );
		while ( $msql->next_record( ) )
		{
			//退貨商品的總額alljine
			$relalljine += $msql->f("jine");
			$alljine += round($msql->f("jine")*$prorate);
			//$alljine += $msql->f("price")*$msql->f("itemtuinums");
		}
		
		
		//如果退貨商品的總額alljine 等於 totalcent 就是 全退
		if($relalljine == $totalcent){
			$isalltui = true;
		}
		$addtuitotal = 0;
		
		/*2017-03-25 計算運費*/
		/*擷取錢幣符號*/
		$TWD = $fsql->getone( "SELECT pricesymbol,goodstotal,yunfei,payid,source FROM {P}_shop_order WHERE orderid='{$orderid}'" );
		$getsource = substr($TWD["source"],0,1);
		$getyunfei = $TWD["yunfei"];
		$goodstotal = $TWD["goodstotal"];
		$getpricesymbol = $TWD["pricesymbol"];
		$getpayid = $TWD["payid"];
		$SYM = $fsql->getone( "SELECT pricecode,rate,point FROM {P}_base_currency WHERE pricesymbol='{$getpricesymbol}'" );
		$getrate = $SYM["rate"];
		$getpoint = $SYM["point"];
		$getpricecode = $SYM["pricecode"];
		if($getpricecode == "TWD"){
			$fsql->query("select dgs from {P}_shop_yun where id='{$getpayid}'");
			if($fsql->next_record()){
				$dgs = $fsql->f("dgs");
				list($setyunfei, $setyunprice) = explode("|",$dgs);
				$oriyunfei = countyunfeip( $tweight, $totalcent, $dgs, $getrate );//原始運費
				$cutpromoyunfei = $getrate!="1"? round(($oriyunfei*$getrate),$getpoint):$oriyunfei;//多國用
			}
		}else{
			$fsql->query("select dgs from {P}_shop_yun where spec='{$getpricecode}'");
			if($fsql->next_record()){
				$dgs = $fsql->f("dgs");
				list($setyunfei, $setyunprice) = explode("|",$dgs);
				$oriyunfei = countyunfeip( $tweight, $totalcent, $dgs, $getrate );//原始運費
				$cutpromoyunfei = $getrate!="1"? round(($oriyunfei*$getrate),$getpoint):$oriyunfei;//多國用
			}
		}

		if($alljine){
			//如果是全退
			// if($isalltui){
			// 	//退還金額$tuitotal就是退貨商品的總額 加上 運費
			// 	//刷卡者刷退刷卡金額，餘額退還帳戶
			// 	if($payid == '1'){
			// 		$tuitotal = $totaloof+$yunfei;
			// 		//餘額退還帳戶
			// 	}else{
			// 		//全退到帳戶
			// 		$tuitotal = $totaloof+$disaccount+$yunfei;
			// 	}
			// }else{
			// 	//2018-03-13
			// 	//如果不是全退
			// 	// $tuitotal = $alljine - $cutpromoyunfei;
			// 	// if($totalcent>1500 && $goodstotal-$promoprice<1500){
			// 	// 	$tuitotal = $alljine-$yunfei;
			// 	// }else{
			// 	// 	$tuitotal = $alljine;
			// 	// }
				
			// 	//2018-08-30
			// 	//realpay<0 表示退款大於實際付款，表示有餘額付費，需補回剩餘餘額
			// 	//修改餘額付款，修改總付款為 0
			// 	if($realpay<0){
			// 		$addtuitotal = abs($realpay);
			// 		$fsql->query( "update {P}_shop_order set disaccount='{$goodstotal}+{$yunfei}',paytotal='0' where orderid='{$orderid}'" );
			// 	}
			// }
			$refundAmountPromoprice = 0;
			if($getPromo["type"] == 1){
				$refundAmountPromoprice = $promoprice;
			}
			if($cutpromoyunfei === 0) {
				$tuitotal = $alljine - $yunfei - $refundAmountPromoprice;
			} else {
				$tuitotal = $alljine - $refundAmountPromoprice;
			}
			
			if(!$isalltui && $realpay<0){
				$addtuitotal = abs($realpay);
				$fsql->query( "update {P}_shop_order set disaccount='{$goodstotal}+{$yunfei}',paytotal='0' where orderid='{$orderid}'" );
			}
		}else{
			exit("發生錯誤: 應該先操作商品退貨流程！");
		}
		
		
		//$tuitotal 退還金額
		
		if ( $memberid != "0" )
		{
				$msql->query( "select memberid from {P}_member where memberid='{$memberid}'" );
				if ( $msql->next_record( ) )
				{
						$account=$msql->f('account');
						//必須扣除運費
						//$goodstotal = $goodstotal - $yunfei;
						$daytime = time( );
						$ip = $_SERVER['REMOTE_ADDR'];
						$logname = $_COOKIE['SYSNAME'];
						if($payid == '1'){
							//退回到餘額
							/*$fsql->query( "update {P}_member set account=account+{$tuitotal},buytotal=buytotal-{$tuitotal} where memberid='{$memberid}'" );
							$memo = "退還餘額：".$tuitotal;
							$msql->query( "insert into {P}_member_pay set 
								`memberid`='{$memberid}',
								`payid`='{$payid}',
								`oof`='{$tuitotal}',
								`method`='退貨退款',
								`type`='後台入帳',
								`addtime`='{$daytime}',
								`fpnum`='',
								`memo`='{$memo}',
								`ip`='{$ip}',
								`logname`='{$logname}'
							" );*/
							$fsql->query( "update {P}_member set buytotal=buytotal-{$tuitotal} where memberid='{$memberid}'" );
							$memo = "不退還餘額，刷卡退刷：".$tuitotal;
							$msql->query( "insert into {P}_member_pay set 
								`memberid`='{$memberid}',
								`payid`='{$payid}',
								`oof`='{$tuitotal}',
								`method`='退刷',
								`type`='後台入帳',
								`addtime`='{$daytime}',
								`fpnum`='',
								`memo`='{$memo}',
								`ip`='{$ip}',
								`logname`='{$logname}',
								`thisaccount`='{$account}'
							" );
						}else{
							$fsql->query( "update {P}_member set account=account+{$tuitotal},buytotal=buytotal-{$tuitotal} where memberid='{$memberid}'" );
							$memo = "退還退貨金額 ".$tuitotal;
							
							$thisaccount = $account+$tuitotal;
							
							$msql->query( "insert into {P}_member_pay set 
								`memberid`='{$memberid}',
								`payid`='{$payid}',
								`oof`='{$tuitotal}',
								`method`='退貨退款',
								`type`='後台入帳',
								`addtime`='{$daytime}',
								`fpnum`='',
								`memo`='{$memo}',
								`ip`='{$ip}',
								`logname`='{$logname}',
								`thisaccount`='{$thisaccount}'
							" );
						}
						
						if($isalltui){
							$fsql->query( "update {P}_shop_order set ifpay='0' where orderid='{$orderid}'" );
							$fsql->query( "update {P}_shop_orderitems set ifpay='0' where orderid='{$orderid}' and itemtui='1'" );
						}else{
							$fsql->query( "update {P}_shop_orderitems set ifpay='0' where orderid='{$orderid}' and itemtui='1'" );
						}
						
						$fsql->query( "insert into {P}_member_buylist set 
							`buyfrom`='{$strModuleShop}',
							`memberid`='{$memberid}',
							`orderid`='{$orderid}',
							`payid`='0',
							`paytype`='{$strOrderUnPay}',
							`paytotal`='-{$tuitotal}',
							`daytime`='{$dtime}',
							`ip`='{$ip}',
							`OrderNo`='{$OrderNo}',
							`logname`='{$logname}',
							`thisaccount`='{$thisaccount}'
						" );
						
						$msql->query( "select * from {P}_shop_config" );
						while ( $msql->next_record( ) )
						{
								$variable = $msql->f( "variable" );
								$value = $msql->f( "value" );
								$GLOBALS['GLOBALS']['SHOPCONF'][$variable] = $value;
						}
						$centopen = $GLOBALS['SHOPCONF']['CentOpen'];
						$centid = $GLOBALS['SHOPCONF']['CentId'];
						$centcol = "cent".$centid;
						if ( $centopen == "1" )
						{
								$fsql->query( "update {P}_member set `".$centcol."`=`".$centcol."`-{$tcent} where memberid='{$memberid}'" );
								$fsql->query( "insert into {P}_member_centlog set 
									`memberid`='{$memberid}',
									`dtime`='{$dtime}',
									`event`='0',
									`memo`='{$strOrderUnPay}',
									`".$centcol."`='-{$tcent}'
								" );
						}
						
						$fsql->query( "update {P}_shop_order set itemtuipay='1',tuipay='$tuitotal',tuiok='1' where orderid='{$orderid}'" );
/*±Hµo«H¥񨭍*/
						if($payid == '1'){
							$msql->query( "SELECT * FROM {P}_shop_mailtemp WHERE tid='7' AND status='1'");//§쩺¼˪O
							if($msql->next_record()){
								$smsg = $memname."|".$GLOBALS['GLOBALS']['CONF'][SiteName]."|".time()."|".$OrderNo."|".$paytype."|".$tuitotal."|".$GLOBALS['GLOBALS']['CONF'][SiteHttp]."|".$yun_no;
								$from = $GLOBALS['GLOBALS']['CONF'][SiteEmail];
								shopmail( $membermail, $from, $smsg, "7" );
							}
						}else{
							$msql->query( "SELECT * FROM {P}_shop_mailtemp WHERE tid='3' AND status='1'");//§쩺¼˪O
							if($msql->next_record()){
								$smsg = $memname."|".$GLOBALS['GLOBALS']['CONF'][SiteName]."|".time()."|".$OrderNo."|".$paytype."|".$tuitotal."|".$GLOBALS['GLOBALS']['CONF'][SiteHttp]."|".$yun_no;
								$from = $GLOBALS['GLOBALS']['CONF'][SiteEmail];
								shopmail( $membermail, $from, $smsg, "3" );
							}
						}
						echo "OK";
						exit( );
				}
				else
				{
						echo "1004";
						exit( );
				}
		}
		else
		{
				$fsql->query( "update {P}_shop_order set ifpay='0',paytime='0' where orderid='{$orderid}'" );
		}
		echo "OK";
		exit( );
		break;
case "modigoodstotal" :
		needauth( 322 );
		trylimit( "_shop_order", 50, "orderid" );
		$orderid = $_POST['orderid'];
		$nowprice = $_POST['nowprice'];
		$msql->query( "select * from {P}_shop_order where orderid='{$orderid}'" );
		if ( $msql->next_record( ) )
		{
				$ifpay = $msql->f( "ifpay" );
				$ifok = $msql->f( "ifok" );
				$iftui = $msql->f( "iftui" );
				$oldgoodstotal = $msql->f( "goodstotal" );
				$oldtotal = $msql->f( "paytotal" );
				$disaccount = $msql->f( "disaccount" );
				/*if ( $ifpay == "1" )
				{
						echo "1001";
						exit( );
				}*/
				if ( $ifok == "1" )
				{
						echo "1002";
						exit( );
				}
				if ( $iftui == "1" )
				{
						echo "1003";
						exit( );
				}
				$newtotal = $oldtotal - ( $oldgoodstotal - $nowprice );
				$fsql->query( "update {P}_shop_order set goodstotal='{$nowprice}',totaloof='{$newtotal}',paytotal='{$newtotal}' where orderid='{$orderid}'" );
				$newtotal = number_format( $newtotal, 0, ".", "" );
				echo "OK_".$newtotal;
				exit( );
		}
		else
		{
				echo "1000";
				exit( );
		}
		break;
case "modiyunfei" :
		needauth( 322 );
		trylimit( "_shop_order", 50, "orderid" );
		$orderid = $_POST['orderid'];
		$nowprice = $_POST['nowprice'];
		$msql->query( "select * from {P}_shop_order where orderid='{$orderid}'" );
		if ( $msql->next_record( ) )
		{
				$ifpay = $msql->f( "ifpay" );
				$ifok = $msql->f( "ifok" );
				$iftui = $msql->f( "iftui" );
				$oldyunfei = $msql->f( "yunfei" );
				$oldtotal = $msql->f( "paytotal" );
				if ( $ifpay == "1" )
				{
						echo "1001";
						exit( );
				}
				if ( $ifok == "1" )
				{
						echo "1002";
						exit( );
				}
				if ( $iftui == "1" )
				{
						echo "1003";
						exit( );
				}
				$newtotal = $oldtotal - ( $oldyunfei - $nowprice );
				$fsql->query( "update {P}_shop_order set yunfei='{$nowprice}',totaloof='{$newtotal}',paytotal='{$newtotal}' where orderid='{$orderid}'" );
				$newtotal = number_format( $newtotal, 0, ".", "" );
				echo "OK_".$newtotal;
				exit( );
		}
		else
		{
				echo "1000";
				exit( );
		}
		break;
case "modipromoprice" :
		needauth( 322 );
		trylimit( "_shop_order", 50, "orderid" );
		$orderid = $_POST['orderid'];
		$nowpromoprice = $_POST['nowpromoprice'];
		$msql->query( "select * from {P}_shop_order where orderid='{$orderid}'" );
		if ( $msql->next_record( ) )
		{
				$ifpay = $msql->f( "ifpay" );
				$ifok = $msql->f( "ifok" );
				$iftui = $msql->f( "iftui" );
				$oldpromoprice = $msql->f( "promoprice" );
				$oldtotal = $msql->f( "paytotal" );
				if ( $ifok == "1" )
				{
						echo "1002";
						exit( );
				}
				if ( $iftui == "1" )
				{
						echo "1003";
						exit( );
				}
				$newtotal = $oldtotal + $oldpromoprice - $nowpromoprice;
				$fsql->query( "update {P}_shop_order set promoprice='{$nowpromoprice}',totaloof='{$newtotal}',paytotal='{$newtotal}' where orderid='{$orderid}'" );
				$newtotal = number_format( $newtotal, 0, ".", "" );
				echo "OK_".$newtotal;
				exit( );
		}
		else
		{
				echo "1000";
				exit( );
		}
		break;
case "ordermodibz" :
		needauth( 321 );
		$orderid = $_POST['orderid'];
		$bztext = htmlspecialchars( $_POST['bztext'] );
		$fsql->query( "update {P}_shop_order set `bz`='{$bztext}' where orderid='{$orderid}'" );
		echo "OK";
		break;
case "addcattemp" :
				$catid = htmlspecialchars( $_POST['catid'] );
				if ( $catid == "" )
				{
								echo $strZlNTC1;
								exit( );
				}
				$msql->query( "update {P}_shop_cat set `cattemp`='1' where catid='{$catid}'" );
				$chgdb= array('query','detail');
				foreach($chgdb AS $chgname){
				$msql->query( "select * from {P}_base_pageset where coltype='shop' and pagename='{$chgname}'" );
				if ( $msql->next_record( ) )
				{
					$fsql->query( "insert into {P}_base_pageset (`id`, `name`, `coltype`, `pagename`, `th`, `ch`, `bh`, `pagetitle`, `metakey`, `metacon`, `bgcolor`, `bgimage`, `bgposition`, `bgrepeat`, `bgatt`, `containwidth`, `containbg`, `containimg`, `containmargin`, `containpadding`, `containcenter`, `topbg`, `topwidth`, `contentbg`, `contentwidth`, `contentmargin`, `bottombg`, `bottomwidth`, `buildhtml`, `xuhao`) VALUES (NULL,'{$msql->f('name')}','{$msql->f('coltype')}','{$chgname}_{$catid}','{$msql->f('th')}','{$msql->f('ch')}','{$msql->f('bh')}','{$msql->f('pagetitle')}','{$msql->f('metakey')}','{$msql->f('metacon')}','{$msql->f('bgcolor')}','{$msql->f('bgimage')}','{$msql->f('bgposition')}','{$msql->f('bgrepeat')}','{$msql->f('bgatt')}','{$msql->f('containwidth')}','{$msql->f('containbg')}','{$msql->f('containimg')}','{$msql->f('containmargin')}','{$msql->f('containpadding')}','{$msql->f('containcenter')}','{$msql->f('topbg')}','{$msql->f('topwidth')}','{$msql->f('contentbg')}','{$msql->f('contentwidth')}','{$msql->f('contentmargin')}','{$msql->f('bottombg')}','{$msql->f('bottomwidth')}','{$msql->f('buildhtml')}','{$msql->f('xuhao')}') " );
				}
				else
				{
								echo $strZlNTC2;
								exit( );
				}
				
				$msql->query( "select * from {P}_base_plusdefault where coltype='shop' and pluslocat='{$chgname}'" );
				while ( $msql->next_record( ) )
				{
					$fsql->query( "INSERT INTO {P}_base_plusdefault (`id`, `coltype`, `pluslable`, `plusname`, `plustype`, `pluslocat`, `tempname`, `tempcolor`, `showborder`, `bordercolor`, `borderwidth`, `borderstyle`, `borderlable`, `borderroll`, `showbar`, `barbg`, `barcolor`, `backgroundcolor`, `morelink`, `width`, `height`, `top`, `left`, `zindex`, `padding`, `shownums`, `ord`, `sc`, `showtj`, `cutword`, `target`, `catid`, `cutbody`, `picw`, `pich`, `fittype`, `title`, `body`, `pic`, `piclink`, `attach`, `movi`, `sourceurl`, `word`, `word1`, `word2`, `word3`, `word4`, `text`, `text1`, `code`, `link`, `link1`, `link2`, `link3`, `link4`, `tags`, `groupid`, `projid`, `moveable`, `classtbl`, `grouptbl`, `projtbl`, `setglobal`, `overflow`, `bodyzone`, `display`, `ifmul`, `ifrefresh`) VALUES (NULL, '{$msql->f('coltype')}', '{$msql->f('pluslable')}', '{$msql->f('plusname')}', '{$msql->f('plustype')}', '{$chgname}_{$catid}', '{$msql->f('tempname')}', '{$msql->f('tempcolor')}', '{$msql->f('showborder')}', '{$msql->f('bordercolor')}', '{$msql->f('borderwidth')}', '{$msql->f('borderstyle')}', '{$msql->f('borderlable')}', '{$msql->f('borderroll')}', '{$msql->f('showbar')}', '{$msql->f('barbg')}', '{$msql->f('barcolor')}', '{$msql->f('backgroundcolor')}', '{$msql->f('morelink')}', '{$msql->f('width')}', '{$msql->f('height')}', '{$msql->f('top')}', '{$msql->f('left')}', '{$msql->f('zindex')}', '{$msql->f('padding')}', '{$msql->f('shownums')}', '{$msql->f('ord')}', '{$msql->f('sc')}', '{$msql->f('showtj')}', '{$msql->f('cutword')}', '{$msql->f('target')}', '{$msql->f('catid')}', '{$msql->f('cutbody')}', '{$msql->f('picw')}', '{$msql->f('pich')}', '{$msql->f('fittype')}', '{$msql->f('title')}', '{$msql->f('body')}', '{$msql->f('pic')}', '{$msql->f('piclink')}', '{$msql->f('attach')}', '{$msql->f('movi')}', '{$msql->f('sourceurl')}', '{$msql->f('word')}', '{$msql->f('word1')}', '{$msql->f('word2')}', '{$msql->f('word3')}', '{$msql->f('word4')}', '{$msql->f('text')}', '{$msql->f('text1')}', '{$msql->f('code')}', '{$msql->f('link')}', '{$msql->f('link1')}', '{$msql->f('link2')}', '{$msql->f('link3')}', '{$msql->f('link4')}', '{$msql->f('tags')}', '{$msql->f('groupid')}', '{$msql->f('projid')}', '{$msql->f('moveable')}', '{$msql->f('classtbl')}', '{$msql->f('grouptbl')}', '{$msql->f('projtbl')}', '{$msql->f('setglobal')}', '{$msql->f('overflow')}', '{$msql->f('bodyzone')}', '{$msql->f('display')}', '{$msql->f('ifmul')}', '{$msql->f('ifrefresh')}')" );
				}
				}								
				echo "OK";
				exit( );
				break;
case "delcattemp" :
				$catid = htmlspecialchars( $_POST['catid'] );
				if ( $catid == "" )
				{
								echo $strZlNTC1;
								exit( );
				}
				$msql->query( "select catid from {P}_shop_cat where catid='{$catid}'" );
				if ( $msql->next_record( ) )
				{
				}
				else
				{
								echo $strZlNTC2;
								exit( );
				}
				$chgdb= array('query','detail');
				foreach($chgdb AS $chgname){
				$pagename = $chgname."_".$catid;
				$msql->query( "delete from {P}_base_pageset where coltype='shop' and pagename='{$pagename}'" );
				$msql->query( "delete from {P}_base_plusdefault where plustype='shop' and pluslocat='{$pagename}'" );
				$msql->query( "delete from {P}_base_plus where plustype='shop' and pluslocat='{$pagename}'" );
				$msql->query( "update {P}_shop_cat set `cattemp`='0' where catid='{$catid}'" );
				}
				echo "OK";
				exit( );
				break;
case "addproj" :
		$project = htmlspecialchars( $_POST['project'] );
		$folder = htmlspecialchars( $_POST['folder'] );
		if ( $project == "" )
		{
				echo $strProjNTC1;
				exit( );
		}
		if ( strlen( $folder ) < 2 || 16 < strlen( $folder ) )
		{
				echo $strProjNTC2;
				exit( );
		}
		if ( !eregi( "^[0-9a-z]{1,16}\$", $folder ) )
		{
				echo $strProjNTC3;
				exit( );
		}
		if ( strstr( $folder, "/" ) || strstr( $folder, "." ) )
		{
				echo $strProjNTC3;
				exit( );
		}
		$arr = array( "main", "html", "class", "detail", "query", "index", "admin", "shopgl", "shopfabu", "shopmodify", "shopcat", "shop" );
		if ( in_array( $folder, $arr ) == true )
		{
				echo $strProjNTC4;
				exit( );
		}
		if ( file_exists( "../project/".$folder ) )
		{
				echo $strProjNTC4;
				exit( );
		}
		$msql->query( "select id from {P}_shop_proj where folder='{$folder}'" );
		if ( $msql->next_record( ) )
		{
				echo $strProjNTC4;
				exit( );
		}
		$pagename = "proj_".$folder;
		@mkdir( "../project/".$folder, 511 );
		$fd = fopen( "../project/temp.php", "r" );
		$str = fread( $fd, "2000" );
		$str = str_replace( "TEMP", $pagename, $str );
		fclose( $fd );
		$filename = "../project/".$folder."/index.php";
		$fp = fopen( $filename, "w" );
		fwrite( $fp, $str );
		fclose( $fp );
		@chmod( $filename, 493 );
		$msql->query( "insert into {P}_shop_proj set 
			`project`='{$project}',
			`folder`='{$folder}'
		" );
		$msql->query( "insert into {P}_base_pageset set 
			`name`='{$project}',
			`coltype`='shop',
			`pagename`='{$pagename}',
			`pagetitle`='{$project}',
			`buildhtml`='index'
		" );
		echo "OK";
		exit( );
		break;
}


function filterXmlStr($str) 
{ 
  return preg_replace("/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/",'',$str);
}

?>
