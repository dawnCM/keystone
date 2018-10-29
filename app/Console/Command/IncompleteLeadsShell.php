<?php 
App::uses('HttpSocket', 'Network/Http');
class IncompleteLeadsShell extends AppShell {
	public $uses = array('ReportTrack');
	
    
    public function main() {
		return false;
    }
        
	 public function processIncompletes(){
	    	$times['start'] = date('Y-m-d H:i:s', strtotime("now") - 7200); //two hours ago
	    	//$times['start'] = '2015-11-30 00:00:00';
	    	$times['end'] = date('Y-m-d H:i:s', strtotime("now") - 3600); //one hour ago
	    	
	    	$start = new MongoDate(strtotime($times['start']));
	    	$end = new MongoDate(strtotime($times['end']));
	    
	    	$sub['lead_created']= array('$gt'=>$start, '$lt'=>$end);
			$sub['lead_data.email']=array('$ne'=>null);
	    	$sub['lead_data.receivableamount'] = array('$exists'=>false);
			$sub['lead_data.calltype'] = array('$eq'=>'internal');
			
			$fields = array('lead_data.firstname', 'lead_data.lastname', 'lead_data.email', 'lead_data.ipaddress', 'lead_data.calltype', 'offer_id', 'lead_data.url');
			
	    	$params = array('conditions' => $sub,'fields'=>$fields, 'order'=>array('_id'=>'DESC'));
	    
	    	$mongo_result = $this->ReportTrack->find('all',$params); 
			
			if(count($mongo_result) > 0){
				$sites = Configure::read('SitesAPI.Sites');
				foreach($mongo_result as $arr=>$rec){
					 
					$offer_id = $rec['ReportTrack']['offer_id'];
					
					$data = $rec['ReportTrack']['lead_data'];
					$call_type = $data['calltype'];
					$ip = $data['ipaddress'];
					$email = $data['email'];
					$url = $sites[$offer_id]['Url'];
					$firstname = ((!empty($data['firstname']))? $data['firstname'] : "");
					$lastname = ((!empty($data['lastname']))? $data['lastname'] : "");
				
					$response = $this->send2Leadbyte($email,$firstname,$lastname,$ip,$url);
					
				}
				
			}
 
	}

	
	public function processIncompletesBatchCsv(){
	    	//$times['start'] = date('Y-m-d H:i:s', strtotime("now") - 7200); //two hours ago
	    	$times['start'] = '2015-11-30 00:00:00';
	    	//$times['end'] = date('Y-m-d H:i:s', strtotime("now") - 3600); //one hour ago
	    	$times['end'] = '2015-12-02 00:00:00';
	    	$start = new MongoDate(strtotime($times['start']));
	    	$end = new MongoDate(strtotime($times['end']));
	    
	    	$sub['lead_created']= array('$gt'=>$start, '$lt'=>$end);
			$sub['lead_data.email']=array('$ne'=>null);
	    	$sub['lead_data.receivableamount'] = array('$exists'=>false);
			$sub['lead_data.calltype'] = array('$eq'=>'internal');
			
			$fields = array('lead_created','lead_data.firstname', 'lead_data.lastname', 'lead_data.email', 'lead_data.ipaddress', 'lead_data.calltype', 'offer_id', 'lead_data.url');
			
	    	$params = array('conditions' => $sub,'fields'=>$fields, 'order'=>array('_id'=>'DESC'));
	    
	    	$mongo_result = $this->ReportTrack->find('all',$params); 
			
			if(count($mongo_result) > 0){
				$sites = Configure::read('SitesAPI.Sites');
				//$fp = fopen('/var/www/html/app/tmp/incompletes.csv','w');
				//Staging Path  - $fp = fopen('/var/www/html/keystone/app/tmp/incompletes.csv','w');  
				foreach($mongo_result as $arr=>$rec){
					$offer_id = $rec['ReportTrack']['offer_id'];
					
					print_r($rec['ReportTrack']['lead_created']);
echo $rec['ReportTrack']['lead_created']->sec;exit;
					$data = $rec['ReportTrack']['lead_data'];
					$call_type = $data['calltype'];
					$ip = $data['ipaddress'];
					$email = $data['email'];
					$url = $sites[$offer_id]['Url'];
					$firstname = ((!empty($data['firstname']))? $data['firstname'] : "");
					$lastname = ((!empty($data['lastname']))? $data['lastname'] : "");
					$created = date("d/m/Y H:i:s",$rec['ReportTrack']['lead_created']->sec);
					
					$info = array();
					$info[] = $email;
					$info[] = $firstname;
					$info[] = $lastname;
					$info[] = $ip;
					$info[] = $url;
					$info[] = $created;
					print_r($info);exit;
                    fputcsv($fp,$info,',');
						
					
				}

				fclose($fp);
				
			}
 
	}    
    
    
	
	/**
	 * Post to LeadByte List
	 * @param string $email
	 * @param string $ip
	 * @param string $site_url
	 * @return jsonp
	 */
	private function send2Leadbyte($email,$firstname,$lastname,$ip,$site_url) {
		$response = array('status'=>'error', 'message'=>'', 'data'=>'');		
		
		$url = 'https://flatsixmedia.leadbyte.co.uk/api/submit.php?campid=CMINCOMPLETEUSCASH&sid=02';
		$options = array();
		$data= array();
		$data['email'] = $email;
		$data['ipaddress'] = $ip;
		
		$data['source'] = $site_url;
		$data['Opt-In_Date'] = date('d/m/Y H:i:s');

		if($firstname != ""){
			$data['firstname'] = $firstname;
		}
		
		if($lastname != ""){
			$data['lastname'] = $lastname;
		}
		

		$socket = new HttpSocket();
		$response = $socket->post($url, $data, $options);
		
		return $response->body;		
	}
    
    
	
	
    
    
}    
