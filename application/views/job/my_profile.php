<?php $msg = empty($msg)? get_session_msg($this): $msg; 
$areas['cancel_retirement_application'] = "Cancel Retirement Application";
$areas['request_confirmation'] = "Request Confirmation";
$areas['cancel_transfer_application'] = "Cancel Transfer Application";
$areas['cancel_leave_application'] = "Cancel Leave Application";
?>
<!DOCTYPE html>
<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">
<link rel="icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">

<title><?php echo SITE_TITLE;?>: <?php echo (!empty($area) && !empty($areas[$area]))? $areas[$area]: 'My Job Profile';?></title>

<!-- Stylesheets -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/jquery-ui.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.mobile.css" media="(max-width:790px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.tablet.css" media="(min-width:791px) and (max-width: 900px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.desktop.css" media="(min-width:901px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.list.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.menu.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/trumbowyg.css"/>

<!-- Javascript -->
<?php echo minify_js('job-my_profile', array('jquery-2.1.1.min.js', 'jquery-ui.js', 'jquery.form.js', 'tmis.js',  'tmis.fileform.js', 'tmis.menu.js', 'tmis.responsive.js', 'trumbowyg.js'));?>
</head>

<body style="margin:0px;">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <?php $this->load->view("addons/secure_header");?>
  <tr>
    <td valign="top" colspan="2" style="padding-top:0px;padding-left:15px;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td id="menucontainer"><?php $this->load->view("addons/menu");?></td>
        <td class="bodyspace" style="padding-left:15px;padding-top:15px; vertical-align:top;">

<table width="100%" border="0" cellspacing="0" cellpadding="0">
      		<tr><td class="h1 grey"><?php echo (!empty($area) && !empty($areas[$area]))? $areas[$area]: 'My Job Profile';?></td></tr>
            <?php echo !empty($msg)?"<tr><td>".format_notice($this,$msg)."</td></tr>": "";?>
            <tr><td>
            
   
            <table border="0" cellspacing="0" cellpadding="10" <?php echo !$this->native_session->get('school')? " style='width:100%;' ":'';?>>
<?php
if(!empty($area) && $area == 'request_confirmation')
{
	if($this->native_session->get('startdate') && strtotime($this->native_session->get('startdate')) < strtotime('- 6 months'))
	{
		if($this->native_session->get('hasapplied') == 'Y')
		{
			echo "<tr><td colspan='2'>".format_notice($this, 'WARNING: You have already applied for confirmation. Please await a response from the approving authorities.')."</td></tr>";
		}
		else 
		{
			echo "<tr><td colspan='2'><div id='confirm_btn_div' class='textfield' style='width:100%;'><button type='button' name='confirm' id='confirm' onclick=\"updateFieldLayer('".base_url()."job/request_confirmation/submit/Y/id/".$this->native_session->get('postingid')."','','confirm_btn_div','confirm_results','')\" class='btn'>Request Job Confirmation</button>
			<br><span class='smalltext'>Clicking this button notifies the ministry about your request. This notifies the relevant authorities to review your teaching profile and history (already available on this system and offline). 
			<br>We will contact you when any action is taken on your request.</span>
			</div>
			<div id='confirm_results'></div></td></tr>";
		}
	}
	else
	{
		echo "<tr><td colspan='2'>".format_notice($this, 'WARNING: Sorry. You have not yet exceeded 6 months on the job to apply for confirmation.')."</td></tr>";
	}
}



else if(!empty($area) && $area == 'cancel_retirement_application')
{
	if(!empty($application))
	{
		echo "<tr><td colspan='2'><div id='cancel_retirement_btn_div' class='textfield' style='width:100%;'><button type='button' name='confirm' id='confirm' onclick=\"updateFieldLayer('".base_url()."retirement/cancel/submit/Y','','cancel_retirement_btn_div','confirm_retirement_results','')\" class='btn'>Cancel Retirement Application</button>
			<br><span class='smalltext'>Clicking this button notifies the ministry about your request. This also deletes your retirement application on record. 
			</div>
			<div id='confirm_retirement_results'></div></td></tr>";
	}
	else
	{
		echo "<tr><td colspan='2'>".format_notice($this, 'WARNING: You do not have an application that can be cancelled. <br>If you applied for retirement, the application can not be cancelled any more unless rejected.')."</td></tr>";
	}
}



else if(!empty($area) && $area == 'cancel_transfer_application')
{
	if(!empty($application))
	{
		echo "<tr><td colspan='2'><div id='cancel_transfer_btn_div' class='textfield' style='width:100%;'><button type='button' name='confirm' id='confirm' onclick=\"updateFieldLayer('".base_url()."transfer/cancel/submit/Y','','cancel_transfer_btn_div','confirm_transfer_results','')\" class='btn'>Cancel Transfer Application</button>
			<br><span class='smalltext'>Clicking this button notifies the ministry about your request. This also deletes your transfer application on record. 
			</div>
			<div id='confirm_transfer_results'></div></td></tr>";
	}
	else
	{
		echo "<tr><td colspan='2'>".format_notice($this, 'WARNING: You do not have an application that can be cancelled. <br>If you applied for a transfer, the application can not be cancelled any more unless rejected.')."</td></tr>";
	}
}



else if(!empty($area) && $area == 'cancel_leave_application')
{
	if(!empty($application))
	{
		echo "<tr><td colspan='2'><div id='cancel_leave_btn_div' class='textfield' style='width:100%;'><button type='button' name='confirm' id='confirm' onclick=\"updateFieldLayer('".base_url()."leave/cancel/submit/Y','','cancel_leave_btn_div','confirm_leave_results','')\" class='btn'>Cancel Leave Application</button>
			<br><span class='smalltext'>Clicking this button notifies the ministry about your request. This also deletes your leave application on record. 
			</div>
			<div id='confirm_leave_results'></div></td></tr>";
	}
	else
	{
		echo "<tr><td colspan='2' width='100%'>".format_notice($this, 'WARNING: You do not have an application that can be cancelled. <br>If you applied for a leave, the application can not be cancelled any more unless rejected.')."</td></tr>";
	}
}


if($this->native_session->get('school'))
{?>

  <tr>
    <td class="label">School:</td>
    <td class='value' style="padding-right:42px;"><?php echo $this->native_session->get('school');?></td>
  </tr>
  <tr>
    <td class="label">Job Name:</td>
    <td class='value' style="padding-right:42px;"><?php echo $this->native_session->get('jobname');?></td>
  </tr>
  <tr>
    <td class="label top">Job Description:</td>
    <td class='value'><?php echo html_entity_decode($this->native_session->get('jobdescription'), ENT_QUOTES);?></td>
  </tr>
  <tr>
    <td class="label">Start Date:</td>
    <td class='value'><?php echo date('d-M-Y', strtotime($this->native_session->get('startdate')));?></td>
  </tr>
<?php
}
else 
{
	echo "<tr><td colspan='2'".(!empty($area)? " style='padding-top:42px;'" : "")." >".format_notice($this, "WARNING: You do not have a current job posting. <br><br>You can <a href=\"javascript:updateFieldLayer('".base_url()."job/submit_current_job','','','currentjobdiv','')\">report your current job</a> for approval or <a href='".base_url()."job/lists/action/apply'>search vacancies and apply</a> for a job.")."</td></tr>
	
	<tr><td><div id='currentjobdiv'></div></td></tr>";
}
?>            </table>
            </td></tr>
        </table>
        
            
        </td>
      </tr>
     </table>
    </td>
  </tr>
  <?php $this->load->view("addons/secure_footer");?>
</table>
</body>
</html>