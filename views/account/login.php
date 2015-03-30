<?php $msg = empty($msg)? get_session_msg($this): $msg; ?>
<!DOCTYPE html>
<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">
<link rel="icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">

<title><?php echo SITE_TITLE;?>: Login</title>

<!-- Stylesheets -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.mobile.css" media="(max-width:790px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.tablet.css" media="(min-width:791px) and (max-width: 900px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.desktop.css" media="(min-width:901px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.list.css"/>

<!-- Javascript -->
<?php echo minify_js('account-login', array('jquery-2.1.1.min.js', 'jquery.form.js', 'tmis.js', 'tmis.fileform.js', 'tmis.responsive.js'));?>
</head>

<body style="margin:0px;">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <?php $this->load->view("addons/public_header", array("page"=>"login"));?>
  <tr>
    <td valign="top" colspan="2" class="bodyspace">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
      <td class="h1 grey">Login</td>
     </tr>
     <?php echo !empty($msg)?"<tr><td>".format_notice($this,$msg)."</td></tr>": "";?>
     <tr>
      <td style="text-align:center;"><div style="display:inline-block;"><form id="tmislogin" method="post" autocomplete="off" ><table border="0" cellspacing="0" cellpadding="10">
  <tr>
    <td class="label">Email Address: </td>
    <td><input type="text" id="loginusername" name="loginusername" placeholder="Email Address" class="textfield" value=""/></td>
  </tr>
  <tr>
    <td class="label">Password: </td>
    <td><input type="password" id="loginpassword" name="loginpassword" placeholder="Password" class="textfield" value=""/></td>
  </tr>
  <tr>
    <td colspan="2"><button type="button" class="greybtn" id="submitlogin" name="submitlogin" style="width:340px;">LOGIN</button></td>
  </tr>
  <tr>
    <td colspan="2"><a href="<?php echo base_url().'account/forgot';?>">Forgot Username or Password?</a> &nbsp;|&nbsp; <a href="<?php echo base_url().'account/apply';?>">Apply for Account</a></td>
  </tr>
      </table>
      </form></div></td>
     </tr>
     </table>
    </td>
  </tr>
  <?php $this->load->view("addons/public_footer");?>
</table>


</body>
</html>