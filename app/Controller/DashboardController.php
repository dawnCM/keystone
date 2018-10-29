<?php
/**
 * Static content controller.
*
* This controller will render views from views/dashboard/
*
* Licensed under The MIT License
* For full copyright and license information, please see the LICENSE.txt
* Redistributions of files must retain the above copyright notice.
*
* @copyright     TBD
* @link          TBD
* @package       app.Controller
* @since         keyStone v1.0
* @license       TBD
*/
class DashboardController extends AppController {
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->autoRedirect = false;
		$this->__isAuthorized($this->Session->read('Auth.User'));
		$this->layout = 'dashboard';
	}

	/**
	 * Main dashboard page functionality.
	 * Renders view from /View/Dashboard/index.ctp
	 */
	public function index(){
		$this->set('loadDashboardJS',true);
	}
	
	/**
	 * Specify what user group has access.  For development speed, this is not in ACL.
	 * @todo Add ACL functionality to keyStone.
	 * @param array $user
	 * @return boolean
	 */
	private function __isAuthorized($user) {
		//Management
		if (in_array($this->Session->read('Auth.User.Group.id'),array('2','3','4','5'))) {
			$this->Auth->allow('index');
			return true;
		}
		return false;
	}
}