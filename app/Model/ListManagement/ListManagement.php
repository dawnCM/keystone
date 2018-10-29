<?php
/**
 * ListManagement Model
 *
 * $useDbConfig ties this model to using mongoDb.
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
class ListManagement extends AppModel {
	public $name = 'ListManagement';	
	public $useTable = 'list_management';
	
	public function send2List($data, $list_name, $list_id, $track_id){
		App::import('Model','Track');
		$track = new Track();
		$url = 'https://flatsixmedia.leadbyte.co.uk/api/submit.php';
		
		if(empty($data['Address2'])){
			$data['Address2'] = '';
		}
		
		$fields = array();
		$fields['email'] = $data['Email'];
		$fields['firstname'] = $data['FirstName'];
		$fields['lastname'] = $data['LastName'];
		$fields['street1'] = $data['Address1'];
		$fields['street2'] = $data['Address2'];
		$fields['towncity']	= $data['City'];
		$fields['county']	= $data['State'];
		$fields['postcode']	= $data['Zip'];
		$fields['ipaddress'] = $data['IPAddress'];
		$fields['source']	= $data['Url'];
		
		$fields['c1'] = 'C1';
		$fields['opt-in_date'] = date("d/m/Y H:i:s");
		$fields['sid'] = '02';
		$fields['campid'] = $list_name;
		$fields['soldp'] = $data['Price'];
				
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, $url );
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_POST,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$fields);
		$cr = curl_exec($ch);
		curl_close($ch);
		
		if(preg_match("/1 | OK/", $cr)){
			$msg = array();
			$msg['LIST_MANAGEMENT'][$list_id] = date('Y-m-d');
			$track_json = json_encode($msg);
			$track->writeLead($track_id, $track_json);
			
		}else{
			$msg = array();
			$msg = array('ERRORS' => array(601=>'Leadbyte Post Failure'));
			$track_json = json_encode($msg);
			$track->writeLead($track_id, $track_json);
		}
	
		
	}


	public function pullListOffer($offerid){
		
		$sql=	"SELECT a.list_id, b.name
				FROM list_management_offer a
				INNER JOIN list_management b ON a.list_id = b.id
				WHERE a.offer_id = '".$offerid."'
				LIMIT 1"; 	
				
		$records = $this->query($sql, false);
		
		if(!empty($records)){
			return array_merge($records[0]['a'],$records[0]['b']);
		}else{
			return array();
		}	
	}
}
?>