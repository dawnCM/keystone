<div class="mainpanel">
	<div class="pageheader">
		<div class="media">
			<div class="pageicon pull-left">
				<i class="fa fa-clipboard"></i>
			</div>
			<div class="media-body">
				<ul class="breadcrumb">
					<li><a href="/dashboard"><i class="glyphicon glyphicon-home"></i></a></li>
					<li><a href="/dashboard">Lead Management</a></li>
					<li>Leads</li>
				</ul>
				<h4>Lead Search</h4>
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
						<h3 class="panel-title">Leads</h3>
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
							<div class="col-sm-6">
								<input id="firstname" type="text" placeholder="First Name" class="form-control input-sm">
							</div>
							<div class="col-sm-6">
								<input id="lastname" type="text" placeholder="Last Name" class="form-control input-sm">
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-6">
								<input id="email" type="email" placeholder="Email" class="form-control input-sm">
							</div>
							<div class="col-sm-6">
								<input id="phone" type="text" placeholder="Phone" class="form-control input-sm" style="display:none;">
								<input id="ip" type="text" placeholder="IP Address" class="form-control input-sm">
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-6">
								<input id="city" type="text" placeholder="City" class="form-control input-sm" style="display:none;">
								<input id="zip" type="text" placeholder="Zip" class="form-control input-sm">
							</div>
							<div class="col-sm-6">
								<select id="state" style="width:100%;">
									<option value="">Select State</option>								
									<option value="al">Alabama</option>
									<option value="ak">Alaska</option>
									<option value="az">Arizona</option>
									<option value="ar">Arkansas</option>
									<option value="ca">California</option>
									<option value="co">Colorado</option>
									<option value="ct">Connecticut</option>
									<option value="de">Delaware</option>
									<option value="dc">District Of Columbia</option>
									<option value="fl">Florida</option>
									<option value="ga">Georgia</option>
									<option value="hi">Hawaii</option>
									<option value="id">Idaho</option>
									<option value="il">Illinois</option>
									<option value="in">Indiana</option>
									<option value="ia">Iowa</option>
									<option value="ks">Kansas</option>
									<option value="ky">Kentucky</option>
									<option value="la">Louisiana</option>
									<option value="me">Maine</option>
									<option value="md">Maryland</option>
									<option value="ma">Massachusetts</option>
									<option value="mi">Michigan</option>
									<option value="mn">Minnesota</option>
									<option value="ms">Mississippi</option>
									<option value="mo">Missouri</option>
									<option value="mt">Montana</option>
									<option value="ne">Nebraska</option>
									<option value="nv">Nevada</option>
									<option value="nh">New Hampshire</option>
									<option value="nj">New Jersey</option>
									<option value="nm">New Mexico</option>
									<option value="ny">New York</option>
									<option value="nc">North Carolina</option>
									<option value="nd">North Dakota</option>
									<option value="oh">Ohio</option>
									<option value="pl">Oklahoma</option>
									<option value="or">Oregon</option>
									<option value="pa">Pennsylvania</option>
									<option value="ri">Rhode Island</option>
									<option value="sc">South Carolina</option>
									<option value="sd">South Dakota</option>
									<option value="tn">Tennessee</option>
									<option value="tx">Texas</option>
									<option value="ut">Utah</option>
									<option value="vt">Vermont</option>
									<option value="va">Virginia</option>
									<option value="wa">Washington</option>
									<option value="wv">West Virginia</option>
									<option value="wi">Wisconsin</option>
									<option value="wy">Wyoming</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-6">
								<div class="ckbox ckbox-primary">
									<input type="checkbox" value="1" id="mobile">
									<label for="mobile">Mobile Lead</label>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="ckbox ckbox-primary">
									<input type="checkbox" value="1" id="military">
									<label for="military">Military Lead</label>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-6">
								<div class="ckbox ckbox-primary">
									<input type="checkbox" value="1" id="redirect">
									<label for="redirect">Redirect Missing</label>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="ckbox ckbox-primary">
									<input type="checkbox" value="1" id="sold">
									<label for="sold">Sold Lead</label>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-6">
								<div class="ckbox ckbox-primary">
									<input type="checkbox" value="1" id="altered">
									<label for="altered">Altered Lead</label>
								</div>
							</div>
							<div class="col-sm-6">
							
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