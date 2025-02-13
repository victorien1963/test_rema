// JavaScript Document

$.fn.center = function () {
	// if ($(window).width() < 990) {
	//     this.css("position","absolute");
	// 	this.css("top", 50 + $(window).scrollTop() +"px");
	//     return this;
	// }else{
	// 	this.css("position","absolute");
	// 	this.css("top", 450 + $(window).scrollTop() +"px");
	//     return this;
	// }
  }

/*會員登入*/
function LoadPopup_MemberLogin(){
	
	// $(".close-nav").trigger("click",CloseNav);
	
	$.ajax({
			type: "POST",
			url:PDV_RP+"member/post.php",
			data: "act=getpoploginform&RP="+PDV_RP,
			success: function(msg){
				//已登入
				
				if(msg == "islogin"){
					window.location=PDV_RP+"member/";
					return false; 
				} else {
					// 新版未登入
					window.location=PDV_RP+"member/login.php";
					return false; 
				}
				
				$("#popupwindow-in").empty();
				$("#popupwindow-in").append(msg);
				
				var bg = $("#popupwindow-in .background-black,#popupwindow-in .window-con");
				
				$(".window-con").center();
				bg.fadeIn();
				$('html, body,.page').addClass('overflow-hidden');
				$(".close-window-icon, .background-black").on("click", function(){
					bg.fadeOut(400, ()=>{
						bg.parent().remove();
					});
					$('html, body,.page').removeClass('overflow-hidden');
				});
				
				$('#loss,#loss2').click(function() { 
					LoadPopup_MemberResetpass();
				}); 

				$('#zhuce,#zhuce2,.zhuce').click(function() { 
					LoadPopup_MemberReg();
				});
				
				$('#LoginForm').submit(function(){ 
					$('#LoginForm').ajaxSubmit({
						url: PDV_RP+'post.php',
						success: function(msg) {
							if(msg=="OK" || msg.substr(0,2)=="OK"){
								bg.fadeOut(400, ()=>{
									bg.parent().remove();
								});
								$('html, body,.page').removeClass('overflow-hidden');
								window.location.reload();
							}else{
								alert(msg);
							}
						}
					}); 
					return false; 
			 	});
				
			}
		});
}

