<?php 
$forwardUrl = !empty($forward)? $forward: get_user_dashboard($this, $this->native_session->get('__user_id'));
?>
<!DOCTYPE html>
<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">
<link rel="icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">

<title><?php echo SITE_TITLE;?>: <?php echo !empty($id)? 'Edit': 'New';?> Group</title>

<!-- Stylesheets -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/jquery-ui.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.mobile.css" media="(max-width:790px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.tablet.css" media="(min-width:791px) and (max-width: 900px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.desktop.css" media="(min-width:901px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.list.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.menu.css"/>

<!-- Javascript -->
<?php echo minify_js('permission-new_group', array('jquery-2.1.1.min.js', 'jquery-ui.js', 'jquery.form.js', 'tmis.js', 'tmis.fileform.js', 'tmis.menu.js', 'tmis.responsive.js'));?>
<script type="text/javascript">
<?php echo !empty($id) && !empty($result['boolean']) && $result['boolean']?"window.top.location.href = '".$forwardUrl."';": "";?>
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
      		<tr><td class="h1 grey"><?php echo !empty($id)? (!empty($action) && $action=='view'? 'View':'Edit'): 'New';?> Group</td></tr>
            <?php echo !empty($msg)?"<tr><td>".format_notice($this,$msg)."</td></tr>": "";?>
            <tr><td>
            
            
            <form id="group_data" method="post" autocomplete="off" action="<?php echo base_url().'permission/add_group'.(!empty($id)? '/id/'.$id: '');?>" class='simplevalidator'>
            <table border="0" cellspacing="0" cellpadding="10">
  
  <tr>
    <td class="label">Group Name:</td>
    <td style="padding-right:42px;"><?php if(!empty($action)){
		echo "<div class='value'>".$this->native_session->get('groupname')."</div>";
		} else {?><input type="text" id="groupname" name="groupname" title="Group Name" class="textfield" style="width:97%;" value="<?php echo $this->native_session->get('groupname');?>"/><?php }?></td>
  </tr>
  <tr>
    <td class="label top">Permissions:</td>
    <td><?php 
		echo "<div style='max-height:200px; overflow-y: auto; overflow-x: hidden;'>
		<table border='0' cellspacing='0' cellpadding='0' class='listtable'>";
		
		if(!empty($permission_list))
		{
			echo "<tr class='header'><td>Category</td><td>Permission</td><td nowrap>Is Default</td></tr>";
			foreach($permission_list AS $permission)
			{
				echo "<tr class='listrow'><td>".ucfirst(str_replace('_', ' ', $permission['category']))."</td>
					
				<td><input type='checkbox' name='permission[]' id='permission_".$permission['id']."' value='".$permission['id']."' ".($this->native_session->get('permission') && in_array($permission['id'], $this->native_session->get('permission'))? ' checked': '')."><label for='permission_".$permission['id']."' style='white-space: nowrap;'>".$permission['permission']."</label></td>
					
				<td><input type='radio' onclick=\"selectAll(this,'*permission_".$permission['id']."')\" name='default' id='default_".$permission['id']."' value='".$permission['id']."' ".($this->native_session->get('default') && $this->native_session->get('default') == $permission['id']? ' checked': '')."><label for='default_".$permission['id']."'>Default</label></td></tr>";
			}
		} else{
			echo "<tr><td>".format_notice($this,'ERROR: There are no permissions to show.')."</td></tr>";
		}
		echo "</table></div>";	
	    ?></td>
  </tr>
  <tr>
    <td class="label">For System Use Only:</td>
    <td><?php if(!empty($action)){
		echo "<div class='value'>".strtoupper($this->native_session->get('forsystem'))."</div>";
		} else {?>
        <div class="nextdiv"><input type="radio" name="forsystem" id="forsystem_no" value="no" <?php echo (!$this->native_session->get('forsystem') || ($this->native_session->get('forsystem') && $this->native_session->get('forsystem')=='no')? 'checked': '');?>>
       <label for="forsystem_no">NO</label></div>
       <div class="nextdiv"><input type="radio" name="forsystem" id="forsystem_yes" value="yes" <?php echo ($this->native_session->get('forsystem') && $this->native_session->get('forsystem')=='yes'? 'checked': '');?>>
       <label for="forsystem_yes">YES</label></div>
		
		<?php }?></td>
  </tr>
  <?php if(!(!empty($action) && $action=='view')) {?>
  <tr>
    <td>&nbsp;</td>
    <td><input type="submit" name="save" id="save" class="btn" value="SAVE" />
	<input type='hidden' id='errormessage' name='errormessage' value='Enter all required fields to continue including selecting a default permission.' /><?php 
	echo !empty($id)? "<input type='hidden' id='groupid' name='groupid' value='".$id."' />": "";
	echo "<input type='hidden' id='forward' name='forward' value='permission/update_group' />";
	?></td>
  </tr>
  <?php }?>
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
</body>
</html>