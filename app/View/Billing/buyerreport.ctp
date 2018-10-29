<div class="mainpanel">
	<div class="pageheader">
		<div class="media">
			<div class="pageicon pull-left">
				<i style="margin-left:5px;" class="fa fa-dollar"></i>
			</div>
			<div class="media-body">
				<ul class="breadcrumb">
					<li><a href="/dashboard"><i class="glyphicon glyphicon-home"></i></a></li>
					<li><a href="/billing/buyerreport">Billing</a></li>
					<li>Buyer Report</li>
				</ul>
				<h4>Buyer Billing Report</h4>
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
						<h3 class="panel-title">Downloadable Billing Reports</h3>
					</div>
					<div class="panel-body">
						<div id="tableLoader"><img src="/images/loaders/sand_small.svg"></div>
						<div class="table-responsive">
						<table id="billingTable" class="table table-hover table-bordered responsive" style="display:none;">
							<thead>
								<tr>
									<th>ID</th>
									<th>Buyer</th>
									<th>Start Date</th>
									<th>End Date</th>
									<th>Report Date</th>
									<th>Download</th>
								</tr>
							</thead>
							<tbody id="billingTableBody"></tbody>
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
						<form action="/billing/add" id="addreport" method="post" accept-charset="utf-8"><div style="display:none;"><input type="hidden" name="_method" value="POST"/></div>
						<div class="form-group">
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
								<select id="select-buyer" name="buyer" data-placeholder="Choose One" class="width300">
								<option value="">Select Billing Group</option>
								<?php foreach($buyer_list as $key=>$buyer){										
									echo '<option value="'.$key.'">'.$buyer.'</option.>';
								}?>
								</select>
							</div>
						</div>
					</div>
					<!-- panel-body -->
					<div class="panel-footer">
						<button class="btn btn-primary" id="search-leads">Generate Report</button>
						<?php echo $this->Form->end(); ?>
					</div><!-- panel-footer -->
				</div>
			</div>
		</div>
		<!-- row -->
	</div>
	<!-- contentpanel -->
</div>
<!-- mainpanel -->

<div class="modal fade confirm-delete-bill" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="panelt panel-danger">
				<div class="panel-heading">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
					<h3 class="panel-title">Delete Billing Report Confirmation</h3>
				</div>
				<div class="panel-body">
					<p>This will delete the Billing report.  This cannot be undone, would you like to continue?</p>
				</div>
				<div class="panel-footer">
						<button id="billDelete" class="btn btn-danger">Yes</button>
						<button  data-dismiss="modal" aria-hidden="true" class="btn btn-dark">No</button>
				</div><!-- panel-footer -->
			</div>
		</div>
	</div>
</div>