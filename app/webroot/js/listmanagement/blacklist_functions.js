
var blacklist_custom_fields = [];

$(function() {
	jQuery("#blacklistrequesttype").select2();
	jQuery("#blacklisttemplatedropdown").select2();
	jQuery('#blacklistpostbuilder').hide();
	jQuery('#blacklisttemplatebuilder').hide();
	jQuery('#blacklistheadertableblock').hide();
	jQuery('#blacklistespunit').hide();
	jQuery('#blacklisttestresponse').hide();
});


	//Reload System and Custom fields display
	function blackListRefreshAvailableFields(){
		
		/*if($("#blacklistsystemfieldsdisplay tbody tr").length ==0){
			var str = "";
				for(var i =0; i < system_fields.length; i++){
						if(i == 0){
							str = str+'<tr><td style = "text-align: center">['+system_fields[i][0]+']</td>';
						}else{
							
							if(i%2 ===0){
								str = str+'<tr><td style = "text-align: center">['+system_fields[i][0]+']</td>';	
								
							}else{
								
								str = str+'<td style = "text-align: center">['+system_fields[i][0]+']</td></tr>';
								
								$('#blacklistsystemfieldsdisplay tbody').append(str);
								str = "";	
								
							}
							
						}
				}
				
				
				if(i%2 !==0){
					str = str+'<td style = "text-align: center">&nbsp;</td></tr>';
					$('#blacklistsystemfieldsdisplay tbody').append(str);
						
				}
		}*/
		
			
			$('#blacklistcustomfieldsdisplay tbody tr').remove();
			
			var str = "";
			for(var i =0; i < blacklist_custom_fields.length; i++){
					if(i == 0){
						str = str+'<tr><td style = "text-align: center">{'+blacklist_custom_fields[i][0]+'}</td>';
					}else{
						
						if(i%2 ===0){
							str = str+'<tr><td width="250px" style = "text-align: center">{'+blacklist_custom_fields[i][0]+'}</td>';	
							
						}else{
							
							str = str+'<td style = "text-align: center">{'+blacklist_custom_fields[i][0]+'}</td></tr>';
							
							$('#blacklistcustomfieldsdisplay tbody').append(str);
							str = "";	
							
						}
						
					}
			}
			
			
			if(i%2 !==0){
				str = str+'<td style = "text-align: center">&nbsp;</td></tr>';
				$('#blacklistcustomfieldsdisplay tbody').append(str);
					
			}
		
		
	}

	
	function blackListCheckBuilderTableOrder(){
		$('#blacklistbuildertable tbody tr').remove();
		for(var j =0; j < blacklist_custom_fields.length; j++){
			var fname = blacklist_custom_fields[j][0];
			var fvalue = blacklist_custom_fields[j][1];
			var found =  checkblackListCustomField(fname);
	
			
			
			if(!found){
				$('#blacklistbuildertable tbody').append('<tr><td style = "text-align: center">'+fname+'</td>'+
									'<td style = "text-align: center"><input readonly class="form-control" type="text" name="blacklistpostfieldname" id="blacklistpostfieldname"></td>'+
									'<td style = "text-align: center">Custom</td></tr>');
				$('#blacklistbuildertable tbody tr:last').find('td').eq(1).find('input').val(fvalue);	
			}
			
		}
	}
	
	
	
	function checkblackListCustomField(field){
		var table_rows2 = $("#blacklistbuildertable tbody tr");
		var match = false;
		var customfound = false;
		table_rows2.each(function(index, ele){
			var cname = ele.childNodes[0].textContent;
			var type = $(this).children()[2].textContent;
			
			if(type == 'Custom'){
				customfound = true;
				if(cname == field){
					match = true;
				}
			}
			
							
		});
		
		//There may not be any customfields created
		if(!match && !customfound){
			return false;
		}else{
			return match;	
		}
		
	}
	
		
	function getBlackListHeaders(){
		var length = $('#blacklistheadertable tbody tr').length;
		var temp = [];
		if(length == 0){
			return [];
		}else{
			$('#blacklistheadertable tbody tr').each(function(index, ele){
				
				temp[index] = [$(this).children()[0].firstChild.value, $(this).children()[1].firstChild.value];
			});
			
			return temp;
		}
	}
	
	
	function getBlackListBasicAuth(){
		var length = $('#blacklistbasicauthtable tbody tr').length;
		var temp = [];
		
		if(length == 0){
			return temp;
		}else{
			temp[0] = [jQuery("#blacklistbasicauthfield").val(), jQuery("#blacklistbasicauthvalue").val()];
			return temp;	
		}
		
	}
	
	
	function getBlackListCustomFields(){
		var length = $('#blacklistcustomfieldtable tbody tr').length;
		var temp = [];
		if(length == 0){
			return [];
		}else{
			$('#blacklistcustomfieldtable tbody tr').each(function(index, ele){
				
				temp[index] = [$(this).children()[0].firstChild.value, $(this).children()[1].firstChild.value];
			});
			
			return temp;
		}	
	}
	
	
	function getBlackListPostFields(){
		var temp = [];
		$('#blacklistbuildertable tbody tr').each(function(index, ele){
			temp[index] = [$(this).children()[0].textContent, $(this).children()[1].firstChild.value, $(this).children()[2].textContent];
		});
		
		return temp;
	}
	
	
	$(document).on('click', '#blacklistaddheader' , function(e) {
		jQuery('#blacklistheadertableblock').show();
		var length = $('#blacklistheadertable tbody tr').length;
		if(length == 0){
			$('#blacklistheadertable tbody').append('<tr><td><input class="form-control" type="text" name="blacklistheaderfield" id="blacklistheaderfield"></td>'+
										'<td><input class="form-control" type="text" name="blacklistheadervalue" id="blacklistheadervalue"></td>'+
										'<td><span id="blacklistdeleteheader"  style = "cursor:pointer" class = "glyphicon glyphicon-remove" aria-hidden="true"></span></td></tr>');
			
		}else{
			$('#blacklistheadertable tr:last').after('<tr><td><input class="form-control" type="text" name="blacklistheaderfield" id="blacklistheaderfield"></td>'+
										'<td><input class="form-control" type="text" name="blacklistheadervalue" id="blacklistheadervalue"></td>'+
										'<td><span id="blacklistdeleteheader"  style = "cursor:pointer" class = "glyphicon glyphicon-remove" aria-hidden="true"></span></td></tr>');
		}
	});
	
	$(document).on('click', '#blacklistdeleteheader' , function(e) {
	
		var index = $(this).parent().parent().index();
			$('#blacklistheadertable tbody tr').eq((index)).remove();
		if($('#blacklistheadertable tbody tr').length == 0){
			jQuery('#blacklistheadertableblock').hide();	
		}
	});
	
	$(document).on('click', '#blacklistaddbasicauth' , function(e) {
		
			$('#blacklistbasicauthtable tbody').append('<tr><td><input class="form-control" type="text" name="blacklistbasicauthfield" id="blacklistbasicauthfield"></td>'+
										'<td><input class="form-control" type="text" name="blacklistbasicauthvalue" id="blacklistbasicauthvalue"></td>'+
										'<td><span id="blacklistdeletebasicauth"  style = "cursor:pointer" class = "glyphicon glyphicon-remove" aria-hidden="true"></span></td></tr>');
			$('#blacklistaddbasicauth').hide();
			
		
	});
	
	$(document).on('click', '#blacklistdeletebasicauth' , function(e) {
	
		var index = $(this).parent().parent().index();
			$('#blacklistbasicauthtable tbody tr').eq(0).remove();
			$('#blacklistaddbasicauth').show();
	});
	

	$(document).on('click', '#blacklistaddcustomfield' , function(e) {
		var length = $('#blacklistcustomfieldtable tbody tr').length;
		if(length == 0){
			$('#blacklistcustomfieldtable tbody').append('<tr><td><input class="form-control" type="text" name="blacklistcustomfield1" id="blacklistcustomfield1"></td>'+
										'<td width = "230px"><input class="form-control"  type="text" name="blacklistcustomfieldvalue1" id="blacklistcustomfieldvalue1"></td>'+
										'<td><span id="blacklistdeletecustomfield"  style = "cursor:pointer" class = "glyphicon glyphicon-remove" aria-hidden="true"></span></td></tr>');
			
		}else{
			$('#blacklistcustomfieldtable tr:last').after('<tr><td><input class="form-control" type="text" name="blacklistcustomfield'+(length+1)+'" id="blacklistcustomfield'+(length+1)+'"></td>'+
										'<td width = "230px"><input class="form-control"  type="text" name="blacklistcustomfieldvalue'+(length+1)+'" id="blacklistcustomfieldvalue'+(length+1)+'"></td>'+
										'<td><span id="blacklistdeletecustomfield"  style = "cursor:pointer" class = "glyphicon glyphicon-remove" aria-hidden="true"></span></td></tr>');
		}
	});
	
	
	$(document).on('change', '#blacklistcustomfieldtable tbody td input' , function(e) {
		var index = $(this).parent().parent().index();
		var td_index = $(this).parent().index();
		var value = $(this).val();
		
		if(td_index == 0){
			var val1 = value;
			var val2 = $(this).parent().parent().find('td').eq(1).find("input").val();
		}else{
			var val2 = value;
			var val1 = $(this).parent().parent().find('td').eq(0).find("input").val();
		}
		
		blacklist_custom_fields[index] = [val1,val2];
		console.log(blacklist_custom_fields);
		if(val1 != "" && val2 != ""){
			blackListCheckBuilderTableOrder();
			blackListRefreshAvailableFields();
		}
	
	});
	
	$(document).on('click', '#blacklistdeletecustomfield' , function(e) {
	
		var index = $(this).parent().parent().index();
		blacklist_custom_fields.splice(index,1);
		$('#blacklistcustomfieldtable tbody tr').eq((index)).remove();
		blackListCheckBuilderTableOrder();
		blackListRefreshAvailableFields();
	});
	
	
	$(document).on('change', '#blacklisttemplatedropdown' , function(e) {
	
		var val = $(this).val();
		if(val == 'No'){
		
			jQuery('#blacklisttemplatebuilder').hide();
			if($('#blacklistbuildertable tbody tr').length == 0){
				
				
			/*	for(var i =0; i < system_fields.length; i++){
					$('#blacklistbuildertable tbody').append('<tr><td style = "text-align: center">'+system_fields[i][0]+'</td>'+
										'<td style = "text-align: center"><input class="form-control" type="text" name="blacklistpostfieldname" id="blacklistpostfieldname"></td>'+
										'<td style = "text-align: center">System</td></tr>');
					
					
				}*/
				
				
				for(var i =0; i < blacklist_custom_fields.length; i++){
					$('#blacklistbuildertable tbody').append('<tr><td style = "text-align: center">'+blacklist_custom_fields[i][0]+'</td>'+
										'<td style = "text-align: center"><input readonly class="form-control" type="text" name="blacklistpostfieldname" id="blacklistpostfieldname"></td>'+
										'<td style = "text-align: center">Custom</td></tr>');
					$('#blacklistbuildertable tbody tr:last').find('td').eq(1).find('input').val(blacklist_custom_fields[i][1]);
					
				}
				
			
			}else{
				
				
				blackListCheckBuilderTableOrder();
				
				
			}	
			jQuery('#blacklistpostbuilder').show();
		}else if(val == 'Yes'){
			jQuery('#blacklistpostbuilder').hide();	
					
			blackListRefreshAvailableFields();
			
			jQuery('#blacklisttemplatebuilder').show();
		}else{
			jQuery('#blacklisttemplatebuilder').hide();
			jQuery('#blacklistpostbuilder').hide();		
		}
		
		
	});
	
	
	$(document).on('click', '#blacklisttestposter' , function(e) {
		jQuery('#blacklisttestresponse').hide();
		jQuery('#blacklisttestblock').show();
		jQuery('#blacklistsendtestposter').show();
		jQuery('#testresponsearea').val('');
		
		var blacklisttemplatedropdown = $("#blacklisttemplatedropdown").val();
		
		if(blacklisttemplatedropdown == 'No'){
			jQuery('#blacklisttestpostbuilder').show();
			jQuery('#blacklisttesttemplatebuilder').hide();
			$('#blacklisttestbuildertable tbody tr').remove();
			var blacklistposter = getBlackListPostFields();
			
			for(var i =0; i < blacklistposter.length; i++){
				$('#blacklisttestbuildertable tbody').append('<tr><td style = "text-align: center">'+blacklistposter[i][0]+'</td>'+
									'<td style = "text-align: center"><input readonly class="form-control" type="text" name="blacklisttesterpostfieldname" id="blacklisttesterpostfieldname"></td>'+
									'<td style = "text-align: center">'+blacklistposter[i][2]+'</td></tr>');
				$('#blacklisttestbuildertable tbody tr:last').find('td').eq(1).find('input').val(blacklistposter[i][1]);
					
			}	
			
			
			
		}else{
			
			jQuery('#blacklisttestpostbuilder').hide();
			jQuery('#blacklisttesttemplatebuilder').show();
			$("#blacklisttestpostarea").val($("#blacklistpostarea").val());
			//var blacklistsystem_clone = jQuery('#blacklistsystemfieldsdisplay tbody tr').clone();
			var blacklistcustom_clone = jQuery('#blacklistcustomfieldsdisplay tbody tr').clone();
			
			jQuery('#blacklisttestsystemfieldsdisplay tbody tr').remove();
			jQuery('#blacklisttestcustomfieldsdisplay tbody tr').remove();
			
			//jQuery('#blacklisttestsystemfieldsdisplay tbody').append(system_clone);
			jQuery('#blacklisttestcustomfieldsdisplay tbody').append(blacklistcustom_clone);
			
			
		}
		
		
		jQuery('#blacklisttestpostdata tbody tr').remove();
		
		/*for(var i =0; i < system_fields.length; i++){
			$('#blacklisttestpostdata tbody').append('<tr><td style = "text-align: center">'+system_fields[i][0]+'</td>'+
											'<td style = "text-align: center"><input class="form-control" type="text" name="blacklisttester2postfieldname" id="blacklisttester2postfieldname"></td>'+
											'</tr>');
			$('#blacklisttestpostdata tbody tr:last').find('td').eq(1).find('input').val(system_fields[i][2]);
			
			
		}*/
		
		
		for(var i =0; i < blacklist_custom_fields.length; i++){
			$('#blacklisttestpostdata tbody').append('<tr><td style = "text-align: center">'+blacklist_custom_fields[i][0]+'</td>'+
											'<td style = "text-align: center"><input readonly class="form-control" type="text" name="blacklisttester2postfieldname" id="blacklisttester2postfieldname"></td>'+
											'</tr>');
			$('#blacklisttestpostdata tbody tr:last').find('td').eq(1).find('input').val(blacklist_custom_fields[i][1]);
			
		}
	
	});
	
	
	$(document).on('click', '#blacklistsendtestposter' , function(e) {	
		jQuery('#blacklisttestresponse').show();
		jQuery('#blacklisttestblock').hide();
		jQuery('#blacklistsendtestposter').hide();
		if(esp_id != 0){
			
			var temp = [];
			$('#blacklisttestpostdata tbody tr').each(function(index, ele){
				temp[index] = [$(this).children()[0].textContent, $(this).children()[1].firstChild.value];
			});
			
			$.ajax({
				type: 	"POST",
				url:	"/listManagement/blackListTestEsp/"+esp_id,
				headers: {'x-keyStone-nonce': nonce, 'Content-Type':'application/json'},
				data:	 JSON.stringify(temp),
				dataType: "json"
			}).done(function(rsp){
				jQuery('#blacklisttestresponsearea').val(JSON.stringify(rsp,null,4));
				
			});	
		}
	
	});

	

