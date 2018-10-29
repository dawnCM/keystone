jQuery(document).ready(function(){ 
	jQuery("#UserLoginForm").validate({
		focusCleanup: true,
    	focusInvalid: false,
		
		errorPlacement: function(error,element){
			return true;
		},
		
		highlight: function(element) {
			jQuery(element).closest('.input-group').removeClass('has-success').addClass('has-error');
		},
		
		unhighlight: function(element) {
			jQuery(element).closest('.input-group').removeClass('has-error');
		},
		
		success: function(element) {
			jQuery(element).closest('.input-group').removeClass('has-error');
        }
	});
	
	jQuery('#UserEmail').focus();
	
	jQuery('#lostpw').on('click',function(){
		if(jQuery('#lostpw').prop("checked") == true){
			jQuery('#loginsubmitbt').removeClass('btn-success').addClass('btn-warning');
			jQuery('#loginsubmitbt > span').text('Reset Password');
			jQuery('#title-text').text('Reset your password');
			jQuery('#description-text').text('This will generate a confirmation email that will require you to take action before your password can be reset.');
			jQuery('.loginfields').hide();
			jQuery('#UserLoginForm').attr({action:'/users/processReset'});
		}else{
			jQuery('#loginsubmitbt').removeClass('btn-warning').addClass('btn-success');
			jQuery('#loginsubmitbt > span').text('Sign In');
			jQuery('#title-text').text('Sign in to your account');
			jQuery('#description-text').text('');
			jQuery('.loginfields').fadeIn();
			jQuery('#UserLoginForm').attr({action:'/users/login'});
		}
	})
});     