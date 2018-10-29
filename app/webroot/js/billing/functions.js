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

//store campaign and creative id
var adjustment_creatives_obj = {};
jQuery(document).ready(function(){
	
	jQuery('#startdate').datepicker({dateFormat: "mm/dd/yy"});
	jQuery('#enddate').datepicker({dateFormat: "mm/dd/yy"});
	jQuery('#adjustment_date').datepicker({dateFormat: "mm/dd/yy"});
	
	jQuery('#startdate').datepicker("setDate", moment().format('MM/DD/YYYY'));
	jQuery('#enddate').datepicker("setDate", moment().add(1,'days').format('MM/DD/YYYY'));
	jQuery('#adjustment_date').datepicker("setDate", moment().format('MM/DD/YYYY'));
		
	jQuery("#startdate").mask("99/99/9999");
	jQuery("#enddate").mask("99/99/9999");
	jQuery("#adjustment_date").mask("99/99/9999");
	
	
	jQuery("#select-buyer").select2();
	 
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
	
	// Billing ->buyergroup
	if(jQuery('#contractgroupTable').length>0){
		jQuery('.contractshow').select2();
	}
	
	jQuery('#select-affiliate').select2();
	jQuery('#select-adjust-type').select2();
	jQuery('#select-campaign').select2();
	jQuery('#select-affiliate-total').select2();
	
	
	// Billing -> buyerreport
	if(jQuery('#billingTable').length>0){

		jQuery.when(
				reportList().done(function(result){
					if(result.length>0) {
						reports = JSON.parse(result);
					}
	    	})
		).then(function(){
			initDataTable();
			table = jQuery('#billingTable').DataTable();
			table.clear();
			for (key in reports) {
			    if (reports.hasOwnProperty(key) && /^0$|^[1-9]\d*$/.test(key) && key <= 4294967294) {	
			    	switch(reports[key].status){
			    	case '0':
			    		dl = 'In Queue...';
			    		break;
			    	case '1':
			    		dl = 'Processing...';
			    		break;
			    	case '2':
			    		dl='<a href="/billing/download/'+key+'"><button class="btn btn-success btn-xs"><span class="fa fa-download"></span></button></a>';
			    		break;
			    	}
			    	rowNode = table.row.add([
			    	'<span id="'+key+'">'+key+'</span>',
			    	'<span>'+reports[key].buyer+'</span>',
			    	'<span>'+reports[key].start+'</span>',
			    	'<span>'+reports[key].end+'</span>',
			    	'<span>'+moment(new Date(reports[key].reportdate)).format('MM/DD/YYYY h:mm:ss A')+'</span>',
			    	'<span style="cursor:pointer;">'+dl+'</span>',
			    	])
			    	.node();
			    }
			}
			table.draw();
			jQuery('#tableLoader').hide();
			jQuery('#billingTable').fadeIn();
		});
	}
	
	//Group Delete Modal Display - Pass group id to groupid data attribute in modal
    $(document).on("click", "#delete-group", function () {
	     var group_id = $(this).data('groupid');
	     var modal = $("#billinggroup-delete-modal-action");
	     modal.attr('data-groupid', group_id);
	    
	});
	
	//Group Delete Modal Perform deletion
    $(document).on("click", "#billinggroup-delete-modal-action", function () {
	    var group_id = $(this).data('groupid');
	    var obj = { 'id' : group_id  };
	     
	    deleteGroup(obj).done(function(rsp){
			$("#billinggroup-delete-modal").modal('hide');
			
			if(rsp.status == 'success'){
				location.reload();	
			}
			
		});
	    
	});
	
	
	
	
	
	$(document).on("click", "#adjustable-totals", function () {
		var start_date = $('#startdate').val();
		var end_date = $('#enddate').val();
		var affiliate = $('#select-affiliate-total').val();
		$('#billingAdjustmentTotalTable tbody tr').remove();
		
		if(start_date == "" || end_date == "" || affiliate == ""){
			return;
		}
		
		
		var post_config = {	'start_date':start_date,
							'end_date' : end_date,
							'affiliate_id' : affiliate
		};
		
		getAdjustmentTotals(post_config).done(function(rsp){
		
			$("#billingAdjustmentTotalTable tbody").append('<tr></tr>');
			for (key in rsp) {
				$("#billingAdjustmentTotalTable tbody").append(	'<tr><td>'+rsp[key][0]+'</td>'+
										    	'<td>'+rsp[key][1]+'</td>'+
										    	'<td>'+start_date+'</td>'+
										    	'<td>'+end_date+'</td>'+
										    	'<td>'+rsp[key][2]+'</td></tr>'
										     );
			};
			
		});
	
	});
	
	
	// Billing -> adjustment
	if(jQuery('#billingAdjustmentTable').length>0){

		jQuery.when(
				getAdjustments().done(function(result){
					if(result.length>0) {
						records = $.parseJSON(result)
					}
	    	})
		).then(function(){
			initDataTableAdjustment();
			table = jQuery('#billingAdjustmentTable').DataTable();
			table.clear();
			for (key in records) {
			    
			    	rowNode = table.row.add([
			    	'<span id="'+key+'">'+records[key][0]+'</span>',
			    	'<span>'+records[key][1]+'</span>',
			    	'<span>'+records[key][2]+'</span>',
			    	'<span>'+records[key][3]+'</span>',
			    	'<span>'+records[key][4]+'</span>',
			    	'<span>'+records[key][5]+'</span>',
			    	'<span>'+records[key][6]+'</span>'
			    	])
			    	.node();
			}
			table.draw();
			jQuery('#tableLoader2').hide();
			jQuery('#billingAdjustmentTable').fadeIn();
		});
	}
	
	
	
	//Billing Adjustable Affiliate Dropdown change
    $(document).on("change", "#select-affiliate", function () {
    	var affiliate_id = $(this).val();
    	if(affiliate_id == "")return;
    	
    	$('#tableLoader').fadeIn();
		$('#adjustable-fields').fadeOut();
		$('#add-adjustable').fadeOut();	
    	$('#tableLoader').fadeIn();
    	$("#select-campaign option").remove();
	    adjustment_creatives_obj = {}; // clear obj
	    $('#creative-id').val(""); //clear creative input
	    
	    campaignList(affiliate_id).done(function(rsp){
			var obj = $.parseJSON(rsp);
			if(obj.status == 'success'){
				
				$("#select-campaign").append('<option SELECTED value="">Select A Campaign</option>');
				$.each(obj.data, function(index,element){
					$("#select-campaign").append('<option value="'+element[0]+'">'+element[0]+'</option>');
					adjustment_creatives_obj[element[0]] = element[1];
				});
			
				$('#tableLoader').fadeOut();
				$('#adjustable-fields').fadeIn();
				$('#add-adjustable').fadeIn();
				$("#select-campaign").val("");	
			}
		});
	});
	
	
	//Billing Adjustable Campaign Dropdown change
    $(document).on("change", "#select-campaign", function () {
    	$('#creative-id').val("");
    	var campaign_id = $(this).val();
    	var creative = adjustment_creatives_obj[campaign_id];
    	if(creative != ""){
    		$('#creative-id').val(creative);
    	}
	});
	
	
	//Billing Adjustable add adjustment
    $(document).on("click", "#add-adjustable", function () {
    	$('#add-adjustable').fadeOut();	
    	$('#tableLoader3').fadeIn();
    	
    	var adjustdate = $("#adjustment_date").val();
    	var adjusttype = $("#select-adjust-type").val();
    	var affiliate_id = $("#select-affiliate").val();
    	var campaign_id = $("#select-campaign").val();
    	var creative_id = $("#creative-id").val();
    	var price = $("#price").val();
    	
    	if(adjustdate == "" || adjusttype == "" || affiliate_id == "" || campaign_id == "" || price == "" || creative_id == ""){
    		$('#add-adjustable').fadeIn();
    		$('#tableLoader3').fadeOut();
    		return;	
    	}
    	
    	var post_config = {	"adjustdate" : adjustdate,
    						"adjusttype" : adjusttype,
    						"affiliate_id" : affiliate_id,
    						"campaign_id" : campaign_id,
    						"creative_id" : creative_id,
    						"price" : price
    	};
    	
    	sendAdjustment(post_config).done(function(rsp){
    		//Reload page to see message saved in session	
    		location.reload();	
    	});
    	
    	
	});
	
	
	
	
	
	
});




