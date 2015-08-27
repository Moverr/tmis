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

<title><?php echo SITE_TITLE;?>: <?php echo !empty($id)? (!empty($action) && $action=='view'? 'View':'Edit'): 'New';?> School</title>

<!-- Stylesheets -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/jquery-ui.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.mobile.css" media="(max-width:790px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.tablet.css" media="(min-width:791px) and (max-width: 900px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.desktop.css" media="(min-width:901px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.list.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.menu.css"/>

<!-- Javascript -->
<?php echo minify_js('school-new_school', array('jquery-2.1.1.min.js', 'jquery-ui.js', 'jquery.form.js', 'tmis.js', 'tmis.callout.js', 'tmis.fileform.js', 'tmis.menu.js', 'tmis.responsive.js'));?>

<script type="text/javascript">
<?php echo !empty($id) && !empty($result['boolean']) && $result['boolean']?"window.top.location.href = '".$forwardUrl."';": "";?>
</script>
</head>

<body style="margin:0px;">
<?php
# Do not show the header, menu and footer when editing
if(empty($id)) {?>
<table width="100%" border="0"   cellpadding="0" >

  <?php $this->load->view("addons/secure_header");?>
  <tr>
    <td valign="top" colspan="2" style="padding-top:0px;padding-left:15px;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
        <td id="menucontainer"><?php $this->load->view("addons/menu");?></td>
        <td class="bodyspace" style="padding-left:15px;padding-top:15px; vertical-align:top;">
<?php }?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
      		<tr><td class="h1 grey"><?php echo !empty($id)? (!empty($action) && $action=='view'? 'View':'Edit'): 'New';?> School</td></tr>
            <?php echo !empty($msg)?"<tr><td>".format_notice($this,$msg)."</td></tr>": "";?>
          <tr><td>


            <form id="school_data" method="post" autocomplete="off" action="<?php echo base_url().'school/add'.(!empty($id)? '/id/'.$id: '');?>" class='simplevalidator'>
            <table border="0" cellspacing="0" cellpadding="10">

  <tr>
    <td class="label">Name:</td>
    <td style="padding-right:42px;"><?php if(!empty($id) && $this->native_session->get('schoolname') || (!empty($action) && $action=='view')){
		echo "<div class='value'>".$this->native_session->get('schoolname')."</div>";
		} else {?><input type="text" id="schoolname" name="schoolname" title="School Name" class="textfield" value="<?php echo $this->native_session->get('schoolname');?>"/><?php }?></td>
  </tr>
  <tr>
    <td class="label">School Type:</td>
    <td><?php if(!empty($id) && $this->native_session->get('schooltype__schooltypes') || (!empty($action) && $action=='view')){
		echo "<div class='value'>".$this->native_session->get('schooltype__schooltypes')."</div>";
		} else {?><input type="text" id="schooltype__schooltypes" name="schooltype__schooltypes" title="School Type" placeholder="Select School Type" class="textfield selectfield  selectfield-medium" value="<?php echo $this->native_session->get('schooltype__schooltypes');?>"/><?php }?></td>
  </tr>
  
  <tr>
    <td class="label">Date Registered:</td>
    <td><?php if(!empty($id) && $this->native_session->get('dateschoolregistered') || (!empty($action) && $action=='view')){
		echo "<div class='value'>".date('d-M-Y',strtotime($this->native_session->get('dateschoolregistered')))."</div>";
		} else {?><input type="text" id="dateschoolregistered" name="dateschoolregistered" title="Date Registered" class="textfield datefield history" value="<?php echo $this->native_session->get('dateschoolregistered')? date('d-M-Y',strtotime($this->native_session->get('dateschoolregistered'))):'';?>"/><?php }?></td>
  </tr>
  
  <tr> 
    <td class="label">Operation Status:</td>
    <td> <input type="radio" name="operation_status" value="Registered" style="cursor:pointer;"   checked='false' class='operation_status' id="registeredx" > Registered  <input type="text" id="schoolregistrationnumber" name="schoolregistrationnumber" title="Ministry of Service Code" dataval="registeredx" placeholder="Ministry of Service Code" class="textfield email optional radiooption" value="<?php echo $this->native_session->get('registration__number');?>"/> <br/> <br/>
         <input type="radio"  checked='false' name="operation_status" value="Registered"  style="cursor:pointer;"   class='operation_status' id="licencedx" > Licenced  &nbsp; &nbsp;<input type="text" id="schoollicencenumber" name="schoollicencenumber" title="Registration Licence Number" placeholder="Registration Licence Number" dataval="licencedx" class="textfield  optional radiooption" value="<?php echo $this->native_session->get('licence_number');?>"/>
    </td>
  </tr>

  <tr>
    <td class="label">Email Address:</td>
    <td><input type="text" id="schoolemailaddress" name="schoolemailaddress" title="School Email Address" placeholder="Optional" class="textfield email optional" value="<?php echo $this->native_session->get('schoolemailaddress');?>"/></td>
  </tr>

  <tr>
    <td class="label">Telephone:</td>
    <td><?php if(!empty($action) && $action=='view'){
		echo "<div class='value'>".$this->native_session->get('schooltelephone')."</div>";
		} else {?><input type="text" id="schooltelephone" name="schooltelephone" title="School Telephone"  placeholder="(e.g: 0782123456)" maxlength="10" class="textfield numbersonly telephone" value="<?php echo $this->native_session->get('schooltelephone');?>"/><?php }?></td>
  </tr>

  <tr>
    <td class="label top">Physical Address:</td>
    <td><?php if(!empty($id) && $this->native_session->get('physicaladddress') || (!empty($action) && $action=='view')){
		echo "<div class='value'>".
		$this->native_session->get('physicaladddress')." ".$this->native_session->get('schooladdress__county')." <br>".$this->native_session->get('schooladdress__district').", ".$this->native_session->get('schooladdress__country')."</div>";
		} else {?><input type="text" id="schooladdress" name="schooladdress" title="School Address" class="textfield placefield physical" value="<?php echo $this->native_session->get('schooladdress__addressline');?>"/><?php }?></td>
  </tr>
    
  <tr>
    <td class="label top">Contact Address:</td>
    <td><?php if(!empty($id) && $this->native_session->get('contactadddress') || (!empty($action) && $action=='view')){
    echo "<div class='value'>".
    $this->native_session->get('contactadddress')." ".$this->native_session->get('schooladdress__county')." <br>".$this->native_session->get('schooladdress__district').", ".$this->native_session->get('schooladdress__country')."</div>";
  } else {?><input type="text" id="contactadddress" name="contactadddress" title="Contact Address" class="textfield placefield physical" value="<?php echo $this->native_session->get('schooladdress__addressline');?>"/><?php }?> <label><input type="checkbox" /> Same as Above </label></td>
  </tr>
  
  <tr  >
    <td class="  label top">Particulars:</td>
    <td  style="" class="table_callout"><?php if(!empty($id) && $this->native_session->get('particulars') || (!empty($action) && $action=='view')){
    echo "<div class='value'>".
    $this->native_session->get('particulars')." </div>";
  } else {?>

    <input type="text" id="particulars__particulars" placeholder="Particulars" name="particulars" title="Particulars" class="textfield selectfield  selectfield-medium searchable tags" value="<?php echo $this->native_session->get('particulars');?>"/>
    <input type="hidden" id="hidden_particulars" value="" />

    <?php }?>
    </td>
  </tr>
   
  <tr >
    <td class="label top">Courses Offered:</td>
    <td class="table_callout" ><?php if(!empty($id) && $this->native_session->get('courses__offered') || (!empty($action) && $action=='view')){
    echo "<div class='value'>". $this->native_session->get('courses__offered')." </div>";
  } else {?>
  <input type="text" placeholder="Search for Courses" id="courses__courses" name="courseoffered" title="Courses Offered" class="textfield selectfield  selectfield-medium tags " value="<?php echo $this->native_session->get('courses__offered');?>"/>
  <!-- <input type="hidden" id="hidden_courses" value="" />
   -->
   <?php }?>
  </td>
 </tr>
 
 
  <tr>
    <td class="label top">Highest Class:</td>
    <td><?php if(!empty($id) && $this->native_session->get('highestclass') || (!empty($action) && $action=='view')){
    echo "<div class='value'>".$this->native_session->get('highestclass')."</div>";
  } else {?><input type="text" id="highestclass__highestclass" name="highestclass" title="Highest Class" class="textfield  selectfield selectfield-medium" placeholder="Highest Class" value="<?php echo $this->native_session->get('highestclasss');?>"/><?php }?></td>
 </tr>
 
 
 <tr> 
    <td class="label top">Distance to Nearest:</td>
    <td>
      <table>
        <tr><td>Primary  School</td> <td> <input type="text" placeholder="kms" id="schooladdress" name="schooladdress" title="School Address" class="textfield  numbersonly" value="<?php echo $this->native_session->get('schooladdress__addressline');?>"   placeholder="kms" style="width:50px;"/> </td> <td>Post Secondary School </td> <td>  <input placeholder="kms" type="text" id="schooladdress" name="schooladdress" title="School Address" class="textfield  numbersonly" value="<?php echo $this->native_session->get('schooladdress__addressline');?>"  style="width:50px;"/> </td> </tr>
        <tr><td>Post Primary School </td> <td> <input type="text" placeholder="kms" id="schooladdress" name="schooladdress"  style="width:50px;"   class="textfield  numbersonly" value="<?php echo $this->native_session->get('schooladdress__addressline');?>"/> </td> <td>DEO's Office</td> <td> <input type="text" id="schooladdress"  placeholder="kms" name="schooladdress" title="School Address" class="textfield  numbersonly" value="<?php echo $this->native_session->get('schooladdress__addressline');?>"  style="width:50px;"/> </td> </tr>
        </tr>
      </table>
   </td>
  </tr>
  
  
  <tr>
    <td class="label top">Major Water Source:</td>
    <td>
      <table>
        <tr><td> <input type="text" placeholder="Piped Water" id="piped_water" name="piped_water" title="Piped Water" class="textfield selectfield  physical" value="<?php echo $this->native_session->get('distancetosource');?>"     /> </td> <td>Distance to Source </td><td> <input type="text"   id="distancetosource" name="distancetosource" title="School Address" class="textfield  numbersonly" value="<?php echo $this->native_session->get('distancetosource');?>"   placeholder="kms" style="width:45px;"  /></td> </tr>
      </table>
    </td>
    </tr>
    <tr>
    <td class="label top">Major School Energy Source:</td>
    <td>
      <table>
        <tr><td> <input type="text" placeholder="Bio Gas" id="schooladdress" name="schooladdress" title="School Address" class="textfield  physical" value="<?php echo $this->native_session->get('schooladdress__addressline');?>"     /> </td>  </tr>
      </table>
   </td>
  </tr>


  <?php if(!(!empty($action) && $action=='view')) {?>
  <tr>
    <td>&nbsp;</td>
    <td><input type="submit" name="save" id="save" class="btn" value="SAVE" /><?php
	echo !empty($id)? "<input type='hidden' id='schoolid' name='schoolid' value='".$id."' />": "";
	echo "<input type='hidden' id='forward' name='forward' value='school/lists".(!empty($action) && $action!='view'? '/action/'.$action: '')."' />";
	?>
  </td>
  </tr>
  <?php }?>
  </table>
            
            </form>
            </td> 
 <?php
# Do not show the header and footer when editing
if(empty($id)) {?>
        </td>
      </tr>
     </table>
    
  <?php $this->load->view("addons/secure_footer");?>
</table>
<?php } else {echo "<input type='hidden' id='layerid' name='layerid' value='' />";}?>
</body>
</html>
