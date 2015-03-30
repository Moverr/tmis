// JavaScript Document

$(function() {
	//Set the HTML required for the timeout to operate
	$('body').append("<div style='display:none;'><a id='refresh_session' href='"+getBaseURL()+"web/page/refresh_session' class='fancybox fancybox.ajax'>Refresh This Session</a></div><input type='hidden' name='time_elapsed' id='time_elapsed' value='0' />");
});

//Handle session expiry
var maxTime = 1200; //20 minutes
var timeElapsed = 0;
//Keep on checking the session every 30 seconds
window.setInterval(checkUserSession, 30000);
//Function to check the user session and log them out if it expires
function checkUserSession()
{
	timeElapsed = +($('#time_elapsed').val()) + 30;
	$('#time_elapsed').val(timeElapsed);
	
	//The time has reached a refresh point. Notify the user
	if(timeElapsed >= maxTime)
	{
		$('#refresh_session').click();
	}
}