<?php echo !empty($msg)?format_notice($this,$msg): "";?> 
<table border="0" cellspacing="0" cellpadding="0" class="listtable">
<?php 
if(!empty($list))
{
	$rowsPerPage = !empty($pagecount)? $pagecount: NUM_OF_ROWS_PER_PAGE;
	$listCount = count($list);
	$page = !empty($page)? $page: 1;
	$stop = ($rowsPerPage >= $listCount && !empty($listid))? "<input name='paginationdiv__".$listid."_stop' id='paginationdiv__".$listid."_stop' type='hidden' value='".$page."' />": "";
	
	echo "<tr class='header'><td>&nbsp;</td><td>".(!empty($action) && $action == 'sent'? 'Recipient': 'Sender')."</td><td>Subject</td><td>Status</td><td>Date</td></tr>";
	
	# Pick the lesser of the two - since if there is a next page, the list count will come with an extra row
	$maxRows = $listCount < $rowsPerPage? $listCount: $rowsPerPage;
	$listType = " data-type='message' ";
	
	for($i=0; $i<$maxRows; $i++)
	{
		$row = $list[$i];
		echo "<tr class='listrow'>
   			 <td width='1%' style='padding:0px; padding-top:5px; vertical-align:top;'>";
		if(!empty($action) && $action == 'archive')
		{
			echo "<div data-val='restore__".$row['id']."' ".$listType." class='restorerow confirm' title='Click to restore'></div>";
		}
		else if (!empty($action) && $action == 'inbox')
		{
			echo "<div data-val='archive__".$row['id']."' ".$listType." class='archiverow confirm' title='Click to archive'></div>";
		}
	
	echo "</td> 
	<td class='top'>".$row['name']."</td>
	<td>[".$row['send_format']."] <a href='".base_url()."message/details/id/".$row['id']."' class='shadowbox closable' ".($row['status'] == 'read'? "style='font-weight:normal;'": "").">".$row['subject']."</a>";
	if(!empty($row['attachment'])) echo " &nbsp;&nbsp; <a href='".base_url()."page/download/folder/documents/file/".$row['attachment']."'><img src='".base_url()."assets/images/attachment.png' border='0' /></a>";
	
	echo "</td>
	<td>".strtoupper($row['status'])."</td>
	<td>".date('d-M-Y h:ia T', strtotime($row['date_sent'])) .
	(!empty($action) && $action == 'inbox'? "<br><div class='rightnote'><a href='".base_url()."message/reply/id/".$row['id']."/format/".$row['send_format']."'>reply</a></div>": "").
	$stop."</td></tr>";
	
	}  
}
else
{
	$stop = "<input name='paginationdiv__".$listid."_stop' id='paginationdiv__".$listid."_stop' type='hidden' value='1' />";
	echo "<tr><td>".format_notice($this,'WARNING: There are no items in this list.').$stop."</td></tr>";
}
?>

  
</table>