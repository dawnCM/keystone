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
	jQuery('#startdate').datepicker({dateFormat: "mm/dd/yy"});
	jQuery('#enddate').datepicker({dateFormat: "mm/dd/yy"});
	
	jQuery('#startdate').datepicker("setDate", moment().format('MM/DD/YYYY'));
	jQuery('#enddate').datepicker("setDate", moment().add(1,'days').format('MM/DD/YYYY'));
		
	jQuery("#startdate").mask("99/99/9999");
	jQuery("#enddate").mask("99/99/9999");
       
    jQuery("#select-affiliate").select2();
      
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
	
	jQuery('#select-affiliate').on('change', function(){
		if(jQuery('#select-affiliate').val() != ''){
			jQuery('#subid_fields').slideDown();
		}else{
			jQuery('#subid_fields').slideUp();
			jQuery('#subid1').val('');
			jQuery('#subid2').val('');
			jQuery('#subid3').val('');
			jQuery('#subid4').val('');
		}
	})
	//Fraud -> IP
	if(jQuery('#ipTable').length>0){
		jQuery('#remove-ip').on('click',function(){
			jQuery('#ipaction').val('whitelist');
		});
		
		jQuery('#add-ip').on('click',function(){
			jQuery('#ipaction').val('blacklist');
		});
		
		initDataTable();
	}
	
	//Fraud -> Lead Time
	if(jQuery('#leadtimeTable').length>0){
		var leads = [];
		var lead = {};
		jQuery.when(
				leadTime(window.report_dates.today, window.report_dates.tomorrow, '-', '-', '-', '-', '-').done(function(result){
				for (i = 0; i < result.data.length; i++) { 
					if(result.data[i].ReportTrack.lead_data == undefined){result.data[i].ReportTrack.lead_data = new Object();}
					lead = new Object();
					lead.offer_id = result.data[i].ReportTrack.offer_id;
					lead.affiliate_id = result.data[i].ReportTrack.affiliate_id;
					lead.track_id = result.data[i].ReportTrack.track_id;
					lead.lead_id = result.data[i].ReportTrack.lead_id;
					lead.date = moment.unix(result.data[i].ReportTrack.lead_created.sec).format("MM/DD/YYYY h:mm a");
					lead.time = result.data[i].ReportTrack.time;
					lead.email = result.data[i].ReportTrack.lead_data.email;
					lead.ipaddress = result.data[i].ReportTrack.lead_data.ipaddress;
					lead.payable = result.data[i].ReportTrack.lead_data.paidamount;
					lead.receivable = result.data[i].ReportTrack.lead_data.receivableamount;
					lead.sub1 = result.data[i].ReportTrack.subid[1];
					lead.sub2 = result.data[i].ReportTrack.subid[2];
					lead.sub3 = result.data[i].ReportTrack.subid[3];
					lead.sub4 = result.data[i].ReportTrack.subid[4];
					
					lead.istest = 0;
					pattern = /test/gi;
					if(pattern.test(result.data[i].ReportTrack.lead_data.firstname)){ lead.istest=1; }
					
					leads[lead.track_id] = lead;
				}
	    	})
		).then(function(){
			initDataTable();
			table = jQuery('#leadtimeTable').DataTable();
			table.clear();
			for (key in leads) {
			    if (leads.hasOwnProperty(key) && /^0$|^[1-9]\d*$/.test(key) && key <= 4294967294) {		    	
			    	rowNode = table.row.add([
			    	'<span id="'+leads[key].affiliate_id+'">'+leads[key].affiliate_id+'</span>',
			    	'<span id="'+leads[key].lead_id+'"><a href="/leads/detail/'+leads[key].track_id+'">'+leads[key].lead_id+'</a></span>',
			    	leads[key].offer_id,
			    	leads[key].sub1,
			    	leads[key].sub2,
			    	leads[key].sub3,
			    	leads[key].sub4,
			    	'$'+parseFloat(Math.round(leads[key].receivable*100)/100).toFixed(2)+' | $'+parseFloat(Math.round(leads[key].payable*100)/100).toFixed(2),
			    	leads[key].time,
			    	leads[key].date
			    	])
			    	.node();
			    	
			    	$( rowNode ).attr('trackid', leads[key].track_id);
			    	
			    	if(leads[key].istest==1){
			    		$( rowNode ).removeClass('odd');
			    		$( rowNode ).removeClass('even');
			    		$( rowNode ).addClass('alert-warning');
			    	};
			    }
			}
			table.draw();	
			jQuery('#tableLoader').hide();
			jQuery('#leadtimeTable').fadeIn();
		});
	}
	
	//Fraud -> Export Lead Time
	jQuery('#export-leads').on('click',function(){
		var start = moment(new Date(jQuery('#startdate').val())).format('YYYY-MM-DD');
		var end = moment(new Date(jQuery('#enddate').val())).format('YYYY-MM-DD');
		var affiliate_id = jQuery('#select-affiliate').val();
		var subid1 = (jQuery('#subid1').val() == '' ? '-' : jQuery('#subid1').val());
		var subid2 = (jQuery('#subid2').val() == '' ? '-' : jQuery('#subid2').val());
		var subid3 = (jQuery('#subid3').val() == '' ? '-' : jQuery('#subid3').val());
		var subid4 = (jQuery('#subid4').val() == '' ? '-' : jQuery('#subid4').val());
		
		document.location.href = '/fraud/export/'+start+'/'+end+'/'+affiliate_id+'/'+subid1+'/'+subid2+'/'+subid3+'/'+subid4;
	});
	
	//Fraud -> Search Lead Time
	jQuery('#search-leads').on('click',function(){
		if(jQuery('#select-affiliate').val() == ''){
			jQuery('.missing-affiliate').modal('toggle');
			return;
		}
		
		jQuery('#leadtimeTable').hide();
		jQuery('#tableLoader').fadeIn();
		
		var leads = [];
		var lead = {};
		
		var start = moment(new Date(jQuery('#startdate').val())).format('YYYY-MM-DD');
		var end = moment(new Date(jQuery('#enddate').val())).format('YYYY-MM-DD');
		var affiliate_id = jQuery('#select-affiliate').val();
		var subid1 = (jQuery('#subid1').val() == '' ? '-' : jQuery('#subid1').val());
		var subid2 = (jQuery('#subid2').val() == '' ? '-' : jQuery('#subid2').val());
		var subid3 = (jQuery('#subid3').val() == '' ? '-' : jQuery('#subid3').val());
		var subid4 = (jQuery('#subid4').val() == '' ? '-' : jQuery('#subid4').val());
		
		jQuery.when(
				leadTime(start, end, affiliate_id, subid1, subid2, subid3, subid4).done(function(result){
				for (i = 0; i < result.data.length; i++) { 
					if(result.data[i].ReportTrack.lead_data == undefined){result.data[i].ReportTrack.lead_data = new Object();}
					lead = new Object();
					lead.offer_id = result.data[i].ReportTrack.offer_id;
					lead.affiliate_id = result.data[i].ReportTrack.affiliate_id;
					lead.track_id = result.data[i].ReportTrack.track_id;
					lead.lead_id = result.data[i].ReportTrack.lead_id;
					lead.date = moment.unix(result.data[i].ReportTrack.lead_created.sec).format("MM/DD/YYYY h:mm a");
					lead.time = result.data[i].ReportTrack.time;
					lead.email = result.data[i].ReportTrack.lead_data.email;
					lead.ipaddress = result.data[i].ReportTrack.lead_data.ipaddress;
					lead.payable = result.data[i].ReportTrack.lead_data.paidamount;
					lead.receivable = result.data[i].ReportTrack.lead_data.receivableamount;
					lead.sub1 = result.data[i].ReportTrack.subid[1];
					lead.sub2 = result.data[i].ReportTrack.subid[2];
					lead.sub3 = result.data[i].ReportTrack.subid[3];
					lead.sub4 = result.data[i].ReportTrack.subid[4];
					
					lead.istest = 0;
					pattern = /test/gi;
					if(pattern.test(result.data[i].ReportTrack.lead_data.firstname)){ lead.istest=1; }
					
					leads[lead.track_id] = lead;
				}
	    	})
		).then(function(){
			table = jQuery('#leadtimeTable').DataTable();
			table.clear();
			for (key in leads) {
			    if (leads.hasOwnProperty(key) && /^0$|^[1-9]\d*$/.test(key) && key <= 4294967294) {		    	
			    	rowNode = table.row.add([
			    	'<span id="'+leads[key].affiliate_id+'">'+leads[key].affiliate_id+'</span>',
			    	'<span id="'+leads[key].lead_id+'"><a href="/leads/detail/'+leads[key].track_id+'">'+leads[key].lead_id+'</a></span>',
			    	leads[key].offer_id,
			    	leads[key].sub1,
			    	leads[key].sub2,
			    	leads[key].sub3,
			    	leads[key].sub4,
			    	'$'+parseFloat(Math.round(leads[key].receivable*100)/100).toFixed(2)+' | $'+parseFloat(Math.round(leads[key].payable*100)/100).toFixed(2),
			    	leads[key].time,
			    	leads[key].date
			    	])
			    	.node();
			    	
			    	$( rowNode ).attr('trackid', leads[key].track_id);
			    	
			    	if(leads[key].istest==1){
			    		$( rowNode ).removeClass('odd');
			    		$( rowNode ).removeClass('even');
			    		$( rowNode ).addClass('alert-warning');
			    	};
			    }
			}
			table.draw();	
			jQuery('#tableLoader').hide();
			jQuery('#leadtimeTable').fadeIn();
		});
	})
});

