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
					<li>Ancillary</li>
				</ul>
				<h4>Ancillary</h4>
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
						<h3 class="panel-title">Ancillary List</h3>
					</div>
					<div class="panel-body">
						<table id="ancillaryTable"
							class="table table-hover table-bordered responsive">
							<thead class="">
								<tr>
									<th>Ancillary ID</th>
									<th>Name</th>
									<th>Type</th>
									<th>URL</th>
									<th>Trigger</th>
									<th>Status</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
							<?php 
							foreach($ancillarylist as $key=>$ancillarydetail){
								if ($ancillarydetail['Ancillary']['status'] == 1){$status = 'Active';}else{$status = 'Inactive';}
								echo "<tr>
									<td>{$ancillarydetail['Ancillary']['id']}</td>
									<td class='editable' data-ancillaryid='{$ancillarydetail['Ancillary']['id']}' data-field='name' data-ancillary='{$ancillarydetail['Ancillary']['name']}'>{$ancillarydetail['Ancillary']['name']}</td>
									<td>{$ancillarydetail['Ancillary']['type']}</td>
									<td class='editable' data-ancillaryid='{$ancillarydetail['Ancillary']['id']}' data-field='url' data-ancillary='{$ancillarydetail['Ancillary']['url']}'>{$ancillarydetail['Ancillary']['url']}</td>
									<td>{$ancillarydetail['Ancillary']['triggeraction']}</td>
									<td class='editable' data-ancillaryid='{$ancillarydetail['Ancillary']['id']}' data-field='status' data-ancillary='{$ancillarydetail['Ancillary']['status']}'>{$status}</td>
									<td><button class=\"btn btn-danger btn-xs\" data-toggle=\"modal\" data-target=\"#ancillary-delete-modal\" data-ancillaryid='{$ancillarydetail['Ancillary']['id']}' id=\"delete-ancillary\"><span class=\"fa fa-trash-o\"></span></button></td>
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
						<h3 class="panel-title">Add New Ancillary</h3>
					</div>
					<div class="panel-body">
						<?php echo $this->Form->create('Site', array('id'=>'addancillary', 'action' => 'addancillary', 'inputDefaults'=>array('label'=>false,'div'=>false))); ?>
						<div class="form-group">
							<div class="col-sm-6">
								<?php echo $this->Form->input('Ancillary.name', array('id'=>'add-ancillary-name', 'class'=>'form-control input-sm', 'placeholder'=>'Ancillary Name')); ?>
							</div>
							<div class="col-sm-6">
								<?php echo $this->Form->input('Ancillary.url', array('id'=>'add-ancillary-url', 'class'=>'form-control input-sm', 'placeholder'=>'URL')); ?>
							</div>
						</div>
						<!-- form-row-1 -->
						<div class="form-group">
							<div class="col-sm-6">
								<select id="add-ancillary-type" name="data[Ancillary][type]" style="width:100%;">
									<option value="">Choose Type</option>
									<option value="page">Page Popup</option>
									<option value="field">Field Popup</option>
									<option value="click">Click Popup</option>
									<option value="backend">Backend</option>
								</select>
							</div>
							<div class="col-sm-6" id="trigger">
								<div id="selectpagelist" style="display:none;">
									<select id="add-ancillary-pagelist" name="targetpage" style="width:100%;">
										<option value="">Choose Page</option>
										<?php foreach($pagelist AS $value=>$name){
											echo "<option value='{$value}'>{$name}</option>";
										}?>
									</select>
								</div>
								<div id="selectfieldlist" style="display:none;">
									<select id="add-ancillary-fieldlist" name="targetfield" style="width:100%;">
										<option value="">Choose Field</option>
										<?php foreach($fieldlist AS $value=>$name){
											echo "<option value='{$value}'>{$name}</option>";
										}?>
									</select>
								</div>
								<div id="selectclickid" style="display:none;">
									<input type="text" id="add-ancillary-clickid" name="targetclick" class="form-control input-sm" placeholder="Click Target ID">
								</div>
								<div id="backendlist" style="display:none;">
									<select id="add-ancillary-backend" name="targetbackend" style="width:100%;">
										<option value="">Choose Type</option>
										<option value="gb">Guaranteed Buy</option>
									</select>
								</div>
							</div>
						</div>
						<!-- form-row-2 -->
						<div class="form-group">
							<div class="col-sm-12">
								<div id="fieldvaluelist" style="display:none;">
									<select id="add-ancillary-fieldvalue" multiple data-placeholder="Choose Value" name="fieldtargetvalue[]" style="width:100%;"></select>
								</div>
								<input type="text" id="add-ancillary-triggervalue" class="form-control input-sm" name="clicktargetvalue" placeholder="Trigger Value" style="display:none;">
							</div>
						</div>
						<!-- form-row-2 -->
						
						<!-- form-row-3 -->
						<div class="form-group" id="winpopsize" style="display:none;">
							<div class="col-sm-6">
								<div id="" >
									<label for="window_width">Window Width</label>
									<select id="window_width" name="window_width" style="width:100%;">
										<option value="100">100px</option>
										<option value="200">200px</option>
										<option value="300">300px</option>
										<option value="400">400px</option>
										<option value="500" SELECTED>500px</option>
										<option value="600">600px</option>
										<option value="700">700px</option>
										<option value="800">800px</option>
										<option value="900">900px</option>
										<option value="1000">1000px</option>
									</select>
								</div>
							</div>
							<div class="col-sm-6">
								<div id="" >
									<label for="window_heigth">Window Heigth</label>
									<select id="window_height" name="window_height" style="width:100%;">
										<option value="100">100px</option>
										<option value="200">200px</option>
										<option value="300">300px</option>
										<option value="400" SELECTED>400px</option>
										<option value="500">500px</option>
										<option value="600">600px</option>
										<option value="700">700px</option>
										<option value="800">800px</option>
										<option value="900">900px</option>
										<option value="1000">1000px</option>
									</select>
								</div>
							</div>
						</div>
						<!-- form-row-3 -->
						
					</div>
					<!-- panel-body -->
					<div class="panel-footer">
						<button class="btn btn-primary">Add Ancillary</button>
						<button type="reset" id="add-ancillary-reset" class="btn btn-dark">Reset</button>
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
	<div id="ancillary-delete-modal" class="modal fade in" data-backdrop="static" data-keyboard="false" aria-hidden="false" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" style="display: none;">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button class="close" aria-hidden="true" data-dismiss="modal" type="button">×</button>
					<button class="btn btn-warning btn-xs" data-toggle="modal" data-target="#ancillary-delete-modal-icon"><span class="glyphicon glyphicon-warning-sign"></span></button><h4 id="myModalLabel" class="modal-title">Ancillary Deletion</h4>
				</div>
				<div class="modal-body"> Attention! This process will delete the ancillary and all dependencies.  Any configurations that are attached to this ancillary will be deleted. </div>
				<div class="modal-footer">
					<button class="btn btn-default" data-dismiss="modal" type="button">Cancel</button>
					<button class="btn btn-primary" id = "ancillary-delete-modal-action" data-ancillaryid = "" type="button">Delete Ancillary</button>
				</div>
			</div>
		</div>
	</div>
	
	
</div>
<script type="text/javascript">
var fieldvalues = {
		CreditRating:{
			excellent:"Excellent",good:"Good",fair:"Fair",poor:"Poor",unsure:"Unsure"},
		Military:{
			"true":"Yes","false":"No"},
		ResidenceType:{
			rent:"Rent",ownwmtg:"Own with Mortgage",ownwomtg:"Own without Mortgage"},
		EmployeeType:{
			self_employed:"Self Employed",employed:"Employed",pension:"Pension",unemployed:"Unemployed"},
		BankAccountType:{
			checking:"Checking",savings:"Savings"},
		DirectDeposit:{
			"true":"Yes","false":"No"}};
</script>
<!-- mainpanel -->