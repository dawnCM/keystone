<?php
/**
 * Leads Controller
 *
 * This file handles the leads for the application, both mysql and mongo are used. 
 * 
 * Leads with [errors] array set in their track_lead will be flagged as error in leads/
 * Leads with [altered] array set in their track_lead will be flagged as 
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     TBD
 * @link          https://github.com/AdLink360/keyStone/wiki/LeadsController
 * @package       app.Controller
 * @since         keyStone v1.0
 * @license       TBD
 */

class LeadsController extends AppController {
	public $uses = array('LeadTrack', 'ReportTrack','Track', 'Cake', 'Affiliate', 'Bucket');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->autoRedirect = false;
		$this->__isAuthorized($this->Session->read('Auth.User'));
		$this->set('loadLeadsJS',true);
	}
	
	/**
	 *  Setup leads main page
	 */
	public function index(){
		$this->layout = 'dashboard';
	
		//Retrieve a list of affiliates
		$this->Affiliate->contain();
		$params = array('order'=>array('Affiliate.affiliate_name'));
		$this->set('affiliate_list', $this->Affiliate->find('all',$params));
	}
	
	/**
	 * Display the details of a lead for the given track_id or posted Lead or Track id.
	 * @param int $track_id
	 */
	public function detail($track_id=null) {
		$this->layout = 'dashboard';

		if($this->request->is('post') && $track_id === null){
			$leadid_pattern = "/([a-zA-Z].*[0-9]|[0-9].*[a-zA-Z])/";

			switch(true){
				case is_numeric($this->request->data['search']):
					$result[] = $this->ReportTrack->find('first', array('conditions'=>array('track_id'=>$this->request->data['search'])));
				break;
				
				case preg_match ($leadid_pattern, $this->request->data['search']):
					$result[] = $this->ReportTrack->find('first', array('conditions'=>array('lead_id'=>$this->request->data['search'])));
				break;
			}
		}else{
			$result[] = $this->ReportTrack->find('first', array('conditions'=>array('track_id'=>$track_id)));
		}

		if(empty($result[0])){
			$this->Session->setFlash('Lead not found.','notify_error');
		}else{
			$result[] = $this->Cake->exportcampaign($result[0]['ReportTrack']['campaign_id'], $result[0]['ReportTrack']['offer_id'], $result[0]['ReportTrack']['affiliate_id']);
				
			$data['ReportTrack']=$result[0]['ReportTrack'];
			$data['Cake']=$result[1]['data'];
				
			$this->set('lead', $data);
		}
	}
	
	/**
	 * Generates the lead query results when searching leads.  These results are not cached, but pull from the mongo reporting DB.
	 * @param string $start
	 * @param string $end
	 * @param string $first_name
	 * @param string $last_name
	 * @param string $email
	 * @param string $phone
	 * @param string $city
	 * @param string $state
	 * @param string $zip
	 * @param string $mobile
	 * @param string $military
	 * @param string $affiliate_id
	 * @param string $ip
	 * @param string $redirect
	 * @param string $sold
	 */
	public function leadQuery($start, $end, $first_name, $last_name, $email, $phone, $city, $state, $zip, $mobile, $military, $affiliate_id, $ip, $redirect, $sold, $altered){
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		$response = array('status'=>'error', 'message'=>'', 'data'=>'');
	
		$start_date = new MongoDB\BSON\UTCDateTime(strtotime($start));
		$end_date = new MongoDB\BSON\UTCDateTime(strtotime($end));
	
		$sub['lead_created']=array('$gt'=>$start_date, '$lt'=>$end_date);
	
		if($first_name != '-'){$sub['lead_data.firstname']= new MongoDB\BSON\Regex("/$first_name/i");}
		if($last_name != '-'){$sub['lead_data.lastname']= new MongoDB\BSON\Regex("/$last_name/i");}
		if($email != '-'){$sub['lead_data.email']= new MongoDB\BSON\Regex("/$email/i");}
		if($city != '-'){$sub['lead_data.city']=$city;}
		if($state != '-'){$sub['lead_data.state']=$state;}
		if($zip != '-'){$sub['lead_data.zip']=$zip;}
		if($mobile == '2'){$sub['lead_data.mobile']='true';}
		if($military == '2'){$sub['lead_data.military']='true';}
		if($affiliate_id != '-'){$sub['affiliate_id']=$affiliate_id;}
		if($ip != '-'){$sub['lead_data.ipaddress']=$ip;}
		if($altered != '-'){$sub['lead_data.altered']=array('$ne'=>null);}
		if($redirect == '1'){
			$sub['lead_data.redirect_urls']=array('$exists'=>false);
			$sub['lead_data.receivableamount']=array('$ne'=>null);
		}
		if($sold == '1'){
			$sub['lead_data.receivableamount']=array('$ne'=>null);
		}
			
		//Limit the fields
		$fields = array('track_id','offer_id','affiliate_id','lead_created','lead_data.receivableamount','lead_data.errors','lead_data.altered','lead_data.paidamount','lead_data.marginamount','lead_data.margin','lead_data.firstname');
	
		$params = array('conditions' => $sub,'fields'=>$fields,'limit'=>250, 'order'=>array('lead_created'=>'desc'));
		$result = $this->ReportTrack->find('all', $params);
		
		$response['status'] = 'success';
		$response['data'] = $result;
		$cache['value'] = $response;
	
		return json_encode($cache['value']);
	}
	
	public function datapull(){
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		$response = array('status'=>'error', 'message'=>'', 'data'=>'');
		
		$us_state_abbrevs = array('AL', 'AK', 'AS', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'DC', 'FM', 'FL', 'GA', 'GU', 'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MH', 'MD', 'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ', 'NM', 'NY', 'NC', 'ND', 'MP', 'OH', 'OK', 'OR', 'PW', 'PA', 'PR', 'RI', 'SC', 'SD', 'TN', 'TX', 'UT', 'VT', 'VI', 'VA', 'WA', 'WV', 'WI', 'WY', 'AE', 'AA', 'AP');
		
		foreach($us_state_abbrevs as $key=>$value){
			$state = strtolower($value);
		
			$sub['lead_data.state']=$state;
			$sub['lead_data.receivableamount']=array('$ne'=>null);
			
			$start = '2016-03-01';
			$end = '2016-04-01';
			
			$start_date = new MongoDB\BSON\UTCDateTime(strtotime($start));
			$end_date = new MongoDB\BSON\UTCDateTime(strtotime($end));
			
			$sub['lead_created']=array('$gt'=>$start_date, '$lt'=>$end_date);
			$sub['offer_id']=array('$ne'=>"40");
			$params = array('conditions' => $sub);
			//$result = $this->ReportTrack->find('count', $params);
			
			$output[$state]=$result;
		}
		
		//sort($output);
		echo "<pre>";
		print_r($output);
	}
	
	public function export($start, $end, $first_name, $last_name, $email, $phone, $city, $state, $zip, $mobile, $military, $affiliate_id, $ip, $redirect, $sold){
		$this->layout = 'ajax';
		$this->response->download("lead_export.csv");
	
		$response = array('status'=>'error', 'message'=>'', 'data'=>'');
	
		$start_date = new MongoDB\BSON\UTCDateTime(strtotime($start));
		$end_date = new MongoDB\BSON\UTCDateTime(strtotime($end));
	
		$sub['lead_created']=array('$gt'=>$start_date, '$lt'=>$end_date);
	
		if($first_name != '-'){$sub['lead_data.firstname']= new MongoDB\BSON\Regex("/$first_name/i");}
		if($last_name != '-'){$sub['lead_data.lastname']= new MongoDB\BSON\Regex("/$last_name/i");}
		if($email != '-'){$sub['lead_data.email']= new MongoDB\BSON\Regex("/$email/i");}
		if($city != '-'){$sub['lead_data.city']=$city;}
		if($state != '-'){$sub['lead_data.state']=$state;}
		if($zip != '-'){$sub['lead_data.zip']=$zip;}
		if($mobile == '2'){$sub['lead_data.mobile']='true';}
		if($military == '2'){$sub['lead_data.military']='true';}
		if($affiliate_id != '-'){$sub['affiliate_id']=$affiliate_id;}
		if($ip != '-'){$sub['lead_data.ipaddress']=$ip;}
		if($redirect == '1'){
			$sub['lead_data.redirect_urls']=array('$exists'=>false);
			$sub['lead_data.receivableamount']=array('$ne'=>null);
		}
		if($sold == '1'){
			$sub['lead_data.receivableamount']=array('$ne'=>null);
		}
	
		//Limit the fields
		$fields = array('track_id','offer_id','affiliate_id','lead_id','lead_created','lead_data.receivableamount','lead_data.errors','lead_data.altered','lead_data.paidamount','lead_data.marginamount','lead_data.margin','lead_data.firstname','lead_data.email');
	
		$params = array('conditions' => $sub,'fields'=>$fields,'limit'=>250,'order'=>array('_id'=>'DESC'));
		$result = $this->ReportTrack->find('all', $params);
	
		foreach($result as $row=>$value){
			if(isset($value['ReportTrack']['lead_data']['receivableamount'])){
				$sold = 'True';
			}else{
				$sold = 'False';
			}
	
			$file[$row]['affiliate_id'] = $value['ReportTrack']['affiliate_id'];
			$file[$row]['offer_id'] = $value['ReportTrack']['offer_id'];
			$file[$row]['track_id'] = $value['ReportTrack']['track_id'];
			$file[$row]['lead_id'] = $value['ReportTrack']['lead_id'];
			$file[$row]['email'] = $value['ReportTrack']['lead_data']['email'];
			$file[$row]['receivable'] = $value['ReportTrack']['lead_data']['receivableamount'];
			$file[$row]['payable'] = $value['ReportTrack']['lead_data']['paidamount'];
			$file[$row]['sold'] = $sold;
			$file[$row]['datetime'] = date('m-d-Y H:i:sa', $value['ReportTrack']['lead_created']->sec);
		}
	
		$response['status'] = 'success';
		$response['data'] = $file;
		$this->set('data', json_encode($response));
	}
	
	/**
	 * Allows increase or decrease of a sold leads receivable amount.  If a bucket exists it will be updated
	 * @param int $buyer_contract_id
	 * @param string $lead_id
	 * @param string $amount
	 */
	public function updateLeadReceivable($track_id, $buyer_contract_id, $lead_id, $amount){
		$this->layout = null;
		$this->autoRender = false;
	
		$keystone_user = $this->Session->read('Auth.User.full_name');
		$note='Receivable updated by: '.$keystone_user;
			
		$this->Cake->updatesalerevenue($buyer_contract_id, $lead_id, "FALSE", $amount, $note);
	
		$this->Session->setFlash('Lead ID '.$lead_id.' receivable was altered to $'.$value.'.','notify_success');
		$trackupdate['ReceivableAmount']=$amount;
		$trackupdate['Altered']=array('1002'=>'Lead Receivable updated via KeyStone by: '.$keystone_user);
			
		$json = json_encode($trackupdate);
		$this->Track->writeLead($track_id, $json);
	
		return $this->redirect('/leads/detail/'.$track_id);
	}
	
	/**
	 * Allows increase or decrease of a sold leads payable amount.  If a bucket exists it will be updated
	 * @param int $track_id
	 * @param int $vertical_id
	 * @param string $lead_id
	 * @param string $amount
	 */
	public function updateLeadPayable($track_id, $vertical_id, $lead_id, $amount){
		$this->layout = null;
		$this->autoRender = false;
		
		$keystone_user = $this->Session->read('Auth.User.full_name');
		$note='Payable updated by: '.$keystone_user;
			
		$this->Cake->updateleadprice($vertical_id, $lead_id, $amount, $note);
		
		// Lookup lead and see if it has a bucket id
		$params = array(
				'fields' 		=> array('_id','bucket_data','offer_id','affiliate_id','campaign_id','subid.1'),
				'conditions'	=> array('track_id'=>$track_id));
		
		$lead = $this->ReportTrack->find('first', $params);
		
		//Determine the change amount compared to the original payable
		if($lead['ReportTrack']['paidamount'] < $amount){
			$altered_amount['value'] = ($amount-$lead['ReportTrack']['paidamount']);
			$altered_amount['type'] = 'add';
		}else{
			$altered_amount['value'] = ($lead['ReportTrack']['paidamount']-$amount);
			$altered_amount['type'] = 'subtract';
		}
				
		// If the lead has a bucket id, does the bucket have anything in it?
		if(!empty($lead['ReportTrack']['bucket_data'])){
			$bucket_contents = $this->Bucket->find('first', array('conditions'=>array('Bucket.id'=>$lead['ReportTrack']['bucket_data']['bucket_id'])));
						
			//Adjust the bucket amount accordingly
			if($bucket_contents['Bucket']['amount'] > $altered_amount['value'] && $altered_amount['type']=='subtract'){
				$value = ($bucket_contents['Bucket']['amount']-$altered_amount['value']);
				$this->Session->setFlash('Lead ID '.$lead_id.' payable was altered and $'.$value.' was removed from their bucket.','notify_success');
			}
			elseif($bucket_contents['Bucket']['amount'] < $altered_amount['value'] && $altered_amount['type']=='subtract'){
				$this->Session->setFlash('Lead ID '.$lead_id.' payable was altered, however their bucket <strong>did not</strong> contain enough money to auto-deduct $'.$value.'','notify_warning');
			}
			else{
				$value = ($bucket_contents['Bucket']['amount']+$altered_amount['value']);
				$term = 'added to';
				$this->Session->setFlash('Lead ID '.$lead_id.' payable was altered and $'.$value.' was added to their bucket.','notify_success');
			}
			
			$this->Bucket->id=$bucket_contents['Bucket']['id'];
			$this->Bucket->set(array('amount'=> $value));
			$this->Bucket->save();
			
			$trackupdate['PaymentType']='CPA';
			
		}else{
			$trackupdate['PaymentType']='RevShare';
			$this->Session->setFlash('Lead ID '.$lead_id.' payable was altered to $'.$value.'.','notify_success');
		}
		
		$trackupdate['PaidAmount']=$altered_amount['value'];
		$trackupdate['Altered']=array('1001'=>'Lead Payable updated via KeyStone by: '.$keystone_user);
			
		$json = json_encode($trackupdate);
		$this->Track->writeLead($track_id, $json);
		
		return $this->redirect('/leads/detail/'.$track_id);
	}
	
	/**
	 * Reject an already converted lead. This will set lead to rejected in cake and remove the money
	 * from their bucket if necessary. If this lead tipped the bucket, the tipped payout amount is returned to the bucket
	 * minus the lead payable.
	 *
	 * @param int $lead_id
	 * @param int $offer_id
	 * @param int $track_id
	 */
	public function rejectlead($lead_id, $offer_id, $track_id){
		$this->layout = null;
		$this->autoRender = false;
	
		$keystone_user = $this->Session->read('Auth.User.full_name');
	
		$response = $this->Cake->updateconversion($lead_id, $offer_id, $keystone_user);
		
		// Lookup lead and see if it has a bucket id
		$params = array(
				'fields' 		=> array('_id','bucket_data','offer_id','affiliate_id','campaign_id','subid.1','lead_data.paidamount'),
				'conditions'	=> array('track_id'=>$track_id));
	
		$lead = $this->ReportTrack->find('first', $params);

		// If the lead has a bucket id, does the bucket have anything in it?
		if(!empty($lead['ReportTrack']['bucket_data'])){
			$tipped = false;
			
			//Has a bucket, does it have a paidamount, if so, this lead tipped the bucket
			if($lead['ReportTrack']['lead_data']['paidamount'] > 0){
				$tipped = true;
			}
			
			$bucket_contents = $this->Bucket->find('first', array('conditions'=>array('Bucket.id'=>$lead['ReportTrack']['bucket_data']['bucket_id'])));

			//If this lead did not tip the bucket, just remove the amount from the bucket
			if($tipped === false){
				if($bucket_contents['Bucket']['amount'] > $lead['ReportTrack']['bucket_data']['amount']){
					$reduce = $lead['ReportTrack']['bucket_data']['amount'];
					$value = ($bucket_contents['Bucket']['amount']-$lead['ReportTrack']['bucket_data']['amount']);
					$this->Bucket->id=$bucket_contents['Bucket']['id'];
					$r = $this->Bucket->saveField('amount',$value);

					$this->Session->setFlash('Lead ID '.$lead_id.' was set to rejected status and $'.number_format($reduce,2,'.','').' was removed from their bucket.','notify_success');
				}else{
					$this->Session->setFlash('Lead ID '.$lead_id.' was set to rejected status, however their bucket <strong>did not</strong> contain enough money to auto-deduct the lead amount.','notify_warning');
				}
			}else{
				//This lead tipped the bucket, take the bucket payout amount, remove the lead amount and place the payout back into the bucket.
				$reduce = $lead['ReportTrack']['bucket_data']['amount'];
				$value = ($lead['ReportTrack']['lead_data']['paidamount']-$lead['ReportTrack']['bucket_data']['amount'])+$bucket_contents['Bucket']['amount'];
				
				$this->Bucket->id=$bucket_contents['Bucket']['id'];
				$r = $this->Bucket->saveField('amount',$value);
				
				$this->Session->setFlash('Lead ID '.$lead_id.' was set to rejected status and $'.number_format($value,2,'.','').' was added to their bucket as this lead tipped their bucket initially','notify_success');
			}
			$trackupdate['PaymentType']='CPA';
		}else{
			$trackupdate['PaymentType']='RevShare';
			$this->Session->setFlash('Lead ID '.$lead_id.' was set to rejected status.','notify_success');
		}
	
		$trackupdate['ReceivableAmount']='0';
		$trackupdate['PaidAmount']='0';
		$trackupdate['Margin']='0.00';
		$trackupdate['MarginAmount']='0';
		$trackupdate['Altered']=array('1000'=>'Lead rejected via KeyStone by: '.$keystone_user);
		
		$json = json_encode($trackupdate);
		$this->Track->writeLead($track_id, $json);
		$this->log('Rejected Lead');
		$this->log($trackupdate['Altered']);
		
		return $this->redirect('/leads/detail/'.$track_id);
	}
	
	/**
	 * Specify what user group has access.  For development speed, this is not in ACL.
	 * @param array $user
	 * @return boolean
	 */
	private function __isAuthorized($user) {
		if (in_array($this->Session->read('Auth.User.Group.id'),array('2','3'))) {
			$this->Auth->allow();
			return true;
		}
	
		return false;
	}
}