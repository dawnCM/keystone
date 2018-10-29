<?php
/**
 * Tools Controller
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

class ToolsController extends AppController {
	public $uses = array('Bucket','Affiliate','Cake','Buyer');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->autoRedirect = false;
		$this->__isAuthorized($this->Session->read('Auth.User'));
		$this->set('loadToolsJS',true);
	}
	
	
	private function _showError($msg){
		$rsp = array();
		$rsp['status'] = "fail";
		$rsp['msg'] = $msg;
		echo json_encode($rsp);
		exit;
	}
	
	public function clonepingtreeview(){
		$this->layout = 'dashboard';
		
		/*
		 
		$buyer_array = $this->Buyer->find('all', array('fields'=>array('Buyer.buyer_id','Buyer.buyer_name'), 'conditions'=>array()));
		
		$buyer_list = array();
		foreach($buyer_array as $index=>$arr){
			$buyer_list[$arr['Buyer']['buyer_id']] = $arr['Buyer']['buyer_name'];
		}
		
		*/
		
		
		$this->set('buyer_list', $this->Buyer->getPingtree(true));
		
		
	}
	
	
	
	public function clonePingTree($copy_pingtree_id, $buyer_name, $affiliate_id, $contract_name, $replace_contractname_string = '', $type='vendor'){
		$buyer_id= $copy_pingtree_id;
	    
		$this->Cake->cakeLogin();
		
		if(empty($buyer_name) ||  empty($contract_name) || empty($replace_contractname_string) || $affiliate_id == ""){
			$this->_showError('All Fields are required.');
		}
		
	
		$result = $this->Cake->exportcontracts($buyer_id, 0);	
		if($result['status'] == "success"){
			$contracts = $result['data'];
		}else{
			$this->_showError($result['data']['message']);
		}
	
	
		$result1 = $this->Cake->createBuyer($buyer_name);
		if($result1['status'] == "success"){
			$new_buyer_id = $result1['data']['buyer_id'];
		}else{
			$this->_showError($result1['data']['message']);
		}
	
		
		
		$contract_loop = (($contracts['row_count'] > 1)? $contracts['buyer_contracts']['buyer_contract'] : $contracts['buyer_contracts'] );
		
		if(count($contract_loop) > 0 ){
			$vertical_id = $contract_loop[0]['vertical']['vertical_id'];
		}
		
		$ct=0;	
		foreach($contract_loop as $k=>$v){
						
			if($ct % 10 == 0){
				sleep(2);
			}
			
			
		
			
			$org_contract_id = $v['buyer_contract_id'];
	
			$final_contract_name = (($replace_contractname_string != "") ? str_replace($replace_contractname_string, $contract_name, $v['buyer_contract_name']) : $contract_name);
			if(empty($final_contract_name))$final_contract_name = $contract_name;
			
			$data_points = array(	'buyer_contract_id' 			=> 0,
									'buyer_id'						=> $new_buyer_id,
									'vertical_id'					=> $vertical_id,
									'buyer_contract_name'			=> $final_contract_name,
									'account_status_id'				=> $v['buyer_contract_status']['buyer_contract_status_id'],
									'offer_id'						=> -1,
									'replace_returns'				=> -1,
									'replacements_non_returnable'  	=> -1,
									'max_return_age_days'			=> -1,
									'buy_upsells' 					=> -1,
									'vintage_leads'					=> -1,
									'min_lead_age_minutes'			=> -1,
									'max_lead_age_minutes'			=> -1,
									'posting_wait_seconds'          => -1,
									'default_confirmation_page_link'=> "",
									'max_post_errors'				=> 11,
									'send_alert_only'				=> -1,
									'rank'							=> 0,//(int)$v['rank'],
									'email_template_id'				=> 0,
									'portal_template_id'			=> 0
			
			);
			
			
			
			$result2 = $this->Cake->createContract($data_points);
			if($result2['status'] == "success"){
				$new_contract_id = $result2['data']['buyer_contract_id'];
			}else{
				$this->_showError($result2['data']['message']);
			}
				
			$this->Cake->copyPoster($org_contract_id, $new_contract_id);
			//echo " - ".$final_contract_name;
			//echo "\n";
			
			
			//Delivery Schedule
			foreach($v['delivery_schedules']['delivery_schedule'] as $a=>$b){
				
				
				$schedule_day = strtolower($b['schedule_day']);
				$time_open = date("m-d-Y ".$b['time_open'].""); 
				$time_closed = date("m-d-Y ".$b['time_closed'].""); 
				$price = $b['default_price'];
				
				$delivery_array = array(	'buyer_contract_id' 			=> $new_contract_id,
											'delivery_schedule_id'			=> 0,
											'delivery_schedule_day'			=> $schedule_day,
											'time_open'						=> $time_open,
											'time_open_modify'				=> 'true',
											'time_closed'					=> $time_closed,
											'time_closed_modify'			=> 'true',
											'cap'							=> 9999,
											'price'							=> $price,
											'price_modify'					=> 'false',
											'sweeper'						=> off,
											'priority'						=> on,
											'no_return'						=> off,
											'schedule_type'					=>'exclusive'
				);
				
				$result = $this->Cake->createSingleDeliveryScheduleItem($delivery_array);	
				
			}
			
			if(!empty($v['filters'])){
				
				//Filters
				//When converted to array, a single filter is not an array of filter items.  Filter loop is used for more than one filter else filter is called directly
				$filter_loop = ((isset($v['filters']['filter'][0])  )? $v['filters']['filter'] : $v['filters'] );
				
				if(isset($v['filters']['filter'][0]) ){
							
						
					
					foreach($filter_loop as $c=>$d){
						
						
						$filter_array = array(	'buyer_contract_id' 			=> $new_contract_id,
												'filter_type_id' 				=> $d['filter_type']['filter_type_id'],
												'filter_id' 					=> 0,
												'filter_value'					=> ((	$d['filter_type']['filter_type_name'] == 'Vendor ID Equals')? $affiliate_id : 
																							 ($d['filter_type']['data_type']['data_type_name'] == "number" ? $d['param_number'] : $d['param_string'])),				
												'add_edit_option'				=> 'add'
						);
						
						$this->Cake->createSingleFilter($filter_array);
						
					
						
					}	
				}else{
				
					$filter_array = array(		'buyer_contract_id' 			=> $new_contract_id,
												'filter_type_id' 				=> $v['filters']['filter']['filter_type']['filter_type_id'],
												'filter_id' 					=> 0,
												'filter_value'					=> (($v['filters']['filter']['filter_type']['filter_type_name'] == 'Vendor ID Equals')? $affiliate_id : 
																							    ($v['filters']['filter']['filter_type']['data_type']['data_type_name'] == "number" ? $v['filters']['filter']['param_number'] : $v['filters']['filter']['param_string'])),
																								
												'add_edit_option'				=> 'add'
					);
					
					
					$this->Cake->createSingleFilter($filter_array);
				
				}	
			}

			


			$this->Cake->updateRank($new_contract_id, $v['rank']);
			$ct++;
		}

		echo json_encode(array('status'=>'success'));exit;
		
		
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
			$this->Auth->allow('index','add','get','edit','update','bliexists');
			return true;
		}
		return false;
	}
}