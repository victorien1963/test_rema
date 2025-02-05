<?php


function ShopMemberOrder()
{

	global $fsql, $msql, $tsql, $sybtype, $strAlreadyFinish, $strDelivering, $strReturn, $strInProcessing, $strIspay, $strCancel, $strSendAPI, $strDelOrder;

	//2016獲取貨幣、匯率
	if ($sybtype) {
		list($getsymbol, $getpricecode, $getrate, $getpoint, $getpriceid) = explode(",", $sybtype);
	} else {
		list($getsymbol, $getpricecode, $getrate, $getpoint, $getpriceid) = explode(",", getDefaultSyb());
	}

	$tempname = $GLOBALS["PLUSVARS"]["tempname"];
	$memberid = $_COOKIE["MEMBERID"];

	//網址攔參數
	$key = $_GET["key"];
	$showpay = $_GET["showpay"];
	$showyun = $_GET["showyun"];
	$showok = $_GET["showok"];
	$startday = $_GET["startday"];
	$endday = $_GET["endday"];

	if ($startday == "" || $endday == "") {
		$endday = date("Y-m-d");
		$enddayArr = explode("-", $endday);
		$endtime = mktime(23, 59, 59, $enddayArr[1], $enddayArr[2], $enddayArr[0]);
		$starttime = $endtime - 691199;
		$startday = date("Y-m-d", $starttime);
		$cantuitime = $endtime - (86400 * 11 - 1);
	} else {
		$enddayArr = explode("-", $endday);
		$endtime = mktime(23, 59, 59, $enddayArr[1], $enddayArr[2], $enddayArr[0]);
		$startdayArr = explode("-", $startday);
		$starttime = mktime(0, 0, 0, $startdayArr[1], $startdayArr[2], $startdayArr[0]);
	}

	if ($showpay == "") {
		$showpay = "all";
	}
	if ($showyun == "") {
		$showyun = "all";
	}
	if ($showok == "") {
		$showok = "0";
	}

	//模板解釋
	$Temp = LoadTemp($tempname);
	$TempArr = SplitTblTemp($Temp);

	$var = array(
		'key' => $key,
		'showpay' => $showpay,
		'showyun' => $showyun,
		'showok' => $showok,
		'startday' => $startday,
		'endday' => $endday,
	);

	$str = ShowTplTemp($TempArr["start"], $var);

	//$scl=" memberid='$memberid' and iftui!='1' and dtime>$starttime and dtime<$endtime ";

	if ($_COOKIE["SYSUSER"] != "") {
		$oktime = time() - ((86400 * 366) - 1);
	} else {
		$oktime = time() - ((86400 * 11) - 1);
	}

	$scl = " memberid='$memberid' ";

	if ($showpay == "1" || $showpay == "0") {
		$scl .= " and ifpay='$showpay' ";
	}

	if ($showyun == "1" || $showyun == "0") {
		$scl .= " and ifyun='$showyun' ";
	}

	/*if($showok=="1" || $showok=="0"){
		$scl.=" and ifok='$showok' ";
	}*/

	if ($key != "") {
		//$scl.=" and (OrderNo='$key' or items regexp '$key' or name regexp '$key' or s_name regexp '$key') ";
	}


	include_once(ROOTPATH . "includes/pages.inc.php");

	$pages = new pages;

	$totalnums = TblCount("_shop_order", "orderid", $scl);

	$pages->setvar(array(
		"key" => $key,
		"startday" => $startday,
		"endday" => $endday,
		"showpay" => $showpay,
		"showyun" => $showyun,
		"showok" => $showok
	));

	$pages->set(5000, $totalnums);

	$pagelimit = $pages->limit();

	$orderList = array();
	$orderIndex = 0;
	$msql->query("select * from {P}_shop_order where $scl order by dtime desc limit $pagelimit");

	while ($msql->next_record()) {

		$orderid = $msql->f('orderid');
		$OrderNo = $msql->f('OrderNo');
		$memberid = $msql->f('memberid');
		$goodstotal = $msql->f('goodstotal');
		$yunzoneid = $msql->f('yunzoneid');
		$yunid = $msql->f('yunid');
		$yuntype = $msql->f('yuntype');
		$yunfei = $msql->f('yunfei');
		$paytotal = $msql->f('paytotal');
		$payid = $msql->f('payid');
		$iflook = $msql->f('iflook');
		$ifpay = $msql->f('ifpay');
		$ifyun = $msql->f('ifyun');
		$ifok = $msql->f('ifok');
		$iftui = $msql->f('iftui');
		$ifchg = $msql->f('ifchg');
		$dtime = $msql->f('dtime');
		$paytime = $msql->f('paytime');
		$yuntime = $msql->f('yuntime');
		$items = $msql->f('items');
		$itemtui = $msql->f('itemtui');
		$disaccount = $msql->f('disaccount');
		$promoprice = $msql->f('promoprice');
		$looktime = $msql->f('looktime');

		$s_name = $msql->f('s_name');
		$s_mobi = $msql->f('s_mobi');

		$tuitime = $msql->f("tuitime") ? date("y-m-d H:i:s",  $msql->f("tuitime")) : "---";
		$chgtime = $msql->f("chgtime") ? date("y-m-d H:i:s",  $msql->f("chgtime")) : "---";
		$tuiyuntime = $msql->f("tuiyuntime") ? date("y-m-d H:i:s",  $msql->f("tuiyuntime")) : "---";
		$gettuitime = $msql->f("gettuitime") ? date("y-m-d H:i:s",  $msql->f("gettuitime")) : "---";

		if ($msql->f("tuitime") < $msql->f("uptime")) {
			$tuidtime = date("y-m-d H:i:s",  $msql->f("uptime"));
		} else {
			$tuidtime = "---";
		}


		list($yunc, $odyunno) = explode("|", $msql->f('sendtypeno'));
		$searchod = $strSendAPI[$yunc] . $yunc;

		$s_addr = $msql->f('s_addr');

		$odtime = date("m-d H:i", $dtime);
		$odlook = $looktime ? date("m-d H:i", $looktime) : "---";
		$odyun = $yuntime ? date("m-d H:i", $yuntime) : "---";

		$InvoiceDate = $msql->f('InvoiceDate');
		$CreateInvoice = $msql->f('CreateInvoice');
		$mm = date("m", $InvoiceDate);
		switch ($mm) {
			case "01":
				$mms = "01-02";
				break;
			case "02":
				$mms = "01-02";
				break;
			case "03":
				$mms = "03-04";
				break;
			case "04":
				$mms = "03-04";
				break;
			case "05":
				$mms = "05-06";
				break;
			case "06":
				$mms = "05-06";
				break;
			case "07":
				$mms = "07-08";
				break;
			case "08":
				$mms = "07-08";
				break;
			case "09":
				$mms = "09-10";
				break;
			case "10":
				$mms = "09-10";
				break;
			case "11":
				$mms = "11-12";
				break;
			case "12":
				$mms = "11-12";
				break;
		}
		$CDate = (date("Y", $InvoiceDate) - 1911) . "年" . $mms . "月";

		// if( $ifyun && time() >$yuntime+(86400*2.5) || $ifok=="1" ){
		// 	$act4= "act";
		// 	$act4= "";
		// }else{
		// 	$act4 = "";
		// }

		$paytype = $msql->f('paytype');

		$pricesymbol = $msql->f('pricesymbol');
		$multiprice = $msql->f('multiprice');
		$price = $multiprice ? $multiprice : $price;
		$multiyunfei = $msql->f('multiyunfei');
		$yunfei = $multiyunfei ? $multiyunfei : $yunfei;
		$paytotal = $multiprice ? $price + $yunfei : number_format($paytotal, $getpoint);

		$tjine = $multiprice + $multiyunfei;


		$payid = $msql->f('payid');
		if ($payid == 1) {
			$paypic = ROOTPATH . "base/files/Brusco/Members/Order/TableOrderPayment_Credit_75x14.png";
		} else {
			$paypic = ROOTPATH . "base/files/Brusco/Members/Order/TableOrderPayment_Delivery_89x14.png";
		}

		$statupic = ROOTPATH . "base/files/Brusco/Members/Order/TableOrderProcess_New_73x14.png";
		$payagain = "";
		if (!$ifpay && $payid == 1 && $iftui == '0' && $itemtui == '0') {

			$payagain = str_replace("{#orderid#}", $orderid, $TempArr["m0"]);
		}

		$dtime = date("Y-n-j", $dtime);

		if ($itemtui == '0') {
			$showtui = str_replace("{#orderid#}", $orderid, $TempArr["m1"]);
			$showtuiprocess = "none";
			$showprocess = "block";
		} else {
			$showtuiprocess = "block";
			$showprocess = "none";
		}
		$strReturn = $itemtui == '1' ? $strReturn : $strCancel;

		switch ($iflook) {
			case "0":
				$status = "---";
				$okimg = "no.png";
				break;
			case "1":
				//$statupic = ROOTPATH."base/files/Brusco/Members/Order/TableOrderProcess_Cancel_73x14.png";
				$status = $strInProcessing;
				break;
		}

		switch ($iftui) {
			case "0":
				$okimg = "no.png";
				break;
			case "1":
				//$statupic = ROOTPATH."base/files/Brusco/Members/Order/TableOrderProcess_Cancel_73x14.png";
				$status = $strReturn;
				$showtui = "";
				break;
		}

		switch ($ifpay) {
			case "0":
				$payimg = "no.png";
				break;
			case "1":
				$payimg = "ok.png";
				$status = $strIspay;
				//$showtui = "";
				break;
		}

		switch ($ifyun) {
			case "0":
				$yunimg = "no.png";
				break;
			case "1":
				//$yunimg="ok.png";
				//$statupic = ROOTPATH."base/files/Brusco/Members/Order/isyun.png";
				$status = $strDelivering;
				$showtui = $yuntime >= $oktime ? '<div class="icon-block"><div class="icon icon-return return-bn"  style="cursor: pointer;"></div></div>' : '---';
				//1598356328//1574688888
				break;
		}

		switch ($ifok) {
			case "0":
				$okimg = "no.png";
				break;
			case "1":
				$okimg = "ok.png";
				//$statupic = ROOTPATH."base/files/Brusco/Members/Order/TableOrderProcess_Finish_88x14.png";
				$status = $strAlreadyFinish;
				$showtui = "";
				break;
		}

		//exit("//-".$ifok);

		/*運費 刷卡*/
		if ($getpriceid == 1) {
			$tsql->query("select dgs from {P}_shop_yun where id='1'");
			if ($tsql->next_record()) {
				$dgs = $tsql->f("dgs");
				list($setyunfei, $setyunprice) = explode("|", $dgs);
			}
			$setyunfei = $getrate != "1" ? round(($setyunfei * $getrate), $getpoint) : $setyunfei;
		} else {
			$tsql->query("select dgs from {P}_shop_yun where spec='{$getpricecode}'");
			if ($tsql->next_record()) {
				$dgs = $tsql->f("dgs");

				list($setyunfei, $setyunprice) = explode("|", $dgs);
				$yunfei = countyunfeip($tweight, $realtjine, $dgs, $getrate);
				$setyunfei2 = $setyunfei;
				$setyunprice2 = $setyunprice;
				$oriyunfei = $yunfei;
				$oriyunfei2 = $yunfei;
				$yunfei = $getrate != "1" ? round(($yunfei * $getrate), $getpoint) : $yunfei;
				$yunfei2 = $yunfei;
			}
		}
		$multiyunfei = number_format($multiyunfei, $getpoint);


		/*帳戶餘額*/
		$memberid = $_COOKIE['MEMBERID'];
		$fsql->query("select account from {P}_member where memberid='{$memberid}'");
		if ($fsql->next_record()) {
			$account = $fsql->f("account");
		} else {
			$account = "0";
		}

		$var = array(
			'orderIndex' => $orderIndex,
			'showOrderDetailLayout' => false,
			'showOrderReturnLayout' => false,
			'showOrderCHGLayout' => false,
			'editContact' => false,
			'orderid' => $orderid,
			'OrderNo' => $OrderNo,
			'items' => $items,
			'paytotal' => $paytotal,
			'yuntype' => $yuntype,
			'yunfei' => $yunfei,
			'okimg' => $okimg,
			'payimg' => $payimg,
			'yunimg' => $yunimg,
			'dtime' => $dtime,
			'paypic' => $paypic,
			'statupic' => $statupic,
			'showtui' => $showtui,
			'pricesymbol' => $pricesymbol,
			'status' => $payagain ? $payagain : $status,
			'iftui' => $iftui,
			'ifchg' => $ifchg,
			'payagain' => $payagain,
			'w400' => $payagain ? "width: 400px;" : "",
			'paytype' => $paytype,
			'account' => $account,
			'disaccount' => $disaccount,
			'promoprice' => $promoprice,
			'pricesymbol' => $pricesymbol,
			'tjine' => $tjine,
			'getsymbol' => $getsymbol,
			'multiyunfei' => $multiyunfei,
			'setyunfei' => $setyunfei,
			'odtime' => $odtime,
			'odlook' => $odlook,
			'odyun' => $odyun,
			'act1' => $odtime == "---" ? "" : "#b3b3b3",
			'act2' => $odlook == "---" ? "" : "#b3b3b3",
			'act3' => $odyun == "---" ? "" : "#b3b3b3",
			'act4' => $odyunno != "---" && $odyun != "---" ? "#b3b3b3" : "",
			'searchod' => $searchod ? $searchod : "javascript:;",
			'odyunno' => $odyunno && $odyun != "---" ? $odyunno : "---",
			'otarget' => $odyunno ? "_blank" : "",
			's_addr' => $s_addr,
			's_name' => $s_name,
			's_mobi' => $s_mobi,
			'editAddr' => $s_addr,
			'editName' => $s_name,
			'editMobi' => $s_mobi,
			'delorder' => $strDelOrder[$ifyun],
			'InDate' => $InvoiceDate ? date("Y-m-d", $InvoiceDate) : "",
			'InTime' => $InvoiceDate ? date("H:i:s", $InvoiceDate) : "",
			'CreateInvoice' => $CreateInvoice,
			'CDate' => $CDate,
			'showinno' => $InvoiceDate ? "block" : "none",
			'tuitime' => $tuitime,
			'chgtime' => $chgtime,
			'tuiyuntime' => $tuiyuntime,
			'gettuitime' => $gettuitime,
			'tuidtime' => $tuidtime,
			'showtuiprocess' => $showtuiprocess,
			'showprocess' => $showprocess,
			'tact2' => $tuiyuntime != "---" ? "act" : "",
			'tact3' => $gettuitime != "---" ? "act" : "",
			'tact4' => $tuidtime != "---" ? "act" : "",
		);

		//push order to order list
		$orderList[$orderIndex]['orderdata'] = $var;

		$str .= ShowTplTemp($TempArr["list"], $var);

		//訂單項目列表
		$k = $itemstr = $itemstr2 = $itemtuistr = $totaljine = "";
		$fsql->query("select * from {P}_shop_orderitems where orderid='$orderid'");
		while ($fsql->next_record()) {

			$itemid = $fsql->f('id');
			$memberid = $fsql->f('memberid');
			$orderid = $fsql->f('orderid');
			$gid = $fsql->f('gid');
			$bn = $fsql->f('bn');
			$goods = $fsql->f('goods');
			$price = $fsql->f('price');
			$weight = $fsql->f('weight');
			$nums = $fsql->f('nums');
			$danwei = $fsql->f('danwei');
			$jine = $fsql->f('jine');
			$cent = $fsql->f('cent');
			$iftui = $fsql->f('iftui');
			$ifyun = $fsql->f('ifyun');
			$yuntime = $fsql->f('yuntime');
			$msg = $fsql->f('msg');
			$itemtui = $fsql->f('itemtui');

			$getsubpic = $tsql->getone("select colorpic from {P}_shop_con where id='$gid'");
			$src = $getsubpic["colorpic"];
			$srcs = dirname($src) . "/sp_" . basename($src);
			$srcs = ROOTPATH . $srcs;

			$colorname = $fsql->f("colorname");

			$pricesymbol = $fsql->f('pricesymbol');
			$multiprice = $fsql->f('multiprice');
			$multijine = $fsql->f('multijine');

			$price = $multiprice ? $multiprice : $price;
			$jine = $multijine ? $multijine : $jine;

			$totaljine += $jine;

			list($buysize, $buyprice, $buyspecid) = explode("^", $fsql->f('fz'));
			//$ccode = $tsql->getone("select colorcode from {P}_shop_conspec where id='{$buyspecid}'");
			/*$tsql->query("select * from {P}_shop_conspec where gid='{$gid}' and colorcode='$ccode[colorcode]' order by id");
			while($tsql->next_record()){
				if($tsql->f("iconsrc")){
					$iconsrc = ROOTPATH.$tsql->f("iconsrc");
					$colorname = ROOTPATH.$tsql->f("colorname");
				}
			}
			$coloricon = "<img src='".$iconsrc."' width='11' height='11' />";*/

			$yuntime = date("y-n-j", $yuntime);

			if ($ifyun == "1") {
				$itemyun = "已發貨";
			} else {
				$itemyun = "未發貨";
			}

			$k++;


			$var = array(
				'itemid' => $itemid,
				'bn' => $bn,
				'goods' => $goods,
				'gid' => $gid,
				'price' => number_format($price, $getpoint),
				'nums' => $nums,
				'weight' => $weight,
				'danwei' => $danwei,
				'tjine' => number_format($tjine, $getpoint),
				'jine' => number_format($jine, $getpoint),
				'yuntime' => $yuntime,
				'cent' => $cent,
				'msg' => $msg,
				'itemyun' => $itemyun,
				'size' => $buysize,
				'coloricon' => $coloricon,
				'colorname' => $colorname,
				'pricesymbol' => $pricesymbol,
				'getsymbol' => $getsymbol,
				'srcs' => $srcs,
				'showtuistr' => $itemtui ? "block" : "none",
				'show_total_proce_layout' => ($k == 1) ? 'flex' : 'none',

			);

			//push item to order list item
			$orderList[$orderIndex]["orderItems"][] = $var;

			$itemstr .= ShowTplTemp($TempArr["m2"], $var);
			$itemstr2 .= ShowTplTemp($TempArr["menu"], $var);
			$itemtuistr .= ShowTplTemp($TempArr["m3"], $var);
			$itemchgstr .= ShowTplTemp($TempArr["m4"], $var);
		}

		$orderList[$orderIndex]["totaljine"] = $totaljine;
		

		// m2,  itemlist  --->訂單列表中商品清單用
		// menu,  itemlist2 ---商品總計時使用，目前應該無用
		// m3,itemtuilist ---退貨商品清單用
		$str = str_replace("{#itemlist#}", $itemstr, $str);
		$str = str_replace("{#itemlist2#}", $itemstr2, $str);
		$str = str_replace("{#totaljine#}", $totaljine, $str);
		$str = str_replace("{#itemtuilist#}", $itemtuistr, $str);
		$str = str_replace("{#itemchglist#}", $itemchgstr, $str);


		$orderIndex++;
	}

	$str .= $TempArr["end"];

	$pagesinfo = $pages->ShowNow();

	$var = array(
		'key' => $key,
		'showpages' => $pages->output(1),
		'pagestotal' => $pagesinfo["total"],
		'pagesnow' => $pagesinfo["now"],
		'pagesshownum' => $pagesinfo["shownum"],
		'pagesfrom' => $pagesinfo["from"],
		'pagesto' => $pagesinfo["to"],
		'totalnums' => $totalnums,
		'kk' => $k
	);

	$str = ShowTplTemp($str, $var);

	$str .= "<script>var orderList = " . json_encode($orderList, JSON_UNESCAPED_UNICODE) . ";</script>";
	return $str;
}
