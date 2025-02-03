// JavaScript Document

var mobile_mode;

var onMobileModeChangedList = [];

function OnMobileModeChanged(func) {
	onMobileModeChangedList.push(func);
}

function SlideIn(obj, callback) {
	obj.show();
	let val = 0;
	if(window.innerWidth > 1000) {
		val = '-150px';
	}
	obj.animate({ left: val }, 400, 'swing', callback);
}

function SlideOut(obj, callback) {
	obj.animate({ left: '100%' }, 400, 'swing', function () {
		obj.hide();

		if (callback) callback();
	});
}

function MobileSlideIn(dropdown, close, onSlideIn, onSlideOut) {
	var originClick = $._data(close.get(0)).events['click'][0].handler;
	SlideIn(dropdown, function () {
		if (onSlideIn) onSlideIn();

		// Close按鈕的事件複寫

		close.off().click(function () {
			if (onSlideOut) onSlideOut();

			SlideOut(dropdown, function () {
				close.off().click(originClick); // 修改回原本事件
			});
		});
	});
}

function CloseNav() {
	SlideOut($('.nav'));
}

function CheckMobileMode() {
	return $(window).width() <= 1200 ? true : false;
}

function InitNav() {
	var $dropdown_nav = $('.dropdown-nav');

	// 清除樣式及事件

	$dropdown_nav.off();

	$('.dropdown-nav div').off();

	$('.mobile-disable').off();

	$('.dropdown-nav .dropdown').each(function () {
		$(this).removeAttr('style');
	});

	$('.nav').removeAttr('style');

	// 依據模式做初始化

	if (mobile_mode) {
		// 取消子結點的點擊事件傳播

		$('.dropdown-nav div').click((event) => {
			event.stopPropagation();
		});

		// 取消連結

		$('.mobile-disable').on('click', (event) => {
			return false;
		});

		// 滑動選單的css初始化

		$dropdown_nav.children('.dropdown').css('left', '100%');

		$('.nav').css('left', '100%');

		// 註冊點擊事件

		$dropdown_nav.each((index, item) => {
			var dropdown_click = () => {
				var $dropdown = $(item).children('.dropdown');

				// 依據是否為頂置選單位置的按鈕做不同處理

				if ($(item).hasClass('header-icon')) {
					/*
					$('.dropdown.small .bi-x-lg').on('click', function() {
						// MobileSlideIn($dropdown, $(item));
						SlideOut($dropdown);
					});
					MobileSlideIn($dropdown, $(item));
					*/
					window.location=PDV_RP+'shop/cart.php';
				} else {
					MobileSlideIn(
						$dropdown,
						$('.close-nav'),
						() => {
							$('.category-name').text($(item).children('.category').text());
						},
						() => {
							$('.series-dropdown .item-con').slideUp();

							$('.category-name').html('');

							$(function () {
								$('.mobile-top-title .memberbn').on('click', () => {
									LoadPopup('Signin');
								});
							});
						}
					);
				}
			};

			$(item)
				.on('click', dropdown_click)

				.find('>a:first')
				.on('click', dropdown_click);
		});

		$('.series-dropdown').each((index, item) => {
			var dropdown_click = () => {
				$('.series-dropdown .item-con').slideUp();

				$('.mobile-item-icon').removeClass('icon-minus');

				$('.mobile-item-icon').addClass('icon-plus');

				$item_con = $(item).children('.item-con');

				$icon_minus = $(item).find('.mobile-item-icon');

				if ($item_con.css('display') == 'none') {
					$item_con.slideDown();

					$icon_minus.removeClass('icon-plus');

					$icon_minus.addClass('icon-minus');
				}
			};

			$(item)
				.on('click', dropdown_click)

				.find('>a:first')
				.on('click', dropdown_click);
		});

		re_select_nav();
	} else {

		/*
		$dropdown_nav.mouseover(function () {
			$(this).children('.dropdown').show();
			$(this).find('.category').addClass('active');
		});

		$dropdown_nav.mouseleave(function () {
			$(this).children('.dropdown').hide();
			$(this).find('.category').removeClass('active');
		});
		*/
		$dropdown_nav.click(function () {
			//$(this).children('.dropdown').toggle();
			window.location=PDV_RP+'shop/cart.php';
		});
		

		/*$(function(){ 

			$('html, body,.page').bind("click",function(e){ 

			var target = $(e.target); 

			if(target.closest(".dropdown").length == 0){ 

				$(".dropdown").hide(); 

			} 

			}) 

		});*/

		
	}
}
// nav反白============================
nav_white();
$(window).scroll(function () {
	nav_white();
});
function nav_white() {
	let scrollTop = $(this).scrollTop();
	let navbar = $('#header .head');

	if(location.pathname == '/')
		if (scrollTop > 80) {
			navbar.addClass('show white');
		} else {
			navbar.removeClass('show white');
		}
}

$(document).ready(function() {
	if(location.pathname != '/'){
		$("#header .head").addClass('show white');
		$('#content').attr('id', 'content1');
	}
});

/*function shoppingCatnum(){

	var shoppingNum = $('.cart-con').children().length;

	$(".shoppingcart-number").html(shoppingNum);

}

function add_cart_num(){

	$(".add-cart").click(function(){

		shoppingCatnum();

	});

}*/

function re_select_nav() {
	$('.mobile-select').click(function () {
		$('.dropdown-nav').children('.dropdown').css('left', '100%');

		$('.nav').css('left', '100%');

		$('.category-name').html('');
	});
}

$(document).ready(function () {
	// 輪播系統

	{
		var marquee = $('.marquee-con');

		marquee.owlCarousel({
			items: 1,

			margin: 10,

			dots: false,

			// nav: true,

			loop: true,

			URLhashListener: false,

			autoplay: true,

			autoplayTimeout: 4000,

			autoplayHoverPause: true,

			smartSpeed: 1000,
		});

		$('.play').on('click', function () {
			owl.trigger('play.owl.autoplay', [3000]);
		});

		$('.stop').on('click', function () {
			owl.trigger('stop.owl.autoplay');
		});
	}

	// 導覽介面初始化

	{
		mobile_mode = CheckMobileMode();

		InitNav();

		OnMobileModeChanged(InitNav);

		$('.search .dropdown').click(function (event) {
			event.stopPropagation();
		});

		$('.icon-search-light').click(function (event) {
			event.stopPropagation();
			$('.search .dropdown').toggle();
			if ($('.search .dropdown').is(':visible')) {
				$('#header .head').addClass('white');
			} else {
				if (!$('#header .head').hasClass('show')) {
					$('#header .head').removeClass('white');
				}
			}
		});

		$('.menu-btn').click(function () {
			var $nav = $('.nav');

			$nav.show();

			$nav.animate({ left: '0%' });
		});

		$('.close-nav').click(CloseNav);
	}

	//手機・PC模式切換事件

	$(window).on('resize', function () {
		// 模式不變

		if (mobile_mode == CheckMobileMode()) return;

		// 模式改變，執行相關處理

		mobile_mode = !mobile_mode;

		onMobileModeChangedList.forEach((f) => f());
	});

	//shoppingCatnum();

	$(window).bind('beforeunload', function () {
		$('body').loading();
	});
});
