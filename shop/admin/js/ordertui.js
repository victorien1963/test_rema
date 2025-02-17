
//配送
$(document).ready(function(){
	
	$(".dotui").click(function(){
		var itemid=this.id.substr(6);
		var nowstat=$("#dotui_"+itemid)[0].value;
		if(nowstat=="訂貨"){
				$.ajax({
					type: "POST",
					url:"post.php",
					data: "act=orderitemtuilist&itemid="+itemid,
					success: function(msg){
						if(msg=="OK"){
							$("#tuistat_"+itemid)[0].src="images/toolbar_ok.gif";
							$("#dotui_"+itemid)[0].value="退訂";
						}else if(msg=="1000"){
							alert("訂購商品不存在");
						}else if(msg=="1001"){
							alert("商品已發貨，不能進行訂貨");
						}else if(msg=="1002"){
							alert("訂單已完成，不能進行訂貨");
						}else if(msg=="1003"){
							alert("訂單已經完全退貨，不能進行單商品訂貨");
						}else if(msg=="1004"){
							alert("訂單不存在");
						}else if(msg=="1005"){
							$("#tuistat_"+itemid)[0].src="images/toolbar_ok.gif";
							$("#dotui_"+itemid)[0].value="退訂";
							var r=confirm("是否產生 POS訂貨單？");
							if (r==true)
						    {
						    	$.ajax({
									type: "POST",
									url:"post.php",
									data: "act=posdin&itemid="+itemid,
									success: function(msg){
										if(msg=="OK"){
											alert("訂貨單已產生！");
										}else{
											alert(msg);
										}
									}
								});
						    	
						    }
						}else{
							alert(msg);
						}
						return false;
					}
				});

		}else{
				$.ajax({
					type: "POST",
					url:"post.php",
					data: "act=orderitemtuilist&itemid="+itemid,
					success: function(msg){
						if(msg=="OK"){
							$("#tuistat_"+itemid)[0].src="images/toolbar_no.gif";
							$("#dotui_"+itemid)[0].value="訂貨";
							/*var r=confirm("是否產生 POS退貨單？");
							if (r==true)
						    {
						    	$.ajax({
									type: "POST",
									url:"post.php",
									data: "act=postui&itemid="+itemid,
									success: function(msg){
										if(msg=="OK"){
											alert("退貨單已產生！");
										}else{
											alert(msg);
										}
									}
								});
						    }*/
						}else if(msg=="1006"){
							alert("訂單尚未收款，請先操作付款確認！");
						}else if(msg=="1000"){
							alert("訂購商品不存在");
						}else if(msg=="1001"){
							alert("商品已發貨，不能進行退訂");
						}else if(msg=="1002"){
							alert("訂單已完成，不能進行退訂");
						}else if(msg=="1003"){
							alert("訂單已經完全退貨，不能進行單商品退貨");
						}else if(msg=="1004"){
							alert("訂單不存在");
						}else if(msg=="1005"){
							$("#tuistat_"+itemid)[0].src="images/toolbar_no.gif";
							$("#dotui_"+itemid)[0].value="訂貨";
						}else{
							alert(msg);
						}
						return false;
					}
				});
		
		}
	});
	
	$(".postui").click(function(){
		var orderid=this.id.substr(7);
			
			var r=confirm("是否產生 POS退貨單？");
			if (r==true)
		    {
		    	$.ajax({
					type: "POST",
					url:"post.php",
					data: "act=postui&orderid="+orderid,
					success: function(msg){
						if(msg=="OK"){
							alert("退貨單已產生！");
							 location.reload();
						}else{
							alert(msg);
						}
					}
				});
		    }
	});
	
	$(".posdin").click(function(){
		var orderid=this.id.substr(7);
			
			var r=confirm("是否產生 POS訂貨單？");
			if (r==true)
		    {
		    	$.ajax({
					type: "POST",
					url:"post.php",
					data: "act=posdin&orderid="+orderid,
					success: function(msg){
						if(msg=="OK"){
							alert("訂貨單已產生！");
						}else{
							alert(msg);
						}
					}
				});
		    }
	});


//列入退訂訂單
$(".dotuithis").click(function(){
		
		var answer = confirm("全退商品才列入退訂，確定列入退訂訂單?")
	if (answer){
		var orderid=this.id.substr(10);
		$.ajax({
			type: "POST",
			url:"post.php",
			data: "act=dotuithis&orderid="+orderid,
			success: function(msg){
				if(msg=="OK"){
					$("#dotuithis_"+orderid).remove();
					window.location.reload();
				}else if(msg=="1000"){
					alert("訂單不存在");
				}else if(msg=="1001"){
					alert("訂單已付款，不能退訂");
				}else if(msg=="1002"){
					alert("訂單已配送，不能退訂");
				}else{
					alert(msg);
				}
			}
		});
	}else{
		return false;
	}
});
//列入有效訂單
$(".doopenthis").click(function(){
		
		var answer = confirm("是否列入有效訂單?")
	if (answer){
		var orderid=this.id.substr(10);
		$.ajax({
			type: "POST",
			url:"post.php",
			data: "act=doopenthis&orderid="+orderid,
			success: function(msg){
				if(msg=="OK"){
					//$("#dotuithis_"+orderid).remove();
					window.location.reload();
				}else if(msg=="1000"){
					alert("發生錯誤");
				}else{
					alert(msg);
				}
			}
		});
	}else{
		return false;
	}
});

//取消退訂
$(".canceltui").click(function(){
		
		var answer = confirm("是否取消退訂?")
	if (answer){
		var itemid=this.id.substr(5);
		$.ajax({
			type: "POST",
			url:"post.php",
			data: "act=canceltui&itemid="+itemid,
			success: function(msg){
				if(msg=="OK"){
					window.location.reload();
				}else if(msg=="1000"){
					alert("訂單不存在");
				}else if(msg=="1001"){
					alert("訂單已付款，不能退訂");
				}else if(msg=="1002"){
					alert("訂單已配送，不能退訂");
				}else{
					alert(msg);
				}
			}
		});
	}else{
		return false;
	}
});

//取消換貨
$(".cancelchg").click(function(){
		
	var answer = confirm("是否取消退訂?")
if (answer){
	var itemid=this.id.substr(5);
	$.ajax({
		type: "POST",
		url:"post.php",
		data: "act=cancelchg&itemid="+itemid,
		success: function(msg){
			if(msg=="OK"){
				window.location.reload();
			}else if(msg=="1000"){
				alert("訂單不存在");
			}else if(msg=="1001"){
				alert("訂單已付款，不能退訂");
			}else if(msg=="1002"){
				alert("訂單已配送，不能退訂");
			}else{
				alert(msg);
			}
		}
	});
}else{
	return false;
}
});

//取消退訂
$(".dountui").click(function(){
		
		var answer = confirm("是否取消退訂註記?")
	if (answer){
		var orderid=this.id.substr(8);
		$.ajax({
			type: "POST",
			url:"post.php",
			data: "act=dountui&orderid="+orderid,
			success: function(msg){
				if(msg=="OK"){
					parent.location.reload();
					parent.$.unblockUI();
				}else if(msg=="1000"){
					alert("訂單不存在");
				}else if(msg=="1001"){
					alert("訂單中仍有退貨商品，不能取消註記");
				}else{
					alert(msg);
				}
			}
		});
	}else{
		return false;
	}
});

//取消換貨
$(".dounchg").click(function(){
		
	var answer = confirm("是否取消換貨註記?")
if (answer){
	var orderid=this.id.substr(8);
	$.ajax({
		type: "POST",
		url:"post.php",
		data: "act=dounchg&orderid="+orderid,
		success: function(msg){
			if(msg=="OK"){
				parent.location.reload();
				parent.$.unblockUI();
			}else if(msg=="1000"){
				alert("訂單不存在");
			}else if(msg=="1001"){
				alert("訂單中仍有換貨商品，不能取消換貨");
			}else{
				alert(msg);
			}
		}
	});
}else{
	return false;
}
});

});