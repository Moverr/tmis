<!DOCTYPE html>
<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">
<link rel="icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">

<title><?php echo SITE_TITLE;?>: Welcome</title>
<!-- Stylesheets -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/jquery-ui.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.mobile.css" media="(max-width:790px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.tablet.css" media="(min-width:791px) and (max-width: 900px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.desktop.css" media="(min-width:901px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.list.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.shadowbox.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.pagination.css"/>
<!-- Javascript -->
<?php echo minify_js('home', array('jquery-2.1.1.min.js', 'jquery-ui.js', 'jquery.form.js', 'tmis.js', 'tmis.callout.js', 'tmis.fileform.js', 'tmis.responsive.js', 'tmis.list.js', 'tmis.shadowbox.js', 'tmis.pagination.js', 'tmis.search.js'));?>
</head>

<body style="margin:0px;">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <?php $this->load->view("addons/public_header", array("page"=>"home"));?>
  <tr>
    <td valign="top" class="leftcolumn"><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><div id="mobilebuttons_details"></div>
    <div id="mobilebuttons"><button type="button" name="backtostep1" id="backtostep1" class="bigbtn">REGISTER</button><br><br>
<button type="button" name="loginbtnbig" id="loginbtnbig" class="biggreybtn">LOGIN</button></div>
    <div class="h1 blue nowrap listheader">Job Notices</div><div class="listsearchfield"><input type="text" id="jobsearch__jobs" name="jobsearch__jobs" data-type="job" placeholder="Search Jobs" class="findfield" value=""/>
<input type='hidden' id='jobsearch__displaydiv' name='jobsearch__displaydiv' value='jobsearch__1' />
<input type='hidden' id='jobsearch__action' name='jobsearch__action' value='<?php echo base_url()."search/load_list/action/view";?>' />
</div></td>
    </tr>
  <tr>
    <td>
    <div id="listcontainer">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><div id="paginationdiv__jobsearch_list">
    <div id="jobsearch__1"><?php $this->load->view('job/list', array(
	'listid'=>'jobsearch',
	'list'=>(!empty($list)? $list: array()), 
	'action'=>(!empty($action)? $action: ''), 
	'msg'=>(!empty($msg)? $msg: '') 
	));?>
    </div></div></td>
  </tr>
  <?php if(!empty($list)){?>
  <tr>
    <td style="padding:40px 15px 10px 15px; "><div class='centerpagination' style="margin:0px;padding:0px;"><div id="jobsearch" class="paginationdiv"><div class="previousbtn" style='display:none;'>&#x25c4;</div><div class="selected">1</div><div class="nextbtn">&#x25ba;</div></div><input name="paginationdiv__jobsearch_action" id="paginationdiv__jobsearch_action" type="hidden" value="<?php echo base_url()."search/load_list/type/job/action/".(!empty($action)? $action: 'view');?>" />
<input name="paginationdiv__jobsearch_maxpages" id="paginationdiv__jobsearch_maxpages" type="hidden" value="<?php echo NUM_OF_LISTS_PER_VIEW;?>" />
<input name="paginationdiv__jobsearch_noperlist" id="paginationdiv__jobsearch_noperlist" type="hidden" value="<?php echo NUM_OF_ROWS_PER_PAGE;?>" />
<input name="paginationdiv__jobsearch_showdiv" id="paginationdiv__jobsearch_showdiv" type="hidden" value="paginationdiv__jobsearch_list" /></div></td>
  </tr>
  <?php }?>
</table>
</div>

<div id="jobslist_btn_only"><button type="button" name="jobslistbtn" id="jobslistbtn" class="btn" style="width:240px;">VIEW JOBS</button></div>
   
    </td>
    </tr>
</table>
</td>
    <td valign="top" class="rightcolumn"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="h1 grey">Teacher Registration</td>
        <td valign="top"><div id="registration_close_btn">&nbsp;</div></td>
      </tr>
      <tr>
        <td colspan="2" class="h2 grey">Registration helps you to speed up your benefit processing, leave application, transfer and many more career tracking options.</td>
      </tr>
      <tr>
        <td colspan="2"><div id="registration_form"><form id="home_registration_form" method="post" autocomplete="off" action="<?php echo base_url();?>register/step_one" class='simplevalidator'>
        <table border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="label">Surname:</td>
    <td><input type="text" id="lastname" name="lastname" title="Surname" class="textfield" value="<?php echo ($this->native_session->get('lastname')? $this->native_session->get('lastname'): '');?>"/></td>
  </tr>
  <tr>
    <td class="label">Other Names:</td>
    <td><input type="text" id="firstname" name="firstname" title="Other Names" class="textfield" value="<?php echo ($this->native_session->get('firstname')? $this->native_session->get('firstname'): '');?>"/></td>
  </tr>
  <tr>
    <td class="label">Telephone:</td>
    <td><input type="text" id="telephone" name="telephone" title="Telephone" placeholder="Optional (e.g: 0782123456)" maxlength="10" class="textfield numbersonly telephone optional" value="<?php echo ($this->native_session->get('telephone')? $this->native_session->get('telephone'): '');?>"/></td>
  </tr>
  <tr>
    <td class="label">Email Address:</td>
    <td><input type="text" id="emailaddress" name="emailaddress" title="Email Address" class="textfield email" value="<?php echo ($this->native_session->get('emailaddress')? $this->native_session->get('emailaddress'): '');?>"/></td>
  </tr>
  <tr>
    <td class="label">Gender:</td>
    <td><div class="nextdiv"><input type="radio" name="gender" id="gender_female" value="female" <?php echo ($this->native_session->get('gender') && $this->native_session->get('gender')=='female'? 'checked': '');?>>
       <label for="gender_female">Female</label></div>
       <div class="nextdiv"><input type="radio" name="gender" id="gender_male" value="male" <?php echo ($this->native_session->get('gender') && $this->native_session->get('gender')=='male'? 'checked': '');?>>
       <label for="gender_male">Male</label></div></td>
  </tr>
  <tr>
    <td class="label">Marital Status:</td>
    <td><div class="nextdiv"><input type="radio" name="marital" id="marital_married" value="married" <?php echo ($this->native_session->get('marital') && $this->native_session->get('marital')=='married'? 'checked': '');?>>
       <label for="marital_married">Married</label></div>
       <div class="nextdiv"><input type="radio" name="marital" id="marital_single" value="single" <?php echo ($this->native_session->get('marital') && $this->native_session->get('marital')=='single'? 'checked': '');?>>
       <label for="marital_single">Single</label></div></td>
  </tr>
  <tr>
    <td class="label">Birth Day:</td>
    <td><input type="text" id="birthday" name="birthday" title="Birth Day" class="textfield datefield birthday" value="<?php echo ($this->native_session->get('birthday')? $this->native_session->get('birthday'): '');?>" readonly/></td>
  </tr>
  <tr>
    <td class="label">Birth Place:</td>
    <td><input type="text" id="birthplace" name="birthplace" title="Birth Place" class="textfield placefield physical" value="<?php echo ($this->native_session->get('birthplace__addressline')? $this->native_session->get('birthplace__addressline'): '');?>" readonly/></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="right" style="padding-top:20px;"><input type="submit" name="step1" id="step1" value="NEXT" class="btn next" /></td>
  </tr>
</table>
        </form>
</div><div id="registration_form_btn_only"><button type="button" name="step1btn" id="step1btn" class="btn" style="width:240px;">REGISTER</button></div></td>
      </tr>
    </table></td>
  </tr>
  <?php $this->load->view("addons/public_footer");?>
</table>


</body>
</html>