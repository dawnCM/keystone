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

$(function() {
	$(document).on('click', '#add-offer' , function(e) {
		var offername = jQuery("#offername").val();

		if(offername == "")return;
		
		jQuery("#offername").val("");
		
		var post_data = {"name":offername};
		var response = addEspOffer(post_data);
		response.done(function(rsp){
			var new_id = rsp.id;
			jQuery("#espoffertable tbody").append('<tr>'+'<td>'+new_id+'</td>'+'<td>'+offername+'</td>'+'</tr>').show('slow');
		});	
			
	});
});


function addEspOffer(data){
	return $.ajax({
				type: 	"POST",
				url:	"/listManagement/addEspOffer",
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
