<?php
if (!defined ('TYPO3_MODE')) die ('Access denied.');

Tx_Extbase_Utility_Extension::registerPlugin(
	$_EXTKEY,
	'Pi1',
	'Community'
);

t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Community');


t3lib_extMgm::addLLrefForTCAdescr('tx_community_domain_model_group','EXT:community/Resources/Private/Language/locallang_csh_tx_community_domain_model_group.xml');
t3lib_extMgm::allowTableOnStandardPages('tx_community_domain_model_group');
$TCA['tx_community_domain_model_group'] = array (
	'ctrl' => array (
		'title'             => 'LLL:EXT:community/Resources/Private/Language/locallang_db.xml:tx_community_domain_model_group',
		'label' 			=> 'name',
		'tstamp' 			=> 'tstamp',
		'crdate' 			=> 'crdate',
		'versioningWS' 		=> 2,
		'versioning_followPages'	=> TRUE,
		'origUid' 			=> 't3_origuid',
		'languageField' 	=> 'sys_language_uid',
		'transOrigPointerField' 	=> 'l18n_parent',
		'transOrigDiffSourceField' 	=> 'l18n_diffsource',
		'delete' 			=> 'deleted',
		'enablecolumns' 	=> array(
			'disabled' => 'hidden'
			),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/Tca.php',
		'iconfile' 			=> t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_community_domain_model_group.gif'
	)
);


t3lib_extMgm::addLLrefForTCAdescr('tx_community_domain_model_aclrole','EXT:community/Resources/Private/Language/locallang_csh_tx_community_domain_model_aclrole.xml');
t3lib_extMgm::allowTableOnStandardPages('tx_community_domain_model_aclrole');
$TCA['tx_community_domain_model_aclrole'] = array (
	'ctrl' => array (
		'title'             => 'LLL:EXT:community/Resources/Private/Language/locallang_db.xml:tx_community_domain_model_aclrole',
		'label' 			=> 'name',
		'tstamp' 			=> 'tstamp',
		'crdate' 			=> 'crdate',
		'versioningWS' 		=> 2,
		'versioning_followPages'	=> TRUE,
		'origUid' 			=> 't3_origuid',
		'languageField' 	=> 'sys_language_uid',
		'transOrigPointerField' 	=> 'l18n_parent',
		'transOrigDiffSourceField' 	=> 'l18n_diffsource',
		'delete' 			=> 'deleted',
		'enablecolumns' 	=> array(
			'disabled' => 'hidden'
			),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/Tca.php',
		'iconfile' 			=> t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_community_domain_model_aclrole.gif'
	)
);

t3lib_extMgm::addLLrefForTCAdescr('tx_community_domain_model_aclrule','EXT:community/Resources/Private/Language/locallang_csh_tx_community_domain_model_aclrule.xml');
t3lib_extMgm::allowTableOnStandardPages('tx_community_domain_model_aclrule');
$TCA['tx_community_domain_model_aclrule'] = array (
	'ctrl' => array (
		'title'             => 'LLL:EXT:community/Resources/Private/Language/locallang_db.xml:tx_community_domain_model_aclrule',
		'label' 			=> 'name',
		'tstamp' 			=> 'tstamp',
		'crdate' 			=> 'crdate',
		'versioningWS' 		=> 2,
		'versioning_followPages'	=> TRUE,
		'origUid' 			=> 't3_origuid',
		'languageField' 	=> 'sys_language_uid',
		'transOrigPointerField' 	=> 'l18n_parent',
		'transOrigDiffSourceField' 	=> 'l18n_diffsource',
		'delete' 			=> 'deleted',
		'enablecolumns' 	=> array(
			'disabled' => 'hidden'
			),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/Tca.php',
		'iconfile' 			=> t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_community_domain_model_aclrule.gif'
	)
);

t3lib_extMgm::addLLrefForTCAdescr('tx_community_domain_model_relation','EXT:community/Resources/Private/Language/locallang_csh_tx_community_domain_model_relation.xml');
t3lib_extMgm::allowTableOnStandardPages('tx_community_domain_model_relation');
$TCA['tx_community_domain_model_relation'] = array (
	'ctrl' => array (
		'title'             => 'LLL:EXT:community/Resources/Private/Language/locallang_db.xml:tx_community_domain_model_relation',
		'label' 			=> 'name',
		'tstamp' 			=> 'tstamp',
		'crdate' 			=> 'crdate',
		'versioningWS' 		=> 2,
		'versioning_followPages'	=> TRUE,
		'origUid' 			=> 't3_origuid',
		'languageField' 	=> 'sys_language_uid',
		'transOrigPointerField' 	=> 'l18n_parent',
		'transOrigDiffSourceField' 	=> 'l18n_diffsource',
		'delete' 			=> 'deleted',
		'enablecolumns' 	=> array(
			'disabled' => 'hidden'
			),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/Tca.php',
		'iconfile' 			=> t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_community_domain_model_relation.gif'
	)
);

