<?php
/**
 * Contract Model
 *
 * This model contains the function storage of the pingtree information from cake into our local db.
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
class Contract extends AppModel {
	public $name = 'Contract';
	public $useTable = 'contracts';
}