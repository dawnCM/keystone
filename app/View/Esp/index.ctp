<div class="mainpanel">
	<div class="pageheader">
		<div class="media">
			<div class="pageicon pull-left">
				<i class="fa fa-envelope"></i>
			</div>
			<div class="media-body">
				<ul class="breadcrumb">
					<li><a href="/dashboard"><i class="glyphicon glyphicon-home"></i></a></li>
					<li><a href="/dashboard">List Management</a></li>
					<li>ESP Management</li>
				</ul>
				<h4>ESP Management</h4>
			</div>
		</div>
		<!-- media -->
	</div>
	<!-- pageheader -->
	
	<div id="ContentLoader"  style="display:none; padding-left: 10px">
		<div class="row">
			<div class="col-md-12">&nbsp;</div>	
		</div>
							
		<div class="row">
			<div class="col-md-2"><img src="/images/loaders/sand_small.svg"></div>
			<div class="col-md-8">&nbsp;</div>
		</div>
	</div>
	
	<div class="contentpanel" id="content">
		<?php echo $this->Session->flash(); ?>
		
		
		<div style="display:none;" id = "savedsuccess" >
			<div class = "row">
				<div class="col-lg-12">
					<div style = "font-size: 16px" class="alert alert-success" role="alert">The ESP was successfully saved!</div>
				</div>
			</div>
			<div class = "row" >
				<div class="col-sm-12">&nbsp;</div>
			</div>	
		</div>
		<div style="display:none;" id = "savederror" >
			<div class = "row">
				<div class="col-lg-12">
					<div style = "font-size: 16px" class="alert alert-danger" role="alert">The ESP was Not saved!  There are errors in the ESP setup.</div>
				</div>
			</div>
			<div class = "row" >
				<div class="col-sm-12">&nbsp;</div>
			</div>	
		</div>
		<div class = "row">
			
			<div class="col-md-4">
				<div class="panel panel-primary" id = "espbox">
					<div class="panel-heading">
						<!-- panel-btns -->
						<h3 class="panel-title">Select an ESP to Modify</h3>
					</div>
					<div class="panel-body">
						
						<div class="row">
							<div class="form-group col-md-12">
								<select id="select-esp" data-placeholder="Choose One" class="width300">
									<?php 
									foreach($esp_list as $key=>$esp){										
										echo '<option value="'.$key.'">'.$esp.'</option.>';
									}?>
								</select>
							</div>
						</div>
						<!-- form-row-1 -->
					</div>
					<!-- panel-body -->
					<div id="footerdisplay" class="panel-footer">
						<button class="btn btn-primary" id="load-esp">Load ESP</button>
						<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
						<button class="btn btn-success" id="create-esp">Create A New ESP</button>
					</div><!-- panel-footer -->
				</div>
				<!-- panel -->
			</div>
	
		</div>
		<!-- row -->
		
		
		
		
		
		<div class = "row">
			<div class="col-md-12" id="espunit">
					<ul class="nav nav-tabs nav-primary">
						<li class="active">
							<a data-toggle="tab" href="#espinfo">
								<strong>ESP Information</strong>
							</a>
						</li>
						
						<li>
							<a data-toggle="tab" href="#esppostout">
								<strong>Post Information</strong>
							</a>
						</li>
						
						<li>
							<a data-toggle="tab" href="#espfilters">
								<strong>Filters</strong>
							</a>
						</li>
						
						<li>
							<a data-toggle="tab" href="#apiesppostout">
								<strong>Add To Suppression API</strong>
							</a>
						</li>
						
						<li>
							<a data-toggle="tab" href="#blacklistesppostout">
								<strong>Pull BlackList API</strong>
							</a>
						</li>
				
					</ul>
					
				<div class="tab-content tab-content-primary mb30" >
					<!-- Tab Esp Information -->
					<div id="espinfo" class="tab-pane active">
						
						<div class="panel-body">
							<div class="col-md-4">
								<div class="form-group" id = "formgroupid">
									<label class="col-md-4 control-label">ESP ID:</label>
									<div class="col-md-8">
										<span id="espid"></span>
									</div>
								</div>
								<div class="form-group">
										
										<div class="col-md-6"  style = "">
											&nbsp;
										</div>
									</div>
								<div class="form-group" id = "formgroupname">
									<label class="col-md-4 control-label">Name:</label>
									<div class="col-md-8">
										<input class="form-control" type="text" id="name" name="name">
									</div>
								</div>
								<div class="form-group">
										
										<div class="col-md-6"  style = "">
											&nbsp;
										</div>
									</div>
								<div class="form-group">
									<label class="col-md-4 control-label">Active Status:</label>
									<div class="col-md-8 control-label">
										<div class="col-md-8">
											
											
											
											<div class="rdio rdio-success">
											<input id="espstatus1" type="radio" value="1" name="espstatus">
											<label for="espstatus1">Active</label>
											</div>
											
											<div class="rdio rdio-warning">
											<input id="espstatus2" type="radio" value="3" name="espstatus">
											<label for="espstatus2">Pending</label>
											</div>
											
											<div class="rdio rdio-danger">
											<input id="espstatus3" type="radio" checked="checked" value="2" name="espstatus">
											<label for="espstatus3">Inactive</label>
											</div>
								
										</div>
										
									</div>
								</div>
							</div>
						</div>						
					</div>
					<!--End Tab Esp Information -->
					
					
					<!-- Tab Post Information -->
						<div id="esppostout" class="tab-pane">
							
							<div class="panel-body">
								<div class="col-md-12">
									<div class="form-group">
										<table border=0 width="30%">
											<tr>
												<td><button type="button" class="btn btn-warning pull-left" data-toggle="modal" id = "testposter" data-target="#testmodal">Test Post</button></td>
											</tr>
										</table>
									</div>
								</div>
								<div class="col-md-8">
									<div class="form-group">
										<label class="col-md-4 control-label">Request Type:</label>
										<div class="form-group col-md-8">
											<select id="requesttype" data-placeholder="Choose One" class="width300">
												<option value="">--CHOOSE--</option>
												<option value="POST">POST</option>
												<option value = "GET">GET</option>
											</select>
										</div>
									</div>
									<div class="form-group" id = "formgrouprequesturl">
										<label class="col-md-4 control-label">Request URL:</label>
										<div class="col-md-8">
											<input class="form-control" type="text" name="requesturl" id="requesturl">
										</div>
									</div>
									<div class="form-group">
										
										<div class="col-md-6"  style = "">
											&nbsp;
										</div>
									</div>
									<div class="form-group" id = "formgroupheader">
										<label class="col-md-4 control-label">Headers:</label>
										
										<div class="col-md-6" id "headertableblock" style = "">
											<div class="table-responsive">
												<table border="0" id = "headertable" width="100%" cellspacing="5" cellpadding="5">
													<thead>
														<tr>
															<th>Header Field</th>
															<th>Header Value</th>
															<th>&nbsp;</th>
															
														</tr>
													</thead>
													<tbody>
														
													</tbody>
												</table>
											</div>
											
											
										</div>
										<div class="col-md-2" style = "padding-top: 5px">
											<span id = "addheader" style = "cursor:pointer" class="label label-primary">Add A Header</span>
										</div>
									</div>
									<div class="form-group">
										
										<div class="col-md-6"  style = "">
											&nbsp;
										</div>
									</div>
									
									<div class="form-group" id = "formgroupbasicauth">
										<label class="col-md-4 control-label">Basic Authentication:</label>
										
										<div class="col-md-6" id "basicauthtableblock" style = "">
											<div class="table-responsive">
												<table border="0" id = "basicauthtable" width="100%" cellspacing="5" cellpadding="5">
													<thead>
														<tr>
															<th>Username</th>
															<th>Password</th>
															<th>&nbsp;</th>
															
														</tr>
													</thead>
													<tbody>
														
													</tbody>
												</table>
											</div>
											
											
										</div>
										<div class="col-md-2" style = "padding-top: 5px">
											<span id = "addbasicauth" style = "cursor:pointer" class="label label-primary">Add Basic Authentication</span>
										</div>
									</div>
									
									<div class="form-group">
										
										<div class="col-md-6"  style = "">
											&nbsp;
										</div>
									</div>
									
									<div class="form-group" id = "formgroupacceptstring">
										<label class="col-md-4 control-label">Success String:</label>
										<div class="col-md-4">
											<input class="form-control" type="text" name="acceptstring" id="acceptstring">
										</div>
									</div>
									
									<div class="form-group">
										
										<div class="col-md-6"  style = "">
											&nbsp;
										</div>
									</div>
									
									<div class="form-group" id = "formgroupacceptcode">
										<label class="col-md-4 control-label">Success HTTP Code:</label>
										<div class="col-md-4">
											<input class="form-control" type="text" name="acceptcode" id="acceptcode">
										</div>
									</div>
									
									<div class="form-group">
										
										<div class="col-md-6"  style = "">
											&nbsp;
										</div>
									</div>
									
									<div class="form-group" id="formgroupcustomfields">
										<label class="col-md-4 control-label">Custom Fields:</label>
										
										<div class="col-md-6" id "customfieldtableblock" style = "">
											<div class="table-responsive">
												<table border="0" id = "customfieldtable" width="100%" cellspacing="5" cellpadding="5">
													<thead>
														<tr>
															<th>Field Name</th>
															<th>Field Value</th>
															<th>&nbsp;</th>
															
														</tr>
													</thead>
													<tbody>
														
													</tbody>
												</table>
											</div>
											
											
										</div>
										<div class="col-md-2" style = "padding-top: 5px">
											<span id = "addcustomfield" style = "cursor:pointer" class="label label-primary">Add A Custom Field</span>
										</div>
									</div>
									
									<div class="form-group">
										
										<div class="col-md-6"  style = "">
											&nbsp;
										</div>
									</div>
									
									<div class="form-group" id="formgroupformatdatefields">
										<label class="col-md-4 control-label">Format Date Fields:</label>
										
										<div class="col-md-6" id "formatdateblock" style = "">
											<div class="table-responsive">
												<table border="0" id = "formatdatetable" width="100%" cellspacing="5" cellpadding="5">
													<thead>
														<tr>
															<th>Field Name</th>
															<th>System Format</th>
															<th>New Format</th>
															
														</tr>
													</thead>
													<tbody>
														
													</tbody>
												</table>
											</div>
											
											
										</div>
										<div class="col-md-2" id = "formatdate" style = "padding-top: 5px">
											<span id = "addadateformat" style = "cursor:pointer" class="label label-primary">Add A Date Format</span>
										</div>
									</div>
									
									<div class="form-group">
										
										<div class="col-md-6"  style = "">
											&nbsp;
										</div>
									</div>
									
									<div class="form-group" id="formgrouptemplate">
										<label class="col-md-4 control-label">JSON OR XML TEMPLATE?:</label>
										<div class="col-md-8">
											<select id="templatedropdown" name="templatedropdown" data-placeholder="Choose One" style="width:100px" >
												<option value = "" selected>Choose</option>
												<option value="Yes">Yes</option>
												<option value = "No">No</option>
											</select>
										</div>
									</div>
									
									<div class="form-group">
										
										<div class="col-md-6"  style = "">
											&nbsp;
										</div>
									</div>		
								
									<div class="form-group" id= "postbuilder">
										<label class="col-md-4 control-label">Request Builder:</label>
										<div class="col-md-8">
									<table border="0" style = "font:Arial, Helvetica, sans-serif;color:black" class = "table table-dark mb30 table-striped" id = "buildertable" width="100%" cellspacing="5" cellpadding="5">
													<thead>
														<tr >
															<th style = "text-align: center">Fields</th>
															<th style = "text-align: center">Post Field Name</th>
															<th style = "text-align: center">Field Type</th>
															
														</tr>
													</thead>
													<tbody>
														
													</tbody>
												</table>
									</div>
									
								</div>			
												
									
								<div class="form-group" id= "templatebuilder">
										<label class="col-md-4 control-label">Request Builder:</label>
										<div class="col-md-8">
											
											<div class = "row">
												<div class = "col-md-7">
													<div class="panel panel-default">
													  <div class="panel-heading">
													    <h3 class="panel-title">Available <b>System</b> Fields</h3>
													  </div>
													  <div class="panel-body">
													    <table style = "" id = "systemfieldsdisplay" width = "100%">
													    	<thead>
													    		
													    	</thead>
													    	<tbody>
													    		
													    	</tbody>
													    </table>
													  </div>
													</div>
												</div>
												
												
												<div class = "col-md-5">
													<div class="panel panel-default">
													  <div class="panel-heading">
													    <h3 class="panel-title">Available <b>Custom</b> Fields</h3>
													  </div>
													  <div class="panel-body">
													    <table id = "customfieldsdisplay" width = "100%">
													    	<thead>
													    		
													    	</thead>
													    	<tbody>
													    		
													    	</tbody>
													    </table>
													  </div>
													</div>
												</div>
												
											</div>
											
											<textarea name = "postarea" id="postarea" class="form-control" rows="5" style="height: 300px;"></textarea>
											
									</div>
									
								</div>		
									
								</div>
							</div>
						</div>
						<!-- END Tab Post Information -->
						
						<!-- Tab Filters Information -->
						<div id="espfilters" class="tab-pane">
						
							<div class="panel-body">
								<div class="col-md-8">
									<div class="form-group" id="formgroupfilteremail">
										<label class="col-md-2 control-label">Email Segmentation:</label>
										<div class="col-md-4">
											<select id="filteremaildropdown" data-placeholder="Choose One" style="width:250px" >
												<option value = "" selected>No Filter</option>
												<option value="1">EQUALS</option>
												<option value = "2">GREATER THAN</option>
												<option value="3">LESS THAN</option>
												<option value = "4">GREATER THAN OR EQUAL TO</option>
												<option value="5">LESS THAN OR EQUAL TO</option>
												<option value = "6">NOT EQUAL TO</option>
												<option value="7">IN LIST (Comma Separated)</option>
												<option value = "8">NOT IN LIST (Comma Separated)</option>
												<option value="9">VALUE CONTAINS</option>
												<option value = "10">VALUE DOES NOT CONTAIN</option>
											</select>
										</div>
										<div class="col-md-6">
											<input class="form-control" type="text" name = "filteremailarea" id="filteremailarea" class="form-control" >
										</div>
									</div>
									<div class="form-group">
											
										<div class="col-md-6"  style = "">
											&nbsp;
										</div>
									</div>	
									
									<div class="form-group" id="formgroupfiltercampaign">
										<label class="col-md-2 control-label">Offer ID:</label>
										<div class="col-md-4">
											<select id="filtercampaigndropdown" data-placeholder="Choose One" style="width:250px" >
												<option value = "" selected>No Filter</option>
												<option value="1">EQUALS</option>
												<option value = "2">GREATER THAN</option>
												<option value="3">LESS THAN</option>
												<option value = "4">GREATER THAN OR EQUAL TO</option>
												<option value="5">LESS THAN OR EQUAL TO</option>
												<option value = "6">NOT EQUAL TO</option>
												<option value="7">IN LIST (Comma Separated)</option>
												<option value = "8">NOT IN LIST (Comma Separated)</option>
												<option value="9">VALUE CONTAINS</option>
												<option value = "10">VALUE DOES NOT CONTAIN</option>
											</select>
										</div>
										<div class="col-md-6">
											<input class="form-control" type="text" name = "filtercampaignarea" id="filtercampaignarea" class="form-control" >
										</div>
									</div>
									
									<div class="form-group">
											
										<div class="col-md-6"  style = "">
											&nbsp;
										</div>
									</div>	
									
									<div class="form-group" id="formgroupfilterhygiene">
										<label class="col-md-2 control-label">Hygiene Score:</label>
										<div class="col-md-4">
											<select id="filterhygienedropdown" data-placeholder="Choose One" style="width:250px" >
												<option value = "" selected>No Filter</option>
												<option value="1">EQUALS</option>
												<option value = "2">GREATER THAN</option>
												<option value="3">LESS THAN</option>
												<option value = "4">GREATER THAN OR EQUAL TO</option>
												<option value="5">LESS THAN OR EQUAL TO</option>
												<option value = "6">NOT EQUAL TO</option>
												<option value="7">IN LIST (Comma Separated)</option>
												<option value = "8">NOT IN LIST (Comma Separated)</option>
												<option value="9">VALUE CONTAINS</option>
												<option value = "10">VALUE DOES NOT CONTAIN</option>
											</select>
										</div>
										<div class="col-md-6">
											<input class="form-control" type="text" name = "filterhygienearea" id="filterhygienearea" class="form-control" >
										</div>
									</div>	
										
										
									<div class="form-group">
											
										<div class="col-md-6"  style = "">
											&nbsp;
										</div>
									</div>	
								</div>
						</div>
					</div>
					<!--End Tab Filter Information -->		
						
				<!-- Tab Esp Subscriber delete -->
					<div id="apiesppostout" class="tab-pane">
						
							<div class="panel-body">
								<div class="col-md-12">
									<div class="form-group">
										<table border=0 width="30%">
											<tr>
												<td><button type="button" class="btn btn-warning pull-left" data-toggle="modal" id = "apitestposter" data-target="#apitestmodal">Test Post</button></td>
											</tr>
											
										</table>
									</div>
								</div>
								<div class="col-md-8">
									<div class="form-group" id = "apiformgrouprequesttype">
										<label class="col-md-4 control-label">Request Type:</label>
										<div class="form-group col-md-8">
											<select id="apirequesttype" data-placeholder="Choose One" class="width300">
												<option value="">--CHOOSE--</option>
												<option value="POST">POST</option>
												<option value = "GET">GET</option>
												<option value = "DELETE">DELETE</option>
											</select>
										</div>
									</div>
									<div class="form-group" id = "apiformgrouprequesturl">
										<label class="col-md-4 control-label">Request URL:</label>
										<div class="col-md-8">
											<input class="form-control" type="text" name="apirequesturl" id="apirequesturl">
										</div>
									</div>
									<div class="form-group">
										
										<div class="col-md-6"  style = "">
											&nbsp;
										</div>
									</div>
									<div class="form-group" id = "apiformgroupheader">
										<label class="col-md-4 control-label">Headers:</label>
										
										<div class="col-md-6" id "headertableblock" style = "">
											<div class="table-responsive">
												<table border="0" id = "apiheadertable" width="100%" cellspacing="5" cellpadding="5">
													<thead>
														<tr>
															<th>Header Field</th>
															<th>Header Value</th>
															<th>&nbsp;</th>
															
														</tr>
													</thead>
													<tbody>
														
													</tbody>
												</table>
											</div>
											
											
										</div>
										<div class="col-md-2" style = "padding-top: 5px">
											<span id = "apiaddheader" style = "cursor:pointer" class="label label-primary">Add A Header</span>
										</div>
									</div>
									<div class="form-group">
										
										<div class="col-md-6"  style = "">
											&nbsp;
										</div>
									</div>
									
									<div class="form-group" id = "apiformgroupbasicauth">
										<label class="col-md-4 control-label">Basic Authentication:</label>
										
										<div class="col-md-6" id "apibasicauthtableblock" style = "">
											<div class="table-responsive">
												<table border="0" id = "apibasicauthtable" width="100%" cellspacing="5" cellpadding="5">
													<thead>
														<tr>
															<th>Username</th>
															<th>Password</th>
															<th>&nbsp;</th>
															
														</tr>
													</thead>
													<tbody>
														
													</tbody>
												</table>
											</div>
											
											
										</div>
										<div class="col-md-2" style = "padding-top: 5px">
											<span id = "apiaddbasicauth" style = "cursor:pointer" class="label label-primary">Add Basic Authentication</span>
										</div>
									</div>
									
									<div class="form-group">
										
										<div class="col-md-6"  style = "">
											&nbsp;
										</div>
									</div>
									
									<div class="form-group" id = "apiformgroupacceptstring">
										<label class="col-md-4 control-label">Success String:</label>
										<div class="col-md-4">
											<input class="form-control" type="text" name="apiacceptstring" id="apiacceptstring">
										</div>
									</div>
									
									<div class="form-group">
										
										<div class="col-md-6"  style = "">
											&nbsp;
										</div>
									</div>
									
									<div class="form-group" id = "apiformgroupacceptcode">
										<label class="col-md-4 control-label">Success HTTP Code:</label>
										<div class="col-md-4">
											<input class="form-control" type="text" name="apiacceptcode" id="apiacceptcode">
										</div>
									</div>
									
									<div class="form-group">
										
										<div class="col-md-6"  style = "">
											&nbsp;
										</div>
									</div>
									
									<div class="form-group" id="apiformgroupcustomfields">
										<label class="col-md-4 control-label">Custom Fields:</label>
										
										<div class="col-md-6" id "apicustomfieldtableblock" style = "">
											<div class="table-responsive">
												<table border="0" id = "apicustomfieldtable" width="100%" cellspacing="5" cellpadding="5">
													<thead>
														<tr>
															<th>Field Name</th>
															<th>Field Value</th>
															<th>&nbsp;</th>
															
														</tr>
													</thead>
													<tbody>
														
													</tbody>
												</table>
											</div>
											
											
										</div>
										<div class="col-md-2" style = "padding-top: 5px">
											<span id = "apiaddcustomfield" style = "cursor:pointer" class="label label-primary">Add A Custom Field</span>
										</div>
									</div>
									
									<div class="form-group">
										
										<div class="col-md-6"  style = "">
											&nbsp;
										</div>
									</div>
									
									
									
									<div class="form-group">
										
										<div class="col-md-6"  style = "">
											&nbsp;
										</div>
									</div>
									
									<div class="form-group" id="apiformgrouptemplate">
										<label class="col-md-4 control-label">JSON OR XML TEMPLATE?:</label>
										<div class="col-md-8">
											<select id="apitemplatedropdown" name="apitemplatedropdown" data-placeholder="Choose One" style="width:100px" >
												<option value = "" selected>Choose</option>
												<option value="Yes">Yes</option>
												<option value = "No">No</option>
											</select>
										</div>
									</div>
									
									<div class="form-group">
										
										<div class="col-md-6"  style = "">
											&nbsp;
										</div>
									</div>		
								
									<div class="form-group" id= "apipostbuilder">
										<label class="col-md-4 control-label">Request Builder:</label>
										<div class="col-md-8">
									<table border="0" style = "font:Arial, Helvetica, sans-serif;color:black" class = "table table-dark mb30 table-striped" id = "apibuildertable" width="100%" cellspacing="5" cellpadding="5">
													<thead>
														<tr >
															<th style = "text-align: center">Fields</th>
															<th style = "text-align: center">Post Field Name</th>
															<th style = "text-align: center">Field Type</th>
															
														</tr>
													</thead>
													<tbody>
														
													</tbody>
												</table>
									</div>
									
								</div>			
												
									
								<div class="form-group" id= "apitemplatebuilder">
										<label class="col-md-4 control-label">Request Builder:</label>
										<div class="col-md-8">
											
											<div class = "row">
												<div class = "col-md-7">
													<div class="panel panel-default">
													  <div class="panel-heading">
													    <h3 class="panel-title">Available <b>System</b> Fields</h3>
													  </div>
													  <div class="panel-body">
													    <table style = "" id = "apisystemfieldsdisplay" width = "100%">
													    	<thead>
													    		
													    	</thead>
													    	<tbody>
													    		
													    	</tbody>
													    </table>
													  </div>
													</div>
												</div>
												
												
												<div class = "col-md-5">
													<div class="panel panel-default">
													  <div class="panel-heading">
													    <h3 class="panel-title">Available <b>Custom</b> Fields</h3>
													  </div>
													  <div class="panel-body">
													    <table id = "apicustomfieldsdisplay" width = "100%">
													    	<thead>
													    		
													    	</thead>
													    	<tbody>
													    		
													    	</tbody>
													    </table>
													  </div>
													</div>
												</div>
												
											</div>
											
											<textarea name = "apipostarea" id="apipostarea" class="form-control" rows="5" style="height: 300px;"></textarea>
											
									</div>
									
								</div>		
									
								</div>
							</div>
						</div>

				
					<!-- Tab Esp Pull Suppression -->
					<div id="blacklistesppostout" class="tab-pane">
						
						<div class="panel-body">
							<div class="col-md-12">
								<div class="form-group">
									<table border=0 width="30%">
										<tr>
											<td><button type="button" class="btn btn-warning pull-left" data-toggle="modal" id = "blacklisttestposter" data-target="#blacklisttestmodal">Test Post</button></td>
										</tr>
										
									</table>
								</div>
							</div>
							<div class="col-md-8">
								<div class="form-group" id = "blacklistformgrouprequesttype">
									<label class="col-md-4 control-label">Request Type:</label>
									<div class="form-group col-md-8">
										<select id="blacklistrequesttype" data-placeholder="Choose One" class="width300">
											<option value="">--CHOOSE--</option>
											<option value="POST">POST</option>
											<option value = "GET">GET</option>
											<option value = "DELETE">DELETE</option>
										</select>
									</div>
								</div>
								<div class="form-group" id = "blacklistformgrouprequesturl">
									<label class="col-md-4 control-label">Request URL:</label>
									<div class="col-md-8">
										<input class="form-control" type="text" name="blacklistrequesturl" id="blacklistrequesturl">
									</div>
								</div>
								<div class="form-group">
									
									<div class="col-md-6"  style = "">
										&nbsp;
									</div>
								</div>
								<div class="form-group" id = "blacklistformgroupheader">
									<label class="col-md-4 control-label">Headers:</label>
									
									<div class="col-md-6" id "headertableblock" style = "">
										<div class="table-responsive">
											<table border="0" id = "blacklistheadertable" width="100%" cellspacing="5" cellpadding="5">
												<thead>
													<tr>
														<th>Header Field</th>
														<th>Header Value</th>
														<th>&nbsp;</th>
														
													</tr>
												</thead>
												<tbody>
													
												</tbody>
											</table>
										</div>
										
										
									</div>
									<div class="col-md-2" style = "padding-top: 5px">
										<span id = "blacklistaddheader" style = "cursor:pointer" class="label label-primary">Add A Header</span>
									</div>
								</div>
								<div class="form-group">
									
									<div class="col-md-6"  style = "">
										&nbsp;
									</div>
								</div>
								
								<div class="form-group" id = "blacklistformgroupbasicauth">
									<label class="col-md-4 control-label">Basic Authentication:</label>
									
									<div class="col-md-6" id "blacklistbasicauthtableblock" style = "">
										<div class="table-responsive">
											<table border="0" id = "blacklistbasicauthtable" width="100%" cellspacing="5" cellpadding="5">
												<thead>
													<tr>
														<th>Username</th>
														<th>Password</th>
														<th>&nbsp;</th>
														
													</tr>
												</thead>
												<tbody>
													
												</tbody>
											</table>
										</div>
										
										
									</div>
									<div class="col-md-2" style = "padding-top: 5px">
										<span id = "blacklistaddbasicauth" style = "cursor:pointer" class="label label-primary">Add Basic Authentication</span>
									</div>
								</div>
								
								<div class="form-group">
									
									<div class="col-md-6"  style = "">
										&nbsp;
									</div>
								</div>
								
								<div class="form-group" id = "blacklistformgroupacceptstring">
									<label class="col-md-4 control-label">Success String:</label>
									<div class="col-md-4">
										<input class="form-control" type="text" name="blacklistacceptstring" id="blacklistacceptstring">
									</div>
								</div>
								
								<div class="form-group">
									
									<div class="col-md-6"  style = "">
										&nbsp;
									</div>
								</div>
								
								<div class="form-group" id = "blacklistformgroupacceptcode">
									<label class="col-md-4 control-label">Success HTTP Code:</label>
									<div class="col-md-4">
										<input class="form-control" type="text" name="blacklistacceptcode" id="blacklistacceptcode">
									</div>
								</div>
								
								<div class="form-group">
									
									<div class="col-md-6"  style = "">
										&nbsp;
									</div>
								</div>
								
								<div class="form-group" id= "tokenblock">
									<div class="col-md-12">
										<table border="0" style = "font:Arial, Helvetica, sans-serif;color:black" class = "table table-dark mb30 table-striped" id = "tokentable" width="100%" cellspacing="5" cellpadding="5">
											<thead>
												<tr >
													<th style = "text-align: center">Date Token Implementation</th>
													<th style = "text-align: center">Explanation</th>
													
													
												</tr>
											</thead>
											<tbody>
												<tr>
														<th>TOKEN::DATE[Y-m-d]::DAYS[-1]</th>
														<th>The date is fomatted to YYYY-mm-dd (2016-02-25) and will be one day before the date of current code execution</th>
														
												</tr>
												<tr>
														<th>TOKEN::DATE[Y-m-d]::DAYS[2]</th>
														<th>The date is fomatted to YYYY-mm-dd (2016-02-25) and will be two days after the date of current code execution</th>
														
												</tr>
												<tr>
														<th>TOKEN::DATE[Y-m-d]::DAYS[0]</th>
														<th>The date is fomatted to YYYY-mm-dd (2016-02-25) and will be the same day as code execution</th>
														
												</tr>
												<tr>
														<th>TOKEN::DATE[Y-m-d]</th>
														<th>The date is fomatted to YYYY-mm-dd (2016-02-25) and will be the same day as code execution</th>
														
												</tr>
												<tr>
														<th>TOKEN::DAYS[0]</th>
														<th>Error!  No format</th>
														
												</tr>
												<tr>
														<th>DATE[Y-m-d]::DAYS[0]</th>
														<th>Error!  No Token String</th>
														
												</tr>
												<tr>
														<th>Example Custom Field Entry</th>
														<th>	
															<table border=1>
																<tr>
																	<td>
																		Field Name
																	</td>
																	<td>
																		Field Value
																	</td>
																</tr>
																<tr>
																	<td>
																		<input readonly class="form-control" style = "width: 200px" type="text" value = "startDate" >
																	</td>
																	<td>
																		<input readonly class="form-control" style = "width: 250px" type="text" value = "TOKEN::DATE[Y-m-d]::DAYS[-1]">
																	</td>
																</tr>
															
															</table>
															
														</th>	
															
												</tr>
											</tbody>
										</table>
										<div>
											<div style = "font-size: 16px" class="alert alert-danger" role="alert">Tokens are used only as <b>CUSTOM FIELD</b> values!</div>	
										</div>
									</div>
								
							</div>			
								
								<div class="form-group">
									
									<div class="col-md-6"  style = "">
										&nbsp;
									</div>
								</div>
								
								<div class="form-group" id="blacklistformgroupcustomfields">
									<label class="col-md-4 control-label">Custom Fields:</label>
									
									<div class="col-md-6" id "blacklistcustomfieldtableblock" style = "">
										<div class="table-responsive">
											<table border="0" id = "blacklistcustomfieldtable" width="100%" cellspacing="5" cellpadding="5">
												<thead>
													<tr>
														<th>Field Name</th>
														<th>Field Value</th>
														<th>&nbsp;</th>
														
													</tr>
												</thead>
												<tbody>
													
												</tbody>
											</table>
										</div>
										
										
									</div>
									<div class="col-md-2" style = "padding-top: 5px">
										<span id = "blacklistaddcustomfield" style = "cursor:pointer" class="label label-primary">Add A Custom Field</span>
									</div>
								</div>
								
								<div class="form-group">
									
									<div class="col-md-6"  style = "">
										&nbsp;
									</div>
								</div>
								
								
								
								<div class="form-group">
									
									<div class="col-md-6"  style = "">
										&nbsp;
									</div>
								</div>
								
								<div class="form-group" id="blacklistformgrouptemplate">
									<label class="col-md-4 control-label">JSON OR XML TEMPLATE?:</label>
									<div class="col-md-8">
										<select id="blacklisttemplatedropdown" name="blacklisttemplatedropdown" data-placeholder="Choose One" style="width:100px" >
											<option value = "" selected>Choose</option>
											<option value="Yes">Yes</option>
											<option value = "No">No</option>
										</select>
									</div>
								</div>
								
								<div class="form-group">
									
									<div class="col-md-6"  style = "">
										&nbsp;
									</div>
								</div>		
							
								<div class="form-group" id= "blacklistpostbuilder">
									<label class="col-md-4 control-label">Request Builder:</label>
									<div class="col-md-8">
								<table border="0" style = "font:Arial, Helvetica, sans-serif;color:black" class = "table table-dark mb30 table-striped" id = "blacklistbuildertable" width="100%" cellspacing="5" cellpadding="5">
												<thead>
													<tr >
														<th style = "text-align: center">Fields</th>
														<th style = "text-align: center">Post Field Name</th>
														<th style = "text-align: center">Field Type</th>
														
													</tr>
												</thead>
												<tbody>
													
												</tbody>
											</table>
								</div>
								
							</div>			
											
								
							<div class="form-group" id= "blacklisttemplatebuilder">
									<label class="col-md-4 control-label">Request Builder:</label>
									<div class="col-md-8">
										
										<div class = "row">
											<div class = "col-md-7">
												<div class="panel panel-default">
												  <div class="panel-heading">
												    <h3 class="panel-title">Available <b>System</b> Fields</h3>
												  </div>
												  <div class="panel-body">
												    <table style = "" id = "blacklistsystemfieldsdisplay" width = "100%">
												    	<thead>
												    		
												    	</thead>
												    	<tbody>
												    		
												    	</tbody>
												    </table>
												  </div>
												</div>
											</div>
											
											
											<div class = "col-md-5">
												<div class="panel panel-default">
												  <div class="panel-heading">
												    <h3 class="panel-title">Available <b>Custom</b> Fields</h3>
												  </div>
												  <div class="panel-body">
												    <table id = "blacklistcustomfieldsdisplay" width = "100%">
												    	<thead>
												    		
												    	</thead>
												    	<tbody>
												    		
												    	</tbody>
												    </table>
												  </div>
												</div>
											</div>
											
										</div>
										
										<textarea name = "blacklistpostarea" id="blacklistpostarea" class="form-control" rows="5" style="height: 300px;"></textarea>
										
								</div>
								
							</div>		
								
							</div>
							</div>
						</div>

				
				<!--  End Tab Pull Suppression LIst -->
						
					<!-- footer -->	
					<div class="panel-footer">
						<table border=0 width="30%">
							<tr>
								<td><button class="btn btn-primary pull-left" id="save-esp">Save ESP</button></td>
								<td align="center"><button class="btn btn-success" id="reset-esp">Start Over</button></td>
							</tr>
							<tr>
								<td colspan="2"><div width="100%" id = "errordisplay"></div></td>
							</tr>
						</table>
					</div>
					<!-- End Footer -->
						
				</div>
			</div>
		</div>	
		
			
	</div>
	<!-- contentpanel -->
	
	
	<!-- Modal -->
	<div id="testmodal" class="modal fade" role="dialog" data-backdrop="static">
	  <div class="modal-dialog">
	
	    <!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title">Test Post</h4>
	      </div>
	      
	      <div class="modal-body">
	        	<div class = "row">
	        		<div class="form-group" id= "testresponse">
							<label class="col-md-2 control-label">Test Response:</label>
							<div class="col-sm-8">
								<textarea readonly name = "testresponsearea" id="testresponsearea" class="form-control" rows="5" style="height: 300px;"></textarea>	
							</div>
						
					</div>	
	        		
	        		
	        		
	        		<div id="testblock">
		        		<div class="form-group" id= "">
								<label class="col-md-2 control-label">Test Data:</label>
								<div class="col-sm-8">
							<table border="0" style = "font:Arial, Helvetica, sans-serif;color:black" class = "table table-dark mb30 table-striped" id = "testpostdata" width="100%" cellspacing="5" cellpadding="5">
											<thead>
												<tr >
													<th style = "text-align: center">Field</th>
													<th style = "text-align: center">Value</th>
													
												</tr>
											</thead>
											<tbody>
												
											</tbody>
										</table>
							</div>
							
						</div>	
		        		
		        		
		        		
		        		<div class="form-group" id= "testpostbuilder">
								<label class="col-md-2 control-label">Request Builder:</label>
								<div class="col-md-8">
							<table border="0" style = "font:Arial, Helvetica, sans-serif;color:black" class = "table table-dark mb30 table-striped" id = "testbuildertable" width="100%" cellspacing="5" cellpadding="5">
											<thead>
												<tr >
													<th style = "text-align: center">Fields</th>
													<th style = "text-align: center">Post Field Name</th>
													<th style = "text-align: center">Field Type</th>
													
												</tr>
											</thead>
											<tbody>
												
											</tbody>
										</table>
							</div>
							
						</div>			
													
										
						<div class="form-group" id= "testtemplatebuilder">
								<label class="col-md-2 control-label">Request Builder:</label>
								<div class="col-md-8">
									
									<div class = "row">
										<div class = "col-md-6">
											<div class="panel panel-default">
											  <div class="panel-heading">
											    <h3 class="panel-title">Available <b>System</b> Fields</h3>
											  </div>
											  <div class="panel-body">
											    <table style = "" id = "testsystemfieldsdisplay" width = "100%">
											    	<thead>
											    		
											    	</thead>
											    	<tbody>
											    		
											    	</tbody>
											    </table>
											  </div>
											</div>
										</div>
										
										
										<div class = "col-md-6">
											<div class="panel panel-default">
											  <div class="panel-heading">
											    <h3 class="panel-title">Available <b>Custom</b> Fields</h3>
											  </div>
											  <div class="panel-body">
											    <table id = "testcustomfieldsdisplay" width = "100%">
											    	<thead>
											    		
											    	</thead>
											    	<tbody>
											    		
											    	</tbody>
											    </table>
											  </div>
											</div>
										</div>
										
									</div>
									
									<textarea readonly name = "testpostarea" id="testpostarea" class="form-control" rows="5" style="height: 300px;"></textarea>
									
							</div>
							
						</div>
					</div>	
	        
	        	</div>
	        
	        
	        
	      </div>
	      <div class="modal-footer">
	       <button type="button" id="sendtestposter" class="btn btn-success pull-left">Send</button>  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      </div>
	    </div>
	
	  </div>
	</div>
	<!-- End Modal -->
	
	
	
	<!--Test Modal -->
	<div id="apitestmodal" class="modal fade" role="dialog" data-backdrop="static">
	  <div class="modal-dialog">
	
	    <!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title">Test Post</h4>
	      </div>
	      
	      <div class="modal-body">
	        	<div class = "row">
	        		<div class="form-group" id= "apitestresponse">
							<label class="col-md-2 control-label">Test Response:</label>
							<div class="col-sm-8">
								<textarea readonly name = "apitestresponsearea" id="apitestresponsearea" class="form-control" rows="5" style="height: 300px;"></textarea>	
							</div>
						
					</div>	
	        		
	        		
	        		
	        		<div id="apitestblock">
		        		<div class="form-group" id= "">
								<label class="col-md-2 control-label">Test Data:</label>
								<div class="col-sm-8">
							<table border="0" style = "font:Arial, Helvetica, sans-serif;color:black" class = "table table-dark mb30 table-striped" id = "apitestpostdata" width="100%" cellspacing="5" cellpadding="5">
											<thead>
												<tr >
													<th style = "text-align: center">Field</th>
													<th style = "text-align: center">Value</th>
													
												</tr>
											</thead>
											<tbody>
												
											</tbody>
										</table>
							</div>
							
						</div>	
		        		
		        		
		        		
		        		<div class="form-group" id= "apitestpostbuilder">
								<label class="col-md-2 control-label">Request Builder:</label>
								<div class="col-md-8">
							<table border="0" style = "font:Arial, Helvetica, sans-serif;color:black" class = "table table-dark mb30 table-striped" id = "apitestbuildertable" width="100%" cellspacing="5" cellpadding="5">
											<thead>
												<tr >
													<th style = "text-align: center">Fields</th>
													<th style = "text-align: center">Post Field Name</th>
													<th style = "text-align: center">Field Type</th>
													
												</tr>
											</thead>
											<tbody>
												
											</tbody>
										</table>
							</div>
							
						</div>			
													
										
						<div class="form-group" id= "apitesttemplatebuilder">
								<label class="col-md-2 control-label">Request Builder:</label>
								<div class="col-md-8">
									
									<div class = "row">
										<div class = "col-md-6">
											<div class="panel panel-default">
											  <div class="panel-heading">
											    <h3 class="panel-title">Available <b>System</b> Fields</h3>
											  </div>
											  <div class="panel-body">
											    <table style = "" id = "apitestsystemfieldsdisplay" width = "100%">
											    	<thead>
											    		
											    	</thead>
											    	<tbody>
											    		
											    	</tbody>
											    </table>
											  </div>
											</div>
										</div>
										
										
										<div class = "col-md-6">
											<div class="panel panel-default">
											  <div class="panel-heading">
											    <h3 class="panel-title">Available <b>Custom</b> Fields</h3>
											  </div>
											  <div class="panel-body">
											    <table id = "apitestcustomfieldsdisplay" width = "100%">
											    	<thead>
											    		
											    	</thead>
											    	<tbody>
											    		
											    	</tbody>
											    </table>
											  </div>
											</div>
										</div>
										
									</div>
									
									<textarea readonly name = "apitestpostarea" id="apitestpostarea" class="form-control" rows="5" style="height: 300px;"></textarea>
									
							</div>
							
						</div>
					</div>	
	        
	        	</div>
	        
	        
	        
	      </div>
	      <div class="modal-footer">
	       <button type="button" id="apisendtestposter" class="btn btn-success pull-left">Send</button>  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      </div>
	    </div>
	
	  </div>
	</div>
	<!-- End Test Modal -->
	
	
	<div id="blacklisttestmodal" class="modal fade" role="dialog" data-backdrop="static">
	  <div class="modal-dialog">
	
	    <!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title">Test Post</h4>
	      </div>
	      
	      <div class="modal-body">
	        	<div class = "row">
	        		<div class="form-group" id= "blacklisttestresponse">
							<label class="col-md-2 control-label">Test Response:</label>
							<div class="col-sm-8">
								<textarea readonly name = "blacklisttestresponsearea" id="blacklisttestresponsearea" class="form-control" rows="5" style="height: 300px;"></textarea>	
							</div>
						
					</div>	
	        		
	        		
	        		
	        		<div id="blacklisttestblock">
		        		<div class="form-group" id= "">
								<label class="col-md-2 control-label">Test Data:</label>
								<div class="col-sm-8">
							<table border="0" style = "font:Arial, Helvetica, sans-serif;color:black" class = "table table-dark mb30 table-striped" id = "blacklisttestpostdata" width="100%" cellspacing="5" cellpadding="5">
											<thead>
												<tr >
													<th style = "text-align: center">Field</th>
													<th style = "text-align: center">Value</th>
													
												</tr>
											</thead>
											<tbody>
												
											</tbody>
										</table>
							</div>
							
						</div>	
		        		
		        		
		        		
		        		<div class="form-group" id= "blacklisttestpostbuilder">
								<label class="col-md-2 control-label">Request Builder:</label>
								<div class="col-md-8">
							<table border="0" style = "font:Arial, Helvetica, sans-serif;color:black" class = "table table-dark mb30 table-striped" id = "blacklisttestbuildertable" width="100%" cellspacing="5" cellpadding="5">
											<thead>
												<tr >
													<th style = "text-align: center">Fields</th>
													<th style = "text-align: center">Post Field Name</th>
													<th style = "text-align: center">Field Type</th>
													
												</tr>
											</thead>
											<tbody>
												
											</tbody>
										</table>
							</div>
							
						</div>			
													
										
						<div class="form-group" id= "blacklisttesttemplatebuilder">
								<label class="col-md-2 control-label">Request Builder:</label>
								<div class="col-md-8">
									
									<div class = "row">
										<div class = "col-md-6">
											<div class="panel panel-default">
											  <div class="panel-heading">
											    <h3 class="panel-title">Available <b>System</b> Fields</h3>
											  </div>
											  <div class="panel-body">
											    <table style = "" id = "blacklisttestsystemfieldsdisplay" width = "100%">
											    	<thead>
											    		
											    	</thead>
											    	<tbody>
											    		
											    	</tbody>
											    </table>
											  </div>
											</div>
										</div>
										
										
										<div class = "col-md-6">
											<div class="panel panel-default">
											  <div class="panel-heading">
											    <h3 class="panel-title">Available <b>Custom</b> Fields</h3>
											  </div>
											  <div class="panel-body">
											    <table id = "blacklisttestcustomfieldsdisplay" width = "100%">
											    	<thead>
											    		
											    	</thead>
											    	<tbody>
											    		
											    	</tbody>
											    </table>
											  </div>
											</div>
										</div>
										
									</div>
									
									<textarea readonly name = "blacklisttestpostarea" id="blacklisttestpostarea" class="form-control" rows="5" style="height: 300px;"></textarea>
									
							</div>
							
						</div>
					</div>	
	        
	        	</div>
	        
	        
	        
	      </div>
	      <div class="modal-footer">
	       <button type="button" id="blacklistsendtestposter" class="btn btn-success pull-left">Send</button>  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      </div>
	    </div>
	
	  </div>
	</div>
	<!-- End Test Modal -->
	
</div>
<!-- mainpanel -->

