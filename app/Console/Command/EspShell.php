<?php 
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
App::uses('CakeEmail', 'Network/Email');
App::uses('HttpSocket', 'Network/Http');



class EspShell extends AppShell {
	public $uses = array('Kqueue','Esp');
	    
    public function main() {
		return false;
    }
	
	
	public function add2SuppressionList($email){
		
		$this->Esp->data['Esp'] = array('Email'=>$email);	
		$type = "api_main"; //Live call
		$rsp_array = $this->Esp->sendToEsp(0, $type);
		
	}
	
	
	
	
	/*
	 * Pulls all Esps and makes a call to pull down the daily blacklist.  Stores in kqueue to be processed later
	 */
	public function pullBlackList(){
		
		$pull_configs = $this->Esp->find('all', array('conditions'=>array()));
		
		if(count($pull_configs) == 0) return false;
		
		foreach($pull_configs as $k=>$v){
			$config = array();
			$id = $v['Esp']['id'];
			$name = $v['Esp']['name'];
			$config = json_decode($v['Esp']['json'], true);
			$api_config = $config['BlackList'];
			
			if(empty($api_config['RequestType']) || empty($api_config['RequestUrl']))continue;
			
			$this->Esp->data['Esp'] = array();	
			$type = "blacklist_main"; //Live call
			$rsp_array = $this->Esp->sendToEsp($id, $type);
		
			
			if($rsp_array[0]['success'] == 'true'){
				
				if(preg_match('/ExpertSender/', $name)){
					$email_array = $this->expertSenderBlackListV1($rsp_array[0]['response']);
					if(is_array($email_array) && count($email_array) > 0){
						$this->Kqueue->create(); //Clear model and start new
						$this->Kqueue->set('name', 'Process Blacklist');
						$this->Kqueue->set('console', 'Esp');
						$this->Kqueue->set('function', 'processBlacklist');
						$this->Kqueue->set('data', json_encode($email_array));
						$this->Kqueue->add();
						
					}
				}else if(preg_match('/ReachMail/', $name)){
					$email_array = $this->reachMailBlackListV1($rsp_array[0]['response']);
					if(is_array($email_array) && count($email_array) > 0){
						$this->Kqueue->create(); //Clear model and start new
						$this->Kqueue->set('name', 'Process Blacklist');
						$this->Kqueue->set('console', 'Esp');
						$this->Kqueue->set('function', 'processBlacklist');
						$this->Kqueue->set('data', json_encode($email_array));
						$this->Kqueue->add();
						
					}
				}
			}
			
		}
	}
	
	
	
	public function processBlackList(){
		$data = json_decode($this->args[0], true);
		$this->Kqueue->set('id', $this->args[1]);
		$this->Kqueue->start();
		
		if(count($data) > 0){
			
			foreach($data as $email){
				$socket = new HttpSocket(array('ssl_verify_peer'=>false, 'ssl_verify_host'=>false));
				$params =  array(	'campid'=>"ESPREMOVED",
									'sid'=>"02",
									'email'=>$email
				);

				$response = $socket->post('https://flatsixmedia.leadbyte.co.uk/api/submit.php', $params);
				
				if($response->body == '1 | OK'){
					//echo 'yes';
				}else{
					//print_r($response->body);
				}
			}
		}
		
		$this->Kqueue->complete();
	}
	
	
	
	
	
	private function expertSenderBlackListV1($rsp){
		
		$xml = simplexml_load_string((STRING)$rsp);	
		$json = json_encode($xml);
		$data = json_decode($json,TRUE);
		$email_array = array();
		
		foreach($data['Data']['RemovedSubscribers']['RemovedSubscriber'] as $k=>$v){
			$email_array[] = $v['Email'];
		}

		return $email_array;
	}
	
	private function reachMailBlackListV1($rsp){
		
		$json_to_array = json_decode($rsp,true);
		
		if(empty($json_to_array))exit;
		
		
		$email_array = array();
		foreach($json_to_array as $k=>$v){
			$email_array[] = $v['Email'];
		}

		return $email_array;
	}
	
	
	
	
}