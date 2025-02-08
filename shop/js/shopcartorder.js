var receiptinfo=0;


$(document).ready(function(){
	
	// 參考 cart.js $(".gostart").click
	
	$("#checked_continue_btn_step3_JohnStopWork").click(function(){
				
		

		var getpaytotal = (orderInfo.oritjine+orderInfo.yunfei-orderInfo.disaccount);
		var payid = $('input[name=delivery]:checked').val();
		var sourceyun = $('input[name=delivery_method]:checked').val();
		var sourceyunfei = orderInfo.yunfei
		var sourcediscount = orderInfo.disaccount;
		var promocode=jdepromocode;

		//ad5df3FKLMrti-zMegUK1r-6i5xp_UVtaZdsEPx_Qy-gHY0smK5PZw
		//c8c0dh-YkYYlwK04uEy2oXHzg9PzI2dIDNkPNB8fkQHLWoTNiAp9UQ
		alert(getpaytotal);
		alert(sourceyun);
		alert(jdepromocode);
		/*
		var payid = $('input[name=payment]:checked').val();
		var source = $('input[name=source]:checked').val();
		var sourceyun = $('input[name=sourceyun]:checked').val();
		var sourceyunfei = $('#sourceyunfei').val();
		var sourcediscount = $('#sourcediscount').val();

		if( payid == 2  ){
			if(getpaytotal >0){
				var promocode = $("#promocode2").val();
			}else{
				var promocode = this.id;
			}
		}else{
			var promocode = this.id;
		}
			$.ajax({
				type: "POST",
				url:PDV_RP+"post.php",
				data: "act=setcookie&cookietype=addnew&cookiename=PAYMENT&getnums="+payid,
				success: function(msg){
					if(typeof source=="undefined"){
						window.location=PDV_RP+'shop/startorder.php?promocode='+promocode;
					}else{
						window.location=PDV_RP+'shop/startorder.php?source='+source+'&sourceyun='+sourceyun+'&sourceyunfei='+sourceyunfei+'&sourcediscount='+sourcediscount+'&promocode='+promocode;
					}
				}
			});
*/

		/*  20241216 後續結合優惠需要用到
		var promotype = $('#promotype').val();
		var promotypecode = $('#promotypecode').val();
		
		if(typeof payid == "undefined"){
			LoadMsg("請選擇付款方式");
			return false
		}
		if(promotype == 2){
			$("#Attention2").css({display:"block"});
			$("#autostart2").fancybox({padding:0, 'showCloseButton':false}).trigger('click');
			$("#fancybox-wrap").css({width:"780px"});
			$("#fancybox-content").css({width:"780px"});
			$("#gopromo2").click(function(){
				window.location=PDV_RP+'shop/cart.php?promotypecode='+promotypecode+'&payid='+payid;
			});
			return false
		}else if(promotype == 3){
			$("#Attention3").css({display:"block"});
			$("#autostart3").fancybox({padding:0, 'showCloseButton':false}).trigger('click');
			$("#fancybox-wrap").css({width:"780px"});
			$("#fancybox-content").css({width:"780px"});
			$("#gopromo3").click(function(){
				window.location=PDV_RP+'shop/cart.php?promotypecode='+promotypecode+'&payid='+payid;
			});
			return false
		}else if(promotype == 1){
			$("#Attention1").css({display:"block"});
			$("#autostart1").fancybox({padding:0, 'showCloseButton':false}).trigger('click');
			$("#fancybox-wrap").css({width:"780px"});
			$("#fancybox-content").css({width:"780px"});
			$("#gopromo1").click(function(){
				var promosize = $('#promosize').val();
				if(promosize == 0){
					alert("請選擇贈品尺寸！");
					return false
				}
				window.location=PDV_RP+'shop/cart.php?promotypecode='+promotypecode+'&promospec='+promosize+'&payid='+payid;
			});
			return false
		}else{
			var getpaytotal = $("#tjine").val();
			var payid = $('input[name=payment]:checked').val();
			var source = $('input[name=source]:checked').val();
			var sourceyun = $('input[name=sourceyun]:checked').val();
			var sourceyunfei = $('#sourceyunfei').val();
			var sourcediscount = $('#sourcediscount').val();
			
			if( payid == 2  ){
				if(getpaytotal >0){
					var promocode = $("#promocode2").val();
				}else{
					var promocode = this.id;
				}
			}else{
				var promocode = this.id;
			}
				$.ajax({
					type: "POST",
					url:PDV_RP+"post.php",
					data: "act=setcookie&cookietype=addnew&cookiename=PAYMENT&getnums="+payid,
					success: function(msg){
						if(typeof source=="undefined"){
							window.location=PDV_RP+'shop/startorder.php?promocode='+promocode;
						}else{
							window.location=PDV_RP+'shop/startorder.php?source='+source+'&sourceyun='+sourceyun+'&sourceyunfei='+sourceyunfei+'&sourcediscount='+sourcediscount+'&promocode='+promocode;
						}
					}
				});
			}
	*/});


	


	$(".x_mark").click(function(){

		var gid=this.id.substr(8);
		var fz=$("#fz_"+gid)[0].value;
		
		var getgid = gid.split("_");
		
		$.ajax({
			type: "POST",
			url:PDV_RP+"post.php",
			data: "act=setcookie&cookietype=del&cookiename=SHOPCART&gid="+getgid[0]+"&picgid="+getgid[2]+"&fz="+fz,
			success: function(msg){
				if(msg=="OK"){
					window.location=PDV_RP+'shop/cart.php';
				}
			}
		});
	});

	$(".x_mark_iphone_rwd").click(function(){

		var gid=this.id.substr(8);
		var fz=$("#fz_"+gid)[0].value;
		
		var getgid = gid.split("_");
		
		$.ajax({
			type: "POST",
			url:PDV_RP+"post.php",
			data: "act=setcookie&cookietype=del&cookiename=SHOPCART&gid="+getgid[0]+"&picgid="+getgid[2]+"&fz="+fz,
			success: function(msg){
				if(msg=="OK"){
					window.location=PDV_RP+'shop/cart.php';
				}
			}
		});
	});
	
	/*
	$("#checked_continue_btn_step1").click(function(){
		$.ajax({
			type: "POST",
			url: PDV_RP+"member/post.php",
			data: "act=getzonelist&pid=1",
			success: function(msg){
				pList.data = new Array();
				$("#zonelist").html(msg);
				//console.log('msg:'+msg);
				if(PDV_LAN == "en"){
					var constr = "Please Select";
				}else if(PDV_LAN == "zh_cn"){
					var constr = "请选择";
				}else{
					var constr = "請選擇";
				}
				$("#Province").html("<option value='s'> "+constr+"</option>"+pList.getOptionString('s'));
				//$('#Province').selectpicker('refresh');
			}
		
	 });


	});
	*/

});


