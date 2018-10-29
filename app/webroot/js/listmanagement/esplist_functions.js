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

var status_changing = false;



var pages_map = {				 "home"  			: "0",
									 "personal_info"	: "1",
									 "verify_identity"	: "2",
									 "employment_info"	: "3",
									 "deposit_cash"		: "4",
									 "finalize"			: "5"
									};
									
					var ancillary_manager = {"page" : 	{"0"	: [],
														 "1"	: [],
														 "2"	: ['yesblueldjskd'],
														 "3"	: [],
														 "4"	: [],
														 "5"	: []
														}
					
											};	



function ancillaryPageChangeCheck(index){
						if(ancillary_manager.page[index].length > 0){
							alert(ancillary_manager.page[index]);
							
							ancillary_manager.page[index].push('tews');
							console.log(ancillary_manager.page[index]);	
						}
					}

$(function() {
	
	
											
					
					
					
					ancillaryPageChangeCheck(2);			
	
	$(document).on('click', '#esplistselect' , function(e) {
			if(status_changing)return;
			status_changing = true;
			
			var tr_index = jQuery($(this)).parent().parent().index();
			var esp_id = jQuery($(this)).parent().parent().attr('data-esp-id');
			
			$(this).hide();
			$(this).parent().append('<select><option value = "">-Choose-</option><option value = "1">Active</option><option value = "3">Pending</option><option value = "2">Inactive</option></select>');
			$(this).parent().find('select').select2();
			
			$(this).parent().find('select').on('blur change mouseleave', function(e){
				var select_value = $(this).val();
				if(select_value == "")return;
				
				var parent_td = $(this).parent();
				$(this).select2('destroy');
				$(this).remove();
				var display_value = (select_value == 1 ? 'Active' : (select_value == 2 ? 'Inactive' : 'Pending'));
				parent_td.find('#esplistselect').html(display_value).show();
				
				var obj = 	{	'status_id' : select_value,
								'id'	: esp_id
							};
				var update_status =  updateStatus(obj);
				update_status.done(function(rsp){
					status_changing = false;
				});	
				
				
				
				
			});
			
			
	});



	jQuery("#esplistselectstatus").select2();

	
	
});

function updateStatus(data){
	return $.ajax({
				type: 	"POST",
				url:	"/listManagement/updateEspStatus",
				headers: {'x-keyStone-nonce': nonce, 'Content-Type':'application/json'},
				data:	 JSON.stringify(data),
				dataType: "json"
			});
}

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
