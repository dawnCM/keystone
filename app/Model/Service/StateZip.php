<?php
/**
 * StateZip Model
 *
 * This model contains StateZip data.
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
class StateZip extends AppModel
{
	public $name = 'StateZip';
	public $useTable = 'state_zips';
	
	/**
	 * Returns the matching state and city for a given zipcode.
	 * @param string $zip
	 * @return object
	 */
	public function getStateCity($zip){
		$response = array('status'=>'error','data'=>'');
		$data = $this->find('first', array(
				'fields' => array('StateZip.state', 'StateZip.city', 'StateZip.zip'),
				'conditions' => array('StateZip.zip' => $zip)
		));
		
		return $data;
	}
}