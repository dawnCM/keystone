<?php
/**
 * Holiday Model
 *
 * This model contains Holiday data.
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
class Holiday extends AppModel
{
	public $name = 'Holiday';
	public $useTable = 'holiday';

	public function getHolidayList($days=90) {
		$today = date('Y-m-d');
		$target = date('Y-m-d', strtotime($today) + (24*3600*$days));
		
		$data = $this->find('all', array(
				'fields' => array('Holiday.name', 'Holiday.hdate'),
				'conditions' => array('Holiday.hdate >= "'.$today.'" AND Holiday.hdate <= "'.$target.'"')));
		
		return $data;
	}
		
	public function isHoliday($date) {
		
	}
}