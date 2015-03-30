<!DOCTYPE html>
<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">
<link rel="icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">

<title><?php echo SITE_TITLE;?>: Contact Us</title>

<!-- Stylesheets -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.mobile.css" media="(max-width:790px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.tablet.css" media="(min-width:791px) and (max-width: 900px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.desktop.css" media="(min-width:901px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.list.css"/>

<!-- Javascript -->
<?php echo minify_js('page-contact_us', array('jquery-2.1.1.min.js', 'jquery.form.js', 'tmis.js', 'tmis.fileform.js', 'tmis.responsive.js'));?>
</head>

<body style="margin:0px;">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <?php $this->load->view("addons/public_header", array("page"=>"contact_us"));?>
  <tr>
    <td valign="top" colspan="2" class="bodyspace">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
      <td class="h1 grey">Contact Us</td>
     </tr>
     <tr>
      <td style="text-align:center;"><div style="display:inline-block;"><form method="post" autocomplete="off" action="<?php echo base_url();?>page/contact_us" class='simplevalidator' ><table border="0" cellspacing="0" cellpadding="10">
<?php
if(!empty($result) && $result){
	echo "<tr><td style='padding-bottom:100px;'>".format_notice($this, $msg)."</td></tr>";
} else {
	echo !empty($msg)? "<tr><td colspan='2'>".format_notice($this, $msg)."</td></tr>": "";
?>
  
  <tr>
    <td class="label">Your Name: </td>
    <td><input type="text" id="yourname" name="yourname" class="textfield" value="<?php echo $this->native_session->get('yourname');?>" maxlength="100"/></td>
  </tr>
  <tr>
    <td class="label">Email Address: </td>
    <td><input type="text" id="emailaddress" name="emailaddress" class="textfield email" value="<?php echo $this->native_session->get('emailaddress');?>" maxlength="100"/></td>
  </tr>
  <tr>
    <td class="label">Telephone: </td>
    <td><input type="text" id="telephone" name="telephone" placeholder="Optional (e.g: 0782123456)" class="textfield numbersonly telephone optional" value="<?php echo $this->native_session->get('telephone');?>" maxlength="10"/></td>
  </tr>
  <tr>
    <td class="label">Reason: </td>
    <td><input type="text" id="reason__contactreason" name="reason__contactreason" placeholder="Enter or Select reason" class="textfield selectfield editable" value="<?php echo $this->native_session->get('reason__contactreason');?>"/></td>
  </tr>
  <tr>
    <td class="label" valign="top">Message: </td>
    <td><textarea id="details" name="details" placeholder="Enter your message here" class="textfield" style="height:120px;"><?php echo $this->native_session->get('details');?></textarea></td>
  </tr>
  <tr>
    <td colspan="2" style="text-align:right;"><input type="submit" class="btn" id="submitmessage" name="submitmessage" style="width:238px;" value="SEND" /></td>
  </tr>
<?php }?>
      </table>
      </form></div>
      <div style="display:inline-block; vertical-align:top; text-align:left; padding-left:20px; padding-top:10px;">
<span class="h2">ADDRESS:
<br>Ministry of Education and Sports</span>
<br>P. O. BOX 7063 Kampala, Uganda
<br>Tel: 256-41-234451/4
<br>Fax: 256-41-234920
<br>Email: <a href='mailto:pro@education.go.ug'>pro@education.go.ug</a>

      </div></td>
     </tr>
     </table>
    </td>
  </tr>
  <?php $this->load->view("addons/public_footer", array("page"=>"contact_us"));?>
</table>


</body>
</html>