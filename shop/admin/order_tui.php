<?php
define( "ROOTPATH", "../../" );
include( ROOTPATH."includes/admin.inc.php" );
include( "language/".$sLan.".php" );
needauth( 321 );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head >
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link  href="css/style.css" type="text/css" rel="stylesheet">
<title><?php echo $strAdminTitle;?></title>
<script type="text/javascript" src="../../base/js/base.js"></script>
<script type="text/javascript" src="js/ordertui.js?570"></script>
</head>
<body>

<?php
$orderid = $_GET['orderid'];
$fsql->query( "select * from {P}_shop_order where orderid='{$orderid}'" );
if ( $fsql->next_record( ) )
{
		$oiftui = $fsql->f( "iftui" );
		$oifok = $fsql->f( "ifok" );
		$oifchg = $fsql->f( "ifchg" );
		$hiddenButton = false;
		$clicktuipos = $fsql->f( "clicktuipos" );
		$tuireason = $fsql->f( "tui_reason" );
		
		if ( $oiftui == "1" )
		{
				$tuithis = "歸入有效訂單";
				$tuithisclass = "doopenthis";
		}
		else
		{
				if($oifchg)
				{
					$hiddenButton =true;

				}
				else
				{
				$tuithis = "歸入退訂訂單";
				$tuithisclass = "dotuithis";
				}
		}
}
if($oifok=="0"){
?>
<div class="listzone" style="margin:10px">
<table width="100%" border="0" cellpadding="3" cellspacing="0">
  <tr>
    <td width="3" height="23" class="biaoti">&nbsp;</td>
    <td width="75" height="23" class="biaoti">商品編號</td>
    <td height="23" class="biaoti">商品名稱</td>
    <td width="70" height="23" class="biaoti">單價(元)</td>
    <td width="39" height="23" class="biaoti">數量</td>
	<td width="39" height="23" class="biaoti">單位</td>
    <td width="70" height="23" class="biaoti">小計(元)</td>
    <td height="23" colspan="2" class="biaoti">訂貨</td>
    </tr>
 
<?php
$msql->query( "select * from {P}_shop_orderitems where orderid='{$orderid}'" );
while ( $msql->next_record( ) )
{
		$itemid = $msql->f( "id" );
		$memberid = $msql->f( "memberid" );
		$orderid = $msql->f( "orderid" );
		$gid = $msql->f( "gid" );
		$bn = $msql->f( "bn" );
		$goods = $msql->f( "goods" );
		$changegoods = $msql->f( "goods" );
		$changesize = $msql->f( "chg_size" );
		$price = $msql->f( "price" );
		$weight = $msql->f( "weight" );
		$nums = $msql->f( "nums" );
		$danwei = $msql->f( "danwei" );
		$jine = $msql->f( "jine" );
		$cent = $msql->f( "cent" );
		$iftui = $msql->f( "iftui" );
		$ifchg = $msql->f( "ifchg" );
		$ifyun = $msql->f( "ifyun" );
		$yuntime = $msql->f( "yuntime" );
		$msg = $msql->f( "msg" );
		$yuntime = date( "Y-n-j", $yuntime );
		
		$itemtui = $msql->f( "itemtui" );
		$colorname = $msql->f( "colorname" );
		
		/*----------取得商品款式、顏色和尺寸----------*/
		$fz = $msql->f( "fz" );
		list($size, $money, $specid) = explode("^",$fz);
		
		if ( $ifchg == "1" )
		{
			$goods = $ifchg? $goods."(".$colorname."/".$size.")<span style=\"color:red\"><申請換貨></span> <button class=\"button cancelchg\" id=\"ttid_".$itemid."\">取消換貨</button>":$goods."(".$colorname."/".$size.")";
			$changeitems.=$changeitems? "<br />".$changegoods."(".$colorname."/".$changesize.")":$changegoods."(".$colorname."/".$changesize.")";
		}
		else
		{
			$goods = $itemtui? $goods."(".$colorname."/".$size.")<span style=\"color:red\"><申請退貨></span> <button class=\"button canceltui\" id=\"ttid_".$itemid."\">取消退貨</button>":$goods."(".$colorname."/".$size.")";
		
		}
		//$goods = $itemtui? $goods."(".$colorname."/".$size.")<span style=\"color:red\"><申請退貨></span> <button class=\"button canceltui\" id=\"ttid_".$itemid."\">取消退貨</button>":$goods."(".$colorname."/".$size.")";
		/*--------------------------------------*/
		
		if ( $iftui == "1" )
		{
				$yunimg = "toolbar_no.gif";
				$yuntext = "訂貨";
		}
		else
		{
			if ( $ifchg == "1" )
			{
				$yunimg = "toolbar_ok.gif";
				$yuntext = "換貨";
			}
			else
			{
				$yunimg = "toolbar_ok.gif";
				$yuntext = "退訂";
			}
		}
		echo "<tr>
    <td width=\"3\" height=\"25\">&nbsp;</td>
    <td width=\"75\" height=\"25\">".$bn."</td>
    <td height=\"25\">".$goods."</td>
    <td width=\"70\" height=\"25\">".$price."</td>
    <td width=\"39\" height=\"25\" id=\"nums_".$itemid."\">".$nums."</td>
    <td width=\"39\" height=\"25\">".$danwei."</td>
    <td width=\"70\" height=\"25\">".$jine."</td>
    <td width=\"30\" height=\"25\"><img id=\"tuistat_".$itemid."\" src=\"images/".$yunimg."\"  border=\"0\"  /></td>
	<td width=\"50\" height=\"25\" align=\"center\">
	  <input name=\"cc\" type=\"button\" id=\"dotui_".$itemid."\" class=\"dotui\" value=\"".$yuntext."\" /></td>
  </tr>
  		";
}
?>
</table>
</div>
<div class="listzone" style="margin:10px">
<table width="100%" border="0" cellpadding="3" cellspacing="0">
  <tr>
    <td width="3" height="23" class="biaoti">&nbsp;</td>
	<td height="23" class="biaoti" <?php echo $hiddenButton ? 'hidden' : ''; ?>>退貨理由</td>
	<td height="23" class="biaoti" <?php echo $hiddenButton ? '' : 'hidden'; ?>>換貨尺寸</td>
  </tr>
  <tr>
  	<td width="3" height="25">&nbsp;</td>
	<td height="25" style="display: <?php echo $hiddenButton ? 'none' : 'inline'; ?>"><?php echo $tuireason; ?></td>
	<td height="25" style="display: <?php echo $hiddenButton ? 'inline' : 'none'; ?>"><?php echo $changeitems; ?></td>
  </tr>
</table>
</div>
<?php }?>
	<!--div style="float:left;width:120px;margin-left: 10px;"><input name="posdin" id="posdin_<?php echo $orderid;?>" type="button" class="posdin" value="產生POS訂貨單" /></div-->
	<div style="float:right;width:200px;"><input name="postui" id="postui_<?php echo $orderid;?>" type="button" class="postui" value="點選回傳 ERP退貨(曾點選<?php echo $clicktuipos;?>次)" style="display: <?php echo $hiddenButton ? 'none' : 'inline'; ?>"/></div>
		<div style="margin:0px 20px 10px 10px;width:120px;float:left;display: <?php echo $hiddenButton ? 'none' : 'inline'; ?>"><input name="tuithis" id="dountui_<?php echo $orderid;?>" type="button" class="dountui" value="取消訂單退訂註記" /></div>
		
		<div style="margin:0px 20px 10px 10px;width:120px;float:left;display: <?php echo $hiddenButton ? 'inline' : 'none'; ?>"><input name="tuithis" id="dounchg_<?php echo $orderid;?>" type="button" class="dounchg" value="取消訂單換貨註記" /></div>
	<div style="margin:0px 60px 10px 10px;width:120px;float:right;"><input name="tuithis" id="dotuithis_<?php echo $orderid;?>" type="button" class="<?php echo $tuithisclass;?>" value="<?php echo $tuithis;?>" style="display: <?php echo $hiddenButton ? 'none' : 'inline'; ?>"/></div>
<p>&nbsp;</p>
</body>
</html>