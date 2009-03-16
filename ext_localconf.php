<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$PATH_community = t3lib_extMgm::extPath('community');

	// this is all here instead of in TS so that it is available in both, BE and FE
$TX_COMMUNITY = array(
	'applications' => array(
		'userProfile' => array(
			'classReference' => 'EXT:community/controller/class.tx_community_controller_userprofileapplication.php:tx_community_controller_UserProfileApplication',
			'label' => 'LLL:EXT:community/lang/locallang_applications.xml:userProfile',
			'accessControl' => array(
				'read' => 'LLL:EXT:community/lang/locallang_privacy.xml:privacy_userProfile_read'
			),
			'actions' => array(),
			'widgets' => array(
				'image' => array(
					'classReference' => 'EXT:community/controller/userprofile/class.tx_community_controller_userprofile_imagewidget.php:tx_community_controller_userprofile_ImageWidget',
					'label' => 'LLL:EXT:community/lang/locallang_applications.xml:userProfile_image',
					'accessControl' => array(
						'read' => 'LLL:EXT:community/lang/locallang_privacy.xml:privacy_userProfile_imageWidget_read'
					),
					'actions' => array(
						'index',
						'thumbnail',
						'customImage'
					),
					'defaultAction' => 'index'
				),
				'statusMessage' => array(
					'classReference' => 'EXT:community/controller/userprofile/class.tx_community_controller_userprofile_statusmessagewidget.php:tx_community_controller_userprofile_StatusMessageWidget',
					'label' => 'LLL:EXT:community/lang/locallang_applications.xml:userProfile_statusMessage',
					'accessControl' => false,
					'actions' => array(
						'index',
						'updateStatusMessage'
					),
					'defaultAction' => 'index'
				),
				'widget' => array(
					'classReference' => 'EXT:community/controller/userprofile/class.tx_community_controller_userprofile_widgetwidget.php:tx_community_controller_userprofile_WidgetWidget',
					'label' => 'LLL:EXT:community/lang/locallang_applications.xml:userProfile_widget',
					'actions' => array(
						'index'
					),
					'defaultAction' => 'index',
					'accessControl' => false,
				),
				'personalInformation' => array(
					'classReference' => 'EXT:community/controller/userprofile/class.tx_community_controller_userprofile_personalinformationwidget.php:tx_community_controller_userprofile_PersonalInformationWidget',
					'label' => 'LLL:EXT:community/lang/locallang_applications.xml:userProfile_personalInformation',
					'actions' => array(
						'index'
					),
					'defaultAction' => 'index',
					'accessControl' => array(
						'read' => 'LLL:EXT:community/lang/locallang_privacy.xml:privacy_userProfile_personalInformationWidget_read'
					)
				),
				'profileActions' => array(
					'classReference' => 'EXT:community/controller/userprofile/class.tx_community_controller_userprofile_profileactionswidget.php:tx_community_controller_userprofile_ProfileActionsWidget',
					'label' => 'LLL:EXT:community/lang/locallang_applications.xml:userProfile_profileActions',
					'actions' => array( // those are not the actual profile actions, but controller actions
						'index',
						'addAsFriend',
						'editRelationship',
						'setRelationships',
						'removeAsFriend'
					),
					'defaultAction' => 'index',
					'accessControl' => false
				),
				'friendsBirthdayList' => array(
					'classReference' => 'EXT:community/controller/userprofile/class.tx_community_controller_userprofile_friendsbirthdaylistwidget.php:tx_community_controller_userprofile_FriendsBirthdayListWidget',
					'label' => 'LLL:EXT:community/lang/locallang_applications.xml:userProfile_friendsBirthdayList',
					'accessControl' => false,
					'actions' => array(
						'index'
					),
					'defaultAction' => 'index'
				),
				'myGroups' => array(
					'classReference' => 'EXT:community/controller/userprofile/class.tx_community_controller_userprofile_mygroupswidget.php:tx_community_controller_userprofile_MyGroupsWidget',
					'label' => 'LLL:EXT:community/lang/locallang_applications.xml:userProfile_myGroups',
					'accessControl' => array(
						'read' => 'LLL:EXT:community/lang/locallang_privacy.xml:privacy_userProfile_myGroupsWidget_read'
					),
					'actions' => array(
						'index'
					),
					'defaultAction' => 'index'
				),
				'myFriends' => array(
					'classReference' => 'EXT:community/controller/userprofile/class.tx_community_controller_userprofile_myfriendswidget.php:tx_community_controller_userprofile_MyFriendsWidget',
					'label' => 'LLL:EXT:community/lang/locallang_applications.xml:userProfile_myFriends',
					'accessControl' => array(
						'read' => 'LLL:EXT:community/lang/locallang_privacy.xml:privacy_userProfile_myFriendsWidget_read'
					),
					'actions' => array(
						'index'
					),
					'defaultAction' => 'index'
				),
				'onlineFriends' => array(
					'classReference' => 'EXT:community/controller/userprofile/class.tx_community_controller_userprofile_onlinefriendswidget.php:tx_community_controller_userprofile_OnlineFriendsWidget',
					'label' => 'LLL:EXT:community/lang/locallang_applications.xml:userProfile_onlineFriends',
					'accessControl' => false,
					'actions' => array(
						'index'
					),
					'defaultAction' => 'index'
				),
				'lastVisitorsLogger' => array(
					'classReference' => 'EXT:community/controller/userprofile/class.tx_community_controller_userprofile_lastvisitorsloggerwidget.php:tx_community_controller_userprofile_LastVisitorsLoggerWidget',
					'label' => 'LLL:EXT:community/lang/locallang_applications.xml:userProfile_lastVisitorsLogger',
					'accessControl' => false,
					'actions' => array(
						'index'
					),
					'defaultAction' => 'index'
				),
				'lastVisitors' => array(
					'classReference' => 'EXT:community/controller/userprofile/class.tx_community_controller_userprofile_lastvisitorswidget.php:tx_community_controller_userprofile_LastVisitorsWidget',
					'label' => 'LLL:EXT:community/lang/locallang_applications.xml:userProfile_lastVisitors',
					'accessControl' => false,
					'actions' => array(
						'index'
					),
					'defaultAction' => 'index'
				),

/*
				'wall' => array(
					'classReference' => 'EXT:community/controller/userprofile/class.tx_community_controller_userprofile_wallwidget.php:tx_community_controller_userprofile_WallWidget',
					'label' => 'LLL:EXT:community/lang/locallang_applications.xml:userProfile_wallStream',
					'accessControl' => array(
						'read' => 'LLL:EXT:community/lang/locallang_privacy.xml:privacy_userProfile_wallWidget_read'
					),
					'actions' => array(
						'index'
					),
					'defaultAction' => 'index'
				),
				'activityStream' => array(
					'classReference' => 'EXT:community/controller/userprofile/class.tx_community_controller_userprofile_activitystreamwidget.php:tx_community_controller_userprofile_ActivityStreamWidget',
					'label' => 'LLL:EXT:community/lang/locallang_applications.xml:userProfile_activityStream',
					'accessControl' => array(
						'read' => 'LLL:EXT:community/lang/locallang_privacy.xml:privacy_userProfile_activityStreamWidget_read'
					),
					'actions' => array(
						'index'
					),
					'defaultAction' => 'index'
				)
*/
			)
		),
		'groupProfile' => array(
			'classReference' => 'EXT:community/controller/class.tx_community_controller_groupprofileapplication.php:tx_community_controller_GroupProfileApplication',
			'label' => 'LLL:EXT:community/lang/locallang_applications.xml:groupProfile',
			'widgets' => array(
				'image' => array(
					'classReference' => 'EXT:community/controller/groupprofile/class.tx_community_controller_groupprofile_imagewidget.php:tx_community_controller_groupprofile_ImageWidget',
					'label' => 'LLL:EXT:community/lang/locallang_applications.xml:groupProfile_image',
					'actions' => array(),
					'accessControl' => false,
					'actions' => array(
						'index',
						'thumbnail',
						'customImage'
					),
					'defaultAction' => 'index'
				),
				'profileActions' => array(
					'classReference' => 'EXT:community/controller/groupprofile/class.tx_community_controller_groupprofile_profileactionswidget.php:tx_community_controller_groupprofile_ProfileActionsWidget',
					'label' => 'LLL:EXT:community/lang/locallang_applications.xml:groupProfile_profileActions',
					'actions' => array( // those are not the actual profile actions, but controller actions
						'index',
						'joinGroup',
						'leaveGroup'
					),
					'defaultAction' => 'index',
					'accessControl' => false
				),
				'birthdayList' => array(
					'classReference' => 'EXT:community/controller/groupprofile/class.tx_community_controller_groupprofile_birthdaylistwidget.php:tx_community_controller_groupprofile_BirthdayListWidget',
					'label' => 'LLL:EXT:community/lang/locallang_applications.xml:groupProfile_birthdayList',
					'accessControl' => false,
					'actions' => array(
						'index'
					),
					'defaultAction' => 'index'
				),
				'groupInformation' => array(
					'classReference' => 'EXT:community/controller/groupprofile/class.tx_community_controller_groupprofile_groupinformationwidget.php:tx_community_controller_groupprofile_GroupInformationWidget',
					'label' => 'LLL:EXT:community/lang/locallang_applications.xml:groupProfile_groupInformation',
					'actions' => array(
						'index'
					),
					'defaultAction' => 'index',
					'accessControl' => false
				),
				'memberList' => array(
					'classReference' => 'EXT:community/controller/groupprofile/class.tx_community_controller_groupprofile_memberlistwidget.php:tx_community_controller_groupprofile_MemberListWidget',
					'label' => 'LLL:EXT:community/lang/locallang_applications.xml:groupProfile_memberList',
					'actions' => array(
						'index'
					),
					'defaultAction' => 'index',
					'accessControl' => false
				)
			),
		),
		'search' => array(
			'classReference' => 'EXT:community/controller/class.tx_community_controller_searchapplication.php:tx_community_controller_SearchApplication',
			'label' => 'LLL:EXT:community/lang/locallang_applications.xml:search',
			'accessControl' => false,
			'actions' => array(
				'index',
				'search'
			),
			'defaultAction' => 'index',
			'widgets' => array(
				'quickSearchInput' => array(
					'classReference' => 'EXT:community/controller/search/class.tx_community_controller_search_quicksearchinputwidget.php:tx_community_controller_search_QuickSearchInputWidget',
					'label' => 'LLL:EXT:community/lang/locallang_applications.xml:search_quickSearchInput',
					'actions' => array(
						'index'
					),
					'defaultAction' => 'index',
					'accessControl' => false
				)
			)
		),
		'userList' => array(
			'classReference' => 'EXT:community/controller/class.tx_community_controller_userlistapplication.php:tx_community_controller_UserListApplication',
			'accessControl' => false,
			'excludeFromPluginListing' => true,
			'action' => array(
				'index'
			),
			'defaultAction' => 'index'
		),
		'listGroups' => array(
			'classReference' => 'EXT:community/controller/class.tx_community_controller_listgroupsapplication.php:tx_community_controller_ListGroupsApplication',
			'label' => 'LLL:EXT:community/lang/locallang_applications.xml:listGroups',
			'accessControl' => false,
			'actions' => array( // those are not the actual profile actions, but controller actions
				'index'
			),
			'defaultAction' => 'index',
		),
		'editGroup' => array(
			'classReference' => 'EXT:community/controller/class.tx_community_controller_editgroupapplication.php:tx_community_controller_EditGroupApplication',
			'label' => 'LLL:EXT:community/lang/locallang_applications.xml:editGroup',
			'accessControl' => false,
			'actions' => array( // those are not the actual profile actions, but controller actions
				'index',
				'saveData',
				'inviteUser',
				'addMember',
				'removeMember',
				'addAdmin',
				'removeAdmin'
			),
			'defaultAction' => 'index',
		),
		'createGroup' => array(
			'classReference' => 'EXT:community/controller/class.tx_community_controller_creategroupapplication.php:tx_community_controller_CreateGroupApplication',
			'label' => 'LLL:EXT:community/lang/locallang_applications.xml:createGroup',
			'accessControl' => false,
			'actions' => array( // those are not the actual profile actions, but controller actions
				'index',
				'createGroup'
			),
			'defaultAction' => 'index',
		),
		'privacy' => array(
			'classReference' => 'EXT:community/controller/class.tx_community_controller_privacyapplication.php:tx_community_controller_PrivacyApplication',
			'label' => 'LLL:EXT:community/lang/locallang_applications.xml:privacy',
			'accessControl' => false,
			'actions' => array(
				'index',
				'savePermissions'
			),
			'defaultAction' => 'index'
		),
		'invite' => array(
			'classReference' => 'EXT:community/controller/class.tx_community_controller_inviteapplication.php:tx_community_controller_InviteApplication',
			'label' => 'LLL:EXT:community/lang/locallang_applications.xml:invite',
			'accessControl' => false,
			'actions' => array(
				'index',
				'sendInvitation',
				'errorMessage',
				'successMessage'
			),
			'defaultAction' => 'index'
		),
/*
		'inbox' => array(
			// the message center stuff
		)
*/
	),
	'tableFieldMaps' => array(
		'fe_users' => array(

		)
	)
);

	// adding save+close+new buttons for some tables
t3lib_extMgm::addUserTSConfig('options.saveDocNew.tx_community_acl_role = 1');
t3lib_extMgm::addUserTSConfig('options.saveDocNew.tx_community_acl_rule = 1');
t3lib_extMgm::addUserTSConfig('options.saveDocNew.tx_community_friend = 1');

	// adding hook connector for mm_forum
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mm_forum']['forum']['profileLink_postLinkGen'][] = 'EXT:community/hooks/class.tx_community_hooks_mmforum.php:&tx_community_hooks_mmforum';

	// Page module content element information/preview hook
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info']['community_CommunityApplication'][] = 'EXT:community/classes/class.tx_community_cmslayouthelper.php:tx_community_CmsLayoutHelper->getExtensionSummary';

?>
