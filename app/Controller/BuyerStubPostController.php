<?php
/**
 * Service Controller
 * Services can be called from outside domains, because of this all services return in jsonp format.
 * All services are restful.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     TBD
 * @link          https://github.com/AdLink360/keyStone/wiki/ServiceController
 * @package       app.Controller
 * @since         keyStone v1.0
 * @license       TBD
 */
App::uses('AuthComponent', 'Controller/Component');
App::uses('HttpSocket', 'Network/Http');
class BuyerStubPostController extends AppController 
{
	public $uses = array('');
	
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow();
	}

	public function cashCall($data){
	return	'<LeadInformation>
			<LeadApplication>
				<SourceId>1ce3d283-0e1b-43e0-99ff-c1236886ab0d</SourceId>
				<SubId>5595</SubId>
				<SubId2>LEAD00011223</SubId2>
				<InitialStatus>100</InitialStatus>
				<TestLead>true</TestLead>
				<ProductType>200</ProductType>
			    <FundingCompany>Pool 000</FundingCompany>
			    <FirstName>' . $data['FirstName'] . '</FirstName>
			    <MiddleName>' . $data['MiddleName'] . '</MiddleName>
			    <LastName>' . $data['LastName'] . '</LastName>
			    <SSN>' . $data['SSN'] . '</SSN>
			    <Street1>' . $data['Street1'] . '</Street1>
			    <Street2>' . $data['Street2'] . '</Street2>
			    <City>' . $data['City'] . '</City>
			    <State>' . $data['State'] . '</State>
			    <Zip>' . $data['Zip'] . '</Zip>
			    <MonthsAtAddress>12</MonthsAtAddress>
			    <Email>' . $data['Email'] . '</Email>
			    <AlternateEmail>' . $data['AlternateEmail'] . '</AlternateEmail>
			    <EveningPhone>' . $data['EveningPhone'] . '</EveningPhone>
			    <DayPhone>' . $data['DayPhone'] . '</DayPhone>
			    <DayPhoneExtension>' . $data['DayPhoneExtension'] . '</DayPhoneExtension>
			    <CellPhone>' . $data['CellPhone'] . '</CellPhone>
			    <OtherPhone>' . $data['OtherPhone'] . '</OtherPhone>
			    <FaxPhone>' . $data['FaxPhone'] . '</FaxPhone>
			    <BestTimeToCall>' . $data['BestTimeToCall'] . '</BestTimeToCall>
			    <Birthday>' . $data['Birthday'] . '</Birthday>
			    <DriversLicenseNumber>' . $data['DriversLicenseNumber'] . '</DriversLicenseNumber>
			    <DriversLicenseState>' . $data['DriversLicenseState'] . '</DriversLicenseState>
			    <Employer>' . $data['Employer'] . '</Employer>
			    <EmployerCompanyPhone>' . $data['EmployerCompanyPhone'] . '</EmployerCompanyPhone>
			    <EmploymentJobTitle>' . $data['EmploymentJobTitle'] . '</EmploymentJobTitle>
			    <MonthsEmployed>' . $data['MonthsEmployed'] . '</MonthsEmployed>
			    <IsRetired>' . $data['IsRetired'] . '</IsRetired>
			    <IsSelfEmployed>' . $data['IsSelfEmployed'] . '</IsSelfEmployed>
			    <SelfSelectedCredit>' . $data['SelfSelectedCredit'] . '</SelfSelectedCredit>
			    <IncomeSource>' . $data['IncomeSource'] . '</IncomeSource>
			    <PayFrequency>' . $data['PayFrequency'] . '</PayFrequency>
			    <NextPayDay>' . $data['NextPayDay'] . '</NextPayDay>
			    <RequestedLoanAmount>' . $data['RequestedLoanAmount'] . '</RequestedLoanAmount>
			    <LoanReason>' . $data['LoanReason'] . '</LoanReason>
			    <IsMilitary>' . $data['IsMilitary'] . '</IsMilitary>
			    <MonthlyIncome>' . $data['MonthlyIncome'] . '</MonthlyIncome>
			    <MonthlyExpenses>' . $data['MonthlyExpenses'] . '</MonthlyExpenses>
			    <RentOrOwn>' . $data['RentOrOwn'] . '</RentOrOwn>
			    <Rent>' . $data['Rent'] . '</Rent>
			    <SupplementalIncome>' . $data['SupplementalIncome'] . '</SupplementalIncome>
			    <IsInDebtProgram>' . $data['IsInDebtProgram'] . '</IsInDebtProgram>
			    <BankName>' . $data['BankName'] . '</BankName>
			    <BankPhone>' . $data['BankPhone'] . '</BankPhone>
			    <BankABA>' . $data['BankABA'] . '</BankABA>
			    <BankAccountNumber>' . $data['BankAccountNumber'] . '</BankAccountNumber>
			    <BankAccountType>' . $data['BankAccountType'] . '</BankAccountType>
			    <BankAccountTermInMonths>' . $data['BankAccountTermInMonths'] . '</BankAccountTermInMonths>
			    <HasDirectDeposit>' . $data['HasDirectDeposit'] . '</HasDirectDeposit>
			    <HasMovedRecently>' . $data['HasMovedRecently'] . '</HasMovedRecently>
			    <HasAgreedToEft>' . $data['HasAgreedToEft'] . '</HasAgreedToEft>
			    <IsHomeOwner>' . $data['IsHomeOwner'] . '</IsHomeOwner>
			    <PrevStreet1>' . $data['PrevStreet1'] . '</PrevStreet1>
			    <PrevStreet2>' . $data['PrevStreet2'] . '</PrevStreet2>
			    <PrevCity>' . $data['PrevCity'] . '</PrevCity>
			    <MoveDate>' . $data['MoveDate'] . '</MoveDate>
			    <PrevState>' . $data['PrevState'] . '</PrevState>
			    <PrevZip>' . $data['PrevZip'] . '</PrevZip>
			    <ClientURLRoot>' . $data['ClientURLRoot'] . '</ClientURLRoot>
			    <ClientIPAddress>' . $data['ClientIPAddress'] . '</ClientIPAddress>
			    <AgreedToDialerTCPA>' . $data['AgreedToDialerTCPA'] . '</AgreedToDialerTCPA>
			    <ExtraDataItems />
			    <Employer>' . $data['Employer'] . '</Employer>
			    <EmployerCompanyPhone>' . $data['EmployerCompanyPhone'] . '</EmployerCompanyPhone>
			    <EmploymentJobTitle>' . $data['EmploymentJobTitle'] . '</EmploymentJobTitle>
			    <EmploymentTypeId>' . $data['EmploymentTypeId'] . '</EmploymentTypeId>
		  	</LeadApplication>
		</LeadInformation>';
	}
}
