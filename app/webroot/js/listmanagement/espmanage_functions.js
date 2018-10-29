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

var custom_fields = [];
var date_fields = ['OptIn', 'DateOfBirth'];

var system_fields = [];
system_fields[0] = ["Affiliate",true,"126"];
system_fields[1] = ["OfferId",true,"36"];
system_fields[2] = ["CampaignId",true,"260"];
system_fields[3] = ["CreativeId",false,"87"];
system_fields[4] = ["SubId1",false,""];
system_fields[5] = ["IPAddress",true,"74.95.28.129"];
system_fields[6] = ["Url",true,"http://test.com"];
system_fields[7] = ["Email",true,"test@go.com"];
system_fields[8] = ["LoanAmount",false,"800"];
system_fields[9] = ["FirstName",true,"test"];
system_fields[10] = ["LastName",true,"test"];
system_fields[11] = ["Address1",false,"3 test street"];
system_fields[12] = ["City",false,"test"];
system_fields[13] = ["State",false,"GA"];
system_fields[14] = ["Zip",false,"30106"];
system_fields[15] = ["HomePhone",false,"7709415653"];
system_fields[16] = ["CellPhone",false,"7709426565"];
system_fields[17] = ["WorkPhone",false,"7709427689"];
system_fields[18] = ["DateOfBirth",false,"05-27-1970"];
system_fields[19] = ["OptIn",true,"02-15-2016"];
system_fields[20] = ["Agree",true,"true"];
system_fields[21] = ["Price",false,"2.00"];


var esp_id = 0;
var esp_config;

