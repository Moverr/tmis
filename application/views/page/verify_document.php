<?php $msg = empty($msg)? get_session_msg($this): $msg; ?>
<!DOCTYPE html>
<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">
<link rel="icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">

<title><?php echo SITE_TITLE;?>: Verify Document</title>

<!-- Stylesheets -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.mobile.css" media="(max-width:790px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.tablet.css" media="(min-width:791px) and (max-width: 900px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.desktop.css" media="(min-width:901px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.list.css"/>

<!-- Javascript -->
<?php echo minify_js('page-verify_document', array('jquery-2.1.1.min.js', 'jquery.form.js', 'tmis.js', 'tmis.fileform.js', 'tmis.responsive.js'));?>
</head>

<body style="margin:0px;">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <?php $this->load->view("addons/public_header", array("page"=>"verify"));?>
  <tr>
    <td valign="top" colspan="2" class="bodyspace">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
      <td class="h1 grey">Verify Document</td>
     </tr>
     <?php echo !empty($msg)?"<tr><td>".format_notice($this,$msg)."</td></tr>": "";?>
     <tr>
      <td style="text-align:center;"><div style="display:inline-block;"><table border="0" cellspacing="0" cellpadding="10" id="verifydocument__form" class='simpleform'>
  <tr><td>
    <div class="nextdiv label" style="width:120px; height:30px;">Document Type: </div>
    <div class="nextdiv"><input type="text" id="documenttype__documenttypes" name="documenttype__documenttypes" placeholder="Select Document Type" class="textfield selectfield" value="" style="width:230px;"/><input type='hidden' id='documenttype' name='documenttype' value='' /></div>
    </td>
  </tr>
  <tr>
    <td>
    <div class="nextdiv label" style="width:120px; height:30px;">Tracking Number: </div>
    <div class="nextdiv"><input type="text" id="trackingnumber" name="trackingnumber" placeholder="Enter Number" class="textfield numbersonly" value="" style="width:260px;"/></div>
    </td>
  </tr>
  <tr>
    <td style="text-align:center;"><button type="button" class="greybtn submitbtn" id="checktracking" name="checktracking" style="width:93%;">VALIDATE TRACKING NUMBER</button>
    <input type='hidden' id='verifydocument__type' name='verifydocument__type' value='verify_document' />
    <input type='hidden' id='verifydocument__ignorepostprocessing' name='verifydocument__ignorepostprocessing' value='verify_document' />
    </td>
  </tr>
  <tr>
    <td><div id='verifydocument__resultsdiv'></div></td>
  </tr>
      </table>
      </div></td>
     </tr>
     </table>
    </td>
  </tr>
  <?php $this->load->view("addons/public_footer");?>
</table>


</body>
</html>