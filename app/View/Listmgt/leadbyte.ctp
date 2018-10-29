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
					<li>LeadByte</li>
				</ul>
				<h4>LeadByte</h4>
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
						<h3 class="panel-title">Leadbyte Lists</h3>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<table id="leadTable" class="table table-hover table-bordered responsive">
								<thead>
									<tr>
										<th>ID</th>
										<th>List Name</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody id="leadbyteTableBody">
								<?php
								foreach($leadbytelists as $list){
									echo '<tr>';
									echo '<td>'.$list['Listmgt']['id'].'</td>';
									echo '<td>'.$list['Listmgt']['name'].'</td>';
									echo '<td data-toggle="modal" data-target=".confirm-delete-list" class="leadbyteListDeleteOpen" data-list-id="'.$list['Listmgt']['id'].'" style="text-align:center; vertical-align:middle; cursor:pointer;"><button class="btn btn-danger btn-xs"><span class="fa fa-trash-o"></span></button></td>';
									echo '</tr>';
								}
								?>
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
						<h3 class="panel-title" id="overview-title">Add Leadbyte List</h3>
					</div>
					<div class="panel-body" >
					
					</div>
					<!-- panel-body -->
					<div class="panel-footer">
						<button class="btn btn-primary" id="search-leads">Add</button>
					</div><!-- panel-footer -->
				</div>
			</div>
		</div>
		<!-- row -->
	</div>
	<!-- contentpanel -->
</div>
<!-- mainpanel -->

<div class="modal fade confirm-delete-list" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="panelt panel-danger">
				<div class="panel-heading">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
					<h3 class="panel-title">Delete Leadbyte List Confirmation</h3>
				</div>
				<div class="panel-body">
					<p>This will delete the Leadbyte list from KeyStone.  This cannot be undone, would you like to continue?</p>
				</div>
				<div class="panel-footer">
						<button id="vendorIpDelete" class="btn btn-danger">Yes</button>
						<button  data-dismiss="modal" aria-hidden="true" class="btn btn-dark">No</button>
				</div><!-- panel-footer -->
			</div>
		</div>
	</div>
</div>