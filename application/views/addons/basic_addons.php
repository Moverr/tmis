<?php 
$jquery = "<script src='".base_url()."assets/js/jquery-2.1.1.min.js' type='text/javascript'></script>";
$javascript = "<script type='text/javascript' src='".base_url()."assets/js/tmis.js'></script>".get_AJAX_constructor(TRUE); 

$tableHTML = "<link rel='stylesheet' href='".base_url()."assets/css/tmis.css' type='text/css' media='screen' />
<link rel='stylesheet' href='".base_url()."assets/css/tmis.list.css' type='text/css' media='screen' />"; 

if(!empty($area) && $area == "show_bigger_image")
{
	$tableHTML .= "<table width='530' height='398' border='0' cellspacing='0' cellpadding='0'><tr><td><img src='".$url."' border='0' /></td></tr></table>"; 
}





else if(!empty($area) && $area == "basic_msg" && !empty($msg)) 
{
	$tableHTML .= format_notice($this, $msg);
}





else if(!empty($area) && $area == "address_field_form")
{
	$tableHTML .= "<table border='0' cellspacing='0' cellpadding='5' id='".$field_id."__form' class='simpleform'>";
	
  	$tableHTML .= !empty($physical_only)? "":"<tr><td>
  			<div class='nextdiv'><input type='radio' name='".$field_id."__addresstype' id='".$field_id."__addresstype_physical' value='physical' ".((!$this->native_session->get($field_id.'__addresstype') || ($this->native_session->get($field_id.'__addresstype') && $this->native_session->get($field_id.'__addresstype')=='physical'))? 'checked': '')."><label for='".$field_id."__addresstype_physical'>Physical</label></div>
  			<div class='nextdiv'><input type='radio' name='".$field_id."__addresstype' id='".$field_id."__addresstype_postal' value='postal' ".(($this->native_session->get($field_id.'__addresstype') && $this->native_session->get($field_id.'__addresstype')=='postal')? 'checked': '')."><label for='".$field_id."__addresstype_postal'>Postal</label></div>
		</td></tr>";
	   
	$tableHTML .= "<tr><td><input type='text' id='".$field_id."__addressline' name='".$field_id."__addressline' class='textfield' placeholder='Address (e.g. Plot 23 Kira Rd)' value='".($this->native_session->get($field_id.'__addressline')? $this->native_session->get($field_id.'__addressline'): '')."' maxlength='200'/></td></tr>
	
  <tr><td><input type='text' id='".$field_id."__county' name='".$field_id."__county' class='textfield selectfield searchable optional' placeholder='County (Optional)' data-val='-district-".$field_id."' value='".($this->native_session->get($field_id.'__county')? $this->native_session->get($field_id.'__county'): '')."' maxlength='200'/>
  <input type='hidden' id='-district-".$field_id."' name='-district-".$field_id."' value='".$field_id."__district' /></td></tr>
  
  <tr><td><input type='text' id='".$field_id."__district' name='".$field_id."__district' class='textfield selectfield editable' placeholder='District or State' value='".($this->native_session->get($field_id.'__district')? $this->native_session->get($field_id.'__district'): '')."' maxlength='200'/>".
  ($this->native_session->get($field_id.'__district__hidden')? "<input type='hidden' id='".$field_id."__district__hidden' name='".$field_id."__district__hidden' value='".$this->native_session->get($field_id.'__district__hidden')."' /><div id='".$field_id."__district__div' class='selectfielddiv'></div>": "")
  ."</td></tr>
  
  <tr><td><input type='text' id='".$field_id."__country' name='".$field_id."__country' class='textfield selectfield' placeholder='Country' value='".($this->native_session->get($field_id.'__country')? $this->native_session->get($field_id.'__country'): '')."' maxlength='200'/>".
  ($this->native_session->get($field_id.'__country__hidden')? "<input type='hidden' id='".$field_id."__country__hidden' name='".$field_id."__country__hidden' value='".$this->native_session->get($field_id.'__country__hidden')."' /><div id='".$field_id."__country__div' class='selectfielddiv'></div>": "")
  ."</td></tr>
  
  <tr><td style='text-align:right'><button type='button' id='".$field_id."__btn' name='".$field_id."__btn' ".($this->native_session->get($field_id.'__addressline')? "class='submitbtn btn' onclick=\"postFormFromLayer('".$field_id."__form')\"": "class='submitbtn greybtn'").">SAVE</button><div id='".$field_id."__resultsdiv' style='display:none;'></div><input type='hidden' id='".$field_id."__response_fields' name='".$field_id."__response_fields' value='".$field_id."__addressline' /><input type='hidden' id='".$field_id."__type' name='".$field_id."__type' value='address' /></td></tr>
  </table>";
}





