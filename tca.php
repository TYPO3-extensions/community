<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_community_acl_role'] = array (
	'ctrl' => $TCA['tx_community_acl_role']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,name'
	),
	'feInterface' => $TCA['tx_community_acl_role']['feInterface'],
	'columns' => array (
		'hidden' => array (
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'name' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:community/lang/locallang_db.xml:tx_community_acl_role.name',
			'config' => array (
				'type' => 'input',
				'size' => '30',
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;1;;1-1-1, name')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);



$TCA['tx_community_acl_rule'] = array (
	'ctrl' => $TCA['tx_community_acl_rule']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,name,resource,feuser,role,access_mode'
	),
	'feInterface' => $TCA['tx_community_acl_rule']['feInterface'],
	'columns' => array (
		'hidden' => array (
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'name' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:community/lang/locallang_db.xml:tx_community_acl_rule.name',
			'config' => array (
				'type' => 'input',
				'size' => '30',
			)
		),
		'resource' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:community/lang/locallang_db.xml:tx_community_acl_rule.resource',
			'config' => array (
				'type' => 'input',
				'size' => '30',
			)
		),
		'feuser' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:community/lang/locallang_db.xml:tx_community_acl_rule.feuser',
			'config' => array (
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'fe_users',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'role' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:community/lang/locallang_db.xml:tx_community_acl_rule.role',
			'config' => array (
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'tx_community_acl_role',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'access_mode' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:community/lang/locallang_db.xml:tx_community_acl_rule.access_mode',
			'config' => array (
				'type' => 'radio',
				'items' => array (
					array('LLL:EXT:community/lang/locallang_db.xml:tx_community_acl_rule.access_mode.I.0', '0'),
					array('LLL:EXT:community/lang/locallang_db.xml:tx_community_acl_rule.access_mode.I.1', '1'),
				),
				'default' => '1'
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;1;;1-1-1, name, resource, feuser, role, access_mode')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);

?>