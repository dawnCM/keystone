<?php
/**
 * API Controller
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     TBD
 * @link          https://github.com/AdLink360/keyStone/wiki/ApiController
 * @package       app.Controller
 * @since         keyStone v1.0
 * @license       TBD
 */
App::uses('HttpSocket', 'Network/Http');
class ApiController extends AppController {
	public $uses = array('Cake','Bucket', 'ApiPostFunctions', 'Api', 'ApiDependency','ApiRules','Track', 'Receivable', 'Payout', 
			'Margin', 'PersonalLoan', 'Payday', 'Installment', 'LowTier', 'Affiliate','RedirectUrl','PostConfig', 'Service','ListManagement','ListManagementValidate','Esp', 'LowTierLong','PaydayPpc', 'PersonalLoanPpc', 'InstallmentPpc', 'SiteConfiguration','VendorDuplicates','Ip','Browser','FraudBrowser', 'LeadTrack', 'LeadUser','Medical', 'Lead', 'Application');
	//public $components = array('Session');
	public $track_id;
	public $debug = false;
	
	var $offer_id;
	var $data = array();
	var $primaryPostConfig;
	var $postUrl;
	var $secondaryPostConfig = array();
	var $post_builder = array();
	var $field;
	var $format;
	var $type;
	var $post_field;
	var $sendArray = array();
	var $campaign;
	var $time_ms;
	var $redirect;
	var $raw_response;
	var $post_id;
	var $source;
	var $process_secondary = "false";
	var $msg;
	var $cake_lead;
	var $calculated_price = 0.00;
	var $errors = array();
	var $keystone_id;
	var $keystoneAffiliateId;
	var $template;
	var $appType2; //holder for app type
	var $site_id; //Keystone Site Id
	
	var $isAlternateMainPost;
	var $onFailSendToAlternate;
	var $persistantReceivableMessageData = array();
	var $appType;
	var $conditional_post;
	var $orig_req_id;
	var $orig_bucket_amount;
	var $orig_lead;
	var $receivable_bucket_id;
	var $receivable_payout;
	var $receivable_margin;
	
	var $show_break_page = false;
	var $status_json;
	var $sess_req_id = "";
	var $sess_cake_lead = "";
	var $post_progress;
	var $isExternalPost = false;
	
	var $listManagementPrice; //Sold price passed to list mangement
	var $required;
	
	var $rules;
	var $default; //default value set in config
	
	//function
	const FUNCTION_CLASS_NAME = 'ApiPostFunctions';
	var $function_result;
	var $function_name;
	
	//Dependency
	const DEPENDENCY_CLASS_NAME = 'ApiDependency';
	var $dependency_name;
	var $dependency_result = array();
	
