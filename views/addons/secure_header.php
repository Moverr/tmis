 <?php 
#Highlight active pages
$settingStyle = $helpStyle = "";
$class = "";
if(!empty($page))
{
	if($page=='my_settings')
	{
		$settingStyle = " style='color:#E4E4E4;'";
		$class = " active";
	}
	else if($page=='help')
	{
		$helpStyle = " style='color:#E4E4E4;'";
	}
} 
?>
 <tr>
    <td colspan="2" class="bluebg"><div class="logo"><a href="<?php echo base_url().get_user_dashboard($this, $this->native_session->get('__user_id'));?>"><img src="<?php echo base_url();?>assets/images/secure_tmis_logo.png" alt="TMIS logo" border="0"></a></div><div class="loginform largelinks">
    <table border="0" cellspacing="0" cellpadding="0">
    	<tr><td class="h2"><?php echo $this->native_session->get('__first_name').", ".$this->native_session->get('__last_name')." (".$this->native_session->get('__permission_group_name').") &nbsp;&nbsp; ";?></td><td class="usericon<?php echo $class;?>">&nbsp;</td><td><a href="<?php echo base_url();?>profile/user_data" <?php echo $settingStyle;?>>My Settings</a></td><td style="padding-left:30px;">&nbsp;</td><td class="secureicon">&nbsp;</td><td><a href="<?php echo base_url();?>account/logout" class="yellow">Log Out</a></td></tr>
    </table>
    </div></td>
  </tr>
  <tr>
    <td colspan="2" class="bluebg topgreyborder"><div style="padding-left:1%;padding-top:10px;padding-bottom:10px;" class="logo h3 lightgrey">TEACHER MANAGEMENT INFORMATION SYSTEM</div><div class="loginform"><a href="<?php echo base_url();?>message/send_new_system" <?php echo $helpStyle;?>>Help</a></div></td>
  </tr>
  
  
  <tr><td colspan="2"><div class="bluebg topgreyborder centrallinks" style="text-align:center; padding-top:10px;padding-bottom:10px;"><a href="<?php echo base_url();?>message/send_new_system" <?php echo $helpStyle;?>>Help</a> &nbsp;&nbsp;|&nbsp;&nbsp; <a href="<?php echo base_url();?>profile/user_data" <?php echo $settingStyle;?>>My Settings</a> &nbsp;&nbsp;|&nbsp;&nbsp; <a href="<?php echo base_url();?>account/logout" class="yellow">Log Out</a></div></td></tr>