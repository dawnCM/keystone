// Global to the DOM
var lead_table;
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
	
	jQuery('#startdate').datepicker({dateFormat: "mm/dd/yy"});
	jQuery('#enddate').datepicker({dateFormat: "mm/dd/yy"});
	
	jQuery('#startdate').datepicker("setDate", moment().format('MM/DD/YYYY'));
	jQuery('#enddate').datepicker("setDate", moment().add(1,'days').format('MM/DD/YYYY'));
		
	jQuery("#startdate").mask("99/99/9999");
	jQuery("#enddate").mask("99/99/9999");
    jQuery("#phone").mask("(999) 999-9999");
    
    jQuery("#select-affiliate").select2();
    jQuery("#state").select2();
    
    jQuery("#select-offer").select2();
    jQuery("#state").select2();
    
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
	
	jQuery('#pmonth-date').on('click',function(){
		jQuery('#startdate').datepicker("setDate", moment().subtract(1,'months').startOf('month').format('MM/DD/YYYY'));
		jQuery('#enddate').datepicker("setDate", moment().subtract(1,'months').endOf('month').add(1,'days').format('MM/DD/YYYY'));
	});
   
    //When table pagination is used, reattach hover nodes on dynamic content.
    jQuery('#leadTable').on( 'page.dt', function () {
		 jQuery("span[rel=hoverovers]", lead_table.fnGetNodes()).popover({ trigger: "hover" }); 
		 
	});
    
    if(jQuery('#leadTable').length>0){
		var leads = [];
		var lead = {};
		jQuery.when(
				leadQuery(window.report_dates.today, window.report_dates.tomorrow, '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-','-','-').done(function(result){
				for (i = 0; i < result.data.length; i++) { 
					if(result.data[i].ReportTrack.lead_data == undefined){result.data[i].ReportTrack.lead_data = new Object();}
					lead = new Object();
					lead.offer_id = result.data[i].ReportTrack.offer_id;
					lead.affiliate_id = result.data[i].ReportTrack.affiliate_id;
					lead.track_id = result.data[i].ReportTrack.track_id;
					lead.date = moment.unix(result.data[i].ReportTrack.lead_created.sec).format("MM/DD/YYYY h:mm a");
			
					if(result.data[i].ReportTrack.lead_data != undefined && result.data[i].ReportTrack.lead_data.receivableamount > 0){
						lead.sold = 'True';
					}else{
						lead.sold = 'False';
					}
					
					lead.iserror = (result.data[i].ReportTrack.lead_data.errors != undefined ? 1 : 0);
					lead.isaltered = (result.data[i].ReportTrack.lead_data.altered != undefined ? 1 : 0);
								
					if(lead.sold == 'True'){
						lead.receivable = result.data[i].ReportTrack.lead_data.receivableamount;
						lead.paid = result.data[i].ReportTrack.lead_data.paidamount;
						lead.margin = result.data[i].ReportTrack.lead_data.marginamount;
						lead.percentage = result.data[i].ReportTrack.lead_data.margin;
					}
					
					lead.istest = 0;
					pattern = /test/gi;
					if(pattern.test(result.data[i].ReportTrack.lead_data.firstname)){ lead.istest=1; }
					
					leads[lead.track_id] = lead;
				}
	    	})
		).then(function(){
			initDataTable();
			table = jQuery('#leadTable').DataTable();
			table.clear();
			for (key in leads) {
			    if (leads.hasOwnProperty(key) && /^0$|^[1-9]\d*$/.test(key) && key <= 4294967294) {
			    	rowhover = (leads[key].sold == 'True') ? 'rel="hoverovers"' : '';
			    	
			    	rowNode = table.row.add([
			    	'<span id="'+leads[key].offer_id+'">'+leads[key].offer_id+'</span>',
			    	'<span id="'+leads[key].affiliate_id+'">'+jQuery('#select-affiliate option[value="'+leads[key].affiliate_id+'"]').text()+'</span>',
			    	'<a href="/leads/detail/'+leads[key].track_id+'">'+leads[key].track_id+'</a>',
			    	leads[key].date,
			    	'<span '+rowhover+' data-container="body" data-toggle="popover" data-html="true" data-placement="top" data-content="<strong>Receivable:</strong> $'+parseFloat(Math.round(leads[key].receivable*100)/100).toFixed(2)+'<br><strong>Margin Amount:</strong> $'+parseFloat(Math.round(leads[key].margin*100)/100).toFixed(2)+'">'+leads[key].sold+'</span>'
			    	])
			    	.node();
			    	
			    	$( rowNode ).attr('trackid', leads[key].track_id);
			    	
			    	if(leads[key].istest==1){
			    		$( rowNode ).removeClass('odd');
			    		$( rowNode ).removeClass('even');
			    		$( rowNode ).addClass('alert-warning');
			    	};
			    	
			    	if(leads[key].iserror==1){
			    		$( rowNode ).removeClass('odd');
			    		$( rowNode ).removeClass('even');
			    		$( rowNode ).addClass('alert-danger');
			    	};
			    	
			    	if(leads[key].isaltered==1){
			    		$( rowNode ).removeClass('odd');
			    		$( rowNode ).removeClass('even');
			    		$( rowNode ).addClass('alert-info');
			    	}
			    }
			}
			table.draw();
			jQuery("span[rel=hoverovers]", lead_table.fnGetNodes()).popover({ trigger: "hover" });			
			jQuery('#tableLoader').hide();
			jQuery('#leadTable').fadeIn();
		});
		
		jQuery('#export-leads').on('click',function(){
			var start = moment(new Date(jQuery('#startdate').val())).format('YYYY-MM-DD');
			var end = moment(new Date(jQuery('#enddate').val())).format('YYYY-MM-DD');
			var first = (jQuery('#firstname').val() == '' ? '-' : jQuery('#firstname').val());
			var last = (jQuery('#lastname').val() == '' ? '-' : jQuery('#lastname').val());
			var email = (jQuery('#email').val() == '' ? '-' : jQuery('#email').val());
			var phone = (jQuery('#phone').val() == '' ? '-' : jQuery('#phone').val());
			var city = (jQuery('#city').val() == '' ? '-' : jQuery('#city').val());
			var state = (jQuery('#state').val() == '' ? '-' : jQuery('#state').val());
			var zip = (jQuery('#zip').val() == '' ? '-' : jQuery('#zip').val());
			var ip = (jQuery('#ip').val() == '' ? '-' : jQuery('#ip').val());
			var mobile = (jQuery('#mobile').val() == '' ? '-' : jQuery('#mobile').val());
			var military = (jQuery('#military').val() == '' ? '-' : jQuery('#military').val());
			var redirect = (jQuery('#redirect').attr("checked") ? '1' : '-');
			var sold = (jQuery('#sold').attr("checked") ? '1' : '-');
			
			var affiliate_id = (jQuery('#select-affiliate').val() == '' ? '-' : jQuery('#select-affiliate').val());
			document.location.href = '/leads/export/'+start+'/'+end+'/'+first+'/'+last+'/'+email+'/'+phone+'/'+city+'/'+state+'/'+zip+'/'+mobile+'/'+military+'/'+affiliate_id+'/'+ip+'/'+redirect+'/'+sold;
		});
	}
	
	//Leads->Leads
	jQuery('#search-leads').on('click',function(){
		jQuery('#leadTable').hide();
		jQuery('#tableLoader').fadeIn();
		
		var leads = [];
		var lead = {};
		var start = moment(new Date(jQuery('#startdate').val())).format('YYYY-MM-DD');
		var end = moment(new Date(jQuery('#enddate').val())).format('YYYY-MM-DD');
		var first = (jQuery('#firstname').val() == '' ? '-' : jQuery('#firstname').val());
		var last = (jQuery('#lastname').val() == '' ? '-' : jQuery('#lastname').val());
		var email = (jQuery('#email').val() == '' ? '-' : jQuery('#email').val());
		var phone = (jQuery('#phone').val() == '' ? '-' : jQuery('#phone').val());
		var city = (jQuery('#city').val() == '' ? '-' : jQuery('#city').val());
		var state = (jQuery('#state').val() == '' ? '-' : jQuery('#state').val());
		var zip = (jQuery('#zip').val() == '' ? '-' : jQuery('#zip').val());
		var ip = (jQuery('#ip').val() == '' ? '-' : jQuery('#ip').val());
		var mobile = (jQuery('#mobile').val() == '' ? '-' : jQuery('#mobile').val());
		var military = (jQuery('#military').val() == '' ? '-' : jQuery('#military').val());
		
		//var redirect = (jQuery('#redirect').val() == '' ? '-' : jQuery('#redirect').val());
		var redirect = (jQuery('#redirect').attr("checked") ? '1' : '-');
		var sold = (jQuery('#sold').attr("checked") ? '1' : '-');
		var altered = (jQuery('#altered').attr("checked") ? '1' : '-');
		
		var affiliate_id = (jQuery('#select-affiliate').val() == '' ? '-' : jQuery('#select-affiliate').val());
		jQuery.when(
				leadQuery(start, end, first, last, email, phone, city, state, zip, mobile, military, affiliate_id, ip, redirect, sold, altered).done(function(result){
				for (i = 0; i < result.data.length; i++) { 	
					lead = new Object();
					lead.offer_id = result.data[i].ReportTrack.offer_id;
					lead.affiliate_id = result.data[i].ReportTrack.affiliate_id;
					lead.track_id = result.data[i].ReportTrack.track_id;
					lead.date = moment.unix(result.data[i].ReportTrack.lead_created.sec).format("MM/DD/YYYY h:mm a");
					
					if(result.data[i].ReportTrack.lead_data.receivableamount != undefined && result.data[i].ReportTrack.lead_data.receivableamount > 0){
						lead.sold = 'True';
					}else{
						lead.sold = 'False';
					}
					
					if(lead.sold == 'True'){
						lead.receivable = result.data[i].ReportTrack.lead_data.receivableamount;
						lead.paid = result.data[i].ReportTrack.lead_data.paidamount;
						lead.margin = result.data[i].ReportTrack.lead_data.marginamount;
						lead.percentage = result.data[i].ReportTrack.lead_data.margin;
					}
					
					lead.iserror = (result.data[i].ReportTrack.lead_data.errors != undefined ? 1 : 0);
					lead.isaltered = (result.data[i].ReportTrack.lead_data.altered != undefined ? 1 : 0);
					lead.istest = 0;
					
					pattern = /test/gi;
					if(pattern.test(result.data[i].ReportTrack.lead_data.firstname)){ lead.istest=1; }
					
					leads[lead.track_id] = lead;
				}
	    	})
		).then(function(){
			table = jQuery('#leadTable').DataTable();
			table.clear();
			for (key in leads) {
			    if (leads.hasOwnProperty(key) && /^0$|^[1-9]\d*$/.test(key) && key <= 4294967294) {
			    	rowhover = (leads[key].sold == 'True') ? 'rel="hoverovers"' : '';
			    	
			    	rowNode = table.row.add([
			    	'<span id="'+leads[key].offer_id+'">'+leads[key].offer_id+'</span>',
			    	'<span id="'+leads[key].affiliate_id+'">'+jQuery('#select-affiliate option[value="'+leads[key].affiliate_id+'"]').text()+'</span>',
			    	'<a href="/leads/detail/'+leads[key].track_id+'">'+leads[key].track_id+'</a>',
			    	leads[key].date,
			    	'<span '+rowhover+' data-container="body" data-toggle="popover" data-html="true" data-placement="top" data-content="<strong>Receivable:</strong> $'+parseFloat(Math.round(leads[key].receivable*100)/100).toFixed(2)+'<br><strong>Margin Amount:</strong> $'+parseFloat(Math.round(leads[key].margin*100)/100).toFixed(2)+'">'+leads[key].sold+'</span>'
			    	])
			    	.node();
			    	
			    	$( rowNode ).attr('trackid', leads[key].track_id);
			    	
			    	if(leads[key].istest==1){
			    		$( rowNode ).removeClass('odd');
			    		$( rowNode ).removeClass('even');
			    		$( rowNode ).addClass('alert-warning');
			    	}
			    	
			    	if(leads[key].iserror==1){
			    		$( rowNode ).removeClass('odd');
			    		$( rowNode ).removeClass('even');
			    		$( rowNode ).addClass('alert-danger');
			    	};
			    	
			    	if(leads[key].isaltered==1){
			    		$( rowNode ).removeClass('odd');
			    		$( rowNode ).removeClass('even');
			    		$( rowNode ).addClass('alert-info');
			    	};
			    }
			}
			table.draw();
			jQuery("span[rel=hoverovers]", lead_table.fnGetNodes()).popover({ trigger: "hover" });
			jQuery('#tableLoader').hide();
			jQuery('#leadTable').fadeIn();	
		});
	});
	
	if(jQuery('#leadDetail').length>0){
		jQuery('#change_receivable').on('click',function(e){
			e.preventDefault();
			jQuery('#change_payable_field').hide();
			jQuery('#change_receivable_field').toggle();
			jQuery('#lead_receivable').focus();
			window.receivable_prev = jQuery('#lead_receivable').val();
		});
		
		//exit receivable field, check the value and update as needed
		jQuery('#lead_receivable').blur(function(){
			var receivable_current = jQuery('#lead_receivable').val();
			if(isNaN(receivable_current)){
				jQuery(this).addClass('alert-danger');
			}else{
				jQuery(this).removeClass('alert-danger');
				
				if(window.receivable_prev != receivable_current){
					var lead_id = 	jQuery('#lead_receivable').data('lead-id');
					var offer_id =  jQuery('#lead_receivable').data('offer-id');
					var track_id =  jQuery('#lead_receivable').data('track-id');
					var vertical_id = jQuery('#lead_receivable').data('vertical_id');
					alert('Feature Offline');
					//window.location = '/leads/updateleadreceivable/'+track_id+'/'+vertical_id+'/'+lead_id+'/'+$amount;
				}
				
				jQuery('#change_receivable_field').hide();
			}
		});
		
		//show/hide payable change
		jQuery('#change_payable').on('click',function(e){
			e.preventDefault();
			jQuery('#change_receivable_field').hide();
			jQuery('#change_payable_field').toggle();
			jQuery('#lead_payable').focus();
			window.payable_prev = jQuery('#lead_payable').val();
		});
				
		//exit payable field, check the value and update as needed
		jQuery('#lead_payable').blur(function(){
			var payable_current = jQuery('#lead_payable').val();
			if(isNaN(payable_current)){
				jQuery(this).addClass('alert-danger');
			}else{
				jQuery(this).removeClass('alert-danger');
				
				if(window.payable_prev != payable_current){
					var lead_id = 	jQuery('#lead_payable').data('lead-id');
					var offer_id =  jQuery('#lead_payable').data('offer-id');
					var track_id =  jQuery('#lead_payable').data('track-id');
					var vertical_id = jQuery('#lead_payable').data('vertical_id');
					alert('Feature Offline');
					//window.location = '/leads/updateleadpayable/'+track_id+'/'+vertical_id+'/'+lead_id+'/'+$amount;
				}
				
				jQuery('#change_payable_field').hide();
			}
		});
						
		//open reject modal
		jQuery('#reject_lead').on('click',function(){
			jQuery('.confirm-reject').modal('toggle');
		});
		
		//confirm reject
		jQuery('#leadConfirmReject').on('click',function(){
			var lead_id = 	jQuery(this).data('lead-id');
			var offer_id =  jQuery(this).data('offer-id');
			var track_id =  jQuery(this).data('track-id');
			window.location = '/leads/rejectlead/'+lead_id+'/'+offer_id+'/'+track_id;
		});
	}
});

/**
 * Ajax call to query mongo by the criteria set.
 */
function leadQuery(start, end, first, last, email, phone, city, state, zip, mobile, military, affiliate_id, ip, redirect, sold, altered){
	return $.ajax({
		url:'/leads/leadQuery/'+start+'/'+end+'/'+first+'/'+last+'/'+email+'/'+phone+'/'+city+'/'+state+'/'+zip+'/'+mobile+'/'+military+'/'+affiliate_id+'/'+ip+'/'+redirect+'/'+sold+'/'+altered,
		headers:{'x-keyStone-nonce': nonce}
	});
}

function getOfferDetails(id){
	return $.ajax({
		url:'/cake/exportoffer/'+id,
		headers:{'x-keyStone-nonce': nonce}
	});
}

function getLeadDetail(id){
	return $.ajax({
		url:'/leads/detail/'+id,
		headers:{'x-keyStone-nonce': nonce}
	});
}

/**
 * Initialise the data table
 */
function initDataTable() {	
	lead_table = jQuery('#leadTable').dataTable({
		'searching': false,
		'iDisplayLength': 10,
		'lengthChange': false,
		'order':[[3,"desc"]]
	});
}