	//Rules
	const RULES_CLASS_NAME = 'ApiRules';
	var $rules_name;
	var $rules_result = array();
	
public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow();
		// All posts must authenticate.
		if($this->request->is('post')){
			if($this->request->header('X-Api-Key')){
				$this->request->data['api_id'] = $this->request->header('X-Api-Id');
				$this->request->data['api_key'] = $this->request->header('X-Api-Key');
				$this->request->data['api_ip'] = $this->request->clientIp();				
			}
			
			// Cache the api authentication request
			$cache['hash'] = md5('api_'.$this->request->data['api_ip'].$this->request->data['api_key'].$this->request->data['api_id']);
			$cache['value'] = false;
			$cache['value'] = Cache::read($cache['hash']);
			
			if($cache['value'] === false){
				$result = $this->Api->apiauthenticate($this->request->data);
				Cache::write($cache['hash'],$result,'5m');
				if($result === false){
					$this->log($this->request->clientIp());
					$this->log($this->request->header('X-Api-Key'));
					$this->response->statusCode(401);
					echo '401 Unauthorized';
					$this->_stop();
				}
			}
		}
	}
		
	//Setting data points that are needed to process the post
	private function _initializeData(ARRAY $post){
		$this->track_id = $post['id']; //Lead.class object
		$this->source = $post['source']; //Source Array
		$this->data = $post['data']; //User data Array
		$this->appType = $post['data']['AppType']; //payday or personal loan or installment
		$this->appType2 = ((isset($this->data['AppType2']) && !empty($this->data['AppType2'])) ? $this->data['AppType2'] : "");
		$this->template = $this->source['Template'];
		$post_config_array = $this->PostConfig->find('first', array( 'fields'=>array('PostConfig.json'), 'conditions'=>array('PostConfig.app_type'=>$this->appType, 'PostConfig.template' => $this->template )));
		$json_decode = json_decode($post_config_array['PostConfig']['json']);
		$this->primaryPostConfig = $json_decode->fields;
		$this->conditional_post = ((isset($json_decode->conditional) && !empty($json_decode->conditional))?$json_decode->conditional : false);
		$keystone_Aff_arr = $this->Affiliate->find('first', array('fields'=>array('Affiliate.id'), 'conditions'=>array('Affiliate.remote_id'=>$this->source['Affiliate'])));//primary key for Affiliates table
		$this->keystoneAffiliateId = $keystone_Aff_arr['Affiliate']['id'];
		$this->site_id = $this->source['SiteId'];
		
		//Load user data into models for later processing
		$this->ApiPostFunctions->init($this->data);
		$this->ApiDependency->init($this->data);
		$this->ApiRules->init($this->data);
		
		//Check for previous values
		if(isset($post['data']['OriginalRequestId']) && !empty($post['data']['OriginalRequestId']))$this->sess_req_id = $post['data']['OriginalRequestId'];
		if(isset($post['data']['CakeLead']) && !empty($post['data']['CakeLead']))$this->sess_cake_lead = $post['data']['CakeLead'];
		
		
	}
	
	//This function determines which post is needed during the state of processing
	private function _checkConditionalPost(){
		if($this->conditional_post){
			if(isset($this->conditional_post->{$this->data['AppType']})){ //Personal Loan Path
				$this->onFailSendToAlternate=true;
				$this->isAlternateMainPost=false;
				$this->post_progress=2; // User coming down persoanl the first time	
			}else if(!isset($this->conditional_post->{$this->data['AppType']}) && empty($this->sess_cake_lead)){ //Payday path
				$this->onFailSendToAlternate=false;
				$this->isAlternateMainPost=false;
				$this->post_progress=1;	//User coming down payday the first time
			}else{//Break page Path
				$this->onFailSendToAlternate=false;
				$this->isAlternateMainPost=true;
				$this->orig_req_id = $this->sess_req_id;
				$this->orig_lead = $this->sess_cake_lead;
				$this->post_progress=3; //User failed personalloan and coming in after break page.
			}
		}else{ //No conditional
			$this->onFailSendToAlternate=false;
			$this->isAlternateMainPost=false;
			$this->post_progress=4; //same as progress one.  No conditional in config.  This doesn't exist yet
		}
		
	}


	/*
	 * This function will take post config and do what is in the config
	 * 
	 * Method Chaining Responsibility:
	 *  -checkFunction() looks to see if a function is to be called and passes the value back to $this->function_result object 
	 *  -checkDependency() looks to see if there are any dependencies to check.  This function returns true/false to a flat array which has to be true in all indexes for field to be added to post.
	 *  -checkRules() looks to see and minipulate data based on rules.  Sends back an array that has true/false in 0 index and 1 index can be ""(blank) or a value. Index 0 must be true to pass rule. ex: array(true, "employed")
	 * 
	 * postFieldDecide() takes the properties set from a method chaining and uses logic and precedence of method chaining values to decide what value should be for post field
	 * clearProperties() resets the class properties reused for calculating correct value during each loop.
	 */
	private function _buildPost(){
			
		foreach($this->primaryPostConfig as $k){
			$this->field = $k->field;
			$this->format = ((!isset($k->format)) ? false : $k->format);
			$this->type = $k->type;
			$this->post_field = $k->post_field;
			$this->function_name = ((!isset($k->function)) ? false : $k->function);
			$this->required = $k->required;
			$this->dependency_name = ((!isset($k->dependency)) ? false : $k->dependency);
			$this->rules_name = ((!isset($k->rules)) ? false : $k->rules);
			$this->default = ((!isset($k->default)) ? false : $k->default);
			$value = @trim($this->data[$this->field]);
			
			//field doesn't exist in data.  No rules value or default
		//	if(!$this->rules_name && !$this->default && (strlen($value) == 0)){
			//	$this->_clearProperties();
			//	continue;	
			//}
			
			//A default is set but the value is different that needs to be set on post
			if($this->default && $this->function_name){
				$this->data[$this->field] = $this->default; //set default value to data field because it may not be there
				$this->default = false; //Set default to false because of precedence in placing posting field/value pair
			}
						
			$this->_checkFunction()->_checkDependency()->_checkRules();
			
			
			$this->_postFieldDecide();
			
			
			$this->_clearProperties();
		}
		
		
	}
	
	
	/*
	 * This function sets the results for config functions that are set in the class properties to be later decided upon.  Called in buildPost function
	 */	
	private function _checkFunction(){
		if(!$this->function_name || $this->default)return $this;
		
		if(method_exists($this->{self::FUNCTION_CLASS_NAME}, $this->function_name)){
			$function_name = $this->function_name;
			$result = $this->{self::FUNCTION_CLASS_NAME}->{$function_name}();
			if($result == "" || $result === ""){
				$this->function_result = "";
			}else{
				$this->function_result = $result;
			}
		}else{
		}
		
		return $this;
	}
	
	/*
	 * This function sets the results for config Dependencies that are set in the class properties to be later decided upon.  Called in buildPost function
	 */	
	private function _checkDependency(){
		if(!$this->dependency_name || $this->default)return $this;
		
		$dependency_explode = explode(",", $this->dependency_name);
		
		foreach($dependency_explode as $k){
			if(empty($k))continue;
			
			$dependency_name = $k;
			if(method_exists($this->{self::DEPENDENCY_CLASS_NAME}, $dependency_name)){
				$result = $this->{self::DEPENDENCY_CLASS_NAME}->{$dependency_name}();
				
				if(is_bool($result)){
					$this->dependency_result[] = $result;
				}else{
					$this->dependency_result[] = false;
				}
				
			
			}else{
			}
		}
		
		//echo "<br><br>";
		return $this;
	}
	
	
	
	/*
	 * This function sets the results for config Rules that are set in the class properties to be later decided upon.  Called in buildPost function
	 */	
	private function _checkRules(){
		if(!$this->rules_name || $this->default)return $this;
		
		$rules_explode = explode(",", $this->rules_name);
		
		foreach($rules_explode as $k){
			if(empty($k))continue;
			
			$rules_name = $k;
			if(method_exists($this->{self::RULES_CLASS_NAME}, $rules_name)){
				//$result is an array.  index[0] - true/false, index[2] - value or blank	
				$result = $this->{self::RULES_CLASS_NAME}->{$rules_name}();
				
				
				if($result[0] == true){
					$this->rules_result[] = $result;
				}else{
					$this->rules_result[] = array(false, "");
				}
				
			
			}else{
			}
		}
		
		return $this;
	}
	
	
	/*
	 * This function performs the logic and precedence for the information gathered on each field.
	 * 
	 *  -_checkRules() gets the value passed back from a rules check when applicable.  Returns false when not needed. Logic for what is already set by checkRules()
	 *  -_checkDependency() checks if field meets dependency setting.  All dependencies must be true for a field to be added when dependencies are applicable. Logic for what is already set by checkDependecy()
	 * 
	 * Precedence order:
	 *  1. Default value set during configuration for a field
	 *  2. Rules value.  
	 *  3. Derived value (function called) - type=derived
	 *  4. Value - data straight from form - type=value
	 *  5. Blank value - will be taken out of post array later by clean array type function
	 * 
	 * Must call addPost to add post_builder to send_array
	 */
	private function _postFieldDecide(){
		
		$default_value = (($this->default) ? $this->default : false);
		$rules_value = $this->_checkRulesVal(); // will return value or false
		$dependency_result = $this->_checkDependencyVal(); // will return true or false
		$function_value = trim($this->function_result);
		$field = $this->field;
		$form_value = @trim($this->data[$field]);
		
		
		if($default_value){
			$field_value = $default_value;
			
		}elseif($rules_value){
			$field_value = $rules_value;
			
		}elseif($this->type == "derived" && ($function_value != "" && $function_value !== "" || $form_value != "" && $form_value !== "") && !$this->dependency_name ){ //type derived with function value or form value and no dependency
			$field_value = (($function_value != false) ? $function_value : $form_value);
						
		}elseif($this->type == "derived" && ($function_value != "" && $function_value !== "" || $form_value != "" && $form_value !== "") && $this->dependency_name && $dependency_result ){ //type derived with function value or form value, with dependency name and dependency result is true
			$field_value = (($function_value != false) ? $function_value : $form_value);
					
		}elseif($this->type == "value" && ($form_value !== "" && $form_value != "") && !$this->dependency_name){ //type value with form value present and no dependency
			$field_value = $form_value;
			
		}elseif($this->type == "value" && ($form_value !== "" && $form_value != "") && $this->dependency_name && $dependency_result){ //type value with form value present and dependency name and dependency result is true
			$field_value = $form_value;
		}else{
			$field_value = "";
		}
		
		if($field_value == "" || $field_value === "")return;
		if($this->dependency_name && $dependency_result === false && $this->type == "value")return;
	
		$this->post_builder[$this->post_field] = $field_value;
				
		
	}

	
	/*
	 * This function gets the Rules set by checkRules() and returns a value or false for postFieldDecide() to use precedence and logic to decide field value.
	 */
	private function _checkRulesVal(){
		
		if(empty($this->rules_result))return false;
		
		
		$value = false;
		foreach($this->rules_result as $k){
			if($k[0] && $k[1] !== ""){
				$value = $k[1];
			}
		}
		return $value;
	}
	
	
	/*
	 * This function gets the dependencies set by checkDependency() and returns true/false for result of all dependencies.  If one dependency is false, the dependency has failed.
	 */
	private function _checkDependencyVal(){
		
		if(empty($this->dependency_result))return false;

		$result = true;
		foreach($this->dependency_result as $k){
			
			if(!$k){
				$result = false;
			}
		}
		return $result;
	}
	
	
	
	/*
	 * Clears all class properties used during the building of post field and values.  Called in buildPost()
	 */
	private function _clearProperties(){
		$this->field="";
		$this->format="";
		$this->type="";
		$this->post_field="";
		$this->required="";
		$this->rules="";
		$this->default="";
		
		
		//function
		$this->function_result="";
		$this->function_name="";
		
		//Dependency
		$this->dependency_name="";
		$this->dependency_result = array();
		
		//Rules
		$this->rules_name="";
		$this->rules_result = array();
		
	}
	
	
	/*
	 * adds finished, validated post to sendArray to be sent later.
	 */
	public function _addPrimaryPost(){
		
		//Place items at beginning of array using array_merge so you can see in CAKE display
		$temp_array = array();	
		$temp_array['ckm_offer_id'] = $this->source['OfferId'];
		$temp_array['ckm_request_id'] =  $this->source['RequestId'];
		$temp_array['process_secondary'] = 'false';
		$temp_array['app_type'] = (( $this->appType2 == "installment") ? 'installment' : ($this->appType == 'ppc_installment' ? 'ppc_installment' : $this->appType));
		
			
		$this->sendArray[] = 	array(	'posting_url'		=>	Configure::read('CakeM.UrlPost').'/d.ashx',
										'fields'			=>	$this->Api->cleanArray(array_merge($temp_array,$this->post_builder))
								);
	}
	
	private function _addSecondaryPost($lead_id){
		$this->post_builder = array();
		$this->post_builder['ckm_lead_id'] = $lead_id;
		$this->post_builder['ckm_resell'] = "1";
		$this->post_builder['ckm_campaign_id'] = $this->source['CampaignId'];
		$this->post_builder['ckm_key'] = $this->Cake->getPostKey($this->source['CampaignId']);
		$this->post_builder['app_type'] = 'secondary';
		
		$this->post_builder['process_secondary'] = "true";
		$post_array = $this->Api->cleanArray($this->post_builder);
		$url = Configure::read('CakeM.UrlPost').'/d.ashx';

		$this->_sendToCake($url, $post_array, "secondary");
								
	}
	
	
	
	
	
	public function _addResellPost(){
		$this->post_builder = array();
		$this->post_builder['ckm_lead_id'] = $this->orig_lead;
		$this->post_builder['ckm_resell'] = "1";
		$this->post_builder['ckm_campaign_id'] = $this->source['CampaignId'];
		$this->post_builder['ckm_key'] = $this->Cake->getPostKey($this->source['CampaignId']);
		$this->post_builder['app_type'] = $this->appType;
		$this->post_builder['loan_amount'] = $this->data['LoanAmount'];
		$this->post_builder['process_secondary'] = "false";
		
		
		$this->sendArray[] = 	array(	'posting_url'		=>	Configure::read('CakeM.UrlPost').'/d.ashx',
										'fields'			=>	$this->Api->cleanArray($this->post_builder)
								);
								
	}
	
	
	
	
	
	
	
	
	
	private function _status_msg($status,$redirect=""){
		$status_array = array(	'status'=>$status,
								'redirect'=>$redirect
		);
		
		
		
		
		
		$this->status_json = json_encode($status_array);
	}
	
	
	
	private function _processBucket($rec_amount, $cake_lead, $request_id, $payout, $margin){
		
		//If receivable is 0.00, do nothing.  This shouldn't happen
		if((FLOAT)$rec_amount <= 0.00)return;
		
			$return_array = array();//Margin And Payout can change
			$insert_bucket =  false;
			$bucket_type = "";
			$track_id = $this->track_id;
	
		
			$bli = $this->keystoneAffiliateId."-0-".$this->source['CampaignId']."-".$this->source['OfferId'];//Base Bucket
			$bli2 = $this->keystoneAffiliateId."-".((!empty($this->source['SubId1']))?$this->source['SubId1']:0)."-".$this->source['CampaignId']."-".$this->source['OfferId'];
			
			if($bli==$bli2)$bli2="";
			
			$bucket_rec = $this->Bucket->find('all',  array( 'conditions'=>array('Bucket.bli'=>array($bli, $bli2)), 'order'=> array('Bucket.id'=> 'asc' )   ));
			if(!empty($bucket_rec)){
				
				$rec_count = count($bucket_rec);
				$has_subs = $bucket_rec[0]['Bucket']['has_subs'];//Check setting from main bucket
							
				if($rec_count > 1){ //Multiple records for affiliate. Main and Sub(s) records
					
					//There can be multiple subs so loop through to find the correct one
					$index = false;
					for($i=1; $i < $rec_count; $i++){
						if($bucket_rec[$i]['Bucket']['sub_id'] == $this->source['SubId1']){
							
							$index = $i;
							break;
						}
					}			
				
					if($has_subs == '1' && $this->source['SubId1'] && $index != false){ //It is okay to pull a sub bucket ID
						$bucket_id = $bucket_rec[$index]['Bucket']['id'];//Use id from sub Affiliate bucket because has_subs is true
						
						if( ($bucket_rec[$index]['Bucket']['override_payout'] != 0.00) && ($bucket_rec[$index]['Bucket']['override_payout'] != 0) && !empty($bucket_rec[$index]['Bucket']['override_payout'])){
							$payout = $bucket_rec[$index]['Bucket']['override_payout'];	
						}
						
						if( ($bucket_rec[$index]['Bucket']['override_margin'] != 0.00) && ($bucket_rec[$index]['Bucket']['override_margin'] != 0) && !empty($bucket_rec[$index]['Bucket']['override_margin'])){
							$margin = $bucket_rec[$index]['Bucket']['override_margin'];	
							$return_array['margin'] = $margin;
						}
						
						
						
					}else if($has_subs == '1' && $this->source['SubId1'] && $index===false){ //Create a new sub Bucket
						$insert_bucket = true;
						$insert_subId = $this->source['SubId1'];
						$bli_insert = $bli2;	
						
					}else{//Default to main bucket
						$bucket_id = $bucket_rec[0]['Bucket']['id'];//Place lead in main bucket for affiliate because has_subs is false.
						$bucket_type = "main";
					}
					
				}else{ //Only the main bucket exists					
					if($has_subs == '1' && $this->source['SubId1'] != '' && $this->source['SubId1'] != '0'){ //Create a new sub bucket record
						$insert_bucket = true;
						$insert_subId = $this->source['SubId1'];
						$bli_insert = $bli2;						
					}else{
						//Use the main bucket
						$bucket_id = $bucket_rec[0]['Bucket']['id'];	
					}	
				}
			}else{//No buckets exist for affilate, create new main bucket
				$insert_bucket = true;
				$insert_subId = 0;
				$bli_insert = $bli;	
			}
			
			if($insert_bucket){
				$bucket_create_array = array(	'bli'				=>	$bli_insert,
												'affiliate_id'		=>	$this->keystoneAffiliateId,
												'sub_id'			=>	$insert_subId,
												'campaign_id'		=>	$this->source['CampaignId'],
												'offer_id'			=>  $this->source['OfferId'],
												'status_id'			=>	5,
												'amount'			=>  number_format(0.00,2),
												'prefill'			=>	number_format(0.00,2),
												'prefill_payback'	=>	number_format(20.00,2),
												'has_subs'			=> 0,
												'override_payout' 	=> number_format(0.00,2),
												'override_margin'	=> number_format(0.00,2)
												
				);	
				$this->Bucket->create($bucket_create_array);
				$this->Bucket->save();
				$bucket_id = $this->Bucket->id;
			}
					
			//Create and add to receivables Table
			$receivable_create_array = array(	'bucket_id'			=>	$bucket_id,
												'track_id'			=>	$track_id,
												'amount'			=>	$rec_amount,
												'margin'			=>	$margin,
												'payout'			=>	$payout
			);
			
			//print_r($receivable_create_array);
			
			$this->Receivable->create($receivable_create_array);
			$this->Receivable->save();
			$receivable_id = $this->Receivable->id;
					
			return $return_array;
	}

	private function _getReceivableAmount($reqid, $offerid, $campaignid, $affid, $creativeid){
			
		$offerid = ((!empty($offerid))?$offerid:'0');
		$campaignid = ((!empty($campaignid))?$campaignid:'0');
		$affid = ((!empty($affid))?$affid:'0');
		$response = $this->Cake->exportconversion($offerid, $campaignid, $affid, $creativeid);
		$amount = 0.00;
		if($response['status'] == 'success'){
			
	
			if(isset($response['data']['conversions']['conversion'][0])){
			
			
				foreach($response['data']['conversions']['conversion'] as $k){
					
					if($k['request_session_id'] == $reqid){
						$amount = $k['received']['amount'];
						break;	
					}
				}
			}else{
				if($response['data']['conversions']['conversion']['request_session_id'] == $reqid){
					$amount = $response['data']['conversions']['conversion']['received']['amount'];
				}
			}
				
		}else{
			$this->log('Failed Receivable Lookup-->');
			$this->log($affid);
			$this->log($campaignid);
		}
		
		return $amount;
		
	}
	
	
	private function _updateTrackLeadId(){
		//Update track table, set lead_id
		$this->Track->set('id', $this->track_id);
		$this->Track->set('lead_id', $this->cake_lead);
		$this->Track->save();
		$this->Track->clear();	
	}
	
	
	private function _processPayout(){		
		//$payment_info = $this->Cake->paymentType($this->source['CampaignId']);
		$cache['hash'] = md5('cake_paymentType_'.$this->source['CampaignId']);
		$cache['value'] = false;
		$cache['value'] = Cache::read($cache['hash']);
			
		if($cache['value'] === false){
			$payment_info = $this->Cake->paymentType($this->source['CampaignId']);
			Cache::write($cache['hash'], $payment_info, '15m');
		}else{
			$payment_info = $cache['value'];
		}
		
		if(is_array($payment_info)){
			$margin = $this->Api->formatDecimal((FLOAT)$payment_info['default_margin']);
			$payout = $this->Api->formatDecimal((FLOAT)$payment_info['default_payout']);
			$price_format = $payment_info['price_format'];
			$is_percentage = (($payment_info['is_percentage'] == "true")? true:false);
			$is_bucket = (($payment_info['is_bucket']=="true")? true: false);
			
			$rec_amount = $this->_getReceivableAmount($this->source['RequestId'],$this->source['OfferId'], $this->source['CampaignId'], $this->source['Affiliate'], $this->source['CreativeId']);
			$rec_amount = (FLOAT)$rec_amount;
			$this->listManagementPrice = $rec_amount;
			
			/*if($rec_amount == 0){
				$this->log('NO AMOUNT-->');
				$this->log($rec_amount);
				$this->log($payment_info);
			}
			*/
			
			if($this->isExternalPost === false){ //Internal Lead
			
			
				if($is_bucket){
					
					$this->_processBucket($rec_amount, $this->cake_lead, $this->source['RequestId'], $payout, $margin);
					$this->calculated_price = $this->Api->formatDecimal((FLOAT)$rec_amount - (($margin/100) * $rec_amount) ); //For price holder in External posts
					
					$payment_type = $price_format;
					$pay_amount = 0.00;
					$margin = $this->Api->formatDecimal((FLOAT)($margin/100));
					//$margin_amount = $this->Api->formatDecimal($rec_amount * $margin );	
					$margin_amount = 0.00;
					
				}else if($is_percentage){
					
					$payment_type = $price_format;
					$margin = $this->Api->formatDecimal((FLOAT) (100.00 - $payout) / 100);
					
					$percentage = $this->Api->formatDecimal((FLOAT)($payout/100));
					$pay_amount = $this->Api->formatDecimal((FLOAT)$rec_amount * $percentage);
					$margin_amount = $this->Api->formatDecimal($rec_amount - $pay_amount );
					
			
					$pixel_url = "http://leadstudiotrack.com/p.ashx?o=".$this->source['OfferId']."&f=pb&t=".$this->cake_lead."&r=".$this->source['RequestId']."&ap=".$pay_amount;
	
					$rsp = $this->Api->firePixel($pixel_url);
					
					if(!preg_match("/SUCCESS/",$rsp)){
						$errors = array('ERRORS' => array(201=>'Possible Pixel Fire Failure - '.urlencode($pixel_url)));
						$track_json = json_encode($errors);
						$this->_trackLead($track_json);		
					}
					
				}else{
					//Fixed
					$payment_type = $price_format;
					$pay_amount = $payout;
					
					$margin = $this->Api->formatDecimal((FLOAT) ($rec_amount - $pay_amount) / $rec_amount);
					$margin_amount = $this->Api->formatDecimal($rec_amount - $pay_amount );	
					
				}

			
			
			}else{ //External Leads
			
				
				
				if($is_bucket){
					
					$this->_processBucket($rec_amount, $this->cake_lead, $this->source['RequestId'], $payout, $margin);
					$this->calculated_price = $this->Api->formatDecimal((FLOAT)$rec_amount - (($margin/100) * $rec_amount) ); //For price holder in External posts
					
					$payment_type = $price_format;
					$pay_amount = 0.00;
					$margin = $this->Api->formatDecimal((FLOAT)($margin/100));
					//$margin_amount = $this->Api->formatDecimal($rec_amount * $margin );	
					$margin_amount = 0.00;
					
					
				
				}else if($is_percentage){
					
					//Percentage payment
					$payment_type = $price_format;
					$margin = $this->Api->formatDecimal((FLOAT) (100.00 - $payout) / 100);
					
					$percentage = $this->Api->formatDecimal((FLOAT)($payout/100));
					$pay_amount = $this->Api->formatDecimal((FLOAT)$rec_amount * $percentage);
					$margin_amount = $this->Api->formatDecimal($rec_amount - $pay_amount );
					
					$this->calculated_price = $this->Api->formatDecimal($rec_amount - $margin_amount);
					
					
					  
					 $pixel_url = "http://leadstudiotrack.com/p.ashx?o=".$this->source['OfferId']."&f=pb&t=".$this->cake_lead."&r=".$this->source['RequestId']."&ap=".$pay_amount;
	
					$rsp = $this->Api->firePixel($pixel_url);
					
					if(!preg_match("/SUCCESS/",$rsp)){
						$errors = array('ERRORS' => array(201=>'Possible Pixel Fire Failure - '.urlencode($pixel_url)));
						$track_json = json_encode($errors);
						$this->_trackLead($track_json);		
					}
					
					
					
				}else{
					//Fixed
					$payment_type = $price_format;
					$pay_amount = $payout;
					
					$margin = $this->Api->formatDecimal((FLOAT) ($rec_amount - $pay_amount) / $rec_amount);
					$margin_amount = $this->Api->formatDecimal($rec_amount - $pay_amount );	
					
					$this->calculated_price = $this->Api->formatDecimal($payout);
					
					$pixel_url = "http://leadstudiotrack.com/p.ashx?o=".$this->source['OfferId']."&f=pb&t=".$this->cake_lead."&r=".$this->source['RequestId']."&ap=".$pay_amount;
					
					$rsp = $this->Api->firePixel($pixel_url);
						
					if(!preg_match("/SUCCESS/",$rsp)){
						$errors = array('ERRORS' => array(201=>'Possible Pixel Fire Failure - '.urlencode($pixel_url)));
						$track_json = json_encode($errors);
						$this->_trackLead($track_json);
					}
				}

			}



			$track_array = array(	'PaymentType' => $payment_type,
									'ReceivableAmount' => (STRING)$rec_amount,
									'PaidAmount' => (STRING)$pay_amount,
									'Margin' =>  (STRING)$margin,
									'MarginAmount' => (STRING)$margin_amount
									
			);
			
			$track_json = json_encode($track_array);
			$this->_trackLead($track_json);
		
				
			
		}else{
			//should never happen error!!!!
		}
	}


	private function _hash_ssn($ssn){
		$ssn;
		$last4 = substr($ssn, -4);
		$last4_md5 = md5($last4);
		$last4_json = json_encode(array("SsnHash"=>$last4_md5));
		
		$this->_trackLead($last4_json);
		
		
	}
	
	public function _sendToCake($url, $post_array, $type="main"){
		
		$time_start = $this->Api->microtime_float();
		//API MODEL
		$response = $this->Cake->send($url, $post_array, 'API Post');
		$time_end = $this->Api->microtime_float();
		$this->time_ms = $time_end - $time_start;

		if($type == "main"){ //Don't overwrite on secondary posts
			$this->raw_response = $response;
			$Response = new SimpleXMLElement($response);
			$this->redirect = (STRING)$Response->redirect;
			$this->msg = (STRING)$Response->msg;
			$this->cake_lead = (STRING)$Response->leadid;
			
			$track_array = array(	'CakeResponse' => $this->msg,
									'CakePostType' => $type
			);
			$track_json = json_encode($track_array);
			$this->_trackLead($track_json);
			
		}else{
		
			$raw_response = $response;
			$Response = new SimpleXMLElement($response);
	
			$redirect = (STRING)$Response->redirect;
			$msg = (STRING)$Response->msg;
			$cake_lead = (STRING)$Response->leadid;	
			
			$track_array = array(	'CakeResponse' => $msg,
									'CakePostType' => $type
			);
			$track_json = json_encode($track_array);
			$this->_trackLead($track_json);
		}
	}
	
	/**
	 * Internal Processing
	 */
	public function _processingCake(){
		
		foreach($this->sendArray as $k){
			$sending_errors = array();
			$url = $k['posting_url'];
			$post_array = $k['fields'];
			
			if($this->isAlternateMainPost){
				$request_id=$this->orig_req_id;
			}else{
				$request_id = $k['fields']['ckm_request_id'];
			}
			
			
			
			
			$this->_sendToCake($url, $post_array);
			
			
						
			if($this->msg == "success"){
				
				if($this->post_progress == 1){ //Payday
					$this->_updateTrackLeadId();
					//$this->_addSecondaryPost($this->cake_lead);
					$this->_processPayout();
					
					
					
					if(empty($this->redirect)){
						$sending_errors[202] = "No Redirect In Post Progress 1";	
					}
					
					$this->_hash_ssn($this->request->data['Ssn']);
										
					//Store Redirect
					$this->redirect = $this->Service->buildRedirect($this->redirect, $this->track_id);
					$this->RedirectUrl->setRedirect($this->track_id, $this->redirect);
					
				}else if($this->post_progress == 2){ //Personal Loan
					
					$this->_updateTrackLeadId();	
					//$this->_addSecondaryPost($this->cake_lead);
					
					$this->_hash_ssn($this->request->data['Ssn']);
					
					if(empty($this->redirect)){ //Send to break page
					
						//$this->sess_req_id = $this->source['RequestId'];
						//$this->sess_cake_lead = $this->cake_lead;
						//$this->show_break_page = true;	

						$sending_errors[206] = "No Redirect in Personal Loan Path";	
						
												
					}else{
						
						$this->_processPayout();
						
						//Store Redirect
						$this->redirect = $this->Service->buildRedirect($this->redirect, $this->track_id);
						$this->RedirectUrl->setRedirect($this->track_id, $this->redirect);
						
						
					}
				
				}else if($this->post_progress == 3){ //Coming from break page
					
					$this->_processPayout();
					
					 if(empty($this->redirect)){
				     	$sending_errors[203] = "No Redirect in Post Progress 3";	
				     }
					
					
					//Store Redirect
					$this->redirect = $this->Service->buildRedirect($this->redirect, $this->track_id);
					$this->RedirectUrl->setRedirect($this->track_id, $this->redirect);
					
				}else{
					$sending_errors[204] = "Invalid Post Progress";	
				}
			}else{
				$sending_errors[100] = "Invalid Cake Status Response";	
			}
			
			
			if(!empty($sending_errors)){
			
				$errors = array('ERRORS' => $sending_errors);
				$track_json = json_encode($errors);
				$this->_trackLead($track_json);	
			}
		}

	}

	private function _createTracking(){
		$this->request->data['request_id'] = $this->source['RequestId'];
		$this->request->data['offer_id'] = $this->source['OfferId'];
		$this->request->data['campaign_id'] = $this->source['CampaignId'];
		$this->request->data['affiliate_id'] = $this->source['Affiliate'];
		
		$this->request->data['Track'] = $this->request->data;	
			
		if($this->Track->save($this->request->data)) {
			return $this->Track->id;
		} else {
			return false;
		}
	}
	
	private function _trackLead($json){
		$this->Track->writeLead($this->track_id, $json);
	}
	
	
	
	/**
	 * This function works with the InstallmentPpc model to setup the External post.
	 */
	private function _processInstallmentPpc($lead_data){
		
		$lead_data['LoanAmountPersonal'] = $this->_mapLoanAmountPersonal($lead_data['LoanAmountPersonal']);
		$this->InstallmentPpc->set($lead_data);
		$this->InstallmentPpc->addDependencies();
		if ($this->InstallmentPpc->validates()) {
			//Build the post
			$this->process_secondary = "false";
			$this->post_builder = $this->Api->cleanArray($this->InstallmentPpc->buildPost()); //fields for post
			$this->source['OfferId'] = $this->request->data['OfferId']; //post credential
			$this->source['CampaignId'] = $this->request->data['CampaignId'];
			$this->source['Affiliate'] =  $this->request->data['Affiliate'];
			$this->source['CreativeId'] =  $this->request->data['CreativeId'];
			$this->source['SubId1'] =  ((!empty($this->request->data['SubId1'])) ?$this->request->data['SubId1'] : 0);
			$this->source['SubId2'] =  ((!empty($this->request->data['SubId2'])) ?$this->request->data['SubId2'] : 0);
			$this->source['SubId3'] =  ((!empty($this->request->data['SubId3'])) ?$this->request->data['SubId3'] : 0);
			$this->source['SubId4'] =  ((!empty($this->request->data['SubId4'])) ?$this->request->data['SubId4'] : 0);
			$this->source['SubId5'] =  ((!empty($this->request->data['SubId5'])) ?$this->request->data['SubId5'] : 0);
			
			//$payment_info = $this->Cake->paymentType($this->source['CampaignId']);
			$cache['hash'] = md5('cake_paymentType_'.$this->source['CampaignId']);
			$cache['value'] = false;
			$cache['value'] = Cache::read($cache['hash']);
				
			if($cache['value'] === false){
				$payment_info = $this->Cake->paymentType($this->source['CampaignId']);
				Cache::write($cache['hash'], $payment_info, '15m');
			}else{
				$payment_info = $cache['value'];
			}
			
			if(!is_array($payment_info)){
				$this->errors[] = 'Generating payment information failed';
				$this->_responseError('System Error');	
			}
			$tierMin = $payment_info['TierMin'];
			$tierMax = $payment_info['TierMax'];
			$subs_array = array($this->source['SubId1'],$this->source['SubId2'],$this->source['SubId3'],$this->source['SubId4'],$this->source['SubId5']);
			$this->source['RequestId'] = $this->Cake->generateSession($this->source['Affiliate'], $this->source['CreativeId'], $subs_array, $payment_info['offer_type'], $payment_info['price_format']); //post credential
			$this->track_id = $this->_createTracking();
			if(!$this->track_id){
				$this->errors[] = 'Generating Track Id Failed';
				$this->_responseError('System Error');	
			} 
			$this->appType = "vendor_ppc_installment";//$this->request->data['AppType']; //post credential
			$this->_addPrimaryPost(); //Create structure for post
			
			$keystone_Aff_arr = $this->Affiliate->find('first', array('fields'=>array('Affiliate.id'), 'conditions'=>array('Affiliate.remote_id'=>$this->source['Affiliate'])));//primary key for Affiliates table
			$this->keystoneAffiliateId = $keystone_Aff_arr['Affiliate']['id'];
	

			$url = $this->sendArray[0]['posting_url'];
			$post_array = $this->sendArray[0]['fields'];
			
			$payout = $this->Api->formatDecimal($payment_info['default_payout']);
			$post_array['vendor_id'] = 	$this->source['Affiliate'];
			$post_array['tier_min']	= ((!empty($tierMin))? $tierMin: $payout);	
			$post_array['tier_max']	= ((!empty($tierMax))? $tierMax: 1200);
				
			$loanAmount = $post_array['loan_amount'];	
			$post_array['loan_amount'] = $this->_mapLoanAmount($loanAmount);
			
			if($post_array['loan_amount'] > 1500){
				$post_array['loan_amount_personal'] = $post_array['loan_amount'];
				//$loanArray = array(1000,900,800);
				//$post_array['loan_amount'] = $loanArray[array_rand($loanArray)];
				$post_array['loan_amount'] = 1000;
				$post_array['app_type'] = "vendor_ppc_personalloan";
				$lead_data['AppType'] = "vendor_ppc_personalloan";
			}else{
				$lead_data['AppType'] = $this->appType;
				//$loanArray = array(1000,900,800);
				if($post_array['loan_amount']>1000){
					//$post_array['loan_amount'] = $loanArray[array_rand($loanArray)];
					$post_array['loan_amount'] = 1000;
				}
			}
			
			
			$lead_data['Mobile'] = 'false';
			$lead_data['CallType'] = "External";
			$lead_data['sub_id'] = $this->source['SubId1'];
			$clean_lead_data = $this->_externalCleanTrackData($lead_data);
			$this->_trackLead(json_encode($clean_lead_data)); //Store data

			
			$this->_sendToCake($url, $post_array);
			
			
			if($this->msg == 'success' && !empty($this->redirect)){
				
				//Send ssn hash
				$this->_hash_ssn($this->request->data['Ssn']);
				$this->_updateTrackLeadId();
				$this->_processPayout();
				
				$this->redirect = $this->Service->buildRedirect($this->redirect, $this->track_id);
				$this->_listManagement($this->request->data);				
				$this->_responseSuccess();	
			}else{
				$this->errors[] = 'Lead was not bought';
				//$this->leadByteVendorUnsold($lead_data);
				$this->_response("Declined",  "decline");
			}
			
			exit;
	
		} else {
		    $validation_array = $this->InstallmentPpc->flatErrorArray();
			if(!empty($validation_array))$this->errors = array_merge($this->errors, $validation_array);
			$this->_responseError($msg="Validation Errors");
		}
	}
	
	/**
	 * This function works with the PaydayPpc model to setup the External post.
	 */
	private function _processPaydayPpc($lead_data){
		
		
		$this->PaydayPpc->set($lead_data);
		if ($this->PaydayPpc->validates()) {
				
			//Build the post
			$this->process_secondary = "false";
			$this->post_builder = $this->Api->cleanArray($this->PaydayPpc->buildPost()); //fields for post
			$this->source['OfferId'] = $this->request->data['OfferId']; //post credential
			$this->source['CampaignId'] = $this->request->data['CampaignId'];
			$this->source['Affiliate'] =  $this->request->data['Affiliate'];
			$this->source['CreativeId'] =  $this->request->data['CreativeId'];
			$this->source['SubId1'] =  ((!empty($this->request->data['SubId1'])) ?$this->request->data['SubId1'] : 0);
			$this->source['SubId2'] =  ((!empty($this->request->data['SubId2'])) ?$this->request->data['SubId2'] : 0);
			$this->source['SubId3'] =  ((!empty($this->request->data['SubId3'])) ?$this->request->data['SubId3'] : 0);
			$this->source['SubId4'] =  ((!empty($this->request->data['SubId4'])) ?$this->request->data['SubId4'] : 0);
			$this->source['SubId5'] =  ((!empty($this->request->data['SubId5'])) ?$this->request->data['SubId5'] : 0);
			
			//$payment_info = $this->Cake->paymentType($this->source['CampaignId']);
			$cache['hash'] = md5('cake_paymentType_'.$this->source['CampaignId']);
			$cache['value'] = false;
			$cache['value'] = Cache::read($cache['hash']);
				
			if($cache['value'] === false){
				$payment_info = $this->Cake->paymentType($this->source['CampaignId']);
				Cache::write($cache['hash'], $payment_info, '15m');
			}else{
				$payment_info = $cache['value'];
			}
						
			if(!is_array($payment_info)){
				$this->errors[] = 'Generating payment information failed';
				$this->_responseError('System Error');	
			}
			
			$tierMin = $payment_info['TierMin'];
			$tierMax = $payment_info['TierMax'];
			$subs_array = array($this->source['SubId1'],$this->source['SubId2'],$this->source['SubId3'],$this->source['SubId4'],$this->source['SubId5']);
			$this->source['RequestId'] = $this->Cake->generateSession($this->source['Affiliate'], $this->source['CreativeId'], $subs_array, $payment_info['offer_type'], $payment_info['price_format']); //post credential
			$this->track_id = $this->_createTracking();
			if(!$this->track_id){
				$this->errors[] = 'Generating Track Id Failed';
				$this->_responseError('System Error');	
			} 
			$this->appType = "vendor_ppc_payday";//$this->request->data['AppType']; //post credential
			$this->_addPrimaryPost(); //Create structure for post
			$keystone_Aff_arr = $this->Affiliate->find('first', array('fields'=>array('Affiliate.id'), 'conditions'=>array('Affiliate.remote_id'=>$this->source['Affiliate'])));//primary key for Affiliates table
			$this->keystoneAffiliateId = $keystone_Aff_arr['Affiliate']['id'];
			
			$url = $this->sendArray[0]['posting_url'];
			$post_array = $this->sendArray[0]['fields'];
			
			$payout = $this->Api->formatDecimal($payment_info['default_payout']);
			$post_array['vendor_id'] = 	$this->source['Affiliate'];
			$post_array['tier_min']	= ((!empty($tierMin))? $tierMin: $payout);	
			$post_array['tier_max']	= ((!empty($tierMax))? $tierMax: 1200);	
			
			$loanAmount = $post_array['loan_amount'];	
			$post_array['loan_amount'] = $this->_mapLoanAmount($loanAmount);
			
			$lead_data['AppType'] = $this->appType;	
			$lead_data['Mobile'] = 'false';
			$lead_data['CallType'] = "External";
			$lead_data['sub_id'] = $this->source['SubId1'];
			$clean_lead_data = $this->_externalCleanTrackData($lead_data);
			$this->_trackLead(json_encode($clean_lead_data)); //Store data
			
			$this->_sendToCake($url, $post_array);
			
			if($this->msg == 'success' && !empty($this->redirect)){
				
				//Send ssn hash
				$this->_hash_ssn($this->request->data['Ssn']);
				$this->_updateTrackLeadId();
				$this->_processPayout();
				$this->redirect = $this->Service->buildRedirect($this->redirect, $this->track_id);
				$this->_listManagement($this->request->data);
				$this->_responseSuccess();	
			}else{
				$this->errors[] = 'Lead was not bought';
				//$this->leadByteVendorUnsold($lead_data);
				$this->_response("Declined",  "decline");
			}
			
			exit;
		
		} else {
		    $validation_array = $this->PaydayPpc->flatErrorArray();
			if(!empty($validation_array))$this->errors = array_merge($this->errors, $validation_array);
			$this->_responseError($msg="Validation Errors");
		}
	}
	
	/**
	 * This function works with the PersonalLoanPPC model to setup the External post.
	 */
	private function _processPersonalLoanPpc($lead_data){
		
		$lead_data['LoanAmountPersonal'] = $this->_mapLoanAmountPersonal($lead_data['LoanAmountPersonal']);
		$this->PersonalLoanPpc->set($lead_data);
		$this->PersonalLoanPpc->addDependencies();
		if ($this->PersonalLoanPpc->validates()) {
			
			//Build the post
			$this->process_secondary = "false";
			$this->post_builder = $this->Api->cleanArray($this->PersonalLoanPpc->buildPost()); //fields for post
			$this->source['OfferId'] = $this->request->data['OfferId']; //post credential
			$this->source['CampaignId'] = $this->request->data['CampaignId'];
			$this->source['Affiliate'] =  $this->request->data['Affiliate'];
			$this->source['CreativeId'] =  $this->request->data['CreativeId'];
			$this->source['SubId1'] =  ((!empty($this->request->data['SubId1'])) ?$this->request->data['SubId1'] : 0);
			$this->source['SubId2'] =  ((!empty($this->request->data['SubId2'])) ?$this->request->data['SubId2'] : 0);
			$this->source['SubId3'] =  ((!empty($this->request->data['SubId3'])) ?$this->request->data['SubId3'] : 0);
			$this->source['SubId4'] =  ((!empty($this->request->data['SubId4'])) ?$this->request->data['SubId4'] : 0);
			$this->source['SubId5'] =  ((!empty($this->request->data['SubId5'])) ?$this->request->data['SubId5'] : 0);
			
			//$payment_info = $this->Cake->paymentType($this->source['CampaignId']);
			$cache['hash'] = md5('cake_paymentType_'.$this->source['CampaignId']);
			$cache['value'] = false;
			$cache['value'] = Cache::read($cache['hash']);
				
			if($cache['value'] === false){
				$payment_info = $this->Cake->paymentType($this->source['CampaignId']);
				Cache::write($cache['hash'], $payment_info, '15m');
			}else{
				$payment_info = $cache['value'];
			}
			
			if(!is_array($payment_info)){
				$this->errors[] = 'Generating payment information failed';
				$this->_responseError('System Error');	
			}
			
			$tierMin = $payment_info['TierMin'];
			$tierMax = $payment_info['TierMax'];
			$subs_array = array($this->source['SubId1'],$this->source['SubId2'],$this->source['SubId3'],$this->source['SubId4'],$this->source['SubId5']);
			$this->source['RequestId'] = $this->Cake->generateSession($this->source['Affiliate'], $this->source['CreativeId'], $subs_array, $payment_info['offer_type'], $payment_info['price_format']); //post credential

			$this->track_id = $this->_createTracking();
			if(!$this->track_id){
				$this->errors[] = 'Generating Track Id Failed';
				$this->_responseError('System Error');	
			} 
			$this->appType = "vendor_ppc_personalloan";//$this->appType = $this->request->data['AppType']; //post credential
			$this->_addPrimaryPost(); //Create structure for post
			$keystone_Aff_arr = $this->Affiliate->find('first', array('fields'=>array('Affiliate.id'), 'conditions'=>array('Affiliate.remote_id'=>$this->source['Affiliate'])));//primary key for Affiliates table
			$this->keystoneAffiliateId = $keystone_Aff_arr['Affiliate']['id'];
			
			
			
			$url = $this->sendArray[0]['posting_url'];
			$post_array = $this->sendArray[0]['fields'];
			
			$payout = $this->Api->formatDecimal($payment_info['default_payout']);
			$post_array['vendor_id'] = 	$this->source['Affiliate'];
			$post_array['tier_min']	= ((!empty($tierMin))? $tierMin: $payout);	
			$post_array['tier_max']	= ((!empty($tierMax))? $tierMax: 1200);
				
			$loanAmount = $post_array['loan_amount_personal'];	
			$post_array['loan_amount_personal'] = $this->_mapLoanAmount($loanAmount);
		
			$lead_data['AppType'] = $this->appType;	
			$lead_data['Mobile'] = 'false';
			$lead_data['CallType'] = "External";
			$lead_data['sub_id'] = $this->source['SubId1'];
			$clean_lead_data = $this->_externalCleanTrackData($lead_data);
			$this->_trackLead(json_encode($clean_lead_data)); //Store data

			$this->_sendToCake($url, $post_array);
			
			
			if($this->msg == 'success' && !empty($this->redirect)){
				
				//Send ssn hash
				$this->_hash_ssn($this->request->data['Ssn']);
				$this->_updateTrackLeadId();
				$this->_processPayout();
				$this->redirect = $this->Service->buildRedirect($this->redirect, $this->track_id);
				$this->_listManagement($this->request->data);
				$this->_responseSuccess();	
			}else{
				$this->errors[] = 'Lead was not bought';
				//$this->leadByteVendorUnsold($lead_data);
				$this->_response("Declined", "decline");
			}
			
			exit;
			
		} else {
		    $validation_array = $this->PersonalLoanPpc->flatErrorArray();
			if(!empty($validation_array))$this->errors = array_merge($this->errors, $validation_array);
			$this->_responseError($msg="Validation Errors");
		}
	}

	/**
	 * This function works with the PersonalLoan model to setup the External post.
	 */
	private function _processPersonalLoan($lead_data){

		// Added specifically for ZP as they do not want to follow our spec.
		// Insert spaghetti code.
		if(!strpos($lead_data['EmploymentTime'],'/')){
			$lead_data['EmploymentTime'] = date('m/Y',strtotime('-'.$lead_data['EmploymentTime'].' months')); 
		}
		
		$lead_data['LoanAmountPersonal'] = $this->_mapLoanAmountPersonal($lead_data['LoanAmountPersonal']);
		$this->PersonalLoan->set($lead_data);
		$this->PersonalLoan->addDependencies();
		if ($this->PersonalLoan->validates()) {
			
			//Build the post
			$this->process_secondary = "false";
			$this->post_builder = $this->Api->cleanArray($this->PersonalLoan->buildPost()); //fields for post
			$this->source['OfferId'] = $this->request->data['OfferId']; //post credential
			$this->source['CampaignId'] = $this->request->data['CampaignId'];
			$this->source['Affiliate'] =  $this->request->data['Affiliate'];
			$this->source['CreativeId'] =  $this->request->data['CreativeId'];
			$this->source['SubId1'] =  ((!empty($this->request->data['SubId1'])) ?$this->request->data['SubId1'] : 0);
			$this->source['SubId2'] =  ((!empty($this->request->data['SubId2'])) ?$this->request->data['SubId2'] : 0);
			$this->source['SubId3'] =  ((!empty($this->request->data['SubId3'])) ?$this->request->data['SubId3'] : 0);
			$this->source['SubId4'] =  ((!empty($this->request->data['SubId4'])) ?$this->request->data['SubId4'] : 0);
			$this->source['SubId5'] =  ((!empty($this->request->data['SubId5'])) ?$this->request->data['SubId5'] : 0);
			
			//$payment_info = $this->Cake->paymentType($this->source['CampaignId']);
			$cache['hash'] = md5('cake_paymentType_'.$this->source['CampaignId']);
			$cache['value'] = false;
			$cache['value'] = Cache::read($cache['hash']);
				
			if($cache['value'] === false){
				$payment_info = $this->Cake->paymentType($this->source['CampaignId']);
				Cache::write($cache['hash'], $payment_info, '15m');
			}else{
				$payment_info = $cache['value'];
			}
			
			if(!is_array($payment_info)){
				$this->errors[] = 'Generating payment information failed';
				$this->_responseError('System Error');	
			}
			
			$tierMin = $payment_info['TierMin'];
			$tierMax = $payment_info['TierMax'];
			$subs_array = array($this->source['SubId1'],$this->source['SubId2'],$this->source['SubId3'],$this->source['SubId4'],$this->source['SubId5']);
			$this->source['RequestId'] = $this->Cake->generateSession($this->source['Affiliate'], $this->source['CreativeId'], $subs_array, $payment_info['offer_type'], $payment_info['price_format']); //post credential

			$this->track_id = $this->_createTracking();
			if(!$this->track_id){
				$this->errors[] = 'Generating Track Id Failed';
				$this->_responseError('System Error');	
			} 
			$this->appType = "vendor_personalloan";//$this->appType = $this->request->data['AppType']; //post credential
			$this->_addPrimaryPost(); //Create structure for post
			$keystone_Aff_arr = $this->Affiliate->find('first', array('fields'=>array('Affiliate.id'), 'conditions'=>array('Affiliate.remote_id'=>$this->source['Affiliate'])));//primary key for Affiliates table
			$this->keystoneAffiliateId = $keystone_Aff_arr['Affiliate']['id'];
			
			
			
			$url = $this->sendArray[0]['posting_url'];
			$post_array = $this->sendArray[0]['fields'];
			
			$payout = $this->Api->formatDecimal($payment_info['default_payout']);
			$post_array['vendor_id'] = 	$this->source['Affiliate'];
			$post_array['tier_min']	= ((!empty($tierMin))? $tierMin: $payout);	
			$post_array['tier_max']	= ((!empty($tierMax))? $tierMax: 1200);
				
			$loanAmount = $post_array['loan_amount_personal'];	
			$post_array['loan_amount_personal'] = $this->_mapLoanAmount($loanAmount);
		
			$lead_data['AppType'] = $this->appType;	
			$lead_data['Mobile'] = 'false';
			$lead_data['CallType'] = "External";
			$lead_data['sub_id'] = $this->source['SubId1'];
			$clean_lead_data = $this->_externalCleanTrackData($lead_data);
			$this->_trackLead(json_encode($clean_lead_data)); //Store data
			$this->_sendToCake($url, $post_array);
			
			if($this->msg == 'success' && !empty($this->redirect)){
				
				//Send ssn hash
				$this->_hash_ssn($this->request->data['Ssn']);
				$this->_updateTrackLeadId();
				$this->_processPayout();
				$this->redirect = $this->Service->buildRedirect($this->redirect, $this->track_id);
				$this->_listManagement($this->request->data);
				$this->_responseSuccess();	
			}else{
				$this->errors[] = 'Lead was not bought';
				//$this->leadByteVendorUnsold($lead_data);
				$this->_response("Declined", "decline");
			}
			exit;
			
		} else {
		    $validation_array = $this->PersonalLoan->flatErrorArray();
			if(!empty($validation_array))$this->errors = array_merge($this->errors, $validation_array);
			$this->_responseError($msg="Validation Errors");
		}
	}


	/**
	 * This function works with the Payday model to setup the External post.
	 */
	private function _processPayday($lead_data){
		
		
		$this->Payday->set($lead_data);
		if ($this->Payday->validates()) {
				
			//Build the post
			$this->process_secondary = "false";
			$this->post_builder = $this->Api->cleanArray($this->Payday->buildPost()); //fields for post
			$this->source['OfferId'] = $this->request->data['OfferId']; //post credential
			$this->source['CampaignId'] = $this->request->data['CampaignId'];
			$this->source['Affiliate'] =  $this->request->data['Affiliate'];
			$this->source['CreativeId'] =  $this->request->data['CreativeId'];
			$this->source['SubId1'] =  ((!empty($this->request->data['SubId1'])) ?$this->request->data['SubId1'] : 0);
			$this->source['SubId2'] =  ((!empty($this->request->data['SubId2'])) ?$this->request->data['SubId2'] : 0);
			$this->source['SubId3'] =  ((!empty($this->request->data['SubId3'])) ?$this->request->data['SubId3'] : 0);
			$this->source['SubId4'] =  ((!empty($this->request->data['SubId4'])) ?$this->request->data['SubId4'] : 0);
			$this->source['SubId5'] =  ((!empty($this->request->data['SubId5'])) ?$this->request->data['SubId5'] : 0);
		
		
			//$payment_info = $this->Cake->paymentType($this->source['CampaignId']);
			$cache['hash'] = md5('cake_paymentType_'.$this->source['CampaignId']);
			$cache['value'] = false;
			$cache['value'] = Cache::read($cache['hash']);
				
			if($cache['value'] === false){
				$payment_info = $this->Cake->paymentType($this->source['CampaignId']);
				Cache::write($cache['hash'], $payment_info, '15m');
			}else{
				$payment_info = $cache['value'];
			}
						
			if(!is_array($payment_info)){
				$this->errors[] = 'Generating payment information failed';
				$this->_responseError('System Error');	
			}
			
			$tierMin = $payment_info['TierMin'];
			$tierMax = $payment_info['TierMax'];
			$subs_array = array($this->source['SubId1'],$this->source['SubId2'],$this->source['SubId3'],$this->source['SubId4'],$this->source['SubId5']);
			$this->source['RequestId'] = $this->Cake->generateSession($this->source['Affiliate'], $this->source['CreativeId'], $subs_array, $payment_info['offer_type'], $payment_info['price_format']); //post credential
						
			$this->track_id = $this->_createTracking();
			if(!$this->track_id){
				$this->errors[] = 'Generating Track Id Failed';
				$this->_responseError('System Error');	
			} 
			$this->appType = "vendor_payday";//$this->request->data['AppType']; //post credential
			$this->_addPrimaryPost(); //Create structure for post
			$keystone_Aff_arr = $this->Affiliate->find('first', array('fields'=>array('Affiliate.id'), 'conditions'=>array('Affiliate.remote_id'=>$this->source['Affiliate'])));//primary key for Affiliates table
			$this->keystoneAffiliateId = $keystone_Aff_arr['Affiliate']['id'];
			
			$url = $this->sendArray[0]['posting_url'];
			$post_array = $this->sendArray[0]['fields'];
			
			$payout = $this->Api->formatDecimal($payment_info['default_payout']);
			$post_array['vendor_id'] = 	$this->source['Affiliate'];
			$post_array['tier_min']	= ((!empty($tierMin))? $tierMin: $payout);	
			$post_array['tier_max']	= ((!empty($tierMax))? $tierMax: 1200);	
			
			$loanAmount = $post_array['loan_amount'];	
			$post_array['loan_amount'] = $this->_mapLoanAmount($loanAmount);
					
			$lead_data['AppType'] = $this->appType;	
			$lead_data['Mobile'] = 'false';
			$lead_data['CallType'] = "External";
			$lead_data['sub_id'] = $this->source['SubId1'];
			$clean_lead_data = $this->_externalCleanTrackData($lead_data);
			$this->_trackLead(json_encode($clean_lead_data)); //Store data
					
			$this->_sendToCake($url, $post_array);
					
			if($this->msg == 'success' && !empty($this->redirect)){
				
				//Send ssn hash
				$this->_hash_ssn($this->request->data['Ssn']);
				$this->_updateTrackLeadId();
				$this->_processPayout();
				$this->redirect = $this->Service->buildRedirect($this->redirect, $this->track_id);
				$this->_listManagement($this->request->data);
				$this->_responseSuccess();	
			}else{
				$this->errors[] = 'Lead was not bought';
				//$this->leadByteVendorUnsold($lead_data);
				$this->_response("Declined",  "decline");
			}
			
			exit;
		
		} else {
		    $validation_array = $this->Payday->flatErrorArray();
			if(!empty($validation_array))$this->errors = array_merge($this->errors, $validation_array);
			$this->_responseError($msg="Validation Errors");
		}
	}

	/**
	 * This function works with the Installment model to setup the External post.
	 */
	private function _processInstallment($lead_data){
		
		$lead_data['LoanAmountPersonal'] = $this->_mapLoanAmountPersonal($lead_data['LoanAmountPersonal']);
		$this->Installment->set($lead_data);
		$this->Installment->addDependencies();
		if ($this->Installment->validates()) {
			//Build the post
			$this->process_secondary = "false";
			$this->post_builder = $this->Api->cleanArray($this->Installment->buildPost()); //fields for post
			$this->source['OfferId'] = $this->request->data['OfferId']; //post credential
			$this->source['CampaignId'] = $this->request->data['CampaignId'];
			$this->source['Affiliate'] =  $this->request->data['Affiliate'];
			$this->source['CreativeId'] =  $this->request->data['CreativeId'];
			$this->source['SubId1'] =  ((!empty($this->request->data['SubId1'])) ?$this->request->data['SubId1'] : 0);
			$this->source['SubId2'] =  ((!empty($this->request->data['SubId2'])) ?$this->request->data['SubId2'] : 0);
			$this->source['SubId3'] =  ((!empty($this->request->data['SubId3'])) ?$this->request->data['SubId3'] : 0);
			$this->source['SubId4'] =  ((!empty($this->request->data['SubId4'])) ?$this->request->data['SubId4'] : 0);
			$this->source['SubId5'] =  ((!empty($this->request->data['SubId5'])) ?$this->request->data['SubId5'] : 0);
			
			//$payment_info = $this->Cake->paymentType($this->source['CampaignId']);
			$cache['hash'] = md5('cake_paymentType_'.$this->source['CampaignId']);
			$cache['value'] = false;
			$cache['value'] = Cache::read($cache['hash']);
				
			if($cache['value'] === false){
				$payment_info = $this->Cake->paymentType($this->source['CampaignId']);
				Cache::write($cache['hash'], $payment_info, '15m');
			}else{
				$payment_info = $cache['value'];
			}
			
			if(!is_array($payment_info)){
				$this->errors[] = 'Generating payment information failed';
				$this->_responseError('System Error');	
			}
			$tierMin = $payment_info['TierMin'];
			$tierMax = $payment_info['TierMax'];
			$subs_array = array($this->source['SubId1'],$this->source['SubId2'],$this->source['SubId3'],$this->source['SubId4'],$this->source['SubId5']);
			$this->source['RequestId'] = $this->Cake->generateSession($this->source['Affiliate'], $this->source['CreativeId'], $subs_array, $payment_info['offer_type'], $payment_info['price_format']); //post credential
			$this->track_id = $this->_createTracking();
			if(!$this->track_id){
				$this->errors[] = 'Generating Track Id Failed';
				$this->_responseError('System Error');	
			} 
			$this->appType = "vendor_installment";//$this->request->data['AppType']; //post credential
			$this->_addPrimaryPost(); //Create structure for post
			
			$keystone_Aff_arr = $this->Affiliate->find('first', array('fields'=>array('Affiliate.id'), 'conditions'=>array('Affiliate.remote_id'=>$this->source['Affiliate'])));//primary key for Affiliates table
			$this->keystoneAffiliateId = $keystone_Aff_arr['Affiliate']['id'];
	

			$url = $this->sendArray[0]['posting_url'];
			$post_array = $this->sendArray[0]['fields'];
			
			$payout = $this->Api->formatDecimal($payment_info['default_payout']);
			$post_array['vendor_id'] = 	$this->source['Affiliate'];
			$post_array['tier_min']	= ((!empty($tierMin))? $tierMin: $payout);	
			$post_array['tier_max']	= ((!empty($tierMax))? $tierMax: 1200);
				
			$loanAmount = $post_array['loan_amount'];	
			$post_array['loan_amount'] = $this->_mapLoanAmount($loanAmount);
			
			if($post_array['loan_amount'] > 1500){
				$post_array['loan_amount_personal'] = $post_array['loan_amount'];
				//$loanArray = array(1000,900,800);
				//$post_array['loan_amount'] = $loanArray[array_rand($loanArray)];
				$post_array['loan_amount'] = 1000;
				$post_array['app_type'] = "vendor_personalloan";
				$lead_data['AppType'] = "vendor_personalloan";
			}else{
				$lead_data['AppType'] = $this->appType;
				//$loanArray = array(1000,900,800);
				if($post_array['loan_amount']>1000){
					//$post_array['loan_amount'] = $loanArray[array_rand($loanArray)];
					$post_array['loan_amount'] = 1000;
				}
			}
					
			$lead_data['Mobile'] = 'false';
			$lead_data['CallType'] = "External";
			$lead_data['sub_id'] = $this->source['SubId1'];
			$clean_lead_data = $this->_externalCleanTrackData($lead_data);
			$this->_trackLead(json_encode($clean_lead_data)); //Store data

			
			$this->_sendToCake($url, $post_array);
			
			
			if($this->msg == 'success' && !empty($this->redirect)){
				
				//Send ssn hash
				$this->_hash_ssn($this->request->data['Ssn']);
				$this->_updateTrackLeadId();
				$this->_processPayout();
				
				$this->redirect = $this->Service->buildRedirect($this->redirect, $this->track_id);
				$this->_listManagement($this->request->data);				
				$this->_responseSuccess();	
			}else{
				$this->errors[] = 'Lead was not bought';
				//$this->leadByteVendorUnsold($lead_data);
				$this->_response("Declined",  "decline");
			}
			
			exit;
	
		} else {
		    $validation_array = $this->Installment->flatErrorArray();
			if(!empty($validation_array))$this->errors = array_merge($this->errors, $validation_array);
			$this->_responseError($msg="Validation Errors");
		}
	}

	/**
	 * This function works with the LowTier model to setup the External post.
	 */
	private function _processLowTier($lead_data){
		
		
		$this->LowTier->set($lead_data);
		if ($this->LowTier->validates()) {
				
			//Build the post
			$this->process_secondary = "false";
			$this->post_builder = $this->Api->cleanArray($this->LowTier->buildPost()); //fields for post
			$this->source['OfferId'] = $this->request->data['OfferId']; //post credential
			$this->source['CampaignId'] = $this->request->data['CampaignId'];
			$this->source['Affiliate'] =  $this->request->data['Affiliate'];
			$this->source['CreativeId'] =  $this->request->data['CreativeId'];
			$this->source['SubId1'] =  ((!empty($this->request->data['SubId1'])) ?$this->request->data['SubId1'] : 0);
			$this->source['SubId2'] =  ((!empty($this->request->data['SubId2'])) ?$this->request->data['SubId2'] : 0);
			$this->source['SubId3'] =  ((!empty($this->request->data['SubId3'])) ?$this->request->data['SubId3'] : 0);
			$this->source['SubId4'] =  ((!empty($this->request->data['SubId4'])) ?$this->request->data['SubId4'] : 0);
			$this->source['SubId5'] =  ((!empty($this->request->data['SubId5'])) ?$this->request->data['SubId5'] : 0);
			
			//$payment_info = $this->Cake->paymentType($this->source['CampaignId']);
			$cache['hash'] = md5('cake_paymentType_'.$this->source['CampaignId']);
			$cache['value'] = false;
			$cache['value'] = Cache::read($cache['hash']);
				
			if($cache['value'] === false){
				$payment_info = $this->Cake->paymentType($this->source['CampaignId']);
				Cache::write($cache['hash'], $payment_info, '15m');
			}else{
				$payment_info = $cache['value'];
			}
						
			if(!is_array($payment_info)){
				$this->errors[] = 'Generating payment information failed';
				$this->_responseError('System Error');	
			}
			
			$tierMin = $payment_info['TierMin'];
			$tierMax = $payment_info['TierMax'];
			$subs_array = array($this->source['SubId1'],$this->source['SubId2'],$this->source['SubId3'],$this->source['SubId4'],$this->source['SubId5']);
			$this->source['RequestId'] = $this->Cake->generateSession($this->source['Affiliate'], $this->source['CreativeId'], $subs_array, $payment_info['offer_type'], $payment_info['price_format']); //post credential
			$this->track_id = $this->_createTracking();
			if(!$this->track_id){
				$this->errors[] = 'Generating Track Id Failed';
				$this->_responseError('System Error');	
			} 
			$this->appType = "vendor_lowtier";//$this->request->data['AppType']; //post credential
			$this->_addPrimaryPost(); //Create structure for post
			$keystone_Aff_arr = $this->Affiliate->find('first', array('fields'=>array('Affiliate.id'), 'conditions'=>array('Affiliate.remote_id'=>$this->source['Affiliate'])));//primary key for Affiliates table
			$this->keystoneAffiliateId = $keystone_Aff_arr['Affiliate']['id'];
			
			$url = $this->sendArray[0]['posting_url'];
			$post_array = $this->sendArray[0]['fields'];
			
			$payout = $this->Api->formatDecimal($payment_info['default_payout']);
			$post_array['vendor_id'] = 	$this->source['Affiliate'];
			$post_array['tier_min']	= ((!empty($tierMin))? $tierMin: $payout);	
			$post_array['tier_max']	= ((!empty($tierMax))? $tierMax: 1200);	
			
			$lead_data['AppType'] = $this->appType;	
			$lead_data['Mobile'] = 'false';
			$lead_data['CallType'] = "External";
			$lead_data['sub_id'] = $this->source['SubId1'];
			$clean_lead_data = $this->_externalCleanTrackData($lead_data);
			$this->_trackLead(json_encode($clean_lead_data)); //Store data
			
			$this->_sendToCake($url, $post_array);
			
			if($this->msg == 'success' && !empty($this->redirect)){
				
				$this->_updateTrackLeadId();
				$this->_processPayout();
				$this->redirect = $this->Service->buildRedirect($this->redirect, $this->track_id);
				$this->_listManagement($this->request->data);
				$this->_responseSuccess();	
			}else{
				$this->errors[] = 'Lead was not bought';
				//$this->leadByteVendorUnsold($lead_data);
				$this->_response("Declined",  "decline");
			}
			
			exit;
		} else {
		    $validation_array = $this->LowTier->flatErrorArray();
			if(!empty($validation_array))$this->errors = array_merge($this->errors, $validation_array);
			$this->_responseError($msg="Validation Errors");
		}
	}

	/**
	 * This function works with the LowTierLong model to setup the External post.
	 */
	private function _processLowTierLong($lead_data){
		
		$this->LowTierLong->set($lead_data);
		if ($this->LowTierLong->validates()) {
				
			//Build the post
			$this->process_secondary = "false";
			$this->post_builder = $this->Api->cleanArray($this->LowTierLong->buildPost()); //fields for post
			$this->source['OfferId'] = $this->request->data['OfferId']; //post credential
			$this->source['CampaignId'] = $this->request->data['CampaignId'];
			$this->source['Affiliate'] =  $this->request->data['Affiliate'];
			$this->source['CreativeId'] =  $this->request->data['CreativeId'];
			$this->source['SubId1'] =  ((!empty($this->request->data['SubId1'])) ?$this->request->data['SubId1'] : 0);
			$this->source['SubId2'] =  ((!empty($this->request->data['SubId2'])) ?$this->request->data['SubId2'] : 0);
			$this->source['SubId3'] =  ((!empty($this->request->data['SubId3'])) ?$this->request->data['SubId3'] : 0);
			$this->source['SubId4'] =  ((!empty($this->request->data['SubId4'])) ?$this->request->data['SubId4'] : 0);
			$this->source['SubId5'] =  ((!empty($this->request->data['SubId5'])) ?$this->request->data['SubId5'] : 0);
			
			//$payment_info = $this->Cake->paymentType($this->source['CampaignId']);
			$cache['hash'] = md5('cake_paymentType_'.$this->source['CampaignId']);
			$cache['value'] = false;
			$cache['value'] = Cache::read($cache['hash']);
				
			if($cache['value'] === false){
				$payment_info = $this->Cake->paymentType($this->source['CampaignId']);
				Cache::write($cache['hash'], $payment_info, '15m');
			}else{
				$payment_info = $cache['value'];
			}
					
			if(!is_array($payment_info)){
				$this->errors[] = 'Generating payment information failed';
				$this->_responseError('System Error');	
			}
			
			$tierMin = $payment_info['TierMin'];
			$tierMax = $payment_info['TierMax'];
			$subs_array = array($this->source['SubId1'],$this->source['SubId2'],$this->source['SubId3'],$this->source['SubId4'],$this->source['SubId5']);
			$this->source['RequestId'] = $this->Cake->generateSession($this->source['Affiliate'], $this->source['CreativeId'], $subs_array, $payment_info['offer_type'], $payment_info['price_format']); //post credential
			$this->track_id = $this->_createTracking();
			if(!$this->track_id){
				$this->errors[] = 'Generating Track Id Failed';
				$this->_responseError('System Error');	
			} 
			$this->appType = "vendor_lowtierlong";//$this->request->data['AppType']; //post credential
			$this->_addPrimaryPost(); //Create structure for post
			$keystone_Aff_arr = $this->Affiliate->find('first', array('fields'=>array('Affiliate.id'), 'conditions'=>array('Affiliate.remote_id'=>$this->source['Affiliate'])));//primary key for Affiliates table
			$this->keystoneAffiliateId = $keystone_Aff_arr['Affiliate']['id'];
			
			$url = $this->sendArray[0]['posting_url'];
			$post_array = $this->sendArray[0]['fields'];
			
			$payout = $this->Api->formatDecimal($payment_info['default_payout']);
			$post_array['vendor_id'] = 	$this->source['Affiliate'];
			$post_array['tier_min']	= ((!empty($tierMin))? $tierMin: $payout);	
			$post_array['tier_max']	= ((!empty($tierMax))? $tierMax: 1200);	
			
			$loanAmount = $post_array['loan_amount'];	
			$post_array['loan_amount'] = $this->_mapLoanAmount($loanAmount);
			
			$lead_data['AppType'] = $this->appType;	
			$lead_data['Mobile'] = 'false';
			$lead_data['CallType'] = "External";
			$lead_data['sub_id'] = $this->source['SubId1'];
			$clean_lead_data = $this->_externalCleanTrackData($lead_data);
			$this->_trackLead(json_encode($clean_lead_data)); //Store data
			
			$this->_sendToCake($url, $post_array);
			
			if($this->msg == 'success' && !empty($this->redirect)){
				
				//Send ssn hash
				$this->_hash_ssn($this->request->data['Ssn']);
				$this->_updateTrackLeadId();
				$this->_processPayout();
				$this->redirect = $this->Service->buildRedirect($this->redirect, $this->track_id);
				$this->_listManagement($this->request->data);
				$this->_responseSuccess();	
			}else{
				$this->errors[] = 'Lead was not bought';
				//$this->leadByteVendorUnsold($lead_data);
				$this->_response("Declined",  "decline");
			}
			
			exit;	
		} else {
		    $validation_array = $this->LowTierLong->flatErrorArray();
			if(!empty($validation_array))$this->errors = array_merge($this->errors, $validation_array);
			$this->_responseError($msg="Validation Errors");
		}
	}
	
	
	/**
+	 * This function works with the Medical model to setup the External post.
+	 */
		private function _processMedicalInsurance($lead_data){

		$this->Medical->set($lead_data);
		if ($this->Medical->validates()) {

			//Build the post
			$this->process_secondary = "false";
			$this->post_builder = $this->Api->cleanArray($this->Medical->buildPost()); //fields for post
			$this->source['OfferId'] = $this->request->data['OfferId']; //post credential
			$this->source['CampaignId'] = $this->request->data['CampaignId'];
			$this->source['Affiliate'] =  $this->request->data['Affiliate'];
			$this->source['CreativeId'] =  $this->request->data['CreativeId'];
			$this->source['SubId1'] =  ((!empty($this->request->data['SubId1'])) ?$this->request->data['SubId1'] : 0);
			$this->source['SubId2'] =  ((!empty($this->request->data['SubId2'])) ?$this->request->data['SubId2'] : 0);

			$cache['hash'] = md5('cake_paymentType_'.$this->source['CampaignId']);
			$cache['value'] = false;
			$cache['value'] = Cache::read($cache['hash']);

			if($cache['value'] === false){

				$payment_info = $this->Cake->paymentType($this->source['CampaignId']);
				Cache::write($cache['hash'], $payment_info, '15m');
			}else{
 				$payment_info = $cache['value'];
			}
			if(!is_array($payment_info)){
				$this->errors[] = 'Generating payment information failed';
				$this->_responseError('System Error');	
			}
		
			$tierMin = $payment_info['TierMin'];
			$tierMax = $payment_info['TierMax'];
			$subs_array = array($this->source['SubId1'],$this->source['SubId2']);

			$this->source['RequestId'] = $this->Cake->generateSession($this->source['Affiliate'], $this->source['CreativeId'], $subs_array, $payment_info['offer_type'], $payment_info['price_format']); //post credential
			$this->track_id = $this->_createTracking();

			if(!$this->track_id){
				$this->errors[] = 'Generating Track Id Failed';
				$this->_responseError('System Error');	
			} 
			$this->appType =$this->request->data['AppType']; //post credential
			$this->_addPrimaryPost(); //Create structure for post
			$keystone_Aff_arr = $this->Affiliate->find('first', array('fields'=>array('Affiliate.id'), 'conditions'=>array('Affiliate.remote_id'=>$this->source['Affiliate'])));//primary key for Affiliates table
			
			$this->keystoneAffiliateId = $keystone_Aff_arr['Affiliate']['id'];
			
			$url = $this->sendArray[0]['posting_url'];
			$post_array = $this->sendArray[0]['fields'];

			$payout = $this->Api->formatDecimal($payment_info['default_payout']);
			$post_array['vendor_id'] = 	$this->source['Affiliate'];
			$post_array['tier_min']	= ((!empty($tierMin))? $tierMin: $payout);	
			$post_array['tier_max']	= ((!empty($tierMax))? $tierMax: 1200);	

			//$loanAmount = $post_array['loan_amount'];	
			//$post_array['loan_amount'] = $this->_mapLoanAmount($loanAmount);
		
			$lead_data['AppType'] = $this->appType;	
			$lead_data['Mobile'] = 'false';
			$lead_data['CallType'] = "External";
			$lead_data['sub_id'] = $this->source['SubId1'];
			
			$this->_trackLead(json_encode($lead_data)); //Store data
			$this->_sendToCake($url, $post_array);

			if($this->msg == 'success' && !empty($this->redirect)){
				
				//Send ssn hash
				$this->_updateTrackLeadId();
				$this->_processPayout();
				$this->redirect = $this->Service->buildRedirect($this->redirect, $this->track_id);
				$this->_listManagement($this->request->data);
				$this->_responseSuccess();	
			}else{
				$this->errors[] = 'Lead was not bought';
				//$this->leadByteVendorUnsold($lead_data);
				$this->_response("Declined",  "decline");
			}
			
			exit;
		}else{
			$validation_array = $this->Medical->flatErrorArray();
			if(!empty($validation_array))$this->errors = array_merge($this->errors, $validation_array);
			$this->_responseError($msg="Validation Errors");
		}
	}

	/**
	 * This function works with the ListMgmt model to setup the External post.
	 */
	private function _processListManagement($lead_data){
	
		
		$this->ListManagementValidate->set($lead_data);
		if($lead_data['CreativeId'] == "-5"){
			$this->ListManagementValidate->removeValidation('Email'); //Remove validation for md5 email
			$this->ListManagementValidate->removeValidation('CreativeId'); //Remove validation for Creative Id
		}
			
		if ($this->ListManagementValidate->validates()) {
			
			$this->process_secondary = "false";
			$this->source['OfferId'] = $this->request->data['OfferId']; //post credential
			$this->source['CampaignId'] = $this->request->data['CampaignId'];
			$this->source['Affiliate'] =  $this->request->data['Affiliate'];
			$this->source['CreativeId'] =  ((!empty($this->request->data['CreativeId'])) ?$this->request->data['CreativeId'] : 0);
			$this->source['SubId1'] =  ((!empty($this->request->data['SubId1'])) ?$this->request->data['SubId1'] : "0");
			$this->source['RequestId'] = "0";
				
			$this->track_id	=99999;
			/*$this->track_id = $this->_createTracking();
			if(!$this->track_id){
				$this->errors[] = 'Generating Track Id Failed';
				$this->_responseError('System Error');
			}*/
			$this->appType = "esp";
					
			$lead_data['AppType'] = $this->appType;
			$lead_data['Mobile'] = 'false';
			$lead_data['CallType'] = "External";
			$lead_data['sub_id'] = $this->source['SubId1'];
			//$this->_trackLead(json_encode($lead_data)); //Store data
				
				
			if($lead_data['CreativeId'] == "-5"){ //Send to Esps with md5 Email
	
				$this->Esp->data['Esp'] = $lead_data;
				$type = "api_main";
				$rsp_array = $this->Esp->sendToEsp(0, $type);
			}else if($lead_data['CreativeId'] == "0"){ //add to suppression list
					
				$this->Esp->data['Esp'] = $lead_data;
				$type = "api_main";
				$rsp_array = $this->Esp->sendToEsp(0, $type);
					
			}else{ //Send to Esps for add subscriber
	
				$this->Esp->data['Esp'] = $lead_data;
				$type = "main";
				$rsp_array = $this->Esp->sendToEsp(0, $type);
			}
			
			$response = array();
			$response['status'] = 'decline';
				
			if(count($rsp_array) > 0){
				foreach($rsp_array as $k=>$v){
					//find if one call was successfully sent
					if($v['success'] == 'true'){
						$response['status'] = "success";
						break;
					}
				}
			}
	
			echo $this->Api->jsonresponse($response);
			exit;	
		} else {
			$validation_array = $this->ListManagementValidate->flatErrorArray();
			if(!empty($validation_array))$this->errors = array_merge($this->errors, $validation_array);
			$this->_responseError($msg="Validation Errors");
		}
	}

	private function leadByteVendorUnsold($data){
		$socket = new HttpSocket(array('ssl_verify_peer'=>false, 'ssl_verify_host'=>false));
		
		//Keystone field names to matching leadbyte field name
		$fields = array('FirstName'=>'firstname', 'LastName'=>'lastname', 'DateOfBirth'=>'dob', 'Address1'=>'street1', 'Address2'=>'street2', 'City'=>'towncity', 'State'=>'county', 'Zip'=>'postcode');
		$params =  array(	'campid'=>"US-VENDORUNSOLDPOST",
							'sid'=>"02",
							'Opt-In_Date'=>date("d/m/Y H:i:s"),
							'email'=>$data['Email'],
							'source'=>$data['Url'],
							'ipaddress'=>$data['IPAddress']
		);
		
		
		foreach($fields as $field=>$postfield){
			
			if($field == "DateOfBirth" && isset($data[$field]) && !empty($data[$field]) ){
				$birthday = explode("/", $data[$field]);
				$params[$postfield] = $birthday[1]."/".$birthday[0]."/".$birthday[2];
			}else if( isset($data[$field]) && !empty($data[$field]) ){
				$params[$postfield] = $data[$field];
			}
		}
		
		$response = $socket->post('https://flatsixmedia.leadbyte.co.uk/api/submit.php', $params);
	}
	
	private function _listManagement($data){
		$records = $this->ListManagement->pullListOffer($this->source['OfferId']);

		if(!empty($records)){
			$data['Price'] = $this->listManagementPrice;
			$this->ListManagement->send2List($data, $records['name'], $records['list_id'], $this->track_id);
		}
	}

	private function _externalCleanTrackData($arr){
		
		$excludedFields = array('Ssn', 'BankAccountType', 'BankRoutingNumber', 'BankAccountNumber', 'BankTime', 'CoSsn');
		
		foreach($arr as $k=>$v){
			if(in_array($k, $excludedFields)){
				unset($arr[$k]);
			}
		}
		
		return $arr;
	}
	
	private function _mapLoanAmountPersonal($amt){
		$amt = (INT)$amt;
		
		if($amt < 100){
			$n_amt = 300;
		}else if( $amt >= 100 && $amt <= 199){
			$n_amt = 300;
		}else if( $amt >= 200 && $amt <= 299){
			$n_amt = 300;
		}else if( $amt >= 300 && $amt <= 399){
			$n_amt = 300;
		}else if( $amt >= 400 && $amt <= 499){
			$n_amt = 300;
		}else if( $amt >= 500 && $amt <= 599){
			$n_amt = 500;
		}else if( $amt >= 600 && $amt <= 699){
			$n_amt = 500;
		}else if( $amt >= 700 && $amt <= 799){
			$n_amt = 500;
		}else if( $amt >= 800 && $amt <= 899){
			$n_amt = 500;
		}else if( $amt >= 900 && $amt <= 999){
			$n_amt = 500;
		}else if( $amt > 999 && $amt <= 1999){
			$n_amt = 1500;
		}else if( $amt >= 2000 && $amt <= 2999){
			$n_amt = 2500;
		}else if( $amt >= 3000 && $amt <= 3999){
			$n_amt = 3500;
		}else if( $amt >= 4000 && $amt <= 4999){
			$n_amt = 4500;
		}else if( $amt >= 5000 && $amt <= 5999){
			$n_amt = 5500;
		}else if( $amt >= 6000 && $amt <= 6999){
			$n_amt = 6500;
		}else if( $amt >= 7000 && $amt <= 7999){
			$n_amt = 7500;
		}else if( $amt >= 8000 && $amt <= 8999){
			$n_amt = 8500;
		}else if( $amt >= 9000 && $amt <= 9999){
			$n_amt = 9500;
		}else if( $amt >= 10000 && $amt <= 10999){
			$n_amt = 10000;
		}else if( $amt >= 11000 && $amt <= 11999){
			$n_amt = 11500;
		}else if( $amt >= 12000 && $amt <= 12999){
			$n_amt = 12500;
		}else if( $amt >= 13000 && $amt <= 13999){
			$n_amt = 13500;
		}else if( $amt >= 14000 && $amt <= 14999){
			$n_amt = 14500;
		}else if( $amt >= 15000 && $amt <= 15999){
			$n_amt = 15000;
		}else if( $amt >= 16000 && $amt <= 16999){
			$n_amt = 16500;
		}else if( $amt >= 17000 && $amt <= 17999){
			$n_amt = 17500;
		}else if( $amt >= 18000 && $amt <= 18999){
			$n_amt = 18500;
		}else if( $amt >= 19000 && $amt <= 19999){
			$n_amt = 19500;
		}else if( $amt >= 20000 && $amt <= 20999){
			$n_amt = 20500;
		}else if( $amt >= 21000 && $amt <= 21999){
			$n_amt = 21500;
		}else if( $amt >= 22000 && $amt <= 22999){
			$n_amt = 22500;
		}else if( $amt >= 23000 && $amt <= 23999){
			$n_amt = 23500;
		}else if( $amt >= 24000 && $amt <= 24999){
			$n_amt = 24500;
		}else if( $amt >= 25000 && $amt <= 25999){
			$n_amt = 25500;
		}else if( $amt >= 26000 && $amt <= 26999){
			$n_amt = 26500;
		}else if( $amt >= 27000 && $amt <= 27999){
			$n_amt = 27500;
		}else if( $amt >= 28000 && $amt <= 28999){
			$n_amt = 28500;
		}else if( $amt >= 29000 && $amt <= 30000){
			$n_amt = 29500;
		}else if( $amt > 30000 ){
			$n_amt = 29500;
		}
		return $n_amt;
	}
	
	private function _mapLoanAmount($amt){
		$amt = (INT)$amt;
		
		if($amt < 100){
			$n_amt = 100;
		}else if( $amt >= 100 && $amt <= 199){
			$n_amt = 100;
		}else if( $amt >= 200 && $amt <= 299){
			$n_amt = 200;
		}else if( $amt >= 300 && $amt <= 399){
			$n_amt = 300;
		}else if( $amt >= 400 && $amt <= 499){
			$n_amt = 400;
		}else if( $amt >= 500 && $amt <= 599){
			$n_amt = 500;
		}else if( $amt >= 600 && $amt <= 699){
			$n_amt = 600;
		}else if( $amt >= 700 && $amt <= 799){
			$n_amt = 700;
		}else if( $amt >= 800 && $amt <= 899){
			$n_amt = 800;
		}else if( $amt >= 900 && $amt <= 1000){
			$n_amt = 900;
		}else if( $amt > 1000 && $amt <= 1999){
			$n_amt = 1500;
		}else if( $amt >= 2000 && $amt <= 2999){
			$n_amt = 2500;
		}else if( $amt >= 3000 && $amt <= 3999){
			$n_amt = 3500;
		}else if( $amt >= 4000 && $amt <= 4999){
			$n_amt = 4500;
		}else if( $amt >= 5000 && $amt <= 5999){
			$n_amt = 5500;
		}else if( $amt >= 6000 && $amt <= 6999){
			$n_amt = 6500;
		}else if( $amt >= 7000 && $amt <= 7999){
			$n_amt = 7500;
		}else if( $amt >= 8000 && $amt <= 8999){
			$n_amt = 8500;
		}else if( $amt >= 9000 && $amt <= 9999){
			$n_amt = 9500;
		}else if( $amt >= 10000 && $amt <= 10999){
			$n_amt = 10000;
		}else if( $amt >= 11000 && $amt <= 11999){
			$n_amt = 11500;
		}else if( $amt >= 12000 && $amt <= 12999){
			$n_amt = 12500;
		}else if( $amt >= 13000 && $amt <= 13999){
			$n_amt = 13500;
		}else if( $amt >= 14000 && $amt <= 14999){
			$n_amt = 14500;
		}else if( $amt >= 15000 && $amt <= 15999){
			$n_amt = 15000;
		}else if( $amt >= 16000 && $amt <= 16999){
			$n_amt = 16500;
		}else if( $amt >= 17000 && $amt <= 17999){
			$n_amt = 17500;
		}else if( $amt >= 18000 && $amt <= 18999){
			$n_amt = 18500;
		}else if( $amt >= 19000 && $amt <= 19999){
			$n_amt = 19500;
		}else if( $amt >= 20000 && $amt <= 20999){
			$n_amt = 20500;
		}else if( $amt >= 21000 && $amt <= 21999){
			$n_amt = 21500;
		}else if( $amt >= 22000 && $amt <= 22999){
			$n_amt = 22500;
		}else if( $amt >= 23000 && $amt <= 23999){
			$n_amt = 23500;
		}else if( $amt >= 24000 && $amt <= 24999){
			$n_amt = 24500;
		}else if( $amt >= 25000 && $amt <= 25999){
			$n_amt = 25500;
		}else if( $amt >= 26000 && $amt <= 26999){
			$n_amt = 26500;
		}else if( $amt >= 27000 && $amt <= 27999){
			$n_amt = 27500;
		}else if( $amt >= 28000 && $amt <= 28999){
			$n_amt = 28500;
		}else if( $amt >= 29000 && $amt <= 30000){
			$n_amt = 29500;
		}else if( $amt > 30000 ){
			$n_amt = 29500;
		}
		
		return $n_amt;
	}
	
	private function _responseSuccess($type='json'){
		
		$response['status'] = 'success';
		$response['message'] = array();
		$response['data'] = array(	"lead_id"  	=> 	$this->track_id,
									"redirect"	=>	$this->redirect,
									"price"		=>	$this->calculated_price
							);
		echo $this->Api->jsonresponse($response);
		exit;	
	}
	
	private function _responseError($msg="",$type='json'){
		
		$response['status'] = "error";
		$response['message'] = 	array($msg =>  ((!empty($this->errors))? $this->errors : array() )
									
									);
		$response['data'] = array("lead_id" => $this->track_id);
		echo $this->Api->jsonresponse($response);
		exit;
		
	}

	private function _response($msg="", $status = "", $type='json'){
		
		if($status == ""){$response['status'] = "decline";}else{$response['status'] = $status;}
		$response['message'] = 	array($msg =>  ((!empty($this->errors))? $this->errors : array() )
									);
		$response['data'] = array("lead_id" => $this->track_id);
		echo $this->Api->jsonresponse($response);
		exit;
		
	}
	
	private function _hasRedirect(){
		if($rsp = $this->RedirectUrl->getRedirect($this->track_id)){
			$this->raw_response = "success - Lead previously processed";
			$this->redirect = $rsp['url'];
			$this->show_break_page = false;
			
			$this->RedirectUrl->id = $rsp['id'];
			$this->RedirectUrl->saveField('count', (INT)$this->RedirectUrl->field('count')+1);
			$this->RedirectUrl->clear();
			
			return true;
				
		}else{
			return false;
		}
	}
	
	//Guaranteed Buy post to cake
	private function _processingGuaranteedBuyer(){
		$gb_info = Configure::read('GuaranteedBuyer.Info');
		$gb_errors = array();
					
	
		$cache['hash'] = md5('cake_paymentType_'.$gb_info['CampaignId']);
		$cache['value'] = false;
		$cache['value'] = Cache::read($cache['hash']);
			
		if($cache['value'] === false){
			$payment_info = $this->Cake->paymentType($gb_info['CampaignId']);
			Cache::write($cache['hash'], $payment_info, '15m');
		}else{
			$payment_info = $cache['value'];
		}
		
		//If true, error will show on original lead
		if(!is_array($payment_info)){
			$gb_errors[401] = "Guaranteed Buyer Payment Type Failed";
			$errors = array('ERRORS' => $gb_errors);
			$track_json = json_encode($errors);
			$this->_trackLead($track_json);	
			return;
		}
		
		//Keep original credentials just incase
		$original_info = array('TrackId'=>$this->track_id,'OfferId'=>$this->source['OfferId'], 'CampaignId'=>$this->source['CampaignId'], 'Affiliate'=>$this->source['Affiliate'], 'CreativeId'=>$this->source['CreativeId']);
		
		//Change credentials to GB INFO
		$this->source['OfferId'] = $this->request->data['OfferId'] = $gb_info['OfferId'];
		$this->source['CampaignId'] = $this->request->data['CampaignId'] = $gb_info['CampaignId'];
		$this->source['SubId1'] = $this->request->data['SubId1'] = $this->source['Affiliate']; //Use main affiliate id as GB SUB 1
		$this->source['Affiliate'] =  $this->request->data['Affiliate'] = $gb_info['Affiliate'];
		$this->source['CreativeId'] =  $this->request->data['CreativeId'] = $gb_info['CreativeId'];
		
		$subs_array = array($this->source['SubId1']); 
		$this->source['RequestId'] = $this->Cake->generateSession($this->source['Affiliate'], $this->source['CreativeId'], $subs_array, $payment_info['offer_type'], $payment_info['price_format']);
		
		$this->track_id = $this->request->data['id'] = false; //set to false so we can be sure if create tracking fails and Track does an insert
		$this->track_id = $this->_createTracking();
		
		//This error will show on the original lead
		if(!$this->track_id){
			$gb_errors[402] = "Guaranteed Buyer Track Id Failed";
			$errors = array('ERRORS' => $gb_errors);
			$track_json = json_encode($errors);
			$this->track_id = $original_info['TrackId']; //set track id back to orignal value to insert error
			$this->_trackLead($track_json);	
			return;
		}
			
		$tracking_fields = array( 'CreditRating','Zip','Military','MonthlyNetIncome','Agree', //landing page
								  'LoanAmount','LoanPurpose','CoApplicant','FirstName','LastName','Email','Address1','Address2','City','ResidenceType','RentMortgage','ResidentSinceDate', //Personal Info
								  'DateOfBirth','DriversLicenseNumber','DriversLicenseState','PrimaryPhone','PhoneType','SecondaryPhone', //Verify Identity
								  'Paydate2','Paydate1','EmployeeType','EmployerName','EmploymentTime','WorkPhone','EmployerAddress','EmployerZip','EmployerCity','PayFrequency', //Employment Info
								  'CoFirstName','CoLastName','CoPrimaryPhone','CoDateOfBirth','CoEmployeeType','CoEmployerName','CoWorkPhone','CoEmploymentTime','CoMonthlyNetIncome','CoAppSameAddr','CoAddress1','CoAddress2','CoZip','CoCity', //Co-Applicant
								  'BankAccountType','BankName','BankTime','DirectDeposit','swap','AgreeConsent','AgreePhone' //Deposit Cash		
		);	
		
		$lead_data = array();	
		foreach($tracking_fields as $field){
			if( isset($this->data[$field]) && $this->data[$field] != "" ){
				$lead_data[$field] = $this->data[$field];
			}
		}	
		
		$lead_data['CallType'] 	= 'internal';
		$lead_data['Template'] 	= $this->source['Template'];
		$lead_data['Theme'] 	= 'GuaranteedBuy';
		$lead_data['sub_id'] 	= $this->source['SubId1'];
		$lead_data['IPAddress'] = $this->data['IPAddress'];
		$lead_data['AppType'] = 'gb';
			
		$this->_trackLead(json_encode($lead_data)); //Store data

		//Needed for payouts/bucket section
		$keystone_Aff_arr = $this->Affiliate->find('first', array('fields'=>array('Affiliate.id'), 'conditions'=>array('Affiliate.remote_id'=>$this->source['Affiliate'])));//primary key for Affiliates table
		$this->keystoneAffiliateId = $keystone_Aff_arr['Affiliate']['id'];
		
		$url = $this->sendArray[0]['posting_url'];
		$post_array = $this->sendArray[0]['fields'];
		
		//Set Cake Post Credentials 
		$post_array['ckm_offer_id'] = $this->source['OfferId'];
		$post_array['ckm_request_id'] = $this->source['RequestId'];
		$post_array['app_type'] = 'gb';
		
		$this->_sendToCake($url, $post_array);
		
		if($this->msg == 'success' && !empty($this->redirect)){
				
			//Send ssn hash
			$this->_hash_ssn($this->data['Ssn']);
			$this->_updateTrackLeadId();
			$this->_processPayout();
		}else{
			//Nothing to do yet
		}
		
		return;
		
	}

	/*
	 * Processes the Internal posts for websites
	 */	
	public function processInternalLead(){
		
		//Check to make sure of Request Type
		if(!$this->request->is('post')){
			//Show decline
		}
		
		if($this->debug)$this->Api->print_msg('Is A Post');
		
		//Get Post Data
		$post = $this->request->data;
		
		
				
		//Check the authorization of 
		if(!isset($post['source']['auth_key'])){
			//Show Decline
		}
		
		if($this->debug)$this->Api->print_msg('Start _initializeData Function');		
		//Set lead, source, userdata and pull post config
		$this->_initializeData($post);
		if($this->debug)$this->Api->print_msg('End _initializeData Function');
		
		if($this->debug)$this->Api->print_msg('Start _checkConditionalPost Function');
		//Check if this is the main post or secondary post
		$this->_checkConditionalPost();
		if($this->debug)$this->Api->print_msg('End _checkConditionalPost Function');
		
		
		//See if lead was completed in less than 2mins. Do not send ltl,widget,prepop affiliate offers to fraud check
		$whitelistIPs = Configure::read('IPWhitelist.list');
		if(!in_array($this->data['IPAddress'], $whitelistIPs) && $this->source['OfferId'] != "50" && $this->source['OfferId'] != "113" && $this->source['CampaignId'] != "1311"){
			
			$track_rec = $this->Track->find('first', array('conditions'=>array('id'=>$this->track_id),
														   'fields'=>array('created')
														  )
										   );
			$time_now = strtotime("now");
			if(!empty($track_rec)){
				$time_then = strtotime($track_rec['Track']['created']);	
				
				$form_completion_time = $time_now-$time_then;
				
				if($form_completion_time <= 120){
					//Mark as error in keystone
					$errors = array('ERRORS' => array(470=>'Fraudulent Lead - Lead Completion Time Was Only '.$form_completion_time.' seconds.'));
					$track_json = json_encode($errors);
					$this->_trackLead($track_json);
					
					//show decline message
					$this->_status_msg('DECLINED');
					echo $this->status_json;
					exit;	
				}
			}
		}	
		
		if(!$this->_hasRedirect()){
			
			//IP Fraud Check
			if($this->Ip->isBlacklisted($this->data['IPAddress'])){
			
				//Mark as error in keystone
				$errors = array('ERRORS' => array(400=>'Fraudulent Lead - IP '.$this->data['IPAddress'].' is blacklisted.'));
				$track_json = json_encode($errors);
				$this->_trackLead($track_json);
				
				//show decline message
				$this->_status_msg('DECLINED');
				echo $this->status_json;
				exit;
			}else{
				
				$browser = $this->data['Browser'];
				if(!empty($browser)){
					//Get browser ID
					$browser_id = $this->Browser->getBrowserId($browser);
					
					
					//Don't break the site if connection fails
					$exception = false;
					try{
						$ip_response = $this->Ip->getIp($this->data['IPAddress']);
					}catch(Exception $e){
						$exception = true;
						$ip_connect_failure = $e->getMessage();
					}
					
					
					//No Connection Failure to IP third party
					if(!$exception){
						//Always an array
						if(is_array($ip_response)){
							$ip = $ip_response['ip'];
						}
						
						//Get the Keystone Affiliate ID.  NOT CAKE AFFILIATE ID
						$affiliate_info = $this->Affiliate->find('first', array( 'fields'=>array('id'),
													 'conditions'=>array('remote_id'=>$this->source['Affiliate'])
													 
											  )
								      );
						
						//Make sure the main data is present
						if(isset($ip) && isset($browser_id) && isset($affiliate_info['Affiliate']['id'])){
							//Add Record
							$fdata = array('affiliate_id' => $affiliate_info['Affiliate']['id'],
										   'browser_id'=> $browser_id,
										   'ip_id'=> $ip,
										   'track_id'=>$this->track_id
										  );
							$this->FraudBrowser->addRecord($fdata);
						}else{
							#Main data is somehow missing. Do Something
						}
					}else{
						#Exception Error Do Something
					}
				}
			}
			
			if($this->post_progress == 1){ //PayDay path
				
				$this->_buildPost();
				$this->_addPrimaryPost();		
				
			}else if($this->post_progress == 2){ //Personal Loan path
				
				$this->_buildPost();
				$this->_addPrimaryPost();		
				
			}else{
				//Came in from break page, resell to payday
				$this->_addResellPost();		
				
			}
			
			$this->_processingCake();
			
			if(preg_match("/success/",$this->raw_response) && !empty($this->redirect)){ //Personal loan Or Payday Accept
				//BLOCK 1
				$this->_listManagement($post['data']);			
				$this->_status_msg('ACCEPT', urlencode($this->redirect) );
			}
			else if(preg_match("/error/",$this->raw_response) ){//All other errors will fall in this block
				//BLOCK 5
				$this->_status_msg('DECLINED');
				
			}else{ //This shouldn't happen but it is here anyway.
				//BLOCK 6
				$this->_status_msg('ERROR');
			}
			
			//Perform Backend Site Config Stuff
			/*function siteConfigExist
			 * @param site_id
			 * @param string  - Type of backend action to look for.
			 */
			if($this->SiteConfiguration->siteConfigExist($this->site_id, 'gb'))$this->_processingGuaranteedBuyer(); //Guaranteed Buyer
		
		}else{
			$this->_status_msg('ACCEPT', urlencode($this->redirect) );	//previous lead
		}

		echo $this->status_json;
		exit;
	}	

	public function processCrmLeadSpa(){

		if(!$this->request->is('post')){
			$this->errors[] = 'Request type is not a POST';
			$this->_responseError($msg="POST");			
		}
		$lead_data = $this->request->data;

		//Post Call -- First API Call
		if( !isset($lead_data['LoanAmountSecond'])){
			$result = $this->step2_processLeadSpa($lead_data);
			$response = json_decode($result);

			if($response->result == 'failed'){
				$return['status'] ='failed';
				$return['redirect'] = '';
			}

			//Lead ID from first API response
			$lead_id = $response->lead_id;
			$lead_data["lead_id"] = $lead_id;
			$return['lead_id'] = $lead_id;

		} else {
			//Lead ID resent on payday amount selection page
			$return['lead_id'] = $lead_data["lead_id"];
		}

		//Call to distrbute
		$result = $this->update_processLeadSpa($lead_data);
		$response = json_decode($result);

		//Response formatting for redirect
		$info = $response->info;
		$return['status'] = $response->message;
        $return['redirect'] = $info->redirectURL;
        $return['total_sold'] = $info->total_delivery;

		if(isset($lead_data['RequestId'])){

			if($info->total_delivery > 0){
		 
		    	$urlpixel = "https://nkoeg.com/pixel.do?t=pb&request_id=".$lead_data['RequestId'];
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $urlpixel);
				$rsp = curl_exec($ch);
				curl_close($ch);
				$lead_data['pixel_response'] = $rsp;

				/*$pixel_url = "https://nkoeg.com/pixel.do?t=pb&request_id=".$lead_data['RequestId'];	
				$rsp = $this->Api->firePixel($pixel_url);
				$lead_data['pixel_response'] = $rsp;*/
		    }
		}

		$lead_info = json_encode($lead_data);
        if(!isset($lead_data['LoanAmountSecond'])){

        	//Insert in Clickmedia DB			
	        $this->Lead->create(array( 'lead_info'=>$lead_info, 'lead_id'=>$lead_data['lead_id'], 'response'=>json_encode($response), 'created_at'=> date("Y-m-d G:i:s", time()),'updated_at'=>date("Y-m-d G:i:s", time())));
			$this->Lead->save();
        }else{

        	//Update Clickmedia DB
        	$lead = $this->Lead->find('first', array(
				'conditions' => array(
				'lead_info LIKE ' => '"%'.$lead_data["lead_id"].'%"'
				)
			));        	
        	$lead = $this->Lead->find('first', $params);			
			$this->Lead->id = $lead['Lead']['id'];
			$this->Lead->set('lead_info', $lead_info);
			if(!empty($response)){
				$this->Lead->set('response', json_encode($response));
			}
			$this->Lead->set('updated_at', date("Y-m-d G:i:s", time()));
			$this->Lead->save();
        }
        
        $minutes = "";
        if( isset($lead_data['Check'])){
        	if($lead_data['Check'] == true)
			{
				$lead = $this->Lead->find('first', $params);
	            $datetime1 = strtotime($lead_data['Created']);	          
				$datetime2 = strtotime(date("Y-m-d G:i:s", time()));			
				$interval  = abs($datetime2 - $datetime1);
				$minutes   = round($interval / 60);		            
			}
		}

		if($minutes == "" || $minutes <= 3)	  
		{
			echo $this->Api->jsonresponse($return);
			exit;

        } else if($minutes > 3){
		    $return['status'] = 'error';
            $return['redirect'] = '';
            echo $return;
			exit;
    	}
	}

	public function step2_processLeadSpa($lead_data){
		$query  = 'lp_test=0'.
                '&ip_address='.$lead_data['IPAddress'].
                '&lp_campaign_id='.$lead_data['Campaign_id'].
                '&lp_campaign_key='.$lead_data['Campaign_key'].
                '&lp_no_distribute=1'.

                //First Page
                '&loan_purpose='.$lead_data['LoanPurpose'].
                '&credit_rating='.$lead_data['CreditRating'].
				'&zip_code='.$lead_data['Zip'].
				'&military='.$lead_data['Military'].
				'&user_data='.$lead_data['Browser'].
				'&agree=1'.
                
				//Personal Information
				'&loan_amount='.$lead_data['LoanAmount'].
                '&first_name='.$lead_data['FirstName'].
                '&last_name='.$lead_data['LastName'].
                '&email_address='.$lead_data['Email'].
                '&address='.$lead_data['Address1'].      
                '&state='.$lead_data['State'].
                '&city='.$lead_data['City'].
                '&residence_type='.$lead_data['ResidenceType'].
                '&rent_mortgage='.$lead_data['RentMortgage'].
                '&residence_month='.$lead_data['ResidentSinceDate'];
                
                if(isset($lead_data['RequestId']))
                	$query.='&lp_request_id='.$lead_data['RequestId'];

                if(isset($lead_data['SubId1']))
                    $query.='&lp_s1='.$lead_data['SubId1'];
                
                if(isset($lead_data['SubId2']))
                    $query.='&lp_s2='.$lead_data['SubId2'];
                
                if(isset($lead_data['SubId3']))
                    $query.='&lp_s3='.$lead_data['SubId3'];
                
                if(isset($lead_data['SubId4']))
                    $query.='&lp_s4='.$lead_data['SubId4'];

                if(isset($lead_data['SubId5']))
                    $query.='&lp_s5='.$lead_data['SubId5'];

                if(isset($lead_data['Address2']))
                    $query.='&address2='.$lead_data['Address2'];

                if(isset($lead_data['LoanAmount1']))
                	$query.='&loan_amount_first='.$lead_data['LoanAmount1'];
                else
                	$query.='&loan_amount_first='.$lead_data['LoanAmount'];
                
                if(isset($lead_data['Url']))
                	$query.='&URL='.$lead_data['Url'];

                $curl = curl_init();

        $url = "https://cmportal.leadspediatrack.com/post.do";
        $query = str_replace(' ', '+', $query);
        curl_setopt_array($curl, array(
          CURLOPT_URL => $url."?".$query,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 180,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_POSTFIELDS => "{}",
        ));

        if (curl_error($curl)) {
            $status_json = "cURL Error #:" . curl_error($curl);
        } else {              
            $status_json = curl_exec($curl);
        }
        curl_close($curl);
        $xml = simplexml_load_string($status_json);
        $status_json= json_encode($xml);
        return $status_json;
	}

	public function update_processLeadSpa($lead_data){

		if($lead_data['LeadID'] != "")
			$lead_id =$lead_data['LeadID'] ;
		else
			$lead_id =$lead_data['lead_id'] ;

		//Formatting
		$dob = date("Y-m-d", strtotime($lead_data['DateOfBirth']));
		$Paydate1 = date("Y-m-d", strtotime($lead_data['Paydate1']));
		$Paydate2 = date("Y-m-d", strtotime($lead_data['Paydate2']));

		$query  =	'lp_test=0'.
				'&lp_exclude_contract_seen=1'.
				'&lp_no_distribute=1'.

				//Verify Identity
				'&dob='.$dob.
                '&SSN='.$lead_data['Ssn'].
                '&drivers_license_number='.$lead_data['DriversLicenseNumber'].
                '&drivers_license_state='.$lead_data['DriversLicenseState'].

                //Employment Info
                '&employee_type='.$lead_data['EmployeeType'].
                '&employer_name='.$lead_data['EmployerName'].
                '&employment_months='.$lead_data['EmploymentTime'].
                '&phone_work='.$lead_data['WorkPhone'].
                '&employer_address='.$lead_data['EmployerAddress'].
                '&employer_zip='.$lead_data['EmployerZip'].
				'&employer_city='.$lead_data['EmployerCity'].
				'&employer_state='.$lead_data['EmployerState'].
				'&monthly_income='.$lead_data['MonthlyNetIncome'].
				'&pay_frequency='.$lead_data['PayFrequency'].
				'&pay_date_1='.$Paydate1.
				'&pay_date_2='.$Paydate2.

				//Bank Details
				'&bank_account_type='.$lead_data['BankAccountType'].
				'&bank_routing_number='.$lead_data['BankRoutingNumber'].
				'&bank_account_number='.$lead_data['BankAccountNumber'].
				'&bank_name='.$lead_data['BankName'].
				'&bank_months='.$lead_data['BankTime'];

		if(isset($lead_data['PrimaryPhone']))                 
        	$query.='&phone_home='.$lead_data['PrimaryPhone'];

        if(isset($lead_data['SecondaryPhone']))                 
        	$query.='&phone_cell='.$lead_data['SecondaryPhone'];

		if(isset($lead_data['DirectDeposit']))  {
        	
        	if($lead_data['DirectDeposit'] == 'true'){
        		$query.='&direct_deposit=1';
        	}else{
        		$query.='&direct_deposit=0';
        	}               
        }

        if(isset($lead_data['AgreeConsent']))  {
        	
        	if($lead_data['AgreeConsent'] == 'true'){
        		$query.='&agree_consent=1';
        	}else{
        		$query.='&agree_consent=0';
        	}               
        }

        if($lead_data['Phone_TCPA'] == "")  {
        	$query.='&agree_phone=0';
    	}else{
    		$phonetcpa = intval(preg_replace('/[^0-9]+/', '', $lead_data['Phone_TCPA']), 10);
    		$query.='&phone_tcpa='.$phonetcpa;
        	$query.='&agree_phone=1';
    	}

    	if(isset($lead_data['LoanAmount']))                    
        	$query.='&loan_amount='.$lead_data['LoanAmount'];

        if(isset($lead_data['LoanAmount1']))                
        	$query.='&loan_amount_first='.$lead_data['LoanAmount1'];
        
        if(isset($lead_data['LoanAmount2']))                 
        	$query.='&loan_amount_second='.$lead_data['LoanAmount2'];

        $query  .= '&lp_api_key=9186a80444607e3a119ef1c02f7d4c84'.
               	  '&api_secret=e914bdc70b6f813aba7227edda802afe'.
                  '&lp_lead_id='.$lead_id;

		$curl = curl_init();
		$url  = "https://cmportal.leadspediatrack.com/distribute.do";
		$query = str_replace(' ', '+', $query);
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $url."?".$query,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 180,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_POSTFIELDS => "{}",
		));

		if (curl_error($curl)) {
            $status_json = "cURL Error #:" . curl_error($curl);
        } else {              
            $status_json = curl_exec($curl);
        }
        curl_close($curl);
        //$status_json = json_encode($status_json);
        return $status_json;
	}

	public function processCrmLeadInactive(){
		//Check to make sure of Request Type
		
		if(!$this->request->is('post')){
			$this->errors[] = 'Request type is not a POST';
			$this->_responseError($msg="POST");
		}
		$lead_data = $this->request->data;

		if( isset($lead_data['TrackId'])){
             $track_id = $lead_data['TrackId'];
        }
		
		$params = array('conditions'=> array('Lead.track_id'=>$track_id));
		$track_count = $this->Lead->find('count', $params);

		if($lead_data['Step'] == 2 && !(isset($lead_data['lead_id']))) {

			$query  = 'lp_test=1'.
                '&ip_address='.$lead_data['IPAddress'].
                '&lp_campaign_id='.$lead_data['Campaign_id'].
                '&lp_campaign_key='.$lead_data['Campaign_key'].
                '&lp_no_distribute=1'.

                //First Page
                '&loan_purpose='.$lead_data['LoanPurpose'].
                '&credit_rating='.$lead_data['CreditRating'].
				'&zip_code='.$lead_data['Zip'].
				'&military='.$lead_data['Military'].
				'&agree=1'.
                
				//Personal Information
				'&loan_amount='.$lead_data['LoanAmount'].
                '&first_name='.$lead_data['FirstName'].
                '&last_name='.$lead_data['LastName'].
                '&email_address='.$lead_data['Email'].
                '&address='.$lead_data['Address1'].      
                '&state='.$lead_data['State'].
                '&city='.$lead_data['City'].
                '&residence_type='.$lead_data['ResidenceType'].
                '&rent_mortgage='.$lead_data['RentMortgage'].
                '&residence_month='.$lead_data['ResidentSinceDate'];
                
                if(isset($lead_data['RequestId']))
                	$query.='&lp_request_id='.$lead_data['RequestId'];

                if(isset($lead_data['SubId1']))
                    $query.='&lp_s1='.$lead_data['SubId1'];
                
                if(isset($lead_data['SubId2']))
                    $query.='&lp_s2='.$lead_data['SubId2'];
                
                if(isset($lead_data['SubId3']))
                    $query.='&lp_s3='.$lead_data['SubId3'];
                
                if(isset($lead_data['SubId4']))
                    $query.='&lp_s4='.$lead_data['SubId4'];

                if(isset($lead_data['SubId5']))
                    $query.='&lp_s5='.$lead_data['SubId5'];

                if(isset($lead_data['Address2']))
                    $query.='&address2='.$lead_data['Address2'];

                if(isset($lead_data['LoanAmount1']))
                	$query.='&loan_amount_first='.$lead_data['LoanAmount1'];
                else
                	$query.='&loan_amount_first='.$lead_data['LoanAmount'];
                
                if(isset($lead_data['Url']))
                	$query.='&URL='.$lead_data['Url'];

                $curl = curl_init();

	        $url = "https://cmportal.leadspediatrack.com/post.do";
	        $query = str_replace(' ', '+', $query);
	        curl_setopt_array($curl, array(
	          CURLOPT_URL => $url."?".$query,
	          CURLOPT_RETURNTRANSFER => true,
	          CURLOPT_ENCODING => "",
	          CURLOPT_MAXREDIRS => 10,
	          CURLOPT_TIMEOUT => 30,
	          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	          CURLOPT_CUSTOMREQUEST => "GET",
	          CURLOPT_POSTFIELDS => "{}",
	        ));

	        if (curl_error($curl)) {
	            $status_json = "cURL Error #:" . curl_error($curl);
	        } else {              
	            $status_json = curl_exec($curl);
	        }
	        curl_close($curl);
	        $xml = simplexml_load_string($status_json);
	        $xml->post_request = "https://cmportal.leadspediatrack.com/post.do?".$query;
	        $result = json_encode($xml);
	        //return $status_json;

        	//$result = $this->step2_processLeadInactive($lead_data);
			$response = json_decode($result);

			if($response->result == 'failed'){
				$return['status'] ='failed';
				$return['redirect'] = '';
			}

			$lead_id = $response->lead_id;
			$lead_data["lead_id"] = $lead_id;
			$return['lead_id'] = $lead_id;
		}

		if(isset($lead_data['Ssn'])){

			$ssn = $this->_hash_ssn_process_crm_lead($this->request->data['Ssn']);
            $lead_data['SsnHash']=$ssn;            

		}

		if($lead_data['Step'] == 6 ){

			$result = $this->update_processLeadInactive($lead_data);
			$response = json_decode($result);
			$info = $response->info;
			$return['status'] = $response->message;
            $return['redirect'] = $info->redirectURL;
            $return['total_sold'] = $info->total_delivery;

            if(isset($lead_data['RequestId'])){

            	if($info->total_delivery > 0) {

	            	/*$urlpixel = "http://nkoeg.com/pixel.do?t=pb&request_id=".$lead_data['RequestId'];
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $urlpixel);
					curl_exec($ch);
					curl_close($ch);*/

					$pixel_url = "https://nkoeg.com/pixel.do?t=pb&request_id=".$lead_data['RequestId'];	
					$rsp = $this->Api->firePixel($pixel_url);
					$lead_data['pixel_response'] = $rsp;
	            }
        	}
      
		}
		$lead_info = json_encode($lead_data);

		if($track_count == 0){ //track not found so add			
			$this->Lead->create(array('track_id'=>$track_id, 'lead_info'=>$lead_info	,'created_at'=> date("Y-m-d G:i:s", time()),'updated_at'=>date("	Y-m-d G:i:s", time())));
			$this->Lead->save();
			$lead_updated_data = false;	

		}else{

			$lead = $this->Lead->find('first', $params);
			
			$response_old = $lead['Lead']['response'];
			$response_new = json_encode($response);
			$response_db = $response_old.$response_new;

			$this->Lead->id = $lead['Lead']['id'];
			$this->Lead->set('track_id', $track_id);
			$this->Lead->set('lead_info', $lead_info);
			if(!empty($response)){
				$this->Lead->set('response', $response_db);
			}
			$this->Lead->set('updated_at', date("Y-m-d G:i:s", time()));
			$this->Lead->save();
			$lead_updated_data = true;
			//$this->Lead->clear();			
		}

		if($lead_updated_data == true && $lead_data['Step'] == 6 && $lead_data['Check'] == true)
		{
			$lead = $this->Lead->find('first', $params);

            $datetime1 = strtotime($lead['Lead']['created_at']);
          
			$datetime2 = strtotime($lead['Lead']['updated_at']);
		
			$interval  = abs($datetime2 - $datetime1);
	
			$minutes   = round($interval / 60);
		
            //$totalDuration = $finishTime->diffInMinutes($startTime);
            if($minutes > 3){
               
                $return['status'] = 'error';
                $return['redirect'] = '';
             
            }

		}
		echo $this->Api->jsonresponse($return);
		exit;
	}

	public function step2_processLeadInactive($lead_data){
		$query  = 'lp_test=1'.
                '&ip_address='.$lead_data['IPAddress'].
                '&lp_campaign_id='.$lead_data['Campaign_id'].
                '&lp_campaign_key='.$lead_data['Campaign_key'].
                '&lp_no_distribute=1'.

                //First Page
                '&loan_purpose='.$lead_data['LoanPurpose'].
                '&credit_rating='.$lead_data['CreditRating'].
				'&zip_code='.$lead_data['Zip'].
				'&military='.$lead_data['Military'].
				'&agree=1'.
                
				//Personal Information
				'&loan_amount='.$lead_data['LoanAmount'].
                '&first_name='.$lead_data['FirstName'].
                '&last_name='.$lead_data['LastName'].
                '&email_address='.$lead_data['Email'].
                '&address='.$lead_data['Address1'].      
                '&state='.$lead_data['State'].
                '&city='.$lead_data['City'].
                '&residence_type='.$lead_data['ResidenceType'].
                '&rent_mortgage='.$lead_data['RentMortgage'].
                '&residence_month='.$lead_data['ResidentSinceDate'];
                
                if(isset($lead_data['RequestId']))
                	$query.='&lp_request_id='.$lead_data['RequestId'];

                if(isset($lead_data['SubId1']))
                    $query.='&lp_s1='.$lead_data['SubId1'];
                
                if(isset($lead_data['SubId2']))
                    $query.='&lp_s2='.$lead_data['SubId2'];
                
                if(isset($lead_data['SubId3']))
                    $query.='&lp_s3='.$lead_data['SubId3'];
                
                if(isset($lead_data['SubId4']))
                    $query.='&lp_s4='.$lead_data['SubId4'];

                if(isset($lead_data['SubId5']))
                    $query.='&lp_s5='.$lead_data['SubId5'];

                if(isset($lead_data['Address2']))
                    $query.='&address2='.$lead_data['Address2'];

                if(isset($lead_data['LoanAmount1']))
                	$query.='&loan_amount_first='.$lead_data['LoanAmount1'];
                else
                	$query.='&loan_amount_first='.$lead_data['LoanAmount'];
                
                if(isset($lead_data['Url']))
                	$query.='&URL='.$lead_data['Url'];

                $curl = curl_init();

        $url = "https://cmportal.leadspediatrack.com/post.do";
        $query = str_replace(' ', '+', $query);
        curl_setopt_array($curl, array(
          CURLOPT_URL => $url."?".$query,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_POSTFIELDS => "{}",
        ));

        if (curl_error($curl)) {
            $status_json = "cURL Error #:" . curl_error($curl);
        } else {              
            $status_json = curl_exec($curl);
        }
        curl_close($curl);
        $xml = simplexml_load_string($status_json);
        $xml->post_request = "https://cmportal.leadspediatrack.com/post.do?".$query;
        $status_json= json_encode($xml);
        return $status_json;
	}

	public function update_processLeadInactive($lead_data){

		$lead_id =$lead_data['lead_id'] ;

		//Formatting
		$dob = date("Y-m-d", strtotime($lead_data['DateOfBirth']));
		$Paydate1 = date("Y-m-d", strtotime($lead_data['Paydate1']));
		$Paydate2 = date("Y-m-d", strtotime($lead_data['Paydate2']));

		$query  =	'lp_test=1'.
				'&lp_exclude_contract_seen=1'.
				'&lp_no_distribute=1'.

				//Verify Identity
				'&dob='.$dob.
                '&SSN='.$lead_data['Ssn'].
                '&drivers_license_number='.$lead_data['DriversLicenseNumber'].
                '&drivers_license_state='.$lead_data['DriversLicenseState'].

                //Employment Info
                '&employee_type='.$lead_data['EmployeeType'].
                '&employer_name='.$lead_data['EmployerName'].
                '&employment_months='.$lead_data['EmploymentTime'].
                '&phone_work='.$lead_data['WorkPhone'].
                '&employer_address='.$lead_data['EmployerAddress'].
                '&employer_zip='.$lead_data['EmployerZip'].
				'&employer_city='.$lead_data['EmployerCity'].
				'&employer_state='.$lead_data['EmployerState'].
				'&monthly_income='.$lead_data['MonthlyNetIncome'].
				'&pay_frequency='.$lead_data['PayFrequency'].
				'&pay_date_1='.$Paydate1.
				'&pay_date_2='.$Paydate2.

				//Bank Details
				'&bank_account_type='.$lead_data['BankAccountType'].
				'&bank_routing_number='.$lead_data['BankRoutingNumber'].
				'&bank_account_number='.$lead_data['BankAccountNumber'].
				'&bank_name='.$lead_data['BankName'].
				'&bank_months='.$lead_data['BankTime'];

		if(isset($lead_data['PrimaryPhone']))                 
        	$query.='&phone_home='.$lead_data['PrimaryPhone'];

        if(isset($lead_data['SecondaryPhone']))                 
        	$query.='&phone_cell='.$lead_data['SecondaryPhone'];

		if(isset($lead_data['DirectDeposit']))  {
        	
        	if($lead_data['DirectDeposit'] == 'true'){
        		$query.='&direct_deposit=1';
        	}else{
        		$query.='&direct_deposit=0';
        	}               
        }

        if(isset($lead_data['AgreeConsent']))  {
        	
        	if($lead_data['AgreeConsent'] == 'true'){
        		$query.='&agree_consent=1';
        	}else{
        		$query.='&agree_consent=0';
        	}               
        }

        if($lead_data['Phone_TCPA'] == "")  {
        	$query.='&agree_phone=0';
    	}else{
    		$phonetcpa = intval(preg_replace('/[^0-9]+/', '', $lead_data['Phone_TCPA']), 10);
    		$query.='&phone_tcpa='.$phonetcpa;
        	$query.='&agree_phone=1';
    	}

    	if(isset($lead_data['LoanAmount']))                    
        	$query.='&loan_amount='.$lead_data['LoanAmount'];

        if(isset($lead_data['LoanAmount1']))                
        	$query.='&loan_amount_first='.$lead_data['LoanAmount1'];
        
        if(isset($lead_data['LoanAmount2']))                 
        	$query.='&loan_amount_second='.$lead_data['LoanAmount2'];

        $query  .= '&lp_api_key=9186a80444607e3a119ef1c02f7d4c84'.
               	  '&api_secret=e914bdc70b6f813aba7227edda802afe'.
                  '&lp_lead_id='.$lead_id;

		$curl = curl_init();
		$url  = "https://cmportal.leadspediatrack.com/distribute.do";
		$query = str_replace(' ', '+', $query);
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $url."?".$query,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_POSTFIELDS => "{}",
		));

		if (curl_error($curl)) {
            $status_json = "cURL Error #:" . curl_error($curl);
        } else {              
            $status_json = curl_exec($curl);
        }
        curl_close($curl);
        //$status_json = json_encode($status_json);

        $status_json_new = json_decode($status_json);

        $status_json_new->distribute_request = "https://cmportal.leadspediatrack.com/distribute.do?".$query;

        $status_json_return = json_encode($status_json_new);

        return $status_json_return;
	}

	public function phpinfo(){
		phpinfo();
	}

	public function processCrmLead(){
		//Check to make sure of Request Type
		
		if(!$this->request->is('post')){
			$this->errors[] = 'Request type is not a POST';
			$this->_responseError($msg="POST");
		}
		$lead_data = $this->request->data;

		if( isset($lead_data['TrackId'])){
             $track_id = $lead_data['TrackId'];
        }

        if( isset($lead_data['lastInsertId'])){
             $lastInsertId = $lead_data['lastInsertId'];
        }
		
		$params = array('conditions'=> array('Lead.id'=>$lastInsertId));

		$track_count = $this->Lead->find('count', $params);
		$lead = $this->Lead->find('first', $params);

		//if($lead_data['Step'] == 2 && !isset($lead_data['LeadID'])){
		if($lead_data['Step'] == 2 && !(isset($lead_data['lead_id']))) {
			
           if($lead_data['FirstName'] != "") {

				$result = $this->step2_processLead($lead_data);
				$response = json_decode($result);
				if($response->result == 'failed'){
					$return['status'] ='failed';
					$return['redirect'] = '';
				}
				$lead_id = $response->lead_id;
				$lead_data["lead_id"] =$lead_id;
				$return['lead_id'] = $lead_id;
			} else {
				$return['status'] ='failed';
				$return['redirect'] = '';
			}
        }

		if(isset($lead_data['Ssn'])){

			$ssn = $this->_hash_ssn_process_crm_lead($this->request->data['Ssn']);
            $lead_data['SsnHash']=$ssn;
           // unset($lead_data['Ssn']);

		}

		if($lead_data['Step'] == 6 ){
			if($lead_data['FirstName'] != "") {

				$distribute_calls = substr_count($lead['Lead']['response'], 'distribute_request');

				if($distribute_calls < 2){
					$result = $this->update_processLead($lead_data);
					$response = json_decode($result);
					$info = $response->info;
					$return['status'] = $response->message;
		            $return['redirect'] = $info->redirectURL;
		            $return['total_sold'] = $info->total_delivery;

		            if(isset($lead_data['RequestId'])){

		            	if($info->total_delivery > 0){
			         
			            	$urlpixel = "https://nkoeg.com/pixel.do?t=pb&request_id=".$lead_data['RequestId'];
							$ch = curl_init();
							curl_setopt($ch, CURLOPT_URL, $urlpixel);
							$rsp = curl_exec($ch);
							curl_close($ch);
							$lead_data['pixel_response'] = $rsp;

							/*$pixel_url = "https://nkoeg.com/pixel.do?t=pb&request_id=".$lead_data['RequestId'];	
							$rsp = $this->Api->firePixel($pixel_url);
							$lead_data['pixel_response'] = $rsp;*/
			            }
			        }
		    	} else {
	    			$return['status'] = "Success";
	            	$return['redirect'] = "";
	            	$return['total_sold'] = 0;
	        	}
	    	}else{
	    		$return['status'] = "Success";
	            $return['redirect'] = "";
	            $return['total_sold'] = 0;
	        }
                  
		}
		$lead_info = json_encode($lead_data);

		if($track_count == 0){ //track not found so add			
			$this->Lead->create(array('track_id'=>$track_id, 'lead_info'=>$lead_info	,'created_at'=> date("Y-m-d G:i:s", time()),'updated_at'=>date("	Y-m-d G:i:s", time())));
			$this->Lead->save();
			$lastInsertId = $this->Lead->getLastInsertID();
			$return['lastInsertId'] = $lastInsertId;
			$lead_updated_data = false;	

		}else{

			/*$lead = $this->Lead->find('first', $params);
			
			$this->Lead->id = $lead['Lead']['id'];
			$this->Lead->set('track_id', $track_id);
			$this->Lead->set('lead_info', $lead_info);
			if(!empty($response)){
				$this->Lead->set('response', json_encode($response));
			}
			$this->Lead->set('updated_at', date("Y-m-d G:i:s", time()));
			$this->Lead->save();
			$lead_updated_data = true;
			//$this->Lead->clear();	*/
			
			$response_old = $lead['Lead']['response'];
			if(!empty($response)){
				$response_new = json_encode($response);
				$response_db = $response_old.$response_new;
			}else{
				$response_db = $response_old;
			}

			//$response_file = $response_db."-".date("Y/m/d h:i:s")."-".$lead_data['LeadID'];
			$response_file = "\n[".date("Y/m/d h:i:s")."] ".$lead_data['LeadID']." - ". $response_db;
			//$file = fopen($_SERVER['DOCUMENT_ROOT'] . "/app/tmp/logs/lead.log","a");
			$file = fopen($_SERVER['DOCUMENT_ROOT'] . "/app/tmp/logs/lead-".date('Y-m-d').".log","a");
			fwrite($file,$response_file);
			fclose($file);

			/*$response_file = $response_db.date("Y/m/d h:i:s");
			$file = new File('/app/tmp/logs/lead.log', true);
			$file->write($response_file);*/

			//$this->log("Test Shraddha");

			$this->Lead->id = $lead['Lead']['id'];
			$this->Lead->set('track_id', $track_id);
			$this->Lead->set('lead_info', $lead_info);
			if(!empty($response)){
				$this->Lead->set('response', $response_db);
			}
			$this->Lead->set('updated_at', date("Y-m-d G:i:s", time()));
			$this->Lead->save();
			$lead_updated_data = true;
			//$this->Lead->clear();					
		}

		/*if($lead_updated_data == true && $lead_data['Step'] == 6 && $lead_data['Check'] == true)
		{
			$lead = $this->Lead->find('first', $params);

            $datetime1 = strtotime($lead['Lead']['created_at']);
          
			$datetime2 = strtotime($lead['Lead']['updated_at']);
		
			$interval  = abs($datetime2 - $datetime1);
	
			$minutes   = round($interval / 60);
		
            //$totalDuration = $finishTime->diffInMinutes($startTime);
            if($minutes > 3){
               
                $return['status'] = 'error';
                $return['redirect'] = '';
             
            }

		}*/

		/*if($return['total_sold'] > 0){
			header("Location: ".$return['redirect']);
			die();
		}*/

		echo $this->Api->jsonresponse($return);
		exit;
	}

	private function _hash_ssn_process_crm_lead($ssn){
		$last4 = substr($ssn, -4);
        $last4_md5 = md5($last4);
        return $last4_md5;  
	}

	public function step2_processLead($lead_data){
		$query  = 'lp_test=0'.
                '&ip_address='.$lead_data['IPAddress'].
                '&lp_campaign_id='.$lead_data['Campaign_id'].
                '&lp_campaign_key='.$lead_data['Campaign_key'].
                '&lp_no_distribute=1'.

                //First Page
                '&loan_purpose='.$lead_data['LoanPurpose'].
                '&credit_rating='.$lead_data['CreditRating'].
				'&zip_code='.$lead_data['Zip'].
				'&military='.$lead_data['Military'].
				'&user_data='.$lead_data['Browser'].
				'&agree=1'.
                
				//Personal Information
				'&loan_amount='.$lead_data['LoanAmount'].
                '&first_name='.$lead_data['FirstName'].
                '&last_name='.$lead_data['LastName'].
                '&email_address='.$lead_data['Email'].
                '&address='.$lead_data['Address1'].      
                '&state='.$lead_data['State'].
                '&city='.$lead_data['City'].
                '&residence_type='.$lead_data['ResidenceType'].
                '&rent_mortgage='.$lead_data['RentMortgage'].
                '&residence_month='.$lead_data['ResidentSinceDate'];
                
                if(isset($lead_data['RequestId']))
                	$query.='&lp_request_id='.$lead_data['RequestId'];

                if(isset($lead_data['SubId1']))
                    $query.='&lp_s1='.$lead_data['SubId1'];
                
                if(isset($lead_data['SubId2']))
                    $query.='&lp_s2='.$lead_data['SubId2'];
                
                if(isset($lead_data['SubId3']))
                    $query.='&lp_s3='.$lead_data['SubId3'];
                
                if(isset($lead_data['SubId4']))
                    $query.='&lp_s4='.$lead_data['SubId4'];

                if(isset($lead_data['SubId5']))
                    $query.='&lp_s5='.$lead_data['SubId5'];

                if(isset($lead_data['Address2']))
                    $query.='&address2='.$lead_data['Address2'];

                if(isset($lead_data['LoanAmount1']))
                	$query.='&loan_amount_first='.$lead_data['LoanAmount1'];
                else
                	$query.='&loan_amount_first='.$lead_data['LoanAmount'];
                
                if(isset($lead_data['Url']))
                	$query.='&URL='.$lead_data['Url'];

                $curl = curl_init();

        $url = "https://cmportal.leadspediatrack.com/post.do";
        $query = str_replace(' ', '+', $query);
        curl_setopt_array($curl, array(
          CURLOPT_URL => $url."?".$query,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 180,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_POSTFIELDS => "{}",
        ));

        if (curl_error($curl)) {
            $status_json = "cURL Error #:" . curl_error($curl);
        } else {              
            $status_json = curl_exec($curl);
        }
        curl_close($curl);
        $xml = simplexml_load_string($status_json);
        $xml->post_request = "https://cmportal.leadspediatrack.com/post.do?".$query;
        $status_json= json_encode($xml);
        return $status_json;
	}

	public function update_processLead($lead_data){

		if($lead_data['LeadID'] != "")
			$lead_id =$lead_data['LeadID'] ;
		else
			$lead_id =$lead_data['lead_id'] ;

		//Formatting
		$dob = date("Y-m-d", strtotime($lead_data['DateOfBirth']));
		$Paydate1 = date("Y-m-d", strtotime($lead_data['Paydate1']));
		$Paydate2 = date("Y-m-d", strtotime($lead_data['Paydate2']));

		$query  =	'lp_test=0'.
				'&lp_exclude_contract_seen=1'.
				'&lp_no_distribute=1'.

				//Verify Identity
				'&dob='.$dob.
                '&SSN='.$lead_data['Ssn'].
                '&drivers_license_number='.$lead_data['DriversLicenseNumber'].
                '&drivers_license_state='.$lead_data['DriversLicenseState'].

                //Employment Info
                '&employee_type='.$lead_data['EmployeeType'].
                '&employer_name='.$lead_data['EmployerName'].
                '&employment_months='.$lead_data['EmploymentTime'].
                '&phone_work='.$lead_data['WorkPhone'].
                '&employer_address='.$lead_data['EmployerAddress'].
                '&employer_zip='.$lead_data['EmployerZip'].
				'&employer_city='.$lead_data['EmployerCity'].
				'&employer_state='.$lead_data['EmployerState'].
				'&monthly_income='.$lead_data['MonthlyNetIncome'].
				'&pay_frequency='.$lead_data['PayFrequency'].
				'&pay_date_1='.$Paydate1.
				'&pay_date_2='.$Paydate2.

				//Bank Details
				'&bank_account_type='.$lead_data['BankAccountType'].
				'&bank_routing_number='.$lead_data['BankRoutingNumber'].
				'&bank_account_number='.$lead_data['BankAccountNumber'].
				'&bank_name='.$lead_data['BankName'].
				'&bank_months='.$lead_data['BankTime'];

		if(isset($lead_data['PrimaryPhone']))                 
        	$query.='&phone_home='.$lead_data['PrimaryPhone'];

        if(isset($lead_data['SecondaryPhone']))                 
        	$query.='&phone_cell='.$lead_data['SecondaryPhone'];

		if(isset($lead_data['DirectDeposit']))  {
        	
        	if($lead_data['DirectDeposit'] == 'true'){
        		$query.='&direct_deposit=1';
        	}else{
        		$query.='&direct_deposit=0';
        	}               
        }

        if(isset($lead_data['AgreeConsent']))  {
        	
        	if($lead_data['AgreeConsent'] == 'true'){
        		$query.='&agree_consent=1';
        	}else{
        		$query.='&agree_consent=0';
        	}               
        }

        if($lead_data['Phone_TCPA'] == "")  {
        	$query.='&agree_phone=0';
    	}else{
    		$phonetcpa = intval(preg_replace('/[^0-9]+/', '', $lead_data['Phone_TCPA']), 10);
    		$query.='&phone_tcpa='.$phonetcpa;
        	$query.='&agree_phone=1';
    	}

    	if(isset($lead_data['LoanAmount']))                    
        	$query.='&loan_amount='.$lead_data['LoanAmount'];

        if(isset($lead_data['LoanAmount1']))                
        	$query.='&loan_amount_first='.$lead_data['LoanAmount1'];
        
        if(isset($lead_data['LoanAmount2']))                 
        	$query.='&loan_amount_second='.$lead_data['LoanAmount2'];

        $query  .= '&lp_api_key=9186a80444607e3a119ef1c02f7d4c84'.
               	  '&api_secret=e914bdc70b6f813aba7227edda802afe'.
                  '&lp_lead_id='.$lead_id;

		$curl = curl_init();
		$url  = "https://cmportal.leadspediatrack.com/distribute.do";
		$query = str_replace(' ', '+', $query);
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $url."?".$query,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 180,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_POSTFIELDS => "{}",
		));

		if (curl_error($curl)) {
            $status_json = "cURL Error #:" . curl_error($curl);
        } else {              
            $status_json = curl_exec($curl);
        }
        curl_close($curl);
        //$status_json = json_encode($status_json);
        $status_json_new = json_decode($status_json);
        $status_json_new->actual_response = $status_json;
        $status_json_new->distribute_request = "https://cmportal.leadspediatrack.com/distribute.do?".$query;

        $status_json_return = json_encode($status_json_new);

        return $status_json_return;
	}	

	public function processExternalLead(){
			
		//Check to make sure of Request Type
		if(!$this->request->is('post')){
			$this->errors[] = 'Request type is not a POST';
			$this->_responseError($msg="POST");
		}
		
		if($this->debug)$this->Api->print_msg('Is A Post');
		$this->isExternalPost=true;
		
		
		//Get Post Data
		$post = $this->request->data;

		$appType = $post['AppType'];
		
		if($this->request->data['ForceSuccess'] == "true"){//Force success response
				$this->redirect = "http://www.testingforceaccept.com";
				$this->calculated_price = $this->Api->formatDecimal(2.50);
				$this->_responseSuccess();
		}
		
		//IP Fraud Check
		if($this->Ip->isBlacklisted($this->request->data['IPAddress'])){
			//lead found so create tracking
			$request['Track']['request_id'] = '0000';
			$request['Track']['offer_id'] = $this->request->data['OfferId'];
			$request['Track']['campaign_id'] = $this->request->data['CampaignId'];
			$request['Track']['affiliate_id'] = $this->request->data['Affiliate'];
		
			if($this->Track->save($request)) {
				$this->track_id = $this->Track->id;
			}else{
				$this->errors[] = 'Invalid Lead Data';
				$this->_responseError('System Error');
			}
				
			//add additional data to track lead
			$post['AppType'] = $appType;
			$post['Mobile'] = 'false';
			$post['CallType'] = "External";
			$post['sub_id'] = $this->request->data['SubId1'];
			$clean_lead_data = $this->_externalCleanTrackData($post);
			$this->_trackLead(json_encode($clean_lead_data)); //Store data
				
			//Mark as error in keystone
			$errors = array('ERRORS' => array(400=>'Fraudulent Lead - IP '.$this->request->data['Ip'].' is blacklisted.'));
			$track_json = json_encode($errors);
			$this->_trackLead($track_json);
				
			//show decline message
			$this->errors[] = 'Fraudulent Lead Data';
			$this->_response('Error','decline');
		}

		if($appType != 'esp'){
			//See if lead is in table within the last hour
						
			//$this->log($this->request->data['Affiliate']);
			$affiliate_id = $this->request->data['Affiliate'];
			$hash = md5($this->request->data['Email'].$this->request->data['Ssn']);
			$lookback = date('Y-m-d H:i:s', strtotime("-1 hour"));
			$params = array('conditions'=> array('VendorDuplicates.affiliate_id !='=>$affiliate_id,
												 'VendorDuplicates.hash'=>$hash,
												 'VendorDuplicates.created >'=>$lookback
												),
							'order'=>array('VendorDuplicates.created'=>'desc')
				
			);
			$dup_count = $this->VendorDuplicates->find('count', $params);
			
			if($dup_count == 0){ //lead not found so add
				$this->VendorDuplicates->create(array('affiliate_id'=>$affiliate_id, 'hash'=>$hash));
				$this->VendorDuplicates->save();
				
			}else{
				//lead found so create tracking
				$request['Track']['request_id'] = '0000';
				$request['Track']['offer_id'] = $this->request->data['OfferId'];
				$request['Track']['campaign_id'] = $this->request->data['CampaignId'];
				$request['Track']['affiliate_id'] = $affiliate_id;
			
				if($this->Track->save($request)) {
					$this->track_id = $this->Track->id;
				}else{
					$this->errors[] = 'Track Id in Deduplication';
					$this->_responseError('System Error');		
				}
				
				//add additional data to track lead
				$post['AppType'] = $appType;	
				$post['Mobile'] = 'false';
				$post['CallType'] = "External";
				$post['sub_id'] = $this->request->data['SubId1'];
				$clean_lead_data = $this->_externalCleanTrackData($post);
				$this->_trackLead(json_encode($clean_lead_data)); //Store data
				
				//Mark as error in keystone
				$errors = array('ERRORS' => array(390=>'Affiliate '.$affiliate_id.' Sent Duplicate Lead'));
				$track_json = json_encode($errors);
				$this->_trackLead($track_json);		
				
				//show decline message
				$this->errors[] = 'Duplicate Lead';
				//$this->leadByteVendorUnsold($lead_data);
				$this->_response('Error','decline');	
			}
		}
		
		#todo - store post and get track id
		switch ($appType) {
		    case "personalloan":
		        $this->_processPersonalLoan($post);
		        break;
			case "payday":
			case "prime_payday":
			case "internal_payday":
		        $this->_processPayday($post);
		        break;
			case "installment":
		        $this->_processInstallment($post);
		        break;
			case "ppc_personalloan":
		        $this->_processPersonalLoanPpc($post);
		        break;
			case "ppc_payday":
		        $this->_processPaydayPpc($post);
		        break;
			case "ppc_installment":
		        $this->_processInstallmentPpc($post);
		        break;
			case "lowtier":
		        $this->_processLowTier($post);
		        break;
			case "lowtierlong":
		        $this->_processLowTierLong($post);
		        break;
	        case "esp":
	        	$this->_processListManagement($post);
	        	break;
        	case "medical":
	        	$this->_processMedicalInsurance($post);
        	break;
	        		
		    default:
		        $this->errors[] = 'Invalid AppType';
				$this->_responseError($msg="AppType");
				break;
		}
		
		//Decline if a lead makes it here.
		$this->errors[] = 'Lead error';
		$this->_responseError($msg="LOGIC");
		exit;
		
	}
	
	public function heartBeat(){
	
		$post = $this->request->data;
		$rsp = array();
		if(!empty($post)){
			if($post['heartbeat'] == "true"){
				$rsp['status'] = 'true';	
			}else{
				$rsp['status'] = 'false';
			}
		}else{
			$rsp['status'] = 'false';
		}	
		
		echo json_encode($rsp);
		exit;
	}
	
	/*search user base on SSN, Zipcode and birth year*/
	public function processUserLookup(){
		
		if(!$this->request->is('post')){
			$this->errors[] = 'Request type is not a POST';
			$this->_responseError($msg="POST");
		}

		//Get Post Data
		$post = $this->request->data;
		
		$lookup_response = $this->_lookupUser($post);
		$appType = 'noc';

		if ($lookup_response['status'] == 'failed'){
				$response['status'] = 'error';
				$response['message'] = 'Data not found';

		}else {
			switch ($appType) {
				case 'noc':
					$data = $lookup_response['data'];
					$loan_amount = property_exists($data, 'LoanAmountPersonal') == true ? $data->LoanAmountPersonal : $data->LoanAmount;
					$post_url = 'http://lsred.xyz/?a=107'.
					'&c=232'.'&p=r'.'&s1=Test'.'&Prepop=true'.'&FirstName='. $data->FirstName.'&LastName=' . $data->LastName.					'&Address1=' . $data->Address1.'&Address2=' . $data->Address2.'&City=' . $data->City.'&State=' . $data->State.'&Zip=' . $data->Zip.'&Email=' . $data->Email .'&CreditRating=' . $data->CreditRating .'&MonthlyNetIncome='. $data->MonthlyNetIncome .'&ResidenceType='. $data->ResidenceType .'&PrimaryPhone='. $data->HomePhone .'&SecondaryPhone='. $data->CellPhone .'&PhoneType='. $data->PhoneType .'&WorkPhone='. $data->WorkPhone .'&EmployeeType='. $data->EmployeeType .'&LoanAmountPayday='. $loan_amount .'&ResidentSinceDate='. $data->ResidentSinceDate .'&DateOfBirth='. date('m/d/Y', strtotime($data->DateOfBirth)) .'&DriversLicenseState='. $data->DriversLicenseState .'&DriversLicenseNumber='. $data->DriversLicenseNumber .'&Military='. $data->Military .'&EmployerName='. $data->EmployerName .'&EmploymentTime='. $data->EmploymentTime .'&EmployerAddress='. str_replace(' ', '+', $data->EmployerAddress).'&EmployerCity='. $data->EmployerCity.'&EmployerState='. $data->EmployerState. '&EmployerZip='. $data->EmployerZip.'&PayFrequency='. $data->PayFrequency. '&DirectDeposit='. $data->DirectDeposit.'&BankAccountType=""&BankTime=""';

					// /* send data to call center*/ 
				
					$result = $this->getShortURL($post_url);
					$url = "https://nocsolutions.apizing.com/lead/?";
					$query ='address= '. str_replace(' ', '+', $data->Address1 . '+' .$data->Address2) .'&city='. $data->City .'&country=US'.'&dob=' . $data->DateOfBirth . '&email=' . $data->Email.'&first_name='. $data->FirstName .'&gender=""'. '&home_phone='. $data->HomePhone.'&ip_address='. $data->IPAddress.'&last_name='. $data->LastName.'&list_id=cash34af'. '&mobile_phone='. $data->CellPhone.'&optin_date='. date('d/m/yyyy').'&postal='. $data->Zip .'&source=quickcashnow.com'.'&state='. $data->State .'&sub_id='.$data->sub_id.'&work_phone='. $data->WorkPhone. '&user_defined_1=something'. '&user_defined_2=something'.'user_defined_3='. $result;

						$HttpSocket = new HttpSocket(array('ssl_verify_peer' => false, 'ssl_verify_host' => false));
						$post_response = $HttpSocket->get($url,$query);
						$response = explode('&',$post_response->body);
					break;
				default:
					# code...
					break;
			}
		}
	    echo $this->Api->jsonresponse($response);
	    exit;
	}

	function _lookupUser($post){
		$response = array();
		$zip 			= $post['Zip'];
		$date_of_year 	= $post['DateOfYear'];
		$ssn 	 		= $post['Ssn'];
	    	/*search user inside track_lead*/

	    	$lead_lookup = $this->LeadTrack->find('first', array(
				'conditions' => array(
				'json_vars like ?' => '%"SsnHash":"%'.$ssn.'%"%',
				)
			));
			if ($lead_lookup['LeadTrack']['track_id'] != ""){
				
				$this->Api->jsonresponse(array('status'=>'success', 'message'=> 'Data found'));

	    		$lead_track_id = $lead_lookup['LeadTrack']['track_id'];
	    		$lead_lookup_data = $this->LeadTrack->find('all', array(
					'conditions' => array(
						'track_id' 	 => $lead_track_id,
						'AND' 	 => array(
							'json_vars LIKE' => '%"Zip":"%'.$zip.'%"%',
							'json_vars LIKE' => '%"DateOfBirth":"%'.$date_of_year.'%"%'
						)
					),
					'fields' => array('json_vars', 'track_id')
				));
	    		if ($lead_lookup_data){
					$lead_data  = json_decode($lead_lookup_data[0]['LeadTrack']['json_vars']);
					$response['status']  = 'success';
					$response['data'] 	 = $lead_data;
    			}
   			} else {
				$this->Api->jsonresponse(array('status'=>'failed', 'message'=> 'Data not found'));
    			$response['status']  = 'failed';
    		}
   		return $response;	
		exit;
	}
	

	/**
	 * Obtains the shorted URL from the given one. 
	 * It uses tinyURL service to do so.
	 * 
	 * @access public
	 * @param String $url
	 * @return String shorted url
	 */

	public function getShortURL($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://tinyurl.com/api-create.php?url=".urlencode($url));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		$short_url = curl_exec($ch);
		curl_close($ch);
		if(empty($short_url)) return $url; else return $short_url;
	}
}