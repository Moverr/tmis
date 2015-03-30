<?php $msg = empty($msg)? get_session_msg($this): $msg; ?>
<!DOCTYPE html>
<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">
<link rel="icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">

<title><?php echo SITE_TITLE;?>: Forgot Password</title>

<!-- Stylesheets -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.mobile.css" media="(max-width:790px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.tablet.css" media="(min-width:791px) and (max-width: 900px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.desktop.css" media="(min-width:901px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.list.css"/>

<!-- Javascript -->
<?php echo minify_js('account-recover_password', array('jquery-2.1.1.min.js', 'jquery.form.js', 'tmis.js', 'tmis.fileform.js', 'tmis.responsive.js'));?>
</head>

<body style="margin:0px;">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <?php $this->load->view("addons/public_header", array("page"=>"terms_of_use"));?>
  <tr>
    <td valign="top" colspan="2" class="bodyspace">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
      <td class="h1 grey">Forgot Password</td>
     </tr>
    <tr><td><br><br>
    Your username is the same as your registered email address. <br>
    <br>If your account was specially created by an administrator without using your email address, please <a href='<?php echo base_url();?>page/contact_us'>contact us</a> for a password recovery. <br>Otherwise, send yourself a temporary password using the form below.</td></tr>
     <tr>
      <td><table border="0" cellspacing="0" cellpadding="10" class="microform" id="forgottable" align="center">
      
   <tr><td>
   
   <div id='forgotmsgdiv'></div></td></tr>
   <tr>
    <td class="label" style="text-align:left; padding-bottom:0px;">Enter Your Registered Email Address: </td>
  </tr>
  <tr>
    <td><input type="text" id="registeredemail" name="registeredemail" class="textfield email" value="" maxlength="50" style="width:325px;"/></td>
  </tr>
  <tr>
    <td><button type="button" class="btn submitmicrobtn" id="sendpassword" name="sendpassword" style="width:340px;">SEND MY TEMPORARY PASSWORD</button>
    <input type="hidden" name="resultsdiv" id="resultsdiv" value="forgotmsgdiv">
    <input type="hidden" name="action" id="action" value="<?php echo base_ulr();?>account/forgot"></td>
  </tr>
      </table></td>
     </tr>
     </table>
    </td>
  </tr>
  <?php $this->load->view("addons/public_footer");?>
</table>


</body>
</html>