

//訂單查詢
$(document).ready(function(){

	

	/*$("img.paystat").each(function(){
		var oldsrc=$(this)[0].src;
		var imgname=oldsrc.substr((oldsrc.length-6),2);
		if(imgname=="ok"){
			$(this).css({cursor:"default"});
		}
	});

	$("img.paystat").mouseover(function(){
		var oldsrc=$(this)[0].src;
		var imgname=oldsrc.substr((oldsrc.length-6),2);
		if(imgname=="no"){
			$(this)[0].src=PDV_RP+"shop/templates/images/payit.gif";
			$(this).mouseout(function(){
				$(this)[0].src=oldsrc;
			});
			$(this).click(function(){
				var orderid=this.id.substr(8);
				window.location='orderpay.php?orderid='+orderid;
			});
		}
	});

	$("#showpay").attr("value",$("#nowshowpay")[0].value);
	$("#showyun").attr("value",$("#nowshowyun")[0].value);
	$("#showok").attr("value",$("#nowshowok")[0].value);

	if($("#key")[0].value==""){
		$("#key")[0].value="商品名稱/訂單號";
		$("#key").css({color:'#909090'});
		$("#key").click(function(){
			if($("#key")[0].value=="商品名稱/訂單號"){
				$("#key")[0].value="";
				$("#key").css({color:'#505050'});
			}
		});
	}

	$("#searchbutton").mouseover(function(){
		if($("#key")[0].value=="商品名稱/訂單號"){
			$("#key")[0].value="";
			$("#key").css({color:'#505050'});
		}
	});*/
	$(document).on('click', '#editAddressBtn', function() {
		$('#exampleModal').modal('show');
	});
	
	$(".delchk").click(function(){
		var getdelid = this.id.substr(7);
		//alert("刪除訂單編號"+getdelid);
		
		if(PDV_LAN == "en"){
			var cText = "Are You Sure You Want To Cancel?";
		}else if(PDV_LAN == "zh_cn"){
			var cText = "你确定要删除这笔订单？";
		}else{
			var cText = "你確定要刪除這筆訂單？";
		}
		
		if(confirm(cText))
		{
				$.ajax({
					type: "POST",
					url:PDV_RP+"shop/post.php",
					data: "act=ordertui&orderid="+getdelid,
					success: function(msg){
						if(msg=="OK"){
							alert("訂單已經取消！");
							location.reload();
						}else if(msg=="1000"){
							alert("訂單不存在");
							$.fancybox.close();
						}else if(msg=="1001"){
							alert("訂單已付款，不能取消");
							$.fancybox.close();
						}else if(msg=="1002"){
							alert("訂單已配送，不能取消");
							$.fancybox.close();
						}else if(msg=="1003"){
							alert("訂單已完成，不能取消");
							$.fancybox.close();
						}else if(msg=="1004"){
							alert("訂單中部分商品已配送，不能取消");
							$.fancybox.close();
						}else{
							alert(msg);
						}
					}
				});
			}
		
	});
	
	$(".repay").click(function(){
		var getorderid = this.id.substr(6);
		//alert("刪除訂單編號"+getdelid);
		window.location='orderpay.php?orderid='+getorderid;
	});

});

function order_data_set() {
	return {
	  showOrderListLayout: true,
	  orderList: orderList,
	  switchShowOrderDetailLayout(event,orderIndex) {
		event.stopPropagation();
		this.orderList[orderIndex].orderdata.showOrderDetailLayout =
		  !this.orderList[orderIndex].orderdata.showOrderDetailLayout;
		console.log(this.orderList[orderIndex].orderdata);
	  },
	  goReturnOrder(orderIndex) {
		this.orderList[orderIndex].orderdata.showOrderReturnLayout = true;
		this.orderList[orderIndex].orderdata.showOrderCHGLayout = false;
		this.showOrderListLayout = false;
	  },
	  cancelReturnOrder(orderIndex) {
		this.orderList[orderIndex].orderdata.showOrderReturnLayout = false;
		this.showOrderListLayout = true;
	  },
	  goCHGOrder(orderIndex) {
		this.orderList[orderIndex].orderdata.showOrderCHGLayout = true;
		this.orderList[orderIndex].orderdata.showOrderReturnLayout = false;
		this.showOrderListLayout = false;
	  },
	  cancelCHGOrder(orderIndex) {
		this.orderList[orderIndex].orderdata.showOrderCHGLayout = false;
		this.showOrderListLayout = true;
	  },
	  sendReturn(orderId) {
		$("#sendtuiform_" + orderId).ajaxSubmit({
		  url: PDV_RP + "shop/post.php",
		  success: function (msg) {
			if (msg == "OK") {
			  alert("申請完成");
			  window.location.reload();
			} else {
			  alert(msg);
			}
		  },
		});
	  },
	  sendCHG(orderId) {
		$("#sendchgform_" + orderId).ajaxSubmit({
		  url: PDV_RP + "shop/post.php",
		  success: function (msg) {
			if (msg == "OK") {
			  alert("申請換貨完成");
			  window.location.reload();
			} else {
			  alert(msg);
			}
		  },
		});
	  },
	  cancelEditContact(orderIndex) {
		this.orderList[orderIndex].orderdata.editContact = false;
		this.orderList[orderIndex].orderdata.editAddr =
		  this.orderList[orderIndex].orderdata.s_addr;
		this.orderList[orderIndex].orderdata.editName =
		  this.orderList[orderIndex].orderdata.s_name;
		this.orderList[orderIndex].orderdata.editMobi =
		  this.orderList[orderIndex].orderdata.s_mobi;
	  },
	  saveEditContact(orderIndex) {
		this.orderList[orderIndex].orderdata.s_addr =
		  this.orderList[orderIndex].orderdata.editAddr;
		this.orderList[orderIndex].orderdata.s_name =
		  this.orderList[orderIndex].orderdata.editName;
		this.orderList[orderIndex].orderdata.s_mobi =
		  this.orderList[orderIndex].orderdata.editMobi;
		this.orderList[orderIndex].orderdata.editContact = false;
	  },
	  testwh() {
		
		var testvalue = $('#whAddr').val();
		var editAddr = $('#addr').val().trim();
		var editName = $('#editName').val().trim();
		var editPhone = $('#editPhone').val().trim();
		var editTell = $('#editTell').val().trim();
		/*
		var faddr = $('#faddr').val().trim();
		var fname = $('#fname').val().trim();
		var fmobi = $('#fmobi').val().trim();
		*/
		if (editAddr !== "") {
			
		this.orderList[testvalue].orderdata.s_addr =editAddr;
		$('#faddr').val(editAddr);
		} 
		if (editName !== "") {
			
		this.orderList[testvalue].orderdata.s_name =editName;
		$('#fname').val(editName);
		} 
		if (editPhone !== "") {
			
		this.orderList[testvalue].orderdata.s_mobi =editPhone;
		$('#fmobi').val(editPhone);
		} 
		$('#exampleModal').modal('hide');
		//var faddr = $('#faddr').val().trim();
		//var fname = $('#fname').val().trim();
		//var fmobi = $('#fmobi').val().trim();
		
    	//alert('faddr: ' + faddr);
    	//alert('fname: ' + fname);
    	//alert('fmobi: ' + fmobi);
	  }
	};
  }
  
  function order_data_init() {}


  function editContactNew(button){
	let id = button.id;
    // 使用 split() 方法分割字串並取得數字部分
    let number = id.split('_')[1];
	$('#whAddr').val(number);
	var testvalue = $('#whAddr').val();
	$('#exampleModal').modal('show');
    //alert('whAddr: ' + testvalue);

  }

