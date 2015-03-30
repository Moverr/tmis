<?php echo !empty($msg)?format_notice($this,$msg): "";
if(!empty($action) && $action == 'changepassword')
{
	echo "<div class='smalltext' style='padding-left:18px;padding-top:5px;'>NOTE: A password should be at least 8 characters and contain a number and a letter.</div>";
}

?> 
<table border="0" cellspacing="0" cellpadding="0" class="listtable">
<?php 
if(!empty($list))
{
	$rowsPerPage = !empty($pagecount)? $pagecount: NUM_OF_ROWS_PER_PAGE;
	$listCount = count($list);
	$page = !empty($page)? $page: 1;
	$stop = ($rowsPerPage >= $listCount && !empty($listid))? "<input name='paginationdiv__".$listid."_stop' id='paginationdiv__".$listid."_stop' type='hidden' value='".$page."' />": "";
	
	echo "<tr class='header'><td>&nbsp;</td><td>Name</td><td>Permission Group</td><td>Email Address</td><td>Telephone</td><td>Status</td><td>Last Updated</td></tr>";
	
	# Pick the lesser of the two - since if there is a next page, the list count will come with an extra row
	$maxRows = $listCount < $rowsPerPage? $listCount: $rowsPerPage;
	$listType = " data-type='user' ";
	
	for($i=0; $i<$maxRows; $i++)
	{
		$row = $list[$i];
		echo "<tr class='listrow' ".($i%2 == 1? "style='background-color:#F0F0F0;'": "").">
    <td width='1%' style='padding:0px; padding-top:5px; vertical-align:top;'>";
	
	
	if(!empty($action) && $action == 'update')
	{
		if($row['status'] == 'completed'){
			echo "<div data-val='approve__".$row['id']."' ".$listType." class='approverow confirm' title='Click to approve'></div>
			<div data-val='reject__".$row['id']."' ".$listType." class='rejectrow' title='Click to reject'></div>
			<div data-val='archive__".$row['id']."' ".$listType." class='archiverow confirm' title='Click to archive'></div>";
		}
		else if($row['status'] == 'active'){
			echo "<div data-val='block__".$row['id']."' ".$listType." class='blockrow confirm' title='Click to block'></div>
			<div data-val='archive__".$row['id']."' ".$listType." class='archiverow confirm' title='Click to archive'></div>";
		}
		else if($row['status'] == 'archived'){
			echo "<div data-val='restore__".$row['id']."' ".$listType." class='restorerow confirm' title='Click to restore'></div>";
		}
		else if($row['status'] == 'blocked'){
			echo "<div data-val='approve__".$row['id']."' ".$listType." class='approverow confirm' title='Click to approve'></div>";
		}
	}
	
	echo "</td> <td>".$row['last_name'].", ".$row['first_name'];
	
	# Changing the user's password
	if(!empty($action) && $action == 'changepassword')
	{
		echo "<div id='userpassword_".$row['id']."__form'>
		<table border='0' cellspacing='0' cellpadding='0' class='microform'>
		<tr>
			<td style='padding-left:0px;'><input type='text' id='userpassword_".$row['id']."__field' name='userpassword_".$row['id']."__field' title='The New User Password' placeholder='New Password' class='textfield password' value='' style='width:140px;'/></td>
			<td style='padding-left:0px;'><button type='button' id='userpassword_".$row['id']."__btn' name='userpassword_".$row['id']."__btn' class='greybtn submitmicrobtn'>SET</button>
			<input type='hidden' name='action' id='action' value='".base_url()."user/change_password/id/".$row['id']."' />
			<input type='hidden' name='resultsdiv' id='resultsdiv' value='userpassword_".$row['id']."__form' /></td>
		</tr>
		</table>
		</div>";
	}
	
	echo "</td> <td>";
	
	#Setting user permissions
	if(!empty($action) && $action == 'setpermission')
	{
		echo "<div id='userpermission_".$row['id']."'></div><input type='text' id='userpermission_".$row['id']."__roles' name='userpermission_".$row['id']."__roles' title='The User Permission Group' placeholder='Select Permission' class='textfield selectfield' onchange=\"updateFieldLayer('".base_url()."user/set_permissions/set_id/".$row['id']."','userpermission_".$row['id']."__roles','','userpermission_".$row['id']."','')\" value='".$row['user_role']."'/>";
	}
	else
	{
		echo $row['user_role'];
	}
	
	echo "</td>
	<td>".$row['email_address']."</td>
	<td>".$row['telephone']."</td>
	<td>".$row['status']."</td>
	<td>".format_date($row['last_updated'],'d-M-Y h:ia T').
	"<br><div class='rightnote'><a href='".base_url()."user/add/id/".$row['id']."/action/view' class='shadowbox closable'>details</a></div>".
	
	((check_access($this, 'add_new_user', 'boolean') && !empty($action) && $action == 'update')? "<div class='rightnote'><a href='".base_url()."user/add/id/".$row['id']."/action/edit/actionurl/".$action."' class='shadowbox'>edit</a></div>": "")
	
	."</td></tr>
	<tr><td style='padding:0px;'></td><td colspan='6' style='padding:0px;'><div id='action__".$row['id']."' class='actionrowdiv' style='display:none;'></div>".$stop."</td></tr>";
	
	}  
}
else
{
	$stop = "<input name='paginationdiv__".$listid."_stop' id='paginationdiv__".$listid."_stop' type='hidden' value='1' />";
	echo "<tr><td>".format_notice($this,'WARNING: There are no items in this list.').$stop."</td></tr>";
}
?>

  
</table>