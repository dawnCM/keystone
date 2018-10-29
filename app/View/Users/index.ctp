<div class="mainpanel">
	<div class="pageheader">
		<div class="media">
			<div class="pageicon pull-left">
				<i class="fa fa-users"></i>
			</div>
			<div class="media-body">
				<ul class="breadcrumb">
					<li><a href="/dashboard"><i class="glyphicon glyphicon-home"></i></a></li>
					<li>User Management</li>
				</ul>
				<h4>User Management</h4>
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
						<h3 class="panel-title">Users</h3>
					</div>
					<div class="panel-body">
						<table id="userTable"
							class="table table-hover table-bordered responsive">
							<thead class="">
								<tr>
									<th>Name</th>
									<th>E-mail</th>
									<th>Title</th>
									<th>Password</th>
									<th>Access</th>
									<th>Account Status</th>
									<th>Photo</th>
								</tr>
							</thead>
							<tbody>
							<?php 
							foreach($userlist as $key=>$userdetails){
								$userstatus = ($userdetails['User']['status'] == "1") ? 'Active' : 'Inactive';
								$userphoto = ($userdetails['User']['photo'] == "1") ? 'Uploaded' : 'None';
								
								echo "<tr>
									<td class='editable' data-userid='{$userdetails['User']['id']}' data-field='name' data-user='{$userdetails['User']['full_name']}'>{$userdetails['User']['full_name']}</td>
									<td class='editable' data-userid='{$userdetails['User']['id']}' data-field='email' data-user='{$userdetails['User']['email']}'>{$userdetails['User']['email']}</td>
									<td class='editable' data-userid='{$userdetails['User']['id']}' data-field='title' data-user='{$userdetails['User']['title']}'>{$userdetails['User']['title']}</td>
									<td class='editable' data-userid='{$userdetails['User']['id']}' data-field='password' data-user=''>Hidden</td>
									<td class='editable' data-userid='{$userdetails['User']['id']}' data-field='group' data-user='{$userdetails['Group']['id']}'>{$userdetails['Group']['name']}</td>
									<td class='editable' data-userid='{$userdetails['User']['id']}' data-field='status' data-user='{$userdetails['User']['status']}'>{$userstatus}</td>
									<td class='editable userphoto' data-userid='{$userdetails['User']['id']}'>{$userphoto}<div class='progress' style='display:none;'><div style='width:10%;' aria-valuemax='100' aria-valuemin='0' aria-valuenow='10' role='progressbar' class='progress-bar progress-bar-info'></div></div></td>
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
						<div class="panel-btns" style="display: none;">
							<a href="" class="panel-minimize tooltips" data-placement="left" data-toggle="tooltip" title="" data-original-title="Minimize Panel"><i class="fa fa-minus"></i> </a>
						</div>
						<!-- panel-btns -->
						<h3 class="panel-title">Add New User</h3>
					</div>
					<div class="panel-body">
						<?php echo $this->Form->create('User', array('id'=>'adduser', 'action' => 'add', 'inputDefaults'=>array('label'=>false,'div'=>false))); ?>
						<div class="form-group">
							<div class="col-sm-4">
								<?php echo $this->Form->input('User.first_name', array('id'=>'add-user-first-name', 'class'=>'form-control input-sm', 'placeholder'=>'First Name')); ?>
							</div>
							<div class="col-sm-4">
								<?php echo $this->Form->input('User.last_name', array('id'=>'add-user-last-name', 'class'=>'form-control input-sm', 'placeholder'=>'Last Name')); ?>
							</div>
							<div class="col-sm-4">
								<?php echo $this->Form->input('User.email', array('id'=>'add-user-email', 'class'=>'form-control input-sm', 'placeholder'=>'Email')); ?>
							</div>
						</div>
						<!-- form-row-1 -->
						<div class="form-group">
							<div class="col-sm-4">
								<?php echo $this->Form->input('User.title', array('id'=>'add-user-title', 'class'=>'form-control input-sm', 'placeholder'=>'Title')); ?>
							</div>
							<div class="col-sm-4">
								<?php echo $this->Form->input('User.password', array('id'=>'add-user-password', 'class'=>'form-control input-sm', 'placeholder'=>'Password', 'minlength'=>5)); ?>
							</div>
							<div class="col-sm-4">
								<select id="add-user-group-id" name="data[User][group_id]" class="ignore">
									<?php foreach($accessgroups as $key=>$group){
										echo '<option value="'.$key.'">'.$group.'</option.>';
									}?>
								</select>
							</div>
						</div>
						<!-- form-row-2 -->
					</div>
					<!-- panel-body -->
					<div class="panel-footer">
						<button class="btn btn-primary">Add User</button>
						<button type="reset" id="add-user-reset" class="btn btn-dark">Reset</button>
						<?php echo $this->Form->end(); ?>
					</div><!-- panel-footer -->
				</div>
				<!-- panel -->
			</div>
		</div>
		<!-- row -->
	</div>
	<!-- contentpanel -->
</div>
<!-- mainpanel -->