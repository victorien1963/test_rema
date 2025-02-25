<?php
define( "ROOTPATH", "../../" );
include( ROOTPATH."includes/admin.inc.php" );
include( ROOTPATH."includes/pages.inc.php" );
include( ROOTPATH."includes/ebmail.inc.php" );
include( "func/query.inc.php" );
include( "language/".$sLan.".php" );
needauth( 321 );

$page = $_REQUEST['page'];	
$step = $_REQUEST['step'];
$key = $_GET['key'];
$shownum = $_REQUEST['shownum'];
$showpay = $_GET['showpay'];
$showyun = $_GET['showyun'];
$showtui = $_GET['showtui'];
$showok = $_GET['showok'];
$startday = $_GET['startday'];
$endday = $_GET['endday'];
$showcountry = $_GET['showcountry'];
$showsource = $_GET['showsource'];
$showcity = $_GET['showcity'];

if ( $startday == "" || $endday == "" )
{
		$endday = date( "Y-m-d" );
		$enddayArr = explode( "-", $endday );
		$endtime = mktime( 23, 59, 59, $enddayArr[1], $enddayArr[2], $enddayArr[0] );
		$starttime = $endtime - (86400*11-1);
		$startday = date( "Y-m-d", $starttime );
}
else
{
		$enddayArr = explode( "-", $endday );
		$endtime = mktime( 23, 59, 59, $enddayArr[1], $enddayArr[2], $enddayArr[0] );
		$startdayArr = explode( "-", $startday );
		$starttime = mktime( 0, 0, 0, $startdayArr[1], $startdayArr[2], $startdayArr[0] );
}


if($_GET["searchtime"]){
list($fromY,$fromM,$fromD) = explode("-",$_GET["searchtime"]);
$endtime = mktime (23, 59, 59, $fromM,$fromD,$fromY);
$starttime = mktime (0, 0, 0, $fromM,$fromD,$fromY);
$startday = $endday = $_GET["searchtime"];
}

if ( $showcountry == "" )
{
		$showcountry = "all";
}
if ( $showsource == "" )
{
		$showsource = "all";
}
if ( $showcity == "" )
{
		$showcity = "all";
}
if ( $showpay == "" )
{
		$showpay = "all";
}
if ( $showyun == "" )
{
		$showyun = "all";
}
if ( $showok == "" )
{
		$showok = "0";
}
if ( $showtui == "" )
{
		$showtui = "0";
}
if ( $shownum == "" || $shownum < 10 )
{
		$shownum = 10;
}
//輸出訂單成csv
$csvday = $_REQUEST['csvday']? $_REQUEST['csvday']:date("Y-m-d");

if($step=="output456"){//暫時不使用
//訂單號碼, 訂購人姓名, 收貨人姓名, 連絡電話, 地址, 貨到付款金額, 出貨日期--大榮
//訂單編號, 溫層, 規格, 代收貨款, 收件人 - 姓名, 收件人 - 電話, 收件人 - 手機, 收件人 - 地址, 出貨日期, 預定配達日期, 預定配達時間, 備註--黑貓
//$e_body= "訂單編號,溫層,規格,代收貨款,收件人 - 姓名,收件人 - 電話,收件人 - 手機,收件人 - 地址,出貨日期,預定配達日期,預定配達時間,備註\n";//黑貓
$scl_output = "ifyun='1' and iftui='0'";
//今日已按配送的訂單輸出
$getdate = explode("-",$csvday);
$fromY = $getdate[0];
$fromM = $getdate[1];
$fromD = $getdate[2];
$starttime = mktime (0,0,0,$fromM,$fromD,$fromY);
$endtime = mktime (23,59,59,$fromM,$fromD,$fromY);
$scl_output .= " and yuntime>='{$starttime}' and yuntime<='{$endtime}'";

	$msql->query( "select * from {P}_shop_order where {$scl_output} and source='0' order by dtime desc" );
	while($msql->next_record()){
		$e_OrderNo=$msql->f("OrderNo");
		$e_name=$msql->f("name");
		$e_s_name=$msql->f("s_name");
		$e_s_tel=$msql->f("s_tel");
		$e_s_mobi=$msql->f("s_mobi");
		$e_s_addr=$msql->f("s_addr");
		$e_payid=$msql->f("payid");
		$e_paytotal= $e_payid == 2 ? (INT)$msql->f("paytotal"):"";
		//$yy = date("Y",time())-1911;//大榮
		//$ym = str_pad(date("m",time()),2,"0",STR_PAD_LEFT);//大榮
		//$yd = str_pad(date("d",time()),2,"0",STR_PAD_LEFT);//大榮
		//$e_yuntime=$yy.$ym.$yd;//大榮
		
		$e_yuntime = date("Ymd",time());//黑貓
		$e_yuntime_t = date("Ymd",time()+86400);//黑貓
		
		//.csv數字轉字串
		$e_OrderNo="=\"".$e_OrderNo."\"";
		$e_s_tel = str_replace('-','',$e_s_tel);
		$e_s_mobi = str_replace('-','',$e_s_mobi);
		$e_s_tel="=\"".$e_s_tel."\"";
		$e_mobi="=\"".$e_s_mobi."\"";
		$e_contact=$e_mobi;
		
		//$e_body.= $e_OrderNo.",".$e_name.",".$e_s_name.",".$e_contact.",".$e_s_addr.",".$e_paytotal.",".$e_yuntime."\n";//大榮
		//$e_body= "訂單編號, 溫層, 規格, 代收貨款, 收件人 - 姓名, 收件人 - 電話, 收件人 - 手機, 收件人 - 地址, 出貨日期, 預定配達日期, 預定配達時間, 備註\n";//黑貓
		$e_body.= $e_OrderNo.",1,1,".$e_paytotal.",".$e_s_name.",".$e_s_tel.",".$e_contact.",".$e_s_addr.",".$e_yuntime.",".$e_yuntime_t.",1,\n";//黑貓

		
	}
	$e_filename = date('YmdHis',time()).'_order_output';
	header('Content-Type: text/html; charset=utf-8' ); 
	header("Content-Disposition: attachment; filename=\"".$e_filename.".csv\"");
	header("Expires: 0");
	header("Pragma: public");
	
	$restxt = str_replace('瀞','__',$e_body);
	$restxt = str_replace('汘','__',$e_body);
	echo mb_convert_encoding($restxt,"BIG5","UTF-8");
	//$restxt = "\xEF\xBB\xBF".$restxt;
	//echo $restxt;	
	
	exit;
}

if($step=="hct"){
//$scl_output = "ifyun='1' and iftui='0'";
//今日已按配送的訂單輸出
$scl_output = "iflook='1' and iftui='0'";
//今日已按處理的訂單輸出
$getdate = explode("-",$csvday);
$fromY = $getdate[0];
$fromM = $getdate[1];
$fromD = $getdate[2];
$starttime = mktime (0,0,0,$fromM,$fromD,$fromY);
$endtime = mktime (23,59,59,$fromM,$fromD,$fromY);
//$scl_output .= " and yuntime>='{$starttime}' and yuntime<='{$endtime}' AND (source='' || source='0')";
$scl_output .= " and looktime>='{$starttime}' and looktime<='{$endtime}' AND (source='' || source='0')";
		//載入 PHPEXCEL
		include_once("func/Classes/PHPExcel.php");
		include_once("func/Classes/PHPExcel/IOFactory.php");
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		// 設置屬性
		$objPHPExcel->getProperties()->setCreator("測試作者")//作者
		   ->setLastModifiedBy("測試修改者")//最後修改者
		   ->setTitle("測試標題")//標題
		   ->setSubject("測試主旨")//主旨
		   ->setDescription("測試註解")//註解
		   ->setKeywords("測試標記")//標記
		   ->setCategory("測試類別");//類別
		//Create a first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		//設定欄位寬度
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);

		//行號
		$excel_line = 1;

		//產生第一列
		$objPHPExcel->getActiveSheet()->setCellValue("A{$excel_line}", "序號");
		$objPHPExcel->getActiveSheet()->setCellValue("B{$excel_line}", "訂單號");
		$objPHPExcel->getActiveSheet()->setCellValue("C{$excel_line}", "收件人姓名");
		$objPHPExcel->getActiveSheet()->setCellValue("D{$excel_line}", "收件人地址");
		$objPHPExcel->getActiveSheet()->setCellValue("E{$excel_line}", "收件人電話");
		$objPHPExcel->getActiveSheet()->setCellValue("F{$excel_line}", "託運備註");
		$objPHPExcel->getActiveSheet()->setCellValue("G{$excel_line}", "商品別編號");
		$objPHPExcel->getActiveSheet()->setCellValue("H{$excel_line}", "商品數量");
		$objPHPExcel->getActiveSheet()->setCellValue("I{$excel_line}", "才積重量");
		$objPHPExcel->getActiveSheet()->setCellValue("J{$excel_line}", "代收貨款");
		$objPHPExcel->getActiveSheet()->setCellValue("K{$excel_line}", "指定配送日期");
		$objPHPExcel->getActiveSheet()->setCellValue("L{$excel_line}", "指定配送時間");


	$msql->query( "select * from {P}_shop_order where {$scl_output} order by dtime desc" );
	while($msql->next_record()){
		$e_OrderNo=$msql->f("OrderNo");
		$e_name=$msql->f("name");
		$e_s_name=$msql->f("s_name");
		$e_s_tel=$msql->f("s_tel");
		$e_s_mobi=$msql->f("s_mobi");
		$e_s_addr=$msql->f("s_addr");
		$e_payid=$msql->f("payid");
		$e_paytotal= $e_payid == 2 || $e_payid == 5? (INT)$msql->f("paytotal"):"";
		$e_yuntime = date("Ymd",time());//黑貓
		$e_yuntime_t = date("Ymd",time()+86400);//黑貓
			
		$e_paytotal = $e_paytotal? $e_paytotal:"0";
		
				//匯出 EXCEL訂單
				/*$e_id=$msql->f("orderid");
				$e_items=mb_substr( $msql->f("items"),0,46,"utf-8" )."...";
				$getnums = $fsql->getone("SELECT SUM(nums)  FROM {P}_shop_orderitems WHERE orderid='$e_id'");
				$e_totalnums = $getnums["SUM(nums)"];*/
				$excel_line++;
				//產生其他列
				$objPHPExcel->getActiveSheet()->setCellValue("A{$excel_line}", "");
				$objPHPExcel->getActiveSheet()->setCellValueExplicit("B{$excel_line}", (string) $e_OrderNo,PHPExcel_Cell_DataType::TYPE_STRING); 
				$objPHPExcel->getActiveSheet()->setCellValue("C{$excel_line}", $e_s_name);
				$objPHPExcel->getActiveSheet()->setCellValue("D{$excel_line}", $e_s_addr);
				$objPHPExcel->getActiveSheet()->setCellValue("E{$excel_line}", (string) $e_s_mobi,PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->getActiveSheet()->setCellValue("F{$excel_line}", "");
				$objPHPExcel->getActiveSheet()->setCellValue("G{$excel_line}", "");
				$objPHPExcel->getActiveSheet()->setCellValue("H{$excel_line}", "1");
				$objPHPExcel->getActiveSheet()->setCellValue("I{$excel_line}", "1");
				$objPHPExcel->getActiveSheet()->setCellValue("J{$excel_line}", $e_paytotal);
				$objPHPExcel->getActiveSheet()->setCellValue("K{$excel_line}", $e_yuntime_t);
				$objPHPExcel->getActiveSheet()->setCellValue("L{$excel_line}", "1");
		
	}

		//產生Excel
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');//20003格式
		// We'll be outputting an excel file
		header('Content-type: application/vnd.ms-excel');
		// It will be called file.xls
		$e_filename = date('YmdHis',time()).'_order_hct.xls';
		header('Content-Disposition: attachment; filename="'.$e_filename.'"');
		// Write file to the browser
		$objWriter->save('php://output');
	
	
	exit;
}

