//更換地址
$(document).ready(function(){
	
	
		var aid = $('#seladdr').val();
	
		$.ajax({
			type: "POST",
			url:PDV_RP+"shop/post.php",
			data: "act=seladdrchg&aid="+aid,
			success: function(msg){
				eval(msg);
				$("#show_name").text(M.N);
				$("#show_mobi").text(M.M);
				$("#show_addr_a").text(M.AA);
				$("#show_addr_b").text(M.AB);
				$("#show_addr_c").text(M.AC);
				$("#show_postcode").text(M.P);
				$("#show_country").text(M.C);
				
				$("#s_name").val(M.N);
				$("#tel").val(M.T);
				$("#s_mobi").val(M.M);
				$("#s_addr").val(M.A);
				$("#s_country").val(M.C);
				if(M.IN) $("#company").val(M.IN);
				if(M.IU) $("#passcode").val(M.IU);
				$("#s_postcode").val(M.P);
				if(aid>0){
					$("a[href='#edit-adress']").show().prop("id","modi_"+aid);
				}else{
					$("a[href='#edit-adress']").hide().prop("id","modi_"+aid);
				}
			}
		});
		
	$(document).on("change", "#seladdr", function () {
		var aid = $(this).val();
		$.ajax({
			type: "POST",
			url:PDV_RP+"shop/post.php",
			data: "act=seladdrchg&aid="+aid,
			success: function(msg){
				eval(msg);
				$("#show_name").text(M.N);
				$("#show_mobi").text(M.M);
				$("#show_addr_a").text(M.AA);
				$("#show_addr_b").text(M.AB);
				$("#show_addr_c").text(M.AC);
				$("#show_postcode").text(M.P);
				$("#show_country").text(M.C);
				
				$("#s_name").val(M.N);
				$("#tel").val(M.T);
				$("#s_mobi").val(M.M);
				$("#s_addr").val(M.A);
				$("#s_country").val(M.C);
				if(M.IN) $("#company").val(M.IN);
				if(M.IU) $("#passcode").val(M.IU);
				$("#s_postcode").val(M.P);
				
				if(aid>0){
					$("a[href='#edit-adress']").show().prop("id","modi_"+aid);
				}else{
					$("a[href='#edit-adress']").hide().prop("id","modi_"+aid);
				}
			}
		});
   });
   
   	$('.editaddr').click(function(){ 
		var aid = this.id.substr(5);
		var memberid = $("#memberid").val();
		if(aid == "0"){
			window.location=PDV_RP+'member/member_contact.php?act=modi&type=mobi&memberid='+memberid;
		}else{
			window.location=PDV_RP+'member/member_contact.php?act=modiaddr&type=mobi&aid='+aid;
		}
   });
   
   	//付款方式
   	var payid=getCookie("PAYMENT");
	
	if( $("#payid").val() == "" ){
		$("#payid").val(payid);
	}
	
	//信用卡付款
	$('.credit-card-select-bn').click(function(){ 
		var cardid = this.id.substr(11);
		$("#cardid").val(cardid);
   });
   
});


//會員退出
$(document).ready(function(){
	
	$('.logoutlink').click(function(){ 
		
		$.ajax({
			type: "POST",
			url: PDV_RP+"post.php",
			data: "act=memberlogout",
			success: function(msg){
				if(msg=="OK"){
					window.location='startorder.php';
				}else{
					LoadMsg(msg);
				}
			}
		});
	

   }); 
});


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


//送出訂單

