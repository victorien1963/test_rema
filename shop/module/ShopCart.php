<?php

/*
	[元件名稱] 購物車
*/
//echo "（程式測試用）".var_dump($_COOKIE[SHOPCART]);
function ShopCart(){

	global $fsql,$msql,$tsql,$sybtype,$strEdit,$strCancel,$strFinish,$strCheckOut,$strContinue;
	
		
	$GLOBALS['fbtrack'] ="
		_lt('send', 'cv', {
		  type: 'Conversion'
		},['7e3035d0-8bec-4778-85f6-8b8f21add298']);
	";
		
		$getdata = $_GET["getdata"];
		$getfz = $_GET["getfz"];
		$act = $_GET["act"];
		
		if($act==""){
			$showitem = "black";
			$showcoupon = "none";
			$showdel = "none";
			$showchk = "black";
			$chkout = "cart.php?act=next";
			$modilink = "window.location='cart.php?act=modi'";
			$moditext = $strEdit;
			$cross = "<i class=\"icon-cross\"></i>";
			$showtitlea = "black";
			$showtitleb = "none";
			$chkstr = $strCheckOut;
			$back=ROOTPATH."index.php";
		}elseif($act=="next"){
			$showitem = "none";
			$showcoupon = "black";
			$showdel = "none";
			$showchk = "black";
			$chkout = "javascript:;";
			$modilink = "";
			$moditext = "";
			$cross = "<i class=\"icon-arrow-left\"></i>";
			$showtitlea = "none";
			$showtitleb = "black";
			$chkstr = $strContinue;
			$gostart = "gostart";
			$back="javascript:window.location='cart.php'";
		}elseif($act=="modi"){
			$showitem = "black";
			$showcoupon = "none";
			$showdel = "black";			
			$showchk = "none";
			$chkout = "cart.php?act=next";
			$modilink = "window.location='cart.php'";
			$moditext = $strFinish;
			$cross = $strCancel;
			$showtitlea = "black";
			$showtitleb = "none";
			$chkstr = $strCheckOut;
			$back="javascript:window.location='cart.php'";
		}
		
		//獲取貨幣、匯率
		if($sybtype){
			list($getsymbol,$getpricecode,$getrate,$getpoint,$getpriceid) = explode(",",$sybtype);
		}else{
			list($getsymbol,$getpricecode,$getrate,$getpoint,$getpriceid) = explode(",",getDefaultSyb());
		}
				
		$tempname=$GLOBALS["PLUSVARS"]["tempname"];		
		$pagename=$GLOBALS["PLUSVARS"]["pagename"];
		
		//模版解釋
		$Temp=LoadTemp($tempname);
		$TempArr=SplitTblTemp($Temp);

		$str=$TempArr["start"];

		
		//判斷積分規則
		$centopen=$GLOBALS["SHOPCONF"]["CentOpen"];

		if($centopen=="1" && isLogin()){
			$showcent="";
		}else{
			$showcent=" style='display:none' ";
		}
		
		setcookie( "RETURN", $_SERVER['HTTP_REFERER'], time( ) + 3600, "/" );
		if(strpos($_SERVER['HTTP_REFERER'],"startorder.php") !== FALSE){
			$return = $_COOKIE["RETURN"] != "" ? $_COOKIE["RETURN"]:$_SERVER['HTTP_REFERER'];
		}else{
			$return = $_SERVER['HTTP_REFERER'];
		}
		//$return = $_COOKIE["RETURN"] != "" ? $_COOKIE["RETURN"]:$_SERVER['HTTP_REFERER'];
		
		//exit($_COOKIE["RETURN"]);
		
		/*獲取商品促銷編碼 2013-12-13*/
			$gettypecode = URIAuthcode($_GET["promotypecode"]);
			if($gettypecode){
				list($promotype,$promo_con,$promo_tjine,$promoid,$promocode) = explode("-",$gettypecode);
				$promo_spec = $_GET["promospec"];
				if($promotype == 1){
					/*送贈品*/
					list($pid) = explode("#",$promo_con);
					list($psize, $psizeid) = explode("^",$promo_spec);
					###加註在訂單COOKIE中###
					$_COOKIE['SHOPCART'] = $_COOKIE['SHOPCART'].$pid."|1|".$psize."^0^".$psizeid."#";
				}
			}
		/**/
		
		$isre = $_GET[isre];

		$CARTSTR=$_COOKIE["SHOPCART"];
		$array=explode('#',$CARTSTR);
		$tnums=sizeof($array)-1;
		$orderList=array();
		$orderTotalInfo=array();
		$deliveryInfo=array();
		$jdepromocode ='';
		$tjine=0;
		$kk=0;
		
		//購物車開始
		$var=array('showcent'=>$showcent,'tnums'=>$tnums);
		$str.=ShowTplTemp($TempArr["m0"],$var);
		
		/*商品加購 2014-02-22 STR*/
			/*先統計時間內有加購的商品ID紀錄*/
			$nowtime = time();
			$tsql->query( "select * from {P}_shop_promotebuy where `sdt`<'{$nowtime}' and `ndt`>'{$nowtime}'" );
			$d = 0;
			while ( $tsql->next_record( ) )
			{
				$listmida = explode(",",$tsql->f('promo_productid'));
				$gida[$d][gida] = $listmida;
				$listmidb = explode(",",$tsql->f('promo_productaddid'));
				$gidm[$d][gidm] = $listmidb;
				$pprice[$d][price] = $tsql->f('promo_money');
				$d++;
			}
			
			/*統計商品在每個合購規則的數量*/
			for($t=0;$t<$tnums;$t++){
				$fffs=explode('|',$array[$t]);
				$gids=$fffs[0];
				$gidacc=$fffs[1];
				for($k=0;$k<count($pprice);$k++){
					if(in_array($gids,$gida[$k][gida])){
						$getaddbuy[$k][gida] += $gidacc;
					}
					if(in_array($gids,$gidm[$k][gidm])){
						$getaddbuy[$k][gidm] += $gidacc;
					}
				}
				
				//記錄商品ID以便篩選是否為特價品
				$gidslist[] = $gids;
			}
			/*計算合購折扣優惠*/
			for($m=0;$m<count($getaddbuy);$m++){
				if($getaddbuy[$m][gida] - $getaddbuy[$m][gidm] >= 0){
					$cutprice += ($getaddbuy[$m][gidm])*$pprice[$m][price];
				}else{
					$cutprice += ($getaddbuy[$m][gida])*$pprice[$m][price];
				}
			}
			
			//exit(var_dump($cutprice));
			
		/*商品加購 2014-02-22 END*/
		
		for($t=0;$t<$tnums;$t++){
				$fff=explode('|',$array[$t]);
				list($gid, $subpicid)=explode("-",$fff[0]);
				$acc=$fff[1];
				$fz=$fff[2];
				/*$addid=$fff[3];
				$cutprice=$fff[4]*$fff[5];*/
				
				//if($cutprice>0){ $addtitle="[折扣商品] ";}else{ $addtitle="";}

				list($buysize, $buyprice, $buyspecid) = explode("^",$fz);
				
				$fzspecid = "_".$buyspecid;
				$specid = $buyspecid;
				//$addtitle = " (".$buycolorname."-".$buysize.")";
				//$addnote = $getisadd? "[加購] ":"";

				$fsql->query("select * from {P}_shop_con where id='$gid'");
				if($fsql->next_record()){
					$bn=$fsql->f('bn');
					$title=$addnote.$fsql->f('title').$addtitle;
					$danwei=$fsql->f('danwei');
				
					/*價格修正 2017-05-21*/
						$getp = $fsql->f('price');
						$buyprice = $getp;
					/*價格修正 END*/
					
					
					if($subpicid){
						$getsubpic = $msql->getone("select src from {P}_shop_con where id='$subpicid'");
						$src=$getsubpic["src"];
					}else{
						$src=$fsql->f('src');	
					}				
					$srcs=dirname($src)."/sp_".basename($src);
					$srcs=ROOTPATH.$srcs;
					$colorname=$fsql->f('colorname');
					
					//幣值更換
					$oribuyprice=$buyprice;
					$oriprice=$fsql->f('price');
					
					$buyprice=$getrate!="1"? round(($buyprice*$getrate),$getpoint):$buyprice;
					$price=$getrate!="1"? round(($fsql->f('price')*$getrate),$getpoint):$fsql->f('price');
						
						
					$price = isset($buyprice)? $buyprice:$price;
					$cent=$fsql->f('cent');
					
					$orijine=$oriprice*$acc;
					
					$jine=$price*$acc;
					
					$realjine=$oriprice*$acc;
					
					$price=getMemberPrice($gid,$price);
					$showprice=number_format($price, $getpoint);
					
					//計算積分
					$cent=accountCent($cent,$price)*$acc;
					
					$goodsurl=ROOTPATH."shop/html/?".$gid.".html";
					
					/*抓取尺寸*/
					//$ccode = $tsql->getone("select colorcode from {P}_shop_conspec where id='{$specid}'");

					$accselect = "";
					if($acc > 10){ $dt = $acc;}else{ $dt = 10;}
					for($ac=1;$ac<=$dt;$ac++){
						if($acc == $ac){
							$accselect .= "<option value=\"".$ac."\" selected>".$ac."</option>";
						}else{
							$accselect .= "<option value=\"".$ac."\">".$ac."</option>";
						}
					}
					
	
				
					$var=array (
					'gid' => $gid,
					'goodsurl' => $goodsurl,
					'jine' => number_format($jine, $getpoint), 
					'price' => number_format($price, $getpoint),
					'acc' => $acc,
					'fz' => $fz,
					'goodsname' => $price>0? $title:"[贈品] ".$title,
					'danwei' => $danwei,
					'bn' => $bn,
					'showcent'=>$showcent,
					'cent'=>$cent,
					'fzspecid'=>$fzspecid,
					'specid'=>$specid,
					'buysize'=> $buysize,
					'srcs' => $srcs,
					'accselect' => $accselect,
					'colorname' => $colorname,
					'pricesymbol' => $getsymbol,
					'picgid' => $subpicid? "_".$subpicid:"",
					);
					$orderList[$t]['orderdata'] = $var;
					$str.=ShowTplTemp($TempArr["list"],$var);
					
					if($getdata){
						if($getdata == $gid && $getfz == $fz){
							$redata = number_format($jine, $getpoint);
						}
					}
					
				}
			$tjine=$tjine+$jine;
			//預設金額
			$tw_tjine=$tw_tjine+$orijine;
			
			$tcent=$tcent+$cent;
			$kk++;
			$mainpro += $getisadd;
		
			
			$realtjine = $realtjine+$realjine;
		}
		if($tnums==$mainpro && !$isre && $CARTSTR){
				//header( "Location:cart.php?isre=1" );
				$str.=ShowTplTemp($TempArr["err1"],$var);
		}
		//$tjine=number_format($tjine,2,'.','');
		
		
		//$tjine = $tjine+$allejine;
		$oritjine=$tjine;
		
		/*電子折價券扣除機制*/
		$memberid = $_COOKIE['MEMBERID'];
		$membertypeid = $_COOKIE['MEMBERTYPEID'];
		$_POST[promocode] = addslashes($_POST[promocode]);
		if($_POST[promochk] || $promocode){
			
			$promocode = $_POST[promocode]? $_POST[promocode]:$promocode;
			
			foreach($gidslist AS $gva){
				$fsql->query( "select price0 from {P}_shop_con where id='{$gva}'" );
				if ( $fsql->next_record( ) ){
					$price0 = $fsql->f( "price0" );
					if($price0 == "" || $price0 == "0"){
						$canuse = true;
					}
				}
			}
			
			//可用於特價商品
			$canuse = true;

			$fsql->query( "select * from {P}_shop_promocode where code='{$promocode}'" );
			if ( $fsql->next_record( ) )
			{			
						$p_type = $fsql->f( "type" );
						$p_type_value = $fsql->f( "type_value" );
						$p_times = $fsql->f( "times" );
						$p_pertimes = $fsql->f( "pertimes" );
						$p_membertypeid = $fsql->f( "membertypeid" );
						$p_memberid = explode(",",$fsql->f( "memberid" ));
						$p_starttime = $fsql->f( "starttime" );
						$p_endtime = $fsql->f( "endtime" );
						$p_used_times = $fsql->f( "used_times" );
						$p_pricelimit = $fsql->f( "pricelimit" );
						
						$p_pricelimit=$getrate!="1"? round(($p_pricelimit*$getrate),$getpoint):$p_pricelimit;
						
						if( $p_pricelimit!="" && $p_pricelimit >0 && $oritjine<$p_pricelimit ){
							$promocode = "";
							$str.= "<script>alert('您的商品金額(".$oritjine.")未達 ".$getsymbol."".$p_pricelimit."元 ，無法使用此優惠碼');</script>";
							$nousecode = TRUE;
						}
						
						/*獲取會員使用次數*/
						if($p_pertimes){
							$tsql->query( "select count(*) from {P}_shop_promocode_log where memberid='{$memberid}' and code='{$promocode}'" );
							$tsql->next_record();
							$memberused = $tsql->f("count(*)");
							if( $memberused >= $p_pertimes){
								$str.= "<script>alert('您的優惠券碼已經超過使用次數!');</script>";
								$nousecode = TRUE;
							}
						}
						
				
				/*時間-會員測定，先測時間，再測會員類型或ID，以會員ID為優先，最後測定次數*/
				if( !$p_starttime || ($p_starttime>0 && $p_starttime < time()) && ($p_endtime>0 && $p_endtime > time()) && !$nousecode){
					
					if( $p_memberid[0] && $canuse){
						//限定會員ID是否相符
						if( in_array($memberid,$p_memberid) ){
							if($p_type == 1){
								//折扣
								//$tjine = $oritjine-$p_type_value + $yunfei;
								$m_p_type_value = $getrate!="1"? round(($p_type_value*$getrate),$getpoint):$p_type_value;
								$tjine = $oritjine-$m_p_type_value;
								$realtjine = $realtjine - $p_type_value;
								$promoprice = $p_type_value;
							}else{
								//打折
								$tjine = round($oritjine*$p_type_value);
								$twtjine = round($tw_tjine*$p_type_value);
								$realtjine = round($realtjine*$p_type_value);
								$promoprice = $tw_tjine - $twtjine;
								//$tjine = $tjine + $yunfei;
							}
								$promochk = "checked";
						}else{
							if(islogin()){
								/*會員ID不相符*/
								$promocode = "";
								$str.= "<script>alert('您輸入的電子折價券碼是特定優惠碼，很可惜您無法使用');</script>";
							}else{
								/*尚未登入*/
								$promocode = "";
								$str.= "<script>alert('請先登入會員再使用電子折價券');</script>";
							}
						}
						
					}elseif($p_membertypeid && $canuse){
						//限定會員類型是否相符
						if($p_membertypeid == $membertypeid){
							if($p_type == 1){
								//折扣
								//$tjine = $oritjine-$p_type_value + $yunfei;
								$m_p_type_value = $getrate!="1"? round(($p_type_value*$getrate),$getpoint):$p_type_value;
								$tjine = $oritjine-$m_p_type_value;
								$realtjine = $realtjine - $p_type_value;
								$promoprice = $p_type_value;
							}else{
								//打折
								$tjine = round($oritjine*$p_type_value);
								$twtjine = round($tw_tjine*$p_type_value);
								$realtjine = round($realtjine*$p_type_value);
								$promoprice = $tw_tjine - $twtjine;
								//$tjine = $tjine + $yunfei;
							}
								$promochk = "checked";
						}else{
							if(islogin()){
								/*會員類型不相符*/
								$promocode = "";
								$str.= "<script>alert('您輸入的電子折價券碼是特定優惠碼，很可惜您無法使用');</script>";
							}else{
								/*尚未登入*/
								$promocode = "";
								$str.= "<script>alert('請先登入會員再使用電子折價券');</script>";
							}
						}
						
					}elseif($canuse){
						//最後次數測定
						if($p_times>0 && $p_used_times<$p_times && $canuse){
							if($p_type == 1){
								//折扣
									//$tjine = $oritjine-$p_type_value + $yunfei;
									$m_p_type_value = $getrate!="1"? round(($p_type_value*$getrate),$getpoint):$p_type_value;
									$tjine = $oritjine-$m_p_type_value;
									$realtjine = $realtjine - $p_type_value;
									$promoprice = $p_type_value;
								}else{
								//打折
									$tjine = round($oritjine*$p_type_value);
									$twtjine = round($tw_tjine*$p_type_value);
									$realtjine = round($realtjine*$p_type_value);
									$promoprice = $tw_tjine - $twtjine;
									//$tjine = $tjine + $yunfei;
								}
									$promochk = "checked";
						}elseif($p_times>0 && $p_used_times>=$p_times && $canuse){
							//次數使用完畢
							$promocode = "";
							$str.= "<script>alert('很抱歉，這個電子折價券次數已經使用完畢');</script>";
						}else{
							//無次數限制
							if($p_type == 1){
								//折扣
									//$tjine = $oritjine-$p_type_value + $yunfei;
									$m_p_type_value = $getrate!="1"? round(($p_type_value*$getrate),$getpoint):$p_type_value;
									$tjine = $oritjine-$m_p_type_value;
									$realtjine = $realtjine - $p_type_value;
									$promoprice = $p_type_value;
								}else{
								//打折
									$tjine = round($oritjine*$p_type_value);
									$twtjine = round($tw_tjine*$p_type_value);
									$realtjine = round($realtjine*$p_type_value);
									$promoprice = $tw_tjine - $twtjine;
									//$tjine = $tjine + $yunfei;
								}
									$promochk = "checked";
									$str.= "<script>alert('折扣生效');</script>";
						}
					}else{
						$str.= "<script>alert('優惠券碼不能只用於特價商品，敬請見諒！');</script>";
						$nousecode = TRUE;
						$promocode = "";
					}
					
				}else{
					//該折扣過期
					$promocode = "";
					if(!$nousecode){
						$str.= "<script>alert('很抱歉，這個電子折價券使用期尚未開始或已過期');</script>";
					}
				}
				
			}else{
				//查無折扣
				if($promocode != ""){
					$ss=$promocode;
					$promocode = "";
					$str.= "<script>alert('很抱歉，這個電子折價券碼[ ".$ss." ]無效');</script>";
				}else{
					$str.= "<script>alert('請輸入電子折價券碼');</script>";
				}
			}
			
		}
		
		/*商品促銷-滿額贈送 2013-12-13 STR*/
		if($gettypecode){
			
			$promo_spec = $_GET["promospec"];
			$fsql->query( "select * from {P}_shop_promote where id='{$promoid}' and `promo_amount`<='{$tjine}'" );
			if ( $fsql->next_record( ) )
			{
				if($promotype == 2){
					/*折價*/
					$m_promo_con = $getrate!="1"? round(($promo_con*$getrate),$getpoint):$promo_con;
					$tjine = $tjine - $m_promo_con;
					$promoprice = $promoprice + $promo_con;
					$promolog = $promotype."|".$promo_con;
				}elseif($promotype == 3){
					/*折價券*/
					$promolog = $promotype."|".$promo_con;
				}elseif($promotype == 1){
					/*送贈品*/
					$promolog = $promotype."|".$promo_con."|".$promo_spec;
				}
				$promotype = 0;
			}
		}else{
				$nowtime = time();
				$fsql->query( "select * from {P}_shop_promote where `sdt`<'{$nowtime}' and `ndt`>'{$nowtime}' and `promo_amount`<='{$tjine}' order by sdt limit 0,1" );
				if ( $fsql->next_record( ) )
				{
					$promoid = $fsql->f( "id" );
					$promo_amount = $fsql->f( "promo_amount" );
					$promotype = $fsql->f( "groupid" );
					$promo_productid = $fsql->f( "promo_productid" );
					$promo_codeid = $fsql->f( "promo_codeid" );
					$promo_money = $fsql->f( "promo_money" );
					$range_add = $fsql->f( "range_add" );
					$probg1 = ROOTPATH.$fsql -> f ('probg1');
					$probg2 = ROOTPATH.$fsql -> f ('probg2');
					$probg3 = ROOTPATH.$fsql -> f ('probg3');
					if($promotype == 2){
						/*折價*/
						if($range_add){
							$gg = (INT)($tjine/$promo_amount);
							$promo_money = $promo_money*$gg;
						}
						
						$typecode = $promotype."-".$promo_money."-".$tjine."-".$promoid."-".$promocode;
						$promotypecode = URIAuthcode($typecode,"GO");
					}elseif($promotype == 3){
						/*折價券*/
						$typecode = $promotype."-".$promo_codeid."-".$tjine."-".$promoid."-".$promocode;
						$promotypecode = URIAuthcode($typecode,"GO");
					}elseif($promotype == 1){
						/*送贈品*/
						$typecode = $promotype."-".$promo_productid."-".$tjine."-".$promoid."-".$promocode;
						$promotypecode = URIAuthcode($typecode,"GO");
						##獲取尺寸
						list($pid, $pcolor) = explode("#",$promo_productid);
						$pcolor = "#".$pcolor;
						$msql->query("select id,size,stocks from {P}_shop_conspec where gid='{$pid}' and colorcode='{$pcolor}' order by id");
						$k = 0;
						while ( $msql->next_record( ) )
						{
							$sizeid=$msql->f('id');
							$size=$msql->f('size');
							$stock=$msql->f('stocks');
							if($stock>0 && !$setsize){ $setsize = $size; $setsizeid = $sizeid;}
								if($stock>0){
									$promoproduct .= '<option value="'.$size.'^'.$sizeid.'">'.$size.'</option>';
								}
								$k++;
						}
					}else{
						$promotype = 0;
					}
					
				}else{
					$promotype = 0;
				}
		}
		/*商品促銷-滿額贈送 2013-12-13 END*/
		/*多國運費*/
		if($getpriceid == 1){
		
				/*運費 刷卡*/
				$tsql->query("select dgs from {P}_shop_yun where id='1'");
				if($tsql->next_record()){
					
						$dgs = $tsql->f("dgs");
						list($setyunfei, $setyunprice) = explode("|",$dgs);
						$yunfei = countyunfeip( $tweight, $realtjine, $dgs );
						$oriyunfei = $yunfei;
						$yunfei = $getrate!="1"? round(($yunfei*$getrate),$getpoint):$yunfei;
						
				}
				/*運費 宅配*/
				$tsql->query("select dgs from {P}_shop_yun where id='2'");
				if($tsql->next_record()){
						$dgs = $tsql->f("dgs");
						list($setyunfei2, $setyunprice2) = explode("|",$dgs);
						$yunfei2 = countyunfeip( $tweight, $realtjine, $dgs );
						$oriyunfei2 = $yunfei2;
						$yunfei2 = $getrate!="1"? round(($yunfei2*$getrate),$getpoint):$yunfei2;
				}
		}else{
			$tsql->query("select dgs from {P}_shop_yun where spec='{$getpricecode}'");
			if($tsql->next_record()){
				$dgs = $tsql->f("dgs");
				
				list($setyunfei, $setyunprice) = explode("|",$dgs);
				$yunfei = countyunfeip( $tweight, $realtjine, $dgs, $getrate );
				$setyunfei2 = $setyunfei;
				$setyunprice2 = $setyunprice;
				$oriyunfei = $yunfei;
				$oriyunfei2 = $yunfei;
				$yunfei = $getrate!="1"? round(($yunfei*$getrate),$getpoint):$yunfei;
				$yunfei2 = $yunfei;
			}
		}
		
		if( AdminCheckModle()==true ){
			$tjine = $tjine ;
			$realtjine = $realtjine;
		}else{
			$tjine = $tjine + $yunfei;
			$realtjine = $realtjine + ($oriyunfei?$oriyunfei:$oriyunfei2);
		}
				/*帳戶餘額*/
				$memberid = $_COOKIE['MEMBERID'];
				$fsql->query( "select account from {P}_member where memberid='{$memberid}'" );
				if ( $fsql->next_record( ) )
				{
						$account = $fsql->f( "account" );
						//$account=$getrate!="1"? round(($account*$getrate),$getpoint):$account;
				}
				else
				{
						$account = "0";
				}
		/*餘額扣除機制*/
		if($account >= $realtjine){
			$disaccount = $realtjine;
			$tjine = 0;
		}else{
			$disaccount = $account;
			$m_account=$getrate!="1"? round(($account*$getrate),$getpoint):$account;
			$tjine = $tjine - $m_account;
		}
		
		/*扣除合購優惠*/
		$tjine = $tjine - $cutprice;
		
		$promopricewithadd = $promoprice + $cutprice;
		
		//帶入下一頁的值
		$geturlstr = $oriyunfei."-".$promocode."-".$promopricewithadd."-".$disaccount."-".$promolog;
		
		if($yunfei>0){ 
			$addyunfei = $oriyunfei2;
		}
		$geturlstr2 = $addyunfei."-".$promocode."-".$promopricewithadd."-".$disaccount."-".$promolog;
		
		$disjine = number_format($oritjine-$tjine, $getpoint);
		$nonfotmattjine=$tjine;
		$paytotal = $tjine;
		$tjine=number_format($tjine, $getpoint);
		
		//多國折價
		$multiaccount = $getrate!="1"? round(($account*$getrate),$getpoint):number_format($account, $getpoint);
		$multipromoprice = $getrate!="1"? round(($promoprice*$getrate),$getpoint):number_format($promoprice, $getpoint);
		$multidisaccount = $getrate!="1"? round(($disaccount*$getrate),$getpoint):number_format($disaccount, $getpoint);
		
		$account = number_format($account, $getpoint);
		$disaccount = number_format($disaccount, $getpoint);
		$promoprice = number_format($promoprice, $getpoint);
		$promoaddprice = number_format($cutprice, $getpoint);
		
		
		//運費顯示設定
		$orisetyunfei = $setyunfei;
		$orisetyunprice = $setyunprice;
		$orisetyunfei2 = $setyunfei2;
		$orisetyunprice2 = $setyunprice2;
		
		//幣值更換
		/*$setyunfei=$getrate!="1"? round(($setyunfei*$getrate),$getpoint):$setyunfei;
		$setyunprice=$getrate!="1"? round(($setyunprice*$getrate),$getpoint):$setyunprice;
		
		$setyunfei2=$getrate!="1"? round(($setyunfei2*$getrate),$getpoint):$setyunfei2;
		$setyunprice2=$getrate!="1"? round(($setyunprice2*$getrate),$getpoint):$setyunprice2;*/
		
		$setyunfei = number_format($setyunfei, $getpoint);
		$setyunprice = number_format($setyunprice, $getpoint);
		$setyunfei2 = number_format($setyunfei2, $getpoint);
		$setyunprice2 = number_format($setyunprice2, $getpoint);
		
		
		/*購物車儲存記錄*/
		$fsql->query("select * from {P}_shop_additems where memberid='$memberid'");
		while($fsql->next_record()){
			$gid = $fsql->f("gid");
			$subpicid = $fsql->f("subpicid");
			if($subpicid){
				$gid .= "-".$subpicid;
			}
			$src = $fsql->f("src");
			$srcs = dirname($src)."/sp_".basename($src);
			$goodsname = $fsql->f("goods");
			$colorname = $fsql->f("colorname");
			$jine = $fsql->f("jine");
			$nums = $fsql->f("nums");
			$fz = $fsql->f("fz");
			list($buysize) = explode("^",$fz);
			$addid = $fsql->f("id");
			$pricesymbol = $fsql->f("pricesymbol");
			$srcs = ROOTPATH.$srcs;
			$var=array(
				'gid' => $gid,
				'fz' => $fz,
				'srcs' => $srcs,
				'goodsname' => $goodsname,
				'bn' => $bn,
				'colorname' => $colorname,
				'buysize' => $buysize,
				'pricesymbol' => $pricesymbol,
				'jine' => $jine,
				'addid' => $addid,
				'nums' => $nums
			);
			$additems.=ShowTplTemp($TempArr["text"],$var);
			$addnums = $addnums+1;
		}
		/**/
		
		if( AdminCheckModle()==true ){
			$yunfei = 0;
			$setyunfei = $setyunfei2 = 0;
			$setyunprice = $setyunprice2 = 0;
		}
		
		
		$var=array(
			'oritjine' => $oritjine,
			'tjine' => $tjine,
			'nonfotmattjine' => $nonfotmattjine,
			'disjine' => $disjine,
			'showcent'=>$showcent,
			'tcent'=>$tcent,
			'yunfei' => $yunfei,
			'account' => $multiaccount? $multiaccount:$account,
			'disaccount' => $multidisaccount? $multidisaccount:$disaccount,
			'promoprice' => $multipromoprice? $multipromoprice:$promoprice,
			'promocode' => $promocode,
			'promochk' => $promochk,
			'return' => $return,
			'promoaddprice' => $promoaddprice,
			'pricesymbol' => $getsymbol,
			'setyunfei' => $setyunfei,
			'setyunprice' => $setyunprice,
			'setyunfei2' => $setyunfei2,
			'setyunprice2' => $setyunprice2,
			'addnums' => $addnums? $addnums:"0",
			'additems' => $additems
		 );
		
		
		if($kk>0){
			if($getpriceid == 1){
				if($_GET["payid"] == 2){
					$checkyun = "checked";
					$checkcard = $tjine? "":"disabled";
				}elseif($_GET["payid"] == 1){
					$checkyun = "";
					$checkcard = $tjine? "checked":"disabled";
				}else{
					$checkyun = $tjine? "":"checked";
					$checkcard = $tjine? "checked":"disabled";
				}
			}else{
				$checkyun = $tjine? "disabled":"checked";
				$checkcard = $tjine? "checked":"disabled";
			}
			
			
			$var["checkyun"]=$checkyun;
			$var["hidecheckyun"]=$checkyun=="disabled"? "style='display:none;'":"";
			$var["checkcard"] = $checkcard;
			
			$str.=ShowTplTemp($TempArr["m1"],$var);
			$orderTotalInfo=$var;
			
			$var["checkyun"]=$checkyun;
			$var["hidecheckyun"]=$checkyun=="disabled"? "style='display:none;'":"";
			$var["checkcard"] = $checkcard;
			
		 	if( AdminCheckModle()==true ){
				$str.=ShowTplTemp($TempArr["menu"],$var);
			}else{
				$str.=ShowTplTemp($TempArr["m2"],$var);
			}
		}else{
			$chkout = "cart.php";
			$tjine = number_format("0", $getpoint);
			
			$var=array(
				'addnums' => $addnums? $addnums:"0",
				'additems' => $additems
		 	);
			
			$str.=ShowTplTemp($TempArr["m3"],$var);
		}
		
				if($redata){
				$redata .= "^".$oritjine."^".$yunfei."^".$multidisaccount."^".$multipromoprice."^".$promoaddprice."^".$tjine."^".URIAuthcode($geturlstr,"GO")."^".URIAuthcode($geturlstr2,"GO")."^".$paytotal."^".$setyunprice."^".$setyunprice2;
				exit($redata);
		}
		
		
		$var=array(
			'promotype' => $promotype,
			'promotypecode' => $promotypecode,
			'promoproduct' => $promoproduct,
			'probg1' => $probg1,
			'probg2' => $probg2,
			'probg3' => $probg3,
			'return' => $return,
			'depromocode' => URIAuthcode($geturlstr,"GO"),
			'promocode2' => URIAuthcode($geturlstr2,"GO"),
			'tjine' => $act=="next"? $tjine:number_format($oritjine,$getpoint),
			'nonfotmattjine' => $nonfotmattjine,
			'pricesymbol' => $getsymbol,
		 	);
		$str.=ShowTplTemp($TempArr["end"],$var);
		$jdepromocode=URIAuthcode($geturlstr,"GO");

		$var=array(
			'sname' => '',
			'market' => '',
			'marketname' => '',
			'marketaddr' => '',
			'sphone' => '',
			'stel' => '',
			'scountry' => '臺灣',
			'scity' => '',
			'szone' => '',
			'spostal' => '',
			'sdetailaddr' => '',
			'saddr' => '',
			'spayid' => '',
			'store_service_num' => '',
			'mk_name' => '',
			'mk_mobi' => '',
			'shipinfo' => ''
		 	);

		$deliveryInfo=$var;
		
		$GLOBALS["addscript"] = $GLOBALS['GLOBALS']['SHOPCONF']['yahooCode'];
		
			$var=array(
				'showitem' => $showitem,
				'showcoupon' => $showcoupon,
				'showdel' => $showdel,
				'showchk' => $showchk,
				'chkout' => $chkout,
				'modilink' => $modilink,
				'moditext' => $moditext,
				'cross' => $cross,
				'showtitlea' => $showtitlea,
				'showtitleb' => $showtitleb,
				'chkstr' => $chkstr,
				'gostart' => $gostart,
				'back' => $back,
				'depromocode' => URIAuthcode($geturlstr,"GO"),
				'promocode2' => URIAuthcode($geturlstr2,"GO"),
			);


		$str = ShowTplTemp($str,$var);
		$str .= "<script>var orderList = " . json_encode($orderList, JSON_UNESCAPED_UNICODE) . ";</script>";
		$str .= "<script>var orderInfo = " . json_encode($orderTotalInfo, JSON_UNESCAPED_UNICODE) . ";</script>";
		$str .= "<script>var deliveryInfo = " . json_encode($deliveryInfo, JSON_UNESCAPED_UNICODE) . ";</script>";
		$str .= "<script>var jdepromocode = " . json_encode($jdepromocode, JSON_UNESCAPED_UNICODE) . ";</script>";
		return $str;

}

