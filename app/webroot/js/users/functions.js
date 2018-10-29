jQuery(document).ready(function(){ 
	// User Data Table
	jQuery('#userTable').DataTable({responsive: true});
	
	// Audit Data Table
	jQuery('#auditTable').DataTable({responsive: true, "order": [[ 5, "desc" ]]});
		
	// DataTables Length to Select2
    jQuery('div.dataTables_length select').removeClass('form-control input-sm');
    jQuery('div.dataTables_length select').css({width: '60px'});
    jQuery('div.dataTables_length select').select2({minimumResultsForSearch: -1});
    
    // Add User Select 2
    jQuery('#add-user-group-id').select2({minimumResultsForSearch: -1, width:'100%'});
    
    if(jQuery('#load-user').length){
    	var href = window.location.href;
    	var selectedid = href.substr(href.lastIndexOf('/') + 1);
    	
    	// Audit User Load Select 2
    	jQuery('#load-user').select2({minimumResultsForSearch: -1, width:'100%'});
    
    	// Change the select to current selected user
    	if(selectedid>0){
    		jQuery('#load-user').select2('val',selectedid);
    		jQuery('#username').html(jQuery('#load-user option:selected').text());
    	}
    	
    	// OnChange Load User
    	jQuery('#load-user').on('change',function(){
    		window.location.replace('/users/activity/'+this.value);
    	});
    }
    
    // Add User Validation
    if (jQuery('#adduser').length){
	    var addUserValidator = jQuery('#adduser').validate({
	    	focusCleanup: true,
	    	focusInvalid: false,
	     	onkeyup: false,
	    	        	    	    	
			highlight: function(element) {
				jQuery(element).closest('.form-group').removeClass('has-success').addClass('has-error');
			},
			
			unhighlight: function(element) {
				jQuery(element).closest('.form-group').removeClass('has-error');
			},
			
			success: function(element) {
				jQuery(element).closest('.form-group').removeClass('has-error').addClass('has-success');
			}
		});
            
	    jQuery('#add-user-email').rules('add',{
	    	emailexists:  true
	    });
	    
	    jQuery.validator.addMethod('emailexists',function(value, element){
	    	return this.optional(element) || emailExists(value);
	    }, 'Email already exists.');
	    
	    jQuery('#add-user-reset').click(function(){
	    	addUserValidator.resetForm();
	    	
	    	jQuery('#adduser *').filter(':input').each(function(){
	    		jQuery(this).closest('.form-group').removeClass('has-error');
	    	});
	    });
    }
    // User Photo Upload
    jQuery('td.userphoto').dropzone({
    	init:function(){
    		this.on("processing",function(file,xhr,formData){
    			var uid =  jQuery(this.element).data('userid');
    			this.options.url = '/users/addphoto/'+uid+'/';
    			jQuery(this.element).children('.progress').show();
    		})
    	},
    	uploadprogress:function(file,progress,bytesSent){
    		jQuery(this.element).children('.progress').children('.progress-bar').css('width',progress+'%');
    	},
    	complete:function(){
    		location.reload(true);
    	},
    	url:'empty',
    	maxFilesize:'2',
    	autoDiscover: false,
    	createImageThumbnails: false,
    	previewTemplate : '<div style="display:none"></div>',
    	headers: {'x-keyStone-nonce': nonce}
    });

    // Click to Edit User Inline
    jQuery('#userTable td').dblclick(function(e){
    	e.preventDefault();
    	var value = jQuery(this).data('user');
    	var field = jQuery(this).data('field');
		var userid = jQuery(this).data('userid');
		
    	if(field == 'password'){
    		jQuery(this).hide().html('<input type="password" id="edit-user-'+field+'" name="data[User]['+field+']" class="form-control input-sm" value="">').fadeIn();
        	jQuery('#edit-user-'+field).focus();
        	jQuery('#edit-user-'+field).blur(function(e){
        		var newvalue = jQuery('#edit-user-'+field).val();
        		var response = {'status':false};
        		
        		if(newvalue != ''){
        			var response = updateUser(userid,field,newvalue);
        		}
        		else{
        			response.status = true;
        		}
    			if(response.status == true){
		    		jQuery(this).parent()
		    		.hide()
		    		.html('Hidden')
		    		.fadeIn();
    			}
        	});
    	}
    	else if(field == 'group'){
    		var groupOptions = jQuery('#add-user-group-id > option').clone();
    		jQuery(this).hide().html('<select id="edit-user-group" name="data[Group][id]"><option value="">Choose one...</option></select>').fadeIn();
    		jQuery('#edit-user-group').append(groupOptions).select2({minimumResultsForSearch: -1, width:'100%'});	
	    	jQuery('#edit-user-'+field).focus();
	    	jQuery('#edit-user-'+field).change(function(e){
	    		var newvalue = jQuery('#edit-user-'+field).val();
	    		var newtext = jQuery('#edit-user-'+field).children('option:selected').text();
	    		if(newvalue != "0"){
	    			var response = updateUser(userid,'group_id',newvalue);
	    			if(response.status == true){
			    		jQuery(this).parent()
			    		.hide()
			    		.attr('data-user',newvalue)
			    		.data('user',newvalue)
			    		.html(newtext)
			    		.fadeIn();
	    			}
	    		}
	    	});
    	}
    	else if(field == 'status'){
    		jQuery(this).hide().html('<select id="edit-user-status" name="data[User][status]"><option value="">Choose one...</option><option value="1">Active</option><option value="0">Inactive</option></select>').fadeIn();
    		jQuery('#edit-user-status').select2({minimumResultsForSearch: -1, width:'100%'});
	    	jQuery('#edit-user-'+field).focus();
	    	jQuery('#edit-user-'+field).change(function(e){
	    		var newvalue = jQuery('#edit-user-'+field).val();
	    		var response = updateUser(userid,field,newvalue);
	    		
	    		if(response.status == true){
		    		jQuery(this).parent()
		    		.hide()
		    		.attr('data-user',newvalue)
		    		.data('user',newvalue);
		    		
		    		if(newvalue == "1"){
		    			jQuery(this).parent().html('Active').fadeIn();
		    		}else{
		    			jQuery(this).parent().html('Inactive').fadeIn();
		    		}
	    		}
	    	});
    	}
    	else{
	    	jQuery(this).hide().html('<input type="text" id="edit-user-'+field+'" name="data[User]['+field+']" class="form-control input-sm" value="'+value+'">').fadeIn();
	    	jQuery('#edit-user-'+field).focus();
	    	jQuery('#edit-user-'+field).blur(function(e){
	    		var newvalue = jQuery('#edit-user-'+field).val();
	    		var validate = true;
	    		var response = {'status':false};
	    		
	    		if(value != newvalue && field == 'email'){
	    			validate = emailExists(newvalue);
	    		}
	    		
	    		if(field == 'name' && value != newvalue){
	    			var nameArray = newvalue.split(' ');
	    			var response1 = updateUser(userid,'first_name',nameArray[0]);
	    			var response2 = updateUser(userid,'last_name',nameArray[1]);
	    		}
	    		else{
		    		if(value != newvalue && validate == true){
		    			response = updateUser(userid,field,newvalue);
		    		}
		    		else if(validate == false){
		    			jQuery(this).parent()
			    		.hide()
			    		.html(value)
			    		.fadeIn();
		    		}
		    		else{
		    			response.status = true;
		    		}
	    		}
		    		if(response.status == true || response1.status == true || response2.status == true){
			    		jQuery(this).parent()
			    		.hide()
			    		.attr('data-user',newvalue)
			    		.data('user',newvalue)
			    		.html(newvalue)
			    		.fadeIn();
		    		}
	    		
	    	});
    	}
    });
});

/**
 * Check if an email address exists in the user table.  This is a sync call
 * as validation cannot wait for async returns.
 * 
 * @param email
 */
function emailExists(email){
	var result = $.ajax({
		url:'/users/emailexists/'+email,
		headers:{'x-keyStone-nonce': nonce},
		async:false
	});

	if(result.responseText == 'true'){return false;}else{return true;}
}

/**
 * Update a user via restful interface.
 * @param id
 * @param field
 * @param value
 * @returns
 */
function updateUser(id,field,value){
	var result = $.ajax({
		url:'/users/edit/'+id+'/'+field+'/'+value,
		dataType:'json',
		headers:{'x-keyStone-nonce': nonce},
		async:false,
	});
	return jQuery.parseJSON(result.responseText);
} 