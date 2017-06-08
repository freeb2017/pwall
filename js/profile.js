$(document).ready( function () {
    $("#forgetForm").validate({
        rules: {
            password: {
                required: true,
                minlength: 5
            },
            confirm_password: {
                required: true,
                minlength: 5,
                equalTo: "#password"
            },
            email: {
                required: true,
                email: true
            },
            agree: "required"
        },
        messages: {
            password: {
                required: "Please provide a password",
                minlength: "Your password must be at least 5 characters long"
            },
            confirm_password: {
                required: "Please provide a password",
                minlength: "Your password must be at least 5 characters long",
                equalTo: "Please enter the same password as above"
            },
            email: "Please enter a valid email address"
        },
        errorElement: "em",
        errorPlacement: function ( error, element ) {
            // Add the `help-block` class to the error element
            error.addClass( "help-block" );

            if ( element.prop( "type" ) === "checkbox" ) {
                error.insertAfter( element.parent( "label" ) );
            } else {
                error.insertAfter( element );
            }
        },
        highlight: function ( element, errorClass, validClass ) {
            $( element ).parents( ".col-sm-5" ).addClass( "has-error" ).removeClass( "has-success" );
        },
        unhighlight: function (element, errorClass, validClass) {
            $( element ).parents( ".col-sm-5" ).addClass( "has-success" ).removeClass( "has-error" );
        }
    } );
	$('#suggested_tbl').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": true,
      "ordering": true,
      "info": false,
      "autoWidth": false
    });
	$(".skills").select2();
	$("#suggestskills").select2();
	$('#suggestskills').on('select2:select', function (evt) {
  		var skills = $(this).val();
  		var user_id = $(".suggestskills").attr("data-uid");
    	var url = "/ajax/manage_suggestions?user_id=" 
    		+ user_id + "&quality_id=" + skills;

    	$.ajax({url: url, dataType: "json",
    		success: function(result){
    			console.log(result);
    			$('.flash-message')
        			.html('Qualities have been suggested successfully!')
        			.removeClass("hide")
        			.addClass("callout-success");
        	},
        	error: function(){},
        	complete: function(){
        		setTimeout(function() {
		            $(".flash-message")
		            	.addClass("callout-success hide")
		            	.html('');
		        }, 1000);
        	}
        });
	});
	$('#suggestskills').on('select2:unselect', function (evt) {
		var skills = "";
		if($(this).val())
  			skills = $(this).val().join(",");
  		console.log(skills);
  		var user_id = $(".suggestskills").attr("data-uid");
    	var url = "/ajax/manage_suggestions?user_id=" 
    		+ user_id + "&quality_id=" + skills;

    	$.ajax({url: url, dataType: "json",
    		success: function(result){
    			$('.flash-message')
        			.html('Qualities have been removed successfully!')
        			.removeClass("hide callout-danger")
        			.addClass("callout-success");
        	},
        	error: function(){
    			$('.flash-message')
        			.html('Qualities can not be removed!')
        			.removeClass("hide callout-success")
        			.addClass("callout-danger");
        	},
        	complete: function(){
        		setTimeout(function() {
		            $(".flash-message")
		            	.addClass("callout-success hide")
		            	.html('');
		        }, 1000);
        	}
        });
	});
	$(".friend-suggest").on("click",function(event){
    	event.preventDefault();

    	var qid = $(this).attr("data-qid");
    	var is_active = $(this).attr("data-active");
    	var self = $(this);

    	var url = "/ajax/suggest_status?quality_id=" + qid + "&is_active=" + is_active;

    	$.ajax({url: url, dataType: "json",
    		success: function(result){
    			self.parent().addClass("text-success").text("Accepted by you");
		    	$('.flash-message').html('Suggested Quality has been accepted successfully!').removeClass("hide");
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
	$("#profileForm").validate({
		rules: {
			username: "required",
			username: {
				required: true,
				minlength: 4
			},
			phone: {
				minlength: 10,
				maxlength: 13
			},
			dob: "required"
		},
		messages: {
			username: {
				required: "Please enter a fullname",
				minlength: "Your username must consist of at least 4 characters"
			},
			phone: "Please enter a valid phone number between 10 to 13 characters"
		},
		errorElement: "em",
		errorPlacement: function ( error, element ) {
			// Add the `help-block` class to the error element
			error.addClass( "help-block" );

			if ( element.prop( "type" ) === "checkbox" ) {
				error.insertAfter( element.parent( "label" ) );
			} else {
				error.insertAfter( element );
			}
		},
		highlight: function ( element, errorClass, validClass ) {
			$( element ).parents( ".col-sm-5" ).addClass( "has-error" ).removeClass( "has-success" );
		},
		unhighlight: function (element, errorClass, validClass) {
			$( element ).parents( ".col-sm-5" ).addClass( "has-success" ).removeClass( "has-error" );
		}
	});

    $('.rate-qualities').on('rating.change', function(event, value, caption) {
    	console.log(value);
    	console.log(caption);

    	var qid = $(this).attr("data-qid");
    	var uid = $(this).attr("data-uid");
    	var rating = $(this).val();
    	var self = $(this);
    	var url = "/ajax/rate_quality?qid=" 
    		+ qid + "&uid=" + uid 
    		+ "&rating=" + rating;

    	self.parents(".rate-me-widget").find(".overlay").removeClass("hide");
    	$.ajax({url: url, dataType: "json",
    		success: function(result){
    			self.parents(".rate-me-widget").find(".widget-user-desc").text(result.info.count);
        	},
        	error: function(){
        		// $('.flash-message')
        		// 	.html('Something went wrong!')
        		// 	.removeClass("hide callout-success")
        		// 	.addClass("callout-danger");        
        	},
        	complete: function(){
        		setTimeout(function() {
        			self.parents(".rate-me-widget").find(".overlay").addClass("hide");
		            $(".flash-message")
		            	.addClass("callout-success hide")
		            	.removeClass("callout-danger")
		            	.html('');
		        }, 1000);
        	}
        });
    });

    $('.rate-qualities').on('rating.clear', function(event) {

    	var qid = $(this).attr("data-qid");
    	var uid = $(this).attr("data-uid");
    	var rating = 0;
    	var self = $(this);
    	var url = "/ajax/rate_quality?qid=" 
    		+ qid + "&uid=" + uid 
    		+ "&rating=" + rating;

    	self.parents(".rate-me-widget").find(".overlay").removeClass("hide");
    	$.ajax({url: url, dataType: "json",
    		success: function(result){
    			self.parents(".rate-me-widget").find(".widget-user-desc").text(result.info.count);
        	},
        	error: function(){
        		// $('.flash-message')
        		// 	.html('Something went wrong!')
        		// 	.removeClass("hide callout-success")
        		// 	.addClass("callout-danger");	        
        	},
        	complete: function(){
        		setTimeout(function() {
        			self.parents(".rate-me-widget").find(".overlay").addClass("hide");
		            $(".flash-message")
		            	.addClass("callout-success hide")
		            	.removeClass("callout-danger")
		            	.html('');
		        }, 1000);
        	}
        });
	});

	$(".qualities-tab li").on("click", function(event){
		if($(this).hasClass("praise")){
			$(this).parent().find(".suggest").removeClass("active");
			$("#suggest").removeClass("active");
		}else{
			$(this).parent().find(".praise").removeClass("active");
			$("#skills").removeClass("active");
		}
	});
});