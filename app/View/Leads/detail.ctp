<div class="mainpanel">
	<div class="pageheader">
		<div class="media">
			<div class="pageicon pull-left">
				<i class="fa fa-clipboard"></i>
			</div>
			<div class="media-body">
				<ul class="breadcrumb">
					<li><a href="/dashboard"><i class="glyphicon glyphicon-home"></i></a></li>
					<li><a href="/leads">Lead Management</a></li>
					<li><a href="/leads">Leads</a></li>
					<li>Lead Detail</li>
				</ul>
				<h4>Lead Detail</h4>
			</div>
		</div>
		<!-- media -->
	</div>
	<!-- pageheader -->

	<div class="contentpanel">
		<?php echo $this->Session->flash(); ?>
		<div class="row">
			<div class="col-md-8">
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs nav-justified">
                                    <li class="active"><a href="#lead_data" data-toggle="tab"><strong>Lead</strong></a></li>
                                    <?php if(array_key_exists('firstname', $lead['ReportTrack']['lead_data'])) {?><li><a href="#personal_data" data-toggle="tab"><strong>Personal</strong></a></li><?php } ?>
                                    <?php if(array_key_exists('employername', $lead['ReportTrack']['lead_data'])) {?><li><a href="#employment_data" data-toggle="tab"><strong>Employment</strong></a></li><?php } ?>
                                    <?php if(array_key_exists('employername', $lead['ReportTrack']['lead_data'])) {?><li><a href="#financial_data" data-toggle="tab"><strong>Financial</strong></a></li><?php } ?>
                                    <?php if(isset($lead['ReportTrack']['lead_data']['coapplicant']) && $lead['ReportTrack']['lead_data']['coapplicant'] == 'Yes') {?><li><a href="#coapp_data" data-toggle="tab"><strong>Co-Applicant</strong></a></li><?php } ?>
                                    <?php if(array_key_exists('redirect_urls', $lead['ReportTrack']['lead_data']) || array_key_exists('redirect_urls', $lead['ReportTrack'])) {?><li><a href="#redirect_data" data-toggle="tab"><strong>Redirect</strong></a></li><?php } ?>
                                    <?php if(array_key_exists('receivableamount', $lead['ReportTrack']['lead_data'])) {?><li><a href="#cake_data" data-toggle="tab"><strong>Billing</strong></a></li><?php } ?>
                                    <?php if(array_key_exists('disposition', $lead['ReportTrack'])) {?><li><a href="#disposition_data" data-toggle="tab"><strong>Dispositions</strong></a></li><?php } ?>                           
                                </ul>
        
                                <!-- Tab panes -->
                                <div class="tab-content mb30">
                                    <div class="tab-pane active" id="lead_data">
										<div class="table-responsive">
											<table id="leadDetail" class="table table-hover mb30">
												<thead>
												<tr>
												<th>#</th>
												<th>&nbsp;</th>
												<th>&nbsp;</th>
												</tr>
												</thead>
												<tbody>
												<tr>
												<td>Lead Type</td>
												<td><?php if($lead['ReportTrack']['lead_data']['calltype']=='internal'){$vertical_id=47; echo 'Affiliate';}else{$vertical_id=59; echo 'Vendor';} ?></td>
												<td>-</td>
												</tr>
												<tr>
												<td>Application</td>
												<td>
												<?php
													switch ($lead['ReportTrack']['lead_data']['apptype']){														
														case 'vendor_installment':
														case 'installment':
															echo 'Installment Loan';
														break;
														
														case 'vendor_payday':
														case 'payday':
															echo 'Payday Loan';
														break;
														
														case 'personalloan':
														case 'vendor_personalloan':
															echo 'Personal Loan';
														break;
														
														case 'lowtier':
														case 'vendor_lowtier':
															echo 'Low Tier Data';
															$vertical_id=60;
														break;
														
														case 'lowtierlong':
														case 'vendor_lowtierlong':
															echo 'Low Tier Long Form Data';
															$vertical_id=59;
														break;
													} 
												
												?></td>
												<td>$<?php if(isset($lead['ReportTrack']['lead_data']['loanamount'])){echo $lead['ReportTrack']['lead_data']['loanamount'];} if(isset($lead['ReportTrack']['lead_data']['loanamountpersonal'])){echo $lead['ReportTrack']['lead_data']['loanamountpersonal'];} ?></td>
												</tr>
												<tr>
												<td>Track ID</td>
												<td><?php echo $lead['ReportTrack']['track_id']; ?></td>
												<td>-</td>
												</tr>
												<tr>
												<td>Request ID</td>
												<td><?php echo $lead['ReportTrack']['request_id']; ?></td>
												<td>-</td>
												</tr>
												<tr>
												<td>Lead ID</td>
												<td><?php if(isset($lead['ReportTrack']['lead_id'])){echo $lead['ReportTrack']['lead_id'];} ?></td>
												<td>-</td>
												</tr>
												<tr>
												<td>Lead Date</td>
												<td>
												<?php 
												echo date('m/d/Y H:i:s a', $lead['ReportTrack']['lead_created']->sec);
												
												?></td>
												<td>-</td>
												</tr>
												<tr>
												<td>Site Template / Theme</td>
												<td>
												<?php 
												if(isset($lead['ReportTrack']['lead_data']['template']) && $lead['ReportTrack']['lead_data']['template'] != '') {
													echo ucfirst($lead['ReportTrack']['lead_data']['template']).' - ';
												}else{
													echo 'No Template - ';
												} 
												if(isset($lead['ReportTrack']['lead_data']['theme']) && $lead['ReportTrack']['lead_data']['theme'] != '') {
													echo ucfirst($lead['ReportTrack']['lead_data']['theme']);
												}else{
													echo 'No Theme';
												}
												?></td>
												<td>-</td>
												</tr>
												<tr>
												<td>Mobile</td>
												<td><?php echo ucfirst($lead['ReportTrack']['lead_data']['mobile']); ?></td>
												<td>-</td>
												</tr>
												<tr>
												<td>Consent</td>
												<td><?php if($lead['ReportTrack']['lead_data']['agreeconsent'] == 'on'){echo 'True';}else{echo 'False';} ?></td>
												<td>-</td>
												</tr>
												<tr>
												<td>Sub ID(s)</td>
												<td><?php 
													if(isset($lead['ReportTrack']['subid'])){
													$i=1;
													foreach($lead['ReportTrack']['subid'] AS $id=>$value){
														if(!is_array($value)){
															echo '<strong>S'.$i.'</strong>: '.$value.'<br>';
														}
														$i++;
													}
												} ?></td>
												<td>-</td>
												</tr>
												</tbody>
										</table>
		                              </div>										
                                    </div><!-- tab-pane -->
                                  
                                    <div class="tab-pane" id="personal_data">
                                    	<div class="table-responsive">
											<table class="table table-hover mb30">
												<thead>
												<tr>
												<th>#</th>
												<th>&nbsp;</th>
												<th>&nbsp;</th>
												</tr>
												</thead>
												<tbody>
												<tr>
												<td>Name</td>
												<td><?php echo ucfirst($lead['ReportTrack']['lead_data']['firstname']).' '.ucfirst($lead['ReportTrack']['lead_data']['lastname']); ?></td>
												<td>-</td>
												</tr>
												<tr>
												<td>Email</td>
												<td><?php echo $lead['ReportTrack']['lead_data']['email']; ?></td>
												<td>-</td>
												</tr>
												<tr>
												<td>Address</td>
												<td><?php echo ucwords($lead['ReportTrack']['lead_data']['address1']).' '.ucwords($lead['ReportTrack']['lead_data']['address2']); ?></td>
												<td>-</td>
												</tr>
												<tr>
												<td>City/State/Zip</td>
												<td><?php echo ucwords($lead['ReportTrack']['lead_data']['city']).' / '.strtoupper($lead['ReportTrack']['lead_data']['state']).' / '.$lead['ReportTrack']['lead_data']['zip']; ?></td>
												<td>-</td>
												</tr>
												<tr>
												<td>Residence Type</td>
												<td><?php echo strtoupper($lead['ReportTrack']['lead_data']['residencetype']); ?></td>
												<td>-</td>
												</tr>
												<tr>
												<td>Move In Date</td>
												<td><?php echo $lead['ReportTrack']['lead_data']['residentsincedate']; ?></td>
												<td>-</td>
												</tr>
												<tr>
												<td>Home Phone</td>
												<td>
												<?php echo $lead['ReportTrack']['lead_data']['homephone']; ?>
												<?php if($lead['ReportTrack']['lead_data']['phonetype'] != 'Mobile' && $lead['ReportTrack']['lead_data']['homephone'] == ''){echo $lead['ReportTrack']['lead_data']['primaryphone'];}?></td>
												<td>-</td>
												</tr>
												<tr>
												<td>Mobile Phone</td>
												<td>
												<?php if(isset($lead['ReportTrack']['lead_data']['mobilephone'])){echo $lead['ReportTrack']['lead_data']['mobilephone'];} ?>
												<?php if($lead['ReportTrack']['lead_data']['phonetype'] == 'Mobile' && $lead['ReportTrack']['lead_data']['mobilephone'] ==''){echo $lead['ReportTrack']['lead_data']['primaryphone'];}?></td>
												<td>-</td>
												</tr>
												<tr>
												<td>Secondary Phone</td>
												<td><?php if(isset($lead['ReportTrack']['lead_data']['secondaryphone'])) {echo $lead['ReportTrack']['lead_data']['secondaryphone'];} ?></td>
												<td>-</td>
												</tr>
												<tr>
												<td>Military</td>
												<td><?php echo ucfirst($lead['ReportTrack']['lead_data']['military']); ?></td>
												<td>-</td>
												</tr>
												<tr>
												<td>Birth Date</td>
												<td><?php echo $lead['ReportTrack']['lead_data']['dateofbirth']; ?></td>
												<td><?php if(isset($lead['ReportTrack']['lead_data']['age'])){echo $lead['ReportTrack']['lead_data']['age'];} ?></td>
												</tr>
												<td>Drivers License</td>
												<td><?php echo $lead['ReportTrack']['lead_data']['driverslicensenumber']; ?></td>
												<td><?php echo strtoupper($lead['ReportTrack']['lead_data']['driverslicensestate']); ?></td>
												</tr>
											</tbody>
										</table>
		                              </div>									
                                    </div><!-- tab-pane -->
                                  
                                    <div class="tab-pane" id="employment_data">
                                    	<div class="table-responsive">
										<table class="table table-hover mb30">
											<thead>
											<tr>
											<th>#</th>
											<th>&nbsp;</th>
											<th>&nbsp;</th>
											</tr>
											</thead>
											<tbody>
											<tr>
											<td>Employer Name</td>
											<td><?php echo ucwords($lead['ReportTrack']['lead_data']['employername']); ?></td>
											<td>-</td>
											</tr>
											<tr>
											<td>Employee Type</td>
											<td><?php echo ucfirst($lead['ReportTrack']['lead_data']['employeetype']); ?></td>
											<td>-</td>
											</tr>
											<tr>
											<td>Work Phone</td>
											<td><?php echo $lead['ReportTrack']['lead_data']['workphone']; ?></td>
											<td>-</td>
											</tr>
											<tr>
											<td>Employer Address</td>
											<td><?php echo ucwords($lead['ReportTrack']['lead_data']['employeraddress']); ?></td>
											<td>-</td>
											</tr>
											<tr>
											<td>City/State/Zip</td>
											<td><?php echo ucwords($lead['ReportTrack']['lead_data']['employercity']).' / '.strtoupper($lead['ReportTrack']['lead_data']['employerstate']).' / '.$lead['ReportTrack']['lead_data']['employerzip']; ?></td>
											<td>-</td>
											</tr>
											<tr>
											<td>Employment Date</td>
											<td><?php echo $lead['ReportTrack']['lead_data']['employmenttime']; ?></td>
											<td>-</td>
											</tr>
											</tbody>
										</table>
		                              </div>		
                                    </div><!-- tab-pane -->
                                  
                                    <div class="tab-pane" id="financial_data">
	                                    <div class="table-responsive">
											<table class="table table-hover mb30">
												<thead>
												<tr>
												<th>#</th>
												<th>&nbsp;</th>
												<th>&nbsp;</th>
												</tr>
												</thead>
												<tbody>
												<tr>
												<td>Monthly Income</td>
												<td>$<?php echo $lead['ReportTrack']['lead_data']['monthlynetincome']; ?></td>
												<td>-</td>
												</tr>
												<tr>
												<td>Bank Name</td>
												<td><?php echo ucwords($lead['ReportTrack']['lead_data']['bankname']); ?></td>
												<td>-</td>
												</tr>
												<tr>
												<td>Direct Deposit</td>
												<td><?php echo ucfirst($lead['ReportTrack']['lead_data']['directdeposit']); ?></td>
												<td>-</td>
												</tr>
												<tr>
												<td>Pay Frequency</td>
												<td><?php echo ucfirst($lead['ReportTrack']['lead_data']['payfrequency']); ?></td>
												<td>-</td>
												</tr>
												<tr>
												<td>Paydate 1</td>
												<td><?php echo $lead['ReportTrack']['lead_data']['paydate1']; ?></td>
												<td>-</td>
												</tr>
												<tr>
												<td>Paydate 2</td>
												<td><?php echo $lead['ReportTrack']['lead_data']['paydate2']; ?></td>
												<td>-</td>
												</tr>
												</tbody>
											</table>
			                              </div>		
                                    </div><!-- tab-pane -->
                                    
                                    <div class="tab-pane" id="coapp_data">
                                    	<div class="table-responsive">
                                    	<?php if(isset($lead['ReportTrack']['lead_data']['cofirstname'])){?>
											<table class="table table-hover mb30">
												<thead>
												<tr>
												<th>#</th>
												<th>&nbsp;</th>
												<th>&nbsp;</th>
												</tr>
												</thead>
												<tbody>
												<tr>
												<td>Co-App Name</td>
												<td><?php echo ucfirst($lead['ReportTrack']['lead_data']['cofirstname']).' '.ucfirst($lead['ReportTrack']['lead_data']['colastname']); ?></td>
												<td>-</td>
												</tr>
												<tr>
												<td>Co-App Address</td>
												<td><?php echo $lead['ReportTrack']['lead_data']['coaddress1'].' '.$lead['ReportTrack']['lead_data']['coaddress2']; ?></td>
												<td>-</td>
												</tr>
												<tr>
												<td>Co-App City/State/Zip</td>
												<td><?php echo ucwords($lead['ReportTrack']['lead_data']['cocity']).' / '.strtoupper($lead['ReportTrack']['lead_data']['costate']).' / '.$lead['ReportTrack']['lead_data']['cozip']; ?></td>
												<td>-</td>
												</tr>
												<tr>
												<td>Co-App Birth Date</td>
												<td><?php echo $lead['ReportTrack']['lead_data']['codateofbirth']; ?></td>
												<td>-</td>
												</tr>
												<tr>
												<td>Co-App Phone</td>
												<td><?php echo $lead['ReportTrack']['lead_data']['coprimaryphone']; ?></td>
												<td>-</td>
												</tr>
												<tr>
												<td>Co-App Employer Name</td>
												<td><?php echo ucwords($lead['ReportTrack']['lead_data']['coemployername']); ?></td>
												<td>-</td>
												</tr>
												<tr>
												<td>Co-App Employee Type</td>
												<td><?php echo ucfirst($lead['ReportTrack']['lead_data']['coemployeetype']); ?></td>
												<td>-</td>
												</tr>
												<tr>
												<td>Co-App Work Phone</td>
												<td><?php echo $lead['ReportTrack']['lead_data']['coworkphone']; ?></td>
												<td>-</td>
												</tr>
												<tr>
												<td>Co-App Employment Date</td>
												<td><?php echo $lead['ReportTrack']['lead_data']['coemploymenttime']; ?></td>
												<td>-</td>
												</tr>
												<tr>
												<td>Co-App Income</td>
												<td>$<?php echo $lead['ReportTrack']['lead_data']['comonthlynetincome']; ?></td>
												<td>-</td>
												</tr>
												</tbody>
											</table>
											<?php } ?>
			                              </div>
                                    </div><!-- tab-pane -->
                                    <div class="tab-pane" id="cake_data">
                                    <div class="table-responsive">
											<table class="table table-hover mb30">
												<thead>
												<tr>
												<th>#</th>
												<th>&nbsp;</th>
												<th>&nbsp;</th>
												</tr>
												</thead>
												<tbody>
												<tr>
												<td>Receivable</td>
												<td>$ <?php if(isset($lead['ReportTrack']['lead_data']['receivableamount'])){echo number_format($lead['ReportTrack']['lead_data']['receivableamount'],2,'.','');} ?></td>
												<td>-</td>
												</tr>
												<tr>
												<td>Payable</td>
												<td>$ <?php if(isset($lead['ReportTrack']['lead_data']['paidamount'])){echo number_format($lead['ReportTrack']['lead_data']['paidamount'],2,'.','');} ?></td>
												<td>-</td>
												</tr>
												<tr>
												<td>House Margin</td>
												<td>
												<?php 
												// This was added because we cahnged margin amounts stored in Lead Track after 9/30.
												$fixdate = date('mdY', $lead['ReportTrack']['lead_created']->sec);
												if($fixdate < 9302015){
													echo (100)-($lead['ReportTrack']['lead_data']['margin']*100);
												}else{											
													if(isset($lead['ReportTrack']['lead_data']['margin'])){echo ($lead['ReportTrack']['lead_data']['margin']*100);} 
												}
												?>
												%</td>
												<td>-</td>
												</tr>
												<tr>
												<td>Profit</td>
												<td>$ <?php if(isset($lead['ReportTrack']['lead_data']['marginamount'])){echo number_format($lead['ReportTrack']['lead_data']['marginamount'],2,'.','');} ?></td>
												<td>-</td>
												</tr>
												</tbody>
											</table>
			                              </div>
                                    </div><!-- tab-pane -->
                                    <div class="tab-pane" id="redirect_data">
                                    <div class="table-responsive">
											<table class="table table-hover mb30">
												<thead>
												<tr>
												<th>Redirect Type</th>
												<th>Url</th>
												<th>Redirected</th>
												</tr>
												</thead>
												<tbody>
												<?php 
												if($lead['ReportTrack']['lead_data']['calltype']=='internal'){ ?>
												<tr>
												<td>Affiliate Redirect URL</td>
												<td><?php echo '<a href="http://service.leadstudio.com/rinternal/'.$lead['ReportTrack']['lead_data']['redirect_urls']['hash_url'].'/'.base64_encode($lead['ReportTrack']['track_id']).'" target="_blank">Link</a>';?></td>
												<td><?php if(isset($lead['ReportTrack']['lead_data']['redirect_urls']['redirected']) && $lead['ReportTrack']['lead_data']['redirect_urls']['redirected'] == "1"){echo 'True';}else{echo 'False';} ?></td>
												</tr>
												<?php }else{ ?>
												<tr>
												<td>Vendor Redirect URL</td>
												<td><?php echo '<a href="http://service.leadstudio.com/rinternal/'.$lead['ReportTrack']['lead_data']['redirect_urls']['hash_url'].'/'.base64_encode($lead['ReportTrack']['track_id']).'" target="_blank">Link</a>';?></td>
												<td><?php if(isset($lead['ReportTrack']['lead_data']['redirect_urls']['redirected']) && $lead['ReportTrack']['lead_data']['redirect_urls']['redirected'] == "1"){echo 'True';}else{echo 'False';} ?></td>
												</tr>
												<?php } ?>
												</tbody>
											</table>
			                              </div>
                                    </div>
                                    <!-- tab-pane -->
                                    <div class="tab-pane" id="disposition_data">
                                    	<div class="table-responsive">
											<table class="table table-hover mb30">
												<thead>
												<tr>
												<th>Buyer</th>
												<th>Status</th>
												<th>Date Sent</th>
												<th>Date Received</th>
												<th>Response Time (sec)</th>
												</tr>
												</thead>
												<tbody>
												<?php 
												$total_time = 0;
												$order = 0;
												foreach($lead['ReportTrack']['disposition'] AS $id=>$disposition){
													foreach($disposition as $stat=>$buyer){
														$d1 = strtotime($buyer['sent_date']);
														$d2 = strtotime($buyer['received_date']);
														$total_time += ($d2-$d1);
															if($id == 'declined'){$flag=''; $icon="";}
															if($id == 'error'){$flag='class="alert-danger"'; $icon="<i class=\"fa fa-exclamation-triangle\"></i> ";}
															if($id == 'approved'){$flag='class="alert-success"'; $icon="";}
															if($id == 'success'){$flag='class="alert-success"'; $icon="";}
															if($id == 'duplicate'){$flag='class="alert-warning"'; $icon="<i class=\"fa fa-retweet\"></i> ";}
															if($id == 'unknown response'){$flag='class="alert-warning"'; $icon="<i class=\"fa fa-question\"></i> ";}
														$tree[$order]['id'] = $id;
														$tree[$order]['d1'] = $d1;
														$tree[$order]['d2'] = $d2;
														$tree[$order]['name'] = $buyer['buyer_contract_name'];
														$tree[$order]['flag'] = $flag;
														$tree[$order]['icon'] = $icon;
														
														$order++;
													}
												}
												
												//Sort the dispositions by sent/recieved dates
												uasort($tree, function($a,$b){
													$c = $a['d1'] - $b['d1'];
													$c .= $a['d2'] - $b['d2'];
													return $c;
												});
												foreach($tree as $disp){
													echo '<tr '.$disp['flag'].'>
														<td>'.ucfirst($disp['name']).'</td>
														<td>'.$disp['icon'].ucfirst($disp['id']).'</td>
														<td>'.date('m-d-Y H:i:s', $disp['d1']).'</td>
														<td>'.date('m-d-Y H:i:s', $disp['d2']).'</td>
														<td>'.($disp['d2']-$disp['d1']).'</td>
														</tr>';
												}
												?>
												<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td><strong>Total: <?php echo $total_time; ?> Seconds</strong></td></tr>
											</tbody>
										</table>
		                              </div>									
                                    </div><!-- tab-pane -->
                                </div><!-- tab-content -->
			</div>
			<div class="col-md-4">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<!-- panel-btns -->
						<h3 class="panel-title" id="overview-title">Details</h3>
					</div>
					<div class="panel-body">
						<strong>Affiliate</strong>
						<div><span class="text-info"><?php echo $lead['Cake']['campaigns']['campaign']['affiliate']['affiliate_name']; ?> <small>(<?php echo $lead['Cake']['campaigns']['campaign']['affiliate']['affiliate_id']; ?>)</small></span></div>
						<strong>Offer</strong>
						<div><span class="text-info"><?php echo $lead['Cake']['campaigns']['campaign']['offer']['offer_name']; ?> <small>(<?php echo $lead['Cake']['campaigns']['campaign']['offer']['offer_id']; ?>)</small></span></div>
						<strong>Contract</strong>
						<div><span class="text-info"><?php echo $lead['Cake']['campaigns']['campaign']['offer_contract']['price_format']['price_format_name']; ?> </span></div>		
						<?php if(array_key_exists('errors', $lead['ReportTrack']['lead_data'])) {?>
							<strong>Errors</strong>
							<?php foreach($lead['ReportTrack']['lead_data']['errors'] as $error_id=>$error_msg){?>
								<div class="alert-danger">&nbsp;<i class="fa fa-exclamation-triangle"></i> <span><?php echo ($error_id == "201") ? urldecode($error_msg) : $error_msg; ?></span></div>
							<?php } ?>								
						<?php } ?>
						<?php if(array_key_exists('altered', $lead['ReportTrack']['lead_data'])) {?>
						<strong>Notifications</strong>
							<?php foreach($lead['ReportTrack']['lead_data']['altered'] as $altered_id=>$altered_msg){?>
								<div class="alert-info">&nbsp;<i class="fa fa-info-circle"></i> <span><?php echo ucwords($altered_msg); ?></span></div>
							<?php } ?>			
						<?php } ?>
					</div>
					<!-- panel-body -->
					<div class="panel-footer">
						<?php if(array_key_exists('receivableamount', $lead['ReportTrack']['lead_data'])) { ?>
						<div class="col-md-6">
							<div class="btn-group mr5">
								<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Adjust Lead <span class="caret"></span></button>
								<ul class="dropdown-menu dropdown-menu-primary" role="menu">
		                        	<li><a id="change_receivable" style="cursor:pointer;">Change Receivable</a></li>
		                            <li><a id="change_payable" style="cursor:pointer;">Change Payable</a></li>
		                            <li class="divider"></li>
		                            <li><a id="reject_lead" style="cursor:pointer;">Reject Lead</a></li>
								</ul>
							</div>
						</div>
						<div class="col-md-6">
							<div class="input-group" id="change_receivable_field" style="display:none;">
								<span class="input-group-addon">$</span>
								<input id="lead_receivable" type="text" value="<?php echo number_format($lead['ReportTrack']['lead_data']['receivableamount'],2,'.',''); ?>" placeholder="Receivable" class="form-control">
							</div>
							<div class="input-group" id="change_payable_field" style="display:none;">
								<span class="input-group-addon">$</span>
								<input id="lead_payable" data-lead-id="<?php if(isset($lead['ReportTrack']['lead_id'])){echo $lead['ReportTrack']['lead_id'];} ?>" data-offer-id="<?php if(isset($lead['ReportTrack']['offer_id'])){echo $lead['ReportTrack']['offer_id'];} ?>" data-track-id="<?php if(isset($lead['ReportTrack']['track_id'])){echo $lead['ReportTrack']['track_id'];} ?>" data-vertical-id="<?php echo $vertical_id; ?>" type="text" value="<?php echo number_format($lead['ReportTrack']['lead_data']['paidamount'],2,'.',''); ?>" placeholder="Receivable" class="form-control">
							</div>
						</div>
						<?php } ?>
					</div><!-- panel-footer -->
				</div>
				<!-- panel -->
			</div>
		</div>
		<!-- row -->
	</div>
	<!-- contentpanel -->