/*會員註冊*/
function LoadPopup_MemberReg(){
	$.getScript(PDV_RP+"member/js/zone.js");
	$.getScript(PDV_TMRP+"js/InputStyle.js");
	
	$.ajax({
			type: "POST",
			url:PDV_RP+"member/post.php",
			data: "act=getpopregform&RP="+PDV_RP,
			success: function(msg){
				//已登入
				if(msg == "islogin"){
					window.location=PDV_RP+"member/";
					return false; 
				}
				
				$("#popupwindow-in").empty();
				$("#popupwindow-in").append(msg);
				
				var bg = $("#popupwindow-in .background-black,#popupwindow-in .window-con");
				
				$(".window-con").center();
				
				bg.fadeIn();
				$('html, body,.page').addClass('overflow-hidden');
				$(".close-window-icon, .background-black").on("click", function(){
					bg.fadeOut(400, ()=>{
						bg.parent().remove();
					});
					$('html, body,.page').removeClass('overflow-hidden');
				});
				
				$('#selcountry').change(function(){ 
		$("#addr").val("");
		$("#postcode").val("");
		$("#zoneid").html("");
		var p = this.value.split("_");
		
		//$("#mov").val(p[2]);
		$("#tel").val(p[2]);
		
			$.ajax({
					type: "POST",
					url: PDV_RP+"member/post.php",
					data: "act=getzonelist&pid="+p[1],
					success: function(msg){
						pList.data = new Array();
						$("#zonelist").html(msg);
						if(PDV_LAN == "en"){
							$("#Province").html("<option value='s'> Please Select</option>"+pList.getOptionString('s'));
						}else if(PDV_LAN == "zh_cn"){
							$("#Province").html("<option value='s'> 请选择</option>"+pList.getOptionString('s'));
						}else{
							$("#Province").html("<option value='s'> 請選擇</option>"+pList.getOptionString('s'));
						}
					}
				
			 });
	}); 
				
				
					$('#email').blur(function(){ 
		var p=$("#email")[0].value;
		
			$.ajax({
					type: "POST",
					url: PDV_RP+"member/post.php",
					data: "act=checkmail&email="+p,
					success: function(msg){
						if(msg=="YES"){
							$('#chkEmail').remove();
							if(PDV_LAN == "en"){
								$('#email').after('<span id="chkEmail" class="label label-info">No one has applied, it is available.</span>');
							}else if(PDV_LAN == "zh_cn"){
								$('#email').after('<span id="chkEmail" class="label label-info">无人申请，可以使用</span>');
							}else{
								$('#email').after('<span id="chkEmail" class="label label-info">無人申請，可以使用</span>');
							}
						}else if(msg=="NO"){
							$('#chkEmail').remove();
							if(p == ""){
								if(PDV_LAN == "en"){
									$('#email').after('<span id="chkEmail" class="label label-danger">Please enter the email.</span>');
								}else if(PDV_LAN == "zh_cn"){
									$('#email').after('<span id="chkEmail" class="label label-danger">请填写电子邮件</span>');
								}else{
									$('#email').after('<span id="chkEmail" class="label label-danger">請填寫電子郵件</span>');
								}
								
							}else{
								if(PDV_LAN == "en"){
									$('#email').after('<span id="chkEmail" class="label label-danger">The e-mail has been registered, please try another one.</span>');
									//alert("The e-mail has already been registered, please replace one.");
								}else if(PDV_LAN == "zh_cn"){
									$('#email').after('<span id="chkEmail" class="label label-danger">该电子邮件已经注册过，请更换一个</span>');
									//alert("该电子邮件已经注册过，请更换一个");
								}else{
									$('#email').after('<span id="chkEmail" class="label label-danger">該電子郵件已經註冊過，請更換一個</span>');
									//alert("該電子郵件已經註冊過，請更換一個");
								}
								
							}
						}else{
							$('#chkEmail').remove();
							$('#email').after(msg);
						}
					}
				
			 });
			});
				$(".memberbn").on("click", ()=>{
					LoadPopup_MemberLogin("Signin");
				})
				
					$('#memberReg').submit(function(){ 
						$('#memberReg').ajaxSubmit({
							url: PDV_RP+'post.php',
							success: function(msg) {
								switch(msg){									
									case "OK":
										if($("#nextstep")[0].value=="enter"){
											window.location='index.php';
										}else{
											window.location='index.php';
										}
									break;
									case "OK_NOMAIL":
										if($("#nextstep")[0].value=="enter"){
											window.location='index.php';
										}else{
											window.location='index.php';
										}
									break;
									case "CHECK":
										alert("會員註冊成功！您註冊的會員類型需要審核後才能登入，感謝您的註冊");
									break;
									default :
										alert(msg);
									break;
								}
								
							}
						}); 
				       return false; 
				   	});
			 		
			}
		});
}

/*忘記密碼*/
function LoadPopup_MemberResetpass(){
	$.ajax({
			type: "POST",
			url:PDV_RP+"member/post.php",
			data: "act=getpopresetpassform&RP="+PDV_RP,
			success: function(msg){
				//已登入
				if(msg == "islogin"){
					window.location=PDV_RP+"member/";
					return false; 
				}
				
				$("#popupwindow-in").empty();
				$("#popupwindow-in").append(msg);
				
				var bg = $("#popupwindow-in .background-black,#popupwindow-in .window-con");
				
				$(".window-con").center();
				bg.fadeIn();
				$('html, body,.page').addClass('overflow-hidden');
				$(".close-window-icon, .background-black").on("click", function(){
					bg.fadeOut(400, ()=>{
						bg.parent().remove();
					});
					$('html, body,.page').removeClass('overflow-hidden');
				});
				
				$('#ForgotForm').submit(function(){ 
					$('#ForgotForm').ajaxSubmit({
						url: PDV_RP+'member/post.php',
						success: function(msg) {
							if(msg=="OK" || msg.substr(0,2)=="OK"){
								bg.fadeOut(400, ()=>{
									bg.parent().remove();
								});
								$('html, body,.page').removeClass('overflow-hidden');
								window.location.reload();
							}else{
								alert(msg);
							}
						}
					}); 
					return false; 
			 	});
			 		
			}
		});
}

