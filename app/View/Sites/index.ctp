<div class="mainpanel">
	<div class="pageheader">
		<div class="media">
			<div class="pageicon pull-left">
				<i class="fa fa-cogs"></i>
			</div>
			<div class="media-body">
				<ul class="breadcrumb">
					<li><a href="/dashboard"><i class="glyphicon glyphicon-home"></i></a></li>
					<li><a href="/dashboard">Site Management</a></li>
					<li>Websites</li>
				</ul>
				<h4>Websites</h4>
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
						<div class="pull-right"></div>
						<h3 class="panel-title">Website List</h3>
					</div>
					<div class="panel-body">
						<table id="siteTable"
							class="table table-hover table-bordered responsive">
							<thead class="">
								<tr>
									<th>Site ID</th>
									<th>Name</th>
									<th>URL</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
							<?php 
							foreach($sitelist as $key=>$sitedetail){
								echo "<tr>
									<td>{$sitedetail['Site']['id']}</td>
									<td class='editable' data-siteid='{$sitedetail['Site']['id']}' data-field='name' data-site='{$sitedetail['Site']['name']}'>{$sitedetail['Site']['name']}</td>
									<td class='editable' data-siteid='{$sitedetail['Site']['id']}' data-field='url' data-site='{$sitedetail['Site']['url']}'>http(s)://{$sitedetail['Site']['url']}</td>
									<td><button class=\"btn btn-danger btn-xs\" data-toggle=\"modal\" data-target=\"#sites-delete-modal\" data-siteid='{$sitedetail['Site']['id']}' id=\"delete-site\"><span class=\"fa fa-trash-o\"></span></button></td>
								</tr>";
							}?>
							</tbody>
						</table>
					</div>
					<!-- panel-body -->
				</div>
			</div>
			<div class="col-md-4">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<!-- panel-btns -->
						<h3 class="panel-title">Add New Site</h3>
					</div>
					<div class="panel-body">
						<?php echo $this->Form->create('Site', array('id'=>'addsite', 'action' => 'addsite', 'inputDefaults'=>array('label'=>false,'div'=>false))); ?>
						<div class="form-group">
							<div class="col-sm-12">
								<?php echo $this->Form->input('Site.name', array('id'=>'add-site-name', 'class'=>'form-control input-sm', 'placeholder'=>'Website Name')); ?>
							</div>
						</div>
						<!-- form-row-1 -->
						<div class="form-group">
							<div class="col-sm-12">
								<?php echo $this->Form->input('Site.url', array('id'=>'add-site-url', 'class'=>'form-control input-sm', 'placeholder'=>'URL')); ?>
							</div>
						</div>
					</div>
					<!-- panel-body -->
					<div class="panel-footer">
						<button class="btn btn-primary">Add Website</button>
						<button type="reset" id="add-site-reset" class="btn btn-dark">Reset</button>
						<?php echo $this->Form->end(); ?>
					</div><!-- panel-footer -->
				</div>
				<!-- panel -->
			</div>
		</div>
		<!-- row -->
	</div>
	<!-- contentpanel -->
	
	<!-- Deletion Modal -->
	<div id="sites-delete-modal" class="modal fade in" data-backdrop="static" data-keyboard="false" aria-hidden="false" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" style="display: none;">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button class="close" aria-hidden="true" data-dismiss="modal" type="button">×</button>
					<button class="btn btn-warning btn-xs" data-toggle="modal" data-target="#sites-delete-modal-icon"><span class="glyphicon glyphicon-warning-sign"></span></button><h4 id="myModalLabel" class="modal-title">Site Deletion</h4>
				</div>
				<div class="modal-body"> Attention! This process will delete the site and all dependencies.  Any configurations that are attached to this site will be deleted. </div>
				<div class="modal-footer">
					<button class="btn btn-default" data-dismiss="modal" type="button">Cancel</button>
					<button class="btn btn-primary" id = "sites-delete-modal-action" data-siteid = "" type="button">Delete Site</button>
				</div>
			</div>
		</div>
	</div>
	
	
	
	
	
	
</div>
<!-- mainpanel -->