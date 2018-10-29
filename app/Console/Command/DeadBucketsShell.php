<?php 
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
App::uses('CakeEmail', 'Network/Email');


class DeadBucketsShell extends AppShell {
	public $uses = array('Bucket');
	    
    public function main() {
		return false;
    }
   
	/**
	 * Pull Buckets that haven't tipped in 3 to 4 weeks
	 */
	public function findUntippedBuckets(){
		$params = array('fields'=>		array('Bucket.id','Bucket.campaign_id','Bucket.amount','Bucket.tipped'),
						'order'=>		array('Bucket.tipped DESC'),
						'conditions'=>	array('Bucket.tipped >' => date('Y-m-d 00:00:00', strtotime("-4 weeks")), //Tip happened after 4 weeks in past
											  'Bucket.tipped <' => date('Y-m-d 23:59:59', strtotime("-3 weeks")) //No tip after 3 weeks in past
										)
		
		);
		$buckets = $this->Bucket->find('all', $params);
		
		if(count($buckets) == 0)exit;
		$file = new File(APP."tmp/untippedbuckets.csv", true);
		$file->write('"Bucket ID","Campaign","Amount","Last Tipped"');
		$file->write("\n");
		
		foreach($buckets as $index=>$bucket){
			$row = $bucket['Bucket'];
			if(empty($row))continue;
			
			$file->write('"'.$row['id'].'","'.$row['campaign_id'].'","'.$row['amount'].'","'.$row['tipped'].'"');
			$file->write("\n");
		}
		
		$subject = "Sick Buckets";
		$msg = "The last bucket tip was between the dates of ".date('Y-m-d 00:00:00', strtotime("-4 weeks"))." and ".date('Y-m-d 23:59:59', strtotime("-3 weeks"));
		$file_name =  $file->name;
		$this->notify($msg, $subject, $file_name);

		$file->delete();
		exit;
	}


	/**
	 * Clear out buckets that haven't tipped in a month
	 */
	public function clearUntippedBuckets(){
		$params = array('fields'=>		array('Bucket.id','Bucket.campaign_id','Bucket.amount','Bucket.tipped'),
						'order'=>		array('Bucket.tipped DESC'),
						'conditions'=>	array('Bucket.tipped <=' => date('Y-m-d 00:00:00', strtotime("-4 weeks")),
											  'Bucket.amount !=' => '0.00'
											 ), 
		
		);
		$buckets = $this->Bucket->find('all', $params);
		
		if(count($buckets) == 0)exit;
		$file = new File(APP."tmp/clearedbuckets.csv", true);
		$file->write('"Bucket ID","Campaign","Amount","Last Tipped"');
		$file->write("\n");
		
		$ids = array();
		foreach($buckets as $index=>$bucket){
			$row = $bucket['Bucket'];
			if(empty($row))continue;
			
			$file->write('"'.$row['id'].'","'.$row['campaign_id'].'","'.$row['amount'].'","'.$row['tipped'].'"');
			$file->write("\n");
			$ids[] = $row['id'];
		}
		
		
		$this->Bucket->query("Update buckets set amount='0.00',prefill='0.00',prefill_payback='20.00',override_margin='0.00',override_payout='0.00' where id in('".implode("','",$ids)."')");
		
		$subject = "Cleared Buckets";
		$msg = "The buckets have not tipped since ".date('Y-m-d 00:00:00', strtotime("-4 weeks"))." so they were cleared.";
		$file_name =  $file->name;
		$this->notify($msg, $subject, $file_name);

		$file->delete();
		exit;
	}
	



	private function notify($msg,$subject, $file="") {
    	$email = new CakeEmail();
		$email->from(array('noreply@leadstudio.com'=>'Keystone'));
		$email->to('nick@leadstudio.com');
		$email->subject($subject);
		if($file != "")$email->attachments(APP.'tmp/'.$file);
		$email->send($msg);
		
    	 
    	return true;
    }











}		