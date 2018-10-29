<?php
/**
 * Api Model
 *
 * This model contains the data function for the bucket controller.
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
class Api extends AppModel {
	public $actsAs = array('AuditLog.Auditable');
	public $name = 'Post';
	public $useTable = 'posts';
	public $api_key = "k8sa0o6VjwPimwKAYS34XaPjfQEWPc";
	
	/*public $validate = array(
		'buyer_contract_id' => array(
			'rule' => 'numeric',
			'message' => 'Buyer Contract ID must be a valid integer.'
			),
		
			
			"FirstName": {
			"required": "true",
			"rgx": "/^([a-zA-Z\\s-\'\\.]{1,50})$/",
			
	);
	*/
	
	/**
	 * Authenticate api post requests.
	 * @todo add creditial in header check also.
	 * @return boolean
	 */
	public function apiauthenticate($data){
		// Pull in affiliate model
		App::import('Model','Affiliate');
		$affiliate = new Affiliate();
		
		// Are the authentication values posted in?
		if(isset($data['api_id']) && isset($data['api_key'])){
			$auth = $affiliate->find('first', array('conditions' => array('Affiliate.remote_id'=>$data['api_id'], 'Affiliate.api_key'=>$data['api_key'], 'Affiliate.status_id'=>5),'contain'=>'AffiliateIp.ip="'.$data['api_ip'].'"'));
			$result = (count($auth['AffiliateIp']) > 0) ? true : false;

			//If the IP lookup fails, we log it in the app/tmp/error.log file.
			if($result === false){ $this->log('Auth Error');}
				return $result;
		}else{
			return false;
		}
	}
		
	public function jsonresponse($response){
		$response['response_date'] = date('Y-m-d H:i:s');
		return str_replace('\\',"", json_encode($response));
	}
	
	public function getPostKey($campaign_id)
	{
		
		$xml_str = $this->exportCampaign($campaign_id);
		$xml_cmp = simplexml_load_string($xml_str);
		
		$post_key = $xml_cmp->campaigns->campaign->submission_options->post_key;
		
		
		return $post_key;
	}
	
	
	public function exportCampaign($campid)
	{
		
		$api_key = $this->api_key;
		$api_url = "http://clickmediaportal.com/api/6/export.asmx/Campaigns?api_key=$api_key&campaign_id=$campid&offer_id=0&affiliate_id=0".
		"&account_status_id=1&media_type_id=0&start_at_row=1&row_limit=0&sort_field=0&sort_descending=TRUE";
	
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, $api_url );
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		$response = curl_exec($ch);
		curl_close($ch);
		
		return $response;		
	}
	
	public function exportOffer($offerid)
	{
		$this->log('Remove Me: 97');
		$api_key = $this->api_key;
		
		$api_url = "http://clickmediaportal.com/api/5/export.asmx/Offers?api_key=$api_key&offer_id=$offerid&offer_name=&advertiser_id=0&vertical_id=0".
		"&offer_type_id=0&media_type_id=0&offer_status_id=0&tag_id=0&start_at_row=1&row_limit=0&sort_field=0&sort_descending=TRUE";
		

		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, $api_url );
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		$response = curl_exec($ch);
		curl_close($ch);
		
		return $response;		
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
	
	
	
	
	
	public function exportConversion($offerid, $campaignid, $affid)
	{
		
		$api_key = $this->api_key;
		date_default_timezone_set('EST');
		
		
		$api_url = "http://clickmediaportal.com/api/11/reports.asmx/Conversions?api_key=".urlencode($api_key)."&start_date=".urlencode(date("m/d/Y H:i:s", strtotime("+10 minutes")))."&".
					"end_date=".urlencode(date("m/d/Y H:i:s", strtotime("+70 minutes")))."&".
					"conversion_type=all&".
					"event_id=0&".
					"affiliate_id=$affid&".
					"advertiser_id=0&".
					"offer_id=$offerid&".
					"affiliate_tag_id=0&".
					"advertiser_tag_id=0&".
					"offer_tag_id=0&campaign_id=$campaignid&".
					"creative_id=0&".
					"price_format_id=0&".
					"disposition_type=all&".
					"disposition_id=0&".
					"affiliate_billing_status=all&advertiser_billing_status=all&test_filter=both&start_at_row=0&row_limit=10000&sort_field=conversion_date&sort_descending=false";
		
		//date_default_timezone_set('UTC');

		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, $api_url );
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		$response = curl_exec($ch);
		curl_close($ch);
		
		date_default_timezone_set('America/New_York');
		return $response;		
	}


	public function addToReceivableQueue($bucket_id, $request_id, $amount, $margin, $payout, $cake_lead, $offer_id)
	{
		$db = new DBpdo();
		$sql = 'INSERT INTO CM_SERVICE_RECEIVABLE_QUEUE (BUCKET_ID,REQUEST_ID,AMOUNT,MARGIN,PAYOUT, CAKE_LEAD_ID, OFFER_ID) VALUES (:bucket_id,:request_id,:amount, :margin, :payout, :cake_lead, :offer_id)';
		echo "\n\nINSERT INTO CM_SERVICE_RECEIVABLE_QUEUE (BUCKET_ID,REQUEST_ID,AMOUNT,MARGIN,PAYOUT, CAKE_LEAD_ID, OFFER_ID) VALUES ('$bucket_id','$request_id','$amount', '$margin', '$payout', '$cake_lead', '$offer_id')\n\n";
		try
		{
			$db->query( $sql );	
			$db->bind( ':bucket_id',$bucket_id );
			$db->bind( ':request_id',$request_id );
			$db->bind( ':amount',$amount );
			$db->bind( ':margin',$margin );
			$db->bind( ':payout',$payout );
			$db->bind( ':cake_lead',$cake_lead );
			$db->bind( ':offer_id',$offer_id );
			$db->execute();
		} catch (Exception $e) {
			//print_r($e);
		}
	}
	
	
	//Remove fields from post that are empty
	public function cleanArray($array){
		$temp_array = array();
		foreach($array as $k=>$v){
			if(trim($v) != "" || trim($v) !== ""){
				$temp_array[$k] = $v;				
			}
		}
		return $temp_array;
	}
	
	
	public function microtime_float(){
	    list($usec, $sec) = explode(" ", microtime());
	    return ((float)$usec + (float)$sec);
	}
	
	
	public function print_msg($msg){
		echo $msg;
		echo "<br><br><br>\n\n\n";
	}	
		
	
	public function formatDecimal($decimal){
		
		$rounded = round($decimal, 2);
		$rounded_precision = number_format($rounded, 2, '.', '');
		return $rounded_precision;
		
	}



	public function firePixel($pixel_url){
		
		$agent= 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, $agent);
		curl_setopt($ch, CURLOPT_URL,$pixel_url);
		$result=curl_exec($ch);
				
		
		return $result;
		
		
	}
	
		/**
	 * Make sure we can connect to Api
	 */
	public function heartBeat(){
		$socket = new HttpSocket(array('ssl_verify_peer'=>false, 'ssl_verify_host'=>false));
		$url = 'https://api.leadstudio.com/heartBeat';
		$data = json_encode(array('heartbeat'=>'true'));
		$request = array(	'header' => array(	'Content-Type'	=> 'application/json',
												'X-Api-Key' 	=> 'keystone314',
												'X-Api-Id'		=> '0' 
										)
						
		);
		
		$response = $socket->post($url, $data, $request);
		
		$status_code = $response->code;
		
		$rsp_array = json_decode($response->body, true);
		
		if($rsp_array['status'] == 'true' && $status_code == '200'){
			return array('status'=>'true');
		}else{
			return false;
		}
	}

	
}