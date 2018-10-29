<?php 
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
App::uses('CakeEmail', 'Network/Email');

require APP.'Vendor/Prewk/XmlStringStreamer/Stream/File.php';
require APP.'Vendor/XmlStringStreamer.php';
require APP.'Vendor/Prewk/XmlStringStreamer/Parser/StringWalker.php';
require APP.'Vendor/Prewk/XmlStringStreamer/Parser/UniqueNode.php';

class BillingShell extends AppShell {
	public $uses = array('Kqueue','Cake','ReportTrack','BillingGroup');
	    
    public function main() {
		return false;
    }
   
	/**
	 * Build the billing report for file for the given data and buyer.
	 */
	private function buildFile($json, $adv, $id){
		$data = json_decode ( $json, true );
		$csv_array = array ();
		
		$csv_array [] = 'TYPE, BUYER NAME,CONTRACT NAME,TOTAL LEADS,TOTAL PRICE';
		$csv_array [] = 'Combined Totals,' . strtoupper ( $adv ) . ', ,' . $data ['totals'] ['leads'] . ', ' . number_format($data['totals']['owed'],2,'.','') . '';
		
		$csv_array [] = ',,,,';
		$csv_array [] = ',,,,';
		
		$csv_array [] = 'Affiliate Totals,' . strtoupper ( $adv ) . ', ,' . $data ['affiliate'] ['leads'] . ',' . $data ['affiliate'] ['owed'] . '';
		if ($data ['affiliate'] ['leads'] > 0) {
			
			foreach ( $data ['affiliate'] ['contracts'] as $k => $v ) {
				if (empty ( $v ))
					continue;
				$csv_array [] = 'Contract Details, ' . strtoupper ( $adv ) . ', ' . $v ['buyer_contract_name'] . ', ' . $v ['leads'] . ' Leads, $' . $v ['owed'] . ' Owed';
			}
		}
		
		$csv_array [] = ',,,,';
		$csv_array [] = ',,,,';
		
		$csv_array [] = 'Vendor Totals,' . strtoupper ( $adv ) . ',,' . $data ['vendor'] ['leads'] . ',' . $data ['vendor'] ['owed'] . '';
		if ($data ['vendor'] ['leads'] > 0) {
			
			foreach ( $data ['vendor'] ['contracts'] as $k => $v ) {
				if (empty ( $v ))
					continue;
				$csv_array [] = 'Contract Details,' . strtoupper ( $adv ) . ', ' . $v ['buyer_contract_name'] . ',' . $v ['leads'] . ' Leads,$' . $v ['owed'] . ' Owed';
			}
		}
		
		$file = new File(APP.'tmp/files/billing/billing_'.$id.'.csv', true);
		foreach($csv_array as $row){
			$file->write($row);
			$file->write("\n");
		}
		
		return true;
	}
	
	private function sendFile($id, $data){
		$email = new CakeEmail();
		$email->from(array('noreply@leadstudio.com'=>'Keystone'));
		$email->to($data['email']);
		$email->subject('Billing : '.$data['buyer'].' : '.$data['startdate'].' - '.$data['enddate']);
		$email->attachments(APP.'tmp/files/billing/billing_'.$id.'.csv');
		$email->send('Your billing report has completed and is attached.  You can also download a copy of this report by logging into KeyStone.  The file will be available for 30 days.');
		
		return true;
	}
	    
