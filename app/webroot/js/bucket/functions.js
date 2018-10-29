// Global to the DOM
window.report_dates ={
	today : moment().format('MM-DD-YYYY'),
	yesterday : moment().subtract(1,'days').format('MM-DD-YYYY'),
	yesterday_key : moment().subtract(1,'days').format('MMDDYYYY'),
	twodays : moment().subtract(2,'days').format('MM-DD-YYYY'),
	fivedays : moment().subtract(5,'days').format('MM-DD-YYYY'),
	weekago : moment().subtract(7,'days').format('MM-DD-YYYY'),
	monthstart : moment().startOf('month').format('MM-DD-YYYY'),
	yearstart : moment().startOf('year').format('MM-DD-YYYY'),
	lastyearstart : moment().startOf('year').subtract('y',1).format('MM-DD-YYYY'),
	lastyearend : moment().startOf('year').subtract('y',1).endOf('year').format('MM-DD-YYYY'),
	currentyear : moment().startOf('year').format('YYYY'),
	lastyear : moment().startOf('year').subtract(1,'y').format('YYYY'),
}

jQuery(document).ready(function(){ 
	//Override popover to allow callback functions
	var showPopover = $.fn.popover.Constructor.prototype.show;
	jQuery.fn.popover.Constructor.prototype.show = function() {
		showPopover.call(this);
		if (this.options.showCallback) {
			this.options.showCallback.call(this);
		}
	}

	var hidePopover = $.fn.popover.Constructor.prototype.hide;
	jQuery.fn.popover.Constructor.prototype.hide = function() {
		if (this.options.hideCallback) {
			this.options.hideCallback.call(this);
		}
		hidePopover.call(this);
	}
	
	//When table pagination is used, reattach hover nodes on dynamic content.
    jQuery('#bucketTable').on( 'page.dt', function () {
		 jQuery("span[rel=hoverovers]", bucket_table.fnGetNodes()).popover({ trigger: "hover" }); 
		 
	});
	
	// Bucket Data Table
	if(jQuery('#bucketTable').length>0){
		var table = initBucketDataTable();
		//Base Buckets
		jQuery('#bucketTable').on('click', 'td.open-bucket', function(){
			var affiliate_id = jQuery(this).parent().data('affiliate-id');

			if(jQuery(this).parent().next('tr.bucket').is(':visible')){
				jQuery(this).parent().css('font-weight', 'normal');
				jQuery(this).parent().nextUntil('tr.open-bucket').hide();
				jQuery(this).parent().find('span:first').removeClass().addClass('fa fa-arrow-right');
				jQuery('.bucket-owner-'+affiliate_id).css('font-weight', 'normal');
				jQuery('.bucket-owner-'+affiliate_id).find('span:first').removeClass().addClass('fa fa-arrow-right');
			}else{
				jQuery(this).parent().css('font-weight', 'bold');
				jQuery(this).parent().find('span:first').removeClass().addClass('fa fa-arrow-down');
				jQuery('.bucket-owner-'+affiliate_id).toggle();
			}
		});
		
		//Sub Buckets
		jQuery('#bucketTable').on('click', 'td.open-subbucket', function(){
			var affiliate_id = jQuery(this).parent().data('affiliate-id');
			var bucket_id = jQuery(this).parent().data('bucket-id');
			
			if(jQuery(this).parent().next('tr.subbucket').is(':visible')){
				jQuery(this).parent().css('font-weight', 'normal');
				jQuery(this).parent().nextUntil('tr.open-subbucket').hide();
				jQuery(this).parent().find('span:first').removeClass().addClass('fa fa-arrow-right');
			}else{
				jQuery(this).parent().css('font-weight', 'bold');
				jQuery(this).parent().find('span:first').removeClass().addClass('fa fa-arrow-down');
				jQuery('.bucket-parent-'+bucket_id).toggle();
			}
		});
		
		// Click to Edit Bucket Inline
	    jQuery('#bucketTable').on('dblclick', 'td.editable', function(e){
	    	e.preventDefault();
	    	var field = jQuery(this).data('field');
	    	var value = jQuery(this).data('value');
	    	var bucketid = jQuery(this).parent().data('bucket-id');
	    	var affiliateid = jQuery(this).parent().data('affiliate-id');
	    	
	    	if(field == 'has_subs'){
	    		jQuery(this).hide().html('<select id="edit-bucket-'+field+'" name="data[Bucket]['+field+']"><option value="">Choose...</option><option value="1">True</option><option value="0">False</option></select>').fadeIn();
	    		jQuery('#edit-bucket-'+field+'').select2({minimumResultsForSearch: -1, width:'100%'});	
	    	}else{
	    		jQuery(this).hide().html('<input type="text" id="edit-bucket-'+field+'" name="data[Bucket]['+field+']" class="form-control input-sm" value="'+value+'">').fadeIn();
	    	}
	    	
	    	jQuery('#edit-bucket-'+field).focus();
	    	
	    	if(field != 'has_subs') {
		    	jQuery('#edit-bucket-'+field).on('blur', function(e) {
		    		var newvalue = jQuery('#edit-bucket-'+field).val();
		    		var validate = true;
		    		var response = {'status':false};
		    		
		    		switch(true){
		    			case field == 'wallet':
		    				// Did we change the original value
			    			if(value != newvalue && validate == true){
				    			response = updateAffiliate(affiliateid,field,newvalue);
				    		}
				    		else if(validate == false){
				    			jQuery(this).parent()
					    		.hide()
					    		.html(value)
					    		.fadeIn();
				    		}
				    		else{
				    			response.status = false;
				    		}
			    			// Valid? return new value, else return original
				    		if(response.status == 'success'){
						    	jQuery(this).parent()
						    	.hide()
						    	.attr('data-value',newvalue)
						    	.html('$ '+newvalue)
						    	.fadeIn();
					    	}else{
					    		jQuery(this).parent()
					    		.hide()
					    		.html('$ '+value)
					    		.fadeIn();
					    	}
			    		break;
		    			break;
			    		
		    			case field == 'amount':
			    		case field == 'prefill':
			    		case field == 'override_payout':
			    			// Did we change the original value
			    			if(value != newvalue && validate == true){
				    			response = updateBucket(bucketid,field,newvalue);
				    		}
				    		else if(validate == false){
				    			jQuery(this).parent()
					    		.hide()
					    		.html(value)
					    		.fadeIn();
				    		}
				    		else{
				    			response.status = false;
				    		}
			    			// Valid? return new value, else return original
				    		if(response.status == 'success'){
						    	jQuery(this).parent()
						    	.hide()
						    	.attr('data-value',newvalue)
						    	.html(newvalue)
						    	.fadeIn();
					    	}else{
					    		jQuery(this).parent()
					    		.hide()
					    		.html('$ '+value)
					    		.fadeIn();
					    	}
			    		break;
			    		
			    		case field == 'prefill_payback':
			    		case field == 'override_margin':
			    			// Did we change the original value
			    			if(value != newvalue && validate == true){
				    			response = updateBucket(bucketid,field,newvalue);
				    		}
				    		else if(validate == false){
				    			jQuery(this).parent()
					    		.hide()
					    		.html(value)
					    		.fadeIn();
				    		}
				    		else{
				    			response.status = false;
				    		}
			    			// Valid? return new value, else return original
				    		if(response.status == 'success'){
						    	jQuery(this).parent()
						    	.hide()
						    	.attr('data-value',newvalue)
						    	.html(newvalue)
						    	.fadeIn();
					    	}else{
					    		jQuery(this).parent()
					    		.hide()
					    		.html(value+' %')
					    		.fadeIn();
					    	}
			    		break;
			    		
			    		case field == 'has_subs':
			    			// Did we change the original value
			    			if(value != newvalue && validate == true){
				    			response = updateBucket(bucketid,field,newvalue);
				    		}
				    		else if(validate == false){
				    			jQuery(this).parent()
					    		.hide()
					    		.html(value)
					    		.fadeIn();
				    		}
				    		else{
				    			response.status = false;
				    		}
			    			// Valid? return new value, else return original
			    			if(value == '1'){value="True";}else{value="False";}
			    			if(newvalue == '1'){newvalue="True";}else{newvalue="False";}
				    		if(response.status == 'success'){
						    	jQuery(this).parent()
						    	.hide()
						    	.attr('data-value',newvalue)
						    	.html(newvalue)
						    	.fadeIn();
					    	}else{
					    		jQuery(this).parent()
					    		.hide()
					    		.html(value)
					    		.fadeIn();
					    	}
			    		break;
		    		}
		    	});
	    	}
	    	else{
	    		jQuery('#edit-bucket-'+field).on('change', function(){
	    			var newvalue = jQuery('#edit-bucket-'+field).val();
		    		var validate = true;
		    		var response = {'status':false};
		    		switch(true){
			    		case field == 'has_subs':
			    			// Did we change the original value
			    			if(value != newvalue && validate == true){
				    			response = updateBucket(bucketid,field,newvalue);
				    		}
				    		else if(validate == false){
				    			jQuery(this).parent()
					    		.hide()
					    		.html(value)
					    		.fadeIn();
				    		}
				    		else{
				    			response.status = false;
				    		}
			    			// Valid? return new value, else return original
			    			if(value == '1'){value="True";}else{value="False";}
			    			if(newvalue == '1'){newvalue="True";}else{newvalue="False";}
				    		if(response.status == 'success'){
						    	jQuery(this).parent()
						    	.hide()
						    	.attr('data-value',newvalue)
						    	.html(newvalue)
						    	.fadeIn();
					    	}else{
					    		jQuery(this).parent()
					    		.hide()
					    		.html(value)
					    		.fadeIn();
					    	}
			    		break;
		    		}
	    		});
	    	}
	    });
	}
});

