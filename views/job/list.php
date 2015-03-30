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
		$listType = " data-type='job' ";
		$row = $list[$i];
		
		echo "<tr class='listrow'>
    <td>
	<table border='0' cellspacing='0' cellpadding='0' width='100%'>
	<tr><td>
	<div class='rowcontent'>";
	
	if($this->native_session->get('__user_id'))
	{
		if($action == 'view'){
			 if(!empty($saved_jobs) && in_array($row['id'], $saved_jobs)) 
			 {
				 echo "<div class='nextdiv activesaverow' style='min-width:30px;margin-right: 3px;' title='Click to save'></div>";
			 } else {
				 echo "<div data-val='save__".$row['id']."' ".$listType." class='nextdiv saverow confirm' style='min-width:30px;margin-right: 3px;' title='Click to save'></div>";
			 }
		}
		
		if($action == 'saved') echo "<div data-val='archive__".$row['id']."' ".$listType." class='nextdiv archiverow confirm' style='min-width:30px;margin-right: 3px;' title='Click to archive'></div>";
	}
	
	
	if($action == 'report')
	{
		echo "<div class='nextdiv header' style='vertical-align:top; padding-top:3px; width: 130px;'>Applicant:</div><div class='nextdiv value'>".$row['applicant_name']."</div>
	<br><div class='nextdiv header' style='width: 130px;'>Application Date:</div><div class='nextdiv value'>".date('d-M-Y h:ia T', strtotime($row['submission_date']))."</div>
	<br><div class='nextdiv header' style='width: 130px;'>Role:</div><div class='nextdiv value'>".$row['role_name']." &nbsp; &nbsp; AT: ".$row['institution_name']."</div>
	<br><div class='nextdiv header' style='width: 130px;'>Job Summary:</div><div class='nextdiv value'>".$row['summary']."</div>";
	}
	else
	{
		echo "<div class='nextdiv header' style='vertical-align:top; padding-top:3px;'>".$row['topic']." ".($action == 'status'? "[".strtoupper($row['application_status'])."]":"")."</div>
	<br><div class='nextdiv value'>ROLE: ".$row['role_name']."</div><div class='nextdiv value'>AT: ".$row['institution_name']."</div>
	<br>
".$row['summary']."</div>
    <div class='leftnote'>".($action == 'status'? "Sent On: ".date('d-M-Y', strtotime($row['submission_date'])): "Respond By: ".date('d-M-Y', strtotime($row['end_date'])) )."</div>";
	}
	
	echo "<div class='rightnote'><a href='".base_url()."vacancy/details/id/".$row['id']."' class='shadowbox closable'>details</a></div>
	".$stop."</td></tr>
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