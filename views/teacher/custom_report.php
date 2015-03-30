<?php $msg = empty($msg)? get_session_msg($this): $msg; ?>
<!DOCTYPE html>
<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">
<link rel="icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">

<title><?php echo SITE_TITLE;?>: Custom Teacher Report</title>

<!-- Stylesheets -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/jquery-ui.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.mobile.css" media="(max-width:790px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.tablet.css" media="(min-width:791px) and (max-width: 900px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.desktop.css" media="(min-width:901px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.list.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.menu.css"/>

<!-- Javascript -->
<?php echo minify_js('teacher-custom_report', array('jquery-2.1.1.min.js', 'jquery-ui.js', 'jquery.form.js', 'tmis.js', 'tmis.fileform.js', 'tmis.menu.js', 'tmis.responsive.js'));?>
</head>

<body style="margin:0px;">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <?php $this->load->view("addons/secure_header");?>
  <tr>
    <td valign="top" colspan="2" style="padding-top:0px;padding-left:15px;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td id="menucontainer"><?php $this->load->view("addons/menu");?></td>
        <td class="bodyspace" style="padding-left:15px;padding-top:15px; vertical-align:top;">

<table width="100%" border="0" cellspacing="0" cellpadding="0">
      		<tr>
              <td><div class="h1 grey nowrap listheader">Custom Teacher Report</div></td></tr>
            <?php echo !empty($msg)?"<tr><td>".format_notice($this,$msg)."</td></tr>": "";?>
            <tr><td>


<table border="0" cellspacing="0" cellpadding="0" width="100%">
  <tr><td>
  <table width='100%' cellpadding='5' class='microform ignoreclear'>
		<tr> 
		<td width='1%' valign='top'><div class='label' style='text-align:left;'>Report Type:</div>
        <input type="text" id="reporttype__reporttypes" name="reporttype__reporttypes" title="Select Report Type" placeholder="Select Report Type" class="textfield selectfield" style="width:300px;" value="Number of Registered Teachers" /></td>
		
		<td width='99%' valign='top' style='padding-top:25px;'><button type='button' class='btn submitmicrobtn' name='generate' id='generate' value='GENERATE' style='width:110px;'>GENERATE</button>
		
		<input type='hidden' id='action' name='action' value='<?php echo base_url()?>teacher/custom_report' />
		<input type='hidden' id='resultsdiv' name='resultsdiv' value='customteacherreport_div' />
		<input type='hidden' id='errormessage' name='errormessage' value='All fields are required to continue.' />
		</td>
		</tr>
        
        <tr>
        <td colspan='2'><div class='label' style='text-align:left;'>Report Specification:</div>
        <div id='reportspecs'><input type="text" id="reportsubtype__registerednumbers" name="reportsubtype__registerednumbers" title="Select Specification" placeholder="Select Specification" class="textfield selectfield" style="width:300px;" value="By Gender" /></div></td>
        </tr>
        
		 <tr>
		   <td colspan='2' class='greybg smalltext'  style='padding:10px;'>Select the report variables and click Generate to view the report below.</td></tr>
		</table>
  </td></tr>
  
  
  
  
  
    
  <tr>
  	<td><div id='customteacherreport_div'></div></td>
  </tr>
</table>

            
            </td></tr>
        </table>
       
        </td>
      </tr>
     </table>
    </td>
  </tr>
  <?php $this->load->view("addons/secure_footer");?>
</table>
</body>
</html>