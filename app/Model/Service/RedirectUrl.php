<?php
/**
 * RedirectUrl Model
 *
 * This model contains RedirectUrl methods and data.
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
class RedirectUrl extends AppModel
{
	public $actsAs = array('AuditLog.Auditable');
	public $name = 'RedirectUrl';
	public $useTable = 'redirect_urls';
	

	public $validate = array(
			'track_id' => array(
					'rule' => '/^[0-9]{1,11}$/',
					'message' => 'Must be an integer with a length between 1 and 11.',
					'required' => true
			),			
			'url' => array(
					'rule' => 'url',
					'required' => true,
					'message' => 'Must be a valid url.'
			)
	);
	
	/**
	 * Get external redirect url given the unique track id
	 * @param int $track_id
	 * @return string
	 */	
	public function getRedirect($track_id)
	{

		$track_array = $this->find('first', array('fields'=>array('RedirectUrl.url','RedirectUrl.id'), 'conditions'=>array('RedirectUrl.track_id'=>$track_id)));
		
		if (empty($track_array['RedirectUrl']['url']))
		{
			return false;
		} 
		else 
		{
			return array('url'=>$track_array['RedirectUrl']['url'], 'id'=> $track_array['RedirectUrl']['id']);
		}

		return false;
	}
	
	/**
	 * Store the external redirect url from the arbitration 
	 * with the unique track id. This method handles  
	 * dynamic urls, static urls and static urls with placeholders.
	 * This method returns the internal redirect page link to the external
	 * redirect.
	 * @param int $track_id
	 * @param string $ext_redirect
	 * @return string
	 */	
	public function setRedirect($track_id,$ext_redirect)
	{
		// Clean up backslashes
		$ext_redirect = str_replace('\\','',$ext_redirect);
		
		// Replace param placeholders in static redirects
		$ext_redirect = $this->__replaceParam($track_id,$ext_redirect);
		
		$data = array('track_id'=>$track_id,'url'=>$ext_redirect);
		
		$this->save(array('RedirectUrl'=>$data));
		
		return $ext_redirect;
	}
	
	/**
	 * Private method to replace the placeholders in static 
	 * redirects with the correct values.
	 * @param int $track_id
	 * @param string $redirect
	 * @return string
	 */
	private function __replaceParam($track_id,$redirect)
	{
		App::import('Model','Track');
		$track = new Track();
		
		$track_array = $track->find('first', array('fields'=>array('Track.affiliate_id', 'Track.request_id'), 'conditions'=>array('Track.id'=>$track_id)));
		$affiliate_id = $track_array['Track']['affiliate_id'];
		$request_id = $track_array['Track']['request_id'];
		
		// add placeholders maps
		$replace = array('{aid}'=>$affiliate_id,'{rid}'=>$request_id,'{tid}'=>$track_id);
		$redirect = strtr($redirect,$replace);
		
		return $redirect;
	}
	
}