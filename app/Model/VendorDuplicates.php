<?php
/**
 * Vendor Duplicates Model
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
class VendorDuplicates extends AppModel {
	public $name = 'VendorDuplicates';	
	public $useTable = 'vendor_duplicates';
}