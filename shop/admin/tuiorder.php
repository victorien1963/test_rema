<?php
define( "ROOTPATH", "../../" );
include( ROOTPATH."includes/admin.inc.php" );
include( ROOTPATH."includes/pages.inc.php" );
include( ROOTPATH."includes/ebmail.inc.php" );
include( "func/query.inc.php" );
include( "language/".$sLan.".php" );
NeedAuth(327);
$step = $_REQUEST['step'];
$page = $_REQUEST['page'];
$key = $_GET['key'];
$shownum = $_REQUEST['shownum'];
$showmem = $_GET['showmem'];
$startday = $_GET['startday'];
$endday = $_GET['endday'];
$showoktui = $_GET['showoktui']? $_GET['showoktui']:"0";

if ( $startday == "" || $endday == "" )
{
		$endday = date( "Y-m-d" );
		$enddayArr = explode( "-", $endday );
		$endtime = mktime( 23, 59, 59, $enddayArr[1], $enddayArr[2], $enddayArr[0] );
		$starttime = $endtime - 2678399;
		$startday = date( "Y-m-d", $starttime );
}
else
{
		$enddayArr = explode( "-", $endday );
		$endtime = mktime( 23, 59, 59, $enddayArr[1], $enddayArr[2], $enddayArr[0] );
		$startdayArr = explode( "-", $startday );
		$starttime = mktime( 0, 0, 0, $startdayArr[1], $startdayArr[2], $startdayArr[0] );
}

if ( $shownum == "" || $shownum < 10 )
{
		$shownum = 100;
}

