<?php echo !empty($msg)?format_notice($this,$msg): ""; ?> 
<table border="0" cellspacing="0" cellpadding="0" class="listtable">
<?php 
if(!empty($list))
{
	$rowsPerPage = !empty($pagecount)? $pagecount: NUM_OF_ROWS_PER_PAGE;
	$listCount = count($list);
	$page = !empty($page)? $page: 1;
	$stop = ($rowsPerPage >= $listCount && !empty($listid))? "<input name='paginationdiv__".$listid."_stop' id='paginationdiv__".$listid."_stop' type='hidden' value='".$page."' />": "";
	
	echo "<tr class='header'><td>&nbsp;</td><td>Teacher</td><td nowrap>Current School</td><td nowrap>Desired School</td><td>Start Date</td><td>Reason</td><td>Status</td><td>Last Updated</td></tr>";
	
	# Pick the lesser of the two - since if there is a next page, the list count will come with an extra row
	$maxRows = $listCount < $rowsPerPage? $listCount: $rowsPerPage;
	$listType = " data-type='transfer' ";
	
	for($i=0; $i<$maxRows; $i++)
	{
		$row = $list[$i];
		echo "<tr class='listrow' ".($i%2 == 1? "style='background-color:#F0F0F0;'": "").">
    <td width='1%' style='padding:0px; padding-top:5px; vertical-align:top;'>";
	
	
	if(!empty($action) && $action == 'institutionapprove')
	{
		if($row['status'] == 'pending'){
			echo "<div data-val='approve_toinstitutionapprove__".$row['id']."' ".$listType." class='approverow confirm' title='Click to approve'></div>
			<div data-val='reject_frominstitutionapprove__".$row['id']."' ".$listType." class='rejectrow' title='Click to reject'></div>";
		}
	}
	else if(!empty($action) && $action == 'countyapprove' && !$this->native_session->get('__nosignature'))
	{
		if($row['status'] == 'institutionapproved'){
			echo "<div data-val='approve_tocountyapprove__".$row['id']."' ".$listType." class='approverow' title='Click to approve'></div>
			<div data-val='reject_fromcountyapprove__".$row['id']."' ".$listType." class='rejectrow' title='Click to reject'></div>";
		}
	}
	else if(!empty($action) && $action == 'pca' && !$this->native_session->get('__nosignature'))
	{
		if($row['status'] == 'countyapproved'){
			echo "<div data-val='approve_topca__".$row['id']."' ".$listType." class='approverow' title='Click to approve'></div>
			<div data-val='reject_frompca__".$row['id']."' ".$listType." class='rejectrow' title='Click to reject'></div>";
		}
	}
	else if(!empty($action) && $action == 'ministryapprove')
	{
		if($row['status'] == 'pcaissued'){
			echo "<div data-val='approve_toministryapprove__".$row['id']."' ".$listType." class='approverow confirm' title='Click to approve'></div>
			<div data-val='reject_fromministryapprove__".$row['id']."' ".$listType." class='rejectrow' title='Click to reject'></div>";
		}
	}
	
	echo "</td> 
	<td style='vertical-align:top;'><a href='".base_url()."teacher/add/id/".$row['teacher_id']."/action/view' class='shadowbox closable'>".$row['teacher_name']."</a></td> 
	<td style='vertical-align:top;'>".(!empty($row['current_school_name'])? $row['current_school_name']: 'NONE')."</td> 
	<td style='vertical-align:top;'>".$row['desired_school_name']."</td> 
	<td style='vertical-align:top;'>".format_date($row['proposed_date'],'d-M-Y');
	if($row['actual_date'] != '0000-00-00') echo " (Actual: ".format_date($row['actual_date'],'d-M-Y').")"; 
	
	echo "</td>
	<td style='vertical-align:top;'>".limit_string_length(html_entity_decode($row['reason'], ENT_QUOTES), 200)."</td>
	<td style='vertical-align:top;'>".strtoupper($row['status'])."</td>
	<td style='vertical-align:top;'>".date('d-M-Y h:ia T', strtotime($row['last_updated']))."</td></tr> 
	
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