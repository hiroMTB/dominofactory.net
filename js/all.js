function toggleFullscreen(){
	document.documentElement.webkitRequestFullScreen();

	if(window.innerHeight == screen.height){
		document.webkitCancelFullScreen();
	}else{
  		document.documentElement.webkitRequestFullScreen();
  	}
}

$(document).ready(
	function(){
		// set click fullscreen
		$(".toggleFullscreen").bind("click", function(){toggleFullscreen()});
	}
);
