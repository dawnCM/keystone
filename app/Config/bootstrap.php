<?php
/**
 * This file is loaded automatically by the app/webroot/index.php file after core.php
 *
 * This file should load/create any application wide configuration settings, such as
 * Caching, Logging, loading additional configuration files.
 *
 * You should also use this file to include any files that provide global functions/constants
 * that your application uses.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.10.8.2117
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

//Set Timezones
date_default_timezone_set('America/New_York');
ini_set('date.timezone', 'America/New_York');

// Setup a 'default' cache configuration for use in the application.
//Cache::config('default', array('engine' => 'File'));
//Cache::config('default', array('engine' => 'Xcache'));

// 1-hour
/*Cache::config('default', array(
'engine' 	=> 'Memcache',
'duration' 	=> 3600,
'servers' 	=> array('127.0.0.1:11211')));

// 5-min
Cache::config ('5m', array (
'engine' 	=> 'Memcache',
'duration' 	=> 300,
'servers' 	=> array ('127.0.0.1:11211')));

// 15-min
Cache::config ('15m', array (
'engine' 	=> 'Memcache',
'duration' 	=> 900,
'servers' 	=> array ('127.0.0.1:11211')));

//24-hours
Cache::config('24h', array(
'engine'	=> 'Memcache',
'duration'	=> 86400,
'servers'	=> array('127.0.0.1:11211')));

//1-week
Cache::config('1w', array(
'engine'        => 'Memcache',
'duration'      => 604800,
'servers'       => array('127.0.0.1:11211')));
*/
/**
 * The settings below can be used to set additional paths to models, views and controllers.
 *
 * App::build(array(
 *     'Model'                     => array('/path/to/models/', '/next/path/to/models/'),
 *     'Model/Behavior'            => array('/path/to/behaviors/', '/next/path/to/behaviors/'),
 *     'Model/Datasource'          => array('/path/to/datasources/', '/next/path/to/datasources/'),
 *     'Model/Datasource/Database' => array('/path/to/databases/', '/next/path/to/database/'),
 *     'Model/Datasource/Session'  => array('/path/to/sessions/', '/next/path/to/sessions/'),
 *     'Controller'                => array('/path/to/controllers/', '/next/path/to/controllers/'),
 *     'Controller/Component'      => array('/path/to/components/', '/next/path/to/components/'),
 *     'Controller/Component/Auth' => array('/path/to/auths/', '/next/path/to/auths/'),
 *     'Controller/Component/Acl'  => array('/path/to/acls/', '/next/path/to/acls/'),
 *     'View'                      => array('/path/to/views/', '/next/path/to/views/'),
 *     'View/Helper'               => array('/path/to/helpers/', '/next/path/to/helpers/'),
 *     'Console'                   => array('/path/to/consoles/', '/next/path/to/consoles/'),
 *     'Console/Command'           => array('/path/to/commands/', '/next/path/to/commands/'),
 *     'Console/Command/Task'      => array('/path/to/tasks/', '/next/path/to/tasks/'),
 *     'Lib'                       => array('/path/to/libs/', '/next/path/to/libs/'),
 *     'Locale'                    => array('/path/to/locales/', '/next/path/to/locales/'),
 *     'Vendor'                    => array('/path/to/vendors/', '/next/path/to/vendors/'),
 *     'Plugin'                    => array('/path/to/plugins/', '/next/path/to/plugins/'),
 * ));
 *
 */

/**
 * Custom Inflector rules can be set to correctly pluralize or singularize table, model, controller names or whatever other
 * string is passed to the inflection functions
 *
 * Inflector::rules('singular', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 * Inflector::rules('plural', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 *
 */

/**
 * Plugins need to be loaded manually, you can either load them one by one or all of them in a single call
 * Uncomment one of the lines below, as you need. Make sure you read the documentation on CakePlugin to use more
 * advanced ways of loading plugins
 *
 */
//CakePlugin::loadAll(); // Loads all plugins at once
CakePlugin::load('AuditLog');
CakePlugin::load('Mongodb');
//CakePlugin::load('DebugKit'); //Loads a single plugin named DebugKit


/**
 * To prefer app translation over plugin translation, you can set
 *
 * Configure::write('I18n.preferApp', true);
 */

