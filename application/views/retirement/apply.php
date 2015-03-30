<?php 
$forwardUrl = !empty($forward)? $forward: get_user_dashboard($this, $this->native_session->get('__user_id'));
?>
<!DOCTYPE html>
<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">
<link rel="icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">

<title><?php echo SITE_TITLE;?>: Retirement Application</title>

<!-- Stylesheets -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/jquery-ui.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.mobile.css" media="(max-width:790px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.tablet.css" media="(min-width:791px) and (max-width: 900px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.desktop.css" media="(min-width:901px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.list.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.menu.css"/>

<!-- Javascript -->
<?php echo minify_js('retirement-apply', array('jquery-2.1.1.min.js', 'jquery-ui.js', 'jquery.form.js', 'tmis.js', 'tmis.callout.js', 'tmis.fileform.js', 'tmis.menu.js', 'tmis.responsive.js'));?>

<script type="text/javascript">
<?php echo !empty($id) && !empty($result['boolean']) && $result['boolean']?"window.top.location.href = '".$forwardUrl."';": "";?>
</script>
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
        <td id="menucontainer"><?php $this->load->view("addons/menu");?></td>
        <td class="bodyspace" style="padding-left:15px;padding-top:15px; vertical-align:top;">
<?php }?>		

<table width="100%" border="0" cellspacing="0" cellpadding="0">
      		<tr><td class="h1 grey">Retirement Application</td></tr>
            <?php echo !empty($msg)?"<tr><td>".format_notice($this,$msg)."</td></tr>": "";?>
            <tr><td>
            
            
            <form id="retirement_application" method="post" autocomplete="off" action="<?php echo base_url().'retirement/apply';?>" class='simplevalidator'>
            <table border="0" cellspacing="0" cellpadding="10">
  <?php if(empty($id) && !empty($current_application)){
	  
	  echo "<tr><td>".format_notice($this,"WARNING: You have already applied for retirement. Please await action by the approving authorities. <br><br>If you want to cancel your current application, go to <a href='".base_url()."retirement/cancel'>the cancellation page</a>.")."</td></tr>";
	  
  } else {?>
  <tr>
    <td class="label">Proposed Retirement Date:</td>
    <td style="padding-right:42px;"><?php if(!empty($id)){
		echo "<div class='value'>".$this->native_session->get('retirementdate')."</div>";
		} else {?><input type="text" id="retirementdate" name="retirementdate" title="Retirement Date" class="textfield datefield" value="<?php echo $this->native_session->get('retirementdate');?>"/><?php }?></td>
  </tr>
  <tr>
    <td class="label top">Reason:</td>
    <td><?php if(!empty($id)){
		echo "<div class='value'>".$this->native_session->get('retirementreason')."</div>";
		} else {?><textarea id="retirementreason" name="retirementreason" title="Retirement Reason" placeholder="Enter the reason for your retirement application (Optional)" class="textfield optional" style="width:430px; min-height: 200px;"><?php echo $this->native_session->get('retirementreason');?></textarea><?php }?></td>
  </tr>
  <?php }
  
   if(empty($id) && empty($current_application)) {?>
  <tr>
    <td>&nbsp;</td>
    <td><input type="submit" name="submit" id="submit" class="btn" value="SUBMIT" /></td>
  </tr>
  <?php }?>
            </table>
            </form>
            
            </td></tr>
        </table>
 
 <?php 
# Do not show the header and footer when editing/viewing
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