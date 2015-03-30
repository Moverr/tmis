<?php echo !empty($msg)?format_notice($this,$msg): "";?> 
<table border="0" cellspacing="0" cellpadding="0" class="listtable">
<?php 
if(!empty($list))
{
	$rowsPerPage = !empty($pagecount)? $pagecount: NUM_OF_ROWS_PER_PAGE;
	$listCount = count($list);
	$page = !empty($page)? $page: 1;
	$stop = ($rowsPerPage >= $listCount && !empty($listid))? "<input name='paginationdiv__".$listid."_stop' id='paginationdiv__".$listid."_stop' type='hidden' value='".$page."' />": "";
	
	# Pick the lesser of the two - since if there is a next page, the list count will come with an extra row
	$maxRows = $listCount < $rowsPerPage? $listCount: $rowsPerPage;
	for($i=0; $i<$maxRows; $i++)
	{
		$row = $list[$i];
		echo "<tr class='listrow'>
    <td>
	<table border='0' cellspacing='0' cellpadding='0' width='100%'>
	<tr><td width='1%' style='padding:0px; padding-top:5px; vertical-align:top;'>";
	
	$listType = " data-type='vacancy' ";
	if(!empty($action) && $action == 'publish')
	{
		if($row['status'] == 'saved'){
			echo "<div data-val='approve_toverify__".$row['id']."' ".$listType." class='approverow' title='Click to approve'></div>
			<div data-val='reject_fromverify__".$row['id']."' ".$listType." class='rejectrow' title='Click to reject'></div>
			<div data-val='archive__".$row['id']."' ".$listType." class='archiverow confirm' title='Click to archive'></div>";
		}
		else if($row['status'] == 'verified'){
			echo "<div data-val='approve_topublish__".$row['id']."' ".$listType." class='publishrow' title='Click to publish'></div>
			<div data-val='reject_frompublish__".$row['id']."' ".$listType." class='rejectrow' title='Click to reject'></div>";
		}
		else if($row['status'] == 'published'){
			echo "<div data-val='archive__".$row['id']."' ".$listType." class='archiverow confirm' title='Click to archive'></div>";
		}
	} 
	else if(!empty($action) && $action == 'archive')
	{
		if($row['status'] == 'saved'){
			echo "<div data-val='archive__".$row['id']."' ".$listType." class='archiverow confirm' title='Click to archive'></div>";
		}
		else if($row['status'] == 'archived'){
			echo "<div data-val='restore__".$row['id']."' ".$listType." class='restorerow confirm' title='Click to restore'></div>";
		}
	}
	else if(!empty($action) && $action == 'verify')
	{
		if($row['status'] == 'saved'){
			echo "<div data-val='approve_toverify__".$row['id']."' ".$listType." class='approverow' title='Click to approve'></div>";
		}
	}
	
	echo "</td>
	<td width='99%'>
	<div class='rowcontent'><div class='nextdiv header".($row['status'] == 'published'? ' yellow': '')."'>".$row['topic']."</div>".($row['status'] == 'published'? "<div class='nextdiv publishicon'>&nbsp;</div>": "")."
	<br><div class='nextdiv value'>ROLE: ".$row['role_name']."</div><div class='nextdiv value'>AT: ".$row['institution_name']."</div>
	<br>
".$row['summary']."</div>
    <div class='leftnote'>Respond By: ".format_date($row['end_date'],'d-M-Y')."</div>
	<div class='rightnote'><a href='".base_url()."vacancy/details/id/".$row['id']."' class='shadowbox closable'>details</a></div>";
	
	echo check_access($this, 'add_new_job', 'boolean') && (!empty($action) && $action == 'publish')?"<div class='rightnote'><a href='".base_url()."vacancy/add/id/".$row['id']."' class='shadowbox'>edit</a></div>": "";
	
	echo "</td></tr>
	<tr><td style='padding:0px;'></td><td style='padding:0px;'><div id='action__".$row['id']."' class='actionrowdiv' style='display:none;'></div>".$stop."</td></tr>
	</table>
	</td>
  </tr>";
	}  
}
else
{
	$stop = "<input name='paginationdiv__".$listid."_stop' id='paginationdiv__".$listid."_stop' type='hidden' value='1' />";
	echo "<tr><td>".format_notice($this,'WARNING: There are no items in this list.').$stop."</td></tr>";
}
?>

  
</table>