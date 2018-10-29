<?php
/**
 * Ip Model
 *
 * This model contains ip data.
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
App::uses('HttpSocket', 'Network/Http');

class Ip extends AppModel 
{
	public $actsAs = array('AuditLog.Auditable');
	public $name = 'Ip';
	public $useTable = 'ip';
	
	public $virtualFields = array(
			'ntoa_ip' => 'INET_NTOA(Ip.ip)'
	);
	
	public $validate = array(
		'ipactual' => array(
			'rule' => array('ip','IPv4'), // or 'IPv6' or 'both' (default)
			'message' => 'Please supply a valid IP address.'
		)
	);
	
	/**
	 * We first look for the ip address in our local database, if not found we request the ip data from our remote service.
	 * IP addresses are stored as integers and require ATON / NTOA conversion.
	 * 
	 * @todo Stop use of ip2long
	 * @todo Adjust return naming convention without using unset.
	 * @param string $ip
	 */
	public function getIp($ip) {
		$fields = array('Ip.id', 'Ip.ntoa_ip', 'Ip.hostname', 'Ip.city', 'Ip.region', 'Ip.country', 'Ip.org', 'Ip.blacklist');
		$data = $this->find('first', array('conditions' => array('Ip.ip' => sprintf("%u", ip2long($ip))), 'fields'=>$fields));
		
		if(count($data)>0){
			$data['Ip']['ip'] = $data['Ip']['ntoa_ip'];
			unset($data['Ip']['ntoa_ip']);

			return $data['Ip'];
		} else {
			return $this->pullIp($ip);
		}
	}
	
	/**
	 * Private function to get IP data given an ip address from a 3rd party service.
	 * @param string $ip_address
	 * @return array
	 */
	private function pullIp($ip) {
		$url = Configure::read('IpAPI.Url');
		$post_url = $url.$ip.'/json';
		
		$HttpSocket = new HttpSocket();
		$response = $HttpSocket->get($post_url);
		
		$ipdata = json_decode($response, true);
				
		if(!empty($ipdata['ip'])){
			$this->addIp($ipdata);
			$ipdata['id'] = $this->getInsertID();
			$ipdata['blacklist'] = 0;
			return $ipdata;
		}else{
			return $ipdata;
		}
	}
	
	/**
	 * Add the ip data to the local database.
	 * @param array $ipdata
	 * @return boolean
	 */
	public function addIp($ipdata){
		$this->set(array(
			'ip' 		=> DboSource::expression('INET_ATON("'.$ipdata['ip'].'")'),
			'hostname' 	=> $ipdata['hostname'],
			'city'		=> $ipdata['city'],
			'region'    => $ipdata['region'],
			'country'	=> $ipdata['country'],
			'org'		=> $ipdata['org'],
			'blacklist' => 0
		));
		
		$this->save();
		return true;
	}
	
	/**
	 * Check a given IP to see if it exists in our local database and is flagged as blacklisted.
	 * @param string $ip
	 * @return boolean
	 */
	public function isBlacklisted($ip){
		$fields = array('Ip.id', 'Ip.blacklist');
		$data = $this->find('first', array('conditions' => array('Ip.ip' => sprintf("%u", ip2long($ip)), 'Ip.blacklist'=>1), 'fields'=>$fields));
		
		if(count($data)>0){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * Look for the IP, if not found add it, then mark the IP as blacklisted.
	 * @param string $ip
	 * @return boolean
	 */
	public function blacklistIp($ip){
		$ipdata = $this->getIp($ip);
		$this->set(array('id'=>$ipdata['id'], 'blacklist'=>1));
		$this->save();
		return true;
	}
	
	/**
	 * Look for the IP, if not found add it, then mark the IP as whitelisted.
	 * @param string $ip
	 * @return boolean
	 */
	public function whitelistIp($ip){
		$ipdata = $this->getIp($ip);
		$this->set(array('id'=>$ipdata['id'], 'blacklist'=>0));
		$this->save();
		return true;
	}
	
	
}