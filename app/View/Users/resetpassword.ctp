<section>
	<div class="panel panel-signin">
		<div class="panel-body">
			<div class="logo text-center">
				<img src="/images/logo-primary.png" alt="keyStone Logo" width="102"
					height="22">
			</div>
			<br />
			<h4 class="text-center mb5">Reset your password</h4>

			<div class="mb30">This will generate a confirmation email that will require you to take action before your password can be reset.</div>

			<form action="/users/processReset" id="UserResetForm" method="post"
				accept-charset="utf-8" novalidate="novalidate">
				<div style="display: none;">
					<input type="hidden" name="_method" value="POST">
				</div>
				<div class="input-group mb15">
					<span class="input-group-addon"><i
						class="glyphicon glyphicon-envelope"></i></span> 
						<input type="text" class="form-control" placeholder="Email" name="email" id="UserEmail" required>
				</div>
				<!-- input-group -->
				<div class="clearfix">
					<div class="pull-left">
						<div class="ckbox ckbox-primary mt10">
							
						</div>
					</div>
					<div class="pull-right">
						<button type="submit" class="btn btn-warning">
							Reset Password<i class="fa fa-angle-right ml5"></i>
						</button>
					</div>
				</div>
			</form>

		</div>
		<!-- panel-body -->
		<div class="panel-footer"></div>
		<!-- panel-footer -->
	</div>
	<!-- panel -->

</section>