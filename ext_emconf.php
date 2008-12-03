<?php

########################################################################
# Extension Manager/Repository config file for ext: "community"
#
# Auto generated 25-11-2008 23:04
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Community',
	'description' => 'A flexible and extensible community system.',
	'category' => 'plugin',
	'author' => 'Ingo Renner',
	'author_email' => 'ingo@typo3.org',
	'shy' => '',
	'dependencies' => 0,
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'alpha',
	'internal' => '',
	'uploadfolder' => 1,
	'createDirs' => '',
	'modify_tables' => 'fe_users',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '0.0.99',
	'constraints' => array(
		'depends' => array(
			'0' => 'felogin',
			'pagebrowse' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:162:{s:9:"ChangeLog";s:4:"f5c1";s:5:"ToDos";s:4:"f82b";s:12:"ext_icon.gif";s:4:"1bdc";s:17:"ext_localconf.php";s:4:"76e3";s:14:"ext_tables.php";s:4:"09c5";s:14:"ext_tables.sql";s:4:"3426";s:7:"tca.php";s:4:"88a2";s:44:"classes/class.tx_community_accessmanager.php";s:4:"1d38";s:49:"classes/class.tx_community_applicationmanager.php";s:4:"7ba9";s:46:"classes/class.tx_community_cmslayouthelper.php";s:4:"7953";s:50:"classes/class.tx_community_localizationmanager.php";s:4:"7233";s:45:"classes/class.tx_community_profilefactory.php";s:4:"5f96";s:39:"classes/class.tx_community_registry.php";s:4:"5ebc";s:39:"classes/class.tx_community_template.php";s:4:"def0";s:42:"classes/acl/class.tx_community_acl_acl.php";s:4:"ea73";s:48:"classes/acl/class.tx_community_acl_exception.php";s:4:"4998";s:47:"classes/acl/class.tx_community_acl_resource.php";s:4:"6b5c";s:43:"classes/acl/class.tx_community_acl_role.php";s:4:"2ccb";s:51:"classes/acl/class.tx_community_acl_roleregistry.php";s:4:"2fa7";s:60:"classes/acl/class.tx_community_acl_roleregistryexception.php";s:4:"7c4e";s:74:"classes/exception/class.tx_community_exception_languagefileunavailable.php";s:4:"92ab";s:62:"classes/exception/class.tx_community_exception_noprofileid.php";s:4:"2a3b";s:65:"classes/exception/class.tx_community_exception_unknownprofile.php";s:4:"e172";s:69:"classes/exception/class.tx_community_exception_unknownprofiletype.php";s:4:"5de1";s:56:"classes/viewhelper/class.tx_community_viewhelper_age.php";s:4:"6c74";s:57:"classes/viewhelper/class.tx_community_viewhelper_date.php";s:4:"51a7";s:60:"classes/viewhelper/class.tx_community_viewhelper_getcobj.php";s:4:"0d3f";s:57:"classes/viewhelper/class.tx_community_viewhelper_link.php";s:4:"a75d";s:56:"classes/viewhelper/class.tx_community_viewhelper_lll.php";s:4:"c1e1";s:58:"classes/viewhelper/class.tx_community_viewhelper_nl2br.php";s:4:"92c2";s:55:"classes/viewhelper/class.tx_community_viewhelper_ts.php";s:4:"4ddf";s:59:"classes/viewhelper/class.tx_community_viewhelper_widget.php";s:4:"c80b";s:63:"classes/viewhelper/class.tx_community_viewhelper_zodiacsign.php";s:4:"ac40";s:73:"controller/class.tx_community_controller_abstractcommunityapplication.php";s:4:"f6d4";s:79:"controller/class.tx_community_controller_abstractcommunityapplicationwidget.php";s:4:"ec41";s:65:"controller/class.tx_community_controller_communityapplication.php";s:4:"ea71";s:67:"controller/class.tx_community_controller_creategroupapplication.php";s:4:"897f";s:65:"controller/class.tx_community_controller_editgroupapplication.php";s:4:"8c77";s:68:"controller/class.tx_community_controller_groupprofileapplication.php";s:4:"f73d";s:62:"controller/class.tx_community_controller_inviteapplication.php";s:4:"78da";s:66:"controller/class.tx_community_controller_listgroupsapplication.php";s:4:"f037";s:63:"controller/class.tx_community_controller_privacyapplication.php";s:4:"067e";s:62:"controller/class.tx_community_controller_searchapplication.php";s:4:"4df9";s:64:"controller/class.tx_community_controller_userlistapplication.php";s:4:"8c69";s:67:"controller/class.tx_community_controller_userprofileapplication.php";s:4:"3b8e";s:93:"controller/groupprofile/class.tx_community_controller_groupprofile_groupinformationwidget.php";s:4:"530d";s:82:"controller/groupprofile/class.tx_community_controller_groupprofile_imagewidget.php";s:4:"6af2";s:87:"controller/groupprofile/class.tx_community_controller_groupprofile_memberlistwidget.php";s:4:"20e8";s:91:"controller/groupprofile/class.tx_community_controller_groupprofile_profileactionswidget.php";s:4:"666f";s:81:"controller/search/class.tx_community_controller_search_quicksearchinputwidget.php";s:4:"a228";s:84:"controller/userprofile/class.tx_community_controller_userprofile_basicinfowidget.php";s:4:"43e1";s:86:"controller/userprofile/class.tx_community_controller_userprofile_contactinfowidget.php";s:4:"e40e";s:94:"controller/userprofile/class.tx_community_controller_userprofile_friendsbirthdaylistwidget.php";s:4:"b20b";s:80:"controller/userprofile/class.tx_community_controller_userprofile_imagewidget.php";s:4:"6841";s:93:"controller/userprofile/class.tx_community_controller_userprofile_lastvisitorsloggerwidget.php";s:4:"ebf7";s:87:"controller/userprofile/class.tx_community_controller_userprofile_lastvisitorswidget.php";s:4:"5cc8";s:83:"controller/userprofile/class.tx_community_controller_userprofile_mygroupswidget.php";s:4:"03db";s:88:"controller/userprofile/class.tx_community_controller_userprofile_onlinefriendswidget.php";s:4:"cf77";s:94:"controller/userprofile/class.tx_community_controller_userprofile_personalinformationwidget.php";s:4:"a035";s:89:"controller/userprofile/class.tx_community_controller_userprofile_profileactionswidget.php";s:4:"e91c";s:88:"controller/userprofile/class.tx_community_controller_userprofile_statusmessagewidget.php";s:4:"d3fc";s:81:"controller/userprofile/class.tx_community_controller_userprofile_widgetwidget.php";s:4:"fc78";s:34:"flexforms/flexform_application.xml";s:4:"fbc8";s:42:"hooks/class.tx_community_hooks_mmforum.php";s:4:"52e2";s:45:"interfaces/interface.tx_community_command.php";s:4:"b4ac";s:64:"interfaces/interface.tx_community_communityapplicationwidget.php";s:4:"2a7a";s:72:"interfaces/interface.tx_community_communityapplicationwidgetprovider.php";s:4:"b4ac";s:65:"interfaces/interface.tx_community_groupprofileactionsprovider.php";s:4:"af58";s:64:"interfaces/interface.tx_community_userprofileactionsprovider.php";s:4:"22f7";s:57:"interfaces/interface.tx_community_userprofileprovider.php";s:4:"32d7";s:42:"interfaces/interface.tx_community_view.php";s:4:"52f7";s:48:"interfaces/interface.tx_community_viewhelper.php";s:4:"585a";s:57:"interfaces/acl/interface.tx_community_acl_aclresource.php";s:4:"1bc6";s:53:"interfaces/acl/interface.tx_community_acl_aclrole.php";s:4:"da4e";s:52:"interfaces/acl/interface.tx_community_acl_assert.php";s:4:"273e";s:31:"lang/locallang_applications.xml";s:4:"15e3";s:30:"lang/locallang_creategroup.xml";s:4:"1c29";s:21:"lang/locallang_db.xml";s:4:"a1e7";s:28:"lang/locallang_editgroup.xml";s:4:"bf86";s:24:"lang/locallang_group.xml";s:4:"2f80";s:48:"lang/locallang_groupprofile_groupinformation.xml";s:4:"301f";s:42:"lang/locallang_groupprofile_memberlist.xml";s:4:"676e";s:46:"lang/locallang_groupprofile_profileactions.xml";s:4:"6a73";s:25:"lang/locallang_invite.xml";s:4:"c8ca";s:29:"lang/locallang_listgroups.xml";s:4:"adfa";s:26:"lang/locallang_privacy.xml";s:4:"3a4e";s:25:"lang/locallang_search.xml";s:4:"f1e5";s:50:"lang/locallang_userprofile_friendsbirthdaylist.xml";s:4:"fed6";s:42:"lang/locallang_userprofile_imagewidget.xml";s:4:"c51c";s:43:"lang/locallang_userprofile_lastvisitors.xml";s:4:"ce3b";s:39:"lang/locallang_userprofile_mygroups.xml";s:4:"d8fe";s:44:"lang/locallang_userprofile_onlinefriends.xml";s:4:"8bf5";s:50:"lang/locallang_userprofile_personalinformation.xml";s:4:"0a4e";s:45:"lang/locallang_userprofile_profileactions.xml";s:4:"5643";s:37:"lang/locallang_userprofile_widget.xml";s:4:"b915";s:63:"model/class.tx_community_model_abstractcommunityapplication.php";s:4:"d68e";s:50:"model/class.tx_community_model_abstractprofile.php";s:4:"6807";s:42:"model/class.tx_community_model_account.php";s:4:"6e8e";s:40:"model/class.tx_community_model_group.php";s:4:"4801";s:47:"model/class.tx_community_model_groupgateway.php";s:4:"7391";s:47:"model/class.tx_community_model_groupprofile.php";s:4:"c673";s:48:"model/class.tx_community_model_modelcriteria.php";s:4:"2fb1";s:39:"model/class.tx_community_model_user.php";s:4:"c9ed";s:46:"model/class.tx_community_model_usergateway.php";s:4:"213d";s:46:"model/class.tx_community_model_userprofile.php";s:4:"485d";s:32:"resources/jquery.autocomplete.js";s:4:"7ec8";s:24:"resources/jquery.form.js";s:4:"778b";s:34:"resources/jquery.ocupload-1.1.1.js";s:4:"211c";s:37:"resources/jquery_editgroup_control.js";s:4:"aa95";s:33:"resources/icons/default_group.png";s:4:"09d3";s:32:"resources/icons/default_user.png";s:4:"bd0d";s:48:"resources/icons/tables/tx_community_acl_role.gif";s:4:"475a";s:48:"resources/icons/tables/tx_community_acl_rule.gif";s:4:"475a";s:46:"resources/icons/tables/tx_community_friend.gif";s:4:"475a";s:45:"resources/icons/tables/tx_community_group.gif";s:4:"475a";s:41:"resources/templates/creategroup/index.htm";s:4:"2597";s:39:"resources/templates/editgroup/index.htm";s:4:"8d9f";s:59:"resources/templates/groupprofile/groupinformationwidget.htm";s:4:"170f";s:53:"resources/templates/groupprofile/memberlistwidget.htm";s:4:"3598";s:57:"resources/templates/groupprofile/profileactionswidget.htm";s:4:"08a1";s:36:"resources/templates/invite/index.htm";s:4:"bbc8";s:40:"resources/templates/listgroups/index.htm";s:4:"6d36";s:37:"resources/templates/privacy/index.htm";s:4:"7e9f";s:36:"resources/templates/search/index.htm";s:4:"2ad0";s:53:"resources/templates/search/quicksearchinputwidget.htm";s:4:"5ffe";s:38:"resources/templates/userlist/index.htm";s:4:"609b";s:61:"resources/templates/userprofile/friendsbirthdaylistwidget.htm";s:4:"6266";s:54:"resources/templates/userprofile/lastvisitorswidget.htm";s:4:"f0bb";s:50:"resources/templates/userprofile/mygroupswidget.htm";s:4:"e141";s:55:"resources/templates/userprofile/onlinefriendswidget.htm";s:4:"eef1";s:61:"resources/templates/userprofile/personalinformationwidget.htm";s:4:"0e80";s:56:"resources/templates/userprofile/profileactionswidget.htm";s:4:"f1a0";s:55:"resources/templates/userprofile/statusmessagewidget.htm";s:4:"0d5e";s:48:"resources/templates/userprofile/widgetwidget.htm";s:4:"5eb9";s:30:"static/community/constants.txt";s:4:"de9e";s:26:"static/community/setup.txt";s:4:"3639";s:45:"view/class.tx_community_view_abstractview.php";s:4:"d613";s:73:"view/creategroup/class.tx_community_view_creategroup_creategrouperror.php";s:4:"4696";s:62:"view/creategroup/class.tx_community_view_creategroup_index.php";s:4:"2c93";s:58:"view/editgroup/class.tx_community_view_editgroup_index.php";s:4:"684b";s:77:"view/groupprofile/class.tx_community_view_groupprofile_contentobjectimage.php";s:4:"b310";s:75:"view/groupprofile/class.tx_community_view_groupprofile_groupinformation.php";s:4:"6b80";s:69:"view/groupprofile/class.tx_community_view_groupprofile_memberlist.php";s:4:"59d2";s:73:"view/groupprofile/class.tx_community_view_groupprofile_profileactions.php";s:4:"dee5";s:52:"view/invite/class.tx_community_view_invite_index.php";s:4:"fdba";s:54:"view/invite/class.tx_community_view_invite_message.php";s:4:"f865";s:60:"view/listgroups/class.tx_community_view_listgroups_index.php";s:4:"ceb2";s:54:"view/privacy/class.tx_community_view_privacy_index.php";s:4:"b79e";s:52:"view/search/class.tx_community_view_search_index.php";s:4:"82c9";s:63:"view/search/class.tx_community_view_search_quicksearchinput.php";s:4:"178d";s:56:"view/userlist/class.tx_community_view_userlist_index.php";s:4:"ef59";s:75:"view/userprofile/class.tx_community_view_userprofile_contentobjectimage.php";s:4:"5fbe";s:73:"view/userprofile/class.tx_community_view_userprofile_editrelationship.php";s:4:"4f3b";s:76:"view/userprofile/class.tx_community_view_userprofile_friendsbirthdaylist.php";s:4:"caf0";s:66:"view/userprofile/class.tx_community_view_userprofile_htmlimage.php";s:4:"5edd";s:69:"view/userprofile/class.tx_community_view_userprofile_lastvisitors.php";s:4:"cba4";s:65:"view/userprofile/class.tx_community_view_userprofile_mygroups.php";s:4:"b80a";s:70:"view/userprofile/class.tx_community_view_userprofile_onlinefriends.php";s:4:"f97a";s:76:"view/userprofile/class.tx_community_view_userprofile_personalinformation.php";s:4:"2776";s:71:"view/userprofile/class.tx_community_view_userprofile_profileactions.php";s:4:"f09d";s:70:"view/userprofile/class.tx_community_view_userprofile_statusmessage.php";s:4:"ff69";s:63:"view/userprofile/class.tx_community_view_userprofile_widget.php";s:4:"1d9f";}',
	'suggests' => array(
	),
);

?>