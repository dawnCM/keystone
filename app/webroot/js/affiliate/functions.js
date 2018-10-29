// Global to the DOM
var affiliate_table;
var vendor_table;
window.report_dates ={
	today : moment().format('MM-DD-YYYY'),
	tomorrow : moment().add(1,'days').format('MM-DD-YYYY'),
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
    // Affiliate Data Table
	if(jQuery('#affiliateTable').length>0){
			
		var affiliates = [];
		var affiliate = {};
		var affiliate_summary = [];
	    jQuery.when(
	    	getAffiliates().done(function(result){
	    		for (i = 0; i < result.data.row_count; i++) { 	
	    			affiliate = new Object();
	    			affiliate.affiliate_id = result.data.affiliates.affiliate[i].affiliate_id;
	    			affiliate.name = result.data.affiliates.affiliate[i].affiliate_name;
	    			affiliate.api = result.data.affiliates.affiliate[i].api_key;
	    			affiliate.address = result.data.affiliates.affiliate[i].address.street_1+' '+result.data.affiliates.affiliate[i].address.street_2;
	    			affiliate.city = result.data.affiliates.affiliate[i].address.city+', '+result.data.affiliates.affiliate[i].address.state+' '+result.data.affiliates.affiliate[i].address.zip_code;
	    			affiliate.contact = result.data.affiliates.affiliate[i].account_managers.contact.contact_name;
	    			affiliate.website = result.data.affiliates.affiliate[i].website;
	    			affiliate.account_status = result.data.affiliates.affiliate[i].account_status.account_status_name;
	    			affiliate.clicks = '0';
	    			affiliate.conversions = '0';
	    			affiliate.conversion_percentage = '0.00';
	    			affiliate.revenue = '0.00';
	    			affiliate.epc = '0.00';
	    			affiliates[result.data.affiliates.affiliate[i].affiliate_id]=affiliate;
	    		}
	    	})
		).then(function(){
			jQuery.when(
				getAffiliateSummary(0,window.report_dates.monthstart,window.report_dates.tomorrow).done(function(result){
					for (i = 0; i < result.data.row_count; i++) {
						if(result.data.row_count<2){
							aff = result.data.affiliates.affiliate_summary;
						}else{
							aff = result.data.affiliates.affiliate_summary[i];
						}
						if(typeof affiliates[aff.affiliate.affiliate_id] != 'undefined') {
							tmp = affiliates[aff.affiliate.affiliate_id];
							tmp.clicks = aff.clicks;
							tmp.conversions = aff.conversions;
							tmp.conversion_percentage = (aff.conversions/aff.clicks)*100;
							tmp.revenue = aff.revenue;
							tmp.epc = aff.revenue/aff.clicks;
							if(tmp.epc == Infinity) {tmp.epc = '0.00';}
							affiliates[aff.affiliate.affiliate_id]=tmp;
							
						}
					}
				})
			).then(function(){
				for (key in affiliates) {
				    if (affiliates.hasOwnProperty(key) && /^0$|^[1-9]\d*$/.test(key) && key <= 4294967294) {
				    	jQuery('#affiliateTableBody').append(
							'<tr class="open-campaigns" style="cursor:pointer;" '+
								'data-address="'+affiliates[key].address+'" '+
								'data-url="'+affiliates[key].website+'" '+
								'data-city="'+affiliates[key].city+'" '+
								'data-acctmgr="'+affiliates[key].contact+'" '+
				    			'data-id="'+affiliates[key].affiliate_id+'"' +
				    			'data-name="'+affiliates[key].name+'">' +
							'<td text-align:center; vertical-align:middle;"><span style="margin-left:0px; id="toggle_'+affiliates[key].affiliate_id+'" class="fa fa-arrow-right"></span></td>' +
							'<td>'+affiliates[key].affiliate_id+'</td>' +
							'<td>'+affiliates[key].name+'</td>' +
							'<td>'+affiliates[key].account_status+'</td>' +
							'<td>'+affiliates[key].clicks+'</td>' +
							'<td>'+affiliates[key].conversions+'</td>' +
							'<td>'+parseFloat(affiliates[key].conversion_percentage).toFixed(2)+' %</td>' +
							'<td>$ '+parseFloat(affiliates[key].epc).toFixed(2)+'</td>' +
							'<td>$ '+parseFloat(affiliates[key].revenue).toFixed(2)+'</td>' +
							'</tr>'
						);
				    }
				}
				jQuery('#affiliateTable').on('init.dt', function(){
					jQuery('#tableLoader').hide();
					jQuery('#affiliateTable').fadeIn();
				})
				initAffiliateDataTable();
			})
		})
	    			
	    // DataTables Length to Select2
	    jQuery('div.dataTables_length select').removeClass('form-control input-sm');
	    jQuery('div.dataTables_length select').css({width: '60px'});
	    jQuery('div.dataTables_length select').select2({minimumResultsForSearch: -1});
	    	    	    
	    jQuery('#affiliateTable').on('click','tr.open-campaigns',function(){
	    	// Set the objects
	    	var tr = jQuery(this).closest('tr');
	    	var trid = jQuery(this).data('id');
	    	var affiliate = {
	    			'name':jQuery(this).data('name'),
	    			'address': jQuery(this).data('address'),
	    			'city': jQuery(this).data('city'),
	    			'acctmgr': jQuery(this).data('acctmgr'),
	    			'url': jQuery(this).data('url'),
	    	}
			var campaigns = new Array();
			var campaign = {};
			var campaign_summary = [];
			var row_status = 0;
	    	
			// If the row is visible then hide it
	    	if(jQuery(this).next('tr.campaignrow').is(':visible')){
	    		row_status = 0;
	    		jQuery(this).css('font-weight', 'normal');
	    		jQuery(this).nextUntil('tr.open-campaigns').remove();
	    		jQuery(this).find('span:first').removeClass().addClass('fa fa-arrow-right');
	    		
	    		// Close details panel
	    		/*
	    		if(!jQuery('.panel .panel-minimize').hasClass('maximize')){
	    			jQuery('.panel .panel-minimize').trigger( "click" );
	    			jQuery('#overview-title').fadeOut(function(){
	    				jQuery(this).html('Details');
	    				jQuery(this).fadeIn();
	    			});
	    		}*/
	    	}else{
	    		row_status = 1;
	    		jQuery(this).css('font-weight', 'bold');
	    		jQuery(this).find('span:first').removeClass().addClass('fa fa-arrow-down');
		    	jQuery(getRowLoader(jQuery(this).data('id'))).insertAfter(this);
	    		jQuery.when(
	    			// Get a list of campaigns for this affiliate
		    		getCampaigns(trid).done(function(result){
	    			for (i = 0; i < result.data.row_count; i++) { 
	    				if(result.data.row_count<2){
	    					campaign = new Object();
	    					campaign.campaign_id = result.data.campaigns.campaign.campaign_id;
	    					campaign.offer_id = result.data.campaigns.campaign.offer.offer_id;
	    					campaign.offer_name = result.data.campaigns.campaign.offer.offer_name;
	    					campaign.price_format = result.data.campaigns.campaign.offer_contract.price_format.price_format_name
	    					campaign.clicks = 0;
	    					campaign.conversions = '0';
	    					campaign.conversion_percentage = '0.00';
	    					campaign.revenue = '0.00';
	    					campaign.epc = '0.00';
	    					campaigns[campaign.campaign_id]=campaign;
	    				}else{
	    					campaign = new Object();
	    					campaign.campaign_id = result.data.campaigns.campaign[i].campaign_id;
	    					campaign.offer_id = result.data.campaigns.campaign[i].offer.offer_id;
	    					campaign.offer_name = result.data.campaigns.campaign[i].offer.offer_name;
	    					campaign.price_format = result.data.campaigns.campaign[i].offer_contract.price_format.price_format_name
	    					campaign.clicks = 0;
	    					campaign.conversions = '0';
	    					campaign.conversion_percentage = '0.00';
	    					campaign.revenue = '0.00';
	    					campaign.epc = '0.00';
	    					campaigns[result.data.campaigns.campaign[i].campaign_id]=campaign;
	    				}
					}
		    	})
		    	).then(function(){
		    		jQuery.when(
		    			// Get the summary data for the campaigns
		    			getCampaignSummary(trid,0,0,window.report_dates.monthstart,window.report_dates.tomorrow).done(function(result){
		    				for (i = 0; i < result.data.row_count; i++){  		
		    				if(result.data.row_count<2){
		    					camp = result.data.campaigns.campaign_summary; //single
		    					if(typeof campaigns[camp.campaign.campaign_id] != 'undefined') {
	    							tmp = campaigns[camp.campaign.campaign_id];
	    							tmp.clicks = camp.clicks;
	    							tmp.conversions = camp.conversions;
	    							tmp.conversion_percentage = (camp.conversions/camp.clicks)*100;
	    							tmp.revenue = camp.revenue;
	    							tmp.epc = camp.revenue/camp.clicks;
	    							if(tmp.epc == Infinity) {tmp.epc = '0.00';}
	    							campaigns[camp.campaign.campaign_id]=tmp;
	    							
		    					}
		    				}else{
		    					camp = result.data.campaigns.campaign_summary[i]; //multi
		    					if(typeof campaigns[camp.campaign.campaign_id] != 'undefined') {
	    							tmp = campaigns[camp.campaign.campaign_id];
	    							tmp.clicks = camp.clicks;
	    							tmp.conversions = camp.conversions;
	    							tmp.conversion_percentage = (camp.conversions/camp.clicks)*100;
	    							tmp.revenue = camp.revenue;
	    							tmp.epc = camp.revenue/camp.clicks;
	    							if(tmp.epc == Infinity) {tmp.epc = '0.00';}
	    							campaigns[camp.campaign.campaign_id]=tmp;
	    						}
		    				}
    					}
	    				})
		    		).then(function(){
		    			/*
		    			// Build Overview Data
		    			jQuery('#overview-title').fadeOut(function(){
		    				jQuery('#overview-title').html('Details - '+affiliate.name);
		    				jQuery('#overview-name').html('<strong>'+affiliate.name+'</strong>');
				    		jQuery('#overview-address').html(affiliate.address);
				    		jQuery('#overview-city').html(affiliate.city);
				    		jQuery('#overview-url').html('Website: <a href="'+affiliate.url+'" target="_blank">'+affiliate.url+'</a>');
				    		jQuery('#overview-acctmgr').html('Account Manager: <strong>'+affiliate.acctmgr+'</strong>');
		    			});
			    		jQuery('#overview-title').fadeIn(function(){
			    			// Open or update details panel
				    		if(jQuery('.panel .panel-minimize').hasClass('maximize')){
				    			jQuery('.panel .panel-minimize').trigger( "click" );
				    		}
				    	
			    		});
			   			*/    		
		    			jQuery('#loader_'+trid).remove();
		    			if(campaigns.length > 0){
		    				for (key in campaigns) {
								newrow = getCampaignRow(campaigns[key]);
								jQuery(''+newrow+'').insertAfter(tr);
							}
		    			}else{
		    				jQuery(tr).css('font-weight', 'normal');
		    	    		jQuery(tr).nextUntil('tr.open-campaigns').remove();
		    	    		jQuery(tr).find('span:first').removeClass().addClass('fa fa-arrow-right');
		    			}
		    		})
		    	});
	    	}
	    });
	}
	
	if(jQuery('#vendorTable').length>0){
		initVendorDataTable();
		jQuery("#select-affiliate").select2();
		
		jQuery(".open-vendor").on('click',function(){
			var vendor_id = jQuery(this).data('vendor-id');
			jQuery(".vendor_"+vendor_id).toggle();
			
			if(jQuery(".vendor_"+vendor_id).is(':visible')){
				jQuery(this).css('font-weight', 'bold');
	    		jQuery(this).find('span:first').removeClass().addClass('fa fa-arrow-down');
			}else{
				jQuery(this).css('font-weight', 'normal');
	    		jQuery(this).find('span:first').removeClass().addClass('fa fa-arrow-right');
			}
		});
		
		jQuery('.vendorDomainDeleteOpen').on('click',function(){
			jQuery('#vendorDomainDelete').attr('data-domain-id', jQuery(this).data('domain-id'));
		});
		
		jQuery('#vendorDomainDelete').on('click',function(){
			var response = deleteDomain(jQuery(this).data('domain-id'));
			if(response.status == true){
				jQuery('td[data-domain-id="'+jQuery(this).data("domain-id")+'"]').parent().remove();
			}
			jQuery('.confirm-delete-domain').modal('toggle');
		});
		
		jQuery('.vendorIpDeleteOpen').on('click',function(){
			jQuery('#vendorIpDelete').attr('data-ip-id', jQuery(this).data('ip-id'));
		});
		
		jQuery('#vendorIpDelete').on('click',function(){
			var response = deleteIp(jQuery(this).data('ip-id'));
			if(response.status == true){
				jQuery('td[data-ip-id="'+jQuery(this).data("ip-id")+'"]').parent().remove();
			}
			jQuery('.confirm-delete-ip').modal('toggle');
		});
	}
});

