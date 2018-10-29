<?php
/**
 * FraudBrowser  Model
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
class FraudBrowser extends AppModel {
	public $actsAs = array('AuditLog.Auditable');
	public $name = 'FraudBrowser';	
	public $useTable = 'fraud_browser';
	

	public function addRecord($fdata){
		$this->set(array(
			'affiliate_id' 	=> $fdata['affiliate_id'],
			'browser_id'	=> $fdata['browser_id'],
			'ip_id'    		=> sprintf("%u", ip2long($fdata['ip_id'])),
			'track_id'		=> $fdata['track_id'],
		));
		
		$this->save();
		return true;
	}

	
}