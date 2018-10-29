<?php
/**
 * Ancillary Model
 *
 * This model contains the data function for the sites controller.
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

class Ancillary extends AppModel {
	public $actsAs = array('AuditLog.Auditable');
	public $name = 'Ancillary';
	public $useTable = 'ancillary';
	
	public $validate = array(
			'name' => array(
					'rule' => 'notEmpty',
					'message' => 'Ancillary Name Field required.',
					'required' => true
			),
			);
}
?>