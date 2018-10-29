<?php 
class FraudCheckShell extends AppShell {
	public $uses = array('Track', 'LeadTrack', 'ReportTrack', 'Cake', 'Ip', 'FraudBrowser', 'Affiliate');
    
    public function main() {
		return false;
    }
    
    /**
     * Pull the leads from the previous day and run each of them against the IP database.  Update each lead track with the result.
     * This is a reactive check for reporting.
     */
	public function checkIp(){
		$date = new DateTime();
		$start = $date->modify('-1 day')->format('Y/m/d');
    	$end = $date->modify('+1 day')->format('Y/m/d');

		//$start = '2015/12/20';
		//$end = '2015/12/21';
		
		$start_date = new MongoDate(strtotime($start));
		$end_date = new MongoDate(strtotime($end));
		
		$sub['lead_created']=array('$gt'=>$start_date, '$lt'=>$end_date);
		$sub['lead_data.ipaddress']=array('$ne'=>null);
		$sub['lead_data.receivableamount']=array('$ne'=>null);
		$sub['lead_data.fraud']=array('$exists'=>false);
		
		//Limit the fields
		$fields = array('track_id','lead_data.ipaddress');
		$params = array('conditions' => $sub,'fields'=>$fields, 'order'=>array('_id'=>'DESC'));
		
		$result = $this->ReportTrack->find('all', $params);
		shuffle($result);
						
		if(count($result)>0){
			foreach($result AS $i=>$lead){
				if($this->_checkCount() >= 700){
					exit;
				}
				
				$response = $this->Ip->getIp($lead['ReportTrack']['lead_data']['ipaddress']);

				$fraud = array('fraud'=>array('ip'=>array('id'=>$response['id'],'blacklist'=>$response['blacklist'],'hostname'=>$response['hostname'],'org'=>$response['org'],'country'=>$response['country'])));
				$this->Track->writeLead($lead['ReportTrack']['track_id'], json_encode($fraud));
				
				$this->Ip->clear();
				$this->Track->clear();
			}
		}
    }
    
    public function addIp(){
     	//$result = $this->Ip->getIp($this->args[0]);
    	//echo $this->_checkCount();
    }
    
    public function checkEmail(){
    	
    }
    
    public function checkBusiness(){
    	
    }
    
    /**
     * Check inbound affiliate (internal sites) lead times for sold leads to verify they are taking at least 4+ minutes.
     */
    public function checkLeadTime(){
    	$date = new DateTime();
    	$start = $date->modify('-60 minute')->format('Y/m/d H:i:s');
    	$end = $date->modify('+60 minute')->format('Y/m/d H:i:s');

    	//$start = '2015/12/20 10:00:00';
    	//$end = '2015/12/20 11:00:00';
    	    	 	
    	$start_date = new MongoDate(strtotime($start));
    	$end_date = new MongoDate(strtotime($end));
    	    	
    	$sub['lead_created']=array('$gt'=>$start_date, '$lt'=>$end_date);
    	$sub['lead_data.calltype']='internal';
		$sub['lead_data.receivableamount']=array('$exists'=>true);
		
		//Mongo Lookup
		$fields = array('track_id','affiliate_id');
		$params = array('conditions'=>$sub,'fields'=>$fields);
		$result = $this->ReportTrack->find('all', $params);
		
		foreach($result as $id=>$data){
			$params = array('conditions'=>array('track_id'=>$data['ReportTrack']['track_id']),'order'=>array('id asc'));
			$lead = $this->LeadTrack->find('all', $params);
			$tracks = (count($lead)-1);
						
			$lead_start = new DateTime($lead[0]['LeadTrack']['created']);
			$lead_end = new DateTime($lead[$tracks]['LeadTrack']['created']);
			$diff = $lead_start->diff($lead_end);
						
			if($diff->i < 4 && $diff->i > 2){
				$links[$data['ReportTrack']['affiliate_id']][] = "https://keystone.leadstudio.com/leads/detail/".$lead[0]['LeadTrack']['track_id'];
			}
		}
		
		if(count($links)>0){
			$send = false;
			$subject = 'KeyStone: Fraud Warning - Lead Time';
			$msg = "Team,\n
KeyStone has detected possible fraud lead(s) that have been completed in less than 3 minutes.\n
Please verify that these lead(s) time are not an automated affiliate.  By clicking the link below you can see the leads details.\n\n";
			
			foreach($links as $aff=>$linklist){
				$count = count($linklist);
				
				if($count > 5){
					$send = true;
					$msg .= "Affiliate: ".$aff."\n";
				
					foreach($linklist AS $id=>$link){
						$msg .= $link."\n";
					}
				}
			}
			
			if($send){
				$this->notify($msg, $subject);
			}
		}
    	
    }

