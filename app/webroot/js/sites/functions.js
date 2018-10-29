//disable deletion process during this time
var inEditMode = false;

function duplicateAffiliate(){
	jQuery('#affiliatelistselect').select2("destroy");
	var newBlock = jQuery('#affiliatelistselect').clone();
	jQuery('#affiliate-restrictions tbody tr:last').after('<tr>'+
														'<td></td>'+
														'<td><input type="text" id="subaffiliate" name="subaffiliate" class="form-control" placeholder="SubId(s)"></td>'+
														'<td><span style = "cursor:pointer" class="glyphicon glyphicon-remove" id="remove-duplicate-affiliate"></span></td>'+
												  '</tr>');
	jQuery('#affiliate-restrictions tbody tr:last td:first').append(newBlock);
												  
	$("#affiliate-restrictions tbody tr td").children("select").select2();
}

jQuery(document).ready(function(){ 
				
	// DataTables Length to Select2
    jQuery('div.dataTables_length select').removeClass('form-control input-sm');
    jQuery('div.dataTables_length select').css({width: '60px'});
    jQuery('div.dataTables_length select').select2({minimumResultsForSearch: -1});

    // Configuration
    if (jQuery('#configurationTable').length){
    	jQuery('#siteconfigselect').select2();
    	jQuery('#siteconfigancillaryselect').select2();
    	jQuery('#affiliatelistselect').select2();
    	
    	jQuery('#configurationTable').DataTable({responsive: true,'bPaginate':false});
    	
    	jQuery('#duplicate-affiliate').on('click',function(e){
    		duplicateAffiliate();
    	});
    	
    	//Button switch - Configuration
    	$("[name='infinite-pop']").bootstrapSwitch();
    	$('input[name="infinite-pop"]').on('switchChange.bootstrapSwitch', function(event, state) {
    		var val = ((state == false) ? '0' : '1');
    	  	var id = $(this).data('siteconfigid');
    	  
    	  	update(id,'infinite_pop',val,'SiteConfiguration');
    	  
    	});
    }
    
    //Remove aff/sub restrictions
    jQuery(document).on('click', '#remove-duplicate-affiliate' , function(e) {
		$(this).parent().parent().remove();
	});
		
	//Add Site Configuration
	jQuery(document).on('click', '#add-siteconfig' , function(e) {
		var site_id = jQuery('#siteconfigselect').val();
		var ancillary_id = jQuery('#siteconfigancillaryselect').val();
		
		if(site_id == "" || ancillary_id == "")return;
		
		var obj = {};
		
		obj['site_id'] = site_id;
		obj['ancillary_id'] = ancillary_id;
		
		var arr_holder = [];
		$('#affiliate-restrictions tbody tr').each(function(index, ele){
				var aff = $(this).find('td:first select').val();
				var sub = $(this).find('td').eq(1).find('input').val();
				
				if(aff != "" && sub != ""){
				 	arr_holder.push({'affiliate' : aff, 'sub' : sub});	
				}else if(aff != "" && sub == ""){
					arr_holder.push({'affiliate' : aff, 'sub' : null});
				}	
		});
		
		if(arr_holder.length > 0){
			obj['restrictions'] = arr_holder;
		}
		
		addSiteConfig(obj).done(function(rsp){
			
			if(rsp.status == 'success'){
				location.reload();	
			}
		});
	});
	
	//Update Site Configuration
	$(document).on('click', '#update-siteconfig' , function(e) {
		var site_id = jQuery('#siteconfigselect').val();
		var ancillary_id = jQuery('#siteconfigancillaryselect').val();
		var config_id = jQuery('#update-siteconfig').data('configid');
		
		if(site_id == "" || ancillary_id == "")return;
		
		var obj = {};
		
		obj['site_id'] = site_id;
		obj['ancillary_id'] = ancillary_id;
		
		//will cause the record to update
		obj['config_id'] = config_id;
		
		var arr_holder = [];
		$('#affiliate-restrictions tbody tr').each(function(index, ele){
				var aff = $(this).find('td:first select').val();
				var sub = $(this).find('td').eq(1).find('input').val();
				
				if(aff != "" && sub != ""){
				 	arr_holder.push({'affiliate' : aff, 'sub' : sub});	
				}else if(aff != "" && sub == ""){
					arr_holder.push({'affiliate' : aff, 'sub' : null});
				}
				
		});
		
		if(arr_holder.length > 0){
			obj['restrictions'] = arr_holder;
		}
		
		addSiteConfig(obj).done(function(rsp){
			
			if(rsp.status == 'success'){
				location.reload();	
			}	
		});
		
	});
    	
    // Add User Validation
    if (jQuery('#addsite').length){
    	jQuery('#siteTable').DataTable({responsive: true});
    	
	    var addSiteValidator = jQuery('#addsite').validate({
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
	    
	    jQuery('#add-site-reset').click(function(){
	    	addSiteValidator.resetForm();
	    	
	    	jQuery('#addsite *').filter(':input').each(function(){
	    		jQuery(this).closest('.form-group').removeClass('has-error');
	    	});
	    });
    }
    
    //Website Delete Modal - Update attribute with site id when modal pops
    $(document).on("click", "#delete-site", function () {
	     var site_id = $(this).data('siteid');
	     var modal = $("#sites-delete-modal-action");
	     modal.attr('data-siteid', site_id);
	    
	});
	
	//Website Delete Modal - Delete Website and all relationships
    $(document).on("click", "#sites-delete-modal-action", function () {
	    var site_id = $(this).data('siteid');
	    var obj = { 'id' : site_id  };
	     
	    deleteSite(obj).done(function(rsp){
			$("#sites-delete-modal").modal('hide');
			
			if(rsp.status == 'success'){
				location.reload();	
			}	
		});
	    
	});
	
	//Ancillary Delete Modal - Update attribute with ancillary id when modal pops
    $(document).on("click", "#delete-ancillary", function () {
	     var ancillary_id = $(this).data('ancillaryid');
	     var modal = $("#ancillary-delete-modal-action");
	     modal.attr('data-ancillaryid', ancillary_id);
	    
	});
	
	//Ancillary Delete Modal - Delete Ancillary and all relationships
    $(document).on("click", "#ancillary-delete-modal-action", function () {
	    var ancillary_id = $(this).data('ancillaryid');
	    var obj = { 'id' : ancillary_id  };
	     
	    deleteAncillary(obj).done(function(rsp){
			$("#ancillary-delete-modal").modal('hide');
			
			if(rsp.status == 'success'){
				location.reload();	
			}
		});    
	});
    
    //Site Configuration Delete Modal - Update attribute with site config id when modal pops
    $(document).on("click", "#delete-siteconfig", function () {
    	if(inEditMode)return;
    	
	     var siteconfig_id = $(this).data('siteconfigid');
	     var modal = $("#siteconfig-delete-modal-action");
	     modal.attr('data-siteconfigid', siteconfig_id);
	    
	});
	
	//Site Configuration Delete Modal - Delete Site Config and all relationships
    $(document).on("click", "#siteconfig-delete-modal-action", function () {
	    var siteconfig_id = $(this).data('siteconfigid');
	    var obj = { 'id' : siteconfig_id  };
	    
	    if(!inEditMode) {
		    deleteSiteConfig(obj).done(function(rsp){
				$("#siteconfig-delete-modal").modal('hide');
				
				if(rsp.status == 'success'){
					location.reload();	
				}
				
			});
		}
	    
	});
    
    //Site Configuration Edit data
    $(document).on("click", "#edit-siteconfig", function () {
    	inEditMode = true;
	    var siteconfig_id = $(this).data('siteconfigid');
		var ancillary_id = $(this).data('ancillaryid');
		var config_id = $(this).data('configid');
		var restrictions_json = $(this).parent().parent().find('td').eq(3).html();
		jQuery('#add-siteconfig').hide();
		jQuery('#update-siteconfig').show();
		jQuery('#cancel-siteconfig').show();
		jQuery('#update-siteconfig').attr('data-configid', config_id);
		
		jQuery('#siteconfigselect').select2('val', siteconfig_id);
		jQuery('#siteconfigancillaryselect').select2('val', ancillary_id);
		
		//clear out static affilate row and all other data
		jQuery('#affiliate-restrictions tbody tr').remove("tr:gt(0)");
		jQuery('#affiliate-restrictions tbody tr td:first select').select2('val', "");	
		jQuery('#affiliate-restrictions tbody tr td').eq(1).find('input').val("");
		
		if(restrictions_json != ""){
			var json_obj = jQuery.parseJSON(restrictions_json);
			
			
			for(var i = 0; json_obj.length > i; i++){
				if(i == 0 ){
					jQuery('#affiliate-restrictions tbody tr td:first select').select2('val', json_obj[i][0]);	
					jQuery('#affiliate-restrictions tbody tr td').eq(1).find('input').val(json_obj[i][1]);	
				}else{
					duplicateAffiliate();
					jQuery('#affiliate-restrictions tbody tr:last td:first select').select2('val', json_obj[i][0]);	
					jQuery('#affiliate-restrictions tbody tr:last td').eq(1).find('input').val(json_obj[i][1]);
				}
			}
		}
	});
	
	//Site Configuration Cancel Edit
    $(document).on("click", "#cancel-siteconfig", function () {
	   	inEditMode=false;
	   	
		jQuery('#add-siteconfig').show();
		jQuery('#update-siteconfig').hide();
		jQuery('#cancel-siteconfig').hide();
		jQuery('#update-siteconfig').attr('data-configid', "");
		
		jQuery('#affiliate-restrictions tbody tr td:first select').select2('val', "");	
		jQuery('#affiliate-restrictions tbody tr td').eq(1).find('input').val("");	
		
		//clear out static affilate row and all other data
		jQuery('#affiliate-restrictions tbody tr').remove("tr:gt(0)");
		jQuery('#affiliate-restrictions tbody tr td:first select').select2('val', "");	
		jQuery('#affiliate-restrictions tbody tr td').eq(1).find('input').val("");
		
		
	});
	
    // Click to Edit Site Inline
    jQuery('#siteTable td').dblclick(function(e){
    	e.preventDefault();
    	var value = jQuery(this).data('site');
    	var field = jQuery(this).data('field');
		var siteid = jQuery(this).data('siteid');
		
		if(field == 'url'){
			jQuery(this).hide().html('http(s)://<input type="text" id="edit-site-'+field+'" name="data[Site]['+field+']" class="form-control input-sm" value="'+value+'">').fadeIn();
		}else{
			jQuery(this).hide().html('<input type="text" id="edit-site-'+field+'" name="data[Site]['+field+']" class="form-control input-sm" value="'+value+'">').fadeIn();
		}
		
		jQuery('#edit-site-'+field).focus();
	    jQuery('#edit-site-'+field).blur(function(e){
		    var newvalue = jQuery('#edit-site-'+field).val();
		    var validate = true;
		    var response = {'status':false};
		        		
		    if(value != newvalue && validate == true){
    			response = update(siteid,field,newvalue,'Site');
    		}
    		else if(validate == false && field=='url'){
    			jQuery(this).parent()
	    		.hide()
	    		.html('http(s)://'+value)
	    		.fadeIn();
    		}
    		else if(validate == false){
    			jQuery(this).parent()
	    		.hide()
	    		.html(value)
	    		.fadeIn();
    		}else{
    			response.status = true;
    		}
		    
		    if(response.status == true && field == 'url'){
				jQuery(this).parent()
				.hide()
				.attr('data-site',newvalue)
				.data('site',newvalue)
				.html('http(s)://'+newvalue)
				.fadeIn();
			}else if(response.status == true){
				jQuery(this).parent()
				.hide()
				.attr('data-site',newvalue)
				.data('site',newvalue)
				.html(newvalue)
				.fadeIn();
			}
		});
    });
    
 // Add Ancillary Validation
    if (jQuery('#addancillary').length){
    	jQuery('#add-ancillary-type').select2({minimumResultsForSearch:-1});
    	jQuery('#add-ancillary-fieldlist').select2({minimumResultsForSearch:-1});
    	jQuery('#add-ancillary-pagelist').select2({minimumResultsForSearch:-1});
    	jQuery('#add-ancillary-backend').select2({minimumResultsForSearch:-1});
    	jQuery('#window_width').select2();
    	jQuery('#window_height').select2();
    	
    	jQuery('#add-ancillary-reset').on('click',function(){
    		jQuery('#selectpagelist').hide();
			jQuery('#selectfieldlist').hide();
			jQuery('#selectclickid').hide();
			jQuery('#fieldvaluelist').hide();
			jQuery('#backendlist').hide();
			jQuery('#add-ancillary-triggervalue').slideUp();
			jQuery('#add-ancillary-fieldvalue').empty();
			jQuery('#add-ancillary-pagelist').select2("val","");
			jQuery('#add-ancillary-fieldlist').select2("val","");
			jQuery('#add-ancillary-type').select2("val","");
			jQuery('#add-ancillary-clickid').val("");
			jQuery('#add-ancillary-backend').select2("val","");
			jQuery('#add-ancillary-url').fadeIn();
			jQuery('#winpopsize').slideUp();
			
    	});
       	
    	jQuery('#add-ancillary-type').change(function(){
    		switch(jQuery(this).val()){
    		case 'page':
    			jQuery('#add-ancillary-fieldlist').select2("val","");
    			jQuery('#add-ancillary-clickid').val("");
    			jQuery('#add-ancillary-triggervalue').val("");
    			jQuery('#add-ancillary-fieldvalue').empty();
    			jQuery('#selectfieldlist').hide();
    			jQuery('#selectclickid').hide();
    			jQuery('#add-ancillary-triggervalue').slideUp();
    			jQuery('#fieldvaluelist').slideUp();
    			jQuery('#selectpagelist').fadeIn();
    			jQuery('#add-ancillary-url').fadeIn();
    			jQuery('#backendlist').hide();
    			jQuery('#add-ancillary-backend').select2("val","");
    			jQuery('#winpopsize').fadeIn();
    			break;
    			
    		case 'field':
    			jQuery('#add-ancillary-pagelist').select2("val","");
    			jQuery('#add-ancillary-clickid').val("");
    			jQuery('#selectpagelist').hide();
    			jQuery('#selectclickid').hide();
    			jQuery('#selectfieldlist').fadeIn();
    			jQuery('#fieldvaluelist').slideUp();
    			jQuery('#add-ancillary-triggervalue').hide();
    			jQuery('#add-ancillary-url').fadeIn();
    			jQuery('#backendlist').hide();
    			jQuery('#add-ancillary-backend').select2("val","");
    			jQuery('#winpopsize').fadeIn();
    			break;
    		
    		case 'click':
    			jQuery('#add-ancillary-pagelist').select2("val","");
    			jQuery('#add-ancillary-fieldlist').select2("val","");
    			jQuery('#add-ancillary-fieldvalue').empty();
    			jQuery('#selectpagelist').hide();
				jQuery('#selectfieldlist').hide();
				jQuery('#fieldvaluelist').hide();
				jQuery('#selectclickid').fadeIn();
				jQuery('#add-ancillary-triggervalue').val("");
				jQuery('#add-ancillary-triggervalue').hide();
				jQuery('#add-ancillary-url').fadeIn();
				jQuery('#backendlist').hide();
    			jQuery('#add-ancillary-backend').select2("val","");
    			jQuery('#winpopsize').fadeIn();
    			break;
    			
    		case 'backend':
    			jQuery('#add-ancillary-fieldlist').select2("val","");
    			jQuery('#add-ancillary-clickid').val("");
    			jQuery('#add-ancillary-triggervalue').val("");
    			jQuery('#add-ancillary-fieldvalue').empty();
    			jQuery('#selectfieldlist').hide();
    			jQuery('#selectclickid').hide();
    			jQuery('#add-ancillary-triggervalue').slideUp();
    			jQuery('#fieldvaluelist').slideUp();
    			jQuery('#add-ancillary-pagelist').select2("val","");
    			jQuery('#selectpagelist').hide();
    			jQuery('#add-ancillary-url').slideUp();
    			jQuery('#backendlist').fadeIn();
    			jQuery('#winpopsize').slideUp();
    			break;
    		
    		default:
    			jQuery('#selectpagelist').hide();
				jQuery('#selectfieldlist').hide();
				jQuery('#selectclickid').hide();
				jQuery('#add-ancillary-triggervalue').slideUp();
				jQuery('#add-ancillary-pagelist').select2("val","");
				jQuery('#add-ancillary-fieldlist').select2("val","");
				jQuery('#add-ancillary-fieldvalue').empty();
				jQuery('#add-ancillary-clickid').val("");
				jQuery('#fieldvaluelist').hide();
				jQuery('#add-ancillary-url').fadeIn();
				jQuery('#backendlist').hide();
    			jQuery('#add-ancillary-backend').select2("val","");
    			jQuery('#winpopsize').fadeIn();
    			break;
    		}
    	});
    	
    	jQuery('#add-ancillary-backend').change(function(){
    		
    	});
    	
    	jQuery('#add-ancillary-fieldlist').change(function(){
    		var fieldval = jQuery(this).val();
    		var active = fieldvalues[fieldval];
    		
    		if(active != undefined){
    			jQuery('#add-ancillary-fieldvalue').empty();
    			jQuery.each( active, function( value, name ) {
    				jQuery('#add-ancillary-fieldvalue').append('<option value="'+value+'">'+name+'</option>');
    			});
    			
    			jQuery('#add-ancillary-fieldvalue').select2({minimumResultsForSearch: -1});
        		jQuery('#fieldvaluelist').slideDown();
        		jQuery('#add-ancillary-triggervalue').slideUp();
    		}else{
    			jQuery('#add-ancillary-fieldvalue').empty();
    			jQuery('#fieldvaluelist').slideUp();
    			jQuery('#add-ancillary-triggervalue').slideDown();
    		}
    	});
    	    	  	
    	jQuery('#ancillaryTable').DataTable({responsive: true, bPaginate : false});
    	
	    var addAncillaryValidator = jQuery('#addancillary').validate({
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
	    
	    jQuery('#add-ancillary-reset').click(function(){
	    	addAncillaryValidator.resetForm();
	    	
	    	jQuery('#addancillary *').filter(':input').each(function(){
	    		jQuery(this).closest('.form-group').removeClass('has-error');
	    		jQuery(this).closest('.form-group').removeClass('has-success');
	    	});
	    });
    }
    
    // Click to Edit Ancillary Inline
    jQuery('#ancillaryTable td').dblclick(function(e){
    	e.preventDefault();
    	var value = jQuery(this).data('ancillary');
    	var field = jQuery(this).data('field');
		var ancillaryid = jQuery(this).data('ancillaryid');
		
		if(field == 'status'){
			jQuery(this).hide().html('<select id="edit-ancillary-status" name="data[Ancillary][status]" class="form-control input-sm"><option value="">Choose One</option><option value="0">Inactive</option><option value="1">Active</option></select>').fadeIn();
			jQuery('#edit-ancillary-status').select2({minimumResultsForSearch: -1, width:'100%'});
			jQuery('#edit-ancillary-status').focus();
		    jQuery('#edit-ancillary-status').change(function(e){
		    	console.log('changed');
			    var newvalue = jQuery('#edit-ancillary-status').val();
			    var display = (newvalue == "0") ? 'Inactive' : 'Active';
			    var validate = true;
			    var response = {'status':false};
			        		
			    if(validate == true){
	    			response = update(ancillaryid,field,newvalue,'Ancillary');
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
			    
			    if(response.status == true){
					jQuery(this).parent()
					.hide()
					.attr('data-ancillary',display)
					.data('ancillary',display)
					.html(display)
					.fadeIn();
				}
			});
		}else if(field == 'url'){
			jQuery(this).hide().html('<input id="edit-ancillary-url" name="data[Ancillary][url]" class="form-control input-sm" value = "'+value+'">').fadeIn();
			jQuery('#edit-ancillary-url').focus();
		    jQuery('#edit-ancillary-url').on('change blur', function(e){
		    	console.log('changed');
			    var newvalue = jQuery('#edit-ancillary-url').val();
			    console.log(newvalue);
			    var validate = true;
			    var response = {'status':false};
			        		
			    if(value != newvalue && validate == true){
			
			    	
			    	var post_config = { 'id':ancillaryid,
			    						'field':field,
			    						'value':newvalue,
			    						'model':'Ancillary'
			    		
			    	};
			    	//send as post because of url
	    			var responsePost = updatePost(post_config);
	    			responsePost.done(function(rsp){
	    				response = rsp;
	    				
	    			});
	    			
	    			
	    			 
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
			    
			    if(response.status == true){
					jQuery(this).parent()
					.hide()
					.attr('data-ancillary',newvalue)
					.data('ancillary',newvalue)
					.html(newvalue)
					.fadeIn();
				}
			});
		
		}else if(ancillaryid != undefined){
	    	jQuery(this).hide().html('<input type="text" id="edit-ancillary-'+field+'" name="data[Ancillary]['+field+']" class="form-control input-sm" value="'+value+'">').fadeIn();
		    jQuery('#edit-ancillary-'+field).focus();
		    jQuery('#edit-ancillary-'+field).blur(function(e){
			    var newvalue = jQuery('#edit-ancillary-'+field).val();
			    var validate = true;
			    var response = {'status':false};
			        		
			    if(value != newvalue && validate == true){
	    			response = update(ancillaryid,field,newvalue,'Ancillary');
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
			    
			    if(response.status == true){
					jQuery(this).parent()
					.hide()
					.attr('data-ancillary',newvalue)
					.data('ancillary',newvalue)
					.html(newvalue)
					.fadeIn();
				}
			});
		}
    });
});

function addSiteConfig(data){

	return $.ajax({
				type: 	"POST",
				url:	"/sites/addConfiguration",
				headers: {'x-keyStone-nonce': nonce, 'Content-Type':'application/json'},
				data:	 JSON.stringify(data),
				dataType: "json"
			});

}

function deleteSiteConfig(data){

	return $.ajax({
				type: 	"POST",
				url:	"/sites/deleteSiteConfiguration",
				headers: {'x-keyStone-nonce': nonce, 'Content-Type':'application/json'},
				data:	 JSON.stringify(data),
				dataType: "json"
			});

}

function deleteSite(data){

	return $.ajax({
				type: 	"POST",
				url:	"/sites/deleteSite",
				headers: {'x-keyStone-nonce': nonce, 'Content-Type':'application/json'},
				data:	 JSON.stringify(data),
				dataType: "json"
			});

}

function deleteAncillary(data){

	return $.ajax({
				type: 	"POST",
				url:	"/sites/deleteAncillary",
				headers: {'x-keyStone-nonce': nonce, 'Content-Type':'application/json'},
				data:	 JSON.stringify(data),
				dataType: "json"
			});

}

function setTriggerValue(){
	//Page was chosen
	if(jQuery('#add-ancillary-type').val() == 'page' && jQuery('#add-ancillary-pagelist').val() != ''){
		jQuery('#add-ancillary-trigger').val(jQuery('#add-ancillary-pagelist').val());
	}
	
	//Field was chosen
	if(jQuery('#add-ancillary-type').val() == 'field' && jQuery('#add-ancillary-fieldlist').val() != '' && jQuery('#add-ancillary-triggervalue').val() != ''){
		
	}
}

/**
 * Update a model via restful interface.
 * @param id
 * @param field
 * @param value
 * @returns
 */
function update(id,field,value,model){
	var result = $.ajax({
		url:'/sites/edit/'+id+'/'+field+'/'+value+'/'+model,
		dataType:'json',
		headers:{'x-keyStone-nonce': nonce},
		async:false,
	});
	return jQuery.parseJSON(result.responseText);
} 

/**
 * Update a model via restful interface. Send as a post because url can break cake
 * @param id
 * @param field
 * @param value
 * @returns
 */
function updatePost(post_config){
	
	return $.ajax({
				type: 	"POST",
				url:	"/sites/edit",
				headers: {'x-keyStone-nonce': nonce, 'Content-Type':'application/json'},
				data:	 JSON.stringify(post_config),
				dataType: "json",
				async:false
	});
	
} 