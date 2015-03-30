<!DOCTYPE html>
<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">
<link rel="icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">

<title><?php echo SITE_TITLE;?>: Previous Schools</title>

<!-- Stylesheets -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/jquery-ui.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.mobile.css" media="(max-width:790px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.tablet.css" media="(min-width:791px) and (max-width: 900px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.desktop.css" media="(min-width:901px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.list.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.menu.css"/>

<!-- Javascript -->
<?php echo minify_js('school-previous', array('jquery-2.1.1.min.js', 'jquery-ui.js', 'jquery.form.js', 'tmis.js', 'tmis.fileform.js', 'tmis.menu.js', 'tmis.responsive.js'));?>
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
      		<tr><td class="h1 grey">Previous Schools</td></tr>
            <?php echo !empty($msg)?"<tr><td>".format_notice($this,$msg)."</td></tr>": "";?>
            <tr><td>
            
<?php
if(!empty($list))
{
	echo "<table width='100%' border='0' cellspacing='0' cellpadding='0' class='listtable'>
	<tr class='header'><td>School</td><td>Location</td><td>Job Name(s)</td><td>Start Date</td><td>End Date</td></tr>";
	foreach($list AS $row)
	{
		echo "<tr class='listrow'><td>".$row['school']."</td><td>".$row['addressline']." ".$row['county']." <br>".$row['district'].", ".$row['country']."</td><td>".$row['jobs']."</td><td>".date('d-M-Y', strtotime($row['startdate']))."</td><td>".((!empty($row['enddate']) && $row['enddate'] != '0000-00-00')? date('d-M-Y', strtotime($row['enddate'])): 'UNKNOWN')."</td></tr>";
	}
	echo "</table>";
}
else
{
	echo format_notice($this, 'WARNING: There are no previous schools yet');
}

?>

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