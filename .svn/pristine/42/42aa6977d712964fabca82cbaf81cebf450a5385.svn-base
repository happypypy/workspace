$(function(){
	/*文字大小自适应*/
	var handlerOrientationChange = function () {
	    var width = window.innerWidth <= 320 ? 320 : window.innerWidth >= 640 ? 640 : window.innerWidth;
	    var fontSize = 100 * (width / 320);
	    document.documentElement.style.fontSize = fontSize + 'px';
	};
	window.onresize = handlerOrientationChange;
	setTimeout(function () {
	    handlerOrientationChange();
	}, 0);	

	$("#open-menu").on("click",function(event){
		event.stopPropagation();
		$("body").addClass("open-menu");
	})
	$(".menu-cover").on("click",function(event){
		if($("body").hasClass("open-menu")){
			event.stopPropagation();
			$("body").removeClass("open-menu");
		}
	})

	// fixFoot();



})

function fixFoot(){
	var ftLi=$(".footer>ul>li").length;
	var ftW=$(".footer").width();
	$(".footer>ul>li").width(ftW/ftLi);
}