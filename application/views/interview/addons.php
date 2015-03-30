<?php 
$jquery = "<script src='".base_url()."assets/js/jquery-2.1.1.min.js' type='text/javascript'></script>";
$javascript = "<script type='text/javascript' src='".base_url()."assets/js/tmis.js'></script>".get_AJAX_constructor(TRUE); 

$tableHTML = "<link rel='stylesheet' href='".base_url()."assets/css/tmis.css' type='text/css' media='screen' />
<link rel='stylesheet' href='".base_url()."assets/css/tmis.list.css' type='text/css' media='screen' />"; 





if(!empty($area) && $area == "submit_recommendation")
{
	#Redirect back to list if submission has been made
	if(!empty($result)) $tableHTML .= "<script type='text/javascript'>window.top.location.href = '".base_url()."interview/lists/action/recommend';</script>";
	
	# There was an error loading the recommendation
	if(!empty($msg)) 
	{
		$tableHTML .= format_notice($this, $msg);
	} 
	# Show the recommendation form
	else
	{
	
		$tableHTML .= "<form id='recommend_".$id."' method='post' action='".base_url()."interview/recommend/id/".$id."'  class='simplevalidator'>
		<table border='0' cellspacing='0' cellpadding='10'>
			<tr><td class='label'>Recommend:</td><td class='value'>".$this->native_session->get('applicant')."</td></tr>
			<tr><td class='label'>Applying To:</td><td class='value'>".$this->native_session->get('institution_name')."</td></tr>
			<tr><td class='label'>Applied On:</td><td class='value'>".date('d-M-Y h:ia T', strtotime($this->native_session->get('submission_date')))."</td></tr>
			<tr><td class='label top'>Your Recommendation:</td><td><textarea id='details' name='details' title='Your recommendation' class='textfield' placeholder='Enter your recommendation here' style='min-width:300px; min-height: 150px;'></textarea></td></tr>
			
			<tr><td>&nbsp;</td><td><input type='submit' name='submit' id='submit' class='btn' value='SUBMIT' /></td></tr>
		</table></form><input type='hidden' id='layerid' name='layerid' value='' />";
	}
}




else if(!empty($area) && $area == "recommendation_list")
{
	$tableHTML .= "<link rel='stylesheet' href='".base_url()."assets/css/tmis.list.css' type='text/css' media='screen' />
	<table border='0' cellspacing='0' cellpadding='0' class='listtable' style='font-size:14px;'>
		<tr class='header'><td>Recommender</td><td>Current School</td><td>Current Role</td><td>Recommendation</td><td>Date</td></tr>";
	foreach($list AS $row)
	{
		$tableHTML .= "<tr class='listrow'>
		<td style='vertical-align:top;'>".$row['recommender']."</td>
		<td style='vertical-align:top;'>".$row['recommender_school']."</td>
		<td style='vertical-align:top;'>".$row['recommender_role']."</td>
		<td>".html_entity_decode($row['notes'], ENT_QUOTES)."</td>
		<td style='vertical-align:top;'>".date('d-M-Y h:ia T', strtotime($row['date_added']))."</td>
		</tr>";
	}
	$tableHTML .= "</table>";

}





