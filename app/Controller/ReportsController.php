<?php
/**
 * Report Controller
 *
 * This file handles the reporting for the application, both mysql and mongo are used. 
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     TBD
 * @link          https://github.com/AdLink360/keyStone/wiki/ReportsController
 * @package       app.Controller
 * @since         keyStone v1.0
 * @license       TBD
 */

class ReportsController extends AppController {
	public $uses = array('LeadTrack', 'Track', 'ReportTrack', 'Cake', 'Affiliate', 'Bucket', 'Contract', 'Buyer', 'Api');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->autoRedirect = false;
		$this->__isAuthorized($this->Session->read('Auth.User'));
		$this->set('loadReportsJS',true);
	}
			
	public function receivables(){
		$this->layout = 'dashboard';
	}
	
	public function tester123(){
		$this->layout = 'dashboard';
		$offerid = '34';
		$campaignid = '1879';
		$affid = '211';
		$creativeid = '85';
		$response = $this->Cake->exportconversion($offerid, $campaignid, $affid, $creativeid);
		echo "<pre>";
		print_r($response);
		exit;
	}
	
	public function leadintake(){
		$this->layout = 'dashboard';
		
		//Retrieve a list of affiliates
		$params = array('order'=>array('Affiliate.affiliate_name'));
		$this->set('affiliate_list', $this->Affiliate->find('all',$params));
		
		//Retrieve a list of offers, list is cached for 1 hour.
		$cache['hash'] = md5('offerlist');
		$cache['value'] = false;
		$cache['value'] = Cache::read($cache['hash']);
		
		if($cache['value'] === false){
			$offerObj = $this->Cake->exportoffer();
			foreach($offerObj['data']['offers'] AS $offers){
				foreach($offers as $offer){
					$cache['value'][$offer['offer_name']] = $offer['offer_id'];
				}
			}
			ksort($cache['value']);
			Cache::write($cache['hash'], $cache['value']);
		}
		$this->set('offer_list',$cache['value']);
		unset($cache);
	}
	
	/**
	 *  Data export main page
	 */
	public function dataexport(){
		$this->layout = 'dashboard';
	
		//Retrieve a list of affiliates
		$this->Affiliate->contain();
		$params = array('order'=>array('Affiliate.affiliate_name'));
		$this->set('affiliate_list', $this->Affiliate->find('all',$params));
		
		//Set mongo field list
		$monogo_fields = array(	"track_id","request_id","lead_id","affiliate_id","offer_id","campaign_id","calltype", "template","theme","mobile","sub_id","sub_id2","ipaddress","creditrating","zip","military","monthlynetincome","agree",
								"apptype","state","loanamount","firstname","lastname","email","address1","address2","city","residencetype","residentsincedate","homephone","cellphone",
								"dateofbirth","driverslicensenumber","driverslicensestate","primaryphone","phonetype","secondaryphone","employerstate","paydate2",
								"employeetype","employername","employmenttime","workphone","employeraddress","employerzip","employercity","payfrequency","paydate1",
								"bankaccounttype","bankname","banktime","directdeposit","agreeconsent","agreephone","paymenttype","receivableamount","paidamount","margin","marginamount");
		$this->set('mongo_field_list', $monogo_fields);
		
		$app_types = array( array('payday','site','Payday'),
							array('personalloan','site','Personal Loan'),
							array('vendor_installment','posted','Vendor Installment'),
							array('vendor_payday','posted','Vendor Payday'),
							array('vendor_personalloan','posted','Vendor Personal Loan'),
							array('vendor_lowtierlong','posted','Vendor Low Tier Long'),
							array('vendor_lowtier','posted','Vendor Low Tier'),
							array('vendor_ppc_payday','posted','Vendor PPC Payday'),
							array('vendor_ppc_personalloan','posted','Vendor PPC Personal Loan'),
							array('vendor_ppc_installment','posted','Vendor PPC Installment'),
							array('gb','guaranteed','Guaranteed Buyer'),
		
		);
		$this->set('apptype_list', $app_types);
		
	}
	
	public function themeperformance(){
		$this->layout = 'dashboard';
		$sites = Configure::read('SitesAPI.Sites');
		$site_list = array();
		foreach($sites as $id=>$arr){
			$site_list[$id] = $arr['Name'];
		}
		$this->set('site_list', $site_list);
	}
	
	public function getThemeData($site, $start, $end){
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		$sites = Configure::read('SitesAPI.Sites');
	
		$mongo = $this->ReportTrack->getDataSource();
		$mongoCollectionObject = $mongo->getMongoCollection($this->ReportTrack);
		$start = new MongoDB\BSON\UTCDateTime(strtotime($start.' 00:00:00'));
		$end = new MongoDB\BSON\UTCDateTime(strtotime($end.' 23:59:59'));
	
		$ops_table = array(
				array( //equivalent to mysql where conditions
						'$match' => array(
								'lead_created' => array('$gt'=> $start, '$lt' => $end),
								'offer_id' => array('$eq'=>$site)
						)
				),
				array( //group by theme and aggregate data as defined
						'$group' => array(
								"_id"		=>	'$lead_data.theme', //group by
								'theme'		=>	array('$first'=>'$lead_data.theme'), //keep the theme name for each group block as identifier
								'clicks'	=>	array('$sum'=>1),
								'leads'	 	=> 	array('$sum'=>
										array('$cond'=>
												array('if'=>'$lead_id'    ,'then'=>1, 'else'=>0) //add 1 if field has value, add 0 if not
										)
								),
								'sold' 	=>	array('$sum'=>
										array('$cond'=>
												array('if'=>'$lead_data.receivableamount'    ,'then'=>1, 'else'=>0) //add 1 if field has value, add 0 if not
										)
								),
								'revenue'=> array('$sum'=>
										array('$cond'=>
												array('if'=>'$lead_data.receivableamount' ,'then'=>'$lead_data.receivableamount', 'else'=>0) //add amount if field has value, add 0 if not
										)
								)
						),
				),
				array(
						//Define fields to show or not show. Needed to create fields and define display when different from $group
						'$project' => 	array('_id'=>0,'theme'=>1,'clicks' => 1, 'leads'=>1, 'sold'=>1, 'revenue'=>1, 'epl'=> //$epl - Created and calculated field after group fields are calculated
								array('$cond'=>
										array('if'=>'$leads' , 'then'=> //$leads not equal to 0, perfrom divison.  If divisor($leads) is 0 it will cause a php fatal error
												array('$divide'=>
														array('$revenue','$leads')),  'else'=>0 )
											
								)
						)
				)
	
		);
	
		//Start Table Data
		$table_array = $mongoCollectionObject->aggregate($ops_table);
		$table_data = array();
		if(count($table_array['result']) > 0){
	
			//Format Decimals
			foreach($table_array['result'] as $k=>$v){
				$table_array['result'][$k]['revenue'] = $this->Api->formatDecimal($v['revenue']);
				$table_array['result'][$k]['epl'] = $this->Api->formatDecimal($v['epl']);
			}
			$table_data = $table_array['result'];
		}
		//End Table Data
	
		//Start Chart Data
		$site_config_item = $sites[$site]; // Site Config
		$chart_data = array();//return array for charts
	
		$mongo_group = array();
		$mongo_group['$group']['_id'] = '$lead_data.theme'; //group by
		$mongo_group['$group']['Theme'] = array('$first'=>'$lead_data.theme'); //keep the theme name for each group block as identifier
	
		$mongo_project = array();
		$mongo_project['$project']['_id'] = 0; //Remove field from results
		$mongo_project['$project']['Theme'] = 1; //Add Theme name as identifier of group results
	
	
	
		//Build Mongo Group Config
		foreach($site_config_item['Pages'] as $page=>$submit_field){
			//Each page will return the number of users that submitted that page
			$mongo_group['$group'][$page] = array('$sum'=>
					array('$cond'=>
							array('if'=>'$'.$submit_field.'' ,'then'=>1, 'else'=>0) //add 1 if field has value, add 0 if not
					)
			);
			//Set a flag in project so mongo will know to display field in group sets
			$mongo_project['$project'][$page] = 1;
				
			//Information needed in json response
			$chart_data['pages'][] = $page;
		}
		//End Mongo Group Build
	
	
		$ops_chart = array(
				array( //equivalent to mysql where conditions
						'$match' => array(
								'lead_created' => array('$gt'=> $start, '$lt' => $end),
								'offer_id' => array('$eq'=>$site)
						)
				),
				$mongo_group, //group by theme and aggregate data as defined
				$mongo_project //Define fields to show or not show. Needed to create fields and define display when different from $group
		);
	
		$chart_array = $mongoCollectionObject->aggregate($ops_chart);
	
		if(count($chart_array['result']) > 0){
				
			$y_axis_max = 0;
			foreach($chart_array['result'] as $i=>$array){
				$theme_name = $array['Theme'];
				$temp_submit_holder = array();//Reset array for every loop
				$temp_submit_holder['name'] = ((empty($theme_name))?'Undefined' : $theme_name);
				//Loop through pages. Get page submits for every theme. set $y_axis_max to greatest page submit quantity.
				for($index=0; count($chart_data['pages']) > $index; $index++){
					$page_name = $chart_data['pages'][$index];
					$page_submits = $array[$page_name];
						
					//Page submits for every page
					$temp_submit_holder['data'][] = $page_submits;
						
					//Need the greatest number of submissions at end of looping
					if($page_submits > $y_axis_max)$y_axis_max=$page_submits;
	
						
				}
	
				//Add to chart data
				$chart_data['chart_series'][] = $temp_submit_holder;
	
			}
				
			$chart_data['y_axis_max'] = $y_axis_max+10;
			$chart_data['items'] = 'true';
		}else{
			$chart_data = array('items'=>'false');
		}
	
	
	
		$response = array('status'=>'success','table_data'=>$table_data,'chart_data'=>$chart_data);
	
		return json_encode($response);
	}

	public function unsoldleadofferdropdown($affiliate_id){
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		$offer_list = array();
		if($affiliate_id == 0){ //Get all offers
			$offers = $this->Cake->exportoffer();
			
			foreach($offers['data']['offers']['offer'] as $index=>$arr){
				$offer_list[$arr['offer_id']] = $arr['offer_name'];	
			}
			
			asort($offer_list);	
		}else{
			$params1 = array('conditions'=>array('Affiliate.id'=>$affiliate_id),
						'fields'=>array('Affiliate.remote_id')
			);
			$remote_affiliate = $this->Affiliate->find('first', $params1);
			$remote_affiliate_id = $remote_affiliate['Affiliate']['remote_id'];
			
			
			$campaigns =  $this->Cake->exportcampaign(0, 0, $remote_affiliate_id);	
			
			if($campaigns['status'] == "success"){
				if($campaigns['data']['row_count'] == 1){
					$campaign_loop = array($campaigns['data']['campaigns']['campaign']);
				}else{
					$campaign_loop = $campaigns['data']['campaigns']['campaign'];
				}

				foreach($campaign_loop as $index=>$arr){
					$offer_list[$arr['offer']['offer_id']] = $arr['offer']['offer_name'];	
				}
			}
			
		}
		asort($offer_list);
		return json_encode($offer_list);

	}

	public function unsoldleads(){
		$this->layout = 'dashboard';
		
		$affiliates = $this->Affiliate->find('all');
		$affiliate_list = array();
		foreach($affiliates as $index=>$arr){
			$affiliate_list[$arr['Affiliate']['id']] = $arr['Affiliate']['affiliate_name'];	
		}
		
		$this->set('affiliate_list', $affiliate_list);
		
		
		
		$offers = $this->Cake->exportoffer();
		$offer_list = array();
		foreach($offers['data']['offers']['offer'] as $index=>$arr){
			$offer_list[$arr['offer_id']] = $arr['offer_name'];	
		}
		
		asort($offer_list);
		$this->set('offer_list', $offer_list);
		
		
	}
	
	public function getunsoldtotals(){
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		$return = array('status'=>'failure');
		
		$explode_start_date = explode("/", $this->request->data['start_date']);
		$explode_end_date = explode("/", $this->request->data['end_date']);
		
		$start_date = $explode_start_date[2]."-".$explode_start_date[0]."-".$explode_start_date[1]." 00:00:00";
		$end_date = $explode_end_date[2]."-".$explode_end_date[0]."-".$explode_end_date[1]." 00:00:00";
		
		
		
		$start = new MongoDB\BSON\UTCDateTime(strtotime($start_date));
		$end = new MongoDB\BSON\UTCDateTime(strtotime($end_date));
		
		$mongo = $this->ReportTrack->getDataSource();
		//2 minutes
		$mongo->setTimeout(120000);
		$mongoCollectionObject = $mongo->getMongoCollection($this->ReportTrack);
		
		$ops = array();
		$ops = array(
						
					    array( //equivalent to mysql where conditions
							'$match' => array( 
								'lead_created' => array('$gte'=> $start, '$lt' => $end),
								//'offer_id' => $offer_id,
								//'affiliate_id'=>"126",
								//'disposition.approved'=>array('$exists'=>true),
								//'disposition'=>array('$exists'=>true),
							)
						),
						
						/*array( //group by theme and aggregate data as defined
								'$group' => array(
										//"_id"		=>	'$lead_data.theme', //group by
										"_id"		=>	array('$substr'=> array('$lead_created', 0, 10)),
										'type'		=>	array('$first'=>'$lead_data.calltype'), //keep the calltype name for each group block as identifier
										'total'	=>	array('$sum'=>1),
										'duplicates' => array('$sum'=>
												array('$cond'=>
														array('if'=>'$lead_data.errors.390'    ,'then'=>1, 'else'=>0) //add 1 if field has value, add 0 if not
												)
										),
										'ipfraud' => array('$sum'=>
												array('$cond'=>
														array('if'=>'$lead_data.errors.400'    ,'then'=>1, 'else'=>0) //add 1 if field has value, add 0 if not
												)
										),
										'incomplete'	 	=> 	array('$sum'=>
												array('$cond'=>
														array('if'=>array('$and'=> array( //Two conditions have to be true
																					//first condition
																					array('$not'=>//bank name not present makes condition true
																					 		array('$lead_data.bankname')// is bankname true
																					), 
																					//second condtion
																					array('$eq'=>array('$lead_data.calltype','internal'))//calltype equals internal
																									
																	    ))       ,'then'=>1, 'else'=>0) //add 1 if field has value, add 0 if not
												)
										),
										'no_buyers' 	=>	array('$sum'=>
												array('$cond'=>
														array(
																'if'=> array('$or'=>array( //Or condtion where two cases can satisfy a true condtion. Only only one OR condition has to be true for true outcome
																				//first or condtion
																				array('$and'=> //all conditions must be true to satisfy as true for OR condtion
																							array(array('$eq'=>	array('$lead_data.calltype','internal')),
																							'$lead_data.bankname', 
																							array('$not'=> //disposition is missing to be true
																										array('$disposition.approved')
																							),
																							array('$not'=> //disposition is missing to be true
																										array('$disposition.success')
																							)           
																	   			)),
																	    		//second or condition
																	   			array('$and'=> //all conditions must be true to satisfy as true for OR condtion
																							array(array('$eq'=>array('$lead_data.calltype','external')), 
																							array('$not'=> //disposition.approved is not set makes condition true
																										array('$disposition.approved')
																							),
																							array('$not'=> //disposition.success is not set makes condition true
																										array('$disposition.success')
																							),
																							array('$not'=> //Don't count duplicate leads as no buyer
																										array('$lead_data.errors.390')
																							),
																							array('$not'=> //Don't count Ip fraud leads as no buyer
																										array('$lead_data.errors.400')
																							)             
																	    		))   
																		))   
																
																
														,'then'=>1, 'else'=>0) //add 1 if field has value, add 0 if not
												)
										),
										'sales'=> array('$sum'=>
												array('$cond'=>
														array('if'=>array('$or'=>array( //Or condtion where two cases can satisfy a true condtion. Only only one OR condition has to be true for true outcome
																		
																							array('$ifNull'=>array('$disposition.approved',0)), //condition 1
																							array('$ifNull'=>array('$disposition.success',0)) //condition 2
																			  
																		))
														
														
														
													
														
														
														,'then'=>1, 'else'=>0) //add amount if field has value, add 0 if not
												)
										),
									
									
										
								),
						),
						array(
							'$project' => array(
													'_id'=>1,'total' => 1, 'incomplete' => 1, 'no_buyers'=> 1, 'sales'=>1, 'type'=>1, 'duplicates'=>1, 'ipfraud'=>1,
													'total_unsold'=>array('$add'=>array('$incomplete','$no_buyers','$duplicates','$ipfraud')),
													/*
													 * 1. subtract sales from total leads
													 * 2. result of 1 and divide by total leads
													 * 3. result of 2 and multiply by 100
													 * 4. sets unsold percentage
													 */
												//	'unsold_percentage' =>array('$multiply'=>array( array('$divide'=>array(  array('$subtract'=>array('$total', '$sales'   )),  '$total'  )), 100)),
											//		'submitted'=>array('$subtract'=>array('$total', '$incomplete' )),
											//		'unsold_submitted_percentage' =>array('$subtract'=>array(100.00, array('$multiply'=>array( array('$divide'=>array( '$sales',  array('$subtract'=>array('$total', '$incomplete'   )) )), 100))))
						//	),
							
					//	),
						
					array('$sort'=>array('lead_created'=>1)), //sort by _id field of project.  Date ASC
					array('$project' => array('lead_created' => 1,'disposition.approved' => 1,'disposition.success' => 1, 'lead_data.errors' => 1, 'lead_data.calltype' => 1, 'lead_data.bankname'=>1 ))
						
										
		);
		
		if(isset($this->request->data['offer_id'])){
			$ops[0]['$match']['offer_id'] = $this->request->data['offer_id'];
		}
		
		
		if(isset($this->request->data['affiliate_id'])){
			$params1 = array('conditions'=>array('Affiliate.id'=>$this->request->data['affiliate_id']),
						'fields'=>array('Affiliate.remote_id')
			);
			$remote_affiliate = $this->Affiliate->find('first', $params1);
			$remote_affiliate_id = $remote_affiliate['Affiliate']['remote_id'];
			$ops[0]['$match']['affiliate_id'] = $remote_affiliate_id;
		}
		
		$results = array();
		
		try{
			$results = $mongoCollectionObject->aggregate($ops);	
		}catch(Exception $e){
			//print_r($e->getMessage());	
		}
	
	
		if(count($results['result']) > 0){
			$return['status'] = "success";
			$data = array();
			$total_leads = 0;
			
			$call_type = $results['result'][0]['lead_data']['calltype'];
			$totals = array();
			
			
			foreach($results['result'] as $index=>$arr){
				$date = date("Y-m-d",$arr['lead_created']->sec);
				$total_leads++;
				$duplicates = 0;
				$ip_fraud = 0;
				$incompletes = 0;
				$no_buyers = 0;
				$sales = 0;
				$lead_time = 0;
				if(isset($arr['lead_data']['errors'][390]))$duplicates++;
				if(isset($arr['lead_data']['errors'][400]))$ip_fraud++;
				if(isset($arr['lead_data']['errors'][470]))$lead_time++;
				
				if(!isset($arr['lead_data']['bankname']) && $call_type == 'internal'){
					$incompletes++;
				}
				
				if($call_type == 'internal' && isset($arr['lead_data']['bankname']) && !isset($arr['disposition']['approved']) && !isset($arr['disposition']['success']) && !isset($arr['lead_data']['errors'][470])){
					$no_buyers++;	
				}
				
				if($call_type == 'external' && !isset($arr['disposition']['approved']) && !isset($arr['disposition']['success']) && !isset($arr['lead_data']['errors'][390]) && !isset($arr['lead_data']['errors'][400])){
					$no_buyers++;	
				}
				
				if(isset($arr['disposition']['approved']) || isset($arr['disposition']['success'])){
					$sales++;
				}
				
				
				
				$totals[$date]['total_leads'] += 1;
				$totals[$date]['duplicates'] += $duplicates;
				$totals[$date]['lead_time'] += $lead_time;
				$totals[$date]['ip_fraud'] += $ip_fraud;
				$totals[$date]['incompletes'] += $incompletes;
				$totals[$date]['no_buyers'] += $no_buyers;
				$totals[$date]['sales'] += $sales;
				
			}

			$chart_totals = array(	'sales'=>0,
									'unsold'=>0,
									'incompletes'=>0,
									'no_buyer'=>0,
									'duplicates'=>0,
									'blacklist'=>0,
									'leadtime'=>0
			);

			foreach($totals as $day=>$info){
				
				if($call_type == 'external'){
					$duplicates = $info['duplicates'];
				}else{
					$duplicates = 'N/A';
				}				
				
				$total_unsold = $info['duplicates']+$info['ip_fraud']+$info['incompletes']+$info['no_buyers']+$info['lead_time'];
				
				
				
				$data[] = array($day,$info['total_leads'],$info['sales'],$total_unsold,$info['incompletes'],$info['no_buyers'],$duplicates,$info['ip_fraud'],$info['lead_time']);
				
				$chart_totals['sales'] += $info['sales'];
				$chart_totals['unsold'] += $total_unsold;
				
				$chart_totals['incompletes'] += $info['incompletes'];
				$chart_totals['no_buyer'] += $info['no_buyers'];
				
				$chart_totals['duplicates'] += (($duplicates == 'N/A') ? 0 : $duplicates);
				$chart_totals['blacklist'] += $info['ip_fraud'];
				$chart_totals['leadtime'] += $info['lead_time'];
				
			}
			
			$unsold_percentage = (FLOAT)$this->Api->formatDecimal(   (($total_leads - $chart_totals['sales']) / $total_leads) * 100   );
			$sold_percentage = (FLOAT)$this->Api->formatDecimal(  100.00 - $unsold_percentage  );
			$chart_totals['sold_percentage'] = $sold_percentage;
			$chart_totals['unsold_percentage'] = $unsold_percentage;
			
			
			$chart_totals['incomplete_percentage'] = (FLOAT)$this->Api->formatDecimal(   ($chart_totals['incompletes'] / $chart_totals['unsold']) * 100   );
			$chart_totals['no_buyer_percentage'] = (FLOAT)$this->Api->formatDecimal(   ($chart_totals['no_buyer'] / $chart_totals['unsold']) * 100   );
			$chart_totals['duplicates_percentage'] = (FLOAT)$this->Api->formatDecimal(   ($chart_totals['duplicates'] / $chart_totals['unsold']) * 100   );
			$chart_totals['blacklist_percentage'] = (FLOAT)$this->Api->formatDecimal(   ($chart_totals['blacklist'] / $chart_totals['unsold']) * 100   );
			$chart_totals['leadtime_percentage'] = (FLOAT)$this->Api->formatDecimal(   ($chart_totals['leadtime'] / $chart_totals['unsold']) * 100   );		
			
			$return['chart_totals'] = $chart_totals;
			$return['data'] = $data;
		}
		
		return json_encode($return);
		
	}

	public function createdatareport(){
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		$return = array('status'=>'failure');
		
		$explode_start_date = explode("/", $this->request->data['startdate']);
		$explode_end_date = explode("/", $this->request->data['enddate']);
		
		$start_date = $explode_start_date[2]."-".$explode_start_date[0]."-".$explode_start_date[1]." 00:00:00";
		$end_date = $explode_end_date[2]."-".$explode_end_date[0]."-".$explode_end_date[1]." 00:00:00";
		
		
		
		$start = new MongoDB\BSON\UTCDateTime(strtotime($start_date));
		$end = new MongoDB\BSON\UTCDateTime(strtotime($end_date));
		
		$mongo = $this->ReportTrack->getDataSource();
		//2 minutes
		$mongo->setTimeout(120000);
		$mongoCollectionObject = $mongo->getMongoCollection($this->ReportTrack);
		
		$ops = array();
		$ops = array(
						
					    array( //equivalent to mysql where conditions
							'$match' => array( 
								'lead_created' => array('$gte'=> $start, '$lt' => $end)
							)
						),
						array('$project' => array('lead_created' => 1, 'track_id' => 1, 'lead_id' => 1 )),
						//array('$limit'=>7)
										
		);
	
		$zip = $this->request->data['zip'];
		$state = strtolower($this->request->data['state']);
		$mobile = $this->request->data['mobile'];
		$military = $this->request->data['military'];
		$redirect = $this->request->data['redirect'];
		$sold = $this->request->data['sold'];
		$altered = $this->request->data['altered'];
		$affiliate_id = $this->request->data['affiliate'];
		$fields = $this->request->data['mongo_fields'];
		$agree_phone = $this->request->data['agreephone'];
		$full_data = $this->request->data['fulldata'];
		$unsold = $this->request->data['unsold'];
		$app_type = $this->request->data['apptype'];
		
		if($affiliate_id != "")$ops[0]['$match']['affiliate_id'] = $affiliate_id;
		if($zip != "")$ops[0]['$match']['lead_data.zip'] = $zip;
		if($state != "")$ops[0]['$match']['lead_data.state'] = $state;
		if($mobile == "true")$ops[0]['$match']['lead_data.mobile'] = "true";
		if($military == "true")$ops[0]['$match']['lead_data.military'] = "true";
		if($altered == "true")$ops[0]['$match']['lead_data.altered'] = array('$ne'=>null);
		if($agree_phone == "true")$ops[0]['$match']['lead_data.agreephone'] = "true";
		if($app_type != "")$ops[0]['$match']['lead_data.apptype'] = $app_type;
		
		if($redirect == "true"){
			$ops[0]['$match']['lead_data.redirect_urls'] = array('$exists'=>false);
			$ops[0]['$match']['lead_data.receivableamount'] = array('$ne'=>null);
		}
		
		if($sold == "true"){
			$ops[0]['$match']['lead_data.receivableamount'] = array('$ne'=>null);
		}
		
		if($unsold == "true"){
			$ops[0]['$match']['lead_data.receivableamount'] = array('$exist'=>false);
		}
		
		$top_level_fields = array("affiliate_id","track_id","request_id","lead_id","offer_id","campaign_id");
		foreach($fields as $field){
			if(in_array($field, $top_level_fields)){
				$ops[1]['$project'][$field] = 1;
			}else{
				$ops[1]['$project']['lead_data.'.$field] = 1;
			}
		}
		
		try{
			$results = $mongoCollectionObject->aggregate($ops);	
		}catch(Exception $e){
			$return['status'] = "failure";
			$return['data'] = $e->getMessage();
			return json_encode($return);	
		}
	
	
		
		
		if(count($results['result']) > 0){
			$fields_csv = '"Created","'.implode('","',$fields).'"';
			$id = time();
			$file_name = "dataexport_".$id.".csv";
			$file = new File(APP."tmp/files/reports/dataexport/".$file_name, true);
			$file->write($fields_csv);
			$file->write("\n");
		
			$logarray = array('message'=>'Data Export','fields'=>$fields_csv,'count'=>count($results['result']),'user'=>$this->Session->read('Auth.User.full_name'));
			$this->log('Export-->');
			$this->log(json_encode($logarray));
			//$this->log('export',json_encode($logarray));
			
			$data = $results['result'];
			
			foreach($data as $index=>$arr){
				$created = date('Y-m-d', $arr['lead_created']->sec);	
				$track_id = ((isset($arr['track_id']))? $arr['track_id'] : "");
				$lead_id = ((isset($arr['lead_id']))? $arr['lead_id'] : "");
				$csv_array = array($created);
				$missing_data = false;
				
				foreach($fields as $field2){
					if(isset($arr['lead_data'][$field2])){
						$csv_array[] = str_replace('"','\"',$arr['lead_data'][$field2]);
					
					}elseif(isset($arr[$field2])){ //check top level fields
						$csv_array[] = str_replace('"','\"',$arr[$field2]);		
					
					}else{
						$csv_array[] = "N/A";
						$missing_data = true;
					}	
				}
				
				if($full_data == 'true' && $missing_data)continue;
				
				$file->write('"'.implode('","',$csv_array).'"');
				$file->write("\n");
				
			}
			
			$file->close();
			$return['status'] = "success";
			$return['id'] = $id;
		}else{
			$return['status'] = "failure";
			$return['data'] = 'No Records Found';
		}	
		
		return json_encode($return);
	
	}

	public function dataexport_download($id){
		$path = APP.'tmp/files/reports/dataexport/dataexport_'.$id.'.csv';
		$this->response->file($path, array('download'=>true, 'name'=>'dataexport_'.$id.'.csv'));
		return $this->response;
	}
	
	public function deletedatareport($id){
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		$return = array('status'=>'failure');
		$path = APP.'tmp/files/reports/dataexport/dataexport_'.$id.'.csv';
		$file = new File($path, false);
		//$file->delete();
		$return['status'] = 'success';
		return json_encode($return);
	}

	public function saleintake(){
		$this->layout = 'dashboard';
		
		//Retrieve a list of affiliates
		$params = array('order'=>array('Affiliate.affiliate_name'));
		$this->set('affiliate_list', $this->Affiliate->find('all',$params));
		
		//Retrieve a list of offers, list is cached for 1 hour.
		$cache['hash'] = md5('offerlist');
		$cache['value'] = false;
		$cache['value'] = Cache::read($cache['hash']);
		
		if($cache['value'] === false){
			$offerObj = $this->Cake->exportoffer();
			foreach($offerObj['data']['offers'] AS $offers){
				foreach($offers as $offer){
					$cache['value'][$offer['offer_name']] = $offer['offer_id'];
				}
			}
			ksort($cache['value']);
			Cache::write($cache['hash'], $cache['value']);
		}
		$this->set('offer_list',$cache['value']);
		unset($cache);
	}
	
	/**
	 * Overview and price point reporting for buyer contracts by tree.
	 */
	public function buyerstatus(){
		$this->layout = 'dashboard';
						
		//Build Pingtrees
		$this->set('pingtree_list', $this->Buyer->getPingtree(false));
	}
	
	/**
	 * Return a list of contracts and data points for the given buyer in a specific tree.
	 * Date format Y-m-d
	 * $buyer_id = tree
	 * $buyer_name = contracts to aggrogate
	 */
	public function getContractReport($start, $end, $buyer_id, $contract_id=0){
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		
		$cache['hash'] = md5($start.$end.$buyer_id.$contract_id);
		$contract_array = explode(',',$contract_id);
		
		$response = array('status'=>'success', 'message'=>'', 'data'=>'');
		
		$start_date = new MongoDB\BSON\UTCDateTime(strtotime($start));
		$end_date = new MongoDB\BSON\UTCDateTime(strtotime($end));
		
		foreach($contract_array as $key=>$contract_id){
			$sub['lead_created']=array('$gt'=>$start_date, '$lt'=>$end_date);
			$sub['lead_data.receivableamount']=array('$ne'=>null);
			$sub['disposition']=array('$ne'=>null);
			
			$params = array('conditions'=>array('remote_contract_id'=>$contract_id), 'fields'=>array('contract_name'));
			$contract_result = $this->Contract->find('first', $params);
	
			$result[$contract_id]['name'] = $contract_result['Contract']['contract_name'];
			
			if($buyer_id == '7' ||  $buyer_id == '28'){
				$sub['lead_data.calltype']='internal';
			}else{
				$sub['lead_data.calltype']='external';
			}
					
			if($this->Session->read('Settings.showtest') != '1'){
				$sub['lead_data.firstname'] = new MongoDB\BSON\Regex('/^(?!.*test).*$/i', 'i');
			}
		
			//Approved---------------------
			$sub['disposition.approved.'.$contract_id]=array('$exists'=>true);
			$params = array('conditions' => $sub);
			$result[$contract_id]['approved'] = $this->ReportTrack->find('count', $params);
			$result[$contract_id]['total'] = $result[$contract_id]['approved'];
			unset($params);
			unset($sub);
			$this->ReportTrack->clear();
			
			//Success---------------------*added to approved*
			$sub['disposition.success.'.$contract_id]=array('$exists'=>true);
			$params = array('conditions' => $sub);
			$result[$contract_id]['approved'] += $this->ReportTrack->find('count', $params);
			$result[$contract_id]['total'] += $result[$contract_id]['approved'];
			unset($params);
			unset($sub);
			
			//Revenue---------------------
			$sub['lead_created']=array('$gt'=>$start_date, '$lt'=>$end_date);
			$sub['lead_data.receivableamount']=array('$ne'=>null);
			$sub['disposition']=array('$ne'=>null);
			$sub['disposition.approved.'.$contract_id]=array('$exists'=>true);
				
			if($buyer_id == '7' ||  $buyer_id == '28'){
				$sub['lead_data.calltype']='internal';
			}else{
				$sub['lead_data.calltype']='external';
			}
				
			if($this->Session->read('Settings.showtest') != '1'){
				$sub['lead_data.firstname'] = new MongoDB\BSON\Regex('/^(?!.*test).*$/i', 'i');
			}
			
			$params = array('conditions' => $sub,'fields'=>array('lead_data.receivableamount'));
			$rev_result = $this->ReportTrack->find('all', $params);
			$result[$contract_id]['revenue'] = 0;
			foreach($rev_result AS $lead){
				$result[$contract_id]['revenue'] += $lead['ReportTrack']['lead_data']['receivableamount'];
			}
			unset($params);
			unset($sub);
			$this->ReportTrack->clear();
			
			//Declined---------------------
			$sub['lead_created']=array('$gt'=>$start_date, '$lt'=>$end_date);
			$sub['lead_data.receivableamount']=array('$ne'=>null);
			$sub['disposition']=array('$ne'=>null);
				
			if($buyer_id == '7' ||  $buyer_id == '28'){
				$sub['lead_data.calltype']='internal';
			}else{
				$sub['lead_data.calltype']='external';
			}
				
			if($this->Session->read('Settings.showtest') != '1'){
				$sub['lead_data.firstname'] = new MongoDB\BSON\Regex('/^(?!.*test).*$/i', 'i');
			}
			
			$sub['disposition.declined.'.$contract_id]=array('$exists'=>true);
			$params = array('conditions' => $sub);
			$result[$contract_id]['declined'] = $this->ReportTrack->find('count', $params);
			$result[$contract_id]['total'] += $result[$contract_id]['declined'];
			unset($params);
			unset($sub);
			$this->ReportTrack->clear();
			
			//Duplicate---------------------
			$sub['lead_created']=array('$gt'=>$start_date, '$lt'=>$end_date);
			$sub['lead_data.receivableamount']=array('$ne'=>null);
			$sub['disposition']=array('$ne'=>null);
				
			if($buyer_id == '7' ||  $buyer_id == '28'){
				$sub['lead_data.calltype']='internal';
			}else{
				$sub['lead_data.calltype']='external';
			}
				
			if($this->Session->read('Settings.showtest') != '1'){
				$sub['lead_data.firstname'] = new MongoDB\BSON\Regex('/^(?!.*test).*$/i', 'i');
			}
				
			$sub['disposition.duplicate.'.$contract_id]=array('$exists'=>true);
			$params = array('conditions' => $sub);
			$result[$contract_id]['duplicate'] = $this->ReportTrack->find('count', $params);
			$result[$contract_id]['total'] += $result[$contract_id]['duplicate'];
			unset($params);
			unset($sub);
			$this->ReportTrack->clear();
			
			//Error---------------------
			$sub['lead_created']=array('$gt'=>$start_date, '$lt'=>$end_date);
			$sub['lead_data.receivableamount']=array('$ne'=>null);
			$sub['disposition']=array('$ne'=>null);
				
			if($buyer_id == '7' ||  $buyer_id == '28'){
				$sub['lead_data.calltype']='internal';
			}else{
				$sub['lead_data.calltype']='external';
			}
				
			if($this->Session->read('Settings.showtest') != '1'){
				$sub['lead_data.firstname'] = new MongoDB\BSON\Regex('/^(?!.*test).*$/i', 'i');
			}
			
			$sub['disposition.error.'.$contract_id]=array('$exists'=>true);
			$params = array('conditions' => $sub);
			$result[$contract_id]['error'] = $this->ReportTrack->find('count', $params);
			$result[$contract_id]['total'] += $result[$contract_id]['error'];
			unset($params);
			unset($sub);
			$this->ReportTrack->clear();
			
			//Timeout---------------------
			$sub['lead_created']=array('$gt'=>$start_date, '$lt'=>$end_date);
			$sub['lead_data.receivableamount']=array('$ne'=>null);
			$sub['disposition']=array('$ne'=>null);
			
			if($buyer_id == '7' ||  $buyer_id == '28'){
				$sub['lead_data.calltype']='internal';
			}else{
				$sub['lead_data.calltype']='external';
			}
			
			if($this->Session->read('Settings.showtest') != '1'){
				$sub['lead_data.firstname'] = new MongoDB\BSON\Regex('/^(?!.*test).*$/i', 'i');
			}
			
			$sub['disposition.timeout.'.$contract_id]=array('$exists'=>true);
			$params = array('conditions' => $sub);
			$result[$contract_id]['timeout'] = $this->ReportTrack->find('count', $params);
			$result[$contract_id]['total'] += $result[$contract_id]['timeout'];
			unset($params);
			unset($sub);
			$this->ReportTrack->clear();
			
			//Fraud---------------------
			$sub['lead_created']=array('$gt'=>$start_date, '$lt'=>$end_date);
			$sub['lead_data.receivableamount']=array('$ne'=>null);
			$sub['disposition']=array('$ne'=>null);
				
			if($buyer_id == '7' ||  $buyer_id == '28'){
				$sub['lead_data.calltype']='internal';
			}else{
				$sub['lead_data.calltype']='external';
			}
				
			if($this->Session->read('Settings.showtest') != '1'){
				$sub['lead_data.firstname'] = new MongoDB\BSON\Regex('/^(?!.*test).*$/i', 'i');
			}
				
			$sub['disposition.fraud.'.$contract_id]=array('$exists'=>true);
			$params = array('conditions' => $sub);
			$result[$contract_id]['fraud'] = $this->ReportTrack->find('count', $params);
			$result[$contract_id]['total'] += $result[$contract_id]['fraud'];
			unset($params);
			unset($sub);
			$this->ReportTrack->clear();
			
			//Unknown
			$sub['lead_created']=array('$gt'=>$start_date, '$lt'=>$end_date);
			$sub['lead_data.receivableamount']=array('$ne'=>null);
			$sub['disposition']=array('$ne'=>null);
				
			//This is temporary, it hardcodes affiliate trees.  I don't like this, but its necesary until we are off cake.
			//Needs to be updated if we add additional affiliate trees.
			if($buyer_id == '7' ||  $buyer_id == '28'){
				$sub['lead_data.calltype']='internal';
			}else{
				$sub['lead_data.calltype']='external';
			}
				
			//Remove tests leads if session does not ask for them.
			if($this->Session->read('Settings.showtest') != '1'){
				$sub['lead_data.firstname'] = new MongoDB\BSON\Regex('/^(?!.*test).*$/i', 'i');
			}
			$sub['disposition.unknown_response.'.$contract_id]=array('$exists'=>true);
			$params = array('conditions' => $sub);
			$result[$contract_id]['unknown_response'] = $this->ReportTrack->find('count', $params);
			$result[$contract_id]['total'] += $result[$contract_id]['unknown_response'];
			unset($params);
			unset($sub);
			$this->ReportTrack->clear();
		}
		
		//Build export and cache it
		foreach($result AS $contract_id=>$contract_data){
			setlocale(LC_MONETARY, 'en_US');
			
			$export['headers'] = array('NAME','SENT','SOLD','ACCEPT','DECLINED','ERROR','TIMEOUT','DUPLICATE','REVENUE','EPL');
			$export['data'][$contract_id]=array(
					$contract_data['name'],
					$contract_data['total'],
					$contract_data['approved'],
					is_nan((($contract_data['approved']/$contract_data['total'])*100)) ? 0 : number_format((($contract_data['approved']/$contract_data['total'])*100),2,'.',','),
					is_nan((($contract_data['declined']/$contract_data['total'])*100)) ? 0 : number_format((($contract_data['declined']/$contract_data['total'])*100),2,'.',','),
					is_nan((($contract_data['error']/$contract_data['total'])*100)) ? 0 : number_format((($contract_data['error']/$contract_data['total'])*100),2,'.',','),
					is_nan((($contract_data['timeout']/$contract_data['total'])*100)) ? 0 : number_format((($contract_data['timeout']/$contract_data['total'])*100),2,'.',','),
					is_nan((($contract_data['duplicate']/$contract_data['total'])*100)) ? 0 : number_format((($contract_data['duplicate']/$contract_data['total'])*100),2,'.',','),
					'$'.number_format($contract_data['revenue'],2,'.',','),
					is_nan(($contract_data['revenue']/$contract_data['total'])) ? 0 : '$'.number_format(($contract_data['revenue']/$contract_data['total']),2,'.',',')
			);
		}
		
		Cache::write($cache['hash'], json_encode($export),'5m');
		
		$response['data'] = $result;
		return json_encode($response);
	}
	
	public function export($key){
		$this->layout = 'ajax';
		$this->response->download("export_".md5($key).".csv");
		
		$data = json_decode(Cache::read(md5($key)),true);
		
		$response = array('status'=>'error', 'message'=>'', 'data'=>'');
		$response['status'] = 'success';
		$response['data']['file'] = $data['data'];
		$response['data']['headers'] = $data['headers'];
		$this->set('data', json_encode($response));
	}
		
	/**
	 * Returns data.today, data.yesterday, data.week
	 * @param string $type
	 */
	public function getSummaryIntake($model='lead', $type='external', $affiliate_id=0, $offer_id=0){
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		
		$cache['hash'] = md5('lead_intake'.$model.$type.$affiliate_id.$offer_id.$this->Session->read('Settings.showtest'));
		$cache['value'] = false;
		$cache['value'] = Cache::read($cache['hash']);
		
		if($cache['value'] === false){
			$response = array('status'=>'error', 'message'=>'', 'data'=>'');
			$date = new DateTime();
			$date->setTime(0,0,0);
			for ($i=0; $i<48; $i++){
				$dates['today'][$i]['start'] = $date->format('Y-m-d H:i:s');
				$date->setTime($date->format('H'), $date->format('i'), 0);
				$date->modify('+30 minute');
				$dates['today'][$i]['end'] = $date->format('Y-m-d H:i:s');
				$date->setTime($date->format('H'), $date->format('i'), 1);
			}
			
			$date = new DateTime();
			$date->modify('-1 day');
			$date->setTime(0,0,0);		
			for ($i=0; $i<48; $i++){
				$dates['yesterday'][$i]['start'] = $date->format('Y-m-d H:i:s');
				$date->setTime($date->format('H'), $date->format('i'), 0);
				$date->modify('+30 minute');
				$dates['yesterday'][$i]['end'] = $date->format('Y-m-d H:i:s');
				$date->setTime($date->format('H'), $date->format('i'), 1);
			}
			
			$date = new DateTime();
			$date->modify('-7 day');
			$date->setTime(0,0,0);			
			for ($i=0; $i<48; $i++){
				$dates['week'][$i]['start'] = $date->format('Y-m-d H:i:s');
				$date->setTime($date->format('H'), $date->format('i'), 0);
				$date->modify('+30 minute');
				$dates['week'][$i]['end'] = $date->format('Y-m-d H:i:s');
				$date->setTime($date->format('H'), $date->format('i'), 1);
			}
			
			foreach($dates AS $day=>$groups){
				foreach($groups AS $key=>$times){
					$start = new MongoDB\BSON\UTCDateTime(strtotime($times['start']));
					$end = new MongoDB\BSON\UTCDateTime(strtotime($times['end']));
									
					$sub['lead_created']=array('$gt'=>$start, '$lt'=>$end);
					$sub['lead_data.calltype'] = $type;
					
					if($affiliate_id>0){
						$sub['affiliate_id'] = $affiliate_id;
						unset($sub['lead_data.calltype']);
					}
					
					if($offer_id>0){
						$sub['offer_id'] = $offer_id;
						unset($sub['lead_data.calltype']);
					}
					
					// If we are querying for sales
					if($model=='sales'){
						$sub['lead_data.receivableamount'] = array('$ne'=>null);
					}
					
					$params = array('conditions' => $sub);
					
					//Remove tests leads if session does not ask for them.
					if($this->Session->read('Settings.showtest') != '1'){
						$params['conditions']['lead_data.firstname'] = new MongoDB\BSON\Regex('/^(?!.*test).*$/i', 'i');
					}
	
					$result[$day][] = (string) $this->ReportTrack->find('count', $params);
					
					unset($sub);
					unset($params);
				}
			}
					
			foreach($result AS $day=>$counter){
				foreach($counter AS $timeframe=>$count){
					$response['data'][$day][]=(int) $count;
				}
				
			}
			
			$response['status'] = 'success';
			$response['message'] = $type;
			Cache::write($cache['hash'], json_encode($response));
			return json_encode($response);
		}else{
			return $cache['value'];
		}
	}
	
	public function leaddetail($track_id=null) {
		$this->layout = 'dashboard';
				
		if($this->request->is('post') && $track_id === null){
			$track_id = $this->request->data['track_id'];	
		}
		
		if($track_id === null || $track_id == '' || !is_numeric($track_id)) {
			$this->Session->setFlash('Invalid Track ID provided.','notify_error');
		} else {
			$cache['hash'] = md5('leadsearch_'.$track_id);
			$cache['value'] = false;
			$cache['value'] = Cache::read($cache['hash']);
			
			if($cache['value'] === false) {
				$result[] = $this->ReportTrack->find('first', array('conditions'=>array('track_id'=>$track_id)));
				
				if(empty($result[0])){
					$this->Session->setFlash('Track ID not found.','notify_error');
				}else{
					$result[] = $this->Cake->exportcampaign($result[0]['ReportTrack']['campaign_id'], $result[0]['ReportTrack']['offer_id'], $result[0]['ReportTrack']['affiliate_id']);
					
					$data['ReportTrack']=$result[0]['ReportTrack'];
					$data['Cake']=$result[1]['data'];
					
					$this->set('lead', $data);
					Cache::write($cache['hash'], $data, '5m');
				}
			} else {
				$this->set('lead', $cache['value']);
			}
		}		
	}
	
	/**
	 * Redirect report, builds the summary redirect reports for vendor and affiliates.
	 * Currently cache is turned off until needed.  Need to rewrite this with a single query
	 * for each time period.
	 */
	public function redirectrate(){
		$this->layout = 'dashboard';
		$redirect_lead_count = 0;
		$sold_lead_count = 0;
		
		$today_redirect_lead_count = 0;
		$today_sold_lead_count = 0;
				
		$aff_redirect_lead_count = 0;
		$aff_sold_lead_count = 0;
		
		$aff_today_redirect_lead_count = 0;
		$aff_today_sold_lead_count = 0;
		
		$aff_yesterday_redirect_lead_count = 0;
		$aff_yesterday_sold_lead_count = 0;
		
		$aff_tomorrow_redirect_lead_count = 0;
		$aff_tomorrow_sold_lead_count = 0;
		
		$yesterday_redirect_lead_count = 0;
		
		//Retrieve a list of affiliates
		$params = array('order'=>array('Affiliate.affiliate_name'));
		$this->set('affiliate_list', $this->Affiliate->find('all',$params));
		
		//Retrieve a list of offers, list is cached for 1 hour.
		$cache['hash'] = md5('offerlist');
		$cache['value'] = false;
		$cache['value'] = Cache::read($cache['hash']);
		
		if($cache['value'] === false){
			$offerObj = $this->Cake->exportoffer();
			foreach($offerObj['data']['offers'] AS $offers){
				foreach($offers as $offer){
					$cache['value'][$offer['offer_name']] = $offer['offer_id'];
				}
			}
			ksort($cache['value']);
			Cache::write($cache['hash'], $cache['value']);
		}
		$this->set('offer_list',$cache['value']);
		unset($cache);		
		
		$cache['hash'] = md5('aff_redirectrate_summary_'.$this->Session->read('Settings.showtest'));
		$cache['value'] = false;
		$cache['value'] = Cache::read($cache['hash']);
		
		//Overall Affiliate Redirect Summary Rate
		if($cache['value'] === false) {
			$date = new DateTime();
			$today_dt = $date->format('Y-m-d');
			$date->modify('-7day');
			$back7 = $date->format('Y-m-d');
			
			$start_date = new MongoDB\BSON\UTCDateTime(strtotime($back7));
			
			$sub['lead_created']=array('$gt'=>$start_date);
			$sub['lead_data.calltype'] = 'internal';
			$sub['lead_data.receivableamount'] = array('$ne'=>null);
			$params = array(
					'fields'=>array('_id','lead_data.redirect_urls.redirected', 'redirect_urls.url'),
					'conditions' => $sub);
				
			//Remove tests leads if session does not ask for them.
			if($this->Session->read('Settings.showtest') != '1'){
				$params['conditions']['lead_data.firstname'] = new MongoDB\BSON\Regex('/^(?!.*test).*$/i', 'i');
			}
				
			$cache['value'] = $this->ReportTrack->find('all', $params);
			Cache::write($cache['hash'], $cache['value'], '5m');
		}
		
		foreach($cache['value'] as $id=>$record){
			if(isset($record['ReportTrack']['lead_data']['redirect_urls']['redirected']) && $record['ReportTrack']['lead_data']['redirect_urls']['redirected'] == '1'){
				$aff_redirect_lead_count++;
			}
		}
		
		$aff_sold_lead_count = count($cache['value']);
		$this->set('aff_overall_redirect_rate',number_format(($aff_redirect_lead_count/$aff_sold_lead_count)*100),0,'.',',');
		$this->set('aff_overall_sold',$aff_sold_lead_count);
		$this->set('aff_overall_redirected',$aff_redirect_lead_count);
		unset($params);
		unset($sub);
		
		$cache['hash'] = md5('aff_redirectrate_today_'.$this->Session->read('Settings.showtest'));
		$cache['value'] = false;
		$cache['value'] = Cache::read($cache['hash']);
		
		//Today Redirect Summary Rate
		if($cache['value'] === false) {
			$date = new DateTime();
			$today_dt = $date->format('Y-m-d');
			$date->modify('+1day');
			$tomorrow_dt = $date->format('Y-m-d');
				
			$sub['lead_data.calltype'] = 'internal';
			$sub['lead_data.receivableamount'] = array('$ne'=>null);
				
			$start_date = new MongoDB\BSON\UTCDateTime(strtotime($today_dt));
			$end_date = new MongoDB\BSON\UTCDateTime(strtotime($tomorrow_dt));
				
			$sub['lead_created']=array('$gt'=>$start_date, '$lt'=>$end_date);
				
			$params = array(
				'fields'=>array('_id','lead_data.redirect_urls.redirected', 'redirect_urls.url'),
				'conditions' => $sub);
				
			//Remove tests leads if session does not ask for them.
			if($this->Session->read('Settings.showtest') != '1'){
				$params['conditions']['lead_data.firstname'] = new MongoDB\BSON\Regex('/^(?!.*test).*$/i', 'i');
			}
				
			$cache['value'] = $this->ReportTrack->find('all', $params);
			Cache::write($cache['hash'], $cache['value'], '5m');
		}
		
		foreach($cache['value'] as $id=>$record){
			if($record['ReportTrack']['lead_data']['redirect_urls']['redirected'] == '1'){
				$aff_today_redirect_lead_count++;
			}
		}
		
		$aff_today_sold_lead_count = count($cache['value']);
		
		$this->set('aff_today_redirect_rate',number_format(($aff_today_redirect_lead_count/$aff_today_sold_lead_count)*100),0,'.',',');
		$this->set('aff_today_sold',$aff_today_sold_lead_count);
		$this->set('aff_today_redirected',$aff_today_redirect_lead_count);
		unset($params);
		unset($sub);
		
		$cache['hash'] = md5('aff_redirectrate_yesterday_'.$this->Session->read('Settings.showtest'));
		$cache['value'] = false;
		$cache['value'] = Cache::read($cache['hash']);
		
		//Yesterday Redirect Summary Rate
		if($cache['value'] === false) {
			$date = new DateTime();
			$today_dt = $date->format('Y-m-d');
			$date->modify('-1day');
			$yesterday_dt = $date->format('Y-m-d');
				
			$sub['lead_data.calltype'] = 'internal';
			$sub['lead_data.receivableamount'] = array('$ne'=>null);
				
			$start_date = new MongoDB\BSON\UTCDateTime(strtotime($yesterday_dt));
			$end_date = new MongoDB\BSON\UTCDateTime(strtotime($today_dt));
				
			$sub['lead_created']=array('$gt'=>$start_date, '$lt'=>$end_date);
				
			$params = array(
				'fields'=>array('_id','lead_data.redirect_urls.redirected', 'redirect_urls.url'),
				'conditions' => $sub);
				
			//Remove tests leads if session does not ask for them.
			if($this->Session->read('Settings.showtest') != '1'){
				$params['conditions']['lead_data.firstname'] = new MongoDB\BSON\Regex('/^(?!.*test).*$/i', 'i');
			}
				
			$cache['value'] = $this->ReportTrack->find('all', $params);
			Cache::write($cache['hash'], $cache['value'], '5m');
		}
		
		foreach($cache['value'] as $id=>$record){
			if($record['ReportTrack']['lead_data']['redirect_urls']['redirected'] == '1'){
				$aff_yesterday_redirect_lead_count++;
			}
		}
		
		$aff_yesterday_sold_lead_count = count($cache['value']);
		$this->set('aff_yesterday_redirect_rate',number_format(($aff_yesterday_redirect_lead_count/$aff_yesterday_sold_lead_count)*100),0,'.',',');
		$this->set('aff_yesterday_sold',$aff_yesterday_sold_lead_count);
		$this->set('aff_yesterday_redirected',$aff_yesterday_redirect_lead_count);
		
		unset($params);
		unset($sub);
		
		//Vendor------------------------------------
		
		$cache['hash'] = md5('redirectrate_summary_'.$this->Session->read('Settings.showtest'));
		$cache['value'] = false;
		$cache['value'] = Cache::read($cache['hash']);

		//Overall Redirect Summary Rate
		if($cache['value'] === false) {
			$date = new DateTime();
			$today_dt = $date->format('Y-m-d');
			$date->modify('-7day');
			$back7 = $date->format('Y-m-d');
				
			$start_date = new MongoDB\BSON\UTCDateTime(strtotime($back7));
			$sub['lead_created']=array('$gt'=>$start_date);
			$sub['lead_data.calltype'] = 'external';
			$sub['lead_data.receivableamount'] = array('$ne'=>null);
			$params = array(
				'fields'=>array('_id','lead_data.redirect_urls.redirected', 'redirect_urls.url'),
				'conditions' => $sub);
			
			//Remove tests leads if session does not ask for them.
			if($this->Session->read('Settings.showtest') != '1'){
				$params['conditions']['lead_data.firstname'] = new MongoDB\BSON\Regex('/^(?!.*test).*$/i', 'i');
			}
			
			$cache['value'] = $this->ReportTrack->find('all', $params);
			Cache::write($cache['hash'], $cache['value'], '5m');
		}
		
		foreach($cache['value'] as $id=>$record){
			if(isset($record['ReportTrack']['lead_data']['redirect_urls']['redirected']) && $record['ReportTrack']['lead_data']['redirect_urls']['redirected'] == '1'){
				$redirect_lead_count++;
			}
		}
		
		$sold_lead_count = count($cache['value']);
		$this->set('overall_redirect_rate',number_format(($redirect_lead_count/$sold_lead_count)*100),0,'.',',');
		$this->set('overall_sold',$sold_lead_count);
		$this->set('overall_redirected',$redirect_lead_count);
		unset($params);
		unset($sub);

		$cache['hash'] = md5('redirectrate_today_'.$this->Session->read('Settings.showtest'));
		$cache['value'] = false;
		$cache['value'] = Cache::read($cache['hash']);
		
		//Today Redirect Summary Rate
		if($cache['value'] === false) {
			$date = new DateTime();
			$today_dt = $date->format('Y-m-d');
			$date->modify('+1day');
			$tomorrow_dt = $date->format('Y-m-d');
			
			$sub['lead_data.calltype'] = 'external';
			$sub['lead_data.receivableamount'] = array('$ne'=>null);
			
			$start_date = new MongoDB\BSON\UTCDateTime(strtotime($today_dt));
			$end_date = new MongoDB\BSON\UTCDateTime(strtotime($tomorrow_dt));
			
			$sub['lead_created']=array('$gt'=>$start_date, '$lt'=>$end_date);
			
			$params = array(
				'fields'=>array('_id','lead_data.redirect_urls.redirected', 'redirect_urls.url'),
				'conditions' => $sub);
			
			//Remove tests leads if session does not ask for them.
			if($this->Session->read('Settings.showtest') != '1'){
				$params['conditions']['lead_data.firstname'] = new MongoDB\BSON\Regex('/^(?!.*test).*$/i', 'i');
			}
			
			$cache['value'] = $this->ReportTrack->find('all', $params);
			Cache::write($cache['hash'], $cache['value'], '5m');
		}
		
		foreach($cache['value'] as $id=>$record){
			if($record['ReportTrack']['lead_data']['redirect_urls']['redirected'] == '1'){
				$today_redirect_lead_count++;
			}
		}
		
		$today_sold_lead_count = count($cache['value']);
		
		$this->set('today_redirect_rate',number_format(($today_redirect_lead_count/$today_sold_lead_count)*100),0,'.',',');
		$this->set('today_sold',$today_sold_lead_count);
		$this->set('today_redirected',$today_redirect_lead_count);
		unset($params);
		unset($sub);
		
		$cache['hash'] = md5('redirectrate_yesterday_'.$this->Session->read('Settings.showtest'));
		$cache['value'] = false;
		$cache['value'] = Cache::read($cache['hash']);
		
		//Yesterday Redirect Summary Rate
		if($cache['value'] === false) {
			$date = new DateTime();
			$today_dt = $date->format('Y-m-d');
			$date->modify('-1day');
			$yesterday_dt = $date->format('Y-m-d');
			
			$sub['lead_data.calltype'] = 'external';
			$sub['lead_data.receivableamount'] = array('$ne'=>null);
			
			$start_date = new MongoDB\BSON\UTCDateTime(strtotime($yesterday_dt));
			$end_date = new MongoDB\BSON\UTCDateTime(strtotime($today_dt));
			
			$sub['lead_created']=array('$gt'=>$start_date, '$lt'=>$end_date);
			
			$params = array(
				'fields'=>array('_id','lead_data.redirect_urls.redirected', 'redirect_urls.url'),
				'conditions' => $sub);
			
			//Remove tests leads if session does not ask for them.
			if($this->Session->read('Settings.showtest') != '1'){
				$params['conditions']['lead_data.firstname'] = new MongoDB\BSON\Regex('/^(?!.*test).*$/i', 'i');
			}
			
			$cache['value'] = $this->ReportTrack->find('all', $params);
			Cache::write($cache['hash'], $cache['value'], '5m');
		}
		
		foreach($cache['value'] as $id=>$record){
			if(isset($record['ReportTrack']['lead_data']['redirect_urls']['redirected']) && $record['ReportTrack']['lead_data']['redirect_urls']['redirected'] == '1'){
				$yesterday_redirect_lead_count++;
			}
		}
		
		$yesterday_sold_lead_count = count($cache['value']);
		$this->set('yesterday_redirect_rate',number_format(($yesterday_redirect_lead_count/$yesterday_sold_lead_count)*100),0,'.',',');
		$this->set('yesterday_sold',$yesterday_sold_lead_count);
		$this->set('yesterday_redirected',$yesterday_redirect_lead_count);
	}
	
	/**
	 * Ajax function for building out custom redirect report request.
	 * @param int $affiliate_id
	 * @param int $offer_id
	 * @param string $start
	 * @param string $end
	 */
	public function getRedirectRate($affiliate_id, $offer_id=0, $campaign_id=0, $start=0, $end=0){
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		$response = array('status'=>'error', 'message'=>'', 'data'=>'');
		
		$this->Affiliate->contain();
		$aff_obj = $this->Affiliate->find('first',array('fields'=>array('Affiliate.affiliate_name'),'conditions'=>array('Affiliate.remote_id'=>$affiliate_id)));
		$redirect['name'] = $aff_obj['Affiliate']['affiliate_name']; 
		$redirect['avg_count'] = 0;
		$redirect['avg_redirect'] = 0;
		$redirect['avg_sold'] = 0;
		$redirect['today_count'] = 0;
		$redirect['today_redirect'] = 0;
		$redirect['today_sold'] = 0;
		$redirect['yesterday_count'] = 0 ;
		$redirect['yesterday_redirect'] = 0;
		$redirect['yesterday_sold'] = 0 ;
		
		$redirect['custom_count'] = 0;
		$redirect['custom_sold'] = 0;
		$redirect['custom_redirect'] = 0;
				
		$date = new DateTime();
		$today_dt = $date->format('Y-m-d');
		
		$date->modify('+1day');
		$tomorrow_dt = $date->format('Y-m-d');
		
		$date->modify('-2day');
		$yesterday_dt = $date->format('Y-m-d');
		
		$date->modify('-6day');
		$back7 = $date->format('Y-m-d');
				
		//Affiliate Custom
		if($offer_id>0){ $sub['offer_id'] = $offer_id; }
		if($campaign_id>0){ $sub['campaign_id'] = $campaign_id; }
		
		if($start != 0){
			$custom_start = new MongoDB\BSON\UTCDateTime(strtotime($start));
			$custom_end = new MongoDB\BSON\UTCDateTime(strtotime($end));
			$sub['lead_created']=array('$gt'=>$custom_start, '$lt'=>$custom_end);
		}
		
		$sub['affiliate_id'] = $affiliate_id;
		$sub['lead_data.receivableamount'] = array('$ne'=>null);
		
		$params = array(
				'fields'=>array('_id','lead_data.redirect_urls.redirected', 'redirect_urls.url'),
				'conditions' => $sub);
			
		//Remove tests leads if session does not ask for them.
		if($this->Session->read('Settings.showtest') != '1'){
			$params['conditions']['lead_data.firstname'] = new MongoDB\BSON\Regex('/^(?!.*test).*$/i', 'i');
		}
		
		$avg = $this->ReportTrack->find('all', $params);
				
		foreach($avg as $id=>$record){
			if(isset($record['ReportTrack']['lead_data']['redirect_urls']['redirected']) && $record['ReportTrack']['lead_data']['redirect_urls']['redirected'] == '1'){
				$redirect['custom_count']++;
			}
		}
		
		$redirect['custom_sold'] = count($avg);
		$redirect['custom_redirect'] = (int) number_format((($redirect['custom_count']/$redirect['custom_sold'])*100),0,'.',',');
		unset($sub);
		unset($params);
		
		
		//Affiliate Average
		if($offer_id>0){ $sub['offer_id'] = $offer_id; }
		if($campaign_id>0){ $sub['campaign_id'] = $campaign_id; }
		$start_date = new MongoDB\BSON\UTCDateTime(strtotime($back7));
		$sub['lead_created']=array('$gt'=>$start_date);
		$sub['affiliate_id'] = $affiliate_id;
		$sub['lead_data.receivableamount'] = array('$ne'=>null);
		$params = array('conditions' => $sub);
			
		//Remove tests leads if session does not ask for them.
		if($this->Session->read('Settings.showtest') != '1'){
			$params['conditions']['lead_data.firstname'] = new MongoDB\BSON\Regex('/^(?!.*test).*$/i', 'i');
		}
			
		$avg = $this->ReportTrack->find('all', $params);
				
		foreach($avg as $id=>$record){
			if(isset($record['ReportTrack']['lead_data']['redirect_urls']['redirected']) && $record['ReportTrack']['lead_data']['redirect_urls']['redirected'] == '1'){
				$redirect['avg_count']++;
			}
		}
		
		$redirect['avg_sold'] = count($avg);
		$redirect['avg_redirect'] = (int) number_format((($redirect['avg_count']/$redirect['avg_sold'])*100),0,'.',',');
		unset($sub);
		unset($params);
		
		//Affiliate Today
		$start_date = new MongoDB\BSON\UTCDateTime(strtotime($today_dt));
		$end_date = new MongoDB\BSON\UTCDateTime(strtotime($tomorrow_dt));
		
		if($offer_id>0){ $sub['offer_id'] = $offer_id; }
		if($campaign_id>0){ $sub['campaign_id'] = $campaign_id; }
		$sub['affiliate_id'] = $affiliate_id;
		$sub['lead_data.receivableamount'] = array('$ne'=>null);
		$sub['lead_created']=array('$gt'=>$start_date, '$lt'=>$end_date);
		$params = array('conditions' => $sub);
			
		//Remove tests leads if session does not ask for them.
		if($this->Session->read('Settings.showtest') != '1'){
			$params['conditions']['lead_data.firstname'] = new MongoDB\BSON\Regex('/^(?!.*test).*$/i', 'i');
		}
			
		$avg = $this->ReportTrack->find('all', $params);
		
		foreach($avg as $id=>$record){
			if($record['ReportTrack']['lead_data']['redirect_urls']['redirected'] == '1'){
				$redirect['today_count']++;
			}
		}
		
		$redirect['today_sold'] = count($avg);
		$redirect['today_redirect'] = (int) number_format((($redirect['today_count']/$redirect['today_sold'])*100),0,'.',',');
		unset($sub);
		unset($params);
		
		//Affiliate Yesterday
		$start_date = new MongoDB\BSON\UTCDateTime(strtotime($yesterday_dt));
		$end_date = new MongoDB\BSON\UTCDateTime(strtotime($today_dt));
		
		if($offer_id>0){ $sub['offer_id'] = $offer_id; }
		if($campaign_id>0){ $sub['campaign_id'] = $campaign_id; }
		$sub['affiliate_id'] = $affiliate_id;
		$sub['lead_data.receivableamount'] = array('$ne'=>null);
		$sub['lead_created']=array('$gt'=>$start_date, '$lt'=>$end_date);
		$params = array('conditions' => $sub);
			
		//Remove tests leads if session does not ask for them.
		if($this->Session->read('Settings.showtest') != '1'){
			$params['conditions']['lead_data.firstname'] = new MongoDB\BSON\Regex('/^(?!.*test).*$/i', 'i');
		}
			
		$avg = $this->ReportTrack->find('all', $params);
		
		foreach($avg as $id=>$record){
			if($record['ReportTrack']['lead_data']['redirect_urls']['redirected'] == '1'){
				$redirect['yesterday_count']++;
			}
		}
		
		$redirect['yesterday_sold'] = count($avg);
		$redirect['yesterday_redirect'] = (int) number_format((($redirect['yesterday_count']/$redirect['yesterday_sold'])*100),0,'.',',');
		
		$response['status'] = 'success';
		$response['data'] = $redirect;
		return json_encode($response);
	}
	
	/**
	 * Generates the lead query results when searching leads.  These results are not cached, but pull from the mongo reporting DB.
	 * @param string $start
	 * @param string $end
	 * @param string $first_name
	 * @param string $last_name
	 * @param string $email
	 * @param string $phone
	 * @param string $city
	 * @param string $state
	 * @param string $zip
	 * @param string $mobile
	 * @param string $military
	 * @param string $affiliate_id
	 * @param string $ip
	 */
	public function leadQuery($start, $end, $first_name, $last_name, $email, $phone, $city, $state, $zip, $mobile, $military, $affiliate_id, $ip, $redirect, $sold){
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		$response = array('status'=>'error', 'message'=>'', 'data'=>'');
	
		$start_date = new MongoDB\BSON\UTCDateTime(strtotime($start));
		$end_date = new MongoDB\BSON\UTCDateTime(strtotime($end));
	
		$sub['lead_created']=array('$gt'=>$start_date, '$lt'=>$end_date);
	
		if($first_name != '-'){$sub['lead_data.firstname']= new MongoDB\BSON\Regex("/$first_name/i", 'i');}
		if($last_name != '-'){$sub['lead_data.lastname']= new MongoDB\BSON\Regex("/$last_name/i", 'i');}
		if($email != '-'){$sub['lead_data.email']= new MongoDB\BSON\Regex("/$email/i", 'i');}
		if($city != '-'){$sub['lead_data.city']=$city;}
		if($state != '-'){$sub['lead_data.state']=$state;}
		if($zip != '-'){$sub['lead_data.zip']=$zip;}
		if($mobile == '2'){$sub['lead_data.mobile']='true';}
		if($military == '2'){$sub['lead_data.military']='true';}
		if($affiliate_id != '-'){$sub['affiliate_id']=$affiliate_id;}
		if($ip != '-'){$sub['lead_data.ipaddress']=$ip;}
		if($redirect == '1'){
			$sub['lead_data.redirect_urls']=array('$exists'=>false); 
			$sub['lead_data.receivableamount']=array('$ne'=>null);
		}
		if($sold == '1'){
			$sub['lead_data.receivableamount']=array('$ne'=>null);
		}
		
		//Limit the fields
		$fields = array('track_id','offer_id','affiliate_id','lead_created','lead_data.receivableamount','lead_data.errors','lead_data.paidamount','lead_data.marginamount','lead_data.margin','lead_data.firstname');
		
		$params = array('conditions' => $sub,'fields'=>$fields,'limit'=>500,'order'=>array('_id'=>'DESC'));
		$result = $this->ReportTrack->find('all', $params);

		$response['status'] = 'success';
		$response['data'] = $result;
		$cache['value'] = $response;
	
		return json_encode($cache['value']);
	}
	
	/**
	 * Pull lead documents from mongo based on the start and end date. (yyyy-mm-dd)
	 * @param string $start
	 * @param string $end
	 * @param int $limit
	 */
	public function getGeneratedLeads($start, $end, $limit=0) {
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		$response = array('status'=>'error', 'message'=>'', 'data'=>'');
		
		$istest = $this->Session->read('Settings.showtest');
		
		if($start == '' || $end == '') {
			$response['message'] = 'Start and End dates required.';
			$cache['value'] = $response;
		}elseif(!is_numeric($limit)){
			$response['message'] = 'Limit must be a number.';
			$cache['value'] = $response;
		} else {
			$cache['hash'] = md5('getgenleads_'.$start.$end.$limit.$istest);
			$cache['value'] = false;
			$cache['value'] = Cache::read($cache['hash']);
			
			if($cache['value'] === false){
				$start_date = new MongoDB\BSON\UTCDateTime(strtotime($start));
				$end_date = new MongoDB\BSON\UTCDateTime(strtotime($end));
				
				if($limit>0){
					$sub['lead_created'] = array('$gt'=>$start_date, '$lt'=>$end_date);
					$sub['lead_data.apptype'] = array('$ne'=>null);
					$params = array('conditions' => $sub, 'order'=>array('_id'=>'DESC'), 'limit'=>$limit);
				}else{
					$sub['lead_created'] = array('$gt'=>$start_date, '$lt'=>$end_date);
					$sub['lead_data.apptype'] = array('$ne'=>null);
					$params = array('conditions' => $sub);
				}
				
				if($this->Session->read('Settings.showtest') == '1'){
					$params['conditions']['lead_data.firstname']=array('$ne'=>'/test/');
				}
				
				$result = $this->ReportTrack->find('all', $params);
				
				$response['status'] = 'success';
				$response['data'] = $result;
				$cache['value'] = $response;

				Cache::write($cache['hash'],$cache['value']);
			}
		}
		
		return json_encode($cache['value']);
	}
	
	/**
	 * Count lead documents from mongo based on the start and end date.  Excludes test leads for non-administrators.
	 * @param string $start
	 * @param string $end
	 */
	public function countGeneratedLeads($start, $end) {
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		$istest = $this->Session->read('Settings.showtest');
		
		$response = array('status'=>'error', 'message'=>'', 'data'=>'');
		
		if($start == '' || $end == '') {
			$response['message'] = 'Start and End dates required.';
			$cache['value'] = $response;
		} else {
			$cache['hash'] = md5('countgenleads_'.$start.$end.$istest);
			$cache['value'] = false;
			$cache['value'] = Cache::read($cache['hash']);
				
			if($cache['value'] === false){
				$start_date = new MongoDB\BSON\UTCDateTime(strtotime($start));
				$end_date = new MongoDB\BSON\UTCDateTime(strtotime($end));
				$sub['lead_created'] = array('$gt'=>$start_date, '$lt'=>$end_date);
				$sub['lead_data.apptype'] = array('$ne'=>null);
				$params = array('conditions' => $sub);
			
				//Remove tests leads for anyone that is not an admin.
				if($this->Session->read('Settings.showtest') != '1'){
					$params['conditions']['lead_data.firstname'] = new MongoDB\BSON\Regex('/^(?!.*test).*$/i', 'i');
				}
			
				$result = $this->ReportTrack->find('count', $params);
			
				$response['status'] = 'success';
				$response['data'] = $result;
				$cache['value'] = $response;
		
				Cache::write($cache['hash'],$cache['value']);
			}
		}
		
		return json_encode($cache['value']);
	}
	
		
	/**
	 * Count sold lead documents from mongo based on the start and end date. Excludes test leads for non-administrators.
	 * @param string $start
	 * @param string $end
	 */
	public function countSoldLeads($start, $end) {
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		$istest = $this->Session->read('Settings.showtest');
		$response = array('status'=>'error', 'message'=>'', 'data'=>'');
		
		if($start == '' || $end == '') {
			$response['message'] = 'Start and End dates required.';
			$cache['value'] = $response;
		} else {
			$cache['hash'] = md5('countsoldleads_'.$start.$end.$istest);
			$cache['value'] = false;
			//$cache['value'] = Cache::read($cache['hash']);
		
			if($cache['value'] === false){
				$start_date = new MongoDB\BSON\UTCDateTime(strtotime($start));
				$end_date = new MongoDB\BSON\UTCDateTime(strtotime($end));
				$sub['lead_created'] = array('$gt'=>$start_date, '$lt'=>$end_date);
				$sub['lead_data.receivableamount'] = array('$ne'=>null);
				$params = array('conditions' => $sub);
				
				//Remove tests leads for anyone that is not an admin.
				if($this->Session->read('Settings.showtest') != '1'){
					$params['conditions']['lead_data.firstname'] = new MongoDB\BSON\Regex('/^(?!.*test).*$/i', 'i');
				}
				
				$result = $this->ReportTrack->find('count', $params);
					
				$response['status'] = 'success';
				$response['data'] = $result;
				$cache['value'] = $response;
		
				Cache::write($cache['hash'],$cache['value']);
			}
		}
		
		return json_encode($cache['value']);
	}
	
	/**
	 * Calculate totals for receivable, paid, margin for the given date range. Excludes test leads for non-administrators.
	 * @param string $start
	 * @param string $end
	 */
	public function getProfit($start, $end) {
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		$istest = $this->Session->read('Settings.showtest');
		$response = array('status'=>'error', 'message'=>'', 'data'=>'');
		
		$profit['receivable_total'] = 0;
		$profit['paid_total'] = 0;
		$profit['margin_total'] = 0;
		
		if($start == '' || $end == '') {
			$response['message'] = 'Start and End dates required.';
			$cache['value'] = $response;
		} else {
			$cache['hash'] = md5('getprofit_'.$start.$end.$istest);
			$cache['value'] = false;
			$cache['value'] = Cache::read($cache['hash']);
		
			if($cache['value'] === false){
				$start_date = new MongoDB\BSON\UTCDateTime(strtotime($start));
				$end_date = new MongoDB\BSON\UTCDateTime(strtotime($end));
								
				//Pull data
				$sub['lead_created'] = array('$gt'=>$start_date, '$lt'=>$end_date);
				$sub['lead_data.apptype'] = array('$ne'=>null);
				$sub['lead_data.receivableamount'] = array('$ne'=>null);
				$sub['lead_data.paidamount'] = array('$ne'=>null);
				$sub['lead_data.marginamount'] = array('$ne'=>null);
							
				$params = array(
						'fields'=>array('_id','lead_data.calltype','lead_data.apptype','lead_created', 'redirect_urls.url','lead_data.receivableamount','lead_data.paidamount','lead_data.marginamount'),
						'conditions' => $sub);
					
				//Remove tests leads for anyone that is not an admin.
				if($this->Session->read('Settings.showtest') != '1'){
					$params['conditions']['lead_data.firstname'] = new MongoDB\BSON\Regex('/^(?!.*test).*$/i', 'i');
				}
					
				$result = $this->ReportTrack->find('all', $params);
				
				foreach($result as $key=>$lead){
					$profit['receivable_total'] += $lead['ReportTrack']['lead_data']['receivableamount'];
					$profit['paid_total'] += $lead['ReportTrack']['lead_data']['paidamount'];
					$profit['margin_total'] += $lead['ReportTrack']['lead_data']['marginamount'];
				}
				
				$response['status'] = 'success';
				$response['data'] = $profit;
				$cache['value'] = $response;
		
				Cache::write($cache['hash'],$cache['value']);
			}
		}
		
		return json_encode($cache['value']);
	}
		
	/**
	 * Aggregate generated leads by date, calltype.  Excludes test leads for non-administrators.
	 * @param string $start
	 * @param string $end
	 */
	public function flotLeadData($start, $end) {
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		$istest = $this->Session->read('Settings.showtest');
			
		$cache['hash'] = md5('flotleads_'.$start.$end.$istest);
		$cache['value'] = false;
		$cache['value'] = Cache::read($cache['hash']);

		if($cache['value'] === false){
			$start_date = new MongoDB\BSON\UTCDateTime(strtotime($start));
			$end_date = new MongoDB\BSON\UTCDateTime(strtotime($end));
		
			//Pull affiliate data
			$sub['lead_created'] = array('$gt'=>$start_date, '$lt'=>$end_date);
			$sub['lead_data.apptype'] = array('$ne'=>null);
			$sub['lead_data.calltype'] = 'internal';
			$params = array(
					'fields'=>array('_id','lead_data.calltype','lead_data.apptype','lead_created', 'redirect_urls.url','lead_data.receivableamount','lead_data.paidamount','lead_data.marginamount'), 
					'conditions' => $sub);
						
			//Remove tests leads for anyone that is not an admin.
			if($this->Session->read('Settings.showtest') != '1'){
				$params['conditions']['lead_data.firstname'] = new MongoDB\BSON\Regex('/^(?!.*test).*$/i', 'i');
			}
			
			$result = $this->ReportTrack->find('all', $params);
			
			//Pull vendor data
			$sub['lead_created'] = array('$gt'=>$start_date, '$lt'=>$end_date);
			$sub['lead_data.apptype'] = array('$ne'=>null);
			$sub['lead_data.calltype'] = 'external';
			$params2 = array(
					'fields'=>array('_id','lead_data.calltype','lead_data.apptype','lead_created', 'redirect_urls.url','lead_data.receivableamount','lead_data.paidamount','lead_data.marginamount'),
					'conditions' => $sub);
			
			//Remove tests leads for anyone that is not an admin.
			if($this->Session->read('Settings.showtest') != '1'){
				$params2['conditions']['lead_data.firstname'] = new MongoDB\BSON\Regex('/^(?!.*test).*$/i', 'i');
			}
			
			$result2 = $this->ReportTrack->find('all', $params2);
			
			foreach($result AS $row) {
				$row['ReportTrack']['lead_created'] = date('w', $row['ReportTrack']['lead_created']->sec);
				$data[] = $row;
			}
			
			foreach($result2 AS $row) {
				$row['ReportTrack']['lead_created'] = date('w', $row['ReportTrack']['lead_created']->sec);
				$data2[] = $row;
			}

			$graph_data['affiliate']['generated'][]=array(0,0);
			$graph_data['affiliate']['generated'][]=array(1,0);
			$graph_data['affiliate']['generated'][]=array(2,0);
			$graph_data['affiliate']['generated'][]=array(3,0);
			$graph_data['affiliate']['generated'][]=array(4,0);
			$graph_data['affiliate']['generated'][]=array(5,0);
			$graph_data['affiliate']['generated'][]=array(6,0);
			
			$graph_data['affiliate']['sold'][]=array(0,0);
			$graph_data['affiliate']['sold'][]=array(1,0);
			$graph_data['affiliate']['sold'][]=array(2,0);
			$graph_data['affiliate']['sold'][]=array(3,0);
			$graph_data['affiliate']['sold'][]=array(4,0);
			$graph_data['affiliate']['sold'][]=array(5,0);
			$graph_data['affiliate']['sold'][]=array(6,0);
			
			$graph_data['affiliate']['profit'][]=array(0,0);
			$graph_data['affiliate']['profit'][]=array(1,0);
			$graph_data['affiliate']['profit'][]=array(2,0);
			$graph_data['affiliate']['profit'][]=array(3,0);
			$graph_data['affiliate']['profit'][]=array(4,0);
			$graph_data['affiliate']['profit'][]=array(5,0);
			$graph_data['affiliate']['profit'][]=array(6,0);
			
			$graph_data['vendor']['generated'][]=array(0,0);
			$graph_data['vendor']['generated'][]=array(1,0);
			$graph_data['vendor']['generated'][]=array(2,0);
			$graph_data['vendor']['generated'][]=array(3,0);
			$graph_data['vendor']['generated'][]=array(4,0);
			$graph_data['vendor']['generated'][]=array(5,0);
			$graph_data['vendor']['generated'][]=array(6,0);
			
			$graph_data['vendor']['sold'][]=array(0,0);
			$graph_data['vendor']['sold'][]=array(1,0);
			$graph_data['vendor']['sold'][]=array(2,0);
			$graph_data['vendor']['sold'][]=array(3,0);
			$graph_data['vendor']['sold'][]=array(4,0);
			$graph_data['vendor']['sold'][]=array(5,0);
			$graph_data['vendor']['sold'][]=array(6,0);
			
			$graph_data['vendor']['profit'][]=array(0,0);
			$graph_data['vendor']['profit'][]=array(1,0);
			$graph_data['vendor']['profit'][]=array(2,0);
			$graph_data['vendor']['profit'][]=array(3,0);
			$graph_data['vendor']['profit'][]=array(4,0);
			$graph_data['vendor']['profit'][]=array(5,0);
			$graph_data['vendor']['profit'][]=array(6,0);
				
			//Build affiliate generated plots
			foreach($data as $rec=>$track) {
				$graph[$track['ReportTrack']['lead_created']]+=1;
			}
				
			foreach($graph AS $day=>$count){
				$graph_data['affiliate']['generated'][$day]=array($day,$count);
			}
			
			unset($graph);
			
			//Build vendor generated plots
			if(is_array($data2)){
				foreach($data2 as $rec=>$track) {
					$graph[$track['ReportTrack']['lead_created']]+=1;
				}
			}
				
			if(is_array($graph)){
				foreach($graph AS $day=>$count){
					$graph_data['vendor']['generated'][$day]=array($day,$count);
				}
			}
			
			unset($graph);
			
			//Build affiliate sold plots
			foreach($data as $rec=>$track) {
				if(array_key_exists('receivableamount',$track['ReportTrack']['lead_data'])){
					$graph[$track['ReportTrack']['lead_created']]+=1;
				}
			}
				
			foreach($graph AS $day=>$count){
				$graph_data['affiliate']['sold'][$day]=array($day,$count);
			}
			
			unset($graph);
			
			//Build vendor sold plots
			foreach($data2 as $rec=>$track) {
				if(array_key_exists('receivableamount',$track['ReportTrack']['lead_data'])){
					$graph[$track['ReportTrack']['lead_created']]+=1;
				}
			}
				
			foreach($graph AS $day=>$count){
				$graph_data['vendor']['sold'][$day]=array($day,$count);
			}
			
			unset($graph);
			
			//Build affiliate profit plots
			foreach($data as $rec=>$track) {
				if(array_key_exists('receivableamount',$track['ReportTrack']['lead_data'])){
					$graph[$track['ReportTrack']['lead_created']]['receivable']+=$track['ReportTrack']['lead_data']['receivableamount'];
					$graph[$track['ReportTrack']['lead_created']]['paid']+=$track['ReportTrack']['lead_data']['paidamount'];
					$graph[$track['ReportTrack']['lead_created']]['margin']+=$track['ReportTrack']['lead_data']['marginamount'];
				}
			}
			
			foreach($graph AS $day=>$finance){
				//$value = ($finance['receivable']-$finance['paid']);
				$value = $finance['receivable'];
				$graph_data['affiliate']['profit'][$day]=array($day,$value);
			}
			
			unset($graph);
			
			//Build vendor profit plots
			foreach($data2 as $rec=>$track) {
				if(array_key_exists('receivableamount',$track['ReportTrack']['lead_data'])){
					$graph[$track['ReportTrack']['lead_created']]['receivable']+=$track['ReportTrack']['lead_data']['receivableamount'];
					$graph[$track['ReportTrack']['lead_created']]['paid']+=$track['ReportTrack']['lead_data']['paidamount'];
					$graph[$track['ReportTrack']['lead_created']]['margin']+=$track['ReportTrack']['lead_data']['marginamount'];
				}
			}
			
			foreach($graph AS $day=>$finance){
				//$value = $finance['receivable']-$finance['paid'];
				$value = $finance['receivable'];
				$graph_data['vendor']['profit'][$day]=array($day,$value);
			}
			
			$cache['value'] = $graph_data;
			Cache::write($cache['hash'],$cache['value']);
		}
			
		return json_encode($cache['value']);
	}
	
	/**
	 * Return list of contracts for the given buyer_id.  If group is set to true return an aggrogated list of buyers by name.
	 * @param int $buyer_id
	 * @return json
	 */
	public function getContracts($buyer_id, $group=false){
		$this->layout = null;
		$this->autoRender = false;
		$this->response->type('json');
		
		$response = array('status'=>'success', 'message'=>'', 'data'=>'');
		if($group === false){
			$params = array('conditions'=>array('id'=>$buyer_id));
			$contracts = $this->Buyer->find('first',$params);
			foreach($contracts['Contract'] AS $contract){
				$response['data'][]=$contract;
			}	
		}else{
			$response['data'] = 'notfinished';
		}
			return json_encode($response);
	}
	
	/**
	 * Specify what user group has access.  For development speed, this is not in ACL.
	 * @param array $user
	 * @return boolean
	 */
	private function __isAuthorized($user) {
		if (in_array($this->Session->read('Auth.User.Group.id'),array('2','3','4','5'))) {
			$this->Auth->allow();
			return true;
		}
		
		return false;
	}
}