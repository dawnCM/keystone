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

class BrokenSeedController extends AppController {
	public $uses = array('LeadTrack', 'ReportTrack','Track', 'Cake', 'Affiliate', 'Bucket', 'Ip', 'Buyer', 'Contract');
	
	
	public function index(){
		
	}


		
	public function seedcontract(){
		$this->layout = 'dashboard';
		echo 'dfdfd';exit;
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