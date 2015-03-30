<?php echo !empty($msg)?format_notice($this,$msg): "";?> 
<table border="0" cellspacing="0" cellpadding="0" class="listtable">
<?php 
if(!empty($list))
{
	$rowsPerPage = !empty($pagecount)? $pagecount: NUM_OF_ROWS_PER_PAGE;
	$listCount = count($list);
	$page = !empty($page)? $page: 1;
	$stop = ($rowsPerPage >= $listCount && !empty($listid))? "<input name='paginationdiv__".$listid."_stop' id='paginationdiv__".$listid."_stop' type='hidden' value='".$page."' />": "";
	
	if(!empty($action) && in_array($action, array('grouplist', 'updategroups')))
	{
		echo "<tr class='header'><td>&nbsp;</td><td>Name</td><td>Default Permission</td><td>For System Only</td><td>Last Updated By</td><td>Last Updated On</td></tr>";
	}
	else if(!empty($action) && $action=='userlist')
	{
		echo "<tr class='header'><td>Name</td><td>Permissions</td><td>Default Permission</td><td>Last Updated On</td></tr>";
	}
	else
	{
		echo "<tr class='header'><td>Category</td><td>Permission</td><td>Code</td><td>URL</td><td>Status</td></tr>";
	}
	
	# Pick the lesser of the two - since if there is a next page, the list count will come with an extra row
	$maxRows = $listCount < $rowsPerPage? $listCount: $rowsPerPage;
	for($i=0; $i<$maxRows; $i++)
	{
		$row = $list[$i];
		if(!empty($action) && in_array($action, array('grouplist', 'updategroups')))
		{
			$listType = " data-type='permission' ";
			
			echo "<tr class='listrow' ".($i%2 == 1? "style='background-color:#F0F0F0;'": "").">
				<td>";
				if($row['is_removable'] == 'Y' && $action=='updategroups'){
					echo "<div data-val='reject__".$row['id']."' ".$listType." class='rejectrow' title='Click to reject'></div>";
				} else { 
					echo "&nbsp;";
				}
				echo "</td> 
				<td><a href='".base_url()."permission/group_permissions/id/".$row['id']."' class='shadowbox closable'>".$row['notes']."</a></td>
				<td>".$row['display']."</td>
				<td>".$row['for_system']."</td>
				<td>".$row['last_updated_user']."</td>
				<td>".format_date($row['last_update_date'],'d-M-Y h:ia T').
	
	((check_access($this, 'change_group_permissions', 'boolean') && $action == 'updategroups')? "<div class='rightnote'><a href='".base_url()."permission/add_group/id/".$row['id']."/action/edit' class='shadowbox'>edit</a></div>": "")
	
				."</td>
			</tr>
			<tr><td style='padding:0px;'></td><td colspan='6' style='padding:0px;'><div id='action__".$row['id']."' class='actionrowdiv' style='display:none;'></div>".$stop."</td></tr>";
		}
		else if(!empty($action) && $action=='userlist')
		{
			echo "<tr class='listrow'>
				<td>".$row['user_name']."</td>
				<td><a href='".base_url()."permission/group_permissions/id/".$row['group_id']."' class='shadowbox closable'>".$row['group_name']."</a></td> 
				<td>".$row['default_permission']."</td>
				<td>".format_date($row['last_update_date'],'d-M-Y h:ia T')."".$stop."</td>
			</tr>";
		}
		else
		{
			echo "<tr class='listrow'>
				<td>".ucfirst(str_replace('_', ' ', $row['category']))."</td> 
				<td>".$row['display']."</td>
				<td>".$row['code']."</td>
				<td>".$row['url']."</td>
				<td>".$row['status']."".$stop."</td>
			</tr>";
		}
	}  
}
else
{
	$stop = "<input name='paginationdiv__".$listid."_stop' id='paginationdiv__".$listid."_stop' type='hidden' value='1' />";
	echo "<tr><td>".format_notice($this,'WARNING: There are no items in this list.').$stop."</td></tr>";
}
?>

  
</table>
