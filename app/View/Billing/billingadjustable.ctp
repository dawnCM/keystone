<div class="mainpanel">
	<div class="pageheader">
		<div class="media">
			<div class="pageicon pull-left">
				<i style="margin-left:5px;" class="fa fa-dollar"></i>
			</div>
			<div class="media-body">
				<ul class="breadcrumb">
					<li><a href="/dashboard"><i class="glyphicon glyphicon-home"></i></a></li>
					<li><a href="/billing/billingadjustable">Billing</a></li>
					<li>Billing Adjustable</li>
				</ul>
				<h4>Billing Adjustable</h4>
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
						<h3 class="panel-title">Billing Adjustments</h3>
					</div>
					<div class="panel-body">
						<div id="tableLoader2" ><img src="/images/loaders/sand_small.svg"></div>
						<div class="table-responsive">
						<table id="billingAdjustmentTable" class="table table-hover table-bordered responsive" >
							<thead>
								<tr>
									<th>ID</th>
									<th>Type</th>
									<th>Affiliate</th>
									<th>Campaign ID</th>
									<th>Adjustment Date</th>
									<th>Effective Date</th>
									<th>Price</th>
								</tr>
							</thead>
							<tbody id="billingAdjustmentTableBody">
					
								
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
						<h3 class="panel-title" id="overview-title">Billing Adjustment Entry</h3>
					</div>
					<div class="panel-body" >
						<div class="form-group">
							<div class="col-sm-6">
								<div class="input-group input-group-sm">
									<input type="text" placeholder="Adjustable Date" class="form-control" id="adjustment_date" name="adjustment_date">
									<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-6">
								<select id="select-adjust-type" name="select-adjust-type" data-placeholder="Choose One" class="width300">
									<option value="">Select Adjustable Type</option>
									<option value="clawback">ClawBack</option>
									<option value="makegood">Make Good</option>
									<option value="buyercredit">Buyer Credit</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-6">
								<select id="select-affiliate" name="select-affiliate" data-placeholder="Choose One" class="width300">
								<option value="">Select Affiliate</option>
								<?php foreach($affiliate_list as $key=>$aff){										
									echo '<option value="'.$key.'">'.$aff.'</option>';
								}?>
								</select>
							</div>
						</div>
						
						<div class="form-group" >
							<div id="tableLoader" style = "display:none"><img src="/images/loaders/sand_small.svg"></div>
						</div>
						<span id = "adjustable-fields" style = "display:none">
							<div class="form-group">
								<div class="col-sm-6">
									<select id="select-campaign" name="select-campaign" data-placeholder="Choose One" class="width300">
									
									</select>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-4">
									<input type="text" placeholder="Adjustable Price" class="form-control input-sm" id="price" name="price">
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-4">
									<input type="text" placeholder="Creative ID" class="form-control input-sm" id="creative-id" name="creative-id">
								</div>
							</div>
						</span>
				
						<hr>
						
					</div>
					<!-- panel-body -->
					<div class="panel-footer">
						<button class="btn btn-primary" id="add-adjustable" style = "display:none">Process Adjustment</button>
						<div id="tableLoader3" style = "display:none"><img src="/images/loaders/sand_small.svg"></div>
					</div><!-- panel-footer -->
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-md-8">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Billing Adjustments Totals By Affiliate</h3>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
						<table id="billingAdjustmentTotalTable" class="table table-hover table-bordered responsive" >
							<thead>
								<tr>
									<th>Affiliate</th>
									<th>Type</th>
									<th>Start Date</th>
									<th>End Date</th>
									<th>Total Amount</th>
								</tr>
							</thead>
							<tbody>
								
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
						<h3 class="panel-title" id="overview-title">Billing Adjustment Totals</h3>
					</div>
					<div class="panel-body" >
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
							<div class="col-sm-6">
								<select id="select-affiliate-total" name="select-affiliate-total" data-placeholder="Choose One" class="width300">
								<option value="">Select Affiliate</option>
								<?php foreach($affiliate_list as $key=>$aff){										
									echo '<option value="'.$key.'">'.$aff.'</option>';
								}?>
								</select>
							</div>
						</div>
				
						<hr>
						
					</div>
					<!-- panel-body -->
					<div class="panel-footer">
						<button class="btn btn-primary" id="adjustable-totals">Adjustment Totals</button>
						<div id="tableLoader4" style = "display:none"><img src="/images/loaders/sand_small.svg"></div>
					</div><!-- panel-footer -->
				</div>
			</div>
		</div>
		<!-- row -->
	</div>
	<!-- contentpanel -->
</div>
<!-- mainpanel -->
