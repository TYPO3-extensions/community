
var _TX_COMMUNITY_EDITGROUP_CURRENT_TAB = 'GENERAL_SETTINGS';
$(document).ready(function(){
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
    
    $('#tx-community-editgroup-settings form').ajaxForm({
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
	
	// showMessage 
	function showMessage(status, msg) {
		$('#tx-community-editgroup-status').empty().append('<p>'+msg+'</p>').removeClass('error').removeClass('wait').removeClass('success').addClass(status).slideDown();
	}
});
