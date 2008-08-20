<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}


$TX_COMMUNITY = array(
	'applications' => array(
		'userProfile' => array(
			'label' => 'LLL:EXT:community/lang/locallang_applications.xml:userProfile',
			'widgets' => array(
				'image' => array(
					'classReference' => 'EXT:community/controller/userprofile/class.tx_community_controller_userprofile_imagewidget.php:tx_community_controller_userprofile_ImageWidget',
					'label' => 'LLL:EXT:community/lang/locallang_applications.xml:userProfile.image'
				),
				'information' => array(
					'classReference' => 'EXT:community/controller/userprofile/class.tx_community_controller_userprofile_informationwidget.php:tx_community_controller_userprofile_InformationWidget',
					'label' => 'LLL:EXT:community/lang/locallang_applications.xml:userProfile.information'
				)
			)
		),
		'groupProfile' => array(
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
		)
	)
);


?>