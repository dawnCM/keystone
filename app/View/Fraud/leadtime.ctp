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
					<li>Lead Time Fraud</li>
				</ul>
				<h4>Lead Time Fraud</h4>
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
						<div class="pull-right"><a id="export-leads" class="new-msg" style="cursor:pointer;"><i class="fa fa-download"></i></a></div>
						<h3 class="panel-title">Lead Time < 2 Minutes</h3>
					</div>
					<div class="panel-body">
						<div id="tableLoader"><img src="/images/loaders/sand_small.svg"></div>
						<div class="table-responsive">
							<table id="leadtimeTable" class="table table-hover table-bordered responsive" style="display:none;">
								<thead>
									<tr>
										<th>Affiliate ID</th>
										<th>Lead ID</th>
										<th>Offer ID</th>
										<th>S1</th>
										<th>S2</th>
										<th>S3</th>
										<th>S4</th>
										<th>Rec/Pay</th>
										<th>Lead Time</th>
										<th>Date/Time</th>
									</tr>
								</thead>
								<tbody id="leadtimeTableBody"></tbody>
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
							<div class="col-sm-12">
								<select id="select-affiliate" data-placeholder="Choose One" class="width300">
								<option value="">Filter by Affiliate</option>
									<?php foreach($affiliate_list as $key=>$aff){										
										if($aff['Affiliate']['remote_id'] != 0){
											echo '<option value="'.$aff['Affiliate']["remote_id"].'">'.$aff['Affiliate']['affiliate_name'].' ('.$aff['Affiliate']['remote_id'].')</option.>';
										}
									}?>
								</select>
							</div>
						</div>
						
						<div id="subid_fields" style="display:none;">
							<div class="form-group">
								<div class="col-sm-6">
									<input id="subid1" type="text" placeholder="Sub ID 1" class="form-control input-sm">
								</div>
								<div class="col-sm-6">
									<input id="subid2" type="text" placeholder="Sub ID 2" class="form-control input-sm">
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-6">
									<input id="subid3" type="text" placeholder="Sub ID 3" class="form-control input-sm">
								</div>
								<div class="col-sm-6">
									<input id="subid4" type="text" placeholder="Sub ID 4" class="form-control input-sm">
								</div>
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

<div class="modal fade missing-affiliate" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="panel panel-danger">
				<div class="panel-heading">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
					<h3 class="panel-title">Request Error</h3>
				</div>
				<div class="panel-body">
					<p>Please select an affiliate for this report.</p>
				</div>
			</div>
		</div>
	</div>
</div>