else if(!empty($area) && $area == "dropdown_list")
{
	$tableHTML .= !empty($list)? $list: "";
}





else if(!empty($area) && $area == "education_form")
{
	$tableHTML .= "<table border='0' cellspacing='0' cellpadding='10' class='microform'>
  <tr>
    <td class='label' style='width:150px;'>Institution:</td>
    <td><div class='nextdiv'><input type='text' id='institutionname' name='institutionname' title='Insitution Name' class='textfield' value='".(!empty($details['institutionname'])? $details['institutionname']: '')."'/></div>
      <div class='nextdiv'><input type='text' id='institution__institutiontype' name='institution__institutiontype' class='textfield selectfield' value='".(!empty($details['institution__institutiontype'])? $details['institution__institutiontype']: '')."' placeholder='Insitution Type' /></div></td>
  </tr>
  <tr>
    <td class='label'>Period:</td>
    <td><div class='nextdiv'>
    <table border='0' cellspacing='0' cellpadding='0'>
    <tr><td class='value' style='padding-right:9px;'>From</td>
    <td><input type='text' id='from__month' name='from__month' placeholder='Month' class='textfield selectfield' style='width:100px;' value='".(!empty($details['from__month'])? $details['from__month']: date('F'))."'/></td>
    <td><input type='text' id='from__pastyear' name='from__pastyear' placeholder='Year' class='textfield selectfield' style='width:55px;' value='".(!empty($details['from__pastyear'])? $details['from__pastyear']: date('Y'))."'/></td>
    </tr>
    </table>
    </div>
    <div class='nextdiv'>
    <table border='0' cellspacing='0' cellpadding='0'>
    <tr><td class='value' style='padding-right:30px;'>To</td>
    <td><input type='text' id='to__month' name='to__month' placeholder='Month' class='textfield selectfield' style='width:100px;' value='".(!empty($details['to__month'])? $details['to__month']: date('F'))."'/></td>
    <td><input type='text' id='to__pastyear' name='to__pastyear' placeholder='Year' class='textfield selectfield' style='width:55px;' value='".(!empty($details['to__pastyear'])? $details['to__pastyear']: date('Y'))."'/></td>
    </tr>
    </table>
    </div></td>
  </tr>
  <tr>
    <td class='label'>Certificate Obtained:</td>
    <td><div class='nextdiv'><input type='text' id='certificatename' name='certificatename' placeholder='e.g., MSc. Mathematics' title='Certificate Name' class='textfield' value='".(!empty($details['certificatename'])? $details['certificatename']: '')."'/></div>
      <div class='nextdiv'><input type='checkbox' id='highestcertificate' name='highestcertificate' value='Y' ".(!empty($details['highestcertificate'])? 'checked': '')."/>
      <label for='highestcertificate'>This is my highest certificate.</label></div></td>
  </tr>
  <tr>
    <td class='label'>Certificate Number:</td>
    <td><input type='text' id='certificatenumber' name='certificatenumber' title='Certificate Number' class='textfield' value='".(!empty($details['certificatenumber'])? $details['certificatenumber']: '')."'/></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><button type='button' name='saveeducation' data-val='".(!empty($details['item_id']) && !empty($type)? $type."_id": "")."' id='saveeducation' class='greybtn submitmicrobtn'>ADD</button><input type='hidden' id='action' name='action' value='".base_url().(($this->native_session->get('is_admin_adding_teacher') || $this->native_session->get('is_teacher_updating'))? "teacher": "register/step_three/action")."/add_education' /><input type='hidden' id='resultsdiv' name='resultsdiv' value='institution_list' />".(!empty($details['item_id']) && !empty($type)? "<input type='hidden' name='".$type."_id' id='".$type."_id' value='".$details['item_id']."' />": "")."</td>
  </tr>
        </table>";
	
	
}








