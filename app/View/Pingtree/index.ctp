<div class="mainpanel">
	<div class="pageheader">
		<div class="media">
			<div class="pageicon pull-left">
				<i class="fa fa-edit"></i>
			</div>
			<div class="media-body">
				<ul class="breadcrumb">
					<li><a href="/dashboard"><i class="glyphicon glyphicon-home"></i></a></li>
					<li><a href="/dashboard">Pingtree Management</a></li>
					<li>Pingtree Order</li>
				</ul>
				<h4>Pingtree</h4>
			</div>
		</div>
		<!-- media -->
	</div>
	<!-- pageheader -->
	
	
	<div class="contentpanel">
		<?php echo $this->Session->flash(); ?>
		<div style="display:none;" id = "nochangemessage" >
			<div class = "row">
				<div class="col-lg-12">
					<div style = "font-size: 16px" class="alert alert-danger" role="alert">There are no changes to Pingtree!</div>
				</div>
			</div>
			<div class = "row" >
				<div class="col-sm-12">&nbsp;</div>
			</div>	
		</div>
		<div class="row">
			<div class="col-md-8">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Pingtree Order <span id="pingtreenameheader"></span></h3>
					</div>
					<div class="panel-body">
						<div id="ContentLoader"  style="display:none; padding-left: 10px">
							<div class="row">
								<div class="col-md-12">&nbsp;</div>	
							</div>
							
							<div class="row">
								<div class="col-md-2"><img src="/images/loaders/sand_small.svg"></div>
								<div class="col-md-8">&nbsp;</div>
							</div>
						</div>
						<div id = "pingtreetabledisplay">
							<div class="table-responsive">
							<div id="tableLoader" style="display:none;"><img src="/images/loaders/sand_small.svg"></div>
	                        	<table class="table table-bordered table-hover mb30" id="pingtreetable" style="cursor:pointer;">
	                            	<thead>
									<tr>
										<th>Active</th>
										<th>Rank</th>
										<th>Contract Name</th>
										<th>Sold</th>
										<th>Sent</th>
										<th>Revenue</th>
										<th>EPL</th>
									</tr>
									</thead>
	                                <tbody></tbody>
	                              </table>
	                        </div>
	                	</div>
					</div>
					<!-- panel-body -->
				</div>
			</div>
			<div class="col-md-4">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<!-- panel-btns -->
						<h3 class="panel-title">Select a Pingtree to Modify</h3>
					</div>
					<div class="panel-body">
						<!--<div class="row">
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
							
						</div>-->
							
		
						
						<div class="row">
							<div class="form-group col-md-12">
								<select id="select-pingtree" data-placeholder="Choose One" class="width300">
									<?php 
									foreach($pingtree_list as $key=>$pingtree){										
										echo '<option value="'.$key.'">'.$pingtree.'</option.>';
									}?>
								</select>
							</div>
						</div>
						<!-- form-row-1 -->
					</div>
					<!-- panel-body -->
					<div id="footerdisplay" class="panel-footer">
						<button class="btn btn-primary" id="generate-pingtree">Load Pingtree</button>
						<button class="btn btn-success" id="save-pingtree" style="display:none;">Save Changes</button>
					</div><!-- panel-footer -->
				</div>
				<!-- panel -->
			</div>
		</div>
	</div>	
	<!-- contentpanel -->
</div>
<!-- mainpanel -->