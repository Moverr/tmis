// JavaScript Document


//Other functions that only work on some responsive views
$(function(){
	// Button for showing the form in tablet mode
	$('#step1btn').on("click",function(){
		$('#registration_close_btn').show('fast');
		$('#registration_form_btn_only').hide('fast');
		$('#registration_form').show('fast');
		$('#listcontainer').hide('fast');
		$('#jobslist_btn_only').show('fast');
		$('.leftcolumn').css({"width":"30%"});
		$('.rightcolumn').css({"width":"70%"});
	});


	//Button for hiding the form in tablet mode
	$('#registration_close_btn, #jobslistbtn').on("click",function(){
		$('#registration_close_btn').hide('fast');
		$('#registration_form_btn_only').show('fast');
		$('#registration_form').hide('fast');
		$('#listcontainer').show('fast');
		$('#jobslist_btn_only').hide('fast');
		$('.leftcolumn').css({"width":"60%"});
		$('.rightcolumn').css({"width":"40%"});
	});


	//Mobile size button actions
	$('#step1btnbig').on("click",function(){
		document.location.href= getBaseURL();
	});

	$('#loginbtnbig').on("click",function(){
		document.location.href= getBaseURL()+"account/login";
	});

});


// Handle step responsiveness by recoloring the divs as you move from one page to another
$(document).ready(function() {
	if($('#stepstracker').length > 0){
		var isChrome = navigator.userAgent.toLowerCase().indexOf('chrome') > -1;
		var index = 0;
		
		$('#stepstracker tr:first-child td').each(function(){
			// Recolor the divs in those cells as you proceed in the steps
			if(index > 1){
				//Color the last step's last div green if the user has gone past that step
				if($(this).hasClass('steptwo') && $(this).hasClass('visited')){
					$(this).parent('tr').find('.stepone').first().removeClass('visited').addClass('visitedwithmore');
				}
				if($(this).hasClass('stepthree') && $(this).hasClass('visited')){
					$(this).parent('tr').find('.steptwo').first().removeClass('visited').addClass('visitedwithmore');
				}
				if($(this).hasClass('stepfour') && $(this).hasClass('visited')){
					$(this).parent('tr').find('.stepthree').first().removeClass('visited').addClass('visitedwithmore');
				}
			}
			
			index++;
		});
		
		
		
		//Forcefully resize the divs if its Chrome browser
		if(isChrome){
			$('#stepstracker tr:first-child td').each(function(){
				$(this).find('div').last().css('margin-right', '0px');
				$(this).css('padding-right', '0px');
				
				// STEP ONE
				if($(this).hasClass('stepone')){
					$(this).find('div').last().width($(this).width()+76);
				}
					
				// MIDDLE STEPS WITHOUT VISITED
				if(($(this).hasClass('steptwo') || $(this).hasClass('stepthree')) && !$(this).hasClass('visitedwithmore')){
					var halfWidth = ($(this).width())/2;
					$(this).find('div').first().width(halfWidth+38);
					$(this).find('div').last().width(halfWidth+38);
				}
				
				// MIDDLE STEPS WITH VISITED	
				if(($(this).hasClass('steptwo') || $(this).hasClass('stepthree')) && $(this).hasClass('visitedwithmore')){
					$(this).find('div').last().width($(this).width()+76);
				}
				
				// STEP FOUR
				if($(this).hasClass('stepfour')){
					$(this).find('div').first().width($(this).width()+76);
				}
				
			});
		}
	}
});