else if(!empty($area) && $area == "education_list")
{

	$tableHTML .= !empty($response['msg'])? format_notice($this, $response['msg']): "";
	
	if($this->native_session->get('education_list'))
	{
		$tableHTML .= "<table border='0' cellspacing='0' cellpadding='0' class='resultslisttable".(!empty($mode) && $mode=='preview'? " preview":" editable")."'>
			<tr><td colspan='3'>Education List</td></tr>";
			
		foreach($this->native_session->get('education_list') AS $row) 
		{
			$tableHTML .= "<tr id='".$row['education_id']."'><td class='edit'>&nbsp;</td>
			<td><div class='nextdiv'><span class='label'>".$row['institution__institutiontype'].": ".$row['institutionname']."</span><br>".$row['certificatename']."</div>
				<div class='nextdiv'>(".$row['from__month']." ".$row['from__pastyear']." - ".$row['to__month']." ".$row['to__pastyear'].")<br>Certificate # ".$row['certificatenumber']."</div>
				<div class='nextdiv'>".(!empty($row['highestcertificate']) && $row['highestcertificate'] == 'Y'? "-- highest --": "&nbsp;")."</div></td>
			<td class='delete'>&nbsp;</td>
			</tr>";
		}
		$tableHTML .= "</table>";
	}
}





else if(!empty($area) && $area == "subject_form")
{
	$tableHTML .= "<table border='0' cellspacing='0' cellpadding='10' class='microform'>
  <tr>
    <td class='label' style='width:150px;'>Subject Name:</td>
    <td><div class='nextdiv'><input type='text' id='subjectname' name='subjectname' title='Subject Name' class='textfield' value='".(!empty($details['subjectname'])? $details['subjectname']: '')."'/></div>
      <div class='nextdiv'><input type='text' id='subject__subjecttype' name='subject__subjecttype' class='textfield selectfield' value='".(!empty($details['subject__subjecttype'])? $details['subject__subjecttype']: '')."' placeholder='Subject Type' /></div></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><button type='button' name='savesubject' id='savesubject' data-val='".(!empty($details['item_id']) && !empty($type)? $type."_id": "")."' class='greybtn submitmicrobtn'>ADD</button><input type='hidden' id='action' name='action' value='".base_url().(($this->native_session->get('is_admin_adding_teacher') || $this->native_session->get('is_teacher_updating'))? "teacher": "register/step_three/action")."/add_subject' /><input type='hidden' id='resultsdiv' name='resultsdiv' value='subject_list' />".(!empty($details['item_id']) && !empty($type)? "<input type='hidden' name='".$type."_id' id='".$type."_id' value='".$details['item_id']."' />": "")."</td>
  </tr>
  </table>";
}