else if(!empty($area) && $area == "set_date")
{
	#Redirect back to list if submission has been made
	if(!empty($result)) $tableHTML .= "<script type='text/javascript'>window.top.location.href = '".base_url()."interview/lists/action/setdate';</script>";
	
	# There was an error loading the details
	if(!empty($msg)) 
	{
		$tableHTML .= format_notice($this, $msg);
	} 
	# Show the form
	else
	{
	
		$tableHTML .= $jquery.$javascript."<link rel='stylesheet' href='".base_url()."assets/css/jquery-ui.css' type='text/css' media='screen' />
		<script src='".base_url()."assets/js/jquery-ui.js' type='text/javascript'></script>
		<script src='".base_url()."assets/js/jquery-ui-timepicker-addon.js' type='text/javascript'></script>
		<script src='".base_url()."assets/js/tmis.fileform.js' type='text/javascript'></script>
		
		<form id='setdate_".$id."' method='post' action='".base_url()."interview/set_date/id/".$id."'  class='simplevalidator'>
		<table border='0' cellspacing='0' cellpadding='10'>
			
			<tr><td class='label'>Applicant:</td><td class='value'>".$this->native_session->get('applicant')."</td></tr>
			
			<tr><td class='label'>Applying To:</td><td class='value'>".$this->native_session->get('institution_name')."</td></tr>
			
			<tr><td class='label'>Applied On:</td><td class='value'>".date('d-M-Y h:ia T', strtotime($this->native_session->get('submission_date')))."</td></tr>
			
			<tr><td class='label top'>Interviewer:</td><td><input type='text' id='interviewer__users' name='interviewer__users' title='Select or Search for User' placeholder='Select or Search for User' class='textfield selectfield searchable' value='' style='width:95%;' /><input type='text' class='textfield' id='userid' name='userid' value='' style='display:none;' /></td></tr>
			
			<tr><td class='label top'>Interview Date:</td><td><input type='text' id='interviewdate' name='interviewdate' title='Interview Date' class='textfield datefield showtime futuredate' value=''/></td></tr>
			
			<tr><td class='label top'>Notes:</td><td><textarea id='notes' name='notes' title='Interview Notes' class='textfield' placeholder='This message is sent to the applicant on submission of this form. Enter information the applicant may need to consider before the interview.' style='min-width:300px; min-height: 200px;'></textarea></td></tr>
			
			<tr><td>&nbsp;</td><td><input type='submit' name='submit' id='submit' class='btn' value='SUBMIT' /></td></tr>
		</table>
		<input type='hidden' id='errormessage' name='errormessage' value='Enter all interview details and a future date' />
		</form>
		<input type='hidden' id='layerid' name='layerid' value='' />";
	}
}





else if(!empty($area) && $area == "add_note")
{
	#Redirect back to list if submission has been made
	if(!empty($result)) $tableHTML .= "<script type='text/javascript'>window.top.location.href = '".base_url()."interview/lists/action/addresult';</script>";
	
	# There was an error loading the application
	if(!empty($msg)) 
	{
		$tableHTML .= format_notice($this, $msg);
	} 
	# Show the note form
	else
	{
	
		$tableHTML .= "<form id='addnote_".$id."' method='post' action='".base_url()."interview/add_note/id/".$id."'  class='simplevalidator'>
		<table border='0' cellspacing='0' cellpadding='10'>
			<tr><td class='label'>Job:</td><td class='value'>".$this->native_session->get('job')."</td></tr>
			<tr><td class='label'>Applicant:</td><td class='value'>".$this->native_session->get('applicant')."</td></tr>
			<tr><td class='label'>Interviewer:</td><td class='value'>".$this->native_session->get('interviewer')."</td></tr>
			<tr><td class='label'>Date:</td><td class='value'>".date('d-M-Y h:ia T', strtotime($this->native_session->get('interview_date')))."</td></tr>
			<tr><td class='label top'>Your Note:</td><td><textarea id='details' name='details' title='Your note' class='textfield' placeholder='Enter your note here' style='min-width:300px; min-height: 150px;'></textarea></td></tr>
			
			<tr><td>&nbsp;</td><td><input type='submit' name='submit' id='submit' class='btn' value='SUBMIT' /></td></tr>
		</table></form>";
	}
}




