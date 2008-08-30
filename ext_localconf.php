<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$PATH_community = t3lib_extMgm::extPath('community');

$TX_COMMUNITY = array(
	'applications' => array(
		'UserProfile' => array(
			'classReference' => 'EXT:community/controller/class.tx_community_controller_userprofileapplication.php:tx_community_controller_UserProfileApplication',
			'label' => 'LLL:EXT:community/lang/locallang_applications.xml:userProfile',
			'widgets' => array(
				'image' => array(
					'classReference' => 'EXT:community/controller/userprofile/class.tx_community_controller_userprofile_imagewidget.php:tx_community_controller_userprofile_ImageWidget',
					'label' => 'LLL:EXT:community/lang/locallang_applications.xml:userProfile.image'
				),
				'personalInformation' => array(
					'classReference' => 'EXT:community/controller/userprofile/class.tx_community_controller_userprofile_personalinformationwidget.php:tx_community_controller_userprofile_PersonalInformationWidget',
					'label' => 'LLL:EXT:community/lang/locallang_applications.xml:userProfile.personalInformation'
				),
			)
		),
/*
		'GroupProfile' => array(
			'classReference' => 'EXT:community/controller/class.tx_community_controller_groupprofileapplication.php:tx_community_controller_GroupProfileApplication',
			'label' => 'LLL:EXT:community/lang/locallang_applications.xml:groupProfile',
			'widgets' => array(
				'image' => array(
					'classReference' => 'EXT:community/controller/groupprofile/class.tx_community_controller_groupprofile_imagewidget.php:tx_community_controller_groupprofile_ImageWidget',
					'label' => 'LLL:EXT:community/lang/locallang_applications.xml:groupProfile.image'
				),
				'information' => array(
					'classReference' => 'EXT:community/controller/groupprofile/class.tx_community_controller_groupprofile_informationwidget.php:tx_community_controller_groupprofile_InformationWidget',
					'label' => 'LLL:EXT:community/lang/locallang_applications.xml:groupProfile.information'
				)
			)
		),
*/
		'Privacy' => array(
			'classReference' => 'EXT:community/controller/class.tx_community_controller_privacyapplication.php:tx_community_controller_PrivacyApplication',
			'label' => 'LLL:EXT:community/lang/locallang_applications.xml:privacy',
			'widgets' => array(

			)
		)
	)
);

	// adding save+close+new buttons for some tables
t3lib_extMgm::addUserTSConfig('options.saveDocNew.tx_community_acl_role = 1');
t3lib_extMgm::addUserTSConfig('options.saveDocNew.tx_community_acl_rule = 1');
t3lib_extMgm::addUserTSConfig('options.saveDocNew.tx_community_friend = 1');

?>