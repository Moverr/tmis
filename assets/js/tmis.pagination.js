//Pagination functionality

/*For use, the following is the basic HTML setup
-----------------------------------------------------------------------
<!-- The div that shows the list of items -->
<div id="paginationdiv__[pagination div ID]_list" class="paginationlist"><div id="[pagination div ID]__1">[Load the first page list. The rest will be loaded by pagination.]</div></div>
    
<!-- The div that shows the pagination -->
<div id="[pagination div ID]" class="paginationdiv">
	<div class="previousbtn" style="display:none;">&#x25c4;</div>
	<div class="selected">1</div>
	<div class="nextbtn">&#x25ba;</div>
</div>
	
<!-- The hidden fields that are required for proper operation -->
//List ACTION (optional). If not given, the action is loaded from the search controller by ID of the pagination div.
<input name="paginationdiv__[pagination div ID]_action" id="paginationdiv__[pagination div ID]_action" type="hidden" value="[the url to load the list items by page]" />

//The maximum number of pagination pages shown (optional). If not given, the maxpages = 5
<input name="paginationdiv__[pagination div ID]_maxpages" id="paginationdiv__[pagination div ID]_maxpages" type="hidden" value="[Maximum no of pages displayed at a time]" />

//The number of items per list (optional). If not given, the number of items per list = 10
<input name="paginationdiv__[pagination div ID]_noperlist" id="paginationdiv__[pagination div ID]_noperlist" type="hidden" value="[Number of items displayed per list]" />

//IMPORTANT: echo a hidden field like that below to stop the next page list load (if you have reached the end of the list)
<input name="paginationdiv__[pagination div ID]_stop" id="paginationdiv__[pagination div ID]_stop" type="hidden" value="[Number of pages loaded]" />
*/


var LOADING_IMG = "loading.gif";

