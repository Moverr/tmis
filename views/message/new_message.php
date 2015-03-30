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

<title><?php echo SITE_TITLE;?>: <?php echo !empty($id)? (!empty($action) && $action=='view'? 'View':'Reply To'): 'New';?> Message</title>

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
<?php echo minify_js('message-new_message', array('jquery-2.1.1.min.js', 'jquery-ui.js', 'jquery.form.js', 'tmis.js', 'tmis.fileform.js', 'tmis.menu.js', 'tmis.responsive.js', 'trumbowyg.js'));?>
<script type="text/javascript">
<?php echo !empty($id) && !empty($result['boolean']) && $result['boolean']?"window.top.location.href = '".$forwardUrl."';": "";?>
</script>
</head>

<body style="margin:0px;"><div id="messagehandler" style="display:none;"></div>
<?php 
# Do not show the header, menu and footer when viewing
if(empty($id) || (!empty($id) && !(!empty($action) && $action=='view'))) {?>
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
      		<tr><td class="h1 grey"><?php echo !empty($id)? (!empty($action) && $action=='view'? 'View':'Reply To'): 'New';?> Message</td></tr>
            <?php echo !empty($msg)?"<tr><td>".format_notice($this,$msg)."</td></tr>": "";?>
            <tr><td>
            
            
            <table border="0" cellspacing="0" cellpadding="10" class='microform'>
  
  <tr>
    <td class="label">Recipient:</td>
    <td style="padding-right:42px;"><?php if(!empty($id)){
		echo "<div class='value'>".$this->native_session->get('recipientname__users')."</div>";
		} else {
			# A non-admin user
			if($this->native_session->get('__permission_group') != '4'){
				if($this->native_session->get('recipientname__users')) {
					echo $this->native_session->get('recipientname__users')." <input type='hidden' id='recipientname__users' name='recipientname__users' value='".$this->native_session->get('recipientname__users')."' /> <input type='hidden' id='userid' name='userid' value='".$this->native_session->get('recipientid')."' />";
					
				} else {
					echo "Website Administrator <input type='hidden' id='recipientname__users' name='recipientname__users' value='_all_admins_' /> <input type='hidden' id='userid' name='userid' value='_all_admins_' />";
				}
			
			# An admin user
			} else {
				?><table>
               	 	<tr><td><input type="text" id="recipientname__users" name="recipientname__users" title="Select or Search for User" placeholder="Select or Search for User" class="textfield selectfield searchable" value="<?php echo $this->native_session->get('recipientname__users');?>" /></td>
                	<td><input type='checkbox' id='selectall' name='selectall' value='All' onClick="passFormValue('selectall', 'recipientname__users', 'checkbox');passFormValue('selectall', 'userid', 'checkbox');"/><label for='selectall'/>Send to all</label><input type='text' class="textfield" id='userid' name='userid' value='<?php echo $this->native_session->get('recipientid');?>' style="display:none;" /></td></tr>
                </table>
			<?php 
			}
		}?></td>
  </tr>
  <?php if(!(!empty($action) && $action=='send_new_sms')){?>
  <tr>
    <td class="label">Subject:</td>
    <td><?php if(!empty($id)){
		echo "<div class='value'>".$this->native_session->get('subject')."</div>";
		} else {?><input type="text" id="subject" name="subject" title="Subject" class="textfield" style="width:97%;" value="<?php echo $this->native_session->get('subject');?>"/><?php }?></td>
  </tr>
  <?php }?>
  <tr>
    <td class="label top">Message:</td>
    <td><?php if(!empty($id)){
		echo "<div class='value'>".$this->native_session->get('message')."</div>";
		} else {?>
        <textarea id="message" name="message" title="Message" class="textfield <?php echo (!(!empty($action) && $action=='send_new_sms')? 'htmlfield': '');?>" placeholder="Enter the message here." style="min-width:300px;"><?php echo $this->native_session->get('message');?></textarea><?php }?></td>
  </tr>
  <?php if(!(!empty($action) && $action=='view')) {?>
  <tr>
    <td>&nbsp;</td>
    <td><button type="button" name="send" id="send" class="btn submitmicrobtn">SEND</button>
    <input type='hidden' id='action' name='action' value='<?php echo base_url().'message/'.(!empty($action)? $action: 'view'); ?>' />
    <input type='hidden' id='tempmessage' name='tempmessage' value='Sending..' />
    <input type='hidden' id='errormessage' name='errormessage' value='You need to select a user and enter all fields to continue.' />
    <input type='hidden' id='resultsdiv' name='resultsdiv' value='messagehandler' /></td>
  </tr>
  <?php }?>
            </table>
            
            
            
            </td></tr>
        </table>
 
 <?php 
# Do not show the header and footer when editing - or viewing
if(empty($id) || (!empty($id) && !(!empty($action) && $action=='view'))) {?>       
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
	$('.htmlfield').trumbowyg({btns: ['formatting',
           '|', btnsGrps.design,
           '|', 'link',
           '|', btnsGrps.justify,
           '|', 'insertHorizontalRule']
	});
});
</script>
</body>
</html>