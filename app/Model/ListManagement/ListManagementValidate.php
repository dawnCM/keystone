<?php
/**
 * ListManagementValidate Model
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
class ListManagementValidate extends AppModel {
	public $actsAs = array('AuditLog.Auditable');
	public $name = 'ListManagementValidate';
	public $useTable = false;
	
	
	//Main Validation that is required for every lead
	public $validate = array(
			'Affiliate' => array(
				'rule' => "numeric",
				'message' => 'Affiliate',
				'required' => true,
				'allowEmpty' => false),
			'CreativeId' => array(
				'rule' => array('boolean'),
				'message' => 'CreativeId',
				'required' => true,
				'allowEmpty' => false),
			'OfferId' => array(
				'rule' => 'numeric',
				'message' => 'OfferId',
				'required' => true,
				'allowEmpty' => false),
			'CampaignId' => array(
				'rule' => '/^([a-zA-Z0-9\s-\'\.\,\_\#\&\/]{1,50})$/',
				'message' => 'CampaignId',
				'required' => true,
				'allowEmpty' => false),
			'IPAddress' => array(
					'rule' => array('ip'),
					'message' => 'IPAddress',
					'required' => true,
					'allowEmpty' => false),
			'Url' => array(
					'rule' => "url",
					'message' => 'Url',
					'required' => true,
					'allowEmpty' => false),
			'LoanAmount' => array(
					'rule' => "/^[0-9]{1,5}?$/",
					'message' => 'LoanAmount',
					'required' => false,
					'allowEmpty' => true),
			'FirstName' => array(
					'rule' => "/^([a-zA-Z\s-'\.]{1,50})$/",
					'message' => 'FirstName',
					'required' => false,
					'allowEmpty' => true),
			'LastName' => array(
					'rule' => "/^([a-zA-Z\s-'\.]{1,50})$/",
					'message' => 'LastName',
					'required' => false,
					'allowEmpty' => true),	
				
			'Address1' => array(
					'rule' => "/^([a-zA-Z0-9\s-'\.\,\_\#\&\/]{1,50})$/",
					'message' => 'Address1',
					'required' => false,
					'allowEmpty' => true),
			'Address2' => array(
					'rule' => "/^([a-zA-Z0-9\s-'\.\,\_\#\&\/]{0,50})$/",
					'message' => 'Address2',
					'required' => false,
					'allowEmpty' => true),
			'City' => array(
					'rule' => "/^([a-zA-Z\s-\.']{1,50})$/",
					'message' => 'City',
					'required' => false,
					'allowEmpty' => true),
			'State' => array(
					'rule' => "/(AK|AL|AR|AZ|CA|CO|CT|DC|DE|FL|GA|HI|IA|ID|IL|IN|KS|KY|LA|MA|MD|ME|MI|MN|MO|MS|MT|NC|ND|NE|NH|NJ|NM|NV|NY|OH|OK|OR|PA|RI|SC|SD|TN|TX|UT|VA|VT|WA|WI|WV|WY)/",
					'message' => 'State',
					'required' => false,
					'allowEmpty' => true),
			'Zip' => array(
					'rule' => "/^[0-9]{5}?$/",
					'message' => 'Zip',
					'required' => false,
					'allowEmpty' => true),
			'DateOfBirth' => array(
					'rule' => array('date', 'mdy'),
					'message' => 'DateOfBirth',
					'required' => false,
					'allowEmpty' => true),		
			'Email' => array(
					'rule' => "/^[\w-]+(\.[\w-]+)*@([a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*?\.[a-zA-Z]{2,6}|(\d{1,3}\.){3}\d{1,3})(:\d{4})?$/",
					'message' => 'Email',
					'required' => true,
					'allowEmpty' => false),		
			'OptIn' => array(
					'rule' => array('date', 'mdy'),
					'message' => 'OptIn',
					'required' => true,
					'allowEmpty' => false),			
			'Price' => array(
					'rule' => "/^\d+(\.\d{1,2})?$/",
					'message' => 'Price',
					'required' => false,
					'allowEmpty' => true)
	);
	
	public function removeValidation($field){
		unset($this->validate[$field]); 
	}
	
	public function flatErrorArray(){
		if(empty($this->validationErrors))return array();
		
		$array = array();
		foreach($this->validationErrors as $k=>$v){
			$array[] = $v[0];
		}
		return $array;
	}
	
	//return a flat array of field
	public function systemFields(){
		$temp = array();
			
		foreach($this->validate as $k=>$v){
			
			$temp[] = $k;
		}
		return $temp;
	}	
}
?>