/**
 * You can attach event listeners to the request lifecycle as Dispatcher Filter. By default CakePHP bundles two filters:
 *
 * - AssetDispatcher filter will serve your asset files (css, images, js, etc) from your themes and plugins
 * - CacheDispatcher filter will read the Cache.check configure variable and try to serve cached content generated from controllers
 *
 * Feel free to remove or add filters as you see fit for your application. A few examples:
 *
 * Configure::write('Dispatcher.filters', array(
 *		'MyCacheFilter', //  will use MyCacheFilter class from the Routing/Filter package in your app.
 *		'MyCacheFilter' => array('prefix' => 'my_cache_'), //  will use MyCacheFilter class from the Routing/Filter package in your app with settings array.
 *		'MyPlugin.MyFilter', // will use MyFilter class from the Routing/Filter package in MyPlugin plugin.
 *		array('callable' => $aFunction, 'on' => 'before', 'priority' => 9), // A valid PHP callback type to be called on beforeDispatch
 *		array('callable' => $anotherMethod, 'on' => 'after'), // A valid PHP callback type to be called on afterDispatch
 *
 * ));
 */
Configure::write('Dispatcher.filters', array(
	'AssetDispatcher',
	'CacheDispatcher'
));

/**
 * Application specific configuration
 */
Configure::write('Global.Domain','https://keystone.leadstudio.com');
Configure::write('Global.Email','nick@leadstudio.com');
Configure::write('Global.Subdomain',substr( env("HTTP_HOST"), 0, strpos(env("HTTP_HOST"), ".") ));
Configure::write('Global.EncryptionSalt','*+&YEIUEKHDhfhe22$');
Configure::write('Global.EncryptionPass','Life=42');

//User groups
Configure::write('Group.Administrator',"1");
Configure::write('Group.Management',"2");
Configure::write('Group.Accounting',"3");
Configure::write('Group.Reporting',"4");
Configure::write('Group.Associate',"5");

//Upload
Configure::write('Upload.path',$_SERVER['DOCUMENT_ROOT'] . "/app/webroot/files");

//Ajax
Configure::write('Ajax.nonce',md5(date('l')));

//CakeMarketing API
Configure::write('CakeM.ApiKey','k8sa0o6VjwPimwKAYS34XaPjfQEWPc');
Configure::write('CakeM.Url','http://leadstudioportal.com/api');
Configure::write('CakeM.UrlPost','http://leadstudiotrack.com');
Configure::write('CakeM.UrlClickPixel','https://leadstudiotrack.com');

//Email Validation API
Configure::write('EmailAPI.DataValidation.Url','https://api.datavalidation.com/1.0/rt/');
Configure::write('EmailAPI.DataValidation.ApiKey', 'a11b546d04461353c3c635f17e0fd8e2');
Configure::write('EmailAPI.BrightVerify.Url','https://bpi.briteverify.com/emails.json');
Configure::write('EmailAPI.BrightVerify.ApiKey', 'key');
//https://bpi.briteverify.com/emails.json?address=johndoe@briteverify.com&apikey=<your-api-key>

//IP Validation API
Configure::write('IpAPI.Url','http://ipinfo.io/');
Configure::write('IpAPI.ApiKey','');

//OSTicket
Configure::write('OSTicket.wsdl', 'https://tickets.leadstudio.com/tickets/api/soap/index.php?wsdl');

//Google Api
Configure::write('Google.Maps.ApiKey', 'AIzaSyCtVn9bSLeFtjlbDQ_nGaJ0x6HTzMglK04'); //app server ip

//Blacklisted IPs
Configure::write('IPBlacklist.list','216.157.9.124,216.157.9.125,184.106.45.104,184.106.45.105,184.106.45.106,184.106.45.107,41.71.207.7,199.231.214.130,99.42.35.15,200.32.198.146,12.68.228.114,41.138.187.78,173.171.133.65,116.203.78.51,49.196.4.20,49.196.7.238,189.219.104.124,170.75.162.151,173.209.211.153');

//Guaranteed Buyer Credentials
Configure::write( 'GuaranteedBuyer.Info', array('Affiliate' => '322', 'CampaignId' => '1009', 'OfferId' => '119', 'CreativeId' => '983'));