$(function() {
	jQuery("#requesttype").select2();
	jQuery("#select-esp").select2();
	jQuery("#templatedropdown").select2();
	jQuery("#filteremaildropdown").select2();
	jQuery('#postbuilder').hide();
	jQuery('#templatebuilder').hide();
	jQuery("#filtercampaigndropdown").select2();
	jQuery("#filterhygienedropdown").select2();
	jQuery('#headertableblock').hide();
	jQuery('#espunit').hide();
	jQuery('#testresponse').hide();
	
	function refreshAvailableFields(){
		
		if($("#systemfieldsdisplay tbody tr").length ==0){
			var str = "";
				for(var i =0; i < system_fields.length; i++){
						if(i == 0){
							str = str+'<tr><td style = "text-align: center">['+system_fields[i][0]+']</td>';
						}else{
							
							if(i%2 ===0){
								str = str+'<tr><td style = "text-align: center">['+system_fields[i][0]+']</td>';	
								
							}else{
								
								str = str+'<td style = "text-align: center">['+system_fields[i][0]+']</td></tr>';
								
								$('#systemfieldsdisplay tbody').append(str);
								str = "";	
								
							}
							
						}
				}
				
				
				if(i%2 !==0){
					str = str+'<td style = "text-align: center">&nbsp;</td></tr>';
					$('#systemfieldsdisplay tbody').append(str);
						
				}
			}
		
			
			$('#customfieldsdisplay tbody tr').remove();
			
			var str = "";
			for(var i =0; i < custom_fields.length; i++){
					if(i == 0){
						str = str+'<tr><td style = "text-align: center">{'+custom_fields[i][0]+'}</td>';
					}else{
						
						if(i%2 ===0){
							str = str+'<tr><td style = "text-align: center">{'+custom_fields[i][0]+'}</td>';	
							
						}else{
							
							str = str+'<td style = "text-align: center">{'+custom_fields[i][0]+'}</td></tr>';
							
							$('#customfieldsdisplay tbody').append(str);
							str = "";	
							
						}
						
					}
			}
			
			
			if(i%2 !==0){
				str = str+'<td style = "text-align: center">&nbsp;</td></tr>';
				$('#customfieldsdisplay tbody').append(str);
					
			}
		
		
	}
	
	function checkBuilderTableOrder(){
		
		if($("#buildertable tbody tr").length != 0){
			var table_rows = $("#buildertable tbody tr");
					
					table_rows.each(function(index, ele){
						
						var type = $(this).children()[2].textContent;
						if(type == "Custom"){
							var current_field_name = $(this).children()[0].textContent;
							var current_field_value = $(this).children().children().val();
							var current_index =  $(this).index();
							var match = false;
							for(var i =0; i < custom_fields.length; i++){
								var c_fn = custom_fields[i][0];
								
								if(c_fn == current_field_name){
									match = true;
									$(this).children().children().val(custom_fields[i][1]);	
								}
								
								
								
							}
							
							//Remove changed or deleted custom fields
							if(!match){
								$("#buildertable tbody tr").eq(index).remove();	
							}
						}
					});
					
					for(var j =0; j < custom_fields.length; j++){
						var fname = custom_fields[j][0];
						var fvalue = custom_fields[j][1];
						var found =  checkCustomField(fname);
				
						
						
						if(!found){
							$('#buildertable tbody').append('<tr><td style = "text-align: center">'+fname+'</td>'+
												'<td style = "text-align: center"><input readonly class="form-control" type="text" name="postfieldname" id="postfieldname"></td>'+
												'<td style = "text-align: center">Custom</td></tr>');
							$('#buildertable tbody tr:last').find('td').eq(1).find('input').val(fvalue);	
						}
						
					}
		}
	}
	
	function checkCustomField(field){
		var table_rows2 = $("#buildertable tbody tr");
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
	
	function getHeaders(){
		var length = $('#headertable tbody tr').length;
		var temp = [];
		if(length == 0){
			return [];
		}else{
			$('#headertable tbody tr').each(function(index, ele){
				
				temp[index] = [$(this).children()[0].firstChild.value, $(this).children()[1].firstChild.value];
			});
			
			return temp;
		}
	}
	
	function getBasicAuth(){
		var length = $('#basicauthtable tbody tr').length;
		var temp = [];
		
		if(length == 0){
			return temp;
		}else{
			temp[0] = [jQuery("#basicauthfield").val(), jQuery("#basicauthvalue").val()];
			return temp;	
		}
		
	}
	
	
	function getCustomFields(){
		var length = $('#customfieldtable tbody tr').length;
		var temp = [];
		if(length == 0){
			return [];
		}else{
			$('#customfieldtable tbody tr').each(function(index, ele){
				
				temp[index] = [$(this).children()[0].firstChild.value, $(this).children()[1].firstChild.value];
			});
			
			return temp;
		}	
	}
	
	
	function getPostFields(){
		var temp = [];
		$('#buildertable tbody tr').each(function(index, ele){
			temp[index] = [$(this).children()[0].textContent, $(this).children()[1].firstChild.value, $(this).children()[2].textContent];
		});
		
		return temp;
	}
	
	
	
	function getDateFormats(){
		var temp = [];
		$('#formatdatetable tbody tr').each(function(index, ele){
			if($(this).children()[2].firstChild.value != ""){
				temp[index] = [$(this).children()[0].textContent, $(this).children()[2].firstChild.value];
			}
		});
		
		return temp;
	}
	
	$(document).on('click', '#create-esp' , function(e) {
		//clear function
		jQuery('#espbox').hide();
		jQuery('#espunit').show();
		
	});
	
	$(document).on('click', '#reset-esp' , function(e) {
		clear();
		jQuery('#select-esp option').remove();
		
		getAllEsps().done(function(result){
			console.log(result);
			for(var i=0; result.length > i; i++){
				console.log(i);
				jQuery('#select-esp').append('<option value="'+result[i][0]+'">'+result[i][1]+'</option>');
			}
			jQuery('#espbox').show();
			jQuery('#espunit').hide();
			
		});
		
	});
	
	
	$(document).on('click', '#load-esp' , function(e) {
		//clear function
		jQuery('#espbox').hide();
		jQuery('#ContentLoader').show();
		
		var load_id = jQuery('#select-esp').val();
		
		jQuery.when(
			getEsp(load_id).done(function(result){
				esp_config = result;
	    	})
		).then(function(){
			
			var config = esp_config.config;
			esp_id = esp_config.id;
			jQuery('#espid').html(esp_id);
			jQuery("#name").val(config.EspName);
			
			$("input[name=espstatus][value='"+config.EspStatus+"']").prop("checked",true);
			
			$('#requesttype').select2('val', config.RequestType);
			$('#apirequesttype').select2('val', config.Api.RequestType);
			$('#blacklistrequesttype').select2('val', config.BlackList.RequestType);

			jQuery("#requesturl").val(config.RequestUrl);
			jQuery("#apirequesturl").val(config.Api.RequestUrl);
			jQuery("#blacklistrequesturl").val(config.BlackList.RequestUrl);
			
			if(config.Headers.length > 0){
				$('#headertable').show();
				
				for(var i = 0; config.Headers.length > i; i++){
					
					$('#headertable tbody').append('<tr><td><input class="form-control" type="text" name="headerfield" id="headerfield"></td>'+
										'<td><input class="form-control" type="text" name="headervalue" id="headervalue"></td>'+
										'<td><span id="deleteheader"  style = "cursor:pointer" class = "glyphicon glyphicon-remove" aria-hidden="true"></span></td></tr>');
					$('#headertable tbody tr:last').find('td').eq(0).find('input').val(config.Headers[i][0]);
					$('#headertable tbody tr:last').find('td').eq(1).find('input').val(config.Headers[i][1]);
					
				}
				
			}
			
			if(config.Api.Headers.length > 0){
				$('#apiheadertable').show();
				
				for(var i = 0; config.Api.Headers.length > i; i++){
					
					$('#apiheadertable tbody').append('<tr><td><input class="form-control" type="text" name="apiheaderfield" id="apiheaderfield"></td>'+
										'<td><input class="form-control" type="text" name="apiheadervalue" id="apiheadervalue"></td>'+
										'<td><span id="apideleteheader"  style = "cursor:pointer" class = "glyphicon glyphicon-remove" aria-hidden="true"></span></td></tr>');
					$('#apiheadertable tbody tr:last').find('td').eq(0).find('input').val(config.Api.Headers[i][0]);
					$('#apiheadertable tbody tr:last').find('td').eq(1).find('input').val(config.Api.Headers[i][1]);
					
				}
				
			}
			
			if(config.BlackList.Headers.length > 0){
				$('#blacklistheadertable').show();
				
				for(var i = 0; config.BlackList.Headers.length > i; i++){
					
					$('#blacklistheadertable tbody').append('<tr><td><input class="form-control" type="text" name="blacklistheaderfield" id="blacklistheaderfield"></td>'+
										'<td><input class="form-control" type="text" name="blacklistheadervalue" id="blacklistheadervalue"></td>'+
										'<td><span id="blacklistdeleteheader"  style = "cursor:pointer" class = "glyphicon glyphicon-remove" aria-hidden="true"></span></td></tr>');
					$('#blacklistheadertable tbody tr:last').find('td').eq(0).find('input').val(config.BlackList.Headers[i][0]);
					$('#blacklistheadertable tbody tr:last').find('td').eq(1).find('input').val(config.BlackList.Headers[i][1]);
					
				}
				
			}
			
			if(config.BasicAuth.length > 0){
					
					$('#basicauthtable tbody').append('<tr><td><input class="form-control" type="text" name="basicauthfield" id="basicauthfield"></td>'+
										'<td><input class="form-control" type="text" name="basicauthvalue" id="basicauthvalue"></td>'+
										'<td><span id="deletebasicauth"  style = "cursor:pointer" class = "glyphicon glyphicon-remove" aria-hidden="true"></span></td></tr>');
					$('#addbasicauth').hide();
					$('#basicauthtable tbody tr:last').find('td').eq(0).find('input').val(config.BasicAuth[0][0]);
					$('#basicauthtable tbody tr:last').find('td').eq(1).find('input').val(config.BasicAuth[0][1]);
					
			}
			
			if(config.Api.BasicAuth.length > 0){
					
					$('#apibasicauthtable tbody').append('<tr><td><input class="form-control" type="text" name="apibasicauthfield" id="apibasicauthfield"></td>'+
										'<td><input class="form-control" type="text" name="apibasicauthvalue" id="apibasicauthvalue"></td>'+
										'<td><span id="apideletebasicauth"  style = "cursor:pointer" class = "glyphicon glyphicon-remove" aria-hidden="true"></span></td></tr>');
					$('#apiaddbasicauth').hide();
					$('#apibasicauthtable tbody tr:last').find('td').eq(0).find('input').val(config.Api.BasicAuth[0][0]);
					$('#apibasicauthtable tbody tr:last').find('td').eq(1).find('input').val(config.Api.BasicAuth[0][1]);
					
			}
			
			if(config.BlackList.BasicAuth.length > 0){
					
					$('#blacklistbasicauthtable tbody').append('<tr><td><input class="form-control" type="text" name="blacklistbasicauthfield" id="blacklistbasicauthfield"></td>'+
										'<td><input class="form-control" type="text" name="blacklistbasicauthvalue" id="blacklistbasicauthvalue"></td>'+
										'<td><span id="blacklistdeletebasicauth"  style = "cursor:pointer" class = "glyphicon glyphicon-remove" aria-hidden="true"></span></td></tr>');
					$('#blacklistaddbasicauth').hide();
					$('#blacklistbasicauthtable tbody tr:last').find('td').eq(0).find('input').val(config.BlackList.BasicAuth[0][0]);
					$('#blacklistbasicauthtable tbody tr:last').find('td').eq(1).find('input').val(config.BlackList.BasicAuth[0][1]);
					
			}
				
			
			jQuery("#acceptstring").val(config.AcceptString);
			jQuery("#acceptcode").val(config.AcceptCode);
			
			jQuery("#apiacceptstring").val(config.Api.AcceptString);
			jQuery("#apiacceptcode").val(config.Api.AcceptCode);
			
			jQuery("#blacklistacceptstring").val(config.BlackList.AcceptString);
			jQuery("#blacklistacceptcode").val(config.BlackList.AcceptCode);
			
			if(config.CustomFields.length > 0){
				
				
				for(var v = 0; config.CustomFields.length > v; v++){
					custom_fields[v] = [config.CustomFields[v][0], config.CustomFields[v][1]];	
					
					$('#customfieldtable tbody').append('<tr><td><input class="form-control" type="text" name="customfield1" id="customfield1"></td>'+
										'<td><input class="form-control" type="text" name="customfieldvalue1" id="customfieldvalue1"></td>'+
										'<td><span id="deletecustomfield"  style = "cursor:pointer" class = "glyphicon glyphicon-remove" aria-hidden="true"></span></td></tr>');
										
					$('#customfieldtable tbody tr:last').find('td').eq(0).find('input').val(config.CustomFields[v][0]);
					$('#customfieldtable tbody tr:last').find('td').eq(1).find('input').val(config.CustomFields[v][1]);
					
				}
				
			}
			
			if(config.Api.CustomFields.length > 0){
				
				
				for(var v = 0; config.Api.CustomFields.length > v; v++){
					api_custom_fields[v] = [config.Api.CustomFields[v][0], config.Api.CustomFields[v][1]];	
					
					$('#apicustomfieldtable tbody').append('<tr><td><input class="form-control" type="text" name="apicustomfield1" id="apicustomfield1"></td>'+
										'<td><input class="form-control" type="text" name="apicustomfieldvalue1" id="apicustomfieldvalue1"></td>'+
										'<td><span id="apideletecustomfield"  style = "cursor:pointer" class = "glyphicon glyphicon-remove" aria-hidden="true"></span></td></tr>');
										
					$('#apicustomfieldtable tbody tr:last').find('td').eq(0).find('input').val(config.Api.CustomFields[v][0]);
					$('#apicustomfieldtable tbody tr:last').find('td').eq(1).find('input').val(config.Api.CustomFields[v][1]);
					
				}
				
			}
			
			if(config.BlackList.CustomFields.length > 0){
				
				
				for(var v = 0; config.BlackList.CustomFields.length > v; v++){
					blacklist_custom_fields[v] = [config.BlackList.CustomFields[v][0], config.BlackList.CustomFields[v][1]];	
					
					$('#blacklistcustomfieldtable tbody').append('<tr><td><input class="form-control" type="text" name="blacklistcustomfield1" id="blacklistcustomfield1"></td>'+
										'<td width = "230px"><input class="form-control" type="text" name="blacklistcustomfieldvalue1" id="blacklistcustomfieldvalue1"></td>'+
										'<td><span id="blacklistdeletecustomfield"  style = "cursor:pointer" class = "glyphicon glyphicon-remove" aria-hidden="true"></span></td></tr>');
										
					$('#blacklistcustomfieldtable tbody tr:last').find('td').eq(0).find('input').val(config.BlackList.CustomFields[v][0]);
					$('#blacklistcustomfieldtable tbody tr:last').find('td').eq(1).find('input').val(config.BlackList.CustomFields[v][1]);
					
				}
				
			}
			
			if(config.FormattedDateFields.length > 0){
				jQuery('#formatdate').hide();
				
				for(var v = 0; date_fields.length > v; v++){
					var match = false;
					for(var i = 0; config.FormattedDateFields.length > i; i++){
						var c_field = config.FormattedDateFields[i][0];
						if(c_field == date_fields[v]){
							match = true;
							$('#formatdatetable tbody').append('<tr><td style = "text-align: center">'+config.FormattedDateFields[i][0]+'</td>'+
															'<td style = "text-align: center">m-d-Y</td>'+
															'<td style = "text-align: center"><input class="form-control" type="text" name="dateformatval" id="dateformatval"></td>'+
															'</tr>');
							$('#formatdatetable tbody tr:last').find('td').eq(2).find('input').val(config.FormattedDateFields[i][1]);	
						}
					}
					
					if(!match){
						$('#formatdatetable tbody').append('<tr><td style = "text-align: center">'+date_fields[v]+'</td>'+
															'<td style = "text-align: center">m-d-Y</td>'+
															'<td style = "text-align: center"><input class="form-control" type="text" name="dateformatval" id="dateformatval"></td>'+
															'</tr>');		
					}
					
				}
				
			}
			
			if(config.Template != "" || config.PostFields.length != 0 ){
				if(config.Template == ""){
					$('#templatedropdown').select2('val', 'No');
					for(var i =0; i < config.PostFields.length; i++){
						
						if(config.PostFields[i][2] == 'System'){
							$('#buildertable tbody').append('<tr><td style = "text-align: center">'+config.PostFields[i][0]+'</td>'+
												'<td style = "text-align: center"><input class="form-control" type="text" name="postfieldname" id="postfieldname"></td>'+
												'<td style = "text-align: center">'+config.PostFields[i][2]+'</td></tr>');
							$('#buildertable tbody tr:last').find('td').eq(1).find('input').val(config.PostFields[i][1]);
						}else{
							
							$('#buildertable tbody').append('<tr><td style = "text-align: center">'+config.PostFields[i][0]+'</td>'+
											'<td style = "text-align: center"><input readonly class="form-control" type="text" name="postfieldname" id="postfieldname"></td>'+
											'<td style = "text-align: center">Custom</td></tr>');
							$('#buildertable tbody tr:last').find('td').eq(1).find('input').val(config.PostFields[i][1]);	
						}
						
					}
					
					jQuery('#postbuilder').show();
					
				}else{
					$('#templatedropdown').select2('val', 'Yes');
					$( "#templatedropdown" ).trigger( "change" );
					$("#postarea").val(config.Template);
					jQuery('#templatebuilder').show();
				}
			}
			
			if(config.Api.Template != "" || config.Api.PostFields.length != 0 ){
				if(config.Api.Template == ""){
					$('#apitemplatedropdown').select2('val', 'No');
					for(var i =0; i < config.Api.PostFields.length; i++){
						
						if(config.Api.PostFields[i][2] == 'System'){
							$('#apibuildertable tbody').append('<tr><td style = "text-align: center">'+config.Api.PostFields[i][0]+'</td>'+
												'<td style = "text-align: center"><input class="form-control" type="text" name="apipostfieldname" id="apipostfieldname"></td>'+
												'<td style = "text-align: center">'+config.Api.PostFields[i][2]+'</td></tr>');
							$('#apibuildertable tbody tr:last').find('td').eq(1).find('input').val(config.Api.PostFields[i][1]);
						}else{
							
							$('#apibuildertable tbody').append('<tr><td style = "text-align: center">'+config.Api.PostFields[i][0]+'</td>'+
											'<td style = "text-align: center"><input readonly class="form-control" type="text" name="apipostfieldname" id="apipostfieldname"></td>'+
											'<td style = "text-align: center">Custom</td></tr>');
							$('#apibuildertable tbody tr:last').find('td').eq(1).find('input').val(config.Api.PostFields[i][1]);	
						}
						
					}
					
					jQuery('#apipostbuilder').show();
					
				}else{
					$('#apitemplatedropdown').select2('val', 'Yes');
					$( "#apitemplatedropdown" ).trigger( "change" );
					$("#apipostarea").val(config.Template);
					jQuery('#apitemplatebuilder').show();
				}
			}
			
			if(config.BlackList.Template != "" || config.BlackList.PostFields.length != 0 ){
				if(config.BlackList.Template == ""){
					$('#blacklisttemplatedropdown').select2('val', 'No');
					for(var i =0; i < config.BlackList.PostFields.length; i++){
						
						if(config.BlackList.PostFields[i][2] == 'System'){
							$('#blacklistbuildertable tbody').append('<tr><td style = "text-align: center">'+config.BlackList.PostFields[i][0]+'</td>'+
												'<td style = "text-align: center"><input class="form-control" type="text" name="blacklistpostfieldname" id="blacklistpostfieldname"></td>'+
												'<td style = "text-align: center">'+config.BlackList.PostFields[i][2]+'</td></tr>');
							$('#blacklistbuildertable tbody tr:last').find('td').eq(1).find('input').val(config.BlackList.PostFields[i][1]);
						}else{
							
							$('#blacklistbuildertable tbody').append('<tr><td style = "text-align: center">'+config.BlackList.PostFields[i][0]+'</td>'+
											'<td style = "text-align: center"><input readonly class="form-control" type="text" name="blacklistpostfieldname" id="blacklistpostfieldname"></td>'+
											'<td style = "text-align: center">Custom</td></tr>');
							$('#blacklistbuildertable tbody tr:last').find('td').eq(1).find('input').val(config.BlackList.PostFields[i][1]);	
						}
						
					}
					
					jQuery('#blacklistpostbuilder').show();
					
				}else{
					$('#blacklisttemplatedropdown').select2('val', 'Yes');
					$( "#blacklisttemplatedropdown" ).trigger( "change" );
					$("#blacklistpostarea").val(config.Template);
					jQuery('#blacklisttemplatebuilder').show();
				}
			}
			
			
			if(config.Filters.length > 0){
				for(var t =0; t < config.Filters.length; t++){
					
					if(config.Filters[t][0] == 'email'){
						$('#filteremaildropdown').select2('val', config.Filters[t][1]);
						jQuery("#filteremailarea").val(config.Filters[t][2]);	
					}
					
					if(config.Filters[t][0] == 'campaign'){
						$('#filtercampaigndropdown').select2('val', config.Filters[t][1]);
						jQuery("#filtercampaignarea").val(config.Filters[t][2]);	
					}
					
					if(config.Filters[t][0] == 'hygiene'){
						$('#filterhygienedropdown').select2('val', config.Filters[t][1]);
						
						jQuery("#filterhygienearea").val(config.Filters[t][2]);	
					}
				}	
				
			}

			
		});
		
		
		
		jQuery('#ContentLoader').hide();	
		jQuery('#espunit').show();	
	});
	
	function clear(){
		
		esp_id=0;
		esp_config="";
		jQuery("#espid").html("");
		jQuery("#name").val('');
		$("input[name=espstatus][value='2']").prop("checked",true);
		
			
		jQuery("#requesturl").val('');
		jQuery("#requesttype").select2('val', '');
		jQuery("#headertable tbody tr").remove();
		jQuery("#basicauthtable tbody tr").remove();
		
		jQuery("#acceptstring").val("");
		jQuery("#acceptcode").val("");
		custom_fields = [];
		jQuery("#customfieldtable tbody tr").remove();
		
		jQuery('#formatdate').show();
		$('#formatdatetable tbody tr').remove();
	
		$('#templatedropdown').select2('val', '');
		jQuery("#buildertable tbody tr").remove();
		jQuery("#postarea").val("");
		
		jQuery('#postbuilder').hide();
		jQuery('#templatebuilder').hide();
		
		$('#filteremaildropdown').select2('val', '');
		jQuery("#filteremailarea").val('');
		
		$('#filtercampaigndropdown').select2('val', '');
		jQuery("#filtercampaignarea").val('');
		
		$('#filterhygienedropdown').select2('val', '');
		jQuery("#filterhygienearea").val('');
		
		
		
		jQuery("#apirequesttype").select2('val', '');
		jQuery("#apirequesturl").val('');
		jQuery("#apiheadertable tbody tr").remove();
		jQuery("#apibasicauthtable tbody tr").remove();
		
		jQuery("#apiacceptstring").val("");
		jQuery("#apiacceptcode").val("");
		api_custom_fields = [];
		jQuery("#apicustomfieldtable tbody tr").remove();
		jQuery("#apipostarea").val("");	
		$('#apitemplatedropdown').select2('val', '');
		jQuery("#apibuildertable tbody tr").remove();		
		jQuery('#apipostbuilder').hide();
		jQuery('#apitemplatebuilder').hide();
		
		jQuery("#blacklistrequesttype").select2('val', '');
		jQuery("#blacklistrequesturl").val('');
		jQuery("#blacklistheadertable tbody tr").remove();
		jQuery("#blacklistbasicauthtable tbody tr").remove();
		
		jQuery("#blacklistacceptstring").val("");
		jQuery("#blacklistacceptcode").val("");
		blacklist_custom_fields = [];
		jQuery("#blacklistcustomfieldtable tbody tr").remove();
		jQuery("#blacklistpostarea").val("");	
		$('#blacklisttemplatedropdown').select2('val', '');
		jQuery("#blacklistbuildertable tbody tr").remove();		
		jQuery('#blacklistpostbuilder').hide();
		jQuery('#blacklisttemplatebuilder').hide();
	
	
	}
	
	
	$(document).on('click', '#save-esp' , function(e) {
		
		//jQuery('#content').hide();
		//jQuery('#ContentLoader').show();
		var error = [];
		var e = 0;
		//Esp Info Tab
		var espname = jQuery("#name").val();
		var espstatus = $("input[name=espstatus]:checked").val();
		
		if(espname == ""){
			error[e] = "Esp Information";
			e++;
			$('#formgroupname').addClass("basicError");
			
		}else{
			$('#formgroupname').removeClass("basicError");
		}
		
		
		//Esp Post Info
		var info_temp_error = false;
		var requesttype = jQuery("#requesttype").val();
		var requesturl = jQuery("#requesturl").val();
		var headers = getHeaders();
		var basicauth = getBasicAuth();
		var acceptstring = jQuery("#acceptstring").val();
		var acceptcode = jQuery("#acceptcode").val();
		var customfields = getCustomFields();
		var dateformatted = getDateFormats();
		var templatedropdown = $("#templatedropdown").val();
		
		if(templatedropdown == 'No'){
			var posttemplate = "";
			var postfields = getPostFields();
		}else if(templatedropdown == 'Yes'){
			var postfields = [];
			var posttemplate = $("#postarea").val();
		}else{
			var postfields = [];
			var posttemplate = "";
		}
		
		
		//API Subscriber Delete Esp Post Info
		var api_requesttype = jQuery("#apirequesttype").val();
		var api_requesturl = jQuery("#apirequesturl").val();
		var api_headers = getApiHeaders();
		var api_basicauth = getApiBasicAuth();
		var api_acceptstring = jQuery("#apiacceptstring").val();
		var api_acceptcode = jQuery("#apiacceptcode").val();
		var api_customfields = getApiCustomFields();
		var api_templatedropdown = $("#apitemplatedropdown").val();
		
		if(api_templatedropdown == 'No'){
			var api_posttemplate = "";
			var api_postfields = getApiPostFields();
		}else if(api_templatedropdown == 'Yes'){
			var api_postfields = [];
			var api_posttemplate = $("#apipostarea").val();
		}else{
			var api_postfields = [];
			var api_posttemplate = "";
		}
		
		
		//API Subscriber Pull Of BlackList
		var blacklist_requesttype = jQuery("#blacklistrequesttype").val();
		var blacklist_requesturl = jQuery("#blacklistrequesturl").val();
		var blacklist_headers = getBlackListHeaders();
		var blacklist_basicauth = getBlackListBasicAuth();
		var blacklist_acceptstring = jQuery("#blacklistacceptstring").val();
		var blacklist_acceptcode = jQuery("#blacklistacceptcode").val();
		var blacklist_customfields = getBlackListCustomFields();
		var blacklist_templatedropdown = $("#blacklisttemplatedropdown").val();
		
		if(blacklist_templatedropdown == 'No'){
			var blacklist_posttemplate = "";
			var blacklist_postfields = getBlackListPostFields();
		}else if(blacklist_templatedropdown == 'Yes'){
			var blacklist_postfields = [];
			var blacklist_posttemplate = $("#blacklistpostarea").val();
		}else{
			var blacklist_postfields = [];
			var blacklist_posttemplate = "";
		}
		
		
		//skip if status is inactive
		if(espstatus == "1" || espstatus == "3"){
			
			if(requesttype == ""){
				info_temp_error = true;
				$('#formgrouprequesttype').addClass("basicError");
				
			}else{
				$('#formgrouprequesttype').removeClass("basicError");
			}
			
			if(requesturl == ""){
				info_temp_error = true;
				$('#formgrouprequesturl').addClass("basicError");
				
			}else{
				$('#formgrouprequesturl').removeClass("basicError");
			}
			
			if(headers.length > 0){
				var header_error = false;
				for(var h = 0; headers.length > h; h++){
					if(headers[h][0] == "" || headers[h][1] == ""){
						info_temp_error = true;	
						header_error = true;
						$('#formgroupheader').addClass("basicError");
					}
					
				}
				
				if(!header_error){
					$('#formgroupheader').removeClass("basicError");	
				}
				
							
			}else{
				$('#formgroupheader').removeClass("basicError");
			}
			
			
			
			if(basicauth.length > 0){
				var basicauth_error = false;
				if(basicauth[0][0] == "" || basicauth[0][1] == ""){
					basicauth_error = true;	
					info_temp_error = true;	
					$('#formgroupbasicauth').addClass("basicError");
				}else{
					$('#formgroupbasicauth').removeClass("basicError");
				}
				
				
							
			}else{
				$('#formgroupbasicauth').removeClass("basicError");
				
			}
			
			
			if(acceptstring == "" && acceptcode == ""){
				info_temp_error = true;
				$('#formgroupacceptstring').addClass("basicError");
				$('#formgroupacceptcode').addClass("basicError");
			}else{
				$('#formgroupacceptstring').removeClass("basicError");
				$('#formgroupacceptcode').removeClass("basicError");
			}
			
			if(customfields.length > 0){
				var customfields_error = false;
				for(var h = 0; customfields.length > h; h++){
					if(customfields[h][0] == "" || customfields[h][1] == ""){
						info_temp_error = true;	
						customfields_error = true;
						$('#formgroupcustomfields').addClass("basicError");
					}
					
				}
				
				if(!customfields_error){
					$('#formgroupcustomfields').removeClass("basicError");	
				}
				
							
			}else{
				$('#formgroupcustomfields').removeClass("basicError");
			}
			
			
			if(templatedropdown == ""){
				info_temp_error = true;	
				$('#formgrouptemplate').addClass("basicError");	
			}else{
				$('#formgrouptemplate').removeClass("basicError");
			}
			
			
			if(templatedropdown == 'Yes'){
				if(posttemplate == ""){
					info_temp_error = true;	
					$('#templatebuilder').addClass("basicError");		
				}else{
					$('#templatebuilder').removeClass("basicError");
				}
			}else{
				$('#templatebuilder').removeClass("basicError");
			}
			
			
			if(info_temp_error){
				error[e] = "Esp Post Information";
				e++;	
				
			}
		}
		
		
		
		//console.log(headers);
		//console.log(basicauth);
		//console.log(customfields);
		//console.log(postfields);
		//console.log(posttemplate);
		
		
		//Filter Info
		var filter = [];
		var i = 0;
		var filter_error = false;
		
		if(jQuery("#filteremaildropdown").val() != "" || jQuery("#filteremailarea").val() != ""){
			filter[i] = ['email', jQuery("#filteremaildropdown").val(), jQuery("#filteremailarea").val()];
			i++;
		}
		
		if(jQuery("#filtercampaigndropdown").val() != "" || jQuery("#filtercampaignarea").val() != ""){
			filter[i] = ['campaign', jQuery("#filtercampaigndropdown").val(), jQuery("#filtercampaignarea").val()];
			i++;
		}
		
		if(jQuery("#filterhygienedropdown").val() != "" || jQuery("#filterhygienearea").val() != ""){
			filter[i] = ['hygiene', jQuery("#filterhygienedropdown").val(), jQuery("#filterhygienearea").val()];
			i++;
		}
		
		
		if(filter.length > 0 ){
			
			for(var h = 0; filter.length > h; h++){
				if(filter[h][1] == "" || filter[h][2] == ""){
					filter_error = true;
					if(filter[h][0] == 'email'){
						$('#formgroupfilteremail').addClass("basicError");	
					}else if(filter[h][0] == 'campaign'){
						$('#formgroupfiltercampaign').addClass("basicError");	
						
					}else if(filter[h][0] == 'hygiene'){
						$('#formgroupfilterhygiene').addClass("basicError");	
						
					}
					
				}else{
					if(filter[h][0] == 'email'){
						$('#formgroupfilteremail').removeClass("basicError");	
					}else if(filter[h][0] == 'campaign'){
						$('#formgroupfiltercampaign').removeClass("basicError");	
						
					}else if(filter[h][0] == 'hygiene'){
						$('#formgroupfilterhygiene').removeClass("basicError");	
						
					}	
				}
				
			}
			
			
		}else{
			$('#formgroupfilteremail').removeClass("basicError");	
			$('#formgroupfiltercampaign').removeClass("basicError");	
			$('#formgroupfilterhygiene').removeClass("basicError");	
		}
		
		
		if(filter_error){
			error[e] = "Esp Filter Information";
			e++;	
			
		}
		
		
		$('html, body').animate({ scrollTop: 0 }, 'fast');
		if(error.length == 0){
			$('#errordisplay').html("");
			var json_object = {};
			
			json_object['EspName'] = espname;
			json_object['EspStatus'] = espstatus;
			json_object['RequestType'] = requesttype;
			json_object['RequestUrl'] = requesturl;
			json_object['Headers'] = headers;
			json_object['BasicAuth'] = basicauth;
			json_object['AcceptString'] = acceptstring;
			json_object['AcceptCode'] = acceptcode;
			json_object['CustomFields'] = customfields;
			json_object['FormattedDateFields'] = dateformatted;
			json_object['Template'] = posttemplate;
			json_object['PostFields'] = postfields;
			json_object['Filters'] = filter;
			
			json_object['Api'] = {};
			json_object['Api']['RequestType'] = api_requesttype;
			json_object['Api']['RequestUrl'] = api_requesturl;
			json_object['Api']['Headers'] = api_headers;
			json_object['Api']['BasicAuth'] = api_basicauth;
			json_object['Api']['AcceptString'] = api_acceptstring;
			json_object['Api']['AcceptCode'] = api_acceptcode;
			json_object['Api']['CustomFields'] = api_customfields;
			json_object['Api']['Template'] = api_posttemplate;
			json_object['Api']['PostFields'] = api_postfields;
			
			json_object['BlackList'] = {};
			json_object['BlackList']['RequestType'] = blacklist_requesttype;
			json_object['BlackList']['RequestUrl'] = blacklist_requesturl;
			json_object['BlackList']['Headers'] = blacklist_headers;
			json_object['BlackList']['BasicAuth'] = blacklist_basicauth;
			json_object['BlackList']['AcceptString'] = blacklist_acceptstring;
			json_object['BlackList']['AcceptCode'] = blacklist_acceptcode;
			json_object['BlackList']['CustomFields'] = blacklist_customfields;
			json_object['BlackList']['Template'] = blacklist_posttemplate;
			json_object['BlackList']['PostFields'] = blacklist_postfields;
			
			
			$.ajax({
				type: 	"POST",
				url:	"/listManagement/saveEsp/"+esp_id,
				headers: {'x-keyStone-nonce': nonce, 'Content-Type':'application/json'},
				data:	 JSON.stringify(json_object),
				dataType: "json"
			}).done(function(rsp){
				$('#savedsuccess').fadeIn('slow');
				setTimeout(function() {
			    	$('#savedsuccess').fadeOut('slow');
				}, 5000);
			});	
			
			
		
		}else{
			$('#savederror').fadeIn('slow');
			setTimeout(function() {
		    	$('#savederror').fadeOut('slow');
			}, 5000);
		}
		
		
		
	});
	
	
	
	
	
	$(document).on('click', '#addheader' , function(e) {
		jQuery('#headertableblock').show();
		var length = $('#headertable tbody tr').length;
		if(length == 0){
			$('#headertable tbody').append('<tr><td><input class="form-control" type="text" name="headerfield" id="headerfield"></td>'+
										'<td><input class="form-control" type="text" name="headervalue" id="headervalue"></td>'+
										'<td><span id="deleteheader"  style = "cursor:pointer" class = "glyphicon glyphicon-remove" aria-hidden="true"></span></td></tr>');
			
		}else{
			$('#headertable tr:last').after('<tr><td><input class="form-control" type="text" name="headerfield" id="headerfield"></td>'+
										'<td><input class="form-control" type="text" name="headervalue" id="headervalue"></td>'+
										'<td><span id="deleteheader"  style = "cursor:pointer" class = "glyphicon glyphicon-remove" aria-hidden="true"></span></td></tr>');
		}
	});
	

	$(document).on('click', '#deleteheader' , function(e) {
	
		var index = $(this).parent().parent().index();
			$('#headertable tbody tr').eq((index)).remove();
		if($('#headertable tbody tr').length == 0){
			jQuery('#headertableblock').hide();	
		}
	});
	
	
	
	$(document).on('click', '#addbasicauth' , function(e) {
		
			$('#basicauthtable tbody').append('<tr><td><input class="form-control" type="text" name="basicauthfield" id="basicauthfield"></td>'+
										'<td><input class="form-control" type="text" name="basicauthvalue" id="basicauthvalue"></td>'+
										'<td><span id="deletebasicauth"  style = "cursor:pointer" class = "glyphicon glyphicon-remove" aria-hidden="true"></span></td></tr>');
			$('#addbasicauth').hide();
			
		
	});
	
	
	
	$(document).on('click', '#deletebasicauth' , function(e) {
	
		var index = $(this).parent().parent().index();
			$('#basicauthtable tbody tr').eq(0).remove();
			$('#addbasicauth').show();
	});
	
	
	
	$(document).on('click', '#addcustomfield' , function(e) {
		var length = $('#customfieldtable tbody tr').length;
		if(length == 0){
			$('#customfieldtable tbody').append('<tr><td><input class="form-control" type="text" name="customfield1" id="customfield1"></td>'+
										'<td><input class="form-control" type="text" name="customfieldvalue1" id="customfieldvalue1"></td>'+
										'<td><span id="deletecustomfield"  style = "cursor:pointer" class = "glyphicon glyphicon-remove" aria-hidden="true"></span></td></tr>');
			
		}else{
			$('#customfieldtable tr:last').after('<tr><td><input class="form-control" type="text" name="customfield'+(length+1)+'" id="customfield'+(length+1)+'"></td>'+
										'<td><input class="form-control" type="text" name="customfieldvalue'+(length+1)+'" id="customfieldvalue'+(length+1)+'"></td>'+
										'<td><span id="deletecustomfield"  style = "cursor:pointer" class = "glyphicon glyphicon-remove" aria-hidden="true"></span></td></tr>');
		}
	});
	
	
	
	$(document).on('change', '#customfieldtable tbody td input' , function(e) {
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
		
		custom_fields[index] = [val1,val2];
		if(val1 != "" && val2 != ""){
			checkBuilderTableOrder();
			refreshAvailableFields();
		}
	
	});
	
	
	
	$(document).on('click', '#deletecustomfield' , function(e) {
	
		var index = $(this).parent().parent().index();
		custom_fields.splice(index,1);
		$('#customfieldtable tbody tr').eq((index)).remove();
		checkBuilderTableOrder();
		refreshAvailableFields();
	});
	
	
	
	$(document).on('change', '#templatedropdown' , function(e) {
	
		var val = $(this).val();
		if(val == 'No'){
		
			jQuery('#templatebuilder').hide();
			if($('#buildertable tbody tr').length == 0){
				
				
				for(var i =0; i < system_fields.length; i++){
					$('#buildertable tbody').append('<tr><td style = "text-align: center">'+system_fields[i][0]+'</td>'+
										'<td style = "text-align: center"><input class="form-control" type="text" name="postfieldname" id="postfieldname"></td>'+
										'<td style = "text-align: center">System</td></tr>');
					
					
				}
				
				
				for(var i =0; i < custom_fields.length; i++){
					$('#buildertable tbody').append('<tr><td style = "text-align: center">'+custom_fields[i][0]+'</td>'+
										'<td style = "text-align: center"><input readonly class="form-control" type="text" name="postfieldname" id="postfieldname"></td>'+
										'<td style = "text-align: center">Custom</td></tr>');
					$('#buildertable tbody tr:last').find('td').eq(1).find('input').val(custom_fields[i][1]);
					
				}
				
			
			}else{
				
				
				checkBuilderTableOrder();
				
				
			}	
			jQuery('#postbuilder').show();
		}else if(val == 'Yes'){
			jQuery('#postbuilder').hide();	
					
			refreshAvailableFields();
			
			jQuery('#templatebuilder').show();
		}else{
			jQuery('#templatebuilder').hide();
			jQuery('#postbuilder').hide();		
		}
		
		
	});
	
	
	
	$(document).on('click', '#testposter' , function(e) {
		jQuery('#testresponse').hide();
		jQuery('#testblock').show();
		jQuery('#sendtestposter').show();
		
		var templatedropdown = $("#templatedropdown").val();
		
		if(templatedropdown == 'No'){
			jQuery('#testpostbuilder').show();
			jQuery('#testtemplatebuilder').hide();
			$('#testbuildertable tbody tr').remove();
			var poster = getPostFields();
			
			for(var i =0; i < poster.length; i++){
				$('#testbuildertable tbody').append('<tr><td style = "text-align: center">'+poster[i][0]+'</td>'+
									'<td style = "text-align: center"><input readonly class="form-control" type="text" name="testerpostfieldname" id="testerpostfieldname"></td>'+
									'<td style = "text-align: center">'+poster[i][2]+'</td></tr>');
				$('#testbuildertable tbody tr:last').find('td').eq(1).find('input').val(poster[i][1]);
					
			}	
			
			
			
		}else{
			
			jQuery('#testpostbuilder').hide();
			jQuery('#testtemplatebuilder').show();
			$("#testpostarea").val($("#postarea").val());
			var system_clone = jQuery('#systemfieldsdisplay tbody tr').clone();
			var custom_clone = jQuery('#customfieldsdisplay tbody tr').clone();
			
			jQuery('#testsystemfieldsdisplay tbody tr').remove();
			jQuery('#testcustomfieldsdisplay tbody tr').remove();
			
			jQuery('#testsystemfieldsdisplay tbody').append(system_clone);
			jQuery('#testcustomfieldsdisplay tbody').append(custom_clone);
			
			
		}
		
		
		jQuery('#testpostdata tbody tr').remove();
		
		for(var i =0; i < system_fields.length; i++){
			$('#testpostdata tbody').append('<tr><td style = "text-align: center">'+system_fields[i][0]+'</td>'+
											'<td style = "text-align: center"><input class="form-control" type="text" name="tester2postfieldname" id="tester2postfieldname"></td>'+
											'</tr>');
			$('#testpostdata tbody tr:last').find('td').eq(1).find('input').val(system_fields[i][2]);
			
			
		}
		
		
		for(var i =0; i < custom_fields.length; i++){
			$('#testpostdata tbody').append('<tr><td style = "text-align: center">'+custom_fields[i][0]+'</td>'+
											'<td style = "text-align: center"><input readonly class="form-control" type="text" name="tester2postfieldname" id="tester2postfieldname"></td>'+
											'</tr>');
			$('#testpostdata tbody tr:last').find('td').eq(1).find('input').val(custom_fields[i][1]);
			
		}
	
	});
	
	
	$(document).on('click', '#sendtestposter' , function(e) {	
		jQuery('#testresponse').show();
		jQuery('#testblock').hide();
		jQuery('#sendtestposter').hide();
		if(esp_id != 0){
			
			var temp = [];
			$('#testpostdata tbody tr').each(function(index, ele){
				temp[index] = [$(this).children()[0].textContent, $(this).children()[1].firstChild.value];
			});
			
			$.ajax({
				type: 	"POST",
				url:	"/listManagement/testEsp/"+esp_id,
				headers: {'x-keyStone-nonce': nonce, 'Content-Type':'application/json'},
				data:	 JSON.stringify(temp),
				dataType: "json"
			}).done(function(rsp){
				jQuery('#testresponsearea').val(JSON.stringify(rsp,null,4));
				
			});	
		}
	
	});
	
	
	
	$(document).on('click', '#addadateformat' , function(e) {	
		jQuery('#formatdate').hide();
		
		for(var i = 0; date_fields.length > i; i++){
			$('#formatdatetable tbody').append('<tr><td style = "text-align: center">'+date_fields[i]+'</td>'+
											'<td style = "text-align: center">m-d-Y</td>'+
											'<td style = "text-align: center"><input class="form-control" type="text" name="dateformatval" id="dateformatval"></td>'+
											'</tr>');	
		}
		
	});
	
});


function getAllEsps(){
	return $.ajax({
		url:'/listManagement/reloadEsps',
		headers:{'x-keyStone-nonce': nonce}
	});
}



function getEsp(id){
	return $.ajax({
		url:'/listManagement/getEsp/'+id,
		headers:{'x-keyStone-nonce': nonce}
	});
}
