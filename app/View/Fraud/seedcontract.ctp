<div class="mainpanel">
	<div class="pageheader">
		<div class="media">
			<div class="pageicon pull-left" style="padding-top: 3px; padding-left: 15px;">
				<i class="fa fa-tree"></i>
			</div>
			<div class="media-body">
				<ul class="breadcrumb">
					<li><a href="/dashboard"><i class="glyphicon glyphicon-home"></i></a></li>
					<li><a href="/dashboard">Fraud</a></li>
					<li>Seed Contract</li>
				</ul>
				<h4>Seed Contract</h4>
			</div>
		</div>
		<!-- media -->
	</div>
	<!-- pageheader -->

	<div class="contentpanel">
		<?php echo $this->Session->flash(); ?>
		<div class="row">
		
			<div class="col-md-9">
				<div class="panel panel-primary" id="">
					<div class="panel-heading">
						
						<h3 class="panel-title">Post Details</h3>
					</div>
					<div id="postLoader" style="display:none"><img src="/images/loaders/sand_small.svg"></div>
					
					<div id="">
						<div class="panel-body" id = 'testcontractpost-mainpanel'>
							<div class="col-sm-8">
								<div class="table-responsive">
									<table width = "100%" id = "testcontract-fields-table" border=0>
										<thead>
											
										</thead>
										<tbody>	
										<tr align="center">
											<td width="50%">
												<table width="100%" border=0 id = "testcontract-table-left" cellpadding="5">
													<thead>
														<tr style = "">
															<th style="text-align:right;padding-right:5px;">Field</th>
															<th style="text-align:center">Value</th>
														</tr>
													</thead>
													<tbody>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">email</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "" id="email"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">pay_date_1</td>
																		<td style="text-align:right;padding-right:5px" width="50%">
																			<div class="input-group input-group-sm">
																				<input type="text" placeholder="Paydate 1" class="form-control" id="pay_date_1">
																				<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
																			</div>
																		</td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">pay_date_3</td>
																		<td style="text-align:right;padding-right:5px" width="50%">
																			<div class="input-group input-group-sm">
																				<input type="text" placeholder="Paydate 3" class="form-control" id="pay_date_3">
																				<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
																			</div>	
																		</td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">loan_amount</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "1000" id="loan_amount"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">first_name</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "Cljoe" id="first_name"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">address_1</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "1 vine st" id="address_1"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">state</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "GA" id="state"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">loan_purpose</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "auto" id="loan_purpose"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">ip_address</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "74.95.28.129" id="ip_address"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">credit_rating</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "excellent" id="credit_rating"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">active_military</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "false" id="active_military"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">phone_type</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "Home" id="phone_type"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">date_of_birth</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "05/27/1977" id="date_of_birth"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">phone_primary</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "7709652431" id="phone_primary"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">residence_years_part</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "1" id="residence_years_part"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">emp_months_part</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "5" id="emp_months_part"></td>
																   	 </tr>
														
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">emp_name</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "ClickMed" id="emp_name"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">emp_city</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "Greenville" id="emp_city"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">emp_zip</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "30331" id="emp_zip"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">bank_account_type</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "checking" id="bank_account_type"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">bank_name</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "Bank America" id="bank_name"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">bank_months_at</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "60" id="bank_months_at"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">age</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "40" id="age"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">monthly_rent_mortgage</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "500" id="monthly_rent_mortgage"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">dl_state</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "GA" id="dl_state"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">accept_terms</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "true" id="accept_terms"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">citizen</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "true" id="citizen"></td>
																   	 </tr>
														
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">offer_id</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "36" id="offer_id"></td>
																   	 </tr>
													</tbody>
												</table>
												
											</td>
										
											<td width="50%">
												<table width="100%" border=0 id = "testcontract-table-right">
													<thead>
														<tr style="">
															<th style="text-align:right;padding-right:5px">Field</th>
															<th style="text-align:center">Value</th>
														</tr>
													</thead>
													<tbody>
														<!--<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">app_type</td>
																		<td style="text-align:right;padding-right:5px" width="50%">
																			<select id="app_type" data-placeholder="Choose One" style = "width:200px">
																				<option value = 'payday' Selected>Payday</option>
																				<option value = 'personalloan'>Personalloan</option>
																				<option value = 'installment'>Installment</option>
																				<option value = 'vendor_payday'>Vendor Payday</option>
																				<option value = 'vendor_personalloan'>Vendor Personalloan</option>
																				<option value = 'vendor_installment'>Vendor Installment</option>
																				<option value = 'vendor_lowtier'>Vendor Lowtier</option>
																				<option value = 'vendor_lowtierlong'>Vendor Lowtier Long</option>
																			</select>
																  </tr>-->
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">pay_date_2</td>
																		<td style="text-align:right;padding-right:5px" width="50%">
																			<div class="input-group input-group-sm">
																				<input type="text" placeholder="Paydate 2" class="form-control" id="pay_date_2">
																				<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
																			</div>
																		</td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">pay_frequency</td>
																		<td style="text-align:right;padding-right:5px" width="50%">
																			<select id="pay_frequency" data-placeholder="Choose One" style = "width:200px">
																				<option value = 'weekly' Selected>Weekly</option>
																				<option value = 'monthly'>Monthly</option>
																				<option value = 'bi-weekly'>Every 2 Weeks</option>
																				<option value = 'semi-monthly'>Twice a Month</option>
																			</select>
																			
																		</td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">loan_amount_personal</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "1500" id="loan_amount_personal"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">last_name</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "Plsmith" id="last_name"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">city</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "Applewood" id="city"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">zip</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "30106" id="zip"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">address_2</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "suite 5" id="address_2"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">residence_type</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "ownwmtg" id="residence_type"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">income_source</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "employed" id="income_source"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">monthly_income</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "3000" id="monthly_income"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">ssn</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "437812432" id="ssn"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">phone_mobile</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "7709876542" id="phone_mobile"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">phone_work</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "7708752324" id="phone_work"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">client_url</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "http://leadstudio.com" id="client_url"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">residence_months_part</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "2" id="residence_months_part"></td>
																   	 </tr>
														
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">direct_deposit</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "true" id="direct_deposit"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">emp_address</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "4 Spring st" id="emp_address"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">emp_state</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "GA" id="emp_state"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">emp_years_part</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "5" id="emp_years_part"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">bank_routing</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "000061052" id="bank_routing"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">bank_number</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "42423432" id="bank_number"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">emp_months</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "36" id="emp_months"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">residence_months</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "60" id="residence_months"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">dl_number</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "234234234" id="dl_number"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">opt_in</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "true" id="opt_in"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">country</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "US" id="country"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">opt_consent</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "true" id="opt_consent"></td>
																   	 </tr>
														<tr style = "">
																		<td style="text-align:right;padding-right:5px" width="50%">campaign_id</td>
																		<td style="text-align:right;padding-right:5px" width="50%"><input type="text" class="form-control" value = "126" id="campaign_id"></td>
																   	 </tr>
														


													</tbody>
												</table>
											</td>
										</tr>
										
										
										
										</tbody>
									</table>
								</div>
						
							</div>
							<div class="col-sm-4">
									<div id = "testcontract-filter-div">
									
									</div>
							</div>
				
						<!-- panel-body -->
						<div class="panel-footer">
						</div>
						<!-- panel-footer -->
					</div>
				</div>
			</div>
		
			<!-- panel -->
			</div>
			
			
			
			
			<div class="col-md-3">
				<div class="panel panel-primary">
					<div class="panel-heading">
						
						<h3 class="panel-title">Find A Contract</h3>
					</div>
					<div id="vendor-search-panel">
						<div class="panel-body">
	
							<div class="form-group">
								<div class="col-sm-12">
									<label for="buyer_name">Select A Buyer</label><br>
									  <select id="select-buyer-testcontractpost" data-placeholder="Choose One" class="width300">
										<?php 
										echo '<option value="" Selected>Select A Buyer</option.>';
										foreach($buyer_list as $key=>$buyer){										
											echo '<option value="'.$key.'">'.$buyer.'</option.>';
										}?>
									</select>
									 
								</div>
							</div>
							
							<div class="form-group" id = "contract-select-group">
								<div class="col-sm-12">
									<label for="buyer_name">Select A Contract</label><br>
									  <select id="select-contract-testcontractpost" data-placeholder="Choose One" class="width300">
										
									</select>
									 
								</div>
							</div>
							<div class="form-group" id = "contract-wait-group">
								<div class="col-sm-12">
									<div><img src="/images/loaders/sand_small.svg"></div>
								</div>
							</div>
				
						<!-- panel-body -->
						<div class="panel-footer">
							<button class="btn btn-primary" id="postdetails-testcontractpost">Get Post Details</button> 
							<button class="btn btn-success pull-left" id="send-testcontractpost">Send Test</button>
							<button class="btn btn-warning pull-right" id="clear-testcontractpost">Start Over</button> 
							
		
						</div>
						<!-- panel-footer -->
					</div>
				</div>
			</div>
		
			<!-- panel -->
			</div>
			
		
			
		</div>
		<!-- row -->
	</div>
	<!-- contentpanel -->
</div>
<!-- mainpanel -->


<!-- Lead Test Modal -->
	<div id="results-contract-modal" class="modal fade in" data-backdrop="static" data-keyboard="false" aria-hidden="false" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" style="display: none;">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					
				</div>
				<div class="modal-body"> 
					<div id = 'results-contract-wait'><img src="/images/loaders/sand_small.svg"> <h2>Attention! The lead is processing.....</h2></div>
					<div id = 'results-contract-response' style = "word-break:break-all"></div>
					
				</div>
				<div class="modal-footer">
					<button id = "results-contract-done" class="btn btn-default"  type="button">Done</button>
				</div>
			</div>
		</div>
	</div>