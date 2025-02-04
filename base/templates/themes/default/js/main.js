//距離400px時出現To Top, Header加上陰影
/*   
$(window).scroll(function(){   
	
	var $window =  $(window).scrollTop();
  
	if($window > 300){
	  $('a.totop').fadeIn(300);  
		
	} else{
	  $('a.totop').fadeOut(300);  
	}
	  
})	
*/
$(function(){

	//To top effect  
	/* 
	$('a.totop').on('click',function(){
	  $('html, body').animate({scrollTop: 0}, 1500);
	  return false;
	}) 
	*/


	//表格RWD 收折效果
	$('.card-info.has-toggle .title').on('click',function(){

		var $this = $(this);
		var $card = $this.parents('.card-info');
		var $sib = $card.siblings('.card');

		$card.toggleClass('active');
		$sib.removeClass('active');

	})
})	