</div>
<!-- mainpanel -->

<!-- modals -->
<div class="modal fade confirm-reject" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="panelt panel-danger">
				<div class="panel-heading">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
					<h3 class="panel-title">Reject Lead Confirmation</h3>
				</div>
				<div class="panel-body">
					<p>This will mark the lead <strong><?php if(isset($lead['ReportTrack']['lead_id'])){echo $lead['ReportTrack']['lead_id'];} ?></strong> rejected, remove the payable and receivable and <strong>cannot be undone!</strong></p>
					<p>Are you sure you want to proceed with this change?</p>
				</div>
				<div class="panel-footer">
						<button data-lead-id="<?php if(isset($lead['ReportTrack']['lead_id'])){echo $lead['ReportTrack']['lead_id'];} ?>" data-offer-id="<?php if(isset($lead['ReportTrack']['offer_id'])){echo $lead['ReportTrack']['offer_id'];} ?>" data-track-id="<?php if(isset($lead['ReportTrack']['track_id'])){echo $lead['ReportTrack']['track_id'];} ?>" id="leadConfirmReject" class="btn btn-danger">Yes</button>
						<button data-dismiss="modal" aria-hidden="true" class="btn btn-dark">No</button>
				</div><!-- panel-footer -->
			</div>
		</div>
	</div>
</div>