else if(!empty($area) && $area == "note_list")
{
	$tableHTML .= "<link rel='stylesheet' href='".base_url()."assets/css/tmis.list.css' type='text/css' media='screen' />
	<table border='0' cellspacing='0' cellpadding='0' class='listtable'>
		<tr class='header'><td nowrap>Added By</td><td>Note</td><td>Date</td></tr>";
	foreach($list AS $row)
	{
		$tableHTML .= "<tr class='listrow'><td style='vertical-align:top;'>".$row['added_by']."</td><td>".html_entity_decode($row['details'], ENT_QUOTES)."</td><td style='vertical-align:top;'>".date('d-M-Y h:ia T', strtotime($row['date_added']))."</td></tr>";
	}
	$tableHTML .= "</table>";

}





else if(!empty($area) && $area == "set_result")
{
	#Redirect back to list if submission has been made
	if(!empty($result)) $tableHTML .= "<script type='text/javascript'>window.top.location.href = '".base_url()."interview/lists/action/addresult';</script>";
	
	# There was an error loading the details
	if(!empty($msg)) 
	{
		$tableHTML .= format_notice($this, $msg);
	} 
	# Show the form
	else
	{
	
		$tableHTML .= $jquery.$javascript."<link rel='stylesheet' href='".base_url()."assets/css/jquery-ui.css' type='text/css' media='screen' />
		<script src='".base_url()."assets/js/jquery-ui.js' type='text/javascript'></script>
		<script src='".base_url()."assets/js/jquery-ui-timepicker-addon.js' type='text/javascript'></script>
		<script src='".base_url()."assets/js/tmis.fileform.js' type='text/javascript'></script>
		
		<form id='setresult_".$id."' method='post' action='".base_url()."interview/set_result/id/".$id."'  class='simplevalidator'>
		<table border='0' cellspacing='0' cellpadding='10'>
			
			<tr><td class='label'>Job:</td><td class='value'>".$this->native_session->get('job')."</td></tr>
			<tr><td class='label'>Applicant:</td><td class='value'>".$this->native_session->get('applicant')."</td></tr>
			<tr><td class='label'>Interviewer:</td><td class='value'>".$this->native_session->get('interviewer')."</td></tr>
			<tr><td class='label'>Planned Interview Date:</td><td class='value'>".date('d-M-Y h:ia T', strtotime($this->native_session->get('interview_date')))."</td></tr>
			
			<tr><td class='label top'>Result:</td><td><div class='nextdiv'><input type='text' id='result__interviewresults' name='result__interviewresults' onchange=\"showHideOnFieldValueCondition('', 'shortlistdiv', 'result__interviewresults', 'Passed')\" title='Select Result' placeholder='Select Result' class='textfield selectfield' value='' style='width:220px;'/></div>
			
			<div id='shortlistdiv' class='nextdiv' style='display:none;'><input type='text' id='shortlist__shortlists' name='shortlist__shortlists' title='Enter or Select Shortlist' data-val='jobid' placeholder='Enter or Select Shortlist' class='textfield selectfield editable optional' value='' style='width:220px;' /><input type='hidden' id='shortlistid' name='shortlistid' value='' /><input type='hidden' id='jobid' name='jobid' value='".$this->native_session->get('job_id')."' /></div></td></tr>
			
			<tr><td class='label top'>Actual Interview Date:</td><td><input type='text' id='interviewdate' name='interviewdate' title='Interview Date' class='textfield datefield showtime' value=''/></td></tr>
			
			<tr><td class='label top'>Duration:</td><td><input type='text' id='duration' name='duration' title='Duration in Minutes' class='textfield numbersonly' placeholder='Minutes' value=''/></td></tr>
			
			<tr><td class='label top'>Notes:</td><td><textarea id='notes' name='notes' title='Interview Notes' class='textfield optional' placeholder='Enter notes related to this result (Optional).' style='min-width:300px; min-height: 200px;'></textarea></td></tr>
			
			<tr><td>&nbsp;</td><td><input type='submit' name='submit' id='submit' class='btn' value='SUBMIT' /></td></tr>
		</table></form>
		<input type='hidden' id='layerid' name='layerid' value='' />";
	}
}




