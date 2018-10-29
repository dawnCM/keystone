<?php
/**
 * Application model for CakePHP.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
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

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {
	
	/**
	 * Used by audit plugin to determin who made changes.
	 * @return array
	 */
	public function currentUser(){
		App::uses('CakeSession', 'Model/Datasource');
		$cu['id'] = CakeSession::read('Auth.User.id');
		$cu['description'] = CakeSession::read('Auth.User.first_name').' '.CakeSession::read('Auth.User.last_name');
		return $cu;
	}
}