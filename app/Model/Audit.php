<?php
/**
 * Audit Model
 *
 * This model contains the data function for the audit database table.
 * This model does not have a controller.
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
class Audit extends AppModel {
	public $name = 'Audit';
	public $useTable = 'audits';
	public $hasMany = array(
			'AuditDelta' => array(
					'className' => 'AuditDelta',
					'foreignKey' => 'audit_id'
			)
	);
	
}