else if(!empty($area) && $area == "view_shortlist")
{
	if(!empty($msg))
	{
		$tableHTML .= format_notice($this, $msg);
	}
	else
	{
		$tableHTML .= $jquery.$javascript."<link rel='stylesheet' href='".base_url()."assets/css/tmis.list.css' type='text/css' media='screen' />
		<script src='".base_url()."assets/js/tmis.list.js' type='text/javascript'></script>
		
		<table width='100%'>
		<tr>
			<td><span class='h1'>".$shortlist_name."</span></td>
			<td><div class='nextdiv downloadcontenticon' style='margin-left:5px;' data-url='interview/download_list/name/".$name."/vacancy/".$vacancy."' title='Click to download'></div></td>
		</tr></table>
		<table border='0' cellspacing='0' cellpadding='0' class='listtable'>
		<tr class='header'><td>Name</td><td nowrap>Teacher Number</td><td>Address</td><td nowrap>Post Applied</td></tr>";
		foreach($list AS $row)
		{
			$tableHTML .= "<tr class='listrow'>
			<td style='vertical-align:top;'>".$row['name']."</td>
			<td style='vertical-align:top;'>".$row['number']."</td>
			<td style='vertical-align:top;'>".$row['address']."</td>
			<td style='vertical-align:top;'>".$row['post_applied']."</td>
			</tr>";
		}
		$tableHTML .= "</table>";
	}
}








