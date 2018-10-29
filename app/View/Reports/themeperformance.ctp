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
					<li>Theme Performance</li>
				</ul>
				<h4>Theme A/B Results</h4>
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
						<h3 class="panel-title">Theme Data <span id = "themeperformance-site-display"></span></h3>
					</div>
					<div class="panel-body">
						<div id="themeperformance-table-loader"  style="display:none; padding-left: 10px">
							<div class="row">
								<div class="col-md-12">&nbsp;</div>	
							</div>
							
							<div class="row">
								<div class="col-md-2"><img src="/images/loaders/sand_small.svg"></div>
								<div class="col-md-8">&nbsp;</div>
							</div>
						</div>
						<table id="themeperformance-table" class="table table-hover table-bordered responsive">
							<thead>
								<tr>
									<th>Theme Name</th>
									<th>Clicks</th>
									<th>Leads</th>
									<th>Sold</th>
									<th>Revenue</th>
									<th>EPL</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
				<!-- panel -->
				<br>
				
				<!-- Chart Panel -->
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Theme Chart <span id = "themeperformance-chart-display"></span></h3>
					</div>
					<div class="panel-body">
						<div id="themeperformance-chart" width="auto" height="auto"></div>	
					</div>
				</div>
				<!-- End Chart Panel-->
				
				
				
				
			</div>
			<!-- End columne -->

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
								<select id="themeperformance-select" name="themeperformance-select"
									data-placeholder="Choose One" class="width300">
									<option value="">Filter by Site</option>
									<?php 
									foreach($site_list as $key=>$val){										
										echo '<option value="'.$key.'">'.$val.'</option.>';
									}
									?>
								</select>
							</div>
						</div>
						<!-- form-row-1 -->
					</div>
					<!-- panel-body -->
					<div class="panel-footer">
						<button class="btn btn-primary" id="themeperformance-calc">Calculate Theme Performance</button>
					</div>
					<!-- panel-footer -->
				</div>
				<!-- panel -->
			</div>
	</div> <!-- row -->
	
	
	<div class="row">
		<div class="col-md-8">
			
		</div>
	</div>
	
	</div> <!-- content panel>
</div> <!--main panel-->