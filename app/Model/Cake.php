<?php
/**
 * Cake Marketing Model
 *
 * This model contains the data function for the cake marketing controller.
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
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
App::uses('CakeEmail', 'Network/Email');


class Cake extends AppModel {
	//public $actsAs = array('AuditLog.Auditable');
	public $name = 'Cake';
	public $useTable = 'cake';
	public $belongsTo = array(
			'Status' => array(
				'className' => 'Status',
				'foreignKey' => 'status_id'));
	
	
	//*************************
	//Stop putting these here.. I have to move from the function to the top of the file to figure out what api call is being made.
	//Place them in the function.
	private $_EXPORT_BUYER_API = '/2/export.asmx/Buyers?op=Buyers';
	private $_EXPORT_BUYER_CONTRACT_API = '/3/export.asmx/BuyerContracts?';
	private $_CREATE_BUYER_API = '/1/addedit.asmx/Buyer?';
	private $_CREATE_CONTRACT_API = '/1/addedit.asmx/BuyerContract?';
	private $_CREATE_SCHEDULE_API = '/1/addedit.asmx/BuyerContractDeliverySchedule?';
	private $_CREATE_FILTER_API = '/1/addedit.asmx/BuyerContractFilter?';
	private $_LEADS_BY_BUYER_API = '/4/reports.asmx/LeadsByBuyer?';
	//Stop putting these here.. I have to move from the function to the top of the file to figure out what api call is being made.
	//Place them in the function.
	//*******************
	
	
	
	
	/*Setting up validation of variables used in the CakeMarekting api.  Typically we validate
	* form field inputs prior to insertion into our own DB.  This was modified to validate variables
	* that are not in a database table.  This allows us to validate data prior to sending to 
	* CakeMarketing's api and handle errors accordingly.
	*/
	public $validate = array(
		'buyer_contract_id' => array(
			'rule' => 'numeric',
			'message' => 'Buyer Contract ID must be a valid integer.'
		),
		'lead_id' => array(
			'rule' => 'alphaNumeric',
			'message' => 'Lead ID is must be a valid alpha/numeric string.'
		),
		'add_to_existing' => array(
			'rule' => array('boolean'),
			'message' => 'Add to Existing requires a valid boolean value (true/false).'
		),
		'amount' => array(
			'rule' => array('money', 'left'),
			'message' => 'Amount requires a valid monetary amount.'
		),
		'campaign_id' => array(
			'rule' => 'numeric',
			'message' => 'Campaign ID must be a numeric value.'
		),
		'affiliate_id' => array(
			'rule' => 'numeric',
			'message' => 'Affiliate ID must be a numeric value.'
		),
		'offer_id' => array(
			'rule' => 'numeric',
			'message' => 'Offer ID must contain a numeric value.'
		),
	);
	
	/**
	 * Returns a list of reject dispositions and their ID's.  This is version 1 api and needs to be
	 * tested before used in production.
	 * @return Ambigous <multitype:string , unknown>
	 */
	public function rejecteddispositions(){
		$response = array('status'=>'error','data'=>'');
		
		$api_key = Configure::read('CakeM.ApiKey');
		$api_array_vars = $this->data['Cake'];
		$api_vars = http_build_query($api_array_vars);
		
		$api_func = '/1/track.asmx?op=RejectedDispositions?api_key='.$api_key;
		$api_url = Configure::read('CakeM.Url').$api_func;
				
		$xml = $this->send($api_url, null, 'accepteddispositions');
		$obj = json_decode(json_encode($xml));
		
		$this->log('xml return reject disposition:');
		$this->log($xml);
		
		foreach($obj as $key=>$value){
			$response['data'][$key]=$value;
		}
		
		if($obj->success == 'true'){
			$response['status'] = 'success';
		}
		else{
			$response['status'] = 'error';
		}
			
		return $response;
	}
	
	public function updateconversion($lead_id, $offer_id, $keystone_user, $payout='0.00', $received='0.00', $disposition_type='rejected', $disposition_id=2, $update_revshare_payout="TRUE", $effective_date_option='today', $custom_date='01/01/2016 00:00:01'){
		$this->data['Cake']['offer_id'] = $offer_id;
		$this->data['Cake']['conversion_id'] = 0;
		$this->data['Cake']['request_session_id'] = 0;
		$this->data['Cake']['transaction_id'] = $lead_id;
		$this->data['Cake']['payout'] = $payout;
		$this->data['Cake']['add_to_existing_payout'] = 'FALSE';
		$this->data['Cake']['received'] = $received;
		$this->data['Cake']['received_option'] = 'total_revenue';
		$this->data['Cake']['disposition_type'] = $disposition_type;
		$this->data['Cake']['disposition_id'] = $disposition_id;
		$this->data['Cake']['update_revshare_payout'] = $update_revshare_payout;
		$this->data['Cake']['effective_date_option'] = $effective_date_option;
		$this->data['Cake']['custom_date'] = $custom_date;
		$this->data['Cake']['note_to_append'] = $disposition_type.' by: '.$keystone_user;
		$this->data['Cake']['disallow_on_billing_status'] = 'ignore';
	
		$response = array('status'=>'error','data'=>'');
		
		$api_key = Configure::read('CakeM.ApiKey');
		$api_array_vars = $this->data['Cake'];
		$api_vars = http_build_query($api_array_vars);
		
		$api_func = '/4/track.asmx/UpdateConversion?api_key='.$api_key.'&';
		$api_url = Configure::read('CakeM.Url').$api_func.$api_vars;
	
		$xml = $this->send($api_url, null, 'updateconversion');
		$obj = json_decode(json_encode($xml));

		foreach($obj as $key=>$value){
			$response['data'][$key]=$value;
		}
		
		if($obj->success == 'true'){
			$response['status'] = 'success';
		}
		else{
			$response['status'] = 'error';
		}
			
		return $response;
	}
	
	/**
	 * Update a sold leads receivable price
	 * @param int $buyer_contract_id
	 * @param string $lead_id
	 * @param string $add_to_existing
	 * @param int $amount
	 * @param string $notes
	 * @return array
	 */
	public function updatesalerevenue($buyer_contract_id=null,$lead_id=null,$add_to_existing=null,$amount=null,$notes=null){
		$this->data['Cake']['buyer_contract_id'] = $buyer_contract_id;
		$this->data['Cake']['lead_id'] = $lead_id;
		$this->data['Cake']['add_to_existing'] = $add_to_existing;
		$this->data['Cake']['amount'] = $amount;
		$this->data['Cake']['notes'] = $notes;
	
		$response = array('status'=>'error', 'data'=>'');
	
		$api_key = Configure::read('CakeM.ApiKey');
		$api_array_vars = $this->data['Cake'];
		$api_vars = http_build_query($api_array_vars);
		$api_func = '/1/track.asmx/UpdateSaleRevenue?api_key='.$api_key.'&';
		$api_url = Configure::read('CakeM.Url').$api_func.$api_vars;
	
		$xml = $this->sendjson($api_url, null, 'updateSaleRevenue');
	
		$obj = json_decode(json_encode($xml));
		$response['data']['xml'] = $obj;
			
		if(preg_match('/Success/',$xml)){
			$response['status'] = 'success';
		}else{
			$response['status'] = 'error';
		}
	
		return $response;
	}
	
	
	/**
	 * Update a sold leads payable price
	 * @param int $vertical_id
	 * @param string $lead_id
	 * @param int $amount
	 * @param string $note
	 * @return string
	 */
	public function updateleadprice($vertical_id, $lead_id, $amount, $note){
		$this->data['Cake']['vertical_id'] = $vertical_id;
		$this->data['Cake']['lead_id'] = $lead_id;
		$this->data['Cake']['add_to_existing'] = 'FALSE';
		$this->data['Cake']['amount'] = $amount;
		$this->data['Cake']['mark_as_returned'] = 'FALSE';
		$this->data['Cake']['custom_date'] = '01/01/2016 00:00:00';
		$this->data['Cake']['effective_date_option'] = 'conversion_date';
		
		$response = array('status'=>'error','data'=>'');
		
		$api_key = Configure::read('CakeM.ApiKey');
		$api_array_vars = $this->data['Cake'];
		$api_vars = http_build_query($api_array_vars);
				
		$api_func = '/2/track.asmx/UpdateLeadPrice?api_key='.$api_key.'&';
		$api_url = Configure::read('CakeM.Url').$api_func.$api_vars.'&'.'note_to_append='.$note;
		
		$xml = $this->send($api_url, null, 'updateleadprice');
		$obj = json_decode(json_encode($xml));
		
		foreach($obj as $key=>$value){
			$response['data'][$key]=$value;
		}
		
		if($obj->success == 'true'){
			$response['status'] = 'success';
		}
		else{
			$response['status'] = 'error';
		}
			
		return $response;
	}
	
	/**
	 * Export affiliate information.  Retrieve the requested data from CakeM.
	 * @param int $affiliate_id
	 */
	public function exportaffiliate($affiliate_id=0){	
		$this->data['Cake']['affiliate_id'] = $affiliate_id;
		$this->data['Cake']['account_manager_id'] = 0;
		$this->data['Cake']['tag_id'] = 0;
		$this->data['Cake']['start_at_row'] = 0;
		$this->data['Cake']['row_limit'] = 0;
		$this->data['Cake']['sort_field'] = 0;
		$this->data['Cake']['sort_descending'] = 'true';
		$this->data['Cake']['affiliate_name'] = '';
	
		$response = array('status'=>'error','data'=>'');
	
		$api_key = Configure::read('CakeM.ApiKey');
		$api_array_vars = $this->data['Cake'];
		$api_vars = http_build_query($api_array_vars);
		$api_func = '/5/export.asmx/Affiliates?api_key='.$api_key.'&';
		$api_url = Configure::read('CakeM.Url').$api_func.$api_vars;
			
		$xml = $this->send($api_url, null, 'exportAffiliate');
		$obj = json_decode(json_encode($xml));
		foreach($obj as $key=>$value){
			$response['data'][$key]=$value;
		}
		
		if($obj->success == 'true'){
			$response['status'] = 'success';
		}
		else{
			$response['status'] = 'error';
		}
			
		return $response;
	}
	
	/**
	 * Detaild information about a specific lead including contract dispositions. 
	 * @param int $lead_id
	 * @return Ambigous <multitype:string , unknown>
	 */
	public function leadinfo($lead_id){
		$this->data['Cake']['lead_id'] = $lead_id;
		$this->data['Cake']['vertical_id'] = 0;
		
		$response = array('status'=>'error', 'data'=>'');
		
		$api_key = Configure::read('CakeM.ApiKey');
		$api_array_vars = $this->data['Cake'];
		$api_vars = http_build_query($api_array_vars);
		$api_func = '/1/get.asmx/LeadInfo?api_key='.$api_key.'&';
		$api_url = Configure::read('CakeM.Url').$api_func.$api_vars;
		
		$xml = $this->send($api_url, null, 'leadinfo');
		$obj = json_decode(json_encode($xml));
		
		foreach($obj as $key=>$value){
			$response['data'][$key]=$value;
		}
		
		if($obj->success == 'true'){
			$response['status'] = 'success';
		}
				
		return $response;
	}
	
	public function affiliatesummary($affiliate_id, $start_date, $end_date){
		$this->data['Cake']['affiliate_id'] = $affiliate_id;
		$this->data['Cake']['start_date'] = $start_date;
		$this->data['Cake']['end_date'] = $end_date;
		$this->data['Cake']['affiliate_manager_id'] = 0;
		$this->data['Cake']['affiliate_tag_id'] = 0;
		$this->data['Cake']['offer_tag_id'] = 0;
		$this->data['Cake']['event_id'] = 0;
		$this->data['Cake']['revenue_filter'] = 'conversions_and_events';
		
		$response = array('status'=>'error','data'=>'');
		
		$api_key = Configure::read('CakeM.ApiKey');
		$api_array_vars = $this->data['Cake'];
		$api_vars = http_build_query($api_array_vars);
		$api_func = '/2/reports.asmx/AffiliateSummary?api_key='.$api_key.'&';
		$api_url = Configure::read('CakeM.Url').$api_func.$api_vars;
		
		$xml = $this->send($api_url, null, 'affiliateSummary');
		$obj = json_decode(json_encode($xml));
		foreach($obj as $key=>$value){
			$response['data'][$key]=$value;
		}
		
		if($obj->success == 'true'){
			$response['status'] = 'success';
		}
		else{
			$response['status'] = 'error';
		}
		
		return $response;
	}
	
	/**
	 * Campaign summary data.  Retrieve the requested data from CakeM.
	 * @param int $affiliate_id
	 * @param int $campaign_id
	 * @param int $offer_id
	 * @param string $start_date
	 * @param string $end_date
	 */
	public function campaignsummary($affiliate_id, $campaign_id, $offer_id, $start_date, $end_date){
		$this->data['Cake']['affiliate_id'] = $affiliate_id;
		$this->data['Cake']['start_date'] = $start_date;
		$this->data['Cake']['end_date'] = $end_date;
		$this->data['Cake']['affiliate_manager_id'] = 0;
		$this->data['Cake']['affiliate_tag_id'] = 0;
		$this->data['Cake']['offer_id'] = $offer_id;
		$this->data['Cake']['offer_tag_id'] = 0;
		$this->data['Cake']['campaign_id'] = $campaign_id;
		$this->data['Cake']['event_id'] = 0;
		$this->data['Cake']['revenue_filter'] = 'conversions_and_events';
		
		$response = array('status'=>'error','data'=>'');
		
		$api_key = Configure::read('CakeM.ApiKey');
		$api_array_vars = $this->data['Cake'];
		$api_vars = http_build_query($api_array_vars);
		$api_func = '/2/reports.asmx/CampaignSummary?api_key='.$api_key.'&';
		$api_url = Configure::read('CakeM.Url').$api_func.$api_vars;
				
		$xml = $this->send($api_url, null, 'campaignSummary');
		$obj = json_decode(json_encode($xml));
		foreach($obj as $key=>$value){
			$response['data'][$key]=$value;
		}
		
		if($obj->success == 'true'){
			$response['status'] = 'success';
		}
		else{
			$response['status'] = 'error';
		}
		
		return $response;
	}
	
	public function dailysummaryexport( $affiliate_id=0, $campaign_id=0, $offer_id=0, $start_date=0, $end_date=0){
		$this->data['Cake']['affiliate_id'] = $affiliate_id;
		$this->data['Cake']['start_date'] = $start_date;
		$this->data['Cake']['end_date'] = $end_date;
		$this->data['Cake']['advertiser_id'] = 0;
		$this->data['Cake']['offer_id'] = $offer_id;
		$this->data['Cake']['vertical_id'] = 0;
		$this->data['Cake']['campaign_id'] = $campaign_id;
		$this->data['Cake']['creative_id'] = 0;
		$this->data['Cake']['account_manager_id'] = 0;
		$this->data['Cake']['include_tests'] = 'false';
		
		$response = array('status'=>'error','data'=>'');
		
		$api_key = Configure::read('CakeM.ApiKey');
		$api_array_vars = $this->data['Cake'];
		$api_vars = http_build_query($api_array_vars);
		$api_func = '/1/reports.asmx/DailySummaryExport?api_key='.$api_key.'&';
		$api_url = Configure::read('CakeM.Url').$api_func.$api_vars;
		
		$xml = $this->send($api_url, null, 'dailySummaryExport');
				
		$obj = json_decode(json_encode($xml));
		
		foreach($obj as $key=>$value){
			$response['data'][$key]=$value;
		}
		
		// Version 1 reports do not have a success / error flag.
		$response['status'] = 'success';
		
		return $response;
	}
		
	/**
	 * Export offer.  Retrieve offer data from CakeM.
	 * @param int $offer_id
	 */
	public function exportoffer($offer_id=0){
		unset($this->data['Cake']);
		$this->data['Cake']['offer_id'] = $offer_id;
		$this->data['Cake']['offer_name'] = '';
		$this->data['Cake']['advertiser_id'] = 0;
		$this->data['Cake']['vertical_id'] = 0;
		$this->data['Cake']['offer_type_id'] = 0;
		$this->data['Cake']['media_type_id'] = 0;
		$this->data['Cake']['offer_status_id'] = 0;
		$this->data['Cake']['tag_id'] = 0;
		$this->data['Cake']['start_at_row'] = 1;
		$this->data['Cake']['row_limit'] = 0;
		$this->data['Cake']['sort_field'] = 0;
		$this->data['Cake']['sort_descending'] = 'true';
		
		$response = array('status'=>'error','data'=>'');
		
		$api_key = Configure::read('CakeM.ApiKey');
		$api_array_vars = $this->data['Cake'];
		$api_vars = http_build_query($api_array_vars);
		$api_func = '/5/export.asmx/Offers?api_key='.$api_key.'&';
		$api_url = Configure::read('CakeM.Url').$api_func.$api_vars;

		$xml = $this->send($api_url, null, 'exportOffer');
		$obj = json_decode(json_encode($xml), true);
		foreach($obj as $key=>$value){
			$response['data'][$key]=$value;
		}
		
		if($obj['success'] == 'true'){
			$response['status'] = 'success';
		}
		else{
			$response['status'] = 'error';
		}
		return $response;
	}
	
	/**
	 * Returns a list of campaigns for the given criteria.
	 * cake.
	 * @param number $campaign_id
	 * @param number $offer_id
	 * @param number $affiliate_id
	 * @param number $json
	 */
	public function exportcampaign($campaign_id=0,$offer_id=0,$affiliate_id=0){
		unset($this->data['Cake']);
		$this->data['Cake']['campaign_id'] = $campaign_id;
		$this->data['Cake']['offer_id'] = $offer_id;
		$this->data['Cake']['affiliate_id'] = $affiliate_id;
		$this->data['Cake']['account_status_id'] = 0;
		$this->data['Cake']['media_type_id'] = 0;
		$this->data['Cake']['start_at_row'] = 1;
		$this->data['Cake']['row_limit'] = 0;
		$this->data['Cake']['sort_field'] = 0;
		$this->data['Cake']['sort_descending'] = 'true';
		
		$response = array('status'=>'error','data'=>'');
		
		$api_key = Configure::read('CakeM.ApiKey');
		$api_array_vars = $this->data['Cake'];
		$api_vars = http_build_query($api_array_vars);
		$api_func = '/6/export.asmx/Campaigns?api_key='.$api_key.'&';
		$api_url = Configure::read('CakeM.Url').$api_func.$api_vars;
		$xml = $this->send($api_url, null, 'exportCampaign');

				
		$obj = json_decode(json_encode($xml), true);
		
		foreach($obj as $key=>$value){
			$response['data'][$key]=$value;
		}
		
		if($obj['success'] == 'true'){
			$response['status'] = 'success';
		}
		else{
			$response['status'] = 'error';
		}
		
		return $response;
	}

	public function exportconversion($offerid, $campaignid, $affid, $creativeid=0)
	{
		date_default_timezone_set('EST'); //Cake runs on EST
		
		$this->data['Cake']['start_date'] = date("m/d/Y H:i:s", strtotime("-70 minutes"));
		$this->data['Cake']['end_date'] = date("m/d/Y H:i:s", strtotime("+70 minutes"));
		$this->data['Cake']['conversion_type'] = "all";
		$this->data['Cake']['event_id'] = 0;
		$this->data['Cake']['affiliate_id'] = $affid;
		$this->data['Cake']['advertiser_id'] = 0;
		$this->data['Cake']['offer_id'] = $offerid;
		$this->data['Cake']['affiliate_tag_id'] = 0;
		$this->data['Cake']['advertiser_tag_id'] = 0;
		$this->data['Cake']['offer_tag_id'] = 0;
		$this->data['Cake']['campaign_id'] = $campaignid;
		$this->data['Cake']['creative_id'] = $creativeid;
		$this->data['Cake']['price_format_id'] = 0;
		$this->data['Cake']['disposition_type'] = "all";
		$this->data['Cake']['disposition_id'] = 0;
		$this->data['Cake']['affiliate_billing_status'] = "all";
		$this->data['Cake']['advertiser_billing_status'] = "all";
		$this->data['Cake']['test_filter'] = "both";
		$this->data['Cake']['start_at_row'] = 0;
		$this->data['Cake']['row_limit'] = 1000;
		$this->data['Cake']['sort_field'] = "conversion_date";
		$this->data['Cake']['sort_descending'] = "false";
		
		
		$response = array('status'=>'error','data'=>'');
		
		$api_key = Configure::read('CakeM.ApiKey');
		$api_array_vars = $this->data['Cake'];
		$api_vars = http_build_query($api_array_vars);
		$api_func = '/11/reports.asmx/Conversions?api_key='.$api_key.'&';
		$api_url = Configure::read('CakeM.Url').$api_func.$api_vars;
		
		$xml = $this->send($api_url, null, 'exportConversion');
		
		$obj = json_decode(json_encode($xml), true);
		$response['data'] = $obj;
		
		foreach($obj as $key=>$value){
			
			$response['data'][$key]=$value;
		}
		
		
		if($obj['success'] == 'true'){
			$response['status'] = 'success';
		}
		else{
			$response['status'] = 'error';
		}
		date_default_timezone_set('America/New_York');
		return $response;
		
	}

	/**
	 * TRACK MassConversionInsert API Version 2.  Create a new conversion
	 * @param int $affiliate_id
	 * @param int $campaign_id
	 * @param int $creative_id
	 * @param float $payout
	 * @param float $received
	 * @param string $transaction_id
	 * @param string $conversion_date
	 * @param int $total_to_insert
	 * @param string $sub_affiliate
	 * @param string $note
	 */
	public function massconversioninsert_v2($affiliate_id, $campaign_id, $creative_id, $payout, $received, $transaction_id,  $conversion_date, $total_to_insert=1, $sub_affiliate='[Empty]', $note='null'){
		$this->data['Cake']['affiliate_id'] = $affiliate_id;
		$this->data['Cake']['campaign_id'] = $campaign_id;
		$this->data['Cake']['sub_affiliate'] = $sub_affiliate;
		$this->data['Cake']['creative_id'] = $creative_id;
		$this->data['Cake']['total_to_insert'] = $total_to_insert;
		$this->data['Cake']['payout'] = $payout;
		$this->data['Cake']['received'] = $received;
		$this->data['Cake']['note'] = $note;
		$this->data['Cake']['transaction_ids'] = $transaction_id;
		$this->data['Cake']['conversion_date'] = $conversion_date;
		
		$response = array('status'=>'error','data'=>'');
		
		$api_key = Configure::read('CakeM.ApiKey');
		$api_array_vars = $this->data['Cake'];
		$api_vars = http_build_query($api_array_vars);
		$api_func = '/2/track.asmx/MassConversionInsert?api_key='.$api_key.'&';
		$api_url = Configure::read('CakeM.Url').$api_func.$api_vars;
				
		$xml = $this->send($api_url, null, 'MassConversionInsert');
		$obj = json_decode(json_encode($xml));
		foreach($obj as $key=>$value){
			$response['data'][$key]=$value;
		}
		
		if($obj->success == 'true'){
			$response['status'] = 'success';
		}
		else{
			$response['status'] = 'error';
		}
		
		return $response;
	}
		
	public function getPostKey($campaign_id) {
		$cmp_arr = $this->exportcampaign($campaign_id);
		$post_key = $cmp_arr['data']['campaigns']['campaign']['submission_options']['post_key'];
		return $post_key;
	}
	
	public function getapikey($username, $password) {
		$this->data['Cake']['username'] = $username;
		$this->data['Cake']['password'] = $password;
		
		$response = array('status'=>'error', 'data'=>'');
		
		$api_key = Configure::read('CakeM.ApiKey');
		$api_array_vars = $this->data['Cake'];
		$api_vars = http_build_query($api_array_vars);
		$api_func = '/1/get.asmx/GetAPIKey?';
		$api_url = Configure::read('CakeM.Url').$api_func.$api_vars;

		$xml = $this->send($api_url, null, 'getApiKey');
				
		$obj = json_decode(json_encode($xml));
		
		foreach($obj as $key=>$value){
			$response['data'][$key]=$value;
		}
		
		// Version 1 reports do not have a success / error flag.
		$response['status'] = 'success';
		
		return $response;
	}
	
	/**
	 * Send the request via curl to Cake Marketing.  If you send in a post array, then it is posted. JSON return.
	 * @param string $url
	 * @param array $post_array
	 * @return array
	 */
	public function sendjson($url, $post_array='', $method=null) {
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, $url );
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
			
		if(is_array($post_array)){
			curl_setopt($ch,CURLOPT_POST,true);
			curl_setopt($ch,CURLOPT_POSTFIELDS,$post_array);
		}

		$cr = curl_exec($ch);
		curl_close($ch);
				
		unset($this->data['Cake']);
		return json_decode($cr);
	}
	
	/**
	 * Send the request via curl to Cake Marketing.  If you send in a post array, then it is posted.  If the response is not valid xml then it is returned as a raw string.
	 * @param string $url
	 * @param array $post_array
	 * @return mixed
	 */
	public function send($url, $post_array='', $method=null) {
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, $url );
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
			
		if(is_array($post_array)){
			curl_setopt($ch,CURLOPT_POST,true);
			curl_setopt($ch,CURLOPT_POSTFIELDS,$post_array);
			$cr = curl_exec($ch);
			curl_close($ch);
			
			// Clear the model and prepare for cake/audit update
			unset($this->data['Cake']);
			
			return $cr;
		}

		$cr = curl_exec($ch);
		curl_close($ch);
		libxml_use_internal_errors(true);
		$xml = simplexml_load_string($cr);
		
		// Clear the model and prepare for cake/audit update
		unset($this->data['Cake']);
							
		if(!$xml){
			return $cr;
		}else {
			return $xml;
		}
	}
	
	/**
	 * Send writes large xml responses to a file rather than return the data.
	 * @param string $url
	 * @param int $file_id
	 * @param string $method
	 * @return boolean
	 */
	public function sendLarge($url, $file_id=0, $method=null) {
		$fp = fopen(APP.'tmp/files/large_tmp_'.$file_id.'.xml', 'a+');
		
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, $url );
		curl_setopt($ch,CURLOPT_BUFFERSIZE, 1024);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_FILE, $fp);

		$cr = curl_exec($ch);
		curl_close($ch);
		fclose($fp);
		
		return true;
	}
	
	/*
	 * A GET REQUEST to generate a session/request id for External posts
	 */
	public function generateSession($affid, $creative, $sub_array, $offer_type, $price_format){
		
		//Build out SubId string
		$sub_ct = 1;
		$sub_string = "";
		foreach($sub_array as $sub){
			if($sub){
				$sub_string .= "&s$sub_ct=".$sub;	
			}
			 $sub_ct++;
		}
		
		if ($offer_type == 'Host-n-Post' && $price_format == 'CPA') 
		{
			$url = Configure::read('CakeM.UrlClickPixel').'?a='.$affid.'&c='.$creative.'&cp=js'.$sub_string;
			
		}
		else if ($offer_type == 'Host-n-Post' && $price_format == 'RevShare') 
		{
			$url = Configure::read('CakeM.UrlClickPixel').'?a='.$affid.'&c='.$creative.'&p=r&cp=js'.$sub_string;
		
		}
		else if ($offer_type == 'Host-n-Post' && $price_format == 'Fixed') 
		{
			$url = Configure::read('CakeM.UrlClickPixel').'?a='.$affid.'&c='.$creative.'&p=f&cp=js'.$sub_string;
		
		}
		
	
		//$extract1 = $this->send($url);
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, $url );
		curl_setopt($ch,CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		$cr = curl_exec($ch);
		curl_close($ch);
		
		//$this->log($cr);
			
		preg_match("/var ckm_request_id = (.*);/", $cr, $matches2);
		return trim($matches2[1]);
	}
	
	public function paymentType($campaign_id,$sub_affiliate_id = null)
	{
		$is_bucket = false;
		$bucket_id = "false";
	
		$cmp_full_array = $this->exportcampaign($campaign_id);
	
		$cmp_array = $cmp_full_array['data'];

		$got_data = $cmp_array['success'];
		$rows = $cmp_array['row_count'];
		
		if ($got_data == 'true' && $rows == 1)
		{
			
			$offer_id = $cmp_array['campaigns']['campaign']['offer']['offer_id'];
			$off_full_array = $this->exportoffer($offer_id);
			
			//print_r($off_full_array);
			$off_array = $off_full_array['data'];
			$offer_type = $off_array['offers']['offer']['offer_type']['offer_type_name'];
			$price_format = $cmp_array['campaigns']['campaign']['offer_contract']['price_format']['price_format_name'];
			$default_payout = (FLOAT)$cmp_array['campaigns']['campaign']['payout']['amount'];//CPA
			$default_payout = number_format(round($default_payout, 2), 2, '.', '');
			$default_payout_percentage = $off_array['offers']['offer']['offer_contracts']['offer_contract_info']['payout']['amount'];
			$is_percentage = $cmp_array['campaigns']['campaign']['payout']['is_percentage'];
			$default_margin = 0.00; //Only set on buckets.  Calculated in all other situations
			
			//if($is_percentage == "true"){ $default_payout = 0.00; }
			
			$is_bucket = NULL;
			//$this->log($campaign_id);
			$cmp_to_array = json_decode(stripslashes($cmp_array['campaigns']['campaign']['notes']), true);		
			
			$cnotes = $cmp_to_array['Bucket'];
			$tierMax = ((!empty($cmp_to_array['TierMax']))? $cmp_to_array['TierMax'] : false);
			$tierMin = ((!empty($cmp_to_array['TierMin']))? $cmp_to_array['TierMin'] : false);
			
			if($is_percentage == 'true' && $tierMin == false){
				$tierMin = 0.00;
			}
			
			if(!empty($cnotes)){
				$is_bucket = $cnotes[0];
				$default_margin = $cnotes[1];
			}

		
			if($is_bucket === NULL){
				$is_bucket = false;
				$default_margin = 20.0;
			}
		
		}
		else
		{
			//should never happen	
			return false;
		}
		
		
		$payment_info = array('is_bucket'=> $is_bucket,'default_payout' => $default_payout,'default_margin' => $default_margin, 'offer_type' => $offer_type, 'price_format' => $price_format, 'is_percentage'=>$is_percentage, 'TierMax'=>$tierMax, 'TierMin'=>$tierMin);
			
				
		return $payment_info;
	}

	public function getBucketId($type,$campaign_id,$sub_affiliate_id = null)
	{
		if ($type == 'sub')
		{
			$bucket_id = $campaign_id."-".$sub_affiliate_id;
		}
		else
		{
			$bucket_id = $campaign;
		}

		return $bucket_id;
	}
	
	public function exportbuyers($buyer_id=0){
		$this->data = array();
		$this->data['param']['api_key'] = Configure::read('CakeM.ApiKey');
		$this->data['param']['buyer_id'] = $buyer_id;
		$this->data['param']['account_status_id'] = 0;
		
		$response = array('status'=>'error','data'=>'');
		
		$api_array_vars = $this->data['param'];
		$api_vars = http_build_query($api_array_vars);
		$api_url = Configure::read('CakeM.Url').$this->_EXPORT_BUYER_API.'&'.$api_vars; 
		$xml = $this->send($api_url, null, 'exportbuyers');
		
		$obj = json_decode(json_encode($xml), true);
		
		foreach($obj as $key=>$value){
			$response['data'][$key]=$value;
		}
		
		if($obj['success'] == 'true'){
			$response['status'] = 'success';
		}
		else{
			$response['status'] = 'error';
		}
		
		return $response;
	}
	
	public function exportcontracts($buyer_id=0, $contract_id=0){
		$this->data = array();
		$this->data['param']['api_key'] = Configure::read('CakeM.ApiKey');
		$this->data['param']['buyer_id'] = $buyer_id;
		$this->data['param']['buyer_contract_id'] = $contract_id;
		$this->data['param']['vertical_id'] = 0;
		$this->data['param']['buyer_contract_status_id'] = 0;
		
		$response = array('status'=>'error','data'=>'');
		
		$api_array_vars = $this->data['param'];
		$api_vars = http_build_query($api_array_vars);
		$api_url = Configure::read('CakeM.Url').$this->_EXPORT_BUYER_CONTRACT_API.$api_vars; 
		$xml = $this->send($api_url, null, 'exportcontracts');
		
	
		$obj = json_decode(json_encode($xml), true);
		
		
		
		foreach($obj as $key=>$value){
			$response['data'][$key]=$value;
		}
		
		if($obj['success'] == 'true'){
			$response['status'] = 'success';
		}
		else{
			$response['status'] = 'error';
		}
		
		return $response;
	}
	
	public function createBuyer($buyer_name){
		$this->data = array();
		$this->data['param']['api_key'] = Configure::read('CakeM.ApiKey');
		$this->data['param']['buyer_id'] = 0;
		$this->data['param']['buyer_name'] = $buyer_name;
		$this->data['param']['account_status_id'] = 2;
		$this->data['param']['account_manager_id'] = 1;
		$this->data['param']['address_street'] = "";
		$this->data['param']['address_street2'] = "";
		$this->data['param']['address_city'] = "";
		$this->data['param']['address_state'] = "";
		$this->data['param']['address_zip_code'] = "";
		$this->data['param']['address_country'] = "USA";
		$this->data['param']['website'] = "";
		$this->data['param']['billing_cycle_id'] = 0;
		$this->data['param']['credit_type'] = 'unlimited';
		$this->data['param']['credit_limit'] = -1;
		
		$response = array('status'=>'error','data'=>'');
		
		$api_array_vars = $this->data['param'];
		$api_vars = http_build_query($api_array_vars);
		$api_url = Configure::read('CakeM.Url').$this->_CREATE_BUYER_API.$api_vars;
		$xml = $this->send($api_url, null, 'createbuyer');
		
		$obj = json_decode(json_encode($xml), true);
		
		foreach($obj as $key=>$value){
			$response['data'][$key]=$value;
		}
		
		if($obj['success'] == 'true'){
			$response['status'] = 'success';
		}
		else{
			$response['status'] = 'error';
		}
		
		return $response;	
		
	}
	
	public function createContract(array $contract){
		
		$contract['api_key'] = Configure::read('CakeM.ApiKey');
		
		$response = array('status'=>'error','data'=>'');
		
		$api_array_vars = $contract;
		$api_vars = http_build_query($api_array_vars);
		$api_url = Configure::read('CakeM.Url').$this->_CREATE_CONTRACT_API.$api_vars; 
		$xml = $this->send($api_url, null, 'createcontract');
		
		$obj = json_decode(json_encode($xml), true);
		
		foreach($obj as $key=>$value){
			$response['data'][$key]=$value;
		}
		
		if($obj['success'] == 'true'){
			$response['status'] = 'success';
		}
		else{
			$response['status'] = 'error';
		}
		return $response;	
		
	}
	
	public function createSingleDeliveryScheduleItem(array $schedule){
		
		$schedule['api_key'] = Configure::read('CakeM.ApiKey');
		
		$response = array('status'=>'error','data'=>'');
		
		$api_array_vars = $schedule;
		$api_vars = http_build_query($api_array_vars);
		$api_url = Configure::read('CakeM.Url').$this->_CREATE_SCHEDULE_API.$api_vars;
		$xml = $this->send($api_url, null, 'createschedule');
		
		$obj = json_decode(json_encode($xml), true);
		
		foreach($obj as $key=>$value){
			$response['data'][$key]=$value;
		}
		
		if($obj['success'] == 'true'){
			$response['status'] = 'success';
		}
		else{
			$response['status'] = 'error';
		}
		
		return $response;	
	}
	
	public function createSingleFilter(array $filter){
		$filter['api_key'] = Configure::read('CakeM.ApiKey');
		
		$response = array('status'=>'error','data'=>'');
		
		$api_array_vars = $filter;
		$api_vars = http_build_query($api_array_vars);
		$api_url = Configure::read('CakeM.Url').$this->_CREATE_FILTER_API.$api_vars;
		$xml = $this->send($api_url, null, 'createfilter');
		
		$obj = json_decode(json_encode($xml), true);
		
		
		foreach($obj as $key=>$value){
			$response['data'][$key]=$value;
		}
		
		if($obj['success'] == 'true'){
			$response['status'] = 'success';
		}
		else{
			$response['status'] = 'error';
		}
		
		return $response;	
	}
		
	/**
	 * Export Leads, because this return can be very large we use sendLarge forcing the output to a file
	 * to avoid memory leak issues.
	 * @param array $filter
	 * @param string $file_id
	 * @return boolean
	 */
	public function exportleadsbybuyer(array $filter, $file_id=null){
		$filter['api_key'] = Configure::read('CakeM.ApiKey');
		$api_array_vars = $filter;
		$api_vars = http_build_query($api_array_vars);
		$api_url = Configure::read('CakeM.Url').$this->_LEADS_BY_BUYER_API.$api_vars;
		$this->sendLarge($api_url, $file_id, 'exportleadsbybuyer');
		return true;
	}
			
	/**
	 * Gets the report from CAKE UI.  Buyers Report
	 */
	public function getMasterBuyerContractSummary($buyer_id, $start_date, $end_date, $date_range){
		$ch = curl_init();
		$post_array = array('buyer'=>$buyer_id, 'd'=>'DESC', 'end_date'=>$end_date,'groupDir'=>'ASC','multisell'=>-1,'n'=>150,'o'=>'sold','p'=>0,'ping'=>-1,
							'post'=>-1,'report_id'=>62,'report_view_id'=>62,'report_views'=>'Default','start_date'=>$start_date,'test'=>0,'upsell'=>-1,'vintage'=>-1);
							
		$headers = array("Content-Type"=>"application/x-www-form-urlencoded");
		curl_setopt($ch,CURLOPT_URL, 'http://leadstudioportal.com/Extjs.ashx?s=reportmasterbuyercontractsummary');
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookies.txt');
		curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch,CURLOPT_POST,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$post_array);
		$cr = curl_exec($ch);
		curl_close($ch);
		return $cr;	
	}
	
	
	public function cakeLogin(){
		$ch = curl_init();
		$post_array = array("p"=>"devian2001","u"=>"jason@clickmedia.com");
		$headers = array("Content-Type"=>"application/x-www-form-urlencoded");
		curl_setopt($ch,CURLOPT_URL, 'http://leadstudioportal.com/login.ashx' );
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookies.txt');
		curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
		curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6"); 
		curl_setopt($ch, CURLOPT_COOKIESESSION, true);  
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch,CURLOPT_POST,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$post_array);
		$cr = curl_exec($ch);
		curl_close($ch);	
	}
	
	public function updateRank($contract_id, $rank){
		$ch = curl_init();
		$post_array = array("id"=>$contract_id,"rank"=>$rank);
		$headers = array("Content-Type"=>"application/x-www-form-urlencoded");
		curl_setopt($ch,CURLOPT_URL, 'http://leadstudioportal.com/handlers/update.ashx?s=bc');
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookies.txt');
		curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch,CURLOPT_POST,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$post_array);
		$cr = curl_exec($ch);
		curl_close($ch);
		return $cr;	
	}
	
	public function seedContract($contract_id, $fields){
		$ch = curl_init();
		$headers = array("Content-Type"=>"application/x-www-form-urlencoded");
		curl_setopt($ch,CURLOPT_URL, 'http://leadstudioportal.com/extjs.ashx?s=testpostnew&bcid='.$contract_id.'&data=0');
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookies.txt');
		curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch,CURLOPT_POST,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$fields);
		$cr = curl_exec($ch);
		curl_close($ch);
		return $cr;	
	}
	
	
	public function copyPoster($org_id, $new_id){
		$ch = curl_init();
		$post_array = array("bc"=>$new_id,"bc_from"=>$org_id,"is_ping"=>0,"url"=>"");
		$headers = array("Content-Type"=>"application/x-www-form-urlencoded");
		curl_setopt($ch,CURLOPT_URL, 'http://leadstudioportal.com/extjs.ashx?s=copyposter&bcid='.$new_id );
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookies.txt');
		curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch,CURLOPT_POST,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$post_array);
		$cr = curl_exec($ch);
		curl_close($ch);
		return $cr;	
	}
	
	public function heartBeatCpaNotesCheck(){
		//Pull all offers that are (a)US Loan	
		$offers = $this->exportoffer();
		$internal_offers = array();
		$missing_notes = array();
		foreach($offers['data']['offers']['offer'] as $k=>$v){
			if($v['vertical']['vertical_id'] == "47"){
				$internal_offers[] = array('name' => $v['offer_name'], 'id' => $v['offer_id']);
			}	
		}
		
		foreach($internal_offers as $index=>$o){
			$offer_id = $o['id'];
			$offer_name = $o['name'];
			
			//pull campaigns in offer
			$campaign = $this->exportcampaign(0,$offer_id,0);
			
			if($campaign['data']['row_count'] == 0)continue;
			
			if($campaign['data']['row_count'] == 1){
				//so we can loop through array correctly
				$campaign_loop[] = $campaign['data']['campaigns']['campaign'];
			}else{
				$campaign_loop = $campaign['data']['campaigns']['campaign'];
			}
			
			foreach($campaign_loop as $k=>$v){
				
				if( (isset($v['offer_contract']['offer_contract_name']) && $v['offer_contract']['offer_contract_name'] == 'CPA') && 
					(isset($v['offer_contract']['price_format']['price_format_name']) && $v['offer_contract']['price_format']['price_format_name'] == 'CPA') ){
					
						if(empty($v['notes'])){
							$missing_notes[] = array('offer_id'=>$offer_id, 'offer_name'=>$offer_name, 'campaign_id'=>$v['campaign_id']);
						}else if(!json_decode($v['notes'])){
							$missing_notes[] = array('offer_id'=>$offer_id, 'offer_name'=>$offer_name, 'campaign_id'=>$v['campaign_id']);
						}else{
							$json = json_decode($v['notes'],true);
							if(!isset($json['Bucket'])){
								$missing_notes[] = array('offer_id'=>$offer_id, 'offer_name'=>$offer_name, 'campaign_id'=>$v['campaign_id']);
							}	
						}
							
				}
				
			}//End Campaign
			
		}//End Offers
		
		if(count($missing_notes) > 0){
			
			return array('passed'=>false, 'data'=>$missing_notes);
		}else{
			return array('passed'=>true);
		}
			
	}
}