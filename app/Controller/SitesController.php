<?php
/**
 * Site Controller
 *
 * This file handles fraud checks for keystone inbound leads.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     TBD
 * @link          https://github.com/adlink360/keyStone/wiki/FraudController
 * @package       app.Controller
 * @since         keyStone v1.0
 * @license       TBD
 */

class SitesController extends AppController {
	public $uses = array('Site','Ancillary','SiteConfiguration','Affiliate');
	
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->autoRedirect = false;
		$this->__isAuthorized($this->Session->read('Auth.User'));
		$this->set('loadSiteJS',true);
	}
	
	public function index(){
		$this->layout = 'dashboard';
		$this->set('sitelist', $this->Site->find('all'));
	}
	
	public function ancillary(){
		$this->layout = 'dashboard';
		$this->set('ancillarylist', $this->Ancillary->find('all'));
		$this->set('pagelist',array('home'=>'Home Page','personal_info'=>'Personal Info','verify_identity'=>'Verify Identity','employment_info'=>'Employment Info','deposit_cash'=>'Deposit Cash','finalize'=>'Finalize'));
		$this->set('fieldlist',array('CreditRating'=>'Credit Rating','Zip'=>'Zip Code','Military'=>'Military','ResidenceType'=>'Type of Residence','EmployeeType'=>'Employee Type','BankAccountType'=>'Bank Account Type','DirectDeposit'=>'Direct Deposit'));
		$this->set('valuelist',array('CreditRating'=>array('excellent'=>'Excellent','good'=>'Good','fair'=>'Fair','poor'=>'Poor','unsure'=>'Unsure'),'Military'=>array('true'=>'Yes','false'=>'No'),'ResidenceType'=>array('rent'=>'Rent','ownwmtg'=>'Own with Mortgage','ownwomtg'=>'Own without Mortgage'),'EmployeeType'=>array('self_employed'=>'Self Employed','employed'=>'Employed','pension'=>'Pension','unemployed'=>'Unemployed'),'BankAccountType'=>array('checking'=>'Checking','savings'=>'Savings'),'DirectDeposit'=>array('true'=>'Yes','false'=>'No')));
	}
	
	public function configuration(){
		$this->layout = 'dashboard';
		$this->Site->recursive = 2;
		$this->set('sitelist',$this->Site->find('all'));
		$this->set('ancillarylist',$this->Ancillary->find('all'));
		$this->set('configlist',$this->SiteConfiguration->find('all'));
		
		//Retrieve a list of affiliates
		$this->Affiliate->contain();
		$params = array('order'=>array('Affiliate.affiliate_name'));
		$this->set('affiliate_list', $this->Affiliate->find('all',$params));
	}
	
	public function addConfiguration(){
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		$response = array('status'=>'failure');
		if($this->request->is('post')){
			
			$save = array();
			$save['site_id'] = $this->request->data['site_id'];
			$save['ancillary_id'] = $this->request->data['ancillary_id'];
			
			if(isset($this->request->data['restrictions']) && is_array($this->request->data['restrictions'])){
				$restriction_holder = array();
				foreach($this->request->data['restrictions'] as $index=>$arr){
					$restriction_holder[] = array($arr['affiliate'], $arr['sub']);
				}
				
				$save['blocked'] = json_encode($restriction_holder);
			}
			
			//update record by setting id field
			if(isset($this->request->data['config_id'])){
				$save['id'] = $this->request->data['config_id'];
			}
			
			
			if($this->SiteConfiguration->save($save)) {
				$response['status'] = 'success';
			}
			
			return json_encode($response);
		}
	}

	
	public function deleteSiteConfiguration(){
		$this->layout = null;
		$this->autoRender = false;
		$response = array('status'=>'failure');

		if($this->request->is('post')){
			$siteconfig_id = $this->request->data['id'];
			$cascade = false; 		
			
			$this->SiteConfiguration->delete( $siteconfig_id, $cascade );
			
			$response['status'] = 'success';
		}

		return json_encode($response);
	}
	
	public function addSite(){
		$this->layout = null;
		$this->autoRender = false;

		if($this->request->is('post')){
			if($this->Site->save($this->request->data)) {
				$this->Session->setFlash('The website was added','notify_success');
			}else{
				$this->Session->setFlash('The website could not be added. Please, try again.','notify_error');
			}
			return $this->redirect('/sites');
		}
	}
	
	public function deleteSite(){
		$this->layout = null;
		$this->autoRender = false;
		$response = array('status'=>'failure');

		if($this->request->is('post')){
			$site_id = $this->request->data['id'];
			$cascade = true; //delete record and has many relationships
			
			$this->Site->delete($site_id, $cascade);
			
			$this->SiteConfiguration->deleteAll( array("SiteConfiguration.site_id" => $site_id), false );
			
			$response['status'] = 'success';
		}
		
		return json_encode($response);
	}
	
	public function addAncillary(){
		$this->layout = null;
		$this->autoRender = false;

		$data = $this->request->data;
		
		foreach($data as $name=>$value){
			if($value == '' && !is_array($value)){
				unset($data[$name]);
			}
		}
		
		switch($data['Ancillary']['type']){
			case 'page':
				$data['Ancillary']['triggeraction'] = json_encode(array('page'=>$data['targetpage'], 'windowheight'=>$data['window_height'], 'windowwidth'=>$data['window_width']));
			break;
			
			case 'field':
				if($data['fieldtargetvalue'] == ''){
					$data['Ancillary']['triggeraction'] = json_encode(array('action' => array($data['targetfield']=>$data['clicktargetvalue']), 'windowheight'=>$data['window_height'], 'windowwidth'=>$data['window_width']));
				}else{
					$data['Ancillary']['triggeraction'] = json_encode(array('action' => array($data['targetfield']=>$data['fieldtargetvalue']), 'windowheight'=>$data['window_height'], 'windowwidth'=>$data['window_width']));
				}
			break;
			
			case 'click':
				$data['Ancillary']['triggeraction'] = json_encode(array('click'=>$data['targetclick'], 'windowheight'=>$data['window_height'], 'windowwidth'=>$data['window_width']));
			break;
			
			case 'backend':
				$data['Ancillary']['triggeraction'] = json_encode(array('backend'=>$data['targetbackend']));
				$data['Ancillary']['url'] = "N/A";
			break;
		}
		
		$data['Ancillary']['status']=0;

		
		if($this->request->is('post')){
			if($this->Ancillary->save($data['Ancillary'])) {
				
				$this->Session->setFlash('The ancillary item was added','notify_success');
			}else{
				
				$this->Session->setFlash('The ancillary item could not be added. Please, try again.','notify_error');
			}
			
			return $this->redirect('/sites/ancillary');
		}
	}

	public function deleteAncillary(){
		$this->layout = null;
		$this->autoRender = false;
		$response = array('status'=>'failure');

		if($this->request->is('post')){
			$ancillary_id = $this->request->data['id'];
			$cascade = true; //delete record and has many relationships
			
			$this->Ancillary->delete($ancillary_id, $cascade);
			
			$this->SiteConfiguration->deleteAll( array("SiteConfiguration.ancillary_id" => $ancillary_id), false );
			
			$response['status'] = 'success';
			
			
		}

		return json_encode($response);
	}
	
	/**
	 * Inline update a user field.  Also accepts post in the case of ancillary and field is url
	 * Restful / json
	 * @access Administrator, Management
	 */
	public function edit($id=null,$field=null,$value=null,$model=null){
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
	
		if($this->request->is('post')){
			$id = $this->request->data['id'];
			$field = $this->request->data['field'];
			$value = $this->request->data['value'];
			$model = $this->request->data['model'];
		}
	
	
		$this->$model->id = $id;
		$response['status'] = false;
		$response['message'] = '';
		if($this->request->is('ajax')){
	
			if ($this->$model->saveField($field,$value,false)) {
				$response['message'] = 'Updated';
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
	 * Specify what user group has access.  For development speed, this is not in ACL.
	 * @param array $user
	 * @return boolean
	 */
	private function __isAuthorized($user) {
		if (in_array($this->Session->read('Auth.User.Group.id'),array('2','3','4','5'))) {
			$this->Auth->allow();
			return true;
		}
		
		return false;
	}
}