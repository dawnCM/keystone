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
			<div class="col-md-8">
				<div class="panel panel-default">
					<div class="panel-heading">
						<div class="pull-right"><a id="export-leads" class="new-msg" style="cursor:pointer;"><i class="fa fa-download"></i></a></div>
						<h3 class="panel-title">Unsold Leads</h3>
					</div>
					<div class="panel-body">
						<div id="tableLoader"><img src="/images/loaders/sand_small.svg"></div>
						<div class="table-responsive">
							<table id="leadTable" class="table table-hover table-bordered responsive" style="display:none;">
								<thead>
									<tr>
										<th>Offer ID</th>
										<th>Affiliate</th>
										<th>Track ID</th>
										<th>Date/Time</th>
										<th>Sold</th>
									</tr>
								</thead>
								<tbody id="leadTableBody"></tbody>
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
						<div class="form-group">
							<div class="col-sm-6">
								<select id="select-offer" name="select-offer" data-placeholder="Choose One" class="width300">
								<option value="">Select Affiliate</option>
								<?php foreach($offer_list as $key=>$offer){										
									echo '<option value="'.$key.'">'.$offer.'</option>';
								}?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-6">
								<select id="select-affiliate" name="select-affiliate" data-placeholder="Choose One" class="width300">
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
						<button class="btn btn-primary" id="calc-redirect">Calculate Redirect</button>
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