else if(!empty($area) && $area == "subject_list")
{
	$tableHTML .= !empty($response['msg'])? format_notice($this, $response['msg']): "";
	
	if($this->native_session->get('subject_list'))
	{
		$tableHTML .= "<table border='0' cellspacing='0' cellpadding='0' class='resultslisttable".(!empty($mode) && $mode=='preview'? " preview":" editable")."'>
			<tr><td colspan='3'>Subject List</td></tr>";
			
		foreach($this->native_session->get('subject_list') AS $row) 
		{
			$tableHTML .= "<tr id='".$row['subject_id']."'><td class='edit'>&nbsp;</td>
			<td><div class='nextdiv'>".$row['subjectname']."</div>
				<div class='nextdiv'>".(!empty($row['subject__subjecttype']) && $row['subject__subjecttype'] != 'Other'? "-- ".strtolower($row['subject__subjecttype'])." --": "&nbsp;")."</div></td>
			<td class='delete'>&nbsp;</td>
			</tr>";
		}
		$tableHTML .= "</table>";
	}
}






else if(!empty($area) && $area == "document_form")
{
	$isEditing = (!empty($details['documentname']) && $this->native_session->get('edit_step_3_document'))? true: false;
	$tableHTML .= "<table border='0' cellspacing='0' cellpadding='10' class='microform'>
  <tr>
    <td class='label top' style='width:150px;'>Document:</td>
    <td style='vertical-align:top;'><div class='nextdiv' style='vertical-align:top;'><input type='text' id='documentname' name='documentname' title='Document Name' placeholder='Name' class='textfield' value='".(!empty($details['documentname'])? $details['documentname']: '')."'/></div>
      <div class='nextdiv hideonedit' ".($isEditing? "style='display:none;'": "")."><input type='text' id='documenturl' name='documenturl' title='Document URL' data-size='500' data-val='jpg,jpeg,png,tiff,pdf' class='textfield filefield".($isEditing? " optional": "")."' placeholder='Document File' value=''/>
    <div class='smalltext'>Allowed Formats: PDF, TIFF, JPG, JPEG, PNG  &nbsp;&nbsp; Max Size: 500kB</div></div></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><button type='button' name='savedocument' data-val='".(!empty($details['item_id']) && !empty($type)? $type."_id": "")."' id='savedocument' class='greybtn submitmicrobtn'>ADD</button><input type='hidden' id='action' name='action' value='".base_url().(($this->native_session->get('is_admin_adding_teacher') || $this->native_session->get('is_teacher_updating'))? "teacher": "register/step_three/action")."/add_document' /><input type='hidden' id='resultsdiv' name='resultsdiv' value='document_list' />".(!empty($details['item_id']) && !empty($type)? "<input type='hidden' name='".$type."_id' id='".$type."_id' value='".$details['item_id']."' />": "")."</td>
  </tr>
  </table>";
}






else if(!empty($area) && $area == "document_list")
{
	$tableHTML .= !empty($response['msg'])? format_notice($this, $response['msg']): "";
	
	if($this->native_session->get('document_list'))
	{
		$tableHTML .= "<table border='0' cellspacing='0' cellpadding='0' class='resultslisttable".(!empty($mode) && $mode=='preview'? " preview":" editable")."'>
			<tr><td colspan='3'>Document List</td></tr>";
			
		foreach($this->native_session->get('document_list') AS $row) 
		{
			$isRemovable = (empty($row['is_removable']) || (!empty($row['is_removable']) && $row['is_removable']=='Y'))? true: false;
			
			$tableHTML .= "<tr id='".$row['document_id']."'><td ".($isRemovable? " class='edit'" :"").">&nbsp;</td>
			<td><a href='".base_url()."page/download/folder/documents/file/".$row['documenturl']."'>".$row['documentname']."</a></td>
			<td ".($isRemovable? " class='delete'" :"").">&nbsp;</td>
			</tr>";
		}
		$tableHTML .= "</table>";
	}
}







