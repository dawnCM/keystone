<div class="mainpanel">
	<div class="pageheader">
		<div class="media">
			<div class="pageicon pull-left">
				<i class="fa fa-sitemap"></i>
			</div>
			<div class="media-body">
				<ul class="breadcrumb">
					<li><a href="/dashboard"><i class="glyphicon glyphicon-home"></i></a></li>
					<li>Affiliates</li>
				</ul>
				<h4>Affiliate List</h4>
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
						<h3 class="panel-title">Affiliates</h3>
					</div>
					<div class="panel-body">
						<div id="tableLoader"><img src="/images/loaders/sand_small.svg"></div>
						<div class="table-responsive">
						<table id="affiliateTable" class="table table-striped table-bordered responsive" style="display:none;">
							<thead>
								<tr>
									<th class="sorting_disabled"></th>
									<th>ID</th>
									<th>Name</th>
									<th>Account</th>
									<th>MTD Clicks</th>
									<th>MTD Conv</th>
									<th>MTD Rate</th>
									<th>MTD EPC</th>
									<th>MTD Rev</th>
								</tr>
							</thead>
							<tbody id="affiliateTableBody"></tbody>
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
							<a href="" class="panel-minimize maximize tooltips" data-placement="left" data-toggle="tooltip" title="" data-original-title="Maximize Panel"><i class="fa fa-plus"></i> </a>
						</div>
						<!-- panel-btns -->
						<h3 class="panel-title" id="overview-title">Details</h3>
					</div>
					<div class="panel-body" style="display:none;">
					<address>
		                <span id="overview-name"></span><br/>
		                <span id="overview-address"></span><br/>
		                <span id="overview-city"></span><br/><br/>
		                
		                <span id="overview-url"></span><br/><br/>
		                
		                
		                
		                <span id="overview-acctmgr"></span>
	             	</address>	
					</div>
					<!-- panel-body -->
					<div class="panel-footer" style="display: none;">

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