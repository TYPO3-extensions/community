<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_community_acl_role'] = array (
	'ctrl' => $TCA['tx_community_acl_role']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,name,is_public,is_friend_role'
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
		'is_public' => array (
			'exclude' => 1,
			'label'   => 'LLL:EXT:community/lang/locallang_db.xml:tx_community_acl_role.is_public',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'is_friend_role' => array (
			'exclude' => 1,
			'label'   => 'LLL:EXT:community/lang/locallang_db.xml:tx_community_acl_role.is_friend_role',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;1;;1-1-1, name, is_public, is_friend_role')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);



$TCA['tx_community_acl_rule'] = array (
	'ctrl' => $TCA['tx_community_acl_rule']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,name,resource,role,access_mode'
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
		'0' => array('showitem' => 'hidden;;1;;1-1-1, name, resource, role, access_mode')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);



$TCA['tx_community_friend'] = array (
	'ctrl' => $TCA['tx_community_friend']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,feuser,friend,role,status'
	),
	'feInterface' => $TCA['tx_community_friend']['feInterface'],
	'columns' => array (
		'hidden' => array (
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'feuser' => array (
			'exclude' => 1,
			'label' => 'LLL:EXT:community/lang/locallang_db.xml:tx_community_friend.feuser',
			'config' => array (
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'fe_users',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'friend' => array (
			'exclude' => 1,
			'label' => 'LLL:EXT:community/lang/locallang_db.xml:tx_community_friend.friend',
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
			'exclude' => 1,
			'label' => 'LLL:EXT:community/lang/locallang_db.xml:tx_community_friend.role',
			'config' => array (
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'tx_community_acl_role',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'status' => array ( // TODO use this field to save a status like "waiting for confirmation" or "friendship cancelled"
			'exclude' => 0,
			'label' => 'LLL:EXT:community/lang/locallang_db.xml:tx_community_friend.status',
			'config' => array (
				'type' => 'input',
				'size' => '30',
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;;;1-1-1, feuser, friend, role, status')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);



$TCA['tx_community_group'] = array (
	'ctrl' => $TCA['tx_community_group']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,name,groupType,description,image,creator,admins,members,pendingmembers'
	),
	'feInterface' => $TCA['tx_community_group']['feInterface'],
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
			'label' => 'LLL:EXT:community/lang/locallang_db.xml:tx_community_group.name',
			'config' => array (
				'type' => 'input',
				'size' => '30',
			)
		),
		'groupType' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:community/lang/locallang_db.xml:tx_community_group.groupType',
			'config' => array (
				'type' => 'radio',
				'items' => array (
					array('LLL:EXT:community/lang/locallang_db.xml:tx_community_group.groupType.I.0', '0'),
					array('LLL:EXT:community/lang/locallang_db.xml:tx_community_group.groupType.I.1', '1'),
					array('LLL:EXT:community/lang/locallang_db.xml:tx_community_group.groupType.I.2', '2'),
					array('LLL:EXT:community/lang/locallang_db.xml:tx_community_group.groupType.I.3', '3'),
				),
			)
		),
		'description' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:community/lang/locallang_db.xml:tx_community_group.description',
			'config' => array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
			)
		),
		'image' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:community/lang/locallang_db.xml:tx_community_group.image',
			'config' => array (
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
				'max_size' => 1024,
				'uploadfolder' => 'uploads/tx_community',
				'show_thumbs' => 1,
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'creator' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:community/lang/locallang_db.xml:tx_community_group.creator',
			'config' => array (
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'fe_users',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'admins' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:community/lang/locallang_db.xml:tx_community_group.admins',
			'config' => array (
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'fe_users',
				'size' => 5,
				'minitems' => 0,
				'maxitems' => 10,
				'MM' => 'tx_community_group_admins_mm',
			)
		),
		'members' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:community/lang/locallang_db.xml:tx_community_group.members',
			'config' => array (
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'fe_users',
				'size' => 10,
				'minitems' => 0,
				'maxitems' => 100,
				'MM' => 'tx_community_group_members_mm',
			)
		),
		'pendingmembers' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:community/lang/locallang_db.xml:tx_community_group.pendingMembers',
			'config' => array (
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'fe_users',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 100,
				'MM' => 'tx_community_group_pendingmembers_mm',
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;1;;1-1-1, name, groupType, description, image, creator, admins, members, pendingmembers')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);

?>