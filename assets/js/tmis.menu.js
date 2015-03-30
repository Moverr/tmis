// JavaScript Document

// Menu functionality
$(function() {
if($('#menucontainer').length > 0){
	
	// Click menu item
	$(document).on('click', '#menucontainer .item div:not(.header)', function(){
		// If this div does not have the selected class then make it selected and load its page
		if(!$(this).hasClass('selected')){
			var selectedDiv = $(this);
			//Remove the selected class from other divs in this container
			$(this).parents('.item').first().find('div').each(function(){
				$(this).removeClass('selected');
			});
			//Now make this div the selected one
			$(this).addClass('selected');
			document.location.href = getBaseURL()+$(this).data('rel');
		}
	});
	
	//Click menu header
	$(document).on('click', '#menucontainer .item .header', function(){
		var parentDiv = $(this).parents('.item').first();
		if(!parentDiv.hasClass('selected')){
			parentDiv.parents('#menucontainer').first().find('.item').each(function(){
				$(this).removeClass('selected');
			});
			parentDiv.addClass('selected');
		}
	});
}
});





