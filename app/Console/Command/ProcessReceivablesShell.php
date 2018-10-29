<?php
class ProcessReceivablesShell extends AppShell {
	public $uses = array('Bucket','Track', 'Receivable', 'Payout', 'Margin');
    public function main() {

		$sql = 'SELECT a.id, a.bucket_id, a.amount, a.margin, a.payout, a.track_id, b.request_id, b.campaign_id, b.lead_id, b.offer_id, c.sub_id, c.prefill, c.prefill_payback, c.override_margin, c.override_payout
				FROM receivables a, track b, buckets c
				WHERE a.processed =0
					AND a.track_id = b.id
					AND a.bucket_id = c.id
					AND c.status_id =5
				ORDER BY a.id ASC';
					
		$records = $this->Receivable->query($sql, $cachequeries = false);
		
		
		if(!$records)exit;
	
		foreach($records as $k){
			//Merge arrays into one data array	
			$m_data = array_merge($k['a'],$k['b'],$k['c']);
			
			//Gather all the data needed to proceed
			$receivable_id = $m_data['id'];
			$bucket_id = $m_data['bucket_id'];
			$track_id = $m_data['track_id'];
			$amount = (FLOAT)$m_data['amount'];
			$request_id = $m_data['request_id'];
			$campaign = $m_data['campaign_id'];
			$sub = $m_data['sub_id'];
			$override_margin = $m_data['override_margin'];
			$override_payout = $m_data['override_payout'];
			$margin = (( $override_margin > 0.00 ) ? $override_margin : $m_data['margin']);
			$payout = (( $override_payout > 0.00 ) ? $override_payout : $m_data['payout']);
			$cake_lead = $m_data['lead_id'];
			$offer_id = $m_data['offer_id'];
			$prefill = (FLOAT)$m_data['prefill'];
			$prefill_payback = (FLOAT)$m_data['prefill_payback']; //Use as percentage
			$ADD_TO_MARGIN_QUEUE = $this->_formatDecimal((FLOAT)($margin/100) * $amount);
			$ADD_TO_BUCKET = $this->_formatDecimal((FLOAT)$amount - $ADD_TO_MARGIN_QUEUE); 
			
			
			//PAYBACK LOGIC START
			$payback = false;
			if($prefill > 0.00){
				$payback = true;
				
				$ADD_TO_PAYBACK = $this->_formatDecimal((FLOAT)($prefill_payback/100) * $ADD_TO_BUCKET);
				
				//Make sure that what we deduct from prefill, will not take prefill below 0.00
				if($ADD_TO_PAYBACK > $prefill){
					$payback_difference = $this->_formatDecimal((FLOAT)$ADD_TO_PAYBACK - $prefill);
			
					//Update the Add To Payback amount
					$ADD_TO_PAYBACK = $this->_formatDecimal((FLOAT)$ADD_TO_PAYBACK - $payback_difference);
						
				}
				
				//Subtract this amount from prefill amount
				$DEDUCT_FROM_PREFILL = $ADD_TO_PAYBACK;
				//calculate the new prefill to be used in bucket update
				$DEDUCT_FROM_PREFILL_CALCULATED = $this->_formatDecimal((FLOAT)$prefill - $DEDUCT_FROM_PREFILL);
				
				//Minus payback from calculated add to bucket amount
				$ADD_TO_BUCKET = $this->_formatDecimal((FLOAT)$ADD_TO_BUCKET - $DEDUCT_FROM_PREFILL);
				
			}
			//PAYBACK LOGIC END
			
			//START THE BUCKETS PROCESS
			
			//Add Receivable minus the margin to the Buckets Table.
			$amount_pull = $this->Bucket->find('first', array(	'conditions' 	=> 	array('Bucket.id'=>$bucket_id),
																'fields'		=>	'Bucket.amount'));
			
			//Get current bucket amount								
			$pulled_amount = (FLOAT)$amount_pull['Bucket']['amount'];
			
			$this->Bucket->set('id', $bucket_id);
			//Add calculated bucket amount to current amount
			$new_bucket_amount = $this->_formatDecimal($ADD_TO_BUCKET+$pulled_amount);
			$this->Bucket->set('amount', $new_bucket_amount);
			
			//To cut down on db calls, add prefill update here since buckets is being updated anyway as the first update
			if($payback){
				$this->Bucket->set('prefill', $DEDUCT_FROM_PREFILL_CALCULATED);	
			}
			
			$result = $this->Bucket->save();
			$this->Bucket->clear();
			//End Add Receivable
			
			//Insert margin into the the Margin table
			$this->Margin->set('bucket_id', $bucket_id);
			$this->Margin->set('track_id', $track_id);
			$this->Margin->set('amount', $ADD_TO_MARGIN_QUEUE);
			$result = $this->Margin->save();
			$this->Margin->clear();
			//End Insert Margin
			
			//IF True, Add the payback amount to Margin
			if($payback){
				$this->Margin->set('bucket_id', $bucket_id);
				$this->Margin->set('track_id', $track_id);
				$this->Margin->set('amount', $ADD_TO_PAYBACK);
				$result = $this->Margin->save();
				$this->Margin->clear();	
			}
			//End margin insert for Payback
			
			//Bucket Tip	
			if($new_bucket_amount >= $payout){
				
				//Tip bucket	
				$this->Bucket->set('id', $bucket_id);
				//Minus payout from new bucket amount
				$deducted_bucket_amount = $this->_formatDecimal((FLOAT)$new_bucket_amount - $payout);
				$this->Bucket->set('amount', $deducted_bucket_amount);
				$this->Bucket->set('tipped', date("Y-m-d H:i:s"));
				$result = $this->Bucket->save();
				$this->Bucket->clear();	
				//End Tip bucket
				
				//Add Payout
				$this->Payout->set('bucket_id', $bucket_id);
				$this->Payout->set('track_id', $track_id);
				$this->Payout->set('amount', $payout);
				$result = $this->Payout->save();
				$this->Payout->clear();
				//End Add Payout
				
			}
			//End Bucket Tip	
			
			//Mark Receivable as processed
			$this->Receivable->set('id', $receivable_id);
			$this->Receivable->set('processed', 1);
			$result = $this->Receivable->save();
			$this->Receivable->clear();	
			//End Mark Receivable	
	
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