//字串加/解密
function URIAuthcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
    if( $operation == 'DECODE') $string=str_replace(array("-","_"), array('+','/'),$string);
     $ckey_length = 4;
     $key = md5($key ? $key : $GLOBALS['UC_KEY']);
     $keya = md5(substr($key, 0, 16));
     $keyb = md5(substr($key, 16, 16));
     $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
     $cryptkey = $keya.md5($keya.$keyc);
     $key_length = strlen($cryptkey);
     $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
     $string_length = strlen($string);
     $result = '';
     $box = range(0, 255);
     $rndkey = array();
     for($i = 0; $i <= 255; $i++) {
         $rndkey[$i] = ord($cryptkey[$i % $key_length]);
     }
     for($j = $i = 0; $i < 256; $i++) {
         $j = ($j + $box[$i] + $rndkey[$i]) % 256;
         $tmp = $box[$i];
         $box[$i] = $box[$j];
         $box[$j] = $tmp;
     }
     for($a = $j = $i = 0; $i < $string_length; $i++) {
         $a = ($a + 1) % 256;
         $j = ($j + $box[$a]) % 256;
         $tmp = $box[$a];
         $box[$a] = $box[$j];
         $box[$j] = $tmp;
         $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
     }
     if($operation == 'DECODE') {
         if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
             return substr($result, 26);
         } else {
             return '';
         }
     } else {
         return $keyc.str_replace(array("=","+","/"), array('','-','_'), base64_encode($result));
     }
 }

?>