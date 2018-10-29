<?php
/**
 * Application Model
 *
 * This model contains the data function for the site application controller.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     TBD
 * @link          TBD
 * @package       app.Model.Application
 * @since         keyStone(SD) v1.0 
 * @license       TBD
 */
App::uses('AuthComponent', 'Controller/Component');
class Application extends AppModel {

	public $name = 'Application';
	public $useTable = false;
	
	//Main Validation that is required for every lead
	public $validate = array(
			'AffiliateId' => array(
				'rule' => "numeric",
				'message' => 'AffiliateId',
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
			'RequestId' => array(
				'rule' => "numeric",
				'message' => 'RequestId',
				'required' => true,
				'allowEmpty' => false),
			'SubId1' => array(
				'rule' => "alphaNumeric",
				'message' => 'SubId1',
				'required' => false,
				'allowEmpty' => true),
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
			'FirstName' => array(
					'rule' => "/^([a-zA-Z\s-\'\.\-]{2,50})$/",
					'message' => 'FirstName',
					'required' => true,
					'allowEmpty' => false),
			'LastName' => array(
					'rule' => "/^([a-zA-Z\s-\'\.\-]{2,50})$/",
					'message' => 'LastName',
					'required' => true,
					'allowEmpty' => false),	
			'ResidentSinceDate' => array(					
					'message' => 'ResidentSinceDate',
					'required' => true,
					'allowEmpty' => false),			
			'Address1' => array(
					'rule' => "/^([a-zA-Z0-9\s-\'\.\,\_\#\&\/]{2,50})$/",
					'message' => 'Address1',
					'required' => true,
					'allowEmpty' => false),
			'Address2' => array(
					'rule' => "/^([a-zA-Z0-9\s-\'\.\,\_\#\&\/]{1,50})$/",
					'message' => 'Address2',
					'required' => false,
					'allowEmpty' => true),
			'City' => array(
					'rule' => "/^([a-zA-Z\s-\.\']{1,50})$/",
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
			'ResidenceType' => array(
					'rule' => "/(Own With Mortgage|Own Without Mortgage|Rent)/",
					'message' => 'ResidenceType',
					'required' => true,
					'allowEmpty' => false),		
			'PrimaryPhone' => array(
					'rule' => "/^[0-9]{10}$/",
					'message' => 'PrimaryPhone',
					'required' => true,
					'allowEmpty' => false),
			'SecondaryPhone' => array(
					'rule' => "/^[0-9]{10}$/",
					'message' => 'SecondaryPhone',
					'required' => false,
					'allowEmpty' => true),
			'CreditRating' => array(
					'rule' => "/(excellent|good|fair|poor|unsure)/",
					'message' => 'CreditRating',
					'required' => true,
					'allowEmpty' => false),	
			'Ssn' => array(
					'rule' => array('ssn', '/^[0-9]{9}$/', 'us'),
					'message' => 'SocialSecurityNumber',
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
			'DriversLicenseState' => array(
					'rule' => "/(AK|AL|AR|AZ|CA|CO|CT|DC|DE|FL|GA|HI|IA|ID|IL|IN|KS|KY|LA|MA|MD|ME|MI|MN|MO|MS|MT|NC|ND|NE|NH|NJ|NM|NV|NY|OH|OK|OR|PA|RI|SC|SD|TN|TX|UT|VA|VT|WA|WI|WV|WY)/",
					'message' => 'DriversLicenseState',
					'required' => true,
					'allowEmpty' => false),
			'DriversLicenseNumber' => array(
					'rule' => "/^[0-9a-zA-Z\s-*]{1,17}?$/",
					'message' => 'DriversLicenseNumber',
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
			'EmployeeType' => array(
					'rule' => "/(pension|unemployed|employed|self_employed|welfare)/",
					'message' => 'EmployeeType',
					'required' => true,
					'allowEmpty' => false),							
			'Military' => array(
					'rule' => "/(true|false)/",
					'message' => 'Military',
					'required' => true,
					'allowEmpty' => false),		
			'EmployerName' => array(
					'rule' => "/^([a-zA-Z0-9\s-'\.\,#_\&\/]{1,50})$/",
					'message' => 'EmployerName',
					'required' => true,
					'allowEmpty' => false),		
			'EmploymentTime' => array(
					'message' => 'EmploymentTime',
					'required' => true,
					'allowEmpty' => false),			
			'EmployerAddress' => array(
					'rule' => "/^([a-zA-Z0-9\s-\'\.\,\_\#\&\/]{2,50})$/",
					'message' => 'EmployerAddress',
					'required' => true,
					'allowEmpty' => false),
			'EmployerCity' => array(
					'rule' => "/^([a-zA-Z\s-\.\']{1,50})$/",
					'message' => 'EmployerCity',
					'required' => true,
					'allowEmpty' => false),
			'EmployerState' => array(
					'rule' => "/(AK|AL|AR|AZ|CA|CO|CT|DC|DE|FL|GA|HI|IA|ID|IL|IN|KS|KY|LA|MA|MD|ME|MI|MN|MO|MS|MT|NC|ND|NE|NH|NJ|NM|NV|NY|OH|OK|OR|PA|RI|SC|SD|TN|TX|UT|VA|VT|WA|WI|WV|WY)/",
					'message' => 'EmployerState',
					'required' => true,
					'allowEmpty' => false),
			'EmployerZip' => array(
					'rule' => "/^[0-9]{5}?$/",
					'message' => 'EmployerZip',
					'required' => true,
					'allowEmpty' => false),		
			'WorkPhone' => array(
					'rule' => "/^[0-9]{10}$/",
					'message' => 'WorkPhone',
					'required' => true,
					'allowEmpty' => false),		
			'Paydate1' => array(
					'rule' => "/^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}$/",
					'message' => 'Paydate1',
					'required' => true,
					'allowEmpty' => false),
			'Paydate2' => array(
					'rule' => "/^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}$/",
					'message' => 'Paydate2',
					'required' => true,
					'allowEmpty' => false),		
			'PayFrequency' => array(
					'rule' => "/(weekly|bi-weekly|semi-monthly|monthly)/",
					'message' => 'PayFrequency',
					'required' => true,
					'allowEmpty' => false),										
			'MonthlyNetIncome' => array(
					'rule' => "/^[0-9]{1,5}?$/",
					'message' => 'MonthlyNetIncome',
					'required' => true,
					'allowEmpty' => false),
			'DirectDeposit' => array(
					'rule' => "/(true|false)/",
					'message' => 'DirectDeposit',
					'required' => true,
					'allowEmpty' => false),
			'BankAccountType' => array(
					'rule' => "/(checking|savings)/",
					'message' => 'BankAccountType',
					'required' => true,
					'allowEmpty' => false),
			'BankRoutingNumber' => array(
					'rule' => "/^([0-9]{9})$/",
					'message' => 'BankRoutingNumber',
					'required' => true,
					'allowEmpty' => false),
			'BankAccountNumber' => array(
					'rule' => "/^[0-9]{4,17}$/",
					'message' => 'BankAccountNumber',
					'required' => true,
					'allowEmpty' => false),
			'BankTime' => array(
					'rule' => "/(9|12|24|36|48|60|72|84|96|108)/",
					'message' => 'BankTime',
					'required' => true,
					'allowEmpty' => false),
			'PhoneType' => array(
					'rule' => "/(Mobile|Home)/",
					'message' => 'PhoneType',
					'required' => true,
					'allowEmpty' => false)
	);
	
	//Validation Dependent on data
	public $validateCoapplicant = array(
			'CoFirstName' => array(
					'rule' => "/^([a-zA-Z\s-\'\.]{2,50})$/",
					'message' => 'CoFirstName',
					'required' => true,
					'allowEmpty' => false),
			'CoLastName' => array(
					'rule' => "/^([a-zA-Z\s-\'\.]{2,50})$/",
					'message' => 'CoFirstName',
					'required' => true,
					'allowEmpty' => false),
			'CoPrimaryPhone' => array(
					'rule' => "/^[0-9]{10}$/",
					'message' => 'CoPrimaryPhone',
					'required' => true,
					'allowEmpty' => false),
			'CoSsn' => array(
					'rule' => array('ssn', '/^[0-9]{9}$/', 'us'),
					'message' => 'CoSocialSecurityNumber',
					'required' => true,
					'allowEmpty' => false),
			'CoDateOfBirth' => array(
					'rule' => array('date', 'mdy'),
					'message' => 'CoDateOfBirth',
					'required' => true,
					'allowEmpty' => false),	
			'CoMonthlyNetIncome' => array(
					'rule' => "/^[0-9]{1,5}?$/",
					'message' => 'CoMonthlyNetIncome',
					'required' => true,
					'allowEmpty' => false),
			'CoEmployerName' => array(
					'rule' => "/^([a-zA-Z0-9\s-'\.\,#_\&\/]{1,50})$/",
					'message' => 'CoEmployerName',
					'required' => true,
					'allowEmpty' => false),		
			'CoWorkPhone' => array(
					'rule' => "/^[0-9]{10}$/",
					'message' => 'CoWorkPhone',
					'required' => true,
					'allowEmpty' => false),		
			'CoEmploymentTime' => array(
					'message' => 'CoEmploymentTime',
					'required' => true,
					'allowEmpty' => false),				
			'CoAppSameAddr' => array(
					'rule' => "/(Yes|No)/",
					'message' => 'CoAppSameAddr',
					'required' => true,
					'allowEmpty' => false),			
			'CoEmployeeType' => array(
					'rule' => "/(self_employed|employed|pension|unemployed|welfare)/",
					'message' => 'CoEmployeeType',
					'required' => true,
					'allowEmpty' => false),				
			
	);
	
	public $validatePersonalLoan = array(
		'LoanAmount' => array(
				'rule' => "/(100|300|500|1000|1500|2500|3500|4500|5500|6500|7500|8500|9500|10000|11500|12500|13500|14500|15000|16500|17500|18500|19500|20500|21500|22500|23500|24500|25500|26500|27500|28500|29500)/",
				'message' => 'LoanAmountPersonal',
				'required' => true,
				'allowEmpty' => false),
		'LoanPurpose' => array(
				'rule' => "/(debt|home|major|auto|other|medical|other)/",
				'message' => 'LoanPurpose',
				'required' => true,
				'allowEmpty' => false),
		'RentMortgage' => array(
				'rule' => "/^[0-9]{1,5}?$/",
				'message' => 'RentMortgage',
				'required' => true,
				'allowEmpty' => false),	
		'CoApplicant' => array(
				'rule' => "/(Yes|No)/",
				'message' => 'CoApplicant',
				'required' => true,
				'allowEmpty' => false),		
	);
	
	public $validatePaydayLoan = array(
		'LoanAmount' => array(
				'rule' => "/^[0-9]{1,5}?$/",
				'message' => 'LoanAmount',
				'required' => true,
				'allowEmpty' => false),
	);
	
	
	//Validation Dependent on data
	public $validateCoapplicantSameAddress = array(
			'CoAddress1' => array(
					'rule' => "/^([a-zA-Z0-9\s-\'\.\,\_\#\&\/]{2,50})$/",
					'message' => 'CoAddress1',
					'required' => true,
					'allowEmpty' => false),
			'CoAddress2' => array(
					'rule' => "/^([a-zA-Z0-9\s-\'\.\,\_\#\&\/]{1,50})$/",
					'message' => 'CoAddress2',
					'required' => false,
					'allowEmpty' => true),
			'CoCity' => array(
					'rule' => "/^([a-zA-Z\s-\.\']{1,50})$/",
					'message' => 'CoCity',
					'required' => true,
					'allowEmpty' => false),
			'CoState' => array(
					'rule' => "/(AK|AL|AR|AZ|CA|CO|CT|DC|DE|FL|GA|HI|IA|ID|IL|IN|KS|KY|LA|MA|MD|ME|MI|MN|MO|MS|MT|NC|ND|NE|NH|NJ|NM|NV|NY|OH|OK|OR|PA|RI|SC|SD|TN|TX|UT|VA|VT|WA|WI|WV|WY)/",
					'message' => 'CoState',
					'required' => true,
					'allowEmpty' => false),
			'CoZip' => array(
					'rule' => "/^[0-9]{5}?$/",
					'message' => 'CoZip',
					'required' => true,
					'allowEmpty' => false)
	);
	
	//Add to the validater as needed
	public function addDependencies(){
		if($this->data['Application']['AppType'] == 'personalloan'){
			foreach($this->validatePersonalLoan as $a=>$b){
				$this->validate[$a]=$b;	
			}	
		}
		
		if($this->data['Application']['AppType'] == 'payday'){
			foreach($this->validatePaydayLoan as $a=>$b){
				$this->validate[$a]=$b;	
			}	
		}
	
		//Add CoApp validation
		if(isset($this->data['Application']['CoApplicant'])){
			if($this->data['Application']['AppType'] == 'personalloan' && $this->data['Application']['CoApplicant'] == 'Yes'){
				foreach($this->validateCoapplicant as $c=>$d){
					$this->validate[$c]=$d;	
				}	
			}
		}
		
		//Add CoApp Address
		if(isset($this->data['Application']['CoApplicant'])){
			if($this->data['Application']['AppType'] == 'personalloan' && $this->data['Application']['CoApplicant'] == 'Yes' && $this->data['Application']['CoAppSameAddr'] == 'No'){
				foreach($this->validateCoapplicantSameAddress as $e=>$f){
					$this->validate[$e]=$f;	
				}	
			}
		}
	}
	
	//Formatted array of errors
	public function flatErrorArray(){
		if(empty($this->validationErrors))return array();
		
		$array = array();
		foreach($this->validationErrors as $k=>$v){
			$array[] = $v[0];
		}
		
		return $array;
	}	
}