<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Loading..</title>
<link rel="shortcut icon" type="image/x-icon" href="<?php echo base_url();?>favicon.ico">
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tmis.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery-1.8.2.min.js"></script>
<script>

$.ajax({
        xhr: function() {
            var xhr = new window.XMLHttpRequest();
            xhr.addEventListener('progress', function(e) {
                
            });
            return xhr;
        }, 
        type: 'POST', 
        url: '<?php echo $pageUrl;?>', 
        data: {}, 
        complete: function(response, status, xhr) {
            $('#loading_div').hide('fast');
			$('#page_content').html(response.responseText);
			document.title = "<?php echo $pageTitle;?>";
        }
});

$(function() {
	var windowHeight = $(document).height(); 
	$('#loading_div').css('height', windowHeight+'px');  
	$('#loading_div').css('margin-top', (windowHeight/2 - 100)+'px'); 
});
</script>

<style>
.progressbar {
	width: 150px;
	background-color: #CCC;
}

.bar {
	width: 1%;
	background-color: #393;
}
.spinner {
  margin: 20px auto;
  width: 50px;
  height: 30px;
  text-align: center;
  font-size: 10px;
}

.spinner > div {
  background-color: #333;
  height: 100%;
  width: 6px;
  display: inline-block;
  
  -webkit-animation: stretchdelay 1.2s infinite ease-in-out;
  animation: stretchdelay 1.2s infinite ease-in-out;
}

.spinner .rect2 {
  -webkit-animation-delay: -1.1s;
  animation-delay: -1.1s;
}

.spinner .rect3 {
  -webkit-animation-delay: -1.0s;
  animation-delay: -1.0s;
}

.spinner .rect4 {
  -webkit-animation-delay: -0.9s;
  animation-delay: -0.9s;
}

.spinner .rect5 {
  -webkit-animation-delay: -0.8s;
  animation-delay: -0.8s;
}

@-webkit-keyframes stretchdelay {
  0%, 40%, 100% { -webkit-transform: scaleY(0.4) }  
  20% { -webkit-transform: scaleY(1.0) }
}

@keyframes stretchdelay {
  0%, 40%, 100% { 
    transform: scaleY(0.4);
    -webkit-transform: scaleY(0.4);
  }  20% { 
    transform: scaleY(1.0);
    -webkit-transform: scaleY(1.0);
  }
}
</style>

</head>

<body style="margin:0px;">
<div id="page_content" style='width:100%;'></div>
<div id="loading_div" style='position:fixed; width:100%; text-align:center;'>
<table border="0" cellspacing="0" cellpadding="0" align="center"><tr><td class="topleftheader" style="margin:0px; padding:0px;">
<?php echo $loadingMessage;?>
</td></tr>
<tr><td align="center">
<div class="spinner">
  <div class="rect1"></div>
  <div class="rect2"></div>
  <div class="rect3"></div>
  <div class="rect4"></div>
  <div class="rect5"></div>
</div>
</td></tr>
</table>
</div>
</body>
</html>