$(document).ready( function () {
	$(".skills").select2();
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
});