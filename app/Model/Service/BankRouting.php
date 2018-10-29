<?php
/**
 * BankRouting Model
 *
 * This model contains BankRouting data.
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
class BankRouting extends AppModel
{
	public $name = 'BankRouting';
	public $useTable = 'bank_abas';

	/**
	 *
	 */
	public function getBankInfo($aba) {
		$data = $this->find('first', array(
				'fields' => array('BankRouting.name','BankRouting.address','BankRouting.city','BankRouting.state','BankRouting.zip','BankRouting.phone'),
				'conditions' => array('BankRouting.aba' => $aba)
		));
		
		return $data;
	}
}