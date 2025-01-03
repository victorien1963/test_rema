<?php
define( "ROOTPATH", "../" );
include( ROOTPATH."includes/common.inc.php" );
include( "language/".$sLan.".php" );
include( "includes/shop.inc.php" );

$act = $_POST['act'];

switch ( $act )
{
case "getName" :
		if( islogin() ){
			$str = $_COOKIE["MEMBERPNAME"];
			echo $str;
		}
		exit( );
		break;
case "delitems" :
		$addid = $_POST['addid'];
		$msql->query("DELETE FROM {P}_shop_additems WHERE id='{$addid}'");
		echo "OK";
		exit( );
		break;
case "additems" :
		//獲取貨幣、匯率
		if($sybtype){
			list($getsymbol,$getpricecode,$getrate,$getpoint,$getpriceid) = explode(",",$sybtype);
		}else{
			list($getsymbol,$getpricecode,$getrate,$getpoint,$getpriceid) = explode(",",getDefaultSyb());
		}
		$gid = $_POST['gid'];
		$fz = $_POST['fz'];
		
		$picgid = (INT)$_POST['picgid'];
		$memberid = $_COOKIE["MEMBERID"];
		$dtime = time();
		if($picgid !=""){
			$gid .= "-".$picgid;
		}
		
		$OLDCOOKIE = $_COOKIE["SHOPCART"];
		$array = explode( "#", $OLDCOOKIE );
		$tnums = sizeof( $array ) - 1;
		$CART = "";
		for ( $t = 0;	$t < $tnums;	$t++	)
		{
			$fff = explode( "|", $array[$t] );
			$oldgid = $fff[0];
			$oldacc = $fff[1];
			$oldfz = $fff[2];
			$olddisc = $fff[3];
			
			if ( stripos($oldgid,$gid)!==false && $fz == $oldfz )
			{
				list($buysize, $buyprice, $buyspecid) = explode("^",$oldfz);
				$msql->query( "SELECT * FROM {P}_shop_additems WHERE gid='$gid' and specid='$buyspecid'");
				if ( !$msql->next_record( ) )
				{
				//加入資料庫
				list($gids, $subpicid)=explode("-",$fff[0]);
				
				$acc = $oldacc;
				$fsql->query( "select * from {P}_shop_con where id='{$gids}'" );
				if ( $fsql->next_record( ) )
				{
					$bn = $fsql->f( "bn" );
					$title=$fsql->f('title');
					$danwei = $fsql->f( "danwei" );
					$src = $fsql->f( "src" );
					$srcs = dirname($src)."/sp_".basename($src);
					/*價格修正 2017-05-21*/
						$getp = $fsql->f('price');
						$buyprice = $getp;
					/*價格修正 END*/
					$price=isset($buyprice)? $buyprice:(INT)$fsql->f('price');
					$colorname = $fsql->f( "colorname" );
					$cent = $fsql->f( "cent" );
					$weight = $fsql->f( "weight" );
					$price = getmemberprice( $gids, $price );
					$jine = $price * $acc;
					$weight = $weight * $acc;
				}
				
				$multiprice = $getrate!="1"? round(($price*$getrate),$getpoint):$price;
				$multijine= $getrate!="1"? round(($jine*$getrate),$getpoint):$jine;
				
				
				$msql->query( "insert into {P}_shop_additems set
					`memberid`='{$memberid}',
					`gid`='{$gid}',
					`specid`='{$buyspecid}',
					`subpicid`='{$subpicid}',
					`bn`='{$bn}',
					`goods`='{$title}',
					`src`='{$src}',
					`colorname`='{$colorname}',
					`price`='{$price}',
					`weight`='{$weight}',
					`nums`='{$acc}',
					`danwei`='{$danwei}',
					`jine`='{$jine}',
					`dtime`='{$dtime}',
					`fz`='{$oldfz}',
					`pricesymbol`='{$getsymbol}',
					`multiprice`='{$multiprice}',
					`multijine`='{$multijine}'
				" );
				$addid = $msql->instid( );
				$srcs = ROOTPATH.$srcs;
				
				$str ='<div class="rema-col product-item border-top align-item-center" id="additem_'.$addid.'">
												<!---->
												<div class="rema-col l2 xss4">
													<img src="'.$srcs.'">
												</div>
												<div class="rema-col l3 xss8">
													<div  class="padding-left">			
														<div class=" font-s title">'.$title.'</div>
														<div class=" font-s mobile-height">'.$bn.'</div>
													</div>
												</div>
												<div class="rema-col l7 xss8 align-center">
													<div class="mobile-padding-left">
														<div class="rema-col l2 xss3 height-24  moble-text-left  font-s mobile-height">'.$colorname.'</div>
														<div class="rema-col l2 xss3 height-24  font-s mobile-height">'.$buysize.'</div>
														<div class="rema-col l3 xss6 height-24  font-s">
															<div class="quantity">
																<div class="quantity-icon">
																	<span class="quantity-minus icon icon-minus"></span>
																</div>
																<input class="quantity-input" value="'.$acc.'" type="text" name="search" 
																	   onkeyup="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,\'\')}else{this.value=this.value.replace(/\D/g,\'\')}" 
																	   onafterpaste="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,\'\')}else{this.value=this.value.replace(/\D/g,\'\')}" 
																	   onchange="quantity_chante($(this))"
																	   maxlength="2">
																<div class="quantity-icon">
																	<span class="quantity-plus icon icon-plus"></span>
																</div>
															</div>
														</div >
														<div class="rema-col l2 xss6 height-24 mobile-float-right">
															<div class="product-detail-word detail-align-center mobile-detail-price mobile-padding-top">
																<div class="font-s title">'.$getsymbol.' '.$jine.'</div>
															</div>
														</div>
														<div class="rema-col l2 xss4 icon-con height-24 ">
															<div class="icon-block mobile-none" onclick=\'javascript:addtocart("'.$gid.'","1","'.$oldfz.'");\' style="cursor: pointer;">
																<div class="icon icon-output order-close"></div>
															</div>
															<div class="mobile-block mobile-padding-top" onclick=\'javascript:addtocart("'.$gid.'","1","'.$oldfz.'");\' style="cursor: pointer;">
																<div class=" font-s">購物籃 &#10095;</div>
															</div>
														</div>
														<div class="rema-col l1 icon-con height-24 mobile-icon-close">
															<div class="icon-block deladditem"  id="add_'.$addid.'" style="cursor: pointer;">
																<div class="icon icon-close order-close"></div>
															</div>
														</div>
													</div>
												</div>
												<!---->
											</div>';
				}
				
			}
		}
		
		echo $str;
		exit( );
		break;
case "getSize" :
		$gid = $_POST["gid"];
		$inacc = $_POST["inacc"];
		//記錄會員的尺寸
		//身高
		$tall = $_POST["tall"];
		//體重
		$weight = $_POST["weight"];
		
		
		//胸圍
		$chest = round($_POST["chest"]/2.54,0);
		//腰圍
		$waist = round($_POST["waist"]/2.54,0);
		//臀圍
		$hips = round($_POST["hips"]/2.54,0);
		
		//記錄在COOKIE
		$sizechart = $tall."^".$weight."^".$_POST["chest"]."^".$_POST["waist"]."^".$_POST["hips"];
		setcookie( "SIZECHART", $sizechart );
		
		$memberid = $_COOKIE["MEMBERID"];
		if($memberid){
			if($chest){
				$scl .= ",chest='$_POST[chest]'";
			}
			if($waist){
				$scl .= ",waist='$_POST[waist]'";
			}
			if($hips){
				$scl .= ",hips='$_POST[hips]'";
			}
			$msql->query("UPDATE {P}_member SET tall='$tall',weight='$weight'$scl WHERE  memberid='{$memberid}'");
		}
		
		
		$setSizearr = array("","XS","S","M","L","XL","XXL");
		$fsql->query("select catpath,sizetype,mainsizetype,sizechart from {P}_shop_con where id='$gid'");
		if($fsql->next_record()){
			$mainsizetype = explode("|",$fsql->f( "mainsizetype" ));
			$sizetype = explode("|",$fsql->f( "sizetype" ));
			list($manwo, $cattype) = explode(":",$fsql->f( "catpath" ));
			$folder = $manwo=="0001"? "man":"women";
			$cattype = (INT)$cattype;
			$sizechart =$fsql->f( "sizechart" );
		}
		
		
		//載入商品尺寸表
		$groupid = $sizechart? $sizechart:(INT)$manwo;
		include_once("cache/".$groupid.".php");
		/*$fsql->query("select * from {P}_shop_size where groupid='$groupid' order by tall asc,weight asc");
		while($fsql->next_record()){
			list($tall_a, $tall_b) = explode("-",$fsql->f( "tall" ));
			list($weight_a, $weight_b) = explode("-",$fsql->f( "weight" ));
			list($chest_a, $chest_b) = explode("-",$fsql->f( "chest" ));
			list($waist_a, $waist_b) = explode("-",$fsql->f( "waist" ));
			list($hips_a, $hips_b) = explode("-",$fsql->f( "hips" ));
			$size = $fsql->f( "sizetype" );
			
			for($o=$tall_a; $o<=$tall_b; $o++){
				for($t=$weight_a; $t<=$weight_b; $t++){
					$showsize[$o][$t] = $showsize[$o][$t]? $showsize[$o][$t]."/".$size:$size;//身高體重
					if(!$showOnesize && $showOnesize !=""){
						$showOnesize = $showOnesize;//最小型號
					}
				}
			}
			if($chest_a){
				for($o=$chest_a; $o<=$chest_b; $o++){
					$showchest[$o] = $size;//胸圍
				}
			}
			if($waist_a){
				for($o=$waist_a; $o<=$waist_b; $o++){
					$showwaist[$o] = $size;//腰圍
				}
			}
			if($hips_a){
				for($o=$hips_a; $o<=$hips_b; $o++){
					$showhips[$o] = $size;//臀圍
				}
			}
		}*/
		
		
		
			//有幾則數據
			$count_chest = count($showchest);
			$count_waist = count($showwaist);
			$count_hips = count($showhips);
			
			//型態
			$twoTemp = false;
			$showone= "block";
			$showmulti= "none";
			
			$getmaincount = count($mainsizetype);
			$getcount = count($sizetype);
			if($folder == "women"){
				$mainsizetype[0] = str_replace("男","女",$mainsizetype[0]);
			}
			
			if($mainsizetype[0]==""){
				$mainsizetype[0] = "none.png";
			}
			
			$orishowbg = "../../base/templates/themes/default/css/IMG/".$folder."/".$mainsizetype[0];
			$showbg = $orishowbg;
			list($a,$mainsizetypename,$c) = explode("-",$mainsizetype[0]);
			
		
		//比對身高體重
		$c=1;
		$d=1;
		ksort($showsize);
		foreach($showsize AS $kk => $vv){
			if($tall<$kk && $c==1){
				$setsize = strtoupper($showOnesize);
			}
			//找身高
			if(!$setsize && $tall == $kk){
				ksort($vv);
				foreach($vv AS $ks => $vs){
					//找體重
					if($weight<$ks && $d==1){
						$setsize = strtoupper($vs);
					}
					if(!$setsize && $weight == $ks){
						$setsize = strtoupper($vs);
					}
					$d++;
				}
			}
			$c++;
		}
		
		if( stripos($setsize,"/") !== false){
			
			list($setsize, $gsubsetsize) = explode("/",$setsize);
			
			$orisetsize = $setsize;
			$setsize = $gsubsetsize;
			//採用雙版
			$twoTemp = true;
			$showone= "none";
			$showmulti= "block";
		}
		
			
		if($chest !="" && $inacc=="inchest"){
			//比對胸圍
			$g=1;
			ksort($showchest);
			foreach($showchest AS $kk => $vv){
				if($chest<$kk && $g==1){
					$subsetsize = strtoupper($vv);
				}
				
				//抓取與身高體重尺寸最大的胸圍數值
				$showpos = stripos(strtoupper($vv),"/".$setsize);
				$showsetsize = count($setsize);
				
				if($showpos !== FALSE){
					$lastchest = $kk;
					if(!$firchest){
						$firchest = $kk;
					}
				}
				
				if(!$subsetsize && $chest==$kk){
					if(substr_count($vv,"/") == 2){
						$gvv = substr($vv,1);
						$getvvarr = explode("/",$gvv);
						if($chest - $lastchest <=1){
							$getvv = $getvvarr[0];
						}else{
							$getvv = $getvvarr[1];
						}
					}else{
						$getvv = substr($vv,$showpos+1,$showsetsize);
					}
					$subsetsize = strtoupper($getvv);
					//最小胸圍數值
					if($g==1){
						$onechest = $kk;
					}
				}elseif(!$subsetsize && $chest>$kk && $g==$count_chest){
					//$subsetsize = strtoupper($showchest[$kk-1]);
					$subsetsize = "";
				}
				$g++;
			}
			
			
			//綜合比對
			if($setsize != $subsetsize && $subsetsize !=""){
				//身高體重與胸圍不符
				$xu_a = array_search($setsize,$setSizearr);//
				$xu_b = array_search($subsetsize,$setSizearr);//
					
				if(!$xu_a && !$xu_b){
					//沒有符合的胸圍
					$setsize = "";
					//不採用雙版
					$twoTemp = false;
					$showone= "block";
					$showmulti= "none";
					
				}elseif($xu_a<$xu_b){
					//胸圍比標準大
					//檢測輸入的胸圍，是否等於標準胸圍1吋
					if($chest-$lastchest <= 2  && $xu_b-$xu_a==1){
						$orisetsize = $setsize;
						$setsize = $subsetsize;
						//採用雙版
						$twoTemp = true;
						$showone= "none";
						$showmulti= "block";
					}else{
						$orisetsize ="";
						$setsize = "";
						//不採用雙版
						$twoTemp = false;
						$showone= "block";
						$showmulti= "none";
					}
				}elseif($xu_a>$xu_b){
					if($firchest-$chest <= 2  && $xu_a-$xu_b==1){
						$orisetsize = $subsetsize;
						$setsize = $setsize;
						//採用雙版
						$twoTemp = true;
						$showone= "none";
						$showmulti= "block";
					}else{
						$setsize = "";
						//不採用雙版
						$twoTemp = false;
						$showone= "block";
						$showmulti= "none";
					}
				}
			}elseif($subsetsize==""){
				$setsize = $orisetsize = "";
				//不採用雙版
				$twoTemp = false;
				$showone= "block";
				$showmulti= "none";
			}
		}elseif($waist !="" && $inacc=="inwaist"){
			
			//比對腰圍
			$g=1;
			
			ksort($showwaist);
			foreach($showwaist AS $kk => $vv){
				if($waist<$kk && $g==1){
					$subsetsize = strtoupper($vv);
				}
				//抓取與身高體重尺寸最大的腰圍數值
				$showpos = stripos(strtoupper($vv),"/".$setsize);
				$showsetsize = count($setsize);
				if($showpos !== FALSE){
					$lastwaist = $kk;
					if(!$firwaist){
						$firwaist = $kk;
					}
				}
				
				if(!$subsetsize && $waist==$kk){
					if(substr_count($vv,"/") == 2){
						$gvv = substr($vv,1);
						$getvvarr = explode("/",$gvv);
						if($chest - $lastchest <=1){
							$getvv = $getvvarr[0];
						}else{
							$getvv = $getvvarr[1];
						}
					}else{
						$getvv = substr($vv,$showpos+1,$showsetsize);
					}
					$subsetsize = strtoupper($getvv);
					//最小腰圍數值
					if($g==1){
						$onewaist = $kk;
					}
				}elseif(!$subsetsize && $waist>$kk && $g==$count_waist){
					//$subsetsize = strtoupper($showwaist[$kk-1]);
					$subsetsize = "";
				}
				$g++;
			}
			
			
			//綜合比對
			if($setsize != $subsetsize && $subsetsize !=""){
				//身高體重與腰圍不符
				$xu_a = array_search($setsize,$setSizearr);//
				$xu_b = array_search($subsetsize,$setSizearr);//
				if(!$xu_a && !$xu_b){
					//沒有符合的腰圍
					$setsize = "";
					//不採用雙版
					$twoTemp = false;
					$showone= "block";
					$showmulti= "none";
				}elseif($xu_a<$xu_b){
					//腰圍比標準大
					//檢測輸入的腰圍，是否等於標準腰圍1吋
					if($waist-$lastwaist <= 2  && $xu_b-$xu_a==1){
						$orisetsize = $setsize;
						$setsize = $subsetsize;
						//採用雙版
						$twoTemp = true;
						$showone= "none";
						$showmulti= "block";
					}else{
						$orisetsize ="";
						$setsize = "";
						//不採用雙版
						$twoTemp = false;
						$showone= "block";
						$showmulti= "none";
					}
				}elseif($xu_a>$xu_b){
					if($firwaist-$waist <= 2  && $xu_a-$xu_b==1){
						$orisetsize = $subsetsize;
						$setsize = $setsize;
						//採用雙版
						$twoTemp = true;
						$showone= "none";
						$showmulti= "block";
					}else{
						$setsize = "";
						//不採用雙版
						$twoTemp = false;
						$showone= "block";
						$showmulti= "none";
					}
				}
			}elseif($subsetsize==""){
				$setsize = $orisetsize = "";
				//不採用雙版
				$twoTemp = false;
				$showone= "block";
				$showmulti= "none";
			}
		}elseif($hips !="" && $inacc=="inhips"){
			//比對臀圍
			$g=1;
			ksort($showhips);
			foreach($showhips AS $kk => $vv){
				if($hips<$kk && $g==1){
					$subsetsize = strtoupper($vv);
				}
				//抓取與身高體重尺寸最大的臀圍數值
				$showpos = stripos(strtoupper($vv),"/".$setsize);
				$showsetsize = count($setsize);
				if($showpos !== FALSE){
					$lasthips = $kk;
					if(!$firhips){
						$firhips = $kk;
					}
				}
				
				if(!$subsetsize && $hips==$kk){
					if(substr_count($vv,"/") == 2){
						$gvv = substr($vv,1);
						$getvvarr = explode("/",$gvv);
						if($chest - $lastchest <=1){
							$getvv = $getvvarr[0];
						}else{
							$getvv = $getvvarr[1];
						}
					}else{
						$getvv = substr($vv,$showpos+1,$showsetsize);
					}
					$subsetsize = strtoupper($getvv);
					//最小臀圍數值
					if($g==1){
						$onehips = $kk;
					}
				}elseif(!$subsetsize && $hips>$kk && $g==$count_hips){
					//$subsetsize = strtoupper($showhips[$kk-1]);
					$subsetsize = "";
				}
				$g++;
			}
			//綜合比對
			if($setsize != $subsetsize && $subsetsize !=""){
				//身高體重與臀圍不符
				$xu_a = array_search($setsize,$setSizearr);//
				$xu_b = array_search($subsetsize,$setSizearr);//
				if(!$xu_a && !$xu_b){
					//沒有符合的臀圍
					$setsize = "";
					//不採用雙版
					$twoTemp = false;
					$showone= "block";
					$showmulti= "none";
				}elseif($xu_a<$xu_b){
					//臀圍比標準大
					//檢測輸入的臀圍，是否等於標準臀圍1吋
					if($hips-$lasthips <= 2  && $xu_b-$xu_a==1){
						$orisetsize = $setsize;
						$setsize = $subsetsize;
						//採用雙版
						$twoTemp = true;
						$showone= "none";
						$showmulti= "block";
					}else{
						$orisetsize ="";
						$setsize = "";
						//不採用雙版
						$twoTemp = false;
						$showone= "block";
						$showmulti= "none";
					}
				}elseif($xu_a>$xu_b){
					if($firhips-$hips <= 2  && $xu_a-$xu_b==1){
						$orisetsize = $subsetsize;
						$setsize = $setsize;
						//採用雙版
						$twoTemp = true;
						$showone= "none";
						$showmulti= "block";
					}else{
						$setsize = "";
						//不採用雙版
						$twoTemp = false;
						$showone= "block";
						$showmulti= "none";
					}
				}
			}elseif($subsetsize==""){
				$setsize = $orisetsize = "";
				//不採用雙版
				$twoTemp = false;
				$showone= "block";
				$showmulti= "none";
			}
		}
			
		$getsetsize = true;
		if($setsize == ""){
			$getsetsize = false;
			$setsize = "<span style='font-size: 24px;'>找不到合適的尺寸</span>";
			$showbg = "../../base/templates/themes/default/css/IMG/".$folder."/none.png";
		}
				/*手機檢測*/
				include_once(ROOTPATH."includes/Mobile_Detect.php");
				$detect = new Mobile_Detect();
				/**/
		
		if($showone != "none" || $sizetype[0]==""){
			if($orisetsize != ""){
				if($getsetsize == false){
					$setsize = $orisetsize;
					$showbg = $orishowbg;
					$showtwoone = false;
				}else{
					//$setsize = $orisetsize." / ".$setsize;
					$showtwoone = true;
				}
			}
			if($showtwoone == false){
				
				if($detect->isMobile() && !$detect->isTablet() || $_GET["mobi"]){
					$str.='<div class="measure-fin-con-one one-result" style="display:'.$showone123.'" >   
							<div class="measure-space-m1"></div>
							<div class="measure-fin-pic" style="background-image:url('.$showbg.');"></div>
							<div class="measure-fin-word" >
								<div class="measure-fin-word-title-m">建議您的尺寸是</div>
								<div class="measure-fin-word-size">'.$setsize.'</div>
								<div class="measure-again">重新尋找</div>
								<div class="measure-space-m"></div>
							</div>
						</div>';
					
				}else{
					$str.='
							<div class="size-count-fin-one" style="display: block;">
								<div class="mobile-fin-con">
									<div class="size-count-fin-item">
										<img class="size-fin-img" src="'.$showbg.'">
										<div class="size-fin-word-con">
											<div class="size-fin-word one-fin">
												<div class="size-fin">'.$setsize.'</div>
												<div class="size-line"></div>
												<div class="size-fin-para">
													<div class="font-s font-w-m">依據您的身材</div>
													<div class="font-s font-w-m">建議您選擇的尺寸</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>';
					/*$str.='<div class="measure-fin-con-one one-result" style="display:'.$showone123.'" >
						<div class="measure-fin-pic" style="background-image:url('.$showbg.');"></div>
						<div class="measure-fin-word" >
							<div class="measure-space-m"></div>
							<div class="measure-fin-word-title">建議您的尺寸是</div>
							<div class="measure-fin-word-size">'.$setsize.'</div>
							<div class="measure-again">重新尋找</div>
						</div>
					</div>';*/
				}
				
			}else{
				if(stripos($mainsizetypename,"png") !== false){
					$mainsizetypename = "";
				}
				list($a,$sizetypename,$c) = explode("-",$mainsizetype[0]);
				$getsizetypename = $sizetypename;
				
				if($getsizetypename == "緊身"){
					$sizetypenameA = "緊身";
					$sizetypenameB = "舒適";
				}elseif($getsizetypename == "修身"){
					$sizetypenameA = "修身";
					$sizetypenameB = "寬鬆";
				}elseif($getsizetypename == "寬鬆"){
					$sizetypenameA = "修身";
					$sizetypenameB = "寬鬆";
				}
				if($detect->isMobile() && !$detect->isTablet() || $_GET["mobi"]){
					$str.='<div class="measure-fin-con-one one-result"  >
								<div class="measure-space-m"></div>
								<div class="measure-m-two-result-con">
									
									<div class="measure-fin-pic" style="background-image:url('.$showbg.');"></div>
									<div class="measure-fin-word" >
										<div class="two-rest">
											<div>
												<div class="measure-fin-word-title-m"><strong>'.$sizetypenameA.'</strong>版型</div>
												<div class="measure-fin-word-size2">'.$orisetsize.'</div>
											</div>
											<div class="two-line"></div>
											<div>
												<div class="measure-fin-word-title-m"><strong>'.$sizetypenameB.'</strong>版型</div>
												<div class="measure-fin-word-size2">'.$setsize.'</div>
											</div>
										</div>
									</div>
								</div>
								<div class="measure-space-m"></div>
								<div class="measure-again">重新尋找</div>
								<div class="measure-space-m"></div>
							</div>';
				}else{
					$str .='<div class="size-count-fin-two">
								<div class=" float-l6">
									<div class="size-count-fin-item-two">
										<img class="size-fin-img" src="'.$showbg.'">
										<div class="size-fin-word-con-two">
											<div class="size-fin-word right-border">
												<div class="font-s"><span class="font-s font-w-l">'.$sizetypenameA.'</span>版型</div>
												<div class="font-s mobile-none">建議您的尺寸是</div>
												<div class="size-fin">'.$orisetsize.'</div>
											</div>
										</div>
									</div>
								</div>
								<div class=" float-l6">
									<div class="size-count-fin-item-two">
										<img class="size-fin-img" src="'.$showbg.'">
										<div class="size-fin-word-con-two">
											<div class="size-fin-word">
												<div class="font-s"><span class="font-s font-w-l">'.$sizetypenameB.'</span>版型</div>
												<div class="font-s mobile-none">建議您的尺寸是</div>
												<div class="size-fin">'.$setsize.'</div>
											</div>
										</div>
									</div>
								</div>
							</div>';
					/*$str .='<div class="measure-fin-con-one one-result" style="display:'.$showone123.'">
							<div class="measure-fin-pic" style="background-image:url('.$showbg.');"></div>
							<div class="measure-fin-word" >

								<div class="measure-fin-word-title">建議您的尺寸是</div>
								<div class="two-rest">
									<div>
										<div class="measure-fin-word-title"><strong>'.$sizetypenameA.'</strong>版型</div>
										<div class="measure-fin-word-size">'.$orisetsize.'</div>
									</div>
									<div class="two-line"></div>
									<div>
										<div class="measure-fin-word-title"><strong>'.$sizetypenameB.'</strong>版型</div>
										<div class="measure-fin-word-size">'.$setsize.'</div>
									</div>
								</div>
								
								<div class="measure-again">重新尋找</div>
							</div>
						</div>';*/
				}
			}
		}else{
			if($detect->isMobile() && !$detect->isTablet() || $_GET["mobi"]){
				$str .= '<div class="two-result" style="display:'.$showmulti123.'">
				<div class="measure-space-m1"></div>
				<div class="measure-fin-con">';
			}else{
			$str.='<div class="two-result" style="display:'.$showmulti123.'"  >
					<div class="measure-fin-con">';
				
			}
			
			foreach($mainsizetype AS $vvs){
				if($folder == "women"){
					$vvs = str_replace("男","女",$vvs);
				}
					$showbg = "../../base/templates/themes/default/css/IMG/".$folder."/".$vvs;
					list($a,$sizetypename,$c) = explode("-",$vvs);
					$getsizetypename = $sizetypename;
					if($detect->isMobile() && !$detect->isTablet() || $_GET["mobi"]){
						$str.='<div class="measure-fin-con-two">
									<div class="measure-fin-pic-2" style="background-image:url('.$showbg.');"></div>
									<div class="measure-fin-word">
										<div class="measure-fin-word-title-m"><strong>'.$sizetypename.'</strong>版型</div>
										<div class="measure-fin-word-size">'.$orisetsize.'</div>
									</div>
								</div>';
					}else{
						$str.='<div class="measure-fin-con-two">
							<div class="measure-fin-pic" style="background-image:url('.$showbg.');"></div>
							<div class="measure-fin-word">
								<div class="measure-fin-word-title-m"><strong>'.$sizetypename.'</strong>版型</div>
								<div class="measure-fin-word-title"><strong>'.$sizetypename.'</strong>的板型<br>建議您的尺寸是</div>
								<div class="measure-fin-word-size">'.$orisetsize.'</div>
							</div>
						</div><div class="two-line1"></div>';
						
					}
			}
			foreach($sizetype AS $vvs){
				if($folder == "women"){
					$vvs = str_replace("男","女",$vvs);
				}
					$showbg = "../../base/templates/themes/default/css/IMG/".$folder."/".$vvs;
					list($a,$sizetypename,$c) = explode("-",$vvs);
					
				if($getsizetypename == "緊身"){
					$sizetypename = "舒適";
				}elseif($getsizetypename == "修身"){
					$sizetypename = "寬鬆";
				}
				
					if($detect->isMobile() && !$detect->isTablet() || $_GET["mobi"]){
						$str.='
							<div class="measure-fin-con-two">
								<div class="measure-fin-pic-2" style="background-image:url('.$showbg.');"></div>
								<div class="measure-fin-word">
									<div class="measure-fin-word-title-m"><strong>'.$sizetypename.'</strong>版型</div>
									<div class="measure-fin-word-size">'.$setsize.'</div>
								</div>
							</div>';
					}else{
						$str.='<div class="measure-fin-con-two">
							<div class="measure-fin-pic" style="background-image:url('.$showbg.');"></div>
							<div class="measure-fin-word">
								<div class="measure-fin-word-title-m"><strong>'.$sizetypename.'</strong>版型</div>
								<div class="measure-fin-word-title"><strong>'.$sizetypename.'</strong>的板型<br>建議您的尺寸是</div>
								<div class="measure-fin-word-size">'.$setsize.'</div>
							</div>
						</div>';
					}
			}
					$str.='</div>
								<div class="measure-again">重新尋找</div>
								<div class="measure-space-m"></div>
								<div class="measure-space"></div>
							</div>';
		}
		
		echo $str;
		exit();
	  break;
case "seladdrchg" :
		$aid = $_POST["aid"];
		$memberid = $_COOKIE['MEMBERID'];
		if($aid == 0){
				$fsql->query( "select * from {P}_member where memberid='{$memberid}'" );
				if ( $fsql->next_record( ) )
				{
						$name = $fsql->f( "name" );
						$tel = $fsql->f( "tel" );
						$mov = $fsql->f( "mov" );
						$postcode = $fsql->f( "postcode" );
						$addr = $fsql->f( "addr" );
						$country = $fsql->f( "country" );
						$company = $fsql->f( "company" );
						$passcode = $fsql->f( "passcode" );
						$sex = $fsql->f( "sex" );
				}
		}else{
				$fsql->query( "select * from {P}_member_addr where memberid='{$memberid}' AND id='{$aid}'" );
				if ( $fsql->next_record( ) )
				{
						$name = $fsql->f( "name" );
						$tel = $fsql->f( "tel" );
						$mov = $fsql->f( "mov" );
						$postcode = $fsql->f( "postcode" );
						$addr = $fsql->f( "addr" );
						$country = $fsql->f( "country" );
						$company = $fsql->f( "company" );
						$passcode = $fsql->f( "passcode" );
						$sex = $fsql->f( "sex" );
				}
		}
			$addr_a=mb_substr( $addr,0,3,"utf-8" );
			$addr_b=mb_substr( $addr,3,3,"utf-8" );
			$addr_c=mb_substr( $addr,6,9999,"utf-8" );
		
		$str = "var M={N:'".$name."',T:'".$tel."',M:'".$mov."',P:'".$postcode."',A:'".$addr."',C:'".$country."',AA:'".$addr_a."',AB:'".$addr_b."',AC:'".$addr_c."',S:'".$sex."',IN:'".$cpmpany."',IU:'".$passcode."'}";
		echo $str;
		exit( );
		break;
case "itemtui" :
	$orderid = $_POST["orderid"];
	$tuilists = $_POST["tuilist"];
	

	$tuimums = $_POST["tuimums"];

	$tuinote = $_POST["tuinote"];
	
	foreach($tuilists AS $lists){
		$tuilist .= $tuilist? ",".$lists:$lists;
	}
	foreach($tuinote AS $notes){
		$tuireason .= $tuireason? ",".$notes:$notes;
	}
	$tuitime = time();
	
	if(!$tuilist){
		exit("操作有誤！請重新申請");
	}
	
	
	//exit("修改中-----退貨項目ID:".$tuilist."/理由:".$tuireason);
	
	$fsql->query( "UPDATE {P}_shop_order SET itemtui='1',tuitime='$tuitime',tui_reason='$tuireason' WHERE orderid='{$orderid}'" );
	$fsql->query( "UPDATE {P}_shop_orderitems SET itemtui='1',tuitime='$tuitime' WHERE orderid='{$orderid}' AND id IN($tuilist)" );
	/*退訂數量*/
	if($tuimums == ""){
		$fsql->query( "UPDATE {P}_shop_orderitems SET itemtuinums=nums WHERE orderid='{$orderid}' AND itemtui='1'" );
	}else{
		$getlist = explode(",",$tuilist);
		foreach($getlist AS $vv){
			$fsql->query( "UPDATE {P}_shop_orderitems SET itemtuinums='{$tuimums[$vv]}' WHERE orderid='{$orderid}' AND id='$vv'" );
		}
	}
	
	/**/
	$fsql->query( "SELECT * FROM {P}_shop_order WHERE orderid='{$orderid}'" );
	if($fsql->next_record()){
		$memname = $fsql->f("name");
		$OrderNo = $fsql->f("OrderNo");
		$paytype = $fsql->f("paytype");
		$paytotal = $fsql->f("paytotal");
		$membermail = $fsql->f("email");
		/**/
		$user = $fsql->f("user");
		$tel = $fsql->f("tel");
		$mobi = $fsql->f("mobi");
		$s_name = $fsql->f("s_name");
		$s_mobi = $fsql->f("s_mobi");
		$s_postcode = $fsql->f("s_postcode");
		$s_tel = $fsql->f("s_tel");
		$s_addr = $fsql->f("s_addr");
	}
	$fsql->query( "SELECT * FROM {P}_shop_orderitems WHERE orderid='{$orderid}' AND id IN($tuilist)" );
	while($fsql->next_record()){
		$goods = $fsql->f("goods");
		$bn = $fsql->f("bn");
		$colorname = $fsql->f("colorname");
		list($size) = explode("^",$fsql->f("fz"));
		/**/
		$price = $fsql->f("price");
		$nums = $fsql->f("nums");
		$jine = $fsql->f("jine");
		/**寄送退貨單使用**/
			$items_html .= '<tr bgcolor="#FFFFFF">';
			$items_html .= '<td width="75" align="center" bgcolor="#FFFFFF" >'.$bn.'</td>';
			$items_html .= '<td height="20" align="center" >'.$goods.'</td>';
			$items_html .= '<td width="80" align="center" bgcolor="#FFFFFF" >'.$price.'</td>';
			$items_html .= '<td width="50" align="center" bgcolor="#FFFFFF" >'.$nums.'</td>';
			$items_html .= '<td width="50" align="center" bgcolor="#FFFFFF" >'.$size.'-'.$colorname.'</td>';
			$items_html .= '<td width="80" align="center" bgcolor="#FFFFFF" >'.$jine.'</td>';
			$items_html .= '</tr>';
		/****/

		$items .= $items? "、".$bn." ".$goods."(".$colorname." ".$size.")":$bn." ".$goods."(".$colorname."/".$size.")";
	}
	
	include_once( ROOTPATH."includes/ebmail.inc.php" );
	$msql->query( "SELECT * FROM {P}_shop_mailtemp WHERE tid='6' AND status='1'");
	if($msql->next_record()){
		$smsg = $memname."|".$GLOBALS['GLOBALS']['CONF'][SiteName]."|".time()."|".$OrderNo."|".$items."|".$paytotal."|".$GLOBALS['GLOBALS']['CONF'][SiteHttp];
		$from = $GLOBALS['GLOBALS']['CONF'][SiteEmail];
		$tuito = $GLOBALS['GLOBALS']['CONF'][TuiEmail];
		
		
			$ordermessage .='<div id="shoporderdetail" style="width:100%;">';
			$ordermessage .='<div class="ordertitle">';
			$ordermessage .='<div style="float:right;font:bold 14px/35px 微軟正黑體,Verdana, Arial, Helvetica, sans-serif;">';
			$ordermessage .='訂單號：'.$OrderNo.' &nbsp; </div>';
			$ordermessage .='客戶退貨單</div><div class="tit" style="line-height:2.5em">訂購人訊息</div>';
			$ordermessage .='<table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#ddeeff" >';
			$ordermessage .='<tr >';
			$ordermessage .='<td height="25" align="center" valign="top" class="itemname">訂 購 人</td>';
			$ordermessage .='<td valign="top" bgcolor="#ffffff" >'.$memname.'</td>';
			$ordermessage .='<td align="center"  class="itemname">會員帳號</td>';
			$ordermessage .='<td height="25" valign="top" bgcolor="#ffffff" >'.$user.' </td>';
			$ordermessage .='</tr>';
			$ordermessage .='<tr >';
			$ordermessage .='<td width="80" height="25" align="center" valign="top" class="itemname">聯絡電話</td>';
			$ordermessage .='<td width="220" valign="top" bgcolor="#ffffff" >'.$tel.'</td>';
			$ordermessage .='<td width="80" align="center"  class="itemname">手機號碼</td>';
			$ordermessage .='<td height="25" valign="top" bgcolor="#ffffff" >'.$mobi.'</td>';
			$ordermessage .='</tr>';
			$ordermessage .='<tr >';
			$ordermessage .='<td height="25" align="center" valign="top" class="itemname">電子郵箱</td>';
			$ordermessage .='<td height="25" colspan="3" valign="top" bgcolor="#ffffff" >'.$membermail.'</td>';
			$ordermessage .='</tr>';
			$ordermessage .='</table>';
			$ordermessage .='<div class="tit" style="line-height:2.5em">收貨人訊息</div>';
			$ordermessage .='<table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#ddeeff" >';
			$ordermessage .='<tr >';
			$ordermessage .='<td width="80" height="25" align="center" class="itemname">收 貨 人</td>';
			$ordermessage .='<td width="220" valign="top" bgcolor="#FFFFFF" >'.$s_name.'</td>';
			$ordermessage .='<td width="80" align="center" valign="top"  class="itemname">手機號碼</td>';
			$ordermessage .='<td height="25" valign="top" bgcolor="#FFFFFF" >'.$s_mobi.'</td>';
			$ordermessage .='</tr>';
			$ordermessage .='<tr >';
			$ordermessage .='<td height="25" align="center" class="itemname">郵遞區號</td>';
			$ordermessage .='<td width="220" valign="top" bgcolor="#FFFFFF" >';
			$ordermessage .='<span class="itemname">'.$s_postcode.'</span></td>';
			$ordermessage .='<td align="center" valign="top"  class="itemname">聯絡電話</td>';
			$ordermessage .='<td height="25" valign="top" bgcolor="#FFFFFF" >'.$s_tel.'</td>';
			$ordermessage .='</tr>';
			$ordermessage .='<tr>';
			$ordermessage .='<td height="25" align="center" class="itemname">詳細地址</td>';
			$ordermessage .='<td height="25" colspan="3" valign="top" bgcolor="#FFFFFF" ><span class="itemname">'.$s_addr.'</span></td>';
			$ordermessage .='</tr>';
			$ordermessage .='</table>';
			$ordermessage .='<div class="tit" style="line-height:2.5em">退貨清單</div>';
			$ordermessage .='<table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#ddeeff" style="margin-bottom:10px">';
			$ordermessage .='<tr valign="top">';
			$ordermessage .='<td width="75" align="center"  class="itemname">商品編號</td>';
			$ordermessage .='<td height="25" align="center"  class="itemname" >商品名稱</td>';
			$ordermessage .='<td width="80" align="center"  class="itemname">單價 (元)</td>';
			$ordermessage .='<td width="50" align="center"  class="itemname">數量</td>';
			$ordermessage .='<td width="50" align="center"  class="itemname">單位</td>';
			$ordermessage .='<td width="80" align="center"  class="itemname">小計 (元)</td>';
			$ordermessage .='</tr>'.$items_html.'</table>';
			$ordermessage .='</div>';
			
		$mailbody = '<html><head>';
		$mailbody .='<title>客戶退貨單</title><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body bgcolor="#ffffff">';
		$mailbody .='<table style="display: inline-table;" border="0" cellpadding="0" cellspacing="0" width="800">';
		//$mailbody .='<tr><td><img name="n1_r1_c1" src="'.$GLOBALS['CONF']['SiteHttp'].'images/mail_top_tuiitem.png" width="800" height="208" alt=""></td></tr>';
		$mailbody .='<tr><td width="100%" valign="top" style="padding:0px;">';
		$mailbody .='<table width="800" border="0" align="left" cellpadding="0" cellspacing="0"><tr><td width="80" height="250">&nbsp;</td>';
		$mailbody .='<td width="640" style="font-family:\'微軟正黑體\',Century Gothic;vertical-align: top;font-size:17px;">'.$ordermessage.'</td>';
		$mailbody .='<td width="80" style="vertical-align: top;">&nbsp;</td></tr></table>';
		//$mailbody .='</td></tr><tr><td><img name="n1_r3_c1" src="'.$GLOBALS['CONF']['SiteHttp'].'images/mail_bt.png" width="800" height="240" alt=""></td></tr></table>';
		$mailbody .='</td></tr></table>';
		$mailbody .='</body></html>';
			

		//寄給管理員
		ebmail( $tuito, $from, "您的網站有一筆退貨申請，請儘速處理!", $mailbody );
		
		//寄給客戶
		shopmail( $membermail, $from, $smsg, "6" );							
	}
	

	echo "OK";
	exit();
	break;
case "chkphonenum" :
	$phonenum = urlencode($_POST["phonenum"]);
	$str = "N";
	$url = "http://www.cetustek.com.tw/PhoneBar.php?";
	$url .= "rentid=24311840&authkey=Cetus9Phone1API7&phonecode=".$phonenum;
	$gg = file_getc($url);
	if (substr($gg, 0,3) == pack("CCC",0xef,0xbb,0xbf)) {
       $gg = substr($gg, 3);
   }
	$getjson = json_decode($gg, true);
	$str = $getjson['isExist'];
	echo $str;
	exit();
	break;
case "delorder" :
	$orderid = $_POST["orderid"];

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
	$fsql->query( "select gid,nums,fz from {P}_shop_orderitems where orderid='{$orderid}'" );
	while ( $fsql->next_record( ) )
	{
		$gid = $fsql->f("gid");
		$acc = $fsql->f("nums");
		list($buysize, $buyprice, $buyspecid) = explode("^",$fsql->f("fz"));
		$tsql->query( "UPDATE {P}_shop_con SET kucun=kucun+{$acc} WHERE id='{$gid}'" );
		if($buyspecid){
			$tsql->query( "UPDATE {P}_shop_conspec SET stocks=stocks+{$acc} WHERE id='{$buyspecid}'" );
		}
	}
	/*加回庫存 END*/
	$fsql->query( "update {P}_shop_order set iftui='1' where orderid='{$orderid}'" );
	
	echo "OK";
	exit();
	break;
case "getcartnums" :
		//獲取貨幣、匯率
		if($sybtype){
			list($getsymbol,$getpricecode,$getrate,$getpoint,$getpriceid) = explode(",",$sybtype);
		}else{
			list($getsymbol,$getpricecode,$getrate,$getpoint,$getpriceid) = explode(",",getDefaultSyb());
		}
		$CARTSTR = $_COOKIE["SHOPCART"];
		$array=explode('#',$CARTSTR);
		$tnums=sizeof($array)-1;
		$tjine=0;
		$tacc=0;
		$kk=0;
		
		for($t=0;$t<$tnums;$t++){
				$fff=explode('|',$array[$t]);
				$gid=$fff[0];
				$acc=$fff[1];
				$fz=$fff[2];
				
				list($size) = explode("^",$fz);

				$fsql->query("select * from {P}_shop_con where id='$gid'");
				if($fsql->next_record()){
					$price=$fsql->f('price');
					$price=getMemberPrice($gid,$price);
					//幣值更換
					$price=$getrate!="1"? round(($price*$getrate),$getpoint):$price;
					$jine=$price*$acc;
					
					$src = $fsql->f('src');
					$srcs=dirname($src)."/sp_".basename($src);
					$srcs=ROOTPATH.$srcs;
					
					$items .= '
									<div class="cat-block">
										<div class="cart-item">
											<div class="cart-img rema-col s3"><img class="cart-img" src="'.$srcs.'"></div>
											<div class="cart-word rema-col s8">
												<div class="name">'.$fsql->f(title).'</div>
												<div class="size">('.$fsql->f(colorname).' '.$size.') X '.$acc.'</div>
												<div class="price font-w-s">'.$getsymbol.' '.number_format($jine, $getpoint).'</div>
											</div>
										</div>
										<div class="cart-line"></div>
									</div>
					';
				}
			$tjine=$tjine+$jine;
			$tacc=$tacc+$acc;
			$kk++;
		}
		$tjine=number_format($tjine, $getpoint);


			echo $items."^".$tacc."^".$tjine;

		
	break;
case "ordertui" :
		$orderid = $_POST['orderid'];

		$dtime = time( );
		$ip = $_SERVER['REMOTE_ADDR'];
		
		$msql->query( "select * from {P}_shop_order where orderid='{$orderid}'" );
		if ( $msql->next_record( ) )
		{
				$ifpay = $msql->f( "ifpay" );
				$ifyun = $msql->f( "ifyun" );
				$ifok = $msql->f( "ifok" );
				$iftui = $msql->f( "iftui" );
				$memberid = $msql->f( "memberid" );
				$paytotal = $msql->f( "paytotal" );
				$payid = $msql->f( "payid" );
				$paytype = $msql->f( "paytype" );
				$tcent = $msql->f( "totalcent" );
				$memname = $msql->f( "name" );
				$membermail = $msql->f( "email" );
				$OrderNo = $msql->f( "OrderNo" );
				$bz = $msql->f( "bz" );
				
				$logname = $_COOKIE['SYSNAME']? $_COOKIE['SYSNAME']:$memname;
				if ( $ifok == "1" )
				{
						echo "1003";
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
				include( ROOTPATH."includes/ebmail.inc.php" );
				if ( $ifpay == "1" )
				{
						/*echo "1001";
						exit( );*/
					if ( $memberid != "0" )
					{				
					$msql->query( "select memberid from {P}_member where memberid='{$memberid}'" );
					if ( $msql->next_record( ) )
					{
							$fsql->query( "update {P}_member set account=account+{$paytotal},buytotal=buytotal-{$paytotal} where memberid='{$memberid}'" );
							$fsql->query( "update {P}_shop_order set ifpay='0',paytime='0',bz='{$bz}\r\n\r\n會員操作退訂訂單，已退還款項至會員帳戶中' where orderid='{$orderid}'" );
							$fsql->query( "insert into {P}_member_buylist set 
								`buyfrom`='{$strModuleShop}',
								`memberid`='{$memberid}',
								`orderid`='{$orderid}',
								`payid`='0',
								`paytype`='{$strOrderUnPay}',
								`paytotal`='-{$paytotal}',
								`daytime`='{$dtime}',
								`ip`='{$ip}',
								`OrderNo`='{$OrderNo}',
								`logname`='{$logname}'
							" );
							//include_once( ROOTPATH."shop/includes/shop.inc.php" );
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
								$msql->query( "SELECT * FROM {P}_shop_mailtemp WHERE tid='3' AND status='1'");
									if($msql->next_record()){
								$smsg = $memname."|".$GLOBALS['GLOBALS']['CONF'][SiteName]."|".time()."|".$OrderNo."|".$paytype."|".$paytotal."|".$GLOBALS['GLOBALS']['CONF'][SiteHttp];
								$from = $GLOBALS['GLOBALS']['CONF'][SiteEmail];
								shopmail( $membermail, $from, $smsg, "3" );							
									}
									
							$fsql->query( "update {P}_shop_order set iftui='1' where orderid='{$orderid}'" );
							/*退還餘額付款20131004*/
							$fsql->query( "select memberid,disaccount from {P}_shop_order where orderid='{$orderid}' and disaccount>0" );
							if ( $fsql->next_record( ) )
							{
								$memberid = $fsql->f("memberid");
								$disaccount = $fsql->f("disaccount");
								$tsql->query( "UPDATE {P}_member SET account=account+{$disaccount} WHERE memberid='{$memberid}'" );
							}
							/*退還餘額付款*/
							/*加回庫存20130831*/
							$fsql->query( "select gid,nums,fz from {P}_shop_orderitems where orderid='{$orderid}'" );
							while ( $fsql->next_record( ) )
							{
								$gid = $fsql->f("gid");
								$acc = $fsql->f("nums");
								list($buysize, $buyprice, $buyspecid) = explode("^",$fsql->f("fz"));
								$tsql->query( "UPDATE {P}_shop_con SET kucun=kucun+{$acc} WHERE id='{$gid}'" );
								if($buyspecid){
									$tsql->query( "UPDATE {P}_shop_conspec SET stocks=stocks+{$acc} WHERE id='{$buyspecid}'" );
								}
							}
							/*加回庫存 END*/
							$fsql->query( "update {P}_shop_orderitems set iftui='1' where orderid='{$orderid}'" );
							
							/*POS 退訂單產生*/
							//postuiorder( $orderid , $OrderNo);
							
							echo "OK";
							exit( );
							}
						}else{
							echo "1001";
							exit( );
						}
						/**/
				}
								$msql->query( "SELECT * FROM {P}_shop_mailtemp WHERE tid='4' AND status='1'");
								if($msql->next_record()){
									$smsg = $memname."|".$GLOBALS['GLOBALS']['CONF'][SiteName]."|".time()."|".$OrderNo."|".$paytype."|".$paytotal."|".$GLOBALS['GLOBALS']['CONF'][SiteHttp];
									$from = $GLOBALS['GLOBALS']['CONF'][SiteEmail];
									shopmail( $membermail, $from, $smsg, "4" );							
								}
				$fsql->query( "update {P}_shop_order set iftui='1',ordertuitime='{$dtime}' where orderid='{$orderid}'" );
				
				/*退還餘額付款20131004*/
				$fsql->query( "select memberid,disaccount from {P}_shop_order where orderid='{$orderid}' and disaccount>0" );
				if ( $fsql->next_record( ) )
				{
					$memberid = $fsql->f("memberid");
					$disaccount = $fsql->f("disaccount");
					$tsql->query( "UPDATE {P}_member SET account=account+{$disaccount} WHERE memberid='{$memberid}'" );
				}
				/*退還餘額付款*/
				/*加回庫存20130831*/
						$fsql->query( "select gid,nums,fz from {P}_shop_orderitems where orderid='{$orderid}'" );
						while ( $fsql->next_record( ) )
						{
							$gid = $fsql->f("gid");
							$acc = $fsql->f("nums");
							list($buysize, $buyprice, $buyspecid) = explode("^",$fsql->f("fz"));
							$tsql->query( "UPDATE {P}_shop_con SET kucun=kucun+{$acc} WHERE id='{$gid}'" );
							if($buyspecid){
								$tsql->query( "UPDATE {P}_shop_conspec SET stocks=stocks+{$acc} WHERE id='{$buyspecid}'" );
							}
						}
				/*加回庫存*/
				$fsql->query( "update {P}_shop_orderitems set iftui='1' where orderid='{$orderid}'" );
				
				/*POS 退訂單產生*/
				//postuiorder( $orderid, $OrderNo );
				
				//api
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
case "getviewitem":
		$gid = $_POST['gid'];
		$OLDCOOKIE = $_COOKIE["VIEWITEM"];
		if ( $OLDCOOKIE == "" )
		{
			setcookie( "VIEWITEM", $gid, time( ) + 3600, "/" );
		}
		else
		{
			$array = explode(",",$OLDCOOKIE);
			if(!in_array($gid,$array)){
				$NEWCOOKIE = $OLDCOOKIE.",".$gid;
				setcookie( "VIEWITEM", $NEWCOOKIE, time( ) + 3600, "/" );
			}
		}
		$str = $NEWCOOKIE;
		echo $str;
		exit() ;
	break;
case "getsizenote":
		$gid = $_POST['gid'];

		$msql->query("select canshu from {P}_shop_con where id='{$gid}'");
		if ( $msql->next_record( ) )
		{
			$str= $msql->f('canshu');
		}
		echo $str;
		exit() ;
	break;
case "getcolor":
		$gid = $_POST['gid'];
		$str = "";
		$getcolor = array();
		$msql->query("select * from {P}_shop_conspec where gid='{$gid}' AND stocks>'0' order by id");
		while ( $msql->next_record( ) )
		{
			$getcolor[]=$msql->f('colorname');
			$color[]=$msql->f('colorcode');
			$iconsrc[]="../../".$msql->f('iconsrc');
		}
		$getconid[] = "0";
		$msql->query( "select id from {P}_shop_pages where gid='{$gid}' order by xuhao" );
		while ( $msql->next_record( ) )
		{
				$getconid[] = $msql->f( "id" );
		}
		$result = array_unique($getcolor);
		if($getcolor){
			$result = array_unique($getcolor);
			$color = array_unique($color);
			$iconsrc = array_unique($iconsrc);
			$i = 0;
			foreach($result AS $key => $colorname){
				if(!$setcolorname){ $setcolorname = $colorname;}
				$str .= "<img ref='".$colorname."' conid='".$getconid[$i]."' class='speclist_s' id='".str_replace("#","",$color[$key])."' src='$iconsrc[$key]' style='cursor:pointer' />";
				$i++;
			}
		}
		
		$str .= "|".str_replace("#","",$color[0])."|".$setcolorname;
		echo $str;
		exit() ;
	break;
case "getsize":
		$gid = $_POST['gid'];
		$msql->query("select id,size,stocks from {P}_shop_conspec where gid='{$gid}' order by id");
		$k = 0;
		while ( $msql->next_record( ) )
		{
			$sizeid=$msql->f('id');
			$size=$msql->f('size');
			$stock=$msql->f('stocks');
			$posproid=$msql->f('posproid');
			include_once( ROOTPATH."costomer.php");
			$data['posproid'] = $posproid;
			//if($posproid == "UAS001-BA,UAS001-GR,UAS001-WHS"){
				$stocks = $fsql->f("stocks");
			//}else{
			//	$stocks=get_stock_one(http_build_query($data));
			//}
			if($stock>0 && !$setsize){ $setsize = $size; $setsizeid = $sizeid;}
				if($stock>0){
					$str .= '<label class="size-nav-item font-w-s font-s selsize" id="'.$size.'-'.$sizeid.'">
                                <input type="hidden" name="size" value="'.$size.'-'.$sizeid.'" autocomplete="off"> '.$size.'
                            </label><a></a>';
				}
				$k++;
		}
		
		$str .= "|size_".$setsize."|".$setsizeid;
		echo $str;
		exit() ;
	break;
case "getsizemobi":
		$gid = $_POST['gid'];
		$msql->query("select id,size,stocks from {P}_shop_conspec where gid='{$gid}' order by id");
		$k = 0;
		$str='<option selected>請選擇尺寸</option>';
		while ( $msql->next_record( ) )
		{
			$sizeid=$msql->f('id');
			$size=$msql->f('size');
			$stock=$msql->f('stocks');
			if($stock>0 && !$setsize){ $setsize = $size; $setsizeid = $sizeid;}
				if($stock>0){
					$str .= '<option value="'.$size.'-'.$sizeid.'">'.$size.'</option>';
				}
				$k++;
		}
		
		$str .= "|size_".$setsize."|".$setsizeid;
		echo $str;
		exit() ;
	break;
case "getprice":
		$specid = $_POST['specid'];
		//獲取貨幣、匯率
		if($sybtype){
			list($getsymbol,$getpricecode,$getrate,$getpoint,$getpriceid) = explode(",",$sybtype);
		}else{
			list($getsymbol,$getpricecode,$getrate,$getpoint,$getpriceid) = explode(",",getDefaultSyb());
		}

		$msql->query("select sprice from {P}_shop_conspec where id='{$specid}'");
		if ( $msql->next_record( ) )
		{
			$str = $msql->f('sprice');
		}
		if($str>0 && $str !=""){
			$strs = $getrate!="1"? round(($str*$getrate),2):$str;
			echo $str."^".$getsymbol." ".number_format($strs,$getpoint);
		}else{
			echo "NULL";
		}
		
		exit() ;
	break;
case "contentpages" :
		$shopid = $_POST['shopid'];
		$str = "<li id='p_0' class='pages'>1</li>";
		$i = 2;
		$id = 0;
		$msql->query( "select id from {P}_shop_pages where gid='{$shopid}' order by xuhao" );
		while ( $msql->next_record( ) )
		{
				$id = $msql->f( "id" );
				$str .= "<li id='p_".$id."' class='pages'>".$i."</li>";
				$i++;
		}
		echo $str;
		exit( );
		break;
		
case "contentpagespic" :
		$shopid = $_POST['shopid'];
		
		$msql->query( "select src from {P}_shop_con where id='{$shopid}'" );
		if($msql->next_record()){
			$picsrc=$msql->f('src');
		}
		$str = "<li id='p_0' class='pages'><img src=\"../../".$picsrc."\" width='70' height='95' /></li>";
		$i = 2;
		$id = 0;
		$msql->query( "select id,src from {P}_shop_pages where gid='{$shopid}' order by xuhao" );
		while ( $msql->next_record( ) )
		{
				$id = $msql->f( "id" );
				$picsrc=$msql->f('src');
				$str .= "<li id='p_".$id."' class='pages'><img src=\"../../".$picsrc."\" width='70' height='95' /></li>";
				$i++;
		}
		echo $str;
		exit( );
		break;
case "getcontent" :
		$shopid = $_POST['shopid'];
		$subshopid = (INT)$_POST['subshopid'];
		if($subshopid != ""){
			$useid = $subshopid;
			$getori = $msql->getone( "select src,body,canshu,colorname from {P}_shop_con where id='{$shopid}'" );
			$getlansori = strTranslate("shop_con", $shopid, "body,canshu,colorname");
			$getori["body"] = $getlansori["body"]? $getlansori["body"]:$getori["body"];
			$getori["canshu"] = $getlansori["canshu"]? $getlansori["canshu"]:$getori["canshu"];
			$getori["colorname"] = $getlansori["colorname"]? $getlansori["colorname"]:$getori["colorname"];
		}else{
			$useid = $shopid;
		}
		
		$RP = $_POST['RP'];
		
				//$getsub = '<div class="item active"><ul class="row">';
				$s=1;
				$msql->query( "select src,body,canshu,colorname from {P}_shop_con where id='{$useid}'" );
				if ( $msql->next_record( ) )
				{
						$getlans = strTranslate("shop_con", $useid);
						
						$src = $msql->f( "src" );
						$body = $getlans['body']? $getlans['body']:$msql->f( "body" );
						$canshu = $getlans['canshu']? $getlans['canshu']:$msql->f( "canshu" );
						if($subshopid != ""){
							$colorname = $getori["colorname"]? $getori["colorname"]:$msql->f( "colorname" );
						}else{
							$colorname = $getlans['colorname']? $getlans['colorname']:$msql->f( "colorname" );
						}
						$body = $body? $RP.$body:$RP.$getori["body"];
						$canshu = $canshu? $RP.$canshu:$RP.$getori["canshu"];
						$src_a = dirname($msql->f( "src" ));
						$src_b = basename($msql->f( "src" ));
						$srcs = $src_a."/sp_".$src_b;
						$selthis = $s==1? "select":"";
                        /*$getmain = '<div class="tab-pane active" id="p-slide-show1-1">
                                    <img class="zoom" src="'.$RP.$srcs.'" data-zoom-image="'.$RP.$src.'" style="width: 100%;" />
                                </div>';
                        
                        $getsub .= '<li class="sliderpic col-md-2 col-sm-3 m-b-5 '.$selthis.'">
                                    <a href="#p-slide-show1-1"><img src="'.$RP.$srcs.'""></a>
                                </li>';*/
						$s++;
				}
				
				$n = 2;
				$msql->query( "select src from {P}_shop_pages where gid='{$useid}' order by xuhao" );
				while ( $msql->next_record( ) )
				{
						$subsrc = $msql->f( "src" );
						$subsrc_a = dirname($msql->f( "src" ));
						$subsrc_b = basename($msql->f( "src" ));
						$subsrcs = $subsrc_a."/sp_".$subsrc_b;
						
						if($n==2){
							$srcL = $subsrcs;
						}else{
							$getsub .= '<div class="item"><div class="product-small-img-item"><a href="#img'.$n.'"><img class="superbig-src" src="'.ROOTPATH.$subsrcs.'"></a></div></div>';
							$getzoom .= '<div class="item"><div class="product-superbig-img-item"><img loading="lazy" class="superbig-src" src="'.ROOTPATH.$subsrcs.'"></div></div>';
						}
						/*
						
						if($n == 12){
							        $getsub .='</ul>
                                    </div>
                                    <!-- END iTEM -->
                                    <!-- iTEM -->
                                    <div class="item">
                                    <ul class="row">';
						}*/
						
						/*$getmain .= '<div class="tab-pane" id="p-slide-show1-'.$n.'">
                                    <img class="zoom" src="'.$RP.$subsrcs.'" data-zoom-image="'.$RP.$subsrc.'" style="width: 100%;" />
                                </div>';
                        $getsub .= '<li class="sliderpic col-md-2 col-sm-3 m-b-5 ">
                                    <a href="#p-slide-show1-'.$n.'"><img src="'.$RP.$subsrcs.'""></a>
                                </li>';*/
                        
                        $n++;
				}
				//$getsub .= '</ul></div>';
		
		$getmain=str_replace("\r\n","",$getmain);
		$getmain=str_replace("\t","",$getmain);
		$getsub=str_replace("\r\n","",$getsub);
		$getsub=str_replace("\t","",$getsub);
		
		//$str="var P={M:'".$getmain."', S:'".$getsub."', B:'".$body."', C:'".$canshu."',N:'".$colorname."'}";
		$str="var P={M:'".$getmain."', SR:'".ROOTPATH.$src."', SL:'".ROOTPATH.$srcL."', S:'".$getsub."', T:'".$getzoom."', N:'".$colorname."'}";
		
		echo $str;
		exit( );
		break;
case "getcontentmobi" :
		$shopid = $_POST['shopid'];
		$subshopid = (INT)$_POST['subshopid'];
		if($subshopid != ""){
			$useid = $subshopid;
			$getori = $msql->getone( "select src,mbody,canshu from {P}_shop_con where id='{$shopid}'" );
		}else{
			$useid = $shopid;
		}
		
		$RP = $_POST['RP'];
		
		$wideS = '<div class="swiper-container" id="swiper-container'.$useid.'">
            <div class="swiper-wrapper" id="showzoom">';
		
				$s=1;
				$msql->query( "select src,mbody,canshu from {P}_shop_con where id='{$useid}'" );
				if ( $msql->next_record( ) )
				{
						$src = $msql->f( "src" );
						$body = $msql->f( "body" );
						$canshu = $msql->f( "canshu" );
						$body = $body? $RP.$body:$RP.$getori["mbody"];
						$src_a = dirname($msql->f( "src" ));
						$src_b = basename($msql->f( "src" ));
						$srcs = $src_a."/sp_".$src_b;
						$selthis = $s==1? "select":"";
                        
                        $getmain = '<div class="swiper-slide">
					                    <div class="swiper-zoom-container" data-swiper-zoom="2">
					                    	<img src="'.$RP.$srcs.'" data-src="'.$RP.$src.'" class="swiper-lazy">
					                    	<div class="swiper-lazy-preloader"></div>
					                    </div>
					                </div>';
						$s++;
				}
				
				$n = 2;
				$msql->query( "select src from {P}_shop_pages where gid='{$useid}' order by xuhao" );
				while ( $msql->next_record( ) )
				{
						$subsrc = $msql->f( "src" );
						$subsrc_a = dirname($msql->f( "src" ));
						$subsrc_b = basename($msql->f( "src" ));
						$subsrcs = $subsrc_a."/sp_".$subsrc_b;
						
                        $getmain .= '<div class="swiper-slide">
					                    <div class="swiper-zoom-container" data-swiper-zoom="2">
					                    	<img src="'.$RP.$subsrcs.'" data-src="'.$RP.$subsrc.'" class="swiper-lazy">
					                    	<div class="swiper-lazy-preloader"></div>
					                    </div>
					                </div>';
                        $n++;
				}
		$wideE = '            </div>
            <div class="swiper-pagination"></div>
        </div>';
		
		$getmain = $wideS.$getmain.$wideE;
		
		$getmain=str_replace("\r\n","",$getmain);
		$getmain=str_replace("\t","",$getmain);
		$getsub=str_replace("\r\n","",$getsub);
		$getsub=str_replace("\t","",$getsub);
		$body=urlencode($body);
		
		$str="var P={U:'".$useid."',M:'".$getmain."', B:'".$body."'}";
		
		echo $str;
		exit( );
		break;
case "getnewcomment" :
		$rid = $_POST['rid'];
		$RP = $_POST['RP'];
		$fsql->query( "select * from {P}_comment where iffb='1' and catid='11' and pid='0' and rid='{$rid}' order by dtime desc limit 0,1" );
		if ( $fsql->next_record( ) )
		{
				$id = $fsql->f( "id" );
				$memberid = $fsql->f( "memberid" );
				$title = $fsql->f( "title" );
				$body = $fsql->f( "body" );
				$dtime = $fsql->f( "dtime" );
				$uptime = $fsql->f( "uptime" );
				$cl = $fsql->f( "cl" );
				$lastname = $fsql->f( "lastname" );
				$pj1 = $fsql->f( "pj1" );
				$count = 0;
				$body = strip_tags( $body );
				if ( $memberid == "-1" )
				{
						$pname = $strGuest;
						$nowface = "1";
						$memberurl = "#";
				}
				else
				{
						$tsql->query( "select * from {P}_member where memberid='{$memberid}'" );
						if ( $tsql->next_record( ) )
						{
								$pname = $tsql->f( "pname" );
								$nowface = $tsql->f( "nowface" );
						}
						$memberurl = $RP."member/home.php?mid=".$memberid;
				}
				$dtime = date( "Y-m-d", $dtime );
				$title = csubstr( $title, 0, 20 );
				$body = csubstr( $body, 0, 120 )." ...";
				$link = $RP."comment/html/?".$id.".html";
				$face = $RP."member/face/".$nowface.".gif";
				$pjstr = shopstarnums( $pj1, $RP );
				$var = array(
						"title" => $title,
						"dtime" => $dtime,
						"pname" => $pname,
						"body" => $body,
						"count" => $count,
						"cl" => $cl,
						"link" => $link,
						"memberurl" => $memberurl,
						"lastname" => $lastname,
						"face" => $face,
						"pjstr" => $pjstr,
						"target" => $target
				);
				$Temp = loadcommontemp( "tpl_shop_comment.htm" );
				$TempArr = splittbltemp( $Temp );
				$str = showtpltemp( $TempArr['list'], $var );
		}
		echo $str;
		exit( );
		break;
case "zhichi" :
		$shopid = $_POST['shopid'];
		if ( !islogin( ) )
		{
				echo "L0";
				exit( );
		}
		$memberid = $_COOKIE['MEMBERID'];
		$mstr = "|".$memberid."|";
		$msql->query( "select tplog,zhichi,memberid from {P}_shop_con where id='{$shopid}'" );
		if ( $msql->next_record( ) )
		{
				$tplog = $msql->f( "tplog" );
				$zhichi = $msql->f( "zhichi" );
				$mid = $msql->f( "memberid" );
		}
		if ( strstr( $tplog, $mstr ) )
		{
				echo "L1";
				exit( );
		}
		else
		{
				$tplog = $tplog.$mstr;
		}
		$msql->query( "update {P}_shop_con set zhichi=zhichi+1,tplog='{$tplog}' where id='{$shopid}'" );
		$num = $zhichi + 1;
		echo $num;
		exit( );
		break;
case "fandui" :
		$shopid = $_POST['shopid'];
		if ( !islogin( ) )
		{
				echo "L0";
				exit( );
		}
		$memberid = $_COOKIE['MEMBERID'];
		$mstr = "|".$memberid."|";
		$msql->query( "select tplog,fandui,memberid from {P}_shop_con where id='{$shopid}'" );
		if ( $msql->next_record( ) )
		{
				$tplog = $msql->f( "tplog" );
				$fandui = $msql->f( "fandui" );
				$mid = $msql->f( "memberid" );
		}
		if ( strstr( $tplog, $mstr ) )
		{
				echo "L1";
				exit( );
		}
		else
		{
				$tplog = $tplog.$mstr;
		}
		$msql->query( "update {P}_shop_con set fandui=fandui+1,tplog='{$tplog}' where id='{$shopid}'" );
		$num = $fandui + 1;
		echo $num;
		exit( );
		break;
case "addfav" :
		$shopid = $_POST['shopid'];
		$url = $_POST['url'];
		if ( !islogin( ) )
		{
				echo "L0";
				exit( );
		}
		$memberid = $_COOKIE['MEMBERID'];
		$msql->query( "select title from {P}_shop_con where id='{$shopid}'" );
		if ( $msql->next_record( ) )
		{
				$title = $msql->f( "title" );
		}
		$msql->query( "select id from {P}_member_fav where url='{$url}' and memberid='{$memberid}'" );
		if ( $msql->next_record( ) )
		{
				echo "L1";
				exit( );
		}
		$msql->query( "insert into {P}_member_fav set title='{$title}',url='{$url}',memberid='{$memberid}'" );
		echo "OK";
		exit( );
		break;
case "chkkucun" :
		if($_POST['specid']){
			$gid = (INT)$_POST['gid'];
			$specid = $_POST['specid'];
			
			$CARTSTR = $_COOKIE["SHOPCART"];
			$array=explode('#',$CARTSTR);
			$tnums=sizeof($array)-1;
			$tjine=0;
			$tacc=0;
			$kk=0;
			for($t=0;$t<$tnums;$t++){
					$fff=explode('|',$array[$t]);
					$gid=$fff[0];
					$acc=$fff[1];
					$fz = $fff[2];
					list($buysize, $buyprice, $buyspecid) = explode("^",$fz);
					if($buyspecid == $specid){
						$nums = $acc+1;
					}
			}
			
		}else{
			list($gid,$specid) = explode("_",$_POST['gid']);
			$nums += $_POST['nums'];
		}
		
		
		
		if($specid){
			$msql->query( "select stocks from {P}_shop_conspec where `id`='{$specid}'" );
			if ( $msql->next_record( ) )
			{
				$kucun = $msql->f( "stocks" );
			}
		}else{
			$msql->query( "select kucun from {P}_shop_con where `id`='{$gid}'" );
			if ( $msql->next_record( ) )
			{
				$kucun = $msql->f( "kucun" );
			}
		}
		if ( $nums <= $kucun )
		{
				echo "OK";
				exit( );
		}
		else
		{
				echo "1000";
				exit( );
		}
		break;
case "getyunzone" :
		$pid = $_POST['pid'];
		$str = "";
		$msql->query( "select * from {P}_shop_yunzone where pid='{$pid}' order by xuhao" );
		while ( $msql->next_record( ) )
		{
				$zoneid = $msql->f( "id" );
				$zone = $msql->f( "zone" );
				$str .= "<option value='".$zoneid."'>".$zone."</option>";
		}
		echo $str;
		exit( );
		break;
case "getyunmethod" :
		$zoneid = $_POST['zoneid'];
		$zonestr = "|".$zoneid."|";
		$str = "";
		$msql->query( "select * from {P}_shop_yun where `zonestr` like '%".$zonestr."%' order by xuhao" );
		while ( $msql->next_record( ) )
		{
				$yunid = $msql->f( "id" );
				$yunname = $msql->f( "yunname" );
				$str .= "<option value='".$yunid."'>".$yunname."</option>";
		}
		echo $str;
		exit( );
		break;
case "getyunintro" :
		$yunid = $_POST['yunid'];
		$msql->query( "select * from {P}_shop_yun where id='{$yunid}'" );
		if ( $msql->next_record( ) )
		{
				$memo = $msql->f( "memo" );
		}
		$memo = nl2br( $memo );
		echo $memo;
		exit( );
		break;
case "getpaymethod" :
		if ( islogin( ) )
		{
				$str = "<option value='0'>".$strMemberAccountPay."</option>";
		}
		else
		{
				$str = "";
		}
		$msql->query( "select * from {P}_member_paycenter where ifuse='1' order by xuhao" );
		while ( $msql->next_record( ) )
		{
				$id = $msql->f( "id" );
				$pcenter = $msql->f( "pcenter" );
				$str .= "<option value='".$id."'>".$pcenter."</option>";
		}
		echo $str;
		exit( );
		break;
case "getpaymethodintro" :
		$payid = $_POST['payid'];
		$msql->query( "select * from {P}_member_paycenter where id='{$payid}'" );
		if ( $msql->next_record( ) )
		{
				$intro = $msql->f( "intro" );
		}
		$intro = nl2br( $intro );
		echo $intro;
		exit( );
		break;
case "accountyunfei" :
		$yunid = $_POST['yunid'];
		$tweight = $_POST['tweight'];
		$tjine = $_POST['tjine'];
		$msql->query( "select * from {P}_shop_yun where id='{$yunid}'" );
		if ( $msql->next_record( ) )
		{
				$yunname = $msql->f( "yunname" );
				$dinge = $msql->f( "dinge" );
				$yunfei = $msql->f( "yunfei" );
				$gs = $msql->f( "gs" );
				$dgs = $msql->f( "dgs" );
				$baojia = $msql->f( "baojia" );
				$baofei = $msql->f( "baofei" );
				$baodi = $msql->f( "baodi" );
		}
		if ( $dinge == "0" )
		{
				$yunfei = countyunfeiw( $tweight, $tjine, $gs );
		}
		if ( $dinge == "2" )
		{
				$yunfei = countyunfeip( $tweight, $tjine, $dgs );
		}
		$yunfei = number_format( $yunfei, 2, ".", "" );
		if ( $baojia == "1" )
		{
				$bao = $tjine * $baofei / 100;
				if ( $bao < $baodi )
				{
						$bao = $baodi;
				}
				$bao = number_format( $bao, 2, ".", "" );
		}
		else
		{
				$bao = "0.00";
		}
		echo "var J={Y:'".$yunfei."',B:'".$bao."'}";
		exit( );
		break;
case "getmemberaccount" :
		if ( islogin( ) )
		{
				$memberid = $_COOKIE['MEMBERID'];
				$fsql->query( "select account from {P}_member where memberid='{$memberid}'" );
				if ( $fsql->next_record( ) )
				{
						$account = $fsql->f( "account" );
						echo $account;
				}
				else
				{
						echo "0";
				}
		}
		else
		{
				echo "0";
		}
		exit( );
		break;
case "getmemberinfo" :
		if ( islogin( ) )
		{
				$memberid = $_COOKIE['MEMBERID'];
				$fsql->query( "select * from {P}_member where memberid='{$memberid}'" );
				if ( $fsql->next_record( ) )
				{
						$name = $fsql->f( "name" );
						$tel = $fsql->f( "tel" );
						$mov = $fsql->f( "mov" );
						$postcode = $fsql->f( "postcode" );
						$email = $fsql->f( "email" );
						$qq = $fsql->f( "qq" );
						$addr = $fsql->f( "addr" );
						$invoicename = $fsql->f( "invoicename" );
						$invoicenumber = $fsql->f( "invoicenumber" );
						$sex = $fsql->f( "sex" );
				}
				$fsql->query( "select * from {P}_shop_order where memberid='{$memberid}' order by dtime desc limit 0,1" );
				if ( $fsql->next_record( ) )
				{
						$s_name = $fsql->f( "s_name" );
						$s_tel = $fsql->f( "s_tel" );
						$s_mobi = $fsql->f( "s_mobi" );
						$s_postcode = $fsql->f( "s_postcode" );
						$s_addr = $fsql->f( "s_addr" );
						$s_qq = $fsql->f( "s_qq" );
						$yunzoneid = $fsql->f( "yunzoneid" );
						$yunid = $fsql->f( "yunid" );
						$s_sex = $fsql->f( "s_sex" );
				}
				$fsql->query( "select pid from {P}_shop_yunzone where id='{$yunzoneid}'" );
				if ( $fsql->next_record( ) )
				{
						$zonepid = $fsql->f( "pid" );
				}
				$str = "var M={N:'".$name."',T:'".$tel."',M:'".$mov."',P:'".$postcode."',E:'".$email."',Q:'".$qq."',A:'".$addr."',S:'".$sex."',IN:'".$invoicename."',IU:'".$invoicenumber."',";
				$str .= "SN:'".$s_name."',ST:'".$s_tel."',SM:'".$s_mobi."',SP:'".$s_postcode."',SA:'".$s_addr."',SQ:'".$s_qq."',SZ:'".$yunzoneid."',SZP:'".$zonepid."',SYU:'".$yunid."',SS:'".$s_sex."'}";
				echo $str;
				exit( );
		}
		break;
#送出訂單##送出訂單##送出訂單##送出訂單##送出訂單##送出訂單##送出訂單##送出訂單##送出訂單##送出訂單##送出訂單##送出訂單##送出訂單##送出訂單##送出訂單#
#送出訂單##送出訂單##送出訂單##送出訂單##送出訂單##送出訂單##送出訂單##送出訂單##送出訂單##送出訂單##送出訂單##送出訂單##送出訂單##送出訂單##送出訂單#
case "orderformsubmit" :
	//echo "呼叫了 john find";
		if($_COOKIE["SYSUSER"] == "wayhunt"){
			/*var_dump($_POST);
			exit();*/
		}
		
		$getcardid = $_POST["cardid"];
		setcookie( "CARDID", $getcardid, time( ) + 3600, "/" );


		include_once( ROOTPATH."includes/ebmail.inc.php" );
		$newordertitle = "您的網站[SITE]有一筆新訂單，請儘速處理!";
		
		//獲取貨幣、匯率
		if($sybtype){
			list($getsymbol,$getpricecode,$getrate,$getpoint,$getpriceid) = explode(",",$sybtype);
		}else{
			list($getsymbol,$getpricecode,$getrate,$getpoint,$getpriceid) = explode(",",getDefaultSyb());
		}
		
		
		$name = $_POST['s_name'];
		$tel = $_POST['s_tel'];
		$mobi = $_POST['s_mobi'];
		//$email = $_POST['email'];
		$email = $_COOKIE['MUSER'];
		//$qq = $_POST['qq'];
		$addr = $_POST['s_addr'];
		$postcode = $_POST['s_postcode'];
		$addrnote = $_POST['addrnote'];
		$country = $_POST['s_country'];
		
		/*超商*/
		$shipinfo = $_POST['shipinfo'];
		$markets = $_POST['markets'];
		$marketname = $_POST['marketname'];
		$store_service_num = $_POST['store_service_num'];
		$marketaddr = $_POST['marketaddr'];
		$mk_name = $_POST['mk_name'];
		$mk_mobi = $_POST['mk_mobi'];
		
		/**/
		if($shipinfo == 2){
			$s_name = $mk_name;
			//$name = $mk_name;
			$s_tel = "";
			$tel = "";
			// $s_addr = $markets." / ".$marketname." / ".$store_service_num." / ".$marketaddr; //address, markets-store, number --- CHANGED ORDER ---
			$s_addr = $marketaddr." / ".$markets." / ".$marketname." / ".$store_service_num; //address, markets-store, number
			$addr = $s_addr;
			$s_mobi = $mk_mobi;
			//$mobi = $mk_mobi;
			$s_postcode = "";
			$postcode = "";
		}else{
			$s_name = $name;
			$s_tel = $tel;
			$s_addr = $addr;
			$s_mobi = $mobi;
			$s_postcode = $postcode;
		}
		
		if($s_name==""){
			exit("請輸入收貨人姓名");
		}
		if($s_addr==""){
			exit("請輸入收貨人地址");
		}
		if($s_mobi==""){
			exit("請輸入收貨人手機號碼");
		}
		
		/*2014-04-11 新增*/
		$receiptinfo = $_POST['receiptinfo'];
		if( $receiptinfo == "1" ){
			if( $_POST["receipt_info_first"] == "1" ){
				$contribute = $_POST["postpayname"]."|".$_POST["postpay"];
			}else{
				$contribute = "自行輸入|".$_POST["postnum"];
			}
		}elseif( $receiptinfo == "2" ){
			if( $_POST["receipt_info_second"] == "1" ){
				$integrated = "銳鎷會員載具|".$_COOKIE['MUSER']."|EG0029";
			}elseif( $_POST["receipt_info_second"] == "2" ){
				$integrated = "手機載具|".$_POST['mobicode']."|3J0002";
			}elseif( $_POST["receipt_info_second"] == "3" ){
				$integrated = "自然人憑證載具|".$_POST['cdcode']."|CQ0001";
			}
		}elseif( $receiptinfo == "3" ){
			$invoicename = $_POST['invoicename'];
			if($invoicename == "發票抬頭"){ $invoicename =""; }
			$invoicenumber= $_POST['invoicenumber'];
			if($invoicenumber == "統一編號"){ $invoicenumber =""; }
		}
		/**/
		
		//$sex = $_POST['Gender'];
		
		//$s_name = $_POST['s_name'];
		//$s_tel = $_POST['s_tel'];
		//$s_addr = $_POST['s_addr'];
		//$s_mobi = $_POST['s_mobi'];
		//$s_postcode = $_POST['s_postcode'];
		//$s_qq = $_POST['s_qq'];
		//$s_sex = $_POST['s_Gender'];
		
		/**/
		$urlstr = URIAuthcode($_POST['urlstr']);
		
		list($newyunfei,$promocode,$promoprice,$disaccount,$promolog) = explode("-",$urlstr);
		
		
		$source = "0";
		
		//2018-06-05 SLOB ADD
		if( AdminCheckModle()==true ){
			$source = $_COOKIE["SOURCE"];
			$sourceyun = $_COOKIE["SOURCEYUN"];
			$sourceyunfei = $_COOKIE["SOURCEYUNFEI"];
			$sourcediscount = $_COOKIE["SOURCEDISCOUNT"];
			$newyunfei = $sourceyunfei? $sourceyunfei:"0";
			//額外折扣
			$promoprice += $sourcediscount? $sourcediscount:"0";
			
			if($source==""){
				$source = "0";
			}
		}
		
		//exit($urlstr);
		
		//多國折價
		$multipromoprice = $getrate!="1"? round(($promoprice*$getrate),$getpoint):$promoprice;
		$multidisaccount = $getrate!="1"? round(($disaccount*$getrate),$getpoint):$disaccount;
		
		
		/**/
		
		/*商品促銷*/
		list($promotype, $promo_con, $promo_spec) = explode("|",$promolog);
		if($promotype == 3){
			/*寄發折價券*/
			###貨到付款會於本支程式寄發電子折價券，線上刷卡則是刷卡後的那支程式###
		}elseif($promotype == 1){
			/*送贈品*/
			list($pid) = explode("#",$promo_con);
			list($psize, $psizeid) = explode("^",$promo_spec);
			
			$_COOKIE['SHOPCART'] = $_COOKIE['SHOPCART'].$pid."|1|".$psize."^0^".$psizeid."#";
		}
		
		/**/
		
		$bz = htmlspecialchars( $_POST['bz'] );
		$zoneid = $_POST['zoneid'];
		
		$yunmethod = $sourceyun? $sourceyun:$_POST['yunmethod'];
		
		$payid = $_POST['payid'];
		$nomemberorder = $GLOBALS['SHOPCONF']['NoMemberOrder'];
		$CARTSTR = $_COOKIE['SHOPCART'];
		$array = explode( "#", $CARTSTR );
		$tnums = sizeof( $array ) - 1;
		if ( $tnums < 1 )
		{
				echo "1000";
				exit( );
		}
		if ( $nomemberorder != "1" && !islogin( ) )
		{
				echo "1005";
				exit( );
		}
		/*if ( $zoneid == "" || $zoneid == "0" )
		{
				echo "1001";
				exit( );
		}*/
		/*if ( $yunmethod == "" || $yunmethod == "0" )
		{
				echo "1004";
				exit( );
		}*/
		if ( $payid == "" )
		{
				echo "1002";
				exit( );
		}
		if ( $payid == "0" && !islogin( ) )
		{
				echo "1003";
				exit( );
		}
		if ( islogin( ) )
		{
				$memberid = $_COOKIE['MEMBERID'];
				$user = $_COOKIE['MUSER'];
		}
		else
		{
				$memberid = 0;
		}
		
		/*2017-03-03 限制60秒內僅能送出訂單一次*/
		$dtime = time();
		$nowtm = $dtime-60;
		if( AdminCheckModle()!=true ){
			$getneworder = $msql->getone("SELECT * FROM {P}_shop_order WHERE memberid='$memberid' AND (dtime>'$nowtm' OR dtime='$dtime')");
			if($getneworder != ""){
				exit("1007");//60秒內僅能送出訂單一次
			}
		}
		
		/**/
		
		$tjine = 0;
		$tweight = 0;
		$kk = 0;
		
		
		for ( $t = 0;	$t < $tnums;	$t++	)
		{
				$fff = explode( "|", $array[$t] );
				//$gid = $fff[0];
				list($gid, $subpicid)=explode("-",$fff[0]);
				$acc = $fff[1];
				$fz = $fff[2];
				$disc=$fff[3];
				//list($buycolorname, $buysize, $buyprice, $buyspecid, $getisadd) = explode("^",$fz);
				//list($discat, $distype, $disnum, $disrate, $disprice) = explode("^",$disc);
				list($buysize, $buyprice, $buyspecid) = explode("^",$fz);
				//$addtitle = " (".$buycolorname."-".$buysize.")";
				//$addnote = $getisadd? "[加購] ":"";
				
				$fsql->query( "select * from {P}_shop_con where id='{$gid}'" );
				if ( $fsql->next_record( ) )
				{
						$bn = $fsql->f( "bn" );
						$title=$addnote.$fsql->f('title').$addtitle;
						$danwei = $fsql->f( "danwei" );
						/*價格修正 2017-05-21*/
							$getp = $fsql->f('price');
							$buyprice = $getp;
						/*價格修正 END*/
						$price=isset($buyprice)? $buyprice:(INT)$fsql->f('price');
					/*促銷商品計算 START*/
						/*$discat-1-單商品折扣。2-滿幾件統一售價*/
						if( $discat == 1 ){
							$disprice = isset($buyprice)? ceil($buyprice)*$acc:ceil($price*$disrate)*$acc;
							$jine=$disprice;
							$price=isset($buyprice)? $buyprice:ceil($price*$disrate);
						}elseif( $discat == 2 ){
							/*訂購數量滿規則*/
							$discpro[$distype][] = $acc."|".$disnum."|".$disprice."|".$price;
							$jine = "";
						}else{
							$jine=$price*$acc;
						}
					/*促銷商品計算 END*/
						$colorname = $fsql->f( "colorname" );
						$cent = $fsql->f( "cent" );
						$weight = $fsql->f( "weight" );
						$price = getmemberprice( $gid, $price );
						//$jine = $price * $acc;
						$weight = $weight * $acc;
						$cent = accountcent( $cent, $price ) * $acc;
						$title = $price>0? $title:"[贈品] ".$title;
						$arr[] = array(
								"memberid" => $memberid,
								"gid" => $gid,
								"bn" => $bn,
								"goods" => $title,
								"colorname" => $colorname,
								"price" => $price,
								"weight" => $weight,
								"nums" => $acc,
								"danwei" => $danwei,
								"jine" => $jine,
								"cent" => $cent,
								"fz" => $fz,
								"isdisc" => $isdisc,
								"discat" => $discat,
								"distype" => $distype,
								"disnum" => $disnum,
								"disrate" => $disrate,
								"disprice" => $disprice,
								"subpicid" => $subpicid,
								"lantype" => $lantype,
						);
						$items .= $bn." ".$title."(".$colorname."/".$buysize."/".$acc.") ";
				}


				
				/*核對庫存並扣除庫存數量20130831*/
				//核對即時庫存數量是否足夠
				if($buyspecid){
					$fsql->query( "SELECT size,colorname,stocks,posproid FROM {P}_shop_conspec WHERE id='{$buyspecid}'" );
					$fsql->next_record();
					$size = $fsql->f("size");
					$stocks = $fsql->f("stocks");
					$posproid = $fsql->f("posproid");
					/*這裡抓 API實際庫存*/
					//1. API先抓回這商品的實際庫存並將實際庫存寫回 _shop_conspec資料表(在API裡面做)
					//抓到的實際庫存一樣使用 $stocks 變數，若有啥問題抓不到就沿用原本 資料表的 $stocks數據
					include_once( ROOTPATH."costomer.php");
					$data['posproid'] = $posproid;
					$Arr = array("UAS001-BA,UAS001-GR,UAS001-WHS","UAS001-BA,UAS001-GR,UAS001-WHM","UAS001-BA,UAS001-GR,UAS001-WHL", 
								"URS002-BA,URS002-LGS", 
								"UCS004-GR,UCS004-DBS",  "UCS004-GR,UCS004-DBM", "UCS004-GR,UCS004-DBL", 
								"UCS005-BA,UCS005-DBS",  "UCS005-BA,UCS005-DBL",
								"UCS006-RD,UCS006-DBS",  "UCS006-RD,UCS006-DBM", "UCS006-RD,UCS006-DBL");
					if (!in_array($posproid, $Arr)){
						$stocks=get_stock_one(http_build_query($data));
						if( $stocks == -1 ){
							exit("1008");
						}
					}
					/**/
					if($stocks<$acc){
						echo $gid."_".$fz."_".$title."(".$size."-".$colorname.") 已被搶購，\r\n庫存不足(".$stocks.")無法購買，\r\n我們將會刪除這項產品的訂購資料並返回購物車，\r\n請您見諒。";
						exit();
					}
				}else{
					$fsql->query( "SELECT title,kucun FROM {P}_shop_con WHERE id='{$gid}'" );
					$fsql->next_record();
					$kucun = $fsql->f("kucun");
					if($kucun<$acc){
						echo $gid."_0_".$title." 已被搶購，\r\n庫存不足無法購買，\r\n我們將會刪除這項產品的訂購資料並返回購物車，\r\n請您見諒。";
						exit();
					}
				}
				//扣除庫存20130831
				$fsql->query( "UPDATE {P}_shop_con SET kucun=kucun-{$acc} WHERE id='{$gid}'" );
				if($buyspecid){
					$fsql->query( "UPDATE {P}_shop_conspec SET stocks=stocks-{$acc} WHERE id='{$buyspecid}'" );
				}
				/**/
				$tjine = $tjine + $jine;
				
				$tcent = $tcent + $cent;
				$tweight = $tweight + $weight;
				$kk++;
		}
		
		/*多件統一價促銷品計算*/
		
		if($discpro){
			$arr_pro = array_keys($discpro);
			$dnums = sizeof($arr_pro)-1;
			for($d=0;$d<=$dnums;$d++){
				$edis=$discpro[$arr_pro[$d]];
				$enums = sizeof($edis)-1;
				//迴圈取得資料
				$eacc=$enum=$eprice=$oriprice=$totalacc=$leaveejine=$oldoriprice="";
					for($k=0;$k<=$enums;$k++){
						list($eacc[], $enum[], $eprice[], $oriprice[]) = explode("|",$edis[$k]);
					}
				//加總所有件數
					foreach($eacc AS $keys=>$values){
						$totalacc += $values;
					}
				$enewacc = floor($totalacc/$enum[0]);
				//計算促銷價
				$ejine = $eprice[0]*$enewacc;
				//取得剩餘件數計算原價，並以價格高的為優先計價
				$eleaveacc = $totalacc%$enum[0];
				if( $eleaveacc > 0 ){
					rsort($oriprice);//價格由大到小排序
					
					for($s=0;$s<$eleaveacc;$s++){
						$leaveejine += $oriprice[$s]? $oriprice[$s]:$oldoriprice;//加總剩餘費用
						$oldoriprice = $oriprice[$s];
					}
				}
				$allejine += $ejine+$leaveejine;
			}
			
		}
		/**/
		$allejine = $tjine;
				
		//$tjine = $tjine+$allejine;

		//$tjine = number_format( $tjine, 2, ".", "" );
		$msql->query( "select * from {P}_shop_yun where id='{$yunmethod}'" );
		if ( $msql->next_record( ) )
		{
				$yunname = $msql->f( "yunname" );
				$dinge = $msql->f( "dinge" );
				$yunfei = $msql->f( "yunfei" );
				$gs = $msql->f( "gs" );
				$dgs = $msql->f( "dgs" );
				$baojia = $msql->f( "baojia" );
				$baofei = $msql->f( "baofei" );
				$baodi = $msql->f( "baodi" );
		}
		if ( $dinge == "0" )
		{
				$yunfei = countyunfeiw( $tweight, $tjine, $gs );
		}
		if ( $dinge == "2" )
		{
				$yunfei = countyunfeip( $tweight, $tjine, $dgs );
		}
		if ( $baojia == "1" )
		{
				$bao = $tjine * $baofei / 100;
				if ( $bao < $baodi )
				{
						$bao = $baodi;
				}
		}
		else
		{
				$bao = "0";
		}
		
		
		/*2021 06 使用台幣，寄送香港，重新計算運費 $newyunfei*/
		if( $newyunfei && ($country == "香港" || $country == "Hong Kong" || $country == "澳門" || $country == "澳门" || $country == "Macao") ){
			$rate = $dsql->getone( "SELECT rate FROM {P}_base_currency WHERE `ifshow`='1' AND  pricecode='HKD' limit 0,1");
			$HKrate = $rate["rate"];
			//語言選擇的地區與寄送地區匯率不同
			if($getrate != $HKrate){
				$tsql->query("select dgs from {P}_shop_yun where spec='HKD'");
				if($tsql->next_record()){
					$dgs = $tsql->f("dgs");
					$realtjine = $tjine - $promoprice;
					$newyunfei = countyunfeip( $tweight, $realtjine, $dgs,$HKrate );
				}
			}
		}elseif($newyunfei && ($country == "馬來西亞" || $country == "马来西亚" || $country == "Malaysia")){
			$rate = $dsql->getone( "SELECT rate FROM {P}_base_currency WHERE `ifshow`='1' AND  pricecode='MYR' limit 0,1");
			$MArate = $rate["rate"];
			//語言選擇的地區與寄送地區匯率不同
			if($getrate != $MArate){
				$tsql->query("select dgs from {P}_shop_yun where spec='MYR'");
				if($tsql->next_record()){
					$dgs = $tsql->f("dgs");
					$realtjine = $tjine - $promoprice;
					$newyunfei = countyunfeip( $tweight, $realtjine, $dgs, $MArate );
				}
			}
		}
		/*END $newyunfei*/
		
	
		$ordertotal = $tjine + $newyunfei - $disaccount - $promoprice;

		if( $ordertotal==$newyunfei && ($disaccount+$promoprice)<=0 ){
			echo "1006";
			exit();
		}
		
		$ordertotal = number_format( $ordertotal, 2, ".", "" );

		/*扣除帳戶餘額*/
		if($disaccount){
			$fsql->query( "update {P}_member set account=account-{$disaccount},buytotal=buytotal+{$ordertotal} where memberid='{$memberid}'" );
		}
		
		
		if ( $payid != "0" )
		{
				$msql->query( "select * from {P}_member_paycenter where id='{$payid}'" );
				if ( $msql->next_record( ) )
				{
						$pcenter = $msql->f( "pcenter" );
						$pcentertype = $msql->f( "pcentertype" );
						$pcenterintro = $msql->f( "intro" );
				}
		}
		else
		{
				$pcenter = $strMemberAccountPay;
		}
		
		$ip = $_SERVER['REMOTE_ADDR'];
		
		/*多國費用*/
		$multiprice = $getrate!="1"? round(($tjine*$getrate),$getpoint):$tjine;
		$multiyunfei = $getrate!="1"? round(($newyunfei*$getrate),$getpoint):$newyunfei;
		
	    $getmember = $msql->getone("SELECT * FROM {P}_member WHERE  memberid='{$memberid}'");
	    $member_tall = $getmember['tall'];
	    $member_weight = $getmember['weight'];
	    $member_chest = $getmember['chest'];
	    $member_waist = $getmember['waist'];
	    $member_hips = $getmember['hips'];
	    
	    if($member_tall == ""){
	    	list($member_tall, $member_weight, $member_chest, $member_waist, $member_hips) = explode("^",$_COOKIE["SIZECHART"]);
	    }
	    
	    $sizerecord = "身高:".$member_tall."/體重:".$member_weight."/胸圍:".$member_chest."/腰圍:".$member_waist."/臀圍:".$member_hips."";
	    
	    //銷售人員
		$sales = $_COOKIE["SYSUSER"];
		
		$msql->query( "insert into {P}_shop_order set
		`memberid`='{$memberid}',
		`user`='{$user}',
		`name`='{$name}',
		`tel`='{$tel}',
		`mobi`='{$mobi}',
		`qq`='{$qq}',
		`email`='{$email}',
		`s_name`='{$name}',
		`s_sex`='{$sex}',
		`s_tel`='{$tel}',
		`s_addr`='{$addr}',
		`addrnote`='{$addrnote}',
		`s_postcode`='{$postcode}',
		`s_mobi`='{$mobi}',
		`s_qq`='{$qq}',
		`s_time`='{$s_time}',
		`goodstotal`='{$tjine}',
		`yunzoneid`='{$zoneid}',
		`yunid`='{$yunmethod}',
		`yuntype`='{$yunname}',
		`yunifbao`='{$baojia}',
		`yunbaofei`='{$bao}',
		`yunfei`='{$newyunfei}',
		`totaloof`='{$ordertotal}',
		`totalcent`='{$tcent}',
		`totalweight`='{$tweight}',
		`payid`='{$payid}',
		`paytype`='{$pcenter}',
		`paytotal`='{$ordertotal}',
		`iflook`='0',
		`ifyun`='0',
		`ifpay`='0',
		`ifok`='0',
		`iftui`='0',
		`ip`='{$ip}',
		`dtime`='{$dtime}',
		`paytime`='0',
		`yuntime`='0',
		`bz`='{$bz}',
		`items`='{$items}',
		`invoicename`='{$invoicename}',
		`invoicenumber`='{$invoicenumber}',
		`disaccount` = '{$disaccount}',
		`promoprice` = '{$promoprice}',
		`promocode` = '{$promocode}',
		`promolog` = '{$promolog}',
		`contribute` = '{$contribute}',
		`integrated` = '{$integrated}',
		`multiprice` = '{$multiprice}',
		`multiyunfei` = '{$multiyunfei}',
		`pricesymbol` = '{$getsymbol}',
		`country` = '{$country}',
		`sizerecord` = '{$sizerecord}',
		`uptime`='{$dtime}',
		`buysource`='{$source}',
		`source`='{$source}',
		`sales`='{$sales}',
		`shipinfo`='{$shipinfo}'
		" );
		$orderid = $msql->instid( );
		
		/*紀錄電子折價券使用*/
		if($promocode){
			$getcode = $fsql->getone( "SELECT * FROM {P}_shop_promocode WHERE code='{$promocode}'" );
			$used_orderid = $getcode[used_orderid]? $getcode[used_orderid].",".$orderid:$orderid;
			$used_userid = $getcode[used_userid]? $getcode[used_userid].",".$memberid:$memberid;
			$fsql->query( "update {P}_shop_promocode set used_times=used_times+1,used_orderid='{$used_orderid}',used_userid='{$used_userid}' where code='{$promocode}'" );
			$fsql->query( "INSERT INTO {P}_shop_promocode_log SET memberid='{$memberid}',cid='{$getcode[id]}',code='{$promocode}'" );
		}
		
		//$OrderNo = $orderid + 100000;
		/*訂單規則民國3碼+月2碼+日期2碼+序號5碼*/
		$OrderNo =(date("Y",$dtime)-1911).date("m",$dtime).date("d",$dtime).str_pad($orderid,5,'0',STR_PAD_LEFT);
		
		$msql->query( "update {P}_shop_order set OrderNo='{$OrderNo}' where orderid='{$orderid}'" );

		$inums = sizeof( $arr );
		$productlist = '';
		for ( $i = 0;	$i < $inums;	$i++	)
		{
				$memberid = $arr[$i]['memberid'];
				$gid = $arr[$i]['gid'];
				$bn = $arr[$i]['bn'];
				$goods = $arr[$i]['goods'];
				$colorname = $arr[$i]['colorname'];
				$price = $arr[$i]['price'];
				$weight = $arr[$i]['weight'];
				$nums = $arr[$i]['nums'];
				$danwei = $arr[$i]['danwei'];
				$jine = $arr[$i]['jine'];
				$cent = $arr[$i]['cent'];
				$fz = $arr[$i]['fz'];
				$subpicid = $arr[$i]['subpicid'];
				$lantype = $arr[$i]['lantype'];
				list($buysize, $buyprice, $buyspecid) = explode("^",$fz);
				/*$color = $fsql->getone( "SELECT colorname FROM {P}_shop_conspec where id='{$buyspecid}'" );
				$fzs = $buysize."-".$color[colorname];*/
				$fzs = $buysize."-".$colorname;
				
				$isdisc = $arr[$i]['isdisc'];
						$discat = $arr[$i]['discat'];
						$distype = $arr[$i]['distype'];
						$disnum = $arr[$i]['disnum'];
						$disrate = $arr[$i]['disrate'];
						$disprice = $arr[$i]['disprice'];
						$desinger = $arr[$i]['desinger'];
						
				/*多國費用*/
				$multiprice = $getrate!="1"? round(($price*$getrate),$getpoint):$price;
				$multijine = $getrate!="1"? round(($jine*$getrate),$getpoint):$jine;
				
				/*2017-03-25*/
				$totalmultiprice += $multiprice;
				$totalmultijine += $multijine;
		
				/*if($nums>1){
					for($o=1;$o<=$nums;$o++){
						$msql->query( "insert into {P}_shop_orderitems set
							`memberid`='{$memberid}',
							`orderid`='{$orderid}',
							`gid`='{$gid}',
							`subpicid`='{$subpicid}',
							`bn`='{$bn}',
							`goods`='{$goods}',
							`colorname`='{$colorname}',
							`price`='{$price}',
							`weight`='{$weight}',
							`nums`='1',
							`danwei`='{$danwei}',
							`jine`='{$price}',
							`cent`='{$cent}',
							`ifyun`='0',
							`iftui`='0',
							`dtime`='{$dtime}',
							`yuntime`='0',
							`fz`='{$fz}',
							`pricesymbol`='{$getsymbol}',
							`multiprice`='{$multiprice}',
							`multijine`='{$multiprice}',
							`lantype`='{$lantype}'
						" );
					}
				}else{*/
					
						$msql->query( "insert into {P}_shop_orderitems set
							`memberid`='{$memberid}',
							`orderid`='{$orderid}',
							`gid`='{$gid}',
							`subpicid`='{$subpicid}',
							`bn`='{$bn}',
							`goods`='{$goods}',
							`colorname`='{$colorname}',
							`price`='{$price}',
							`weight`='{$weight}',
							`nums`='{$nums}',
							`danwei`='{$danwei}',
							`jine`='{$jine}',
							`cent`='{$cent}',
							`ifyun`='0',
							`iftui`='0',
							`dtime`='{$dtime}',
							`yuntime`='0',
							`fz`='{$fz}',
							`pricesymbol`='{$getsymbol}',
							`multiprice`='{$multiprice}',
							`multijine`='{$multijine}',
							`lantype`='{$lantype}'
						" );
				//}
		/**寄送訂單使用**/
			$items_html .= '<tr bgcolor="#FFFFFF"><td width="75" align="center" bgcolor="#FFFFFF" >'.$bn.'</td><td height="20" align="center" >'.$goods.'</td><td width="80" align="center" bgcolor="#FFFFFF" >'.$price.'</td><td width="50" align="center" bgcolor="#FFFFFF" >'.$nums.'</td><td width="50" align="center" bgcolor="#FFFFFF" >'.$fzs.'</td><td width="80" align="center" bgcolor="#FFFFFF" >'.$jine.'</td></tr>';
		/****/

		}

		/*2017-03-25*/
		/*if($multidisaccount-$totalmultijine > 0){
			$totalmultiprice = 0;
			$totalmultiyunfei = $multidisaccount-$totalmultijine-$multiyunfei;
		}else{
			$totalmultiprice = $totalmultijine - $multidisaccount - $multipromoprice;
			$totalmultiyunfei = $multiyunfei;
		}*/
		if($multidisaccount-$totalmultijine-$multiyunfei > 0){
			$totalmultiprice = 0;
			$totalmultiyunfei = 0;
		}else{
			if($multidisaccount-$multiyunfei>0){
				$totalmultiprice = $totalmultijine - ($multidisaccount-$multiyunfei);
				$totalmultiyunfei = 0;
			}else{
				$totalmultiprice = $totalmultijine - $multidisaccount;
				$totalmultiyunfei = $multiyunfei;
			}
			
			$totalmultiprice = $totalmultiprice - $multipromoprice;
			
		}
		

		$msql->query( "UPDATE {P}_shop_order SET `multiprice`='{$totalmultiprice}',`multiyunfei`='{$totalmultiyunfei}' WHERE orderid='{$orderid}'" );
		
		/**寄送訂單使用**/
				$msql->query( "select * from {P}_shop_yunzone where id='{$zoneid}'" );
				if ( $msql->next_record( ) )
				{
								$zonepid = $msql->f( "pid" );
								$zonestr = $msql->f( "zone" );
								if ( $zonepid != "0" )
								{
												$fsql->query( "select * from {P}_shop_yunzone where id='{$zonepid}'" );
												if ( $fsql->next_record( ) )
												{
																$pzone = $fsql->f( "zone" );
																$zonestr = $pzone." ".$zonestr;
												}
								}
				}
		/****/
		if ( islogin( ) )
		{
			/*$fsql->query( "update {P}_member set 
			`name`='{$name}',
			`tel`='{$tel}',
			`mov`='{$mobi}',
			`email`='{$email}',
			`qq`='{$qq}' where memberid='{$memberid}'" );*/
			/*$fsql->query( "update {P}_member set 
			`name`='{$name}',
			`mov`='{$mobi}',
			`sex`='{$sex}',
			`addr`='{$addr}',
			`invoicename`='{$invoicename}',
			`invoicenumber`='{$invoicenumber}' 
			 where memberid='{$memberid}'" );*/
			$fsql->query( "update {P}_member set 
			`invoicename`='{$invoicename}',
			`invoicenumber`='{$invoicenumber}' 
			 where memberid='{$memberid}'" );
		}
		if ( $payid == "0" && islogin( ) )
		{
				$msql->query( "select account from {P}_member where memberid='{$memberid}'" );
				if ( $msql->next_record( ) )
				{
						$account = $msql->f( "account" );
						if ( $ordertotal <= $account && 0 <= $ordertotal )
						{
								$fsql->query( "update {P}_member set account=account-{$ordertotal},buytotal=buytotal+{$ordertotal} where memberid='{$memberid}'" );
								$fsql->query( "update {P}_shop_order set iflook='0',ifpay='1',paytime='{$dtime}' where orderid='{$orderid}'" );
								$fsql->query( "insert into {P}_member_buylist set 
									`buyfrom`='{$strModuleShop}',
									`memberid`='{$memberid}',
									`orderid`='{$orderid}',
									`payid`='0',
									`paytype`='{$strMemberAccountPay}',
									`paytotal`='{$ordertotal}',
									`daytime`='{$dtime}',
									`ip`='{$ip}',
									`OrderNo`='{$OrderNo}',
									`logname`='{$user}'
								" );
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
								membercentupdate( $memberid, "313" );
								$ordermessage ='<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><link href="'.$GLOBALS['GLOBALS']['CONF'][SiteHttp].'/shop/templates/css/shoporderdetail.css" rel="stylesheet" type="text/css" /><title>'.$GLOBALS['GLOBALS']['CONF'][SiteName].'訂單</title></head><body><div id="shoporderdetail"><div class="ordertitle"><div style="float:right;font:bold 14px/35px Verdana, Arial, Helvetica, sans-serif;">訂單號：'.$OrderNo.' &nbsp; </div>'.$GLOBALS['GLOBALS']['CONF'][SiteName].'訂單</div><div class="tit" style="line-height:2.5em">訂購人訊息</div><table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#ddeeff" >   <tr >    <td height="25" align="center" valign="top" class="itemname">訂 購 人</td>    <td valign="top" bgcolor="#ffffff" >'.$name.'</td>    <td align="center"  class="itemname">會員帳號</td>    <td height="25" valign="top" bgcolor="#ffffff" >'.$user.' </td>  </tr>  <tr >    <td width="80" height="25" align="center" valign="top" class="itemname">聯絡電話</td>    <td width="220" valign="top" bgcolor="#ffffff" >'.$tel.'</td>    <td width="80" align="center"  class="itemname">手機號碼</td>    <td height="25" valign="top" bgcolor="#ffffff" >'.$mobi.'</td>  </tr>  <tr >    <td height="25" align="center" valign="top" class="itemname">電子郵箱</td>    <td width="220" valign="top" bgcolor="#ffffff" >'.$email.'</td>    <td align="center"  class="itemname">即時通/MSN</td>    <td height="25" valign="top" bgcolor="#ffffff" ><span class="itemname">'.$qq.'</span></td>  </tr>    </table><div class="tit" style="line-height:2.5em">收貨人訊息</div><table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#ddeeff" >   <tr >    <td width="80" height="25" align="center" class="itemname">收 貨 人</td>    <td width="220" valign="top" bgcolor="#FFFFFF" >'.$s_name.'</td>    <td width="80" align="center" valign="top"  class="itemname">配送地區</td>    <td height="25" valign="top" bgcolor="#FFFFFF" >'.$zonestr.'</td>  </tr>  <tr >    <td height="25" align="center" class="itemname">聯絡電話</td>    <td width="220" valign="top" bgcolor="#FFFFFF" >'.$s_tel.'</td>    <td align="center" valign="top"  class="itemname">詳細地址</td>    <td height="25" valign="top" bgcolor="#FFFFFF" ><span class="itemname">'.$s_addr.'</span></td>  </tr>  <tr >    <td height="25" align="center" class="itemname">手機號碼</td>    <td valign="top" bgcolor="#FFFFFF" >'.$s_mobi.'</td>    <td align="center" valign="top"  class="itemname">郵遞區號</td>    <td height="25" valign="top" bgcolor="#FFFFFF" ><span class="itemname">'.$s_postcode.'</span></td>  </tr>  <tr >    <td height="25" align="center" class="itemname">即時通/MSN</td>    <td width="220" valign="top" bgcolor="#FFFFFF" >'.$s_qq.'</td>    <td align="center" valign="top"  class="itemname">配送方法</td>    <td height="25" valign="top" bgcolor="#FFFFFF" >'.$yunname.'</td>  </tr>  </table><div class="tit" style="line-height:2.5em">商品清單</div><table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#ddeeff" style="margin-bottom:10px">  <tr valign="top">    <td width="75" align="center"  class="itemname">商品編號</td>    <td height="25" align="center"  class="itemname" >商品名稱</td>    <td width="80" align="center"  class="itemname">單價 (元)</td>    <td width="50" align="center"  class="itemname">數量</td>    <td width="50" align="center"  class="itemname">規格</td>    <td width="80" align="center"  class="itemname">小計 (元)</td>    </tr>'.$items_html.'</table><div class="tit" style="line-height:2.5em">訂購備註</div><div class="bz" style="border:1px #ccc solid;padding:5px">'.$bz.'</div><div class="totaldiv">商品總價：'.$tjine.' 元 &nbsp; 配送費用：'.(INT)$newyunfei.' 元 &nbsp; 折扣：'.$bao.' 元 &nbsp; 訂單總金額：'.$ordertotal.' 元 &nbsp; <br />訂購時間：'.date("Y-m-d H:i:s",$dtime).' &nbsp; 付款方式：'.$pcenter.' &nbsp; 付款時間：'.date("Y-m-d H:i:s",$dtime).'  </div><div class="tit" style="line-height:2.5em">付款注意事項</div><div class="bz" style="border:1px #ccc solid;padding:5px">'.$pcenterintro.'</div></div></body></html>';
								
		$mailbody = '<html><head>';
		$mailbody .='<title>'.$fromtitle.'</title><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body bgcolor="#ffffff">';
		$mailbody .='<table style="display: inline-table;" border="0" cellpadding="0" cellspacing="0" width="800">';
		//$mailbody .='<tr><td><img name="n1_r1_c1" src="'.$GLOBALS['CONF']['SiteHttp'].'images/mail_top_order.png" width="800" height="208" alt=""></td></tr>';
		$mailbody .='<tr><td width="100%" valign="top" style="padding:0px;">';
		$mailbody .='<table width="800" border="0" align="left" cellpadding="0" cellspacing="0"><tr><td width="80" height="250">&nbsp;</td>';
		$mailbody .='<td width="640" style="font-family:\'微軟正黑體\',Century Gothic;vertical-align: top;font-size:17px;">'.$ordermessage.'</td>';
		$mailbody .='<td width="80" style="vertical-align: top;">&nbsp;</td></tr></table>';
		//$mailbody .='</td></tr><tr><td><img name="n1_r3_c1" src="'.$GLOBALS['CONF']['SiteHttp'].'images/mail_bt.png" width="800" height="240" alt=""></td></tr></table>';
		$mailbody .='</td></tr></table>';
		$mailbody .='</body></html>';
								
								$GLOBALS['GLOBALS']['CONF'][OrderEmail] = $GLOBALS['GLOBALS']['CONF'][OrderEmail] !=""? $GLOBALS['GLOBALS']['CONF'][OrderEmail]:$GLOBALS['GLOBALS']['CONF'][SiteEmail];
								
								ebmail( $GLOBALS['GLOBALS']['CONF'][OrderEmail], $GLOBALS['GLOBALS']['CONF'][SiteEmail], str_replace("SITE",$GLOBALS['GLOBALS']['CONF'][SiteName],$newordertitle), $mailbody );
								
								$subjtitle = "訂單成立通知- 訂單編號".$OrderNo;
								
								$ordertext = $name."|".$GLOBALS['GLOBALS']['CONF'][SiteName]."|".time()."|".$OrderNo."|".$pcenter."|".$ordertotal."|".$GLOBALS['GLOBALS']['CONF'][SiteHttp];
								
								if($source == "0"){
									if(!shopmail( $email, $GLOBALS['GLOBALS']['CONF'][SiteEmail], $ordertext, "5" )){
										ebmail( $email, $GLOBALS['GLOBALS']['CONF'][SiteEmail], $subjtitle, $mailbody );
									}
								}

								
								echo "OK_PAYED_".$orderid;
								exit( );
						}
				}
		}
		
		if($payid>1)//貨到付款
		{
$ordermessage ='<div id="shoporderdetail" style="width:100%;"><div class="ordertitle"><div style="float:right;font:bold 14px/35px 微軟正黑體,Verdana, Arial, Helvetica, sans-serif;"><div style="font-weight:900;font-size:9pt;" align="center">此郵件為系統自動傳送，請勿直接回覆此郵件!</div>訂單號：'.$OrderNo.' &nbsp; </div>您的購物訂單</div><div class="tit" style="line-height:2.5em">訂購人訊息</div><table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#ddeeff" >   <tr >    <td height="25" align="center" valign="top" class="itemname">訂 購 人</td>    <td valign="top" bgcolor="#ffffff" >'.$name.'</td>    <td align="center"  class="itemname">會員帳號</td>    <td height="25" valign="top" bgcolor="#ffffff" >'.$user.' </td>  </tr>  <tr >    <td width="80" height="25" align="center" valign="top" class="itemname">聯絡電話</td>    <td width="220" valign="top" bgcolor="#ffffff" >'.$tel.'</td>    <td width="80" align="center"  class="itemname">手機號碼</td>    <td height="25" valign="top" bgcolor="#ffffff" >'.$mobi.'</td>  </tr>  <tr >    <td height="25" align="center" valign="top" class="itemname">電子郵箱</td>    <td height="25" colspan="3" valign="top" bgcolor="#ffffff" >'.$email.'</td>    </tr>    </table><div class="tit" style="line-height:2.5em">收貨人訊息</div><table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#ddeeff" >   <tr >    <td width="80" height="25" align="center" class="itemname">收 貨 人</td>    <td width="220" valign="top" bgcolor="#FFFFFF" >'.$s_name.'</td>    <td width="80" align="center" valign="top"  class="itemname">手機號碼</td>    <td height="25" valign="top" bgcolor="#FFFFFF" >'.$s_mobi.'</td>  </tr>  <tr >    <td height="25" align="center" class="itemname">郵遞區號</td>    <td width="220" valign="top" bgcolor="#FFFFFF" ><span class="itemname">'.$s_postcode.'</span></td>    <td align="center" valign="top"  class="itemname">聯絡電話</td>    <td height="25" valign="top" bgcolor="#FFFFFF" >'.$s_tel.'</td>  </tr>  <tr >    <td height="25" align="center" class="itemname">詳細地址</td>    <td height="25" colspan="3" valign="top" bgcolor="#FFFFFF" ><span class="itemname">'.$s_addr.'</span></td>    </tr>  </table><div class="tit" style="line-height:2.5em">商品清單</div><table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#ddeeff" style="margin-bottom:10px">  <tr valign="top">    <td width="75" align="center"  class="itemname">商品編號</td>    <td height="25" align="center"  class="itemname" >商品名稱</td>    <td width="80" align="center"  class="itemname">單價 (元)</td>    <td width="50" align="center"  class="itemname">數量</td>    <td width="50" align="center"  class="itemname">規格</td>    <td width="80" align="center"  class="itemname">小計 (元)</td>    </tr>'.$items_html.'</table><div class="totaldiv">商品總價：'.$tjine.' 元 &nbsp; 配送費用：'.(INT)$newyunfei.' 元 &nbsp; 折扣(餘額/優惠券)：'.number_format($disaccount,0).'/'.number_format($promoprice,0).' 元 &nbsp; 訂單總金額：'.number_format($ordertotal,0).' 元 &nbsp; <br />訂購時間：'.date("Y-m-d H:i:s",$dtime).' &nbsp; 付款方式：'.$pcenter.' &nbsp; 付款時間：無  </div></div>';

		$mailbody = '<html><head>';
		$mailbody .='<title>'.$fromtitle.'</title><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body bgcolor="#ffffff">';
		$mailbody .='<table style="display: inline-table;" border="0" cellpadding="0" cellspacing="0" width="800">';
		//$mailbody .='<tr><td><img name="n1_r1_c1" src="'.$GLOBALS['CONF']['SiteHttp'].'images/mail_top_order.png" width="800" height="208" alt=""></td></tr>';
		$mailbody .='<tr><td width="100%" valign="top" style="padding:0px;">';
		$mailbody .='<table width="800" border="0" align="left" cellpadding="0" cellspacing="0"><tr><td width="80" height="250">&nbsp;</td>';
		$mailbody .='<td width="640" style="font-family:\'微軟正黑體\',Century Gothic;vertical-align: top;font-size:17px;">'.$ordermessage.'</td>';
		$mailbody .='<td width="80" style="vertical-align: top;">&nbsp;</td></tr></table>';
		//$mailbody .='</td></tr><tr><td><img name="n1_r3_c1" src="'.$GLOBALS['CONF']['SiteHttp'].'images/mail_bt.png" width="800" height="240" alt=""></td></tr></table>';
		$mailbody .='</td></tr></table>';
		$mailbody .='</body></html>';
		
		$GLOBALS['GLOBALS']['CONF'][OrderEmail] = $GLOBALS['GLOBALS']['CONF'][OrderEmail] !=""? $GLOBALS['GLOBALS']['CONF'][OrderEmail]:$GLOBALS['GLOBALS']['CONF'][SiteEmail];

		ebmail( $GLOBALS['GLOBALS']['CONF'][OrderEmail], $GLOBALS['GLOBALS']['CONF'][SiteEmail], str_replace("SITE",$GLOBALS['GLOBALS']['CONF'][SiteName],$newordertitle), $mailbody );
		$subjtitle = "訂單成立通知- 訂單編號".$OrderNo;
		
		$ordertext = $name."|".$GLOBALS['GLOBALS']['CONF'][SiteName]."|".time()."|".$OrderNo."|".$pcenter."|".$ordertotal."|".$GLOBALS['GLOBALS']['CONF'][SiteHttp];
		
		if($source == "0"){
			
			if(!shopmail( $email, $GLOBALS['GLOBALS']['CONF'][SiteEmail], $ordertext, "5" )){
				ebmail( $email, $GLOBALS['GLOBALS']['CONF'][SiteEmail], $subjtitle, $mailbody );
			}
			
																
			/*寄發折價券 2013-12-14*/
			if($promotype == 3){
				$msql->query( "select code,mail_temp,memberid from {P}_shop_promocode where id='{$promo_con}' " );
				if ( $msql->next_record( ) ){
					$code = $msql->f( "code" );
					$mail_temp = $msql->f("mail_temp");
					$promo_member = $msql->f("memberid");
				}
				$fromtitle = "Rema 銳馬-促銷活動電子折價券";
				$fromemail = $GLOBALS['GLOBALS']['CONF'][SiteEmail];
				$mail_temp = str_replace("\r\n","",$mail_temp);
				$mail_temp = str_replace("\r","",$mail_temp);
				$mail_temp = str_replace("\n","",$mail_temp);
				$message = $mail_temp;

				$promo_mailbody = '<html><head>';
				$promo_mailbody .='<title>'.$fromtitle.'</title><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body bgcolor="#ffffff">';
				$promo_mailbody .='<table style="display: inline-table;" border="0" cellpadding="0" cellspacing="0" width="800">';
				$promo_mailbody .='<tr><td><img name="n1_r1_c1" src="'.$GLOBALS['CONF']['SiteHttp'].'images/mail_top_promo.png" width="800" height="208" alt=""></td></tr>';
				$promo_mailbody .='<tr><td width="100%" valign="top" style="padding:0px;">';
				$promo_mailbody .='<table width="800" border="0" align="left" cellpadding="0" cellspacing="0"><tr><td width="80" height="250">&nbsp;</td>';
				$promo_mailbody .='<td width="400" style="font-family:\'微軟正黑體\',Century Gothic;vertical-align: top;font-size:17px;">'.$message.'</td><td width="80">&nbsp;</td>';
				$promo_mailbody .='<td style="vertical-align: top;"><img name="n1_r3_c1" src="'.$GLOBALS['CONF']['SiteHttp'].'images/mail_right_reg.png"></td></tr></table>';
				$promo_mailbody .='</td></tr><tr><td><img name="n1_r3_c1" src="'.$GLOBALS['CONF']['SiteHttp'].'images/mail_bt.png" width="800" height="240" alt=""></td></tr></table>';
				$promo_mailbody .='</body></html>';
				
				
				$msql->query( "select user,email from {P}_member where email<>'' and  memberid='$memberid'" );
				if( $msql->next_record( ) ){
					$user = $msql->f( "user" );
					$email = $msql->f( "email" )? $msql->f( "email" ):$user;

						if(preg_match("/^[A-Za-z0-9\.|-|_]*[@]{1}[A-Za-z0-9\.|-|_]*[.]{1}[a-z]{2,5}$/", $email)){
							$allmail = $email;
						}
					/*回填限定會員ID*/
					$promo_member = $promo_member.",".$memberid;
					$msql->query("UPDATE {P}_shop_promocode SET memberid='{$promo_member}' WHERE id='{$promo_con}'");
					ebmail( $allmail, $fromemail, $fromtitle, $promo_mailbody );
				}
			}
			/**/
		}
			
		}else{
			//刷卡
			
			$ordermessage ='<div id="shoporderdetail" style="width:100%;"><div class="ordertitle"><div style="float:right;font:bold 14px/35px 微軟正黑體,Verdana, Arial, Helvetica, sans-serif;"><div style="font-weight:900;font-size:9pt;" align="center">此郵件為系統自動傳送，請勿直接回覆此郵件!</div>訂單號：'.$OrderNo.' &nbsp; </div>您的購物訂單</div><div class="tit" style="line-height:2.5em">訂購人訊息</div><table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#ddeeff" >   <tr >    <td height="25" align="center" valign="top" class="itemname">訂 購 人</td>    <td valign="top" bgcolor="#ffffff" >'.$name.'</td>    <td align="center"  class="itemname">會員帳號</td>    <td height="25" valign="top" bgcolor="#ffffff" >'.$user.' </td>  </tr>  <tr >    <td width="80" height="25" align="center" valign="top" class="itemname">聯絡電話</td>    <td width="220" valign="top" bgcolor="#ffffff" >'.$tel.'</td>    <td width="80" align="center"  class="itemname">手機號碼</td>    <td height="25" valign="top" bgcolor="#ffffff" >'.$mobi.'</td>  </tr>  <tr >    <td height="25" align="center" valign="top" class="itemname">電子郵箱</td>    <td height="25" colspan="3" valign="top" bgcolor="#ffffff" >'.$email.'</td>    </tr>    </table><div class="tit" style="line-height:2.5em">收貨人訊息</div><table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#ddeeff" >   <tr >    <td width="80" height="25" align="center" class="itemname">收 貨 人</td>    <td width="220" valign="top" bgcolor="#FFFFFF" >'.$s_name.'</td>    <td width="80" align="center" valign="top"  class="itemname">手機號碼</td>    <td height="25" valign="top" bgcolor="#FFFFFF" >'.$s_mobi.'</td>  </tr>  <tr >    <td height="25" align="center" class="itemname">郵遞區號</td>    <td width="220" valign="top" bgcolor="#FFFFFF" ><span class="itemname">'.$s_postcode.'</span></td>    <td align="center" valign="top"  class="itemname">聯絡電話</td>    <td height="25" valign="top" bgcolor="#FFFFFF" >'.$s_tel.'</td>  </tr>  <tr >    <td height="25" align="center" class="itemname">詳細地址</td>    <td height="25" colspan="3" valign="top" bgcolor="#FFFFFF" ><span class="itemname">'.$s_addr.'</span></td>    </tr>  </table><div class="tit" style="line-height:2.5em">商品清單</div><table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#ddeeff" style="margin-bottom:10px">  <tr valign="top">    <td width="75" align="center"  class="itemname">商品編號</td>    <td height="25" align="center"  class="itemname" >商品名稱</td>    <td width="80" align="center"  class="itemname">單價 (元)</td>    <td width="50" align="center"  class="itemname">數量</td>    <td width="50" align="center"  class="itemname">單位</td>    <td width="80" align="center"  class="itemname">小計 (元)</td>    </tr>'.$items_html.'</table><div class="totaldiv">商品總價：'.$tjine.' 元 &nbsp; 配送費用：'.(INT)$newyunfei.' 元 &nbsp; 折扣(餘額/優惠券)：'.number_format($disaccount,0).'/'.number_format($promoprice,0).' 元 &nbsp; 訂單總金額：'.number_format($ordertotal,0).' 元 &nbsp; <br />訂購時間：'.date("Y-m-d H:i:s",$dtime).' &nbsp; 付款方式：'.$pcenter.' &nbsp; 付款時間：{#paytime#}  </div></div>';

		$mailbody = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"><html><head>';
		$mailbody .='<title>'.$fromtitle.'</title><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body bgcolor="#ffffff">';
		$mailbody .='<table style="display: inline-table;" border="0" cellpadding="0" cellspacing="0" width="800">';
		//$mailbody .='<tr><td><img name="n1_r1_c1" src="'.$GLOBALS['CONF']['SiteHttp'].'images/mail_top_order.png" width="800" height="208" alt=""></td></tr>';
		$mailbody .='<tr><td width="100%" valign="top" style="padding:0px;">';
		$mailbody .='<table width="800" border="0" align="left" cellpadding="0" cellspacing="0"><tr><td width="80" height="250">&nbsp;</td>';
		$mailbody .='<td width="640" style="font-family:\'微軟正黑體\',Century Gothic;vertical-align: top;font-size:17px;">'.$ordermessage.'</td>';
		$mailbody .='<td width="80" style="vertical-align: top;">&nbsp;</td></tr></table>';
		//$mailbody .='</td></tr><tr><td><img name="n1_r3_c1" src="'.$GLOBALS['CONF']['SiteHttp'].'images/mail_bt.png" width="800" height="240" alt=""></td></tr></table>';
		$mailbody .='</td></tr></table>';
		$mailbody .='</body></html>';
		
		$mailbody = addslashes($mailbody);
		
		$msql->query("UPDATE {P}_shop_order SET card_mail_admin='{$mailbody}' WHERE orderid='{$orderid}'");
		
		$ordertext = $name."|".$GLOBALS['GLOBALS']['CONF'][SiteName]."|".time()."|".$OrderNo."|".$pcenter."|".$ordertotal."|".$GLOBALS['GLOBALS']['CONF'][SiteHttp];
		
		$msql->query( "SELECT * FROM {P}_shop_mailtemp WHERE tid='5' AND status='1' " );//抓取樣板
		if($msql->next_record()){
			$mailbody = "";
			$getsmtext = explode("|",$ordertext);//解析替換參數:1.會員姓名、2.網站名稱、3.時間、4.訂單編號、5.付款方式、6.金額、7.網址
			$subject = str_replace("{site_name}",$getsmtext[1],$msql->f("subject"));
			$sendmsg=$msql->f("fix_content")? $msql->f("fix_content"):$msql->f("content");//樣板
				$getsmtext[2] = date("Y-m-d H:i:s",$getsmtext[2]);
				$sendmsg=$getsmtext[0] != "0" ? str_replace("{member_name}",$getsmtext[0],$sendmsg):$sendmsg;//替換第一個參數
				$sendmsg=$getsmtext[1] != "0" ? str_replace("{site_name}",$getsmtext[1],$sendmsg):$sendmsg;//替換第二個參數
				$sendmsg=$getsmtext[2] != "0" ? str_replace("{this_time}",$getsmtext[2],$sendmsg):$sendmsg;//替換第三個參數
				$sendmsg=$getsmtext[3] != "0" ? str_replace("{order_no}",$getsmtext[3],$sendmsg):$sendmsg;//替換第四個參數
				$sendmsg=$getsmtext[4] != "0" ? str_replace("{pay_type}",$getsmtext[4],$sendmsg):$sendmsg;//替換第五個參數
				$sendmsg=$getsmtext[5] != "0" ? str_replace("{pay_total}",$getsmtext[5],$sendmsg):$sendmsg;//替換第六個參數
				$sendmsg=$getsmtext[6] != "0" ? str_replace("{site_url}",$getsmtext[6],$sendmsg):$sendmsg;//替換第七個參數
				$message = str_replace( "\r\n", "<br>", $sendmsg );
				
				$mailbody = '<html><head>';
				$mailbody .='<meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body bgcolor="#ffffff">';
				$mailbody .='<table style="display: inline-table;" border="0" cellpadding="0" cellspacing="0" width="800">';
				//$mailbody .='<tr><td><img name="n1_r1_c1" src="'.$GLOBALS['CONF']['SiteHttp'].'images/mail_top_order.png" width="800" height="208" alt=""></td></tr>';
				$mailbody .='<tr><td width="100%" valign="top" style="padding:0px;">';
				$mailbody .='<table width="800" border="0" align="left" cellpadding="0" cellspacing="0"><tr><td width="80" height="250">&nbsp;</td>';
				$mailbody .='<td width="400" style="font-family:\'微軟正黑體\',Century Gothic;vertical-align: top;font-size:17px;">'.$message.'</td><td width="80">&nbsp;</td>';
				$mailbody .='<td style="vertical-align: top;"><img name="n1_r3_c1" src="'.$GLOBALS['CONF']['SiteHttp'].'images/mail_right_order.png"></td></tr></table>';
				//$mailbody .='</td></tr><tr><td><img name="n1_r3_c1" src="'.$GLOBALS['CONF']['SiteHttp'].'images/mail_bt.png" width="800" height="240" alt=""></td></tr></table>';
				$mailbody .='</td></tr></table>';
				$mailbody .='</body></html>';
				
				$mailbody = addslashes($mailbody);
		}

				
				$msql->query("UPDATE {P}_shop_order SET card_mail='{$mailbody}' WHERE orderid='{$orderid}'");
			
		}
		
		include_once( ROOTPATH."costomer.php");
		$data['orderid'] = $orderid;
		cre_order(http_build_query($data));
		echo "OK_".$orderid;
		exit( );
		break;
case "payfrommemberaccount" :
		$orderid = $_POST['orderid'];
		if ( !islogin( ) )
		{
				echo "1006";
				exit( );
		}
		$dtime = time( );
		$ip = $_SERVER['REMOTE_ADDR'];
		$user = $_COOKIE['MUSER'];
		$memberid = $_COOKIE['MEMBERID'];
		$msql->query( "select * from {P}_shop_order where orderid='{$orderid}' and memberid='{$memberid}'" );
		if ( $msql->next_record( ) )
		{
				$memberid = $msql->f( "memberid" );
				$OrderNo = $msql->f( "OrderNo" );
				$paytotal = $msql->f( "paytotal" );
				$payid = $msql->f( "payid" );
				$ifpay = $msql->f( "ifpay" );
				$iftui = $msql->f( "iftui" );
				$tcent = $msql->f( "totalcent" );
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
				if ( $payid == "0" )
				{
						$msql->query( "select account from {P}_member where memberid='{$memberid}'" );
						if ( $msql->next_record( ) )
						{
								$account = $msql->f( "account" );
								if ( $paytotal <= $account && 0 <= $paytotal )
								{
										$fsql->query( "update {P}_member set account=account-{$paytotal},buytotal=buytotal+{$paytotal} where memberid='{$memberid}'" );
										$fsql->query( "update {P}_shop_order set iflook='0',ifpay='1',paytime='{$dtime}' where orderid='{$orderid}'" );
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
											`logname`='{$user}'
										" );
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
										membercentupdate( $memberid, "313" );
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
								echo "1005";
								exit( );
						}
				}
				else
				{
						echo "1003";
						exit( );
				}
		}
		else
		{
				echo "1000";
				exit( );
		}
		break;
case "orderlook" :
		$orderno = $_POST['orderno'];
		$sname = $_POST['sname'];
		$fsql->query( "select orderid from {P}_shop_order where OrderNo='{$orderno}' and s_name='{$sname}'" );
		if ( $fsql->next_record( ) )
		{
				$md = substr( md5( $orderno.$sname ), 0, 5 );
				echo "OK_".$md;
				exit( );
		}
		else
		{
				echo "1000";
				exit( );
		}
		break;
}

//字串加密
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