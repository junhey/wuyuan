var WebServiceURL = "http://junhey.com/app/phonegap_bysj/index.php/webservices/";
var ServiceURL="http://junhey.com/app/phonegap_bysj/";

$(function($){
	$(".refresh").click(function(){
		history.go(0);
	});
	$(".refresh_right").click(function(){
		history.go(0);
	});
	$(".back").click(function(){
		history.go(-1);
	});
	var titObj=$(".c_tit ul li");
	var contObj=$(".c_cont ul li");
	$(".c_tit ul li:first").css("background-color","#30ad92");
	$(titObj).each(function(index,element){
		$(this).on("click",function(){
			$(this).css("background-color","#30ad92").siblings().css("background-color","#c6c6c6");
			$(contObj).eq(index).show().siblings().hide();
		});
	});
	//var height=$(window).height();//�������ǰ���ڵĸ߶�
	//var width=$(window).width();//�������ǰ���ڵĸ߶� 					
	
	//alert($(document.body).height());//�������ǰ�����ĵ�body�ĸ߶�
	//alert($(document.body).width());//�������ǰ�����ĵ�body�ĸ߶�  
});