/**
 * Display the campaign/offer sub row while we get sub row data.
 * @returns String
 */
function getCampaignRow(data){
	var priceformat = '';
	if(data.price_format == 'CPA'){priceformat='<a href="/buckets">'+data.price_format+'</a>';}else{priceformat=data.price_format;}
    return '<tr class="campaignrow subrow">' +
    '<td><span style="color:darkgray;  margin-left:15px;" class="fa fa-arrow-right"></span></td>' +
    '<td>'+data.campaign_id+'-'+data.offer_id+'</td>' +
    '<td>'+data.offer_name+'</td>' +
    '<td>'+priceformat+'</td>' +
    '<td>'+data.clicks+'</td>' +
	'<td>'+data.conversions+'</td>' +
	'<td>'+parseFloat(data.conversion_percentage).toFixed(2)+' %</td>' +
	'<td>$ '+parseFloat(data.epc).toFixed(2)+'</td>' +
	'<td>$ '+parseFloat(data.revenue).toFixed(2)+'</td>' +
    '</tr>';
}

/**
 * Add the loader while we wait for data to be returned.
 * @returns {String}
 */
function getRowLoader(id){
	return '<tr id="loader_'+id+'">' +
    '<td text-align:center; vertical-align:middle;" colspan="9">' +
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
		url:'/cake/exportaffiliate',
		headers:{'x-keyStone-nonce': nonce}
	});
}

