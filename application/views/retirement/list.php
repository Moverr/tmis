<?php echo !empty($msg)?format_notice($this,$msg): ""; ?> 
<table border="0" cellspacing="0" cellpadding="0" class="listtable">
<?php 
if(!empty($list))
{
	$rowsPerPage = !empty($pagecount)? $pagecount: NUM_OF_ROWS_PER_PAGE;
	$listCount = count($list);
	$page = !empty($page)? $page: 1;
	$stop = ($rowsPerPage >= $listCount && !empty($listid))? "<input name='paginationdiv__".$listid."_stop' id='paginationdiv__".$listid."_stop' type='hidden' value='".$page."' />": "";
	
	echo "<tr class='header'><td>&nbsp;</td><td>Teacher</td><td>Age</td><td>Reason</td><td>Proposed Date</td><td>Status</td><td>Last Updated</td></tr>";
	
	# Pick the lesser of the two - since if there is a next page, the list count will come with an extra row
	$maxRows = $listCount < $rowsPerPage? $listCount: $rowsPerPage;
	$listType = " data-type='retirement' ";
	
	for($i=0; $i<$maxRows; $i++)
	{
		$row = $list[$i];
		echo "<tr class='listrow' ".($i%2 == 1? "style='background-color:#F0F0F0;'": "").">
    <td width='1%' style='padding:0px; padding-top:5px; vertical-align:top;'>";
	
	
	if(!empty($action) && $action == 'approve' && !$this->native_session->get('__nosignature'))
	{
		if($row['status'] == 'pending'){
			echo "<div data-val='approve__".$row['id']."' ".$listType." class='approverow' title='Click to approve'></div>
			<div data-val='reject__".$row['id']."' ".$listType." class='rejectrow' title='Click to reject'></div>";
		}
	}
	
	echo "</td> 
	<td><a href='".base_url()."teacher/add/id/".$row['retiree_id']."/action/view' class='shadowbox closable'>".$row['teacher_name']."</a></td> 
	<td style='".format_age($row['age'])."'>".$row['age']." yrs".format_age($row['age'],'timeleft')."</td>
	<td>".limit_string_length(html_entity_decode($row['retiree_reason'], ENT_QUOTES), 200)."</td>
	<td>".format_date($row['proposed_date'],'d-M-Y');
	if($row['confirmed_date'] != '0000-00-00') echo " (Actual: ".format_date($row['confirmed_date'],'d-M-Y').")"; 
	
	echo "</td>
	<td>".strtoupper($row['status'])."</td>
	<td>".format_date($row['last_updated'],'d-M-Y h:ia T')."</td></tr>
	
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