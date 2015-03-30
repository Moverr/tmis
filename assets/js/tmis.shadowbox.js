// JavaScript Document


// Make shadow box link show popup
$(function() {
	
	// Load the shadow box
	$(document).on('click', '.shadowbox', function(e){
		e.preventDefault();
		var parent = $(document);
		var parentWindow = $(window);
		var closable = $(this).hasClass('closable')? " class='closableframe' ": "";
		
		// Remove the shadow box if it is already on the page
		if($('#__shadowbox').length > 0){
			$('#__shadowbox').remove();
		}
		// Put the div and iframe to load the link href
		$(this).after("<div id='__shadowbox' style='display:none;'><iframe src='"+$(this).attr('href')+"' "+closable+" onload='repositionFrame()' marginheight='0' frameborder='0'></iframe><div id='__shadowbox_closer'></div></div>");
		
		// resize and reposition the div
		$('#__shadowbox').offset({ top: 0, left: 0 });
		$('#__shadowbox').height(parent.height());
		
		var iFrame = $('#__shadowbox iframe');
		iFrame.css('max-height', (parentWindow.height()*0.8)+'px');
		iFrame.css('min-height', '200px');
		iFrame.css('min-width', (parentWindow.width()*0.4)+'px');
		iFrame.css('max-width', (parentWindow.width()*0.6)+'px');
		
		//Show the shadowbox after loading the iframe
		$('#__shadowbox').fadeIn('fast');
	});
	
	
	// Close the shadowbox
	$(document).on('click', '#__shadowbox_closer', function(e){
		$('#__shadowbox').fadeOut('fast');
		$('#__shadowbox').remove();
	});
	
	
	
	

	// Close shadow box if user clicks outside and it allows closing
	$(document).mouseup(function (e){
    	if($(".closableframe").length > 0)
		{
			var calloutContainer = $(".closableframe");
			var calloutContainerChildren = calloutContainer.find('body, table, div');
		
			//If the target of the click isn't the container... nor a descendant of the container, hide it
   			if (!calloutContainer.is(e.target) && calloutContainer.has(e.target).length === 0 && !calloutContainerChildren.is(e.target) && calloutContainerChildren.has(e.target).length === 0) 
    		{
       	 		$('#__shadowbox_closer').click();
    		}
		}
	});
	
});


//Function to reposition the iframe after it has been loaded
function repositionFrame()
{
	var iFrame = $('#__shadowbox iframe');
	var contentObj = iFrame.contents().find('table, div').first();
	
	iFrame.width(contentObj.outerWidth());
	iFrame.height(contentObj.outerHeight());
	//Postion iframe
	iFrame.offset({ top: ($(window).outerHeight()*0.5 - iFrame.outerHeight()*0.5), left: ($(window).outerWidth()*0.5 - iFrame.outerWidth()*0.5) });
	
	//Now position closer div
	var closer = $('#__shadowbox_closer');
	closer.offset({top: (iFrame.offset().top + 3), left: (iFrame.offset().left + iFrame.outerWidth() - closer.outerWidth() - 5)});
}



