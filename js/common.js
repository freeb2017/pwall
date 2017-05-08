$(document).ready(function(){
	if ($(".flash-message").find('p').html() != null 
			&& 
		$(".flash-message").find('p').html().length != 0) {
        
        $(".flash-message").removeClass("hide");
        
        setTimeout(function() {
            $(".flash-message").fadeOut("slow")
        }, 5000)
    }

    


          	$('.rb-rating').rating({
                'showCaption': true,
                'stars': '5',
                'min': '0',
                'max': '4',
                'step': '1',
                'size': 'xs',
                'starCaptions': {
                	0: 'Not Good', 
                	1: 'Beginner', 
                	2: 'Intermediate', 
                	3: 'Very Good',
                	4: 'Advanced'
                }
            });

});