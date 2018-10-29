<?php
/**
 * ApiRules Model
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
class ApiRules extends AppModel {
	var $data;

	/*
	 * The rules class will return an array.
	 * array[0] - will return a true/false
	 * array[1] - will return a value when needed or can stay blank
	 * 
	 * The DerivedPostFieldsManager will know what to do with derived function, dependencies, and rules to return the correct value.
	 */


	/*
	 *Store the posted data in an Array. 
	 * This data will be used throughout to build the post to cake
	 * @param Array $data
	 */
	public function init(ARRAY $data){
		$this->data = $data;
		
		
	}

	public function coEmployeeTypeRule(){
		if($this->data['CoApplicant'] == "No"){
			return array(true, 'employed');
		}else{
			return array(false,'');
		}
		
	}	

	public function coAppSameAddrRule(){
		if($this->data['CoApplicant'] == "No"){
			return array(true, 'No');
		}else{
			return array(false,'');
		}	
	}



	public function CoAddress1Rule(){
		if($this->data['CoAppSameAddr'] == "Yes"){
			return array(true, $this->data['Address1']);
		}else{
			return array(false,'');	
		}
	} 
	
	public function CoAddress2Rule(){
		if($this->data['CoAppSameAddr'] == "Yes"){
			return array(true, $this->data['Address2']);
		}else{
			return array(false,'');	
		}
	} 
	
	public function CoCityRule(){
		if($this->data['CoAppSameAddr'] == "Yes"){
			return array(true, $this->data['City']);
		}else{
			return array(false,'');	
		}
	} 
	
	public function CoStateRule(){
		if($this->data['CoAppSameAddr'] == "Yes"){
			return array(true, $this->data['State']);
		}else{
			return array(false,'');	
		}
	} 


	public function CoZipRule(){
		if($this->data['CoAppSameAddr'] == "Yes"){
			return array(true, $this->data['Zip']);
		}else{
			return array(false,'');	
		}
	} 

	

}
?>