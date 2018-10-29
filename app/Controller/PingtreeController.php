<?php
/**
 * Pingtree Controller
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     TBD
 * @link          https://github.com/Vexedmonkey/keyStone/wiki/AffiliateController
 * @package       app.Controller
 * @since         keyStone v1.0
 * @license       TBD
 */
class PingtreeController extends AppController {
	public $uses = array('Cake','Api','ReportTrack');
	private $_buyers; //array
	private $_contracts; //array

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->autoRedirect = false;
		$this->__isAuthorized($this->Session->read('Auth.User'));
		$this->set('loadPingtreeJS',true);
	}
			
	

	public function index(){
		$this->layout = 'dashboard';
		
		$buyers = $this->Cake->exportbuyers();
		$pingtree_list = array();
		foreach($buyers['data']['buyers']['buyer'] as $k=>$v){
			if(preg_match("/\(v\)|\(a\)/",$v['buyer_name'])){
				$pingtree_list[$v['buyer_id']] = $v['buyer_name'];
			}
		}
		asort($pingtree_list);
		
		//$pingtree_list =  array("170"=>"(v)Test Pingtree rank");
		$this->set('pingtree_list', $pingtree_list);
		
	}
	
	public function savePingtreeData(){
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
	
		$this->Cake->cakeLogin();
		
		$data = $this->request->data;
		
		foreach($data as $k=>$v){
			$update_type = $v['UpdateType'];
			$contract_id = $v['ContractId'];
			$buyer_id = $v['BuyerId'];
			$status = (($v['Status'] == "Active") ? 1 : (($v['Status'] == "Inactive")  ? 2 : 3));
			$rank = (INT)$v['Rank'];
			
			$data_points = array(	'buyer_contract_id' 	=> $contract_id,
									'buyer_id'						=> $buyer_id,
									'vertical_id'					=> 0,
									'buyer_contract_name'			=> "",
									'account_status_id'				=> $status,
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
									'rank'							=> $rank,//(int)$v['rank'],
									'email_template_id'				=> 0,
									'portal_template_id'			=> 0
			
			);
			
			
			
			
			switch ($update_type) {
				case 'r':
					$this->Cake->updateRank($contract_id, $rank);	
					break;
					
				case 's':
					$this->Cake->createContract($data_points);
					break;
					
				case 'sr':
					$this->Cake->updateRank($contract_id, $rank);
					$this->Cake->createContract($data_points);
					break;
				
			}
		}

		return json_encode(array("status"=>"done"));	
	}
		
	public function getPingtreeData($buyer_id){
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		
		$this->Cake->cakeLogin();
		
		$start_date = date("Y-m-d 00:00:00", strtotime("-3 day"));
		$end_date =  date("Y-m-d 23:59:59", strtotime("-1 day"));
		
	
		$this->_getContracts($buyer_id);
		$contract_loop = (($this->_contracts['row_count'] > 1)? $this->_contracts['buyer_contracts']['buyer_contract'] : $this->_contracts['buyer_contracts'] );
		$display = array();
		$pending = array();	
		$inactive = array();
		$rank_ct = 0;
		$rank_holder = array();
		
		$comparison = array();
		
		$start = new MongoDate(strtotime($start_date));
		$end = new MongoDate(strtotime($end_date));
		
		$mongo = $this->ReportTrack->getDataSource();
		$mongoCollectionObject = $mongo->getMongoCollection($this->ReportTrack);
		
		
		//Order contracts by rank.  0,1,2,3,4
		usort($contract_loop, function($a, $b) {
		    return $a['rank'] - $b['rank'];
		});
		$ct = 1;
		foreach($contract_loop as $k=>$v){
			
			$contract_id = (INT)$v['buyer_contract_id'];
			$contract_name_array = explode("-", (STRING)$v['buyer_contract_name']);
			$pre = ((preg_match("/\(pd\)/", $v['buyer_contract_name']))? '(pd)' : '(pl)');	
			$contract_name = $pre.' '.trim($contract_name_array[1]);
			
			if(preg_match('/ONLY FOR BILLING/', $v['buyer_contract_name']) || empty($contract_name) ){
				continue;
			}
			
			$rank = (INT)$v['rank'];
			$status = $v['buyer_contract_status']['buyer_contract_status_name'];
			
			if($status == "Active"){
				$ops = array();
				$ops = array(
							    array( //equivalent to mysql where conditions
									'$match' => array( 
										'lead_created' => array('$gt'=> $start, '$lt' => $end),
										'$or' => array(		array("disposition.approved.$contract_id.buyer_contract_id"=>"$contract_id"),
															array("disposition.error.$contract_id.buyer_contract_id"=>"$contract_id"),
															array("disposition.declined.$contract_id.buyer_contract_id"=>"$contract_id"),
															array("disposition.timeout.$contract_id.buyer_contract_id"=>"$contract_id"),
															array("disposition.duplicate.$contract_id.buyer_contract_id"=>"$contract_id"),
															array("disposition.fraud.$contract_id.buyer_contract_id"=>"$contract_id"),
															array("disposition.unknown.$contract_id.buyer_contract_id"=>"$contract_id"),
													  )
									)
								),
							    array( //group by theme and aggregate data as defined
							        '$group' => array(
							            "_id"		=>	null, //group by
							            'sent'	=>	array('$sum'=>1),
							            'sold' 	=>	array('$sum'=> 	
																	array('$cond'=> 
																					array('if'=>array('$eq'=>array('$disposition.approved.'.$contract_id.'.buyer_contract_id', "$contract_id"))   ,'then'=>1, 'else'=>0) //add 1 if field has value, add 0 if not
																	)
													),
										'revenue'=> array('$sum'=> 	
																	array('$cond'=> 
																					array('if'=>array('$eq'=>array('$disposition.approved.'.$contract_id.'.buyer_contract_id', "$contract_id"))   ,'then'=>'$lead_data.receivableamount', 'else'=>0) //add amount if field has value, add 0 if not
																	)
													)
							        ),
							    ),
							    array(
							    	//Define fields to show or not show. Needed to create fields and define display when different from $group
							        '$project' => 	array('_id'=>0, 'sent' => 1, 'sold'=>1, 'revenue'=>1, 'epl'=> //$epl - Created and calculated field after group fields are calculated
							        																			array('$divide'=>
							        																						array('$revenue','$sent')
																													  )     
							           				) 
							    )
				);
				
				$results = array();
				$results = $mongoCollectionObject->aggregate($ops);
				
				if($rank_ct == 0){ //first time in block
					
					$rank_ct++;
					$rank_holder[] = $rank;
					$type = "main";
					$children = 0;
				
					
				}else{
					if(!in_array($rank, $rank_holder)){
						$rank_ct++;
						$rank_holder[] = $rank;
						if($children > 0){
							$last_main_index = $this->_getLastMainIndex($display);
							$display[$last_main_index]['RowType'] = 'main_wrr';
							$display[$last_main_index]['Children'] = $children;
						}
						$type = "main";
						$children = 0;
					}else{
						$type = "rr";
						$children++;	
					}
					
				}
				
				
				if(count($results['result']) == 0){
					
					$display[] = array('ContractId'=>$contract_id, 'RowType'=>$type, 'ContractName' => $contract_name, 'Rank'=>$rank_ct, 'Status'=>$status, 'Sold'=>"0", 'Sent'=>"0", 'Revenue'=>"0.00", 'EPL'=>"0" );
					$comparison[] = array($status, "Rank ".$rank, $contract_name);
				}else{
					$results = $results['result'];
					
					$display[] = array('ContractId'=>$contract_id, 'RowType'=>$type, 'ContractName' => $contract_name, 'Rank'=>$rank_ct, 'Status'=>$status, 'Sold'=>$results[0]['sold'], 'Sent'=>$results[0]['sent'], 'Revenue'=>$this->Api->formatDecimal($results[0]['revenue']), 'EPL'=>$this->Api->formatDecimal($results[0]['epl']) );
					$comparison[] = array($status, "Rank ".$rank, $contract_name);
				}
			}else if($status == "Pending"){
				$pending[] = array('ContractId'=>$contract_id, 'RowType'=>'main', 'ContractName'=>$contract_name, 'Rank'=>$rank, 'Status'=>$status, 'Sold'=>"0", 'Sent'=>"0", 'Revenue'=>"0.00", 'EPL'=>"0" );
			
			}else if($status == "Inactive"){
				$inactive[] = array('ContractId'=>$contract_id, 'RowType'=>'main', 'ContractName'=>$contract_name, 'Rank'=>$rank, 'Status'=>$status, 'Sold'=>"0", 'Sent'=>"0", 'Revenue'=>"0.00", 'EPL'=>"0" );
				
			}
			
			$ct++;	
		}

		//Add pending contracts under active contracts
		if(count($pending) > 0){
			usort($pending, function($a, $b) {
			    return $a['Rank'] - $b['Rank'];
			});
		
			foreach($pending as $k=>$v){
				$rank_ct++;
				$pending[$k]['Rank'] = $rank_ct;
				
				
				array_push($display,$pending[$k]);
				$comparison[] = array('Pending', "Rank ".$v['Rank'], $v['ContractName']);
			}
			
		}

		//Add inactive contracts under pending contracts
		if(count($inactive) > 0){
			usort($inactive, function($a, $b) {
			    return $a['Rank'] - $b['Rank'];
			});
			
			foreach($inactive as $k=>$v){
				$rank_ct++;
				$inactive[$k]['Rank'] = $rank_ct;
				
				array_push($display,$inactive[$k]);	
				$comparison[] = array('Inactive', "Rank ".$v['Rank'], $v['ContractName'] );
			}
			
		}

		
		$response = array('status'=>'success','comparison'=>$comparison, 'display'=>$display, 'summary'=> $masterBuyerContractSummary['summary']);
		return json_encode($response);

	}
	
	
	private function _getLastMainIndex($array){
		$length = count($array)-1;
		
		for($i=$length; $i >= 0; $i-- ){
			if($array[$i]['RowType'] == 'main'){
				return $i;
			}
		}
	}
	
	
	
	private function _getContracts($buyer_id, $contract_id=0){
	
		$result = $this->Cake->exportcontracts($buyer_id, $contract_id);	
		
		if($result['status'] == "success"){
			$this->_contracts = $result['data'];
		}else{
			$this->_showError($result['data']['message']);
		}
	}
	
	private function _array_column(array $input, $columnKey, $indexKey = null) {
        $array = array();
        foreach ($input as $key=>$value) {
        
            if ( ! isset($value[$columnKey])) {
                trigger_error("Key \"$columnKey\" does not exist in array");
                return false;
            }
            if (is_null($indexKey)) {
                $array[] = $value[$columnKey];
            }
            else {
                if ( ! isset($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not exist in array");
                    return false;
                }
                if ( ! is_scalar($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not contain scalar value");
                    return false;
                }
                $array[$value[$indexKey]] = $value[$columnKey];
            }
        }
        return $array;
    }
	
	
	private function _showError($msg){
		echo $msg;
		exit;
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
			$this->Auth->allow();
			return true;
		}
		
		return false;
	}
}