# Select the interview board
else if(!empty($area) && $area == "select_board")
{
	if(!empty($msg))
	{
		$tableHTML .= format_notice($this, $msg);
	}
	else
	{
		$tableHTML .= $jquery.$javascript."<link rel='stylesheet' href='".base_url()."assets/css/tmis.list.css' type='text/css' media='screen' />
		<script src='".base_url()."assets/js/tmis.fileform.js' type='text/javascript'></script>
		<script src='".base_url()."assets/js/tmis.list.js' type='text/javascript'></script>
		
		<table width='100%' cellpadding='5' class='microform'>
		<tr><td colspan='2' class='h1'>Interview Board</td></tr>
		<tr><td colspan='2'><div id='boardsaveresults_div'></div></td></tr>
		
		<tr>
			<td class='label' width='1%'>Board Name:</td><td>".
			(!empty($view)? "<span class='value'>".$this->native_session->get('boardname__boards')."</span>": "<input type='text' id='boardname__boards' name='boardname__boards' title='Select or Enter Board Name' placeholder='Select or Enter Board Name' class='textfield selectfield editable' value='".$this->native_session->get('boardname__boards')."' style='width:92%;'/><input type='hidden' id='boardid' name='boardid' value='' />")
			."</td>
		</tr>
		
		<tr>
			<td class='label top' nowrap>Board members:</td><td>
			".(!empty($view)? "": 
			"<div id='addmemberlink'><a href='javascript:;' onclick=\"showHideOnCondition('addmemberlink', 'addmemberform', '')\">Add a Member</a></div>
			<div id='addmemberform' style='display:none;'><table width='100%' cellpadding='0'>
			<tr>
				<td><input type='text' id='membername__users' name='membername__users' title='Select or search for member' placeholder='Select or Search For Member' class='textfield selectfield searchable optional' value='' style='width:97%;'/>
				<input type='hidden' id='userid' name='userid' value='' /></td>
				<td><button type='button' class='greybtn' name='addmember' id='addmember' onclick=\"updateFieldLayer('".base_url()."interview/add_board_member','userid<>membername__users','','memberlist_div','Select a user to add');updateFieldValue('membername__users<>userid', '<>')\" value='ADD'>ADD</button></td>
			</tr>
			</table><br></div>")
			
			
			."<div id='memberlist_div' style='min-height:150px;overflow-y: auto; overflow-x: hidden;'>";
			if($this->native_session->get('boardmembers'))
			{
				$tableHTML .= "<table border='0' cellspacing='0' cellpadding='0' class='listtable'>
					<tr class='header'>".(!empty($view)? "": "<td width='1%'>&nbsp;</td>")."<td>Member</td><td width='1%' nowrap>Is Chairman</td></tr>";
				
				foreach($this->native_session->get('boardmembers') AS $row)
				{
					$tableHTML .= "<tr class='listrow'>".(!empty($view)? "": "<td style='cursor: pointer;' onclick=\"confirmActionToLayer('".base_url()."interview/remove_board_member/userid/".$row['member_id']."', '', '', 'memberlist_div', 'Are you sure you want to remove this member')\"><img src='".base_url()."assets/images/reject_grey.png' /><input type='hidden' id='boardmembers_".$row['member_id']."' name='boardmembers[]' value='".$row['member_id']."' /></td>")."</td>
						<td style='vertical-align:top;'>".html_entity_decode($row['member_name'], ENT_QUOTES)."</td>
						<td style='vertical-align:top;'>
						".(!empty($view)? $row['is_chairman']: "<input type='radio' id='ischairman_".$row['member_id']."' name='ischairman' value='".$row['member_id']."' ".($row['is_chairman'] == 'Y'? "checked": "")."/>")."
						</td>
						</tr>";
				}
				$tableHTML .= "</table>";
			}
			else 
			{
				$tableHTML .= "None selected";
			}
			
			$tableHTML .= "</div></td>
		</tr>".
		
		(!empty($view)? "": "<tr><td>&nbsp;</td><td><button type='button' class='btn submitmicrobtn' name='saveboard' id='saveboard' value='SAVE'>SAVE</button>
		<br><span class='smalltext'>NOTE: This will also update the interviewer to be the board chairman</span>
		<input type='hidden' id='action' name='action' value='".base_url()."interview/select_board/id/".$id."' />
		<input type='hidden' id='resultsdiv' name='resultsdiv' value='boardsaveresults_div' />
		<input type='hidden' id='errormessage' name='errormessage' value='Enter or select a board' />
		<input type='hidden' id='layerid' name='layerid' value='' />
		</td></tr>")
		
		."</table>";
		
	}
	
}





# Only board members list
else if(!empty($area) && $area == "select_board_members")
{
	
	if($this->native_session->get('boardmembers'))
	{
		$tableHTML .= $jquery.$javascript."<link rel='stylesheet' href='".base_url()."assets/css/tmis.list.css' type='text/css' media='screen' />
		<script src='".base_url()."assets/js/tmis.list.js' type='text/javascript'></script>
		
		".(!empty($msg)? format_notice($this, $msg):"")."
		<table border='0' cellspacing='0' cellpadding='0' class='listtable'>
			<tr class='header'><td width='1%'>&nbsp;</td><td>Member</td><td width='1%' nowrap>Is Chairman</td></tr>";
				
		foreach($this->native_session->get('boardmembers') AS $row)
		{
			$tableHTML .= "<tr class='listrow'>
						<td style='cursor: pointer;' onclick=\"confirmActionToLayer('".base_url()."interview/remove_board_member/userid/".$row['member_id']."', '', '', 'memberlist_div', 'Are you sure you want to remove this member')\"><img src='".base_url()."assets/images/reject_grey.png' /><input type='hidden' id='boardmembers_".$row['member_id']."' name='boardmembers[]' value='".$row['member_id']."' /></td></td>
						<td style='vertical-align:top;'>".html_entity_decode($row['member_name'], ENT_QUOTES)."</td>
						<td style='vertical-align:top;'><input type='radio' id='ischairman_".$row['member_id']."' name='ischairman' value='".$row['member_id']."' ".($row['is_chairman'] == 'Y'? "checked": "")."/> </td>
						</tr>";
		}
		$tableHTML .= "</table>";
	}
	else 
	{
		$tableHTML .= !empty($msg)? format_notice($this, $msg):"";
		$tableHTML .= "None selected";
	}

}








	
echo $tableHTML;
?>