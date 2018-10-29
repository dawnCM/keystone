<?php
/**
 * ListManagementController  *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     TBD
 * @link          https://github.com/Vexedmonkey/keyStone/wiki/ListController
 * @package       app.Controller
 * @since         keyStone v1.0
 * @license       TBD
 */
class ListManagementController extends AppController {
	public $uses = array('Esp','Cake', 'ListManagement'); 
	
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->autoRedirect = false;
		$this->__isAuthorized($this->Session->read('Auth.User'));
		$this->set('loadListmanagementJS',true);
	}
				
	/**
	 *
	 * @todo Populate local db for appropriate model interaction.
	 */
	public function esp(){
		$this->layout = 'dashboard';
		
		$params = array('fields'=>array('Esp.name', 'Esp.id'));
		$esp_list = array();
		$esp_array = $this->Esp->find('all', $params);
		
		foreach($esp_array as $k=>$v){
			$esp_list[$v['Esp']['id']] = $v['Esp']['name'];	
		}
		
		$offer_array = $this->ListManagement->find('all', array('order' =>	array('ListManagement.id') ));
		$offer_list = array();
		foreach($offer_array as $k=>$v){
			
			$offer_list[$v['ListManagement']['id']] = $v['ListManagement']['name'];	
		}
		
		$this->set('offer_list', $offer_list);
		$this->set('esp_list', $esp_list);
		$this->render('espmanagement');
	}
	
	
	public function reloadEsps(){
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		
		$params = array('fields'=>array('Esp.name', 'Esp.id'));
		$esp_list = array();
		$esp_array = $this->Esp->find('all', $params);
		
		foreach($esp_array as $k=>$v){
			$str = (STRING)$v['Esp']['name'];
			$esp_list[] = array($v['Esp']['id'], $v['Esp']['name']);	
		}
		return json_encode($esp_list);

	}
	
	public function getEsp($id){
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		
		$esp = $this->Esp->find('first', array(	'conditions' 	=> 	array('Esp.id'=>$id), 'fields' =>	array('Esp.name', 'Esp.status_id', 'Esp.json')));
		$return = array();
				
		$return['status'] = "success";
		$return['id'] = $id;
		$return['config'] = json_decode($esp['Esp']['json'], true);
		
		return json_encode($return);														
	}
	
	public function espList(){
		$this->layout = 'dashboard';
		
		$esp_array = $this->Esp->find('all', array('order' =>	array('Esp.status_id') ));
		
		
		$return = array();
		$esp_list = array();
		foreach($esp_array as $k=>$v){
			$str = (STRING)$v['Esp']['name'];
			$esp_list[] = array($v['Esp']['id'], $v['Esp']['name'], $v['Esp']['status_id']);	
		}
		

		$this->set('esp_list', $esp_list);
		$this->render('esplist');														
		
		
	}
	
	public function espOffer(){
		$this->layout = 'dashboard';
		
		$offer_array = $this->ListManagement->find('all', array('order' =>	array('ListManagement.id') ));
		
		
		$return = array();
		$offer_list = array();
		foreach($offer_array as $k=>$v){
			
			$offer_list[] = array($v['ListManagement']['id'], $v['ListManagement']['name']);	
		}
		

		$this->set('offer_list', $offer_list);
		$this->render('espoffer');														
		
		
	}
	
	public function getEspOffers(){
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		
		$offer_array = $this->ListManagement->find('all', array('order' =>	array('ListManagement.id') ));
		$offer_list = array();
		foreach($offer_array as $k=>$v){
			
			$offer_list[] = array($v['ListManagement']['id'], $v['ListManagement']['name']);	
		}
		
		return json_encode($offer_list);
	}
	
	public function addEspOffer(){
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		
		$data = $this->request->data;
		$this->ListManagement->clear();
		
		$this->ListManagement->set('name', $data['name']);
		$this->ListManagement->save();	
		$new_id = $this->ListManagement->id;
		
				
		$return = array();
		
		$return['status'] = "success";
		$return['id'] = $new_id;
		
		return json_encode($return);	
			
	}
	
	public function updateEspStatus(){
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		
		$data = $this->request->data;
		$this->Esp->clear();
		$this->Esp->id = $data['id'];
		
		$esp = $this->Esp->find('first', array(	'conditions' 	=> 	array(), 'fields' =>	array('Esp.name', 'Esp.status_id', 'Esp.json')));
		$config = json_decode($esp['Esp']['json'], true);
		$config['EspStatus'] = $data['status_id'];
		
		$this->Esp->set('status_id', $data['status_id']);
		$this->Esp->set('json', json_encode($config));
		$this->Esp->save();	
		
		
				
		$return = array();
		
		$return['status'] = "success";
		$return['id'] = $data['id'];
		
		return json_encode($return);	
			
	}
	
	
	public function saveEsp($id){
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		
		$config = $this->request->data;
		
		if($id == 0){
			$esp_array = array(	'name'		=>	$config['EspName'],
								'status_id'		=>	$config['EspStatus'],
								'json'			=>	json_encode($config),
			);	
			$this->Esp->create($esp_array);
			$this->Esp->save();
			$esp_id = $this->Esp->id;	
			
		}else{
			$this->Esp->set('id', $id);
			$this->Esp->set('status_id', $config['EspStatus']);
			$this->Esp->set('name', $config['EspName']);
			$this->Esp->set('json', json_encode($config));
				
			$this->Esp->save();
			$this->Esp->clear();
			$esp_id = $id;	
		}
		
		
		$return = array();
		
		$return['status'] = "success";
		$return['id'] = $esp_id;
		$return['config'] = $config;
		
		return json_encode($return);		
	
	}
	
	
	public function testEsp($id){
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		
		$temp = array();
		foreach($this->request->data as $k=>$v){
			$temp[$v[0]] = $v[1];
		}

		$this->Esp->data['Esp'] = $temp;
		$type = "main_test";	
		$rsp_array = $this->Esp->sendToEsp($id, $type);
		
		return json_encode($rsp_array);
	
	}
	
	//Add to suppression list test
	public function apiTestEsp($id){
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		
		$temp = array();
		foreach($this->request->data as $k=>$v){
			$temp[$v[0]] = $v[1];
		}

		$this->Esp->data['Esp'] = $temp;		
		$type = "api_test";
		$rsp_array = $this->Esp->sendToEsp($id, $type);
		
		return json_encode($rsp_array);
	
	}
	
	public function blackListTestEsp($id){
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		
		//We only post custom fields so no user data
		$this->Esp->data['Esp'] = array();	
		$type = "blacklist_test";
		$rsp_array = $this->Esp->sendToEsp($id, $type);
		
		
		return json_encode($rsp_array);
	
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
			$this->Auth->allow();
			return true;
		}
		
		return false;
	}
}