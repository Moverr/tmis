// JavaScript Document


// Handle clicking on list buttons
$(function(){
	$(document).on('click', '.addcontenticon, .downloadcontenticon, .printcontenticon', function(e){
		var url = getBaseURL()+$(this).data('url');
		if($(this).hasClass('newwindow')){
			 window.open(url,'_blank');
		} else if($(this).hasClass('newwindowfromiframe')){
			window.parent.location.href= url;
		} else {
		   window.location.href = url;
		}
	});
	
	
	
	
	// Special list button on the same page as the list
	$(document).on('click', '.selectlistbtn', function(e){
		// 1. Show the target div
		var targetDiv = $('#'+$(this).attr('id').split('__')[0]+'__div').length > 0? $(this).attr('id').split('__')[0]+'__div': '';
		$('#'+targetDiv).fadeIn('fast');
		
		// Notify system to set session for further search in lists
		updateFieldLayer(getBaseURL()+$(this).data('url'),'','',targetDiv+'__IGNORE','');
		
		// 2. Remove the button to prevent multiple clicking
		$(this).css('display','none');
		// 3. Show the select list checkboxes
		$(document).find('.listcheckbox').each(function(){ $(this).show('fast');});
	});
	
	$(document).on('click', '.selectlistcancelbtn', function(e){
		// 1. hide the div with the details
		var containerDiv = $(this).parents('.selectlistdiv').first();
		containerDiv.fadeOut('fast');
		
		// 2. re-show the selectlistbtn
		$('#'+containerDiv.attr('id').split('__')[0]+'__btn').css('display','inline-block');
		// 3. Also hide the select list checkboxes
		$(document).find('.listcheckbox').each(function(){ $(this).hide('fast');});
		
		// Notify system to clear session for further search in lists
		var url = $('#'+containerDiv.attr('id').split('__')[0]+'__btn').data('url');
		updateFieldLayer(getBaseURL()+url+'/clear/Y','','',containerDiv.attr('id')+'__IGNORE','');
		
	});
	
});








//Handles list actions 
$(function() {
	$(document).on('click', '.approverow, .rejectrow, .publishrow, .archiverow, .restorerow, .blockrow, .saverow', function(e){
		
		// Find all action siblings and remove the active class
		$(this).parents('.listtable').first().find('.approverow, .rejectrow, .publishrow, .archiverow, .restorerow, .blockrow, .saverow').each(function(){
			$(this).removeClass('active');
		});
		
		var listType = $(this).data('type');
		var rowValues = $(this).data('val').split('__');
		
		if($(this).hasClass('confirm'))
		{
			if(window.confirm("Are you sure you want to "+rowValues[0].split('_')[0]+" this "+listType+"?")) {
				// Post this for processing archive or restore
				var fieldsToPost = { id: rowValues[1], listtype: listType, action: rowValues[0] };
				
				$.ajax({
        			type: "POST",
       				url: getBaseURL()+listType+'/verify',
      				data: fieldsToPost,
      				beforeSend: function() {
           				showWaitDiv('start');
					},
					error: function(xhr, status, error) {
  						showWaitDiv('end');
						console.log(xhr.responseText);
					},
      	 			success: function(data) {
		   				//console.log(data);
						showWaitDiv('end');
						updateFieldLayer(document.URL,'','','','');
					}
   				});
			}
		}
		else 
		{
			var url = getBaseURL()+listType+'/verify/action/'+rowValues[0]+'/id/'+rowValues[1];
			if($('#action__'+rowValues[1]).length > 0) updateFieldLayer(url,'','','action__'+rowValues[1],'');
		}
		// Show action as active
		$(this).addClass('active');
	});
	
	
	// Cancel list action
	$(document).on('click', '.cancellistbtn', function(e){
		$(this).parents('.listrow').first().find('.approverow, .rejectrow, .archiverow, .publishrow').each(function(){
			$(this).removeClass('active');
		});
		
		var parentDiv = $(this).parents('.actionrowdiv').first();
		parentDiv.fadeOut('fast');
		parentDiv.html('');
	});
	
	
	
	// Confirm list action
	$(document).on('click', '.confirmlistbtn', function(e){
		
		var btnId = $(this).attr('id');
		var idStub = btnId.substring(btnId.indexOf("_")+1, btnId.length );
		var id = btnId.split('_').pop();
		var listType = $('#hidden_'+idStub).val();
		var clearToPost = true;
		
		// Check if there are other custom field values
		var otherValues = "";
		$(this).parents('table').first().find('.otherfield').each(function(){
			if($(this).val() != ''){
				otherValues += '|'+$(this).attr('id').replace('_'+idStub, '')+'='+replaceBadChars($(this).val());
			} else if(!$(this).hasClass('optional')){
				clearToPost = false;
			}
		});
		
		
		
		if(clearToPost)
		{
			var fieldsToPost = { reason: $('#reason_'+idStub).val(), id: id, listtype: listType, action: idStub.replace('_'+id, ''), other:otherValues.replace(/^|/, '') };
		
			$.ajax({
        		type: "POST",
       			url: getBaseURL()+listType+"/verify",
      			data: fieldsToPost,
      			beforeSend: function() {
					showWaitDiv('start');
				},
				error: function(xhr, status, error) {
  					showWaitDiv('end');
					console.log(xhr.responseText);
				},
      	 		success: function(data) {
		   			//console.log(data);
					showWaitDiv('end');
					updateFieldLayer(document.URL,'','','','');
				}
   			});
		}
		else
		{
			showServerSideFadingMessage('Enter all required fields to continue.');
		}
	});
	
});






