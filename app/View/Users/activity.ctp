<div class="mainpanel">
	<div class="pageheader">
		<div class="media">
			<div class="pageicon pull-left">
				<i class="fa fa-users"></i>
			</div>
			<div class="media-body">
				<ul class="breadcrumb">
					<li><a href="/dashboard"><i class="glyphicon glyphicon-home"></i></a></li>
					<li><a href="/users">User Management</a></li>
					<li>Activity Log</li>
				</ul>
				<h4>Activity Log</h4>
			</div>
		</div>
		<!-- media -->
	</div>
	<!-- pageheader -->

	<div class="contentpanel">
		<?php echo $this->Session->flash(); ?>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">User Audit Log: <span id="username"><?php echo ucfirst($this->Session->read('Auth.User.full_name'))?></span></h3>
					</div>
					<div class="panel-body">
						<table id="auditTable"
							class="table table-striped table-bordered responsive">
							<thead class="">
								<tr>
									<th>Change Type</th>
									<th>Model</th>
									<th>Property</th>
									<th>Previous</th>
									<th>New</th>
									<th>Date</th>
								</tr>
							</thead>
							<tbody>
							<?php 
							foreach($audit_trail as $key=>$audit){
								if(count($audit['AuditDelta']) == 0){
									$audit['AuditDelta'][0]['property_name'] = '-';
									$audit['AuditDelta'][0]['old_value'] = '-';
									$audit['AuditDelta'][0]['new_value'] = '-';
								}
								$date = date("F d, Y h:ia", strtotime($audit['Audit']['created']));
								echo "<tr>
								<td>{$audit['Audit']['event']}</td>
								<td>{$audit['Audit']['model']}</td>
								<td>{$audit['AuditDelta'][0]['property_name']}</td>
								<td>{$audit['AuditDelta'][0]['old_value']}</td>
								<td>{$audit['AuditDelta'][0]['new_value']}</td>
								<td>{$date}</td>
								</tr>";
							}
							?>
							</tbody>
						</table>
					</div>
					<!-- panel-body -->
				</div>
			</div>
		</div>
		<!-- row -->
	</div>
	<!-- contentpanel -->
</div>
<!-- mainpanel -->