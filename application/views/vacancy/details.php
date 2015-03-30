<?php $msg = empty($msg)? get_session_msg($this): $msg; ?>
<!DOCTYPE html>
<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo SITE_TITLE;?>: Details</title>

<!-- Stylesheets -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/jquery-ui.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.mobile.css" media="(max-width:790px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.tablet.css" media="(min-width:791px) and (max-width: 900px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.desktop.css" media="(min-width:901px)" />

<!-- Javascript -->
<?php echo minify_js('vacancy-details', array('jquery-2.1.1.min.js', 'jquery-ui.js', 'jquery.form.js', 'tmis.js', 'tmis.fileform.js'));?>
</head>

<body style="margin:0px;">
<table width="100%" border="0" cellspacing="0" cellpadding="5">
<?php 
echo !empty($msg)?"<tr><td>".format_notice($msg)."</td></tr>":"";
	
# Show the vancancy details
if(!empty($details))
{
	echo "<tr><td class='h2' style='padding-top:0px;'><b>".$details['topic']."</b></td></tr>
	<tr><td class='h3 value'><div class='nextdiv'>ROLE: ".$details['role_name']."</div><div class='lastdiv'>AT: ".$details['institution_name']."</div></td></tr>
	<tr><td>".str_replace("href=", " target='_blank' href=", html_entity_decode($details['details'], ENT_QUOTES))."</td></tr>
	<tr><td style='padding-top:20px;'><div class='leftnote h3 value'>Respond By: ".date('d-M-Y', strtotime($details['end_date']))."</div></td></tr>";
	
	# Show the apply button
	if(check_access($this, 'apply_for_job', 'boolean') || !$this->native_session->get('__user_id'))
	{
		echo "<tr><td style='padding-top:20px;text-align:center;'><div id='apply_btn_div'><input type='button' id='applyforjob' name='applyforjob' style='width:200px;' value='APPLY' ";
	
		if($this->native_session->get('__user_id')){
			echo " class='btn frompop' data-rel='".base_url()."job/apply/action/confirm/id/".$details['id']."' ";
		} else {
			echo " class='btn' onclick=\"updateFieldLayer('".base_url()."job/apply/id/".$details['id']."','','apply_btn_div','application_option_div','')\" ";
		}
	
		echo " /></div><div id='application_option_div' style='display:none;'></div></td></tr>";
	}
}	
?>
</table>
<input type="hidden" id="layerid" name="layerid" value="" />
</body>
</html>