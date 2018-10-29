<?php
/**
 * Application level Controller
 *
 * This file controls the building and access level for the left navigation menu.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     TBD
 * @link          TBD
 * @package       app.Controller
 * @since         keyStone v1.0
 * @license       TBD
 */
class NavigationController extends AppController {
	public function beforeFilter() {
		parent::beforeFilter ();
		$this->Auth->allow ( 'buildLeft');
	}
	
	/**
	 * Build the array of menu items and their meta data
	 * 
	 * @todo Apply access level dynamically from UI/DB.
	 * @return array
	 */
	public function buildLeft() {
		$this->autoRender = false;
		$menuItems = array ();
		
		// Add access restricted page
		$accessControl = array (
				'dashboard' => array (
						'parent' => array (
								'title' => 'Dashboard',
								'link' => '/dashboard',
								'icon' => 'fa-home',
								'active' => $this->__isActive ( '/dashboard' ),
								'access' => array (
										'Administrator',
										'Management',
										'Accounting',
										'Reporting',
										'Associate' 
								) 
						) 
				),
				'affiliates' => array (
						'parent' => array (
								'title' => 'Affiliates',
								'link' => '',
								'icon' => 'fa-sitemap',
								'active' => $this->__isActive ( 'affiliates', 'partial' ),
								'access' => array (
										'Administrator',
										'Management'
								)
						),
						'sub' => array (
								'list' => array (
										'access' => array (
												'Administrator',
												'Management'
										),
										'meta' => array (
												'link' => '/affiliates',
												'title' => 'Affiliate List',
												'active' => $this->__isActive ( '/affiliates' )
										)
								),
								'domains' => array (
										'access' => array (
												'Administrator',
												'Management'
										),
										'meta' => array (
												'link' => '/affiliates/domains',
												'title' => 'Api Access',
												'active' => $this->__isActive ( '/affiliates/domains' )
										)
								)
						)
				),
				'reports' => array (
						'parent' => array (
								'link' => '',
								'title' => 'Reports',
								'icon' => 'fa-edit',
								'active' => $this->__isActive ( 'reports', 'partial' ),
								'access' => array (
										'Administrator',
										'Management',
										'Accounting',
										'Reporting',
										'Associate' 
								) 
						),
						'sub' => array (
								'intake' => array (
										'access' => array (
												'Administrator',
												'Management',
												'Accounting',
												'Reporting',
												'Associate' 
										),
										'meta' => array (
												'link' => '/reports/leadintake',
												'title' => 'Lead Intake',
												'active' => $this->__isActive ( '/reports/leadintake' ) 
										) 
								),
								'saleintake' => array (
										'access' => array (
												'Administrator',
												'Management',
												'Accounting',
												'Reporting',
												'Associate'
										),
										'meta' => array (
												'link' => '/reports/saleintake',
												'title' => 'Sales Intake',
												'active' => $this->__isActive ( '/reports/saleintake' )
										)
								),
								'redirectrate' => array (
										'access' => array (
												'Administrator',
												'Management',
												'Accounting',
												'Reporting',
												'Associate' 
										),
										'meta' => array (
												'link' => '/reports/redirectrate',
												'title' => 'Redirect Rate',
												'active' => $this->__isActive ( '/reports/redirectrate' ) 
										) 
								),
								'buyerstatus' => array (
										'access' => array (
												'Administrator',
												'Management',
												'Accounting',
												'Reporting',
												'Associate'
										),
										'meta' => array (
												'link' => '/reports/buyerstatus',
												'title' => 'Buyer Status',
												'active' => $this->__isActive ( '/reports/buyerstatus' )
										)
								),
								'unsoldleads' => array (
										'access' => array (
												'Administrator',
												'Management',
												'Accounting',
												'Reporting',
												'Associate'
										),
										'meta' => array (
												'link' => '/reports/unsoldleads',
												'title' => 'Unsold Leads',
												'active' => $this->__isActive ( '/reports/unsoldleads' )
										)
								),
								'dataexport' => array (
										'access' => array (
												'Administrator',
												'Management',
												'Accounting',
												'Reporting',
												'Associate'
										),
										'meta' => array (
												'link' => '/reports/dataexport',
												'title' => 'Data Export',
												'active' => $this->__isActive ( '/reports/dataexport' )
										)
								),
								'themeperformance' => array (
										'access' => array (
												'Administrator',
												'Management',
												'Accounting',
												'Reporting',
												'Associate'
										),
										'meta' => array (
												'link' => '/reports/themeperformance',
												'title' => 'A/B Test Results',
												'active' => $this->__isActive ( '/reports/themeperformance' )
										)
								)
						) 
				),
				'billing' => array (
						'parent' => array (
								'link' => '',
								'title' => 'Billing',
								'icon' => 'fa-usd',
								'active' => $this->__isActive ( 'billing', 'partial' ),
								'access' => array (
										'Administrator',
										'Management',
										'Accounting',
										'Reporting',
								) 
						),
						'sub' => array (
								'report' => array (
										'access' => array (
												'Administrator',
												'Management',
												'Accounting',
												'Reporting',
										),
										'meta' => array (
												'link' => '/billing/buyerreport',
												'title' => 'Buyer Report',
												'active' => $this->__isActive ( '/billing/buyerreport' ) 
										) 
								),
								'group' => array (
										'access' => array (
												'Administrator',
												'Management',
												'Accounting',
												'Reporting',
										),
										'meta' => array (
												'link' => '/billing/buyergroup',
												'title' => 'Buyer Groups',
												'active' => $this->__isActive ( '/billing/buyergroup' )
										)
								),
								'adjustable' => array (
										'access' => array (
												'Administrator',
												'Management',
												'Accounting',
												'Reporting',
										),
										'meta' => array (
												'link' => '/billing/billingadjustable',
												'title' => 'Billing Adjustable',
												'active' => $this->__isActive ( '/billing/billingadjustable' )
										)
								)
						) 
				),
				'leads' => array (
						'parent' => array (
								'title' => 'Lead Management',
								'link' => '/leads',
								'icon' => 'fa-clipboard',
								'active' => $this->__isActive ( 'leads', 'partial' ),
								'access' => array (
										'Administrator',
										'Management',
										'Accounting',
										'Reporting',
										'Associate'
								)
						)
				),
				'buckets' => array (
						'parent' => array (
								'title' => 'Bucket Management',
								'link' => '/buckets',
								'icon' => 'fa-bitbucket',
								'active' => $this->__isActive ( '/buckets' ),
								'access' => array (
										'Administrator',
										'Management'
								)
						)
				),
				'listmanagement' => array (
						'parent' => array (
								'title' => 'List Management',
								'link' => '',
								'icon' => ' fa-envelope',
								'active' => $this->__isActive ( 'listManagement', 'partial' ),
								'access' => array (
										'Administrator',
										'Management',
										'Reporting',
								)
						),
						'sub' => array (
								'esp' => array (
										'access' => array (
												'Administrator',
												'Management',
												'Accounting',
												'Reporting',
												'Associate' 
										),
										'meta' => array (
												'link' => '/listManagement/esp',
												'title' => 'ESP Management',
												'active' => $this->__isActive ( '/listManagement/esp' ) 
										) 
								),
								'esplist' => array (
										'access' => array (
												'Administrator',
												'Management',
												'Accounting',
												'Reporting',
												'Associate' 
										),
										'meta' => array (
												'link' => '/listManagement/espList',
												'title' => 'ESP List',
												'active' => $this->__isActive ( '/listManagement/espList' ) 
										) 
								),
								'espoffer' => array (
										'access' => array (
												'Administrator',
												'Management',
												'Accounting',
												'Reporting',
												'Associate' 
										),
										'meta' => array (
												'link' => '/listManagement/espOffer',
												'title' => 'ESP Offer List',
												'active' => $this->__isActive ( '/listManagement/espOffer' ) 
										) 
								)        
						)
				),
				'site' => array (
						'parent' => array (
								'link' => '',
								'title' => 'Site Management',
								'icon' => 'fa-cogs',
								'active' => $this->__isActive ( 'sites', 'partial' ),
								'access' => array (
										'Administrator',
										'Management'
										
								) 
						),
						'sub' => array (
								'Websites' => array (
										'access' => array (
												'Administrator',
												'Management',
										),
										'meta' => array (
												'link' => '/sites',
												'title' => 'Websites',
												'active' => $this->__isActive ( '/sites' ) 
										) 
								),
								'Ancillary' => array (
										'access' => array (
												'Administrator',
												'Management',
										),
										'meta' => array (
												'link' => '/sites/ancillary',
												'title' => 'Ancillary',
												'active' => $this->__isActive ( '/sites/ancillary' )
										)
								),
								'Config' => array (
										'access' => array (
												'Administrator',
												'Management',
										),
										'meta' => array (
												'link' => '/sites/configuration',
												'title' => 'Site Configuration',
												'active' => $this->__isActive ( '/sites/configuration' )
										)
								),
						) 
				),
				'usermgt' => array (
						'parent' => array (
								'title' => 'User Management',
								'link' => '/users',
								'icon' => 'fa-users',
								'active' => $this->__isActive ( 'users', 'partial' ),
								'access' => array (
										'Administrator',
										'Management',
										'Reporting'
								)
						)
				),
				'pingtree' => array (
						'parent' => array (
								'title' => 'Pingtree Management',
								'link' => '/pingtree',
								'icon' => 'fa-list',
								'active' => $this->__isActive ( '/pingtree' ),
								'access' => array (
										'Administrator',
										'Management',
								) 
						) 
				),
				'fraud' => array (
						'parent' => array (
								'link' => '',
								'title' => 'Fraud Management',
								'icon' => 'fa-legal',
								'active' => $this->__isActive ( 'fraud', 'partial' ),
								'access' => array (
										'Administrator',
										'Management',
										'Accounting',
										'Reporting',
								)
						),
						'sub' => array (
								'Lead Time' => array (
										'access' => array (
												'Administrator',
												'Management',
												'Accounting',
												'Reporting',
										),
										'meta' => array (
												'link' => '/fraud/leadtime',
												'title' => 'Lead Time',
												'active' => $this->__isActive ( '/fraud/leadtime' )
										)
								),
								'IP Fraud' => array (
										'access' => array (
												'Administrator',
												'Management',
												'Accounting',
												'Reporting',
										),
										'meta' => array (
												'link' => '/fraud/ip',
												'title' => 'IP Fraud',
												'active' => $this->__isActive ( '/fraud/ip' )
										)
								),
								'Seed Contract' => array (
										'access' => array (
												'Administrator',
										),
										'meta' => array (
												'link' => '/fraud/seedcontract/',
												'title' => 'Seed Contract',
												'active' => $this->__isActive ( '/fraud/seedcontract' )
										)
								)
						)
				),
				'help' => array (
						'parent' => array (
								'title' => 'Help Desk',
								'link' => 'https://leadstudio.freshdesk.com',
								'icon' => 'fa-microphone',
								'active' => $this->__isActive ( '/help' ),
								'access' => array (
										'Administrator',
										'Management',
										'Reporting',
										'Associate',
										'Accounting'
								)
						)
				),
				'tickets' => array (
						'parent' => array (
								'title' => 'Ticket Desk',
								'link' => 'https://tickets.leadstudio.com/tickets/scp/',
								'icon' => 'fa-ticket',
								'active' => $this->__isActive ( '/tickets' ),
								'access' => array (
										'Administrator',
										'Management',
										'Reporting',
										'Associate',
										'Accounting'
								)
						)
				),
				'tools' => array (
						'parent' => array (
								'title' => 'Tools',
								'link' => '',
								'icon' => 'fa-wrench',
								'active' => $this->__isActive ( 'tools', 'partial' ),
								'access' => array (
										'Administrator'
										
								)
						),
						'sub' => array (
								'clonepingtreeview' => array (
													'access' => array (
															'Administrator',
													),
													'meta' => array (
															'link' => '/tools/clonepingtreeview/',
															'title' => 'Clone Pingtree',
															'active' => $this->__isActive ( '/tools/clonepingtreeview' )
													)
								)
						)
				),
		);
		
		// Get user group
		$userGroup = $this->Session->read ( 'Auth.User.Group.name' );
		
		// Build accessible menu item array
		foreach ( $accessControl as $control => $items ) {
			foreach ( $items as $base => $item ) {
				if ($base == 'parent') {
					if (in_array ( $userGroup, $items ['parent'] ['access'] )) {
						$menuItems [$control] ['parent'] = $items ['parent'];
					}
				} else {
					foreach ( $item as $item => $module ) {
						if (in_array ( $userGroup, $module ['access'] )) {
							$menuItems [$control] ['sub'] [] = array (
									'title' => $module ['meta'] ['title'],
									'link' => $module ['meta'] ['link'],
									'active' => $module ['meta'] ['active'] 
							);
						}
					}
				}
			}
		}
		
		/*
		 * // Static page example
		 * //'meta'=>array('link'=>'/widgets/factory/oa/tabbed','title'=>'Overnight Averages','icon'=>'icon-tasks','active'=>$this->__isActive('/oa','partial')),
		 * if($this->Session->read('Auth.User')){
		 * $menuItems['logout']=array('parent'=>array('title'=>'Logout','link'=>'/users/logout','icon'=>'isw-lock','active'=>$this->__isActive('/users/logout')));
		 * }else{
		 * $menuItems['login']=array('parent'=>array('title'=>'Login','link'=>'/users/login','icon'=>'isw-unlock','active'=>$this->__isActive('/users/login')));
		 * }
		 */
		return $menuItems;
	}
	
