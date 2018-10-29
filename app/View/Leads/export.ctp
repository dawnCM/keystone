<?php
$data = json_decode($data,true);
$trec = 0;
$tpay = 0;
$header = array('Affiliate ID', 'Offer ID', 'Track ID', 'Lead ID', 'Email', 'Receivable','Payable', 'Sold', 'Date/Time');
echo implode(',', $header) . "\n";
foreach ($data['data'] as $id=>$row):
	echo implode(',', $row) . "\n";
	$trec += $row['receivable'];
	$tpay += $row['payable'];
endforeach;
$footer = array('','','','','',$trec,$tpay,'','');
echo implode(',',$footer);
?>