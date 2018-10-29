<?php
/**
 * ApiDepenency Model
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
class ApiDependency extends AppModel {

	 var $data;
	 
	 //Pass in data for accessibility
	 public function init(ARRAY $data){
	 	$this->data = $data;
	 }

	public function coapplicant(){
		if($this->data['CoApplicant'] == "Yes"){
			return true;
		}else{
			return false;
		}
	}	

	public function coapplicantNotSameAddress(){
		if($this->data['CoAppSameAddr'] == "No"){
			return true;
		}else{
			return false;
		}	
	}

	public function previousaddress(){
		if($this->data['ResidenceTimeYear'] < 2){
			return true;
		}else{
			return false;
		}	
	}

	public function previousemployer(){
		if($this->data['EmploymentTimeYear'] < 2){
			return true;
		}else{
			return false;
		}	
	}

	public function isPersonalLoan(){
		if($this->data['AppType'] == "personalloan"){
			return true;
		}else{
			return false;
		}
	}
	
	public function isPayday(){
		if($this->data['AppType'] == "payday"){
			return true;
		}else{
			return false;
		}
	}
	

}

?>