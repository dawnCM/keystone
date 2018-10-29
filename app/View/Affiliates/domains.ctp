<div class="mainpanel">
	<div class="pageheader">
		<div class="media">
			<div class="pageicon pull-left">
				<i class="fa fa-sitemap"></i>
			</div>
			<div class="media-body">
				<ul class="breadcrumb">
					<li><a href="/dashboard"><i class="glyphicon glyphicon-home"></i></a></li>
					<li><a href="/affiliates">Affiliates</a></li>
					<li>Api Access</li>
				</ul>
				<h4>Api Access</h4>
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
						<h3 class="panel-title">Vendors</h3>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
						<table id="vendorTable" class="table table-hover table-bordered responsive">
							<thead>
								<tr>
									<th class="sorting_disabled"></th>
									<th>Api ID</th>
									<th>Api Key</th>
									<th>Name</th>
									<th>IP Address</th>
									<th>Domains</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody id="vendorTableBody">
							<?php 
								foreach($affiliate_list as $affiliate) {
									if(count($affiliate['AffiliateIp'])>0 || count($affiliate['AffiliateDomain']) > 0) {
										echo '<tr data-vendor-id="'.$affiliate['Affiliate']['id'].'" class="open-vendor" style="cursor:pointer;">';
										echo '<td style="text-align:center; vertical-align:middle;"><span style="margin-left:0px; id="vendor_'.$affiliate['Affiliate']['id'].'" class="fa fa-arrow-right"></span></td>';
										echo '<td>'.$affiliate['Affiliate']['remote_id'].'</td>';
										echo '<td>'.$affiliate['Affiliate']['api_key'].'</td>';
										echo '<td>'.$affiliate['Affiliate']['affiliate_name'].'</td>';
										echo '<td>-</td>';
										echo '<td>-</td>';
										echo '<td>-</td>';
										echo '</tr>';
									}
									
									if(count($affiliate['AffiliateIp']>0)) {
										foreach($affiliate['AffiliateIp'] as $ip) {
											echo '<tr style="display:none;" class="subrow vendor_'.$affiliate['Affiliate']['id'].'">';
											echo '<td style="text-align:center; vertical-align:middle;"><span style="color:darkgray;  margin-left:24px;" class="fa fa-arrow-right"></span></td>';
										    echo '<td>-</td>';
										    echo '<td>-</td>';
										    echo '<td>-</td>';
										    echo '<td>'.$ip['ip'].'</td>';
										    echo '<td></td>';
											echo '<td data-toggle="modal" data-target=".confirm-delete-ip" class="vendorIpDeleteOpen" data-ip-id="'.$ip['id'].'" style="text-align:center; vertical-align:middle; cursor:pointer;"><button class="btn btn-danger btn-xs"><span class="fa fa-trash-o"></span></button></td>';
										    echo '</tr>';
										}
									}
									
									if(count($affiliate['AffiliateDomain']>0)) {
										foreach($affiliate['AffiliateDomain'] as $domain) {
											echo '<tr style="display:none;" class="subrow vendor_'.$affiliate['Affiliate']['id'].'">';
											echo '<td style="text-align:center; vertical-align:middle;"><span style="color:darkgray;  margin-left:24px;" class="fa fa-arrow-right"></span></td>';
											echo '<td>-</td>';
											echo '<td>-</td>';
											echo '<td>-</td>';
											echo '<td>-</td>';
											echo '<td><a href="http://'.$domain['domain'].'" target="_blank">'.$domain['domain'].'</a></td>';
											echo '<td data-toggle="modal" data-target=".confirm-delete-domain" class="vendorDomainDeleteOpen" data-domain-id="'.$domain['id'].'" style="text-align:center; vertical-align:middle; cursor:pointer;"><span class="fa fa-trash-o"></span></td>';
											echo '</tr>';
										}
									}
								}
							?>
							</tbody>
						</table>
						</div>
					</div>
					<!-- panel-body -->
				</div>
			</div>
			<div class="col-md-4">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<div class="panel-btns" style="display: none;">
							<a href="" class="panel-minimize tooltips" data-placement="left" data-toggle="tooltip" title="" data-original-title="Minimize Panel"><i class="fa fa-minus"></i> </a>
						</div>
						<!-- panel-btns -->
						<h3 class="panel-title">Add Domain or IP</h3>
					</div>
					<div class="panel-body">
					<?php echo $this->Form->create('Affiliate', array('id'=>'add_domains_ip', 'action' => 'add_domains_ip', 'inputDefaults'=>array('label'=>false,'div'=>false))); ?>
						<div class="row">
							<div class="form-group col-md-12">
								<select id="select-affiliate" name="affiliate-remote-id" data-placeholder="Choose One" class="width300">
								<option value="">Choose a Vendor</option>
									<?php foreach($affiliate_list as $key=>$aff){										
										
											echo '<option value="'.$aff['Affiliate']["id"].'">'.$aff['Affiliate']['affiliate_name'].' ('.$aff['Affiliate']['remote_id'].')</option.>';
										
									}?>
								</select>
							</div>
						</div>
						<!-- form-row-1 -->
						<div class="row">
							<div class="form-group col-md-6">
								<input type="text" id="add-affiliate-domain" class="form-control" name="add-affiliate-domain" placeholder="Domain">
							</div>
							<div class="form-group col-md-6">
								<input type="text" id="add-affiliate-ip" class="form-control" name="add-affiliate-ip" placeholder="IP Address">
							</div>
						</div>
						<!-- form-row-2 -->
					</div>
					<!-- panel-body -->
					<div class="panel-footer">
						<button class="btn btn-primary">Add Data</button>
						<button type="reset" id="add-user-reset" class="btn btn-dark">Reset</button>
						<?php echo $this->Form->end(); ?>
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
<div class="modal fade confirm-delete-domain" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="panelt panel-danger">
				<div class="panel-heading">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
					<h3 class="panel-title">Delete Domain Confirmation</h3>
				</div>
				<div class="panel-body">
					<p>This will delete the domain.  This cannot be undone, would you like to continue?</p>
				</div>
				<div class="panel-footer">
						<button id="vendorDomainDelete" class="btn btn-danger">Yes</button>
						<button  data-dismiss="modal" aria-hidden="true" class="btn btn-dark">No</button>
				</div><!-- panel-footer -->
			</div>
		</div>
	</div>
</div>

<div class="modal fade confirm-delete-ip" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="panelt panel-danger">
				<div class="panel-heading">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
					<h3 class="panel-title">Delete IP Address Confirmation</h3>
				</div>
				<div class="panel-body">
					<p>This will delete the IP address.  This cannot be undone, would you like to continue?</p>
				</div>
				<div class="panel-footer">
						<button id="vendorIpDelete" class="btn btn-danger">Yes</button>
						<button  data-dismiss="modal" aria-hidden="true" class="btn btn-dark">No</button>
				</div><!-- panel-footer -->
			</div>
		</div>
	</div>
</div>