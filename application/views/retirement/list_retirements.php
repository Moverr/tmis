<?php $msg = empty($msg)? get_session_msg($this): $msg; 
$type = (!empty($action) && $action == 'report')? 'List': 'Applications';
?>
<!DOCTYPE html>
<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">
<link rel="icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">

<title><?php echo SITE_TITLE;?>: Retirement <?php echo $type;?></title>

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
<?php echo minify_js('retirement-list_retirements', array('jquery-2.1.1.min.js', 'jquery-ui.js', 'jquery.form.js', 'tmis.js', 'tmis.fileform.js', 'tmis.menu.js', 'tmis.responsive.js', 'tmis.list.js', 'tmis.shadowbox.js', 'tmis.pagination.js', 'tmis.search.js'));?>
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
              <td><div class="h1 grey nowrap listheader">Retirement <?php echo $type;?><?php if(check_access($this, 'view_retirements', 'boolean') && (!empty($action) && $action == 'report') && !empty($list)) echo "<div class='nextdiv downloadcontenticon' data-url='retirement/download' title='Click to download'></div>";?></div><div class="listsearchfield"><input type="text" id="retirementsearch__retirements" data-type="retirement" name="retirementsearch__retirements" placeholder="Search Retirement" class="findfield" value=""/>
<input type='hidden' id='retirementsearch__displaydiv' name='retirementsearch__displaydiv' value='retirementsearch__1' />
<input type='hidden' id='retirementsearch__action' name='retirementsearch__action' value='<?php echo base_url()."search/load_list/action/".(!empty($action)? $action: 'view');?>' />
</div></td></tr>
            <tr><td>
       
<table width="100%" border="0" cellspacing="0" cellpadding="0">
 <tr>
    <td>
    <div id="listcontainer">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><div id="paginationdiv__retirementsearch_list">
    <div id="retirementsearch__1"><?php $this->load->view('retirement/list', array(
	'listid'=>'retirementsearch',
	'list'=>(!empty($list)? $list: array()), 
	'action'=>(!empty($action)? $action: ''), 
	'msg'=>(!empty($msg)? $msg: '') 
	));?></div></div></td>
  </tr>
  <?php if(!empty($list)){?>
  <tr>
    <td style="padding:40px 15px 10px 15px; "><div class='centerpagination' style="margin:0px;padding:0px;"><div id="retirementsearch" class="paginationdiv"><div class="previousbtn" style='display:none;'>&#x25c4;</div><div class="selected">1</div><div class="nextbtn">&#x25ba;</div></div><input name="paginationdiv__retirementsearch_action" id="paginationdiv__retirementsearch_action" type="hidden" value="<?php echo base_url()."search/load_list/type/retirement/action/".(!empty($action)? $action: 'view');?>" />
<input name="paginationdiv__retirementsearch_maxpages" id="paginationdiv__retirementsearch_maxpages" type="hidden" value="<?php echo NUM_OF_LISTS_PER_VIEW;?>" />
<input name="paginationdiv__retirementsearch_noperlist" id="paginationdiv__retirementsearch_noperlist" type="hidden" value="<?php echo NUM_OF_ROWS_PER_PAGE;?>" />
<input name="paginationdiv__retirementsearch_showdiv" id="paginationdiv__retirementsearch_showdiv" type="hidden" value="paginationdiv__retirementsearch_list" /></div></td>
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