<?php $msg = empty($msg)? get_session_msg($this): $msg;
if(!empty($action) && in_array($action, array('setdate', 'recommend', 'recommendations')) ) $listName = "Application";
else  $listName = "Interview";
?>
<!DOCTYPE html>
<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">
<link rel="icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">

<title><?php echo SITE_TITLE;?>: <?php echo $listName;?> List</title>

<!-- Stylesheets -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/jquery-ui.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.mobile.css" media="(max-width:790px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.tablet.css" media="(min-width:791px) and (max-width: 900px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.desktop.css" media="(min-width:901px)" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.list.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.menu.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.shadowbox.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.pagination.css"/>

<!-- Javascript -->
<?php echo minify_js('interview-list_interviews', array('jquery-2.1.1.min.js', 'jquery-ui.js', 'jquery.form.js', 'tmis.js', 'tmis.fileform.js', 'tmis.menu.js', 'tmis.responsive.js', 'tmis.list.js', 'tmis.shadowbox.js', 'tmis.pagination.js', 'tmis.search.js','jquery-ui-timepicker-addon.js'));?>
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
              <td><div class="h1 grey nowrap listheader"><?php echo $listName;?>s</div>
        <?php if(!empty($action) && $action == 'setdate' && check_access($this, 'set_interview_date', 'boolean') && !empty($list)) echo "<div id='multiuserdateset__btn' class='nextdiv multiusericon selectlistbtn' style='margin-left:15px; margin-top:10px;'  data-url='interview/select_multi_user' title='Click to set date for multiple applicants'></div>";?>

<div class="listsearchfield">

<input type="text" id="interviewsearch__interviews" data-type="interview" name="interviewsearch__interviews" placeholder="Search <?php echo $listName;?>s" class="findfield" value=""/>
<input type='hidden' id='interviewsearch__displaydiv' name='interviewsearch__displaydiv' value='interviewsearch__1' />
<input type='hidden' id='interviewsearch__action' name='interviewsearch__action' value='<?php echo base_url()."search/load_list/action/".(!empty($action)? $action: 'view');?>' /></div>
 </td></tr>

 <tr>
   <td>
   <div id='multiuserdateset__div' class="selectlistdiv" style="display:none;">
           <?php echo "<table width='100%' cellpadding='5' class='microform'>
		<tr>
		<td width='96%' valign='top'><div class='label' style='text-align:left;'>Applicants:</div><div id='input_multiuserdateset__div' class='textfield value' style='min-height:88px;max-height:88px;min-width:96%; overflow-y: auto; overflow-x: hidden;' data-default='Search and select applicants from the list below.'>Search and select applicants from the list below.</div>
		</td>

		<td width='2%' valign='top'><div class='label' style='text-align:left;'>Interview Date:</div><input type='text' id='interviewdate' name='interviewdate' title='Interview Date' class='textfield datefield showtime' value=''/>

		<div class='label' style='margin-top:10px;text-align:left;'>Interview Board:</div>
		<input type='text' id='boardname__viewonlyboards' name='boardname__viewonlyboards' title='Select Board Name' placeholder='Select Board Name' class='textfield selectfield' value=''/><input type='hidden' id='boardid' name='boardid' value='' />
		</td>

		<td width='2%' valign='top' style='padding-top:25px;'><button type='button' class='btn submitmicrobtn selectlistconfirmbtn' name='setdate' id='setdate' value='SET DATE' style='width:110px;'>SET DATE</button>
		<button type='button' class='greybtn selectlistcancelbtn' name='cancelsetdate' id='cancelsetdate' value='CANCEL' style='width:110px; margin-top:10px;'>CANCEL</button>
		<input type='hidden' id='action' name='action' value='".base_url()."interview/select_multi_user' />
		<input type='hidden' id='resultsdiv' name='resultsdiv' value='multiuserdateset__div__MSG' />
		<input type='hidden' id='errormessage' name='errormessage' value='All fields are required to continue.' />
		</td>
		</tr>
		 <tr><td colspan='3' class='greybg smalltext'  style='padding:10px;'>NOTE: The board chairman will be set as the interviewer user for the selected users</td></tr>
		</table>";?>
           </div>
           <div id='multiuserdateset__div__MSG' style="display:none;"></div>
           <div id='multiuserdateset__div__IGNORE' style="display:none;"></div></td>
           </tr>

            <tr><td>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
 <tr>
    <td>
    <div id="listcontainer">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><div id="paginationdiv__interviewsearch_list">
    <div id="interviewsearch__1"><?php $this->load->view('interview/list', array(
	'listid'=>'interviewsearch',
	'list'=>(!empty($list)? $list: array()),
	'action'=>(!empty($action)? $action: ''),
	'msg'=>(!empty($msg)? $msg: '')
	));?></div></div></td>
  </tr>
  <?php if(!empty($list)){?>
  <tr>
    <td style="padding:40px 15px 10px 15px; "><div class='centerpagination' style="margin:0px;padding:0px;"><div id="interviewsearch" class="paginationdiv"><div class="previousbtn" style='display:none;'>&#x25c4;</div><div class="selected">1</div><div class="nextbtn">&#x25ba;</div></div><input name="paginationdiv__interviewsearch_action" id="paginationdiv__interviewsearch_action" type="hidden" value="<?php echo base_url()."search/load_list/type/interview/action/".(!empty($action)? $action: 'view');?>" />
<input name="paginationdiv__interviewsearch_maxpages" id="paginationdiv__interviewsearch_maxpages" type="hidden" value="<?php echo NUM_OF_LISTS_PER_VIEW;?>" />
<input name="paginationdiv__interviewsearch_noperlist" id="paginationdiv__interviewsearch_noperlist" type="hidden" value="<?php echo NUM_OF_ROWS_PER_PAGE;?>" />
<input name="paginationdiv__interviewsearch_showdiv" id="paginationdiv__interviewsearch_showdiv" type="hidden" value="paginationdiv__interviewsearch_list" /></div></td>
  </tr>
  <?php }?>
</table>
</div>

    </td>
    </tr>
</table>

            </td></tr>
        </table></td>
      </tr>
     </table>
    </td>
  </tr>
  <?php $this->load->view("addons/secure_footer");?>
</table>

</body>
</html>