$(function() {
	//Specify the pagination sizes
	//setPaginationSizes();
	
	//What happens if you click on a pagination div child
	$(".paginationdiv").on("click", "div", function(){ 
		var parentDiv = $(this).parent('div');
		//Specify the pages to load at a time
		var maxPages = !$('#paginationdiv__'+parentDiv.attr('id')+'_maxpages').length? 5 : parseInt($('#paginationdiv__'+parentDiv.attr('id')+'_maxpages').val());
		var noPerList = !$('#paginationdiv__'+parentDiv.attr('id')+'_noperlist').length? 10 : parseInt($('#paginationdiv__'+parentDiv.attr('id')+'_noperlist').val());
		//Specify the base URL to go to when the div is clicked
		var baseUrl = !$('#paginationdiv__'+parentDiv.attr('id')+'_action').length? getBaseURL()+'search/load_list/t/'+parentDiv.attr('id') : $('#paginationdiv__'+parentDiv.attr('id')+'_action').val();
		//The number of div children in the pagination parent div
		var noOfChildren = parentDiv.children('div').length;
			
		
		
		// Remove any selected class marker in the divs before assigning it to the clicked div
		parentDiv.children('div').each(function(){
			$(this).removeClass('selected');
		});
		
		//If it is not the next or previous button, make it the highlighted div
		if(!$(this).hasClass('nextbtn') && !$(this).hasClass('previousbtn')){
			$(this).addClass('selected');
			//First hide all the other divs in the same parent
			$('#paginationdiv__'+parentDiv.attr('id')+'_list').children('div').each(function(){
				$(this).fadeOut(0);
			});
			
			//If the div child does not exist, show and load
			var divContainerId = parentDiv.attr('id')+'__'+$(this).index();
			var divListContainers = $('#paginationdiv__'+parentDiv.attr('id')+'_list').find('#'+divContainerId);
			
			if(!divListContainers.length || (divListContainers.length && divListContainers.first().html().indexOf(LOADING_IMG) >= 0)){
				if(!divListContainers.length) $('#paginationdiv__'+parentDiv.attr('id')+'_list').append("<div id='"+divContainerId+"'></div>");
				updateFieldLayer(baseUrl+'/p/'+$(this).index()+'/n/'+noPerList,'','',divContainerId,'');
			
			} else {
				divListContainers.first().fadeIn();
			}
			//Go to the top of the list
			scrollToAnchor('paginationdiv__'+parentDiv.attr('id')+'_list');
		
			
		
		//If this is the next button
		} else if($(this).hasClass('nextbtn')){
			
			//If there is a previous navigation that is not being shown (because of previous navigation), 
			//first show that div set - in order
			if(!$(this).prev().is(":visible")){
				var minShown = getLastVisibleDivIndex(parentDiv)+1;
				var maxDivToShow = ((minShown + maxPages) > (noOfChildren-1))? (noOfChildren-1): (minShown + maxPages);
				
				
				parentDiv.children('div').each(function(index){
					//Fade out divs out of range
					if(!$(this).hasClass('previousbtn') && !$(this).hasClass('nextbtn') && (index < minShown || index >= maxDivToShow)){ 
						$(this).fadeOut(0);
					}
					//Show the divs that are within range
					if(index >= minShown && index < maxDivToShow){ 
						$(this).fadeIn();
					}
				});
				
				//If the previous div was hidden after reaching beginning, restore visibility
				if(!parentDiv.find('.previousbtn').first().is(":visible") && minShown > 1) {
					parentDiv.find('.previousbtn').first().fadeIn();
				}
			
			// Create a new div and load the list
			} else {
				
				//If we have not received a stop pagination hidden field, we continue adding pagination
				if(!$('#paginationdiv__'+parentDiv.attr('id')+'_stop').length){
					parentDiv.children('div').last().before("<div>"+(noOfChildren-1)+"</div>");
			
					if(noOfChildren > (maxPages+1)){
						var minShown = noOfChildren - maxPages;
						parentDiv.children('div').each(function(index){
							if(index > 0 && index < minShown){ 
								$(this).fadeOut(0);
							}
						});
				
						// Put a border on the previous if we have reached the limit of shown divs
						// Remember first div does not have a left border
						if(noOfChildren == (maxPages+1)){
							parentDiv.find('.previousbtn').first().addClass('rightborder');
						} else {
							parentDiv.find('.previousbtn').first().removeClass('rightborder');
						}
				
						//Show the previous btn if it is invisible
						if(!parentDiv.find('.previousbtn').first().is(":visible")) {
							parentDiv.find('.previousbtn').first().fadeIn();
						}
					}
				
					//Make the div selected
					$(this).prev('div').addClass('selected');
			
					//Now load the actual list on the page
					//-----------------------------------------------------------------------
					var pageIndex = noOfChildren - 1;//Remove the previous div (index 0) therefore it is = (noOfChildren - 2) + 1
					var divContainerId = parentDiv.attr('id')+'__'+pageIndex;
					//First hide all the other divs in the same parent
					$('#paginationdiv__'+parentDiv.attr('id')+'_list').children('div').each(function(){
						$(this).fadeOut(0);
					});
					$('#paginationdiv__'+parentDiv.attr('id')+'_list').append("<div id='"+divContainerId+"'></div>");
					//Make the height of the list div at least the inner height of the list container
					$('#'+divContainerId).height($('#paginationdiv__'+parentDiv.attr('id')+'_list').innerHeight());
					updateFieldLayer(baseUrl+'/p/'+pageIndex+'/n/'+noPerList,'','',divContainerId,'');		
					//Go to the top of the div being shown
					scrollToAnchor('paginationdiv__'+parentDiv.attr('id')+'_list');
					
					
				//Hide the next div if the list is completed
				} else {
					var nextBtn = parentDiv.find('.nextbtn').first();
					nextBtn.prev('div').addClass('selected');
					nextBtn.fadeOut(0);
				}
			}
			
		
		//If this is the previous button
		} else if($(this).hasClass('previousbtn')){
			var maxDivToShow = getLastVisibleDivIndex(parentDiv) - maxPages;
			var minShown = maxDivToShow < maxPages? 0: (maxDivToShow - maxPages);
			
			parentDiv.children('div').each(function(index){
				//Fade out first div if previous items are less than the max number of pages per show
				if(index == 0 && maxDivToShow <= maxPages){ 
					$(this).fadeOut(0);
				}
				//Fade out divs out of range
				if(index > 0 && ((minShown == 0 && index > maxDivToShow) || (minShown > 0 && (index <= minShown || index > maxDivToShow))) && !$(this).hasClass('nextbtn')){ 
					$(this).fadeOut(0);
				}
				//Show the divs that are within range
				if(index > 0 && ((minShown == 0 && index <= maxDivToShow) || (minShown > 0 && index > minShown && index <= maxDivToShow))){ 
					$(this).fadeIn();
				}
				
			});
			
			//Restore the next div if it is hidden
			if(!parentDiv.find('.nextbtn').first().is(":visible")) parentDiv.find('.nextbtn').first().fadeIn();
		}
		
		
	});
	
});


//Get the last div of the given div's children
function getLastVisibleDivIndex(parentDiv){
	var visibleId = 1;
	parentDiv.children('div').each(function(index){
		if($(this).is(":visible") && !$(this).hasClass('previousbtn') && !$(this).hasClass('nextbtn')){
			visibleId = index;
		}
	});
	
	return visibleId;
}



//Function to reset pagination item sizes
function setPaginationSizes(){
	$(".paginationdiv").height($(".paginationdiv div").outerHeight());
	$(".paginationlist").height($(".paginationlist").parent().innerHeight() - $(".paginationlist").next(".paginationdiv").height() - parseInt($(".paginationlist").css("marginBottom").replace('px', '')) - parseInt($(".paginationlist").css("marginTop").replace('px', '')));
	$(".paginationlist").width($(".paginationlist").parent().innerWidth() - parseInt($(".paginationlist").css("marginLeft").replace('px', '')) - parseInt($(".paginationlist").css("marginRight").replace('px', '')));
}
