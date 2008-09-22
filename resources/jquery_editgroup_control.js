
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
	_PLEASE_WAIT = (typeof _PLEASE_WAIT == 'undefined') ? '' : _PLEASE_WAIT;
	_FORM_ACTION = (typeof _FORM_ACTION == 'undefined') ? '' : _FORM_ACTION;
	_GROUP_ID = (typeof _GROUP_ID == 'undefined') ? 0 : _GROUP_ID;
	_USER_ID = (typeof _USER_ID == 'undefined') ? 0 : _USER_ID;

	
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
						if (response.status == 'success') {
							$('#memberRow'+_USER_ID).fadeOut('slow');
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

	$("#confirmRequestDialog").dialog({ 
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
						'tx_community[do]': 'confirmRequest'
					},
					function(response) {
						response = eval('('+response+')');
						showMessage(response.status, response.msg, 5000);
						if (response.status == 'success') {
							$('#memberRow'+_USER_ID).fadeOut('slow').find('td').each(function() {
								if ($(this).hasClass('member_status')) {
									$(this).empty();
								}
								if ($(this).hasClass('member_actions')) {
									$(this).empty();
								}
							}).appendTo('#MEMBER_SETTINGS_MEMBERS').fadeIn('slow');
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

	$("#rejectRequestDialog").dialog({ 
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
						'tx_community[do]': 'rejectRequest'
					},
					function(response) {
						response = eval('('+response+')');
						showMessage(response.status, response.msg, 5000);
						if (response.status == 'success') {
							$('#memberRow'+_USER_ID).fadeOut('slow');
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
	
	$('.confirmRequest').click(function(e) {
		id = $(e.target).attr('id');
		id = id.replace('confirmRequest', '');
		_USER_ID = id;
		$("#confirmRequestDialog").dialog('open');
	});
	
	$('.rejectRequest').click(function(e) {
		id = $(e.target).attr('id');
		id = id.replace('rejectRequest', '');
		_USER_ID = id;
		$("#rejectRequestDialog").dialog('open');
	});
	
	// Invite User
	function formatItem(row) {
		return row[0] + " (<strong>id: " + row[1] + "</strong>)";
	}
	function formatResult(row) {
		return row[0].replace(/(<.+?>)/gi, '');
	}

	$('#INVITE_MEMBER input[@name="tx_community[invite_search]"]').autocomplete(_FORM_ACTION, {
		width: 300,
		autoFill: true,
		multiple: true,
		/* matchContains: true, */
		formatItem: formatItem,
		formatResult: formatResult,
		extraParams: {
			'tx_community[ajaxAction]': 'inviteMember',
			'tx_community[do]': 'search',
			'tx_community[group]': _GROUP_ID
		},
	});
	$('#INVITE_MEMBER input[@name="tx_community[invite_search]"]').result(function(event, data, formatted) {
		var hidden = $('#inviteUids');
		console.log(hidden);
		hidden.val( (hidden.val() ? hidden.val() + ";" : hidden.val()) + data[1]);
	});

	
	$('#INVITE_MEMBER .doInviteLink').click(function() {
		var hidden = $('#inviteUids');
		$.post(
			_FORM_ACTION,
			{
				'tx_community[ajaxAction]': 'inviteMember',
				'tx_community[group]': _GROUP_ID,
				'tx_community[do]': 'invite',
				'tx_community[inviteUids]': hidden.val()
			},
			function(response) {
				response = eval('('+response+')');
				showMessage(response.status, response.msg, 5000);
			}
		);
	});
	
	
});
