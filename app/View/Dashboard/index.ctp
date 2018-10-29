<div class="mainpanel">
	<div class="pageheader">
		<div class="media">
			<div class="pageicon pull-left">
				<i class="fa fa-home"></i>
			</div>
			<div class="media-body">
				<ul class="breadcrumb">
					<li><a href=""><i class="glyphicon glyphicon-home"></i> </a></li>
					<li>Dashboard</li>
				</ul>
				<h4>Dashboard</h4>
			</div>
		</div>
		<!-- media -->
	</div>
	<!-- pageheader -->

	<div class="contentpanel">
		<?php echo $this->Session->flash(); ?>
		<div class="row row-stat">
			<div class="col-md-4">
				<div class="panel panel-success-alt noborder">
					<div class="panel-heading noborder">
						<div class="panel-icon">
							<i class="fa fa-users"></i>
						</div>
						<div class="media-body">
							<h5 class="md-title nomargin">Generated Leads</h5>
							<h1 id="gl-today" class="mt5"><img src="/images/loaders/sand_white_32.svg"></h1>
						</div>
						<!-- media-body -->
						<hr>
						<div class="clearfix mt20">
							<div class="col-md-4 col-xs-4">
								<h5 class="md-title nomargin">Yesterday</h5>
								<h4 id="gl-yesterday" class="nomargin">0</h4>
							</div>
							<div class="col-md-4 col-xs-4">
								<h5 class="md-title nomargin">This Week</h5>
								<h4  id="gl-week"class="nomargin">0</h4>
							</div>
							<div class="col-md-4 col-xs-4">
								<h5 class="md-title nomargin">This Month</h5>
								<h4  id="gl-month"class="nomargin">0</h4>
							</div>
						</div>

					</div>
					<!-- panel-body -->
				</div>
				<!-- panel -->
			</div>
			<!-- col-md-4 -->

			<div class="col-md-4">
				<div class="panel panel-primary noborder">
					<div class="panel-heading noborder">
						<div class="panel-icon">
							<i class="fa fa-user"></i>
						</div>
						<div class="media-body">
							<h5 class="md-title nomargin">Sold Leads</h5>
							<h1 id="sl-today" class="mt5"><img src="/images/loaders/sand_white_32.svg"></h1>
						</div>
						<!-- media-body -->
						<hr>
						<div class="clearfix mt20">
							<div class="col-md-4 col-xs-4">
								<h5 class="md-title nomargin">Yesterday</h5>
								<h4  id="sl-yesterday"class="nomargin">0</h4>
							</div>
							<div class="col-md-4 col-xs-4">
								<h5 class="md-title nomargin">This Week</h5>
								<h4  id="sl-week"class="nomargin">0</h4>
							</div>
							<div class="col-md-4 col-xs-4">
								<h5 class="md-title nomargin">This Month</h5>
								<h4  id="sl-month"class="nomargin">0</h4>
							</div>
						</div>

					</div>
					<!-- panel-body -->
				</div>
				<!-- panel -->
			</div>
			<!-- col-md-4 -->

			<div class="col-md-4">
				<div class="panel panel-dark noborder">
					<div class="panel-heading noborder">
						<div class="panel-icon">
							<i class="fa fa-dollar"></i>
						</div>
						<div class="media-body">
							<h5 class="md-title nomargin">Revenue</h5>
							<h1 id="pr-today" class="mt5"><img src="/images/loaders/sand_white_32.svg"></h1>
						</div>
						<!-- media-body -->
						<hr>
						<div class="clearfix mt20">
							<div class="col-md-4 col-xs-4">
								<h5 class="md-title nomargin">Yesterday</h5>
								<h4 id="pr-yesterday" class="nomargin">$0</h4>
							</div>
							<div class="col-md-4 col-xs-4">
								<h5 class="md-title nomargin">This Week</h5>
								<h4 id="pr-week" class="nomargin">$0</h4>
							</div>
							<div class="col-md-4 col-xs-4">
								<h5 class="md-title nomargin">This Month</h5>
								<h4 id="pr-month" class="nomargin">$0</h4>
							</div>
						</div>

					</div>
					<!-- panel-body -->
				</div>
				<!-- panel -->
			</div>
			<!-- col-md-4 -->
		</div>
		<!-- row -->
		<div class="row">
			<div class="col-md-4">
				<div class="panel panel-default">
					<div class="panel-body padding15">
						<h5 class="md-title mt0 mb10">Generated Leads</h5>
						<div id="basicFlotLegend" class="flotLegend"></div>
						<div id="genleadflot" class="flotChart"></div>
					</div>
					<!-- panel-body -->
				</div>
				<!-- panel -->
			</div>

			<div class="col-md-4">
				<div class="panel panel-default">
					<div class="panel-body padding15">
						<h5 class="md-title mt0 mb10">Sold Leads</h5>
						<div id="basicFlotLegend2" class="flotLegend"></div>
						<div id="soldleadflot" class="flotChart"></div>
					</div>
					<!-- panel-body -->
				</div>
				<!-- panel -->
			</div>

			<div class="col-md-4">
				<div class="panel panel-default">
					<div class="panel-body padding15">
						<h5 class="md-title mt0 mb10">Revenue</h5>
						<div id="basicFlotLegend3" class="flotLegend"></div>
						<div id="profitflot" class="flotChart"></div>
					</div>
					<!-- panel-body -->
				</div>
				<!-- panel -->
			</div>

		</div>
		<!-- row -->
	</div>
	<!-- contentpanel -->
</div>
<!-- mainpanel -->