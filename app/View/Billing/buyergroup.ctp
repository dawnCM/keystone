<div class="mainpanel">
	<div class="pageheader">
		<div class="media">
			<div class="pageicon pull-left">
				<i style="margin-left:5px;" class="fa fa-dollar"></i>
			</div>
			<div class="media-body">
				<ul class="breadcrumb">
					<li><a href="/dashboard"><i class="glyphicon glyphicon-home"></i></a></li>
					<li><a href="/billing/buyergroup">Billing</a></li>
					<li>Buyer Contract Groups</li>
				</ul>
				<h4>Buyer Contract Groups</h4>
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
						<h3 class="panel-title">Buyer Contract Groups</h3>
					</div>
					<div class="panel-body">
						<div id="tableLoader" style="display:none;"><img src="/images/loaders/sand_small.svg"></div>
						<div class="table-responsive">
						<table id="contractgroupTable" class="table table-hover table-bordered responsive">
							<thead>
								<tr>
									<th>ID</th>
									<th>Group Name</th>
									<th>Contracts</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody id="contractgroupTableBody">
							<?php 
							foreach($billing_groups as $key=>$group){
								echo"<tr>";
								echo "<td>{$group['BillingGroup']['id']}</td>";
								echo "<td>{$group['BillingGroup']['group_name']}</td>";
								echo "<td><select class='contractshow' style='width:95%;'>";
								echo "<option>Contract List</option>";
								if(is_array($group['BillingGroupContracts'])){
								foreach($group['BillingGroupContracts'] as $key=>$contract){
									foreach($contract_list as $key2=>$contract_detail){
										if($contract['contract_id'] == $contract_detail['Contract']['id']){
											echo "<option>{$contract_detail['Contract']['contract_name']}</option>";
										}
									}
								}
								}
								echo "</select></td>";
								echo "<td><button class='btn btn-success btn-xs'  title='Edit' data-billinggroupid='{$group['BillingGroup']['id']}' id='edit-billinggroupconfig'><span class='glyphicon glyphicon-edit'></span></button>
									&nbsp;&nbsp;
									<button class='btn btn-danger btn-xs' title='Delete' data-toggle='modal' data-target='#billinggroup-delete-modal' data-groupid='{$group['BillingGroup']['id']}' id='delete-group'><span class='fa fa-trash-o'></span></button>
									</td>";
								echo "</tr>";
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
				
						</div>
						<!-- panel-btns -->
						<h3 class="panel-title" id="overview-title">Add Contract Grouping</h3>
					</div>
					<div class="panel-body" >
						<form action="/billing/addgroup" id="addgroup" method="post" accept-charset="utf-8"><div style="display:none;"><input type="hidden" name="_method" value="POST"/></div>
						<div class="form-group">
							<div class="col-sm-12">
								<input type="text" placeholder="Buyer Group Name" class="form-control input-sm" id="groupname" name="group_name">
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-12">
								<select id="select-buyer" name="contract_list[]" multiple data-placeholder="Choose One" style="width:100%;">
								<option value="">Select Buyer</option>
								<?php foreach($buyer_list as $key=>$buyer){
									if($key !== ''){										
										echo '<option value="'.$key.'">'.$key.'</option.>';
									}
								}?>
								</select>
							</div>
						</div>
					</div>
					<!-- panel-body -->
					<div class="panel-footer">
						<button class="btn btn-primary" id="search-leads">Add Buyer Group</button>
						<?php echo $this->Form->end(); ?>
					</div><!-- panel-footer -->
				</div>
			</div>
		</div>
		<!-- row -->
	</div>
	<!-- contentpanel -->
</div>
<!-- mainpanel -->

<!-- Deletion Modal -->
	<div id="billinggroup-delete-modal" class="modal fade in" data-backdrop="static" data-keyboard="false" aria-hidden="false" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" style="display: none;">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button class="close" aria-hidden="true" data-dismiss="modal" type="button">X</button>
					<button class="btn btn-warning btn-xs" data-toggle="modal" data-target="#billinggroup-delete-modal-icon"><span class="glyphicon glyphicon-warning-sign"></span></button><h4 id="myModalLabel" class="modal-title">Billing Group Deletion</h4>
				</div>
				<div class="modal-body">Attention! This process will delete the Billing Group. </div>
				<div class="modal-footer">
					<button class="btn btn-default" data-dismiss="modal" type="button">Cancel</button>
					<button class="btn btn-primary" id = "billinggroup-delete-modal-action" data-groupid = "" type="button">Delete Billing Group</button>
				</div>
			</div>
		</div>
	</div>