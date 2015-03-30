<?php 
$jquery = "<script src='".base_url()."assets/js/jquery-2.1.1.min.js' type='text/javascript'></script>";
$javascript = "<script type='text/javascript' src='".base_url()."assets/js/tmis.js'></script>".get_AJAX_constructor(TRUE); 

$tableHTML = "<link rel='stylesheet' href='".base_url()."assets/css/tmis.css' type='text/css' media='screen' />
<link rel='stylesheet' href='".base_url()."assets/css/tmis.list.css' type='text/css' media='screen' />"; 





if(!empty($area) && $area == "verify_confirmation")
{
	$item = $action == 'reject'? 'posting': 'confirmation';
	$additional = $action == 'reject'? "WARNING: The posting will be completely removed from the system. ": "";
	
	$tableHTML .= "<table border='0' cellspacing='0' cellpadding='5' width='100%' />
	<tr><td width='99%'>";
	
	
	if($action == 'approve_toapprove')
	{
		$tableHTML .= "<table border='0' cellspacing='0' cellpadding='0' width='100%' />
		<tr><td class='label'>Minute Number:</td><td><input type='text' id='minutenumber_".$action."_".$id."' name='minutenumber_".$action."_".$id."' placeholder='In the format 188/2015(29)(i)' class='textfield yellowfield otherfield' style='width:97%'/></td>
		</tr>
		</table>"; 
	}
	else
	{
		$tableHTML .="<textarea id='reason_".$action."_".$id."' name='reason_".$action."_".$id."' class='yellowfield' style='width:100%' placeholder='".$additional."Enter the reason you want to ".$list_type." this ".$item.". (Optional)'></textarea>";
	}
	
	$tableHTML .= "</td>
	<td width='1%'><input id='confirm_".$action."_".$id."' name='confirm_".$action."_".$id."' type='button' class='greybtn confirmlistbtn' style='width:125px;' value='CONFIRM' /><div style='padding-top:5px;'><input id='cancel_".$action."_".$id."' name='cancel_".$action."_".$id."' type='button' class='greybtn cancellistbtn' style='width:125px;' value='CANCEL' /><input type='hidden' id='hidden_".$action."_".$id."' name='hidden_".$action."_".$id."' value='confirmation' /></div></td></tr>
	</table>";
}












echo $tableHTML;
?>