<?php
class ProcessPayoutsShell extends AppShell {
	public $uses = array('Bucket', 'Track', 'LeadTrack', 'Receivable', 'Payout', 'Margin', 'Api');
    public function main() {
    	$records = false;
		$sql = 'SELECT a.id, a.bucket_id, a.amount, a.track_id, b.request_id, b.campaign_id, b.lead_id, b.offer_id, b.affiliate_id, c.margin, c.payout, c.amount as rec_amount
				FROM payouts a, track b, receivables c
				WHERE a.processed =0
					AND a.track_id = b.id
					AND a.track_id = c.track_id
				ORDER BY a.id ASC';
					
		$records = $this->Payout->query($sql, $cachequeries = false);

		if(!$records)exit;
		
		foreach($records as $k){
			//Merge arrays into one data array	
			$m_data = array_merge($k['a'],$k['b'],$k['c']);
			
			//Gather all the data needed to proceed
			$payout_id = $m_data['id'];
			$bucket_id = $m_data['bucket_id'];
			$amount = (FLOAT)$m_data['amount'];
			$request_id = $m_data['request_id'];
			$campaign_id = $m_data['campaign_id'];
			$track_id = $m_data['track_id'];
			
			$lead_id = $m_data['lead_id'];
			$offer_id = $m_data['offer_id'];
			$affiliate_id = $m_data['affiliate_id'];
			$margin = $m_data['margin'];
			$payout = (FLOAT)$m_data['payout'];
			$rec_amount = (FLOAT)$m_data['rec_amount'];
			
			
			$pixel_url = "http://leadstudiotrack.com/p.ashx?o=".$offer_id."&f=pb&t=".$lead_id."&r=".$request_id."&ap=".$amount;
			
			$agent= 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
	
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_VERBOSE, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_USERAGENT, $agent);
			curl_setopt($ch, CURLOPT_URL,$pixel_url);
			$result=curl_exec($ch);
	
			
			
			
			//Mark Payout as processed
			$this->Payout->set('id', $payout_id);
			$this->Payout->set('processed', 1);
			$result = $this->Payout->save();
			
			$this->Payout->clear();	
			//End Mark Payout
			
			
			$json = json_encode(array(	'PaidAmount' => (STRING)$amount,
										'Margin' =>  (STRING)$this->_formatDecimal($margin/100),
										'MarginAmount' => (STRING)$this->_formatDecimal(($margin/100) * $amount)
									
			));
			
			//$this->Track->writeLead($track_id, $json);
			$trackdata['LeadTrack']['track_id'] = $track_id;
			$trackdata['LeadTrack']['json_vars'] = $json;
			$this->LeadTrack->save($trackdata);
			//END BUCKETS PROCESS
		}
		
		exit;	
			
	}
	
	private function _formatDecimal($decimal){
		$rounded = round($decimal, 2);
		$rounded_precision = number_format($rounded, 2, '.', '');
		return $rounded_precision;
	}
}

?>	