$(document).ready(function(){
	$('#OrderForm').submit(function(){

		//urlstr  就等於 jdepromocode =>是從shopcart 傳到startorder

		//var cityText = $('#Province option:selected').text();
		var addrValue =$("#addr").val();
		var overseaAddrValue=$("#saddr").val();
		//alert('cityText' + cityText);
		//alert('deliveryInfo.saddr:'+deliveryInfo.saddr);
		$("#s_addr").val(addrValue);
		//alert($("#s_addr").val());


		/*
		$("#addrnote").val("0");
		var addrnote = 0;
		*/

		const deliveryMethod = $('input[name="delivery_method"]:checked').val();

		const deliveryMethodALL = $('input[name="delivery"]:checked').attr('id');
    	// 根據選擇的配送方法和付款方式更新選項
		if(deliveryMethodALL)
		{
			if (deliveryMethodALL === 'overseas_shipping'){
				$("#s_addr").val(overseaAddrValue);
				var overseaCountryValue = $('#selcountry2').val();
				let overseaparts = overseaCountryValue.split("_");
				$("#s_country").val(overseaparts[0]);
				var noa = IndexAddr("000"+$("#s_addr")[0].value);
				if($("#s_name")[0].value==""){
					if(PDV_LAN == "en"){
						LoadMsg("Please fill in your name.");
					}else if(PDV_LAN == "zh_cn"){
						LoadMsg("请填写收货人姓名");
					}else{
						LoadMsg("請填寫收貨人姓名");
					}
					return false;
				}
				
				if($("#s_addr")[0].value==""){
					if(PDV_LAN == "en"){
						LoadMsg("Please fill in your address.");
					}else if(PDV_LAN == "zh_cn"){
						LoadMsg("请填写收货人地址");
					}else{
						LoadMsg("請填寫收貨人地址");
					}
					return false;
				}
				if($("#s_mobi")[0].value==""){
				
					if(PDV_LAN == "en"){
						LoadMsg("Please fill in the contact number.");
					}else if(PDV_LAN == "zh_cn"){
						LoadMsg("请填写联络电话");
					}else{
						LoadMsg("請填寫聯絡電話");
					}
					return false;
				}

			}
			else{
    			if (deliveryMethod) {
        			if (deliveryMethod === 'convenience_store') {
            			$("#shipinfo").val("2");
        			} 
					else if (deliveryMethod === 'home_delivery') {
            		$("#shipinfo").val("1");
        			}

		if( $("#shipinfo").val() == 1 ){
			var noa = IndexAddr("000"+$("#s_addr")[0].value);
			
			if($("#s_name")[0].value==""){
				if(PDV_LAN == "en"){
					LoadMsg("Please fill in your name.");
				}else if(PDV_LAN == "zh_cn"){
					LoadMsg("请填写收货人姓名");
				}else{
					LoadMsg("請填寫收貨人姓名");
				}
				return false;
			}
			
			if($("#s_addr")[0].value==""){
				if(PDV_LAN == "en"){
					LoadMsg("Please fill in your address.");
				}else if(PDV_LAN == "zh_cn"){
					LoadMsg("请填写收货人地址");
				}else{
					LoadMsg("請填寫收貨人地址");
				}
				return false;
			}
			
			if($("#payid")[0].value==2 && noa && Number($("#tjine")[0].value)>0){
				LoadMsg("外離島無法使用貨到付款，請改用信用卡付款，敬請見諒。");
				//window.location=PDV_RP+'shop/cart.php';
				return false;
			}
			var nob = IndexAddrB("000"+$("#s_addr")[0].value);
			
			if($("#payid")[0].value==2 && nob){
				LoadMsg("偏遠地區無法使用貨到付款，請改用信用卡付款，敬請見諒。");
				//window.location=PDV_RP+'shop/cart.php';
				return false;
			}
			if(noa || nob){
				$("#addrnote").val("1");
				var addrnote = 1;
				//alert('addrnote:' + addrnote);
			}else{
				$("#addrnote").val("0");
				var addrnote = 0;
				//alert('addrnote0:' + addrnote);
			}

			if($("#s_mobi")[0].value==""){
				
				if(PDV_LAN == "en"){
					LoadMsg("Please fill in the contact number.");
				}else if(PDV_LAN == "zh_cn"){
					LoadMsg("请填写联络电话");
				}else{
					LoadMsg("請填寫聯絡電話");
				}
				return false;
			}
		}else if($("#shipinfo").val() == 2){
				
			var nosa = IndexAddr("000"+$("#marketaddr").val());
			if(nosa){
				LoadMsg("外離島無法使用超商取貨，請改用宅配寄送，敬請見諒。");
				return false;
			}

			
			if( $("#marketname").val() == "" ){
				LoadMsg("請填寫 超商門市名稱！");
				return false;
			}
			if( $("#store_service_num").val() == "" ){
				LoadMsg("請填寫 服務編號！");
				return false;
			}
			if( $("#marketaddr").val() == "" ){
				LoadMsg("請填寫 超商門市地址！");
				return false;
			}
			if( $("#mk_name").val() == "" ){
				LoadMsg("請填寫 取貨人姓名！");
				return false;
			}
			if( $("#mk_mobi").val() == "" ){
				LoadMsg("請填寫 行動電話！");
				return false;
			}
		}





    			}
				else{
					LoadMsg("請選擇配送資訊！");
						return false;
				}
			}

		}
		else
		{
			LoadMsg("請選擇配送方式！");
				return false;
		}




		if($("#tjine")[0].value=="" || Number($("#tjine")[0].value)<0){
			LoadMsg("您的購物車中沒有商品或商品金額錯誤，不能送出訂單");
			return false;
		}


		if($("#payid")[0].value==""){
			LoadMsg("請選擇付款方式");
			return false;
		}
		if($("#payid")[0].value=="3"){
			$("#payid").val("1");
		}
		


		

	if( $("#receiptinfo").val() == 1 && $("#receipt_info_first").val() == 1){
			if($("#postpay").val() == 0 ){
			LoadMsg("請選擇您要捐贈的社福單位！");
			return false;
			}
	}
	if( $("#receiptinfo").val() == 1 && $("#receipt_info_first").val() == 2){
		if( $("#postnum").val() == "" )
		{
			LoadMsg("請輸入您要捐贈的社福愛心碼！");
			return false;
		}
	}

	if( $("#receiptinfo").val() == 2 && $("#receipt_info_second").val() == 2 ){
		var p=$("#barcode-input").val();
		var w=$("#barcode-input-2").val();
		if(p =="" || p!=w){
			LoadMsg("兩次輸入的手機載具條碼不一致，請檢查！");
			return false;
		}

		var patt = /^\/[0-9A-Z.+-]{7}$/;
        if(!patt.test(p)){
            LoadMsg("手機載具條碼格式不符，請輸入含斜線共8碼，例如/ABC+123。");
            return false;
        }

		$("#mobicode").val(p);
		//alert(p);
		//alert($("#mobicode").val());

	}	
	if( $("#receiptinfo").val() == 2 && $("#receipt_info_second").val() == 3 ){
		var p=$("#cdcode").val();
		var w=$("#recdcode").val();
		if(p =="" || p!=w){
			LoadMsg("兩次輸入的自然人憑證條碼不一致，請檢查！");
			return false;
		}
	}
	
	if( $("#receiptinfo").val() == 3){
		var cn=$("#company-input").val();
		var cnb=$("#company-input-2").val();
		if(cn == "")
		{
			LoadMsg("請輸入統一編號！");
			return false;
		}
		if(cnb == "")
		{
			LoadMsg("請輸入發票抬頭！");
			return false;
		}
		$("#invoicename").val(cnb);
		$("#invoicenumber").val(cn);
	}


	if( $("#receiptinfo").val() == 2 && $("#receipt_info_second").val() == 2 )
	{
		var p=$("#barcode-input").val();
		var w=$("#barcode-input-2").val();

		/*檢測手機條碼正確性*/
			$.ajax({
				type: "POST",
				url: PDV_RP+"shop/post.php",
				data: "act=chkphonenum&phonenum="+encodeURIComponent(p),
				success: function(msgs){
					
					if(msgs=="N"){
						LoadMsg("您的手機條碼並未登錄在財政部電子發票平台，請選擇其他方式！");
						return false;
					}else{
						
			$('#OrderForm').ajaxSubmit({
			url: 'post.php',
			data: {'addrnote': addrnote},
			success: function(msg) {
				
				msg = String(msg);
				
				if(msg.substr(0,2)=="OK"){
					
					//清除cookie
					$.ajax({
						type: "POST",
						url:PDV_RP+"post.php",
						data: "act=setcookie&cookietype=empty&cookiename=SHOPCART",
						success: function(msg){
						}
					});
					
					//判斷是否付款
					if(msg.substr(3,5)=="PAYED"){
						var orderid=msg.substr(9);
						$().alertwindow("訂單送出並付款成功","orderdetail.php?orderid="+orderid);
					}else{
						var orderid=msg.substr(3);
						setTimeout('window.location="orderpay.php?orderid='+orderid+'";',500);
					}
				}else if(msg.substr(0,7)=="wayhunt"){
					//$('div#notice').hide();
					//LoadMsg(msg.substr(8));
				}else if(msg=="999"){
					LoadMsg("60秒內不能再次送出訂單");
				}else if(msg=="1000"){
					LoadMsg("您的購物車中沒有商品");
				}else if(msg=="1001"){
					LoadMsg("請選擇配送區域");
				}else if(msg=="1002"){
					LoadMsg("請選擇付款方法");
				}else if(msg=="1003"){
					LoadMsg("您尚未登入，不能從會員帳戶扣款付款訂單");
				}else if(msg=="1004"){
					LoadMsg("請選擇配送方法");
				}else if(msg=="1005"){
					LoadMsg("您尚未登入，不能送出訂單");
				}else if(msg=="1006"){
					LoadMsg("訂購錯誤，請洽詢客服人員為您處理！");
				}else if(msg=="1007"){
					LoadMsg("60秒內僅能送出訂單一次！");
				}else{
					/*刪除庫存不足之訂購資料*/
					var listmsg = msg.split("_");
					var gid = listmsg[0];
					var fz = listmsg[1];
					
					$.ajax({
						type: "POST",
						url:PDV_RP+"post.php",
						data: "act=setcookie&cookietype=del&cookiename=SHOPCART&gid="+gid+"&fz="+fz,
						success: function(msg){
							
							msg = String(msg);
							
							if(msg=="OK"){
								LoadMsgToUrl(listmsg[2],PDV_RP+'shop/cart.php');
							}else{
								LoadMsg("ERROR:"+msg);
							}
						}
					});
					
				}
			}
		});/**/ 
					}
				}
			});
	}
	else
	{

		$('#OrderForm').ajaxSubmit({
			url: 'post.php',
			data: {'addrnote': addrnote},
			success: function(msg) {
				
				msg = String(msg);
				
		/*LoadMsg(msg);
		return false;*/
				if( msg.substr(0,2)=="OK" ){
					$('div#notice').hide();
					//清除cookie
					$.ajax({
						type: "POST",
						url:PDV_RP+"post.php",
						data: "act=setcookie&cookietype=empty&cookiename=SHOPCART",
						success: function(msg){
						}
					});
					
					//判斷是否付款
					if(msg.substr(3,5)=="PAYED"){
						var orderid=msg.substr(9);
						$().alertwindow("訂單送出並付款成功","orderdetail.php?orderid="+orderid);
					}else{
						var orderid=msg.substr(3);
						setTimeout('window.location="orderpay.php?orderid='+orderid+'";',500);
					}
				}else if(msg=="1000"){
					LoadMsg("您的購物車中沒有商品");
				}else if(msg=="1001"){
					LoadMsg("請選擇配送區域");
				}else if(msg=="1002"){
					LoadMsg("請選擇付款方法");
				}else if(msg=="1003"){
					LoadMsg("您尚未登入，不能從會員帳戶扣款付款訂單");
				}else if(msg=="1004"){
					LoadMsg("請選擇配送方法");
				}else if(msg=="1005"){
					LoadMsg("您尚未登入，不能送出訂單");
				}else if(msg=="1006"){
					LoadMsg("訂購錯誤，請洽詢客服人員為您處理！");
				}else if(msg=="1007"){
					LoadMsg("60秒內僅能送出訂單一次！");
				} else if(msg=="1008") {
					LoadMsg("網路不穩請重新再試一次");
				} else {
					/*刪除庫存不足之訂購資料*/
					var listmsg = msg.split("_");
					var gid = listmsg[0];
					var fz = listmsg[1];
					
					$.ajax({
						type: "POST",
						url:PDV_RP+"post.php",
						data: "act=setcookie&cookietype=del&cookiename=SHOPCART&gid="+gid+"&fz="+fz,
						success: function(msg){
							
							msg = String(msg);
							
							if(msg=="OK"){
								LoadMsgToUrl(listmsg[2],PDV_RP+'shop/cart.php');
							}else{
								LoadMsg("ERROR:"+msg);
							}
						}
					});
					
				}
			}
		});/**/
	}
	

		return false;




	}); 
});
/*
$(document).ready(function() {
    // 這裡放置你希望在 DOM 完全加載後執行的代碼
	//alert('province');
	$.ajax({
		type: "POST",
		url: PDV_RP+"member/post.php",
		data: "act=getzonelist&pid=1",
		success: function(msg){
			pList.data = new Array();
			$("#zonelist").html(msg);
			if(PDV_LAN == "en"){
				var constr = "Please Select";
			}else if(PDV_LAN == "zh_cn"){
				var constr = "请选择";
			}else{
				var constr = "請選擇";
			}
			$("#Province").html("<option value='s'> "+constr+"</option>"+pList.getOptionString('s'));
			//$('#Province').selectpicker('refresh');
		}
	
 });

});
*/



