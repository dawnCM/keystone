<?php
/**
 * ReportTrack Model
 *
 * This model contains the data function for the reporting track/lead data from mongoDB.
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
class ReportTrack extends AppModel {
	public $primaryKey = '_id';
	public $name = 'ReportTrack';	
	public $useDbConfig = 'report';
}
?>