function getCityName($address) {
	$cityList = array(
		"基隆市",
		"台北市",
		"新北市",
		"桃園市",
		"新竹市",
		"新竹縣",
		"苗栗縣",
		"台中市",
		"彰化縣",
		"南投縣",
		"雲林縣",
		"嘉義市",
		"嘉義縣",
		"台南市",
		"高雄市",
		"屏東縣",
		"台東縣",
		"花蓮縣",
		"宜蘭縣",
		"澎湖縣",
		"金門縣",
		"連江縣"
	);

	foreach ($cityList as $city) {
		if ( strpos($address, $city) !== false ) {
			return $city;
		}
	}
	return $address;
}

function getAreaName($address) {
	$area = ['區', '市', '鎮', '鄉'];

	$array = null;
	foreach ($area as $city) {
		if ( strpos($address, $city) !== false ) {
			$array = explode($city, $address);
			return $array['0'] . $city;
		}
	}
	return $address;
}

if ( $step == 'sf' ) {
	// $scl_output = "ifyun='1' and iftui='0'";
	// 今日已按配送的訂單輸出
	$scl_output = "iflook='1' and iftui='0'";
	// 今日已按處理的訂單輸出
	$getdate   = explode("-",$csvday);
	$fromY     = $getdate[0];
	$fromM     = $getdate[1];
	$fromD     = $getdate[2];
	$starttime = mktime (0,0,0,$fromM,$fromD,$fromY);
	$endtime   = mktime (23,59,59,$fromM,$fromD,$fromY);
	// $scl_output .= " and yuntime>='{$starttime}' and yuntime<='{$endtime}' AND (source='' || source='0')";
	$scl_output .= " and looktime>='{$starttime}' and looktime<='{$endtime}' AND (source='' || source='0')";
	// 載入 PHPEXCEL
	include_once("func/Classes/PHPExcel.php");
	include_once("func/Classes/PHPExcel/IOFactory.php");
	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();
	// 設置屬性
	$objPHPExcel->getProperties()->setCreator("測試作者")//作者
		->setLastModifiedBy("測試修改者")//最後修改者
		->setTitle("測試標題")//標題
		->setSubject("測試主旨")//主旨
		->setDescription("測試註解")//註解
		->setKeywords("測試標記")//標記
		->setCategory("測試類別");//類別
	// Create a first sheet
	$objPHPExcel->setActiveSheetIndex(0);
	// 設定欄位寬度
	$columnIterator = $objPHPExcel->getActiveSheet()->getColumnIterator('A', 'CF');
	foreach ($columnIterator as $column) {
		$columnIndex = $column->getColumnIndex();
		$objPHPExcel->getActiveSheet()->getColumnDimension($columnIndex)->setWidth(30);
	}
	// 產生第一列
	$excel_line = 1;
	// 字型粗體
	$objPHPExcel->getActiveSheet()->getStyle($excel_line)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(180);

	$excel_array = [1, 2];
	foreach ($excel_array as $v) {
		// Set the horizontal alignment to left
		$objPHPExcel->getActiveSheet()->getStyle($v)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		// Set the vertical alignment to center
		$objPHPExcel->getActiveSheet()->getStyle($v)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		// Set the wrap text property
		$objPHPExcel->getActiveSheet()->getStyle($v)->getAlignment()->setWrapText(true);
		// Set the font to Microsoft YaHei
		$objPHPExcel->getActiveSheet()->getStyle($v)->getFont()->setName('Microsoft YaHei');
	}

	$columnIterator = $objPHPExcel->getActiveSheet()->getColumnIterator('A', 'D');
	foreach ($columnIterator as $column) {
		$columnIndex = $column->getColumnIndex();
		$objPHPExcel->getActiveSheet()->getStyle("{$columnIndex}{$excel_line}")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle("{$columnIndex}{$excel_line}")->getFill()->getStartColor()->setRGB('FFE5CC');
	}

	$columnIterator = $objPHPExcel->getActiveSheet()->getColumnIterator('E', 'U');
	foreach ($columnIterator as $column) {
		$columnIndex = $column->getColumnIndex();
		$objPHPExcel->getActiveSheet()->getStyle("{$columnIndex}{$excel_line}")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle("{$columnIndex}{$excel_line}")->getFill()->getStartColor()->setRGB('87CEEB');
	}

	$columnIterator = $objPHPExcel->getActiveSheet()->getColumnIterator('V', 'AM');
	foreach ($columnIterator as $column) {
		$columnIndex = $column->getColumnIndex();
		$objPHPExcel->getActiveSheet()->getStyle("{$columnIndex}{$excel_line}")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle("{$columnIndex}{$excel_line}")->getFill()->getStartColor()->setRGB('FFD1DC');
	}

	$columnIterator = $objPHPExcel->getActiveSheet()->getColumnIterator('AN', 'AW');
	foreach ($columnIterator as $column) {
		$columnIndex = $column->getColumnIndex();
		$objPHPExcel->getActiveSheet()->getStyle("{$columnIndex}{$excel_line}")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle("{$columnIndex}{$excel_line}")->getFill()->getStartColor()->setRGB('FFE5CC');
	}

	$columnIterator = $objPHPExcel->getActiveSheet()->getColumnIterator('AX', 'BC');
	foreach ($columnIterator as $column) {
		$columnIndex = $column->getColumnIndex();
		$objPHPExcel->getActiveSheet()->getStyle("{$columnIndex}{$excel_line}")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle("{$columnIndex}{$excel_line}")->getFill()->getStartColor()->setRGB('B8E5FF');
	}

	$columnIterator = $objPHPExcel->getActiveSheet()->getColumnIterator('BD', 'BS');
	foreach ($columnIterator as $column) {
		$columnIndex = $column->getColumnIndex();
		$objPHPExcel->getActiveSheet()->getStyle("{$columnIndex}{$excel_line}")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle("{$columnIndex}{$excel_line}")->getFill()->getStartColor()->setRGB('BDFCC9');
	}

	$columnIterator = $objPHPExcel->getActiveSheet()->getColumnIterator('BT', 'CA');
	foreach ($columnIterator as $column) {
		$columnIndex = $column->getColumnIndex();
		$objPHPExcel->getActiveSheet()->getStyle("{$columnIndex}{$excel_line}")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle("{$columnIndex}{$excel_line}")->getFill()->getStartColor()->setRGB('D8B4FF');
	}

	$columnIterator = $objPHPExcel->getActiveSheet()->getColumnIterator('CB', 'CF');
	foreach ($columnIterator as $column) {
		$columnIndex = $column->getColumnIndex();
		$objPHPExcel->getActiveSheet()->getStyle("{$columnIndex}{$excel_line}")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle("{$columnIndex}{$excel_line}")->getFill()->getStartColor()->setRGB('B8E5FF');
	}

	$objPHPExcel->getActiveSheet()->setCellValue("A{$excel_line}", "*客戶訂單號");
	$objPHPExcel->getActiveSheet()->getStyle("A{$excel_line}")->getFont()->setColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_RED));

	$objPHPExcel->getActiveSheet()->setCellValue("B{$excel_line}", "客戶訂單號2");
	$objPHPExcel->getActiveSheet()->setCellValue("C{$excel_line}", "代理運單號");
	$objPHPExcel->getActiveSheet()->setCellValue("D{$excel_line}", "運單號");
	$objPHPExcel->getActiveSheet()->setCellValue("E{$excel_line}", "*寄件方姓名");
	$objPHPExcel->getActiveSheet()->getStyle("E{$excel_line}")->getFont()->setColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_RED));

	$objPHPExcel->getActiveSheet()->setCellValue("F{$excel_line}", "寄件方手機號");
	$objPHPExcel->getActiveSheet()->setCellValue("G{$excel_line}", "寄件方固定電話");
	$objPHPExcel->getActiveSheet()->setCellValue("H{$excel_line}", "*寄件方詳細地址");
	$objPHPExcel->getActiveSheet()->getStyle("H{$excel_line}")->getFont()->setColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_RED));

	$objPHPExcel->getActiveSheet()->setCellValue("I{$excel_line}", "寄件方縣/區");
	$objPHPExcel->getActiveSheet()->setCellValue("J{$excel_line}", "*寄件方城市");
	$objPHPExcel->getActiveSheet()->getStyle("J{$excel_line}")->getFont()->setColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_RED));

	$objPHPExcel->getActiveSheet()->setCellValue("K{$excel_line}", "*寄件方州/省");
	$objPHPExcel->getActiveSheet()->getStyle("K{$excel_line}")->getFont()->setColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_RED));

	$objPHPExcel->getActiveSheet()->setCellValue("L{$excel_line}", "*寄件方國家/地區");
	$objPHPExcel->getActiveSheet()->getStyle("L{$excel_line}")->getFont()->setColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_RED));

	$objPHPExcel->getActiveSheet()->setCellValue("M{$excel_line}", "*寄件方郵編");
	$objPHPExcel->getActiveSheet()->getStyle("M{$excel_line}")->getFont()->setColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_RED));

	$objPHPExcel->getActiveSheet()->setCellValue("N{$excel_line}", "寄件方郵箱");
	$objPHPExcel->getActiveSheet()->setCellValue("O{$excel_line}", "寄件類型");
	$objPHPExcel->getActiveSheet()->setCellValue("P{$excel_line}", "寄件方公司");
	$objPHPExcel->getActiveSheet()->setCellValue("Q{$excel_line}", "寄件方證件類型");
	$objPHPExcel->getActiveSheet()->setCellValue("R{$excel_line}", "寄件方證件號碼");
	$objPHPExcel->getActiveSheet()->setCellValue("S{$excel_line}", "寄件方VAT號");
	$objPHPExcel->getActiveSheet()->setCellValue("T{$excel_line}", "寄件方EORI號");
	$objPHPExcel->getActiveSheet()->setCellValue("U{$excel_line}", "寄件方IOSS號");

	$objPHPExcel->getActiveSheet()->setCellValue("V{$excel_line}", "*收件方姓名");
	$objPHPExcel->getActiveSheet()->getStyle("V{$excel_line}")->getFont()->setColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_RED));

	$objPHPExcel->getActiveSheet()->setCellValue("W{$excel_line}", "收件方手機號");
	$objPHPExcel->getActiveSheet()->setCellValue("X{$excel_line}", "收件方固定電話");
	$objPHPExcel->getActiveSheet()->setCellValue("Y{$excel_line}", "*收件方詳細地址");
	$objPHPExcel->getActiveSheet()->getStyle("Y{$excel_line}")->getFont()->setColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_RED));

	$objPHPExcel->getActiveSheet()->setCellValue("Z{$excel_line}", "道路名");
	$objPHPExcel->getActiveSheet()->setCellValue("AA{$excel_line}", "建築編號");
	$objPHPExcel->getActiveSheet()->setCellValue("AB{$excel_line}", "收件方縣/區");
	$objPHPExcel->getActiveSheet()->setCellValue("AC{$excel_line}", "*收件方城市");
	$objPHPExcel->getActiveSheet()->getStyle("AC{$excel_line}")->getFont()->setColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_RED));

	$objPHPExcel->getActiveSheet()->setCellValue("AD{$excel_line}", "*收件方州/省");
	$objPHPExcel->getActiveSheet()->getStyle("AD{$excel_line}")->getFont()->setColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_RED));

	$objPHPExcel->getActiveSheet()->setCellValue("AE{$excel_line}", "*收件方國家/地區");
	$objPHPExcel->getActiveSheet()->getStyle("AE{$excel_line}")->getFont()->setColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_RED));

	$objPHPExcel->getActiveSheet()->setCellValue("AF{$excel_line}", "*收件方郵編");
	$objPHPExcel->getActiveSheet()->getStyle("AF{$excel_line}")->getFont()->setColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_RED));

	$objPHPExcel->getActiveSheet()->setCellValue("AG{$excel_line}", "收件方郵箱");
	$objPHPExcel->getActiveSheet()->setCellValue("AH{$excel_line}", "收件類型");
	$objPHPExcel->getActiveSheet()->setCellValue("AI{$excel_line}", "收件方公司");
	$objPHPExcel->getActiveSheet()->setCellValue("AJ{$excel_line}", "收件方證件類型");
	$objPHPExcel->getActiveSheet()->setCellValue("AK{$excel_line}", "收件方證件號碼");
	$objPHPExcel->getActiveSheet()->setCellValue("AL{$excel_line}", "收件方VAT號(VAT/GST)");
	$objPHPExcel->getActiveSheet()->setCellValue("AM{$excel_line}", "海關登記進口商號碼(收件方EORI/IOR)");

	$objPHPExcel->getActiveSheet()->setCellValue("AN{$excel_line}", "商品編號");
	$objPHPExcel->getActiveSheet()->setCellValue("AO{$excel_line}", "*商品名稱");
	$objPHPExcel->getActiveSheet()->getStyle("AO{$excel_line}")->getFont()->setColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_RED));

	$objPHPExcel->getActiveSheet()->setCellValue("AP{$excel_line}", "規格");
	$objPHPExcel->getActiveSheet()->setCellValue("AQ{$excel_line}", "*商品數量");
	$objPHPExcel->getActiveSheet()->getStyle("AQ{$excel_line}")->getFont()->setColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_RED));

	$objPHPExcel->getActiveSheet()->setCellValue("AR{$excel_line}", "*單位");
	$objPHPExcel->getActiveSheet()->getStyle("AR{$excel_line}")->getFont()->setColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_RED));

	$objPHPExcel->getActiveSheet()->setCellValue("AS{$excel_line}", "單位重量");
	$objPHPExcel->getActiveSheet()->setCellValue("AT{$excel_line}", "*商品單價");
	$objPHPExcel->getActiveSheet()->getStyle("AT{$excel_line}")->getFont()->setColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_RED));

	$objPHPExcel->getActiveSheet()->setCellValue("AU{$excel_line}", "*原產地");
	$objPHPExcel->getActiveSheet()->getStyle("AU{$excel_line}")->getFont()->setColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_RED));

	$objPHPExcel->getActiveSheet()->setCellValue("AV{$excel_line}", "海關編碼");
	$objPHPExcel->getActiveSheet()->setCellValue("AW{$excel_line}", "國條碼");

	$objPHPExcel->getActiveSheet()->setCellValue("AX{$excel_line}", "*包裹總件數");
	$objPHPExcel->getActiveSheet()->getStyle("AX{$excel_line}")->getFont()->setColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_RED));

	$objPHPExcel->getActiveSheet()->setCellValue("AY{$excel_line}", "長度單位");
	$objPHPExcel->getActiveSheet()->setCellValue("AZ{$excel_line}", "長");
	$objPHPExcel->getActiveSheet()->setCellValue("BA{$excel_line}", "寬");
	$objPHPExcel->getActiveSheet()->setCellValue("BB{$excel_line}", "高");
	$objPHPExcel->getActiveSheet()->setCellValue("BC{$excel_line}", "*商品貨幣");
	$objPHPExcel->getActiveSheet()->getStyle("BC{$excel_line}")->getFont()->setColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_RED));

	$objPHPExcel->getActiveSheet()->setCellValue("BD{$excel_line}", "*快件類型");
	$objPHPExcel->getActiveSheet()->getStyle("BD{$excel_line}")->getFont()->setColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_RED));

	$objPHPExcel->getActiveSheet()->setCellValue("BE{$excel_line}", "附加服務1");
	$objPHPExcel->getActiveSheet()->setCellValue("BF{$excel_line}", "附加服務1代收貨款卡號");
	$objPHPExcel->getActiveSheet()->setCellValue("BG{$excel_line}", "附加服務1內容");
	$objPHPExcel->getActiveSheet()->setCellValue("BH{$excel_line}", "附加服務2");
	$objPHPExcel->getActiveSheet()->setCellValue("BI{$excel_line}", "附加服務2內容");
	$objPHPExcel->getActiveSheet()->setCellValue("BJ{$excel_line}", "附加服務3");
	$objPHPExcel->getActiveSheet()->setCellValue("BK{$excel_line}", "簽單返還內容");
	$objPHPExcel->getActiveSheet()->setCellValue("BL{$excel_line}", "簽單返還備註");
	$objPHPExcel->getActiveSheet()->setCellValue("BM{$excel_line}", "是否自取件");
	$objPHPExcel->getActiveSheet()->setCellValue("BN{$excel_line}", "追溯碼");
	$objPHPExcel->getActiveSheet()->setCellValue("BO{$excel_line}", "*寄件方式");
	$objPHPExcel->getActiveSheet()->getStyle("BO{$excel_line}")->getFont()->setColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_RED));

	$objPHPExcel->getActiveSheet()->setCellValue("BP{$excel_line}", "預約時間");
	$objPHPExcel->getActiveSheet()->setCellValue("BQ{$excel_line}", "運單備註");
	$objPHPExcel->getActiveSheet()->setCellValue("BR{$excel_line}", "出口報關方式");
	$objPHPExcel->getActiveSheet()->setCellValue("BS{$excel_line}", "進口報關方式");

	$objPHPExcel->getActiveSheet()->setCellValue("BT{$excel_line}", "*付款方式");
	$objPHPExcel->getActiveSheet()->getStyle("BT{$excel_line}")->getFont()->setColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_RED));

	$objPHPExcel->getActiveSheet()->setCellValue("BU{$excel_line}", "月結卡號");
	$objPHPExcel->getActiveSheet()->setCellValue("BV{$excel_line}", "*稅金付款方式");
	$objPHPExcel->getActiveSheet()->getStyle("BV{$excel_line}")->getFont()->setColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_RED));

	$objPHPExcel->getActiveSheet()->setCellValue("BW{$excel_line}", "稅金結算賬號");
	$objPHPExcel->getActiveSheet()->setCellValue("BX{$excel_line}", "貿易條件");
	$objPHPExcel->getActiveSheet()->setCellValue("BY{$excel_line}", "寄件原因");
	$objPHPExcel->getActiveSheet()->setCellValue("BZ{$excel_line}", "寄件原因內容");
	$objPHPExcel->getActiveSheet()->setCellValue("CA{$excel_line}", "商業發票備註");

	$objPHPExcel->getActiveSheet()->setCellValue("CB{$excel_line}", "PO Number");
	$objPHPExcel->getActiveSheet()->setCellValue("CC{$excel_line}", "進口商公司名稱");
	$objPHPExcel->getActiveSheet()->setCellValue("CD{$excel_line}", "進口商聯系電話");
	$objPHPExcel->getActiveSheet()->setCellValue("CE{$excel_line}", "進口商地址");
	$objPHPExcel->getActiveSheet()->setCellValue("CF{$excel_line}", "申報運費");
	// 行號
	++$excel_line;
	// 產生第二列
	$objPHPExcel->getActiveSheet()->setCellValue("A{$excel_line}", "必填，支持輸入字母和數字，max=64");
	$objPHPExcel->getActiveSheet()->setCellValue("B{$excel_line}", "非必填，max=24");
	$objPHPExcel->getActiveSheet()->setCellValue("C{$excel_line}", "非必填，max=40");
	$objPHPExcel->getActiveSheet()->setCellValue("D{$excel_line}", "僅支持預分配訂單的客戶可填，max=4000. 
	一個訂單只能有一個母單號，如果是子母件，以半角逗號分隔，母單號在第一個位置，如SF13123456788,SF20123456788, SF20123456789");
	$objPHPExcel->getActiveSheet()->setCellValue("E{$excel_line}", "必填，max=100");
	$objPHPExcel->getActiveSheet()->setCellValue("F{$excel_line}", "必填，max=20，寄件方手機號和固定電話至少填寫一項");
	$objPHPExcel->getActiveSheet()->setCellValue("G{$excel_line}", "必填，max=20，寄件方手機號和固定電話至少填寫一項");
	$objPHPExcel->getActiveSheet()->setCellValue("H{$excel_line}", "必填，max=200");
	$objPHPExcel->getActiveSheet()->setCellValue("I{$excel_line}", "非必填，max=128");
	$objPHPExcel->getActiveSheet()->setCellValue("J{$excel_line}", "必填，max=128");
	$objPHPExcel->getActiveSheet()->setCellValue("K{$excel_line}", "必填，max=64");
	$objPHPExcel->getActiveSheet()->setCellValue("L{$excel_line}", "必選，下拉選擇");
	$objPHPExcel->getActiveSheet()->setCellValue("M{$excel_line}", "max=20，必填。 沒有郵編的國家/地區可以不填寫。");
	$objPHPExcel->getActiveSheet()->setCellValue("N{$excel_line}", "非必填，max=128");
	$objPHPExcel->getActiveSheet()->setCellValue("O{$excel_line}", "下拉選擇");
	$objPHPExcel->getActiveSheet()->setCellValue("P{$excel_line}", "max=100，當【寄件類型】為“公司件”時，則此處必填");
	$objPHPExcel->getActiveSheet()->setCellValue("Q{$excel_line}", "下拉選擇");
	$objPHPExcel->getActiveSheet()->setCellValue("R{$excel_line}", "max=100");
	$objPHPExcel->getActiveSheet()->setCellValue("S{$excel_line}", "max=18");
	$objPHPExcel->getActiveSheet()->setCellValue("T{$excel_line}", "max=18");
	$objPHPExcel->getActiveSheet()->setCellValue("U{$excel_line}", "max=150");

	$objPHPExcel->getActiveSheet()->setCellValue("V{$excel_line}", "必填，max=100");
	$objPHPExcel->getActiveSheet()->setCellValue("W{$excel_line}", "收件方手機號和固定電話至少填寫一項，max=20");
	$objPHPExcel->getActiveSheet()->setCellValue("X{$excel_line}", "收件方手機號和固定電話至少填寫一項，max=20");
	$objPHPExcel->getActiveSheet()->setCellValue("Y{$excel_line}", "必填，max=200");
	$objPHPExcel->getActiveSheet()->setCellValue("Z{$excel_line}", "當收件國家為韓國時必填，其餘國家/地區非必填，max=100");
	$objPHPExcel->getActiveSheet()->setCellValue("AA{$excel_line}", "當收件國家為韓國時必填，其餘國家/地區非必填，max=100");
	$objPHPExcel->getActiveSheet()->setCellValue("AB{$excel_line}", "非必填，max=128");
	$objPHPExcel->getActiveSheet()->setCellValue("AC{$excel_line}", "必填，max=128");
	$objPHPExcel->getActiveSheet()->setCellValue("AD{$excel_line}", "必填，max=64");
	$objPHPExcel->getActiveSheet()->setCellValue("AE{$excel_line}", "必選，下拉選擇");
	$objPHPExcel->getActiveSheet()->setCellValue("AF{$excel_line}", "max=20，必填。 沒有郵編的國家/地區可以不填寫。");
	$objPHPExcel->getActiveSheet()->setCellValue("AG{$excel_line}", "非必填，max=128");
	$objPHPExcel->getActiveSheet()->setCellValue("AH{$excel_line}", "下拉選擇");
	$objPHPExcel->getActiveSheet()->setCellValue("AI{$excel_line}", "max=100，當【收件類型】為“公司件”時，則此處必填");
	$objPHPExcel->getActiveSheet()->setCellValue("AJ{$excel_line}", "下拉選擇");
	$objPHPExcel->getActiveSheet()->setCellValue("AK{$excel_line}", "max=100");
	$objPHPExcel->getActiveSheet()->setCellValue("AL{$excel_line}", "max=18");
	$objPHPExcel->getActiveSheet()->setCellValue("AM{$excel_line}", "max=18");

	$objPHPExcel->getActiveSheet()->setCellValue("AN{$excel_line}", "max=60，非必填");
	$objPHPExcel->getActiveSheet()->setCellValue("AO{$excel_line}", "必填，max=100");
	$objPHPExcel->getActiveSheet()->setCellValue("AP{$excel_line}", "非必填，max =100");
	$objPHPExcel->getActiveSheet()->setCellValue("AQ{$excel_line}", "必填，僅支持輸入數字，max=17");
	$objPHPExcel->getActiveSheet()->setCellValue("AR{$excel_line}", "商品的單位，舉例：件/包/袋，必填，max=30");
	$objPHPExcel->getActiveSheet()->setCellValue("AS{$excel_line}", "非必填，max =17");
	$objPHPExcel->getActiveSheet()->setCellValue("AT{$excel_line}", "必填，max = 23");
	$objPHPExcel->getActiveSheet()->setCellValue("AU{$excel_line}", "必選，下拉選擇");
	$objPHPExcel->getActiveSheet()->setCellValue("AV{$excel_line}", "非必填，max=100");
	$objPHPExcel->getActiveSheet()->setCellValue("AW{$excel_line}", "非必填，max=50");

	$objPHPExcel->getActiveSheet()->setCellValue("AX{$excel_line}", "必填，僅支持輸入數字，max=4");
	$objPHPExcel->getActiveSheet()->setCellValue("AY{$excel_line}", "下拉選擇，非必選");
	$objPHPExcel->getActiveSheet()->setCellValue("AZ{$excel_line}", "非必填，僅支持輸入數字，max=17");
	$objPHPExcel->getActiveSheet()->setCellValue("BA{$excel_line}", "非必填，僅支持輸入數字，max=17");
	$objPHPExcel->getActiveSheet()->setCellValue("BB{$excel_line}", "非必填，僅支持輸入數字，max=17");
	$objPHPExcel->getActiveSheet()->setCellValue("BC{$excel_line}", "必選，下拉選擇");

	$objPHPExcel->getActiveSheet()->setCellValue("BD{$excel_line}", "必選，下拉選擇");
	$objPHPExcel->getActiveSheet()->setCellValue("BE{$excel_line}", "COD-代收貨款，非必選");
	$objPHPExcel->getActiveSheet()->setCellValue("BF{$excel_line}", "若附加服務1選擇了COD，則卡號必填，max = 30");
	$objPHPExcel->getActiveSheet()->setCellValue("BG{$excel_line}", "max = 30");
	$objPHPExcel->getActiveSheet()->setCellValue("BH{$excel_line}", "INSURE-保價，下拉選擇，非必選");
	$objPHPExcel->getActiveSheet()->setCellValue("BI{$excel_line}", "max = 30，此欄目代表托寄物的保價聲明價值。

	如果此欄目擁有任何數額，IUOP系統將會讀取此數額為聲明價值，而不是讀取托寄物的實際聲明價值。
	
	若托寄物發生遺失或破損，順豐國際按托寄物的聲明價值和損失比例賠償。
	
	若托寄物聲明價值高於實際價值，按實際價值和損失比例賠償。");
	$objPHPExcel->getActiveSheet()->setCellValue("BJ{$excel_line}", "密碼認證，下拉選擇，非必選。

	請務必填寫能支援短訊服務(SMS)的收件方手機號。");
	$objPHPExcel->getActiveSheet()->setCellValue("BK{$excel_line}", "max = 50，當選擇簽單返還時，此處必填，請於此欄目填寫所需的簽單返還派件證明類型，支持多選。
	簽單返還類型
	1. 簽名
	2. 蓋章
	如果需要多選，請以半角逗號分隔。舉例：如選擇簽名 + 蓋章，請輸入 1,2。");
	$objPHPExcel->getActiveSheet()->setCellValue("BL{$excel_line}", "max =50，可填寫簽名/蓋章位置、簽回單張數或其他要求，非必填。");
	$objPHPExcel->getActiveSheet()->setCellValue("BM{$excel_line}", "下拉選擇，非必選");
	$objPHPExcel->getActiveSheet()->setCellValue("BN{$excel_line}", "max=40");
	$objPHPExcel->getActiveSheet()->setCellValue("BO{$excel_line}", "下拉選擇，必選");
	$objPHPExcel->getActiveSheet()->setCellValue("BP{$excel_line}", "取原寄地國家的時區，日期格式為2021/01/22 18:30，非必填");
	$objPHPExcel->getActiveSheet()->setCellValue("BQ{$excel_line}", "非必填，max=200");
	$objPHPExcel->getActiveSheet()->setCellValue("BR{$excel_line}", "下拉選擇");
	$objPHPExcel->getActiveSheet()->setCellValue("BS{$excel_line}", "下拉選擇");

	$objPHPExcel->getActiveSheet()->setCellValue("BT{$excel_line}", "下拉選擇，必選");
	$objPHPExcel->getActiveSheet()->setCellValue("BU{$excel_line}", "月結卡號/月結帳號
	當付款方式為“第三方付”，則月結卡號必填，max=20");
	$objPHPExcel->getActiveSheet()->setCellValue("BV{$excel_line}", "下拉選擇，必選");
	$objPHPExcel->getActiveSheet()->setCellValue("BW{$excel_line}", "當稅金付款方式為“寄付”和“第三方付”，則此處必填max=20");
	$objPHPExcel->getActiveSheet()->setCellValue("BX{$excel_line}", "下拉選擇，非必選");
	$objPHPExcel->getActiveSheet()->setCellValue("BY{$excel_line}", "下拉選擇，非必選");
	$objPHPExcel->getActiveSheet()->setCellValue("BZ{$excel_line}", "max=200，非必填");
	$objPHPExcel->getActiveSheet()->setCellValue("CA{$excel_line}", "max=200，非必填");

	$objPHPExcel->getActiveSheet()->setCellValue("CB{$excel_line}", "FBA预约单号，max=256，非必填");
	$objPHPExcel->getActiveSheet()->setCellValue("CC{$excel_line}", "非必填，max=100");
	$objPHPExcel->getActiveSheet()->setCellValue("CD{$excel_line}", "非必填，max=20");
	$objPHPExcel->getActiveSheet()->setCellValue("CE{$excel_line}", "非必填，max=200");
	$objPHPExcel->getActiveSheet()->setCellValue("CF{$excel_line}", "此費用僅適用於目的地國的申報，請根據需要如實填寫，否則可能會影響清關進程");

	$msql->query( "select * from {P}_shop_order where {$scl_output} order by dtime desc" );
	while( $msql->next_record() ) {
	    $e_memberid  = $msql->f("memberid");
		$e_OrderNo   = $msql->f("OrderNo");
		$e_name      = $msql->f("name");
		$e_s_name    = $msql->f("s_name");
		$e_s_tel     = $msql->f("s_tel");
		$e_s_mobi    = $msql->f("s_mobi");
		$e_s_addr    = $msql->f("s_addr");
		$e_payid     = $msql->f("payid");
		$e_postcode  = $msql->f('s_postcode') ? $msql->f('s_postcode') : 0;
		$e_pay       = $msql->f('paytotal');
		$e_paytotal  = $e_payid == 2 || $e_payid == 5 ? (INT)$e_pay : "";
		$e_yuntime   = date("Ymd", time());// 黑貓
		$e_yuntime_t = date("Ymd", time() + (60 * 60 * 24));// 黑貓
		$e_paytotal = $e_paytotal ? $e_paytotal : "0";
		
		$fsql->query("select postcode from {P}_member where memberid={$e_memberid}");
		if ( $fsql->next_record() ) {
        	$e_postcode = $fsql->f("postcode") ? $fsql->f("postcode") : 0;
        }
        
		$e_s_addr  = str_replace(' ', '', $e_s_addr);
		$address   = getCityName($e_s_addr);

		$array = explode($address, $e_s_addr);
		$address_1 = getAreaName($array['1']);
		// 匯出 EXCEL訂單
		/*$e_id=$msql->f("orderid");
		$e_items=mb_substr( $msql->f("items"),0,46,"utf-8" )."...";
		$getnums = $fsql->getone("SELECT SUM(nums)  FROM {P}_shop_orderitems WHERE orderid='$e_id'");
		$e_totalnums = $getnums["SUM(nums)"];*/
		$excel_line++;
		// 產生其他列
		$objPHPExcel->getActiveSheet()->setCellValueExplicit("A{$excel_line}", (string) $e_OrderNo, PHPExcel_Cell_DataType::TYPE_STRING); 
		$objPHPExcel->getActiveSheet()->setCellValue("B{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("C{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("D{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("E{$excel_line}", '銳鎷股份有限公司');
		$objPHPExcel->getActiveSheet()->setCellValue("F{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("G{$excel_line}", '227600110');
		$objPHPExcel->getActiveSheet()->setCellValue("H{$excel_line}", '台北市松山區民生東路五段31號');
		$objPHPExcel->getActiveSheet()->setCellValue("I{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("J{$excel_line}", '台北市');
		$objPHPExcel->getActiveSheet()->setCellValue("K{$excel_line}", '台北市');
		$objPHPExcel->getActiveSheet()->setCellValue("L{$excel_line}", '中國臺灣');
		$objPHPExcel->getActiveSheet()->setCellValue("M{$excel_line}", '105');
		$objPHPExcel->getActiveSheet()->setCellValue("N{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("O{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("P{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("Q{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("R{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("S{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("T{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("U{$excel_line}", '');

		$objPHPExcel->getActiveSheet()->setCellValue("V{$excel_line}", $e_s_name);
		$objPHPExcel->getActiveSheet()->setCellValue("W{$excel_line}", (string) $e_s_mobi, PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()->setCellValue("X{$excel_line}", (string) $e_s_tel, PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()->setCellValue("Y{$excel_line}", $e_s_addr);
		$objPHPExcel->getActiveSheet()->setCellValue("Z{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("AA{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("AB{$excel_line}", $address . $address_1);
		$objPHPExcel->getActiveSheet()->setCellValue("AC{$excel_line}", $address);
		$objPHPExcel->getActiveSheet()->setCellValue("AD{$excel_line}", '台灣');
		$objPHPExcel->getActiveSheet()->setCellValue("AE{$excel_line}", '中國臺灣');

		$objPHPExcel->getActiveSheet()->setCellValue("AF{$excel_line}", "=VLOOKUP(AB{$excel_line}, 郵遞區號!A2:B374, 2, 0)", PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()->setCellValue("AG{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("AH{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("AI{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("AJ{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("AK{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("AL{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("AM{$excel_line}", '');

		$objPHPExcel->getActiveSheet()->setCellValue("AN{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("AO{$excel_line}", '服飾');
		$objPHPExcel->getActiveSheet()->setCellValue("AP{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("AQ{$excel_line}", '1');
		$objPHPExcel->getActiveSheet()->setCellValue("AR{$excel_line}", '件');
		$objPHPExcel->getActiveSheet()->setCellValue("AS{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("AT{$excel_line}", $e_pay);
		$objPHPExcel->getActiveSheet()->setCellValue("AU{$excel_line}", '中國臺灣');
		$objPHPExcel->getActiveSheet()->setCellValue("AV{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("AW{$excel_line}", '');

		$objPHPExcel->getActiveSheet()->setCellValue("AX{$excel_line}", '1');
		$objPHPExcel->getActiveSheet()->setCellValue("AY{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("AZ{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("BA{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("BB{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("BC{$excel_line}", 'NTD');

		$objPHPExcel->getActiveSheet()->setCellValue("BD{$excel_line}", '順豐特快');
		if ( $e_paytotal === '0' ) {
			$objPHPExcel->getActiveSheet()->setCellValue("BE{$excel_line}", '');
			$objPHPExcel->getActiveSheet()->setCellValue("BF{$excel_line}", '');
			$objPHPExcel->getActiveSheet()->setCellValue("BG{$excel_line}", 0);
		}
		else {
			$objPHPExcel->getActiveSheet()->setCellValue("BE{$excel_line}", 'COD-代收貨款');
			$objPHPExcel->getActiveSheet()->setCellValue("BF{$excel_line}", '8860693557');
			$objPHPExcel->getActiveSheet()->setCellValue("BG{$excel_line}", $e_paytotal);
		}
		$objPHPExcel->getActiveSheet()->setCellValue("BH{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("BI{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("BJ{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("BK{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("BL{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("BM{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("BN{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("BO{$excel_line}", '自行聯系快遞員或自寄');
		$objPHPExcel->getActiveSheet()->setCellValue("BP{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("BQ{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("BR{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("BS{$excel_line}", '');

		$objPHPExcel->getActiveSheet()->setCellValue("BT{$excel_line}", '寄付');
		$objPHPExcel->getActiveSheet()->setCellValue("BU{$excel_line}", '8860693557');
		$objPHPExcel->getActiveSheet()->setCellValue("BV{$excel_line}", '到付');
		$objPHPExcel->getActiveSheet()->setCellValue("BW{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("BX{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("BY{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("BZ{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("CA{$excel_line}", '');

		$objPHPExcel->getActiveSheet()->setCellValue("CB{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("CC{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("CD{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("CE{$excel_line}", '');
		$objPHPExcel->getActiveSheet()->setCellValue("CF{$excel_line}", '');
	}
	// Create sheet
	$sheet2 = $objPHPExcel->createSheet();
	$sheet2->setTitle('郵遞區號');
	// $objPHPExcel->setActiveSheetIndex(1);
	// 產生第一列
	$excel_line = 1;
	$sheet2->getColumnDimension('A')->setWidth(20);
	$sheet2->setCellValue("A{$excel_line}", "地區");
	$sheet2->setCellValue("B{$excel_line}", "郵遞區號");
	
	$area     = ['台北市中正區','台北市大同區','台北市中山區','台北市松山區','台北市大安區','台北市萬華區','台北市信義區','台北市士林區','台北市北投區','台北市內湖區','台北市南港區','台北市文山區','基隆市仁愛區','基隆市信義區','基隆市中正區','基隆市中山區','基隆市安樂區','基隆市暖暖區','基隆市七堵區','新北市萬里區','新北市金山區','新北市板橋區','新北市汐止區','新北市深坑區','新北市石碇區','新北市瑞芳區','新北市平溪區','新北市雙溪區','新北市貢寮區','新北市新店區','新北市坪林區','新北市烏來區','新北市永和區','新北市中和區','新北市土城區','新北市三峽區','新北市樹林區','新北市鶯歌區','新北市三重區','新北市新莊區','新北市泰山區','新北市林口區','新北市蘆洲區','新北市五股區','新北市八里區','新北市淡水區','新北市三芝區','新北市石門區','宜蘭縣宜蘭市','宜蘭縣頭城鎮','宜蘭縣礁溪鄉','宜蘭縣壯圍鄉','宜蘭縣員山鄉','宜蘭縣羅東鎮','宜蘭縣三星鄉','宜蘭縣大同鄉','宜蘭縣五結鄉','宜蘭縣冬山鄉','宜蘭縣蘇澳鎮','宜蘭縣南澳鄉','新竹市','新竹縣新竹市','新竹縣竹北市','新竹縣湖口鄉','新竹縣新豐鄉','新竹縣新埔鎮','新竹縣關西鎮','新竹縣芎林鄉','新竹縣寶山鄉','新竹縣竹東鎮','新竹縣五峰鄉','新竹縣橫山鄉','新竹縣尖石鄉','新竹縣北埔鄉','新竹縣峨眉鄉','桃園市中壢區','桃園市平鎮區','桃園市龍潭區','桃園市楊梅區','桃園市新屋區','桃園市觀音區','桃園市桃園區','桃園市龜山區','桃園市八德區','桃園市大溪區','桃園市復興區','桃園市大園區','桃園市蘆竹區','苗栗縣竹南鎮','苗栗縣頭份市','苗栗縣三灣鄉','苗栗縣南庄鄉','苗栗縣獅潭鄉','苗栗縣後龍鎮','苗栗縣通霄鎮','苗栗縣苑裡鎮','苗栗市','苗栗縣苗栗市','苗栗縣造橋鄉','苗栗縣頭屋鄉','苗栗縣公館鄉','苗栗縣大湖鄉','苗栗縣泰安鄉','苗栗縣銅鑼鄉','苗栗縣三義鄉','苗栗縣西湖鄉','苗栗縣卓蘭鎮','台中市中區','台中市東區','台中市南區','台中市西區','台中市北區','台中市北屯區','台中市西屯區','台中市南屯區','台中市太平區','台中市大里區','台中市霧峰區','台中市烏日區','台中市豐原區','台中市后里區','台中市石岡區','台中市東勢區','台中市和平區','台中市新社區','台中市潭子區','台中市大雅區','台中市神岡區','台中市大肚區','台中市沙鹿區','台中市龍井區','台中市梧棲區','台中市清水區','台中市大甲區','台中市外埔區','台中市大安區','彰化市','彰化縣彰化市','彰化縣芬園鄉','彰化縣花壇鄉','彰化縣秀水鄉','彰化縣鹿港鎮','彰化縣福興鄉','彰化縣線西鄉','彰化縣和美鎮','彰化縣伸港鄉','彰化縣員林鎮','彰化縣社頭鄉','彰化縣永靖鄉','彰化縣埔心鄉','彰化縣溪湖鎮','彰化縣大村鄉','彰化縣埔鹽鄉','彰化縣田中鎮','彰化縣北斗鎮','彰化縣田尾鄉','彰化縣埤頭鄉','彰化縣溪州鄉','彰化縣竹塘鄉','彰化縣二林鎮','彰化縣大城鄉','彰化縣芳苑鄉','彰化縣二水鄉','南投市','南投縣南投市','南投縣中寮鄉','南投縣草屯鎮','南投縣國姓鄉','南投縣埔里鎮','南投縣仁愛鄉','南投縣名間鄉','南投縣集集鎮','南投縣水里鄉','南投縣魚池鄉','南投縣信義鄉','南投縣竹山鎮','南投縣鹿谷鄉','嘉義縣嘉義市','嘉義市東區','嘉義市西區','嘉義縣番路鄉','嘉義縣梅山鄉','嘉義縣竹崎鄉','嘉義縣阿里山','嘉義縣中埔鄉','嘉義縣大埔鄉','嘉義縣水上鄉','嘉義縣鹿草鄉','嘉義縣太保市','嘉義縣朴子市','嘉義縣東石鄉','嘉義縣六腳鄉','嘉義縣新港鄉','嘉義縣民雄鄉','嘉義縣大林鎮','嘉義縣溪口鄉','嘉義縣義竹鄉','嘉義縣布袋鎮','雲林縣斗南鎮','雲林縣大埤鄉','雲林縣虎尾鎮','雲林縣土庫鎮','雲林縣褒忠鄉','雲林縣東勢鄉','雲林縣臺西鄉','雲林縣崙背鄉','雲林縣麥寮鄉','雲林縣斗六市','雲林縣林內鄉','雲林縣古坑鄉','雲林縣莿桐鄉','雲林縣西螺鎮','雲林縣二崙鄉','雲林縣北港鎮','雲林縣水林鄉','雲林縣口湖鄉','雲林縣四湖鄉','雲林縣元長鄉','台南市中西區','台南市東區','台南市南區','台南市北區','台南市安平區','台南市安南區','台南市永康區','台南市歸仁區','台南市新化區','台南市左鎮區','台南市玉井區','台南市楠西區','台南市南化區','台南市仁德區','台南市關廟區','台南市龍崎區','台南市官田區','台南市麻豆區','台南市佳里區','台南市西港區','台南市七股區','台南市將軍區','台南市學甲區','台南市北門區','台南市新營區','台南市後壁區','台南市白河區','台南市東山區','台南市六甲區','台南市下營區','台南市柳營區','台南市鹽水區','台南市善化區','台南市大內區','台南市山上區','台南市新市區','台南市安定區','高雄市新興區','高雄市前金區','高雄市苓雅區','高雄市鹽埕區','高雄市鼓山區','高雄市旗津區','高雄市前鎮區','高雄市三民區','高雄市楠梓區','高雄市小港區','高雄市左營區','高雄市仁武區','高雄市大社區','高雄市岡山區','高雄市路竹區','高雄市阿蓮區','高雄市田寮區','高雄市燕巢區','高雄市橋頭區','高雄市梓官區','高雄市彌陀區','高雄市永安區','高雄市湖內區','高雄市鳳山區','高雄市大寮區','高雄市林園區','高雄市鳥松區','高雄市大樹區','高雄市旗山區','高雄市美濃區','高雄市六龜區','高雄市內門區','高雄市杉林區','高雄市甲仙區','高雄市桃源區','高雄市那瑪夏','高雄市茂林區','高雄市茄萣區','屏東市','屏東縣屏東市','屏東縣三地門','屏東縣霧臺鄉','屏東縣瑪家鄉','屏東縣九如鄉','屏東縣里港鄉','屏東縣高樹鄉','屏東縣鹽埔鄉','屏東縣長治鄉','屏東縣麟洛鄉','屏東縣竹田鄉','屏東縣內埔鄉','屏東縣萬丹鄉','屏東縣潮州鎮','屏東縣泰武鄉','屏東縣來義鄉','屏東縣萬巒鄉','屏東縣崁頂鄉','屏東縣新埤鄉','屏東縣南州鄉','屏東縣林邊鄉','屏東縣東港鎮','屏東縣琉球鄉','屏東縣佳冬鄉','屏東縣新園鄉','屏東縣枋寮鄉','屏東縣枋山鄉','屏東縣春日鄉','屏東縣獅子鄉','屏東縣車城鄉','屏東縣牡丹鄉','屏東縣恆春鎮','屏東縣滿州鄉','澎湖縣馬公市','澎湖縣西嶼鄉','澎湖縣望安鄉','澎湖縣七美鄉','澎湖縣白沙鄉','澎湖縣湖西鄉','金門縣金沙鎮','金門縣金湖鎮','金門縣金寧鄉','金門縣金城鎮','金門縣烈嶼鄉','金門縣烏坵鄉','台東市','台東縣台東市','臺東縣綠島鄉','臺東縣蘭嶼鄉','臺東縣延平鄉','臺東縣卑南鄉','臺東縣鹿野鄉','臺東縣關山鎮','臺東縣海端鄉','臺東縣池上鄉','臺東縣東河鄉','臺東縣成功鎮','臺東縣長濱鄉','臺東縣太麻里','臺東縣金峰鄉','臺東縣大武鄉','臺東縣達仁鄉','花蓮市','花蓮縣花蓮市','花蓮縣新城鄉','花蓮縣秀林鄉','花蓮縣吉安鄉','花蓮縣壽豐鄉','花蓮縣鳳林鎮','花蓮縣光復鄉','花蓮縣豐濱鄉','花蓮縣瑞穗鄉','花蓮縣萬榮鄉','花蓮縣玉里鎮','花蓮縣卓溪鄉','花蓮縣富里鄉','南海島東沙群島','南海島南沙群島','釣魚台'];
	$postcode = ['100','103','104','105','106','108','110','111','112','114','115','116','200','201','202','203','204','205','206','207','208','220','221','222','223','224','226','227','228','231','232','233','234','235','236','237','238','239','241','242','243','244','247','248','249','251','252','253','260','261','262','263','264','265','266','267','268','269','270','272','300','300','302','303','304','305','306','307','308','310','311','312','313','314','315','320','324','325','326','327','328','330','333','334','335','336','337','338','350','351','352','353','354','356','357','358','360','360','361','362','363','364','365','366','367','368','369','400','401','402','403','404','406','407','408','411','412','413','414','420','421','422','423','424','426','427','428','429','432','433','434','435','436','437','438','439','500','500','502','503','504','505','506','507','508','509','510','511','512','513','514','515','516','520','521','522','523','524','525','526','527','528','530','540','540','541','542','544','545','546','551','552','553','555','556','557','558','600','600','600','602','603','604','605','606','607','608','611','612','613','614','615','616','621','622','623','624','625','630','631','632','633','634','635','636','637','638','640','643','646','647','648','649','651','652','653','654','655','700','701','702','704','708','709','710','711','712','713','714','715','716','717','718','719','720','721','722','723','724','725','726','727','730','731','732','733','734','735','736','737','741','742','743','744','745','800','801','802','803','804','805','806','807','811','812','813','814','815','820','821','822','823','824','825','826','827','828','829','830','831','832','833','840','842','843','844','845','846','847','848','849','851','852','900','900','901','902','903','904','905','906','907','908','909','911','912','913','920','921','922','923','924','925','926','927','928','929','931','932','940','941','942','943','944','945','946','947','880','881','882','883','884','885','890','891','892','893','894','896','950','950','951','952','953','954','955','956','957','958','959','961','962','963','964','965','966','970','970','971','972','973','974','975','976','977','978','979','981','982','983','817','819','290'];
	foreach ($area as $k => $v) {
		++$excel_line;
		// 產生其他列
		$sheet2->setCellValueExplicit("A{$excel_line}", $v); 
		$sheet2->setCellValue("B{$excel_line}", $postcode[$k]);
	}

	// 產生Excel 20003格式
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	// We'll be outputting an excel file
	header('Content-type: application/vnd.ms-excel');
	// It will be called file.xls
	$e_filename = date('YmdHis', time()) . '_order_sf.xls';
	header('Content-Disposition: attachment; filename="'.$e_filename.'"');
	// Write file to the browser
	$objWriter->save('php://output');
	exit;
}

/*發票檔*/
if($step=="receipt"){
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

$isok = $_REQUEST['isok'];
$issource = $_REQUEST['source'];
$iscity = $_REQUEST['city'];

$scl_receipt = "dtime>={$starttime} and dtime<={$endtime} and iftui='0'";

if ( $isok == "1" || $isok == "0" )
{
		$scl_receipt .= " and ifok='{$isok}' ";
}
if ( $issource != "all" )
{
	if($issource=="0"){
		$scl .= " and (source='' || source='0')";
	}else{
		$scl_receipt .= " and source='{$issource}'";
	}
}
if ( $iscity != "all" && $iscity != "")
{
		$scl_receipt .= " and s_addr regexp '{$iscity}'";
}


$e_body = "訂單編號,配送,發票,發票號碼,訂單來源,訂單日期,買方名稱,買方統編,電話/手機,買方地址,買方Email,產品編號,品名,數量,單價,課稅別,發票方式,付款方式,備註,載具類別,載具編號,愛心碼\n";

$msql->query( "select * from {P}_shop_order where {$scl_receipt} order by dtime desc" );
while($msql->next_record()){
	$e_OrderNo=$msql->f("OrderNo");
	$e_name=$msql->f("name");
	$e_s_name=$msql->f("s_name");
	$e_s_tel=$msql->f("s_tel");
	$e_s_mobi=$msql->f("s_mobi");
	$e_s_addr=$msql->f("s_addr");
	$e_payid=$msql->f("payid");
	//$e_paytotal= $e_payid == 2 ? (INT)$msql->f("paytotal"):"0";
	$e_paytotal = (INT)$msql->f("paytotal");
	$promoprice = $msql->f("promoprice");
	$yunfei = (INT)$msql->f("yunfei");
	/*$yy = date("Y",time())-1911;
	$ym = str_pad(date("m",time()),2,"0",STR_PAD_LEFT);
	$yd = str_pad(date("d",time()),2,"0",STR_PAD_LEFT);
	$e_yuntime=$yy.$ym.$yd;
	*/
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
	
	$ifreceipt = $msql->f( "ifreceipt" )=="1"? "已開發票":"未開";
		
	$CreateInvoice = $msql->f( "CreateInvoice" );
	$source = $msql->f( "source" );
	
		switch ( $source )
		{
		case "0" :
				$sourcestr = "官網訂單";
				break;
		case "1-1" :
				$sourcestr = "板橋店";
				break;
		case "1-2" :
				$sourcestr = "新莊店";
				break;
		case "1-3" :
				$sourcestr = "內湖店";
				break;
		case "1-4" :
				$sourcestr = "三重店";
				break;
		case "1-5" :
				$sourcestr = "南屯店";
				break;
		case "2-1" :
				$sourcestr = "蝦皮";
				break;
		case "2-2" :
				$sourcestr = "MOMO";//
				break;
		case "2-3" :
				$sourcestr = "Yahoo超級商城";//
				break;
		default :
				$sourcestr = "官網訂單";
		}
	
		/*捐款*/
		list($contribute_title,$contribute_code,$contribute_type) = explode("|",$msql->f( "contribute" ));
		if($contribute_code)($re_type = "1");
		/*載具*/
		list($integrated_title,$integrated_code,$integrated_type) = explode("|",$msql->f( "integrated" ));
		if($integrated_type)($re_type = "0");
		
	if($e_payid == "1" || $e_payid == "4"){
		$e_paym = "3";
	}elseif($e_payid == "2" || $e_payid == "5"){
		$e_paym = "5";
	}else{
		$e_paym = "1";
	}
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
	$fsql->query( "select * from {P}_shop_orderitems where orderid='$orderid'" );
while($fsql->next_record()){
	$orderbn = $fsql->f("bn");
	list($fz) = explode("^",$fsql->f("fz"));
	$goods = $fsql->f("goods");
	$danwei = $fsql->f("danwei");
	$item_nums = $fsql->f("nums");
	$items = $goods."(".$fz.")";
	$item_paytotal = (INT)$fsql->f("price");
	$ifyun = $fsql->f( "ifyun" )? "已配送":"未送";
	
	
	if($nn > 1) $e_OrderNo = $dtime = $e_name = $CreateInvoice = $sourcestr = $invoicenumber = $mobi = $e_s_addr = $email = $re_type = $e_paym = $integrated_type = $integrated_code = $contribute_code = "";
	
	//訂單編號,配送,發票,訂單日期,買方名稱,買方統編,電話/手機,買方地址,買方Email,產品編號,品名,數量,單價,課稅別,發票方式,付款方式,備註,載具類別,載具編號,愛心碼
	$e_body.= $e_OrderNo.",".$ifyun.",".$ifreceipt.",".$CreateInvoice.",".$sourcestr.",".$dtime.",".$e_name.",".$invoicenumber.",".$mobi.",".$e_s_addr.",".$email.",".$orderbn.",".$items.",".$item_nums.",".$item_paytotal.",1,".$re_type.",".$e_paym.",,".$integrated_type.",".$integrated_code.",".$contribute_code."\n";
	
	$nn++;
	
}
	if($yunfei > 0) $e_body.= ",,,,,,,,,SHIP,運費,1,".$yunfei.",,,,,,,\n";
	if($promoprice > 0) $e_body.= ",,,,,,,,,D01,活動折扣,1,-".$promoprice.",,,,,,,\n";
	$e_body.= ",,,,,,,,,,,,".$e_paytotal.",,,,,,,\n"; //2019.02.16 新增總額

	
	//訂單編號,訂單日期,買方名稱,買方統編,電話/手機,買方地址,買方Email,產品編號,品名,數量,單價,課稅別,發票方式,付款方式,備註,載具類別,載具編號,愛心碼
	//$e_body.= $e_OrderNo.",".$dtime.",".$e_name.",".$invoicenumber.",".$mobi.",".$e_s_addr.",".$email.",".$orderbn.",".$items.",1,".$paytotal.",1,".$re_type.",".$e_paym.",,".$integrated_type.",".$integrated_code.",".$contribute_code."\n";
}
	$e_filename = date('YmdHis',time()).'_order_receipt';
	header('Content-Type: text/html; charset=utf-8' ); 
	header("Content-Disposition: attachment; filename=\"".$e_filename.".csv\"");
	header("Expires: 0");
	header("Pragma: public");
	
	$restxt = str_replace('瀞','__',$e_body);
	$restxt = str_replace('汘','__',$e_body);
	//echo mb_convert_encoding($restxt,"BIG5","UTF-8");
	$restxt = "\xEF\xBB\xBF".$restxt;
	echo $restxt;
	exit;
}

/*對帳單*/
if($step=="statement"){
//訂單編號,訂單來源,訂單日期,買方名稱,付款金額,付款方式,信用卡後4碼,餘額付款或退貨,是否開立,發票號碼,開立發票日期

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

$isok = $_REQUEST['isok'];
$issource = $_REQUEST['source'];
$iscity = $_REQUEST['city'];

//$scl_receipt = "dtime>={$starttime} and dtime<={$endtime} and iftui='0'";
$scl_receipt = "dtime>={$starttime} and dtime<={$endtime} ";

if ( $isok == "1" || $isok == "0" )
{
		$scl_receipt .= " and ifok='{$isok}' ";
}
if ( $issource != "all" )
{
	if($issource=="0"){
		$scl .= " and (source='' || source='0')";
	}else{
		$scl_receipt .= " and source='{$issource}'";
	}
}
if ( $iscity != "all" && $iscity != "")
{
		$scl_receipt .= " and s_addr regexp '{$iscity}'";
}


$e_body = "訂單編號,訂單來源,訂單日期,買方名稱,付款金額,付款方式,信用卡後4碼,餘額付款或退貨,是否開立,發票號碼,開立發票日期\n";

$msql->query( "select * from {P}_shop_order where {$scl_receipt} order by dtime desc" );
while($msql->next_record()){
	$e_OrderNo=$msql->f("OrderNo");
	$e_name=$msql->f("name");
	$e_s_name=$msql->f("s_name");
	$e_s_tel=$msql->f("s_tel");
	$e_s_mobi=$msql->f("s_mobi");
	$e_s_addr=$msql->f("s_addr");
	$e_payid=$msql->f("payid");
	$e_paytotal = (INT)$msql->f("paytotal");
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
	
	
	$ifreceipt = $msql->f( "ifreceipt" )=="1"? "已開發票":"未開";
	
	$receiptime = $msql->f( "ifreceipt" )=="1"? $msql->f( "receiptime" ):"---";
	if($receipttime && $receipttime!="---"){
		$receipttime = date("Y-m-d",$receipttime);
	}
	
	$disaccount = $msql->f( "disaccount" ) + $msql->f( "tuipay" );
		
	$CreateInvoice = $msql->f( "CreateInvoice" );
	$source = $msql->f( "source" );
	
		switch ( $source )
		{
		case "0" :
				$sourcestr = "官網訂單";
				break;
		case "1-1" :
				$sourcestr = "板橋店";
				break;
		case "1-2" :
				$sourcestr = "新莊店";
				break;
		case "1-3" :
				$sourcestr = "內湖店";
				break;
		case "1-4" :
				$sourcestr = "三重店";
				break;
		case "1-5" :
				$sourcestr = "南屯店";
				break;
		case "2-1" :
				$sourcestr = "蝦皮";
				break;
		case "2-2" :
				$sourcestr = "MOMO";//
				break;
		case "2-3" :
				$sourcestr = "Yahoo超級商城";//
				break;
		default :
				$sourcestr = "官網訂單";
		}
	
		/*捐款*/
		list($contribute_title,$contribute_code,$contribute_type) = explode("|",$msql->f( "contribute" ));
		if($contribute_code)($re_type = "1");
		/*載具*/
		list($integrated_title,$integrated_code,$integrated_type) = explode("|",$msql->f( "integrated" ));
		if($integrated_type)($re_type = "0");
		
	if($e_payid == "1" || $e_payid == "4"){
		$e_paym = "3";
	}elseif($e_payid == "2" || $e_payid == "5"){
		$e_paym = "5";
	}else{
		$e_paym = "1";
	}
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
	$card_num = "---";
	switch($e_payid){
		case "1":
			$re_type_str = "官網信用卡";
			$listbz=explode("]^[",$msql->f("bz"));
			list($ggg, $card_num) = explode("]-[",$listbz[1]);
			$card_num ="=\"".$card_num."\"";
			break;
		case "2":
			$re_type_str = "官網貨到付款";
			$card_num="---";
			break;
		case "3":
			$re_type_str = "店面現金";
			$card_num="---";
			break;
		case "4":
			$re_type_str = "店面信用卡";
			$card_num="---";
			break;
		case "5":
			$re_type_str = "貨到付款";
			$card_num="---";
			break;
		case "6":
			$re_type_str = "LINE PAY";
			$card_num="---";
			break;
	}
	
	//訂單編號,訂單來源,訂單日期,買方名稱,付款金額,付款方式,信用卡後4碼,餘額付款或退貨,是否開立,發票號碼,開立發票日期
	$e_body.= $e_OrderNo.",".$sourcestr.",".$dtime.",".$e_name.",".$paytotal.",".$re_type_str.",".$card_num.",".$disaccount.",".$ifreceipt.",".$CreateInvoice.",".$receipttime."\n";
	
	
}
	$e_filename = date('YmdHis',time()).'_order_bank';
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
<link  href="css/style.css?2" type="text/css" rel="stylesheet">
<title><?php echo $strAdminTitle; ?></title>
<script type="text/javascript" src="../../base/js/base132.js"></script>
<script type="text/javascript" src="../../base/js/blockui.js"></script>
<script type="text/javascript" src="js/order.js?18"></script>
<script type="text/javascript" src="js/Date/WdatePicker.js"></script>
<style>
.list:hover {
    background-color: #f4f4f4;
}
.list td {
    border-bottom: 1px #ccc solid;
    padding: 8px 3px;
}
</style>
</head>

<body>
<?php
	if(!$page && $GLOBALS['GLOBALS']['CONF']['re_check']=="0"){
		//原本的 主機開立發票
		/*$oktime= time() - (86400*11-1);
		echo "<script>$.blockUI({message: \"訂單鑑賞期檢測並開立發票...\",css:{width: '200px',backgroundColor: '#fff',borderColor:'#999999'}}); </script>";
		$tsql->query("SELECT * FROM {P}_shop_order WHERE ifpay='1' AND ifyun='1' AND iftui='0' AND itemtui='0' AND ifreceipt='0' AND yuntime<='$oktime' AND orderid>'5297'  AND (source='' || source='0')");
		while($tsql->next_record()){
			$orderid = $tsql->f("orderid");
			$ret = INVO($orderid);
			if($ret == "OK"){
				$fsql->query( "update {P}_shop_order set ifok='1' where orderid='{$orderid}' and itemtui='0'" );
			}else{
				$err .= $err? ",".$orderid:$orderid;
			}
		}
		
		if($err != ""){
			echo "<script>$.unblockUI({onUnblock: function() { alert('(若數量過多，表示尚未開啟自動開立發票)\\r\\n發票開立錯誤之訂單編號：".$err."'); }});</script>";
		}else{
			echo "<script>$.unblockUI();</script>";
		}*/
	}else{
		//採用 ERP開立發票
		//尚未確定
	}
?>
<div class="searchzone">
<table width="100%" border="0" cellspacing="0" cellpadding="3">
 
  <tr> 
      <td height="28"  > 
        <table border="0" cellspacing="1" cellpadding="0" width="100%">
           <tr> 
            <td><div style="float:left;"> <form name="search" action="order.php" method="get" id="ordersearch">
             <input name="startday" type="text"  class="input" id="startday" style="cursor:pointer" onClick="WdatePicker()"  value="<?php echo $startday; ?>" size="10"  readonly/>-
			 <input name="endday" type="text"  class="input" id="endday" style="cursor:pointer" onClick="WdatePicker()"  value="<?php echo $endday; ?>" size="10"  readonly/>
			   <select name="showcountry" id="showcountry">
      			<option value="all"  <?php echo seld( $showcountry, "all" ); ?>>全部國家</option>
     			 <option value="1" <?php echo seld( $showcountry, "1" ); ?>>臺灣訂單</option>
     			 <option value="2" <?php echo seld( $showcountry, "2" ); ?>>國外訂單</option>
   			 </select>
			   <select name="showpay" id="showpay">
      			<option value="all"  <?php echo seld( $showpay, "all" ); ?>>付款狀態</option>
     			 <option value="0" <?php echo seld( $showpay, "0" ); ?>>未付款</option>
     			 <option value="1" <?php echo seld( $showpay, "1" ); ?>>已付款</option>
   			 </select>
   			 <select name="showyun" id="showyun">
     			 <option value="all"  <?php echo seld( $showyun, "all" ); ?>>配送狀態</option>
    			  <option value="0"  <?php echo seld( $showyun, "0" ); ?>>未配送</option>
    			  <option value="1"  <?php echo seld( $showyun, "1" ); ?>>已配送</option>
   			 </select>
	  		<select name="showok" id="showok">
    			  <option value="0"  <?php echo seld( $showok, "0" ); ?>>處理中訂單</option>
      			<option value="1"  <?php echo seld( $showok, "1" ); ?>>已完成訂單</option>
      			   <option value="all"  <?php echo seld( $showok, "all" ); ?>>全部訂單</option>
   			 </select>
			 <select name="showtui" id="showtui">
    			  <option value="0" <?php echo seld( $showtui, "0" ); ?>>有效訂單</option>
      			<option value="1" <?php echo seld( $showtui, "1" ); ?>>退訂訂單</option>
   			 </select>
			  <select name="shownum">
                <option value="10"  <?php echo seld( $shownum, "10" ); ?>>每頁10條</option>
                <option value="20" <?php echo seld( $shownum, "20" ); ?>>每頁20條</option>
                <option value="30" <?php echo seld( $shownum, "30" ); ?>>每頁30條</option>
                <option value="50" <?php echo seld( $shownum, "50" ); ?>>每頁50條</option>
              </select>
			   <select name="showsource" id="showsource">
      			<option value="all"  <?php echo seld( $showsource, "all" ); ?>>訂單來源</option>
      			<option value="0"  <?php echo seld( $showsource, "0" ); ?>>官網訂單</option>
     			 <option value="1-1" <?php echo seld( $showsource, "1-1" ); ?>>板橋店</option>
     			 <option value="1-2" <?php echo seld( $showsource, "1-2" ); ?>>新莊店</option>
     			 <option value="1-3" <?php echo seld( $showsource, "1-3" ); ?>>內湖店</option>
     			 <option value="1-4" <?php echo seld( $showsource, "1-4" ); ?>>三重店</option>
     			 <option value="1-5" <?php echo seld( $showsource, "1-5" ); ?>>南屯店</option>
     			 <option value="2-1" <?php echo seld( $showsource, "2-1" ); ?>>蝦皮</option>
     			 <option value="2-2" <?php echo seld( $showsource, "2-2" ); ?>>MOMO</option>
     			 <option value="2-3" <?php echo seld( $showsource, "2-3" ); ?>>Yahoo超級商城</option>
   			 </select>
			   <select name="showcity" id="showcity">
      			<option value="all"  <?php echo seld( $showcity, "all" ); ?>>縣市訂單</option>
      			<?php
					$msql->query( "select cat from {P}_member_zone where pid='1'" );
					while ( $msql->next_record( ) )
					{
							$zcat = $msql->f( "cat" );
							echo '<option value="'.$zcat.'" '.seld($showcity, $zcat ).'>'.$zcat.'</option>';
					}
      
      			?>
   			 </select>
              <input type="text" name="key" value="<?php echo $key; ?>"  size="12" class="input" />
              <input type="submit" name="Submit" value="<?php echo $strSearchTitle; ?>" class="button" />
             
            </form>
            	</div>
            <div style="float:left;margin-left:20px;">
            <form name="receiptform" action="order.php" method="get" id="orderreceipt">
           			<input type="hidden" name="step" value="receipt" />  
           			<input type="hidden" name="sd" id="sd" value="" />
           			<input type="hidden" name="ed" id="ed" value="" />
           			<input type="hidden" name="source" id="source" value="" />
           			<input type="hidden" name="city" id="city" value="" />
           			<input type="hidden" name="isok" id="isok" value="<?php echo $showok; ?>" />
           		   <input class="button" type="button" value="<?php echo "輸出訂單";?>" id="subreceipt" style="cursor:pointer;border:1px solid #000;">
           	</form>
           	</div>
            <div style="float:left;margin-left:20px;">
            <form name="statementform" action="order.php" method="get" id="statement">
           			<input type="hidden" name="step" value="statement" />  
           			<input type="hidden" name="sd" id="bsd" value="" />
           			<input type="hidden" name="ed" id="bed" value="" />
           			<input type="hidden" name="source" id="bsource" value="" />
           			<input type="hidden" name="city" id="bcity" value="" />
           			<input type="hidden" name="isok" id="bisok" value="<?php echo $showok; ?>" />
           		   <input class="button" type="button" value="<?php echo "輸出對帳單";?>" id="bankstatement" style="cursor:pointer;border:1px solid #000;">
           	</form>
           	</div>
           	<div style="float:right;">
           	<form name="ouputform" action="order.php" method="get" id="orderoutput">
           			<input type="hidden" id="outputstep" name="step" value="output" />
           			配送日：<input name="csvday" type="text"  class="input" id="csvday" style="cursor:pointer" onClick="WdatePicker()"  value="<?php echo $csvday; ?>" size="10"  readonly/>
           		   <input class="button" type="button" value="新竹物流" onClick="javascript:$('#outputstep').val('hct');ouputform.submit();" style="cursor:pointer;border:1px solid #000;">
           		   <input class="button" type="button" value="順丰物流" onClick="javascript:$('#outputstep').val('sf');ouputform.submit();" style="cursor:pointer;border:1px solid #000;">
           			<button type="button" class="button" style="cursor:pointer;border:1px solid #000;" onClick="var cd=$('#csvday').val();window.open('order_print_all_new.php?csvday='+cd);">產生發貨單</button> <button type="button" class="button" style="cursor:pointer;border:1px solid #000;" id="insertecat">匯入托運單(新竹物流)</button>
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


$scl = " dtime>{$starttime} and dtime<{$endtime} ";

if ( $showcountry== "1" )
{
		$scl .= " and (country='臺灣' OR country='')";
}elseif( $showcountry== "2" ){
		$scl .= " and country<>'臺灣' and country<>''";
}
if ( $showsource != "all" )
{
	if($showsource=="0"){
		$scl .= " and (source='' || source='0')";
	}else{
		$scl .= " and source='{$showsource}'";
	}
}
if ( $showcity != "all" )
{
		$scl .= " and s_addr regexp '{$showcity}'";
}
if ( $showpay == "1" || $showpay == "0" )
{
		$scl .= " and ifpay='{$showpay}' ";
}
if ( $showyun == "1" || $showyun == "0" )
{
		$scl .= " and ifyun='{$showyun}' ";
}
if ( $showok == "1" || $showok == "0" )
{
		$scl .= " and ifok='{$showok}' ";
}
if ( $showtui == "1" || $showtui == "0" )
{
		$scl .= " and iftui='{$showtui}' ";
}
if ( $key != "" )
{
		trylimit( "_shop_order", 50, "orderid" );
		$scl .= " and (orderid='{$key}' or OrderNo='{$key}' or items regexp '{$key}' or name regexp '{$key}' or s_name regexp '{$key}') ";
}
if ( $showtui == "1" )
{
		$dodis = " style='display:none' ";
		$nododis = " ";
}
else
{
		$dodis = " ";
		$nododis = " style='display:none' ";
}
$msql->query( "select count(orderid) from {P}_shop_order where {$scl}" );
if ( $msql->next_record( ) )
{
		$totalnums = $msql->f( "count(orderid)" );
}
$pages = new pages( );
$pages->setvar( array(
		"key" => $key,
		"shownum" => $shownum,
		"showpay" => $showpay,
		"showyun" => $showyun,
		"showok" => $showok,
		"showtui" => $showtui,
		"showcountry" => $showcountry,
		"showsource" => $showsource,
		"startday" => $startday,
		"endday" => $endday
) );
$pages->set( $shownum, $totalnums );
$pagelimit = $pages->limit( );

		$msql->query( "select * from {P}_member_paycenter where hbtype='tcbbank_card' and ifuse='1' limit 0,1" );
		if ( $msql->next_record( ) )
		{
				$pv['payid'] = $msql->f( "id" );
				$pv['pcenteruser'] = $msql->f( "pcenteruser" );
				$pv['pcenterkey'] = $msql->f( "pcenterkey" );
				$pv['key1'] = $msql->f( "key1" );
				$pv['key2'] = $msql->f( "key2" );
				list($merID, $MerchantID) = explode("-", $pv['pcenteruser']);
				$TerminalID = $pv['pcenterkey']; 
		}

?>
<div class="listzone">
<table width="100%" border="0" cellspacing="0" cellpadding="5" align="center">
          <tr>
            <td width="65" height="23"  class="biaoti" style="padding-left:10px">訂單號</td>
            <td width="65" height="23"  class="biaoti" >訂購人</td>
            <td height="23"  class="biaoti" >訂購商品</td>
            <td width="70"  class="biaoti" >商品總價</td>
            <td width="50"  class="biaoti" >運費</td>
            <td width="50"  class="biaoti" >餘額付費</td>
    		<td width="50"  class="biaoti" >折價金額</td>
            <td width="65" height="23"  class="biaoti" >訂單總額</td>
            <td width="70" height="23"  class="biaoti" >下單時間</td>
            <td width="60" height="23"  class="biaoti" >寄送國家</td>
            <td width="70" height="23"  class="biaoti" >付款方式</td>
            <td width="60" height="23"  class="biaoti" >訂單來源</td>
            <td width="60" height="23"  class="biaoti" >銷售員</td>
		    <td width="33" height="23" align="center"  class="biaoti" <?php echo $dodis; ?>>處理</td>
		    <td width="33" height="23" align="center"  class="biaoti" <?php echo $dodis; ?>>付款</td>
            <td width="33" height="23" align="center"  class="biaoti" <?php echo $dodis; ?>>配送</td>
    		<td width="33" height="23" align="center"  class="biaoti" <?php echo $dodis; ?>>發票</td>
            <td width="33" height="23" align="center"  class="biaoti" <?php echo $dodis; ?>>完成</td>
            <td width="33" height="23" align="center"  class="biaoti" >詳情</td>
		    <td height="23" width="33"  class="biaoti" align="center" <?php echo $dodis; ?>>退訂</td>
		    <td height="23" width="33"  class="biaoti" align="center" <?php echo $nododis; ?>>取消退訂</td>
          </tr>
          
<?php
$fsql->query("SELECT user,name FROM {P}_base_admin WHERE user!='wayhunt'");
while ( $fsql->next_record( ) )
{
	$saleslist[$fsql->f("user")] = $fsql->f("name");
}
$msql->query( "select * from {P}_shop_order where {$scl} order by dtime desc limit {$pagelimit}" );
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
		$ifok = $msql->f( "ifok" );
		$iftui = $msql->f( "iftui" );
		$ifchg = $msql->f( "ifchg" );
		$itemtui = $msql->f( "itemtui" );
		$dtime = $msql->f( "dtime" );
		$paytime = $msql->f( "paytime" );
		$yuntime = $msql->f( "yuntime" );
		$items = str_replace(") ",")<br>",$msql->f( "items" ));
		$dtime = date( "y-m-d H:i:s", $dtime );
		$paytime = date( "y-m-d H:i:s", $paytime );
		$uptime = date( "y-m-d H:i:s", $uptime );
		$memname = $msql->f( "name" );
		$membermail = $msql->f( "email" );
		$disaccount = $msql->f( "disaccount" );
		$promoprice = $msql->f( "promoprice" );
		$country = $msql->f( "country" );
		$source = $msql->f( "source" );
		$buysource = $msql->f( "buysource" );
		
		$ifreceipt = $msql->f( "ifreceipt" );
		$sales = $msql->f( "sales" );
		$looklog = $msql->f( "looklog" );
		
		$slists = "<option value=''>尚未記錄</option>";
		foreach($saleslist AS $kk=>$vv){
			if($sales == $kk){
				$slists .= "<option value='".$kk."' selected>".$vv."</option>";
			}else{
				$slists .= "<option value='".$kk."'>".$vv."</option>";
			}
		}
		
		$paytype = str_replace("宅配","",$paytype);
		if($paytype == "線上刷卡"){
			$paytype = "<span style='color: blue;'>".$paytype."</span>";
		}
		
		if($yunid>0){
			$getYun = $fsql->getone( "select yunname from {P}_shop_yun where id='$yunid'" );
			$paytype .= "<br><span style='color:red'>".$getYun['yunname']."</span>";
		}
		
		switch ( $ifreceipt )
		{
		case "0" :
				$reimg = "toolbar_no.gif";
				break;
		case "1" :
				$reimg = "toolbar_ok.gif";
				break;
		}
		
		switch ( $iflook )
		{
		case "0" :
				$lookimg = "toolbar_no.gif";
				break;
		case "1" :
				$lookimg = "toolbar_ok.gif";
				break;
		}
		switch ( $ifok )
		{
		case "0" :
				$okimg = "toolbar_no.gif";
				break;
		case "1" :
				$okimg = "toolbar_ok.gif";
				break;
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
		switch ( $ifyun )
		{
		case "0" :
				$yunimg = "toolbar_no.gif";
				break;
		case "1" :
				$yunimg = "toolbar_ok.gif";
				break;
		}
		if ( $memberid == "0" )
		{
				$user = "非會員";
		}
		
		
		
		switch ( $source )
		{
		case "0" :
				$sourcestr = "官網訂單";
				break;
		case "1-1" :
				$sourcestr = "板橋店";
				break;
		case "1-2" :
				$sourcestr = "新莊店";
				break;
		case "1-3" :
				$sourcestr = "內湖店";
				break;
		case "1-4" :
				$sourcestr = "三重店";
				break;
		case "1-5" :
				$sourcestr = "南屯店";
				break;
		case "2-1" :
				$sourcestr = "蝦皮";
				break;
		case "2-2" :
				$sourcestr = "MOMO";//
				break;
		case "2-3" :
				$sourcestr = "Yahoo超級商城";//
				break;
		default :
				$sourcestr = "官網訂單";
		}
		
		$sourceselect = '
			 <select class="chgsource" id="so_'.$orderid.'">
      			<option value="0" '.seld( $source, "0" ).'>官網訂單</option>
     			 <option value="1-1" '.seld( $source, "1-1" ).'>板橋店</option>
     			 <option value="1-2" '.seld( $source, "1-2" ).'>新莊店</option>
     			 <option value="1-3" '.seld( $source, "1-3" ).'>內湖店</option>
     			 <option value="1-4" '.seld( $source, "1-4" ).'>三重店</option>
     			 <option value="1-5" '.seld( $source, "1-5" ).'>南屯店</option>
     			 <option value="2-1" '.seld( $source, "2-1" ).'>蝦皮</option>
     			 <option value="2-2" '.seld( $source, "2-2" ).'>MOMO</option>
     			 <option value="2-3" '.seld( $source, "2-3" ).'>Yahoo超級商城</option>
   			 </select>';
		
		$tuinote = "";
		if($itemtui){
			$tuinote = "<span style='color:red;'>(此筆訂單有申請退貨)</span>";
		}
		if($ifchg){
			$tuinote = "<span style='color:red;'>(此筆訂單有申請換貨)</span>";
		}
		echo "<tr class=\"list\" id=\"tr_".$orderid."\" >
             <td width=\"65\" valign=\"top\"  style=\"padding-left:10px\">".$OrderNo." </td>
			 <td width=\"65\" valign=\"top\"><a href=\"../../member/admin/index.php?memberid=".$memberid."\" target=\"_blank\" style=\"color:#996600\">".$name."</a></td>
			 <td valign=\"top\">".$items.$noteifpay[$orderid].$tuinote." </td>
			 <td width=\"70\" valign=\"top\" >";
		/*if ( $ifpay != "1" && $ifok != "1" && iftui != "1" )
		{
				echo "<input id=\"goodstotal_".$orderid."\" class=\"modiprice\" value=\"".(INT)$goodstotal."\" style=\"width:65px\" readonly />";
		}
		else
		{
				echo (INT)$goodstotal;
		}*/
		echo "<input id=\"goodstotal_".$orderid."\" class=\"modiprice\" value=\"".(INT)$goodstotal."\" style=\"width:65px\" readonly />";
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
		if($iftui == "1" && $payid == "1"){
			$recardpay = '<form name=fm method=post action="https://www.focas.fisc.com.tw/FOCAS_WEBPOS/orderInquery/">
			<input type=hidden name=MerchantID value="'.$MerchantID.'">
			<input type=hidden name=TerminalID value="'.$TerminalID.'">
			<input type=hidden name=merID value="'.$merID.'">
			<input type=hidden name=purchAmt value="'.(INT)$paytotal.'">
			<input type=hidden name=ResURL value="http://localhost:3003:80checkorder_tcbbank.php">
			<input type="text" class="input" name="lidm" value="" placeholder="輸入合庫顯示的訂單編號">
			<input type="submit" class="button" value="檢測刷卡訂單">
			</form>';
		}else{
			$recardpay = "";
		}
		echo "</td>
			 <!--<td width=\"50\" valign=\"top\" id=\"yunbaofei_".$orderid."\">".(INT)$yunbaofei."</td>-->
			<td width=\"70\" valign=\"top\" id=\"disaccount_".$orderid."\">".(INT)$disaccount."</td>
			<td width=\"70\" valign=\"top\" id=\"promoprice_".$orderid."\">";
		if ( $ifok != "1" && iftui != "1" )
		{
				echo "<input id=\"newpromoprice_".$orderid."\" class=\"modipromoprice\" value=\"".(INT)$promoprice."\" style=\"width:45px\" readonly />";
		}
		else
		{
				echo (INT)$promoprice;
		}
		echo "</td>
			 <td width=\"65\" valign=\"top\" id=\"paytotal_".$orderid."\">".(INT)$paytotal."</td>
             <td width=\"70\" valign=\"top\">".$dtime." </td>
            <td width=\"60\" valign=\"top\">".$country." </td>
             <td width=\"70\" valign=\"top\">".$paytype.$recardpay." </td>
            <td width=\"60\" valign=\"top\">".$sourceselect." </td>
            <td width=\"60\" valign=\"top\"><select class=\"chgsales\" id=\"sa_".$orderid."\">".$slists."</select></td>
             <td width=\"33\" align=\"center\" valign=\"top\" ".$dodis."><img id=\"orderlook_".$orderid."\" src=\"images/".$lookimg."\"  border=\"0\" class=\"orderlook\" style=\"cursor:pointer\" /><br />".$looklog."</td>
             <td width=\"33\" align=\"center\" valign=\"top\" ".$dodis."><img id=\"orderpay_".$orderid."\" src=\"images/".$payimg."\"  border=\"0\" class=\"orderpay\" style=\"cursor:pointer\" /></td>
             <td width=\"33\" align=\"center\" valign=\"top\" ".$dodis."><img id=\"orderyun_".$orderid."\" src=\"images/".$yunimg."\"  border=\"0\" class=\"orderyun\"  style=\"cursor:pointer\" /></td>
            <td width=\"33\" align=\"center\" valign=\"top\" ".$dodis."><img id=\"orderre_".$orderid."\" src=\"images/".$reimg."\"  border=\"0\" class=\"orderre\"  style=\"cursor:pointer\" /></td>
             <td width=\"33\" align=\"center\" valign=\"top\" ".$dodis."><img id=\"orderok_".$orderid."\" src=\"images/".$okimg."\"  border=\"0\" class=\"orderok\"  style=\"cursor:pointer\" /></td>
             <td width=\"33\" align=\"center\" valign=\"top\" ><a href=\"order_detail.php?orderid=".$orderid."\"><img src=\"images/look.png\" width=\"24\" height=\"24\"  border=\"0\" /></a> </td>
             <td width=\"33\" align=\"center\" valign=\"top\" ".$dodis."><img id=\"ordertui_".$orderid."\" src=\"images/delete.png\"  border=\"0\" class=\"ordertuithis\"  style=\"cursor:pointer\" />
			 </td>
             <td width=\"33\" align=\"center\" valign=\"top\" ".$nododis."><img id=\"order_".$orderid."\" src=\"images/reflesh.gif\"  border=\"0\" class=\"orderthis\"  style=\"cursor:pointer\" />
			 </td>
           </tr>";
}
echo "
</table>
</div>
";
$pagesinfo = $pages->shownow( );
?>
<div id="showpages">
	  <div id="pagesinfo"><?php echo $strPagesTotalStart.$totalnums.$strPagesTotalEnd; ?> <?php echo $strPagesMeiye.$pagesinfo['shownum'].$strPagesTotalEnd; ?> <?php echo $strPagesYeci; ?> <?php echo $pagesinfo['now']."/".$pagesinfo['total']; ?></div>
	  <div id="pages"><?php echo $pages->output( 1 ); ?></div>
</div>
	
	<script src="js/frame.js"></script>
	<script src="../../base/admin/assets/js/plugins/iframeautoheight/jquery.autoheight.js"></script>
	<script>
		$(document).ready(function(){
			//載入時隱藏選單
			$('#sidemenu, #topmenu', window.parent.document).removeClass('in');
			//呼叫左側功能選單
			$().getMenuGroup('shop');
		});
	</script>
	<br /><br /><br /><br /><br />
</body>
</html>