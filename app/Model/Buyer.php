<?php
/**
 * Buyer Model
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
class Buyer extends AppModel {
	public $actsAs = array('Containable');
	public $name = 'Buyer';
	public $useTable = 'buyers';
	
	public $hasMany = array(
			'Contract' => array(
					'className' => 'Contract',
					'foreignKey' => 'buyer_id'
			)
	);
	
	public function getPingtree($cake=false){
		if($cake === true){
			$params = array(
					'fields'=>array('remote_buyer_id','buyer_name'),
					'order'=>array('buyer_name asc')
			);
		}else{
			$params = array(
					'fields'=>array('id','buyer_name'),
					'order'=>array('buyer_name asc')
			);
		}
		
		return $this->find('list', $params);
	}
	
	public function getBuyer($buyer_id,$cake=false){
	
	}
}