/**
 * Initialise the bucket data table
 */
function initBucketDataTable() {	
	bucket_table = jQuery('#bucketTable').dataTable({
			'iDisplayLength': 100,
			'bLengthChange' : false,
			'stateSave': false,
			'columns': [
			    {'orderable':false,},
				{'orderable':false,},
				{'orderable':false,},
				{'orderable':false,},
				{'orderable':false,},
				{'orderable':false,},
				{'orderable':false,},
				{'orderable':false,},
				{'orderable':false,},
				{'orderable':false,},
				{'orderable':false,}]
		});
	
	jQuery("td[rel=hoverovers]", bucket_table.fnGetNodes()).popover({ trigger: "hover" });	
	return bucket_table;
}

/**
 * Add the loader while we wait for data to be returned.
 * @returns {String}
 */
function getRowLoader(id){
	return '<tr id="loader_'+id+'">' +
    '<td text-align:center; vertical-align:middle;" colspan="11">' +
    '<div><img src="/images/loaders/sand_small.svg"></div>' +
    '</td>' +
    '</tr>';
}

/**
 * Retrieve a list of affiliates
 * @returns json
 */
function getAffiliates(){
	return $.ajax({
		url:'/affiliates/getAffiliates',
		headers:{'x-keyStone-nonce': nonce} 
	});
}

/**
 * Update a bucket via restful interface.  Single field update per call, used for 
 * inline edits.
 * @param id
 * @param field
 * @param value
 * @returns
 */
function updateBucket(id,field,value){
	var result = $.ajax({
		url:'/buckets/edit/'+id+'/'+field+'/'+value,
		dataType:'json',
		headers:{'x-keyStone-nonce': nonce},
		async:false,
	});
	return jQuery.parseJSON(result.responseText);
}

function updateAffiliate(id,field,value){
	var result = $.ajax({
		url:'/buckets/editaffiliate/'+id+'/'+field+'/'+value,
		dataType:'json',
		headers:{'x-keyStone-nonce': nonce},
		async:false,
	});
	return jQuery.parseJSON(result.responseText);
}