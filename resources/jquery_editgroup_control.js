
var _TX_COMMUNITY_EDITGROUP_CURRENT_TAB = 'GENERAL_SETTINGS';

function clearMessage() {
	$('#tx-community-editgroup-status').slideUp('fade');
}

// showMessage 
var tt1;
function showMessage(status, msg, timer) {
	$('#tx-community-editgroup-status').empty().append('<p>'+msg+'</p>').removeClass('error').removeClass('wait').removeClass('success').addClass(status).slideDown('fade');
	window.clearTimeout(tt1);
	if (timer > 0) {
		tt1 = window.setTimeout('clearMessage()', timer);
	}
}

$(document).ready(function(){
	// init image uploader
	$('#tx-community-editgroup-imageupload').upload({
		name: 'tx_community[imageFile]',
		method: 'post',
		enctype: 'multipart/form-data',
		action: _FORM_ACTION,
		params: {
			'tx_community[ajaxAction]': 'saveImage',
			'tx_community[group]': _GROUP_ID
		},
		onSubmit: function() {
			showMessage('wait', _PLEASE_WAIT);
		},
		onComplete: function(response) {
			response = eval('('+response+')');
			$('#tx-community-group-image').attr('src', response.newImage);
			$('#tx-community-group-image').attr('width', response.newWidth);
			$('#tx-community-group-image').attr('height', response.newHeight);
			
			showMessage(response.status, response.msg, 5000);
		}
	});
	
    // init tabs
    $("#tx-community-editgroup-settings ul#tab_navigation").tabs({
    	fx: {
    		height: 'toggle',
    		opacity: 'toggle',
    		duration: 'slow'
    	},
    	select: function(e, ui) {
    		ui.tab.blur();
   			_TX_COMMUNITY_EDITGROUP_CURRENT_TAB = ui.panel.id;
   			switch (_TX_COMMUNITY_EDITGROUP_CURRENT_TAB) {
   				case 'GENERAL_SETTINGS':
   					$('#ajaxActionHolder').val('saveGeneral');
   				break;
   				case 'IMAGE_SETTINGS':
   					$('#ajaxActionHolder').val('saveImage');
   				break;
   				case 'MEMBER_SETTINGS':
   					$('#ajaxActionHolder').val('changeMemberStatus');
   				break;
   				case 'INVITE_MEMBER':
   					$('#ajaxActionHolder').val('inviteMember');
   				break;
   			}
    	}
    });
    
    $('#tx-community-editgroup-settings form.ajaxForm').ajaxForm({
    	'dataType': 'json',
    	'beforeSubmit': function() {
    		showMessage('wait', _PLEASE_WAIT);
    	},
    	'success': function(response) {
			showMessage(response.status, response.msg);
		}
   	});
	
	// activate ajax functions
	$('#ajaxActionHolder').val('saveGeneral');

	$("#makeAdminDialog").dialog({ 
		modal: true,
		autoOpen: false,
		draggable: false,
		resizable: false,
		overlay: { 
			opacity: 0.5, 
			background: "black" 
		},
		dialogClass: 'flora',
		buttons: { 
			"Ok": function(e) {
				showMessage('wait', _PLEASE_WAIT);
				$.post(
					_FORM_ACTION,
					{
						'tx_community[ajaxAction]': 'changeMemberStatus',
						'tx_community[group]': _GROUP_ID,
						'tx_community[memberUid]': _USER_ID,
						'tx_community[do]': 'makeAdmin'
					},
					function(response) {
						response = eval('('+response+')');
						if (response.status == 'success') {
							self.location.reload();
						}
					}
				);
				$(this).dialog("close"); 
			}, 
			"Cancel": function() { 
				$(this).dialog("close"); 
			} 
		}
	});	

	$("#removeMemberDialog").dialog({ 
		modal: true, 
		autoOpen: false,
		draggable: false,
		resizable: false,
		overlay: { 
			opacity: 0.5, 
			background: "black" 
		},
		dialogClass: 'flora',
		buttons: { 
			"Ok": function() { 
				showMessage('wait', _PLEASE_WAIT);
				$.post(
					_FORM_ACTION,
					{
						'tx_community[ajaxAction]': 'changeMemberStatus',
						'tx_community[group]': _GROUP_ID,
						'tx_community[memberUid]': _USER_ID,
						'tx_community[do]': 'removeMember'
					},
					function(response) {
						response = eval('('+response+')');
						showMessage(response.status, response.msg, 5000);
					}
				);
				$(this).dialog("close"); 
			}, 
			"Cancel": function() { 
				$(this).dialog("close"); 
			} 
		}
	});	

	$('.makeAdmin').click(function(e) {
		id = $(e.target).attr('id');
		id = id.replace('makeAdmin', '');
		_USER_ID = id;
		$("#makeAdminDialog").dialog('open');
	});
	
	$('.removeMember').click(function(e) {
		id = $(e.target).attr('id');
		id = id.replace('removeMember', '');
		_USER_ID = id;
		$("#removeMemberDialog").dialog('open');
	});
});
