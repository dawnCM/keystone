<?php
/**
 * Medical Model
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
class Medical extends AppModel {
	//public $actsAs = array('AuditLog.Auditable');
	public $name = 'Medical';
	public $useTable = 'buckets';
	
	
	
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
		'DateOfBirth' => array(
			'rule' => array('date', 'mdy'),
			'message' => 'DateOfBirth',
			'required' => true,
			'allowEmpty' => false),		
		'Email' => array(
			'rule' => "/^[\w-]+(\.[\w-]+)*@([a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*?\.[a-zA-Z]{2,6}|(\d{1,3}\.){3}\d{1,3})(:\d{4})?$/",
			'message' => 'Email',
			'required' => true,
			'allowEmpty' => false),		
		'AgreePhone' => array(
			'rule' => "/(true|false|1|0)/",
			'message' => 'AgreePhone',
			'required' => false,
			'allowEmpty' => true)
	);
	
	
	
	
	public function buildPost(){
		
		$data = $this->data['medical'];	
		
		if(!$data['AgreePhone']){$data['AgreePhone']='';}
			
		App::import('Model','ApiPostFunctions');
		$ap = new ApiPostFunctions();

		$data['DateOfBirthMonth'] = substr($data['DateOfBirth'], 0, 2);
		$data['DateOfBirthDay']   = substr($data['DateOfBirth'], 3, 2);
		$data['DateOfBirthYear']  = substr($data['DateOfBirth'], 6, 4);
		
		$ap->init($data);
		$main = array(	'first_name'				=>	$data['FirstName'],
						'last_name'					=>	$data['LastName'],
						'phone_primary'				=>	$data['HomePhone'],
						'citizen'					=>	'true',
						'date_of_birth'				=> $ap->getDateOfBirth(),
						'age'						=> $ap->getAge(),
						'email'						=> $data['Email'],
						'calc_income' 				=> $ap->getCalcIncomeUS(),
						'bank_name' 				=> $ap->getBankName(),
						'opt_in'					=> $data['AgreePhone'],
						'ip_address'				=> $data['IPAddress'],
						'country'					=> 'US',
						'browser_info'				=> 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0',
						'mobile_browser'			=> 'false',
						'best_contact_time' 		=> 'morning',
						'effective_date'			=> $ap->getEffectiveDate(),
						'active_bankruptcy'			=> 'false',
						'phone_type' 				=> 'Home',
						'created_time_stamp' 		=> $ap->getTimestamp()
		);
			
		return $main;
	}

	/**
	 * Custom validation rule to see if the values match or not
	 * @param array $check
	 * @param string $compare
	 */
	public function doNotMatch($check, $compare){
		$value = array_values($check);
		$value = $value[0];
	
		return ($this->data['Medical'][$compare] != $value);
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