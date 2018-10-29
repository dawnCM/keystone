<?php
/**
 * Billing model
 *
 * This file is dashboard model file. You can put all
 * dashboard model-related methods here.
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
class Billing extends AppModel {
	public $useTable = false;
	public function parentNode() {
		return null;
	}
	
	public function downloadPath($id){
		return APP.'tmp/files/billing/billing_'.$id.'.csv';
	}
}