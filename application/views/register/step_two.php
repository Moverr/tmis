<!DOCTYPE html>
<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">
<link rel="icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">

<title><?php echo SITE_TITLE;?>: Register</title>

<!-- Stylesheets -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/jquery-ui.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.mobile.css" media="(max-width:790px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.tablet.css" media="(min-width:791px) and (max-width: 900px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.desktop.css" media="(min-width:901px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.list.css"/>

<!-- Javascript -->
<?php echo minify_js('register-step_two', array('jquery-2.1.1.min.js', 'jquery-ui.js', 'jquery.form.js', 'tmis.js', 'tmis.callout.js', 'tmis.fileform.js', 'tmis.responsive.js'));?>
</head>

<body style="margin:0px;">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <?php $this->load->view("addons/public_header", array("page"=>"register"));?>
  <tr>
    <td valign="top" colspan="2" class="bodyspace">
    <form id="home_registration_form" method="post" autocomplete="off" action="<?php echo base_url();?>register/step_two" class='simplevalidator'>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
      <td class="h1 grey">Teacher Registration</td>
     </tr>
     <tr>
     <td>
     <table width="100%" border="0" cellspacing="0" cellpadding="0" id="stepstracker">
     	<tr>
    		<td class='stepone visited'><div>&nbsp;</div><div>&nbsp;</div><div>&nbsp;</div></td>
     		<td class='visitedfiller'>&nbsp;</td>
     		<td class='steptwo visited'><div>&nbsp;</div><div>&nbsp;</div><div>&nbsp;</div></td>
     		<td class='unvisitedfiller'>&nbsp;</td>
     		<td class='stepthree unvisited'><div>&nbsp;</div><div>&nbsp;</div><div>&nbsp;</div></td>
     		<td class='unvisitedfiller'>&nbsp;</td>
     		<td class='stepfour unvisited'><div>&nbsp;</div><div>&nbsp;</div><div>&nbsp;</div></td>
     	</tr>
     	<tr>
     		<td class='visited'>Personal Information</td>
     		<td>&nbsp;</td>
     		<td class='visited'>Identification &amp; Contacts</td>
     		<td>&nbsp;</td>
     		<td class='unvisited'>Education &amp; Qualifications</td>
     		<td>&nbsp;</td>
    		<td class='unvisited'>Preview &amp; Submit</td>
     	</tr>
     </table>
     </td>
     </tr>
     
     <?php echo !empty($msg)?"<tr><td>".format_notice($this,$msg)."</td></tr>": "";?>
     
     <tr>
      <td>
      
        <table border="0" cellspacing="0" cellpadding="10">
  <tr>
    <td class="label<?php echo !$this->native_session->get('edit_step_2')? " addinfo": "";?>">Confirmation Code:</td>
    <td><?php if($this->native_session->get('edit_step_2')){
			echo "<span class='value'>".$this->native_session->get('verificationcode')."</span><input type='hidden' id='verificationcode' name='verificationcode' value='".$this->native_session->get('verificationcode')."'/>";
		 } else {?><input type="text" id="verificationcode" name="verificationcode" title="Confirmation Code" class="textfield" value="<?php echo ($this->native_session->get('verificationcode')? $this->native_session->get('verificationcode'): '');?>"/>
    <br><span class="smalltext">Check your email for code.</span><?php }?></td>
  </tr>
  <tr>
    <td class="label">Teacher UTS Number:</td>
    <td><input type="text" id="teacherid" name="teacherid" title="Teacher UTS Number" class="textfield optional" placeholder="Optional" value="<?php echo ($this->native_session->get('teacherid')? $this->native_session->get('teacherid'): '');?>"/></td>
  </tr>
  <tr>
    <td class="label">Permanent Address:</td>
    <td><input type="text" id="permanentaddress" name="permanentaddress" title="Permanent Address" class="textfield placefield" value="<?php echo ($this->native_session->get('permanentaddress')? $this->native_session->get('permanentaddress'): '');?>"/></td>
  </tr>
  <tr>
    <td class="label">Contact Address:</td>
    <td><div class="nextdiv"><input type="text" id="contactaddress" name="contactaddress" title="Contact Address" class="textfield placefield" value="<?php echo ($this->native_session->get('contactaddress')? $this->native_session->get('contactaddress'): '');?>"/></div>
    <div class="nextdiv"><input type="checkbox" id="permanentsameascontact" name="permanentsameascontact" value="Y" /><label for="permanentsameascontact">Same as above</label></div></td>
  </tr>
  <tr>
    <td class="label">Country of Citizenship:</td>
    <td><div class="nextdiv"><input type="text" id="citizenship__country" name="citizenship__country" title="Country of Citizenship" placeholder="Select Country" class="textfield selectfield" value="<?php echo ($this->native_session->get('citizenship__country')? $this->native_session->get('citizenship__country'): '');?>"/></div>
    <div class="nextdiv"><input type="text" id="citizenship__citizentype" name="citizenship__citizentype" title="How You Obtained Citizenship" placeholder="Citizen By" class="textfield selectfield" value="<?php echo ($this->native_session->get('citizenship__citizentype') && $this->native_session->get('citizenship__citizentype') != '_CITIZENSHIP_TYPE_'? $this->native_session->get('citizenship__citizentype'): '');?>"/></div></td>
  </tr>
</table>
        
      </td>
     </tr>
     <tr>
      <td>
     <table width="100%" border="0" cellspacing="0" cellpadding="0" class='buttonnav'>
     <tr>
     <td><button type="button" name="backtostep1" id="backtostep1" class="greybtn back">BACK</button></td>
     <td class='spacefiller'>&nbsp;</td>
     <td><?php 
	 if($this->native_session->get('just_preview_2')){
		 echo "<button type='button' name='step2preview' id='step2preview' class='greybtn'>SAVE &amp; PREVIEW</button>";
	 } else {
		 ?><button type="button" name="step2save" id="step2save" class="greybtn">SAVE &amp; EXIT</button>
<?php } ?></td>
     <td><input type="submit" name="step2" id="step2" class="btn next" value="NEXT" /></td>
     </tr>
     <tr>
       <td colspan="4" class='note'>Applications not completed within 14 days will be automatically deleted by the system.</td>
       </tr>
     </table> 
      </td>
     </tr>
     </table>
     </form>
    </td>
  </tr>
  <?php $this->load->view("addons/public_footer");?>
</table>


</body>
</html>