//Internal Sites Config
Configure::write( 'SitesAPI.Sites',	array(	'16'	=>	array('Name' => 'Accesspointlending', 'Url' => 'https://accesspointlending.com', 'Pages' => array('Landing Page'=>'lead_data.zip','Personal Info Page'=>'lead_data.firstname','Verify Identity Page'=>'lead_data.dateofbirth','Employment Info Page'=>'lead_data.employername','Co-Applicant Page'=>'lead_data.cofirstname','Deposit Cash Page'=>'lead_data.directdeposit','Finalize Page'=>'lead_data.receivableamount')),
											'39'	=>	array('Name' => 'Chesapeakeloanservices', 'Url' => 'https://chesapeakeloanservices.com', 'Pages' => array('Landing Page'=>'lead_data.zip','Personal Info Page'=>'lead_data.firstname','Verify Identity Page'=>'lead_data.dateofbirth','Employment Info Page'=>'lead_data.employername','Co-Applicant Page'=>'cofirstname','Deposit Cash Page'=>'lead_data.directdeposit','Finalize Page'=>'lead_data.receivableamount')),
											'35'	=>	array('Name' => 'Fccrloans', 'Url' => 'https://fccrloans.com', 'Pages' => array('Landing Page'=>'lead_data.zip','Personal Info Page'=>'lead_data.firstname','Verify Identity Page'=>'lead_data.dateofbirth','Employment Info Page'=>'lead_data.employername','Co-Applicant Page'=>'cofirstname','Deposit Cash Page'=>'lead_data.directdeposit','Finalize Page'=>'lead_data.receivableamount')),
											'37'	=>	array('Name' => 'Libertylendingexchange', 'Url' => 'https://libertylendingexchange.com', 'Pages' => array('Landing Page'=>'lead_data.zip','Personal Info Page'=>'lead_data.firstname','Verify Identity Page'=>'lead_data.dateofbirth','Employment Info Page'=>'lead_data.employername','Co-Applicant Page'=>'cofirstname','Deposit Cash Page'=>'lead_data.directdeposit','Finalize Page'=>'lead_data.receivableamount')),
											'50'	=>	array('Name' => 'Longertermloans', 'Url' => 'https://longertermloans.com', 'Pages' => array('Landing Page'=>'lead_data.zip','Personal Info Page'=>'lead_data.firstname','Verify Identity Page'=>'lead_data.dateofbirth','Employment Info Page'=>'lead_data.employername','Co-Applicant Page'=>'cofirstname','Deposit Cash Page'=>'lead_data.directdeposit','Finalize Page'=>'lead_data.receivableamount')),
											'43'	=>	array('Name' => 'Peerkeyloan', 'Url' => 'https://peerkeyloan.com', 'Pages' => array('Landing Page'=>'lead_data.zip','Personal Info Page'=>'lead_data.firstname','Verify Identity Page'=>'lead_data.dateofbirth','Employment Info Page'=>'lead_data.employername','Co-Applicant Page'=>'cofirstname','Deposit Cash Page'=>'lead_data.directdeposit','Finalize Page'=>'lead_data.receivableamount')),
											'49'	=>	array('Name' => 'Securaloan', 'Url' => 'https://securaloan.com', 'Pages' => array('Landing Page'=>'lead_data.zip','Personal Info Page'=>'lead_data.firstname','Verify Identity Page'=>'lead_data.dateofbirth','Employment Info Page'=>'lead_data.employername','Co-Applicant Page'=>'cofirstname','Deposit Cash Page'=>'lead_data.directdeposit','Finalize Page'=>'lead_data.receivableamount')),
											'36'	=>	array('Name' => 'Winshiplending', 'Url' => 'https://winshiplending.com', 'Pages' => array('Landing Page'=>'lead_data.zip','Personal Info Page'=>'lead_data.firstname','Verify Identity Page'=>'lead_data.dateofbirth','Employment Info Page'=>'lead_data.employername','Co-Applicant Page'=>'cofirstname','Deposit Cash Page'=>'lead_data.directdeposit','Finalize Page'=>'lead_data.receivableamount'))
									)
);

//Internal Sites Whitelist Ip 
Configure::write('IPWhitelist.list', array('50.251.135.237'));


/**
 * Configures default file logging options
 */
App::uses('CakeLog', 'Log');
CakeLog::config('debug', array(
	'engine' => 'File',
	'types' => array('notice', 'info', 'debug'),
	'file' => 'debug',
));

CakeLog::config('error', array(
	'engine' => 'File',
	'types' => array('warning', 'error', 'critical', 'alert', 'emergency'),
	'file' => 'error',
));

//Locations of sub-directory models
App::build(array(
		'Model'	=>	array( 	$_SERVER['DOCUMENT_ROOT'] . '/app/Model/Api/',
							$_SERVER['DOCUMENT_ROOT'] . '/app/Model/Bucket/',
							$_SERVER['DOCUMENT_ROOT'] . '/app/Model/Track/',
							$_SERVER['DOCUMENT_ROOT'] . '/app/Model/Receivable/',
							$_SERVER['DOCUMENT_ROOT'] . '/app/Model/Api/ExternalPost/',
							$_SERVER['DOCUMENT_ROOT'] . '/app/Model/Service/',
							$_SERVER['DOCUMENT_ROOT'] . '/app/Model/Affiliate/',
							$_SERVER['DOCUMENT_ROOT'] . '/app/Model/ListManagement/',
							$_SERVER['DOCUMENT_ROOT'] . '/app/Model/SiteManagement/',
							$_SERVER['DOCUMENT_ROOT'] . '/app/Model/Api/Buyer/'
						)
	)
);
