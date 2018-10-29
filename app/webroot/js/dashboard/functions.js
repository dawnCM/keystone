// Global to the DOM
window.report_dates ={
	today : moment().format('MM-DD-YYYY'),
	tomorrow : moment().add(1,'days').format('MM-DD-YYYY'),
	yesterday : moment().subtract(1,'days').format('MM-DD-YYYY'),
	twodays : moment().subtract(2,'days').format('MM-DD-YYYY'),
	fivedays : moment().subtract(5,'days').format('MM-DD-YYYY'),
	weekstart : moment().startOf('week').format('MM-DD-YYYY'),
	weekago : moment().subtract(7,'days').format('MM-DD-YYYY'),
	monthstart : moment().startOf('month').format('MM-DD-YYYY'),
	yearstart : moment().startOf('year').format('MM-DD-YYYY'),
	lastyearstart : moment().startOf('year').subtract(1,'y').format('MM-DD-YYYY'),
	lastyearend : moment().startOf('year').subtract(1,'y').endOf('year').format('MM-DD-YYYY'),
	currentyear : moment().startOf('year').format('YYYY'),
	lastyear : moment().startOf('year').subtract(1,'y').format('YYYY'),
}

jQuery(document).ready(function(){
	var leads = [];
	var generated_leads={};
	var sold_leads={};
	var profit={};
	var affiliate_leads={};
	var vendor_leads={};
	
	var today = moment(window.report_dates.today,'MM-DD-YYYY').format('YYYY-MM-DD');
	var tomorrow = moment(window.report_dates.tomorrow,'MM-DD-YYYY').format('YYYY-MM-DD');
	var yesterday = moment(window.report_dates.yesterday,'MM-DD-YYYY').format('YYYY-MM-DD');
	var weekstart = moment(window.report_dates.weekstart,'MM-DD-YYYY').format('YYYY-MM-DD');
	var monthstart = moment(window.report_dates.monthstart,'MM-DD-YYYY').format('YYYY-MM-DD');
	
	/*****Generated*******/
	jQuery.when(
		// Count generated - today
		countGeneratedLeads(today, tomorrow).done(function(result){
			generated_leads.today = result.data; 
		})
		
	).then(function(){
		$('#gl-today').delay(300).fadeOut('slow', function(){
			$('#gl-today').html(generated_leads.today).fadeIn('slow');	
		});
	});
	
	jQuery.when(
		// Count generated - yesterday			
		countGeneratedLeads(yesterday, today).done(function(result){
			generated_leads.yesterday = result.data; 
		})
		
	).then(function(){
		$('#gl-yesterday').delay(300).fadeOut('slow', function(){
			$('#gl-yesterday').html(generated_leads.yesterday).fadeIn('slow');
		});
	});
	
	jQuery.when(
		// Count generated - week
		countGeneratedLeads(weekstart, tomorrow).done(function(result){
			generated_leads.week = result.data; 
		})
		
	).then(function(){
		$('#gl-week').delay(300).fadeOut('slow', function(){
			$('#gl-week').html(generated_leads.week).fadeIn('slow');
		});
	});
	
	jQuery.when(
			// Count generated - month
			countGeneratedLeads(monthstart, tomorrow).done(function(result){
				generated_leads.month = result.data; 
			})
			
		).then(function(){
			$('#gl-month').delay(300).fadeOut('slow', function(){
				$('#gl-month').html(generated_leads.month).fadeIn('slow');
			});
		});
	
	/*****Sold*******/
	jQuery.when(
			// Count sold - today
			countSoldLeads(today, tomorrow).done(function(result){
				sold_leads.today = result.data; 
			})
			
		).then(function(){
			$('#sl-today').delay(300).fadeOut('slow', function(){
				$('#sl-today').html(sold_leads.today).fadeIn('slow');	
			});
		});
		
	jQuery.when(
		// Count sold - yesterday			
		countSoldLeads(yesterday, today).done(function(result){
			sold_leads.yesterday = result.data; 
		})
		
	).then(function(){
		$('#sl-yesterday').delay(300).fadeOut('slow', function(){
			$('#sl-yesterday').html(sold_leads.yesterday).fadeIn('slow');
		});
	});
	
	jQuery.when(
		// Count sold - week
		countSoldLeads(weekstart, tomorrow).done(function(result){
			sold_leads.week = result.data; 
		})
		
	).then(function(){
		$('#sl-week').delay(300).fadeOut('slow', function(){
			$('#sl-week').html(sold_leads.week).fadeIn('slow');
		});
	});
	
	jQuery.when(
			// Count sold - month
			countSoldLeads(monthstart, tomorrow).done(function(result){
				sold_leads.month = result.data; 
			})
			
		).then(function(){
			$('#sl-month').delay(300).fadeOut('slow', function(){
				$('#sl-month').html(sold_leads.month).fadeIn('slow');
			});
		});
		
	/*****Profit*******/
	jQuery.when(
		// Count profit - today
		getProfit(today, tomorrow).done(function(result){
			if(result.data != null){
				profit.today = (result.data.receivable_total - result.data.paid_total); 
				profit.today = parseFloat(Math.round(profit.today * 100) / 100).toFixed(2);
			}else{
				profit.today = 0;
			}
		})
	).then(function(){
		$('#pr-today').delay(300).fadeOut('slow', function(){
			$('#pr-today').html('$ '+profit.today).fadeIn('slow');	
		});
	});
	
	jQuery.when(
			// Count profit - yesterday
			getProfit(yesterday, today).done(function(result){
				if(result.data != null){
					profit.yesterday = (result.data.receivable_total - result.data.paid_total); 
					profit.yesterday = parseFloat(Math.round(profit.yesterday * 100) / 100).toFixed(0);
				}else{
					profit.yesterday = 0;
				}
			})
		).then(function(){
			$('#pr-yesterday').delay(300).fadeOut('slow', function(){
				$('#pr-yesterday').html('$ '+profit.yesterday).fadeIn('slow');	
			});
		});
	
	jQuery.when(
			// Count profit - week
			getProfit(weekstart, tomorrow).done(function(result){
				if(result.data != null){
					profit.week = (result.data.receivable_total - result.data.paid_total); 
					profit.week = parseFloat(Math.round(profit.week * 100) / 100).toFixed(0);
				}else{
					profit.week = 0;
				}
			})
		).then(function(){
			$('#pr-week').delay(300).fadeOut('slow', function(){
				$('#pr-week').html('$ '+profit.week).fadeIn('slow');	
			});
		});
	
	jQuery.when(
			// Count profit - month
			getProfit(monthstart, tomorrow).done(function(result){
				if(result.data != null){
					profit.month = (result.data.receivable_total - result.data.paid_total); 
					profit.month = parseFloat(Math.round(profit.month * 100) / 100).toFixed(0);
				}else{
					profit.month = 0;
				}
			})
		).then(function(){
			$('#pr-month').delay(300).fadeOut('slow', function(){
				$('#pr-month').html('$ '+profit.month).fadeIn('slow');	
			});
		});
		
	/**
	 * Ajax call to count generated leads for the given date range
	 * @param start
	 * @param end
	 */
	function countGeneratedLeads(start, end){
		return $.ajax({
			url:'/reports/countGeneratedLeads/'+start+'/'+end,
			headers:{'x-keyStone-nonce': nonce}
		});
	}
	
	/**
	 * Ajax call to count sold leads for the given date range
	 * @param start
	 * @param end
	 */
	function countSoldLeads(start, end){
		return $.ajax({
			url:'/reports/countSoldLeads/'+start+'/'+end,
			headers:{'x-keyStone-nonce': nonce}
		});
	}
	
	/**
	 * Ajax call to count sold leads for the given date range
	 * @param start
	 * @param end
	 */
	function getProfit(start, end){
		return $.ajax({
			url:'/reports/getProfit/'+start+'/'+end,
			headers:{'x-keyStone-nonce': nonce}
		});
	}
	
	/**
	 * 
	 * @param start
	 * @param end
	 * @param type
	 */
	function flotLeadData(start, end){
		return $.ajax({
			url:'/reports/flotLeadData/'+start+'/'+end,
			headers:{'x-keyStone-nonce': nonce}
		});
	}
		
	
	
    function showTooltip(x, y, contents) {
		jQuery('<div id="tooltip" class="tooltipflot">' + contents + '</div>').css( {
		    position: 'absolute',
		    display: 'none',
		    top: y + 5,
		    left: x + 5
		}).appendTo("body").fadeIn(200);
    }
    		
	var previousPoint = null;
	jQuery("#genleadflot").bind("plothover", function (event, pos, item) {
	jQuery("#x").text(pos.x.toFixed(2));
	jQuery("#y").text(pos.y.toFixed(2));
			
	if(item) {
	    if (previousPoint != item.dataIndex) {
		previousPoint = item.dataIndex;
						
		jQuery("#tooltip").remove();
		var x = item.datapoint[0],
		y = item.datapoint[1];
	 			
		showTooltip(item.pageX, item.pageY,
		item.series.label + " : " + y);
	    }
			
	} else {
	    jQuery("#tooltip").remove();
	    previousPoint = null;            
	}
		
    });
		
    jQuery("#genleadflot").bind("plotclick", function (event, pos, item) {
	if (item) {
	    gplot.highlight(item.series, item.datapoint);
	}
    });
    		
	var previousPoint = null;
	jQuery("#soldleadflot").bind("plothover", function (event, pos, item) {
	jQuery("#x").text(pos.x.toFixed(2));
	jQuery("#y").text(pos.y.toFixed(2));
			
	if(item) {
	    if (previousPoint != item.dataIndex) {
		previousPoint = item.dataIndex;
						
		jQuery("#tooltip").remove();
		var x = item.datapoint[0],
		y = item.datapoint[1];
	 			
		showTooltip(item.pageX, item.pageY,
			item.series.label + " : " + y);
	    }
			
	} else {
	    jQuery("#tooltip").remove();
	    previousPoint = null;            
	}
		
    });
		
    jQuery("#soldleadflot").bind("plotclick", function (event, pos, item) {
	if (item) {
	    splot.highlight(item.series, item.datapoint);
	}
    });
    		
	var previousPoint = null;
	jQuery("#profitflot").bind("plothover", function (event, pos, item) {
	jQuery("#x").text(pos.x.toFixed(2));
	jQuery("#y").text(pos.y.toFixed(2));
			
	if(item) {
	    if (previousPoint != item.dataIndex) {
		previousPoint = item.dataIndex;
						
		jQuery("#tooltip").remove();
		var x = item.datapoint[0].toFixed(2),
		y = item.datapoint[1].toFixed(2);
	 			
		showTooltip(item.pageX, item.pageY,
		item.series.label + " : $" + y);
	    }
			
	} else {
	    jQuery("#tooltip").remove();
	    previousPoint = null;            
	}
		
    });
		
    jQuery("#profitflot").bind("plotclick", function (event, pos, item) {
	if (item) {
	    pplot.highlight(item.series, item.datapoint);
	}
    });
    
    flotLeadData(weekstart, tomorrow).done(function(result) {
    	//Chart 1
        var affiliate = result.affiliate.generated;
        var vendor = result.vendor.generated;
    	var gplot = jQuery.plot(jQuery("#genleadflot"),
			[{
			    data: affiliate,
			    label: "Affiliate",
			    color: "#428bca"
			},
		        {
			    data: vendor,
			    label: "Vendor",
			    color: "#b830b3"
		        }
			],
			{
			    series: {
				lines: {
				    show: false
				},
				splines: {
				    show: true,
				    tension: 0.4,
				    lineWidth: 1,
				    fill: 0.4
				},
				shadowSize: 0
			    },
			    points: {
				show: true,
			    },
			    legend: {
				container: '#basicFlotLegend',
		                noColumns: 0
			    },
			    grid: {
				hoverable: true,
				clickable: true,
				borderColor: '#ddd',
				borderWidth: 0,
				labelMargin: 5,
				backgroundColor: '#fff'
			    },
			    yaxis: {
				color: '#eee',
				tickDecimals: 0,
				minTickSize: 5,
				min: 0
			    },
			    xaxis: {
				color: '#eee',
				ticks: [[0.0,'Sun'], [1.0,'Mon'], [2.0,'Tue'], [3.0,'Wed'], [4.0,'Thu'], [5.0,'Fri'], [6.0,'Sat']],
				axisLabelUseCanvas: true,
				axisLabelFontFamily: 'Verdana, Arial, Helvetica, Tahoma, sans-serif',
				axisLabelPadding: 5
			    }
			});
    	
    	//Chart 2
    	var affiliate = result.affiliate.sold;
        var vendor = result.vendor.sold;
    	
        var splot = jQuery.plot(jQuery("#soldleadflot"),
    	[{
    	    data: affiliate,
    	    label: "Affiliate",
    	    color: "#428bca"
    	},
            {
    	    data: vendor,
    	    label: "Vendor",
    	    color: "#b830b3"
            }
    	],
    	{
    	    series: {
    		lines: {
    		    show: false
    		},
    		splines: {
    		    show: true,
    		    tension: 0.4,
    		    lineWidth: 1,
    		    fill: 0.5
    		},
    		shadowSize: 0
    	    },
    	    points: {
    		show: true
    	    },
    	    legend: {
    		container: '#basicFlotLegend2',
                    noColumns: 0
    	    },
    	    grid: {
    		hoverable: true,
    		clickable: true,
    		borderColor: '#ddd',
    		borderWidth: 0,
    		labelMargin: 5,
    		backgroundColor: '#fff'
    	    },
    	    yaxis: {
    		min: 0,
    		color: '#eee',
    		tickDecimals: 0,
			minTickSize: 5,
			min: 0
    	    },
    	    xaxis: {
    		color: '#eee',
    		ticks: [[0.0,'Sun'], [1.0,'Mon'], [2.0,'Tue'], [3.0,'Wed'], [4.0,'Thu'], [5.0,'Fri'], [6.0,'Sat']],
    		axisLabelUseCanvas: true,
    		axisLabelFontFamily: 'Verdana, Arial, Helvetica, Tahoma, sans-serif',
    		axisLabelPadding: 5
    	    }
    	});
        
        //Chart 3
        var affiliate =  result.affiliate.profit;
        var vendor = result.vendor.profit;
    	
        var pplot = jQuery.plot(jQuery("#profitflot"),
    	[{
    	    data: affiliate,
    	    label: "Affiliate",
    	    color: "#428bca"
    	},
            {
    	    data: vendor,
    	    label: "Vendor",
    	    color: "#b830b3"
            }
    	],
    	{
    	    series: {
    		lines: {
    		    show: false
    		},
    		splines: {
    		    show: true,
    		    tension: 0.4,
    		    lineWidth: 1,
    		    fill: 0.4
    		},
    		shadowSize: 0
    	    },
    	    points: {
    		show: true
    	    },
    	    legend: {
    		container: '#basicFlotLegend3',
                    noColumns: 0
    	    },
    	    grid: {
    		hoverable: true,
    		clickable: true,
    		borderColor: '#ddd',
    		borderWidth: 0,
    		labelMargin: 5,
    		backgroundColor: '#fff'
    	    },
    	    yaxis: {
    		min: 0,
    		color: '#eee',
    		tickDecimals: 2,
			minTickSize: 5,
			min: 0
    	    },
    	    xaxis: {
    		color: '#eee',
    		ticks: [[0.0,'Sun'], [1.0,'Mon'], [2.0,'Tue'], [3.0,'Wed'], [4.0,'Thu'], [5.0,'Fri'], [6.0,'Sat']],
    		axisLabelUseCanvas: true,
    		axisLabelFontFamily: 'Verdana, Arial, Helvetica, Tahoma, sans-serif',
    		axisLabelPadding: 5
    	    }
    	});
    })
});   