function LoadInPagePopup(contentid, divid){
	var ndata = $("#"+contentid).html();
	// $("#"+contentid).html("");
	var data = '<section id="'+divid+'"><div class="background-black" style="display: none;"></div>'+ndata+'</section>';
	$("#popupwindow-in").empty();
	$("#popupwindow-in").append(data);
	
	var bg = $("#popupwindow-in .background-black,#popupwindow-in .window-con");
				
				$(".window-con").center();
	bg.fadeIn("fast").css("opacity",1);
	$('html, body,.page').addClass('overflow-hidden');

	$(".close-window-icon, .background-black").on("click", function(){
		bg.fadeOut(400, ()=>{
			//bg.parent().remove();
			$("#"+divid).remove();
		});
		$('html, body,.page').removeClass('overflow-hidden');
		$("#"+contentid).html(ndata);
	});
	
	$(document).on("submit", "#memberAddrModify", function () {
		
		var SG_addr = $("#saddr").val();
		var SG_name = $("#sname").val();
		var SG_mov = $("#smov").val();
		
		if(SG_addr==""){
			alert("請填寫地址");
			return false;
		}
		if(SG_name==""){
			alert("請填寫姓名");
			return false;
		}
		if(SG_mov==""){
			alert("請填寫手機號碼");
			return false;
		}

		$('#memberAddrModify').ajaxSubmit({
			url: PDV_RP+"member/post.php",
			success: function(msg) {
				$("#"+contentid).html(ndata);
				if(msg=="OK"){
					if(PDV_LAN == "en"){
						LoadPopup("ModifyOk");
						$("#seladdr").trigger('change');
					}else if(PDV_LAN == "zh_cn"){
						LoadPopup("ModifyOk");
						$("#seladdr").trigger('change');
					}else{
						LoadPopup("ModifyOk");
						$("#seladdr").trigger('change');
					}
				}else{
					alert(msg);
				}
			}
		}); 
       return false; 
   }); 
	
	$(document).on("submit", "#AddAddr", function () {
		
		var G_addr = $("#addr").val();
		var G_name = $("#name").val();
		var G_mov = $("#amov").val();
		
		if(G_addr==""){
			alert("請填寫地址");
			return false;
		}
		if(G_name==""){
			alert("請填寫姓名");
			return false;
		}
		if(G_mov==""){
			alert("請填寫手機號碼");
			return false;
		}
		
		$('#AddAddr').ajaxSubmit({
			url: PDV_RP+"member/post.php",
			success: function(msg) {
				$("#"+contentid).html(ndata);
				if(msg=="OK"){
					if(PDV_LAN == "en"){
						LoadMsg("Modify Successful!");
						sleep(2000).then(() => {
                            window.location.reload();
                        })
					}else if(PDV_LAN == "zh_cn"){
						LoadMsg("信息修改成功！");
						sleep(2000).then(() => {
                            window.location.reload();
                        })
					}else{
						LoadMsg("資料修改成功！");
						sleep(2000).then(() => {
                            window.location.reload();
                        })
					}
				}else if(msg=="OKADD"){
					if(PDV_LAN == "en"){
						LoadMsg("Add Successful!");
						sleep(2000).then(() => {
                            window.location.reload();
                        })
					}else if(PDV_LAN == "zh_cn"){
						LoadMsg("通讯录新增成功！");
						sleep(2000).then(() => {
                            window.location.reload();
                        })
					}else{
						LoadMsg("通訊錄新增成功！");
						sleep(2000).then(() => {
                            window.location.reload();
                        })
						// window.location.reload();
					}
					if(REF == ""){
						//setTimeout('window.location=PDV_RP+\'member/member_contact.php\';',500);
					}
				}else{
					LoadMsg(msg);
				}
			}
		}); 
       return false; 
   });
	
}