function order_data_set() {
	return {
	  showOrderListLayout: true,
	  orderList: orderList,
	  orderInfo: orderInfo,
	  deliveryInfo: deliveryInfo,
	  jdepromocode :jdepromocode,
	  cuspromocode :cuspromocode,
	  minusItem(orderIndex) {
		if(this.orderList[orderIndex].orderdata.acc>1)
		{
			var tempnum =Number(this.orderList[orderIndex].orderdata.acc);
			this.orderList[orderIndex].orderdata.acc=String(tempnum-1);
			var tempjline =Number(this.orderList[orderIndex].orderdata.jine.replace(/,/g, ""));
			this.orderInfo.oritjine=this.orderInfo.oritjine-tempjline;
			
			var gid=this.orderList[orderIndex].orderdata.gid;
		var fz =this.orderList[orderIndex].orderdata.fz;
		var num=this.orderList[orderIndex].orderdata.acc;
		var getgid = gid.split("_");
		updateShopCart(gid,fz,num,getgid,orderIndex);
		}
	  },
	  addItem(orderIndex) {
		var tempnum =Number(this.orderList[orderIndex].orderdata.acc);
		this.orderList[orderIndex].orderdata.acc=String(tempnum+1);
		var tempjline =Number(this.orderList[orderIndex].orderdata.jine.replace(/,/g, ""));
		this.orderInfo.oritjine=this.orderInfo.oritjine+tempjline;

		var gid=this.orderList[orderIndex].orderdata.gid;
		var fz =this.orderList[orderIndex].orderdata.fz;
		var num=this.orderList[orderIndex].orderdata.acc;
		var getgid = gid.split("_");
		updateShopCart(gid,fz,num,getgid,orderIndex);
		//alert(this.orderList[orderIndex].orderdata.gid+this.orderList[orderIndex].orderdata.fzspecid+this.orderList[orderIndex].orderdata.picgid);
		
		//alert(this.orderList[orderIndex].orderdata.fz);
	  }}
  }


  function order_data_init() {}



  function IndexAddr(str)
{
	var sum = "";
	//var noaddr = ["琉球","澎湖","馬公","西嶼","望安鄉","七美鄉","白沙鄉","湖西鄉","金門縣","金沙鎮","金湖鎮","金寧鄉","金寧鎮","金城鎮","烈嶼","烏坵","馬祖","南竿","北竿","東引"];
	var noaddr = ["澎湖","馬公","西嶼","望安鄉","七美鄉","白沙鄉","湖西鄉","金門縣","金沙鎮","金湖鎮","金寧鄉","金寧鎮","金城鎮","烈嶼","烏坵","馬祖","南竿","北竿","東引"];
	for (var i=0;i<=20;i++){	
		var s = str.indexOf(noaddr[i]);
		if(s >= 0){
			sum = s;
		}
	}
	return(sum);
}

