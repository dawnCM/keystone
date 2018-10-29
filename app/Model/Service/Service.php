<?php
/**
 * Service Model
 *
 * This model contains service data.
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
App::uses('HttpSocket', 'Network/Http');
class Service extends AppModel
{		
	/**
	 * Authenticate api post requests.
	 * @todo add creditial in header check also.
	 * @return boolean
	 */
	public function serviceAuthenticate($data){
		// Pull in affiliate model
		App::import('Model','Affiliate');
		$affiliate = new Affiliate();
	
		// Are the authentication values posted in?
		if(isset($data['api_id']) && isset($data['api_key'])){
			// Look for the record in the affiliate table.
			return ($affiliate->find('count', array('conditions' => array('Affiliate.remote_id'=>$data['api_id'], 'Affiliate.api_key'=>$data['api_key'], 'Affiliate.status_id'=>5))) > 0 ? true : false);
		}else{
			return false;
		}
	}
		
	/**
	 * Add the date to the response and json encode the response array.
	 * @param string $jsonp
	 */
	public function jsonpresponse($response, $callback) {
		$response['response_date'] = date('Y-m-d H:i:s');	
		$callbackFuncName = $callback;
		$jsonp = sprintf("%s(%s)", $callbackFuncName, json_encode($response));
		
		return $jsonp;
	}
		
	/**
	 * Add the date to the response and json encode the array.
	 * @param string $response
	 */
	public function jsonresponse($response) {
		$response['response_date'] = date('Y-m-d H:i:s');
		return json_encode($response);
	}
	
	/**
	 * Health check of system and processes
	 */
	public function heartBeat(){
		$socket = new HttpSocket(array('ssl_verify_peer'=>false, 'ssl_verify_host'=>false));
		$response = $socket->get('https://service.leadstudio.com/getcityStatebyZip/33407');
		return $response->code;
	}
	
	public function buildRedirect($url, $track_id) {
		$url = urldecode($url);
		$url = str_replace('\\',"", $url);
		
		//Get encryption settings
		$encryptionMethod = "AES-256-CBC";
		$secretHash = Configure::read('Global.EncryptionSalt');
		
		//To encrypt
		$encryptedurl = @openssl_encrypt($url, $encryptionMethod, $secretHash);
				
		$redirect = 'https://service.leadstudio.com/r/'.base64_encode($encryptedurl).'/'.base64_encode($track_id);
		return $redirect;
	}
	
	public function decryptRedirect($encrypted) {
		//Get encryption settings
		$encryptionMethod = "AES-256-CBC";
		$secretHash = Configure::read('Global.EncryptionSalt');

		$encrypted = base64_decode($encrypted);
		
		//To Decrypt
		$decrypted = openssl_decrypt($encrypted, $encryptionMethod, $secretHash);
		
		return $decrypted;
	}
}