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

<title><?php echo SITE_TITLE;?>: <?php echo !empty($id)? (!empty($action) && $action=='view'? 'View':'Edit'): 'New';?> Census</title>

<!-- Stylesheets -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/jquery-ui.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.mobile.css" media="(max-width:790px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.tablet.css" media="(min-width:791px) and (max-width: 900px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.desktop.css" media="(min-width:901px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.list.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.menu.css"/>

<!-- Javascript -->
<?php echo minify_js('census-new_census', array('jquery-2.1.1.min.js', 'jquery-ui.js', 'jquery.form.js', 'tmis.js', 'tmis.callout.js', 'tmis.fileform.js', 'tmis.menu.js', 'tmis.responsive.js'));?>

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
      		<tr><td class="h1 grey"><?php echo !empty($id)? (!empty($action) && $action=='view'? 'View':'Edit'): 'New';?> Census</td></tr>
            <?php echo !empty($msg)?"<tr><td>".format_notice($this,$msg)."</td></tr>": "";?>
            <tr><td>


            <form id="census_data" method="post" autocomplete="off" action="<?php echo base_url().'census/add'.(!empty($id)? '/id/'.$id: '');?>" class='simplevalidator'>
            <table border="0" cellspacing="0" cellpadding="10">

  <tr>
    <td class="label">Teacher:</td>
    <td style="padding-right:42px;"><?php if(!empty($id)){
		echo "<div class='value'>".$this->native_session->get('teachername__teachers')."</div>";
		} else {?><input type="text" id="teachername__teachers" name="teachername__teachers" title="Select or Search for Teacher" placeholder="Select or Search for Teacher" class="textfield selectfield searchable" value="<?php echo $this->native_session->get('teachername__teachers');?>" style="width:95%;" /><input type='text' class="textfield" id='teacherid' name='teacherid' value='<?php echo $this->native_session->get('teacherid');?>' style="display:none;" /><?php }?></td>
  </tr>
  <tr>
    <td class="label">Census Period:</td>
    <td><div class='nextdiv'><input type="text" id="censusstart" name="censusstart" title="Census Start Date" class="textfield datefield history" placeholder="Census Start Date" value="<?php echo $this->native_session->get('censusstart')? date('d-M-Y',strtotime($this->native_session->get('censusstart'))):'';?>"/></div><div class='nextdiv'><input type="text" id="censusend" name="censusend" title="Census End Date" placeholder="Census End Date" class="textfield datefield history" value="<?php echo $this->native_session->get('censusend')? date('d-M-Y',strtotime($this->native_session->get('censusend'))): '';?>"/></div></td>
  </tr>
  <tr>




  <td class="label">Main Subject Specialization:</td>
  <td> <?php if(!empty($id)){
  echo "<div class='value'>".$this->native_session->get('subjectspecialization__subjectspecialization')."</div>";
} else {?>
 <input type="text" id="subjectspecialization__subjectspecialization" name="subjectspecialization__subjectspecialization" title="Select or Search for Main Subject Specialization" placeholder="Select or Search for Main Subject Specialization" class="textfield selectfield selectfield_multiple searchable" value="<?php echo $this->native_session->get('teachername__teachers');?>" style="width:95%;" />
 <input type='text' class="textfield" id='subjectspecialization' name='subjectspecialization' value='<?php echo $this->native_session->get('teacherid');?>' style="display:none;" /><?php }?></td>
  </tr>
<!--  <tr>
    <td class="label top">Responsibilities:</td>
    <td><?php
		echo "<div style='max-height:200px; overflow-y: auto; overflow-x: hidden;'>
		<table border='0' cellspacing='0' cellpadding='0' class='listtable'>";

		if(!empty($responsibility_list))
		{
			echo "<tr class='header'><td>Code</td><td>Name</td></tr>";
			foreach($responsibility_list AS $row)
			{
				echo "<tr class='listrow'><td>".$row['code']."</td>

				<td><input type='checkbox' name='responsibility[]' id='responsibility_".$row['id']."' value='".$row['id']."' ".($this->native_session->get('responsibility') && in_array($row['id'], $this->native_session->get('responsibility'))? ' checked': '')."><label for='responsibility_".$row['id']."' style='white-space: nowrap;'>".$row['responsibility']."</label></td>
				</tr>";
			}
		} else{
			echo "<tr><td>".format_notice($this,'ERROR: There are no responsibilities to show.')."</td></tr>";
		}
		echo "</table></div>";
	    ?></td>
  </tr> -->

  <tr>
    <td>&nbsp; </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td class="label top">Training:</td>
    <td><?php
		echo "<div style='max-height:200px; overflow-y: auto; overflow-x: hidden;'>
		<table border='0' cellspacing='0' cellpadding='0' class='listtable'>";

		if(!empty($training_list))
		{
			echo "<tr class='header'><td>Code</td><td>Name</td><td>Type</td></tr>";
			foreach($training_list AS $row)
			{
				echo "<tr class='listrow'><td>".$row['code']."</td>

				<td><input type='checkbox' name='training[]' id='training_".$row['id']."' value='".$row['id']."' ".($this->native_session->get('training') && in_array($row['id'], $this->native_session->get('training'))? ' checked': '')."><label for='training_".$row['id']."' style='white-space: nowrap;'>".$row['training']."</label></td>
				<td>".ucfirst($row['type'])."</td>
				</tr>";
			}
		} else{
			echo "<tr><td>".format_notice($this,'ERROR: There are no training items to show.')."</td></tr>";
		}
		echo "</table></div>";
	    ?></td>
  </tr>

  <tr>
    <td class="label">Average Weekly Workload:</td>
    <td><?php if(!empty($action) && $action=='view'){
		echo "<div class='value'>".$this->native_session->get('averageworkload')."</div>";
		} else {?><input type="text" id="averageworkload" name="averageworkload" title="Average Weekly Workload"  placeholder="Time in hours" maxlength="5" class="textfield numbersonly" value="<?php echo $this->native_session->get('averageworkload');?>"/><?php }?></td>
  </tr>


  <?php if(!(!empty($action) && $action=='view')) {?>
  <tr>
    <td>&nbsp;</td>
    <td><input type="submit" name="save" id="save" class="btn" value="SAVE" /><?php
	echo !empty($id)? "<input type='hidden' id='censusid' name='censusid' value='".$id."' />": "";
	echo "<input type='hidden' id='forward' name='forward' value='census/lists".(!empty($action) && $action!='view'? '/action/'.$action: '')."' />";
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
