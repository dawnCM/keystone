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

var pingtree;
var comparison;
var summary;
var display;
var enddate;	
var buyername;
var active_clicked=false;
var rank_clicked=false;
var buyerId;

$(function() {
	jQuery("#select-pingtree").select2();
	  	$("#finish").on('click',function(){
	  		data=[];
	  		var children_obj = $("#contract_order").children("[id*='contract']");
	  		var children_rr_obj = $("#contract_order_rr").children("[id*='contract_rr']");
			var length = children_obj.length;	
	  		for(i=0; i < length; i++){
   				data.push({
   					'ContractName' : children_obj.get(i).textContent,
   					'ContractId'   : children_obj.get(i).id.replace('contract_',''),
   					'Rank'		   : i+1
   				});
   				
   				if(children_rr_obj[i].childNodes[0].children.length > 0){
   					var child_ct = children_rr_obj[i].childNodes[0].children.length;
   					var child_obj = children_rr_obj[i].childNodes[0];
   					
   					for(var j = 0; j < child_ct; j++){
	   					data.push({
		   					'ContractName' : $('#'+child_obj.children[j].id).html(),
		   					'ContractId'   : child_obj.children[j].id.replace('clone',''),
		   					'Rank'		   : i+1
		   				});
		   			}
   					
   				}	
		   	}
	  	});	    
});

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
		
	
	
	
	jQuery("#tablediv").hide();
	
	function findRowByName(name){
		var obj_length = comparison.length;
		
		for(var i = 0; i < obj_length; i++){
			if(comparison[i][2] == name){
				return i;
			}
		}
		
		
	}
	
	$(document).on('click', '#save-pingtree' , function(e) {
		jQuery('#pingtreetabledisplay').fadeOut();
		jQuery('#ContentLoader').fadeIn();
		jQuery('#footerdisplay').fadeOut();
		var table_rows = $("#pingtreetable tr:gt(0)");
		var ct = 0;
		var rank_ct = 0;
		var data = [];
		table_rows.each(function(index, ele){
			var active = "";
			var rank =	"";
			var name = $(this).children()[2].textContent;
			var type = $(this).attr('rowtype');
			var contract_id = $(this).attr('contractid');
			if(type != 'rr'){
				active = $(this).children().children()[0].textContent;
				rank =	$(this).children().children()[1].textContent;
				rank_ct++;
			}else{
				rank = "Rank "+rank_ct;
				active = "Active";
			}
			
			var table_data = [active,rank,name];
			
			//console.log(table_data);
			//console.log(comparison[ct]);
			
			if(JSON.stringify(comparison[ct]) !== JSON.stringify(table_data)){
				org_row_index = findRowByName(name);
				var update_type=false;
				var status_change=false;
				var rank_change=false;
				
				if(type != 'rr'){
					if(table_data[0] != comparison[org_row_index][0]){
						status_change=true;
					}
					
					if(table_data[1] != comparison[org_row_index][1]){
						rank_change=true;
					}
				}else{
					if( comparison[org_row_index][0] != 'Active' ){
						status_change=true;
					}
					
					if(table_data[1] != comparison[org_row_index][1]){
						rank_change=true;
					}	
				}
				if(status_change && rank_change){
					update_type = "sr";
				}else if(status_change && !rank_change){
					update_type = "s";
				}else if(!status_change && rank_change){
					update_type = "r";
				}
				
				
				if(update_type){
					data.push({
	   					'ContractName' 	: table_data[2],
	   					'ContractId'   	: contract_id,
	   					'Rank'		  	: rank_ct,
	   					'Status'		: table_data[0],
	   					'UpdateType'	: update_type,
	   					'BuyerId'		: buyerId
	   				});
				}

			}
		
			//console.log('');
			ct++;
				
		});	
		
		if(data.length == 0){
			$('#nochangemessage').fadeIn('slow');
			setTimeout(function() {
			    $('#nochangemessage').fadeOut('fast');
			}, 3000);
			jQuery('#ContentLoader').fadeOut();
			jQuery('#pingtreetabledisplay').fadeIn();
			jQuery('#footerdisplay').fadeIn();
		}else{
		
			$.ajax({
				type: 	"POST",
				url:	"/pingtree/savePingtreeData",
				headers: {'x-keyStone-nonce': nonce, 'Content-Type':'application/json'},
				data:	 JSON.stringify(data),
				dataType: "json"
			}).done(function(rsp){
				jQuery('#ContentLoader').fadeOut();
				jQuery('#pingtreetabledisplay').fadeIn();
				jQuery('#footerdisplay').fadeIn();
			});	
		}
	});
	
	function reorderRank(){
		var table_rows = $("#pingtreetable tr:gt(0)");
		var rank_ct = 1;
		var index_ct = 1;
		table_rows.each(function(index, ele){
			var rowtype = $(this).attr('rowtype');
			var status = $('#pingtreetable tbody tr').eq(index).find("td").eq(0)[0].textContent;
			
			
			if(rowtype != 'rr'){
				$(this).children().children()[1].textContent = 'Rank '+rank_ct;
				rank_ct++
			}else{
				$(this).children()[1].innerHTML = '<span id="rr_item"  style = "cursor:pointer" class = "glyphicon glyphicon-remove" aria-hidden="true"></span>';
					
			}
			
			if(rowtype == 'main_wrr'){
				$('#pingtreetable tbody tr').eq(index).removeClass();
		    	$('#pingtreetable tbody tr').eq(index).addClass('alert-success');
		    }else if(rowtype == 'rr'){
		    	$('#pingtreetable tbody tr').eq(index).removeClass();
		    	$('#pingtreetable tbody tr').eq(index).addClass('alert-success');
		    }else if(rowtype == 'main' && status == 'Active'){
		    	$('#pingtreetable tbody tr').eq(index).removeClass();
		    	$('#pingtreetable tbody tr').eq(index).addClass('alert-success');
		    }else if(rowtype == 'main' && status == 'Pending'){
		    	$('#pingtreetable tbody tr').eq(index).removeClass();
		    	$('#pingtreetable tbody tr').eq(index).addClass('alert-warning');
		    }else if(rowtype == 'main' && status == 'Inactive'){
		    	$('#pingtreetable tbody tr').eq(index).removeClass();
		    	$('#pingtreetable tbody tr').eq(index).addClass('alert-danger');
			}
			
			index_ct++;
		});
			
	}
	
	$(document).on('click', '#rr_item' , function(e) {
		var row_index = $(this).parent().parent()[0].rowIndex;
		
		var row_clone = $('#pingtreetable tbody tr').eq(row_index-1).clone(); 
		//get parent tr of round robin subsets to access count
		var prev_tr_children = (parseInt($('#pingtreetable tbody tr').eq(row_index-1).prevAll('[rowtype=main_wrr]').first().attr('rr_children'))-1);
		var contract_id = row_clone.attr('contractid');
		
		//set row so later we can update rr_table if needed
		var parent_tr_row = $('#pingtreetable tbody tr').eq(row_index-1).prevAll('[rowtype=main_wrr]').first()[0].rowIndex;
		
		$('#pingtreetable tbody tr').eq(row_index-1).prevAll('[rowtype=main_wrr]').first().attr('rr_children', prev_tr_children);
		
		if(prev_tr_children < 1){
			//$('#rr_table tbody tr').eq(parent_tr_row-1)[0].innerHTML = '<td>&nbsp;</td>';
			$('#pingtreetable tbody tr').eq(row_index-1).prevAll('[rowtype=main_wrr]').first().attr('rowtype','main');
			
		}
		
		//Place the deleted row at bottom of table.
		row_clone.find("td").eq(0).html('<span style = "cursor:pointer;text-decoration: underline;font-style: italic;" id="active_display">Active</span>');
		row_clone.find("td").eq(1).html('<span style = "cursor:pointer;text-decoration: underline;font-style: italic;" id="rank_display">Rank '+$('#pingtreetable tbody tr').length+'</span>');
		$('#pingtreetable tbody tr').eq(row_index-1).remove();
		$('#pingtreetable tbody').append('<tr contractid = "'+contract_id+'" rowtype="main" rr_children = "0">'+row_clone.html()+'</tr>');
		
		reorderRank();	
	});	
	
	$(document).on('click', '#rank_display' , function(e) {
			if(rank_clicked ===true){return;}
			if($(this).parent().parent().attr('rowtype') == 'main_wrr'){return;}
			if($(this).parent()[0].previousElementSibling.textContent != 'Active'){return;};
			
			rank_clicked=true;
			var row_index = $(this).parent().parent()[0].rowIndex;
			var rank_text = $(this).html();
			var rank = parseInt(rank_text.replace("Rank ", ""));
			var table_rows = $("#pingtreetable tr:gt(0)");
		
			
			var select = '<select id="rank_select" rank_row = "'+row_index+'" org_rank = "'+rank_text+'">';
			select += '<option value = "cancel" >CANCEL</option>';
			
			var ct = 1;
			table_rows.each(function(index, ele){
				var status = $(this)[0].childNodes[0].textContent;
				var rowtype = $(this)[0].attributes.rowtype.value;
				
				
				
				if(rank == ct && rowtype != 'rr'){
			
					ct++;
					return;
					
				}
				
				if(rowtype == 'rr'){
					return;
				}else{
					if(status != "Active"){
						ct++;
						return;
					}else{
						select += '<option value = "'+(ct)+'" >RR Rank '+ct+'</option>'; 
						ct++;	
					}
				}
				
			});

			select = select += '</select>';
			$(this).html(select);
			
	});	
	
	$(document).on('blur change mouseleave', '#rank_select' , function(e) {
		
		var value = $(this).val();
		var row_index = parseInt($(this).attr('rank_row')); //original index of item to go in round robin
		
		
		if(value == 'cancel'){
			$(this).parent().html($(this).attr('org_rank'));		
		}else{
			var clone_current = jQuery("#pingtreetable tbody").find("tr:nth-child("+(row_index)+")").clone();
			var contract_id = clone_current.attr('contractid');
			clone_current.find("td").eq(0).html('&nbsp');
			clone_current.find("td").eq(1).html('&nbsp');
			
			jQuery("#pingtreetable tbody").find("tr:nth-child("+(row_index)+")").remove();
			
			var row_main_obj = jQuery('#pingtreetable tbody').find("tr td:nth-child(2) span").filter(function(i,e){
			   
				if( $(this).text() == ("Rank "+value)){
					return true;
				}else{
					return false;
				}
			});
			
			//Index of row that the round robin will go under
			var row_index_main = row_main_obj[0].parentElement.parentElement.rowIndex;
			
			//console.log('main Index - '+row_index_main);
			//console.log(jQuery("#pingtreetable tbody").find("tr:nth-child("+(row_index_main)+")"));
			
			jQuery("#pingtreetable tbody").find("tr:nth-child("+(row_index_main)+")").attr("rowtype",'main_wrr');
			var rr_children = parseInt(jQuery("#pingtreetable tbody").find("tr:nth-child("+(row_index_main)+")").attr("rr_children"))+1;
			jQuery("#pingtreetable tbody").find("tr:nth-child("+(row_index_main)+")").attr("rr_children", rr_children);
			
			jQuery("#pingtreetable > tbody > tr").eq(row_index_main-1).after('<tr contractid="'+contract_id+'" rowtype="rr">'+clone_current.html()+'</tr>');
			
			reorderRank();

		}
		rank_clicked=false;
		
	});

	$(document).on('click', '#active_display' , function(e) {
			if(active_clicked ===true){return;}
			if($(this).parent().parent().attr('rowtype') == 'main_wrr'){return;}
			
			active_clicked=true;
			var value = $(this).html();
			
			var row_index = $(this).parent().parent()[0].rowIndex;
			var select = '<select id="active_select" active_row = "'+row_index+'">';
			var select_options = Array('Active', 'Pending', 'Inactive');
			for(var i = 0; i < 3; i++){
				if(select_options[i] == value){
					select += '<option value = "'+select_options[i]+'" selected>'+select_options[i]+'</option>'; 
				}else{
					select += '<option value = "'+select_options[i]+'">'+select_options[i]+'</option>';
				}
			}
			select = select += '</select>';
			$(this).html(select);
 
	});	

	$(document).on('blur change mouseleave', '#active_select' , function(e) {
		var row_index = $(this).attr('active_row');
		$(this).parent().html($(this).val());
		reorderRank();
		active_clicked=false;
	});
	
	$('#pingtreetable tbody').sortable({
		items: '[rowtype=main],[rowtype=main_wrr]',
		helper:"original",
		//containment: "#pingtreetable tbody",
		
		start: function(event, ui){			
	   		//Move cursor to middle when dragging to round robin table
	 		//$( this ).sortable( "option", "cursorAt", { left: jQuery("#pingtreetable").width() / 2.5 } ); 
	 		//Move cursor to middle when dragging to round robin table
	 		//$( this ).sortable( "option", "cursorAt", { left: jQuery("#pingtreetable").width() / 2.5 } ); 
	
	   	},	
		update: function(event, ui){
			//Move cursor to middle when dragging to round robin table
	 	//	$( this ).sortable( "option", "cursorAt", { left: jQuery("#pingtreetable").width() / 2.5 } ); 
			var rowindex = ui.item[0].rowIndex;
			if(ui.item.attr('rowtype') != 'main'){
				$(this).sortable("cancel");
			}
			
			//Stop sorting of a main into a main round robin with subset round robin rows under 
			var prev_element = ui.item.prev();
			
			if(prev_element.attr('rowtype') == 'main_wrr'){
			 	$(this).sortable("cancel");
			}
		},
		activate: function(event, ui){
	
		},
		receive: function(event, ui) {
	   		
	   	},
		change: function(event, ui){
			
		},
		stop: function(event, ui){
			reorderRank();
		
		},
	}).disableSelection();
			
	jQuery('#generate-pingtree').on('click',function(){
		
		jQuery('#pingtreetable').hide();
		jQuery('#tableLoader').fadeIn();
		jQuery('#pingtreetable > tbody').empty();
		jQuery('#footerdisplay').fadeOut();
		
		
		var buyer_id = jQuery('#select-pingtree').val();
		buyerId = buyer_id; //global
		var buyer_name = jQuery('#select-pingtree option:selected').text();
		//var startdate = moment().subtract(1,'days').format('MM/DD/YYYY');//jQuery('#startdate').val();
		//var enddate = moment().format('MM/DD/YYYY');//jQuery('#enddate').val();
		
				
		jQuery('#pingtreename').html(buyer_name);
		jQuery('#pingtreenamesummary').html(buyer_name);
		jQuery('#pingtreenameheader').html(' - '+buyer_name);
		
		jQuery.when(
			getPingtreeData(buyer_id).done(function(result){
				pingtree = result;
	    	})
		).then(function(){
			
			comparison = pingtree.comparison;
			display = pingtree.display;
			summary = pingtree.summary;
			if(summary != null){
				var summary_template = '<table border ="1" width="100%">'+
										'<tr>'+
											'<th>Total Posts</th>'+
											'<th>Total Sold</th>'+
											'<th>Revenue</th>'+
										'</tr>'+
										'<tr>'+
											'<td>'+summary.posts+'</td>'+
											'<td>'+summary.sold+'</td>'+
											'<td>$'+summary.revenue+'</td>'+
										'</tr>'+
									'</table>';
				
				
				jQuery("#summaryinfo").html(summary_template);
			}
			
			var table = $('#pingtreetable > tbody');
			$.each(display, function() {
					var tr,td1,td2;
					if(this.RowType == 'rr'){
						tr = '<tr  rowtype="rr" contractid="'+this.ContractId+'">';
						td1 = '<td width = "7%">&nbsp;</td>'+
								'<td width = "7%"><span id="rr_item"  style = "cursor:pointer" class = "glyphicon glyphicon-remove" aria-hidden="true"></span></td>';
								
					}else if(this.RowType == 'main'){
						tr = '<tr rr_children="0" rowtype="main" contractid="'+this.ContractId+'">';
						td1 = '<td width = "7%"><span style = "cursor:pointer;text-decoration: underline;font-style: italic;" id="active_display">'+this.Status+'</span></td>'+
								'<td width = "7%"><span style = "cursor:pointer;text-decoration: underline;font-style: italic;" id="rank_display">Rank '+this.Rank+'</span></td>';
					}else{
						tr = '<tr  rowtype="main_wrr" rr_children = "'+this.Children+'" contractid="'+this.ContractId+'">';
						td1 = '<td width = "7%"><span style = "cursor:pointer;text-decoration: underline;font-style: italic;" id="active_display">'+this.Status+'</span></td>'+
								'<td width = "7%"><span style = "cursor:pointer;text-decoration: underline;font-style: italic;" id="rank_display">Rank '+this.Rank+'</span></td>';
					}
					
					td2 = '<td width = "25%">'+this.ContractName+'</td>'+
							'<td width = "5%">'+this.Sold+'</td>' +
							'<td width = "5%">'+this.Sent+'</td>' +
							'<td width = "5%">$ '+this.Revenue+'</td>' +
							'<td width = "5%">'+this.EPL+'</td>';
					
				    table.append(
				        tr  
				        + td1
				        + td2
				        + '</tr>'
				    );
				    
				    if(this.RowType == 'main_wrr'){
				    	table.find('tr:last').removeClass();
				    	table.find('tr:last').addClass('alert-success');
				    }else if(this.RowType == 'rr'){
				    	table.find('tr:last').removeClass();
				    	table.find('tr:last').addClass('alert-success');
				    }else if(this.RowType == 'main' && this.Status == 'Active'){
				    	table.find('tr:last').removeClass();
				    	table.find('tr:last').addClass('alert-success');
				    	//table.find('tr:last').children('td').addClass('alert-success');
				    }else if(this.RowType == 'main' && this.Status == 'Pending'){
				    	table.find('tr:last').removeClass();
				    	table.find('tr:last').addClass('alert-warning');
				    }else if(this.RowType == 'main' && this.Status == 'Inactive'){
				    	table.find('tr:last').removeClass();
				    	table.find('tr:last').addClass('alert-danger');
				    }    
			});
			
			jQuery('#tableLoader').hide();
			jQuery('#pingtreetable').fadeIn();
			jQuery('#save-pingtree').fadeIn();
			jQuery('#footerdisplay').fadeIn();
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
		jQuery('#enddate').datepicker("setDate", moment().subtract(1,'months').endOf('month').format('MM/DD/YYYY'));
	});
		
	
	
});

/**
 * Ajax call to get buyer data or generate report
 * @param start
 * @param end
 * @param buyer
 */
function getPingtreeData(buyer_id){
	return $.ajax({
		url:'/pingtree/getPingtreeData/'+buyer_id,
		headers:{'x-keyStone-nonce': nonce}
	});
}