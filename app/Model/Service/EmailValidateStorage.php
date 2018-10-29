<?php
/**
 * Email Validation and Storage Model
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
App::uses('AuthComponent', 'Controller/Component');
class EmailValidateStorage extends AppModel {
	public $actsAs = array('AuditLog.Auditable');
	public $name = 'EmailValidateStorage';
	public $useTable = 'email_storage';
	
	public $validate = array(
			'email' => array(
				'rule' => 'email',
				'required' => true,
				'message' => 'Invalid email format'
			)
	);
	
	public function getEmail($email){
		$data = $this->find('first', array(
				'fields' => array('EmailValidateStorage.email', 'EmailValidateStorage.score'),
				'conditions' => array('EmailValidateStorage.email' => $email)));
		
		return $data;
	}
	
	public function brightVerify($email){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, Configure::read('EmailAPI.BrightVerify.Url').'?address='.$email.'&apikey='.Configure::read('EmailAPI.DataValidation.ApiKey'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		$response->status = 'valid';
		$response->disposable = false;
		$response->role_address = false;
		
		//$response = json_decode(curl_exec($ch));
		$score = 0;
		curl_close($ch);
		
		if($response->status == 'valid'){$score = $score+1;}else{$score = $score+3;}
		if($response->disposable == true){$score = $score+3;}
		if($response->role_address == true){$score = $score+3;}
		
		$this->data['EmailValidateStorage']['score'] = $score;
		$this->data['EmailValidateStorage']['email'] = $email;
		$this->save($this->data);
		
		return array('email'=>$email,'score'=>$score);
	}
	
	public function dataValidation($email){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, Configure::read('EmailAPI.DataValidation.Url').$email.'/?pretty=true');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization:bearer '.Configure::read('EmailAPI.DataValidation.ApiKey')));
		
		$response = json_decode(curl_exec($ch));
		curl_close($ch);
		
		switch (true){
			case $response->grade == 'A+':
				$score = 1;
			break;
			case $response->grade == 'A':
				$score = 2;
			break;
			case $response->grade == 'B':
				$score = 3;
			break;
			case $response->grade == 'C':
				$score = 4;
			break;
			case $response->grade == 'D':
				$score = 5;
			break;
			case $response->grade == 'F':
				$score = 6;
			break;
			default:
				$score = 0;
			break;
		}
		$this->data['EmailValidateStorage']['score'] = $score;
		$this->data['EmailValidateStorage']['email'] = $email;
		$this->save($this->data);
		
		return array('email'=>$email,'score'=>$score);
	}
}