/**
 * Initialise the data table
 */
function initDataTable() {	
	lead_table = jQuery('#leadtimeTable').dataTable({
		'searching': false,
		'iDisplayLength': 10,
		'lengthChange': false,
		'order':[[9,"desc"]]
	});
	
	ip_table = jQuery('#ipTable').dataTable({
		'searching': true,
		'iDisplayLength': 100,
		'lengthChange': false,
		'order':[[5,"desc"]]
	});
}

/**
 * Ajax call to query mongo by the criteria set.
 */
function leadTime(start, end, affiliate_id, sub1, sub2, sub3, sub4){
	return $.ajax({
		url:'/fraud/leadTimeQuery/'+start+'/'+end+'/'+affiliate_id+'/'+sub1+'/'+sub2+'/'+sub3+'/'+sub4,
		headers:{'x-keyStone-nonce': nonce}
	});
}


/************  Start Seed COntract  ********************************/

jQuery(document).ready(function(){ 
	jQuery('#select-buyer-testcontractpost').select2();
	jQuery('#select-contract-testcontractpost').select2();
	jQuery('#postdetails-testcontractpost').hide();
	jQuery('#contract-select-group').hide();
	jQuery('#contract-wait-group').hide();
	jQuery('#testcontractpost-mainpanel').hide();
	
	jQuery('#send-testcontractpost').hide();
	jQuery('#clear-testcontractpost').hide();


	//Buyer Dropdown change
    $(document).on("change", "#select-buyer-testcontractpost", function () {
    	var buyer_id = $(this).val();
    	if(buyer_id == "")return;
    	
    	clearTestPost();
	    
	    contractList(buyer_id).done(function(rsp){
			var obj = $.parseJSON(rsp);
			if(obj.status == 'success'){
				
				
				$.each(obj.data, function(index,element){
					$("#select-contract-testcontractpost").append('<option value="'+element[0]+'">'+element[1]+'</option>');
					
				});
			
				$('#contract-wait-group').fadeOut();
				$('#contract-select-group').fadeIn();
				$('#postdetails-testcontractpost').fadeIn();
				$("#select-contract-testcontractpost").val("");	
			}
		});
	});

	
	//Get Post Details
    $(document).on("click", "#postdetails-testcontractpost", function () {
    	var contract_id = jQuery('#select-contract-testcontractpost').val();
    	
    	if(contract_id == "")return;
    	
    	$('#postdetails-testcontractpost').fadeOut();
    	$('#postLoader').fadeIn();
    	
    	contractDetails(contract_id).done(function(rsp){
			var obj = $.parseJSON(rsp);
			if(obj.status == 'success'){
				
				jQuery('#testcontract-filter-div').html('');
				jQuery('#testcontract-filter-div').append('<br>');	
				$.each(obj.data, function(index,element){
					jQuery('#testcontract-filter-div').append('<div class="alert alert-info" style = "word-break:break-all">'+
  																	'<strong>'+element[0]+'</strong><br>'+element[1]+'</div>');	
				});
				
				$('#postLoader').fadeOut()
				jQuery('#testcontractpost-mainpanel').fadeIn();
				jQuery('#send-testcontractpost').fadeIn();
				jQuery('#clear-testcontractpost').fadeIn();
				
				
			}
		});
    	
    });
    
    
    
	$(document).on("click", "#clear-testcontractpost", function () {
		
		clearTestPost();	
		
	});


	$(document).on("click", "#send-testcontractpost", function () {
		jQuery('#results-contract-modal').modal('show');
		var contract_id = $('#select-contract-testcontractpost').val();
		var post_obj = {}; 
		
		jQuery('#testcontract-table-left tbody tr td:first-child').each(function() {
    		post_obj[$(this).text()] = jQuery('#'+$(this).text()).val();
		});
		
		jQuery('#testcontract-table-right tbody tr td:first-child').each(function() {
    		post_obj[$(this).text()] = jQuery('#'+$(this).text()).val();
		});
		
		post_obj['bc'] = contract_id;
		
		processTestContract(post_obj).done(function(rsp){
				jQuery('#results-contract-wait').fadeOut();
				jQuery('#results-contract-response').html(rsp);
				jQuery('#results-contract-response').fadeIn();
				jQuery('#results-contract-done').fadeIn();
		});
		
		
	});

	$(document).on("click", "#results-contract-done", function () {
	
		jQuery('#results-contract-modal').modal('hide');
		jQuery('#results-contract-wait').fadeIn();
		jQuery('#results-contract-response').fadeOut();
		jQuery('#results-contract-done').fadeOut();	
		jQuery('#results-contract-response').html('');
	});





});

