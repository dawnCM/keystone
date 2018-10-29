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
class Cpf extends AppModel {
	public $actsAs = array('AuditLog.Auditable');
	public $name = 'Cpf';
	public $useTable = 'cpf';
}