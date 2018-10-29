<?php
/**
 * PaydayPpc Model
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
class PaydayPpc extends AppModel {
	public $actsAs = array('AuditLog.Auditable');
	public $name = 'PaydayPpc';
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
			'Url' => array(
					'rule' => "url",
					'message' => 'Url',
					'required' => true,
					'allowEmpty' => false),
					
			'LoanAmount' => array(
					'rule' => "/200|300|400|500|600|700|750|800|900|1000/",
					'message' => 'LoanAmount',
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
			'ResidentSinceDate' => array(
					'rule' => "/^((0[1-9])|(1[0-2]))\/(\d{4})$/",
					'message' => 'ResidentSinceDate',
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
			'ResidenceType' => array(
					'rule' => "/(ownwmtg|ownwomtg|rent)/",
					'message' => 'ResidenceType',
					'required' => true,
					'allowEmpty' => false),
			'HomePhone' => array(
					'rule' => "/^[0-9]{10}$/",
					'message' => 'HomePhone',
					'required' => true,
					'allowEmpty' => false),
			'CellPhone' => array(
					'rule' => "/^[0-9]{10}$/",
					'message' => 'HomePhoneArea',
					'required' => false,
					'allowEmpty' => true),
			'CreditRating' => array(
				'rule' => "/(excellent|good|fair|poor|unsure)/",
				'message' => 'CreditRating',
				'required' => true,
				'allowEmpty' => false),	
			'Ssn' => array(
					'rule' => array('ssn', '/^[0-9]{9}$/', 'us'),
					'message' => 'Ssn',
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
					'rule'  => "/^([a-zA-Z0-9\s-'\.\,#_\&\/]{1,50})$/",
					'message' => 'EmployerName',
					'required' => true,
					'allowEmpty' => false),		
			'EmploymentTime' => array(
					'rule' => "/^((0[1-9])|(1[0-2]))\/(\d{4})$/",
					'message' => 'EmploymentTime',
					'required' => true,
					'allowEmpty' => false),			
			'EmployerAddress' => array(
					'rule' => "/^([a-zA-Z0-9\s-'\.\,\_\#\&\/]{1,50})$/",
					'message' => 'EmployerAddress',
					'required' => true,
					'allowEmpty' => false),
			'EmployerCity' => array(
					'rule' => "/^([a-zA-Z\s-\.']{1,50})$/",
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
					'rule' => "/(3|6|9|12|24|36|48|60|72)/",
					'message' => 'BankTime',
					'required' => true,
					'allowEmpty' => false),
			/*'PhoneType' => array(
					'rule' => "/(Mobile|Home)/",
					'message' => 'PhoneType',
					'required' => true,
					'allowEmpty' => false)*/
	);
	
	
	
	
	public function buildPost(){
		
		$data = $this->data['PaydayPpc'];	
			
		App::import('Model','ApiPostFunctions');
		$ap = new ApiPostFunctions();

		$data['DateOfBirthMonth'] = substr($data['DateOfBirth'], 0, 2);
		$data['DateOfBirthDay'] = substr($data['DateOfBirth'], 3, 2);
		$data['DateOfBirthYear'] = substr($data['DateOfBirth'], 6, 4);
		
		$ssn_explode = explode("-", $data['SSN']);
		$data['Ssn1'] = $ssn_explode[0];
		$data['Ssn2'] = $ssn_explode[1];
		$data['Ssn3'] = $ssn_explode[2];
		
		$ap->init($data);
		$ap->data['ResidenceTotalMonths'] = $ap->getResidenceTimeMonthsv1();
		$ap->data['ResidenceTimeYear'] = $ap->getResidenceYearsPartv1();
		$ap->data['EmploymentTotalMonths'] = $ap->getEmployerTimeMonthsv1();
		$ap->data['EmploymentTimeYear'] = $ap->getEmployerYearsPartv1();
	
		$main = array(	'residence_years_part'			=>	$ap->getResidenceYearsPartv1(),
						'residence_months_part'			=>	$ap->getResidenceMonthsPartv1(),
						'emp_years_part'			=>	$ap->getEmployerYearsPartv1(),
						'emp_months_part'			=>	$ap->getEmployerMonthsPartv1(),
						'emp_months'			=>	$ap->getEmployerTimeMonthsv1(),
						'residence_months'			=>	$ap->getResidenceTimeMonthsv1(),
						'credit_rating'			=>	$data['CreditRating'],
						'residence_type'			=>	$data['ResidenceType'],
						'ref_1_name'			=>	"Not Collected",
						'ref_1_phone'			=>	"7707905940",
						'ref_1_relationship'			=>	"Not Collected",
						'ref_2_name'			=>	"Not Collected",
						'ref_2_phone'			=>	"7707905940",
						'ref_2_relationship'			=>	"Not Collected",
						'residence_since'			=>	$ap->getResidenceTimeSincev1(),
						'emp_hire_date'			=>	$ap->getEmployerTimeSincev1(),
						'loan_amount'			=>	$data['LoanAmount'],
						'income_source'			=>	$data['EmployeeType'],
						'bank_months_at'			=>	$data['BankTime'],
						'phone_secondary'			=>	$data['CellPhone'],
						'phone_mobile'			=>	$data['CellPhone'],
						'ssn'			=>	$data['Ssn'],
						'first_name'			=>	$data['FirstName'],
						'last_name'			=>	$data['LastName'],
						'address_1'			=>	$data['Address1'],
						'address_2'			=>	$data['Address2'],
						'city'			=>	$data['City'],
						'state'			=>	$data['State'],
						'zip'			=>	$data['Zip'],
						'phone_primary'			=>	$data['HomePhone'],
						'citizen'			=>	'true',
						'date_of_birth'			=>	$ap->getDateOfBirth(),
						'age'			=>	$ap->getAge(),
						'email'			=>	$data['Email'],
						'dl_state'			=>	$data['DriversLicenseState'],
						'dl_number'			=>	$data['DriversLicenseNumber'],
						'emp_name'			=>	$data['EmployerName'],
						'emp_address'			=>	$data['EmployerAddress'],
						'emp_city'			=>	$data['EmployerCity'],
						'emp_state'			=>	$data['EmployerState'],
						'emp_zip'			=>	$data['EmployerZip'],
						'phone_work'			=>	$data['WorkPhone'],
						'monthly_income'			=>	$data['MonthlyNetIncome'],
						'income_range'			=>	$ap->getIncomeRange(),
						'income_range2'			=>	$ap->getIncomeRange2(),
						'yearly_income_range'			=>	$ap->getYearlyIncomeRange(),
						'pay_date_1' 	=>	 $data['Paydate1'],
						'pay_date_2' 	=>	 $data['Paydate2'],
						'pay_date_3' 	=>	 $ap->getPayDate3US(),
						'pay_frequency' 	=>	 $data['PayFrequency'],
						'calc_income' 	=>	 $ap->getCalcIncomeUS(),
						'direct_deposit' 	=>	 $data['DirectDeposit'],
						'active_military' 	=>	 $data['Military'],
						'bank_name' 	=>	 $ap->getBankName(),
						'bank_account_type' 	=>	 $data['BankAccountType'],
						'bank_routing' 	=>	 $data['BankRoutingNumber'],
						'bank_number' 	=>	 $data['BankAccountNumber'],
						'opt_consent'			=>	$data['AgreeConsent'],
						'accept_terms'			=>	$data['Agree'],
						'opt_in'			=>	$data['AgreePhone'],
						'ip_address'			=>	$data['IPAddress'],
						'country'			=>	'US',
						'browser_info'			=>	'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0',
						'client_url'			=>	$data['Url'],
						'mobile_browser'			=>	'false',
						'best_contact_time' => 'morning',
						'effective_date'			=>	$ap->getEffectiveDate(),
						'active_bankruptcy'			=>	'false',
						'phone_type' 	=>	 	'Home',
						'residence_months_map' 	=>	 	$ap->getResidenceMonthsMap(),
						'emp_months_map' 	=>	 	$ap->getEmployerMonthsMap(),
						'bank_months_at' 	=>	 	'12',
						'bank_months_map' 	=>	 	'12',
						'created_time_stamp' => 	$ap->getTimestamp()
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