//Clear forms and display
function clearTestPost() {
	jQuery('#testcontractpost-mainpanel').fadeOut();
	jQuery('#testcontract-filter-div').html('');
	jQuery('#send-testcontractpost').fadeOut();
	$("#select-contract-testcontractpost option").remove();
	$("#select-contract-testcontractpost").append('<option SELECTED value="">Select A Contract</option>');
	$("#select-contract-testcontractpost").select2("val", "");
	jQuery('#clear-testcontractpost').fadeOut();
	$('#postdetails-testcontractpost').fadeOut();
	$('#contract-select-group').fadeOut();	
	jQuery('#pay_date_1').datepicker({dateFormat: "mm/dd/yy"});
	jQuery('#pay_date_1').datepicker("setDate", moment().add(1,'days').format('MM/DD/YYYY'));
	jQuery('#pay_date_2').datepicker({dateFormat: "mm/dd/yy"});
	jQuery('#pay_date_2').datepicker("setDate", moment().add(8,'days').format('MM/DD/YYYY'));
	jQuery('#pay_date_3').datepicker({dateFormat: "mm/dd/yy"});
	jQuery('#pay_date_3').datepicker("setDate", moment().add(-8,'days').format('MM/DD/YYYY'));
	$('#pay_frequency').select2();
	$('#app_type').select2();
    	
}


/*
 * Call to process test lead
 */
function processTestContract(post_config){
	return $.ajax({
				type: 	"POST",
				url:	"/fraud/processtestcontract",
				headers: {'x-keyStone-nonce': nonce, 'Content-Type':'application/json'},
				data:	 JSON.stringify(post_config),
				dataType: "html"
	});
}


/**
 * Ajax call to query contracts.
 */
function contractList(buyer_id){
	return $.ajax({
		url:'/fraud/getcontracts/'+buyer_id,
		headers:{'x-keyStone-nonce': nonce}
	});
}

/**
 * Ajax call to query contract details.
 */
function contractDetails(contract_id){
	return $.ajax({
		url:'/fraud/getcontractdetails/'+contract_id,
		headers:{'x-keyStone-nonce': nonce}
	});
}

/***************** End Seed Contract ******************************************/