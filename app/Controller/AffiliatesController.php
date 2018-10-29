<?php
/**
 * Affiliate Controller
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     TBD
 * @link          https://github.com/Vexedmonkey/keyStone/wiki/AffiliateController
 * @package       app.Controller
 * @since         keyStone v1.0
 * @license       TBD
 */
class AffiliatesController extends AppController {
	public $uses = array('Affiliate','Cake','Bucket','AffiliateIp','AffiliateDomain');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->autoRedirect = false;
		$this->__isAuthorized($this->Session->read('Auth.User'));
		$this->set('loadAffiliatesJS',true);
	}
				
	/**
	 * Display the affiliates.  I opted for not cacheing at this level as the call to get the affiliate
	 * list is cached internally at the CakeController.  Future version will read locally from a populated table.
	 * This iteration pulls from the CakeController via ajax.  Future: $affiliatelist = $this->Affiliate->find('all');
	 *
	 * @todo Populate local db for appropriate model interaction.
	 */
	public function index(){
		$this->layout = 'dashboard';
		//$affiliate_list = $this->Affiliate->find('all');
		
	}
	
	public function domains(){
		$this->layout = 'dashboard';
		
		//Retrieve a list of affiliates
		$this->set('affiliate_list', $this->Affiliate->find('all'));
	}
	
	public function add_domains_ip(){
		$this->layout = null;
		$this->autoRender = false;
		
		if($this->request->is('post')){
			$data = $this->request->data;
			if($data['affiliate-remote-id'] == ''){
				$this->Session->setFlash('No vendor selected!','notify_error');
				return $this->redirect('/affiliates/domains');
			}
			
			if($data['add-affiliate-domain']!=''){
				$domain = array('AffiliateDomain'=>array('domain'=>$data['add-affiliate-domain'],'affiliate_id'=>$data['affiliate-remote-id']));
				$this->AffiliateDomain->save($domain);
				$this->Session->setFlash('Vendor data added','notify_success');
			}
			
			if($data['add-affiliate-ip']!=''){
				$ip = array('AffiliateIp'=>array('ip'=>$data['add-affiliate-ip'],'affiliate_id'=>$data['affiliate-remote-id']));
				$this->AffiliateIp->save($ip);
				$this->Session->setFlash('Vendor data added','notify_success');
			}
			return $this->redirect('/affiliates/domains');
		}
	}
	
	public function add_domains_ip_range(){
		/*
		HostMin: 204.93.131.1
		HostMax: 204.93.131.126
		*/
		
		$this->layout = null;
		$this->autoRender = false;
		$data['affiliate-remote-id'] = "278"; //matches the "id" field in the affiliates table
		for($i=1;$i<255;$i++){
			//$this->AffiliateIp->create();
			//$ip = array('AffiliateIp'=>array('ip'=>'72.44.206.'.$i,'affiliate_id'=>$data['affiliate-remote-id']));
			//$this->AffiliateIp->save($ip);
			//$this->AffiliateIp->clear();
		}
	}
			
	public function delete_domains($id=null){
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		
		$this->AffiliateDomain->id = $id;
		$response['status'] = false;
		$response['message'] = '';
		if($this->request->is('ajax')){
			if ($this->AffiliateDomain->delete($id, false)) {
				$response['status'] = true;
				return json_encode($response);
			}
			$response['message'] = 'The domain could not be deleted.';
		}
		else{
			$response['message'] = 'Invalid request';
		}
		
		return json_encode($response);
	}
	
	public function delete_ip($id=null){
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		
		$this->AffiliateIp->id = $id;
		$response['status'] = false;
		$response['message'] = '';
		if($this->request->is('ajax')){
			if ($this->AffiliateIp->delete($id, false)) {
				$response['status'] = true;
				return json_encode($response);
			}
			$response['message'] = 'The IP could not be deleted.';
		}
		else{
			$response['message'] = 'Invalid request';
		}
		
		return json_encode($response);
	}
	
	public function getAffiliates() {
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		
		$response['status'] = 'success';
		$response['data'] = '';
		$response['message'] = '';
		
		
		$response['data'] =	$this->Affiliate->find('all'); 
		
		return json_encode($response);
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