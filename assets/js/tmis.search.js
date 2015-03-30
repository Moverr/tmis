//Pagination functionality

/*For use, the following is the basic HTML setup
-----------------------------------------------------------------------
<!-- The search field -->
<input type="text" id="[search field id]__[search type]" name="[search field id]__[search type]" placeholder="[Search instructions]" class="findfield[ other field classes here]" value=""/>

<!-- The hidden fields that are required for proper operation -->
// The area to display the search results
<input name="[search field id]__displaydiv" id="[search field id]__displaydiv" type="hidden" value="[the ID of the div to show the search results]" />

// Search ACTION (optional). If not given, the action is loaded from the search controller by [search field id].
<input name="[search field id]__action" id="[search field id]__action" type="hidden" value="[the url to perform the search]" />

// Search by fields (optional). If not given, the default field for the [search type] is used.
<input name="[search field id]__searchby" id="[search field id]__searchby" type="hidden" value="[field1|field2|field3..]" />

//IMPORTANT: echo a hidden field like that below to stop the next page list load (if the search results are less than the pagination list maximum number of rows)
<input name="paginationdiv__[pagination div ID]_stop" id="paginationdiv__[pagination div ID]_stop" type="hidden" value="[Number of pages loaded]" />
*/


var LOADING_IMG = "loading.gif";

$(function() {
	
	//What happens if you start typing in a find field (search field)
	$(document).on("keyup", ".findfield", function(){ 
		var fieldParts = $(this).attr('id').split('__');
		var searchFieldId = fieldParts[0];
		// The search action
		var fieldValue = $(this).val().length > 0? replaceBadChars($(this).val()): '_';
		var action = ($('#'+searchFieldId+'__action').length > 0? $('#'+searchFieldId+'__action').val(): getBaseURL()+'search/load_list')+'/type/'+$(this).data('type')+'/phrase/'+fieldValue;
			
		//Activate search if text is more than 2 characters
		if($(this).val().length > 1){
			// The fields to search by and append to the url
			var searchBy = $('#'+searchFieldId+'__searchby').length > 0? $('#'+searchFieldId+'__searchby').val().replace('|', '--'): '';
			action += searchBy !=''? '/searchby/'+searchBy: '';
			
			// Add a clear-search-field option if not available
			if(!$(this).hasClass('clearfield')){
				$(this).addClass('clearfield');
			}
			
			// Now show the search results
			var displayDiv = $('#'+searchFieldId+'__displaydiv').val();
			var displayheight = $('#'+displayDiv).parents('div, td').first().height();
			$('#'+displayDiv).css('min-height', displayheight+'px');
			
			//Update the pagination URL if available
			if($('#paginationdiv__'+searchFieldId+'_action').length > 0) $('#paginationdiv__'+searchFieldId+'_action').val(action);
			updateFieldLayer(action,'','',displayDiv,'');
			
			
			//Find if the results div is in a list and make proper adjustments
			var listId = displayDiv.substr(0, displayDiv.indexOf('__'));
			if($('#'+displayDiv).parents('#paginationdiv__'+listId+'_list').first().length > 0)
			{
				var containerDiv = $('#'+displayDiv).parents('#paginationdiv__'+listId+'_list').first();
				//Remove all divs not being used to display the list
				containerDiv.find('div').each(function(){
					if($(this).attr('id') != displayDiv){
						$(this).remove();
					}
				});
				//Remove all divs not the first page of pagination or the previous and next
				if($('#'+listId).length > 0){
					$('#'+listId).find('div').each(function(){
						if(!($(this).hasClass('previousbtn') || $(this).hasClass('nextbtn') || $(this).html() == '1')){
							$(this).remove();
						}
						if($(this).html() == '1'){
							$(this).addClass('selected');
							$(this).fadeIn('fast');
						}
					});
				}
			}
		}
		//Reload the list if the field is empty
		else if($(this).val().length == 0 && $(this).hasClass('clearfield'))
		{
			updateFieldLayer(action+'/__clear/Y','','',$('#'+searchFieldId+'__displaydiv').val(),'');
		}
	});
	
	
	
	// What happens if you click on a search field
	$(document).on("click", ".findfield", function(e){
		//Activate if has class that clears field
		if($(this).hasClass('clearfield')){
			
			//Only clear if the clicked area is where the clear icon is shown
			var fieldOffset = $(this).offset().left;
			if(e.pageX > fieldOffset &&  e.pageX < (fieldOffset + 30)){
				var fieldParts = $(this).attr('id').split('__');
				var searchFieldId = fieldParts[0];
				
				// The search action
				var fieldValue = $(this).val().length > 0? replaceBadChars($(this).val()): '_';
				var action = ($('#'+searchFieldId+'__action').length > 0? $('#'+searchFieldId+'__action').val(): getBaseURL()+'search/load_list')+'/type/'+$(this).data('type')+'/phrase/'+fieldValue;
				
				//Clear the field and remove all data
				$(this).val('');
				$(this).removeClass('clearfield');
				
				//Update the pagination URL if available
				if($('#paginationdiv__'+searchFieldId+'_action').length > 0) $('#paginationdiv__'+searchFieldId+'_action').val(action);
				
				// Then load the default list
				updateFieldLayer(action+'/__clear/Y','','',$('#'+searchFieldId+'__displaydiv').val(),'');
			}
		}
	});
	
});
