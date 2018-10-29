<?php
/**
 * KQueue model
 *
 * This file is the queue model file. You can put all
 * queue model-related methods here.
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
class Kqueue extends AppModel {
	public $actsAs = array('AuditLog.Auditable');
	public $name = 'Kqueue';
	public $useTable = 'queue';
	
	public function parentNode() {
		return null;
	}
	
	/**
	 * Add a job to the queue
	 * @return boolean
	 */
	public function add(){
		$this->save($this->data);
		return true;
	}
	
	/**
	 * Delete a record from the queue.
	 * @return boolean
	 */
	public function delete(){
		$this->delete($this->data);
		return true;
	}
	
	/**
	 * Set the start status and time.
	 * @return boolean
	 */
	public function start(){
		$this->data['Kqueue']['status'] = 1;
		$this->data['Kqueue']['start_date'] = date('Y-m-d h:i:s');
		$this->save($this->data);
		return true;
	}
	
	/**
	 * Set the complete status and time.
	 * @return boolean
	 */
	public function complete(){
		$this->data['Kqueue']['status'] = 2;
		$this->data['Kqueue']['complete_date'] = date('Y-m-d h:i:s');
		$this->save($this->data);
		return true;
	}
}