function IndexAddrB(str)
{
	var sum = "";
	/*var noaddr = ["南澳鄉","大同鄉","蘇澳鎮","瑞芳區","貢寮區","雙溪區","平溪區","石門區","大溪鎮","復興鄉","尖石鄉","五峰鄉","泰安鄉","和平區","鹿谷鄉","集集鎮","信義鄉","仁愛鄉","古坑鄉","大埔鄉","阿里山鄉","中埔鄉","竹崎鄉","梅山鄉","番路鄉","玉井區","左鎮區","楠西區","南化區","龍崎區","內門區","杉林區","甲仙區","六龜區","茂林區","桃源區","關仔嶺","旗山區","美濃","田寮區","來義鄉","泰武鄉","獅子鄉","春日鄉","恆春鎮","枋山鄉","車城鄉","牡丹鄉","滿州鄉","瑞穗鄉","長濱鄉","大武鄉","金峰鄉","達仁鄉","太麻里鄉"];
	for (var i=0;i<=55;i++){	
		var s = str.indexOf(noaddr[i]);
		if(s >= 0){
			sum = s;
		}
	}*/
	return(sum);
}



function handleTabClick(event) {
    const target = event.target; // 獲取被點擊的按鈕
    const targetValue = target.getAttribute("data-target"); // 獲取 data-target 屬性值
	
	if(targetValue=='cloud')
	{
		$("#receiptinfo").val(2);
		$("#receipt_info_second").val(1);
	}
	else if(targetValue=='company')
	{
		//var cn=$("#company-input").val();
		//var cnb=$("#company-input-2").val();
		$("#receiptinfo").val(3);
		//$("#invoicename").val(cnb);
		//$("#invoicenumber").val(cn);
	}
	else if(targetValue=='barcode')
	{
		/*
		var p=$("#barcode-input").val();
		var w=$("#barcode-input-2").val();
		if(p =="" || p!=w){
			LoadMsg("兩次輸入的手機載具條碼不一致，請檢查！");
			return false;
		}

		var patt = /^\/[0-9A-Z.+-]{7}$/;
        if(!patt.test(p)){
            LoadMsg("手機載具條碼格式不符，請輸入含斜線共8碼，例如/ABC+123。");
            return false;
        }
	    */
		$("#receiptinfo").val(2);
		$("#receipt_info_second").val(2);
		//$("#mobicode").val(p);

	}
	else if(targetValue=='donate')
	{

	}


}


