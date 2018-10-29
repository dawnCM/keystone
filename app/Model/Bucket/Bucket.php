<?php
/**
 * Bucket Model
 *
 * This model contains the data function for the bucket controller.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     TBD
 * @link          TBD
 * @package       app.Model
 * @since         keyStone v1.0
 * @license       TBD
 */
App::uses('AuthComponent', 'Controller/Component');
class Bucket extends AppModel {
	public $actsAs = array('AuditLog.Auditable');
	public $name = 'Bucket';
	public $useTable = 'buckets';
	
	
	public $validate = array(
			'prefill' => array(
					'rule' => array('decimal', 2),
					'message' => 'Prefill requires a valid monetary amount.'),
			'amount' => array(
					'rule' => array('decimal', 2),
					'message' => 'Amount requires a valid monetary amount.'),
			'override_payout' => array(
					'rule' => array('decimal', 2),
					'message' => 'Amount requires a valid monetary amount.'),
			'prefill_payback' => array(
					'rule' => array('decimal',2),
					'message' => 'Prefill payback requires a valid percentage (20.00)'),
	);
	
	/**
	 * Convert a full bucket list into and aggregate list by affiliate id.
	 * @param array $buckets
	 * @return array $aggregate
	 */
	public function aggregateBuckets($buckets){
		$aggregate = array();
		foreach($buckets AS $key=>$aff){
			if(count($aff['Bucket'])>0){
				$aggregate[$aff['Affiliate']['id']]['name']=$aff['Affiliate']['affiliate_name'];
				$aggregate[$aff['Affiliate']['id']]['wallet']=$aff['Affiliate']['wallet'];
				foreach($aff['Bucket'] as $id=>$bucket){
					$aggregate[$aff['Affiliate']['id']]['amount'] += $bucket['amount'];
					$aggregate[$aff['Affiliate']['id']]['prefill'] += $bucket['prefill'];
				}
				$aggregate[$aff['Affiliate']['id']]['buckets'] = $aff['Bucket'];
			}
		}
		
		return $aggregate;
	}
		
	/**
	 * Function for calculating the net/revshare.
	 * @param $amount
	 * @param $margin
	 * @return array
	 */
	public function calculateRevenueSplit($amount, $margin){
		$revenue['net'] = number_format(($margin/100) * $amount,2,'.','');
		$revenue['revenue_share'] = number_format($amount - $revenue['net'],2,'.','');
	
		return $revenue;
	}


	
	
	


}