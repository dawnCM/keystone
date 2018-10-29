<div class="mainpanel">
	<div class="pageheader">
		<div class="media">
			<div class="pageicon pull-left">
				<i class="fa fa-files-o"></i>
			</div>
			<div class="media-body">
				<ul class="breadcrumb">
					<li><a href="/dashboard"><i class="glyphicon glyphicon-home"></i></a></li>
					<li>CPF Management</li>
				</ul>
				<h4>CPF Management</h4>
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
						<h3 class="panel-title">Processed Files</h3>
					</div>
					<div class="panel-body">
						<table id="cpfTable" class="table table-striped table-bordered responsive">
							<thead class="">
								<tr>
									<th>ID</th>
									<th>Name</th>
									<th>Records</th>
									<th>Processed</th>
									<th>Errors</th>
									<th>Created Date</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								foreach($cpflist as $model=>$value){
									$date = date("F d, Y h:ia", strtotime($value['Cpf']['created']));
									echo "<tr>
										<td>{$value['Cpf']['id']}</td>
										<td><a href='files/cpf/".md5($value['Cpf']['name']).".csv' data-toggle='modal' data-target='.bs-example-modal-panel'>{$value['Cpf']['name']}</a></td>
										<td>{$value['Cpf']['records']}</td>
										<td>{$value['Cpf']['processed']}</td>
										<td>{$value['Cpf']['errors']}</td>
										<td>{$date}</td>
									</tr>";
								}
								?>
							</tbody>
						</table>
					</div>
					<!-- panel-body -->
				</div>
			</div>
			<div class="col-md-4">
				<div class="panel panel-primary">
                                    <div class="panel-heading padding5">
                                        <h3 class="panel-title">Process File</h3>
                                    </div>
                                    <div class="panel-body">
                                    	<div class="dropzone dz-clickable" id="cpfFile">
                                    		<div class="progress" style="display:none;"><div style="width:5%;" aria-valuemax="100" aria-valuemin="0" aria-valuenow="5" role="progressbar" class="progress-bar progress-bar-info"></div></div>
                                    		<div class="dz-spin" style="display:none;"></div>
                                    	</div>
                                    </div><!-- panel-body -->
                                    <div class="panel-footer">
                                    	                                        
                                    </div>
                                    <!-- panel-footer -->
                                </div><!-- panel -->
				<!-- panel -->
			</div>
		</div>
		<!-- row -->
	</div>
	<!-- contentpanel -->
</div>
<!-- mainpanel -->
<div class="modal fade bs-example-modal-panel" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
	<div class="modal-dialog">
		<div class="panelt panel-default">
   			<div class="panel-heading">
       			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
       			<h3 class="panel-title">File Content</h3>
   			</div>
   			<div class="panel-body modal-content">
       			
   			</div>
		</div>
	</div>
</div>