/**
 * Ajax call to query the queue for billing reports.
 */
function getAdjustments(){
	return $.ajax({
		url:'/billing/getadjustments',
		headers:{'x-keyStone-nonce': nonce}
	});
}


function getAdjustmentTotals($post_config){
	return $.ajax({
				type: 	"POST",
				url:	"/billing/getadjustmentstotals",
				headers: {'x-keyStone-nonce': nonce, 'Content-Type':'application/json'},
				data:	 JSON.stringify($post_config),
				dataType: "json"
	});
}

/*
 * Process adjustment
 */
function sendAdjustment($post_config){
	return $.ajax({
				type: 	"POST",
				url:	"/billing/addadjustment",
				headers: {'x-keyStone-nonce': nonce, 'Content-Type':'application/json'},
				data:	 JSON.stringify($post_config),
				dataType: "json"
	});
}


/**
 * Ajax call to query affiliate campaigns.
 */
function campaignList(affiliate_id){
	return $.ajax({
		url:'/billing/getcampaigns/'+affiliate_id,
		headers:{'x-keyStone-nonce': nonce}
	});
}


function deleteGroup(data){

	return $.ajax({
				type: 	"POST",
				url:	"/billing/deleteGroup",
				headers: {'x-keyStone-nonce': nonce, 'Content-Type':'application/json'},
				data:	 JSON.stringify(data),
				dataType: "json"
			});

}



/**
 * Ajax call to query the queue for billing reports.
 */
function reportList(){
	return $.ajax({
		url:'/billing/getreports',
		headers:{'x-keyStone-nonce': nonce}
	});
}

/**
 * Initialise the data table
 */
function initDataTable() {	
	lead_table = jQuery('#billingTable').dataTable({
		'searching': false,
		'iDisplayLength': 10,
		'lengthChange': false,
		'order':[[0,"desc"]]
	});
}

/*
 * Initialise adjustment table data
 */
function initDataTableAdjustment() {	
	lead_table = jQuery('#billingAdjustmentTable').dataTable({
		'searching': false,
		'iDisplayLength': 10,
		'lengthChange': false,
		'order':[[0,"asc"]]
	});
}