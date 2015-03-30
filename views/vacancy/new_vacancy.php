<?php 
$forward = check_access($this, 'publish_job_notices', 'boolean', false)? 'vacancy/lists/action/publish': get_user_dashboard($this, $this->native_session->get('__user_id'));
?>
<!DOCTYPE html>
<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">
<link rel="icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">

<title><?php echo SITE_TITLE;?>: <?php echo !empty($id)? 'Edit': 'New';?> Job</title>

<!-- Stylesheets -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/jquery-ui.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.mobile.css" media="(max-width:790px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.tablet.css" media="(min-width:791px) and (max-width: 900px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.desktop.css" media="(min-width:901px)" />

<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.list.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.menu.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/trumbowyg.css"/>
 
<!-- Javascript -->
<?php echo minify_js('vacancy-new_vacancy', array('jquery-2.1.1.min.js', 'jquery-ui.js', 'jquery.form.js', 'tmis.js', 'tmis.fileform.js', 'tmis.menu.js', 'tmis.responsive.js', 'trumbowyg.js'));?>
<script type="text/javascript">
<?php 
echo !empty($id) && !empty($result['boolean']) && $result['boolean']?"window.top.location.href = '".base_url().$forward."';": "";?>
</script>
</head>

<body style="margin:0px;">
<?php 
# Do not show the header, menu and footer when editing
if(empty($id)) {?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <?php $this->load->view("addons/secure_header");?>
  <tr>
    <td valign="top" colspan="2" style="padding-top:0px;padding-left:15px;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td id="menucontainer"><?php $this->load->view("addons/menu");?></td>
        <td class="bodyspace" style="padding-left:15px;padding-top:15px; vertical-align:top;">
<?php }?>		

<table width="100%" border="0" cellspacing="0" cellpadding="0">
      		<tr><td class="h1 grey"><?php echo !empty($id)? 'Edit': 'New';?> Job</td></tr>
            <?php echo !empty($msg)?"<tr><td>".format_notice($this,$msg)."</td></tr>": "";?>
            <tr><td>
            
            
            <form id="user_data" method="post" autocomplete="off" action="<?php echo base_url().'vacancy/add'.(!empty($id)? '/id/'.$id: '');?>" class='simplevalidator'>
            <table border="0" cellspacing="0" cellpadding="10">
  <tr>
    <td class="label">Institution:</td>
    <td style="padding-right:42px;"><?php if(!empty($id) && $this->native_session->get('institution__institutions')){
		echo "<div class='value'>".$this->native_session->get('institution__institutions')."</div>";
		} else {?><input type="text" id="institution__institutions" name="institution__institutions" title="Select or Search for Instution" placeholder="Select or Search for Institution" class="textfield selectfield searchable" value="<?php echo $this->native_session->get('institution__institutions');?>" style="width:97%;"/><?php }?></td>
  </tr>
  <tr>
    <td class="label">Duty:</td>
    <td style="padding-right:42px;"><?php if(!empty($id) && $this->native_session->get('role__jobroles')){
		echo "<div class='value'>".$this->native_session->get('role__jobroles')."</div>";
		} else {?><input type="text" id="role__jobroles" name="role__jobroles" title="Select Duty" placeholder="Select Job Duty" class="textfield selectfield" value="<?php echo $this->native_session->get('role__jobroles');?>" style="width:97%;"/><?php }?></td>
  </tr>
  <tr>
    <td class="label">Headline:</td>
    <td><input type="text" id="headline" name="headline" title="Headline" class="textfield" style="width:97%;" value="<?php echo $this->native_session->get('headline');?>"/></td>
  </tr>
  <tr>
    <td class="label top">Summary:</td>
    <td><textarea id="summary" name="summary" title="Job Summary" class="textfield" style="width:97%; height:60px;"><?php echo $this->native_session->get('summary');?></textarea></td>
  </tr>
  <tr>
    <td class="label top">Details:</td>
    <td style="padding-top:0px;"><textarea id="details" name="details" title="Job Details" class="textfield" placeholder="Enter the job details here."><?php echo $this->native_session->get('details');?></textarea></td>
  </tr>
  <tr>
    <td class="label">Publish Duration:</td>
    <td><div class="nextdiv"><input type="text" id="publishstart" name="publishstart" title="Start Date" placeholder="Start Date" maxlength="20" class="textfield datefield" value="<?php echo $this->native_session->get('publishstart');?>"/></div><div class="nextdiv"><input type="text" id="publishend" name="publishend" title="End Date" placeholder="End Date" maxlength="20" class="textfield datefield" value="<?php echo $this->native_session->get('publishend');?>"/></div></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><input type="submit" name="save" id="save" class="btn" value="SAVE" /><?php echo !empty($id)? "<input type='hidden' id='vacancyid' name='vacancyid' value='".$id."' />": "";
	
	echo  empty($id)? "<input type='hidden' id='forwardurl' name='forwardurl' value='".$forward."' />": "";?></td>
  </tr>
            </table>
            </form>
            
            </td></tr>
        </table>
 
 <?php 
# Do not show the header and footer when editing
if(empty($id)) {?>       
        </td>
      </tr>
     </table>
    </td>
  </tr>
  <?php $this->load->view("addons/secure_footer");?>
</table>
<?php } else {echo "<input type='hidden' id='layerid' name='layerid' value='' />";}?>

<script type="text/javascript">
$(function(){	
	var btnsGrps = jQuery.trumbowyg.btnsGrps;
	$('#details').trumbowyg({btns: ['formatting',
           '|', btnsGrps.design,
           '|', 'link',
           '|', btnsGrps.justify,
           '|', btnsGrps.lists,
           '|', 'insertHorizontalRule']
	});
});
</script>
</body>
</html>