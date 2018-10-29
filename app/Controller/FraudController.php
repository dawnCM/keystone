<?php
/**
 * Fraud Controller
 *
 * This file handles fraud checks for keystone inbound leads.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     TBD
 * @link          https://github.com/adlink360/keyStone/wiki/FraudController
 * @package       app.Controller
 * @since         keyStone v1.0
 * @license       TBD
 */

class FraudController extends AppController {
	public $uses = array('LeadTrack', 'ReportTrack','Track', 'Cake', 'Affiliate', 'Bucket', 'Ip', 'Buyer', 'Contract');
	
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->autoRedirect = false;
		$this->__isAuthorized($this->Session->read('Auth.User'));
		$this->set('loadFraudJS',true);
	}
	
	/**
	 * Display a list of users.  This is limited by access, Administrators and managers have the rights
	 * to view,adjust and add.  Everyone else can only see themselves.
	 */
	public function index(){
		$this->layout = 'dashboard';
	}
	
	public function leadtime(){
		$this->layout = 'dashboard';
		
		//Retrieve a list of affiliates
		$this->Affiliate->contain();
		$params = array('order'=>array('Affiliate.affiliate_name'));
		$this->set('affiliate_list', $this->Affiliate->find('all',$params));
	}
	
	public function ip(){
		$this->layout = 'dashboard';
		
		if($this->request->isPost()){
			$this->Ip->set('ipactual',$this->request->data['ip']);
			
			if(!$this->Ip->validates(array('fieldList' => array('ipactual')))){
				$this->Session->setFlash('Invalid IP address supplied.','notify_error');
				
				//Retrieve a list of black listed IP's
				$params = array('conditions' => array('Ip.blacklist' => 1));
				$this->set('ip_list', $this->Ip->find('all', $params));
				$this->render();
			}

			if($this->request->data['ipaction'] == 'blacklist'){
				$this->Ip->blacklistIp($this->request->data['ip']);
				$this->Session->setFlash('IP address has been blacklisted.','notify_success');
			}else{
				$this->Ip->whitelistIp($this->request->data['ip']);
				$this->Session->setFlash('IP address has been whitelisted.','notify_success');
			}
		}
		
		//Retrieve a list of black listed IP's
		$params = array('conditions' => array('Ip.blacklist' => 1));
		$this->set('ip_list', $this->Ip->find('all', $params));
	}
	
	/**
	 * Generates the lead time report
	 * @param string $start
	 * @param string $end
	 * @param string $affiliate_id
	 * @param string $sub1
	 * @param string $sub2
	 * @param string $sub3
	 * @param string $sub4
	 */
	public function leadTimeQuery($start, $end, $affiliate_id='-', $sub1='-', $sub2='-', $sub3='-', $sub4='-'){	
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		$response = array('status'=>'error', 'message'=>'', 'data'=>'');
		
		$cache['hash'] = md5('ltq'.$start.$end.$affiliate_id.$sub1.$sub2.$sub3.$sub4);
		$cache['value'] = false;
		$cache['value'] = Cache::read($cache['hash']);
	
		if($cache['value'] === false){
			$start_date = new MongoDate(strtotime($start));
			$end_date = new MongoDate(strtotime($end));
		
			$sub['lead_created']=array('$gt'=>$start_date, '$lt'=>$end_date);
			$sub['lead_data.receivableamount']=array('$ne'=>null);
			$sub['lead_data.calltype']='internal';
			
			if($affiliate_id != '-'){$sub['affiliate_id']=$affiliate_id;}
			if($sub1 != '-'){$sub['subid.1']=$sub1;}
			if($sub2 != '-'){$sub['subid.2']=$sub2;}
			if($sub3 != '-'){$sub['subid.3']=$sub3;}
			if($sub4 != '-'){$sub['subid.4']=$sub4;}
			
			//Limit the fields
			$fields = array('lead_id','track_id','offer_id','affiliate_id','lead_created','subid','firstname','lead_data.receivableamount','lead_data.paidamount','lead_data.email','lead_data.ipaddress');
		
			$params = array('conditions' => $sub,'fields'=>$fields,'order'=>array('_id'=>'DESC'));
			$result = $this->ReportTrack->find('all', $params);
			
			foreach($result as $id=>$data){
				$params = array('conditions'=>array('track_id'=>$data['ReportTrack']['track_id']),'order'=>array('id asc'));
				$lead = $this->LeadTrack->find('all', $params);
				
				//Remove the fraud tracks from the count as they can happen long after the lead creation.  We will need to add additional
				//tracks to remove as we add them.
				foreach($lead as $lid=>$ldata){
					if(strpos($ldata['LeadTrack']['json_vars'],'fraud')){
						unset($lead[$lid]);
					}
				}
	
				$tracks = (count($lead)-1);
			
				$lead_start = new DateTime($lead[0]['LeadTrack']['created']);
				$lead_end = new DateTime($lead[$tracks]['LeadTrack']['created']);
				$diff = $lead_start->diff($lead_end);
							
				if($diff->i < 2){
					$data['ReportTrack']['time'] = $diff->i.':'.$diff->s;
					$lead_response[] = $data;
					$data2['file']['leadid'] = $data['ReportTrack']['lead_id'];
					$data2['file']['affiliateid'] = $data['ReportTrack']['affiliate_id'];
					$data2['file']['offerid'] = $data['ReportTrack']['offer_id'];
					$data2['file']['email'] = $data['ReportTrack']['lead_data']['email'];
					$data2['file']['ipaddress'] = $data['ReportTrack']['lead_data']['ipaddress'];
					$data2['file']['paidamount'] = $data['ReportTrack']['lead_data']['paidamount'];
					$data2['file']['receivableamount'] = $data['ReportTrack']['lead_data']['receivableamount'];
					$data2['file']['sub1'] = $data['ReportTrack']['subid'][1];
					$data2['file']['sub2'] = $data['ReportTrack']['subid'][2];
					$data2['file']['sub3'] = $data['ReportTrack']['subid'][3];
					$data2['file']['time'] = $diff->i.':'.$diff->s;
					$lead_response2[] = $data2;
				}
			}
			
			if(count($lead_response)>0){
				$response['data'] = $lead_response;
				$response['file'] = $lead_response2;
			}
		
			$response['status'] = 'success';
			$cache['value'] = $response;
			Cache::write($cache['hash'], $cache['value'], '5m');
		}
		
		return json_encode($cache['value']);
		
	}
	
	public function export($start, $end, $affiliate_id='-', $sub1='-', $sub2='-', $sub3='-', $sub4='-'){
		$this->layout = 'ajax';
		$this->response->download($affiliate_id."_export.csv");

		$response = array('status'=>'error', 'message'=>'', 'data'=>'');
		
		$cache['hash'] = md5(''.$start.$end.$affiliate_id.$sub1.$sub2.$sub3.$sub4);
		$cache['value'] = false;
		$cache['value'] = Cache::read($cache['hash']);
	
		if($cache['value'] === false){
			$start_date = new MongoDate(strtotime($start));
			$end_date = new MongoDate(strtotime($end));
		
			$sub['lead_created']=array('$gt'=>$start_date, '$lt'=>$end_date);
			$sub['lead_data.receivableamount']=array('$ne'=>null);
			$sub['lead_data.calltype']='internal';
			
			if($affiliate_id != '-'){$sub['affiliate_id']=$affiliate_id;}
			if($sub1 != '-'){$sub['subid.1']=$sub1;}
			if($sub2 != '-'){$sub['subid.2']=$sub2;}
			if($sub3 != '-'){$sub['subid.3']=$sub3;}
			if($sub4 != '-'){$sub['subid.4']=$sub4;}
			
			//Limit the fields
			$fields = array('lead_id','track_id','offer_id','affiliate_id','lead_created','subid','firstname','lead_data.receivableamount','lead_data.paidamount','lead_data.email','lead_data.ipaddress');
		
			$params = array('conditions' => $sub,'fields'=>$fields,'order'=>array('_id'=>'DESC'));
			$result = $this->ReportTrack->find('all', $params);

			foreach($result as $id=>$data){
				$params = array('conditions'=>array('track_id'=>$data['ReportTrack']['track_id']),'order'=>array('id asc'));
				$lead = $this->LeadTrack->find('all', $params);
				
				//Remove the fraud tracks from the count as they can happen long after the lead creation.  We will need to add additional
				//tracks to remove as we add them.
				foreach($lead as $lid=>$ldata){
					if(strpos($ldata['LeadTrack']['json_vars'],'fraud')){
						unset($lead[$lid]);
					}
				}
	
				$tracks = (count($lead)-1);
			
				$lead_start = new DateTime($lead[0]['LeadTrack']['created']);
				$lead_end = new DateTime($lead[$tracks]['LeadTrack']['created']);
				$diff = $lead_start->diff($lead_end);
				
				if($diff->i < 2){
					$data['ReportTrack']['time'] = $diff->i.':'.$diff->s;
					$lead_response[] = $data;
					$data2['file']['leadid'] = $data['ReportTrack']['lead_id'];
					$data2['file']['affiliateid'] = $data['ReportTrack']['affiliate_id'];
					$data2['file']['offerid'] = $data['ReportTrack']['offer_id'];
					$data2['file']['email'] = $data['ReportTrack']['lead_data']['email'];
					$data2['file']['ipaddress'] = $data['ReportTrack']['lead_data']['ipaddress'];
					$data2['file']['paidamount'] = $data['ReportTrack']['lead_data']['paidamount'];
					$data2['file']['receivableamount'] = $data['ReportTrack']['lead_data']['receivableamount'];
					$data2['file']['sub1'] = $data['ReportTrack']['subid'][1];
					$data2['file']['sub2'] = $data['ReportTrack']['subid'][2];
					$data2['file']['sub3'] = $data['ReportTrack']['subid'][3];
					$data2['file']['time'] = $diff->i.':'.$diff->s;
					$lead_response2[] = $data2;
				}
			}
			
			if(count($lead_response)>0){
				$response['data'] = $lead_response;
				$response['file'] = $lead_response2;
			}
		
			$response['status'] = 'success';
			$cache['value'] = $response;
			Cache::write($cache['hash'], $cache['value'], '5m');
		}

		$this->set('data', json_encode($cache['value']));
	}



		
	public function seedcontract(){
		$this->layout = 'dashboard';
		
		$params = array(
					'fields'=>array('id','buyer_name'),
					'order'=>array('buyer_name asc')
			);
		
		$buyers = $this->Buyer->find('list', $params);
		$this->set('buyer_list', $buyers);
	}

	public function getcontracts($buyer_id){
		$this->layout = null;
		$this->autoRender = false;
		$response = array('status'=>'failure');
		
		$params = array(
					'fields'=>array('remote_contract_id','contract_name'),
					'conditions' => array('buyer_id'=>$buyer_id),
					'order'=>array('contract_name asc')
			);
		
		$contracts = $this->Contract->find('list', $params);
		$array = array();
		foreach($contracts as $id=>$name){
			$array[] = array($id,$name);	
		}
		
		$response['status'] = 'success';
		$response['data'] = $array;
		
		
		return json_encode($response);		

	}
	
	public function getcontractdetails($contract_id){
		$this->layout = null;
		$this->autoRender = false;
		$response = array('status'=>'failure');
		$buyer_id=0;
		$details = $this->Cake->exportcontracts($buyer_id, $contract_id);
		
		$filters = $details['data']['buyer_contracts']['buyer_contract']['filters']['filter'];
		
		if(!isset($filters[0])){
			$filters = array($filters);
		}
		$data = array();	
		foreach($filters as $index=>$arr){
			$desc = $arr['filter_type']['filter_type_name'];
			
			if(!empty($arr['param_number']))$val=$arr['param_number'];
			if(!empty($arr['param_string']))$val=$arr['param_string'];
			if(!empty($arr['param_date']))$val=$arr['param_date'];
			if(!empty($arr['param_boo']))$val=$arr['param_boo'];
			
			$data[] = array($desc, $val);
		}		
				
		$response['status'] = 'success';
		$response['data'] = $data;		
				
		return json_encode($response);
	}

	
	public function processtestcontract(){
		$this->layout = null;
		$this->autoRender = false;	
		$response = array('status'=>'failure');
		
		$data = $this->request->data;
		$contract_id = $data['bc'];
		
		$this->Cake->cakeLogin();
		$lead_require = '';
		$lead_response = $this->Cake->seedContract($contract_id, $data);
		
		if(trim($lead_response) != ""){
			$contract_details = $this->Cake->exportcontracts(0, $contract_id );
			$contract_response_details = $contract_details['data']['buyer_contracts']['buyer_contract']['delivery_method']['responses']['response'];
		
			if(!isset($contract_response_details[0])){
				$contract_response_details = array($contract_response_details);
			}
			$decision = '';
		
			foreach($contract_response_details as $index=>$arr){
			
				$msg = $arr['delivery_method_response_text'];
				$decision_status = $arr['response_disposition_name'];
				
				if(preg_match("/$msg/", $lead_response)){
					$decision .= 'The Test Status is - <b>'.$decision_status.'</b><br><br>';
					break;	
				}
			}
			
			if($decision == ''){
				$decision .= 'The Test Status is - <b>Unknown</b><br><br>';
			}
			
			$lead_response = str_replace('Matched Buyer Lead ID','<br><br>Matched Buyer Lead ID', $lead_response);
			$lead_response = str_replace('Matched Buyer Redirect','<br>Matched Buyer Redirect', $lead_response);
			$lead_response = str_replace('Matched Buyer Price','<br>Matched Buyer Price', $lead_response);
			
			echo $decision.$lead_response;
		}else{
			echo 'Ooopss! Something went wrong.';
		}
		
		exit;
		
	}

	


		
	/**
	 * Specify what user group has access.  For development speed, this is not in ACL.
	 * @param array $user
	 * @return boolean
	 */
	private function __isAuthorized($user) {
		if (in_array($this->Session->read('Auth.User.Group.id'),array('2','3','4','5'))) {
			$this->Auth->allow();
			return true;
		}
		
		return false;
	}
}