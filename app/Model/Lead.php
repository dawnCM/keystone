<?php
/**
 * Lead Model
 *
 * This model contains the function for the lead controller.
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
class Lead extends AppModel {
	public $useTable = 'lead';
	public function parentNode() {
		return null;
	}
}