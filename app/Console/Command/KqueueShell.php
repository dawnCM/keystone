<?php 
class KqueueShell extends AppShell {
	public $uses = array('Kqueue');
    
    public function main() {
		return false;
    }
    
	/**
	 * Process for checking the queue table for waiting jobs.  Currently we limit this to two queue processes at a time.
	 * Post evaluation results may increase this at a later date.
	 */
	public function process(){
    	$queue = $this->Kqueue->find('all', array('conditions' => array('Kqueue.status =' => 0), 'limit'=>2));
		
    	if(!empty($queue)){
			foreach($queue AS $job){
				$this->Kqueue->id = $job['Kqueue']['id'];
				$this->Kqueue->start();
				$this->dispatchShell($job['Kqueue']['console'], $job['Kqueue']['function'], $job['Kqueue']['data'], $job['Kqueue']['id']);
				
			}
    	}
    }
}