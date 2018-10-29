<?php
/**
 * Billing Group Model
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
class BillingGroup extends AppModel {
	public $actsAs = array('AuditLog.Auditable');
	public $name = 'BillingGroup';	
	public $useTable = 'billing_groups';
	
	public $hasMany = array(
			'BillingGroupContracts' => array(
					'className' => 'BillingGroupList',
					'foreignKey' => 'billing_group_id',
					'fields' => array('contract_id')
			)
	);
}