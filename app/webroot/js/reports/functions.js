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
    jQuery("#select-offer").select2();
    jQuery("#state").select2();
    jQuery("#themeperformance-select").select2();
    
    
    jQuery("#select-affiliate-unsold").select2();
    jQuery("#select-offer-unsold").select2();
    jQuery("#select-apptype").select2();

    if(jQuery('#themeperformance-table').length>0){
    	jQuery("#themeperformance-table thead").hide();	
    }
   
    //When table pagination is used, reattach hover nodes on dynamic content.
    jQuery('#leadTable').on( 'page.dt', function () {
    	jQuery("span[rel=hoverovers]", lead_table.fnGetNodes()).popover({ trigger: "hover" });  
	});
    
    // Reports->Buyer Contract
	if(jQuery('#contractoverviewTable').length>0){
		jQuery("#select-pingtree").select2();
		jQuery("#select-pingtree").on('change', function(){
			jQuery("#buyer-container").slideUp();
			jQuery("#select-buyer").find('option').remove().end();
			if(jQuery("#select-pingtree").val() !== '0'){
				var pingtree = jQuery("#select-pingtree option:selected").text().match(/\)(.*)/)
				
				jQuery.when(
					getContracts(jQuery(this).val()).done(function(result){
						jQuery('#select-buyer').append('<option value="0">Select Contract</option>');
						for (i = 0; i < result.data.length; i++) {
							var name = result.data[i].contract_name;
							name=name.replace(pingtree[1]+' - ','');
							jQuery('#select-buyer').append(jQuery('<option>',{
								value:result.data[i].remote_contract_id,
								text:name
							}))
						}
						jQuery("#select-buyer").select2();
						jQuery("#buyer-container").slideDown();
						jQuery("#contract-status").show();
				}));
			}
		});
		
		jQuery("#contract-status").on('click',function(){
			jQuery('#contractoverviewTable').hide();
			jQuery('#tableLoader').fadeIn();
			jQuery('#contractoverviewTableBody').empty();
			var buyer_id = jQuery('#select-pingtree').val();
			var contract_id = jQuery('#select-buyer').val();
			var start = moment(new Date(jQuery('#startdate').val())).format('YYYY-MM-DD');
			var end = moment(new Date(jQuery('#enddate').val())).format('YYYY-MM-DD');
		
			jQuery.when(
				getContractReport(start,end,buyer_id,contract_id).done(function(result){
					
					jQuery.each(result.data,function(index,value){
						var accept = 	isNaN(((value.approved/value.total)*100)) ? 0 : ((value.approved/value.total)*100).toFixed(1);
						var decline = 	isNaN(((value.declined/value.total)*100)) ? 0 : ((value.declined/value.total)*100).toFixed(1);
						var error = 	isNaN(((value.error/value.total)*100)) ? 0 : ((value.error/value.total)*100).toFixed(1);
						var timeout = 	isNaN(((value.timeout/value.total)*100)) ? 0 : ((value.timeout/value.total)*100).toFixed(1);
						var duplicate =	isNaN(((value.duplicate/value.total)*100)) ? 0 : ((value.duplicate/value.total)*100).toFixed(1);
						var epl = isNaN(((value.revenue/value.total))) ? 0 : ((value.revenue/value.total)).toFixed(2)
						var name = jQuery("#select-buyer option[value='"+index+"']").text();
												
						jQuery('#contractoverviewTableBody').append(
								"<tr><td>"+name+"</td>" +
								"<td>"+value.total+"</td>" +
								"<td>"+value.approved+"</td>" +
								"<td>"+accept+"</td>" +
								"<td>"+decline+"</td>" +
								"<td>"+error+"</td>" +
								"<td>"+timeout+"</td>" +
								"<td>"+duplicate+"</td>" +
								"<td>$"+value.revenue.toFixed(2)+"</td>" +
								"<td>$"+epl+"</td></tr>");
					})
				})
			).then(function(){
				jQuery('#tableLoader').hide();
				jQuery('#contractoverviewTable').fadeIn();
				jQuery("#export-report").fadeIn();
				setTimeout(function(){
					jQuery("#export-report").fadeOut('fast');
				},240000);
			})
		});
		
		jQuery("#export-report").on('click',function(){
			var start = moment(new Date(jQuery('#startdate').val())).format('YYYY-MM-DD');
			var end = moment(new Date(jQuery('#enddate').val())).format('YYYY-MM-DD');
			var buyer_id = jQuery('#select-pingtree').val();
			var contract_id = jQuery('#select-buyer').val();
			document.location.href = '/reports/export/'+start+end+buyer_id+contract_id;
		});
		
		jQuery('#report-reset').on('click',function(){
			jQuery('#custom-date').trigger('click');
			jQuery("#buyer-container").slideUp();
			jQuery("#select-buyer").find('option').remove().end();
			jQuery("#contract-status").hide();
			jQuery("#export-report").fadeIn();
		});
	}
	
	
	
	//Reports -> unsold leads
	if(jQuery('#unsoldTable').length>0){
		initUnsoldDataTable();	
	}
	
	
	jQuery("#select-affiliate-unsold").on('change',function(){
		var val = $(this).val();
		if(val == ""){
			val = 0;
		}
		
		$('#unsoldofferdiv').hide();
		$('#unsoldofferLoader').show();
		
		getUnsoldOfferDropDown(val).done(function(result){
			$("#select-offer-unsold option").remove();
			
			$("#select-offer-unsold").append('<option SELECTED value="">Select A Offer</option>');
			$.each(result, function(index,element){
				$("#select-offer-unsold").append('<option value="'+index+'">'+element+'</option>');
			});
			
			$('#unsoldofferLoader').hide();
			$('#unsoldofferdiv').show();
		});
	});
	
	
	
	jQuery("#unsold-generate").on('click',function(){
		var start_date = $('#startdate').val();
		var end_date = $('#enddate').val();
		var affiliate = $('#select-affiliate-unsold').val();
		var offer = $('#select-offer-unsold').val();		
		
		if(start_date == "" || end_date == "" || (offer == "" && affiliate == "")){
			return;
		}
		
		
		var post_config = {	'start_date':start_date,
							'end_date' : end_date,
		};
		
		if(offer != ""){
			post_config['offer_id'] = offer;
		}
		
		if(affiliate != ""){
			post_config['affiliate_id'] = affiliate;
		}
		
		
		var table = jQuery('#unsoldTable').DataTable();
		table.clear();
		
		jQuery('#unsoldtableLoader').fadeIn();
		
		jQuery.when(
			getUnsoldTotals(post_config).done(function(rsp){
				rows = rsp.data;
				charts = rsp.chart_totals;
				
				
			})
			
		).then(function(){
			var obj_empty = true;
			for (key in rows) {
				
				rowNode = table.row.add([	'<span>'+rows[key][0]+'</span>',
											    '<span>'+rows[key][1]+'</span>',
											    '<span>'+rows[key][2]+'</span>',
											    '<span>'+rows[key][3]+'</span>',
											    '<span>'+rows[key][4]+'</span>',
											    '<span>'+rows[key][5]+'</span>',
											    '<span>'+rows[key][6]+'</span>',
											    '<span>'+rows[key][7]+'</span>',
											    '<span>'+rows[key][8]+'</span>'
										]).node();
				obj_empty = false;
				
			};
			
			jQuery('#unsoldtableLoader').fadeOut();
			table.draw();
		
			
				if(!obj_empty){
					var sold_unsold_array = [];
					
					sold_unsold_array.push({
						                    name: '<b>Sold Leads Percentage</b>',
						                    y: charts.sold_percentage,
						                    sliced: true,
						                    selected: true
						                });
					sold_unsold_array.push({
						                    name: '<b>Unsold Leads Percentage</b>',
						                    y: charts.unsold_percentage,
						                    sliced: true,
						                    selected: true
						                });
					
					//Chart Display
					var chart_totals_config = {
						chart: {
			                plotBackgroundColor: null,
			                plotBorderWidth: null,
			                plotShadow: false,
			                type: 'pie'
			            },
				        title: {
				            text: 'Percentage Split'
				        },
				        tooltip: {
		                	pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
			            },
			            plotOptions: {
			                pie: {
			                    allowPointSelect: true,
			                    cursor: 'pointer',
			                    dataLabels: {
			                        enabled: false
			                    },
			                    showInLegend: true
			                }
			            },
			            credits: {
			                enabled: false
			            },
				        series: [{
			                name: 'Sold/Unsold Totals',
			                colorByPoint: true,
			                data: sold_unsold_array
			            }]	
						
					};
					
					$('#unsold-sold-chart').highcharts(chart_totals_config);
					
					
					var unsold_error_array = [];
					
					unsold_error_array.push({
						                    name: '<b>Incomplete Leads Percentage</b>',
						                    y: charts.incomplete_percentage,
						                    sliced: true,
						                    selected: true
						                });
					unsold_error_array.push({
						                    name: '<b>No Buyer Leads Percentage</b>',
						                    y: charts.no_buyer_percentage,
						                    sliced: true,
						                    selected: true
						                });
					unsold_error_array.push({
						                    name: '<b>Duplicate Leads Percentage</b>',
						                    y: charts.duplicates_percentage,
						                    sliced: true,
						                    selected: true
						                });
					unsold_error_array.push({
						                    name: '<b>BlackList Leads Percentage</b>',
						                    y: charts.blacklist_percentage,
						                   // sliced: true,
						                   // selected: true
						                });
						                
					unsold_error_array.push({
						                    name: '<b>LeadTime Leads Percentage</b>',
						                    y: charts.leadtime_percentage,
						                   // sliced: true,
						                    //selected: true
						                });
					
					//Chart2 Display
					var chart_totals_errors_config = {
						chart: {
			                plotBackgroundColor: null,
			                plotBorderWidth: null,
			                plotShadow: false,
			                type: 'pie'
			            },
				        title: {
				            text: 'Percentage Split'
				        },
				        tooltip: {
		                	pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
			            },
			            plotOptions: {
			                pie: {
			                    allowPointSelect: true,
			                    cursor: 'pointer',
			                    dataLabels: {
			                        enabled: false
			                    },
			                    showInLegend: true
			                }
			            },
			            credits: {
			                enabled: false
			            },
				        series: [{
			                name: 'Unsold/Errors Totals',
			                colorByPoint: true,
			                data: unsold_error_array
			            }]	
						
					};
					
					$('#unsold-error-chart').highcharts(chart_totals_errors_config);
				}
			});
		
		
		
		
		
		
	});
	
	
    
    // Reports->Leads
	if(jQuery('#leadTable').length>0){
		var leads = [];
		var lead = {};
		jQuery.when(
				leadQuery(window.report_dates.today, window.report_dates.tomorrow, '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-','-').done(function(result){
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
			    	'<span id="'+leads[key].affiliate_id+'">'+leads[key].affiliate_id+'</span>',
			    	'<a href="/reports/leaddetail/'+leads[key].track_id+'">'+leads[key].track_id+'</a>',
			    	leads[key].date,
			    	'<span '+rowhover+' data-container="body" data-toggle="popover" data-html="true" data-placement="top" data-content="<strong>Receivable:</strong> $'+parseFloat(Math.round(leads[key].receivable*100)/100).toFixed(2)+'<br><strong>Margin Amount:</strong> $'+parseFloat(Math.round(leads[key].margin*100)/100).toFixed(2)+'">'+leads[key].sold+'</span>'
			    	])
			    	.node();
			    	
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
			    }
			}
			table.draw();
			jQuery("span[rel=hoverovers]", lead_table.fnGetNodes()).popover({ trigger: "hover" });			
			jQuery('#tableLoader').hide();
			jQuery('#leadTable').fadeIn();
		});
	}
	
	// Reports->Redirect Rate
	if(jQuery('#affiliate-redirect-title').length>0){
		jQuery('#select-affiliate').on('change',function(){
			jQuery('#select-campaign').find('option').remove().end().append('<option value="">Filter by Campaign ID</option>');
			jQuery('#select-offer').prop('selectedIndex',0);
			jQuery('#select-offer').select2("val", "");
			jQuery('#campaign-container').slideUp();
		});
		
		jQuery('#select-offer').on('change',function(){
			if(jQuery('#select-offer').val() == "0"){
				jQuery('#campaign-container').slideUp();
			}else{
				if(jQuery('#select-affiliate').val() != "0"){
					var affiliate_id = jQuery('#select-affiliate').val();
					var offer_id = jQuery('#select-offer').val();
					jQuery.when(
						getCampaigns(affiliate_id,offer_id).done(function(result){
							if(typeof result.data.campaigns.campaign !== 'undefined' && result.data.campaigns.campaign && result.data.campaigns.campaign.constructor === Array){
								var campaigns = result.data.campaigns.campaign;
								
								jQuery.each(campaigns, function (i, item) {
								    jQuery('#select-campaign').append($('<option>', { 
								        value: item.campaign_id,
								        text : 'Campaign ('+item.campaign_id+')' 
								    }));
								});
								jQuery('#select-campaign').select2();
								jQuery('#campaign-container').slideDown();
							}else{
								jQuery('#select-campaign').find('option').remove().end().append('<option value="">Filter by Campaign ID</option>');
								jQuery('#campaign-container').slideUp();
							}
						})
					)
				}
			}
		});
		
		jQuery('#calc-redirect').on('click',function(){
			var affiliate_id = jQuery('#select-affiliate').val();
			var offer_id = jQuery('#select-offer').val();
			var campaign_id = jQuery('#select-campaign').val();
			var start = moment(new Date(jQuery('#startdate').val())).format('YYYY-MM-DD');
			var end = moment(new Date(jQuery('#enddate').val())).format('YYYY-MM-DD');
			
			if(offer_id == ''){offer_id=0;}
			if(campaign_id == ''){campaign_id=0;}
			
			if(jQuery('#custom-redirect').is(':visible')){
				jQuery('#custom-stacked-chart-default').empty();
				jQuery('#custom-redirect').slideUp();
			}
			
			jQuery.when(
				getRedirectRate(affiliate_id,offer_id,campaign_id,start,end).done(function(result){
					jQuery('.today-redirect-2').removeClass('progress-bar-success');
					jQuery('.today-redirect-2').removeClass('progress-bar-warning');
					jQuery('.today-redirect-2').removeClass('progress-bar-danger');
					
					jQuery('.yesterday-redirect-2').removeClass('progress-bar-success');
					jQuery('.yesterday-redirect-2').removeClass('progress-bar-warning');
					jQuery('.yesterday-redirect-2').removeClass('progress-bar-danger');
					
					jQuery('.avg-redirect-2').removeClass('progress-bar-success');
					jQuery('.avg-redirect-2').removeClass('progress-bar-warning');
					jQuery('.avg-redirect-2').removeClass('progress-bar-danger');
					
					jQuery('.custom-redirect-2').removeClass('progress-bar-success');
					jQuery('.custom-redirect-2').removeClass('progress-bar-warning');
					jQuery('.custom-redirect-2').removeClass('progress-bar-danger');
									
					jQuery('#custom-redirect-title').html(result.data.name+' Summary');
					jQuery('.today-redirect-1').html('<strong>'+result.data.today_redirect+'%</strong>');
					jQuery('.today-redirect-2').css('width',result.data.today_redirect+'%');
					jQuery('.today-redirect-2').attr('aria-valuenow',result.data.today_redirect);
					switch(true){
						case result.data.today_redirect >=80:
							jQuery('.today-redirect-2').addClass('progress-bar-success');
						break;
						case result.data.today_redirect >=70:
							jQuery('.today-redirect-2').addClass('progress-bar-warning');
						break;
						case result.data.today_redirect <=69:
							jQuery('.today-redirect-2').addClass('progress-bar-danger');
						break;
					}
					
					jQuery('.yesterday-redirect-1').html('<strong>'+result.data.yesterday_redirect+'%</strong>');
					jQuery('.yesterday-redirect-2').css('width',result.data.yesterday_redirect+'%');
					jQuery('.yesterday-redirect-2').attr('aria-valuenow',result.data.yesterday_redirect);
					switch(true){
						case result.data.yesterday_redirect >=80:
							jQuery('.yesterday-redirect-2').addClass('progress-bar-success');
						break;
						case result.data.yesterday_redirect >=70:
							jQuery('.yesterday-redirect-2').addClass('progress-bar-warning');
						break;
						case result.data.yesterday_redirect <=69:
							jQuery('.yesterday-redirect-2').addClass('progress-bar-danger');
						break;
					}
					
					jQuery('.avg-redirect-1').html('<strong>'+result.data.avg_redirect+'%</strong>');
					jQuery('.avg-redirect-2').css('width',result.data.avg_redirect+'%');
					jQuery('.avg-redirect-2').attr('aria-valuenow',result.data.avg_redirect);
					switch(true){
						case result.data.avg_redirect >=80:
							jQuery('.avg-redirect-2').addClass('progress-bar-success');
						break;
						case result.data.avg_redirect >=70:
							jQuery('.avg-redirect-2').addClass('progress-bar-warning');
						break;
						case result.data.avg_redirect <=69:
							jQuery('.avg-redirect-2').addClass('progress-bar-danger');
						break;
					}
					
					jQuery('.custom-redirect-1').html('<strong>'+result.data.custom_redirect+'%</strong>');
					jQuery('.custom-redirect-2').css('width',result.data.custom_redirect+'%');
					jQuery('.custom-redirect-2').attr('aria-valuenow',result.data.custom_redirect);
					switch(true){
						case result.data.custom_redirect >=80:
							jQuery('.custom-redirect-2').addClass('progress-bar-success');
						break;
						case result.data.custom_redirect >=70:
							jQuery('.custom-redirect-2').addClass('progress-bar-warning');
						break;
						case result.data.custom_redirect <=69:
							jQuery('.custom-redirect-2').addClass('progress-bar-danger');
						break;
					}
					
					jQuery('#custom-redirect').slideDown();
					var m5 = new Morris.Bar({
						// ID of the element in which to draw the chart.
						element: 'custom-stacked-chart-default',
					    data: [
					        { y: 'Today', a:result.data.today_sold, b:result.data.today_count},
					        { y: 'Yesterday', a:result.data.yesterday_sold,  b:result.data.yesterday_count},
					        { y: '7d Avg', a:result.data.avg_sold, b:result.data.avg_count },
					        { y: 'Custom', a:result.data.custom_sold, b:result.data.custom_count },
					    ],
					    xkey: 'y',
					    ykeys: ['a', 'b'],
					    labels: ['Sold', 'Redirected'],
					    barColors: ['#428bca', '#1caf9a'],
					    lineWidth: '1px',
					    fillOpacity: 0.8,
					    smooth: true,
					    stacked: true,
					    hideHover: true
					})
				})
			);
		});
	}
	
	//Reports->Intake	    
	jQuery('#calc-intake').on('click',function(){
		var affiliate_id = jQuery('#select-affiliate').val();
		var affiliate_name = jQuery('#select-affiliate option:selected').text();
		var offer_id = jQuery('#select-offer').val();
		
		if(jQuery('#custom-intake-container').is(':visible')){
			jQuery('#custom-intake-container').slideUp();
		}
		jQuery('#custom-intake-title').html(affiliate_name+' Summary');
		jQuery('#custom-intake-container').slideDown();
		
		getSummaryIntake('lead','na',affiliate_id,offer_id);
	})
	
	if(jQuery('#vendor-intake-title').length>0){
		getSummaryIntake('lead','external',0,0);
		getSummaryIntake('lead','internal',0,0);
	}
	
	//Reports->Sales Intake	    
	jQuery('#calc-salesintake').on('click',function(){
		var affiliate_id = jQuery('#select-affiliate').val();
		var affiliate_name = jQuery('#select-affiliate option:selected').text();
		var offer_id = jQuery('#select-offer').val();
		
		if(jQuery('#custom-salesintake-container').is(':visible')){
			jQuery('#custom-salesintake-container').slideUp();
		}
		jQuery('#custom-salesintake-title').html(affiliate_name+' Summary');
		jQuery('#custom-salesintake-container').slideDown();
		
		getSummaryIntake('sales','na',affiliate_id,offer_id);
	})
	
	if(jQuery('#vendor-salesintake-title').length>0){
		getSummaryIntake('sales','external',0,0);
		getSummaryIntake('sales','internal',0,0);
	}
	
	//Theme A/B Report
	jQuery('#themeperformance-calc').on('click',function(){
		var site = jQuery('#themeperformance-select').val();
		var start = moment(new Date(jQuery('#startdate').val())).format('YYYY-MM-DD');
		var end = moment(new Date(jQuery('#enddate').val())).format('YYYY-MM-DD');
		var table_data = {};
		var chart_data = {};
		
		if(site == "" || start == "Invalid date" || end == "Invalid date")return;
		
		jQuery('#themeperformance-table tbody tr').remove(); //clear table
		if(jQuery('#themeperformance-chart').highcharts()){
			jQuery('#themeperformance-chart').highcharts().destroy();	
		}//.destroy(); //clear chart
		jQuery('#themeperformance-site-display').html('- '+jQuery('#themeperformance-select').select2('data').text);	
		
		jQuery.when(
			getThemeData(site,start,end).done(function(result){
				table_data = result.table_data;
				chart_data = result.chart_data;		
			})
		).then(function(){
			
			jQuery('#themeperformance-table thead').show();
			
			//Display table data
			if(table_data.length > 0){
				$.each(table_data, function() {
					jQuery('#themeperformance-table tbody').append(	'<tr>'+
																		'<td>'+this.theme+'</td>'+
																		'<td>'+this.clicks+'</td>'+
																		'<td>'+this.leads+'</td>'+
																		'<td>'+this.sold+'</td>'+
																		'<td>$'+this.revenue+'</td>'+
																		'<td>'+this.epl+'</td>'+
																	'</tr>'
															);
				});
				
				
			}else{
				jQuery('#themeperformance-table tbody').append(	'<tr>'+
																	'<td>&nbsp;</td>'+
																	'<td>0</td>'+
																	'<td>0</td>'+
																	'<td>0</td>'+
																	'<td>$0.00</td>'+
																	'<td>0.00</td>'+
																'</tr>'
														);	
			}
			//End display table data
			
			//Start Charts
			if(chart_data.items == 'true'){
				var chart_series = [];
				var chart_pages = [];
				if(chart_data.chart_series.length > 0){
					for(var i = 0; chart_data.chart_series.length > i; i++ ){
						
						var arr = Object.keys(chart_data.chart_series[i]['data']).map(function (key) {return chart_data.chart_series[i]['data'][key]});
						
						chart_series.push({ 'name' : chart_data.chart_series[i]['name'],
											'data' : arr							
						});
						
						
					}
				}
				
				if(chart_data.pages.length > 0){
					for(var i = 0; chart_data.pages.length > i; i++ ){
						chart_pages[i] =  chart_data.pages[i];
						
					}
				}
				
				
				//Chart Display
				var chart_config = {
					chart: {
			            type: 'column'
			        },
			        title: {
			            text: jQuery('#themeperformance-select').select2('data').text+' Theme Results'
			        },
			        xAxis: {
			            categories: chart_pages,
			            crosshair: true
			        },
			        yAxis: {
			            min: 0,
			            ceiling: chart_data.y_axis_max,
			            title: {
			                text: 'Users Who Submitted Page'
			            }
			        },
			        credits: {
		                enabled: false
		            },
			        tooltip: {
			            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
			            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
			                '<td style="padding:0"><b>{point.y:.0f} Submissions</b></td></tr>',
			            footerFormat: '</table>',
			            shared: true,
			            useHTML: true
			        },
			        plotOptions: {
			            column: {
			                pointPadding: 0.2,
			                borderWidth: 1
			            }
			        },
			        series: chart_series	
					
				};
				
				$('#themeperformance-chart').highcharts(chart_config);
			}//End Charts
		});
	});
			
	//Reports->Leads
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
		
		var affiliate_id = (jQuery('#select-affiliate').val() == '' ? '-' : jQuery('#select-affiliate').val());
		jQuery.when(
				leadQuery(start, end, first, last, email, phone, city, state, zip, mobile, military, affiliate_id, ip, redirect, sold).done(function(result){
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
			    	leads[key].affiliate_id,
			    	'<a href="/reports/leaddetail/'+leads[key].track_id+'">'+leads[key].track_id+'</a>',
			    	leads[key].date,
			    	'<span '+rowhover+' data-container="body" data-toggle="popover" data-html="true" data-placement="top" data-content="<strong>Receivable:</strong> $'+parseFloat(Math.round(leads[key].receivable*100)/100).toFixed(2)+'<br><strong>Margin Amount:</strong> $'+parseFloat(Math.round(leads[key].margin*100)/100).toFixed(2)+'">'+leads[key].sold+'</span>'
			    	])
			    	.node();
			    	
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
			    }
			}
			table.draw();
			jQuery("span[rel=hoverovers]", lead_table.fnGetNodes()).popover({ trigger: "hover" });
			jQuery('#tableLoader').hide();
			jQuery('#leadTable').fadeIn();	
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
	
	jQuery('#pmonth-date').on('click',function(){
		jQuery('#startdate').datepicker("setDate", moment().subtract(1,'months').startOf('month').format('MM/DD/YYYY'));
		jQuery('#enddate').datepicker("setDate", moment().subtract(1,'months').endOf('month').add(1,'days').format('MM/DD/YYYY'));
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

function buildChartIntake(data,type){
	if(type == 'vendor'){
		var chart1 = new Highcharts.Chart({
			chart:{
				renderTo: 'vendor-intake',
				marginBottom: 40,
				zoomType: 'x'
			},
	        title: {
	            text: '',
	            x: -20 //center
	        },
	        subtitle: {
	            text: '',
	            x: -20
	        },
	        xAxis: {
	            categories: ['12am','12:30am','1am','1:30am','2am','2:30am','3am','3:30am','4am','4:30am','5am','5:30am','6am','6:30am','7am','7:30am','8am','8:30am','9am','9:30am','10am','10:30am','11am','11:30am','12pm','12:30pm','1pm','1:30pm','2pm','2:30pm','3pm','3:30pm','4pm','4:30pm','5pm','5:30pm','6pm','6:30pm','7pm','7:30pm','8pm','8:30pm','9pm','9:30pm','10pm','10:30pm','11pm','11:30pm'],
	            labels:{
	                step:2
	            },
	        },
	        yAxis: {
	            title: {
	                text: 'Leads Generated'
	            },
	            plotLines: [{
	                value: 0,
	                width: 1,
	                color: '#808080'
	            }]
	        },
	        credits:{
	        	enabled:false
	        },
	        legend: {
	           enabled:false
	        },
	        tooltip:{
	        	shared:true
	        },
	        series: [{
	            name: 'Today',
	            color: '#5cb85c',
	            marker: {
	                enabled: false
	            }
	        }, {
	            name: 'Yesterday',
	            color: '#428bca',
	            marker: {
	                enabled: false
	            }
	        }, {
	            name: 'Prior Week',
	            color: '#4e5154',
	            marker: {
	                enabled: false
	            }
	        }]
	    });
		
		chart1.series[0].setData(data.today);
		chart1.series[1].setData(data.yesterday);
		chart1.series[2].setData(data.week);
		chart1.redraw();
	}
	
	if(type == 'affiliate'){
		var chart2 = new Highcharts.Chart({
			chart:{
				renderTo: 'affiliate-intake',
				marginBottom: 40,
				zoomType: 'x'
			},
	        title: {
	            text: '',
	            x: -20 //center
	        },
	        subtitle: {
	            text: '',
	            x: -20
	        },
	        xAxis: {
	            categories: ['12am','12:30am','1am','1:30am','2am','2:30am','3am','3:30am','4am','4:30am','5am','5:30am','6am','6:30am','7am','7:30am','8am','8:30am','9am','9:30am','10am','10:30am','11am','11:30am','12pm','12:30pm','1pm','1:30pm','2pm','2:30pm','3pm','3:30pm','4pm','4:30pm','5pm','5:30pm','6pm','6:30pm','7pm','7:30pm','8pm','8:30pm','9pm','9:30pm','10pm','10:30pm','11pm','11:30pm'],
	            labels:{
	                step:2
	            },
	        },
	        yAxis: {
	            title: {
	                text: 'Leads Generated'
	            },
	            plotLines: [{
	                value: 0,
	                width: 1,
	                color: '#808080'
	            }]
	        },
	        credits:{
	        	enabled:false
	        },
	        legend: {
	            enabled:false
	        },
	        tooltip:{
	        	shared:true
	        },
	        series: [{
	            name: 'Today',
	            color: '#5cb85c',
	            marker: {
	                enabled: false
	            }
	        }, {
	            name: 'Yesterday',
	            color: '#428bca',
	            marker: {
	                enabled: false
	            }
	        }, {
	            name: 'Prior Week',
	            color: '#4e5154',
	            marker: {
	                enabled: false
	            }
	        }]
	    });
		
		chart2.series[0].setData(data.today);
		chart2.series[1].setData(data.yesterday);
		chart2.series[2].setData(data.week);
		chart2.redraw();
	}
	
	if(type == 'custom'){
		var chart3 = new Highcharts.Chart({
			chart:{
				renderTo: 'custom-intake',
				marginBottom: 40,
				zoomType: 'x'
			},
	        title: {
	            text: '',
	            x: -20 //center
	        },
	        subtitle: {
	            text: '',
	            x: -20
	        },
	        xAxis: {
	            categories: ['12am','12:30am','1am','1:30am','2am','2:30am','3am','3:30am','4am','4:30am','5am','5:30am','6am','6:30am','7am','7:30am','8am','8:30am','9am','9:30am','10am','10:30am','11am','11:30am','12pm','12:30pm','1pm','1:30pm','2pm','2:30ampm','3pm','3:30pm','4pm','4:30pm','5pm','5:30pm','6pm','6:30pm','7pm','7:30pm','8pm','8:30pm','9pm','9:30pm','10pm','10:30pm','11pm','11:30pm'],
	            labels:{
	                step:2
	            },
	        },
	        yAxis: {
	            title: {
	                text: 'Leads Generated'
	            },
	            plotLines: [{
	                value: 0,
	                width: 1,
	                color: '#808080'
	            }]
	        },
	        credits:{
	        	enabled:false
	        },
	        legend: {
	            enabled:false
	        },
	        tooltip:{
	        	shared:true
	        },
	        series: [{
	            name: 'Today',
	            color: '#5cb85c',
	            marker: {
	                enabled: false
	            }
	        }, {
	            name: 'Yesterday',
	            color: '#428bca',
	            marker: {
	                enabled: false
	            }
	        }, {
	            name: 'Prior Week',
	            color: '#4e5154',
	            marker: {
	                enabled: false
	            }
	        }]
	    });
		
		chart3.series[0].setData(data.today);
		chart3.series[1].setData(data.yesterday);
		chart3.series[2].setData(data.week);
		chart3.redraw();
	}
	
	if(type == 'vendorsales'){
		var chart4 = new Highcharts.Chart({
			chart:{
				renderTo: 'vendor-salesintake',
				marginBottom: 40,
				zoomType: 'x'
			},
	        title: {
	            text: '',
	            x: -20 //center
	        },
	        subtitle: {
	            text: '',
	            x: -20
	        },
	        xAxis: {
	        	categories: ['12am','12:30am','1am','1:30am','2am','2:30am','3am','3:30am','4am','4:30am','5am','5:30am','6am','6:30am','7am','7:30am','8am','8:30am','9am','9:30am','10am','10:30am','11am','11:30am','12pm','12:30pm','1pm','1:30pm','2pm','2:30ampm','3pm','3:30pm','4pm','4:30pm','5pm','5:30pm','6pm','6:30pm','7pm','7:30pm','8pm','8:30pm','9pm','9:30pm','10pm','10:30pm','11pm','11:30pm'],
	            labels:{
	                step:2
	            },
	        },
	        yAxis: {
	            title: {
	                text: 'Sales'
	            },
	            plotLines: [{
	                value: 0,
	                width: 1,
	                color: '#808080'
	            }]
	        },
	        credits:{
	        	enabled:false
	        },
	        legend: {
	           enabled:false
	        },
	        tooltip:{
	        	shared:true
	        },
	        series: [{
	            name: 'Today',
	            color: '#5cb85c',
	            marker: {
	                enabled: false
	            }
	        }, {
	            name: 'Yesterday',
	            color: '#428bca',
	            marker: {
	                enabled: false
	            }
	        }, {
	            name: 'Prior Week',
	            color: '#4e5154',
	            marker: {
	                enabled: false
	            }
	        }]
	    });
		
		chart4.series[0].setData(data.today);
		chart4.series[1].setData(data.yesterday);
		chart4.series[2].setData(data.week);
		chart4.redraw();
	}
	
	if(type == 'affiliatesales'){
		var chart5 = new Highcharts.Chart({
			chart:{
				renderTo: 'affiliate-salesintake',
				marginBottom: 40,
				zoomType: 'x'
			},
	        title: {
	            text: '',
	            x: -20 //center
	        },
	        subtitle: {
	            text: '',
	            x: -20
	        },
	        xAxis: {
	        	categories: ['12am','12:30am','1am','1:30am','2am','2:30am','3am','3:30am','4am','4:30am','5am','5:30am','6am','6:30am','7am','7:30am','8am','8:30am','9am','9:30am','10am','10:30am','11am','11:30am','12pm','12:30pm','1pm','1:30pm','2pm','2:30ampm','3pm','3:30pm','4pm','4:30pm','5pm','5:30pm','6pm','6:30pm','7pm','7:30pm','8pm','8:30pm','9pm','9:30pm','10pm','10:30pm','11pm','11:30pm'],
	            labels:{
	                step:2
	            },
	        },
	        yAxis: {
	            title: {
	                text: 'Sales'
	            },
	            plotLines: [{
	                value: 0,
	                width: 1,
	                color: '#808080'
	            }]
	        },
	        credits:{
	        	enabled:false
	        },
	        legend: {
	            enabled:false
	        },
	        tooltip:{
	        	shared:true
	        },
	        series: [{
	            name: 'Today',
	            color: '#5cb85c',
	            marker: {
	                enabled: false
	            }
	        }, {
	            name: 'Yesterday',
	            color: '#428bca',
	            marker: {
	                enabled: false
	            }
	        }, {
	            name: 'Prior Week',
	            color: '#4e5154',
	            marker: {
	                enabled: false
	            }
	        }]
	    });
		
		chart5.series[0].setData(data.today);
		chart5.series[1].setData(data.yesterday);
		chart5.series[2].setData(data.week);
		chart5.redraw();
	}
	
	if(type == 'customsales'){
		var chart6 = new Highcharts.Chart({
			chart:{
				renderTo: 'custom-salesintake',
				marginBottom: 40,
				zoomType: 'x'
			},
	        title: {
	            text: '',
	            x: -20 //center
	        },
	        subtitle: {
	            text: '',
	            x: -20
	        },
	        xAxis: {
	        	categories: ['12am','12:30am','1am','1:30am','2am','2:30am','3am','3:30am','4am','4:30am','5am','5:30am','6am','6:30am','7am','7:30am','8am','8:30am','9am','9:30am','10am','10:30am','11am','11:30am','12pm','12:30pm','1pm','1:30pm','2pm','2:30ampm','3pm','3:30pm','4pm','4:30pm','5pm','5:30pm','6pm','6:30pm','7pm','7:30pm','8pm','8:30pm','9pm','9:30pm','10pm','10:30pm','11pm','11:30pm'],
	            labels:{
	                step:2
	            },
	        },
	        yAxis: {
	            title: {
	                text: 'Sales'
	            },
	            plotLines: [{
	                value: 0,
	                width: 1,
	                color: '#808080'
	            }]
	        },
	        credits:{
	        	enabled:false
	        },
	        legend: {
	            enabled:false
	        },
	        tooltip:{
	        	shared:true
	        },
	        series: [{
	            name: 'Today',
	            color: '#5cb85c',
	            marker: {
	                enabled: false
	            }
	        }, {
	            name: 'Yesterday',
	            color: '#428bca',
	            marker: {
	                enabled: false
	            }
	        }, {
	            name: 'Prior Week',
	            color: '#4e5154',
	            marker: {
	                enabled: false
	            }
	        }]
	    });
		
		chart6.series[0].setData(data.today);
		chart6.series[1].setData(data.yesterday);
		chart6.series[2].setData(data.week);
		chart6.redraw();
	}
}


//Data Export

//Check all report fields
$(document).on('click', '#dataexport-checkall' , function(e) {
	jQuery('#dataexport-table input').prop('checked',true);
	jQuery('#dataexport-checkall').hide();
	jQuery('#dataexport-uncheckall').show();
	
});

//uncheck all report fields
$(document).on('click', '#dataexport-uncheckall' , function(e) {
	jQuery('#dataexport-table input').prop('checked',false);
	jQuery('#dataexport-uncheckall').hide();
	jQuery('#dataexport-checkall').show();
	
	
});


$(document).on('click', '#dataexport-generate' , function(e) {
	jQuery('#dataexport-maincontent').hide();	
	jQuery('#dataexport-tableLoader').show();
	
	

	var obj = {};
	
	obj['startdate'] = jQuery('#startdate').val();
	obj['enddate'] = jQuery('#enddate').val();
	obj['zip'] = jQuery('#zip').val();
	obj['state'] = jQuery('#state').val();
	obj['mobile'] = (($('#mobile').is(':checked'))? 'true':'false');
	obj['military'] = (($('#military').is(':checked'))? 'true':'false');
	obj['redirect'] = (($('#redirect').is(':checked'))? 'true':'false');
	obj['sold'] = (($('#sold').is(':checked'))? 'true':'false');
	obj['altered'] = (($('#altered').is(':checked'))? 'true':'false');
	obj['affiliate'] = jQuery('#select-affiliate').val();
	obj['fulldata'] = (($('#fulldata').is(':checked'))? 'true':'false');
	obj['agreephone'] = (($('#agreephone').is(':checked'))? 'true':'false');
	obj['apptype'] = jQuery('#select-apptype').val();
	
	
	var checked_fields = jQuery('#dataexport-table input[type=\'checkbox\']');
	obj['mongo_fields'] = [];
	
	for(var i=0; i < checked_fields.length; i++){
		var checkbox_id = checked_fields[i].id;
		if($('#'+checkbox_id+'').is(':checked')){
			obj['mongo_fields'].push(checkbox_id.replace('mongo_', ''));	
		}
	}
	
	createDataReport(obj).done(function(result){
		if(result.status == 'success'){
			window.location.href = 'dataexport_download/'+result.id;
			jQuery('#dataexport-maincontent').show();	
			jQuery('#dataexport-tableLoader').hide();
			
			jQuery('#dataexport-success-message').html(result.data);
			$('#dataexport-success').fadeIn('slow');
			
		}else{
			jQuery('#dataexport-maincontent').show();	
			jQuery('#dataexport-tableLoader').hide();
			
			jQuery('#dataexport-alert-message').html(result.data);
			$('#dataexport-alert').fadeIn('slow');
		}		
	});
	
});


$(document).on('click', '#dataexport-alert-close' , function(e) {
	$('#dataexport-alert').fadeOut('slow');
	jQuery('#dataexport-alert-message').html("");	
});

$(document).on('click', '#dataexport-success-close' , function(e) {
	$('#dataexport-success').fadeOut('slow');
});

/**
 * Ajax call to get generated leads for the given date range
 * @param start
 * @param end
 */
function getGeneratedLeads(start, end, limit){
	return $.ajax({
		url:'/reports/getGeneratedLeads/'+start+'/'+end+'/'+limit,
		headers:{'x-keyStone-nonce': nonce}
	});
}

/**
 * Ajax call to get redirect rate for affiliate id given.
 * @param affiliate_id
 */
function getRedirectRate(affiliate_id,offer_id,campaign_id,start,end){
	return $.ajax({
		url:'/reports/getRedirectRate/'+affiliate_id+'/'+offer_id+'/'+campaign_id+'/'+start+'/'+end+'/',
		headers:{'x-keyStone-nonce': nonce}
	});
}

/**
 * Intake ajax call for both lead and sales.
 * @param model
 * @param type
 * @param affiliate_id
 * @param offer_id
 */
function getSummaryIntake(model,type,affiliate_id,offer_id){
	$.ajax({
		async:true,
		url:'/reports/getSummaryIntake/'+model+'/'+type+'/'+affiliate_id+'/'+offer_id,
		headers:{'x-keyStone-nonce': nonce},
		dataType: "json",
        success: function (result) {
        	if(type == 'external'){
        		if(model == 'lead')
        			buildChartIntake(result.data,'vendor');
        		if(model == 'sales')
        			buildChartIntake(result.data,'vendorsales');
        	}else if(type == 'na'){
        		if(model == 'lead')
        			buildChartIntake(result.data,'custom');
        		if(model == 'sales')
        			buildChartIntake(result.data,'customsales');
        	}else{
        		if(model == 'lead')
        			buildChartIntake(result.data,'affiliate');
        		if(model == 'sales')
        			buildChartIntake(result.data,'affiliatesales');
        	}
        }
	});
}

/**
 * Ajax call to query mongo by the criteria set.
 */
function leadQuery(start, end, first, last, email, phone, city, state, zip, mobile, military, affiliate_id, ip, redirect, sold){
	return $.ajax({
		url:'/reports/leadQuery/'+start+'/'+end+'/'+first+'/'+last+'/'+email+'/'+phone+'/'+city+'/'+state+'/'+zip+'/'+mobile+'/'+military+'/'+affiliate_id+'/'+ip+'/'+redirect+'/'+sold,
		headers:{'x-keyStone-nonce': nonce}
	});
}

function getContracts(id){
	return $.ajax({
		url:'/reports/getContracts/'+id,
		headers:{'x-keyStone-nonce': nonce}
	});
}

function getContractReport(start, end, buyer_id, contract_id){
	return $.ajax({
		url:'/reports/getContractReport/'+start+'/'+end+'/'+buyer_id+'/'+contract_id,
		headers:{'x-keyStone-nonce': nonce}
	});
}

function getOfferDetails(id){
	return $.ajax({
		url:'/cake/exportoffer/'+id,
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

/**
 * Initialise the unsold data table
 */
function initUnsoldDataTable() {	
	lead_table = jQuery('#unsoldTable').dataTable({
		'searching': false,
		'paging': false,
		'lengthChange': true
		
	});
}

function getCampaigns(affiliate_id,offer_id){
	return $.ajax({
		url:'/cake/exportcampaign/0/'+offer_id+'/'+affiliate_id,
		headers:{'x-keyStone-nonce': nonce}
	});
}

function getThemeData(site, start, end){
	return $.ajax({
		url:'/reports/getThemeData/'+site+'/'+start+'/'+end,
		headers:{'x-keyStone-nonce': nonce}
	});	
}


function getUnsoldOfferDropDown(aff){
	return $.ajax({
		url:'/reports/unsoldleadofferdropdown/'+aff,
		headers:{'x-keyStone-nonce': nonce}
	});	
}


function getUnsoldTotals(post_config){
	return $.ajax({
				type: 	"POST",
				url:	"/reports/getunsoldtotals",
				headers: {'x-keyStone-nonce': nonce, 'Content-Type':'application/json'},
				data:	 JSON.stringify(post_config),
				dataType: "json"
	});
}

function createDataReport(post_config){
	return $.ajax({
				type: 	"POST",
				url:	"/reports/createdatareport",
				headers: {'x-keyStone-nonce': nonce, 'Content-Type':'application/json'},
				data:	 JSON.stringify(post_config),
				dataType: "json"
	});
}

function deleteDataReport(id){
	return $.ajax({
		url:"/reports/deletedatareport/"+id,
		headers:{'x-keyStone-nonce': nonce}
	});
}

