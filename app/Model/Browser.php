<?php
/**
 * Browser  Model
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
class Browser extends AppModel {
	public $actsAs = array('AuditLog.Auditable');
	public $name = 'Browser';	
	public $useTable = 'browsers';
	
	
	
	
	/*
	 * param String $browser
	 * 
	 * returns browser id.  
	 */
	public function getBrowserId($browser){
		$hash = md5($browser);
		$result = $this->find('first', array('fields'=>array('id'),
							  		'conditions'=>array('browser_hash'=>$hash)
							  )
				   );
		
		if(!empty($result)){
			return $result['Browser']['id'];
		}else{
			$this->set(array(
				'browser_name' 		=> $browser,
				'browser_hash'		=> $hash,
			));
			$this->save();
			return $this->getInsertID();
		}
		
	}
	
}