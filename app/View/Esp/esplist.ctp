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
					<li>ESP List</li>
				</ul>
				<h4>ESP List</h4>
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
			<div class = "row" >
				<div class="col-sm-12">&nbsp;</div>
			</div>	
		</div>
		
		<div class = "row">
			
			<div class="col-md-6">
				
				<div class="panel panel-default">
					<div class="panel-heading">
						<div class="panel-btns">
							<a href="" class="tooltips" data-toggle="tooltip" data-original-title="Recalculate"><i class="fa fa-repeat"></i></a>
						</div>
						<h3 class="panel-title">ESP List</h3>
					</div>
					<div class="panel-body">
						<table id="esplisttable" class="table table-hover table-bordered responsive">
							<thead>
								<tr>
									<th>ESP ID</th>
									<th>ESP Name</th>
									<th>Active Status</th>
								</tr>
							</thead>
							<tbody>
								
								<?php 
									$i=0;
									foreach($esp_list as $index=>$esp){
										
										$status = (($esp[2] == "1") ? 'Active' : (($esp[2] == "2") ? 'Inactive': 'Pending'));
																				
										echo '<tr data-esp-id = "'.$esp[0].'">';
										echo '<td>'.$esp[0].'</td>';
										echo '<td>'.$esp[1].'</td>';
										echo '<td><span id = "esplistselect" style = "cursor:pointer;text-decoration:underline;color:blue">'.$status.'</span></td>';
										echo '</tr>';
										
									}?>	
							</tbody>
						</table>
					</div>
				</div>
				<!-- panel -->
			</div>
	
		</div>
		<!-- row -->
		
	</div>
	<!-- contentpanel -->
	
	
</div>
<!-- mainpanel -->