// 更新選擇的選項
function updateSelectedOption() {
	const deliveryMethod = $('input[name="delivery_method"]:checked').val();

	// 根據選擇的配送方法和付款方式更新選項
	if (deliveryMethod) {
			if (deliveryMethod === 'convenience_store') {
					if ($('#standard_credit_card').is(':checked')) {
							selectedPaymentOption = '信用卡付款／超商取貨'; // 1
					} else if ($('#standard_cash').is(':checked')) {
							selectedPaymentOption = '貨到付款／超商取貨'; // 3
					}
			} else if (deliveryMethod === 'home_delivery') {
					if ($('#standard_credit_card').is(':checked')) {
							selectedPaymentOption = '信用卡付款／宅配到府'; // 2
					} else if ($('#standard_cash').is(':checked')) {
							selectedPaymentOption = '貨到付款／宅配到府'; // 4
					}
			}
	}

	// 更新 配送和付款方式 顯示選擇的結果
	$('#delivery_options_show h3').text(selectedPaymentOption);
}


function updateShopCart(gid,fz,nums,getgid,dataIndex) {

			//檢查庫存

			$.ajax({
				type: "POST",
				url:PDV_RP+"shop/post.php",
				data: "act=chkkucun&gid="+gid+"&nums="+nums,
				success: function(msg){
					if(msg=="OK"){
						$.ajax({
							type: "POST",
							url:PDV_RP+"post.php",
							data: "act=setcookie&cookietype=modi&cookiename=SHOPCART&gid="+getgid[0]+"&picgid="+getgid[2]+"&nums="+nums+"&fz="+fz,
							success: function(msg){
								if(msg=="OK"){
									$.get(PDV_RP+"shop/cart.php?getdata="+getgid[0]+"&getfz="+fz, function(data) {
										/*
										var restr = data.split("^");
										$("#jine_"+gid).text(restr[0]);
										$("#chg_oritjine").text(addCommas(restr[1]));
										$("#oritjine").val(restr[1]);
										$("#payyunfei").text(restr[2]);
										$("#yunfei").val(restr[2]);
										$("#chg_disaccount").text(restr[3]);
										$("#groupon").text(restr[4]);
										$("#grouponadd").text(restr[5]);
										$("#paytotal").text(restr[6]);
										$("#tjine").val(restr[9]);
										$("#promocode1").text(restr[7]);
										$(".gostart").attr("id",restr[7]);
										$("#promocode2").val(restr[8]);
										$("#setyunprice").val(restr[10]);
										$("#setyunprice2").val(restr[11]);
										
										if(parseInt(restr[6])>0){
											$("#payment1").prop("disabled", false);
											$("label.payment1").removeClass("disabled");
										}else{
											$("#payment1").prop("disabled", true);
											$("#payment2").prop("checked", true);
											$("label.payment1").addClass("disabled");
											$("label.payment2").addClass("checked");
										}
											*/
									});
									
									window.location=PDV_RP+'shop/cart.php';
								}else if(msg=="1000"){
									alert("訂購數量錯誤");
									
								}else{
									alert(msg);
									
								}
							}
						});

					}else if(msg=="1000"){
						alert("該商品缺貨或剩餘數量不足");
						window.location=PDV_RP+'shop/cart.php';
					}else{
						alert(msg);
					}
				}
			});
}

