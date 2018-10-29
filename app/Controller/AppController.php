<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
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

App::uses('Controller', 'Controller');
App::uses('CakeTime', 'Utility');
App::uses('CakeEmail', 'Network/Email');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		TBD - <link to git wiki>
 */
class AppController extends Controller {
	public $uses = array('User');
	public $components = array(
			'Auth' => array(
				'authenticate' => array(
                        'all' => array (
                                        'scope' => array('User.status' => 1)
                        ),
                        'Form' => array(
                                        'fields' => array('username'=>'email')
                        )
                ),
				'authorize' => array('Controller'),
				'loginRedirect' => array('controller' => 'dashboard', 'action' => ''),
				'logoutRedirect' => array('controller' => '/', 'action' => ''),
				'loginAction' => array('controller' => '/', 'action' => ''),
						
				),
			'Session',
			'RequestHandler',
			'Cookie'
	);
	
	public $helpers = array('Html', 'Form', 'Session');
		
	public function beforeFilter() {
		if ($this->request->is('ajax')) {
			$basekey = Configure::read('Ajax.nonce');
			$userkey = $this->Session->read('Auth.User.id');
			if($this->request->header('x-keyStone-nonce') != $basekey || $userkey < 1){
				throw new ForbiddenException();
				exit;
			}
		}
			
		//Smart Load Js
		$this->set('loadLoginJS',false);
		$this->set('loadDashboardJS',false);
		$this->set('loadUsersJS',false);
		$this->set('loadBucketsJS',false);
		$this->set('loadCpfJS',false);
		$this->set('loadAffiliatesJS',false);
		$this->set('loadReportsJS',false);
		$this->set('loadToolsJS',false);
		$this->set('loadBillingJS',false);
		$this->set('loadPingtreeJS',false);
		$this->set('loadFraudJS',false);
		$this->set('loadListmgtJS',false);
		$this->set('loadListmanagementJS',false);
		$this->set('loadSitesJS',false);
				
		//KeyStone Cookie
		$this->Cookie->name="keystone";
		$this->Cookie->time=432000; //5 days
		$this->Cookie->key = '=+-keyStone314~(.)Y(.)~314keyStone-+=';
		$this->Cookie->type('aes');
	}
	
	/**
	 * This function is required for auth to work, but it is being overloaded within each controller
	 * as __isAuthorized.  It was decided that for now it is easier to maintain controller/method access
	 * at the controller level.  I'm sure I will kick myself for this later but time constraints dictate.
	 */
	public function isAuthorized($user) {
		$group = Configure::read('Group');
		//Administrator
		if ( isset($user['group_id']) && $user['group_id'] === $group['Administrator'] ) {
			return true;
		}
		$this->Session->setFlash('Your account is not authorized for the requested section.','flash_error');
		return false;
	}
}