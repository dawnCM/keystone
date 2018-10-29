<div class="mainpanel">
	<div class="pageheader">
		<div class="media">
			<div class="pageicon pull-left" style="padding-top: 3px; padding-left: 15px;">
				<i class="fa fa-tree"></i>
			</div>
			<div class="media-body">
				<ul class="breadcrumb">
					<li><a href="/dashboard"><i class="glyphicon glyphicon-home"></i></a></li>
					<li><a href="/dashboard">Tools</a></li>
					<li>Clone Ping Tree</li>
				</ul>
				<h4>Clone PingTree</h4>
			</div>
		</div>
		<!-- media -->
	</div>
	<!-- pageheader -->

	<div class="contentpanel">
		<?php echo $this->Session->flash(); ?>
		<div class="row">
		
			<div class="col-md-10">
				<div class="panel panel-primary" id="clonepingtree">
					<div class="panel-heading">
						
						<h3 class="panel-title">Copy Vendor PingTree</h3>
					</div>
					<div id="vendorLoader" style="display:none"><img src="/images/loaders/sand_small.svg"></div>
					<div id="vendor-search-panel">
						<div class="panel-body">
	
							<div class="form-group">
								<div class="col-md-2">
									<label for="buyer_name">New Buyer Name</label>
									  <input type="text" class="form-control" id="buyer_name">
									  <small>Example: (v)Leads Power</small>
								</div>
								<div class="col-md-2">
									<label for="contract_name">New Contract Name</label>
									  <input type="text" class="form-control" id="contract_name">
									<small>Example: Leads Power</small>
								</div>
								<div class="col-sm-1">
									<label for="buyer_name">New Affiliate Id</label>
									  <input type="text" class="form-control" id="affiliate_id">
								</div>
								<div class="col-sm-2">
									<label for="buyer_name">Search & Replace Text</label>
									  <input type="text" class="form-control" id="replace_text" value = "Partner Weekly">
									  <small>The existing contract name</small>
								</div>
								<div class="col-sm-4">
									<label for="buyer_name">Copy From Pingtree</label><br>
									  <select id="select-copypingtree" data-placeholder="Choose One" class="width300">
										<?php 
										foreach($buyer_list as $key=>$buyer){										
											echo '<option value="'.$key.'">'.$buyer.'</option.>';
										}?>
									</select>
									 
								</div>
							</div>
				
						<!-- panel-body -->
						<div class="panel-footer">
							<button class="btn btn-primary" id="copy_pingtree">Copy PingTree</button> 
						</div>
						<!-- panel-footer -->
					</div>
				</div>
			</div>
		
			<!-- panel -->
			</div>
			
			
			<br>
			<div class="col-md-6">
			<div  id = "pingtree_results" style = "display:none">
				<div class="panel-heading" >
					<h3 class="panel-title">PingTree Results</h3>
				</div>
				
					<div class="panel-body">
						<div class="row">
							<div class="col-sm-12">
								<div id = "pingtree_details"></div>
								<button type="button" class="btn btn-default" id = "clear_details" aria-label="Left Align">
								<span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Start Over
								</button>
							</div>
						</div>
							
					</div>
					<!-- panel-body -->
					
					<!-- panel-footer -->
			</div>
			</div>
			
			
			
			
			
		</div>
		<!-- row -->
	</div>
	<!-- contentpanel -->
</div>
<!-- mainpanel -->