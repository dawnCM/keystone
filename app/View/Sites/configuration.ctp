<div class="mainpanel">
	<div class="pageheader">
		<div class="media">
			<div class="pageicon pull-left">
				<i class="fa fa-cogs"></i>
			</div>
			<div class="media-body">
				<ul class="breadcrumb">
					<li><a href="/dashboard"><i class="glyphicon glyphicon-home"></i></a></li>
					<li><a href="/dashboard">Site Management</a></li>
					<li>Site Configuration</li>
				</ul>
				<h4>Site Configuration</h4>
			</div>
		</div>
		<!-- media -->
	</div>
	<!-- pageheader -->
	

	<div class="contentpanel">
		<?php echo $this->Session->flash(); ?>
		<div class="row">
			<div class="col-md-8">
				<div class="panel panel-default">
					<div class="panel-heading">
						<div class="pull-right"></div>
						<h3 class="panel-title">Configuration List</h3>
					</div>
					<div class="panel-body">
						<table id="configurationTable"
							class="table table-hover table-bordered responsive">
							<thead class="">
								<tr>
									<th>Configuration ID</th>
									<th>Website</th>
									<th>Ancillary</th>
									<th>Blocked</th>
									<th>Status</th>
									<th>Infinite Pops</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
							<?php 
							foreach($configlist as $key=>$configdetail){
								if ($configdetail['Ancillary']['status'] == 1){$status = 'Active';}else{$status = 'Inactive';}
								$switch_state = (($configdetail['SiteConfiguration']['infinite_pop'] != '1')? '' : 'checked');
								
								//Check to see if to display infinite pops switch
								$show_infinite_pops = ((array_key_exists('backend', json_decode($configdetail['Ancillary']['triggeraction'], true)) )? false : true);
								
								
								echo "<tr>
									<td>{$configdetail['SiteConfiguration']['id']}</td>
									<td>{$configdetail['Site']['name']}</td>
									<td>{$configdetail['Ancillary']['name']}</td>
									<td>{$configdetail['SiteConfiguration']['blocked']}</td>
									<td>{$status}</td>
									<td>".(($show_infinite_pops)? "<input name=\"infinite-pop\" data-siteconfigid='{$configdetail['SiteConfiguration']['id']}'  id=\"infinite-pop\" type=\"checkbox\" $switch_state data-size=\"mini\" data-on-color=\"success\" data-off-color=\"danger\">" : 'N/A')."</td>
									<td>
										<button class=\"btn btn-success btn-xs\"  title=\"Edit\" data-configid='{$configdetail['SiteConfiguration']['id']}'  data-ancillaryid='{$configdetail['SiteConfiguration']['ancillary_id']}'  data-siteconfigid='{$configdetail['SiteConfiguration']['site_id']}' id=\"edit-siteconfig\"><span class=\"glyphicon glyphicon-edit\"></span></button>
										&nbsp;&nbsp;<button class=\"btn btn-danger btn-xs\" data-toggle=\"modal\" data-target=\"#siteconfig-delete-modal\" data-siteconfigid='{$configdetail['SiteConfiguration']['id']}' id=\"delete-siteconfig\"><span class=\"fa fa-trash-o\"></span></button>
									</td>
								</tr>";
							}?>
							</tbody>
						</table>
					</div>
					<!-- panel-body -->
				</div>
			</div>
			<div class="col-md-4">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<!-- panel-btns -->
						<h3 class="panel-title">Add New Site Configuration</h3>
					</div>
					<div class="panel-body">
						<div class="form-group">
							<div class="col-sm-12">
								<select id="siteconfigselect" name="data[SiteConfiguration][site_id]"style="width:100%">
								<option value="">Choose a website</option>
									<?php 
									foreach($sitelist as $site){
										echo "<option value='{$site['Site']['id']}'>{$site['Site']['name']}</option>";
									}
									?>
								</select>
							</div>
						</div>
						<!-- form-row-1 -->
						<div class="form-group">
							<div class="col-sm-12">
								<select id="siteconfigancillaryselect" name="data[SiteConfiguration][ancillary_id]" style="width:100%">
								<option value="">Ancillary Configuration</option>
									<?php 
									foreach($ancillarylist as $ancillary){
										echo "<option value='{$ancillary['Ancillary']['id']}'>{$ancillary['Ancillary']['name']} ({$ancillary['Ancillary']['id']})</option>";
									}
									?>
								</select>
							</div>
						</div>
						<!-- form-row-2 -->
						<div class="form-group" id="affiliategroup">
							<div class="col-sm-12">
								<table id = "affiliate-restrictions" border="0" width = "100%">
									<thead></thead>
									<tbody>
										<tr>
											<td>
												<select id="affiliatelistselect" name="affiliate_id" style="width:100%">
													<option value="">Block by Affiliate</option>
														<?php foreach($affiliate_list as $key=>$aff){										
															if($aff['Affiliate']['remote_id'] != 0){
																echo '<option value="'.$aff['Affiliate']["remote_id"].'">'.$aff['Affiliate']['affiliate_name'].' ('.$aff['Affiliate']['remote_id'].')</option.>';
															}
														}?>
												</select>
											</td>
											<td>
												<input type="text" id="subaffiliate" name="subaffiliate" class="form-control" placeholder="SubId(s)">
											</td>
											<td>
												&nbsp;
											</td>
										</tr>
									</tbody>
								</table>		
							</div>
						</div>
						<!-- form-row-3 -->
						<div class="form-group">
							<div class="col-sm-12">
								<button class="btn btn-warning btn-xs" id="duplicate-affiliate"><span class="fa fa-plus-circle"></span></button>
							</div>
						</div>
					</div>
					<!-- panel-body -->
					<div class="panel-footer">
						<button class="btn btn-primary" id = "add-siteconfig">Add Configuration</button>
						<button class="btn btn-success" data-configid="" style = "display:none" id = "update-siteconfig">Update Configuration</button>
						<button class="btn btn-default" style = "display:none" id = "cancel-siteconfig">Cancel</button>
					</div><!-- panel-footer -->
				</div>
				<!-- panel -->
			</div>
		</div>
		<!-- row -->
	</div>
	<!-- contentpanel -->
	
	<!-- Deletion Modal -->
	<div id="siteconfig-delete-modal" class="modal fade in" data-backdrop="static" data-keyboard="false" aria-hidden="false" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" style="display: none;">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button class="close" aria-hidden="true" data-dismiss="modal" type="button">×</button>
					<button class="btn btn-warning btn-xs" data-toggle="modal" data-target="#siteconfig-delete-modal-icon"><span class="glyphicon glyphicon-warning-sign"></span></button><h4 id="myModalLabel" class="modal-title">Site Configuration Deletion</h4>
				</div>
				<div class="modal-body"> Attention! This process will delete the Site Configuration. </div>
				<div class="modal-footer">
					<button class="btn btn-default" data-dismiss="modal" type="button">Cancel</button>
					<button class="btn btn-primary" id = "siteconfig-delete-modal-action" data-siteconfigid = "" type="button">Delete Site Configuration</button>
				</div>
			</div>
		</div>
	</div>
	
	
</div>
<!-- mainpanel -->