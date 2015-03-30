<?php 
$jquery = "<script src='".base_url()."assets/js/jquery-2.1.1.min.js' type='text/javascript'></script>";
$javascript = "<script type='text/javascript' src='".base_url()."assets/js/tmis.js'></script>".get_AJAX_constructor(TRUE); 

$tableHTML = "<link rel='stylesheet' href='".base_url()."assets/css/tmis.css' type='text/css' media='screen' />
<link rel='stylesheet' href='".base_url()."assets/css/tmis.list.css' type='text/css' media='screen' />"; 





if(!empty($area) && $area == "report_specifications")
{
	
	if($type == 'teacherappointments')
	{
		$tableHTML .= "<div class='nextdiv'><input type='text' id='reportsubtype__".$type."' name='reportsubtype__".$type."' title='Select Specification' placeholder='Select Specification' class='textfield selectfield' onclick='setDatePicker()' style='width:300px;' value='' /></div>
		
		<div class='nextdiv'><input type='text' id='startdate' name='startdate' title='Start Date' placeholder='Start Date' class='textfield datefield clickactivated' value='".date('d-M-Y', strtotime('- 1 month'))."' /></div>
		<div class='nextdiv' style='min-width:20px;'>TO</div>
		<div class='nextdiv'><input type='text' id='enddate' name='enddate' title='End Date' placeholder='End Date' class='textfield datefield clickactivated' value='".date('d-M-Y', strtotime('now'))."' /></div>";
	}
	else
	{
		$tableHTML .= "<input type='text' id='reportsubtype__".$type."' name='reportsubtype__".$type."' title='Select Specification' placeholder='Select Specification' class='textfield selectfield' style='width:300px;' value='' />";
	}
	
	
	
}



# Show the custom reports generated here
if(!empty($area) && $area == "custom_report_view")
{
	if(!empty($list))
	{
		# Display some reports in special way because there are very many list items
		if(($posts['reporttype__reporttypes'] == 'Number of Registered Teachers' && !empty($posts['reportsubtype__registerednumbers']) && $posts['reportsubtype__registerednumbers'] == 'By School')
	
			|| ($posts['reporttype__reporttypes'] == 'Number of Registered Teachers' && !empty($posts['reportsubtype__registerednumbers']) && $posts['reportsubtype__registerednumbers'] == 'By District')
			
			|| ($posts['reporttype__reporttypes'] == 'Teacher Appointments' && !empty($posts['reportsubtype__teacherappointments']) && $posts['reportsubtype__teacherappointments'] == 'By School')
			
			|| ($posts['reporttype__reporttypes'] == 'Teacher Appointments' && !empty($posts['reportsubtype__teacherappointments']) && $posts['reportsubtype__teacherappointments'] == 'By District')
		)
		{
			foreach($list AS $row)
			{
				$tableHTML .= "<div class='inlineitem'>".$row['field']." (".add_commas($row['value'],0).")</div>";
			}
		}
		
		
		# Otherwise, user the generic list display
		else
		{
		
			$tableHTML .= "<table border='0' cellspacing='0' cellpadding='0' class='listtable' style=\"width:auto;\">";
			# 1. Get the fields to be used as the list header
			$tableHTML .= "<tr class='header'>";
			foreach($list[0] AS $key=>$value) $tableHTML .= "<td>".$key."</td>";
			$tableHTML .= "</tr>";
			
			# 2. Now show the list
			foreach($list AS $row)
			{
				$tableHTML .= "<tr class='listrow'>";
				foreach($row AS $value) $tableHTML .= "<td>".$value."</td>";
				$tableHTML .= "</tr>";
			}
			
			$tableHTML .= "</table>";
		}
		
	}
	else $tableHTML .= format_notice($this, "WARNING: There are no result items for this report.");
}





echo $tableHTML;
?>