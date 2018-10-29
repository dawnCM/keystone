<?php
/**
 * Site Model
 *
 * This model contains the data function for the sites controller.
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
class Site extends AppModel {
	public $actsAs = array('AuditLog.Auditable','Containable');
	public $name = 'Site';
	public $useTable = 'sites';
	public $hasMany = array('SiteConfiguration' => array(
					'className' => 'SiteConfiguration',
					'fields' => array('id','site_id','ancillary_id','blocked','infinite_pop')
			));
	
	public $validate = array(
			'name' => array(
					'rule' => 'notEmpty',
					'message' => 'Site Name Field required.',
					'required' => true
			),
			'url' => array(
					'rule' => 'notEmpty',
					'message' => 'URL Field required',
					'allowEmpty' => FALSE,
					'required' => true
			),
				
	);
}
?>