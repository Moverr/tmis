<!DOCTYPE html>
<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">
<link rel="icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">

<title><?php echo SITE_TITLE;?>: Promotion Application</title>

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
<?php echo minify_js('job-promotion_application', array('jquery-2.1.1.min.js', 'jquery-ui.js', 'jquery.form.js', 'tmis.js',  'tmis.fileform.js', 'tmis.menu.js', 'tmis.responsive.js', 'trumbowyg.js'));?>
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
      		<tr><td class="h1 grey">Promotion Application</td></tr>
            <?php echo !empty($msg)?"<tr><td>".format_notice($this,$msg)."</td></tr>": "";?>
            <tr><td>
            
   <form id="promotion_application" method="post" autocomplete="off" action="<?php echo base_url().'job/apply_for_promotion';?>" class='simplevalidator'>
            <table border="0" cellspacing="0" cellpadding="10" <?php echo !$this->native_session->get('jobname')? " style='width:100%;' ": "";?>>
<?php 
if($this->native_session->get('jobname'))
{
?>
  <tr>
    <td class="label">Your Current Job:</td>
    <td class='value'><?php echo $this->native_session->get('jobname');?></td>
  </tr>
  <tr>
    <td class="label">Select Job:</td>
    <td><input type="text" id="job__schooljobs" name="job__schooljobs" title="Select Job" placeholder="Select Desired Job" class="textfield selectfield searchable" value="<?php echo $this->native_session->get('job__schooljobs');?>" style="width:85%;"/><input type='hidden' id='vacancyid' name='vacancyid' value='<?php echo $this->native_session->get('vacancyid');?>' /></td>
  </tr>
  <tr>
    <td class="label">Desired Effective Date:</td>
    <td><input type="text" id="proposeddate" name="proposeddate" title="Effective Date" class="textfield datefield" value="<?php echo $this->native_session->get('proposeddate')? date('d-M-Y',strtotime($this->native_session->get('proposeddate'))):'';?>" style="width:95%;"/></td>
  </tr>
  <tr>
    <td class="label top">Reason:</td>
    <td><textarea id="promotionreason" name="promotionreason" title="Reason" class="textfield optional" placeholder="Enter the reason here (Optional)." style="width:300px; height: 150px;"><?php echo $this->native_session->get('promotionreason');?></textarea></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><input type="submit" name="submit" id="submit" class="btn" value="SUBMIT" /></td>
  </tr>
<?php } 
else 
{
	echo "<tr><td>".format_notice($this, "WARNING: You do not have a current job posting to submit a promotion application.")."</td></tr>";
}

?>

            </table>
</form>

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