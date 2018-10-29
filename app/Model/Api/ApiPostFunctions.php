<?php
/**
 * ApiPostFunctions Marketing Model
 *
 * This model contains the data function for the cake marketing controller.
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
class ApiPostFunctions extends AppModel {
	var $data;
	
	/*
	 *Store the posted data in an Array. 
	 * This data will be used throughout to build the post to cake
	 * @param Array $data
	 */
	public function init(ARRAY $data){
		
		$this->data = $data;
		
		
	}
	
	
	
	public function getResidenceMonthsMap(){
		
		$res_months = (INT)$this->data['ResidenceTotalMonths'];
		
		if($res_months <= 12){
			return $res_months;
		}else{
			$years = (INT)$this->data['ResidenceTimeYear'];
			return $years * 12; 
		}
		
		
	}
	
	
	
	public function getEmployerMonthsMap(){
		
		$emp_months = (INT)$this->data['EmploymentTotalMonths'];
		
		if($emp_months <= 12){
			return $emp_months;
		}else{
			$years = (INT)$this->data['EmploymentTimeYear'];
			return $years * 12; 
		}
	}
	
	
	/**
	 * This function will return residence since date.
	 * Version 1(payday,winship) will call the function timeSincev1(). 
	 * (mm/YYYY)
	 * field - $residence_since
	 * 
	 * @return timeSincev1() function result
	 **/	
	public function getResidenceTimeSincev1(){
		
		$parts = $this->_residenceParts();
		
		$month = $parts['month'];
		$year = $parts['year'];
			
		return $this->timeSincev1($month, $year);
	}
	
	/**
	 * This function will return Residence Months.
	 * Version 1(payday,winship) will call the function timeMonthsv1().
	 * field - $residence_months
	 * 
	 * @return timeMonthsv1() function result
	 **/	
	public function getResidenceTimeMonthsv1(){
		
		$parts = $this->_residenceParts();
		
		$month = $parts['month'];
		$year = $parts['year'];
			
		return $this->timeMonthsv1($month, $year);
	}
	
	
	/**
	 * This function will return Residence Years Part.
	 * Version 1(payday,winship) will call the function yearsPartv1().
	 * field - $residence_years_part
	 * 
	 * @return yearsPartv1() function result
	 **/	
	public function getResidenceYearsPartv1(){
		
		$parts = $this->_residenceParts();
		
		$month = $parts['month'];
		$year = $parts['year'];
			
		return $this->yearsPartv1($month, $year);
	}
	
	/**
	 * This function will return Residence Months Part.
	 * Version 1(payday,winship) will call the function monthsPartv1().
	 * field - $residence_months_part
	 * 
	 * @return monthsPartv1() function result
	 **/	
	public function getResidenceMonthsPartv1(){
		
		$parts = $this->_residenceParts();
		
		$month = $parts['month'];
		$year = $parts['year'];
			
		return $this->monthsPartv1($month, $year);
	}
	
	
	/**
	 * This function will return employer hire date.
	 * Version 1(payday,winship) will call the function timeSincev1(). 
	 * (mm/YYYY)
	 * field - $employer_hire_date
	 * 
	 * @return timeSincev1() function result
	 **/	
	public function getEmployerTimeSincev1(){
		
		$parts = $this->_employmentParts();
		
		$month = $parts['month'];
		$year = $parts['year'];
			
		return $this->timeSincev1($month, $year);
	}
	
	/**
	 * This function will return Employer Months.
	 * Version 1(payday,winship) will call the function timeMonthsv1().
	 * field - $employer_months
	 * 
	 * @return timeMonthsv1() function result
	 **/	
	public function getEmployerTimeMonthsv1(){
		
		$parts = $this->_employmentParts();
		
		$month = $parts['month'];
		$year = $parts['year'];
		
		$month = $parts['month'];
		$year = $parts['year'];
			
		return $this->timeMonthsv1($month, $year);
	}
	
	
	/**
	 * This function will return Employer Years Part.
	 * Version 1(payday,winship) will call the function yearsPartv1().
	 * field - $employer_years_part
	 * 
	 * @return yearsPartv1() function result
	 **/	
	public function getEmployerYearsPartv1(){
		
		$parts = $this->_employmentParts();
		
		$month = $parts['month'];
		$year = $parts['year'];
			
		return $this->yearsPartv1($month, $year);
	}
	
	/**
	 * This function will return Employer Months Part.
	 * Version 1(payday,winship) will call the function monthsPartv1().
	 * field - $employer_months_part
	 * 
	 * @return monthsPartv1() function result
	 **/	
	public function getEmployerMonthsPartv1(){
		
		$parts = $this->_employmentParts();
		
		$month = $parts['month'];
		$year = $parts['year'];
			
		return $this->monthsPartv1($month, $year);
	}
	
	
	/**
	 * This function will return mobile phone.
	 * Version 1(payday,winship) will call the function monthsPartv1().
	 * (7775550000)
	 * field - $phone_secondary
	 * 
	 * @return homephone as mobilephone
	 **/	
	public function getMobilePhonev1(){
		
		$mobilephone = @$this->data["MobilePhoneArea"].@$this->data["MobilePhonePrefix"].@$this->data["MobilePhoneExchange"];

		return $mobilephone;
	}
	
	/**
	 * This function will return ssn#.
	 * (123456789)
	 * field - $ssn
	 * 
	 * @return ssn function
	 **/	
	public function getSsn(){
		
		$ssn = $this->data["Ssn1"].$this->data["Ssn2"].$this->data["Ssn3"];

		return $ssn;
	}
	
	/**
	 * This function will return Co ssn#.
	 * (123456789)
	 * field - $cossn
	 * 
	 * @return ssn function
	 **/	
	public function getCoSsn(){
		
		$ssn = $this->data["CoSsn1"].$this->data["CoSsn2"].$this->data["CoSsn3"];

		return $ssn;
	}
	
	
	public function getTimestamp(){
		return date("Y-m-d H:i:s");	
	}
	
	
	/**
	 * This function will return residence since date.
	 * Version 2 (secura,aloan) will call the function timeSincev2(). 
	 * (month = 5 / year = 6)
	 * field - $residence_since
	 * 
	 * @return timeSincev2() function result
	 **/	
	public function getResidenceTimeSincev2(){
		$month = @$this->data["ResidenceTimeMonth"];
		$year = @$this->data["ResidenceTimeYear"];
		$this->timeSincev2($month, $year);
		return $this->timeSincev2($month, $year);
	}
	
	/**
	 * This function will return Residence Months.
	 * Version 2 (secura,aloan) will call the function timeMonthsv2().
	 * field - $residence_months
	 * 
	 * @return timeMonthsv2() function result
	 **/	
	public function getResidenceTimeMonthsv2(){
		
		$month = $this->data["ResidenceTimeMonth"];
		$year = $this->data["ResidenceTimeYear"];
			
		return $this->timeMonthsv2($month, $year);
	}
	
	
	/**
	 * This function will return Residence Years Part.
	 * Version 2 (secura,aloan) will call the function yearsPartv2().
	 * field - $residence_years_part
	 * 
	 * @return yearsPartv2() function result
	 **/	
	public function getResidenceYearsPartv2(){
		
		$month = $this->data["ResidenceTimeMonth"];
		$year = $this->data["ResidenceTimeYear"];
			
		return $this->yearsPartv2($month, $year);
	}
	
	/**
	 * This function will return Residence Months Part.
	 * Version 2 (secura,aloan) will call the function monthsPartv2().
	 * field - $residence_months_part
	 * 
	 * @return monthsPartv2() function result
	 **/	
	public function getResidenceMonthsPartv2(){
		
		$month = $this->data["ResidenceTimeMonth"];
		$year = $this->data["ResidenceTimeYear"];
			
		return $this->monthsPartv2($month, $year);
	}
	
	
	/**
	 * This function will return employer hire date.
	 * Version 2 (secura,aloan) will call the function timeSincev2(). 
	 * (month = 5 / year = 6)
	 * field - $employer_hire_date
	 * 
	 * @return timeSincev2() function result
	 **/	
	public function getEmployerTimeSincev2(){
		
		$month = $this->data["EmploymentTimeMonth"];
		$year = $this->data["EmploymentTimeYear"];
			
		return $this->timeSincev2($month, $year);
	}
	
	/**
	 * This function will return Employer Months.
	 * Version 2 (secura,aloan) will call the function timeMonthsv2().
	 * field - $employer_months
	 * 
	 * @return timeMonthsv2() function result
	 **/	
	public function getEmployerTimeMonthsv2(){
		
		$month = $this->data["EmploymentTimeMonth"];
		$year = $this->data["EmploymentTimeYear"];
			
		return $this->timeMonthsv2($month, $year);
	}
	
	
	
	/**
	 * This function will return Employer Years Part.
	 * Version 1(PL) will call the function yearsPartv1().
	 * field - $employer_years_part
	 * 
	 * @return yearsPartv1() function result
	 **/	
	public function getCoEmployerYearsPartv1(){
		
		$parts = $this->_coEmploymentParts();
		
		$month = $parts['month'];
		$year = $parts['year'];
			
		return $this->yearsPartv1($month, $year);
	}
	
	/**
	 * This function will return Employer Months Part.
	 * Version 1(PL) will call the function monthsPartv1().
	 * field - $employer_months_part
	 * 
	 * @return monthsPartv1() function result
	 **/	
	public function getCoEmployerMonthsPartv1(){
		
		$parts = $this->_coEmploymentParts();
		
		$month = $parts['month'];
		$year = $parts['year'];
		return $this->timeMonthsv1($month, $year);
	}
	
	
	
	
	
	
	/**
	 * This function will return Employer Years Part.
	 * Version 2 (secura,aloan) will call the function yearsPartv2().
	 * field - $employer_years_part
	 * 
	 * @return yearsPartv2() function result
	 **/	
	public function getEmployerYearsPartv2(){
		
		$month = $this->data["EmploymentTimeMonth"];
		$year = $this->data["EmploymentTimeYear"];
			
		return $this->yearsPartv2($month, $year);
	}
	
	/**
	 * This function will return Employer Months Part.
	 * Version 2 (secura,aloan) will call the function monthsPartv2().
	 * field - $employer_months_part
	 * 
	 * @return monthsPartv2() function result
	 **/	
	public function getEmployerMonthsPartv2(){
		
		$month = $this->data["EmploymentTimeMonth"];
		$year = $this->data["EmploymentTimeYear"];
			
		return $this->monthsPartv2($month, $year);
	}
	
	/**
	 * This function will return mobile phone.
	 * Version 2(secura,aloan) will get homephone and return as mobile phone
	 * (7775550000)
	 * field - $mobile_phone
	 * 
	 * @return homephone as mobilephone
	 **/	
	public function getMobilePhonev2(){
		
		if ($this->data['PrimaryPhoneType'] == 'Mobile') {
			return $this->getHomePhone();
		}else{
			return "";
		}

	}
	
	/**
	 * This function will return best phone.
	 * Version 2(secura,aloan) will get homephone and return as best phone
	 * (7775550000)
	 * field - $best_phone
	 * 
	 * @return homephone as best phone
	 **/	
	public function getBestPhonev2(){
		
		$homephone = $this->data["HomePhoneArea"].$this->data["HomePhonePrefix"].$this->data["HomePhoneExchange"];
			
		return $homephone;
	}
	
	
	/**
	 * This function will return Home Phone.
	 * (7775550000)
	 * field - $phone_primary
	 * 
	 * @return homephone as best phone
	 **/	
	public function getHomePhone(){
		
		$homephone = $this->data["HomePhoneArea"].$this->data["HomePhonePrefix"].$this->data["HomePhoneExchange"];
			
		return $homephone;
	}
	
	/**
	 * This function will return work phone.
	 * (7775550000)
	 * field - $phone_work
	 * 
	 * @return workphone
	 **/	
	public function getWorkPhone(){
		
		$workphone = $this->data["WorkPhoneArea"].$this->data["WorkPhonePrefix"].$this->data["WorkPhoneExchange"];

		return $workphone;
	}
	
	/**
	 * This function will return Co work phone.
	 * (7775550000)
	 * field - $co_work_phone
	 * 
	 * @return workphone
	 **/	
	public function getCoWorkPhone(){
		
		$workphone = $this->data["CoWorkPhoneArea"].$this->data["CoWorkPhonePrefix"].$this->data["CoWorkPhoneExchange"];

		return $workphone;
	}
	
	
	/**
	 * This function will return prev work phone.
	 * (7775550000)
	 * field - $prev_phone_work
	 * 
	 * @return prev_workphone
	 **/	
	public function getPrevWorkPhone(){
		
		$workphone = $this->data["PreviousWorkPhoneArea"].$this->data["PreviousWorkPhonePrefix"].$this->data["PreviousWorkPhoneExchange"];

		return $workphone;
	}
	
	
	/**
	 * This function will return Date Of Birth.
	 * field - $date_of_birth
	 * 
	 * @return dateOfBirth() function
	 **/	
	public function getDateOfBirth(){
		$parts = explode('/', $this->data["DateOfBirth"]);
		$dobm = $parts[0];
		$dobd = $parts[1];
		$doby = $parts[2];
		
		return $this->dateOfBirth($dobm,$dobd,$doby);
			
	}
	
	/**
	 * This function will return Age.
	 * field - $age
	 * 
	 * @return age() function
	 **/	
	public function getAge(){
		
		$parts = explode('/', $this->data["DateOfBirth"]);
		$dobm = $parts[0];
		$dobd = $parts[1];
		$doby = $parts[2];
		
		return $this->age($dobm,$dobd,$doby);
			
	}
	
	
	public function getCoApplicant(){
		$coapp = $this->data['CoApplicant'];
		
		return $this->yesNoBool($coapp);
	}
	
	
	/**
	 * This function will return Income Range.
	 * field - $income_range
	 * 
	 * @return IncomeRange() function
	 **/	
	public function getIncomeRange(){
		
		$monthly_income = $this->data['MonthlyNetIncome'];
		
		return $this->incomeRange($monthly_income);
			
	}
	
	
	/**
	 * This function will return Income Range2.
	 * field - $income_range2
	 * 
	 * @return IncomeRange2() function
	 **/	
	public function getIncomeRange2(){
		
		$monthly_income = $this->data['MonthlyNetIncome'];
		
		return $this->incomeRange2($monthly_income);
			
	}
	
	/**
	 * This function will return Yearly Income Range.
	 * field - $yearly_income_range
	 * 
	 * @return yearlyIncomeRange() function
	 **/	
	public function getYearlyIncomeRange(){
		
		$monthly_income = $this->data['MonthlyNetIncome'];
		
		return $this->yearlyIncomeRange($monthly_income);
			
	}
	
	/**
	 * This function will return PayDate1.
	 * field - $pay_date_1
	 * 
	 * @return payDate1() function
	 **/	
	public function getPayDate1(){
		
		$paydate1 = $this->data['Paydate1'];
		$format = $this->format;
		if(empty($format))$this->format = "false";
		
		return $this->payDate1($paydate1,$format);
			
	}

	/**
	 * This function will return PayDate1.
	 * field - $pay_date_2
	 * 
	 * @return payDate2() function
	 **/	
	public function getPayDate2(){
		
		$paydate2 = $this->data['Paydate2'];
		$format = $this->format;
		if(empty($format))$this->format = "false";
		
		return $this->payDate2($paydate2,$format);
			
	}
	
	/**
	 * This function will return PayDate3.
	 * field - $pay_date_3
	 * 
	 * @return payDate3() function
	 **/	
	public function getPayDate3US(){
		
		$paydate1 = $this->data['Paydate1'];
		$pay_frequency = $this->data['PayFrequency'];
		$format = $this->format;
		if(empty($format))$this->format = "false";
		
		return $this->payDate3US($paydate1,$pay_frequency,$format);
			
	}
	
	
	/**
	 * This function will return CalcIncome.
	 * field - calc_income
	 * 
	 * @return calcIncome() function
	 **/	
	public function getCalcIncomeUS(){
		
		$monthly_income = $this->data['MonthlyNetIncome'];
		$pay_frequency = $this->data['PayFrequency'];
	
		return $this->calcIncomeUS($monthly_income, $pay_frequency);
			
	}
	
	
	
	/**
	 * This function will return IPaddress.
	 * field - ip_address
	 * 
	 * @return ipAddress() function
	 **/	
	public function getIpAddress(){
		
		return $this->ipAddress();
			
	}
	
	/**
	 * This function will return User Agent.
	 * field - browser_info
	 * 
	 * @return browserInfo() function
	 **/	
	public function getBrowserInfo(){
		
		$browser_info = $this->browserInfo();
	
		return $browser_info;
			
	}
	
	
	/**
	 * This function will return Url.
	 * field - url
	 * 
	 * @return url() function
	 **/	
	public function getUrl(){
		
		$url = $this->url();
	
		return $url;
			
	}
	
	
	/**
	 * This function will return Is Mobile.
	 * field - mobile_browser
	 * 
	 * @return isMobile() function
	 **/	
	public function getIsMobile(){
		
		$is_mobile = $this->isMobile();
	
		return $is_mobile;
			
	}
	
	/**
	 * This function will return Effective Date.
	 * field - effective_date
	 * timestamp
	 * 
	 * @return effectiveDate() function
	 **/	
	public function getEffectiveDate(){
		
		$format = $this->format;
		if(empty($format))$this->format = "false";
		
		$date = $this->timestamp($format);
	
		return $date;
			
	}
	
	
	/**
	 * This function will return Co-applicant best phone.
	 * Version 2(secura,aloan) will get Co-applicant homephone and return as Co-applicant best phone
	 * (7775550000)
	 * field - $co_app_best_phone
	 * 
	 * @return Co-applicant homephone as Co-applicant best phone
	 **/	
	public function getCoBestPhonev2(){
		
		$co_homephone = $this->data["CoHomePhoneArea"].$this->data["CoHomePhonePrefix"].$this->data["CoHomePhoneExchange"];
			
		return $co_homephone;
	}
	
	/**
	 * This function will return Co-applicant Date Of Birth.
	 * field - $co_app_dob
	 * 
	 * @return dateOfBirth() function
	 **/	
	public function getCoDateOfBirth(){
		
		$parts = explode('/', $this->data["CoDateOfBirth"]);
		$dobm = $parts[0];
		$dobd = $parts[1];
		$doby = $parts[2];
		
		return $this->dateOfBirth($dobm,$dobd,$doby);
			
	}
	
	
	
		
	/**
	 * This function will return Co-applicant employer hire date.
	 * Version 2 (secura,aloan) will call the function timeSincev2(). 
	 * (month = 5 / year = 6)
	 * field - co_app_emp_start_date
	 * 
	 * @return timeSincev2() function result
	 **/	
	public function getCoEmployerTimeSincev2(){
		
		$parts = $this->_coEmploymentParts();
		
		$month = $parts['month'];
		$year = $parts['year'];
			
		return $this->timeSincev1($month, $year);
	}
	
	/**
	 * This function will return Co-applicant Employee Type.
	 * field - $co_app_income_source
	 * 
	 * @return coEmployeeType() function
	 **/	
	public function getCoEmployeeType(){
		
		$income_source = $this->data['CoEmployeeType'];
		$co_app = $this->data['CoApplicant'];
		
		return $this->coEmployeeType($income_source, $co_app);
			
	}
	
	/**
	 * This function will return Co-applicant Same Address.
	 * field - $co_app_same_address
	 * 
	 * @return coSameAddress() function
	 **/	
	public function getCoSameAddress(){
		
		$same_address = $this->data['CoAppSameAddr'];
		$co_app = $this->data['CoApplicant'];
		
		return $this->coSameAddress($same_address, $co_app);
			
	}
	
	
	/**
	 * This function will return  previous residence since date.
	 * Version 2 (secura,aloan) will call the function timeSincev2(). 
	 * (month = 5 / year = 6)
	 * field - $residence_prev_since
	 * 
	 * @return timeSincev2() function result
	 **/	
	public function getPrevResidenceTimeSincev2(){
		
		$month = $this->data["PreviousResidenceTimeMonth"];
		$year = $this->data["PreviousResidenceTimeYear"];
			
		return $this->timeSincev2($month, $year);
	}
	
	/**
	 * This function will return Previous Residence Months.
	 * Version 2 (secura,aloan) will call the function timeMonthsv2().
	 * field - $residence_prev_months
	 * 
	 * @return timeMonthsv2() function result
	 **/	
	public function getPrevResidenceTimeMonthsv2(){
		
		$month = $this->data["PreviousResidenceTimeMonth"];
		$year = $this->data["PreviousResidenceTimeYear"];
			
		return $this->timeMonthsv2($month, $year);
	}
	
	
	/**
	 * This function will return previous Residence Years Part.
	 * Version 2 (secura,aloan) will call the function yearsPartv2().
	 * field - $residence_prev_years_part
	 * 
	 * @return yearsPartv2() function result
	 **/	
	public function getPrevResidenceYearsPartv2(){
		
		$month = $this->data["PreviousResidenceTimeMonth"];
		$year = $this->data["PreviousResidenceTimeYear"];
			
		return $this->yearsPartv2($month, $year);
	}
	
	/**
	 * This function will return Previous Residence Months Part.
	 * Version 2 (secura,aloan) will call the function monthsPartv2().
	 * field - $residence_prev_months_part
	 * 
	 * @return monthsPartv2() function result
	 **/	
	public function getPrevResidenceMonthsPartv2(){
		
		$month = $this->data["PreviousResidenceTimeMonth"];
		$year = $this->data["PreviousResidenceTimeYear"];
			
		return $this->monthsPartv2($month, $year);
	}

	/*
	 * This function will return since date.
	 * Modified with ltrim cause someone decided to use (mm)
		 * 
	 	 * Version 1(payday,winship)
		 * @param - $month (mm)
		 * @param - $year (YYYY)
		 * @return $since (ISO 8601 date) 
	* */
	public function timeSincev1($month, $year){
		$day = "01";
		$month = ltrim($month,'0');
		$since = date('m-d-Y', mktime(1, 1, 1, ($month+0), $day, $year));
		return $since;
	}
	
	
	/*
	 * This function will return total months
	 * Modified with ltrim cause someone decided to use (mm)
		 *
	     * Version 1(payday,winship) 
		 * @param - $month (mm)
		 * @param - $year (YYYY)
		 * @return $months 
	* */
	public function timeMonthsv1($month, $year){
		$day = "01";
		$month = ltrim($month,'0');
		$rdate = mktime(22,0,0,($month+0),$day,$year);
		$today = time();
		$months = floor(($today-$rdate)/2628000);
		if ($months < 1 || empty($months)) $months = 1;
		
		return $months;
	}

	
	/*
	 * This function will return years part
		 * 
	 	 * Version 1(payday,winship)
		 * @param - $month (mm)
		 * @param - $year (YYYY)
		 * @return $years_part 
	* */
	public function yearsPartv1($month, $year){
		$months = $this->timeMonthsv1($month, $year);
		
		if ($months >= 12) {
			$years_part = floor($months/12);
		} else {
			$years_part = '0';
		}
		
		if($years_part > 10)$years_part='10';
	
		return $years_part;
	}


	/*
	 * This function will return months part
		 * 
	 	 * Version 1(payday,winship)
		 * @param - $month (mm)
		 * @param - $year (YYYY)
		 * @return $months_part 
	* */
	public function monthsPartv1($month, $year){
		$months = $this->timeMonthsv1($month, $year);
		
		if ($months >= 12) {
			$months_part = fmod($months,12);
			if(empty($months_part) || $months_part == 0 || $months_part === 0){
				$months_part='0';
			}
		} else {
			$months_part = $months;
		}
	
		return $months_part;
	}


	/*
	 * This function will return since date.
		 * 
	 	 * Version 2(secura,aloan)
		 * @param - $month (2)
		 * @param - $year (5)
		 * @return $since (ISO 8601 date) 
	* */
	public function timeSincev2($month, $year){
		$submonth = -(intval($month));
		$subyear = -(intval($year));
		$since = $this->_addDate(date('Y-m-d h:i:s'),0,$submonth,$subyear); 
		return $since;
	}
	
	
	/*
	 * This function will return total months
		 *
	     * Version 2(secura,aloan) 
		 * @param - $month (2)
		 * @param - $year (5)
		 * @return $months 
	* */
	public function timeMonthsv2($month, $year){
		$months = intval($month)+((intval($year)*12)); 
		if ($months < 1 || empty($months)) $months = 1;
		
		return $months;
	}

	
	/*
	 * This function will return years part
		 * 
	 	 * Version 2(secura,aloan)
		 * @param - $month (2)
		 * @param - $year (5)
		 * @return $years_part 
	* */
	public function yearsPartv2($month, $year){
		$years_part = $year;
		
	
		return $years_part;
	}


	/*
	 * This function will return months part
		 * 
	 	 * Version 2(secura,aloan)
		 * @param - $month (2)
		 * @param - $year (5)
		 * @return $months_part 
	* */
	public function monthsPartv2($month, $year){
		$months_part = $month;	
	
		return $months_part;
	}


	/*
	 * This function will return Date Of Birth
		 * 
	 	 * @param - $month (mm)
		 * @param - $day (dd)
	 	 * @param - $year (YYYY) 	
		 * @return $dob
	* */
	public function dateOfBirth($dobm,$dobd,$doby){
		$dob = $dobm."-".$dobd."-".$doby;
	
		return $dob;
	}
	
	/*
	 * This function will return Age
		 * 
	 	 * @param - $month (mm)
		 * @param - $day (dd)
	 	 * @param - $year (YYYY) 	
		 * @return $year_diff
	* */
	public function age($month,$day,$year){
		$year_diff  = (int)date("Y") - $year;
		$month = (int)$month;
		$day = (int)$day;
		$year = (int)$year;
		
			//User has not had a birthday yet
			if(date("m") < $month){
				--$year_diff;
			}else{
				//Check to see if we are in the same month as birthday
				if($month == date("m")){
					//Birthday has not passed yet
					if(date("d") < $day){
						--$year_diff;
					}
				}
			}
			return $year_diff;
	}
	
	public function bestContactTime($bct){
		if($bct == 'morning'){
			$value = "M";
		}else if($bct == 'Afternoon'){
			$value = "A";
		}else if($bct == 'evening'){
			$value = "E";
		}else{
			$value = "E";
		}
		
		return $value;
	}
	
	public function yesNoBool($str){
		if($str == "Yes"){
			return "1";
		}else{
			return "0";
		}
	}
	
	
	public function trueFalseBool($str){
		if($str == "true"){
			return "1";
		}else{
			return "0";
		}
	}
	
	public function getPhone($area, $prefix, $exchange){
		return $area.$prefix.$exchange;
	}

	
	/*
	 * This function will Income Range
		 * 
	 	 * @param - $monthly_income
		 * @return $income_range
	* */
	public function incomeRange($monthly_income){
		$income_range = "Less Than 500";
		if ($monthly_income < 500) {
			$income_range = "Less Than 500";
		} else if ($monthly_income >= 500 && $monthly_income <= 800) {
			$income_range = "500 - 800";
		} else if ($monthly_income >= 801 && $monthly_income <= 999) {	
			$income_range = "801 - 999";
		} else if ($monthly_income >= 1000 && $monthly_income <= 1250) {
			$income_range = "1000 - 1250";
		} else if ($monthly_income >= 1251 && $monthly_income <= 1500) {
			$income_range = "1251 - 1500";
		} else if ($monthly_income >= 1501 && $monthly_income <= 1750) {
			$income_range = "1501 - 1750";
		} else if ($monthly_income >= 1751 && $monthly_income <= 2000) {
			$income_range = "1751 - 2000";
		} else if ($monthly_income >= 2001 && $monthly_income <= 2250) {
			$income_range = "2001 - 2250";
		} else if ($monthly_income >= 2251 && $monthly_income <= 2500) {
			$income_range = "2251 - 2500";
		} else if ($monthly_income >= 2751 && $monthly_income <= 2750) {
			$income_range = "2501 - 2750";
		} else if ($monthly_income >= 2751 && $monthly_income <= 3000) {
			$income_range = "2751 - 3000";
		} else if ($monthly_income >= 3001 && $monthly_income <= 3250) {
			$income_range = "3001 - 3250";
		} else if ($monthly_income >= 3251 && $monthly_income <= 3500) {
			$income_range = "3251 - 3500";
		} else if ($monthly_income >= 3501 && $monthly_income <= 3750) {
			$income_range = "3501 - 3750";
		} else if ($monthly_income >= 3751 && $monthly_income <= 4000) {
			$income_range = "3751 - 4000";
		} else if ($monthly_income >= 4001 && $monthly_income <= 4250) {
			$income_range = "4001 - 4250";
		} else if ($monthly_income >= 4251 && $monthly_income <= 4500) {
			$income_range = "4251 - 4500";
		} else if ($monthly_income >= 4501 && $monthly_income <= 4750) {
			$income_range = "4501 - 4750";
		} else if ($monthly_income >= 4751 && $monthly_income <= 5000) {
			$income_range = "4751 - 5000";
		} else if ($monthly_income > 5000) {
			$income_range = "More Than 5000";
		}

		return $income_range;
	}


	/*
	 * This function will Income Range2
		 * 
	 	 * @param - $monthly_income
		 * @return $income_range2
	* */
	public function incomeRange2($monthly_income){
		$income_range2 = "NA";
		if ($monthly_income >= 500 && $monthly_income <= 750) {
			$income_range2 = "$500-750";
		} else if ($monthly_income >= 751 && $monthly_income <= 1000) {	
			$income_range2 = "$751-1,000";
		} else if ($monthly_income >= 1001 && $monthly_income <= 1250) {
			$income_range2 = "$1,001-1,250";
		} else if ($monthly_income >= 1251 && $monthly_income <= 1500) {
			$income_range2 = "$1,251-1,500";
		} else if ($monthly_income >= 1501 && $monthly_income <= 1750) {
			$income_range2 = "$1,501-1,750";
		} else if ($monthly_income >= 1751 && $monthly_income <= 2000) {
			$income_range2 = "$1,751-2,000";
		} else if ($monthly_income >= 2001 && $monthly_income <= 2500) {
			$income_range2 = "$2,001-2,500";
		} else if ($monthly_income >= 2501 && $monthly_income <= 3000) {
			$income_range2 = "$2,501-3,000";
		} else if ($monthly_income >= 3001 && $monthly_income <= 3500) {
			$income_range2 = "$3,001-3,500";
		} else if ($monthly_income >= 3501 && $monthly_income <= 4000) {
			$income_range2 = "$3,501-4,000";
		} else if ($monthly_income >= 4001 && $monthly_income <= 4500) {
			$income_range2 = "$4,001-4,500";
		} else if ($monthly_income >= 4501 && $monthly_income <= 5000) {
			$income_range2 = "$4,501-5,000";
		} else if ($monthly_income > 5000) {
			$income_range2 = "5,000+";
		}
		
		return $income_range2;
	}

	
	/*
	 * This function will Yearly Income Range
		 * 
	 	 * @param - $monthly_income
		 * @return $yearly_income_range
	* */
	public function yearlyIncomeRange($monthly_income){
		$yearly_income = $monthly_income*12;
		$yearly_income_range = '1';
		if ($yearly_income <= 15000)
		{
			$yearly_income_range = '2';
		}
		else if ($yearly_income <= 20000)
		{
			$yearly_income_range = '3';
		}
		else if ($yearly_income <= 25000)
		{
			$yearly_income_range = '4';
		}
		else if ($yearly_income < 30000)
		{
			$yearly_income_range = '5';
		}
		else if ($yearly_income <= 35000)
		{
			$yearly_income_range = '6';
		}
		else if ($yearly_income <= 40000)
		{
			$yearly_income_range = '7';
		}
		else if ($yearly_income <= 45000)
		{
			$yearly_income_range = '8';
		}
		else if ($yearly_income <= 50000)
		{
			$yearly_income_range = '9';
		}
		else if ($yearly_income < 60000)
		{
			$yearly_income_range = '10';
		}
		else if ($yearly_income >= 60000)
		{
			$yearly_income_range = '11';
		}	
		
		return $yearly_income_range;
	}
	
	
	/*
	 * This function will return PayDate1
		 * 
	 	 * @param - $paydate1
	 	 * @param - $format 
		 * @return $paydate
	* */
	public function payDate1($paydate1, $format){
		
		if($format){
			$paydate = date($format, strtotime($paydate1));
		}else{
			$paydate = $paydate1;
		}
		
		return $paydate;
		
	}
	
	/*
	 * This function will return PayDate2
		 * 
	 	 * @param - $paydate2
	 	 * @param - $format 
		 * @return $paydate
	* */
	public function payDate2($paydate2, $format){
		
		if($format){
			$paydate = date($format, strtotime($paydate2));
		}else{
			$paydate = $paydate2;
		}
		
		return $paydate;
		
	}
	


	/*
	 * This function will return PayDate3
		 * 
	 	 * @param - $paydate1
	 	 * @param - $pay_frequency		 
	 	 * @param - $format 
		 * @return $pay_date_3
	* */
	public function payDate3US($paydate1,$pay_frequency,$format){
		if($format){
			$format = $format;
		}else{
			$format = 'c';
		}
		
		$format = 'm/d/Y';
		$pay_date_1 = strtotime($paydate1);
		if ($pay_frequency == 'weekly') {
			$pay_date_3 = date($format, strtotime("-1 week",$pay_date_1));
		} else if ($pay_frequency == 'bi-weekly') {
			$pay_date_3 = date($format, strtotime("-2 weeks",$pay_date_1));
		} else if ($pay_frequency == 'semi-monthly') {
			$pay_date_3 = date($format, strtotime("-2 weeks",$pay_date_1));
		} else {
			$pay_date_3 = date($format, strtotime("-1 month",$pay_date_1));
		}
		//$pay_date_3 = getLastBusinessDay($pay_date_3);
		return $pay_date_3; 
		
		//$explode = explode("T", $pay_date_3);
		//$exploded = explode("-",$explode[0]);
		//$pay_date_3 = date("m/d/Y", mktime(0,0,0, $exploded[1],$exploded[2],$exploded[0]));
		
		
		//return $pay_date_3;
		
	}


	/*
	 * This function will return CalcIncome
		 * 
	 	 * @param - $monthly_income
	 	 * @param - $pay_frequency		 
		 * @return $calc_income
	* */
	public function calcIncomeUS($monthly_income, $pay_frequency){
		$calc_income = $monthly_income; 
		$yearlyNetIncome = $monthly_income*12;
		if ($pay_frequency == 'weekly') {
			$calc_income = $yearlyNetIncome/52;
		} else if ($pay_frequency == 'bi-weekly') {
			$calc_income = $yearlyNetIncome/26;
		} else if ($pay_frequency == 'semi-monthly') {
			$calc_income = $yearlyNetIncome/24;
		}
		$calc_income = intval($calc_income);
		return $calc_income;
		
	}
	
	/*
	 * This function will return IPAddress
		 * 
		 * @return $ip
	* */
	public function ipAddress(){
		$ip = $_SERVER['REMOTE_ADDR'];
		return $ip;
		
	}
	
	
	/*
	 * This function will return Browser Info
		 * 
		 * @return $browser_info
	* */
	public function browserInfo(){
		$browser_info = $_SERVER['HTTP_USER_AGENT'];
	
		return $browser_info;
		
	}
	
	/*
	 * This function will return URl
		 * 
		 * @return $url
	* */
	public function url(){
		$url = $_SERVER['HTTP_HOST'];
		return $url;
		
	}
	
	/*
	 * This function will return Is Mobile
		 * 
		 * @return $isMob
	* */
	public function isMobile(){
		return false;
		$isMob = false;
		$mobile_browser = '0';
	
		if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
			$mobile_browser++;
		}
	
		if ((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') > 0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
			$mobile_browser++;
		}
	
		$mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
		$mobile_agents = array(
			'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
			'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
			'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
			'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
			'newt','noki','oper','palm','pana','pant','phil','play','port','prox',
			'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
			'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
			'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
			'wapr','webc','winw','winw','xda ','xda-');
	
		if (in_array($mobile_ua,$mobile_agents)) {
			$mobile_browser++;
		}
	
		if (strpos(strtolower($_SERVER['ALL_HTTP']),'OperaMini') > 0) {
			$mobile_browser++;
		}
	
		if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'windows') > 0) {
			$mobile_browser = 0;
		}
	
		if ($mobile_browser > 0) {
		   $isMob = true;
		}
	
		return $isMob;
		
	}


	

	/*
	 * This function will return TimeStamp
		 * 
	 	 * @param - $format 	
		 * @return $timestamp
	* */
	public function timestamp($format){
		if($format == "false")$format = 'c';
		
		$timestamp = date("m-d-Y", time());
		
		return $timestamp;
	}


	public function getBankName(){
		$aba = $this->data['BankRoutingNumber'];
		$cache['hash'] = md5('getbankinfo_'.$aba);
		$cache['value'] = false;
		$cache['value'] = Cache::read($cache['hash']);
		
		
		if($cache['value'] === false) {
			App::import('Model','BankRouting');
			$bank_obj = new BankRouting();
			$bank_obj->setDataSource('slave');
			$response['data'] = $bank_obj->getBankInfo($aba);
			$cache['value'] = $response['data'];
			Cache::write($cache['hash'],$cache['value'],'1w');
		} else {
			$response['data'] = $cache['value'];
		}
		
	
		return $response['data']['BankRouting']['name'];
	}



	/*
	 * Date format function
	 */	
	private function _addDate($givendate,$day=0,$mth=0,$yr=0) {
	      $cd = strtotime($givendate);
	      $newdate = date('m-d-Y', mktime(date('h',$cd),
			date('i',$cd), date('s',$cd), date('m',$cd)+$mth,
			date('d',$cd)+$day, date('Y',$cd)+$yr));
	      return $newdate;
	}
	
	
	
	private function _residenceParts(){
		$parts = explode('/', $this->data["ResidentSinceDate"]);
		return array('month'=>$parts[0], 'year'=>$parts[1]);
	}
	
	
	private function _employmentParts(){
		$parts = explode('/', $this->data["EmploymentTime"]);
		return array('month'=>$parts[0], 'year'=>$parts[1]);
	}
	
	
	private function _coEmploymentParts(){
		$parts = explode('/', $this->data["CoEmploymentTime"]);
		return array('month'=>$parts[0], 'year'=>$parts[1]);	
	}
	
	
	
	
}

?>