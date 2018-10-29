<?php
/**
 * Billing Controller
 *
 * This file handles the authentication/authorization of a user to the applications. It also controls the adding, editing 
 * of users to the keyStone system.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     TBD
 * @link          https://github.com/adlink360/keyStone/wiki/BillingController
 * @package       app.Controller
 * @since         keyStone v1.0
 * @license       TBD
 */

class BillingController extends AppController {
	public $uses = array('Billing', 'Kqueue', 'Cake', 'Buyer', 'Contract', 'BillingGroup', 'BillingGroupList', 'BillingAdjustable', 'Affiliate', 'Track', 'Api');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->autoRedirect = false;
		$this->__isAuthorized($this->Session->read('Auth.User'));
		$this->set('loadBillingJS',true);
	}
	
	public function buyerreport() {
		$this->layout = 'dashboard';
		$params = array('fields'=>array('id', 'group_name'),'order'=>'group_name');
		$buyer_groups = $this->BillingGroup->find('list',$params);
		$this->set('buyer_list', $buyer_groups);
	}
	
	public function buyergroup(){
		$this->layout = 'dashboard';
		
		//Build the contract list
		$params = array('fields'=>array('id','remote_contract_id', 'contract_name'));
		$results = $this->Contract->find('all',$params);
				
		//Break the contract names out to a list of unique contract names.
		foreach($results as $key=>$contract){
			$split = explode('-',$contract['Contract']['contract_name']);
	
			if($split[1]!=''){
				$cn = explode('(',$split[1]);
			}
			$buyer_list[trim($cn[0])]=0;
		}
		
		//Build the existing groups list
		$bg_params = array('order'=>'BillingGroup.group_name');
		$billing_groups = $this->BillingGroup->find('all', $bg_params);
								
		$this->set('buyer_list', $buyer_list);
		$this->set('billing_groups', $billing_groups);
		$this->set('contract_list', $results);
	}
	
	public function billingadjustable(){
		$this->layout = 'dashboard';
		
		$affiliates = $this->Affiliate->find('all');
		$affiliate_list = array();
		foreach($affiliates as $index=>$arr){
			$affiliate_list[$arr['Affiliate']['id']] = $arr['Affiliate']['affiliate_name'];	
		}
		
		$this->set('affiliate_list', $affiliate_list);
	}
	
	public function addgroup(){
		$this->layout = null;
		$this->autoRender = false;
		
		if($this->request->is('post')){
			$this->BillingGroup->data['group_name'] = $this->request->data['group_name'];
			$this->BillingGroup->data['original_contracts'] = json_encode($this->request->data['contract_list']);
			$this->BillingGroup->save($this->BillingGroup->data);
			
			$billing_group_id = $this->BillingGroup->getInsertID();
			$params = array('fields'=>array('id', 'remote_contract_id', 'contract_name'));
			$results = $this->Contract->find('all',$params);
				
			$contract_list = $this->request->data['contract_list'];
			$group_list = array();
			
			foreach($results as $key=>$contract){
				$split = explode('-',$contract['Contract']['contract_name']);
			
				if($split[1]!=''){
					$cn = explode('(',$split[1]);
					foreach($contract_list as $key=>$target_contract){
						if(trim($cn[0]) == $target_contract){
							//array_push($group_list,$contract['Contract'][id]);
							$this->BillingGroupList->data['billing_group_id'] = $billing_group_id;
							$this->BillingGroupList->data['contract_id'] = $contract['Contract']['id'];
							$this->BillingGroupList->save($this->BillingGroupList->data);
						}
						$this->BillingGroupList->clear();
					}
				}
			}
			$this->Session->setFlash('Billing group successfully added.','notify_success');
		}
		
		return $this->redirect('/billing/buyergroup');
	}
	
	/**
	 * Used for front end ajax call to fill the billing table with available reports.
	 */
	public function getreports(){
		$this->layout = null;
		$this->autoRender = false;
		$data = $this->Kqueue->find('all', array('conditions' => array('Kqueue.name =' => 'Billing Report')));
		$response = array();
		foreach($data as $row){
			$qdata = json_decode($row['Kqueue']['data'], true);
			$response[$row['Kqueue']['id']]['start'] = $qdata['startdate'];
			$response[$row['Kqueue']['id']]['end'] = $qdata['enddate'];
			$response[$row['Kqueue']['id']]['buyer'] = $qdata['buyer'];
			$response[$row['Kqueue']['id']]['status'] = $row['Kqueue']['status'];
			$response[$row['Kqueue']['id']]['reportdate'] = $row['Kqueue']['created'];
		}
		
		return json_encode($response);
	}
	
	/*
	 *Get campaigns of affiliate
	 */
	public function getcampaigns($affiliate_id){
		$this->layout = null;
		$this->autoRender = false;
		$response = array('status'=>'failure');
		
		$params = array('conditions'=>array('Affiliate.id'=>$affiliate_id),
						'fields'=>array('Affiliate.remote_id')
		);
		
		$remote_affiliate = $this->Affiliate->find('first', $params);
		$remote_affiliate_id = $remote_affiliate['Affiliate']['remote_id'];
		
		$campaign_list = $this->Cake->exportcampaign(0,0,$remote_affiliate_id);
		
		if($campaign_list['status'] == "success" && $campaign_list['data']['row_count'] > 0){
			$response['status'] = 'success';
			
			if($campaign_list['data']['row_count'] == 1){
				$campaign_list['data']['campaigns']['campaign'] = array($campaign_list['data']['campaigns']['campaign']);
			}
				
			foreach($campaign_list['data']['campaigns']['campaign'] as $index=>$arr){
				$creative = ((isset($arr['exceptions']['allowed_creatives']['creative']['creative_id']))?$arr['exceptions']['allowed_creatives']['creative']['creative_id']:"");
				$response['data'][] = array($arr['campaign_id'],$creative);
			}
		}

		return json_encode($response);
	}


	/**
	 * Used for front end ajax call to fill the billing table with available reports.
	 */
	public function getadjustments(){
		$this->layout = null;
		$this->autoRender = false;
		$data = $this->BillingAdjustable->find('all');
		$response = array();
		foreach($data as $index=>$row){
			$response[] = array($row['BillingAdjustable']['id'],$row['BillingAdjustable']['type'],$row['BillingAdjustable']['affiliate_id'],$row['BillingAdjustable']['campaign_id'],$row['BillingAdjustable']['created'],$row['BillingAdjustable']['adjustment_date'],$row['BillingAdjustable']['price']);
		}
		
		return json_encode($response);
	}
	
	
	/**
	 * Used for front end ajax call to find adjustment totals
	 */
	public function getadjustmentstotals(){
		$this->layout = null;
		$this->autoRender = false;
		$response = array('status'=>'failure');
	
		$start_date = $this->request->data['start_date'];
		$start_date_format = explode("/", $start_date);
		$end_date = $this->request->data['end_date'];
		$end_date_format = explode("/", $end_date);
		$affiliate_id = $this->request->data['affiliate_id'];
		
		$params1 = array('conditions'=>array('Affiliate.id'=>$affiliate_id),
						'fields'=>array('Affiliate.remote_id')
		);
		$remote_affiliate = $this->Affiliate->find('first', $params1);
		$remote_affiliate_id = $remote_affiliate['Affiliate']['remote_id'];
		
		$params2 = array('fields' => array('BillingAdjustable.affiliate_id','BillingAdjustable.type','SUM(BillingAdjustable.price) as total_price'),
				  		'conditions' => array('BillingAdjustable.affiliate_id'=>$remote_affiliate_id, 'BillingAdjustable.created >='=>$start_date_format[2].'-'.$start_date_format[0].'-'.$start_date_format[1], 'BillingAdjustable.created <='=>$end_date_format[2].'-'.$end_date_format[0].'-'.$end_date_format[1]),
				  		'group'=> array('BillingAdjustable.type')
				  );
		
		$data = $this->BillingAdjustable->find('all', $params2);
		
	
		$response = array();
		foreach($data as $index=>$row){
			
			$response[] = array($row['BillingAdjustable']['affiliate_id'],$row['BillingAdjustable']['type'],$row[0]['total_price']);
		}
		
		return json_encode($response);
	}
	
	
	
	/*
	 *Add Adjustment to Billing
	 */
	public function addadjustment(){
		$this->layout = null;
		$this->autoRender = false;
		$response = array('status'=>'failure');
		$receivable = "0.00";
		$data = $this->request->data;
		
		$adjust_date_post = $data['adjustdate'];
		$adjust_date_format = explode("/", $adjust_date_post);
		$adjust_date = $adjust_date_format[2]."-".$adjust_date_format[0]."-".$adjust_date_format[1].date(" H:i:s"); //mysql table insert
		$conversion_date = $adjust_date_format[2]."-".$adjust_date_format[0]."-".$adjust_date_format[1]; //MassConversionInsert Format
	
		
		
		$params = array('conditions'=>array('Affiliate.id'=>$data['affiliate_id']),
						'fields'=>array('Affiliate.remote_id')
		);
		$remote_affiliate = $this->Affiliate->find('first', $params);
		
		if(empty($remote_affiliate)){
			$this->Session->setFlash('No Results From Selected Affiliate','notify_error');
			return json_encode($response);	
		}else if(!is_numeric($data['price'])){
			$this->Session->setFlash('The Adjustable Amount Must Be Numeric Or A Decimal','notify_error');
			return json_encode($response);	
		}
		
		$remote_affiliate_id = $remote_affiliate['Affiliate']['remote_id'];
		//Pull Campaign to get offer id
		$campaign_list = $this->Cake->exportcampaign($data['campaign_id'],0,$remote_affiliate_id);
	
		//Export offer to get the vertical id
		$offer_id = $campaign_list['data']['campaigns']['campaign']['offer']['offer_id'];
		$offer_list = $this->Cake->exportoffer($offer_id);
		
		$vertical_id = $offer_list['data']['offers']['offer']['vertical']['vertical_id'];
		$creative_id = $data['creative_id'];
		
		if(empty($creative_id)){
			$creative_id = 	((isset($campaign_list['data']['campaigns']['campaign']['exceptions']['allowed_creatives']['creative']['creative_id']) ) ?
									$campaign_list['data']['campaigns']['campaign']['exceptions']['allowed_creatives']['creative']['creative_id'] : ""					
							);
		}
		
		
		$lead_affiliate_id = $remote_affiliate_id;
		$lead_offer_id = $offer_id;
		$lead_campaign_id = $data['campaign_id'];
		$lead_creative_id = $creative_id;
		
		$tracking = array();
		$tracking['request_id'] = '9999';
		$tracking['offer_id'] = $lead_offer_id;
		$tracking['campaign_id'] = $lead_campaign_id;
		$tracking['affiliate_id'] = $lead_affiliate_id;
		$tracking['created'] = $adjust_date;
		$tracking['modified'] = $adjust_date;
		$tracking['Track'] = $tracking;
		if($this->Track->save($tracking)) {
			$lead_track_id = $this->Track->id;
			$this->Track->clear();
		} else {
			$this->Session->setFlash('Unable To Generate A Tracking ID','notify_error');
			return json_encode($response);	
		}
		
		unset($tracking['Track']);
		
		$tracking['AppType'] = 'adjustment';	
		$tracking['Mobile'] = 'false';
		$tracking['CallType'] = "Internal";
		$tracking['FirstName'] = $data['adjusttype'];
		$tracking['LastName'] = 'Adjustment';
		$tracking['Paydate1'] = date("m/d/Y", strtotime("+2 days"));

		$price =(STRING) $this->Api->formatDecimal($data['price']);
		
		if($data['adjusttype'] == 'clawback'){ //negative paid amount
			$price = "-".$price;
		}else if($data['adjusttype'] == 'buyercredit'){ //negative receivable amount
			$receivable = "-".$price;
			$price = "0.00";
		}
		
		$tracking['PaymentType'] = (STRING)$data['adjusttype'];
		$tracking['ReceivableAmount'] = (STRING)$receivable;
		$tracking['PaidAmount'] = (STRING)$price;
		$tracking['Margin'] = "0.00";
		$tracking['MarginAmount'] = "0.00";
		$this->Track->writeLead($lead_track_id, json_encode($tracking));
		
		
		$keystone_user = $this->Session->read('Auth.User.full_name');
		$note='Coversion Created By: '.$keystone_user;
		
		$insert_conversion = $this->Cake->massconversioninsert_v2($lead_affiliate_id, $lead_campaign_id, $lead_creative_id, $price, $receivable, $lead_track_id,  $conversion_date, 1, '[Empty]', $note);
		
		
		if($insert_conversion['status'] != 'success' || $insert_conversion['data']['success'] != 'true'){
			$this->Session->setFlash('Unable To Insert Conversion','notify_error');
			return json_encode($response);		
		}else{
			$this->BillingAdjustable->set( array('type'=>$data['adjusttype'],
												 'affiliate_id'=>$lead_affiliate_id,
												 'campaign_id'=>$lead_campaign_id,
												 'price'=>(($data['adjusttype'] == 'buyercredit')?$receivable:$price),
												 'adjustment_date'=> $adjust_date	
										   )
											
									);
			if($this->BillingAdjustable->save()){
				$adjustment_id = $this->BillingAdjustable->id;
				$this->Session->setFlash('The Billing Adjustment Was Successfully Created!   Record ID #'.$adjustment_id.'','notify_success');
				$response['status'] = 'success';
				return json_encode($response);	
			}else{
				$this->Session->setFlash('Unable To Save Adjustment To Database.  All Other Processes Were Successful','notify_error');
				return json_encode($response);	
			}
			
		}
		
	}
	
		
	/**
	 * Method to add a billing report to kqueue.
	 * Post / Redirect
	 */
	public function add(){
		$this->layout = null;
		$this->autoRender = false;

		if($this->request->is('post')){
			if($this->request->data['buyer'] == ''){
				$this->Session->setFlash('A buyer group must be selected.','notify_error');
			}else{
				
				$group_info = $this->BillingGroup->find('first',array('conditions'=>array('BillingGroup.id'=>$this->request->data['buyer'])));
				
				$kqueue['email'] = 		$this->Session->read('Auth.User.email');
				$kqueue['fullname'] = 	$this->Session->read('Auth.User.full_name');
				$kqueue['startdate'] = 	$this->request->data['startdate'];
				$kqueue['enddate'] = 	$this->request->data['enddate'];
				$kqueue['buyergroup'] = $this->request->data['buyer'];
				$kqueue['buyer'] = $group_info['BillingGroup']['group_name'];
				
				$this->Kqueue->data['name'] = 'Billing Report';
				$this->Kqueue->data['console'] = 'Billing';
				$this->Kqueue->data['function'] = 'generate';
				$this->Kqueue->data['data'] = json_encode($kqueue);
				
				$this->Kqueue->add();
				
				$this->Session->setFlash('Billing report successfully scheduled.','notify_success');
			}
		}
		return $this->redirect('/billing/buyerreport');
	}

	/*
	 *Delete Group 
	 */
	public function deleteGroup(){
		$this->layout = null;
		$this->autoRender = false;
		$response = array('status'=>'failure');

		if($this->request->is('post')){
			$group_id = $this->request->data['id'];
			$cascade = false; 		
			
			$this->BillingGroup->delete( $group_id, $cascade );
			$this->BillingGroupList->deleteAll( array('billing_group_id' => $group_id), $cascade );
			$response['status'] = 'success';
		}

		return json_encode($response);
	}
	
	/**
	 * Allows us to download the link but keep the file in a protected area only available to the application.
	 * @param integer $id
	 */
	public function download($id){
		$path = $this->Billing->downloadPath($id);
		$this->response->file($path, array('download'=>true, 'name'=>'billing_'.$id.'.csv'));
		return $this->response;
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
			$this->Auth->allow();
			return true;
		}elseif (in_array($this->Session->read('Auth.User.Group.id'),array('3','4','5'))){
			$this->Auth->allow();
			return true;
		}
		return false;
	}
}