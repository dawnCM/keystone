<?php
/**
 * ESP Model
 *
 * $useDbConfig ties this model to using mongoDb.
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
App::uses('HttpSocket', 'Network/Http');
class Esp extends AppModel {	
	public $name = 'Esp';	
	public $useTable = 'esp';
	
	
	public function sendToEsp($id=0, $type="main"){
		App::import('Model','Track');
		$track = new Track();
		
		//Check data and set non existant system fields to blank
		$data = $this->checkData($this->data['Esp']);
		
		$conditions = array();
		if($id > 0){
			$conditions['Esp.id'] = $id;
		}
		
		//Subscriber add, only pull active Esps
		if($type == "main"){
			$conditions['Esp.status_id']=1;
		}
		
		$pull_configs = $this->find('all', array('conditions'=>$conditions));
		
		$status_array = array();
		
		if(count($pull_configs) == 0) return false;
		
		$results_array = array();
		foreach($pull_configs as $k=>$v){
			$config = array();
			$id = $v['Esp']['id'];
			$name = $v['Esp']['name'];
			$status_array[$name] = array();
			$config = json_decode($v['Esp']['json'], true);
			
			//Add Api Suppression test
			if($type == "api_test" || $type == "api_main"){
				$temp_holder = $config['Api'];
				$config = array();
				$config = $temp_holder;
				$config['FormattedDateFields'] = array();
				
			}

			//Pull Api BlackList test
			if($type == "blacklist_test" || $type == "blacklist_main"){
				$temp_holder = $config['BlackList'];
				$config = array();
				$config = $temp_holder;
				$config['FormattedDateFields'] = array();
				$config['PostFields'] = array();
				$config['MappingFields'] = array();
				
			}
			
			$esp_access = false;
			
			if(count(@$config['Filters']) == 0 || !isset($config['Filters'])){
				$esp_access = true;
			}else{
				
				$esp_access = $this->checkFilters($config['Filters'], $data);
			}
		
			if(!$esp_access){
				$results_array['error'][$name][] = 'Filters';
				continue;
			}
			
			//Check to see if there is a list mapping for offer id
			if($type == "api_test" || $type == "api_main" || $type == "main_test" || $type == "main"){
				$offer_id = $data['OfferId'];
				$mapping_match = false;
			
				foreach($config['MappingFields'] as $index=>$arr){
					if($arr[0] == $offer_id){
						$mapping_match = true;
						break;
					}	
				}
				
				if(!$mapping_match){
					$results_array['error'][$name][] = 'Offer Mapping';
					continue;	
				}
			}
			
			$accept_string = $config['AcceptString'];
			$accept_code = $config['AcceptCode'];
			
			
			$request_config = array("RequestType"=> 			$config['RequestType'],
									"RequestUrl"=> 				$config['RequestUrl'],
									"Headers" => 				$config['Headers'],
									"BasicAuth" =>				$config['BasicAuth'],
									"CustomFields" =>			$config['CustomFields'],
									"FormattedDateFields" => 	$config['FormattedDateFields'],
									"Template" =>				$config['Template'],
									"PostFields" =>				$config['PostFields'],
									"MappingFields" =>			$config['MappingFields']
									
			);
			
			if(empty($request_config['RequestType']) || empty($request_config['RequestUrl'])){
				$results_array['error'][$name][] = 'Missing Post';	
				continue;
			}
			
			
			$response_obj = $this->sendRequest($request_config, $data, $type);
			
	
			//escape forward slash for regex match
			$accept_string = str_replace(array('/'), array('\\/'), $accept_string);
			
			//Take out \n \r \t - escape back slash
			$body_rsp = str_replace(array('\\r', '\\n', '\\t', '\\'), array('', '', '','\\'), (STRING)$response_obj->body);
			$rsp_code = $response_obj->code;
	
			$success = false;
			
			if( ($accept_code != "") && ($accept_string != "") ){ // both present
				if((preg_match("/$accept_string/", $body_rsp)) && ($accept_code == $rsp_code)){
					$success = true;
				}	
			}else if(($accept_code != "") && ($accept_string == "")){ //accept code present
				if($accept_code == $rsp_code){
					$success = true;
				}
			}else if(($accept_code == "") && ($accept_string != "")){ //accept string present
				if(preg_match("/$accept_string/", $body_rsp) ){
					$success = true;
				}	
			}
			
			$status_array = array();
			if($success){
				$status_array['success'] = "true";
				$status_array['request_sent'] = "true";
				$status_array['esp'] = $name;
				$status_array['response'] = $body_rsp;
			
				
			}else{
				$status_array['success'] = "false";
				$status_array['request_sent'] = "true";
				$status_array['esp'] = $name;
				$status_array['response'] = $body_rsp;
				//$status_array['code'] = $rsp_code;
				//$status_array['request'] = $response_obj;
			}
			
			$results_array[] = $status_array;
		}
		
		if($type == "main"){
			/*$msg = array();
			$msg['LIST_MANAGEMENT'][$list_id] = date('Y-m-d');
			$track_json = json_encode($msg);
			$track->writeLead($track_id, $track_json);
			
			
			$msg = array();
			$msg = array('ERRORS' => array(601=>'Leadbyte Post Failure'));
			$track_json = json_encode($msg);
			$track->writeLead($track_id, $track_json);*/
		}
		
		return $results_array;

		
	}



	
	
	
	
	private function checkFilters($config, $data){
		$match = false;
		foreach($config as $k=>$v){
			$type = $v[0];
			$op = $v[1];
			$filter_value = $v[2];
			
			switch ($type) {
				case 'email':
					$email_seg = explode('@', strtolower($data['Email']));
					$response = $this->filterOperation($op, $email_seg[1], $filter_value);
					if($response)$match = true;
					
					break;
				
				case 'campaign':
					$response = $this->filterOperation($op, $data['OfferId'], $filter_value);
					if($response)$match = true;
					
					break;
					
				case 'hygiene':
					$response = $this->filterOperation($op, $data['Hygiene'], $filter_value);
					if($response)$match = true;
					
					break;
					
				default:
					
					break;
			}
			
			
		}
		
		return $match;
	}

	
	//Populate non existant system fields with blank
	private function checkData($data){
		
		App::import('Model','ListManagementValidate');
		$lmv = new ListManagementValidate();
		$system_fields = $lmv->systemFields();
		$user_data = array();
		
		foreach($data as $k=>$v){
		 $user_data[] = $k;	
		}
		
		foreach($system_fields as $field){
			if(!in_array($field, $user_data)){
				$data[$field] = "";
			}
		}
		
		return $data;
	}

	private function filterOperation($op, $data_point, $filter_value){
		$success = false;			
		switch ($op) {
			case '1': //equals
				
				if($filter_value == $data_point){
					$success = true;
				}else{
					$sucess = false;
				}
				break;
			
			case '2': //Greater Than
			
				if( !is_numeric($filter_value) || !is_numeric($data_point) ){
					$success = false;	
				}else{
					if($data_point > $filter_value){
						$success = true;
					}else{
						$success =  false;
					}
				}
				
				break;
				
			case '3': //Less Than
			
				if( !is_numeric($filter_value) || !is_numeric($data_point) ){
					$success = false;	
				}else{
					if($data_point < $filter_value){
						$success = true;
					}else{
						$success =  false;
					}
				}
				
				break;	
			
			case '4': //Greater Than Or Equal To
			
				if( !is_numeric($filter_value) || !is_numeric($data_point) ){
					$success = false;	
				}else{
					if($data_point >= $filter_value){
						$success = true;
					}else{
						$success =  false;
					}
				}
				
				break;
			
			case '5': //Less Than Or Equal To
			
				if( !is_numeric($filter_value) || !is_numeric($data_point) ){
					$success = false;	
				}else{
					if($data_point <= $filter_value){
						$success = true;
					}else{
						$success =  false;
					}
				}
				
				break;
				
			case '6': //Not Equal To
				
				if($filter_value != $data_point){
					$success = true;
				}else{
					$sucess = false;
				}
				break;
			
			case '7': //In List - comma separated
				
				$list = explode(",", $filter_value);
				
				if(empty($list)){
					$success = false;
				}else{
					foreach($list as $k){
						
						if(!empty($k)){
							if(strtolower($k) == strtolower($data_point)){
								$success = true;
								
							}
						}
					}
				}
				
				break;
				
			case '8': //Not In List - comma separated
				
				$list = explode(",", $filter_value);
				if(empty($list)){
					$success = false;
				}else{
					$flag = false;
					foreach($list as $k){
						if(!empty($k)){
							if(strtolower($k) == strtolower($data_point)){
								$flag = true;
								
							}
						}
					}
					
					if($flag){
						$success = false;
					}else{
						$success = true;
					}
				}
				
				break;
				
			case '9': //Contains
			
				$filter_value = strtolower($filter_value);
				if(preg_match("/$filter_value/", strtolower($data_point))){
					$success = true;
				}else{
					$sucess = false;
				}
				break;
			
			case '10': //Does Not Contain
			
				$filter_value = strtolower($filter_value);
				if(preg_match("/$filter_value/", strtolower($data_point))){
					$success = false;
				}else{
					$sucess = true;
				}
				break;
			
			default:
				$success = false;
				break;
		}		
		
		return $success;
	}

	
	private function buildTemplateArrays($data, $custom_fields, $request_config){
		
		$return = array(	'system_search'=>array(),
							'system_replace'=>array(),
							'custom_search'=>array(),
							'custom_replace'=>array()
		);
		
		
		foreach($data as $k=>$v){
			$return['system_search'][] = "[".$k."]";
			$return['system_replace'][] = $v;
		}
		
		
		
		foreach($custom_fields as $s=>$t){
			
			if($t == 'Token::OFFER-MAPPING'){
				//add on mapping for list id
				$return['custom_search'][] = "{".$s."}";
				$return['custom_replace'][] = $this->getMappingListId($request_config['MappingFields'], $data['OfferId']);
			}else{
			
				$return['custom_search'][] = "{".$s."}";
				$return['custom_replace'][] = $t;
			}
		}

		
		return $return;
	}
	
	private function getMappingListId($map, $offer_id){
		
		foreach($map as $index=>$arr){
			
			if($arr[0] == $offer_id){
			
				return $arr[2];
			}
		}
	}

	private function getUrl($url,$custom, $map, $offer_id){
		if(empty($map)){
			return $url;
		}else if(empty($custom)){
			return $url;
		}else{
			foreach($custom as $index=>$arr){
				$field = "{".$arr[0]."}";
				
				$url = str_replace($field, $this->getMappingListId($map, $offer_id), $url);
				
			}
			
			return $url;
		}
	}


	private function sendRequest($request_config, $data, $type="main"){
		
		$socket = new HttpSocket(array('ssl_verify_peer'=>false, 'ssl_verify_host'=>false));
		$request_properties = array();
		$request_url = $this->getUrl($request_config['RequestUrl'],$request_config['CustomFields'],$request_config['MappingFields'], $data['OfferId']);
		$request_type = $request_config['RequestType'];
		
		if(count($request_config['Headers']) > 0){
			foreach($request_config['Headers'] as $k=>$v){
				$request_properties['header'][$v[0]] = $v[1]; 
			}
		}
		
		
		if(count($request_config['BasicAuth']) > 0){
			$request_properties['auth'] = array('method'=>'Basic', 'user'=>$request_config['BasicAuth'][0][0], 'pass'=>$request_config['BasicAuth'][0][1]);
		}
		
		$custom_fields = array();
		if(count($request_config['CustomFields']) > 0){
			
			foreach($request_config['CustomFields'] as $k=>$v){
				$custom_fields[$v[0]] = $this->checkToken($v[1]);	
			}
		}
		
		
		if(count($request_config['FormattedDateFields']) > 0){
			
			foreach($request_config['FormattedDateFields'] as $k=>$v){
				$field = $v[0];
				$format = $v[1];
				$date = explode("-", $data[$field]);
				$date_format = $date[1]." ".date("F",$date[0])." ".$date[2];	
				
				$stamp = strtotime($date_format);
				$data[$field] = date($format, $stamp);
			}
			
		}
		
		$request_data = array();
		if(count($request_config['PostFields']) > 0){ //No Template
			
			foreach($request_config['PostFields'] as $f=>$v){
				$field = $v[0];
				$post_field = $v[1];
				$type = $v[2];
				
				if(empty($post_field))continue;
				
				if($type == "System"){
					$request_data[$post_field] = $data[$field];
				}else if($type == "Custom" && $post_field == 'Token::OFFER-MAPPING'){
					$request_data[$field] = $this->getMappingListId($request_config['MappingFields'], $data['OfferId']);
				}else{
					//reverse values for user created custom fields
					$request_data[$field] = $post_field;	
				}
			}
			
		}else if( ($type == 'blacklist_main' || $type == 'blacklist_test') && $request_config['Template'] == "" ){ //Api BlackList Pull.  
			if(count($custom_fields) > 0){
				foreach($custom_fields as $k=>$v){
					$request_data[$k] = $v;
				}
			}
			
		}else{ //Template
			$template_arrays = $this->buildTemplateArrays($data, $custom_fields, $request_config);
		
			$template_raw = (STRING)$request_config['Template'];
		
			//search for system fields and replace with value
			$template_sys = str_replace($template_arrays['system_search'], $template_arrays['system_replace'], $template_raw);
		
			//search for custom fields and replace with value
			$template = str_replace($template_arrays['custom_search'], $template_arrays['custom_replace'], $template_sys);
		
			$request_data = (STRING)$template;
			
		}
		
		//print_r(array($request_url, $request_data, $request_properties));
		if(strtoupper($request_type) == "GET"){
			$request_response = $socket->get($request_url, $request_data, $request_properties);
		}else if(strtoupper($request_type) == "POST"){
			$request_response = $socket->post($request_url, $request_data, $request_properties);	
		}else if(strtoupper($request_type) == "DELETE"){
			$request_response = $socket->delete($request_url, $request_data, $request_properties);	
		}
		//echo 'Response';print_r($request_response);
		return $request_response;		
	}


	private function checkToken($val){
	
		if(preg_match("/TOKEN::/", $val)){
			$token_array = explode("::",$val);
			$return = $this->tokenOperation($val);
			if($return){
				return $return;
			}else{
				return "";
			}
			
		}else{
			return $val;
		}
		
	}
	
	private function tokenOperation($token_string){
		if(preg_match("/DATE/", $token_string)){
			$matches = array();
			preg_match("/DATE\[(.*?)\]/", $token_string, $matches);
			$format =  $matches[1];
			
			$matches2 = array();
			preg_match("/DAYS\[(.*?)\]/", $token_string, $matches2);
			if(!empty($matches2)){
				$days = $matches2[1];
			}else{
				$days = 0;
			}
			
			$now = time();
			$now_modified = strtotime("$days day", $now);
			
			return date($format, $now_modified);
			
		}
		
		return $token_string;
		
	}
	
	
	
}