function LoadPopup(fileName,getid){
	var rnd = Date.now();
	
	$.ajax({
		url: PDV_TMRP + fileName + ".html?"+rnd,
	})
	.done((data)=>{
		
		fix1=data.replace(/{#GETID#}/g,getid);
		fix2=fix1.replace(/{#TM#}/g,PDV_TMRP);
		newdata=fix2.replace(/{#RP#}/g,PDV_RP);
		
		$("#popupwindow-in").empty();
		$("#popupwindow-in").append(newdata);
		
		var bg = $("#popupwindow-in .background-black,#popupwindow-in .window-con");
				
				$(".window-con").center();
		bg.fadeIn();
		$('html, body,.page').addClass('overflow-hidden');
		$(".close-window-icon, .background-black").on("click", function(){
			
			bg.fadeOut(400, ()=>{
				bg.parent().remove();
			});
			$('html, body,.page').removeClass('overflow-hidden');

		});
	});
}

function LoadMsg(getmsg){
	var data = '<section id="popmsg"><div class="background-black" style="display: none;"></div><div class="window-con"><div class="window-block"><div class="window-center-word window-word-break font-w-l">'+getmsg+'</div><div class="popupu-bn-con poputwindow-bn"><a class="login-bn new-connection-bn close-window-icon">關閉</a></div></div></div></div></section>';
	
	$("#popupwindow-in").empty();
	$("#popupwindow-in").append(data);
	
	var bg = $("#popupwindow-in .background-black,#popupwindow-in .window-con");
				
				$(".window-con").center();
	bg.fadeIn();
	$('html, body,.page').addClass('overflow-hidden');
	$(".close-window-icon, .background-black").on("click", function(){
		bg.fadeOut(400, ()=>{
			$("#popmsg").remove();
		});
		$('html, body,.page').removeClass('overflow-hidden');
	});
}
function LoadMsgToUrl(getmsg, url){
	var data = '<section id="popmsg"><div class="background-black" style="display: none;"></div><div class="window-con"><div class="window-block"><div class="window-center-word window-word-break font-w-l">'+getmsg+'</div><div class="popupu-bn-con poputwindow-bn"><a class="login-bn new-connection-bn close-window-icon">確定</a></div></div></div></div></section>';
	
	$("#popupwindow-in").empty();
	$("#popupwindow-in").append(data);
	
	var bg = $("#popupwindow-in .background-black,#popupwindow-in .window-con");
				
				$(".window-con").center();
	bg.fadeIn();
	$('html, body,.page').addClass('overflow-hidden');
	$(".close-window-icon, .background-black").on("click", function(){
		bg.fadeOut(400, ()=>{
			$("#popmsg").remove();
		});
		$('html, body,.page').removeClass('overflow-hidden');
		window.location=url;
	});
}

// 20221212 Add
function sleep (time) {
  return new Promise((resolve) => setTimeout(resolve, time));
}

$(function(){ 
	$(".background-black").bind("click",function(e){ 
	var target = $(e.target); 
	if(target.closest(".window-con").length == 0){ 
	$(".background-black").hide(); 
	$('html, body,.page').removeClass('overflow-hidden');
	} 
	}) 
});

$(function(){
	$(".member").on("click", ()=>{
		LoadPopup_MemberLogin("Signin");
	})
});

$(function(){
	$(".signupbn").on("click", ()=>{
		LoadPopup_MemberReg("Signup");
	})
});

$(function(){
	$(".connection-bn").on("click", ()=>{
		LoadInPagePopup("add-adress","showaddadress");
	})
});

$(function(){
	$(".refund-bn").on("click", ()=>{
		LoadInPagePopup("refund","showrefund");
	})
});

$(function(){
	$(".refund-completed-bn").on("click", ()=>{
		LoadPopup("RefundCompleted");
	})
});

$(function(){
	$(".address-member-edit").on("click", ()=>{
		LoadInPagePopup("edit-adress","showaddadress");
	})
});

$(function(){
	$(".invioce-bn-con").on("click",function(){
		var obj = this.id;
		var getid= obj.substr(7);
		LoadPopup("Invoice",getid);
	})
});

$(function(){
	$(".country-select-bn").on("click", ()=>{
		LoadInPagePopup("SelectCountry","select-country");
	})
});

$(function(){
	$(".coupon-bn").on("click", ()=>{
		LoadPopup("Coupon");
	})
});

$(function(){
	$(".member-change").on("click", ()=>{
		LoadPopup("MemberComplete");
	})
});

$(function(){
	$('#loss,#loss2').click(function() { 
		LoadPopup_MemberResetpass();
	}); 

	$('#zhuce,#zhuce2,.zhuce').click(function() { 
		LoadPopup_MemberReg();
	});

	
});