// flexform to select the action
$extensionName = t3lib_div::underscoredToUpperCamelCase($_EXTKEY);
$pluginSignature = strtolower($extensionName) . '_pi1';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
t3lib_extMgm::addPiFlexFormValue($pluginSignature, 'FILE:EXT:'.$_EXTKEY.'/Configuration/FlexForm/Actions.xml');

// extend fe_users
t3lib_div::loadTCA('fe_users');
$feUserColumns  = array(
	'political_view' => array(
		'exclude' => 0,
		'label'	=> 'LLL:EXT:community/Resources/Private/Language/locallang.xml:profile.details.politicalView',
		'config' => array(
			'type' => 'text',
			'cols' => 30,
			'rows' => 5
		)
	),
	'religious_view' => array(
		'exclude' => 0,
		'label'	=> 'LLL:EXT:community/Resources/Private/Language/locallang.xml:profile.details.religiousView',
		'config' => array(
			'type' => 'text',
			'cols' => 30,
			'rows' => 5
		)
	),
	'activities' => array(
		'exclude' => 0,
		'label'	=> 'LLL:EXT:community/Resources/Private/Language/locallang.xml:profile.details.activities',
		'config' => array(
			'type' => 'text',
			'cols' => 30,
			'rows' => 5
		)
	),
	'interests' => array(
		'exclude' => 0,
		'label'	=> 'LLL:EXT:community/Resources/Private/Language/locallang.xml:profile.details.interests',
		'config' => array(
			'type' => 'text',
			'cols' => 30,
			'rows' => 5
		)
	),
	'music' => array(
		'exclude' => 0,
		'label'	=> 'LLL:EXT:community/Resources/Private/Language/locallang.xml:profile.details.music',
		'config' => array(
			'type' => 'text',
			'cols' => 30,
			'rows' => 5
		)
	),
	'movies' => array(
		'exclude' => 0,
		'label'	=> 'LLL:EXT:community/Resources/Private/Language/locallang.xml:profile.details.movies',
		'config' => array(
			'type' => 'text',
			'cols' => 30,
			'rows' => 5
		)
	),
	'books' => array(
		'exclude' => 0,
		'label'	=> 'LLL:EXT:community/Resources/Private/Language/locallang.xml:profile.details.books',
		'config' => array(
			'type' => 'text',
			'cols' => 30,
			'rows' => 5
		)
	),
	'quotes' => array(
		'exclude' => 0,
		'label'	=> 'LLL:EXT:community/Resources/Private/Language/locallang.xml:profile.details.quotes',
		'config' => array(
			'type' => 'text',
			'cols' => 30,
			'rows' => 5
		)
	),
	'about_me' => array(
		'exclude' => 0,
		'label'	=> 'LLL:EXT:community/Resources/Private/Language/locallang.xml:profile.details.aboutMe',
		'config' => array(
			'type' => 'text',
			'cols' => 30,
			'rows' => 5
		)
	),
	'cellphone' => array(
		'exclude' => 0,
		'label'	=> 'LLL:EXT:community/Resources/Private/Language/locallang.xml:profile.details.cellphone',
		'config' => array(
			'type' => 'input',
			'width' => 30
		)
	),
	'gender' => array(
		'exclude' => 0,
		'label'	=> 'LLL:EXT:community/Resources/Private/Language/locallang.xml:profile.details.gender',
		'config' => array(
			'type' => 'select',
			'items' => array(
				array('---', ''),
				array('LLL:EXT:community/Resources/Private/Language/locallang.xml:profile.details.gender.female', 'female'),
				array('LLL:EXT:community/Resources/Private/Language/locallang.xml:profile.details.gender.male', 'male'),
			)
		)
	),
	'date_of_birth' => array(
		'exclude' => 0,
		'label' => 'LLL:EXT:community/Resources/Private/Language/locallang.xml:profile.details.dateOfBirth',
		'config'  => array(
			'type' => 'input',
			'eval' => 'date'
		)
	)
);
t3lib_extMgm::addTCAcolumns('fe_users',$feUserColumns, 1);

t3lib_extMgm::addToAllTCATypes('fe_users','gender','', 'after:name');
t3lib_extMgm::addToAllTCATypes('fe_users','--div--;Community,political_view,religious_view,activities,interests,music,movies,books,quotes,about_me,cellphone,date_of_birth;;;;1-1-1');

?>