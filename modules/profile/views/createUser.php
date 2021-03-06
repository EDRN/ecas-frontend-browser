<?php 
/*
 * Create User Profile:
 * Allow user to create profile
 * 		includes validation
 */

?>

<script type="text/javascript">
$(document).ready(function() {
	// validate signup form on keyup and submit
	var validator = $("#signupform").validate({
		rules: {
			firstname: "required",
			lastname: "required",
			username: {
				required: true,
				minlength: 2,
			},
			password: {
				required: true,
				minlength: 5
			},
			password_confirm: {
				required: true,
				minlength: 5,
				equalTo: "#password"
			},
			email: {
				required: true,
				email: true,
			}
		},
		messages: {
			firstname: "Enter your firstname",
			lastname: "Enter your lastname",
			username: {
				required: "Enter a username",
				minlength: jQuery.format("Enter at least {0} characters"),
			},
			password: {
				required: "Provide a password",
				rangelength: jQuery.format("Enter at least {0} characters")
			},
			password_confirm: {
				required: "Repeat your password",
				minlength: jQuery.format("Enter at least {0} characters"),
				equalTo: "Enter the same password as above"
			},
			email: {
				required: "Please enter a valid email address",
				minlength: "Please enter a valid email address",
			},
		},
		// set this class to error-labels to indicate valid fields
		success: function(label) {
			// set &nbsp; as text for IE
			label.html("&nbsp;").addClass("checked");
		}
	});
	
	// propose username by combining first- and lastname
	$("#username").focus(function() {
		var firstname = $("#firstname").val();
		var lastname = $("#lastname").val();
		if(firstname && lastname && !this.value) {
			this.value = firstname + "." + lastname;
		}
	});

});
</script>


    <div class='span-22 append-1 prepend-1 last' id='profile_container'>
    	<h1>Obtaining Access</h1>

		<hr class="space">
		
 		<fieldset id='profile_fieldset'>
		<form id="signupform" autocomplete="off" method="post" action="<?php echo SITE_ROOT?>/profile/access.do">
	    	<hr class="space">

	    	<div class="span-4">
	    		<h6 align="right">First Name</h6>
	    	</div>
	    	<div class="span-15">
	    		<input class="profile_input" id="firstname" name="firstname" type="text" value="" maxlength="100" />
	    	</div>
	    	
	    	<hr class="space">
	    	
	    	<div class="span-4 ">
	    		<h6 align="right">Last Name</h6>
	    	</div>
	    	<div class="span-15">
	    		<input class="profile_input" id="lastname" name="lastname" type="text" value="" maxlength="100" />
	    	</div>
	    	
	    	<hr class="space">
	    	
	    	<div class="span-4">
	    		<h6 align="right">Desired Login Name</h6>
	    	</div>
	    	<div class="span-15">
	    		<input class="profile_input" id="username" name="username" type="text" value="" maxlength="50" />
	    	</div>

	    	<hr class="space">
	    	
	    	<div class="span-4">
	    		<h6 align="right">Email</h6>
	    	</div>
	    	<div class="span-15">
	    		<input class="profile_input" id="email" name="email" type="text" value="" maxlength="150" />
	    	</div>
	    		
    		<hr class="space">
    		<hr class="space">
    		    	
        	<div class="span-4 ">
	    		<h6 align="right">Choose a password</h6>
	    	</div>
	    	<div class="span-15">
	    		<input class="profile_input" id="password" name="password" type="password" maxlength="50" value="" />
	    	</div>
	    	
	    	<hr class="space">
	    	
	    	<div class="span-4">
	    		<h6 align="right">Confirm password</h6>
	    	</div>
	    	<div class="span-15">
	    		<input class="profile_input" id="password_confirm" name="password_confirm" type="password" maxlength="50" value="" />
	    	</div>
	    	
	    	<hr class="space">
	    	<hr class="space">
	    	
	    	<div class="span-10" align="center">
	    		<input class="profile_input" id="button_new_account" type="submit" value="Submit">
    		</div>

    	</form>
    	</fieldset>
    </div>


<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
</script>
<script type="text/javascript">
_uacct = "UA-2623402-1";
urchinTracker();
</script>
    

    