else if(!empty($area) && in_array($area, array("verify_vacancy", "verify_user", "verify_permission", "verify_school", "verify_census")))
{
	$item = str_replace('verify_', '', $area);
	$additional = in_array($area, array("verify_user", "verify_permission", "verify_school"))? "WARNING: The ".$item." will be completely removed from the system. ": "";
	
	$tableHTML .= "<table border='0' cellspacing='0' cellpadding='5' width='100%' />
	<tr><td width='99%'><textarea id='reason_".$action."_".$id."' name='reason_".$action."_".$id."' class='yellowfield' style='width:100%' placeholder='".$additional."Enter the reason you want to ".$list_type." this ".$item.". (Optional)'></textarea></td>
	<td width='1%'><input id='confirm_".$action."_".$id."' name='confirm_".$action."_".$id."' type='button' class='greybtn confirmlistbtn' style='width:125px;' value='CONFIRM' /><div style='padding-top:5px;'><input id='cancel_".$action."_".$id."' name='cancel_".$action."_".$id."' type='button' class='greybtn cancellistbtn' style='width:125px;' value='CANCEL' /><input type='hidden' id='hidden_".$action."_".$id."' name='hidden_".$action."_".$id."' value='".$item."' /></div></td></tr>
	</table>";
}





else if(!empty($area) && in_array($area, array("verify_interview")))
{
	$item = str_replace('verify_', '', $area);
	
	$tableHTML .= "<table border='0' cellspacing='0' cellpadding='5' width='100%' />
	<tr><td width='99%'><textarea id='reason_".$action."_".$id."' name='reason_".$action."_".$id."' class='yellowfield' style='width:100%' placeholder='WARNING: The ".$item." will be completely removed from the system. Enter the reason you want to cancel this ".$item.". (Optional)'></textarea></td>
	<td width='1%'><input id='confirm_".$action."_".$id."' name='confirm_".$action."_".$id."' type='button' class='greybtn confirmlistbtn' style='width:125px;' value='CONFIRM' /><div style='padding-top:5px;'><input id='cancel_".$action."_".$id."' name='cancel_".$action."_".$id."' type='button' class='greybtn cancellistbtn' style='width:125px;' value='CANCEL' /><input type='hidden' id='hidden_".$action."_".$id."' name='hidden_".$action."_".$id."' value='".$item."' /></div></td></tr>
	</table>";
}




else if(!empty($area) && $area == 'verify_teacher')
{
	$item = str_replace('verify_', '', $area);
	
	$tableHTML .= "<table border='0' cellspacing='0' cellpadding='5' width='100%' />
	<tr><td width='99%'>";
	
	
	if($action == 'approve_toactive')
	{
		$tableHTML .= "<table border='0' cellspacing='0' cellpadding='0' width='100%' />
		<tr><td class='label'>Grade:</td><td><input type='text' id='_".$action."_".$id."grade__grades' name='_".$action."_".$id."grade__grades' placeholder='Select Grade' class='textfield selectfield yellowfield otherfield' style='width:97%' onclick='setDatePicker()'/></td>
			<td class='label'>Effective Date:</td><td><input type='text' id='effectivedate_".$action."_".$id."' name='effectivedate_".$action."_".$id."' title='Effective Date' class='textfield datefield clickactivated yellowfield otherfield' value='' style='width:97%'/></td>
		</tr>
		<tr>
			<td class='label top'>Comment:</td><td colspan='3'><textarea id='reason_".$action."_".$id."' name='reason_".$action."_".$id."' class='yellowfield' style='width:100%' maxlength='200' placeholder='This comment will be sent to all those parties involved in the approval of this teacher. (Optional)'></textarea></td>
		</tr>
		</table>"; 
	}
	else
	{
		$tableHTML .="<textarea id='reason_".$action."_".$id."' name='reason_".$action."_".$id."' class='yellowfield' style='width:100%' placeholder='Enter the reason you want to ".$list_type." this ".$item.". (Optional)'></textarea>";
	}
	
	$tableHTML .= "</td>
	<td width='1%' style='vertical-align:top; padding-top:10px;'><input id='confirm_".$action."_".$id."' name='confirm_".$action."_".$id."' type='button' class='greybtn confirmlistbtn' style='width:125px;' value='CONFIRM' /><div style='padding-top:5px;'><input id='cancel_".$action."_".$id."' name='cancel_".$action."_".$id."' type='button' class='greybtn cancellistbtn' style='width:125px;' value='CANCEL' /><input type='hidden' id='hidden_".$action."_".$id."' name='hidden_".$action."_".$id."' value='".$item."' /></div></td></tr>
	</table>";
}




