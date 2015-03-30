// JavaScript Document

$(function() {
    
	//Show form when field with desired class is clicked
	// Add more classes after 'click' like so: '.placefield, .otherclass, .anotherclass', function(){
	$(document).on('click','.placefield',function(){
		var fieldId = $(this).attr('id');
		//If the place div is available, then just show that, else, load a new div content
		if($('#'+fieldId+'__div').length > 0)
		{
			$('#'+fieldId+'__div').fadeIn('fast');
		} 
		else 
		{
			//Now specify where the callouts pick their forms
			if($(this).hasClass('placefield'))
			{
				var physicalOnly = $('#'+fieldId).hasClass('physical')? "/physical_only/Y": "";
				$('#'+fieldId).after("<div id='"+fieldId+"__div' class='callout'></div>");
				$('#'+fieldId+'__div').css('min-height',(physicalOnly == ''? 265: 230)+'px');
				$('#'+fieldId+'__div').css('min-width',$(this).outerWidth());
				updateFieldLayer(getBaseURL()+"page/address_field_form/field_id/"+fieldId+physicalOnly,'','',fieldId+'__div','');
			}
			
			//TODO: Add more call out options here
		}
		// Minus 10 for the pointer
		var offsetTop = $('#'+fieldId).offset().top - $('#'+fieldId+'__div').outerHeight();// - 10;
		var offsetLeft = $('#'+fieldId).offset().left;
		$('#'+fieldId+'__div').offset({ top: offsetTop, left: offsetLeft });
	});


	// Close call out bubble if user clicks outside
	$(document).mouseup(function (e)
	{
    	var calloutContainer = $(".callout");
		var calloutContainerDiv = calloutContainer.children('div');
		
		//If the target of the click isn't the container... nor a descendant of the container, hide it
   		if (!calloutContainer.is(e.target) && calloutContainer.has(e.target).length === 0 && !calloutContainerDiv.is(e.target) && calloutContainerDiv.has(e.target).length === 0) 
    	{
       	 	calloutContainerDiv.hide('fast');
			calloutContainer.hide('fast');
    	}
	});
	
});

	
	
	



