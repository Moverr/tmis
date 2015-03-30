<?php
//Which page is this footer being loaded on?
$page = !empty($page)? $page: "home"; 

#Show the other links as active if on that page
$termsLink = $page != "terms"? "<a href=\"".base_url()."page/terms_of_use\">Terms</a>": "<span class='label'>Terms</span>";
$privacyLink = " &nbsp;&nbsp;|&nbsp;&nbsp; ".($page != "privacy"? "<a href=\"".base_url()."page/privacy_policy\">Privacy</a>": "<span class='label'>Privacy</span>");
$contactLink = " &nbsp;&nbsp;|&nbsp;&nbsp; ".($page != "contact_us"? "<a href=\"".base_url()."page/contact_us\">Contact Us</a>": "<span class='label'>Contact Us</span>");
$applyLink = " &nbsp;&nbsp;|&nbsp;&nbsp; ".($page != "new_account"? "<a href=\"".base_url()."account/apply\">New Account</a>": "<span class='label'>New Account</span>");
?>
<tr>
    <td colspan="2" class="greybg footer">&copy;<?php echo date("Y");?> MoES with support from UNESCO.<br>
<?php echo $termsLink.$privacyLink.$contactLink.$applyLink;?><input type="hidden" id="layerid" name="layerid" value="" /></td>
  </tr>