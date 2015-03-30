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
<?php echo minify_js('register-step_four', array('jquery-2.1.1.min.js', 'jquery-ui.js', 'jquery.form.js', 'tmis.js', 'tmis.callout.js', 'tmis.fileform.js', 'tmis.responsive.js'));?>
</head>

<body style="margin:0px;">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <?php $this->load->view("addons/public_header", array("page"=>"register"));?>
  <tr>
    <td valign="top" colspan="2" class="bodyspace">
    <form id="home_registration_form" method="post" autocomplete="off" action="<?php echo base_url();?>register/step_four" class='simplevalidator'>
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
     		<td class='visitedfiller'>&nbsp;</td>
     		<td class='stepfour visited'><div>&nbsp;</div><div>&nbsp;</div><div>&nbsp;</div></td>
     	</tr>
     	<tr>
     		<td class='visited'>Personal Information</td>
     		<td>&nbsp;</td>
     		<td class='visited'>Identification &amp; Contacts</td>
     		<td>&nbsp;</td>
     		<td class='visited'>Education &amp; Qualifications</td>
     		<td>&nbsp;</td>
    		<td class='visited'>Preview &amp; Submit</td>
     	</tr>
     </table>
     </td>
     </tr>
     
     <?php echo !empty($msg)?"<tr><td>".format_notice($this,$msg)."</td></tr>": "";?>
     
     <tr><td class="greybg" style="padding-left:5px; vertical-align:top;"><div id="editstep1" class="editicon nextdiv" style="min-width:30px;"></div><div class="nextdiv h3" style="vertical-align:top; padding-top:2px;">Personal Information</div></td></tr>
     <tr><td><table border="0" cellspacing="0" cellpadding="5">
  <tr>
    <td class="previewlabel" style="width:150px;">Surname:</td>
    <td class='value'><?php echo ($this->native_session->get('lastname')? $this->native_session->get('lastname'): '&nbsp;');?></td>
  </tr>
  <tr>
    <td class="previewlabel">Other Names:</td>
    <td class='value'><?php echo ($this->native_session->get('firstname')? $this->native_session->get('firstname'): '&nbsp;');?></td>
  </tr>
  <tr>
    <td class="previewlabel">Telephone:</td>
    <td class='value'><?php echo ($this->native_session->get('telephone')? $this->native_session->get('telephone'): '&nbsp;');?></td>
  </tr>
  <tr>
    <td class="previewlabel">Email Address:</td>
    <td class='value'><?php echo ($this->native_session->get('emailaddress')? $this->native_session->get('emailaddress'): '&nbsp;');?></td>
  </tr>
  <tr>
    <td class="previewlabel">Gender:</td>
    <td class='value'><?php echo ($this->native_session->get('gender')? ucfirst($this->native_session->get('gender')): '&nbsp;');?></td>
  </tr>
  <tr>
    <td class="previewlabel">Marital Status:</td>
    <td class='value'><?php echo ($this->native_session->get('marital')? ucfirst($this->native_session->get('marital')): '&nbsp;');?></td>
  </tr>
  <tr>
    <td class="previewlabel">Birth Day:</td>
    <td class='value'><?php echo ($this->native_session->get('birthday')? $this->native_session->get('birthday'): '&nbsp;');?></td>
  </tr>
  <tr>
    <td class="previewlabel">Birth Place:</td>
    <td class='value'><?php echo ($this->native_session->get('birthplace__addressline')? $this->native_session->get('birthplace__addressline').', '.($this->native_session->get('birthplace__county')? $this->native_session->get('birthplace__county').', ': '').$this->native_session->get('birthplace__district').', '.$this->native_session->get('birthplace__country'): '&nbsp;');?></td>
  </tr>
