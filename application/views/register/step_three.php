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
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.desktop.css" media="(min-width:901px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.tablet.css" media="(min-width:791px) and (max-width: 900px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.mobile.css" media="(max-width:790px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.list.css"/>

<!-- Javascript -->
<?php echo minify_js('register-step_three', array('jquery-2.1.1.min.js', 'jquery-ui.js', 'jquery.form.js', 'tmis.js', 'tmis.callout.js', 'tmis.fileform.js', 'tmis.responsive.js'));?>
</head>

<body style="margin:0px;">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <?php $this->load->view("addons/public_header", array("page"=>"register"));?>
  <tr>
    <td valign="top" colspan="2" class="bodyspace">
    <form id="home_registration_form" method="post" autocomplete="off" action="<?php echo base_url();?>register/step_three">
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
     		<td class='visitedfiller'>&nbsp;</td>
     		<td class='stepthree visited'><div>&nbsp;</div><div>&nbsp;</div><div>&nbsp;</div></td>
     		<td class='unvisitedfiller'>&nbsp;</td>
     		<td class='stepfour unvisited'><div>&nbsp;</div><div>&nbsp;</div><div>&nbsp;</div></td>
     	</tr>
     	<tr>
     		<td class='visited'>Personal Information</td>
     		<td>&nbsp;</td>
     		<td class='visited'>Identification &amp; Contacts</td>
     		<td>&nbsp;</td>
     		<td class='visited'>Education &amp; Qualifications</td>
     		<td>&nbsp;</td>
    		<td class='unvisited'>Preview &amp; Submit</td>
     	</tr>
     </table>
     </td>
     </tr>
     
     <?php 
	 echo !empty($msg)?"<tr><td>".format_notice($this,$msg)."</td></tr>": "";
	 $msg = "";
	 ?>
     
     <tr><td class="greybg h3" style="padding-left:5px;">Education:</td></tr>
     <tr>
      <td><div id="form_div__education"><?php $this->load->view('addons/basic_addons', array('area'=>'education_form'));?></div></td>
     </tr>
     
     <tr><td><div id="institution_list">
     <?php
	 if($this->native_session->get('education_list')){
		 $this->load->view('addons/basic_addons', array('area'=>'education_list'));
	 } else {
	 ?>
     <table border="0" cellspacing="0" cellpadding="0" class="resultslisttable">
     <tr><td>Current List</td></tr>
     <tr><td>No education added yet.</td></tr>
     </table>
     <?php }?>
     </div></td></tr>
     <tr><td>&nbsp;</td></tr>
     
     
     
     <tr><td class="greybg h3" style="padding-left:5px;">Subjects Taught:</td></tr>
     
     <tr>
       <td><div id="form_div__subject"><?php $this->load->view('addons/basic_addons', array('area'=>'subject_form'));?></div></td></tr>
     <tr><td><div id="subject_list">
     <?php
	 if($this->native_session->get('subject_list')){
		 $this->load->view('addons/basic_addons', array('area'=>'subject_list'));
	 } else {
	 ?>
     <table border="0" cellspacing="0" cellpadding="0" class="resultslisttable">
     <tr><td>Current List</td></tr>
     <tr><td>No subject added yet.</td></tr>
     </table>
     <?php }?>
     </div></td></tr>
     <tr><td>&nbsp;</td></tr>
     
     
     
     <tr><td class="greybg h3" style="padding-left:5px;">Qualification Documents:</td></tr>
     
     <tr>
       <td><div id="form_div__document"><?php $this->load->view('addons/basic_addons', array('area'=>'document_form'));?></div></td></tr>
     <tr><td><div id="document_list">
     <?php
	 if($this->native_session->get('document_list')){
		 $this->load->view('addons/basic_addons', array('area'=>'document_list'));
	 } else {
	 ?>
     <table border="0" cellspacing="0" cellpadding="0" class="resultslisttable">
     <tr><td>Current List</td></tr>
     <tr><td>No document added yet.</td></tr>
     </table>
     <?php }?>
     </div></td></tr>
     <tr><td style="padding-top:30px;">&nbsp;</td></tr>
     
     <tr>
      <td>
     <table width="100%" border="0" cellspacing="0" cellpadding="0" class='buttonnav'>
     <tr>
     <td><button type="button" name="backtostep2" id="backtostep2" class="greybtn back">BACK</button></td>
     <td class='spacefiller'>&nbsp;</td>
     <td><?php 
	 if($this->native_session->get('just_preview_3')){
		 echo "<button type='button' name='step3preview' id='step3preview' class='greybtn'>SAVE &amp; PREVIEW</button>";
	 } else {
		 ?><button type="button" name="step3save" id="step3save" class="greybtn">SAVE &amp; EXIT</button>
<?php } ?></td>
     <td><input type="submit" name="step3" id="step3" value="NEXT" class="btn next" /></td>
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