<?php
/**
 * CPF Controller
 *
 * This file handles the CPF management for the applications. 
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     TBD
 * @link          https://github.com/Vexedmonkey/keyStone/wiki/BucketsController
 * @package       app.Controller
 * @since         keyStone v1.0
 * @license       TBD
 */
class CpfController extends AppController {
	public $uses = array('Cpf');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->autoRedirect = false;
		$this->isAuthorized($this->Session->read('Auth.User'));
		$this->set('loadCpfJS',true);
	}
	
	public function index(){
		$this->layout = 'dashboard';
		
		//Retrieve a list of users
		$this->set('cpflist', $this->Cpf->find('all'));
	}
	
	/**
	 * Upload the CPF file and process each csv line through Cake Marketing.
	 */
	public function addfile(){
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		
		$response = array('success'=>false,'message'=>'','response'=>'');
		
		if($this->request->is('ajax')){
			$uploadname = $_FILES['file']['name'];
			$uploadext = end((explode('.',$uploadname)));
			$uploaddir = Configure::read('Upload.path') . '/cpf/';
			$uploadfile = $uploaddir . md5($uploadname) . '.' . $uploadext;
			
			//Verify the file is csv and that the upload was successful
			if($uploadext == 'csv' && move_uploaded_file($_FILES['file']['tmp_name'],$uploadfile)){
				$this->request->data['Cpf']['name'] = $uploadname;
				$this->request->data['Cpf']['hash'] = md5($uploadname);
				//Save the record to the DB.
				if($this->Cpf->save($this->request->data)) {
					$response['success'] = true;
					$response['response'] = $this->__processUpload($uploadfile);
					//Records are done processing, update the db row.
					$this->request->data['Cpf']['id']=$this->Cpf->getInsertID();
					$this->request->data['Cpf']['records'] = $response['response']['records'];
					$this->request->data['Cpf']['processed'] = $response['response']['processed'];
					$this->request->data['Cpf']['errors'] = $response['response']['errors'];
					$this->request->data['Cpf']['errors_json'] = json_encode($response['response']['errors_json']);
					//Update the row with the processed data
					$this->Cpf->save($this->request->data);
				}else{
					$response['success'] = false;
				}
			}else{
				$response['message'] = 'File could not be uploaded to the server.';
			}
		}
		else{
			$response['message'] = 'Invalid request';
		}
		
		return json_encode($response);
	}
	
	
	private function __processUpload($filepath){
		$process['records'] = 0;
		$process['processed'] = 0;
		$process['errors'] = 0;
		$process['errors_json'] = array();
		
		//Open the file
		$handle = fopen($filepath, 'r');
		while(($data = fgetcsv($handle,500,',')) !== false ){
			//Skip the first row of column headers
			if($process['records'] > 0){
				usleep(250000); //tmp
				//Call Cake
				//$process['errors']++;
				//$process['errors_json'][$process['records']]['Cake Media Error Message'];
				$process['processed']++;
			}
			$process['records']++;
		}
		//Remove the first row count
		$process['records']--;
		return $process;
	}

	/**
	 * Specify what user group has access.  For development speed, this is not in ACL yet.
	 * @todo Add ACL functionality to keyStone.
	 * @param array $user
	 * @return boolean
	 */
	private function __isAuthorized($user) {
		//Management
		if (in_array($this->Session->read('Auth.User.Group.id'),array('2'))) {
			$this->Auth->allow('index');
			return true;
		}
		return false;
	}
}