<?php 
class PullCakeShell extends AppShell {
	public $uses = array('Affiliate','Cake','Status','Contract','Buyer','BillingGroupList','BillingGroup','VendorDuplicates');
    
    public function main() {
		return false;
    }
    
    /**
     * Pull affiliates and insert new records.  This does not update affiliates.
     * @todo Add the update functionality if we determine its needed.
     */
    public function pullAffiliates(){
		$affiliates = $this->Cake->exportaffiliate();
				
		foreach($affiliates['data']['affiliates']->affiliate AS $affiliate){
			$count = $this->Affiliate->find('count', array('conditions' => array('Affiliate.remote_id'=>$affiliate->affiliate_id)));

			// Do we have this affiliate already?
			if($count == 0){
				$data['Affiliate']['remote_id'] = $affiliate->affiliate_id;
				$data['Affiliate']['affiliate_name'] = $affiliate->affiliate_name;
				$data['Affiliate']['api_key'] = $affiliate->api_key;
					
				$status = $this->Status->findByName(strtolower($affiliate->account_status->account_status_name));
					
				// Log the the missing status.  To be used with notify in the future.
				if(empty($status)){
					$status['Status']['id'] = 0;
				}
					
				$data['Affiliate']['status_id'] = $status['Status']['id'];
				$this->Affiliate->save($data);
				$this->Affiliate->clear();
			}
		}
    }
    
    /**
     * Pull buyers (trees), contracts from Cake and populate our local db with the data.
     */
    public function pullTrees(){
    	//Rebuild the tables each time.
    	$this->Contract->query('TRUNCATE buyers');
    	$this->Buyer->query('TRUNCATE contracts');
    	    
    	//Pull all buyers (trees) from cake.
    	$buyers = $this->Cake->exportbuyers();
    	foreach($buyers['data']['buyers']['buyer'] as $buyer){
    		$buyer_list[]=$buyer['buyer_id'];
    	}
    
    	//Cycle through each buyer and pull its associated contracts and write it all to the local db.
    	foreach($buyer_list as $buyer_id){
    		$contract_list = $this->Cake->exportcontracts($buyer_id);
    		foreach($contract_list['data']['buyer_contracts'] as $contract_data){
    			$contract_count = count($contract_data);

    			if($contract_count > 0){
    				$i=0;
    				while($i<$contract_count){
    						
    					if(is_array($contract_data[$i])){
    						$data['Contract']['remote_contract_id']=$contract_data[$i]['buyer_contract_id'];
    						$data['Contract']['contract_name']=$contract_data[$i]['buyer_contract_name'];
    							
    						$data['Buyer']['remote_buyer_id']=$contract_data[$i]['buyer']['buyer_id'];
    						$data['Buyer']['buyer_name']=$contract_data[$i]['buyer']['buyer_name'];
    							    							
    						$params = array('conditions'=>array('Contract.remote_contract_id'=>$contract_data[$i]['buyer_contract_id']));
    						$crow = $this->Contract->find('count',$params);
    							
    						$params = array('conditions'=>array('Buyer.remote_buyer_id'=>$contract_data[$i]['buyer']['buyer_id']));
    						$brow = $this->Buyer->find('count',$params);
    					}else{
    						$data['Contract']['remote_contract_id']=$contract_data['buyer_contract_id'];
    						$data['Contract']['contract_name']=$contract_data['buyer_contract_name'];
    
    						$data['Buyer']['remote_buyer_id']=$contract_data['buyer']['buyer_id'];
    						$data['Buyer']['buyer_name']=$contract_data['buyer']['buyer_name'];
        
    						$params = array('conditions'=>array('Contract.remote_contract_id'=>$contract_data['buyer_contract_id']));
    						$crow = $this->Contract->find('count',$params);
    
    						$params = array('conditions'=>array('Buyer.remote_buyer_id'=>$contract_data['buyer']['buyer_id']));
    						$brow = $this->Buyer->find('count',$params);
    					}
    
    					//Buyer New
    					if($brow < 1){
    						$this->Buyer->save($data);
    						$bid = $this->Buyer->getInsertID();
    					}
    					
    					//Contract New
    					if($crow < 1){
    						$data['Contract']['buyer_id'] = $bid;
    						$this->Contract->save($data);
    					}
    
    					unset($data);
    					unset($params);
    					$this->Buyer->clear();
    					$this->Contract->clear();
    					$i++;
    				}
    			}
    		}
    	}
    }
    
    /**
     * Rebuild billing groups list
     */
    public function rebuildBillingGroupList(){
    	//Rebuild the tables each time.
    	$this->BillingGroupList->query('TRUNCATE billing_group_list');
    
    	//Pull current contracts
    	$params = array('fields'=>array('id', 'remote_contract_id', 'contract_name'));
    	$contract_results = $this->Contract->find('all',$params);
    
    	//Pull current billing groups created
    	$billing_groups = $this->BillingGroup->find('all');
    
    	foreach($billing_groups as $index=>$bg_array){
    		$bg_id = $bg_array['BillingGroup']['id'];
    		$bg_search_array = json_decode($bg_array['BillingGroup']['original_contracts'], true);
    			
    		foreach($contract_results as $key=>$contract){
    			if(preg_match("/ONLY FOR BILLING/", $contract['Contract']['contract_name']))continue;
    
    			$contract_id = $contract['Contract']['id'];
    			$split = explode('-',$contract['Contract']['contract_name']);
    
    			if(isset($split[1]) && $split[1]!=''){
    				$cn = explode('(',$split[1]);
    				foreach($bg_search_array as $target_contract){
    					if(trim($cn[0]) == $target_contract){
    						//array_push($group_list,$contract['Contract'][id]);
    						$this->BillingGroupList->data['billing_group_id'] = $bg_id;
    						$this->BillingGroupList->data['contract_id'] = $contract_id;
    						$this->BillingGroupList->save($this->BillingGroupList->data);
    						$this->BillingGroupList->clear();
    					}
    
    				}
    			}
    		}
    
    	}
    
    }
    
    /**
     * Delete older records in vendor_duplicates table
     */
    public function purgeOldVendorDuplicateRecords(){
    	$conditions = array('VendorDuplicates.created <'=>date('Y-m-d H:i:s', strtotime("-65 mins")));
    	$this->VendorDuplicates->deleteAll($conditions,false);
    }
	
	
	/**
     * Delete Data Export files
     */
    public function deleteDataExportFiles(){
    	
		$_10minago = strtotime("-10 mins");
		$dir = new Folder(APP."tmp/files/reports/dataexport");
		$files = $dir->find('.*\.csv', true);
		
		foreach($files as $index=>$file){
			$file = new File(APP."tmp/files/reports/dataexport/".$file, false, 0777);
			$file_timestamp = $file->lastChange();
			
			if(is_int($file_timestamp)){
				
				if($file_timestamp <= $_10minago){
					$file->delete();
				}
			}
			$file->close();
		}
		
    }
	
}