// Add a field value to a list div
function addListFieldValue(checkboxId, containerDiv, hiddenFieldStamp, realValue, htmlValue)
{
	//Get the value parts to be passed on
	var realValueParts = realValue.split('|');
	var fieldDivId = hiddenFieldStamp+'__'+realValueParts[0];
			
	// Checkbox is checked
	if($('#'+checkboxId).is(":checked"))
	{
		// Remove default text
		if($('#'+containerDiv).html() == $('#'+containerDiv).data('default')) $('#'+containerDiv).html('');
		
		if($('#'+containerDiv).find('#'+fieldDivId).length == 0) {
			$('#'+containerDiv).append("<div onclick=\"removeTextFieldListItem('"+containerDiv+"', '"+fieldDivId+"')\" class='textfieldlistitem' id='"+fieldDivId+"'><input type='hidden' id='"+hiddenFieldStamp+"-"+realValueParts[0]+"' name='"+hiddenFieldStamp+"[]' value='"+realValueParts[0]+"' />"+htmlValue+"</div>");
		}
		
		//Now add all the other hidden fields if any
		if(realValueParts.length > 1 && $('#'+fieldDivId).length > 0)
		{
			for(var i=1; i<realValueParts.length; i++){
				var partComponents = realValueParts[i].split('=');
				//Add the hidden field if it is not already available
				if($('#'+fieldDivId).find('#'+partComponents[0]+'-'+partComponents[1]).length == 0) 
				{
					$('#'+fieldDivId).append("<input type='hidden' id='"+partComponents[0]+'-'+partComponents[1]+"' name='"+partComponents[0]+"[]' value='"+partComponents[1]+"' />");
				}
			}
		}
		
		
	} 
	// Remove the checked item
	else 
	{
		//Remove just the unchecked other value if there are more rows than the primary identifier 
		//e.g. more applications for a single user
		if(realValueParts.length > 1 && $('#'+fieldDivId).length > 0)
		{
			for(var i=1; i<realValueParts.length; i++){
				var partComponents = realValueParts[i].split('=');
				$('#'+fieldDivId).find('#'+partComponents[0]+'-'+partComponents[1]).first().remove();
			}
			
		}
		//Otherwise remove the primary div
		else $('#'+containerDiv).find('#'+fieldDivId).first().remove();
		
		//Restore default text if div is empty
		if($('#'+containerDiv).html() == '') $('#'+containerDiv).html($('#'+containerDiv).data('default'));
	}
	
}




//Remove an element from a given container
function removeTextFieldListItem(containerId, elementId)
{
	$('#'+containerId).find('#'+elementId).first().remove();
}