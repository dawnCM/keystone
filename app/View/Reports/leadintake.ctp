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
					<li>Lead Intake</li>
				</ul>
				<h4>Lead Intake</h4>
			</div>
		</div>
		<!-- media -->
	</div>
	<!-- pageheader -->

	<div class="contentpanel">
		<?php echo $this->Session->flash(); ?>
		<div class="row">
			<div class="col-md-8">
				<div class="panel panel-default" id="custom-intake-container" style="display:none;">
					<div class="panel-heading">
						<h3 class="panel-title" id="custom-intake-title"></h3>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-12">
								<div id="custom-intake" style="height:189px;"></div>
							</div>
						</div>
					</div>
					<!-- panel-body -->
				</div>
				<!-- End Custom -->
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title" id="vendor-intake-title">Vendor Intake</h3>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-12">
								<div id="vendor-intake" style="height:189px;"></div>
							</div>
						</div>
					</div>
					<!-- panel-body -->
				</div>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title" id="affiliate-intake-title">Affiliate Intake</h3>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-12">
								<div id="affiliate-intake" style="height:189px;"></div>
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
						</div>
						<!-- form-row-1 -->
					</div>
					<!-- panel-body -->
					<div class="panel-footer">
						<button class="btn btn-primary" id="calc-intake">Calculate Intake</button>
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