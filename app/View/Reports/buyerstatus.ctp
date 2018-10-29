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
					<li>Buyer Status Report</li>
				</ul>
				<h4>Buyer Contract Status</h4>
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
						<div class="pull-right"><a id="export-report" class="new-msg" style="cursor:pointer;"><i class="fa fa-download"></i></a></div>
						<h3 class="panel-title">Buyer <span id="contractname"></span> <span id="treename"></span></h3>
					</div>
					<div class="panel-body">
						<div id="tableLoader" style="display:none;"><img src="/images/loaders/sand_small.svg"></div>
						<div class="table-responsive">
						<table id="contractoverviewTable" class="table table-hover table-bordered responsive">
							<thead>
								<tr>
									<th>Name</th>
									<th>Sent</th>
									<th>Sold</th>
									<th>Accept %</th>
									<th>Decline %</th>
									<th>Error %</th>
									<th>Timeout %</th>
									<th>Duplicate %</th>
									<th>Revenue</th>
									<th>EPL</th>
								</tr>
							</thead>
							<tbody id="contractoverviewTableBody">
								<tr><td colspan="10">No data available in table</td></tr>
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
						<h3 class="panel-title" id="overview-title">Report Criteria</h3>
					</div>
					<div class="panel-body" >
						<div class="form-group" id="displaydates">
							<div class="col-sm-6">
								<div class="input-group input-group-sm">
									<input type="text" placeholder="Start Date" class="form-control" id="startdate" name="startdate">
									<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="input-group input-group-sm">
									<input type="text" placeholder="End Date" class="form-control" id="enddate" name="enddate">
									<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-4">
								<div class="rdio rdio-primary">
									<input type="radio" name="radio" id="custom-date" value="1" checked="checked">
									<label for="custom-date">Custom</label>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="rdio rdio-primary">
									<input type="radio" name="radio" id="yesterday-date" value="1">
									<label for="yesterday-date">Yesterday</label>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="rdio rdio-primary">
									<input type="radio" name="radio" id="week-date" value="1">
									<label for="week-date">Week</label>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-4">
								<div class="rdio rdio-primary">
									<input type="radio" name="radio" id="month-date" value="1">
									<label for="month-date">Month</label>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="rdio rdio-primary">
									<input type="radio" name="radio" id="pmonth-date" value="1">
									<label for="pmonth-date">Prev Month</label>
								</div>
							</div>
						</div>
						<hr>
						<div class="form-group">
							<div class="col-sm-6">
								<select id="select-pingtree" class="width300">
								<option value="0">Select Pingtree</option>
									<?php 
									foreach($pingtree_list as $buyer_id=>$buyer_name){										
										echo '<option value="'.$buyer_id.'">'.$buyer_name.'</option>';
									}?>
								</select>
							</div>
						</div>
						<div id="buyer-container" class="form-group" style="display:none;">
							<div class="col-sm-6">
								<select id="select-buyer" name="buyer" multiple data-placeholder="Choose One" class="width300">
								<option value="">Select Buyer</option>
								</select>
							</div>
						</div>
					</div>
					<!-- panel-body -->
					<div class="panel-footer">
						<button class="btn btn-primary" id="contract-status" style="display:none;">Generate Report</button>
						<button type="reset" id="report-reset" class="btn btn-default">Reset</button>
					</div><!-- panel-footer -->
				</div>
			</div>
		</div>
		<!-- row -->
	</div>
	<!-- contentpanel -->
</div>
<!-- mainpanel -->