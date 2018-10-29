
var api_custom_fields = [];

$(function() {
	jQuery("#apirequesttype").select2();
	jQuery("#apitemplatedropdown").select2();
	jQuery('#apipostbuilder').hide();
	jQuery('#apitemplatebuilder').hide();
	jQuery('#apiheadertableblock').hide();
	jQuery('#apiespunit').hide();
	jQuery('#apitestresponse').hide();
});


	//Reload System and Custom fields display
	function apiRefreshAvailableFields(){
		
		if($("#apisystemfieldsdisplay tbody tr").length ==0){
			var str = "";
				for(var i =0; i < system_fields.length; i++){
						if(i == 0){
							str = str+'<tr><td style = "text-align: center">['+system_fields[i][0]+']</td>';
						}else{
							
							if(i%2 ===0){
								str = str+'<tr><td style = "text-align: center">['+system_fields[i][0]+']</td>';	
								
							}else{
								
								str = str+'<td style = "text-align: center">['+system_fields[i][0]+']</td></tr>';
								
								$('#apisystemfieldsdisplay tbody').append(str);
								str = "";	
								
							}
							
						}
				}
				
				
				if(i%2 !==0){
					str = str+'<td style = "text-align: center">&nbsp;</td></tr>';
					$('#apisystemfieldsdisplay tbody').append(str);
						
				}
			}
		
			
			$('#apicustomfieldsdisplay tbody tr').remove();
			
			var str = "";
			for(var i =0; i < api_custom_fields.length; i++){
					if(i == 0){
						str = str+'<tr><td style = "text-align: center">{'+api_custom_fields[i][0]+'}</td>';
					}else{
						
						if(i%2 ===0){
							str = str+'<tr><td style = "text-align: center">{'+api_custom_fields[i][0]+'}</td>';	
							
						}else{
							
							str = str+'<td style = "text-align: center">{'+api_custom_fields[i][0]+'}</td></tr>';
							
							$('#apicustomfieldsdisplay tbody').append(str);
							str = "";	
							
						}
						
					}
			}
			
			
			if(i%2 !==0){
				str = str+'<td style = "text-align: center">&nbsp;</td></tr>';
				$('#apicustomfieldsdisplay tbody').append(str);
					
			}
		
		
	}

	
	function apiCheckBuilderTableOrder(){
		
		if($("#apibuildertable tbody tr").length != 0){
			var table_rows = $("#apibuildertable tbody tr");
					
					table_rows.each(function(index, ele){
						
						var type = $(this).children()[2].textContent;
						if(type == "Custom"){
							var current_field_name = $(this).children()[0].textContent;
							var current_field_value = $(this).children().children().val();
							var current_index =  $(this).index();
							var match = false;
							for(var i =0; i < api_custom_fields.length; i++){
								var c_fn = api_custom_fields[i][0];
								
								if(c_fn == current_field_name){
									match = true;
									$(this).children().children().val(api_custom_fields[i][1]);	
								}
								
								
								
							}
							
							//Remove changed or deleted custom fields
							if(!match){
								$("#apibuildertable tbody tr").eq(index).remove();	
							}
						}
					});
					
					for(var j =0; j < api_custom_fields.length; j++){
						var fname = api_custom_fields[j][0];
						var fvalue = api_custom_fields[j][1];
						var found =  checkApiCustomField(fname);
				
						
						
						if(!found){
							$('#apibuildertable tbody').append('<tr><td style = "text-align: center">'+fname+'</td>'+
												'<td style = "text-align: center"><input readonly class="form-control" type="text" name="apipostfieldname" id="apipostfieldname"></td>'+
												'<td style = "text-align: center">Custom</td></tr>');
							$('#apibuildertable tbody tr:last').find('td').eq(1).find('input').val(fvalue);	
						}
						
					}
		}
	}
	
	
	
	function checkApiCustomField(field){
		var table_rows2 = $("#apibuildertable tbody tr");
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
	
		
	function getApiHeaders(){
		var length = $('#apiheadertable tbody tr').length;
		var temp = [];
		if(length == 0){
			return [];
		}else{
			$('#apiheadertable tbody tr').each(function(index, ele){
				
				temp[index] = [$(this).children()[0].firstChild.value, $(this).children()[1].firstChild.value];
			});
			
			return temp;
		}
	}
	
	
	function getApiBasicAuth(){
		var length = $('#apibasicauthtable tbody tr').length;
		var temp = [];
		
		if(length == 0){
			return temp;
		}else{
			temp[0] = [jQuery("#apibasicauthfield").val(), jQuery("#apibasicauthvalue").val()];
			return temp;	
		}
		
	}
	
	
	function getApiCustomFields(){
		var length = $('#apicustomfieldtable tbody tr').length;
		var temp = [];
		if(length == 0){
			return [];
		}else{
			$('#apicustomfieldtable tbody tr').each(function(index, ele){
				
				temp[index] = [$(this).children()[0].firstChild.value, $(this).children()[1].firstChild.value];
			});
			
			return temp;
		}	
	}
	
	
	function getApiPostFields(){
		var temp = [];
		$('#apibuildertable tbody tr').each(function(index, ele){
			temp[index] = [$(this).children()[0].textContent, $(this).children()[1].firstChild.value, $(this).children()[2].textContent];
		});
		
		return temp;
	}
	
	
	$(document).on('click', '#apiaddheader' , function(e) {
		jQuery('#apiheadertableblock').show();
		var length = $('#apiheadertable tbody tr').length;
		if(length == 0){
			$('#apiheadertable tbody').append('<tr><td><input class="form-control" type="text" name="apiheaderfield" id="apiheaderfield"></td>'+
										'<td><input class="form-control" type="text" name="apiheadervalue" id="apiheadervalue"></td>'+
										'<td><span id="apideleteheader"  style = "cursor:pointer" class = "glyphicon glyphicon-remove" aria-hidden="true"></span></td></tr>');
			
		}else{
			$('#apiheadertable tr:last').after('<tr><td><input class="form-control" type="text" name="apiheaderfield" id="apiheaderfield"></td>'+
										'<td><input class="form-control" type="text" name="apiheadervalue" id="apiheadervalue"></td>'+
										'<td><span id="apideleteheader"  style = "cursor:pointer" class = "glyphicon glyphicon-remove" aria-hidden="true"></span></td></tr>');
		}
	});
	
	$(document).on('click', '#apideleteheader' , function(e) {
	
		var index = $(this).parent().parent().index();
			$('#apiheadertable tbody tr').eq((index)).remove();
		if($('#apiheadertable tbody tr').length == 0){
			jQuery('#apiheadertableblock').hide();	
		}
	});
	
	$(document).on('click', '#apiaddbasicauth' , function(e) {
		
			$('#apibasicauthtable tbody').append('<tr><td><input class="form-control" type="text" name="apibasicauthfield" id="apibasicauthfield"></td>'+
										'<td><input class="form-control" type="text" name="apibasicauthvalue" id="apibasicauthvalue"></td>'+
										'<td><span id="apideletebasicauth"  style = "cursor:pointer" class = "glyphicon glyphicon-remove" aria-hidden="true"></span></td></tr>');
			$('#apiaddbasicauth').hide();
			
		
	});
	
	$(document).on('click', '#apideletebasicauth' , function(e) {
	
		var index = $(this).parent().parent().index();
			$('#apibasicauthtable tbody tr').eq(0).remove();
			$('#apiaddbasicauth').show();
	});
	

	$(document).on('click', '#apiaddcustomfield' , function(e) {
		var length = $('#apicustomfieldtable tbody tr').length;
		if(length == 0){
			$('#apicustomfieldtable tbody').append('<tr><td><input class="form-control" type="text" name="apicustomfield1" id="apicustomfield1"></td>'+
										'<td><input class="form-control" type="text" name="apicustomfieldvalue1" id="apicustomfieldvalue1"></td>'+
										'<td><span id="apideletecustomfield"  style = "cursor:pointer" class = "glyphicon glyphicon-remove" aria-hidden="true"></span></td></tr>');
			
		}else{
			$('#apicustomfieldtable tr:last').after('<tr><td><input class="form-control" type="text" name="apicustomfield'+(length+1)+'" id="apicustomfield'+(length+1)+'"></td>'+
										'<td><input class="form-control" type="text" name="apicustomfieldvalue'+(length+1)+'" id="apicustomfieldvalue'+(length+1)+'"></td>'+
										'<td><span id="apideletecustomfield"  style = "cursor:pointer" class = "glyphicon glyphicon-remove" aria-hidden="true"></span></td></tr>');
		}
	});
	
	
	$(document).on('change', '#apicustomfieldtable tbody td input' , function(e) {
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
		
		api_custom_fields[index] = [val1,val2];
		if(val1 != "" && val2 != ""){
			apiCheckBuilderTableOrder();
			apiRefreshAvailableFields();
		}
	
	});
	
	$(document).on('click', '#apideletecustomfield' , function(e) {
	
		var index = $(this).parent().parent().index();
		api_custom_fields.splice(index,1);
		$('#apicustomfieldtable tbody tr').eq((index)).remove();
		apiCheckBuilderTableOrder();
		apiRefreshAvailableFields();
	});
	
	
	$(document).on('change', '#apitemplatedropdown' , function(e) {
	
		var val = $(this).val();
		if(val == 'No'){
		
			jQuery('#apitemplatebuilder').hide();
			if($('#apibuildertable tbody tr').length == 0){
				
				
				for(var i =0; i < system_fields.length; i++){
					$('#apibuildertable tbody').append('<tr><td style = "text-align: center">'+system_fields[i][0]+'</td>'+
										'<td style = "text-align: center"><input class="form-control" type="text" name="apipostfieldname" id="apipostfieldname"></td>'+
										'<td style = "text-align: center">System</td></tr>');
					
					
				}
				
				
				for(var i =0; i < api_custom_fields.length; i++){
					$('#apibuildertable tbody').append('<tr><td style = "text-align: center">'+api_custom_fields[i][0]+'</td>'+
										'<td style = "text-align: center"><input readonly class="form-control" type="text" name="apipostfieldname" id="apipostfieldname"></td>'+
										'<td style = "text-align: center">Custom</td></tr>');
					$('#apibuildertable tbody tr:last').find('td').eq(1).find('input').val(api_custom_fields[i][1]);
					
				}
				
			
			}else{
				
				
				apiCheckBuilderTableOrder();
				
				
			}	
			jQuery('#apipostbuilder').show();
		}else if(val == 'Yes'){
			jQuery('#apipostbuilder').hide();	
					
			apiRefreshAvailableFields();
			
			jQuery('#apitemplatebuilder').show();
		}else{
			jQuery('#apitemplatebuilder').hide();
			jQuery('#apipostbuilder').hide();		
		}
		
		
	});
	
	
	$(document).on('click', '#apitestposter' , function(e) {
		jQuery('#apitestresponse').hide();
		jQuery('#apitestblock').show();
		jQuery('#apisendtestposter').show();
		jQuery('#testresponsearea').val('');
		
		var apitemplatedropdown = $("#apitemplatedropdown").val();
		
		if(apitemplatedropdown == 'No'){
			jQuery('#apitestpostbuilder').show();
			jQuery('#apitesttemplatebuilder').hide();
			$('#apitestbuildertable tbody tr').remove();
			var apiposter = getApiPostFields();
			
			for(var i =0; i < apiposter.length; i++){
				$('#apitestbuildertable tbody').append('<tr><td style = "text-align: center">'+apiposter[i][0]+'</td>'+
									'<td style = "text-align: center"><input readonly class="form-control" type="text" name="apitesterpostfieldname" id="apitesterpostfieldname"></td>'+
									'<td style = "text-align: center">'+apiposter[i][2]+'</td></tr>');
				$('#apitestbuildertable tbody tr:last').find('td').eq(1).find('input').val(apiposter[i][1]);
					
			}	
			
			
			
		}else{
			
			jQuery('#apitestpostbuilder').hide();
			jQuery('#apitesttemplatebuilder').show();
			$("#apitestpostarea").val($("#apipostarea").val());
			var apisystem_clone = jQuery('#apisystemfieldsdisplay tbody tr').clone();
			var apicustom_clone = jQuery('#apicustomfieldsdisplay tbody tr').clone();
			
			jQuery('#apitestsystemfieldsdisplay tbody tr').remove();
			jQuery('#apitestcustomfieldsdisplay tbody tr').remove();
			
			jQuery('#apitestsystemfieldsdisplay tbody').append(apisystem_clone);
			jQuery('#apitestcustomfieldsdisplay tbody').append(apicustom_clone);
			
			
		}
		
		
		jQuery('#apitestpostdata tbody tr').remove();
		
		for(var i =0; i < system_fields.length; i++){
			$('#apitestpostdata tbody').append('<tr><td style = "text-align: center">'+system_fields[i][0]+'</td>'+
											'<td style = "text-align: center"><input class="form-control" type="text" name="apitester2postfieldname" id="apitester2postfieldname"></td>'+
											'</tr>');
			$('#apitestpostdata tbody tr:last').find('td').eq(1).find('input').val(system_fields[i][2]);
			
			
		}
		
		
		for(var i =0; i < api_custom_fields.length; i++){
			$('#apitestpostdata tbody').append('<tr><td style = "text-align: center">'+api_custom_fields[i][0]+'</td>'+
											'<td style = "text-align: center"><input readonly class="form-control" type="text" name="apitester2postfieldname" id="apitester2postfieldname"></td>'+
											'</tr>');
			$('#apitestpostdata tbody tr:last').find('td').eq(1).find('input').val(api_custom_fields[i][1]);
			
		}
	
	});
	
	
	$(document).on('click', '#apisendtestposter' , function(e) {	
		jQuery('#apitestresponse').show();
		jQuery('#apitestblock').hide();
		jQuery('#apisendtestposter').hide();
		if(esp_id != 0){
			
			var temp = [];
			$('#apitestpostdata tbody tr').each(function(index, ele){
				temp[index] = [$(this).children()[0].textContent, $(this).children()[1].firstChild.value];
			});
			
			$.ajax({
				type: 	"POST",
				url:	"/listManagement/apiTestEsp/"+esp_id,
				headers: {'x-keyStone-nonce': nonce, 'Content-Type':'application/json'},
				data:	 JSON.stringify(temp),
				dataType: "json"
			}).done(function(rsp){
				jQuery('#apitestresponsearea').val(JSON.stringify(rsp,null,4));
				
			});	
		}
	
	});

	

