<!DOCTYPE html>
<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">
<link rel="icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">

<title><?php echo SITE_TITLE;?>: About Us</title>

<!-- Stylesheets -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.mobile.css" media="(max-width:790px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.tablet.css" media="(min-width:791px) and (max-width: 900px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.desktop.css" media="(min-width:901px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.list.css"/>

<!-- Javascript -->
<?php echo minify_js('page-about_us', array('jquery-2.1.1.min.js', 'jquery.form.js', 'tmis.js', 'tmis.fileform.js', 'tmis.responsive.js'));?>
</head>

<body style="margin:0px;">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <?php $this->load->view("addons/public_header", array("page"=>"about_us"));?>
  <tr>
    <td valign="top" colspan="2" class="bodyspace">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
      <td class="h1 grey">About TMIS</td>
     </tr>
     <tr>
      <td><div class="picturediv"><img src="<?php echo base_url();?>assets/images/minister_of_education.jpg" alt="Minister of Education Uganda" border="0"><br><span class="name">Ms. Jessica Alupo</span><br><span class="caption">Minister of Education and Sports</span></div>The Teacher Management Information System (TMIS) is a system aimed at streamlining the delivery of government services to teachers and other members of the teaching profession in Uganda. The TMIS is a simple user-friendly approach to supporting a harmonized and timely access to teacher information at all administrative levels of the Ministry of Education and associated agencies at national, district and education institution levels. Its main objective is to solve the problem of disjointed teacher records accross the various sections of government and the education ministry. 
<br>Development of this system was sponsored by The United Nations Educational, Scientific and Cultural Organization (UNESCO); a specialized agency of the United Nations and commissioned in 2014 by the Education Ministry - headed by the Minister of Education and Sports, Ms. Jessica Alupo.
<br><br>
The Ugandan Ministry of Education and Sports (MoES) has outlined in various Government policy documents goals that include increasing access and retention, reducing inequalities, improving relevance and quality of education in Uganda. According to the Teacher Issues in Sub-Sahara Africa (TISSA) report, Uganda faces challenges in the quantity and quality of teachers at all levels. The study also revealed that Uganda lacks reliable data on teachers with most of the administrative data being manually generated. To address these concerns, the UNESCO in collaboration with the MoES is implementing the CapEFA programme for teachers with the TMIS system being one of the components. 
<br><br>
The implementation of TMIS module is also an aspect of strengthening the Education Management Information System (EMIS) and broadening teacher data needs across several sections of the ministry. TMIS is to provide accurate information on demand for teachers at primary and secondary level. Therefore, the Ministry will be able to make accurate predictions and plans for training and recruitment of needed teaching resources. This will ensure that pre-service and in-service training needs are evidence-based by removing the guesswork and reliance on outdated census data. 
<br><br>
In addition, it is helping to speed up document processing and improving performance of the education officers in doing their job by enabling faster issuance of certificates and case followup. The Teacher Instructor Education and Training (TIET) section of the ministry is using information from the teachers to perform in-service training and planning hence improving the quality of teachers in Ugandan schools by ensuring that there are sufficient opportunities for upgrading pedagogical skills.
<br><br>
As old systems are being phased out, all teachers will be required to register with the TMIS system to receive service as well as follow up on their current and future service requests from the ministry. If the reader is a teacher or a member of the education community in Uganda, they are encouraged to start their registration process now by clicking on the REGISTER button below.
<br><br>
      <div style="padding:20px;text-align:center;"><button type="button" name="backtostep1" id="backtostep1" class="bigbtn">REGISTER</button></div>
      </td>
     </tr>
     </table>
    </td>
  </tr>
  <?php $this->load->view("addons/public_footer");?>
</table>


</body>
</html>