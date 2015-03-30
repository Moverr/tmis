<?php 
if($this->native_session->get('__user_id')) $forwardUrl = !empty($forward)? $forward: get_user_dashboard($this, $this->native_session->get('__user_id'));
?>
<!DOCTYPE html>
<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">
<link rel="icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">

<title><?php echo SITE_TITLE;?>: <?php echo !empty($id)? (!empty($action) && $action=='view'? 'View':'Edit'): 'New';?> Application</title>

<!-- Stylesheets -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/jquery-ui.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.mobile.css" media="(max-width:790px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.tablet.css" media="(min-width:791px) and (max-width: 900px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.desktop.css" media="(min-width:901px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.list.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.menu.css"/>

<!-- Javascript -->
<?php echo minify_js('application-new_application', array('jquery-2.1.1.min.js', 'jquery-ui.js', 'jquery.form.js', 'tmis.js', 'tmis.fileform.js', 'tmis.menu.js', 'tmis.responsive.js'));?>

<script type="text/javascript">
<?php echo !empty($id) && !empty($result['boolean']) && $result['boolean']?"window.top.location.href = '".$forwardUrl."';": "";?>
</script>
</head>

<body style="margin:0px;">
<?php 
# Do not show the header, menu and footer when editing
if(empty($id)) {?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <?php $this->load->view("addons/".($this->native_session->get('__user_id')? 'secure_header': 'public_header'), array('page'=>'new_account'));?>
  <tr>
    <td valign="top" colspan="2" style="padding-top:0px;padding-left:15px;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <?php echo $this->native_session->get('__user_id')? "<td id='menucontainer'>".$this->load->view("addons/menu")."</td>": "<td>&nbsp;</td>";?>
        
        <td class="bodyspace" style="padding-left:15px;padding-top:15px; vertical-align:top;">
<?php }?>		

<table width="100%" border="0" cellspacing="0" cellpadding="0">
      		<tr><td class="h1 grey"><?php echo !empty($id)? (!empty($action) && $action=='view'? 'View':'Edit'): 'New';?> User</td></tr>
            <?php echo !empty($msg)?"<tr><td>".format_notice($this,$msg)."</td></tr>": "";?>
            <tr><td>
            
            
            <form id="user_data" method="post" autocomplete="off" action="<?php echo base_url().'account/apply'.(!empty($id)? '/id/'.$id: '');?>" class='simplevalidator'>
            <table border="0" cellspacing="0" cellpadding="10" <?php if(!$this->native_session->get('__user_id')) echo "align='center'";?>>
  
  <tr>
    <td class="label">Role:</td>
    <td style="padding-right:42px;"><?php if(!empty($id) && $this->native_session->get('role__roles') || (!empty($action) && $action=='view')){
		echo "<div class='value'>".$this->native_session->get('role__roles')."</div>";
		} else {?><input type="text" id="role__roles" name="role__roles" title="Select Role" placeholder="Select User Role" class="textfield selectfield" value="<?php echo $this->native_session->get('role__roles');?>" style="width:97%;"/><?php }?></td>
  </tr>
  <tr>
    <td class="label">Surname:</td>
    <td><?php if(!empty($action) && $action=='view'){
		echo "<div class='value'>".$this->native_session->get('lastname')."</div>";
		} else {?><input type="text" id="lastname" name="lastname" title="Surname" class="textfield" style="width:97%;" value="<?php echo $this->native_session->get('lastname');?>"/><?php }?></td>
  </tr>
  <tr>
    <td class="label">Other Names:</td>
    <td><?php if(!empty($action) && $action=='view'){
		echo "<div class='value'>".$this->native_session->get('firstname')."</div>";
		} else {?><input type="text" id="firstname" name="firstname" title="Other Names" class="textfield" style="width:97%;" value="<?php echo $this->native_session->get('firstname');?>"/><?php }?></td>
  </tr>
  <tr>
    <td class="label top">Email Address:</td>
    <td><?php if(!empty($id) && $this->native_session->get('emailaddress') || (!empty($action) && $action=='view')){
		echo "<div class='value'>".$this->native_session->get('emailaddress')."</div>";
		} else {?><input type="text" id="emailaddress" name="emailaddress" title="Email Address" class="textfield" style="width:97%;" value="<?php echo $this->native_session->get('emailaddress');?>"/><br><span class="smalltext">The password will be automatically generated <br>and sent to this email address.</span><?php }?></td>
  </tr>
  <tr>
    <td class="label">Telephone:</td>
    <td><?php if(!empty($action) && $action=='view'){
		echo "<div class='value'>".$this->native_session->get('telephone')."</div>";
		} else {?><input type="text" id="telephone" name="telephone" title="Telephone"  placeholder="Optional (e.g: 0782123456)" maxlength="10" class="textfield numbersonly telephone optional" style="width:97%;" value="<?php echo $this->native_session->get('telephone');?>"/><?php }?></td>
  </tr>
  <?php if(!(!empty($action) && $action=='view')) {?>
  <tr>
    <td>&nbsp;</td>
    <td><input type="submit" name="save" id="save" class="btn" value="SUBMIT" /><?php 
	$forward = $this->native_session->get('__user_id')? "user/applications/action/view": "";
	echo !empty($id)? "<input type='hidden' id='userid' name='userid' value='".$id."' /><input type='hidden' id='forward' name='forward' value='".$forward."' />": "";
	?></td>
  </tr>
  <?php }?>
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
  <?php $this->load->view("addons/".($this->native_session->get('__user_id')? "secure_footer": "public_footer"), array('page'=>'new_account'));?>
</table>
<?php } else {echo "<input type='hidden' id='layerid' name='layerid' value='' />";}?>
</body>
</html>