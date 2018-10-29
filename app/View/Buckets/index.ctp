<div class="mainpanel">
	<div class="pageheader">
		<div class="media">
			<div class="pageicon pull-left">
				<i class="fa fa-bitbucket"></i>
			</div>
			<div class="media-body">
				<ul class="breadcrumb">
					<li><a href="/dashboard"><i class="glyphicon glyphicon-home"></i></a></li>
					<li>Bucket Management</li>
				</ul>
				<h4>Bucket Management</h4>
			</div>
		</div>
		<!-- media -->
	</div>
	<!-- pageheader -->

	<div class="contentpanel">
		<?php echo $this->Session->flash(); ?>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<div class="panel-btns">
							<a href="" class="tooltips" data-toggle="tooltip" data-original-title="Recalculate"><i class="fa fa-repeat"></i></a>
						</div>
						<h3 class="panel-title">Buckets</h3>
					</div>
					<div class="panel-body">
						<table id="bucketTable" class="table table-hover table-bordered responsive">
							<thead>
								<tr>
									<th class="sorting_disabled"></th>
									<th>ID</th>
									<th>Name</th>
									<th>Amount</th>
									<th>Prefill</th>
									<th>Wallet</th>
									<th>PFP</th>
									<th>Subs</th>
									<th>Margin</th>
									<th>Payout</th>
									<th class="never">Search_Affiliate_Name</th>
								</tr>
							</thead>
							<tbody id="bucketTableBody">
							<?php
							//echo "<pre>";
							//print_r($aggregatelist);
							//exit;
							?>
							<?php foreach($aggregatelist as $key=>$affiliate){ 
								// Affiliate
								if($affiliate['wallet'] == null){$affiliate['wallet'] = '0.00';}
								echo '
								<tr data-affiliate-id="'.$key.'" class="bucket-affiliate_'.$key.'" role="row">
									<td style="cursor:pointer; text-align:center; vertical-align:middle;" class="open-bucket sorting_1">
										<span style="margin-left:0px;" class="fa fa-arrow-right"></span>
									</td>
									<td>'.$key.'</td>
									<td>'.$affiliate['name'].'</td>
									<td>$ '.number_format($affiliate['amount'], 2, '.', ',').'</td>
									<td>$ '.number_format($affiliate['prefill'], 2, '.', ',').'</td>
									<td data-value="'.$affiliate['wallet'].'" data-field="wallet" class="editable">$ '.number_format($affiliate['wallet'],2,'.',',').'</td>
									<td>-</td>
									<td>-</td>
									<td>-</td>
									<td>-</td>
									<td></td>
								</tr>';
								//add base buckets
								
								foreach($affiliate['buckets'] as $bucket){
								// Base bucket
									if(is_array($bucket['Main'])){
										$hover = '';
										
										if($bucket['Main']['tipped'] != '') { 
											$tipped = date("m-d-Y h:i a", strtotime($bucket['Main']['tipped']));
											$hover = 'rel="hoverovers"';
										}
										
										$subs = ($bucket['Main']['has_subs'] == '1') ? 'True' : 'False';
										echo '
										<tr data-affiliate-id="'.$key.'" data-bucket-id="'.$bucket['Main']['id'].'" class="affiliate-'.$key.' bucket bucket-owner-'.$key.'" style="display:none;" role="row">
											<td style="color:darkgray; text-align:center; vertical-align:middle; cursor:pointer" class="open-subbucket sorting_1">
												<span style="margin-left:24px;" class="fa fa-arrow-right"></span>
											</td>
											<td '.$hover.' data-container="body" data-toggle="popover" data-html="true" data-placement="top" data-content="Bucket Tipped: '.$tipped.'">'.$bucket['Main']['bli'].'</td>
											<td>'.$bucket['Main']['offer_name'].'</td>
											<td data-value="'.$bucket['Main']['amount'].'" data-field="amount" class="editable">$ '.number_format($bucket['Main']['amount'],2,'.',',').'</td>
											<td data-value="'.$bucket['Main']['prefill'].'" data-field="prefill" class="editable">$ '.number_format($bucket['Main']['prefill'], 2, '.', ',').'</td>
											<td>-</td>
											<td data-value="'.$bucket['Main']['prefill_payback'].'" data-field="prefill_payback" class="editable">'.$bucket['Main']['prefill_payback'].' %</td>
											<td data-value="'.$bucket['Main']['has_subs'].'" data-field="has_subs" class="editable">'.$subs.'</td>
											<td data-value="'.$bucket['Main']['override_margin'].'" data-field="override_margin" class="editable">'.$bucket['Main']['override_margin'].' %</td>
											<td data-value="'.$bucket['Main']['override_payout'].'" data-field="override_payout" class="editable">$ '.$bucket['Main']['override_payout'].'</td>
											<td>'.$affiliate['name'].'</td>
										</tr>';
										$parent_bucket_id = $bucket['Main']['id'];
									}
									if(is_array($bucket['Sub'])) {
									// Sub bucket									
										foreach($bucket['Sub'] as $subkey=>$subbucket){
											$hover = '';
											if($subbucket['tipped'] != '') { 
												$tipped = date("m-d-Y h:i a", strtotime($subbucket['tipped']));
												$hover = 'rel="hoverovers"';
											}
							
											echo '
											<tr data-affiliate-id="'.$key.'" data-bucket-id="'.$subbucket['id'].'" data-bucket-owner="'.$key.'" class="subbucketaffiliate-'.$key.' affiliate-'.$key.' bucket-parent-'.$parent_bucket_id.' subbucket bucket" style="display:none;" role="row">
												<td style="color:darkgray; text-align:center; vertical-align:middle; cursor:pointer">
													<span style="margin-left:24px;" class="fa fa-arrow-right"></span>
												</td>
												<td '.$hover.' data-container="body" data-toggle="popover" data-html="true" data-placement="top" data-content="Bucket Tipped: '.$tipped.'">'.$subbucket['bli'].'</td>
												<td>'.$subbucket['offer_name'].'</td>
												<td data-value="'.$subbucket['amount'].'" data-field="amount" class="editable">$ '.number_format($subbucket['amount'],2,'.',',').'</td>
												<td data-value="'.$subbucket['prefill'].'" data-field="prefill" class="editable">$ '.number_format($subbucket['prefill'], 2, '.', ',').'</td>
												<td>-</td>
												<td data-value="'.$subbucket['prefill_payback'].'" data-field="prefill_payback" class="editable">'.$subbucket['prefill_payback'].' %</td>
												<td>'.$subbucket['has_subs'].'</td>
												<td data-value="'.$subbucket['override_margin'].'" data-field="override_margin" class="editable">'.$subbucket['override_margin'].' %</td>
												<td data-value="'.$subbucket['override_payout'].'" data-field="override_payout" class="editable">$ '.$subbucket['override_payout'].'</td>
												<td>'.$affiliate['name'].'</td>
											</tr>';
										}
									}
								}
								
							}
								?>
							</tbody>
						</table>
					</div>
					<!-- panel-body -->
				</div>
			</div>
		</div>
		<!-- row -->
	</div>
	<!-- contentpanel -->
</div>
<!-- mainpanel -->