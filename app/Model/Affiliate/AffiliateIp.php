<?php
/**
 * Affiliate IP Model
 *
 * This model contains the data function for the affiliate ip controller.
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
class AffiliateIp extends AppModel {
	public $name = 'AffiliateIp';
	public $useTable = 'affiliate_ip';
}