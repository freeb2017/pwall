$(document).ready( function () {
	$('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": true,
      "ordering": true,
      "info": false,
      "autoWidth": false
    });
    $(".friend-request-make").on("click",function(event){
    	event.preventDefault();

    	var user_id = $(this).attr("data-uid");
    	var self = $(this);

    	var url = "/ajax/make_friend?user_id=" + user_id;

    	$.ajax({url: url, dataType: "json",
    		success: function(result){
    			self.closest("tr").fadeOut("slow");
		    	$('.flash-message').html('Friend Request has been sent successfully!').removeClass("hide");
		        setTimeout(function() {
		            $(".flash-message").addClass("hide").html('');
		        }, 5000);
        	},
        	error: function(){
        		$('.flash-message').html('Something went wrong!').removeClass("hide callout-success").addClass("callout-danger");
		        setTimeout(function() {
		            $(".flash-message").addClass("callout-success hide").removeClass("callout-danger").html('');
		        }, 5000);
        	}
        });
    });

    $(".friend-request").on("click",function(event){
    	event.preventDefault();

    	var user_one_id = $(this).parent().attr("data-u1");
    	var user_two_id = $(this).parent().attr("data-u2");
    	var user_id = $(this).attr("data-uid");
    	var status = $(this).attr("data-status");
    	var self = $(this);
    	var url = "/ajax/manage_friendship?user1=" 
    		+ user_one_id + "&user2=" + user_two_id 
    		+ "&user=" + user_id + "&status=" + status;

    	$.ajax({url: url, dataType: "json",
    		success: function(result){
    			
    			if(status == 'DECLINED'){
    				self.text("Accept");
    				self.attr("data-status","ACCEPTED");
    				self.removeClass("text-danger").addClass("text-success");
    			} else {
    				self.attr("data-status","DECLINED");
    				self.text("Decline");
    				self.removeClass("text-success").addClass("text-danger");
    			}

    			if(self.parent().find("a").length > 1)
    				self.closest("a").remove();
    			
		    	$('.flash-message')
		    		.html('Request has been processed successfully!')
		    		.removeClass("hide");
        	},
        	error: function(){
        		$('.flash-message')
        			.html('Something went wrong!')
        			.removeClass("hide callout-success")
        			.addClass("callout-danger");	        
        	},
        	complete: function(){
        		setTimeout(function() {
		            $(".flash-message")
		            	.addClass("callout-success hide")
		            	.removeClass("callout-danger")
		            	.html('');
		        }, 5000);
        	}
        });
    });
});