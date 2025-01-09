
//購物車和訂購
$(document).ready(function(){
	
			$.ajax({
				type: "POST",
				url:PDV_RP+"shop/post.php",
				data: "act=getName",
				success: function(msg){
					if(msg != ""){
						if(msg.length>6){
							$("#username").html( "<span>"+msg+"</span>" );
						}else{
							$("#username").html( msg );
						}
					}
				}
			});
	
		/*儲存物車*/
		$(".additems").click(function(){
			var gid=this.id.substr(8);
			var fz=$("#fz_"+gid)[0].value;
			
			var getgid = gid.split("_");
			
			$.ajax({
				type: "POST",
				url:"post.php",
				data: "act=additems&gid="+getgid[0]+"&picgid="+getgid[2]+"&fz="+fz,
				success: function(msg){
					if(msg != ""){
						$("#addnums").text( parseInt($("#addnums").text())+1 );
					}
					$("#showitems").after(msg);
				}
			});
			
		});
		$(document).on("click",".deladditem",function(){
			var addid=this.id.substr(4);
			$.ajax({
				type: "POST",
				url:"post.php",
				data: "act=delitems&addid="+addid,
				success: function(msg){
					if(msg == "OK"){
						$("#additem_"+addid).remove();
						$("#addnums").text( parseInt($("#addnums").text())-1 );
					}else{
						alert(msg);
					}
				}
			});
		});
	/*var payid = $('input[name=payment]:checked').val();

					$.ajax({
						type: "POST",
						url:PDV_RP+"post.php",
						data: "act=setcookie&cookietype=addnew&cookiename=PAYMENT&getnums="+payid,
						success: function(msg){
						}
					});*/

					

		/*2014-07-22*/
			$(".payment1").click(function(){
				var getyunfei = $("#yunfei").val();
				var getpaytotal = $("#tjine").val();
				//運費設定
				var setyunprice = parseFloat($("#setyunprice").val());
				if( getyunfei != 0){
					var oripay = FloatSubtraction(getpaytotal, getyunfei);
					var newpay = FloatAdd(oripay, setyunprice);
					var fornewpay = addCommas(newpay);
					$("#payyunfei").text(setyunprice);
					$("#yunfei").val(setyunprice)
					$("#paytotal").text(fornewpay);
					$("#tjine").val(newpay);
				}
			});
			$(".payment2").click(function(){
				var getyunfei = $("#yunfei").val();
				var getpaytotal = $("#tjine").val();
				//運費設定
				var setyunprice = parseFloat($("#setyunprice2").val());
				if( getyunfei != 0 && getpaytotal>0){
					var oripay = FloatSubtraction(getpaytotal, getyunfei);
					var newpay = FloatAdd(oripay, setyunprice);			
					var fornewpay = addCommas(newpay);
					$("#payyunfei").text(setyunprice);
					$("#yunfei").val(setyunprice)
					$("#paytotal").text(fornewpay);
					$("#tjine").val(newpay);
				}
			});
		/*2014-07-22*/
		$("#cartempty").click(function(){
			$.ajax({
				type: "POST",
				url:PDV_RP+"post.php",
				data: "act=setcookie&cookietype=empty&cookiename=SHOPCART",
				success: function(msg){
					if(msg=="OK"){
						window.location=PDV_RP+'shop/cart.php';
					}
				}
			});
		});

		$(".cartdel").click(function(){

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
		
			function doDEL(aa,bb,cc,dd){
			    $.ajax({
			    	type: "POST",
					url:PDV_RP+"post.php",
					data: "act=setcookie&cookietype=del&cookiename=SHOPCART&gid="+aa+"&picgid="+bb+"&fz="+cc,
					success: function(msg){
						if(msg=="OK"){
							$("#del_"+dd).remove();
						}
					}
			    })
			}
		
		$("#cartdelbtn").click(function(){

			
			var collection = $("input[name^='cartdel']");
			if( collection.length > 0 ){
				
			    var i = 0;
			    var fn = function(){
			        var element = $(collection[i]);
			        if(element.is(':checked')){
				        var gid=element.val();
						var fz=$("#fz_"+gid)[0].value;
						
						var getgid = gid.split("_");
						
						doDEL(getgid[0],getgid[2],fz,gid);
					}
			        
			        if( ++i < collection.length ){
			            setTimeout(fn, 100);
			    	}
			    };
			    fn();
			}
			
			
		});
		
		/*$(".cartacc").change(function(){
			if($(this)[0].value=="" || parseInt($(this)[0].value)<1 || isNaN($(this)[0].value) || Math.ceil($(this)[0].value)!=parseInt($(this)[0].value)){
				$(this)[0].value="1";
			}
		});*/
		

		
		/*2015-05-04*/
		$(".quantity-minus, .quantity-plus").click(function(){
			var gid=this.id.substr(8);
			var fz=$("#fz_"+gid)[0].value;
			var nums=$("#cartacc_"+gid)[0].value;
			var getgid = gid.split("_");
			
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
										$("#paytotal2").text(restr[6]);
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
									});
									
									//window.location=PDV_RP+'shop/cart.php';
								}else if(msg=="1000"){
									alert("訂購數量錯誤");
								}else{
									alert(msg);
									
								}
							}
						});

					}else if(msg=="1000"){
						alert("該商品已無庫存");
						window.location=PDV_RP+'shop/cart.php';
					}else{
						alert(msg);
					}
				}
			});

		});
			
		$(document).on("change",".cartacc",function(){
			var gid=this.id.substr(8);
			var fz=$("#fz_"+gid)[0].value;
			var nums=$("#cartacc_"+gid)[0].value;
			var getgid = gid.split("_");
			
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
									});
									
									//window.location=PDV_RP+'shop/cart.php';
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
		});
		
		$(".gostart").click(function(){
				
			//檢測商品促銷
			/*'autoDimensions':false(fancybox)*/
			var payid = $('input[name=payment]:checked').val();
			var promotype = $('#promotype').val();
			var promotypecode = $('#promotypecode').val();
			
			if(typeof payid == "undefined"){
				LoadMsg("請選擇付款方式");
				return false
			}

			//alert(payid);
			//alert(promotype);
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
				//alert(payid);
				if( payid == 2  ){
					if(getpaytotal >0){
						var promocode = $("#promocode2").val();
					}else{
						var promocode = this.id;
					}
				}else{
					var promocode = this.id;
					//alert(promocode);
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
		});

		$("#backtoshop").click(function(){
			window.location=PDV_RP+'shop/class/';
		});
});


