<?php 
$jquery = "<script src='".base_url()."assets/js/jquery-2.1.1.min.js' type='text/javascript'></script>";
$javascript = "<script type='text/javascript' src='".base_url()."assets/js/tmis.js'></script>".get_AJAX_constructor(TRUE); 

$tableHTML = "<link rel='stylesheet' href='".base_url()."assets/css/tmis.css' type='text/css' media='screen' />
<link rel='stylesheet' href='".base_url()."assets/css/tmis.list.css' type='text/css' media='screen' />"; 





if(!empty($area) && $area == "verify_retirement")
{
	$additional = ($action == 'reject')? "WARNING: The retirement application will be completely removed from the system. ": "";
	
	$tableHTML .= "<table border='0' cellspacing='0' cellpadding='5' width='100%' />
	<tr><td width='99%'>";
	
	if($action == 'approve')
	{
		$tableHTML .= "<textarea id='reason_".$action."_".$id."' name='reason_".$action."_".$id."' class='yellowfield' style='width:100%' placeholder='Enter additional information to be recorded on the retirement notice.'></textarea>"; 
	}
	else
	{
		$tableHTML .="<textarea id='reason_".$action."_".$id."' name='reason_".$action."_".$id."' class='yellowfield' style='width:100%' placeholder='".$additional."Enter the reason you want to ".$list_type." this retirement. (Optional)'></textarea>";
	}
	
	$tableHTML .= "</td>
	<td width='1%' style='vertical-align:top;'><input id='confirm_".$action."_".$id."' name='confirm_".$action."_".$id."' type='button' class='greybtn confirmlistbtn' style='width:125px;' value='CONFIRM' /><div style='padding-top:5px;'><input id='cancel_".$action."_".$id."' name='cancel_".$action."_".$id."' type='button' class='greybtn cancellistbtn' style='width:125px;' value='CANCEL' /><input type='hidden' id='hidden_".$action."_".$id."' name='hidden_".$action."_".$id."' value='retirement' /></div></td></tr>
	</table>";
}












echo $tableHTML;
?>