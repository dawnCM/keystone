<?php
/**
 * Track Model
 *
 * This model contains the data function for the track controller.
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
class Track extends AppModel {
	//public $actsAs = array('AuditLog.Auditable');
	public $name = 'Track';
	public $useTable = 'track';
	
	public function writeLead($track_id, $json) {
		App::import('model','LeadTrack');
		$lead_track = new LeadTrack();
		
		$this->data['LeadTrack']['track_id'] = $track_id;
		$this->data['LeadTrack']['json_vars'] = $json;
		
		if($lead_track->save($this->data)){
			return true;
		} else {
			return false;
		}
	}
}
?>