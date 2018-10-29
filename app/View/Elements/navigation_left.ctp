<?php 
$navElements = $this->requestAction('/navigation/buildleft');
?>
<h5 class="leftpanel-title">Navigation</h5>
<ul class="nav nav-pills nav-stacked">
<?php 
foreach($navElements as $items=>$item){
	if(count($item)==1){
    	//No Sub Menu Detected
		$class = $item['parent']['active'];
		echo '<li class="'.$class.'"><a href="'.$item['parent']['link'].'"><i class="fa '.$item['parent']['icon'].'"></i> <span>'.$item['parent']['title'].'</span></a></li>';
	}
	else{
		//Sub Menu Detected
		foreach($item as $name=>$module){
			if($name == 'parent'){
				$class = $module['active'];
				echo '<li class="parent '.$class.'"><a href="'.$item['parent']['link'].'"><i class="fa '.$item['parent']['icon'].'"></i> <span>'.$item['parent']['title'].'</span></a>';
				echo '<ul class="children">';
			}else{
				foreach($module as $sub){
					$class = $sub['active'];
					echo '<li class="'.$class.'"><a href="'.$sub['link'].'">'.$sub['title'].'</a></li>';
				}
			}
		}
		echo '</ul>';
		echo '</li>';
	}
}
?>
</ul>