$(document).ready(function(){
	$('#OrderForm').submit(function(){
		/*alert('送出表單');
		alert('s_name:' + $("#s_name")[0].value);
		alert('s_addr:' +$("#s_addr")[0].value);
		alert('payid:' +$("#payid")[0].value);
		alert('marketname:' +$("#marketname").val());
		alert('store_service_num:' +$("#store_service_num").val());
		alert('marketaddr:' +$("#marketaddr").val());
		alert('postpayname:' +$("#postpayname").val());
		alert('cardid:' +$("#cardid").val());
		alert('s_mobi:' +$("#s_mobi").val());
		alert('act:' +$("#act").val());
		alert('tjine:' +$("#tjine").val());
		alert('urlstr:' +$("#urlstr").val());*/
		/*LoadMsg("送出訂單");
		return false;*/
		
		/*超商 20190719*/
		if( $('input:checked[name="shipinfo"]').length == 0 ){
				LoadMsg("請選擇配送資訊！");
				return false;
		}else{
			if( $('input:checked[name="shipinfo"]').val() == 1 ){
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
					window.location=PDV_RP+'shop/cart.php';
					return false;
				}
				var nob = IndexAddrB("000"+$("#s_addr")[0].value);
				
				if($("#payid")[0].value==2 && nob){
					LoadMsg("偏遠地區無法使用貨到付款，請改用信用卡付款，敬請見諒。");
					window.location=PDV_RP+'shop/cart.php';
					return false;
				}

				
		//alert('noa:' +noa);
		
		//alert('nob:' +nob);
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
			}else if($('input:checked[name="shipinfo"]').val() == 2){
				
				var nosa = IndexAddr("000"+$("#marketaddr").val());
				if(nosa){
					LoadMsg("外離島無法使用超商取貨，請改用宅配寄送，敬請見諒。");
					return false;
				}



				// $('#markets').on("change", function() { 
				//     if($(this).val() == '統一超商 7-11')  {
				//     	$('#store_service_num').attr('maxlength','6');
				//     	console.log("6");
				//     }

				//     if($(this).val() == '全家 FamilyMart') {
				//     	$('#store_service_num').attr('maxlength','5');
				//     	console.log("5");
				//     }

				//     if($(this).val() == '萊爾富 Hi-Life') {
				//     	$('#store_service_num').attr('maxlength','4');
				//     	console.log("4");
				//     }

				//     if($(this).val() == 'OK便利商店') {
				//     	$('#store_service_num').attr('maxlength','4');
				//     	console.log("4");
				//     }
				// });


//.trigger();


// 門市地址
// 選擇超商
// (統一超商7-11)
// 門市名稱
// 門市店號(必須為6位數)
// 取貨人姓名
// 行動電話



// 門市地址
// 選擇超商
// (全家FamilyMart)
// 門市名稱
// 服務代號(必須為5位數)
// 取貨人姓名
// 行動電話



// 門市地址
// 選擇超商
// (萊爾富Hi-Life、OK便利商店)
// 門市名稱
// 門市店號(必須為4位數)
// 取貨人姓名
// 行動電話

				
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

if( $('input:checked[name="receiptinfo"]').length == 0 ){
		LoadMsg("請選擇發票資訊！");
		return false;
}else{
	if( $('input:radio:checked[name="receiptinfo"]').val() == 1 && $('input:radio:checked[name="receipt_info_first"]').val() == 1 && $("#postpay").val() == 0 ){
			LoadMsg("請選擇您要捐贈的社福單位！");
			return false;
	}
	if( $('input:radio:checked[name="receiptinfo"]').val() == 1 && $('input:radio:checked[name="receipt_info_first"]').val() == 2 && $("#postnum").val() == "" ){
			LoadMsg("請輸入您要捐贈的社福愛心碼！");
			return false;
	}

	if( $('input:radio:checked[name="receiptinfo"]').val() == 2 && $('input:radio:checked[name="receipt_info_second"]').val() == 2 ){
		var p=$("#mobicode").val();
		var w=$("#remobicode").val();
		if(p =="" || p!=w){
			LoadMsg("兩次輸入的手機載具條碼不一致，請檢查！");
			return false;
		}

		var patt = /^\/[0-9A-Z.+-]{7}$/;
        if(!patt.test(p)){
            LoadMsg("手機載具條碼格式不符，請輸入含斜線共8碼，例如/ABC+123。");
            return false;
        }
	}	
	if( $('input:radio:checked[name="receiptinfo"]').val() == 2 && $('input:radio:checked[name="receipt_info_second"]').val() == 3 ){
		var p=$("#cdcode").val();
		var w=$("#recdcode").val();
		if(p =="" || p!=w){
			LoadMsg("兩次輸入的自然人憑證條碼不一致，請檢查！");
			return false;
		}
	}
	
	if( $('input:radio:checked[name="receiptinfo"]').val() == 3 && $('#invoicenumber').val() == "" ){
			LoadMsg("請輸入統一編號！");
			return false;
	}
	if( $('input:radio:checked[name="receiptinfo"]').val() == 3 && $('#invoicename').val() == "" ){
			LoadMsg("請輸入發票抬頭！");
			return false;
	}
	
	
	
}
		
		if($("#tjine")[0].value=="" || Number($("#tjine")[0].value)<0){
			LoadMsg("您的購物車中沒有商品或商品金額錯誤，不能送出訂單");
			return false;
		}


		if($("#payid")[0].value==""){
			LoadMsg("請選擇付款方式");
			return false;
		}
	
	var checkText=$("#postpay").find("option:selected").text();
	$("#postpayname").val(checkText);

	if( $('input:radio:checked[name="receiptinfo"]').val() == 2 && $('input:radio:checked[name="receipt_info_second"]').val() == 2 ){
		
		//alert('第一種');
		var p=$("#mobicode").val();
		var w=$("#remobicode").val();

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
	}else{
		//alert('第二種');
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



//	Select change control
$(document).on("change", "#markets", function () {
	// alert( "Handler for .change() called." );

	//	$('#markets').on("change", function() { 
	    if($(this).val() == '統一超商 7-11')  {
	    	$('#store_service_num').attr('maxlength','6');
	    	$('#store_service_num').attr('placeholder','請輸入 門市店號 (123456)');
	    	$('#store_service_num').val('');
	    	// console.log("6");
	    }

	    if($(this).val() == '全家 FamilyMart') {
	    	$('#store_service_num').attr('maxlength','5');
	    	$('#store_service_num').attr('placeholder','請輸入 門市店號 (12345)');
	    	$('#store_service_num').val('');
	    	// console.log("5");
	    }

	    if($(this).val() == '萊爾富 Hi-Life') {
	    	$('#store_service_num').attr('maxlength','4');
	    	$('#store_service_num').attr('placeholder','請輸入 門市店號 (1234)');
	    	$('#store_service_num').val('');
	    	// console.log("4");
	    }

	    if($(this).val() == 'OK便利商店') {
	    	$('#store_service_num').attr('maxlength','4');
	    	$('#store_service_num').attr('placeholder','請輸入 門市店號 (1234)');
	    	$('#store_service_num').val('');
	    	// console.log("4");
	    }
	//	});
});



//浮點計算
function adv_format(value,num) {
	var a_str = formatnumber(value,num);
	var a_int = parseFloat(a_str);
	if (value.toString().length>a_str.length)
	{
	var b_str = value.toString().substring(a_str.length,a_str.length+1)
	var b_int = parseFloat(b_str);
	if (b_int<5)
	{
	return a_str
	}
	else
	{
	var bonus_str,bonus_int;
	if (num==0)
	{
	bonus_int = 1;
	}
	else
	{
	bonus_str = "0."
	for (var i=1; i<num; i++)
	bonus_str+="0";
	bonus_str+="1";
	bonus_int = parseFloat(bonus_str);
	}
	a_str = formatnumber(a_int + bonus_int, num)
	}
	}
	return a_str
	}

	function formatnumber(value,num) //直接去尾
	{
	var a,b,c,i
	a = value.toString();
	b = a.indexOf('.');
	c = a.length;
	if (num==0)
	{
	if (b!=-1)
	a = a.substring(0,b);
	}
	else
	{
	if (b==-1)
	{
	a = a + ".";
	for (i=1;i<=num;i++)
	a = a + "0";
	}
	else
	{
	a = a.substring(0,b+num+1);
	for (i=c;i<=b+num;i++)
	a = a + "0";
	}
	}
	return a
}



//獲取彈出式登入框
(function($){
	$.fn.orderMemberLogin = function(act){
		
		//獲取登入表單
		$.ajax({
			type: "POST",
			url:PDV_RP+"member/post.php",
			data: "act=getpoploginform&RP="+PDV_RP,
			success: function(msg){
				
				$('html').append(msg);
				$.blockUI({message: $('div#loginDialog'),css:{width:'300px'}}); 
				$('.pwClose').click(function() { 
					if(act=="1"){
						$.unblockUI(); 
						$('div#loginDialog').remove();
					}else{
						window.location.reload();
					}
					
				}); 

				$('img#zhuce').click(function() { 
					$.unblockUI(); 
					window.location=PDV_RP+"member/reg.php";
				}); 

				$("img#fmCodeImg").click(function () { 
					$("img#fmCodeImg")[0].src=PDV_RP+"codeimg.php?"+Math.round(Math.random()*1000000);
				 });

				 $('#LoginForm').submit(function(){ 

					$('#LoginForm').ajaxSubmit({
						target: 'div#loginnotice',
						url: PDV_RP+'post.php',
						success: function(msg) {
							if(msg=="OK" || msg.substr(0,2)=="OK"){
								$('div#loginnotice').hide();
								$.unblockUI(); 
								$('div#loginDialog').remove();
								window.location.reload();
							}else{
								$('div#loginnotice').show();
							}
						}
					}); 
			   
					return false; 

			 	}); 


			}
		});

		
	};
})(jQuery);