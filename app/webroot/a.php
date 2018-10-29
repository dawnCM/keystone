<?php 

$fraud = array('fraud'=>array('ip'=>array('id'=>'447','blacklist'=>'0','host'=>'google-public-dns','org'=>'AS15169 Google Inc','country'=>'US')));

echo "<pre>";
print_r($fraud);
echo "</pre>";
echo json_encode($fraud);

?>