	private function __getOpenTicketCount(){
		$wsdl = Configure::read('OSTicket.wsdl');
		$osticket = new SoapClient($wsdl);
		
		// Set up the parameters
		$args = array(
				'username'      => $this->osTicket_Username,
				'password'      => $this->osTicket_Password,
				'origin'        => 'Web',
				'alertuser'     => true,
				'alertstaff'    => true,
				'ticketData'    => array(
						'name'      => utf8_encode($data['Name']),
						'email'     => utf8_encode($data['Email']),
						'subject'   => utf8_encode($data['Subject']),
						'message'   => utf8_encode($data['Message']),
						'topicId'   => $this->osTicket_topicId,
						'deptId'    => $this->osTicket_departmentId,
						'staffId'   => null,
						'dueDate'   => null,
						'time'      => null,
						'pri'       => 2,
						'phone'     => null,
				)
		);
		
		try {
			// Send the request and receive the ticketID
			$result = $osticket->__call('ostTicket.open',$args);
		}
		catch (SoapFault $e) {
			throw $e;
		}
		
		return $result;
	}

	
	/**
	 * Determines if the called path matches the url of the navigation item, if so, the active class is applied.
	 * 
	 * @param string $location        	
	 * @param string $match=exact
	 *        	(exact,partial)
	 * @return string
	 */
	private function __isActive($location, $match = 'exact') {
		$this->autoRender = false;
		switch (true) {
			case $match == 'exact' :
				return ($location == $_SERVER ['REQUEST_URI']) ? 'active' : '';
				break;
			
			case $match == 'partial' :
				$pos = strpos ( $_SERVER ['REQUEST_URI'], $location );
				return ($pos == true) ? 'active' : '';
				break;
			
			default :
				return '';
				break;
		}
	}
}