<?php 
# Which page is this header being loaded on?
$page = !empty($page)? $page: "home"; 

#Show the home link if you are not on the home page
$homeLink = $page != "home"? "<a href=\"".base_url()."\">Home</a> &nbsp;&nbsp;|&nbsp;&nbsp; ": "";
#Show the other links as active if on that page
$aboutLink = $page != "about_us"? "<a href=\"".base_url()."page/about_us\">About TMIS</a>": "<span class='label'>About TMIS</span>";
$faqsLink = " &nbsp;&nbsp;|&nbsp;&nbsp; ".($page != "faqs"? "<a href=\"".base_url()."page/faqs\">FAQs</a>": "<span class='label'>FAQs</span>");
$verifyLink = " &nbsp;&nbsp;|&nbsp;&nbsp; ".($page != "verify"? "<a href=\"".base_url()."page/verify\">Verify</a>": "<span class='label'>Verify</span>");
$logoutLink = ($page != "login" && $this->native_session->get('__user_id')? " &nbsp;&nbsp;|&nbsp;&nbsp; <a href=\"".base_url()."account/logout\">Logout</a>": "");

?>
<tr>
    <td colspan="2" class="greybg"><div class="logo"><a href="<?php echo base_url();?>"><img src="<?php echo base_url();?>assets/images/tmis_logo.png" alt="TMIS logo" border="0"></a></div><?php if($page != "login" && !$this->native_session->get('__user_id') ){?><div class="loginform"><form method="post" autocomplete="off" ><table border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><input type="text" id="loginusername" name="loginusername" placeholder="Email Address" class="textfield" value="" maxlength="100"  style="width:140px;"/></td>
    <td><input type="password" id="loginpassword" name="loginpassword" placeholder="Password" class="textfield" value="" maxlength="100" style="width:140px;"/></td>
    <td><button type="button" class="greybtn" id="submitlogin" name="submitlogin">LOGIN</button></td>
  </tr>
</table></form></div><?php }?></td>
  </tr>
  <tr>
    <td colspan="2" class="greybg topwhiteborder"><div style="padding-left:1%;padding-top:10px;padding-bottom:10px;" class="logo h3 grey">TEACHER MANAGEMENT INFORMATION SYSTEM</div><div class="loginform"><?php echo $homeLink.$aboutLink.$faqsLink.$verifyLink.$logoutLink;?></div></td>
  </tr>
  
  
  <tr><td colspan="2"><div class="greybg topwhiteborder centrallinks" style="text-align:center; padding-top:10px;padding-bottom:10px;"><?php echo $homeLink.$aboutLink.$faqsLink.$verifyLink.$logoutLink;?></div></td></tr>
  