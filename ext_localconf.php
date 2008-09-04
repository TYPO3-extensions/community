<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$PATH_community = t3lib_extMgm::extPath('community');

	// this is all here instead of in TS so that it is available in both, BE and FE
$TX_COMMUNITY = array(
	'applications' => array(
		'UserProfile' => array(
			'classReference' => 'EXT:community/controller/class.tx_community_controller_userprofileapplication.php:tx_community_controller_UserProfileApplication',
			'label' => 'LLL:EXT:community/lang/locallang_applications.xml:userProfile',
			'accessControl' => array(
				'read' => 'LLL:EXT:community/lang/locallang_privacy.xml:privacy.userProfile.read'
			),
			'widgets' => array(
				'image' => array(
					'classReference' => 'EXT:community/controller/userprofile/class.tx_community_controller_userprofile_imagewidget.php:tx_community_controller_userprofile_ImageWidget',
					'label' => 'LLL:EXT:community/lang/locallang_applications.xml:userProfile.image',
					'actions' => array(), // TODO move execute() stuff to at least indexAction()
					'accessControl' => array(
						'read' => 'LLL:EXT:community/lang/locallang_privacy.xml:privacy.userProfile.imageWidget.read',
//						'addComment' => 'LLL:EXT:community/lang/locallang_privacy.xml:privacy.userProfile.imageWidget.addComment'
					)
				),
				'personalInformation' => array(
					'classReference' => 'EXT:community/controller/userprofile/class.tx_community_controller_userprofile_personalinformationwidget.php:tx_community_controller_userprofile_PersonalInformationWidget',
					'label' => 'LLL:EXT:community/lang/locallang_applications.xml:userProfile.personalInformation',
					'actions' => array(), // TODO move execute() stuff to at least indexAction()
					'accessControl' => array(
						'read' => 'LLL:EXT:community/lang/locallang_privacy.xml:privacy.userProfile.personalInformationWidget.read'
					)
				),
				'profileActions' => array(
					'classReference' => 'EXT:community/controller/userprofile/class.tx_community_controller_userprofile_profileactionswidget.php:tx_community_controller_userprofile_ProfileActionsWidget',
					'label' => 'LLL:EXT:community/lang/locallang_applications.xml:userProfile.profileActions',
					'actions' => array( // those are not the actual profile actions, but controller actions
						'index',
						'addAsFriend'
					),
					'defaultAction' => 'index',
					'accessControl' => false
				)
			)
		),
		'GroupProfile' => array(
			'classReference' => 'EXT:community/controller/class.tx_community_controller_groupprofileapplication.php:tx_community_controller_GroupProfileApplication',
			'label' => 'LLL:EXT:community/lang/locallang_applications.xml:groupProfile',
			'widgets' => array(
				'profileActions' => array(
					'classReference' => 'EXT:community/controller/groupprofile/class.tx_community_controller_groupprofile_profileactionswidget.php:tx_community_controller_groupprofile_ProfileActionsWidget',
					'label' => 'LLL:EXT:community/lang/locallang_applications.xml:groupProfile_profileActions',
					'actions' => array( // those are not the actual profile actions, but controller actions
						'index',
						'joinGroup'
					),
					'defaultAction' => 'index',
					'accessControl' => false
				)
			)
		),
		'EditGroup' => array(
			'classReference' => 'EXT:community/controller/class.tx_community_controller_editgroupapplication.php:tx_community_controller_EditGroupApplication',
			'label' => 'LLL:EXT:community/lang/locallang_applications.xml:editGroup',
			'accessControl' => false,
			'actions' => array( // those are not the actual profile actions, but controller actions
				'index',
				'saveData',
				'inviteUser',
				'removeMember',
				'changeAdmin'
			),
			'defaultAction' => 'index',
		),
		'Privacy' => array(
			'classReference' => 'EXT:community/controller/class.tx_community_controller_privacyapplication.php:tx_community_controller_PrivacyApplication',
			'label' => 'LLL:EXT:community/lang/locallang_applications.xml:privacy',
			'accessControl' => false,
			'actions' => array(
				'index',
				'savePermissions'
			),
			'defaultAction' => 'index'
		)
	)
);

	// adding save+close+new buttons for some tables
t3lib_extMgm::addUserTSConfig('options.saveDocNew.tx_community_acl_role = 1');
t3lib_extMgm::addUserTSConfig('options.saveDocNew.tx_community_acl_rule = 1');
t3lib_extMgm::addUserTSConfig('options.saveDocNew.tx_community_friend = 1');

?>