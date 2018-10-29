<?php
/**
 * Payday Model
 *
 * This model contains the data function for the Api controller.
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
class LowTier extends AppModel {
	//public $actsAs = array('AuditLog.Auditable');
	public $name = 'LowTier';
	public $useTable = false;
	
	
	
	//Main Validation that is required for every lead
	public $validate = array(
			'Affiliate' => array(
				'rule' => "numeric",
				'message' => 'Affiliate',
				'required' => true,
				'allowEmpty' => false),
			'CreativeId' => array(
				'rule' => "numeric",
				'message' => 'CreativeId',
				'required' => true,
				'allowEmpty' => false),
			'OfferId' => array(
				'rule' => "numeric",
				'message' => 'OfferId',
				'required' => true,
				'allowEmpty' => false),
			'CampaignId' => array(
				'rule' => "numeric",
				'message' => 'CampaignId',
				'required' => true,
				'allowEmpty' => false),
			'SubId1' => array(
				'rule' => "/^([a-zA-Z0-9-\|\_]{0,50})$/",
				'message' => 'SubId1',
				'required' => false,
				'allowEmpty' => true),
			'SubId2' => array(
				'rule' => "/^([a-zA-Z0-9-\|\_]{0,50})$/",
				'message' => 'SubId2',
				'required' => false,
				'allowEmpty' => true),
			'SubId3' => array(
				'rule' => "/^([a-zA-Z0-9-\|\_]{0,50})$/",
				'message' => 'SubId3',
				'required' => false,
				'allowEmpty' => true),
			'SubId4' => array(
				'rule' => "/^([a-zA-Z0-9-\|\_]{0,50})$/",
				'message' => 'SubId4',
				'required' => false,
				'allowEmpty' => true),
			'SubId5' => array(
				'rule' => "/^([a-zA-Z0-9-\|\_]{0,50})$/",
				'message' => 'SubId5',
				'required' => false,
				'allowEmpty' => true),
			'IPAddress' => array(
					'rule' => array('ip'),
					'message' => 'IPAddress',
					'required' => true,
					'allowEmpty' => false),
			
			'FirstName' => array(
					'rule' => "/^([a-zA-Z\s-'\.]{1,50})$/",
					'message' => 'FirstName',
					'required' => true,
					'allowEmpty' => false),
			'LastName' => array(
					'rule' => "/^([a-zA-Z\s-'\.]{1,50})$/",
					'message' => 'LastName',
					'required' => true,
					'allowEmpty' => false),
			
			'Address1' => array(
					'rule' => "/^([a-zA-Z0-9\s-'\.\,\_\#\&\/]{1,50})$/",
					'message' => 'Address1',
					'required' => true,
					'allowEmpty' => false),
			'Address2' => array(
					'rule' => "/^([a-zA-Z0-9\s-'\.\,\_\#\&\/]{0,50})$/",
					'message' => 'Address2',
					'required' => false,
					'allowEmpty' => true),
			'City' => array(
					'rule' => "/^([a-zA-Z\s-\.']{1,50})$/",
					'message' => 'City',
					'required' => true,
					'allowEmpty' => false),
			'State' => array(
					'rule' => "/(AK|AL|AR|AZ|CA|CO|CT|DC|DE|FL|GA|HI|IA|ID|IL|IN|KS|KY|LA|MA|MD|ME|MI|MN|MO|MS|MT|NC|ND|NE|NH|NJ|NM|NV|NY|OH|OK|OR|PA|RI|SC|SD|TN|TX|UT|VA|VT|WA|WI|WV|WY)/",
					'message' => 'State',
					'required' => true,
					'allowEmpty' => false),
			'Zip' => array(
					'rule' => "/^[0-9]{5}?$/",
					'message' => 'Zip',
					'required' => true,
					'allowEmpty' => false),
			
			'HomePhone' => array(
					'rule' => "/^[0-9]{10}$/",
					'message' => 'HomePhone',
					'required' => true,
					'allowEmpty' => false),
			
			'Email' => array(
					'rule' => "/^[\w-]+(\.[\w-]+)*@([a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*?\.[a-zA-Z]{2,6}|(\d{1,3}\.){3}\d{1,3})(:\d{4})?$/",
					'message' => 'Email',
					'required' => true,
					'allowEmpty' => false),
			'Agree' => array(
					'rule' => "/(true)/",
					'message' => 'Agree',
					'required' => true,
					'allowEmpty' => false),		
			'AgreeConsent' => array(
					'rule' => "/(true)/",
					'message' => 'AgreeConsent',
					'required' => true,
					'allowEmpty' => false),
			'AgreePhone' => array(
					'rule' => "/(true|false)/",
					'message' => 'AgreePhone',
					'required' => false,
					'allowEmpty' => true),
			'Url' => array(
					'rule' => "url",
					'message' => 'Url',
					'required' => true,
					'allowEmpty' => false)	
			
	);
	
	
	
	
	public function buildPost(){
		
		$data = $this->data['LowTier'];	
			
		App::import('Model','ApiPostFunctions');
		$ap = new ApiPostFunctions();

		$ap->init($data);
		
	
		$main = array(	
						'first_name'			=>	$data['FirstName'],
						'last_name'			=>	$data['LastName'],
						'address_1'			=>	$data['Address1'],
						'address_2'			=>	$data['Address2'],
						'city'			=>	$data['City'],
						'state'			=>	$data['State'],
						'zip'			=>	$data['Zip'],
						'phone_primary'			=>	$data['HomePhone'],
						'email'			=>	$data['Email'],
						'ip_address'			=>	$data['IPAddress'],
						'effective_date'			=>	$ap->getEffectiveDate(),
						'created_time_stamp' => 	$ap->getTimestamp(),
						'opt_consent'			=>	$data['AgreeConsent'],
						'accept_terms'			=>	$data['Agree'],
						'opt_in'			=>	$data['AgreePhone'],
						'client_url'			=>	$data['Url']
		);
			
		return $main;
	}



	public function flatErrorArray(){
		if(empty($this->validationErrors))return array();
		
		$array = array();
		foreach($this->validationErrors as $k=>$v){
			$array[] = $v[0];
		}
		
		
		return $array;
	}
	
	
	
}


?>