if($step=="return"){
//訂單編號,訂單日期,買方名稱,買方統編,電話/手機,買方地址,買方Email,產品編號,品名,數量,單價,課稅別,發票方式,付款方式,備註,載具類別,載具編號,愛心碼


//按設定的日期訂單輸出

$getdate = explode("-",$_REQUEST['sd']);
$fromY = $getdate[0];
$fromM = $getdate[1];
$fromD = $getdate[2];
$starttime = mktime (0,0,0,$fromM,$fromD,$fromY);
$getdate = explode("-",$_REQUEST['ed']);
$fromY = $getdate[0];
$fromM = $getdate[1];
$fromD = $getdate[2];
$endtime = mktime (23,59,59,$fromM,$fromD,$fromY);



$scl_receipt = "tuitime>={$starttime} and tuitime<={$endtime} and tuiok='1'";



$e_body = "訂單編號,配送,發票,訂單日期,買方名稱,買方統編,電話/手機,買方地址,買方Email,產品編號,品名,數量,單價,課稅別,發票方式,付款方式,備註,載具類別,載具編號,愛心碼\n";

$msql->query( "select * from {P}_shop_order where {$scl_receipt} order by tuitime desc" );
while($msql->next_record()){
	$e_OrderNo=$msql->f("OrderNo");
	$e_name=$msql->f("name");
	$e_s_name=$msql->f("s_name");
	$e_s_tel=$msql->f("s_tel");
	$e_s_mobi=$msql->f("s_mobi");
	$e_s_addr=$msql->f("s_addr");
	$e_payid=$msql->f("payid");
	$e_paytotal= $e_payid == 2 ? (INT)$msql->f("paytotal"):"0";
	$promoprice = $msql->f("promoprice");
	$yunfei = (INT)$msql->f("yunfei");
	/*發票新增*/
	$orderid= $msql->f("orderid");
	$dtime = $msql->f("dtime");
	$yy = date("Y",$dtime);
	$ym = str_pad(date("m",$dtime),2,"0",STR_PAD_LEFT);
	$yd = str_pad(date("d",$dtime),2,"0",STR_PAD_LEFT);
	$dtime=$yy."/".$ym."/".$yd;
	
	$invoicenumber = $msql->f( "invoicenumber" );
	$mobi = $msql->f( "mobi" );
	$user = $msql->f( "user" );
	$email = $user;
	$re_type = "2";
	
		/*捐款*/
		list($contribute_title,$contribute_code,$contribute_type) = explode("|",$msql->f( "contribute" ));
		if($contribute_code)($re_type = "1");
		/*載具*/
		list($integrated_title,$integrated_code,$integrated_type) = explode("|",$msql->f( "integrated" ));
		if($integrated_type)($re_type = "0");
		
	if($e_payid == "1"){ $e_paym = "3"; }else{ $e_paym = "5"; }
	$items = $msql->f( "items" );
	$paytotal = (INT)$msql->f( "paytotal" );
	//.csv數字轉字串
	$e_OrderNo="=\"".$e_OrderNo."\"";
	$e_s_mobi = str_replace('-','',$e_s_mobi);
	$e_mobi="=\"".$e_s_mobi."\"";
	$e_contact=$e_mobi;
	$mobi="=\"".$mobi."\"";
	$dtime="=\"".$dtime."\"";
	$contribute_code="=\"".$contribute_code."\"";
	
	/*產品編號*/
	$orderbn = $items = "";
	$nn = 1;
	$fsql->query( "select * from {P}_shop_orderitems where orderid='$orderid' and itemtui='1'" );
while($fsql->next_record()){
	$orderbn = $fsql->f("bn");
	list($fz) = explode("^",$fsql->f("fz"));
	$goods = $fsql->f("goods");
	$danwei = $fsql->f("danwei");
	$item_nums = $fsql->f("nums");
	$items = $goods."(".$fz.")";
	$item_paytotal = (INT)$fsql->f("price");
	$ifreceipt = $fsql->f( "ifreceipt" )? "以開發票":"未開";
	$ifyun = $fsql->f( "ifyun" )? "已配送":"未送";
	
	
	if($nn > 1) $e_OrderNo = $dtime = $e_name = $invoicenumber = $mobi = $e_s_addr = $email = $re_type = $e_paym = $integrated_type = $integrated_code = $contribute_code = "";
	
	//訂單編號,配送,發票,訂單日期,買方名稱,買方統編,電話/手機,買方地址,買方Email,產品編號,品名,數量,單價,課稅別,發票方式,付款方式,備註,載具類別,載具編號,愛心碼
	$e_body.= $e_OrderNo.",".$ifyun.",".$ifreceipt.",".$dtime.",".$e_name.",".$invoicenumber.",".$mobi.",".$e_s_addr.",".$email.",".$orderbn.",".$items.",".$item_nums.",".$item_paytotal.",1,".$re_type.",".$e_paym.",,".$integrated_type.",".$integrated_code.",".$contribute_code."\n";
	
	$nn++;
	
}
	if($yunfei > 0) $e_body.= ",,,,,,,,,SHIP,運費,1,".$yunfei.",,,,,,,\n";
	if($promoprice > 0) $e_body.= ",,,,,,,,,D01,活動折扣,1,-".$promoprice.",,,,,,,\n";

	
	//訂單編號,訂單日期,買方名稱,買方統編,電話/手機,買方地址,買方Email,產品編號,品名,數量,單價,課稅別,發票方式,付款方式,備註,載具類別,載具編號,愛心碼
	//$e_body.= $e_OrderNo.",".$dtime.",".$e_name.",".$invoicenumber.",".$mobi.",".$e_s_addr.",".$email.",".$orderbn.",".$items.",1,".$paytotal.",1,".$re_type.",".$e_paym.",,".$integrated_type.",".$integrated_code.",".$contribute_code."\n";
}
	$e_filename = date('YmdHis',time()).'_order_return';
	header('Content-Type: text/html; charset=utf-8' ); 
	header("Content-Disposition: attachment; filename=\"".$e_filename.".csv\"");
	header("Expires: 0");
	header("Pragma: public");
	
	$restxt = str_replace('瀞','__',$e_body);
	$restxt = str_replace('汘','__',$e_body);
	$restxt = "\xEF\xBB\xBF".$restxt;
	echo $restxt;
	exit;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head >
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link  href="css/style.css" type="text/css" rel="stylesheet">
<title><?php echo $strAdminTitle; ?></title>
<script type="text/javascript" src="../../base/js/base.js"></script>
<script type="text/javascript" src="../../base/js/blockui.js"></script>
<script type="text/javascript" src="js/order.js?55233"></script>
<script type="text/javascript" src="js/Date/WdatePicker.js"></script>
<style>
.list:hover {
    background-color: #f4f4f4;
}
.list td {
    border-bottom: 1px #ccc solid;
    padding: 8px 0;
}
</style>
</head>

<body>
<div class="searchzone">
<table width="100%" border="0" cellspacing="0" cellpadding="3">
 
  <tr> 
      <td height="28"  > 
        <table border="0" cellspacing="1" cellpadding="0" width="100%">
           <tr> 
            <td>
      		<div style="float:left;">
      		<form name="search" action="tuiorder.php" method="get" id="ordersearch">
    			<input name="startday" type="text"  class="input" id="startday" style="cursor:pointer" onClick="WdatePicker()"  value="<?php echo $startday;?>" size="9"  readonly/>
             -
			 <input name="endday" type="text"  class="input" id="endday" style="cursor:pointer" onClick="WdatePicker()"  value="<?php echo $endday;?>" size="9"  readonly/>
				<select name="showoktui" id="showoktui">
					<option value="0"  <?php echo seld( $showoktui, "0" );?>>未處理完成退貨訂單</option>
					<option value="1"  <?php echo seld( $showoktui, "1" );?>>已處理完成退貨訂單</option>
					<option value="ALL"  <?php echo seld( $showoktui, "ALL" );?>>全部退貨訂單</option>
				</select>
              <input type="text" name="key" value="<?php echo $key; ?>"  size="12" class="input" />
              <input type="submit" name="Submit" value="<?php echo $strSearchTitle; ?>" class="button" />
            </form>
      		</div>
            <div style="float:left;margin-left:20px;">
            <form name="receiptform" action="tuiorder.php" method="get" id="orderreceipt">
           			<input type="hidden" name="step" value="return" />  
           			<input type="hidden" name="sd" id="sd" value="" />
           			<input type="hidden" name="ed" id="ed" value="" />
           		   <input class="button" type="button" value="<?php echo "依日期區間輸出完成退貨訂單";?>" id="subreceipt" style="cursor:pointer;border:1px solid #000;">
           	</form>
           	</div>
      
	      </td>
          </tr>
        </table>
    </td>
  </tr> 
</table>

</div>

<?php

$scl = " itemtui='1' ";

if ( $key != "" )
{
		$scl .= " and (OrderNo regexp '{$key}' or items regexp '{$key}' or name regexp '{$key}' or s_name regexp '{$key}') ";
}

if( $starttime && $endtime ){
	$scl .= " and tuitime>{$starttime} and tuitime<{$endtime} ";
}

if( $showoktui!="ALL" && $showoktui>0 ){
	$scl .= " and clicktuipos > 0 and tuiok > 0";
}elseif($showoktui!="ALL"){
	$scl .= " and (clicktuipos = 0 OR tuiok = 0)";
}

 $scl .= " and orderid>'5297'";

 $scl .= " or (ifchg='1'  and chgtime>{$starttime} and chgtime<{$endtime} ) ";

$msql->query( "select count(orderid) from {P}_shop_order where {$scl} order by tuitime desc" );
if ( $msql->next_record( ) )
{
		$totalnums = $msql->f( "count(orderid)" );
}
$pages = new pages( );
$pages->setvar( array(
		"key" => $key,
		"shownum" => $shownum,
		"showoktui" => $showoktui,
		"startday" => $startday,
		"endday" => $endday
) );
$pages->set( $shownum, $totalnums );
$pagelimit = $pages->limit( );
?>
<div class="listzone">
<table width="100%" border="0" cellspacing="0" cellpadding="5" align="center">
          <tr>
            <td width="65" height="23"  class="biaoti" style="padding-left:10px">訂單號</td>
            <td width="65" height="23"  class="biaoti" >訂購人</td>
            <td height="23"  class="biaoti" >退貨商品</td>
            <td width="70"  class="biaoti" >商品總價</td>
            <td width="50"  class="biaoti" >運費</td>
            <td width="50"  class="biaoti" >餘額付費</td>
    		<td width="50"  class="biaoti" >折價金額</td>
            <td width="65" height="23"  class="biaoti" >訂單總額</td>
            <td width="75" height="23"  class="biaoti" >退訂商品金額</td>
            <td width="80" height="23"  class="biaoti" >下單時間</td>
            <td width="80" height="23"  class="biaoti" >申請退訂時間</td>
            <td width="80" height="23"  class="biaoti" >操作退訂時間</td>
		    <td width="33" height="23" align="center"  class="biaoti" <?php echo $dodis; ?>>付款</td>
            <td width="50" height="23" align="center"  class="biaoti">派遣快遞</td>
            <td width="50" height="23" align="center"  class="biaoti">收到退貨</td>
            <td width="33" height="23" align="center"  class="biaoti" >詳情</td>
		    <td height="23" width="33"  class="biaoti" align="center" >退貨</td>
          </tr>
          
<?php
$msql->query( "select * from {P}_shop_order where {$scl} order by tuitime desc limit {$pagelimit}" );
while ( $msql->next_record( ) )
{
		$orderid = $msql->f( "orderid" );
		$OrderNo = $msql->f( "OrderNo" );
		$memberid = $msql->f( "memberid" );
		$user = $msql->f( "user" );
		$name = $msql->f( "name" );
		$goodstotal = $msql->f( "goodstotal" );
		$yunzoneid = $msql->f( "yunzoneid" );
		$yunid = $msql->f( "yunid" );
		$yuntype = $msql->f( "yuntype" );
		$yunfei = $msql->f( "yunfei" );
		$yunbaofei = $msql->f( "yunbaofei" );
		$paytotal = $msql->f( "paytotal" );
		$iflook = $msql->f( "iflook" );
		$payid = $msql->f( "payid" );
		$paytype = $msql->f( "paytype" );
		$ifpay = $msql->f( "ifpay" );
		$ifyun = $msql->f( "ifyun" );
		$iftuiyun = $msql->f( "iftuiyun" );
		$ifgettui = $msql->f( "ifgettui" );
		$ifok = $msql->f( "ifok" );
		$iftui = $msql->f( "iftui" );
		$ifchg = $msql->f("ifchg");
		$dtime = $msql->f( "dtime" );
		$paytime = $msql->f( "paytime" );
		$yuntime = $msql->f( "yuntime" );
		$tuiyuntime = $msql->f( "tuiyuntime" );
		$gettuitime = $msql->f( "gettuitime" );
		//$items = $msql->f( "items" );
		$ordertime = date( "Y-m-d H:i:s", $dtime );
		$dtime = date( "y-m-d", $dtime );
		$paytime = date( "y-m-d", $paytime );
		//$uptime = date( "y-m-d", $uptime );
		$tuiyuntime = $tuiyuntime? date( "y-m-d H:i:s", $tuiyuntime ):"---";
		$gettuitime = $gettuitime? date( "y-m-d H:i:s", $gettuitime ):"---";
		$memname = $msql->f( "name" );
		$membermail = $msql->f( "email" );
		$disaccount = $msql->f( "disaccount" );
		$promoprice = $msql->f( "promoprice" );
		$promocode = $msql->f( "promocode" );
		if($promocode != ""){
			$getPromo = $tsql->getone( "select type,type_value from {P}_shop_promocode where code='{$promocode}'" );
			if($getPromo["type"]==2){
				$prorate = $getPromo["type_value"];
			}
		}else{
			$prorate = 1;
		}
		
		
		$tuitime = date( "y-m-d H:i:s",  $msql->f( "tuitime" ) );
		$chgtime = date( "y-m-d H:i:s",  $msql->f( "chgtime" ) );
		if($ifchg)
		{
			if( $msql->f( "chgtime" )<$msql->f( "uptime" ) ){
				$uptime = date( "y-m-d H:i:s",  $msql->f( "uptime" ) );
			}else{
				$uptime = "---";
			}
		}
		else
		{
		if( $msql->f( "tuitime" )<$msql->f( "uptime" ) ){
			$uptime = date( "y-m-d H:i:s",  $msql->f( "uptime" ) );
		}else{
			$uptime = "---";
		}
		}
		$items=$itemifpays="";
		$tuitjine="0";
		$fsql->query( "SELECT bn,iftui,ifpay,ifchg,id,goods,nums,tuitime,chgtime,fz,colorname,jine FROM {P}_shop_orderitems WHERE orderid='$orderid' and (itemtui='1' or ifchg='1')" );
		while( $fsql->next_record() ){
			$itid = $fsql->f("id");
			$iftui = $fsql->f("iftui");
			$ifchg = $fsql->f("ifchg");
			$itemifpay = $fsql->f("ifpay");
			$bn = $fsql->f("bn");
			$goods = $fsql->f("goods");
			$nums = $fsql->f("nums");
			$jine = $fsql->f("jine");
			if($prorate<1){
				$jine = floor($jine*$prorate);
			}
			$colorname = $fsql->f("colorname");
			list($size) = explode("^",$fsql->f("fz"));
			$tuitime = date( "y-m-d H:i:s",  $fsql->f( "tuitime" ) );
			$chgtime = date( "y-m-d H:i:s",  $fsql->f( "chgtime" ) );
			$goods = $bn." ".$goods."(".$colorname."/".$size."/".$nums.")";
			
			if($iftui){
				$items .= $items? "<br /><span style=\"color:red\"><退></span><del>".$goods."(".$nums.")</del>":"<span style=\"color:red\"><退></span><del>".$goods."(".$nums.")</del>";
				$tuitjine = $tuitjine+$jine;
			}else{
				if($ifchg)
				{
					$items .= $items? "<br /><span style=\"color:blue\"><換></span><del>".$goods."(".$nums.")</del>":"<span style=\"color:blue\"><換></span><del>".$goods."(".$nums.")</del>";
					$tuitime=$chgtime;
				}
				else
				{
				$items .= $items? "<br />".$goods:$goods;
				}
			}
			
			$itemifpays += $itemifpay;
		}
		
		if($itemifpays>0){
			$ifpay = 1;
		}else{
			$ifpay = 0;
		}
	
		
		switch ( $ifpay )
		{
		case "0" :
				$payimg = "toolbar_no.gif";
				break;
		case "1" :
				$payimg = "toolbar_ok.gif";
				break;
		}
		switch ( $iftuiyun )
		{
		case "0" :
				$tuiyunimg = "toolbar_no.gif";
				break;
		case "1" :
				$tuiyunimg = "toolbar_ok.gif";
				break;
		}
		switch ( $ifgettui )
		{
		case "0" :
				$gettuiimg = "toolbar_no.gif";
				break;
		case "1" :
				$gettuiimg = "toolbar_ok.gif";
				break;
		}
		if ( $memberid == "0" )
		{
				$user = "非會員";
		}
		echo "<tr class=\"list\" id=\"tr_".$orderid."\" >
             <td width=\"65\" valign=\"top\"  style=\"padding-left:10px\">".$OrderNo." </td>
			 <td width=\"65\" valign=\"top\">".$name."</td>
			 <td valign=\"top\">".$items.$noteifpay[$orderid]." </td>
			 <td width=\"70\" valign=\"top\" >";
		if ( $ifpay != "1" && $ifok != "1" && iftui != "1" )
		{
				echo "<input id=\"goodstotal_".$orderid."\" class=\"modiprice\" value=\"".(INT)$goodstotal."\" style=\"width:65px\" readonly />";
		}
		else
		{
				echo (INT)$goodstotal;
		}
		echo "</td>
			 <td width=\"50\" valign=\"top\">";
		if ( $ifpay != "1" && $ifok != "1" && iftui != "1" )
		{
				echo "<input id=\"yunfei_".$orderid."\" class=\"modiyunfei\" value=\"".(INT)$yunfei."\" style=\"width:45px\" readonly />";
		}
		else
		{
				echo (INT)$yunfei;
		}
		echo "</td>
			<td width=\"70\" valign=\"top\" id=\"disaccount_".$orderid."\">".(INT)$disaccount."</td>
			<td width=\"70\" valign=\"top\" id=\"promoprice_".$orderid."\">".(INT)$promoprice."</td>
			 <td width=\"65\" valign=\"top\" id=\"paytotal_".$orderid."\">".(INT)$paytotal."</td>
			 <td width=\"75\" valign=\"top\" >".number_format($tuitjine)."</td>
             <td width=\"80\" valign=\"top\">".$ordertime." </td>
             <td width=\"80\" valign=\"top\">".$tuitime." </td>
             <td width=\"80\" valign=\"top\">".$uptime." </td>
             <td width=\"33\" align=\"center\" valign=\"top\" ".$dodis."><img id=\"orderpay_".$orderid."\" src=\"images/".$payimg."\"  border=\"0\" class=\"orderpay\" style=\"cursor:pointer\" /></td>
             <td width=\"33\" align=\"center\" valign=\"top\"><img id=\"tuiyun_".$orderid."\" src=\"images/".$tuiyunimg."\"  border=\"0\" class=\"ordertuiyun\"  style=\"cursor:pointer\" /><br/>".$tuiyuntime."</td>
             <td width=\"33\" align=\"center\" valign=\"top\"><img id=\"gettui_".$orderid."\" src=\"images/".$gettuiimg."\"  border=\"0\" class=\"ordergettui\"  style=\"cursor:pointer\" /><br/>".$gettuitime."</td>
             <td width=\"33\" align=\"center\" valign=\"top\" ><a href=\"order_detail.php?orderid=".$orderid."\"><img src=\"images/look.png\" width=\"24\" height=\"24\"  border=\"0\" /></a> </td>
             <td width=\"33\" align=\"center\" valign=\"top\" ><img id=\"ordertui_".$orderid."\" src=\"images/delete.png\"  border=\"0\" class=\"ordertui\"  style=\"cursor:pointer\" />
			 </td>
           </tr>";
			$tuitotal = $tuitotal+$tuitjine;
}
echo "
<tr>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td>
		退訂總額
	</td>
	<td>
		".number_format($tuitotal)."
	</td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
</tr>
</table>
</div>
";
$pagesinfo = $pages->shownow( );
?>
<div id="showpages">
	  <div id="pagesinfo"><?php echo $strPagesTotalStart.$totalnums.$strPagesTotalEnd; ?> <?php echo $strPagesMeiye.$pagesinfo['shownum'].$strPagesTotalEnd; ?> <?php echo $strPagesYeci; ?> <?php echo $pagesinfo['now']."/".$pagesinfo['total']; ?></div>
	  <div id="pages"><?php echo $pages->output( 1 ); ?></div>
</div><script src="../../base/admin/assets/js/plugins/iframeautoheight/iframeResizer.contentWindow.min.js"></script>
	<br><br><br><br>
</body>
</html>