<?php
if (!defined ('TYPO3_MODE')) die ('Access denied.');

Tx_Extbase_Utility_Extension::registerPlugin(
	$_EXTKEY,
	'Pi1',
	'Community'
);

t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Community');

//$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY . '_pi1'] = 'pi_flexform';
//t3lib_extMgm::addPiFlexFormValue($_EXTKEY . '_pi1', 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/flexform_list.xml');


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

// flexform to select the action
$extensionName = t3lib_div::underscoredToUpperCamelCase($_EXTKEY);
$pluginSignature = strtolower($extensionName) . '_pi1';       
$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
t3lib_extMgm::addPiFlexFormValue($pluginSignature, 'FILE:EXT:'.$_EXTKEY.'/Configuration/FlexForm/Actions.xml');    

?>