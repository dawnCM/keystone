<?php

class TestController extends AppController {
	public $uses = array('ReportTrack','SpringLeaf');
	public $json = '{"applicant":{"first_name":"Barney","middle_initial":"J","last_name":"Rubble","suffix":"JR","maternal_surname":"Stone","ssn":"666789999","birth_date":"1950-01-01","marital_status":"Single","email":"brubble@stoneage.com","secondary_email":"barney.rubble@test.com","primary_phone":{"number":"8124591234","type":"Home","extension":"12345"},"secondary_phone":{"number":"8124592135","type":"Mobile"},"current_address":{"line1":"525Monroe","line2":"Apt2-A","city":"FantasyIsland","state":"IL","zip":"60750"},"previous_address":{"line1":"525Monroe","line2":"Apt6-F","city":"FantasyIsland","state":"IL","zip":"60750"},"drivers_license":{"state":"IL","identifier":"123456789"},"current_employment":{"name":"BedrockGravel","job_title":"Boss","is_retired":false,"is_self_employed":false,"income_type":"employment","phone":{"number":"8124594321","type":"Other"}},"previous_employment":{"name":"SlateConstruction","job_title":"Contractor","is_retired":false,"is_self_employed":false,"income_type":"employment","phone":{"number":"8124593412","type":"Other"}},"address_months_at":18,"previous_address_months_at":41,"employment_months_at":12,"previous_employment_months_at":36,"monthly_income_gross":10000,"monthly_income_net":7000,"monthly_income_other":1000,"monthly_housing_expenses":1000,"home_value":10000,"mortgage_balance":10000,"income_range":"Greaterthanorequalto60000","housing_status":"Own","bank_card_count":3,"number_of_dependents":2,"credit_rating":"good","credit_score_estimate":580,"active_military":false,"active_bankruptcy":false,"web_client":{"ip_address":"127.0.0.1","user_agent":"Mozilla/5.0(Macintosh;IntelMacOSX10_9_2)Chrome/36.0.1985.125","referrer":"http://google.com","mobile_browser":false},"pay_schedule":{"pay_frequency":"biweekly","last_paycheck_amount":1234.56,"pay_date_one":"2014-04-23","pay_date_two":"2014-05-30","pay_date_three":"2014-03-23"},"bank_account":{"bank_name":"BANKOFAMERICA","account_type":"checking","routing_number":"061000052","account_number":"3322332223","direct_deposit":true,"months_at":72}},"coapplicant":{"first_name":"Betty","middle_initial":"L","last_name":"Rubble","suffix":"III","maternal_surname":"Flint","ssn":"666879999","birth_date":"1950-01-01","marital_status":"Single","email":"coapp.email@test.com","primary_phone":{"number":"8124591234","type":"Home"},"current_address":{"line1":"525Monroe","line2":"Apt2-A","city":"FantasyIsland","state":"IL","zip":"60750"},"previous_address":{"line1":"123Main","line2":"Apt6-F","city":"FantasyIsland","state":"IL","zip":"60750"},"drivers_license":{"state":"IL","identifier":"987654321"},"current_employment":{"name":"Bedrock","job_title":"Assistant","is_retired":true,"is_self_employed":true,"income_type":"employment","phone":{"number":"8124599999","type":"other"}},"address_months_at":27,"previous_address_months_at":6,"employment_months_at":26,"previous_employment_months_at":7,"monthly_income_gross":1000,"monthly_income_net":8000,"monthly_income_other":100},"relationship":"Non-spouse","partner_transaction_id":"2013049149507459","requested_amount":10000,"reason_for_loan":"BillConsolidation","best_contact_time":"inthemorning,athome","note_to_branch":"Thisisanote","agrees_to_terms":true,"agrees_to_auto_dial":true,"has_collateral":true,"lead_source":"12345","lead_sub_source":"abcd"}';
	
	
	private function _print($field, $location){
		
		
		
	}
	
	
	public function test3(){
		$this->SpringLeaf->set(json_decode($this->json,true));
		$result = $this->SpringLeaf->buildPost();
		print_r(json_encode($result));exit;
	}
	