//從訂購按鈕新增cookie
function addtocart(gid,nums,fz){
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
						data: "act=setcookie&cookietype=add&cookiename=SHOPCART&gid="+gid+"&nums="+nums+"&fz="+fz,
						success: function(msg){
							if(msg=="OK"){
								window.location=PDV_RP+'shop/cart.php';
							}else if(msg=="1000"){
								alert("訂購數量錯誤");
								
							}else{
								alert(msg);
							}
						}
					});

				}else if(msg=="1000"){
					alert("該商品已無庫存");
				}else{
					alert(msg);
				}
			}
		});
}



function addCommas(nStr)
{
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
	return x1 + x2;
}


//浮點數相加
function FloatAdd(arg1, arg2)
{
  var r1, r2, m;
  try { r1 = arg1.toString().split(".")[1].length; } catch (e) { r1 = 0; }
  try { r2 = arg2.toString().split(".")[1].length; } catch (e) { r2 = 0; }
  m = Math.pow(10, Math.max(r1, r2));
  return (FloatMul(arg1, m) + FloatMul(arg2, m)) / m;
}
//浮點數相減
function FloatSubtraction(arg1, arg2)
{
  var r1, r2, m, n;
  try { r1 = arg1.toString().split(".")[1].length } catch (e) { r1 = 0 }
  try { r2 = arg2.toString().split(".")[1].length } catch (e) { r2 = 0 }
  m = Math.pow(10, Math.max(r1, r2));
  n = (r1 >= r2) ? r1 : r2;
  return ((arg1 * m - arg2 * m) / m).toFixed(n);
}
//浮點數相乘
function FloatMul(arg1, arg2)
{
  var m = 0, s1 = arg1.toString(), s2 = arg2.toString();
  try { m += s1.split(".")[1].length; } catch (e) { }
  try { m += s2.split(".")[1].length; } catch (e) { }
  return Number(s1.replace(".", "")) * Number(s2.replace(".", "")) / Math.pow(10, m);
}
//浮點數相除
function FloatDiv(arg1, arg2)
{
  var t1 = 0, t2 = 0, r1, r2;
  try { t1 = arg1.toString().split(".")[1].length } catch (e) { }
  try { t2 = arg2.toString().split(".")[1].length } catch (e) { }
  with (Math)
  {
    r1 = Number(arg1.toString().replace(".", ""))
    r2 = Number(arg2.toString().replace(".", ""))
    return (r1 / r2) * pow(10, t2 - t1);
  }
}