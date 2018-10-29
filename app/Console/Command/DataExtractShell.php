<?php 
class DataExtractShell extends AppShell {
	public $uses = array('Track', 'LeadTrack', 'ReportTrack', 'Cake');
    
    public function main() {
		return false;
    }
        
 public function backdate(){
    	$times['start'] = '2016-01-19 00:00:01';//$date->format('Y-m-d H:i:s');
    	$times['end'] = '2016-01-19 23:59:59';  //$date->format('Y-m-d H:i:s');
    
    	$start = new MongoDate(strtotime($times['start']));
    	$end = new MongoDate(strtotime($times['end']));
    
    	$sub['lead_created']= array('$gt'=>$start, '$lt'=>$end);;
    	$sub['lead_id']		= array('$ne'=>'');
    	$sub['lead_id']		= array('$exists'=>true);
    	$params = array('conditions' => $sub);
    
    	// Remove test leads
    	$params['conditions']['lead_data.firstname'] = new MongoRegex('/^(?!.*test).*$/i');
    
    	$mongo_result = $this->ReportTrack->find('all',$params);
    
    	foreach($mongo_result as $track){
    		$leadinfo = json_encode($this->Cake->leadinfo($track['ReportTrack']['lead_id']));
    		$leadinfo = json_decode(strtolower($leadinfo),true);
    		$track_id = $track['ReportTrack']['track_id'];
    			
    		$tl[$track_id] = $track['ReportTrack'];
    
    		//Add the subids to mongo
    		$tl[$track_id]['subid'][1] = $leadinfo['data']['traffic_info']['subid_1'];
    		$tl[$track_id]['subid'][2] = $leadinfo['data']['traffic_info']['subid_2'];
    		$tl[$track_id]['subid'][3] = $leadinfo['data']['traffic_info']['subid_3'];
    		$tl[$track_id]['subid'][4] = $leadinfo['data']['traffic_info']['subid_4'];
    		$tl[$track_id]['subid'][5] = $leadinfo['data']['traffic_info']['subid_5'];
    			
    		//Add dispositions to mongo
    		if(is_array($leadinfo['data']['posts']) && isset($leadinfo['data']['posts']['post'][0]['buyer'])){
    			foreach($leadinfo['data']['posts']['post'] AS $key=>$disposition){
    				$explode = explode('-', $disposition['buyer_contract']['buyer_contract_name']);
    				$name = strtolower(trim($explode[1]));
    					
    				$tl[$track_id]['disposition'][$disposition['response_disposition']][$disposition['buyer_contract']['buyer_contract_id']]['buyer_contract_id'] = 	$disposition['buyer_contract']['buyer_contract_id'];
    				$tl[$track_id]['disposition'][$disposition['response_disposition']][$disposition['buyer_contract']['buyer_contract_id']]['buyer_contract_name'] = 	$name;
    				$tl[$track_id]['disposition'][$disposition['response_disposition']][$disposition['buyer_contract']['buyer_contract_id']]['sent_date'] = 			$disposition['sent_date'];
    				$tl[$track_id]['disposition'][$disposition['response_disposition']][$disposition['buyer_contract']['buyer_contract_id']]['received_date'] = 		$disposition['received_date'];
    				$tl[$track_id]['disposition'][$disposition['response_disposition']][$disposition['buyer_contract']['buyer_contract_id']]['ping_tree'] = 			$disposition['buyer']['buyer_name'];
    			}
    		}else{
    			if(isset($leadinfo['data']['posts'])){
    				$explode = explode('-', $leadinfo['data']['posts']['post']['buyer_contract']['buyer_contract_name']);
    				if(isset($explode[1])){
    					$name = strtolower(trim($explode[1]));
    					$tl[$track_id]['disposition'][$leadinfo['data']['posts']['post']['response_disposition']][$leadinfo['data']['posts']['post']['buyer_contract']['buyer_contract_id']]['buyer_contract_id'] = 	$leadinfo['data']['posts']['post']['buyer_contract']['buyer_contract_id'];
    					$tl[$track_id]['disposition'][$leadinfo['data']['posts']['post']['response_disposition']][$leadinfo['data']['posts']['post']['buyer_contract']['buyer_contract_id']]['buyer_contract_name'] = 	$name;
    					$tl[$track_id]['disposition'][$leadinfo['data']['posts']['post']['response_disposition']][$leadinfo['data']['posts']['post']['buyer_contract']['buyer_contract_id']]['sent_date'] = 			$leadinfo['data']['posts']['post']['sent_date'];
    					$tl[$track_id]['disposition'][$leadinfo['data']['posts']['post']['response_disposition']][$leadinfo['data']['posts']['post']['buyer_contract']['buyer_contract_id']]['received_date'] = 		$leadinfo['data']['posts']['post']['received_date'];
    					$tl[$track_id]['disposition'][$leadinfo['data']['posts']['post']['response_disposition']][$leadinfo['data']['posts']['post']['buyer_contract']['buyer_contract_id']]['ping_tree'] = 			$leadinfo['data']['posts']['post']['buyer']['buyer_name'];
    				}
    			}
    		}
    			
    
    		//print_r($tl);
    		//echo "\n";
    		//echo $track_id."\n";
    		//exit;
    		//exit;
    		//$this->ReportTrack->save($tl[$track_id]);
    		//$this->ReportTrack->clear();
    	}
    }
    
    
    public function mongoImport() {
    	$this->layout = null;
    	$this->autoRender = false;
    
    	//Slave
    	$this->Track->setDataSource('slave');
    
    	//Pull all tracks for the last 60 minutes.  This runs every 5 minutes and should allow for 45 minutes to complete a lead.
    	$result = $this->Track->query(
    	'SELECT track.id AS track_id, track.request_id, track.lead_id, track.offer_id, track.campaign_id, track.affiliate_id, track.created AS lead_created,
		track_lead.json_vars,
		receivables.id AS receivables_id, receivables.bucket_id, receivables.amount, receivables.margin, receivables.payout, receivables.created AS receivables_created,
		redirect_urls.url AS redirect_url, redirect_urls.count
		FROM track
		INNER JOIN track_lead ON track.id = track_lead.track_id
		LEFT JOIN receivables ON track.id = receivables.track_id
		LEFT JOIN redirect_urls ON track.id = redirect_urls.track_id
    	WHERE (track.created < NOW() AND track.created >= NOW() - INTERVAL 15 MINUTE)
    	OR (track_lead.created < NOW() AND track_lead.created >= NOW() - INTERVAL 5 MINUTE)');
		    	
		$tl = array();
    	$foundHash = false;
    	
    	//Did we find records to process
    	if(count($result)>0) {
    		foreach ($result as $track) {
    			$track_id = $track['track']['track_id'];
    			$tl[$track_id]['track_id'] = $track_id;
    			$tl[$track_id]['_id'] = $track_id;
    
    			if($track['track']['request_id'] !== NULL) { $tl[$track_id]['request_id'] = $track['track']['request_id']; }
    			if($track['track']['lead_id'] != '') { $tl[$track_id]['lead_id'] = $track['track']['lead_id']; }
    			if($track['track']['offer_id'] !== NULL) { $tl[$track_id]['offer_id'] = $track['track']['offer_id']; }
    			if($track['track']['campaign_id'] !== NULL) { $tl[$track_id]['campaign_id'] = $track['track']['campaign_id']; }
    			if($track['track']['affiliate_id'] !== NULL) { $tl[$track_id]['affiliate_id'] = $track['track']['affiliate_id']; }
    			
    			//Hash URL Block
    			if($track['track_lead']['json_vars'] !==null){
    				$hash = json_decode($track['track_lead']['json_vars'],true);
    			}
    			    			
    			if($track['track_lead']['json_vars'] !== NULL) {
    				
    				//Hash Block Start
    				$hash = json_decode($track['track_lead']['json_vars'],true);
    				
    				if(array_key_exists('redirect_urls', $hash)){
    					if($hash['redirect_urls']['hash_url'] !== null){
    						$reserveFormat = $hash['redirect_urls']['hash_url'];
    						$foundHash = true;
    					}
    				}
    				//Hash Block End
    				
    				$json = json_decode(strtolower($track['track_lead']['json_vars']),true);
    				if(count($json)>0){
    					$json=array_change_key_case($json, CASE_LOWER);
    					unset($json['trackid']);
    					unset($json['requestid']);
    					
    					if($foundHash === true){
    						$json['redirect_urls']['hash_url']=$reserveFormat;
    						$foundHash = false;
    					}
    					
    					if(array_key_exists('cakeresponse', $json)) {
    						unset($json['cakeresponse']);
    						unset($json['cakeposttype']);
    					}
    					    						
    					if(array_key_exists('receivableamount',$json)){
    						$json['receivableamount'] = (float) $json['receivableamount'];
    						$json['paidamount'] = (float) $json['paidamount'];
    						$json['marginamount'] = (float) $json['marginamount'];
    						$json['margin'] = (float) $json['margin'];
    					}
    					
    					if(array_key_exists('fraud',$json)){
    						$json['fraud']['ip']['blacklist'] = (bool)($json['fraud']['ip']['blacklist']==0) ? false : true; 
    					}
    					
   						$tl[$track_id]['lead_data'][] = $json;
    				}
    			}
    			    			
    			if($track['redirect_urls']['redirect_url'] !== NULL) {
    				$tl[$track_id]['redirect_urls']['url'] = $track['redirect_urls']['redirect_url'];
    				$tl[$track_id]['redirect_urls']['count'] = (int) $track['redirect_urls']['count'];
    				
    				if(array_key_exists('hash_url', $track['redirect_urls'])){
    					$tl[$track_id]['redirect_urls']['hash_url'] = $reserveStructure;
    				}
    			}
    			    
    			if($track['receivables']['receivables_id'] !== NULL) {
    				$tl[$track_id]['bucket_data']['bucket_id'] = $track['receivables']['bucket_id'];
    				$tl[$track_id]['bucket_data']['receivables_id'] = $track['receivables']['receivables_id'];
    				$tl[$track_id]['bucket_data']['amount'] = (float) $track['receivables']['amount'];
    				$tl[$track_id]['bucket_data']['margin'] = (float) $track['receivables']['margin'];
    				$tl[$track_id]['bucket_data']['payout'] = (float) $track['receivables']['payout'];
    			}
    
    			if($track['track']['lead_created'] != NULL) {
    				$tl[$track_id]['lead_created'] = new MongoDate(strtotime($track['track']['lead_created']));
    			}
    			    			    			
    			if($track['track']['lead_id'] != '') {
    				$leadinfo = json_encode($this->Cake->leadinfo($track['track']['lead_id']));
					$leadinfo = json_decode(strtolower($leadinfo),true);
					
					//Add the subids to mongo
					$tl[$track_id]['subid'][1] = $leadinfo['data']['traffic_info']['subid_1'];
					$tl[$track_id]['subid'][2] = $leadinfo['data']['traffic_info']['subid_2'];
					$tl[$track_id]['subid'][3] = $leadinfo['data']['traffic_info']['subid_3'];
					$tl[$track_id]['subid'][4] = $leadinfo['data']['traffic_info']['subid_4'];
					$tl[$track_id]['subid'][5] = $leadinfo['data']['traffic_info']['subid_5'];
					
					//Add dispositions to mongo
    				if(is_array($leadinfo['data']['posts']) && isset($leadinfo['data']['posts']['post'][0]['buyer'])){
    					foreach($leadinfo['data']['posts']['post'] AS $key=>$disposition){
    						$explode = explode('-', $disposition['buyer_contract']['buyer_contract_name']);
    						$name = strtolower(trim($explode[1]));
    						
    						$tl[$track_id]['disposition'][$disposition['response_disposition']][$disposition['buyer_contract']['buyer_contract_id']]['buyer_contract_id'] = 	$disposition['buyer_contract']['buyer_contract_id'];
    						$tl[$track_id]['disposition'][$disposition['response_disposition']][$disposition['buyer_contract']['buyer_contract_id']]['buyer_contract_name'] = 	$name;
    						$tl[$track_id]['disposition'][$disposition['response_disposition']][$disposition['buyer_contract']['buyer_contract_id']]['sent_date'] = 			$disposition['sent_date'];
    						$tl[$track_id]['disposition'][$disposition['response_disposition']][$disposition['buyer_contract']['buyer_contract_id']]['received_date'] = 		$disposition['received_date'];
    						$tl[$track_id]['disposition'][$disposition['response_disposition']][$disposition['buyer_contract']['buyer_contract_id']]['ping_tree'] = 			$disposition['buyer']['buyer_name'];
    					}
    				}else{
   						$explode = explode('-', $leadinfo['data']['posts']['post']['buyer_contract']['buyer_contract_name']);
   						$name = strtolower(trim($explode[1]));
   						
   						$tl[$track_id]['disposition'][$leadinfo['data']['posts']['post']['response_disposition']][$leadinfo['data']['posts']['post']['buyer_contract']['buyer_contract_id']]['buyer_contract_id'] = 	$leadinfo['data']['posts']['post']['buyer_contract']['buyer_contract_id'];
   						$tl[$track_id]['disposition'][$leadinfo['data']['posts']['post']['response_disposition']][$leadinfo['data']['posts']['post']['buyer_contract']['buyer_contract_id']]['buyer_contract_name'] = 	$name;
   						$tl[$track_id]['disposition'][$leadinfo['data']['posts']['post']['response_disposition']][$leadinfo['data']['posts']['post']['buyer_contract']['buyer_contract_id']]['sent_date'] = 			$leadinfo['data']['posts']['post']['sent_date'];
   						$tl[$track_id]['disposition'][$leadinfo['data']['posts']['post']['response_disposition']][$leadinfo['data']['posts']['post']['buyer_contract']['buyer_contract_id']]['received_date'] = 		$leadinfo['data']['posts']['post']['received_date'];
   						$tl[$track_id]['disposition'][$leadinfo['data']['posts']['post']['response_disposition']][$leadinfo['data']['posts']['post']['buyer_contract']['buyer_contract_id']]['ping_tree'] = 			$leadinfo['data']['posts']['post']['buyer']['buyer_name'];
    				}
    			}
    		}

    		foreach($tl AS $track_id=>$data) {
    			if(array_key_exists('lead_data',$data)) {
    				unset($tl[$track_id]['lead_data']);
    				foreach($data['lead_data'] AS $dex=>$arr) {
    					foreach($arr AS $key=>$value) {
    						$tl[$track_id]['lead_data'][$key]=$value;
    					}
    				}
    			}
    
    			//Does Track ID exist in Mongo?
    			$params = array('conditions' => array('_id' => "{$track_id}"));
    			$mongo_result = $this->ReportTrack->find('all', $params);

    			if(count($mongo_result)>0){
    				//needs testing
    				if(array_key_exists('lead_data',$mongo_result[0]['ReportTrack']) && array_key_exists('fraud',$mongo_result[0]['ReportTrack']['lead_data'])) {
    					$tl[$track_id]['lead_data']['fraud'] = @array_merge($mongo_result[0]['ReportTrack']['lead_data']['fraud'],$tl[$track_id]['lead_data']['fraud']);
    				}
    				
					if(array_key_exists('lead_data',$mongo_result[0]['ReportTrack'])) {
						$tl[$track_id]['lead_data'] = @array_merge($mongo_result[0]['ReportTrack']['lead_data'],$tl[$track_id]['lead_data']);
					}
	
					if(array_key_exists('bucket_data',$mongo_result[0]['ReportTrack'])) {
						$tl[$track_id]['bucket_data'] = @array_merge($mongo_result[0]['ReportTrack']['bucket_data'],$tl[$track_id]['bucket_data']);
					}
	
					if(array_key_exists('redirect_urls',$mongo_result[0]['ReportTrack'])) {
						$tl[$track_id]['redirect_urls'] = @array_merge($mongo_result[0]['ReportTrack']['redirect_urls'],$tl[$track_id]['redirect_urls']);
					}
					
					if(array_key_exists('disposition',$mongo_result[0]['ReportTrack'])){
						$tl[$track_id]['disposition'] = $tl[$track_id]['disposition'];
					}					
				}
				
    			$this->ReportTrack->save($tl[$track_id]);
    			$this->ReportTrack->clear();
    		}
    	}
    }
}