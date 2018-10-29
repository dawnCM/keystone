<?php
/**
 * NpaNpx Model
 *
 * This model contains NpaNpx data.
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
class NpaNpx extends AppModel
{
	public $name = 'NpaNpx';
	public $useTable = 'npa_npx';
	
	public function isMatch($npanpx) {
		$count = $this->find('count',array('conditions' => array('NpaNpx.npa_npx' => $npanpx)));
		if ($count > 0) {
			return true;
		} else {
			return false;
		}
	}
}