</table></td></tr>
     <tr><td style="padding-top:30px;">&nbsp;</td></tr>
     
     
     
     <tr><td class="greybg" style="padding-left:5px; vertical-align:top;"><div id="editstep2" class="editicon nextdiv" style="min-width:30px;"></div><div class="nextdiv h3" style="vertical-align:top; padding-top:2px;">Identification &amp; Contacts</div></td></tr>
     <tr><td><table border="0" cellspacing="0" cellpadding="5">
  <tr>
    <td class="previewlabel" style="width:150px;">Teacher File Number:</td>
    <td class='value'><?php echo ($this->native_session->get('teacherid')? $this->native_session->get('teacherid'): '&nbsp;');?></td>
  </tr>
  <tr>
    <td class="previewlabel">Permanent Address:</td>
    <td class='value'><?php echo ($this->native_session->get('permanentaddress')? $this->native_session->get('permanentaddress').', '.($this->native_session->get('permanentaddress__county')? $this->native_session->get('permanentaddress__county').', ': '').$this->native_session->get('permanentaddress__district').', '.$this->native_session->get('permanentaddress__country'): '&nbsp;');?></td>
  </tr>
  <tr>
    <td class="previewlabel">Contact Address:</td>
    <td class='value'><?php echo ($this->native_session->get('contactaddress')? $this->native_session->get('contactaddress').', '.($this->native_session->get('contactaddress__county')? $this->native_session->get('contactaddress__county').', ': '').$this->native_session->get('contactaddress__district').', '.$this->native_session->get('contactaddress__country'): '&nbsp;');?></td>
  </tr>
  <tr>
    <td class="previewlabel">Country of Citizenship:</td>
    <td class='value'><?php echo ($this->native_session->get('citizenship__country')? $this->native_session->get('citizenship__country'): '&nbsp;').($this->native_session->get('citizenship__citizentype')? " (".$this->native_session->get('citizenship__citizentype').")": '');?></td>
  </tr>
</table></td></tr>
     <tr><td style="padding-top:30px;">&nbsp;</td></tr>
     
     
     
     
     <tr><td class="greybg" style="padding-left:5px; vertical-align:top;"><div id="editstep3" class="editicon nextdiv" style="min-width:30px;"></div><div class="nextdiv h3" style="vertical-align:top; padding-top:2px;">Education &amp; Qualifications</div></td></tr>
     <tr><td><div id="institution_list">
     <?php
	 if($this->native_session->get('education_list')){
		 $this->load->view('addons/basic_addons', array('area'=>'education_list', 'mode'=>'preview'));
	 } else {
	 ?>
     <table border="0" cellspacing="0" cellpadding="0" class="resultslisttable">
     <tr><td>Education List</td></tr>
     <tr><td>No education added yet.</td></tr>
     </table>
     <?php }?>
     </div></td></tr>
     <tr><td>&nbsp;</td></tr>
     
     <tr><td><div id="subject_list">
     <?php
	 if($this->native_session->get('subject_list')){
		 $this->load->view('addons/basic_addons', array('area'=>'subject_list', 'mode'=>'preview'));
	 } else {
	 ?>
     <table border="0" cellspacing="0" cellpadding="0" class="resultslisttable">
     <tr><td>Subjects Taught</td></tr>
     <tr><td>No subject added.</td></tr>
     </table>
     <?php }?>
     </div></td></tr>
     <tr><td>&nbsp;</td></tr>
     
     <tr><td><div id="document_list">
     <?php
	 if($this->native_session->get('document_list')){
		 $this->load->view('addons/basic_addons', array('area'=>'document_list', 'mode'=>'preview'));
	 } else {
	 ?>
     <table border="0" cellspacing="0" cellpadding="0" class="resultslisttable">
     <tr><td>Qualification Documents</td></tr>
     <tr><td>No document added.</td></tr>
     </table>
     <?php }?>
     </div></td></tr>
     <tr><td style="padding-top:30px;">&nbsp;</td></tr>
     
     <tr>
      <td>
     <table width="100%" border="0" cellspacing="0" cellpadding="0" class='buttonnav'>
     <tr>
     <td><button type="button" name="backtostep3" id="backtostep3" class="greybtn back">BACK</button></td>
     <td class='spacefiller'>&nbsp;</td>
     <td>&nbsp;</td>
     <td><input type="submit" name="step4" id="step4" value="SUBMIT" class="btn" /></td>
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