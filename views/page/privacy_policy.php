<!DOCTYPE html>
<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">
<link rel="icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">

<title><?php echo SITE_TITLE;?>: Privacy Policy</title>

<!-- Stylesheets -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.mobile.css" media="(max-width:790px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.tablet.css" media="(min-width:791px) and (max-width: 900px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.desktop.css" media="(min-width:901px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.list.css"/>

<!-- Javascript -->
<?php echo minify_js('page-privacy_policy', array('jquery-2.1.1.min.js', 'jquery.form.js', 'tmis.js', 'tmis.fileform.js', 'tmis.responsive.js'));?>
</head>

<body style="margin:0px;">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <?php $this->load->view("addons/public_header", array("page"=>"privacy"));?>
  <tr>
    <td valign="top" colspan="2" class="bodyspace">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
      <td class="h1 grey">Privacy Policy</td>
     </tr>
     <tr>
      <td>
<span class='h2'>SECTION 1 - WHAT DO WE DO WITH YOUR INFORMATION?</span>
<br>When you register at our website, as part of the verification process, we collect the personal information you give us such as your name, address and email address. When you browse our website, we also automatically receive your computer’s internet protocol (IP) address in order to provide us with information that helps us learn about your browser and operating system.
<br><br>

<span class='h2'>SECTION 2 - CONSENT</span>
<br><span class='h2'>How do you get my consent?</span>
<br>When you provide us with personal information to complete a registration, verify your identity or request a service, we imply that you consent to our collecting it and using it for that specific reason only.
If we ever ask for your personal information for a secondary reason, like marketing, we will either ask you directly for your expressed consent, or provide you with an opportunity to say no.
<br>
<br><span class='h2'>How do I withdraw my consent?</span>
<br>If after you opt-in, you change your mind, you may withdraw your consent for us to contact you, for the continued collection, use or disclosure of your information, at anytime, by contacting us using our contact information on the contact us page.
<br><br>

<span class='h2'>SECTION 3 - DISCLOSURE</span>
<br>We may disclose your personal information if we are required by law to do so or if you violate our Terms of Service.
<br><br>

<span class='h2'>SECTION 4 – AMAZON.COM</span>
<br>Our website is hosted on AMAZON.COM with other failover servers on 1AND1.COM. These servers may not be located in the same region as you or even in Uganda. They provide us with the online hosting platform (the Cloud) that allows us to provide services to you.
Your data is stored through Amazon.com’s data storage services and applications. They store your data on a secure server behind a firewall.
<br><br>

<span class='h2'>SECTION 5 - THIRD-PARTY SERVICES</span>
<br>In general, the third-party providers, if used, will only collect, use and disclose your information to the extent necessary to allow them to perform the services they provide to us.
<br><br>
However, certain third-party service providers, such as payment gateways and other payment transaction processors, have their own privacy policies in respect to the information we are required to provide to them for your purchase-related transactions if payment is ever required.
<br>
For these providers, we recommend that you read their privacy policies so you can understand the manner in which your personal information will be handled by these providers. In particular, remember that certain providers may be located in or have facilities that are located a different jurisdiction than either you or us. So if you elect to proceed with a transaction that involves the services of a third-party service provider, then your information may become subject to the laws of the jurisdiction(s) in which that service provider or its facilities are located.
<br><br>

<span class='h2'>Links</span>
<br>When you click on links on our website, they may direct you away from our site. We are not responsible for the privacy practices of other sites and encourage you to read their privacy statements.
<br><br>

<span class='h2'>SECTION 6 - SECURITY</span>
<br>To protect your personal information, we take reasonable precautions and follow industry best practices to make sure it is not inappropriately lost, misused, accessed, disclosed, altered or destroyed.
If you provide us with your personal information, the information is encrypted using secure socket layer technology (SSL) and stored with a AES-256 encryption.  Although no method of transmission over the Internet or electronic storage is 100% secure, we follow all standard security requirements to ensure that your data is protected.
<br><br>

<span class='h2'>SECTION 7 - AGE OF CONSENT</span>
<br>By using this site, you represent that you are at least the age of majority in your country, state or province of residence, or that you are the age of majority in your state or province of residence and you have given us your consent to allow any of your minor dependents to use this site.
<br><br>

<span class='h2'>SECTION 8 - CHANGES TO THIS PRIVACY POLICY</span>
<br>We reserve the right to modify this privacy policy at any time, so please review it frequently. Changes and clarifications will take effect immediately upon their posting on the website. If we make material changes to this policy, we will notify you here that it has been updated, so that you are aware of what information we collect, how we use it, and under what circumstances, if any, we use and/or disclose it.
<br><br>

<span class='h2'>QUESTIONS AND CONTACT INFORMATION</span>
<br>If you would like to: access, correct, amend or delete any personal information we have about you, register a complaint, or simply want more information contact our Privacy Compliance Officer at <a href='mailto:security@tmis.go.ug'>security@tmis.go.ug</a>.
<br><br>
<br><span class='value'>LAST UPDATED: 07-FEB-2015</span>
      </td>
     </tr>
     </table>
    </td>
  </tr>
  <?php $this->load->view("addons/public_footer", array("page"=>"privacy"));?>
</table>


</body>
</html>