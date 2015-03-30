<?php 
$jquery = "<script src='".base_url()."assets/js/jquery-2.1.1.min.js' type='text/javascript'></script>";
$javascript = "<script type='text/javascript' src='".base_url()."assets/js/tmis.js'></script>".get_AJAX_constructor(TRUE); 

$tableHTML = "<link rel='stylesheet' href='".base_url()."assets/css/tmis.css' type='text/css' media='screen' />
<link rel='stylesheet' href='".base_url()."assets/css/tmis.list.css' type='text/css' media='screen' />"; 





if(!empty($area) && $area == "verify_leave")
{
	$additional = ($action == 'reject_fromapprove')? "WARNING: The leave application will be completely removed from the system. ": "";
	
	$tableHTML .= "<table border='0' cellspacing='0' cellpadding='5' width='100%' />
	<tr><td width='99%'>";
	
	
	if($action == 'approve_tosend')
	{
		$tableHTML .= "<table border='0' cellspacing='0' cellpadding='0' width='100%' />
		<tr><td class='label'>Leave Type:</td><td><input type='text' id='_".$action."_".$id."leavetype__leavetypes' name='_".$action."_".$id."leavetype__leavetypes' placeholder='Select Leave Type' class='textfield yellowfield selectfield otherfield' style='width:97%' onclick='setDatePicker()'/></td>
		</tr>
		<tr><td class='label'>Minute Number:</td><td><input type='text' id='minutenumber_".$action."_".$id."' name='minutenumber_".$action."_".$id."' placeholder='In the format 188/2015(29)(i)' class='textfield yellowfield otherfield' style='width:97%'/></td>
		</tr>
		<tr><td class='label'>Duration:</td><td><div class='nextdiv'><input type='text' id='startdate_".$action."_".$id."' name='startdate_".$action."_".$id."' title='Start Date' placeholder='Start Date' class='textfield datefield clickactivated yellowfield otherfield' value='' style='width:97%'/></div><div class='nextdiv'><input type='text' id='enddate_".$action."_".$id."' name='enddate_".$action."_".$id."' title='End Date' placeholder='End Date' class='textfield datefield clickactivated yellowfield otherfield' value='' style='width:97%'/></div></td>
		</tr>
		<tr><td class='label top'>Details:</td><td><textarea id='reason_".$action."_".$id."' name='reason_".$action."_".$id."' class='yellowfield' style='width:100%' maxlength='150' placeholder='Enter the official reason for granting the leave.'></textarea></td>
		</tr>
		</table>"; 
	}
	else
	{
		$tableHTML .="<textarea id='reason_".$action."_".$id."' name='reason_".$action."_".$id."' class='yellowfield' style='width:100%' placeholder='".$additional."Enter the reason you want to ".$list_type." this leave. (Optional)'></textarea>";
	}
	
	$tableHTML .= "</td>
	<td width='1%' style='vertical-align:top;'><input id='confirm_".$action."_".$id."' name='confirm_".$action."_".$id."' type='button' class='greybtn confirmlistbtn' style='width:125px;' value='CONFIRM' /><div style='padding-top:5px;'><input id='cancel_".$action."_".$id."' name='cancel_".$action."_".$id."' type='button' class='greybtn cancellistbtn' style='width:125px;' value='CANCEL' /><input type='hidden' id='hidden_".$action."_".$id."' name='hidden_".$action."_".$id."' value='leave' /></div></td></tr>
	</table>";
}












echo $tableHTML;
?>