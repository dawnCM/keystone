<?php
/**
 * SpringLeaf Model
 *
 * This model contains the data function for the Api controller.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     TBD
 * @link          TBD
 * @package       app.Model
 * @since         keyStone v1.0
 * @license       TBD
 */
App::uses('AuthComponent', 'Controller/Component');
class SpringLeaf extends AppModel {
	public $name = 'SpringLeaf';
	public $useTable = false;
	
	//Returns array
	public function buildPost(){
		//Grab data array set in controller and set to $data
		$data = $this->data['SpringLeaf'];
		
		//SpringLeaf does not allow over 15 chars for license number.... weird.
		$data["drivers_license_identifier"] = substr($data["drivers_license_identifier"],0,14);
			
		/*
		 * array( [0]field_name - postfield name for post to SpringLeaf
		 *        [1]value - Drop the value of field from keystone post in place. If missing place boolean false  
		 * 		  [2]required - Not using now in any logic below but the setting is accurate.
		 *        [3]location - json representation of path to field/value. Needed so the json structure is correctly formatted.
		 * 		  [4]boolean - value will be true/false.  Need to know this so I can process it correctly (optional)
		 * )
		 */
		$app_config = array(
			array("field_name"=>"first_name","value"=>((isset($data["first_name"]) && $data["first_name"] != "")? $data["first_name"] : false), "required"=>true, "location"=>'["applicant"]' ),
			array("field_name"=>"last_name","value"=>((isset($data["last_name"]) && $data["last_name"] != "")? $data["last_name"] : false), "required"=>true, "location"=>'["applicant"]' ),
			array("field_name"=>"ssn","value"=>((isset($data["ssn"]) && $data["ssn"] != "")? $data["ssn"] : false), "required"=>true, "location"=>'["applicant"]' ),
			array("field_name"=>"birth_date","value"=>((isset($data["birth_date"]) && $data["birth_date"] != "")? $data["birth_date"] : false), "required"=>true, "location"=>'["applicant"]' ),
			array("field_name"=>"email","value"=>((isset($data["email"]) && $data["email"] != "")? $data["email"] : false), "required"=>true, "location"=>'["applicant"]' ),
			
			array("field_name"=>"number","value"=>((isset($data["primary_phone_number"]) && $data["primary_phone_number"] != "")? $data["primary_phone_number"] : false), "required"=>true, "location"=>'["applicant","primary_phone"]' ),
			array("field_name"=>"type","value"=>((isset($data["primary_phone_type"]) && $data["primary_phone_type"] != "")? $data["primary_phone_type"] : false), "required"=>true, "location"=>'["applicant","primary_phone"]' ),
			
			
			array("field_name"=>"line1","value"=>((isset($data["address_line1"]) && $data["address_line1"] != "")? $data["address_line1"] : false), "required"=>true, "location"=>'["applicant","current_address"]' ),
			array("field_name"=>"line2","value"=>((isset($data["address_line2"]) && $data["address_line2"] != "")? $data["address_line2"] : false), "required"=>false, "location"=>'["applicant","current_address"]' ),
			array("field_name"=>"city","value"=>((isset($data["address_city"]) && $data["address_city"] != "")? $data["address_city"] : false), "required"=>true, "location"=>'["applicant","current_address"]' ),
			array("field_name"=>"state","value"=>((isset($data["address_state"]) && $data["address_state"] != "")? $data["address_state"] : false), "required"=>true, "location"=>'["applicant","current_address"]' ),
			array("field_name"=>"zip","value"=>((isset($data["address_zip"]) && $data["address_zip"] != "")? $data["address_zip"] : false), "required"=>true, "location"=>'["applicant","current_address"]' ),
			
			array("field_name"=>"state","value"=>((isset($data["drivers_license_state"]) && $data["drivers_license_state"] != "")? $data["drivers_license_state"] : false), "required"=>true, "location"=>'["applicant","drivers_license"]' ),
			array("field_name"=>"identifier","value"=>((isset($data["drivers_license_identifier"]) && $data["drivers_license_identifier"] != "")? $data["drivers_license_identifier"] : false), "required"=>true, "location"=>'["applicant","drivers_license"]' ),
			
			array("field_name"=>"name","value"=>((isset($data["employment_name"]) && $data["employment_name"] != "")? $data["employment_name"] : false), "required"=>true, "location"=>'["applicant","current_employment"]' ),
			array("field_name"=>"is_retired","value"=>(BOOLEAN)((isset($data["employment_is_retired"]) && $data["employment_is_retired"] != "")? $data["employment_is_retired"] : false), "required"=>true, "location"=>'["applicant","current_employment"]', "boolean"=>true ),
			array("field_name"=>"is_self_employed","value"=>(BOOLEAN)((isset($data["employment_is_self_employed"]) && $data["employment_is_self_employed"] != "")? $data["employment_is_self_employed"] : false), "required"=>false, "location"=>'["applicant","current_employment"]', "boolean"=>true ),
			array("field_name"=>"income_type","value"=>((isset($data["employment_income_type"]) && $data["employment_income_type"] != "")? $data["employment_income_type"] : false), "required"=>false, "location"=>'["applicant","current_employment"]' ),
			array("field_name"=>"number","value"=>((isset($data["employment_phone_number"]) && $data["employment_phone_number"] != "")? $data["employment_phone_number"] : false), "required"=>true, "location"=>'["applicant","current_employment","phone"]' ),
			array("field_name"=>"type","value"=>((isset($data["employment_phone_type"]) && $data["employment_phone_type"] != "")? $data["employment_phone_type"] : false), "required"=>true, "location"=>'["applicant","current_employment","phone"]' ),
			
			array("field_name"=>"address_months_at","value"=>((isset($data["address_months_at"]) && $data["address_months_at"] != "")? (INT)$data["address_months_at"] : false), "required"=>true, "location"=>'["applicant"]' ),
			array("field_name"=>"employment_months_at","value"=>((isset($data["employment_months_at"]) && $data["employment_months_at"] != "")? (INT)$data["employment_months_at"] : false), "required"=>true, "location"=>'["applicant"]' ),
			array("field_name"=>"monthly_housing_expenses","value"=>((isset($data["monthly_housing_expenses"]) && $data["monthly_housing_expenses"] != "")? (FLOAT)$data["monthly_housing_expenses"] : false), "required"=>true, "location"=>'["applicant"]' ),
			array("field_name"=>"monthly_income_net","value"=>((isset($data["monthly_income_net"]) && $data["monthly_income_net"] != "")? (FLOAT)$data["monthly_income_net"] : false), "required"=>true, "location"=>'["applicant"]' ),
			array("field_name"=>"income_range","value"=>((isset($data["income_range"]) && $data["income_range"] != "")? $data["income_range"] : false), "required"=>true, "location"=>'["applicant"]' ),
			array("field_name"=>"housing_status","value"=>((isset($data["housing_status"]) && $data["housing_status"] != "")? $data["housing_status"] : false), "required"=>true, "location"=>'["applicant"]' ),
			array("field_name"=>"credit_rating","value"=>((isset($data["credit_rating"]) && $data["credit_rating"] != "")? $data["credit_rating"] : false), "required"=>true, "location"=>'["applicant"]' ),
			array("field_name"=>"active_military","value"=>(BOOLEAN)((isset($data["active_military"]) && $data["active_military"] != "")? $data["active_military"] : false), "required"=>true, "location"=>'["applicant"]', "boolean"=>true ),
			array("field_name"=>"active_bankruptcy","value"=>(BOOLEAN)((isset($data["active_bankruptcy"]) && $data["active_bankruptcy"] != "")? $data["active_bankruptcy"] : false), "required"=>true, "location"=>'["applicant"]', "boolean"=>true ),
			array("field_name"=>"credit_score_estimate","value"=>((isset($data["credit_score_estimate"]) && $data["credit_score_estimate"] != "")? (INT)$data["credit_score_estimate"] : false), "required"=>true, "location"=>'["applicant"]' ),
			array("field_name"=>"best_contact_time","value"=>((isset($data["best_contact_time"]) && $data["best_contact_time"] != "")? $data["best_contact_time"] : false), "required"=>true, "location"=>'[]' ),
			
			array("field_name"=>"ip_address","value"=>((isset($data["ip_address"]) && $data["ip_address"] != "")? $data["ip_address"] : false), "required"=>true, "location"=>'["applicant","applicant_web_client"]' ),
			array("field_name"=>"user_agent","value"=>((isset($data["user_agent"]) && $data["user_agent"] != "")? $data["user_agent"] : false), "required"=>false, "location"=>'["applicant","applicant_web_client"]' ),
			array("field_name"=>"referrer","value"=>((isset($data["referrer"]) && $data["referrer"] != "")? $data["referrer"] : false), "required"=>true, "location"=>'["applicant","applicant_web_client"]' ),
			array("field_name"=>"mobile_browser","value"=>(BOOLEAN)((isset($data["mobile_browser"]) && $data["mobile_browser"] != "")? $data["mobile_browser"] : false), "required"=>false, "location"=>'["applicant","applicant_web_client"]', "boolean"=>true ),
			
			array("field_name"=>"pay_frequency","value"=>((isset($data["pay_schedule_pay_frequency"]) && $data["pay_schedule_pay_frequency"] != "")? $data["pay_schedule_pay_frequency"] : false), "required"=>true, "location"=>'["applicant","pay_schedule"]' ),
			array("field_name"=>"pay_date_one","value"=>((isset($data["pay_schedule_pay_date_one"]) && $data["pay_schedule_pay_date_one"] != "")? $data["pay_schedule_pay_date_one"] : false), "required"=>true, "location"=>'["applicant","pay_schedule"]' ),
			array("field_name"=>"pay_date_two","value"=>((isset($data["pay_schedule_pay_date_two"]) && $data["pay_schedule_pay_date_two"] != "")? $data["pay_schedule_pay_date_two"] : false), "required"=>true, "location"=>'["applicant","pay_schedule"]' ),
			array("field_name"=>"pay_date_three","value"=>((isset($data["pay_schedule_pay_date_three"]) && $data["pay_schedule_pay_date_three"] != "")? $data["pay_schedule_pay_date_three"] : false), "required"=>false, "location"=>'["applicant","pay_schedule"]' ),
			
			array("field_name"=>"bank_name","value"=>((isset($data["bank_name"]) && $data["bank_name"] != "")? $data["bank_name"] : false), "required"=>false, "location"=>'["applicant","bank_account"]' ),
			array("field_name"=>"account_type","value"=>((isset($data["account_type"]) && $data["account_type"] != "")? $data["account_type"] : false), "required"=>true, "location"=>'["applicant","bank_account"]' ),
			array("field_name"=>"routing_number","value"=>((isset($data["routing_number"]) && $data["routing_number"] != "")? $data["routing_number"] : false), "required"=>false, "location"=>'["applicant","bank_account"]' ),
			array("field_name"=>"account_number","value"=>((isset($data["account_number"]) && $data["account_number"] != "")? $data["account_number"] : false), "required"=>false, "location"=>'["applicant","bank_account"]' ),
			array("field_name"=>"direct_deposit","value"=>(BOOLEAN)((isset($data["direct_deposit"]) && $data["direct_deposit"] != "")? $data["direct_deposit"] : false), "required"=>false, "location"=>'["applicant","bank_account"]', "boolean"=>true ),
			array("field_name"=>"months_at","value"=>((isset($data["bank_account_months_at"]) && $data["bank_account_months_at"] != "")? (INT)$data["bank_account_months_at"] : false), "required"=>false, "location"=>'["applicant","bank_account"]' ),
			
			array("field_name"=>"partner_transaction_id","value"=>((isset($data["partner_transaction_id"]) && $data["partner_transaction_id"] != "")? $data["partner_transaction_id"] : false), "required"=>true, "location"=>'[]' ),
			array("field_name"=>"requested_amount","value"=>((isset($data["requested_amount"]) && $data["requested_amount"] != "")? $data["requested_amount"] : false), "required"=>true, "location"=>'[]' ),
			array("field_name"=>"agrees_to_terms","value"=>(BOOLEAN)((isset($data["agrees_to_terms"]) && $data["agrees_to_terms"] != "")? $data["agrees_to_terms"] : false), "required"=>true, "location"=>'[]', "boolean"=>true ),
			array("field_name"=>"lead_source","value"=>((isset($data["lead_source"]) && $data["lead_source"] != "")? $data["lead_source"] : false), "required"=>true, "location"=>'[]' ),
			array("field_name"=>"lead_sub_source","value"=>((isset($data["lead_sub_source"]) && $data["lead_sub_source"] != "")? $data["lead_sub_source"] : false), "required"=>false, "location"=>'[]' ),
			array("field_name"=>"reason_for_loan","value"=>((isset($data["reason_for_loan"]) && $data["reason_for_loan"] != "")? $data["reason_for_loan"] : false), "required"=>true, "location"=>'[]' ),
			array("field_name"=>"agrees_to_auto_dial","value"=>(BOOLEAN)((isset($data["agrees_to_auto_dial"]) && $data["agrees_to_auto_dial"] != "")? $data["agrees_to_auto_dial"] : false), "required"=>false, "location"=>'[]', "boolean"=>true )
		);
		
		if(isset($data["secondary_phone_number"]) && $data["secondary_phone_number"] != ""){
			$app_config[] = array("field_name"=>"number","value"=>((isset($data["secondary_phone_number"]) && $data["secondary_phone_number"] != "")? $data["secondary_phone_number"] : false), "required"=>false, "location"=>'["applicant","secondary_phone"]' );
			$app_config[] = array("field_name"=>"type","value"=>((isset($data["secondary_phone_type"]) && $data["secondary_phone_type"] != "")? $data["secondary_phone_type"] : false), "required"=>false, "location"=>'["applicant","secondary_phone"]' );
		}	
		
		
		//print_r($app_config);exit;
		$main = array();
		
		foreach($app_config as $index=>$rec){
			$field_name = $rec['field_name'];
			$value = $rec['value'];
			$is_req = $rec['required']; //Not really using yet.  $is_req(true/false) is accurate but if no field value we are continuing on.
			$location = json_decode($rec['location'],true); //holds an array of index/associations that are the path to particular field/value to match json template. 
			$boolean = ((isset($rec['boolean']))? true : false); //boolean fields can have a boolean value so this flag lets me know
			
			$temp = array();
			
			if($value === false && !$boolean)continue;
			
			if(!empty($location)){ 
				if(count($location) == 1){ //1 level deep
					//ex: {applicant => array(field=>value)}
					$temp[$location[0]] = array($field_name=>$value);
					
				}else if(count($location) == 2){ //2 levels deep
					//ex: {applicant->current_address=>array(field=>value)}
					$temp[$location[0]][$location[1]] = array($field_name=>$value);
					
				}else{ //3 levels deep
					//ex: {applicant->current_employment->phone=>array(field=>value)}
					$temp[$location[0]][$location[1]][$location[2]] = array($field_name=>$value);
				}
				
				//$main will absorb the new associative array strucuture in $temp and return array with all elements.
				//array_merge_recursive will not overwrite associations indexes that are the same. It will push to the end of same associative indexes
				$main = array_merge_recursive($main, $temp);
				
			}else{
				//top level of array - ex: {first_name=>'test'}
				$main[$field_name] = $value;
			}
		}
				
		return $main;
	}



	
}


?>