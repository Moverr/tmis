<?php $msg = empty($msg)? get_session_msg($this): $msg; ?>
<!DOCTYPE html>
<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">
<link rel="icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">

<title><?php echo SITE_TITLE;?>: My Settings</title>

<!-- Stylesheets -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.mobile.css" media="(max-width:790px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.tablet.css" media="(min-width:791px) and (max-width: 900px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.desktop.css" media="(min-width:901px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.list.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.menu.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.shadowbox.css"/>

<!-- Javascript -->
<?php echo minify_js('profile-user_data', array('jquery-2.1.1.min.js', 'jquery.form.js', 'tmis.js', 'tmis.callout.js', 'tmis.fileform.js', 'tmis.menu.js', 'tmis.responsive.js', 'tmis.shadowbox.js'));?>
</head>

<body style="margin:0px;">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <?php $this->load->view("addons/secure_header", array('page'=>'my_settings'));?>
  <tr>
    <td valign="top" colspan="2" style="padding-left:15px;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td id="menucontainer"><?php $this->load->view("addons/menu", array('clear_menu'=>'Y'));?></td>
        <td class="bodyspace" style="padding-left:15px;padding-top:15px; vertical-align:top;">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
      		<?php echo !empty($msg)?"<tr><td>".format_notice($this,$msg)."</td></tr>": "";?>
            <tr><td style="vertical-align:middle;"><div class="nextdiv h1 grey">My Settings</div><div class="nextdiv editcontenticon editcontent"></div></td></tr>
            <tr><td>
            
            
            <form id="user_data" method="post" autocomplete="off" enctype="multipart/form-data" action="<?php echo base_url();?>profile/user_data" class='simplevalidator'>
            <table border="0" cellspacing="0" cellpadding="10" width="100%">
  <tr>
    <td class="label" width='1%'>User Name:</td>
    <td class='value'><?php echo $this->native_session->get('profile_loginname');?></td>
    <td rowspan="2" <?php echo $this->native_session->get('__permission_group') == '4'? " width='98%'": "";?>><?php 
	if($this->native_session->get('__permission_group') == 4)
	{
		echo "<table align='right'  class='listtable' style='border: 1px solid #DDDDDD; text-align:right; width: 220px;'>
		
		<tr><td><div class='rightnote'><a href='".base_url()."cron/update_query_cache' class='shadowbox closable'>refresh query cache</a></div></td></tr>
		
		<tr><td><div class='rightnote'><a href='".base_url()."cron/update_message_cache' class='shadowbox closable'>refresh message cache</a></div></td></tr>
		
		</table>";
	}
	?></td>
  </tr>
  <tr>
    <td class="label">Current Role:</td>
    <td class='value'><?php echo $this->native_session->get('profile_userrole');?></td>
    </tr>
  <tr>
    <td class="label top" nowrap>Current Password:</td>
    <td><div class="viewdiv value">*********</div><div class="editdiv"><input type="password" id="currentpassword" name="currentpassword" title="Current Password" placeholder="Current Password" maxlength="100" class="textfield optional" value=""/>
    <div class="editdiv smalltext">Enter the password only if you want to change it.</div></div></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td class="label editdiv">New Password:</td>
    <td><div class="nextdiv"><div class="editdiv"><input type="password" id="newpassword" name="newpassword" title="New Password" placeholder="New Password" maxlength="100" class="textfield optional" value=""/></div></div><div class="nextdiv"><div class="editdiv"><input type="password" id="repeatpassword" name="repeatpassword" title="Repeat New Password" placeholder="Repeat New Password" maxlength="100" class="textfield optional" value=""/></div></div>
    <div class="editdiv smalltext">Your new password should be at least 8 characters long with a letter and a number.</div>
    </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td class="label">Surname:</td>
    <td><div class="viewdiv value"><?php echo $this->native_session->get('profile_lastname');?></div><div class="editdiv"><input type="text" id="lastname" name="lastname" title="Surname" class="textfield" value="<?php echo $this->native_session->get('profile_lastname');?>"/></div></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td class="label">Other Names:</td>
    <td><div class="viewdiv value"><?php echo $this->native_session->get('profile_firstname');?></div><div class="editdiv"><input type="text" id="firstname" name="firstname" title="Other Names" class="textfield" value="<?php echo $this->native_session->get('profile_firstname');?>"/></div></td>
    <td>&nbsp;</td>
  </tr>
  
  <tr>
    <td class="label top">Photo:</td>
    <td><div class="viewdiv value"><?php echo $this->native_session->get('profile_photo')? "<img src='".base_url().'assets/uploads/images/'.$this->native_session->get('profile_photo')."' style='max-height:80px;' border='0' />": 'None';?></div><div class="editdiv">
    <?php echo $this->native_session->get('profile_photo')? "<img src='".base_url().'assets/uploads/images/'.$this->native_session->get('profile_photo')."' style='max-height:110px;' border='0' /><br>": '';?>
    <input type="text" id="photo" name="photo" title="Your Profile Photo" data-size='300' data-val='jpg,jpeg,gif,png,tiff' class="textfield filefield optional" <?php echo $this->native_session->get('profile_photo')? "placeholder='Select New Image'": '';?> value=""/>
    <div class="editdiv smalltext">Allowed Formats: JPG, JPEG, GIF, PNG, TIFF  &nbsp;&nbsp; Max Size: 300kB</div></div></td>
    <td>&nbsp;</td>
  </tr>
  
  <tr>
    <td class="label top">Signature on File:</td>
    <td><div class="viewdiv value"><?php echo $this->native_session->get('profile_signature')? "<img src='".base_url().'assets/uploads/images/'.$this->native_session->get('profile_signature')."' style='max-height:80px;' border='0' />": 'None';?></div><div class="editdiv">
    <?php echo $this->native_session->get('profile_signature')? "<img src='".base_url().'assets/uploads/images/'.$this->native_session->get('profile_signature')."' style='max-height:80px;' border='0' /><br>": '';?>
    <input type="text" id="signature" name="signature" title="Your Signature on File" data-size='300' data-val='jpg,jpeg,gif,png,tiff' class="textfield filefield optional" <?php echo $this->native_session->get('profile_signature')? "placeholder='Select New Image'": '';?> value=""/>
    <div class="editdiv smalltext">Allowed Formats: JPG, JPEG, GIF, PNG, TIFF  &nbsp;&nbsp; Max Size: 300kB</div></div></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td class="label">Telephone:</td>
    <td><div class="viewdiv value"><?php echo ($this->native_session->get('profile_telephone')? $this->native_session->get('profile_telephone'): '&nbsp;');?></div><div class="editdiv"><input type="text" id="telephone" name="telephone" title="Telephone" placeholder="Optional" maxlength="16" class="textfield numbersonly optional" value="<?php echo ($this->native_session->get('profile_telephone')? $this->native_session->get('profile_telephone'): '');?>"/></div></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td class="label">Email Address:</td><td class="value"><?php echo $this->native_session->get('profile_emailaddress');?></td>
    <td class="value">&nbsp;</td>
  </tr>
  <tr>
    <td class="label">Address:</td>
    <td><div class="viewdiv value"><?php echo ($this->native_session->get('contactaddress__addressline')? $this->native_session->get('contactaddress__addressline')." ".$this->native_session->get('contactaddress__county')." <br>".$this->native_session->get('contactaddress__district').($this->native_session->get('contactaddress__country')? ", ".$this->native_session->get('contactaddress__country'): ''): 'None');?></div><div class="editdiv"><input type="text" id="contactaddress" name="contactaddress" title="Contact Address" placeholder="" maxlength="16" class="textfield placefield optional" value="<?php echo ($this->native_session->get('contactaddress__addressline')? $this->native_session->get('contactaddress__addressline'): '');?>"/></div></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td class="label">&nbsp;</td>
    <td colspan="2" class="editdiv"><input type="submit" name="save" id="save" class="btn" value="SAVE" /></td>
  </tr>
            </table>
            </form>
            
            </td></tr>
        </table></td>
      </tr>
     </table>
    </td>
  </tr>
  <?php $this->load->view("addons/secure_footer");?>
</table>


</body>
</html>