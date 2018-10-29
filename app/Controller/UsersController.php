<?php
/**
 * User Controller
 *
 * This file handles the authentication/authorization of a user to the applications. It also controls the adding, editing 
 * of users to the keyStone system.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     TBD
 * @link          https://github.com/Vexedmonkey/keyStone/wiki/UsersController
 * @package       app.Controller
 * @since         keyStone v1.0
 * @license       TBD
 */
App::uses('CakeEmail', 'Network/Email');

class UsersController extends AppController {
	public $uses = array('User','Audit');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->autoRedirect = false;
		$this->Auth->allow('login','emailexists','processReset','resetpassword');
		$this->__isAuthorized($this->Session->read('Auth.User'));
		$this->set('loadUsersJS',true);
	}
	
	/**
	 * Display a list of users.  This is limited by access, Administrators and managers have the rights
	 * to view,adjust and add.  Everyone else can only see themselves.
	 */
	public function index(){
		$this->layout = 'dashboard';
		
		//Retrieve a list of access groups
		$accessgroups = $this->User->Group->find('list');
		$this->set(compact('accessgroups'));
		
		//Retrieve a list of users - limit by auth
		if(in_array($this->Session->read('Auth.User.Group.id'),array('3','4','5'))){
			$params = array('conditions'=>array('User.email'=>$this->Session->read('Auth.User.email')));
			$this->set('userlist', $this->User->find('all', $params));
		}else{
			$this->set('userlist', $this->User->find('all'));
		}
	}

	/**
	 * Return the user object for the supplied user id.
	 * Restful / json
	 * @param int $id
	 * @access Administrator,Management
	 */
	public function get($id=null){
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		$this->User->setDataSource('slave');
		
		$result = $this->User->find('first', array('conditions'=>array('User.id'=>$id)));
		
		return json_encode($result);
	}

	/**
	 * Method for administrators/managers to add additional users to keyStone.
	 * Post / Redirect
	 * @access Administrator,Management
	 */
	public function add(){
		$this->layout = null;
		$this->autoRender = false;

		if($this->request->is('post')){
			if($this->User->save($this->request->data)) {
				$this->Session->setFlash('The user was added','notify_success');
			}else{
				$this->Session->setFlash('The user could not be saved. Please, try again.','notify_error');
			}
			return $this->redirect('/users');
		}
	}

	/**
	 * Inline update a user field.
	 * Restful / json
	 * @access Administrator, Management
	 */
	public function edit($id=null,$field=null,$value=null){
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		
		$this->User->id = $id;
		$response['status'] = false;
		$response['message'] = '';
		if($this->request->is('ajax')){
			if ($this->User->saveField($field,$value,true)) {
				$response['status'] = true;
				return json_encode($response);
			}
			$response['message'] = 'Field: '.$field.' with the value of '.$value.' could not be saved.';
		}
		else{
			$response['message'] = 'Invalid request';
		}
		return json_encode($response);
	}
	
	/**
	 * Upload user photo, update User table to reflect user photo exists.
	 * @access Administrator, Management
	 * @param string $id
	 */
	public function addphoto($id=null){
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		
		$this->User->id = $id;
		$response['status'] = false;
		$response['message'] = '';
		
		if($this->request->is('ajax')){
			$uploadname = $_FILES['file']['name'];
			$uploadext = end((explode('.',$uploadname)));
			$uploaddir = Configure::read('Upload.path') . '/users/';
			$uploadfile = $uploaddir . md5($this->User->id) . '.' . $uploadext;
			
			if(move_uploaded_file($_FILES['file']['tmp_name'],$uploadfile)){
				$this->User->saveField('photo',1,false);
				$response['message'] = 'User file uploaded successfully.';
			}else{
				$response['message'] = 'File could not be uploaded to server.';
			}
		}
		else{
			$response['message'] = 'Invalid request';
		}
		
		return json_encode($response);
	}
	
	/**
	 * Check to see if the email account exists in the user table, this is a restful function.
	 * Restful / json
	 * @access User
	 * @param string $email
	 * @return boolean
	 */
	public function emailexists($email=null){
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		$this->User->setDataSource('slave');
		
		$user = $this->User->find('first', array('conditions'=>array('User.email'=>$email)));

		if(count($user)>0){
			return json_encode(true);
		}else{
			return json_encode(false);
		}
	}
	
	public function activity($user=null){
		$this->layout = 'dashboard';
		
		//Read from the slave
		$this->Audit->setDataSource('slave');
		
		//If the user is empty
		if($user === null){
			$user = $this->Session->read('Auth.User.id');
		}
		
		//Lock down to management only
		if(!in_array($this->Session->read('Auth.User.Group.id'),array('1','2'))){
			$user = $this->Session->read('Auth.User.id');
		}
				
		$cache['value'] = $this->Audit->find('all', array('conditions' => array('Audit.source_id' => $user)));
		$this->set('audit_trail',$cache['value']);
		
		if(in_array($this->Session->read('Auth.User.Group.id'),array('1','2'))){
			$this->set('userlist', $this->User->find('all'));
			$this->render('/Users/activity_full');
		}
	}

	/**
	 * Log a user into keyStone.
	 * @todo auto_login cookie logic.
	 * @access User
	 */
	public function login() {
		if ($this->request->is('post')) {
			$this->layout = null;
			$this->autoRender = false;

			if ($this->Auth->login()) {
				$usr = $this->Session->read('Auth');

				return $this->redirect($this->Auth->redirectUrl());
			}

			$this->Session->setFlash('Your username or password was incorrect.','flash_error');
			$this->redirect('/');
		}

		if ($this->Session->read('Auth.User')) {
			return $this->redirect($this->Auth->redirectUrl());
		}
		 
		$this->redirect('/');
	}
	
	public function resetpassword($hash='', $date=''){
		$this->layout = null;
		$this->autoRender = false;
		
		if($hash != '' && $date == md5(date('mdy'))){
			$email_address = base64_decode($hash);
			$newpw = $this->confirmedReset($email_address);
			
$email_text = "Your password has been reset to:

{$newpw}";
			
			$email = new CakeEmail();
			$email->from(array('noreply@leadstudio.com'=>'Keystone'));
			$email->to($email_address);
			$email->subject('Keystone Password Reset');
			$email->send($email_text);
			
			$this->Session->setFlash('Your password has been reset, please check your email.','flash_success');
			$this->redirect('/');
		}
		$this->redirect('/');
	}
	
	public function processReset(){
		if($this->request->is('post')){
			$this->layout = null;
			$this->autoRender = false;
			
			$params = array('conditions'=>array('email'=>$this->request['data']['User']['email']));
			$user = $this->User->find('count', $params);
		
			if($user<1){
				$this->Session->setFlash('Unable to reset the password for '.$this->request['data']['User']['email'],'flash_error');
				$this->redirect('/');
			}
			
$email_text = "A request to reset your password for KeyStone has been received.  If you did not request a new password, please ignore this email.
If you did request a new password, please click the link below to confirm your request.  This link is only valid for 24 hours.

https://keystone.leadstudio.com/users/resetpassword/".base64_encode($this->request['data']['User']['email'])."/".md5(date('mdy'));

			$email = new CakeEmail();
			$email->from(array('noreply@leadstudio.com'=>'Keystone'));
			$email->to($this->request['data']['User']['email']);
			$email->subject('Keystone Password Reset Request');
			$email->send($email_text);

			$this->Session->setFlash('Password reset instructions have been emailed to you.','flash_error');
			$this->redirect('/');
		}
		$this->redirect('/');
	}
	
	private function confirmedReset($email){
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890@";
		$password = substr(str_shuffle($chars),0,8);

		$conditions = array('email'=>$email);
		$params = array('conditions'=>$conditions);
		$user = $this->User->find('first',$params);

		$this->User->id = $user['User']['id'];
		$this->User->saveField('password',$password,true);
		
		return $password;
	}

	/**
	 * Log a user out of keyStone
	 * @access User
	 */
	public function logout() {
		$this->Session->destroy();
		$this->Auth->logout();
		$this->redirect('/');
	}
	
	private function __getUsers(){
		//Setup cache
		$cache['hash'] = md5('user_getusers');
		$cache['value'] = false;
		$cache['value'] = Cache::read($cache['hash']);
		
		$this->User->setDataSource('slave');
		
		if($cache['value'] === false){
			$cache['value'] = $this->User->find('all');
			Cache::write($cache['hash'],$cache['value'],'5m');
		}
		
		return $cache['value'];
	}
	
	/**
	 * Specify what user group has access.  For development speed, this is not in ACL.
	 * @todo Add ACL functionality to keyStone.
	 * @param array $user
	 * @return boolean
	 */
	private function __isAuthorized($user) {
		//Management
		if (in_array($this->Session->read('Auth.User.Group.id'),array('2'))) {
			$this->Auth->allow('login','logout','emailexists','addphoto','add','activate','edit','get','index','activity');
			return true;
		}elseif (in_array($this->Session->read('Auth.User.Group.id'),array('3','4','5'))){
			//$this->Auth->allow('login','logout','emailexists','activity');
			$this->Auth->allow('login','logout','emailexists','addphoto','edit','get','index','activity');
			return true;
		}
		return false;
	}
}