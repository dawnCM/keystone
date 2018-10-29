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
					<li>Unsold Leads</li>
				</ul>
				<h4>Unsold Leads</h4>
			</div>
		</div>
		<!-- media -->
	</div>
	<!-- pageheader -->

	<div class="contentpanel">
		<?php echo $this->Session->flash(); ?>
		<div class="row">
			<div class="col-md-9">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Unsold Leads</h3>
					</div>
					<div class="panel-body">
						<div id="unsoldtableLoader" style="display:none;"><img src="/images/loaders/sand_small.svg"></div>
						<div class="table-responsive">
							<table id="unsoldTable" class="table table-hover table-bordered responsive" >
								<thead>
										<tr><th>Date</th>
										<th>Total Leads</th>
										<th>Sales</th>
										<th>Total Unsold</th>
										<th>Incompletes</th>
										<th>No Buyer</th>
										<th>Duplicates</th>
										<th>Blacklisted IP</th>
										<th>Lead Time</th>
										</tr>
								</thead>
								<tbody id="unsoldTableBody"></tbody>
							</table>
						</div>
					</div>
					<!-- panel-body -->
				</div>
				
				<div class="row">
					<div class="col-md-6">
						<!-- Chart Panel -->
						<div class="panel panel-default">
							<div class="panel-heading">
								<h3 class="panel-title">Sold/Unsold Totals <span id = "unsold-sold-chart-display"></span></h3>
							</div>
							<div class="panel-body">
								<div id="unsold-sold-chart" width="auto" height="auto"></div>	
							</div>
						</div>
						<!-- End Chart Panel-->
					</div>
					<div class="col-md-6">
						<!-- Chart Panel -->
						<div class="panel panel-default">
							<div class="panel-heading">
								<h3 class="panel-title">Unsold/Errors Totals <span id = "unsold-error-chart-display"></span></h3>
							</div>
							<div class="panel-body">
								<div id="unsold-error-chart" width="auto" height="auto"></div>	
							</div>
						</div>
						<!-- End Chart Panel-->
					</div>
				</div>
				
				
				
				
			</div>
			<div class="col-md-3">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<div class="panel-btns" style="display: none;">
							<a href="" class="panel-minimize tooltips" data-placement="left"
								data-toggle="tooltip" title=""
								data-original-title="Minimize Panel"><i class="fa fa-minus"></i>
							</a>
						</div>
						<!-- panel-btns -->
						<h3 class="panel-title">Search</h3>
					</div>
					<div class="panel-body">
						<div class="row">
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
						<div class="form-group">
							<div class="col-sm-6">
								<select id="select-offer-unsold" name="select-offer-unsold" data-placeholder="Choose One" class="width300">
								<option value="">Select Offer</option>
								<?php foreach($offer_list as $key=>$offer){										
									echo '<option value="'.$key.'">'.$offer.'</option>';
								}?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-6">
								<select id="select-affiliate-unsold" name="select-affiliate-unsold" data-placeholder="Choose One" class="width300">
								<option value="">Select Affiliate</option>
								<?php foreach($affiliate_list as $key=>$aff){										
									echo '<option value="'.$key.'">'.$aff.'</option.>';
								}?>
								</select>
							</div>
						</div>
						</div>
						<!-- form-row-1 -->
					</div>
					<!-- panel-body -->
					<div class="panel-footer">
						<button class="btn btn-primary" id="unsold-generate">Generate Report</button>
					</div>
					<!-- panel-footer -->
				</div>
				<!-- panel -->
			</div>
		</div>
		<!-- row -->
	</div>
	<!-- contentpanel -->
</div>
<!-- mainpanel -->