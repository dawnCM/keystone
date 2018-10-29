<?php
/**
 * PersonalLoan Model
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
class PersonalLoan extends AppModel {
	//public $actsAs = array('AuditLog.Auditable');
	public $name = 'PersonalLoan';
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
			'LoanAmountPersonal' => array(
					'rule' => "/(100|300|500|1000|1500|2500|3500|4500|5500|6500|7500|8500|9500|10000|11500|12500|13500|14500|15000|16500|17500|18500|19500|20500|21500|22500|23500|24500|25500|26500|27500|28500|29500)/",
					'message' => 'LoanAmountPersonal',
					'required' => true,
					'allowEmpty' => false),
			'LoanPurpose' => array(
					'rule' => "/(debt|home|major|auto|other)/",
					'message' => 'LoanPurpose',
					'required' => true,
					'allowEmpty' => false),
			'CoApplicant' => array(
					'rule' => "/(Yes|No|yes|no)/",
					'message' => 'CoApplicant',
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
			'RentMortgage' => array(
					'rule' => "/^[0-9]{1,5}?$/",
					'message' => 'RentMortgage',
					'required' => true,
					'allowEmpty' => false),		
			'HomePhone' => array(
					'ruleOne' => array(
						'rule' => "/^[0-9]{10}$/",
						'message' => 'HomePhone',
						'required' => true,
						'allowEmpty' => false),
					'ruleTwo' => array(
						'rule' => array('doNotMatch','WorkPhone'),
						'message' => 'HomePhoneMatchesWorkPhone',
					)),
			'CellPhone' => array(
					'rule' => "/^[0-9]{10}$/",
					'message' => 'CellPhone',
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
					'rule' => "/^([a-zA-Z0-9-\|\_\/]{0,50})$/",
					'message' => 'EmploymentTime',
					'required' => true,
					'allowEmpty' => false),			
			'EmployerAddress' => array(
					'rule' => "/^([a-zA-Z0-9\s-'\.\,\_\#\&\/]{1,50})$/",
					'message' => 'EmployerAddress',
					'required' => false),
			'EmployerCity' => array(
					'rule' => "/^([a-zA-Z\s-\.']{1,50})$/",
					'message' => 'EmployerCity',
					'required' => false),
			'EmployerState' => array(
					'rule' => "/(AK|AL|AR|AZ|CA|CO|CT|DC|DE|FL|GA|HI|IA|ID|IL|IN|KS|KY|LA|MA|MD|ME|MI|MN|MO|MS|MT|NC|ND|NE|NH|NJ|NM|NV|NY|OH|OK|OR|PA|RI|SC|SD|TN|TX|UT|VA|VT|WA|WI|WV|WY)/",
					'message' => 'EmployerState',
					'required' => false),
			'EmployerZip' => array(
					'rule' => "/^[0-9]{5}?$/",
					'message' => 'EmployerZip',
					'required' => false),				
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
					'rule' => "/(3|6|9|12|24|36|48|60|72|84|96|108)/",
					'message' => 'BankTime',
					'required' => true,
					'allowEmpty' => false),
			/*'PhoneType' => array(
					'rule' => "/(Mobile|Home)/",
					'message' => 'PhoneType',
					'required' => true,
					'allowEmpty' => false)*/
	);
	
	//Validation Dependent on data
	public $validateCoapplicant = array(
			'CoFirstName' => array(
					'rule' => "/^([a-zA-Z\s-'\.]{1,50})$/",
					'message' => 'CoFirstName',
					'required' => true,
					'allowEmpty' => false),
			'CoLastName' => array(
					'rule' => "/^([a-zA-Z\s-'\.]{1,50})$/",
					'message' => 'CoFirstName',
					'required' => true,
					'allowEmpty' => false),
			'CoHomePhone' => array(
					'rule' => "/^[0-9]{10}$/",
					'message' => 'CoHomePhone',
					'required' => true,
					'allowEmpty' => false),
			'CoSsn' => array(
					'rule' => array('ssn', '/^[0-9]{9}$/', 'us'),
					'message' => 'CoSSN',
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
					'rule' => "/^([a-zA-Z0-9\s-'\.\,#_]{1,50})$/",
					'message' => 'CoEmployerName',
					'required' => true,
					'allowEmpty' => false),		
			'CoWorkPhone' => array(
					'rule' => "/^[0-9]{10}$/",
					'message' => 'CoWorkPhone',
					'required' => true,
					'allowEmpty' => false),		
			'CoEmploymentTime' => array(
					'rule' => "/^((0[1-9])|(1[0-2]))\/(\d{4})$/",
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
	
	//Validation Dependent on data
	public $validateCoapplicantSameAddress = array(
			'CoAddress1' => array(
					'rule' => "/^([a-zA-Z0-9\s-'\.\,\_\#\&\/]{1,50})$/",
					'message' => 'CoAddress1',
					'required' => true,
					'allowEmpty' => false),
			'CoAddress2' => array(
					'rule' => "/^([a-zA-Z0-9\s-'\.\,\_\#\&\/]{0,50})$/",
					'message' => 'CoAddress2',
					'required' => false,
					'allowEmpty' => true),
			'CoCity' => array(
					'rule' => "/^([a-zA-Z\s-\.']{1,50})$/",
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
		
		//Add CoApp validation
		if($this->data['PersonalLoan']['CoApplicant'] == 'Yes'){
			foreach($this->validateCoapplicant as $a=>$b){
				$this->validate[$a]=$b;	
			}	
		}
		
		//Add CoApp Address
		if($this->data['PersonalLoan']['CoAppSameAddr'] == 'Yes'){
			foreach($this->validateCoapplicantSameAddress as $c=>$d){
				$this->validate[$c]=$d;	
			}	
		}
		
		
	}
	
	
	
	public function buildPost(){
		App::import('Model','ApiPostFunctions');
		$ap = new ApiPostFunctions();
		
		$data = $this->data['PersonalLoan'];
		
		$data['DateOfBirthMonth'] = substr($data['DateOfBirth'], 0, 2);
		$data['DateOfBirthDay'] = substr($data['DateOfBirth'], 3, 2);
		$data['DateOfBirthYear'] = substr($data['DateOfBirth'], 6, 4);
		
		if(isset($data['CoDateOfBirth']) && !empty($data['CoDateOfBirth'])){
			$data['CoDateOfBirthMonth'] = substr($data['CoDateOfBirth'], 0, 2);
			$data['CoDateOfBirthDay'] = substr($data['CoDateOfBirth'], 3, 2);
			$data['CoDateOfBirthYear'] = substr($data['CoDateOfBirth'], 6, 4);	
		}
		
		
		$ap->init($data);
		$ap->data['ResidenceTotalMonths'] = $ap->getResidenceTimeMonthsv1();
		$ap->data['ResidenceTimeYear'] = $ap->getResidenceYearsPartv1();
		$ap->data['EmploymentTotalMonths'] = $ap->getEmployerTimeMonthsv1();
		$ap->data['EmploymentTimeYear'] = $ap->getEmployerYearsPartv1();
		
		
	$main = array(	'loan_amount_personal' 	=>	 $data['LoanAmountPersonal'],
					'loan_purpose' 	=>	 $data['LoanPurpose'],
					'co_applicant' 	=>	 $data['CoApplicant'],
					'credit_rating' 	=>	 $data['CreditRating'],
					'first_name' 	=>	 $data['FirstName'],
					'last_name' 	=>	 $data['LastName'],
					'residence_since' 	=>	 $ap->getResidenceTimeSincev1(),
					'residence_months' 	=>	 $ap->getResidenceTimeMonthsv1(),
					'residence_years_part' 	=>	 $ap->getResidenceYearsPartv1(),
					'residence_months_part' 	=>	 $ap->getResidenceMonthsPartv1(),
					'address_1' 	=>	 $data['Address1'],
					'address_2' 	=>	 $data['Address2'],
					'city' 	=>	 $data['City'],
					'state' 	=>	 $data['State'],
					'zip' 	=>	 $data['Zip'],
					'residence_type' 	=>	 $data['ResidenceType'],
					'monthly_rent_mortgage' 	=>	 $data['RentMortgage'],
					'phone_primary' 	=>	 $data['HomePhone'],
					'phone_secondary' 	=>	 $data['CellPhone'],
					'phone_mobile' 	=>	 	$data['CellPhone'],
					'ssn' 	=>	 $data['Ssn'],
					'citizen' 	=>	 'true',
					'date_of_birth' 	=>	 $ap->getDateOfBirth(),
					'age' 	=>	 $ap->getAge(),
					'email' 	=>	 $data['Email'],
					'dl_state' 	=>	 $data['DriversLicenseState'],
					'dl_number' 	=>	 $data['DriversLicenseNumber'],
					'income_source' 	=>	 $data['EmployeeType'],
					'emp_name' 	=>	 $data['EmployerName'],
					'emp_hire_date' 	=>	 $ap->getEmployerTimeSincev1(),
					'emp_months' 	=>	 $ap->getEmployerTimeMonthsv1(),
					'emp_years_part' 	=>	 $ap->getEmployerYearsPartv1(),
					'emp_months_part' 	=>	 $ap->getEmployerMonthsPartv1(),
					'emp_address' 	=>	 $data['EmployerAddress'],
					'emp_city' 	=>	 $data['EmployerCity'],
					'emp_state' 	=>	 $data['EmployerState'],
					'emp_zip' 	=>	 $data['EmployerZip'],
					'phone_work' 	=>	 $data['WorkPhone'],
					'monthly_income' 	=>	 $data['MonthlyNetIncome'],
					'income_range' 	=>	 $ap->getIncomeRange(),
					'income_range2' 	=>	 $ap->getIncomeRange2(),
					'yearly_income_range' 	=>	 $ap->getYearlyIncomeRange(),
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
					'phone_type' 	=>	 	'Home',
					'bank_months_at' 	=>	 $data['BankTime'],
					'bank_months_map' 	=>	 $data['BankTime'],
					'opt_consent' 	=>	 $data['AgreeConsent'],
					'opt_in' 	=>	 $data['AgreePhone'],
					'accept_terms' 	=>	 $data['Agree'],
					'ip_address' 	=>	 $data['IPAddress'],
					'best_contact_time' => 'morning',
					'country' 	=>	 'US',
					'browser_info' 	=>	 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0',
					'client_url' 	=>	 $data['Url'],
					'job_title' 	=>	 '1',
					'mobile_browser' 	=>	 'false',
					'created_time_stamp' => 	$ap->getTimestamp(),
					'effective_date' 	=>	 $ap->getEffectiveDate(),
					'residence_months_map' 	=>	 	$ap->getResidenceMonthsMap(),
					'emp_months_map' 	=>	 	$ap->getEmployerMonthsMap(),
					'active_bankruptcy' 	=>	 'false');
			
			if($data['CoApplicant'] == 'Yes'){
			
				$co_app = array(	'coapp_firstname' 	=>	 $data['CoFirstName'],
									'coapp_lastname' 	=>	 $data['CoLastName'],
									'coapp_best_phone' 	=>	 $data['CoHomePhone'],
									'coapp_ssn' 	=>	 	 $data['CoSsn'],
									'coapp_dob' 	=>	 	 $ap->getCoDateOfBirth(),
									'coapp_income_source' 	=>	 $data['CoEmployeeType'],
									'coapp_emp_name' 	=>	 $data['CoEmployerName'],
									'coapp_work_phone' 	=>	 $data['CoWorkPhone'],
									'coapp_monthly_income' 	=>	 $data['CoMonthlyNetIncome'],
									'coapp_emp_years' 	=>	 $ap->getCoEmployerYearsPartv1(),
									'coapp_emp_months' 	=>	 $ap->getCoEmployerMonthsPartv1(),
									'coapp_emp_start_date' 	=>	 $ap->getCoEmployerTimeSincev2(),
									'coapp_same_address' 	=>	 $data['CoAppSameAddr']);
									
				$main = array_merge($main, $co_app);					
									
				if($data['CoAppSameAddr'] == 'Yes'){
					
					$co_app_addr = array(	'coapp_address' 	=>	 $data['Address1'],
											'coapp_address_2' 	=>	 $data['Address2'],
											'coapp_city' 	=>	 $data['City'],
											'coapp_state' 	=>	 $data['State'],
											'coapp_zip' 	=>	 $data['Zip']
					);	
					
				}else{
					
					$co_app_addr = array(	'coapp_address' 	=>	 $data['CoAddress1'],
											'coapp_address_2' 	=>	 $data['CoAddress2'],
											'coapp_city' 	=>	 $data['CoCity'],
											'coapp_state' 	=>	 $data['CoState'],
											'coapp_zip' 	=>	 $data['CoZip']
										);	
					
				}
				
				$main = array_merge($main, $co_app_addr);
			
			}else{
				
				$co_app = array(	'coapp_income_source' 	=>	 'employed',
									'coapp_same_address' 	=>	 'No');
										
				$main = array_merge($main, $co_app);
			}
		
			
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
		
		return ($this->data['PersonalLoan'][$compare] != $value);
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