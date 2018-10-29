<?php
/**
 * Lead Track Model
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
class LeadTrack extends AppModel {
	public $name = 'LeadTrack';
	public $useTable = 'track_lead';
	
	
	public function heartBeat(){
		return $this->find('first', array('conditions'=>array('LeadTrack.id'=>3546)));
	}
	
}
?>