	public function test2(){
		$array = json_decode($this->json,true);
	
		
		foreach($array as $element=>$arr){
			
			
			if(is_array($arr)){
			
				$location = array($element);
				foreach($arr as $child=>$val){
					
					if(is_array($val)){
							$location[] = $child;
						foreach($val as $grandchildfield=>$val2){
								
							if(is_array($val2)){
								$location[] = $grandchildfield;
								foreach($val2 as $greatgrandchildfield=>$val3){
									$field_name = $element."_".$child."_".$grandchildfield."_".$greatgrandchildfield;
									$this->_print($field_name, $location);
									
								}
								array_pop($location);
							}else{
								$field_name = $element."_".$child."_".$grandchildfield;
								$this->_print($field_name, $location);	
							}		
						
							
						}
						array_pop($location);	
						
					}else{
						$field_name = $element."_".$child;
						$this->_print($field_name, $location);
					}
				}
			}else{
				$location = array();
				$this->_print($element, $location);
			}
			
		}
		
		exit;
		
		
		
	}
	
	
	
	
	public function test(){
		//$times['start'] = date('Y-m-d H:i:s', strtotime("now") - 7200); //two hours ago
    	$times['start'] = '2016-04-11 00:00:00';
    	//$times['end'] = date('Y-m-d H:i:s', strtotime("now") - 3600); //one hour ago
    	$times['end'] = '2016-04-12 23:59:59';
		
		
    	$start = new MongoDate(strtotime($times['start']));
    	$end = new MongoDate(strtotime($times['end']));
		print_r($start);exit;
		$aggregate = array();
		
		$aggregate['$match'] = array('$gt'=>$start, '$lt'=>$end);

		$fields = array('lead_data.email', 'lead_data.ipaddress', 'lead_data.calltype', 'offer_id', 'lead_data.url');
			
	    $params = array('conditions' => $aggregate, 'limit'=>'3','fields'=>$fields, 'order'=>array('_id'=>'DESC'));
	    
	   
	   $mongo = $this->ReportTrack->getDataSource();
		$mongoCollectionObject = $mongo->getMongoCollection($this->ReportTrack);
		
		$c_id = 3130;
		
		$ops = array(
		    array( //equivalent to mysql where conditions
				'$match' => array( 
					'lead_created' => array('$gt'=> $start, '$lt' => $end),
					'$or' => array(		array("disposition.approved.$c_id.buyer_contract_id"=>"$c_id"),
										array("disposition.error.$c_id.buyer_contract_id"=>"$c_id"),
										array("disposition.declined.$c_id.buyer_contract_id"=>"$c_id"),
										array("disposition.timeout.$c_id.buyer_contract_id"=>"$c_id"),
										array("disposition.duplicate.$c_id.buyer_contract_id"=>"$c_id"),
										array("disposition.fraud.$c_id.buyer_contract_id"=>"$c_id"),
										array("disposition.unknown.$c_id.buyer_contract_id"=>"$c_id"),
								  )
				)
			),
			//array('$limit'=>50)
		    array( //group by theme and aggregate data as defined
		        '$group' => array(
		            "_id"		=>	null, //group by
		            'sent'	=>	array('$sum'=>1),
		            'sold' 	=>	array('$sum'=> 	
												array('$cond'=> 
																array('if'=>array('$eq'=>array('$disposition.approved.'.$c_id.'.buyer_contract_id', "$c_id"))   ,'then'=>1, 'else'=>0) //add 1 if field has value, add 0 if not
												)
								),
					'revenue'=> array('$sum'=> 	
												array('$cond'=> 
																array('if'=>array('$eq'=>array('$disposition.approved.'.$c_id.'.buyer_contract_id', "$c_id"))   ,'then'=>'$lead_data.receivableamount', 'else'=>0) //add amount if field has value, add 0 if not
												)
								)
		        ),
		    ),
		    array(
		    	//Define fields to show or not show. Needed to create fields and define display when different from $group
		        '$project' => 	array('_id'=>0, 'lead_data.receivableamount'=>1, 'sent' => 1, 'sold'=>1, 'revenue'=>1, 'epl'=> //$epl - Created and calculated field after group fields are calculated
		        										//array('$cond'=> 
		        												//array('if'=>'$sold' , 'then'=> //$leads not equal to 0, perfrom divison.  If divisor($leads) is 0 it will cause a php fatal error
		        																			array('$divide'=>
		        																						array('$revenue','$sent'))//,  'else'=>0 )     
																									
															//)
		           				) 
		    )
		    
		);
		$results = $mongoCollectionObject->aggregate($ops);

		
		
		echo "<pre>";
		print_r($times);
		print_r($results['result']);exit;
	   
	   
	   // $mongo_result = $this->ReportTrack->find('all',$params); 		
			
		//print_r($mongo_result);exit;	
		
			
			
		echo 'hey';
		
		
		
		
		
		
		
		
		exit;
	}
}