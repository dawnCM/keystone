<?php
/**
 * LeadUsers Model
 *
 * This model contains the data function for the API controller for managinig user.
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
class LeadUser extends AppModel {
	//public $actsAs = array('AuditLog.Auditable');
	public $name = 'LeadUser';	
	public $useTable = 'lead_users';
}
?>