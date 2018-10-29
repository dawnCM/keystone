<?php
/**
 * Site Configuration Model
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
App::uses('AuthComponent', 'Controller/Component','Containable');
class SiteConfiguration extends AppModel {
	public $actsAs = array('AuditLog.Auditable');
	public $name = 'SiteConfiguration';
	public $useTable = 'site_configuration';
	public $belongsTo = array(
			'Site' => array(
					'className' => 'Site',
					'foreignKey' => 'site_id',
					'fields' => 'id,name,url'
			),
			'Ancillary' => array(
					'className' =>'Ancillary',
					'foreignKey' => 'ancillary_id',
					'fields' => array('name','type','url','triggeraction','status')
			)
	);

	public function siteConfigExist($site_id, $backend_action){
		
		$ancillaryConfig = $this->find('all', array( 'conditions' => array('SiteConfiguration.site_id' => $site_id, 'Ancillary.status'=>"1") ));
		
		if(!empty($ancillaryConfig)){
			//I need the array in the same format.  No numeric index when only one record.
			if(isset($ancillaryConfig['SiteConfiguration'])){
				$ancillaryConfig = array($ancillaryConfig);
			}
						
			foreach($ancillaryConfig  as $index=>$configuration){
			
				$config = $configuration['Ancillary'];
				$blocked = $configuration['SiteConfiguration']['blocked'];
				$ancillary_type = $config['type'];
				
				//Ancilliary record isn't a backend type
				if($ancillary_type != 'backend')continue;

				if(!empty($blocked)){ // not blank
					$blocked_array = json_decode($blocked, true);
					
					if(is_array($blocked_array)){
						$block_ancillary = false;
						foreach($blocked_array as $blocked_data){
							$aff = $blocked_data[0];
							$subs = $blocked_data[1];
							
							$subs = ((empty($subs) || $subs == "" || $subs == null || $subs == "null") ? false : $subs);
							
							if(!$subs){ // no subs so search for a match on affiliate only
							
								if($aff == $affiliate_id){
									
									$block_ancillary = true;
									continue;
								}
							}else if($aff == $affiliate_id){ //affilite id matach but subs are present
							
								$exploded_subs = explode(",", $subs);
								foreach($exploded_subs as $s_id){
									if(empty($s_id))continue;
									
									if($s_id == $sub_id){
										$block_ancillary = true;
										continue;	
									}
								}
							}
						}
												
						if($block_ancillary){
							//Next record	
							continue;
						}
					}	
				}
							
				$trigger = json_decode($config['triggeraction'], true);
				$backend_action_present = $trigger['backend'];
				$action_found = false;
				
				if($backend_action_present == $backend_action){
					$action_found = true;	
				}	
			}
			
			if($action_found){
				return true;
			}
			
			return false;
			
		}else{
			return false;
		}
		
		return false;
	}	
}
?>