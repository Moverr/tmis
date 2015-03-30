<?php $msg = empty($msg)? get_session_msg($this): $msg; ?>
<!DOCTYPE html>
<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">
<link rel="icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">

<title><?php echo SITE_TITLE;?>: Apply for Leave</title>

<!-- Stylesheets -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/jquery-ui.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.mobile.css" media="(max-width:790px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.tablet.css" media="(min-width:791px) and (max-width: 900px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.desktop.css" media="(min-width:901px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.list.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.menu.css"/>

<!-- Javascript -->
<?php echo minify_js('leave-apply', array('jquery-2.1.1.min.js', 'jquery-ui.js', 'jquery.form.js', 'tmis.js', 'tmis.fileform.js', 'tmis.menu.js', 'tmis.responsive.js'));?>

</head>

<body style="margin:0px;">
<?php 
# Do not show the header, menu and footer when editing
if(empty($id)) {?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <?php $this->load->view("addons/secure_header");?>
  <tr>
    <td valign="top" colspan="2" style="padding-top:0px;padding-left:15px;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td id="menucontainer"><?php if($this->native_session->get('__user_id')) $this->load->view("addons/menu");?></td>
        <td class="bodyspace" style="padding-left:15px;padding-top:15px; vertical-align:top;">
<?php }?>		

<table width="100%" border="0" cellspacing="0" cellpadding="0">
      		<tr><td class="h1 grey">Apply for Leave</td></tr>
            <?php echo !empty($msg)?"<tr><td>".format_notice($this,$msg)."</td></tr>": "";?>
            <tr><td>
            
            
            <form id="leave_application" method="post" autocomplete="off" action="<?php echo base_url().'leave/apply';?>" class='simplevalidator'>
            <table border="0" cellspacing="0" cellpadding="10" <?php echo empty($currentJob)? " width='100%' ": "";?>>
<?php 
if(!empty($currentJob))
{
?>

  <tr>
    <td class="label">Desired Start Date:</td>
    <td><input type="text" id="leavestartdate" name="leavestartdate" title="Leave Start Date" class="textfield datefield" style="width:95%;" value="<?php echo $this->native_session->get('leavestartdate');?>"/></td>
  </tr>
  <tr>
    <td class="label">Desired End Date:</td>
    <td><input type="text" id="leaveenddate" name="leaveenddate" title="Leave End Date" class="textfield datefield" style="width:95%;" value="<?php echo $this->native_session->get('leaveenddate');?>"/></td>
  </tr>
  <tr>
    <td class="label top">Reason:</td>
    <td><textarea id="leavereason" name="leavereason" title="Reason for Leave" class="textfield" style="width:300px; height:150px;"><?php echo $this->native_session->get('leavereason');?></textarea></td>
  </tr>
 
  <tr>
    <td>&nbsp;</td>
    <td><input type="submit" name="save" id="save" class="btn" value="SUBMIT" /></td>
  </tr>
<?php }
else 
{
	echo "<tr><td>".format_notice($this, "WARNING: You do not have a current job posting to submit a leave application.")."</td></tr>";
}
?>
            </table>
            </form>
            
            </td></tr>
        </table>
 
 <?php 
# Do not show the header and footer when editing
if(empty($id)) {?>       
        </td>
      </tr>
     </table>
    </td>
  </tr>
  <?php $this->load->view("addons/secure_footer");?>
</table>
<?php } else {echo "<input type='hidden' id='layerid' name='layerid' value='' />";}?>
</body>
</html>