/**
 * Retrieve a list of summary data for all affiliates
 * @returns
 */
function getAffiliateSummary(affiliate_id,start_date,end_date){
	if(typeof affiliate_id === 'undefined'){affiliate_id=0;}
	if(typeof start_date === 'undefined'){start_date=0;}
	if(typeof end_date === 'undefined'){end_date=0;}
	return $.ajax({
		url:'/cake/affiliatesummary/'+affiliate_id+'/'+start_date+'/'+end_date,
		headers:{'x-keyStone-nonce':nonce}
	});
}

/**
 * Retrieve a list of campaigns for the given affiliate id
 * @param int $affiliate_id
 * @returns json
 */
function getCampaigns(affiliate_id){
	return $.ajax({
		url:'/cake/exportcampaign/0/0/'+affiliate_id,
		headers:{'x-keyStone-nonce': nonce}
	});
}

/**
 * Retrieve summary about a specific campaign/offer
 * @param affiliate_id
 * @param campaign_id
 * @param offer_id
 * @returns json
 */
function getCampaignSummary(affiliate_id,campaign_id,offer_id,start_date,end_date){
	if(typeof affiliate_id === 'undefined'){affiliate_id=0;}
	if(typeof campaign_id === 'undefined'){campaign_id=0;}
	if(typeof offer_id === 'undefined'){offer_id=0;}
	if(typeof start_date === 'undefined'){start_date=0;}
	if(typeof end_date === 'undefined'){end_date=0;}
	
	return $.ajax({
		url:'/cake/campaignsummary/'+affiliate_id+'/'+campaign_id+'/'+offer_id+'/'+start_date+'/'+end_date,
		headers:{'x-keyStone-nonce': nonce}
	});
}

