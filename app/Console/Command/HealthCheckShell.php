<?php
App::uses('CakeEmail', 'Network/Email');
App::uses('HttpSocket', 'Network/Http');

class HealthCheckShell extends AppShell {
	public $uses = array('Affiliate', 'LeadTrack', 'User', 'Cake', 'Service', 'Api');
	
	/**
	 * Runs every 45 minutes to verify system integrity.
	 */
    public function heartBeat() {
    	$testPacket = new Object();
    	$pass = true;
		
		$testPacket->service = $this->Service->heartBeat();
    	$testPacket->user = $this->User->heartBeat();
		$testPacket->affiliate = $this->Affiliate->heartBeat();
		$testPacket->lead = $this->LeadTrack->heartBeat();
		//$testPacket->api = $this->Api->heartBeat();
		$testPacket->cakeCpaMissingNotes = $this->Cake->heartBeatCpaNotesCheck();
    			
		switch(false){
			case is_array($testPacket->user):
			$pass = false;
			$testPacket->error = 'User';
			$subject = 'User Check Failed';
			break;
			
			case is_array($testPacket->affiliate):
			$pass = false;
			$testPacket->error = 'Affiliate';
			$subject = 'Affiliate Check Failed';
			break;
			
			case is_array($testPacket->lead):
			$pass = false;
			$testPacket->error = 'Lead';
			$subject = 'Lead Check Failed';
			break;
			
			case ($testPacket->service == '200'):
			$pass = false;
			$testPacket->error = 'Service';
			$subject = 'Service Check Failed';
			break;
			
			case ($testPacket->cakeCpaMissingNotes['passed'] == true):
			$pass = false;
			$testPacket->error = 'Cake CPA Campaign Missing Notes Field';
			$subject = 'Cake Campaign Error';
			break;
			
			/*
			case is_array($testPacket->api):
			$pass = false;
			$testPacket->error = 'Api';
			$subject = 'API Check Failed';
			break;
			*/
		}
				
		if($pass === false){
			
			$this->notify(json_encode($testPacket),'keyStone : HeartBeat : '.$subject);
		}
    }
    
    /**
     * Runs each night to measure system growth.
     */
    public function physical() {
    	//count mysql rows
    	//count mongo rows
    	//get log file size
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