	/*
	 * Find records in last hour where BrowserId, State and HostName are the same. Send Alert Email
	 */
	public function runBrowserFraud(){

		$_1hourago = date("Y-m-d H:i:s", strtotime("-1 hour"));
		$now = date("Y-m-d H:i:s", strtotime("now"));
		$dupe_data = array();
		
		//Pull all affiliates and hits in last hour
		$affiliate_list = $this->FraudBrowser->find('all', array( 'fields'=>array('affiliate_id', 'count(affiliate_id) as entries'),
												 'conditions'=>array('created >'=> $_1hourago, 'created <='=>$now),
												 'group'=>array('affiliate_id')
										  )
							      );
	
		if(!$affiliate_list)exit;
		
		foreach($affiliate_list as $index=>$rec){
			$aff_id = $rec['FraudBrowser']['affiliate_id'];
			$total_aff_hits = (INT)$rec[0]['entries']; //total hits by affiliate
			
			if($total_aff_hits < 51){
				$percentage = .10;
			}else if($total_aff_hits < 101){
				$percentage = .08;
			}else if($total_aff_hits >= 101){
				$percentage = .05;
			}
			
			$_10percent = number_format($total_aff_hits * $percentage, 2, '.', ''); //10% value of total hits - decimal format
			$whole_percent_explode = explode(".",$_10percent);
			$whole_v10percent = (INT)$whole_percent_explode[0]; //whole number format of 10% - no rounding
			
			
			//Nothing less than 20 leads so 10% will be 2 or greater
			if($total_aff_hits < 20)continue;
			
			//find duplicate data with count by affiliate
			$sql = "Select count(*) as entries, a.affiliate_id, a.browser_id, b.org, b.region
				    From fraud_browser a
				    Left Join ip b ON a.ip_id=b.ip
				    Where a.created > '$_1hourago' AND a.created <= '$now' AND a.affiliate_id = '$aff_id'
				    Group By a.browser_id, b.org, b.region
				    Having entries > 1";
				    
			$affiliate_hits = $this->FraudBrowser->query($sql,false);
			
			
			if(!$affiliate_hits)continue;
	
			
			foreach($affiliate_hits as $index1=>$rec1){
				$duplicate_hits = (INT)$rec1[0]['entries']; //duplicate data hits
				
				//if duplicate data hits is less than the 10% needed of total hits for affiliate, continue.
				if($duplicate_hits < $whole_v10percent)continue;
				
				$browser_id = $rec1['a']['browser_id'];
				$org = str_replace(array("'"), array("\'"), $rec1['b']['org']); //escape backslash
				$region = $rec1['b']['region'];
				
				//Get track ids for the affiliate, browserid, org duplicates
				$sql2 = "Select DISTINCT a.track_id
				   		From fraud_browser a
				   		Left Join ip b ON a.ip_id=b.ip
				    	Where a.created > '$_1hourago' AND 
				    		  a.created <= '$now' AND 
				    		  a.affiliate_id = '$aff_id' AND 
				    		  a.browser_id = '$browser_id' AND 
				    		  b.org = '$org' AND
				    		  b.region = '$region'";
				    
				$affiliate_dups_list = $this->FraudBrowser->query($sql2,false);
				
								  
				if(!$affiliate_dups_list)continue;
				
				//store the affiliate with array of all track ids associated
				foreach($affiliate_dups_list as $key=>$arr){
					$dupe_data[$aff_id][] = $arr['a']['track_id'];
				}
				
				
				
			}					  
		}
		
		if(empty($dupe_data)){
			echo 'Affiliates Sent - '.count($dupe_data);	
			exit;
		}else{
	
			$msg = "Team,\n
	KeyStone has detected possible fraud leads conversions that have exceeded 10% conversion ratio\n
	with the same browser type from the same IP/Co-Location.\n
	Please verify that these leads are valid.\n\n";
			
			foreach($dupe_data as $affiliate=>$arr){
				
				//Get the remote_id(Cake Id) and name of affiliate
				$affiliate_info = $this->Affiliate->find('first', array( 'fields'=>array('affiliate_name','remote_id'),
													 'conditions'=>array('id'=>$affiliate)
													 
											  )
								      );
				
				if(empty($affiliate_info))continue;
				
				$cake_aff_id = $affiliate_info['Affiliate']['remote_id'];
				$affiliate_name = $affiliate_info['Affiliate']['affiliate_name'];
				
				//Create affiliate header display
				$msg .= "\nAffiliate - ".$affiliate_name." ($cake_aff_id)\n";
				$msg .= "\nTracking IDs - ";
				//Add Tracking Id sub header to show tracking ids - comma separated
				foreach($arr as $track_id){
					$msg .= "$track_id, ";
				}
				
				//Give me some space
				$msg .= "\n\n\n";
				
			}
			
			$subject = "KeyStone: Fraud Warning - IP/Browser";
			$this->notify($msg, $subject);
			echo 'Affiliates Sent - '.count($dupe_data);
		}	
	}

        
    /**
     * Check how many IP's have been added today.  We are currently limited to 1000 per day.
     */
    private function _checkCount(){
    	$params = array('conditions'=>array('created <'=>date('Y-m-d H:i:s'), 'created >'=>date('Y-m-d').' 00:00:00'));
    	return $this->Ip->find('count', $params);
    }
    
    private function notify($msg,$subject) {
    	$Email = new CakeEmail();
    	$to=array('nick@leadstudio.com','geramy@clickmedia.com','dawn@clickmedia.com','jason@clickmedia.com','caroline@clickmedia.com'); 
    	$Email->from(array('noreplay@leadstudio.com' => 'keyStone'))
    	->to($to)
    	->subject($subject)
    	->send($msg);
    	 
    	return true;
    }
}