<?php
$data_array = json_decode($data,true);
$header = $data_array['data']['headers'];
echo implode(',', $header) . "\n";
foreach ($data_array['data']['file'] as $id=>$row){
	echo implode(',', $row) . "\n";
}
?>