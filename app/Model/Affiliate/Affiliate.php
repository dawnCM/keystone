<?php
/**
 * Affiliate Model
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
class Affiliate extends AppModel {
	public $actsAs = array('AuditLog.Auditable','Containable');
	public $name = 'Affiliate';
	public $useTable = 'affiliates';
	
	public $hasMany = array(
			'Bucket' => array(
					'className' => 'Bucket',
					'foreignKey' => 'affiliate_id'
			),
			'AffiliateIp' => array(
					'className' => 'AffiliateIp',
					'foreignKey' => 'affiliate_id',
					'fields' => 'ip, id'
			),
			'AffiliateDomain' => array(
					'className' => 'AffiliateDomain',
					'foreignKey' => 'affiliate_id',
					'fields' => 'domain, id'
			),
	);
	
	public function heartBeat(){
		return $this->find('first', array('conditions'=>array('Affiliate.id'=>1)));
	}
	
}