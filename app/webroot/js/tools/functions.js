// Global to the DOM
window.report_dates ={
	today : moment().format('YYYY-MM-DD'),
	tomorrow: moment().add(1,'days').format('YYYY-MM-DD'),
	yesterday : moment().subtract(1,'days').format('YYYY-MM-DDY'),
	twodays : moment().subtract(2,'days').format('YYYY-MM-DDY'),
	fivedays : moment().subtract(5,'days').format('YYYY-MM-DDY'),
	weekago : moment().subtract(7,'days').format('YYYY-MM-DDY'),
	monthstart : moment().startOf('month').format('YYYY-MM-DDY'),
	yearstart : moment().startOf('year').format('YYYY-MM-DDY'),
	lastyearstart : moment().startOf('year').subtract(1,'y').format('YYYY-MM-DDY'),
	lastyearend : moment().startOf('year').subtract(1,'y').endOf('year').format('YYYY-MM-DDY'),
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
	
	jQuery('#select-copypingtree').select2();
   
	//Clear fields and start over
	jQuery('#clear_details').on('click',function(){

		jQuery('#vendor-search-panel').fadeIn();
		jQuery('#vendorLoader').fadeOut();

		jQuery('#pingtree_results').hide();
		jQuery('#pingtree_results').fadeOut();
		
		jQuery('#buyer_name').val('');
		jQuery('#contract_name').val('');
		jQuery('#affiliate_id').val('');
		
		jQuery('#clonepingtree').show();
		jQuery('#clonepingtree').fadeIn();
	});
	
	//tools/clone pingtree
	jQuery('#copy_pingtree').on('click',function(){
		jQuery('#vendor-search-panel').fadeOut();
		jQuery('#vendorLoader').fadeIn();
		
		var buyername = jQuery('#buyer_name').val();
		var contractname = jQuery('#contract_name').val();
		var affiliateid = jQuery('#affiliate_id').val();
		var replacetext = jQuery('#replace_text').val();
		var copy_pingtree_id = jQuery('#select-copypingtree').val();
		var rsp;
	
		jQuery.when(
			copy_pingtree(copy_pingtree_id, buyername, contractname, affiliateid, replacetext).done(function(result){
				rsp = jQuery.parseJSON(result);
	    	})
		).then(function(){
			
			
			var success = '<h3>The creation of the '+buyername+' pingtree was successful</h3>'
			jQuery("#clonepingtree").fadeOut();
			if(rsp.status == 'success'){
				
				jQuery("#pingtree_details").html(success);
				jQuery("#pingtree_results").removeClass().addClass("panel panel-success");
				
			}else{
				var error = '<h3>The creation of the '+buyername+' pingtree was unsuccessful</h3><br><h3>'+rsp.msg+'</h3>';
				jQuery("#pingtree_details").html(error);
				jQuery("#pingtree_results").removeClass().addClass("panel panel-danger");
				
			}
			jQuery("#pingtree_results").fadeIn();			
		});
	});
	
	//General date select tools
	jQuery('#custom-date').on('click',function(){
		jQuery('#startdate').datepicker("setDate", moment().format('MM/DD/YYYY'));
		jQuery('#enddate').datepicker("setDate", moment().add(1,'days').format('MM/DD/YYYY'));
	});
	
	jQuery('#yesterday-date').on('click',function(){
		jQuery('#startdate').datepicker("setDate", moment().subtract(1,'days').format('MM/DD/YYYY'));
		jQuery('#enddate').datepicker("setDate", moment().format('MM/DD/YYYY'));
	});
	
	jQuery('#week-date').on('click',function(){
		jQuery('#startdate').datepicker("setDate", moment().subtract(7,'days').format('MM/DD/YYYY'));
		jQuery('#enddate').datepicker("setDate", moment().add(1,'days').format('MM/DD/YYYY'));
	});
	
	jQuery('#month-date').on('click',function(){
		jQuery('#startdate').datepicker("setDate", moment().startOf('month').format('MM/DD/YYYY'));
		jQuery('#enddate').datepicker("setDate", moment().add(1,'days').format('MM/DD/YYYY'));
	});
		
	jQuery('#mobile').on('click',function(){
		if(jQuery('#mobile').is(':checked'))
			jQuery('#mobile').val(2);
		else
			jQuery('#mobile').val(1);
	});
	
	jQuery('#military').on('click',function(){
		if(jQuery('#military').is(':checked'))
			jQuery('#military').val(2);
		else
			jQuery('#military').val(1);
	});
});

/**
 * Ajax call to copy ping tree
 * @param buyername
 * @param contractname
 * @param affiliateid
 * @paaram replacetext
 */
function copy_pingtree(copy_pingtree_id, buyername, contractname, affiliateid, replacetext){
	return $.ajax({
		url:'/tools/clonePingTree/'+copy_pingtree_id+'/'+buyername+'/'+affiliateid+'/'+contractname+'/'+replacetext,
		headers:{'x-keyStone-nonce': nonce, 'X-Api-Id':0, 'X-Api-Key':'keystone314'}
	});
}




