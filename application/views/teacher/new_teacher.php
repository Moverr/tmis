<?php 
$forwardUrl = !empty($forward)? $forward: get_user_dashboard($this, $this->native_session->get('__user_id'));
$msg = empty($msg)? get_session_msg($this): $msg; ?>
<!DOCTYPE html>
<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">
<link rel="icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">

<title><?php echo SITE_TITLE;?>: <?php echo !empty($id)? (!empty($action) && $action=='view'? 'View':'Edit'): 'New';?> Teacher</title>

<!-- Stylesheets -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/jquery-ui.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.mobile.css" media="(max-width:790px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.tablet.css" media="(min-width:791px) and (max-width: 900px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.desktop.css" media="(min-width:901px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.list.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.menu.css"/>

<!-- Javascript -->
<?php echo minify_js('teacher-new_teacher', array('jquery-2.1.1.min.js', 'jquery-ui.js', 'jquery.form.js', 'tmis.js', 'tmis.callout.js', 'tmis.fileform.js', 'tmis.menu.js', 'tmis.responsive.js'));?>
<script type="text/javascript">
<?php echo !empty($id) && !empty($result['boolean']) && $result['boolean']?"window.top.location.href = '".$forwardUrl."';": "";?>
</script>
</head>

<body style="margin:0px;">
<?php 
# Do not show the header, menu and footer when editing
if(empty($id) || !empty($editing_teacher)) {?>
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
      		<tr><td class="h1 grey"><?php echo $this->native_session->get('is_teacher_updating')? 'My Teacher Profile': (!empty($id)? (!empty($action) && $action=='view'? 'View':'Edit'): 'New').' Teacher';?></td></tr>
            <?php echo !empty($msg)?"<tr><td>".format_notice($this,$msg)."</td></tr>": "";?>
            <tr><td>
            
            
<form id="teacher_data" method="post" autocomplete="off" action="<?php echo base_url().(!empty($editing_teacher)? 'profile/teacher_data': 'teacher/add'.(!empty($id)? '/id/'.$id: ''));?>" class='simplevalidator'>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
  <tr><td class="greybg h3" style="padding:5px; padding-left:10px;">Personal Information</td></tr>
  
<tr><td><table border="0" cellspacing="0" cellpadding="5" width="100%">
  <tr>
    <td class="label" style="width:150px;">Surname:</td>
    <td><?php if(!empty($preview)){ 
		echo "<span class='value'>".$this->native_session->get('lastname')."</span>";
	} else {?><input type="text" id="lastname" name="lastname" title="Surname" class="textfield" value="<?php echo ($this->native_session->get('lastname')? $this->native_session->get('lastname'): '');?>"/><?php }?></td>
    <td rowspan="8" style="vertical-align:top; padding:10px; text-align:right;"><?php 
	if(!empty($preview)){
		echo $this->native_session->get('photo')? "<img src='".base_url().'assets/uploads/images/'.$this->native_session->get('photo')."' style='max-height:110px;' border='0' /><br>": '';
	} else { echo "&nbsp;";}
	?></td>
  </tr>
  <tr>
    <td class="label">Other Names:</td>
    <td><?php if(!empty($preview)){ 
		echo "<span class='value'>".$this->native_session->get('firstname')."</span>";
	} else {?><input type="text" id="firstname" name="firstname" title="Other Names" class="textfield" value="<?php echo ($this->native_session->get('firstname')? $this->native_session->get('firstname'): '');?>"/><?php }?></td>
    </tr>
  <tr>
    <td class="label">Telephone:</td>
    <td><?php if(!empty($preview)){ 
		echo "<span class='value'>".$this->native_session->get('telephone')."</span>";
	} else {?><input type="text" id="telephone" name="telephone" title="Telephone" placeholder="Optional" maxlength="10" class="textfield numbersonly optional" value="<?php echo ($this->native_session->get('telephone')? $this->native_session->get('telephone'): '');?>"/><?php }?></td>
    </tr>
  <tr>
    <td class="label">Email Address:</td>
    <td><?php if(!empty($preview) || !empty($id)){ 
		echo "<span class='value'>".$this->native_session->get('emailaddress')."</span>";
		} else {?>
      <input type="text" id="emailaddress" name="emailaddress" title="Email Address" class="textfield email" value="<?php echo $this->native_session->get('emailaddress');?>"/>
      <?php }?></td>
    </tr>
  <tr>
    <td class="label">Gender:</td>
    <td><?php if(!empty($preview)){ 
		echo "<span class='value'>".ucfirst($this->native_session->get('gender'))."</span>";
	} else {?>
      <div class="nextdiv"><input type="radio" name="gender" id="gender_female" value="female" <?php echo ($this->native_session->get('gender') && $this->native_session->get('gender')=='female'? 'checked': '');?>>
        <label for="gender_female">Female</label></div>
      <div class="nextdiv"><input type="radio" name="gender" id="gender_male" value="male" <?php echo ($this->native_session->get('gender') && $this->native_session->get('gender')=='male'? 'checked': '');?>>
        <label for="gender_male">Male</label></div>
      <?php }?></td>
    </tr>
  <tr>
    <td class="label">Marital Status:</td>
    <td><?php if(!empty($preview)){ 
		echo "<span class='value'>".ucfirst($this->native_session->get('marital'))."</span>";
	} else {?>
      <div class="nextdiv"><input type="radio" name="marital" id="marital_married" value="married" <?php echo ($this->native_session->get('marital') && $this->native_session->get('marital')=='married'? 'checked': '');?>>
        <label for="marital_married">Married</label></div>
      <div class="nextdiv"><input type="radio" name="marital" id="marital_single" value="single" <?php echo ($this->native_session->get('marital') && $this->native_session->get('marital')=='single'? 'checked': '');?>>
        <label for="marital_single">Single</label></div>
      <?php }?></td>
    </tr>
  <tr>
    <td class="label">Birth Day:</td>
    <td><?php if(!empty($preview)){ 
		echo "<span class='value'>".($this->native_session->get('birthday') && $this->native_session->get('birthday') != '0000-00-00'? date('d-M-Y', strtotime($this->native_session->get('birthday'))): '&nbsp;')."</span>";
	} else {?>
      <input type="text" id="birthday" name="birthday" title="Birth Day" class="textfield datefield birthday" value="<?php echo format_date($this->native_session->get('birthday'), 'd-M-Y');?>" readonly/>
      <?php }?></td>
    </tr>
  <tr>
    <td class="label">Birth Place:</td>
    <td><?php if(!empty($preview)){ 
		echo "<span class='value'>".$this->native_session->get('birthplace__addressline')." ".$this->native_session->get('birthplace__county')." <br>".$this->native_session->get('birthplace__district').($this->native_session->get('birthplace__country')? ", ".$this->native_session->get('birthplace__country'): '')."</span>";
	} else {?><input type="text" id="birthplace" name="birthplace" title="Birth Place" class="textfield placefield physical" value="<?php echo ($this->native_session->get('birthplace__addressline')? $this->native_session->get('birthplace__addressline'): '');?>" readonly/><?php }?></td>
    </tr>
</table></td></tr>
    
     
     
     
     <tr><td class="greybg h3"  style="padding:5px; padding-left:10px;">Identification &amp; Contacts</td></tr>
     <tr><td><table border="0" cellspacing="0" cellpadding="5" width="100%">
  <tr>
    <td class="label" style="width:150px;">Teacher UTS Number:</td>
    <td><?php if(!empty($preview)){ 
		echo "<span class='value'>".$this->native_session->get('teacherid')."</span>";
	} else {?><input type="text" id="teacherid" name="teacherid" title="Teacher ID Number" class="textfield optional" placeholder="Optional" value="<?php echo ($this->native_session->get('teacherid')? $this->native_session->get('teacherid'): '');?>"/>
    <?php }?></td>
  </tr>
  <tr>
    <td class="label">Permanent Address:</td>
    <td><?php if(!empty($preview)){ 
		echo "<span class='value'>".$this->native_session->get('permanentaddress__addressline')." ".$this->native_session->get('permanentaddress__county')." <br>".$this->native_session->get('permanentaddress__district').($this->native_session->get('permanentaddress__country')? ", ".$this->native_session->get('permanentaddress__country'): '')."</span>";
	} else {?><input type="text" id="permanentaddress" name="permanentaddress" title="Permanent Address" class="textfield placefield" value="<?php echo ($this->native_session->get('permanentaddress')? $this->native_session->get('permanentaddress'): '');?>"/>
    <?php }?></td>
  </tr>
  <tr>
    <td class="label">Contact Address:</td>
    <td><?php if(!empty($preview)){ 
		echo "<span class='value'>".$this->native_session->get('contactaddress__addressline')." ".$this->native_session->get('contactaddress__county')." <br>".$this->native_session->get('contactaddress__district').($this->native_session->get('contactaddress__country')? ", ".$this->native_session->get('contactaddress__country'): '')."</span>";
	} else {?><div class="nextdiv"><input type="text" id="contactaddress" name="contactaddress" title="Contact Address" class="textfield placefield" value="<?php echo ($this->native_session->get('contactaddress')? $this->native_session->get('contactaddress'): '');?>"/></div>
    <div class="nextdiv"><input type="checkbox" id="permanentsameascontact" name="permanentsameascontact" value="Y" /><label for="permanentsameascontact">Same as above</label></div><?php }?></td>
  </tr>
  <tr>
    <td class="label">Country of Citizenship:</td>
    <td><?php if(!empty($preview)){ 
		echo "<span class='value'>".$this->native_session->get('citizenship__country').($this->native_session->get('citizenship__citizentype') && $this->native_session->get('citizenship__citizentype') != '_CITIZENSHIP_TYPE_'? " (".$this->native_session->get('citizenship__citizentype').")": "")."</span>";
	} else {?><div class="nextdiv"><input type="text" id="citizenship__country" name="citizenship__country" title="Country of Citizenship" placeholder="Select Country" class="textfield selectfield" value="<?php echo ($this->native_session->get('citizenship__country')? $this->native_session->get('citizenship__country'): '');?>"/></div>
    <div class="nextdiv"><input type="text" id="citizenship__citizentype" name="citizenship__citizentype" title="How Teacher Obtained Citizenship" placeholder="Citizen By" class="textfield selectfield" value="<?php echo ($this->native_session->get('citizenship__citizentype') && $this->native_session->get('citizenship__citizentype') != '_CITIZENSHIP_TYPE_'? $this->native_session->get('citizenship__citizentype'): '');?>"/></div><?php }?></td>
  </tr>
</table></td></tr>
     
     
<tr><td class="greybg h3"  style="padding:5px; padding-left:10px;">Education:</td></tr>
     <?php if(empty($preview)){?>
     <tr>
      <td><div id="form_div__education" class='ignorearea'><?php $this->load->view('addons/basic_addons', array('area'=>'education_form'));?></div></td>
     </tr>
     <?php }?>
     
     <tr><td><div id="institution_list">
     <?php
	 if($this->native_session->get('education_list')){
		 $this->load->view('addons/basic_addons', array('area'=>'education_list', 'mode'=>(!empty($preview)? 'preview': '') ));
	 } else {
	 ?>
     <table border="0" cellspacing="0" cellpadding="0" class="resultslisttable">
     <tr><td>Current List</td></tr>
     <tr><td>No education added yet.</td></tr>
     </table>
     <?php }?>
     </div></td></tr>
<tr><td>&nbsp;</td></tr>    
     
     
<tr><td class="greybg h3"  style="padding:5px; padding-left:10px;">Subjects Taught:</td></tr>
     
     <?php if(empty($preview)){?>
     <tr>
       <td><div id="form_div__subject" class='ignorearea'><?php $this->load->view('addons/basic_addons', array('area'=>'subject_form'));?></div></td>
       </tr>
     <?php }?>
     
     <tr><td><div id="subject_list">
     <?php
	 if($this->native_session->get('subject_list')){
		 $this->load->view('addons/basic_addons', array('area'=>'subject_list', 'mode'=>(!empty($preview)? 'preview': '') ));
	 } else {
	 ?>
     <table border="0" cellspacing="0" cellpadding="0" class="resultslisttable">
     <tr><td>Current List</td></tr>
     <tr><td>No subject added yet.</td></tr>
     </table>
     <?php }?>
     </div></td></tr>
<tr><td>&nbsp;</td></tr>     
     
     
<tr><td class="greybg h3"  style="padding:5px; padding-left:10px;">Qualification Documents:</td></tr>
     
     <?php if(empty($preview)){?>
     <tr>
       <td><div id="form_div__document" class='ignorearea'><?php $this->load->view('addons/basic_addons', array('area'=>'document_form'));?></div></td>
       </tr>
     <?php }?>
     
     <tr><td><div id="document_list">
     <?php
	 if($this->native_session->get('document_list')){
		 $this->load->view('addons/basic_addons', array('area'=>'document_list', 'mode'=>(!empty($preview)? 'preview': '') ));
	 } else {
	 ?>
     <table border="0" cellspacing="0" cellpadding="0" class="resultslisttable">
     <tr><td>Current List</td></tr>
     <tr><td>No document added yet.</td></tr>
     </table>
     <?php }?>
     </div></td></tr>
<tr><td>&nbsp;</td></tr> 


<?php if(!empty($preview) && !empty($documents)){?>
<tr><td class="greybg h3"  style="padding:5px; padding-left:10px;">Issued Documents List:</td></tr>
<?php 
echo "<tr><td>
<table class='listtable'>
<tr class='header'><td>Document</td><td>Date Issued</td></tr>";
foreach($documents AS $row)
{
	if($row['description'] == 'Transfer Pca') $row['description'] = 'Transfer PCA';
	
	echo "<tr class='listrow'><td><a href='".base_url()."page/download/folder/documents/file/".$row['url']."'>".$row['description']."</a></td><td>".date('d-M-Y h:ia T', strtotime($row['date_added']))."</td></tr>";
}

echo "</table></td></tr>";
}?>
     
     
     
<?php if(!(!empty($action) && $action=='view')){?>    
<tr><td>
     <table width="100%" border="0" cellspacing="0" cellpadding="0" class='buttonnav'>
     <tr>
     <td><?php if(empty($id) && empty($editing_teacher)) {?><button type="button" name="canceladd" id="canceladd" class="greybtn" onclick="location.href='<?php echo base_url().'teacher/cancel/action/view';?>'">CANCEL</button><?php }?></td>
     <td class='spacefiller'>&nbsp;</td>
     <?php if(!empty($preview)){?>
     <td><button type="button" name="edit" id="edit" class="btn" onclick="location.href='<?php echo base_url().(!empty($editing_teacher)? 'profile/teacher_data/edit/Y': 'teacher/add/edit/Y'.(!empty($id)? '/id/'.$id: ''));?>'">EDIT</button></td>
     <td><?php echo !empty($id)? "<input type='hidden' id='userid' name='userid' value='".$id."'/>": ''; ?><input type="submit" name="save" id="save" value="SUBMIT" class="btn" /></td>
     <?php } else { ?>
     <td>&nbsp;</td>
     <td><input type="submit" name="preview" id="preview" value="PREVIEW" class="btn" /></td>
     <?php } ?>
     </tr>
     </table></td></tr> 
<?php } ?> 
</table>
</form>
            
            </td></tr>
        </table>
 
 <?php 
# Do not show the header and footer when editing
if(empty($id) || !empty($editing_teacher)) {?>       
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