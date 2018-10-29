<div class="mainpanel">
	<div class="pageheader">
		<div class="media">
			<div class="pageicon pull-left">
				<i class="fa fa-legal"></i>
			</div>
			<div class="media-body">
				<ul class="breadcrumb">
					<li><a href="/dashboard"><i class="glyphicon glyphicon-home"></i></a></li>
					<li><a href="/dashboard">Dashboard</a></li>
					<li>Fraud</li>
					<li>IP</li>
				</ul>
				<h4>IP Management</h4>
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
							<h3 class="panel-title">Blacklisted IP's</h3>
						</div>
						<div class="panel-body">
							<div id="tableLoader" style="display:none;"><img src="/images/loaders/sand_small.svg"></div>
							<div class="table-responsive">
								<table id="ipTable" class="table table-hover table-bordered responsive">
									<thead>
										<tr>
											<th>ID</th>
											<th>IP Address</th>
											<th>Host</th>
											<th>City</th>
											<th>State</th>
											<th>Date/Time</th>
										</tr>
									</thead>
									<tbody id="ipTableBody">
									<?php 
										foreach($ip_list as $key=>$value){
											echo "<tr>";
											echo "<td>{$value['Ip']['id']}</td>";
											echo "<td>{$value['Ip']['ntoa_ip']}</td>";
											echo "<td>{$value['Ip']['hostname']}</td>";
											echo "<td>{$value['Ip']['city']}</td>";
											echo "<td>{$value['Ip']['region']}</td>";
											echo "<td>{$value['Ip']['modified']}</td>";
											echo "</tr>";
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
							<div class="panel-btns">
								<a href="" class="panel-minimize maximize tooltips" data-placement="left" data-toggle="tooltip" title="" data-original-title="Maximize Panel"><i class="fa fa-plus"></i> </a>
							</div>
							<!-- panel-btns -->
							<h3 class="panel-title" id="overview-title">Add / Remove IP</h3>
						</div>
						<div class="panel-body">
							<form action="/fraud/ip" id="IpIpForm" method="post" accept-charset="utf-8">
							<div style="display:none;">
							<input type="hidden" name="_method" value="POST">
							<input type="hidden" id="ipaction" name="ipaction" value="blacklist">
							</div>							
							<div class="form-group">
								<input type="text" id="ip" class="form-control" name="ip" placeholder="IP Address">
							</div>
						</div>
						<!-- panel-body -->
						<div class="panel-footer">
							<button class="btn btn-primary" id="add-ip">Blacklist IP</button>
							<button class="btn btn-success" id="remove-ip">Whitelist IP</button>
							</form>
						</div><!-- panel-footer -->
					</div>
				</div>
			</div>
		<!-- row -->
	</div>
	<!-- contentpanel -->
</div>
<!-- mainpanel -->