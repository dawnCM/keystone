<div class="mainpanel">
	<div class="pageheader">
		<div class="media">
			<div class="pageicon pull-left">
				<i class="fa fa-envelope"></i>
			</div>
			<div class="media-body">
				<ul class="breadcrumb">
					<li><a href="/dashboard"><i class="glyphicon glyphicon-home"></i></a></li>
					<li><a href="/dashboard">List Management</a></li>
					<li>ESP Offer List</li>
				</ul>
				<h4>ESP Offer List</h4>
			</div>
		</div>
		<!-- media -->
	</div>
	<!-- pageheader -->
	
	<div class="contentpanel" id="content">
		<?php echo $this->Session->flash(); ?>
		
		<div style="display:none;" id = "savedsuccess" >
			<div class = "row">
				<div class="col-lg-12">
					<div style = "font-size: 16px" class="alert alert-success" role="alert">The ESP was successfully saved!</div>
				</div>
			</div>
		</div>
		
		<div class = "row">
			<div class="col-md-8">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Offers</h3>
					</div>
					<div class="panel-body">
						<table id="espoffertable" class="table table-hover table-bordered responsive">
							<thead>
								<tr>
									<th>Offer ID</th>
									<th>Offer Name</th>
								</tr>
							</thead>
							<tbody>
							<?php 
							foreach($offer_list as $index=>$offer){									
								echo '<tr>';
								echo '<td>'.$offer[0].'</td>';
								echo '<td>'.$offer[1].'</td>';
								echo '</tr>';
							}?>	
							</tbody>
						</table>
					</div>
				</div>
				<!-- panel -->
			</div>
			
			<div class="col-md-4">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<!-- panel-btns -->
						<h3 class="panel-title">Add an ESP Offer</h3>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="form-group col-md-12">
								<label for="offername">Offer Name</label>
   		 						<input type="text" class="form-control input-sm" id="offername" placeholder="Offer Name">
							</div>
						</div>
						<!-- form-row-1 -->
					</div>
					<!-- panel-body -->
					<div id="footerdisplay" class="panel-footer">
						<button class="btn btn-primary" id="add-offer">Add Offer</button>
					</div><!-- panel-footer -->
				</div>
			</div>
		</div>
		<!-- row -->
	</div>
	<!-- contentpanel -->
</div>
<!-- mainpanel -->