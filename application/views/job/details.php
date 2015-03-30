<!DOCTYPE html>
<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">
<link rel="icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">

<title><?php echo SITE_TITLE;?>: Job Details</title>

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
<?php echo minify_js('job-details', array('jquery-2.1.1.min.js', 'jquery-ui.js', 'jquery.form.js', 'tmis.js', 'tmis.fileform.js', 'tmis.menu.js', 'tmis.responsive.js', 'trumbowyg.js'));?>
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
      		<tr><td class="h1 grey">Job Details</td></tr>
            <?php echo !empty($msg)?"<tr><td>".format_notice($this,$msg)."</td></tr>": "";?>
            <tr><td>
            
   
            <table border="0" cellspacing="0" cellpadding="10">
<?php
if(!empty($area) && $area == 'confirm_job_option')
{
	if($this->native_session->get('__teacher_status') && $this->native_session->get('__teacher_status') == 'active')
	{
		echo "<tr><td colspan='2'><div id='confirm_btn_div' class='textfield' style='width:100%;'><button type='button' name='confirm' id='confirm' onclick=\"updateFieldLayer('".base_url()."job/apply/action/submit/id/".$id."','','confirm_btn_div','confirm_results','')\" class='btn'>Confirm Application</button>
	<br><span class='smalltext'>Clicking this button notifies the ministry about your interest. This notifies the relevant authorities to review your teaching profile and history (already available on this system and offline). 
	<br>We will contact you in case you qualify for the next stage.</span>
	</div>
	<div id='confirm_results'></div></td></tr>";
	}
	else
	{
		echo "<tr><td colspan='2'>".format_notice($this, 'WARNING: Your teacher status has not yet been approved to apply for jobs.')."</td></tr>";
	}
}
?>

  <tr>
    <td class="label">Institution:</td>
    <td class='value' style="padding-right:42px;"><?php echo $this->native_session->get('institution__institutions');?></td>
  </tr>
  <tr>
    <td class="label">Role:</td>
    <td class='value' style="padding-right:42px;"><?php echo $this->native_session->get('role__jobroles');?></td>
  </tr>
  <tr>
    <td class="label">Headline:</td>
    <td class='value'><?php echo $this->native_session->get('headline');?></td>
  </tr>
  <tr>
    <td class="label top">Summary:</td>
    <td class='value'><?php echo $this->native_session->get('summary');?></td>
  </tr>
  <tr>
    <td class="label top">Details:</td>
    <td class='value' style="padding-top:0px;"><?php echo html_entity_decode($this->native_session->get('details'), ENT_QUOTES);?></td>
  </tr>
  <tr>
    <td class="label">Publish Duration:</td>
    <td><div class="nextdiv value"><?php echo $this->native_session->get('publishstart');?></div> to <div class="nextdiv value"><?php echo $this->native_session->get('publishend');?></div></td>
  </tr>
            </table>
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