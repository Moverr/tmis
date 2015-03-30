<?php 
$jquery = "<script src='".base_url()."assets/js/jquery-2.1.1.min.js' type='text/javascript'></script>";
$javascript = "<script type='text/javascript' src='".base_url()."assets/js/tmis.js'></script>".get_AJAX_constructor(TRUE); 

$tableHTML = "<link rel='stylesheet' href='".base_url()."assets/css/tmis.css' type='text/css' media='screen' />
<link rel='stylesheet' href='".base_url()."assets/css/tmis.list.css' type='text/css' media='screen' />"; 





if(!empty($area) && $area == "submit_current_job")
{
	if(!empty($msg))
	{
		$tableHTML .= format_notice($this, $msg);
	}
	else
	{
		$tableHTML .= $jquery.$javascript.
	"<script type='text/javascript' src='".base_url()."assets/js/tmis.form.js'></script>".
	"<table border='0' cellspacing='0' cellpadding='5' width='100%' class='microform'/>
	<tr><td width='1%' class='label'>School</td><td width='1%'><input type='text' id='schoolname__schools' name='schoolname__schools' title='Your school name' placeholder='Search or select school' class='textfield selectfield searchable' value='' onclick='setDatePicker()'/><input type='hidden' id='schoolid' name='schoolid' value='' /></td></tr>
	
	<tr><td width='1%' class='label'>Job Name</td><td width='1%'><input type='text' id='jobname__jobroles' name='jobname__jobroles' title='Your job name' placeholder='Select a Job Name' class='textfield selectfield' value=''/></td></tr>
	
	<tr><td width='1%' class='label'>Date Started</td><td width='1%'><input type='text' id='startdate' name='startdate' title='Start Date' class='textfield datefield clickactivated' value='' /></td></tr>
	
	<tr><td width='1%' class='label'>&nbsp;</td><td width='1%'><button type='button' id='submitjob' name='submitjob' class='btn submitmicrobtn' style='width:125px;' value='submitjob'>SUBMIT</button>
	<input type='hidden' id='action' name='action' value='".base_url()."job/submit_current_job' />
	<input type='hidden' id='resultsdiv' name='resultsdiv' value='currentjobdiv' />
	</td></tr>
	</table>";
	}
}












echo $tableHTML;
?>