function deleteDomain(id){
	var result = $.ajax({
		url:'/affiliates/delete_domains/'+id,
		dataType:'json',
		headers:{'x-keyStone-nonce': nonce},
		async:false,
	});
	return jQuery.parseJSON(result.responseText);
} 

function deleteIp(id){
	var result = $.ajax({
		url:'/affiliates/delete_ip/'+id,
		dataType:'json',
		headers:{'x-keyStone-nonce': nonce},
		async:false,
	});
	return jQuery.parseJSON(result.responseText);
} 

/**
 * Initialise the affiliate data table
 */
function initAffiliateDataTable() {	
	affiliate_table = jQuery('#affiliateTable').dataTable({
			'iDisplayLength': 50,
			'bLengthChange' : false,
			'stateSave': false,
			'columns': [{
                'orderable':false,
				},
				null,
				null,
				null,
				null,
				null,
				null,
				null,
				null]
		});
}

/**
 * Initialise the vendor data table
 */
function initVendorDataTable() {	
	affiliate_table = jQuery('#vendorTable').dataTable({
			'iDisplayLength': 1000,
			'bLengthChange' : false,
			'stateSave': false,
			'columns': [
			    {'orderable':false,},
				{'orderable':false,},
				{'orderable':false,},
				{'orderable':false,},
				{'orderable':false,},
				{'orderable':false,},
				{'orderable':false,}]
		});
}