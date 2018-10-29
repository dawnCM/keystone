<?php
/**
 * Bucket Controller
 *
 * This file handles the affiliate bucket (revshare/payout limit) for the applications. 
 * Terminology:
 * BLI   			- Bucket Label Identifier (affiliate_id - sub_id - campaign_id - offer_id)
 * Gross 			- Receivable amount
 * Net   			- Margin amount from receivable (margin/100) * gross)
 * Revenue Share 	- Amount placed into the bucket (gross - net)
 * Margin           - Calculated company margin
 * Threshold		- Payout limit for an account (tipping point) 
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     TBD
 * @link          https://github.com/Vexedmonkey/keyStone/wiki/BucketsController
 * @package       app.Controller
 * @since         keyStone v1.0
 * @license       TBD
 */
class BucketsController extends AppController {
	public $uses = array('Bucket','Affiliate','Cake');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->autoRedirect = false;
		$this->__isAuthorized($this->Session->read('Auth.User'));
		$this->set('loadBucketsJS',true);
	}
	
	/**
	 * Display the bucket dashboard, reads from slave db.
	 * Cache: 5 minutes
	 */
	public function index(){
		$this->layout = 'dashboard';
		
		//Read from the slave
		$this->Bucket->setDataSource('slave');
		
		//Setup cache
		$cache['hash'] = md5('bucket_index');
		$cache['value'] = false;
		$cache['value'] = Cache::read($cache['hash']);
		
		//Retrieve a list of buckets
		if($cache['value'] === false){
			$this->Affiliate->contain('Bucket');
			$affiliatelist = $this->Affiliate->find('all');
			
			$this->set('affiliatelist', $affiliatelist);
			
			$cache['value'] = $affiliatelist;
			Cache::write($cache['hash'],$cache['value'],'5m');
		}else{
			$this->set('affiliatelist', $cache['value']);
			$affiliatelist = $cache['value'];
		}
		
		//Create a list of aggregate buckets by affiliate id, using this loop rather than the model as I need cacheing.
		$aggregate = array();
		foreach($affiliatelist AS $key=>$aff){
			if(count($aff['Bucket'])>0){
				$aggregate[$aff['Affiliate']['id']]['name']=$aff['Affiliate']['affiliate_name'];
				$aggregate[$aff['Affiliate']['id']]['wallet']=$aff['Affiliate']['wallet'];
				foreach($aff['Bucket'] as $id=>$bucket){
					
					$aggregate[$aff['Affiliate']['id']]['amount'] += $bucket['amount'];
					$aggregate[$aff['Affiliate']['id']]['prefill'] += $bucket['prefill'];
					
					$cache['cake_hash'] = md5('cake_exportoffer_'.$bucket['offer_id']);
					$cache['cake_value'] = false;
					$cache['cake_value'] = Cache::read($cache['cake_hash']);
					
					if($cache['cake_value'] === false){
						$cache['cake_value'] = $this->Cake->exportoffer($bucket['offer_id']);
						$aff['Bucket'][$id]['offer_name'] = $cache['cake_value']['data']['offers']['offer']['offer_name'];
						Cache::write($cache['cake_hash'],$cache['cake_value']);
					}else{
						$aff['Bucket'][$id]['offer_name'] = $cache['cake_value']['data']['offers']['offer']['offer_name'];
					}
				}
				//$aggregate[$aff['Affiliate']['id']]['buckets'] = $aff['Bucket'];
				foreach($aff['Bucket'] as $i=>$ibucket){
					if($ibucket['sub_id'] == '0'){
						$fin[$ibucket['affiliate_id'].$ibucket['campaign_id'].$ibucket['offer_id']]['Main'] = $ibucket;
					}else{
						$fin[$ibucket['affiliate_id'].$ibucket['campaign_id'].$ibucket['offer_id']]['Sub'][] = $ibucket;
					}
				}
		
				$aggregate[$aff['Affiliate']['id']]['buckets'] = $fin;
				unset($fin);
			}
		}

		$this->set('aggregatelist',$aggregate);
	}
	
	public function get_affiliate_bucket($affiliate_id) {
		$this->layout = null;
		$this->autoRender =  false;
		$this->response->type('json');
		$this->Bucket->setDataSource('slave');
		
		//Setup cache
		$cache['hash'] = md5('affiliate_bucket_'.$affiliate_id);
		$cache['value'] = false;
		$cache['value'] = Cache::read($cache['hash']);
		
		$response = array('status'=>'fail','message'=>'','data'=>'');
		if($cache['value'] === false){
			$result = $this->Affiliate->find('first', array('conditions'=>array('Affiliate.id'=>$affiliate_id)));
			if(empty($result)){
				$response['message'] = 'Affiliate '.$affiliate_id.' could not be found.';
			}else{
				$response['status'] = 'success';
				$response['message'] = 'Affiliate '.$affiliate_id.' found.';
				$response['data'] = $result;
			}
			$cache['value'] = $response;
			Cache::write($cache['hash'],$cache['value'],'5m');
		}else{
			$response = $cache['value'];
		}
		
		return json_encode($response);
	}
			
	/**
	 * Add a new bucket, restful implementation with json return and requires ajax request.  If you add a new bucket, we clear
	 * the display cache so you can see your changes.
	 */
	public function add(){
		$this->layout = null;
		$this->autoRender = false;
		
		if ($this->request->is('post')){
			//Setup cache key to clear
			$cache['hash'] = md5('bucket_index');
			
			if ($this->Bucket->save($this->request->data)){
				Cache::delete($cache['hash'], '5m');
				$this->Session->setFlash('The bucket was added.','notify_success');
			}else{
				$errors = $this->Bucket->invalidFields();
				if($errors){
					foreach($errors as $k=>$v){
						$msg = "The field <strong>{$k}</strong> was invalid, {$v[0]}";
						$this->Session->setFlash($msg,'notify_error');
						break;
					}
				}else{
					$this->Session->setFlash('Failed to save the new bucket.','notify_error');
				}
			}
			
			return $this->redirect('/buckets');
		}
	}
	
	/**
	 * Update a bucket amount.  Prefill payback is in effect, if the bucket has been tipped and there is a positive prefill amount, then 
	 * future revenue shares will deduct the prefill amount before being added to the bucket volume.
	 * @param string $bli
	 * @param $amount
	 * @param $margin
	 */
	public function update($bli='', $amount=0.00, $margin=20.00){
		$this->layout = null;
		$this->autoRender =  false;
		$this->response->type('json');
		
		//Setup cache key to clear
		$cache['hash'] = md5('bucket_index');
				
		$response = array('status'=>'fail','message'=>'','data'=>'');
				
		if ($this->request->is('ajax')){
			$revenue = $this->Bucket->calculateRevenueSplit($amount,$margin);
			$bucket = $this->__getBucket($bli);
			
			$this->Bucket->id = $bucket['Bucket']['id'];

			//Has the bucket been tipped and contains a prefill amount?
			if($bucket['Bucket']['tipped'] != NULL && $bucket['Bucket']['prefill'] > 0){
				//Is the rev share enough to pay back the whole prefill?
				if($revenue['revenue_share'] > $bucket['Bucket']['prefill']){
					$revenue['revenue_share'] = number_format(($revenue['revenue_share']-$bucket['Bucket']['prefill']),2,'.','');
					$this->request->data['Bucket']['prefill'] = number_format(0,2,'.','');
				}else{

					//Reduce the prefill until it is payed back.
					$this->request->data['Bucket']['prefill'] = number_format(($bucket['Bucket']['prefill']-$revenue['revenue_share']),2,'.','');
					$revenue['revenue_share'] = number_format(0,2,'.','');
				}
				$this->request->data['Bucket']['amount'] = number_format(($bucket['Bucket']['amount']+$revenue['revenue_share']),2,'.','');
			}else{
				$this->request->data['Bucket']['amount'] = number_format(($bucket['Bucket']['amount']+$revenue['revenue_share']),2,'.','');
			}

			$this->Bucket->save($this->request->data);
			Cache::delete($cache['hash'], '5m');
			
			$response['status'] = 'success';
			$response['message'] = 'Bucket '.$bli.' was updated.';
			$response['data']['gross'] = $amount;
			$response['data']['net'] = $revenue['net'];
			$response['data']['revenue_share'] = $revenue['revenue_share'];
			$response['data']['current'] = number_format(($bucket['Bucket']['amount']+$revenue['revenue_share']),2,'.','');
			$response['data']['threshold'] = $bucket['Bucket']['payout'];
		}
		else{
			$response['message'] = 'Invalid request';
		}
		
		return json_encode($response);
	}
	
	/**
	 * Inline update a bucket field.  This method handles restful style updates.
	 * @access Administrator, Management
	 */
	public function edit($id=null,$field=null,$value=null){
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
	
		$response = array('status'=>'fail','message'=>'','data'=>'');
		
		$this->Bucket->id = $id;
		$response['status'] = 'fail';
		$response['message'] = '';
		if($this->request->is('ajax')){
			//Setup cache key to clear
			$cache['hash'] = md5('bucket_index');
			
			if ($this->Bucket->saveField($field,$value,true)) {
				$response['status'] = 'success';
				$response['message'] = 'Field '.$field.' updated with value '.$value;
				Cache::delete($cache['hash'], '5m');
				return json_encode($response);
			}
			$response['message'] = 'Field: '.$field.' with the value of '.$value.' could not be saved.';
		}
		else{
			$response['message'] = 'Invalid request';
		}
		return json_encode($response);
	}
	
	public function editaffiliate($id=null,$field=null,$value=null){
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		
		$response = array('status'=>'fail','message'=>'','data'=>'');
		
		$this->Affiliate->id = $id;
		$response['status'] = 'fail';
		$response['message'] = '';
		if($this->request->is('ajax')){
			//Setup cache key to clear
			$cache['hash'] = md5('bucket_index');
				
			if ($this->Affiliate->saveField($field,$value,true)) {
				$response['status'] = 'success';
				$response['message'] = 'Field '.$field.' updated with value '.$value;
				Cache::delete($cache['hash'], '5m');
				return json_encode($response);
			}
			$response['message'] = 'Field: '.$field.' with the value of '.$value.' could not be saved.';
		}
		else{
			$response['message'] = 'Invalid request';
		}
		return json_encode($response);
	}
	
	/**
	 * Check to see if the bli exists in the buckets table, this is a restful function.
	 * @access User
	 * @param string $bli
	 * @return boolean
	 */
	public function bliexists($bli=null){
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		$this->Bucket->setDataSource('slave');

		$bucket = json_decode($this->get($bli));
		
		if($bucket->success == false){
			return json_encode(false);
		}else{
			return json_encode(true);
		}
	}
	
	/**
	 * Retrieve the details of a single bucket by BLI.
	 * Cache: 5 minutes
	 * @param string $bli
	 */
	public function get($bli){
		$this->layout = null;
		$this->autoRender =  false;
		$this->response->type('json');
		$this->Bucket->setDataSource('slave');
		
		//Setup cache
		$cache['hash'] = md5('bucket_get_'.$bli);
		$cache['value'] = false;
		$cache['value'] = Cache::read($cache['hash']);
		
		$response = array('status'=>'fail','message'=>'','data'=>'');
		if($cache['value'] === false){
			$result = $this->Bucket->find('first', array('conditions'=>array('Bucket.bli'=>$bli)));
			if(empty($result)){
				$response['message'] = 'Bucket '.$bli.' could not be found.';
			}else{
				$response['status'] = 'success';
				$response['message'] = 'Bucket '.$bli.' found.';
				$response['data'] = $result;
			}
			$cache['value'] = $response;
			Cache::write($cache['hash'],$cache['value'],'5m');
		}else{
			$response = $cache['value'];
		}
		
		return json_encode($response);
	}
	
	protected function __tipBucket($bli){
		$this->layout = null;
		$this->autoRender = false;
		
		$bucket = $this->__getBucket($bli);
		$this->request->data['Bucket']['amount'] = number_format($bucket['Bucket']['amount']-$bucket['Bucket']['payout'],2,'.','');
		$this->request->data['Bucket']['tipped'] = date('Y-m-d H:i:s');
		
		if($this->Bucket->save($this->request->data)){
			//fire cake pixel
			return true;	
		}
		return false;
	}
	
	/**
	 * Private function for internal addition of buckets.
	 * @param string $bli
	 * @param $amount
	 * @param $payout
	 * @return boolean
	 */
	private function __addBucket($bli, $amount=0.00, $payout=0.00, $prefill=0.00){
		$this->layout = null;
		$this->autoRender = false;
			
		$addbucket = array('Bucket'=>array('bli'=>$bli,'amount'=>(float)$amount,'payout'=>(float)$payout,'prefill'=>(float)$prefill));
		
		if ($this->Bucket->save($addbucket)){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * Private function for retrieving a specific bucket internally, This is a recursive function
	 * and will call itself until a bucket is created to satisfy the BLI passed in.  Be careful.
	 * @param string $bli
	 */
	private function __getBucket($bli){
		$this->layout = null;
		$this->autoRender =  false;
		$bucket = $this->Bucket->find('first', array('conditions'=>array('Bucket.bli'=>$bli)));
		
		if($bucket){
			return $bucket;
		}else{
			if($this->__addBucket($bli)){
				return $this->__getBucket($bli);
			}
		}
		
		return false;
	}
				
	/**
	 * Specify what user group has access.  For development speed, this is not in ACL.
	 * @todo Add ACL functionality to keyStone.
	 * @param array $user
	 * @return boolean
	 */
	private function __isAuthorized($user) {
		//Management
		if (in_array($this->Session->read('Auth.User.Group.id'),array('2'))) {
			$this->Auth->allow('index','add','get','edit','update','bliexists','editaffiliate');
			return true;
		}
		return false;
	}
}