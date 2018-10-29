<?php
$data = json_decode($data,true);
$header = array('Lead ID','Affiliate ID','Offer ID','Email Address','IP Address','Payable','Receivable','S1','S2','S3','Lead Time');
echo implode(',', $header) . "\n";
foreach ($data['file'] as $id=>$row):
	echo implode(',', $row['file']) . "\n";
endforeach;
?>