	/**
	 * Generate a billing report for the given dates and buyer
	 */
	public function generate(){
		$data = json_decode($this->args[0], true);
		$this->Kqueue->id = $this->args[1];
		//$data = array('startdate'=>'03/01/2016', 'enddate'=>'04/01/2016', 'buyergroup'=>'9', 'buyer'=>'Money Bee');
	
		$explode_start_date = explode("/", $data['startdate']);
		$explode_end_date = explode("/", $data['enddate']);
		
		$start_date = $explode_start_date[2]."-".$explode_start_date[0]."-".$explode_start_date[1]." 00:00:00";
		$end_date = $explode_end_date[2]."-".$explode_end_date[0]."-".$explode_end_date[1]." 00:00:00";
		
		
		$buyer_group = $data['buyergroup'];
		
		$group_info = $this->BillingGroup->find('first',array('recursive'=>2,'conditions'=>array('BillingGroup.id'=>$buyer_group)));
		$buyer_name = $group_info['BillingGroup']['group_name'];
		
		
		$contracts = $group_info['BillingGroupContracts'];
		$contracts_or_array = array();
		$buyer_name_lower = strtolower($buyer_name);
		foreach($contracts as $index=>$info){
			$c_id = $info['Contract']['remote_contract_id'];
			$contracts_or_array[] = array("disposition.approved.$c_id.buyer_contract_id"=>"$c_id");
			$contracts_or_array[] = array("disposition.success.$c_id.buyer_contract_id"=>"$c_id");
		}
		
	
		
		$start = new MongoDate(strtotime($start_date));
		$end = new MongoDate(strtotime($end_date));
		
		$mongo = $this->ReportTrack->getDataSource();
		//2 minutes
		MongoCursor::$timeout = -1;

		$mongoCollectionObject = $mongo->getMongoCollection($this->ReportTrack);
		
		$ops = array();
		$ops = array(
						
					    array( //equivalent to mysql where conditions
							'$match' => array( 
								'lead_created' => array('$gt'=> $start, '$lt' => $end),
								'lead_data.receivableamount' => array('$exists'=>true),
								'$or' => $contracts_or_array
								  	
							)
						),
						array(
							'$project' => array(
													'lead_data.calltype' => 1, 'disposition.approved' => 1, 'disposition.success' => 1, 'lead_data.receivableamount' => 1
							)
						
						),
						//array('$limit'=>1)
						
										
		);
		
		$results = array();
		
		try{
			$results = $mongoCollectionObject->aggregate($ops);	
			
		}catch(Exception $e){
			$subject = "BillingShell Failure";
			$msg = 'Message:  '.$e->getMessage()."\n\n";
			$msg .= 'Data:  '.print_r($data, true); 
			$this->notify($msg,$subject);
			exit;
		}
		
		
		if(count($results['result']) <= 0)exit;	
		
		$transactions = array (
					'buyer' => $buyer_name,
					'totals' => array (
							'leads' => 0,
							'owed' => 0 
					),
					'affiliate' => array (
							'leads' => 0,
							'owed' => 0,
							'contracts' => array () 
					),
					'vendor' => array (
							'leads' => 0,
							'owed' => 0,
							'contracts' => array () 
					) 
			);
	
    	foreach($results['result'] as $key=>$info){
    		
			$call_type = $info['lead_data']['calltype'];
			$success_mongo_string = "";
			
			//Get the contract id from mongo array structure
			if(isset($info['disposition']['approved']) && !empty($info['disposition']['approved'])){
				foreach($info['disposition']['approved'] as $id=>$arr){
					$buyer_contract_id = $id;
					$success_mongo_string = "approved";
					break;
				}
			}else{
				foreach($info['disposition']['success'] as $id=>$arr){
					$buyer_contract_id = $id;
					$success_mongo_string = "success";
					break;
				}	
			}
			
    		$buyer_contract_name = 	$info['disposition'][$success_mongo_string][$buyer_contract_id]['buyer_contract_name'];
    		$price = (double) $info['lead_data']['receivableamount'];
    		$ping_tree = $info['disposition'][$success_mongo_string][$buyer_contract_id]['ping_tree'];
    		
    		
    		if ($price <= 0)continue;
    		
    		if ($call_type == 'internal') {
    			$buyer_type = 'affiliate'; // affiliate
    		} else if ($call_type == 'external') {
    			$buyer_type = 'vendor'; // vendor
    		}
    			    		
    		// Totals for all
    		$transactions ['totals'] ['leads'] += 1;
    		$transactions ['totals'] ['owed'] += $price;
    		
    		// Totals by buyer type - affiliate/vendor
    		$transactions [$buyer_type] ['leads'] += 1;
    		$transactions [$buyer_type] ['owed'] += $price;
    		
    		// Increment leads and add price
    		if(isset($transactions[$buyer_type]['contracts'][$buyer_contract_id]['leads'])){
	    		$transactions[$buyer_type]['contracts'][$buyer_contract_id]['leads'] += 1;
	    		$transactions[$buyer_type]['contracts'][$buyer_contract_id]['owed'] += $price;
    		}else{
    			$transactions[$buyer_type]['contracts'][$buyer_contract_id]['leads'] = 1;
    			$transactions[$buyer_type]['contracts'][$buyer_contract_id]['owed'] = $price;
    			$transactions[$buyer_type]['contracts'][$buyer_contract_id]['buyer_contract_name'] = $ping_tree.' - '.$buyer_contract_name;
    		}
    		
    		$transactions[$buyer_type]['owed'] = number_format($transactions[$buyer_type]['owed'],2,'.','');
    		$transactions[$buyer_type]['contracts'][$buyer_contract_id]['owed'] = number_format($transactions[$buyer_type]['contracts'][$buyer_contract_id]['owed'],2,'.','');
    		$transactions['totals']['owed'] = number_format($transactions['totals']['owed'],2,'.','');
    	
    	}

	
    	$transactions_json = json_encode ( $transactions );

		//Build and save the file
		$this->buildFile($transactions_json, $buyer_name, $this->args[1]);
					
		//Email the file
		$this->sendFile($this->args[1], $data);
		
		//Flag the queue request as complete
		$this->Kqueue->complete();
    }

	private function notify($msg,$subject) {
    	$Email = new CakeEmail();
    	$Email->from(array('noreplay@leadstudio.com' => 'keyStone'))
    	->to('nick@leadstudio.com')
    	->subject($subject)
    	->send($msg);
    	 
    	return true;
    }
}