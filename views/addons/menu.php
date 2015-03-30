<?php 
	$permissions = $this->_query_reader->get_list('get_permission_details', array('permissions'=>"'".implode("','", $this->native_session->get('__permissions'))."'"));
	$menu = array();
	$selectedCategory = "";
	if(empty($clear_menu))
	{
		if(!$this->native_session->get('__selected_permission'))
		{
			$default = $this->native_session->get('__default_permission');
			$this->native_session->set('__selected_permission', $default);
		}
	}
	else
	{
		$this->native_session->delete('__selected_permission');
	}	
	
	# Generate the menu array with the data results
	foreach($permissions AS $row)
	{
		$menu[$row['category']] = !empty($menu[$row['category']])? $menu[$row['category']]:array();
		array_push($menu[$row['category']], array('code'=>$row['code'], 'display'=>$row['display'], 'url'=>$row['url']));
			
		if(empty($clear_menu) && $row['code'] == $this->native_session->get('__selected_permission')) 
		{
			$selectedCategory = $row['category'];
		}
	}
	
		
	# Now generate the menu itself
	foreach($menu AS $item=>$subMenus)
	{
		echo "<div class='item".($item == $selectedCategory? ' selected': '')."'>
            <div class='header'>".ucwords(str_replace('_',' ', $item))."</div>";
		foreach($subMenus AS $sub)
		{
			echo "<div data-rel='".$sub['url']."'".($this->native_session->get('__selected_permission') == $sub['code']? " class='selected'": '').">".$sub['display']."</div>";
		}
        echo "</div>";
	}
		
?>