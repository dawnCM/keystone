<?php
/**
 * Service Controller
 * Services can be called from outside domains, because of this all services return in jsonp format.
 * All services are restful.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     TBD
 * @link          https://github.com/AdLink360/keyStone/wiki/ServiceController
 * @package       app.Controller
 * @since         keyStone v1.0
 * @license       TBD
 */
App::uses('AuthComponent', 'Controller/Component');
App::uses('HttpSocket', 'Network/Http');
class ServiceController extends AppController 
{
	public $uses = array('Service','StateZip','BankRouting', 'NpaNpx', 'Holiday', 'EmailValidateStorage', 'LeadTrack', 'Track', 'Cake', 'ReportTrack', 'Affiliate','ListManagement','SiteConfiguration','SpringLeaf');
	
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow();
		
		// All posts must authenticate.
		if($this->request->is('post')){
			if($this->request->header('X-Api-Key')){
				$authdata['api_id'] = $this->request->header('X-Api-Id');
				$authdata['api_key'] = $this->request->header('X-Api-Key');
			}
			if($this->Service->serviceAuthenticate($authdata) === false){
				$this->response->statusCode(401);
				echo '401 Unauthorized';
				$this->_stop();
			}
		}
	}
		
	/**
	 * Get a state and city combination for the provided zipcode.  A callback function can be passed.
	 * @param integer $zip
	 * @param string $callback
	 * @return jsonp
	 */
	public function getCityStatebyZip($zip, $callback='callback') {
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		$response = array('status'=>'error', 'message'=>'', 'data'=>'');
		
		$cache['hash'] = md5('statezip_'.$zip);
		$cache['value'] = false;
		$cache['value'] = Cache::read($cache['hash']);
					
		if($cache['value'] === false) {
			$this->StateZip->setDataSource('slave');
			$response['data'] = $this->StateZip->getStateCity($zip);

			$cache['value'] = $response['data'];
			Cache::write($cache['hash'],$cache['value'],'1w');
		} else {
			$response['data'] = $cache['value'];
		}
				
		if($response['data']) {
			$response['status'] = 'success';
		} else {
			$response['status'] = 'error';
			$response['message'] = 'Zipcode not found.';
		}
		
		return $this->Service->jsonpresponse($response, $callback);
	}
	
	/**
	 * Create a track id for the incoming lead.  This must be called before you can use trackLead().
	 * @param integer $request_id
	 * @param integer $offer_id
	 * @param integer $campaign_id
	 * @param integer $affiliate_id
	 * @param string $callback
	 */
	public function trackStart() {
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		$response = array('status'=>'error', 'message'=>'', 'data'=>'');
		
		if($this->request->is('post')) {
			$this->request->data['Track'] = $this->request->data;	
		}
				
		if($this->Track->save($this->request->data)) {
			$response['status'] = 'success';
			$response['data'] = array('track_id'=>$this->Track->id);
		} else {
			$response['message'] = 'Track ID could not be created.';
		}
		
		return $this->Service->jsonresponse($response);
	}
	
	/**
	 * Insert lead tracking into the DB.  Method trackStart() must be called first to generate a track_id.
	 * Track Multiple Variables - POST: service.leadstudio.com/trackLead/{track_id}
	 * @param string $track_id
	 * @param array $data
	 * @param string $callback
	 */
	public function trackLead($track_id) {
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
				
		$response = array('status'=>'error', 'message'=>'', 'data'=>'');
		
		if($this->request->is('post')){
			$data = $this->request->data;
			$json = json_encode($data);
			$response['data'] = $this->Track->writeLead($track_id, $json);
		}
		
		if($response['data'] === true) {
			$response['status'] = 'success';
		} else {
			$response['message'] = 'Invalid request.';
		}
		
		return $this->Service->jsonresponse($response);
	}
		
	/**
	 * Returns an object of bank information for the gtiven aba routing number.
	 * @param integer $aba
	 * @param string $callback
	 * @return jsonp
	 */
	public function getBankInfobyABA($aba, $callback='callback') {
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		$response = array('status'=>'error', 'message'=>'', 'data'=>'');
		
		$cache['hash'] = md5('getbankinfo_'.$aba);
		$cache['value'] = false;
		$cache['value'] = Cache::read($cache['hash']);

		if($cache['value'] === false) {			
			$this->BankRouting->setDataSource('slave');
			$response['data'] = $this->BankRouting->getBankInfo($aba);
			$cache['value'] = $response['data'];
			Cache::write($cache['hash'],$cache['value'],'1w');
		} else {
			$response['data'] = $cache['value'];
		}
			
		if($response['data']) {
			$response['status'] = 'success';
		} else {
			$response['status'] = 'error';
			$response['data'] = 'Bank ABA not found.';
		}			
		
		return $this->Service->jsonpresponse($response, $callback);
	}

	/**
	 * Return true if the npa/npx combonation exists in the database.
	 * @param integer $npa
	 * @param integer $npx
	 * @param string $callback
	 * @return jsonp
	 */
	public function npaNpxCheck($npa, $npx, $callback='callback') {
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		$response = array('status'=>'error', 'message'=>'', 'data'=>'');		
		
		$npanpx = $npa.$npx;
		$cache['hash'] = md5('npanpx_'.$npanpx);
		$cache['value'] = false;
		$cache['value'] = Cache::read($cache['hash']);
			
		if($cache['value'] === false) {
			$this->NpaNpx->setDataSource('slave');
			$response['data'] = $this->NpaNpx->isMatch($npanpx);
				
				if (!empty($response['data'])) {
					$cache['value'] = $response['data'];
					Cache::write($cache['hash'],$cache['value'],'1w');
				}				
				
			} else {
				$response['data'] = $cache['value'];
			}
	
			if(!empty($response['data'])) {
				$response['status'] = 'success';
			}else{
				$response['status'] = 'error';
				$response['data'] = 'NPA/NPX search failed.';
			}			
		
		return $this->Service->jsonpresponse($response, $callback);		
	}
	
	/**
	 * Return list of holidays for the given time frame
	 * @param number $days
	 * @param string $callback
	 * @return jsonp
	 */
	public function getHoliday($days=30, $callback='callback') {
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		$response = array('status'=>'error', 'message'=>'', 'data'=>'');
		
		$date = date('Ymd');
		
		$cache['hash'] = md5('holiday_'.$date.'_'.$days);
		$cache['value'] = false;
		$cache['value'] = Cache::read($cache['hash']);
			
		if($cache['value'] === false) {
			$this->Holiday->setDataSource('slave');
			$response['data'] = $this->Holiday->getHolidayList($days);
		
			if(!empty($response['data'])) {
				$cache['value'] = $response['data'];
				Cache::write($cache['hash'],$cache['value'],'1w');
			}
		} else {
			$response['data'] = $cache['value'];
		}
		
		if(!empty($response['data'])) {
			$response['status'] = 'success';
		}else{
			$response['status'] = 'error';
			$response['data'] = 'No dates found.';
		}
		
		return $this->Service->jsonpresponse($response, $callback);
	}
	
	/*
	 * Return ancillary data
	 * @param - keystone site id
	 * @return json
	 */
	public function getAncillaryPops($site_id){
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		$response = array('status'=>'error', 'message'=>'', 'data'=>'');
	
		$query = $this->SiteConfiguration->find('all', array( 'conditions' => array('SiteConfiguration.site_id' => $site_id, 'Ancillary.status'=>"1") ));
	
		if(!empty($query)){
			//I need the array in the same format.  No numeric index when only one record.
			if(isset($query['SiteConfiguration'])){
				$query = array($query);
			}
			$response['status'] = "success";
			$response['body'] = $query;
		}
	
		return json_encode($response);
	}
	
	/**
	 * Email Validation & Storage
	 */
	public function emailValidation($email, $callback='callback', $type='brightVerify') {
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		$response = array('status'=>'error', 'message'=>'', 'data'=>'');
		
		$this->EmailValidateStorage->set('email', $email);
		
		if($this->EmailValidateStorage->validates()){
			$result = $this->EmailValidateStorage->getEmail($email);
			
			if(count($result)>0){
				$response['status'] = 'success';
				$response['data'] = $result['EmailValidateStorage'];
			}else{
				$response['status'] = 'success';
				$response['data'] = $this->EmailValidateStorage->$type($email);
			}
		}else{
			$response['status'] = 'error';
			$response['message'] = 'Email address was an invalid format.';
		}

		// Return the response
		return $this->Service->jsonpresponse($response, $callback);
	}
	
	/**
	 * Payment Information
	 */
	public function paymentInformation($campaign_id) {
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		$response = array('status'=>'error', 'message'=>'', 'data'=>'');
		
		$payment_info = $this->Cake->paymentType($campaign_id);
			if(is_array($payment_info)){
				$response['status'] = 'success';
				$response['data'] = $payment_info; 
			}else{
				$response['status'] = 'error';
				$response['message'] = 'Payment Information wan not found';
			}
		
		// Return the response
		echo json_encode($response);
		exit;
	}
	
	public function testr(){
		$this->layout = null;
		$this->autoRender = false;
		
		$data = array();
		$url = 'https://api.sandbox.bills.com/personal-loan/lead.json';
		
		$data['first_name'] = $this->request->data['first_name'];
		$data['last_name'] = $this->request->data['last_name'];
		$data['email'] = $this->request->data['email'];
			
		$data['mailing_address']['street'] = $this->request->data['street'];
		$data['mailing_address']['street2'] = $this->request->data['street2'];
		$data['mailing_address']['city'] = $this->request->data['city'];
		$data['mailing_address']['state'] = $this->request->data['state'];
		$data['mailing_address']['zip'] = $this->request->data['zip'];
			
		$data['day_phone'] = $this->request->data['day_phone'];
		$data['credit_rating'] = $this->request->data['credit_rating'];
		$data['date_of_birth'] = $this->request->data['date_of_birth'];
		$data['annual_income'] = $this->request->data['annual_income'];
		$data['loan_amount'] = $this->request->data['loan_amount'];
		$data['loan_purpose'] = $this->request->data['loan_purpose'];
		$data['employment_status'] = $this->request->data['employment_status'];
		$data['citizenship_status'] = $this->request->data['citizenship_status'];
		$data['employer_name'] = $this->request->data['employer_name'];
		$data['has_checking_account'] = $this->request->data['has_checking_account'];
			
		$data['tracking']['unique_identifier'] = $this->request->data['unique_identifier'];
		$data['tracking']['ip_address'] = $this->request->data['ip_address'];
			
		$json = json_encode($data);
			
		//$HttpSocket = new HttpSocket();
		//$HttpSocket->configAuth('Basic', 'adlink_360', 'iK9YxQ8yYvvmB0Y19zA1CNp5O0i7lf7d');
		echo "<pre>";
		//print_r($HttpSocket->post($url, $json));
	}
	
	/**
	 * Handle internal post.
	 * Specifically for force-sales through cake, they still need a url to post to that responds.
	 * This takes place of the securecorplink service that CM1 used.
	 */
	public function internalStubPost() {
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		$response = array('status'=>'error', 'message'=>'', 'data'=>'');
				
		if($this->request->is('post')){
			$status = isset($this->request->data['status']) ? $this->request->data['status'] : 'approved';
			$price = isset($this->request->data['price']) ? $this->request->data['price'] : '0.00';
			$redirect = isset($this->request->data['redirect']) ? $this->request->data['redirect'] : 'https://leadstudio.com';
			
			switch($status){
				case 'declined':
					$response['status'] = 'declined';
					$response['message'] = 'lead could not be sold';
					break;
				case 'error':
					$response['status'] = 'error';
					$response['message'] = 'an error has occured';
					break;
				case 'timeout':
					$response['status'] = 'timeout';
					$response['message'] = 'no response returned';
					break;
				case 'approved':
				default:
					$response['status'] = 'approved';
					$response['message'] = 'lead accepted';
					break;
			}
			$response['data']=array('redirect'=>$redirect, 'price'=>$price);
		}else{
			$response['message'] = 'Invalid request type.';
		}
		
		return json_encode($response);
	}
	
	/**
	 * Post to LeadByte List
	 * @param string $email
	 * @param string $firstname
	 * @param string $lastname
	 * @param string $ip
	 * @param string $site_url
	 * @return jsonp
	 */
	public function send2Leadbyte($email,$firstname,$lastname,$ip,$site_url) {
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		$response = array('status'=>'error', 'message'=>'', 'data'=>'');
	
		$url = 'https://flatsixmedia.leadbyte.co.uk/api/submit.php?campid=CMINITIALUSCASH&sid=02';
		$options = array();
		$data= array();
		$data['email'] = $email;
		$data['firstname'] = $firstname;
		$data['lastname'] = $lastname;
		$data['ipaddress'] = $ip;
	
		$data['source'] = $site_url;
		$data['Opt-In_Date'] = date('d/m/Y');
	
	
		$socket = new HttpSocket(array('ssl_verify_host'=>false));
		$response = $socket->post($url, $data, $options);
	
		return $this->Service->jsonresponse($response->body);
	}
	
	/**
	 * For buyer contract post that require handling Cake cannot do.
	 */
	public function buyerStubPost() {
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		if($this->request->is('post')){
			switch($this->request->data['buyer']){
				case 'lenderedge':
				$redirect = 'https://www.loanme.com/ALLSTAR/LMP/1409/redirect/index.html?trackingid=4013&phone=844-344-9646&subid=&subaffiliateId='.$this->request->data['ckm_subid'].'&FirstName='.$this->request->data['first_name'].'&LastName='.$this->request->data['last_name'].'&EmailAddress='.$this->request->data['email_address'].'&HomePhone='.$this->request->data['phone_home'].'&State='.$this->request->data['state'];
				
				$HttpSocket = new HttpSocket();
				unset($this->request->data['buyer']);
				$apiPost = $HttpSocket->post('http://r.lenderedge.com/pre/', $this->request->data);
				
				//Yes, attaching this string to the end of the XML response makes it invalid XML.
				//Cake does not care as it treats the response as a text string anyways.
				if(strstr($apiPost, '>success')){
					$apiPost .= '<redirect_true>'.$redirect.'</redirect_true>';
				}
					
				return $apiPost;
				break;
				
				case 'pivot':
				$apiPost['redirect'] = 'https://www.pivothealth.com/product/short-term-health-insurance/agent/57142/?agent_tracking_id='.$this->request->data['request_id'].'&utm_source=57142&utm_medium=FeeCM&utm_campaign=agents';
				$apiPost['status'] = 'success';
				$apiPost['price'] = $this->request->data['price'];
				return json_encode($apiPost);
				break;
				
				case 'hiquote':
				$apiPost['redirect'] = 'https://www.hiiquote.com/quote/quote.php?Plan_ID=109&code=A10850040000000';
				$apiPost['status'] = 'success';
				$apiPost['price'] = $this->request->data['price'];
				return json_encode($apiPost);
				break;
				
				case 'obama':
				$apiPost['redirect'] = 'http://leadstudiotrack.com/?a=104&c=1881&s1=';
				$apiPost['status'] = 'success';
				return json_encode($apiPost);
				break;
							
				case 'affiliateroi':
				$apiPost['redirect'] = 'https://www.my411.com/list.php?c='.$this->request->data['id'].'&k='.$this->request->data['affiliate_id'].'&z1='.$this->request->data['zip'].'&z3='.$this->request->data['state'];
				$apiPost['status'] = 'success';
				$apiPost['price'] = $this->request->data['price'];
				return json_encode($apiPost);
				break;
				case 'luav':				
				$apiPost['redirect'] = 'https://www.betterloanchoice.com/loan/click/?source=clickmedia&mobile=0'.'&source_id='.$this->request->data['source_id'].'&fName='.$this->request->data['fName'].'&lName='.$this->request->data['lName'].'&email='.$this->request->data['email'].'&homeZip='.$this->request->data['homeZip'];	
				$apiPost['status'] = 'success';
				$apiPost['price'] = $this->request->data['price'];                
				return json_encode($apiPost);             
				break;
				case 'netaktion':
					$apiPost['redirect'] = 'http://netaktiontrack.info/?a=16&c=416&p=c&s1='.$this->request->data['affiliate_id'];
					$apiPost['status'] = 'success';
					$apiPost['price'] = $this->request->data['price'];
				return json_encode($apiPost);
				break;
				case 'threesidesmedia':
					$address1 = ($this->request->data['Address2'] !='') ? '&Address2='.$this->request->data['Address2'] : "";
					$apiPost['redirect'] = 'https://www.fastdayloans.com/prepopulate.php?aid=164&sid='.$this->request->data['subID'].'&FirstName='.$this->request->data['FirstName'].'&LastName='.$this->request->data['LastName'].'&Email='.$this->request->data['Email'].'&State='.$this->request->data['State'].'&Address1='.$this->request->data['Address1'].$address1.'&City='.$this->request->data['City'].'&PostalCode='.$this->request->data['PostalCode'].'&PhoneNumber='.$this->request->data['PhoneNumber'];
					$apiPost['status'] = 'success';
					return json_encode($apiPost);

					break;
				case 'golden':
				$apiPost['redirect'] = 'https://offer.fast5kloans.com?aid=10181&acid=15&subid='.$this->request->data['affiliate_id'].'&hpostal='.$this->request->data['zip'].'&fname='.$this->request->data['first_name'].'&lname='.$this->request->data['last_name'].'&email='.$this->request->data['email'];
				$apiPost['status'] = 'success';
				$apiPost['price'] = $this->request->data['price'];
				return json_encode($apiPost);
				break;
				
				case 'revpie':
				$apiPost['redirect'] = 'http://consumer-application.com/winshiplending/';
				$apiPost['status'] = 'success';
				$apiPost['price'] = $this->request->data['price'];
				return json_encode($apiPost);
				break;
				
				case 'moneybee':
					//https://www.moneybee.com/redirect.asp?guid=M4NDIWP3TM1Z
				$apiPost['redirect'] = 'https://www.moneybee.com/redirect.asp?guid=Y3GU4ZLQNRSB&sid='.$this->request->data['affiliate_id'].'&name='.$this->request->data['full_name'].'&email='.$this->request->data['email_address'].'&phone='.$this->request->data['phone_primary'].'&Bill_Address='.$this->request->data['address_1'].'&Bill_Address2='.$this->request->data['address_2'].'&Bill_City='.$this->request->data['city'].'&Bill_State='.$this->request->data['state'].'&Bill_Zip='.$this->request->data['zip'];
				$apiPost['status'] = 'success';
				$apiPost['price'] = $this->request->data['price'];
				return json_encode($apiPost);
				break;
				
				case 'fraktle':
					$apiPost['redirect'] = 'https://www.checkfreescore.com/redirect.asp?guid=QR1NSAKNQKTW&sid='.$this->request->data['affiliate_id'].'&sid2='.$this->request->data['sub1'].'&cid=';
					$apiPost['status'] = 'success';
					$apiPost['price'] = '0.80';
					return json_encode($apiPost);
					break;
				
				case 'golden2':
				$apiPost['redirect'] = 'https://offer.epcvip.com/?aid=500235&acid=7&subid='.$this->request->data['affiliate_id'].'&hpostal='.$this->request->data['zip'].'&fname='.$this->request->data['first_name'].'&lname='.$this->request->data['last_name'].'&email='.$this->request->data['email_address'].'';
				$apiPost['status'] = 'success';
				$apiPost['price'] = $this->request->data['price'];
				return json_encode($apiPost);
				break;
					
				case 'universal':
				$apiPost['redirect'] = 'http://interest.interestservices.com/flow_V2/?bgc=%233298cc&imgt=Lender+Options&hdc=%23FFFFFF&c1='.$this->request->data['c1'];
				$apiPost['status'] = 'success';
				$apiPost['price'] = $this->request->data['price'];
				return json_encode($apiPost);
				
				case 'efolks':
					$url = $this->request->data['url'];
					
					//post from CAKE has the same names as the fields needed for efolks
					$apiPost = $this->request->data;
					
					unset($apiPost['url']);
					unset($apiPost['buyer']);
					unset($apiPost['price']);
						
					//$cid_map = array("36" => "1006", "43" => "1000", "16" => "1001", "39" => "1002", "35" => "1003", "37" => "1004", "49" => "1005" );
						
					switch(true){
						case strpos($apiPost['referrer'],'winship'):
							$apiPost['cid'] = '1006';
							break;
						case strpos($apiPost['referrer'],'secure'):
							$apiPost['cid'] = '1005';
							break;
						case strpos($apiPost['referrer'],'liberty'):
							$apiPost['cid'] = '1004';
							break;
						case strpos($apiPost['referrer'],'fcc'):
							$apiPost['cid'] = '1003';
							break;
						case strpos($apiPost['referrer'],'chesap'):
							$apiPost['cid'] = '1002';
							break;
						case strpos($apiPost['referrer'],'access'):
							$apiPost['cid'] = '1001';
							break;
						case strpos($apiPost['referrer'],'peer'):
							$apiPost['cid'] = '1000';
							break;
					}
					
					//Cid contains the offer id.  Use it with cid_map array to get the cid.
					//$apiPost['cid'] ="1006";// $cid_map[$apiPost['cid']];
					
					$apiPost['password'] = 'p@ssword';
					
					$HttpSocket = new HttpSocket(array('ssl_verify_peer' => false, 'ssl_verify_host' => false));
					$response = $HttpSocket->post($url, $apiPost);
					$xml = simplexml_load_string($response->body);
					return json_encode($xml);
						
					break;
						
				case 'offerannex':
					$key = $this->request->data['key'];
					$url = $this->request->data['url']."?key=".$key;
					$transaction_url = $this->request->data['transaction_url']."?key=".$key;;
					$apiPost = $this->request->data;
						
					//post from CAKE has the same names as the fields needed for offer annex
					unset($apiPost['url']);
					unset($apiPost['buyer']);
					unset($apiPost['transaction_url']);
					unset($apiPost['key']);
						
					//Get transaction id
					$HttpSocket = new HttpSocket(array('ssl_verify_peer' => false, 'ssl_verify_host' => false));
					$response1 = $HttpSocket->post($transaction_url, array('ip' => $apiPost['client_ip']));
					$response1_info = json_decode($response1->body, true);
					$transaction_id = $response1_info['transaction'];
					$apiPost['trans_id'] = $transaction_id;
						
					//Main post
					$HttpSocket2 = new HttpSocket(array('ssl_verify_peer' => false, 'ssl_verify_host' => false));
					$response2 = $HttpSocket2->post($url, $apiPost, array("header" => array("accept"=>"application/json, text/javascript","Content-Type"=>"application/x-www-form-urlencoded")));
					$response2_info = $response2->body;
					return $response2_info;
					
				break;
				
				case 'firstloanchoice':
					$url = $this->request->data['url'];
					
					$lead = array(
							'first_name'=>$this->request->data['first_name'],
							'last_name'=>$this->request->data['last_name'],
							'email'=>$this->request->data['email'],
							'phone'=>$this->request->data['phone'],
							'zip'=>$this->request->data['zip']);
					
					$json = json_encode($lead);
				
					$socket = new HttpSocket(array('ssl_verify_peer' => false, 'ssl_verify_host' => false));
					$response = $socket->post($url, $json, array('header'=>array('Content-Type'=>'application/json')));
					
					//$this->log('--FirstLoanChoice--');
					//$this->log($json);
					
					if($response->isOk()){
						$apiPost['redirect'] = 'https://www.firstloanchoice.com/success.php';
						$apiPost['status'] = 'success';
						$apiPost['price'] = $this->request->data['price'];
					}else{
						$apiPost['status'] = 'declined';
					}
					
					return json_encode($apiPost);
				break;
				
				case 'clickfunding':
				$url = "http://www.grpads.com/api/cpcpayable/?email={$this->request->data['email']}&ipaddress={$this->request->data['ipaddress']}&offerid=2734";
				
				$HttpSocket = new HttpSocket();

				$myresponse = $HttpSocket->get($url);
				$pos = strpos($myresponse, 'accept');
				if($pos === false){
					$apiPost['status'] = 'reject';
				}else{
					$apiPost['status'] = 'accept';
					$apiPost['price']=$this->request->data['price'];
					$apiPost['redirect'] = "http://yrepdeliver.com/track/?offerid={$this->request->data['offerid']}&affiliateid={$this->request->data['id']}&subid={$this->request->data['subid']}&email={$this->request->data['email']}";
				}
												
				return json_encode($apiPost);
				break;
				
				case 'testlead':
					$apiPost['redirect']='http://clickmedia.com';
					$apiPost['price']=$this->request->data['price'];
					$apiPost['status'] = $this->request->data['status'];
					return json_encode($apiPost);
				break;
				
				case '3side':
					//3sides has no posting system, get via URL only.
					$url = 'https://leadpathapp.com/process.aspx';
					$query = 'pd=59&USERID=ClickMedia&password=Lu3As29kap&PR=113&Source='.$this->request->data['subid'].
					'&FN='.$this->request->data['firstname'].
					'&LN='.$this->request->data['lastname'].
					'&EM='.$this->request->data['email'].
					'&A1='.$this->request->data['address1'].
					'&A2='.$this->request->data['address2'].
					'&PC='.$this->request->data['zip'].
					'&HT='.$this->request->data['homephone'].
					'&IP='.$this->request->data['ipaddress'].
					'&BANK_ROUTING_NUMBER='.$this->request->data['bankrouting'].
					'&CHECKING_ACCOUNT_NUMBER='.$this->request->data['bankaccount'].
					'&BIRTHDATE='.$this->request->data['dob'].
					'&SSN='.$this->request->data['ssn'].
					'&HOMEOWNER='.$this->request->data['ownrent'].
					'&YEARSINHOME='.$this->request->data['homelengthyears'].
					'&MONTHSINHOME='.$this->request->data['homelengthmonths'].
					'&CONTACT_TIME=1'.
					'&DRIVERS_LICENSE='.$this->request->data['driverslicense'].
					'&DRIVER_STATE='.$this->request->data['driverslicensestate'].
					'&US_CITIZEN=1'.
					'&INCOME_SOURCE='.$this->request->data['incomesource'].
					'&DIRECT_DEPOSIT='.$this->request->data['directdeposit'].
					'&EMPLOYER='.$this->request->data['employername'].
					'&WT='.$this->request->data['workphone'].
					'&OCCUPATION=unknown'.
					'&ET='.$this->request->data['workphone'].
					'&EMPLOYER_A1='.$this->request->data['employeraddress'].
					'&EMPLOYER_PC='.$this->request->data['employerzip'].
					'&MONTHLYINCOME_NET='.$this->request->data['netincome'].
					'&INCOME_FREQUENCY='.$this->request->data['payfrequency'].
					'&INCOME_DATE1='.$this->request->data['paydate1'].
					'&US_MILITARY_MEMBER='.$this->request->data['military'].
					'&BANK_NAME='.$this->request->data['bankname'].
					'&BANK_ACCOUNT_TYPE='.$this->request->data['accounttype'].
					'&BANK_ACCOUNT_DURATION='.$this->request->data['banktime'].
					'&REQUESTED_PAYDAY_LOAN='.$this->request->data['loanamount'].
					'&TIME_EMPLOYED='.$this->request->data['employmentlength'].
					'&WEBSITE='.$this->request->data['website'];
					
					$HttpSocket = new HttpSocket(array('ssl_verify_peer' => false, 'ssl_verify_host' => false));
					$response = $HttpSocket->get($url,$query);
					$pieces = explode('&',$response->body);
					
					switch(true){
						case $pieces[0] == 'RESULT=Rejected':
							$return['msg'] = $pieces[3];
							$return['status'] = 'declined';
						break;
						
						case $pieces[0] == 'RESULT=Success':
							$redirect = explode('=',$pieces[5]);
							$return['redirect'] = $redirect[1];
							$return['status'] = 'accepted';
						break;
						
						case $pieces[0] == 'RESULT=Error':
							$return['msg'] = $pieces[6];
							$return['status'] = 'error';
						break;
						
						default:
							$return['msg'] = 'Unknown Error';
							$return['status'] = 'error';
						break;
					}
										
					return json_encode($return);

				break;
				
				case 'skyfin':
					$url = 'http://lms.zocaloans.com/plm.net/lead/xml/Processor.ashx';
					$duedate = date('m/d/Y', strtotime('+6 months'));
					
					// swapping between credentials because cake can't handle round robin at the contract level.
					// this is not true round robin, but gives roughly the same effect.
					$swap = rand(0,1);
					switch(true){
						case $this->request->data['contracttype'] == "5":
							if($swap === 0){
								$credentials['username'] = 'zlcm5';
								$credentials['password'] = 'zocapw5';
								$credentials['storeid'] = '33';
							}else{
								$credentials['username'] = 'zlcm4';
								$credentials['password'] = 'zocapw4';
								$credentials['storeid'] = '32';
							}
							$price = '5.00';
						break;
						
						case $this->request->data['contracttype'] == "10":

							if($swap === 0){
								 $credentials['username'] = 'zlcm7';
                                 $credentials['password'] = 'zocacm7';
                                 $credentials['storeid'] = '70';
                			}else{
								$credentials['username'] = 'zlcm6';
								$credentials['password'] = 'zocapw6';
								$credentials['storeid'] = '46';
				            }
                            $price = '10.00';
						break;
						
						case $this->request->data['contracttype'] == "30":
							if($swap === 0){
								$credentials['username'] = 'zlcm2';
								$credentials['password'] = 'zocapw2';
								$credentials['storeid'] = '30';
							}else{
								$credentials['username'] = 'zlcm3';
								$credentials['password'] = 'zocapw3';
								$credentials['storeid'] = '31';
							}
							$price = '30.00';
						break;
						
						/*case $this->request->data['contracttype'] == "35":
								$credentials['username'] = 'zlcm1';
								$credentials['password'] = 'zocapw1';
								$credentials['storeid'] = '29';
								$price = '35.00';
						break; */
					}
					
$bankname = str_replace('&',' and ', $this->request->data['bankname']);

$xml = '<?xml version="1.0" ?>
<LeadRequest>
	<Username>'.$credentials['username'].'</Username>
	<Password>'.$credentials['password'].'</Password>
	<StoreID>'.$credentials['storeid'].'</StoreID>
	<LeadAffiliate>'.$this->request->data['affiliateid'].'</LeadAffiliate>
	<RefID>'.$this->request->data['refid'].'</RefID>
	<ClientIpAddress>'.$this->request->data['ip_address'].'</ClientIpAddress>
	<Firstname>'.$this->request->data['firstname'].'</Firstname>
	<Lastname>'.$this->request->data['lastname'].'</Lastname>
	<SSN>'.$this->request->data['ssn'].'</SSN>
	<Email>'.$this->request->data['email'].'</Email>
	<DOB>'.$this->request->data['dob'].'</DOB>
	<DriversLicense>'.$this->request->data['dl'].'</DriversLicense>
	<DriversLicenseState>'.$this->request->data['dlstate'].'</DriversLicenseState>
	<Language>e</Language>
	<HomeAddress>'.$this->request->data['address1'].'</HomeAddress>
	<HomeCity>'.$this->request->data['city'].'</HomeCity>
	<HomeState>'.$this->request->data['state'].'</HomeState>
	<HomeZip>'.$this->request->data['zip'].'</HomeZip>
	<MailAddress>'.$this->request->data['address1'].'</MailAddress>
	<MailCity>'.$this->request->data['city'].'</MailCity>
	<MailState>'.$this->request->data['state'].'</MailState>
	<MailZip>'.$this->request->data['zip'].'</MailZip>
	<TimeAtAddress>'.$this->request->data['addresslength'].'</TimeAtAddress>
	<HomePhone>'.$this->request->data['phonehome'].'</HomePhone>
	<CellPhone>'.$this->request->data['phonehome'].'</CellPhone>
	<WorkPhone>'.$this->request->data['phonework'].'</WorkPhone>
	<BankInfo>
		<BankName>'.$bankname.'</BankName>
		<AbaNumber>'.$this->request->data['bankrouting'].'</AbaNumber>
		<CheckingAccount>'.$this->request->data['bankaccount'].'</CheckingAccount>
		<AccountToUse>C</AccountToUse>
		<AccountLength>'.$this->request->data['banktime'].'</AccountLength>
	</BankInfo>
	<EmploymentInfo>
		<MonthlyIncome>'.$this->request->data['netincome'].'</MonthlyIncome>
		<PayFrequency>'.$this->request->data['payfrequency'].'</PayFrequency>
		<IncomeType>'.$this->request->data['incometype'].'</IncomeType>
		<PayrollType>'.$this->request->data['payrolltype'].'</PayrollType>
		<NextPayDay>'.$this->request->data['paydate1'].'</NextPayDay>
		<SecondNextPayDay>'.$this->request->data['paydate2'].'</SecondNextPayDay>
		<Employer>'.$this->request->data['employer'].'</Employer>
		<EmploymentLength>'.$this->request->data['employmentlength'].'</EmploymentLength>
		<Address>'.$this->request->data['employeraddress'].'</Address>
		<City>'.$this->request->data['employercity'].'</City>
		<State>'.$this->request->data['employerstate'].'</State>
		<Zip>'.$this->request->data['employerzip'].'</Zip>
	</EmploymentInfo>
	<LoanInfo>
		<Amount>'.$this->request->data['loanamount'].'</Amount>
		<DueDate>'.$duedate.'</DueDate>
	</LoanInfo>
</LeadRequest>';
				
				$socket = new HttpSocket(array('ssl_verify_peer' => false, 'ssl_verify_host' => false));
				$response = $socket->post($url, $xml);
								
				$xml2 = simplexml_load_string($response->body);
				$response = json_decode(json_encode($xml2),true);
								
				if($response['Status'] == 'ERROR'){
					$return['status'] = 'error';
					$return['msg'] = $response['Errors']['Error'];
				}
				elseif($response['Status'] == 'REJECTED'){
					$return['status'] = 'declined';
					$return['msg'] = 'Lead Declined: '.$response['RejectReason'];
				}
				elseif($response['Status'] == 'ACCEPTED'){
					$return['status'] = 'accepted';
					$return['redirect'] = $response['RedirectURL'];
					$return['price'] = $price;
				}
				else{
					$return['status'] = 'error';
					$return['msg'] = 'Unknown Error';
				}
				
				return json_encode($return);
				
				break;

				case 'skyfinle':
				$url = 'https://zoca.digitaljobler.com/service/submit';
					$swap = rand(0,1);
					switch(true){
						case $this->request->data['contracttype'] =="5":
							if($swap === 0){
								$campaign['campaignID'] = '10';
							}else{
								$campaign['campaignID'] = '11';
							}
							$price = '5.00';
						break;
						
						case $this->request->data['contracttype'] == "10":
							if($swap === 0){
								$campaign['campaignID'] = '6';
					    	}else{
								$campaign['campaignID'] = '7';
						     }
					         $price = '10.00';
						break;
						
						case $this->request->data['contracttype'] == "30":
							if($swap === 0){
								$campaign['campaignID'] = '8';
							}else{
								$campaign['campaignID'] = '9';
							}
							$price = '30.00';
						break;
					}
					$bankName = str_replace('","',' and ', $this->request->data['bankName']);
					$query = 'campaignID='.$campaign['campaignID'].
					'&subID='.$this->request->data['subID'].
					'&subID2='.$this->request->data['subID2'].
					'&subID3='.$this->request->data['subID3'].
					'&subID4='.$this->request->data['subID4'].
					'&subID5='.$this->request->data['subID5'].
					'&subID6='.$this->request->data['subID6'].
					'&subID7='.$this->request->data['subID7'].
					'&subID8='.$this->request->data['subID8'].
					'&subID9='.$this->request->data['subID9'].
					'&subID10='.$this->request->data['subID10'].
					'&sourceURL='.$this->request->data['sourceURL'].
					'&ipAddress='.$this->request->data['ipAddress'].
					'&gender='.$this->request->data['gender'].
					'&firstName='.$this->request->data['firstName'].
					'&lastName='.$this->request->data['lastName'].
					'&streetAddress='.$this->request->data['streetAddress'].
					'&streetAddress2='.$this->request->data['streetAddress2'].
					'&city='.$this->request->data['city'].
					'&state='.$this->request->data['state'].
					'&zipCode='.$this->request->data['zipCode'].
					'&homePhone='.$this->request->data['homePhone'].
					'&workPhone='.$this->request->data['workPhone'].
					'&workPhoneExt='.$this->request->data['workPhoneExt'].
					'&mobilePhone='.$this->request->data['mobilePhone'].
					'&email='.$this->request->data['email'].
					'&dateOfBirth='.$this->request->data['dateOfBirth'].
					'&ssn='.$this->request->data['ssn'].
					'&ownRent='.$this->request->data['ownRent'].
					'&yearsAtResidence='.$this->request->data['yearsAtResidence'].
					'&monthsAtResidence='.$this->request->data['monthsAtResidence'].
					'&citizen='.$this->request->data['citizen'].
					'&licenseNumber='.$this->request->data['licenseNumber'].
					'&licenseState='.$this->request->data['licenseState'].
					'&incomeSource='.$this->request->data['incomeSource'].
					'&title='.$this->request->data['title'].
					'&employer='.$this->request->data['employer'].
					'&employerAddress='.$this->request->data['employerAddress'].
					'&employerCity='.$this->request->data['employerCity'].
					'&employerState='.$this->request->data['employerState'].
					'&employerZip='.$this->request->data['employerZip'].
					'&yearsAtEmployer='.$this->request->data['yearsAtEmployer'].
					'&monthsAtEmployer='.$this->request->data['monthsAtEmployer'].
					'&monthlyIncome='.$this->request->data['monthlyIncome'].
					'&loanAmount='.$this->request->data['loanAmount'].
					'&payMethod='.$this->request->data['payMethod'].
					'&payPeriod='.$this->request->data['payPeriod'].
					'&firstPayDate='.$this->request->data['firstPayDate'].
					'&secondPayDate='.$this->request->data['secondPayDate'].
					'&bankName='.$bankName.
					'&bankPhone='.$this->request->data['bankPhone'].
					'&bankAccountType='.$this->request->data['bankAccountType'].
					'&bankRoutingNumber='.$this->request->data['bankRoutingNumber'].
					'&bankAccountNumber='.$this->request->data['bankAccountNumber'].
					'&referencePrimaryName='.$this->request->data['referencePrimaryName'].
					'&referencePrimaryPhone='.$this->request->data['referencePrimaryPhone'].
					'&referencePrimaryRelation='.$this->request->data['referencePrimaryRelation'].
					'&referenceSecondaryName='.$this->request->data['referenceSecondaryName'].
					'&referenceSecondaryPhone='.$this->request->data['referenceSecondaryPhone'].
					'&referenceSecondaryRelation='.$this->request->data['referenceSecondaryRelation'].
					'&activeMilitary='.$this->request->data['activeMilitary'].
					'&optin='.$this->request->data['optin'].
					'&timeToCall='.$this->request->data['timeToCall'];

					$HttpSocket = new HttpSocket(array('ssl_verify_peer' => false, 'ssl_verify_host' => false));
					$response = $HttpSocket->get($url,$query);
					$pieces = explode(',',$response->body);

					switch(true){
						case $pieces[0] == 'REJECTED':
							$return['msg'] = $pieces[1];
							$return['status'] = 'declined';
						break;
						
						case $pieces[0] == 'ACCEPTED':
							$return['redirect'] = $pieces[1];
							$return['price']	= $price;
							$return['status'] 	= 'accepted';
						break;
						
						case $pieces[0] == 'POST_ERROR':
							$return['msg'] = $pieces[1];
							$return['status'] = 'error';
						break;
						
						default:
							$return['msg'] = 'Unknown Error';
							$return['status'] = 'error';
						break;
					}
					return json_encode($return);
				break;

				case 'cashwise':
					//Is this a test call
					if($this->request->data['test'] == '1'){
						$pingUrl = 'http://cashwise.leadspediatrack.com/ping.do?lp_test=1';
						$postUrl = 'http://cashwise.leadspediatrack.com/post.do?lp_test=1';
						$data['lp_test'] = '1';
					}else{
						$pingUrl = 'http://cashwise.leadspediatrack.com/ping.do';
						$postUrl = 'http://cashwise.leadspediatrack.com/post.do';
					}
					
					//Authentication
					$data['lp_campaign_id'] = $this->request->data['lpcampaignid'];
					$data['lp_campaign_key'] = $this->request->data['lpcampaignkey'];
					$data['lp_response'] = 'JSON';
															
					//Ping
					$data['ip_address'] = $this->request->data['ip_address'];
					$data['email_address'] = $this->request->data['email_address'];
					
					$HttpSocket = new HttpSocket(array('timeout'=>60,'ssl_verify_peer' => false, 'ssl_verify_host' => false));
					$headers = array();
					
					try{
						$response['ping'] = $HttpSocket->post($pingUrl, $data, array('header'=>$headers));
					}catch(Exception $e){
						$return['status'] = "ping error";
						$return['msg'] = $e->getMessage();
						return json_encode($return);
					}
					
					$pingResponse = json_decode($response['ping'],true);
				
					if($pingResponse['result'] == 'success'){
					
						//Post
						$data['lp_s1'] = $this->request->data['affiliate_id'];
						$data['lp_ping_id'] = $pingResponse['ping_id'];
						$data['first_name'] = $this->request->data['first_name'];
						$data['last_name'] = $this->request->data['last_name'];
						$data['phone_home'] = $this->request->data['home_phone'];
						$data['phone_work'] = $this->request->data['work_phone'];
						$data['address'] = $this->request->data['address'];
						$data['address2'] = $this->request->data['address2'];
						$data['city'] = $this->request->data['city'];
						$data['state'] = $this->request->data['state'];
						$data['zip_code'] = $this->request->data['zip'];
						$data['dob'] = $this->request->data['dob'];
						$data['ip_address'] = $this->request->data['ip_address'];
						
						try{
							$response['post'] = $HttpSocket->post($postUrl, $data, array('header'=>$headers));
							
						}catch(Exception $e){
							$return['status'] = "ping error";
							$return['msg'] = $e->getMessage();
							return json_encode($return);
						}
						
						$postResponse = json_decode($response['post'],true);
						
						if($postResponse['result'] == 'success'){
							$return['status'] = 'success';
							$return['price'] = $postResponse['price'];
							$return['redirect'] = 'https://www.tfctitleloans.com/title-loan-1991/';
							return json_encode($return);
						}else{
							$return['status'] = 'declined';
							$return['msg'] = $postResponse['msg'];
							return json_encode($return);
						}
					}else{
						$return['status'] = 'declined';
						$return['msg'] = $pingResponse['msg']." - ".$pingResponse['price'];
						return json_encode($return);
					}					
				break;
								
				case 'freedom':				
					$data = array();
					$url = $this->request->data['url'];
										
					$data['first_name'] = $this->request->data['first_name'];
					$data['last_name'] = $this->request->data['last_name'];
					$data['email'] = $this->request->data['email'];
					
					$data['mailing_address']['street'] = $this->request->data['street'];
					$data['mailing_address']['street2'] = $this->request->data['street2'];
					$data['mailing_address']['city'] = $this->request->data['city'];
					$data['mailing_address']['state'] = $this->request->data['state'];
					$data['mailing_address']['zip'] = $this->request->data['zip'];
					
					$data['day_phone'] = $this->request->data['day_phone'];
					$data['credit_rating'] = $this->request->data['credit_rating'];
					$data['date_of_birth'] = $this->request->data['date_of_birth'];
					$data['annual_income'] = ($this->request->data['annual_income']*12);
					$data['loan_amount'] = $this->request->data['loan_amount'];
					$data['loan_purpose'] = $this->request->data['loan_purpose'];
					$data['employment_status'] = $this->request->data['employment_status'];
					$data['citizenship_status'] = $this->request->data['citizenship_status'];
					$data['employer_name'] = $this->request->data['employer_name'];
					$data['has_checking_account'] = $this->request->data['has_checking_account'];
					
					$data['tracking']['unique_identifier'] = $this->request->data['unique_identifier'];
					$data['tracking']['ip_address'] = $this->request->data['ip_address'];
					
					$data['tracking']['utm_medium'] = $this->request->data['utm_medium'];
					$data['tracking']['utm_source'] = $this->request->data['utm_source'];
					$data['tracking']['utm_campaign'] = $this->request->data['utm_campaign'];
					
					$json = json_encode($data);
										
					$HttpSocket = new HttpSocket();
					$HttpSocket->configAuth('Basic', $this->request->data['username'], $this->request->data['password']);
					$response = $HttpSocket->post($url, $json, array('header'=>array('Content-Type'=>'application/json')));			
					$response = json_decode($response->body,true);
					//$this->log('FREEDOM');
					//$this->log($response);
					if(isset($response['status']) && $response['status'] == 'accepted'){
						$response['price'] = $this->request->data['price'];
						$response['redirect'] = 'https://global.leadstudio.com/freedomplus/accept.php?first_name='.$this->request->data['first_name'];
					}

					return json_encode($response);

				break;
				
				case 'springleaf':
					$url = 'https://api.onemain.financial/application';
					$username = $this->request->data['username'];
					$password = $this->request->data['password'];
					$version = $this->request->data['version'];
					unset($this->request->data['url']);
					unset($this->request->data['username']);
					unset($this->request->data['password']);
					unset($this->request->data['version']);
						
					$credit_score_estimate_name = $this->request->data['credit_score_estimate']; //poor,excellent,fair,good,unsure
					if($credit_score_estimate_name == 'excellent'){
						$credit_score_estimate = 800;
					}else if($credit_score_estimate_name == 'good'){
						$credit_score_estimate = 750;
					}else if($credit_score_estimate_name == 'fair'){
						$credit_score_estimate = 700;
					}else if($credit_score_estimate_name == 'poor'){
						$credit_score_estimate = 600;
					}else{
						$credit_score_estimate = 500;
					}
					$this->request->data['credit_score_estimate'] = $credit_score_estimate;
						
					$this->SpringLeaf->set($this->request->data); //pass data to model
					$poster_array = $this->SpringLeaf->buildPost();
						
					$json = json_encode($poster_array);
					
					$HttpSocket = new HttpSocket(array('timeout'=>60,'ssl_verify_peer' => false, 'ssl_verify_host' => false));
					$headers = array('x-api-username'=>$username,
							'x-api-password'=>$password,
							'Content-Type'=>'application/json',
							'HTTP_ACCEPT'=>'text/json;version='.$version
					);
						
					try{
						$response = $HttpSocket->post($url, $json, array('header'=>$headers));
					}catch(Exception $e){
						$return['status'] = "error";
						$return['msg'] = $e->getMessage();
						return json_encode($return);
					}
					$rsp_array = json_decode($response->body,true);
					$status = $rsp_array['status'];
											
					//if decision exists it means status was success
					$decision = ((isset($rsp_array['data']['decision']))? $rsp_array['data']['decision']:false);
						
					$return = array();
					if( $decision && in_array($rsp_array['data']['decision'], array('approved','counter','delayed')) ){ //Approved
						$return['status'] = "approved";
						$return['redirect'] = $rsp_array['data']['credit_offer']['desktop_url'];
				
					}else if($decision == 'declined'){ //Declined
						$return['status'] = "declined";
				
					}else if($decision == 'duplicate'){ //Duplicate
						$return['status'] = "duplicate";
							
					}else if(!$decision || in_array($rsp_array['data']['decision'], array('failure'))){//error
						$return['status'] = "error";
						$return['msg'] = $rsp_array['data']['errors'];
				
					}else{ //Error/should never happen
						$return['status'] = "error";
						$return['msg'] = 'Unknown error';
					}
				
					return json_encode($return);
						
				break;
								
				case 'lendingpoint':
					$data = array();
					$return =  array();
					$url_creation = $this->request->data['url']."?key=".$this->request->data['authKey'];
					$url_prequal = $this->request->data['url2'];
					$price = $this->request->data['price'];
				
					$data['authToPullCredit'] = $this->request->data['authToPullCredit'];
					$data['city'] = $this->request->data['city'];
					$data['dob'] = $this->request->data['dob'];
						
					$data['email'] = $this->request->data['email'];
					$data['employerCity'] = $this->request->data['employerCity'];
					$data['employerName'] = $this->request->data['employerName'];
					$data['employerPhone'] = $this->request->data['employerPhone'];
					$data['employerState'] = $this->request->data['employerState'];
						
					$data['employerStreetAddress'] = $this->request->data['employerStreetAddress'];
					$data['employerZip'] = $this->request->data['employerZip'];
					$data['firstName'] = $this->request->data['firstName'];
					$data['annualIncome'] = ($this->request->data['income']*12);
					$data['jobTitle'] = $this->request->data['jobTitle'];
					$data['lastName'] = $this->request->data['lastName'];
					$data['loanAmount'] = $this->request->data['loanAmount'];
					$data['loanPurpose'] = $this->request->data['loanPurpose'];
					$data['phone'] = $this->request->data['phone'];
					$data['source'] = $this->request->data['source'];
						
					$data['ssn'] = $this->request->data['ssn'];
					$data['state'] = $this->request->data['state'];
					$data['subProvider'] = $this->request->data['subProvider'];
					$data['subSource'] = $this->request->data['subSource'];
					$data['pointCode'] = $this->request->data['pointCode'];
					$data['street'] = $this->request->data['street'];
						
					$data['timeAtCurrentAddress'] = date("m/01/Y", strtotime($this->request->data['totalAddressTime'] ." months ago"));
					$data['employmentStartDate'] = date("m/01/Y", strtotime($this->request->data['totalWorkTime'] ." months ago"));
						
					if(!empty($this->request->data['unit']) && $this->request->data['unit'] != ""){
						$data['unit'] = $this->request->data['unit'];
					}
						
					$data['useOfFunds'] = $this->request->data['useOfFunds'];
					$data['zip'] = $this->request->data['zip'];
						
						
					$json = json_encode($data);
				
					$HttpSocket = new HttpSocket(array('ssl_verify_peer' => false, 'ssl_verify_host' => false));
					$response = $HttpSocket->post($url_creation, $json, array('header'=>array('Content-Type'=>'application/json','Accept'=>'application/json','Exchange-Format'=>'Core')));
						
					$response_obj = json_decode($response->body,true);
						
					if(isset($response_obj['response']) && $response_obj['response'] == "Accept"){
						$leadid = $response_obj['leadId'];
						$url_prequal .= $leadid;
						$HttpSocket2 = new HttpSocket(array('ssl_verify_peer' => false, 'ssl_verify_host' => false));
						$response2 = $HttpSocket2->get($url_prequal, "key=".$this->request->data['authKey'], array('header'=>array('Content-Type'=>'application/json','Accept'=>'application/json','Exchange-Format'=>'Core')));
				
						$prequal_json = $response2->body;
				
						$prequal = json_decode($prequal_json, true);
						if(isset($prequal['personalizedURL'])){
							$redirect = $prequal['personalizedURL'];
							$return['redirect'] = $redirect;
							$return['status'] = "Accept";
							$return['price'] = $price;
								
							return json_encode($return);
						}else{
							return $prequal_json;
						}
				
					}else{
						return $response->body;
					}
				
					break;
					
				case 'applieddatafinance':
					$url = $this->request->data['url'];
					$this->request->data['subid'] = $this->request->data['adf_campaign'].'.'.$this->request->data['affiliate'].'_'.$this->request->data['sub'].'.'.$this->request->data['internal_lead_id'];
					
					unset($this->request->data['url']);
					unset($this->request->data['adf_campaign']);
					unset($this->request->data['affiliate']);
					unset($this->request->data['sub']);
					unset($this->request->data['internal_lead_id']);
					unset($this->request->data['buyer']);
					
				
					$HttpSocket = new HttpSocket(array('timeout'=>60,'ssl_verify_peer' => false, 'ssl_verify_host' => false));
					$headers = array('Content-Type'=>'application/x-www-form-urlencoded'
							   );
					
					try{
						$response = $HttpSocket->post($url, $this->request->data, array('header'=>$headers));
					}catch(Exception $e){
						$return['status'] = "post_exception";
						$return['msg'] = $e->getMessage();	
						return json_encode($return);
					}
					$rsp_xml = $response->body;
					return $rsp_xml;
					
					
					break;
					
				case 'sigma':
					$url = 'https://www.thebestleadmaster.com/api/lead/post';
					
					$xml = '<?xml version="1.0" encoding="utf-8" ?> 
<EXTPOSTTRANSACTION>
    <STLTRANSACTIONINFO>
        <TRANSACTIONTYPE>100</TRANSACTIONTYPE>
        <USERID>clickmed</USERID>
        <PASSWORD>clickmed</PASSWORD>
        <STOREID>50100000001</STOREID>
        <STLTRANSACTIONID></STLTRANSACTIONID>
        <EXTTRANSACTIONID></EXTTRANSACTIONID>
        <MESSAGENUMBER>0</MESSAGENUMBER>
        <MESSAGEDESC></MESSAGEDESC>
        <STLTRANSACTIONDATE></STLTRANSACTIONDATE>
    </STLTRANSACTIONINFO>
    <EXTTRANSACTIONDATA>
        <CUSTOMER>
            <CUSTSSN>'.$this->request->data['custssn'].'</CUSTSSN>
            <CUSTFNAME>'.$this->request->data['first_name'].'</CUSTFNAME>
            <CUSTMNAME></CUSTMNAME>
            <CUSTLNAME>'.$this->request->data['last_name'].'</CUSTLNAME>
            <CUSTSUFFIX></CUSTSUFFIX>
            <CUSTADD1>'.$this->request->data['address_1'].'</CUSTADD1>
            <CUSTADD2>'.$this->request->data['address_2'].'</CUSTADD2>
            <CUSTCITY>'.$this->request->data['city'].'</CUSTCITY>
            <CUSTSTATE>'.$this->request->data['state'].'</CUSTSTATE>
            <CUSTZIP>'.$this->request->data['zip'].'</CUSTZIP>
            <CUSTZIP4></CUSTZIP4>
            <CUSTHOMEPHONE>'.$this->request->data['phone_primary'].'</CUSTHOMEPHONE>
            <CUSTMOBILEPHONE>'.$this->request->data['phone_mobile'].'</CUSTMOBILEPHONE>
            <CUSTWORKPHONE>'.$this->request->data['phone_work'].'</CUSTWORKPHONE>
            <CUSTEMAIL>'.$this->request->data['email'].'</CUSTEMAIL>
            <CUSTDOB>'.$this->request->data['dob_m-d-y'].'</CUSTDOB>
            <CUST18YRSOLD>1</CUST18YRSOLD>
            <CUSTDLSTATE>'.$this->request->data['dl_state'].'</CUSTDLSTATE>
            <CUSTDLNO>'.$this->request->data['dl_number'].'</CUSTDLNO>
            <UTILBILLVERIFIED></UTILBILLVERIFIED>
            <YRSATCURRADD>'.$this->request->data['years_addr'].'</YRSATCURRADD>
            <MNTHSATCURRADD>'.$this->request->data['residence_months'].'</MNTHSATCURRADD>
            <HOMESTATUS>'.$this->request->data['home_status'].'</HOMESTATUS>
            <MKTCODES>293</MKTCODES>
            <PDLOANRCVDFROM>ClickMedia-123456789012345</PDLOANRCVDFROM>
            <ISMILITARY>'.$this->request->data['military'].'</ISMILITARY>
        </CUSTOMER>
        <BANK>
            <CUSTABANO>'.$this->request->data['bank_routing'].'</CUSTABANO>
            <CUSTBANKNAME>'.$this->request->data['bank_name'].'</CUSTBANKNAME>
            <CUSTACCTNO>'.$this->request->data['bank_number'].'</CUSTACCTNO>
            <CUSTACCTTYPE>'.$this->request->data['account_type'].'</CUSTACCTTYPE>
            <ACCT30DAYSOLD>1</ACCT30DAYSOLD>
            <BANKACTIVEFLAG>P</BANKACTIVEFLAG>
        </BANK>
        <EMPLOYER>
            <TYPEOFINCOME>'.$this->request->data['income_type'].'</TYPEOFINCOME>
            <EMPNAME>'.$this->request->data['emp_name'].'</EMPNAME>
            <EMPADD1>'.$this->request->data['emp_address'].'</EMPADD1>
            <EMPCITY>'.$this->request->data['emp_city'].'</EMPCITY>
            <EMPSTATE>'.$this->request->data['emp_state'].'</EMPSTATE>
            <EMPZIP>'.$this->request->data['emp_zip'].'</EMPZIP>
            <EMPZIP4></EMPZIP4>
            <EMPPHONE>'.$this->request->data['phone_work'].'</EMPPHONE>
            <EMPLTYPE>F</EMPLTYPE>
            <JOBTITLE>Office</JOBTITLE>
            <AVGSALARY>'.$this->request->data['monthly_income'].'</AVGSALARY>
            <TYPEOFPAYROLL>'.$this->request->data['direct_deposit'].'</TYPEOFPAYROLL>
            <PERIODICITY>'.$this->request->data['frequency'].'</PERIODICITY>
            <LASTPAYDATE>'.$this->request->data['paydate1_mdy'].'</LASTPAYDATE>
            <NEXTPAYDATE>'.$this->request->data['paydate2_mdy'].'</NEXTPAYDATE>
            <FREQUENCY>'.$this->request->data['frequency2'].'</FREQUENCY>
        </EMPLOYER>
        <APPLICATION>
            <REQUESTEDAMOUNT>'.$this->request->data['loan_amount'].'</REQUESTEDAMOUNT>
			<CLIENTIP>'.$this->request->data['ip_address'].'</CLIENTIP>
            <REQUESTEDDUEDATE></REQUESTEDDUEDATE>
            <REQUESTEDEFFECTIVEDATE></REQUESTEDEFFECTIVEDATE>
            <LOANTYPE>S</LOANTYPE>
        </APPLICATION>
    </EXTTRANSACTIONDATA>
</EXTPOSTTRANSACTION>';
					
				$socket = new HttpSocket();
				$response = $socket->post($url, $xml);
				
				$xml2 = simplexml_load_string($response->body);
				$response = json_decode(json_encode($xml2),true);
				
				if ($response['STLTRANSACTIONDATA']['SUCCESS']=='1'){
					$apiPost['redirect'] = $response['STLTRANSACTIONDATA']['APPLICATIONURL'];
					$apiPost['price'] = $this->request->data['price'];
					$apiPost['status'] = 'Approved';
					return json_encode($apiPost);		
				}elseif($response['STLTRANSACTIONDATA']['SUCCESS']=='0'){
					$apiPost['status'] = 'Declined';
					//$this->log('---Sigma---');
					//$this->log($response);
					//$this->log($xml);
					return json_encode($apiPost);	
				}
				else{
					$apiPost['status'] = 'Error';
					return json_encode($apiPost);	
				}
				
				break;

				case 'cashcall':
				$url = 'https://primeleads.cashcall.com/Leads/LeadPost.aspx?LeadType=CCCG';
				$redirect = 'https://www.cashcall.com/ccc/library/thankyou/thankyou.html?phone=844-378-2899';

				$data = $this->request->data;
				$xml = '<?xml version="1.0" encoding="utf-8" ?> 
				<LeadInformation>
					<LeadApplication>
						<SourceId>' . $data['SourceId'] . '</SourceId>
						<SubId>5595</SubId>
						<SubId2>LEAD00011223</SubId2>
						<InitialStatus>100</InitialStatus>
						<TestLead>false</TestLead>
						<ProductType>' . $data['ProductType'] . '</ProductType>
					    <FundingCompany>' . $data['FundingCompany'] . '</FundingCompany>
					    <FirstName>' . $data['FirstName'] . '</FirstName>
					    <LastName>' . $data['LastName'] . '</LastName>
					    <SSN>' . $data['SSN'] . '</SSN>
					    <Street1>' . $data['Street1'] . '</Street1>
					    <Street2>' . $data['Street2'] . '</Street2>
					    <City>' . $data['City'] . '</City>
					    <State>' . $data['State'] . '</State>
					    <Zip>' . $data['Zip'] . '</Zip>
					    <MonthsAtAddress>12</MonthsAtAddress>
					    <Email>' . $data['Email'] . '</Email>
					    <CellPhone>' . $data['CellPhone'] . '</CellPhone>
					    <Birthday>' . $data['Birthday'] . '</Birthday>
					    <DriversLicenseNumber>' . $data['DriversLicenseNumber'] . '</DriversLicenseNumber>
					    <DriversLicenseState>' . $data['DriversLicenseState'] . '</DriversLicenseState>
					    <Employer>' . $data['Employer'] . '</Employer>
					    <EmployerCompanyPhone>' . $data['EmployerCompanyPhone'] . '</EmployerCompanyPhone>
					    <EmploymentJobTitle>' . $data['EmploymentJobTitle'] . '</EmploymentJobTitle>
					    <MonthsEmployed>' . $data['MonthsEmployed'] . '</MonthsEmployed>
					    <IsSelfEmployed>' . $data['IsSelfEmployed'] . '</IsSelfEmployed>
					    <SelfSelectedCredit>' . $data['SelfSelectedCredit'] . '</SelfSelectedCredit>
					    <IncomeSource>' . $data['IncomeSource'] . '</IncomeSource>
					    <PayFrequency>' . $data['PayFrequency'] . '</PayFrequency>
					    <NextPayDay>' . $data['NextPayDay'] . '</NextPayDay>
					    <RequestedLoanAmount>' . $data['RequestedLoanAmount'] . '</RequestedLoanAmount>
					    <LoanReason>' . $data['LoanReason'] . '</LoanReason>
					    <IsMilitary>' . $data['IsMilitary'] . '</IsMilitary>
					    <MonthlyIncome>' . $data['MonthlyIncome'] . '</MonthlyIncome>
					    <MonthlyExpenses>' . $data['MonthlyExpenses'] . '</MonthlyExpenses>
					    <SupplementalIncome>' . $data['SupplementalIncome'] . '</SupplementalIncome>
					    <IsInDebtProgram>' . $data['IsInDebtProgram'] . '</IsInDebtProgram>
					    <BankName>' . $data['BankName'] . '</BankName>
					    <BankPhone>' . $data['BankPhone'] . '</BankPhone>
					    <BankABA>' . $data['BankABA'] . '</BankABA>
					    <BankAccountNumber>' . $data['BankAccountNumber'] . '</BankAccountNumber>
					    <BankAccountType>' . $data['BankAccountType'] . '</BankAccountType>
					    <BankAccountTermInMonths>' . $data['BankAccountTermInMonths'] . '</BankAccountTermInMonths>
					    <HasDirectDeposit>' . $data['HasDirectDeposit'] . '</HasDirectDeposit>
					    <IsHomeOwner>' . $data['IsHomeOwner'] . '</IsHomeOwner>
					    <ClientURLRoot>' . $data['ClientURLRoot'] . '</ClientURLRoot>
					    <ClientIPAddress>' . $data['ClientIPAddress'] . '</ClientIPAddress>
					    <AgreedToDialerTCPA>' . $data['AgreedToDialerTCPA'] . '</AgreedToDialerTCPA>
					    <ExtraDataItems />
					    <Employer>' . $data['Employer'] . '</Employer>
					    <EmployerCompanyPhone>' . $data['EmployerCompanyPhone'] . '</EmployerCompanyPhone>
			  		</LeadApplication>
				</LeadInformation>';

				$socket 	  = new HttpSocket(array('ssl_verify_peer' => false, 'ssl_verify_host' => false));
				$response 	  = $socket->post($url, $xml);
				$xml2 		  = simplexml_load_string($response->body);
				$api_response = json_decode(json_encode($xml2),true);

				switch ($api_response['ERRORSTATUS']['@attributes']['ERRORNUMBER']) {
					case '0':
						$apiPost['status'] 		 = 'accepted';
						$apiPost['msg'] 		 = $api_response['ERRORSTATUS']['ERRORDESCRIPTION'];
						$apiPost['systemleadid'] = $api_response['SYSTEMLEADID'];
						$apiPost['redirect'] 	 = $redirect;
						break;
					case '100':
					case '200':
						$apiPost['status'] 		 = 'Rejected';
						$apiPost['msg'] 		 = $api_response['ERRORSTATUS']['ERRORDESCRIPTION'];
						$apiPost['systemleadid'] = $api_response['SYSTEMLEADID'];
						break;
					case '300':
						$apiPost['status'] 		 = 'Failure';
						$apiPost['msg'] 		 = $api_response['ERRORSTATUS']['ERRORDESCRIPTION'];
						$apiPost['systemleadid'] = $api_response['SYSTEMLEADID'];
						break;
					case '400':
						$apiPost['status'] 		 = 'Null';
						$apiPost['msg'] 		 = $api_response['ERRORSTATUS']['ERRORDESCRIPTION'];
						$apiPost['systemleadid'] = $api_response['SYSTEMLEADID'];
						break;	
					case '500':
						$apiPost['status'] 		 = 'Unreachable';
						$return['msg'] 			 = $api_response['ERRORSTATUS']['ERRORDESCRIPTION'];
						$apiPost['systemleadid'] = $api_response['SYSTEMLEADID'];
						break;		
					default:
						$apiPost['status'] = 'error';
						$apiPost['msg'] = 'Unknown Error';
						break;
				}
				return json_encode($apiPost);
				break;

				case 'cashcallCA':
				$url = 'https://primeleads.cashcall.com/Leads/LeadPost.aspx?LeadType=CCCG';
				$redirect = 'https://www.cashcall.com/ccc/library/thankyou/thankyou.html?phone=844-496-5351';

				$data = $this->request->data;
				$xml = '<?xml version="1.0" encoding="utf-8" ?> 
				<LeadInformation>
					<LeadApplication>
						<SourceId>' . $data['SourceId'] . '</SourceId>
						<SubId>5595</SubId>
						<SubId2>LEAD00011223</SubId2>
						<InitialStatus>100</InitialStatus>
						<TestLead>false</TestLead>
						<ProductType>' . $data['ProductType'] . '</ProductType>
					    <FundingCompany>' . $data['FundingCompany'] . '</FundingCompany>
					    <FirstName>' . $data['FirstName'] . '</FirstName>
					    <LastName>' . $data['LastName'] . '</LastName>
					    <SSN>' . $data['SSN'] . '</SSN>
					    <Street1>' . $data['Street1'] . '</Street1>
					    <Street2>' . $data['Street2'] . '</Street2>
					    <City>' . $data['City'] . '</City>
					    <State>' . $data['State'] . '</State>
					    <Zip>' . $data['Zip'] . '</Zip>
					    <MonthsAtAddress>12</MonthsAtAddress>
					    <Email>' . $data['Email'] . '</Email>
					    <CellPhone>' . $data['CellPhone'] . '</CellPhone>
					    <Birthday>' . $data['Birthday'] . '</Birthday>
					    <DriversLicenseNumber>' . $data['DriversLicenseNumber'] . '</DriversLicenseNumber>
					    <DriversLicenseState>' . $data['DriversLicenseState'] . '</DriversLicenseState>
					    <Employer>' . $data['Employer'] . '</Employer>
					    <EmployerCompanyPhone>' . $data['EmployerCompanyPhone'] . '</EmployerCompanyPhone>
					    <EmploymentJobTitle>' . $data['EmploymentJobTitle'] . '</EmploymentJobTitle>
					    <MonthsEmployed>' . $data['MonthsEmployed'] . '</MonthsEmployed>
					    <IsSelfEmployed>' . $data['IsSelfEmployed'] . '</IsSelfEmployed>
					    <SelfSelectedCredit>' . $data['SelfSelectedCredit'] . '</SelfSelectedCredit>
					    <IncomeSource>' . $data['IncomeSource'] . '</IncomeSource>
					    <PayFrequency>' . $data['PayFrequency'] . '</PayFrequency>
					    <NextPayDay>' . $data['NextPayDay'] . '</NextPayDay>
					    <RequestedLoanAmount>' . $data['RequestedLoanAmount'] . '</RequestedLoanAmount>
					    <LoanReason>' . $data['LoanReason'] . '</LoanReason>
					    <IsMilitary>' . $data['IsMilitary'] . '</IsMilitary>
					    <MonthlyIncome>' . $data['MonthlyIncome'] . '</MonthlyIncome>
					    <MonthlyExpenses>' . $data['MonthlyExpenses'] . '</MonthlyExpenses>
					    <SupplementalIncome>' . $data['SupplementalIncome'] . '</SupplementalIncome>
					    <IsInDebtProgram>' . $data['IsInDebtProgram'] . '</IsInDebtProgram>
					    <BankName>' . $data['BankName'] . '</BankName>
					    <BankPhone>' . $data['BankPhone'] . '</BankPhone>
					    <BankABA>' . $data['BankABA'] . '</BankABA>
					    <BankAccountNumber>' . $data['BankAccountNumber'] . '</BankAccountNumber>
					    <BankAccountType>' . $data['BankAccountType'] . '</BankAccountType>
					    <BankAccountTermInMonths>' . $data['BankAccountTermInMonths'] . '</BankAccountTermInMonths>
					    <HasDirectDeposit>' . $data['HasDirectDeposit'] . '</HasDirectDeposit>
					    <IsHomeOwner>' . $data['IsHomeOwner'] . '</IsHomeOwner>
					    <ClientURLRoot>' . $data['ClientURLRoot'] . '</ClientURLRoot>
					    <ClientIPAddress>' . $data['ClientIPAddress'] . '</ClientIPAddress>
					    <AgreedToDialerTCPA>' . $data['AgreedToDialerTCPA'] . '</AgreedToDialerTCPA>
					    <ExtraDataItems />
					    <Employer>' . $data['Employer'] . '</Employer>
					    <EmployerCompanyPhone>' . $data['EmployerCompanyPhone'] . '</EmployerCompanyPhone>
			  		</LeadApplication>
				</LeadInformation>';

				$socket 	  = new HttpSocket(array('ssl_verify_peer' => false, 'ssl_verify_host' => false));
				$response 	  = $socket->post($url, $xml);
				$xml2 		  = simplexml_load_string($response->body);
				$api_response = json_decode(json_encode($xml2),true);

				switch ($api_response['ERRORSTATUS']['@attributes']['ERRORNUMBER']) {
					case '0':
						$apiPost['status'] 		 = 'accepted';
						$apiPost['msg'] 		 = $api_response['ERRORSTATUS']['ERRORDESCRIPTION'];
						$apiPost['systemleadid'] = $api_response['SYSTEMLEADID'];
						$apiPost['redirect'] 	 = $redirect;
						break;
					case '100':
					case '200':
						$apiPost['status'] 		 = 'Rejected';
						$apiPost['msg'] 		 = $api_response['ERRORSTATUS']['ERRORDESCRIPTION'];
						$apiPost['systemleadid'] = $api_response['SYSTEMLEADID'];
						break;
					case '300':
						$apiPost['status'] 		 = 'Failure';
						$apiPost['msg'] 		 = $api_response['ERRORSTATUS']['ERRORDESCRIPTION'];
						$apiPost['systemleadid'] = $api_response['SYSTEMLEADID'];
						break;
					case '400':
						$apiPost['status'] 		 = 'Null';
						$apiPost['msg'] 		 = $api_response['ERRORSTATUS']['ERRORDESCRIPTION'];
						$apiPost['systemleadid'] = $api_response['SYSTEMLEADID'];
						break;	
					case '500':
						$apiPost['status'] 		 = 'Unreachable';
						$return['msg'] 			 = $api_response['ERRORSTATUS']['ERRORDESCRIPTION'];
						$apiPost['systemleadid'] = $api_response['SYSTEMLEADID'];
						break;		
					default:
						$apiPost['status'] = 'error';
						$apiPost['msg'] = 'Unknown Error';
						break;
				}
				return json_encode($apiPost);
				break;
			}
		}
	}
		
	/**
	 * Public redirect function used to decrypt the url and forward the user to the destination.
	 * @param string $hash_url
	 * @param string $hash_track_id
	 */
	public function r($hash_url='', $hash_track_id='') {
		$this->layout = null;
		$this->autoRender = false;	
		
		//Decrypt / Decode the url/track id
		$decrypted_url = $this->Service->decryptRedirect($hash_url);
		$track_id = base64_decode($hash_track_id);
		$decrypted_url = str_replace(' ','+',$decrypted_url);
		if(!filter_var($decrypted_url, FILTER_VALIDATE_URL) === false) {
			if(is_numeric($track_id)){
				$tracking['redirect_urls']['redirected']='1';
				$tracking['redirect_urls']['hash_url']=$hash_url;
				$json = json_encode($tracking);
				$this->Track->writeLead($track_id, $json);
			}else{
				$this->log('Lead redirected, yet the track_id was invalid.');
			}
			//Redirect the user
			$this->redirect($decrypted_url);
		}else{
			if(is_numeric($track_id)){
				$tracking['errors']['800']='Redirect URL decryption error.';
				$tracking['redirect_urls']['redirected']='0';
				$tracking['redirect_urls']['hash_url']=$hash_url;
				$json = json_encode($tracking);
				$this->Track->writeLead($track_id, $json);
			}
			$this->log($track_id);
			$this->log($decrypted_url);
			$this->log('Invalid Redirection URL');
			echo 'Invalid Redirect URL';
		}
	}
	
	/**
	 * Internal Testing redirect function used to decrypt the url and forward the user to the destination.
	 * @param string $hash_url
	 * @param string $hash_track_id
	 */
	public function rinternal($hash_url='', $hash_track_id='') {
		$this->layout = null;
		$this->autoRender = false;
	
		//Decrypt / Decode the url/track id
		$decrypted_url = $this->Service->decryptRedirect($hash_url);
		$track_id = base64_decode($hash_track_id);
		$decrypted_url = str_replace(' ','+',$decrypted_url);
		if(!filter_var($decrypted_url, FILTER_VALIDATE_URL) === false) {
			if(is_numeric($track_id)){
					
			}else{
				$this->log('Lead redirected, yet the track_id was invalid. (rint)');
			}
			//Redirect the user
			$this->redirect($decrypted_url);
		}else{
			if(is_numeric($track_id)){
	
			}
			$this->log($track_id);
			$this->log($decrypted_url);
			$this->log('Invalid Redirection URL (rint)');
			echo 'Invalid Redirect URL (rint)';
		}
	}
				
	/**
	 * Used by keyStone dashboard to set session variables from interaction triggers.
	 * Must be an ajax call with nonce.
	 * @param string $key
	 * @param mixed $value
	 */
	public function setSessionData($key, $value){
		$this->layout = null;
		$this->autoRender = false;
		
		if($this->request->is('ajax')){
			$this->Session->write($key, $value);
		}
	}
				
	/**
	 * Specify what user group has access.  For development speed, this is not in ACL.
	 * @todo Add ACL functionality to keyStone.
	 * @param array $user
	 * @return boolean
	 */
	private function __isAuthorized($user) {
		//Management
		if (in_array($this->Session->read('Auth.User.Group.id'),array('2','3','4','5'))) {
			$this->Auth->allow('index');
			return true;
		}
		return false;
	}
}
