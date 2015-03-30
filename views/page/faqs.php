<!DOCTYPE html>
<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">
<link rel="icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">

<title><?php echo SITE_TITLE;?>: FAQs</title>

<!-- Stylesheets -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.mobile.css" media="(max-width:790px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.tablet.css" media="(min-width:791px) and (max-width: 900px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.desktop.css" media="(min-width:901px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.list.css"/>

<!-- Javascript -->
<?php echo minify_js('page-faqs', array('jquery-2.1.1.min.js', 'jquery.form.js', 'tmis.js', 'tmis.fileform.js', 'tmis.responsive.js'));?>
</head>

<body style="margin:0px;">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <?php $this->load->view("addons/public_header", array("page"=>"faqs"));?>
  <tr>
    <td valign="top" colspan="2" class="bodyspace">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
      <td class="h1 grey">Frequently Asked Questions (FAQs)</td>
     </tr>
     <tr>
      <td style="padding-top:10px;">
<span class="h3">What is TMIS?</span><br>
The Teacher Management Information System (TMIS) is a simple user-friendly solution to supporting a harmonized and timely access to teacher information at all administrative levels of the Ministry of Education and associated agencies; at national, district and education institution levels. Its main objective is to solve the problem of dis-jointed teacher records accross the various sections of government and the education ministry. By enabling teachers to take control of their records and increasing transparency in the ministry's provision of services, it is speeding up service delivery, improving the ministry's efficiency and the relationship of the teaching professionals with government.
<br><br>
<span class="h3">Why do I need to register with TMIS?</span><br>
If you are a teacher, your record will need to be in the system to be matched across the various organizations in the ministry. Registering on TMIS provides you with the benefit of a faster service for your requests such as issuance of certificates, requesting transfers, leave application and many more. It is a service that will be indispensible for a teacher's interaction with the ministry and you are therefore encouraged to register earlier so that your application is approved sooner.
<br><br>
<span class="h3">Who owns TMIS?</span><br>
TMIS is owned by the Ugandan Ministry of Education and Sports (MoES). It has full control over your access and service provision as detailed in the <a href='<?php echo base_url();?>page/terms_of_use'>terms of use</a>. TMIS was developed with the support of The United Nations Educational, Scientific and Cultural Organization (UNESCO); a specialized agency of the United Nations.
<br><br>
<span class="h3">How do I get TMIS access?</span><br>
To get access to the services of TMIS, you have to be a teacher in a government sponsored school or a professional in the education ministry of Uganda. Teachers register on the system through a 4-stage form that begins at the home page. Other non-teacher education professionals are encouraged to apply for approval using the <a href='<?php echo base_url();?>account/apply'>signup link</a> on our website.
<br><br>
<span class="h3">I am stuck. Where can I find help?</span><br>
If you are stuck and can not complete a process on the website, you are encouraged to send our admin team a message using our <a href='<?php echo base_url();?>page/contact_us'>contact us form</a> any time of day. Inquiries will be handled depending on urgency and first-come-first-served basis. Our admin team determines the urgency of the matter or inquiry. You are advised to send only relevant messages and be patient for a response. Additional inquiries on the same matter do not speed up our response to you.

<br><br>
We thank you for using the Teacher Management Information System (TMIS) to improve our service delivery.
      
      </td>
     </tr>
     </table>
    </td>
  </tr>
  <?php $this->load->view("addons/public_footer");?>
</table>


</body>
</html>