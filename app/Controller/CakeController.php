<?php
/**
 * Cake Controller
 *
 * This file is responsible for the Cake Marketing api integration into keyStone.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 * 
 * All Json returns should be in the following format:
 * {
 * 		status : "success" [success|fail|error]
 *      message : "" [short msg for fail,error]
 * 		data : {}
 * }
 *
 * @copyright     TBD
 * @link          https://github.com/AdLink360/keyStone/wiki/CakeController
 * @package       app.Controller
 * @since         keyStone v1.0
 * @license       TBD
 */

class CakeController extends AppController {
	public $uses = array('Cake','Status','Affiliate');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->autoRedirect = false;
		$this->__isAuthorized($this->Session->read('Auth.User'));
	}
		
	//****************Track****************//
	/**
	 * Update sales revenue in Cake Marketing. 
	 * @param int $buyer_contract_id
	 * @param string $lead_id
	 * @param bool $add_to_existing
	 * @param string $amount
	 * @param string $notes
	 * @todo transform xml to json for usage.
	 * @return json
	 */
	public function updatesalerevenue($buyer_contract_id=null,$lead_id=null,$add_to_existing=null,$amount=null,$notes=null) {
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		
		$this->request->data['Cake']['buyer_contract_id'] = $buyer_contract_id;
		$this->request->data['Cake']['lead_id'] = $lead_id;
		$this->request->data['Cake']['add_to_existing'] = $add_to_existing;
		$this->request->data['Cake']['amount'] = $amount;
		$this->request->data['Cake']['notes'] = $notes;
		
		// Holding these for audit.
		$api_array_vars = $this->data['Cake'];
		
		if($this->Cake->validates()){
			$response = $this->Cake->updatesalerevenue($buyer_contract_id, $lead_id, $add_to_existing, $amount, $notes);
		}else{
			$response['status'] = 'fail';
			$response['message'] = $this->Cake->validationErrors;
		}
		
		return json_encode($response);
	}
	
	//****************Export****************//
	/**
	 * Export campaign information out of Cake Marketing.  This response has a 1 hour cache in front of it.
	 * @param int $campaign_id
	 * @param int $offer_id
	 * @param int $affiliate_id
	 */
	public function exportcampaign($campaign_id=0,$offer_id=0,$affiliate_id=0){
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		
		$cache['hash'] = md5('cake_exportcampaign_'.$campaign_id.'_'.$offer_id.'_'.$affiliate_id);
		
		$cache['value'] = false;
		$cache['value'] = Cache::read($cache['hash']);
		
		if($cache['value'] === false){
			$this->request->data['Cake']['campaign_id'] = $campaign_id;
			$this->request->data['Cake']['offer_id'] = $offer_id;
			$this->request->data['Cake']['affiliate_id'] = $affiliate_id;
			$this->request->data['Cake']['account_status_id'] = 0;
			$this->request->data['Cake']['media_type_id'] = 0;
			$this->request->data['Cake']['start_at_row'] = 1;
			$this->request->data['Cake']['row_limit'] = 0;
			$this->request->data['Cake']['sort_field'] = 0;
			$this->request->data['Cake']['sort_descending'] = 'true';
			
			if($this->Cake->validates()){
				$cache['value'] = $this->Cake->exportcampaign($campaign_id, $offer_id, $affiliate_id);
			}else{
				$response['status'] = 'fail';
				$response['message'] = $this->Cake->validationErrors;
				$cache['value'] = $response;
			}
		}
		
		// Cache the response for 1 hour.
		Cache::write($cache['hash'],$cache['value']);
		return json_encode($cache['value']);
	}
						
	/**
	 * Export offer information out of Cake Marketing.  This response has a 1 hour cache in front of it.
	 * @param int $offer_id
	 */
	public function exportoffer($offer_id=0){
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
	
		$cache['hash'] = md5('cake_exportoffer_'.$offer_id);
	
		$cache['value'] = false;
		$cache['value'] = Cache::read($cache['hash']);
	
		if($cache['value'] === false){
			$this->request->data['Cake']['offer_id'] = $offer_id;
			$this->request->data['Cake']['offer_name'] = '';
			$this->request->data['Cake']['advertiser_id'] = 0;
			$this->request->data['Cake']['vertical_id'] = 0;
			$this->request->data['Cake']['offer_type_id'] = 0;
			$this->request->data['Cake']['media_type_id'] = 0;
			$this->request->data['Cake']['offer_status_id'] = 0;
			$this->request->data['Cake']['tag_id'] = 0;
			$this->request->data['Cake']['start_at_row'] = 1;
			$this->request->data['Cake']['row_limit'] = 0;
			$this->request->data['Cake']['sort_field'] = 0;
			$this->request->data['Cake']['sort_descending'] = 'true';
			
			if($this->Cake->validates()){
				$cache['value'] = $this->Cake->exportoffer($offer_id);
			}else{
				$response['status'] = 'fail';
				$response['message'] = $this->Cake->validationErrors;
				$cache['value'] = $response;
			}
		}
		
		// Cache the response for 1 hour.
		Cache::write($cache['hash'],$cache['value']);
		return json_encode($cache['value']);
	}
	
	/**
	 * Export affiliate information.  This response has a 1 hour cache in front of it.
	 * @param int $affiliate_id
	 */
	public function exportaffiliate($affiliate_id=0){	
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		
		$cache['hash'] = md5('cake_exportaffiliate_'.$affiliate_id);
	
		$cache['value'] = false;
		$cache['value'] = Cache::read($cache['hash']);
	
		if($cache['value'] === false){
			$this->Cake->data['Cake']['affiliate_id'] = $affiliate_id;
			if($this->Cake->validates()){
				// The model retrieves the data from cake marketing api.
				$cache['value'] = $this->Cake->exportaffiliate($affiliate_id);
			}else{
				$response['status'] = 'fail';
				$response['message'] = $this->Cake->validationErrors;
				$cache['value'] = $response;
			}
		}
		// Cache the response for 1 hour.
		Cache::write($cache['hash'],$cache['value']);
		return json_encode($cache['value']);
	}
	
	//****************Reports****************//
	public function campaignsummary($affiliate_id=0, $campaign_id=0, $offer_id=0, $start_date=0, $end_date=0){
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		
		if($start_date == 0){
			$start_date = date("m-d-Y", time() - 60 * 60 * 24);
		}
		
		if($end_date == 0){
			$end_date = date("m-d-Y");
		}
			
		$cache['hash'] = md5('cake_campaignsummary_'.$affiliate_id.'_'.$campaign_id.'_'.$offer_id.'_'.$start_date.$end_date);
		$cache['value'] = false;
		$cache['value'] = Cache::read($cache['hash']);
		
		if($cache['value'] === false){
			$this->request->data['Cake']['affiliate_id'] = $affiliate_id;
			$this->request->data['Cake']['start_date'] = $start_date;
			$this->request->data['Cake']['end_date'] = $end_date;
			$this->request->data['Cake']['affiliate_manager_id'] = 0;
			$this->request->data['Cake']['affiliate_tag_id'] = 0;
			$this->request->data['Cake']['offer_id'] = $offer_id;
			$this->request->data['Cake']['offer_tag_id'] = 0;
			$this->request->data['Cake']['campaign_id'] = $campaign_id;
			$this->request->data['Cake']['event_id'] = 0;
			$this->request->data['Cake']['revenue_filter'] = 'conversions_and_events';

			if($this->Cake->validates()){
				$cache['value'] = $this->Cake->campaignsummary($affiliate_id, $campaign_id, $offer_id, $start_date, $end_date);
			}else{
				$response['status'] = 'fail';
				$response['message'] = $this->Cake->validationErrors;
				$cache['value'] = $response;
			}
		}
		
		// Cache the response for 1 hour.
		Cache::write($cache['hash'],$cache['value']);
		return json_encode($cache['value']);
	}

	/**
	 * @param number $affiliate_id
	 * @param number $start_date
	 * @param number $end_date
	 */
	public function affiliatesummary($affiliate_id=0, $start_date=0, $end_date=0){
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		
		if($start_date == 0){
			$start_date = date("m-d-Y", time() - 60 * 60 * 48);  // Need to double check this.
		}
		
		if($end_date == 0){
			$end_date = date("m-d-Y");
		}
			
		$cache['hash'] = md5('cake_affiliatesummary_'.$affiliate_id.'_'.$start_date.$end_date);
		
		$cache['value'] = false;
		$cache['value'] = Cache::read($cache['hash']);
		
		if($cache['value'] === false){
			$this->request->data['Cake']['affiliate_id'] = $affiliate_id;
			$this->request->data['Cake']['start_date'] = $start_date;
			$this->request->data['Cake']['end_date'] = $end_date;
			$this->request->data['Cake']['affiliate_manager_id'] = 0;
			$this->request->data['Cake']['affiliate_tag_id'] = 0;
			$this->request->data['Cake']['offer_tag_id'] = 0;
			$this->request->data['Cake']['event_id'] = 0;
			$this->request->data['Cake']['revenue_filter'] = 'conversions_and_events';
						
			if($this->Cake->validates()){
				$cache['value'] = $this->Cake->affiliatesummary($affiliate_id, $start_date, $end_date);
			}else{
				$response['status'] = 'fail';
				$response['message'] = $this->Cake->validationErrors;
				$cache['value'] = $response;
			}
		}
		
		// Cache the response for 15 minutes.
		Cache::write($cache['hash'],$cache['value'],'15m');
		return json_encode($cache['value']);
	}
	
	/**
	 * Pull the daily summary report, by default the date range will be the previous day.
	 * @todo This report needs to be looked at before production use.  It does not appear to accept id's as intended.
	 * @param number $campaign_id
	 * @param number $offer_id
	 * @param number $affiliate_id
	 * @param number $start_date
	 * @param number $end_date
	 */
	public function dailysummaryexport($affiliate_id=0, $campaign_id=0, $offer_id=0, $start_date=0, $end_date=0){
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');

		if($start_date == 0){
			$start_date = date("m-d-Y", time() - 60 * 60 * 24);
		}

		if($end_date == 0){
			$end_date = date("m-d-Y");
		}
			
		$cache['hash'] = md5('cake_dailysummaryexport_'.$offer_id.'_'.$campaign_id.'_'.$affiliate_id.'_'.$start_date.$end_date);
		
		$cache['value'] = false;
		$cache['value'] = Cache::read($cache['hash']);
		
		if($cache['value'] === false){
			$this->request->data['Cake']['affiliate_id'] = $affiliate_id;
			$this->request->data['Cake']['start_date'] = $start_date;
			$this->request->data['Cake']['end_date'] = $end_date;
			$this->request->data['Cake']['advertiser_id'] = 0;
			$this->request->data['Cake']['offer_id'] = $offer_id;
			$this->request->data['Cake']['vertical_id'] = 0;
			$this->request->data['Cake']['campaign_id'] = $campaign_id;
			$this->request->data['Cake']['creative_id'] = 0;
			$this->request->data['Cake']['account_manager_id'] = 0;
			$this->request->data['Cake']['include_tests'] = 'false';
			
			if($this->Cake->validates()){
				$cache['value'] = $this->Cake->dailysummaryexport($affiliate_id, $campaign_id, $offer_id, $start_date, $end_date);
			}else{
				$response['status'] = 'fail';
				$response['message'] = $this->Cake->validationErrors;
				$cache['value'] = $response;
			}
		}
		
		// Cache the response for 1 hour.
		Cache::write($cache['hash'],$cache['value']);
		return json_encode($cache['value']);	
	}
		
	//****************Get****************//
	/**
	 * Need to validate use of this function.  Version 1 api's are strange.
	 * @param string $username
	 * @param string $password
	 */
	public function getapikey($username, $password){
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
	
		$cache['hash'] = md5('cake_getapikey_'.$username.'_'.$password);
	
		$cache['value'] = false;
		//$cache['value'] = Cache::read($cache['hash']);
	
		if($cache['value'] === false){
			$this->request->data['Cake']['username'] = $username;
			$this->request->data['Cake']['password'] = $password;
			
			if($this->Cake->validates()){
				$cache['value'] = $this->Cake->getapikey($username, $password);
			}else{
				$response['status'] = 'fail';
				$response['message'] = $this->Cake->validationErrors;
				$cache['value'] = $response;
			}
						
			// Cache the response for 1 hour.
			Cache::write($cache['hash'],$cache['value']);
		}
	
		return json_encode($cache['value']);
	}
	
	
	/**
	 * Specify what user group has access.  For development speed, this is not in ACL.
	 * @todo Add ACL functionality to keyStone.
	 * @param array $user
	 * @return boolean
	 */
	private function __isAuthorized($user) {
		//Management
		if (in_array($this->Session->read('Auth.User.Group.id'),array('2','3','4'))) {
			$this->Auth->allow();
			return true;
		}
		return false;
	}
}