<?php
/**
 * User Model
 *
 * This model contains the function for the user controller.
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
class User extends AppModel {
	public $actsAs = array('AuditLog.Auditable');
	public $name = 'User';
	public $useTable = 'users';
	public $displayField = 'email';
	public $belongsTo = array(
			'Group' => array(
					'fields' => array('id','name')
			)
	);
	
	public $validate = array(
			'first_name' => array(
					'rule' => 'notEmpty',
					'message' => 'First Name Field required.',
					'required' => true
			),
			'last_name' => array(
					'rule' => 'notEmpty',
					'message' => 'Last Name Field required.',
					'allowEmpty' => FALSE,
					'required' => true
			),
			'email' => array(
					'rule' => 'email',
					'message' => 'Email does not appear to be valid.',
					'allowEmpty' => FALSE,
					'required' => true
			),
			'title' => array(
					'rule' => 'notEmpty',
					'message' => 'Title Field required.',
					'required' => true
			),
			'password' => array(
					'rule' => array('minLength', 5),
           			'message' => 'Minimum 5 characters long',
					'required' => true,
			),
			
	);
	
	public $virtualFields = array(
		'full_name' => 'CONCAT(User.first_name, " ", User.last_name)'
	);
		
    public function beforeSave($options = array()) {
    	if(isset($this->data['User']['password'])) {
        	$this->data['User']['password'] = AuthComponent::password($this->data['User']['password']);
    	}
        return true;
    }
    
    public function heartBeat(){
    	return $this->find('first', array('conditions'=>array('User.id'=>1)));
    }
}