<?php
/**
 * Billing Group List Model
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
class BillingGroupList extends AppModel {
	public $actsAs = array('AuditLog.Auditable');
	public $name = 'BillingGroupList';	
	public $useTable = 'billing_group_list';
	
	public $belongsTo = array(
			'Contract' => array(
					'className' => 'Contract',
					'foreignKey' => 'contract_id',
					'fields' => array('id', 'remote_contract_id')
			),
			'BillingGroup' => array(
					'className' => 'BillingGroup',
					'foreignKey' => 'billing_group_id'
			)
			
			
	);
}