else if(!empty($area) && $area == 'permission_list')
{
	$tableHTML .= "<link rel='stylesheet' href='".base_url()."assets/css/tmis.list.css' type='text/css' media='screen' />
	<table border='0' cellspacing='0' cellpadding='5' width='100%' class='listtable' />
	<tr class='header'><td>Category</td><td>Permission</td></tr>";
	foreach($list AS $row)
	{
		$tableHTML .= "<tr class='listrow'><td>".ucwords(str_replace('_', ' ', $row['category']))."</td><td>".$row['display']."</td></tr>";
	}
	$tableHTML .= "</table>";
}





else if(!empty($area) && $area == 'message_sending_results')
{
	if($result['boolean'])
	{
		$msg = !empty($result['msg'])? $result['msg']: 'The message has been sent';
		$this->native_session->set('msg', $msg);
		$tableHTML = "<script>document.location.href='".base_url()."message/inbox';</script>";
	}
	else 
	{
		$tableHTML = !empty($result['msg'])? $result['msg']: 'error';
	}
}





else if(!empty($area) && $area == 'message_details')
{
	if(!empty($message))
	{
		$tableHTML .= "<table border='0' cellspacing='0' cellpadding='5' width='100%' class='listtable' />
			<tr><td colspan='2' class='h2' style='font-weight:bold;'>".$message['subject']."</td></tr>
			<tr><td class='label'>Date:</td><td class='value'>".date('d-M-Y h:ia T', strtotime($message['date_sent']))."</td></tr>
			<tr><td class='label'>From:</td><td class='value'>".$message['sender_name']."</td></tr>
			<tr><td class='label'>To:</td><td class='value'>".$message['recipient_name']."</td></tr>
			<tr><td class='label top'>Details:</td><td>".html_entity_decode($message['details'],ENT_QUOTES)."</td></tr>".
			(!empty($message['attachment'])? "<tr><td class='label'>Attachment:</td><td class='value'><a href='".base_url()."page/download/folder/documents/file/".$message['attachment']."'>".$message['attachment']."</a></td></tr>": '')."</table>";
			
	} 
	else 
	{
		$tableHTML .= format_notice($this,$msg);
	}
}








else if(!empty($area) && $area == "census_sub_lists")
{
	if(!empty($list))
	{
		$tableHTML .= "<table border='0' cellspacing='0' cellpadding='0' class='listtable'>
			<tr class='header'><td>Code</td><td>Name</td>".($type=='training'? "<td>Type</td>": '')."</tr>";
			
		foreach($list AS $row) 
		{
			$tableHTML .= "<tr class='listrow'>
			<td>".$row['code']."</td>
			<td>".$row[$type]."</td>".
			($type=='training'? "<td>".$row['type']."</td>": '')
			."</tr>";
		}
		$tableHTML .= "</table>";
	}
	else if(empty($msg))
	{
		$tableHTML .= format_notice($this, 'WARNING: There is no '.$type.' for this census.');
	}
	else
	{
		$tableHTML .= format_notice($this, $msg);
	}
}








else if(!empty($area) && $area == "choose_job_option")
{
	$tableHTML .= "<table border='0' cellspacing='0' cellpadding='5' width='100%'>
	<tr><td colspan='2' class='h1'>Are you registered yet?</td></tr>
	<tr><td style='text-align:left;'><button type='button' name='proceed' id='proceed' data-rel='".base_url()."account/login' class='greybtn frompop'>Yes. Login and Apply</button></td><td style='text-align:right;'><button type='button' name='register' id='register' data-rel='".base_url()."register/step_one' class='btn frompop'>No. Register and Apply</button></td></tr>
	</table>";
	
}












echo $tableHTML;
?>