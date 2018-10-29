<div class="mainpanel">
	<div class="pageheader">
		<div class="media">
			<div class="pageicon pull-left">
				<i class="fa fa-edit"></i>
			</div>
			<div class="media-body">
				<ul class="breadcrumb">
					<li><a href="/dashboard"><i class="glyphicon glyphicon-home"></i></a></li>
					<li><a href="/dashboard">Reports</a></li>
					<li>Receivables</li>
				</ul>
				<h4>Receivables</h4>
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
						<h3 class="panel-title">Receivables</h3>
					</div>
					<div class="panel-body">
						<div id="tableLoader"><img src="/images/loaders/sand_small.svg"></div>
						<div class="table-responsive">
							<table id="receivablesTable" class="table table-striped table-bordered responsive" style="display:none;">
								<thead>
									<tr>
										<th>Offer ID</th>
										<th>Affiliate ID</th>
										<th>Track ID</th>
										<th>Date/Time</th>
										<th>Sold</th>
									</tr>
								</thead>
								<tbody id="receivablesTableBody"></tbody>
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
						<h3 class="panel-title" id="overview-title">Search</h3>
					</div>
					<div class="panel-body" >
						<div class="form-group">
							<div class="col-sm-6">
								<div class="input-group input-group-sm">
									<input type="text" placeholder="Start Date" class="form-control" id="startdate">
									<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="input-group input-group-sm">
									<input type="text" placeholder="End Date" class="form-control" id="enddate">
									<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-3">
								<div class="rdio rdio-primary">
									<input type="radio" name="radio" id="custom-date" value="1" checked="checked">
									<label for="custom-date">Custom</label>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="rdio rdio-primary">
									<input type="radio" name="radio" id="yesterday-date" value="1">
									<label for="yesterday-date">Yesterday</label>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="rdio rdio-primary">
									<input type="radio" name="radio" id="week-date" value="1">
									<label for="week-date">Week</label>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="rdio rdio-primary">
									<input type="radio" name="radio" id="month-date" value="1">
									<label for="month-date">Month</label>
								</div>
							</div>
						</div>
						<hr>
						<div class="form-group">
							<div class="col-sm-6">
								<select id="affiliateid" class="form-control lead-select">
									<option value="">Select Affiliate</option>								
									<option value="1">Affiliate Name</option>
								</select>
							</div>
							<div class="col-sm-6">
								<select id="offerid" class="form-control lead-select">
									<option value="">Select Campaign</option>								
									<option value="1">Campaign Name</option>
								</select>
							</div>
						</div>
					</div>
					<!-- panel-body -->
					<div class="panel-footer">
						<button class="btn btn-primary" id="search-leads">Search</button>
					</div><!-- panel-footer -->
				</div>
			</div>
		</div>
		<!-- row -->
	</div>
	<!-- contentpanel -->
</div>
<!-- mainpanel -->