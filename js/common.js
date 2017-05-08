$(document).ready(function(){
	if ($(".flash-message").find('p').html() != null 
			&& 
		$(".flash-message").find('p').html().length != 0) {
        
        $(".flash-message").removeClass("hide");
        
        setTimeout(function() {
            $(".flash-message").fadeOut("slow")
        }, 5000)
    }
});