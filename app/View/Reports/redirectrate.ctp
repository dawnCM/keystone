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
					<li>Redirect Rate</li>
				</ul>
				<h4>Redirect Rate</h4>
			</div>
		</div>
		<!-- media -->
	</div>
	<!-- pageheader -->

	<div class="contentpanel">
		<?php echo $this->Session->flash(); ?>
		<div class="row">
			<div class="col-md-8">
				<div class="panel panel-default" id="custom-redirect" style="display:none;">
					<div class="panel-heading">
						<h3 class="panel-title" id="custom-redirect-title"></h3>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-6">
								<div>
									<div class="md-title" style="float: left;">Today</div>
									<div class="today-redirect-1 text-primary" style="float: right;"></div>
								</div>
								<div style="clear:both;" class="progress progress-striped active">
									<div aria-valuemax="100" aria-valuemin="0" role="progressbar" class="today-redirect-2 progress-bar"></div>
								</div>
								<div>
									<div class="md-title" style="float: left; margin-top:15px;">Yesterday</div>
									<div class="yesterday-redirect-1 text-primary" style="float: right; margin-top:15px;"></div>
								</div>
								<div style="clear: both;" class="progress progress-striped active">
									<div aria-valuemax="100" aria-valuemin="0" role="progressbar" class="yesterday-redirect-2 progress-bar"></div>
								</div>
								<div>
									<div class="md-title" style="float: left; margin-top:15px;" id="redirect-title">7 day average</div>
									<div class="avg-redirect-1 text-primary" style="float: right; margin-top:15px;"></div>
								</div>
								<div style="clear: both;" class="progress progress-striped active">
									<div aria-valuemax="100" aria-valuemin="0" role="progressbar" class="avg-redirect-2 progress-bar"></div>
								</div>
								<div>
									<div class="md-title" style="float: left; margin-top:15px;" id="redirect-title">Selected Dates</div>
									<div class="custom-redirect-1 text-primary" style="float: right; margin-top:15px;"></div>
								</div>
								<div style="clear: both;" class="progress progress-striped active">
									<div aria-valuemax="100" aria-valuemin="0" role="progressbar" class="custom-redirect-2 progress-bar"></div>
								</div>
							</div>
							<div class="col-md-6">
								<div id="custom-stacked-chart-default" style="height:250px"></div>
							</div>
						</div>
					</div>
					<!-- panel-body -->
				</div>
				<!-- End Custom -->
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title" id="vendor-redirect-title">Vendor Summary</h3>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-6">
								<div>
									<div class="md-title" style="float: left;">Today</div>
									<div class="text-primary" style="float: right;">
										<strong><?php echo $today_redirect_rate; ?>%</strong>
									</div>
								</div>
								<div style="clear: both;" class="progress progress-striped active">
									<?php switch(true){
											case $today_redirect_rate>=80:
												$bar_color='success';
											break;
											case $today_redirect_rate>=70:
												$bar_color='warning';
											break;
											case $today_redirect_rate<=69:
												$bar_color='danger';
											break;
									 }?>
									<div style="width: <?php echo $today_redirect_rate; ?>%" aria-valuemax="100" aria-valuemin="0"
										aria-valuenow="<?php echo $today_redirect_rate; ?>" role="progressbar" class="progress-bar progress-bar-<?php echo $bar_color; ?>"></div>
								</div>
								<div>
									<div class="md-title" style="float: left; margin-top:15px;">Yesterday</div>
									<div class="text-primary" style="float: right; margin-top:15px;">
										<strong><?php echo $yesterday_redirect_rate; ?>%</strong>
									</div>
								</div>
								<div style="clear: both;" class="progress progress-striped active">
								<?php switch(true){
											case $yesterday_redirect_rate>=80:
												$bar_color='success';
											break;
											case $yesterday_redirect_rate>=70:
												$bar_color='warning';
											break;
											case $yesterday_redirect_rate<=69:
												$bar_color='danger';
											break;
									 }?>
									<div style="width: <?php echo $yesterday_redirect_rate; ?>%" aria-valuemax="100" aria-valuemin="0"
										aria-valuenow="<?php echo $yesterday_redirect_rate; ?>" role="progressbar" class="progress-bar progress-bar-<?php echo $bar_color; ?>"></div>
								</div>
								<div>
									<div class="md-title" style="float: left; margin-top:15px;" id="redirect-title">7 day average</div>
									<div class="text-primary" style="float: right; margin-top:15px;">
										<strong><?php echo $overall_redirect_rate; ?>%</strong>
									</div>
								</div>
								<div style="clear: both;" class="progress progress-striped active">
									<?php switch(true){
											case $overall_redirect_rate>=80:
												$bar_color='success';
											break;
											case $overall_redirect_rate>=70:
												$bar_color='warning';
											break;
											case $overall_redirect_rate<=69:
												$bar_color='danger';
											break;
									 }?>
									<div style="width: <?php echo $overall_redirect_rate; ?>%" aria-valuemax="100" aria-valuemin="0"
										aria-valuenow="<?php echo $overall_redirect_rate; ?>" role="progressbar" class="progress-bar progress-bar-<?php echo $bar_color; ?>"></div>
								</div>
							</div>
							<div class="col-md-6">
								<script>
								//-----------------------------------Redirect Report---------------------------------------//
								jQuery(document).ready(function(){ 
									if(jQuery('#vendor-redirect-title').length>0){
										var m4 = new Morris.Bar({
											// ID of the element in which to draw the chart.
											element: 'vendor-stacked-chart-default',
										    data: [
										        { y: 'Today', a: <?php echo $today_sold; ?>,  b: <?php echo $today_redirected; ?> },
										        { y: 'Yesterday', a: <?php echo $yesterday_sold; ?>,  b: <?php echo $yesterday_redirected; ?> },
										        { y: '7d Avg', a: <?php echo $overall_sold; ?>, b: <?php echo $overall_redirected; ?> },
										    ],
										    xkey: 'y',
										    ykeys: ['a', 'b'],
										    labels: ['Sold', 'Redirected'],
										    barColors: ['#428bca', '#1caf9a'],
										    lineWidth: '1px',
										    fillOpacity: 0.8,
										    smooth: true,
										    stacked: true,
										    hideHover: true
										});
									}
								});
								</script>
								<div id="vendor-stacked-chart-default" class="height175"></div>
							</div>
						</div>
					</div>
					<!-- panel-body -->
				</div>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title" id="affiliate-redirect-title">Affiliate Summary</h3>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-6">
								<div>
									<div class="md-title" style="float: left; margin-top:15px;">Today</div>
									<div class="text-primary" style="float: right; margin-top:15px;">
										<strong><?php echo $aff_today_redirect_rate; ?>%</strong>
									</div>
								</div>
								<div style="clear: both;" class="progress progress-striped active">
									<?php switch(true){
											case $aff_today_redirect_rate>=80:
												$bar_color='success';
											break;
											case $aff_today_redirect_rate>=70:
												$bar_color='warning';
											break;
											case $aff_today_redirect_rate<=69:
												$bar_color='danger';
											break;
									 }?>
									<div style="width: <?php echo $aff_today_redirect_rate; ?>%" aria-valuemax="100" aria-valuemin="0"
										aria-valuenow="<?php echo $aff_today_redirect_rate; ?>" role="progressbar" class="progress-bar progress-bar-<?php echo $bar_color; ?>"></div>
								</div>
								<div>
									<div class="md-title" style="float: left; margin-top:15px;">Yesterday</div>
									<div class="text-primary" style="float: right; margin-top:15px;">
										<strong><?php echo $aff_yesterday_redirect_rate; ?>%</strong>
									</div>
								</div>
								<div style="clear: both;" class="progress progress-striped active">
								<?php switch(true){
											case $aff_yesterday_redirect_rate>=80:
												$bar_color='success';
											break;
											case $aff_yesterday_redirect_rate>=70:
												$bar_color='warning';
											break;
											case $aff_yesterday_redirect_rate<=69:
												$bar_color='danger';
											break;
									 }?>
									<div style="width: <?php echo $aff_yesterday_redirect_rate; ?>%" aria-valuemax="100" aria-valuemin="0"
										aria-valuenow="<?php echo $aff_yesterday_redirect_rate; ?>" role="progressbar" class="progress-bar progress-bar-<?php echo $bar_color; ?>"></div>
								</div>
								<div>
									<div class="md-title" style="float: left;" id="redirect-title">7 Day Average</div>
									<div class="text-primary" style="float: right;">
										<strong><?php echo $aff_overall_redirect_rate; ?>%</strong>
									</div>
								</div>
								<div style="clear: both;" class="progress progress-striped active">
									<?php switch(true){
											case $aff_overall_redirect_rate>=80:
												$bar_color='success';
											break;
											case $aff_overall_redirect_rate>=70:
												$bar_color='warning';
											break;
											case $aff_overall_redirect_rate<=69:
												$bar_color='danger';
											break;
									 }?>
									<div style="width: <?php echo $aff_overall_redirect_rate; ?>%" aria-valuemax="100" aria-valuemin="0"
										aria-valuenow="<?php echo $aff_overall_redirect_rate; ?>" role="progressbar" class="progress-bar progress-bar-<?php echo $bar_color; ?>"></div>
								</div>
							</div>
							<div class="col-md-6">
								<script>
								//-----------------------------------Redirect Report---------------------------------------//
								jQuery(document).ready(function(){ 
									if(jQuery('#affiliate-redirect-title').length>0){
										var m4 = new Morris.Bar({
											// ID of the element in which to draw the chart.
											element: 'affiliate-stacked-chart-default',
										    data: [
										        { y: 'Today', a: <?php echo $aff_today_sold; ?>,  b: <?php echo $aff_today_redirected; ?> },
										        { y: 'Yesterday', a: <?php echo $aff_yesterday_sold; ?>,  b: <?php echo $aff_yesterday_redirected; ?> },
										        { y: '7d Avg', a: <?php echo $aff_overall_sold; ?>, b: <?php echo $aff_overall_redirected; ?> },
										    ],
										    xkey: 'y',
										    ykeys: ['a', 'b'],
										    labels: ['Sold', 'Redirected'],
										    barColors: ['#428bca', '#1caf9a'],
										    lineWidth: '1px',
										    fillOpacity: 0.8,
										    smooth: true,
										    stacked: true,
										    hideHover: true
										});
									}
								});
								</script>
								<div id="affiliate-stacked-chart-default" class="height175"></div>
							</div>
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
						<hr>
							<div class="form-group col-md-12">
								<select id="select-affiliate" name="affiliate-remote-id"
									data-placeholder="Choose One" class="width300">
									<option value="0">Filter by Affiliate</option>
									<?php 
									foreach($affiliate_list as $key=>$aff){										
										echo '<option value="'.$aff['Affiliate']['remote_id'].'">'.$aff['Affiliate']['affiliate_name'].' ('.$aff['Affiliate']['remote_id'].')</option.>';
									}
									?>
								</select>
							</div>
							<div class="form-group col-md-12">
								<select id="select-offer" name="offer-id" data-placeholder="Choose One" class="width300">
									<option value="">Filter by Offer</option>
									<?php 
									foreach($offer_list as $offer=>$key){										
										echo '<option value="'.$key.'">'.$offer.' ('.$key.')</option.>';
									}
									?>
								</select>
							</div>
							<div class="form-group col-md-12" id="campaign-container" style="display:none;">
								<select id="select-campaign" name="campaign-id" data-placeholder="Choose One" class="width300">
									<option value="">Filter by Campaign ID</option>
								</select>
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