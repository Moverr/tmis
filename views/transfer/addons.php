<?php 
$jquery = "<script src='".base_url()."assets/js/jquery-2.1.1.min.js' type='text/javascript'></script>";
$javascript = "<script type='text/javascript' src='".base_url()."assets/js/tmis.js'></script>".get_AJAX_constructor(TRUE); 

$tableHTML = "<link rel='stylesheet' href='".base_url()."assets/css/tmis.css' type='text/css' media='screen' />
<link rel='stylesheet' href='".base_url()."assets/css/tmis.list.css' type='text/css' media='screen' />"; 





if(!empty($area) && $area == "verify_transfer")
{
	$additional = ($action == 'reject_frominstitutionapprove')? "WARNING: The transfer application will be completely removed from the system. ": "";
	
	$tableHTML .= "<table border='0' cellspacing='0' cellpadding='5' width='100%' />
	<tr><td width='99%'>";
	
	
	if($action == 'approve_tocountyapprove')
	{
		$tableHTML .= "<table border='0' cellspacing='0' cellpadding='0' width='100%' />
		<tr><td class='label'>Minute Number:</td><td><input type='text' id='minutenumber_".$action."_".$id."' name='minutenumber_".$action."_".$id."' placeholder='In the format 188/2015(29)(i)' class='textfield yellowfield otherfield' style='width:97%'/></td>
		</tr>
		</table>"; 
	}
	else if($action == 'approve_topca')
	{
		$tableHTML .= "<table border='0' cellspacing='0' cellpadding='0' width='100%' />
		<tr><td class='label top' style='width:1%;' nowrap>Subjects:</td><td><textarea id='subjectlist_".$action."_".$id."' name='subjectlist_".$action."_".$id."' style='width:100%' placeholder='Enter the subjects the teacher is going to teach (e.g., Mathematics, Social Studies, Religious Studies)' class='textfield yellowfield otherfield' style='width:97%'></textarea></td>
		</tr>
		</table>"; 
	}
	else
	{
		$tableHTML .="<textarea id='reason_".$action."_".$id."' name='reason_".$action."_".$id."' class='yellowfield' style='width:100%' placeholder='".$additional."Enter the reason you want to ".$list_type." this transfer. (Optional)'></textarea>";
	}
	
	$tableHTML .= "</td>
	<td width='1%'><input id='confirm_".$action."_".$id."' name='confirm_".$action."_".$id."' type='button' class='greybtn confirmlistbtn' style='width:125px;' value='CONFIRM' /><div style='padding-top:5px;'><input id='cancel_".$action."_".$id."' name='cancel_".$action."_".$id."' type='button' class='greybtn cancellistbtn' style='width:125px;' value='CANCEL' /><input type='hidden' id='hidden_".$action."_".$id."' name='hidden_".$action."_".$id."' value='transfer' /></div></td></tr>
	</table>";
}












echo $tableHTML;
?>