<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$PATH_community    = t3lib_extMgm::extPath('community');
$PATHrel_community = t3lib_extMgm::extRelPath('community');

	// adding the ACL tables
$TCA['tx_community_acl_role'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:community/lang/locallang_db.xml:tx_community_acl_role',
		'label'     => 'name',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',
		'delete' => 'deleted',
		'enablecolumns' => array (
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => $PATH_community . 'tca.php',
		'iconfile'          => $PATHrel_community . 'resources/icons/tables/tx_community_acl_role.gif',
	),
);

$TCA['tx_community_acl_rule'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:community/lang/locallang_db.xml:tx_community_acl_rule',
		'label'     => 'name',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',
		'delete' => 'deleted',
		'enablecolumns' => array (
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => $PATH_community . 'tca.php',
		'iconfile'          => $PATHrel_community . 'resources/icons/tables/tx_community_acl_rule.gif',
	),
);

$TCA['tx_community_friend'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:community/lang/locallang_db.xml:tx_community_friend',
		'label'     => 'feuser',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',
		'dynamicConfigFile' => $PATH_community . 'tca.php',
		'iconfile'          => $PATHrel_community . 'resources/icons/tables/tx_community_friend.gif',
	),
);

	// extending fe_users
$feUsersTempColumns = array (
	'tx_community_sex' => array (
		'exclude' => 1,
		'label' => 'LLL:EXT:community/lang/locallang_db.xml:fe_users.tx_community_sex',
		'config' => array (
			'type' => 'radio',
			'items' => array (
				array('LLL:EXT:community/lang/locallang_db.xml:fe_users.tx_community_sex.I.0', 'm'),
				array('LLL:EXT:community/lang/locallang_db.xml:fe_users.tx_community_sex.I.1', 'f'),
			),
		)
	),
	'tx_community_nickname' => array (
		'exclude' => 1,
		'label' => 'LLL:EXT:community/lang/locallang_db.xml:fe_users.tx_community_nickname',
		'config' => array (
			'type' => 'input',
			'size' => '30',
		)
	),
	'tx_community_firstname' => array (
		'exclude' => 1,
		'label' => 'LLL:EXT:community/lang/locallang_db.xml:fe_users.tx_community_firstname',
		'config' => array (
			'type' => 'input',
			'size' => '30',
		)
	),
	'tx_community_middlename' => array (
		'exclude' => 1,
		'label' => 'LLL:EXT:community/lang/locallang_db.xml:fe_users.tx_community_middlename',
		'config' => array (
			'type' => 'input',
			'size' => '30',
		)
	),
	'tx_community_lastname' => array (
		'exclude' => 1,
		'label' => 'LLL:EXT:community/lang/locallang_db.xml:fe_users.tx_community_lastname',
		'config' => array (
			'type' => 'input',
			'size' => '30',
		)
	),
	'tx_community_mobilephone' => array (
		'exclude' => 1,
		'label' => 'LLL:EXT:community/lang/locallang_db.xml:fe_users.tx_community_mobilephone',
		'config' => array (
			'type' => 'input',
			'size' => '30',
		)
	),
	'tx_community_instantmessager' => array (
		'exclude' => 1,
		'label' => 'LLL:EXT:community/lang/locallang_db.xml:fe_users.tx_community_instantmessager',
		'config' => array (
			'type' => 'input',
			'size' => '30',
		)
	),
	'tx_community_birthday' => array (
		'exclude' => 1,
		'label' => 'LLL:EXT:community/lang/locallang_db.xml:fe_users.tx_community_birthday',
		'config' => array (
			'type'     => 'input',
			'size'     => '8',
			'max'      => '20',
			'eval'     => 'date',
			'checkbox' => '0',
			'default'  => '0'
		)
	),
	'tx_community_activities' => array (
		'exclude' => 1,
		'label' => 'LLL:EXT:community/lang/locallang_db.xml:fe_users.tx_community_activities',
		'config' => array (
			'type' => 'text',
			'cols' => '30',
			'rows' => '5',
		)
	),
	'tx_community_interests' => array (
		'exclude' => 1,
		'label' => 'LLL:EXT:community/lang/locallang_db.xml:fe_users.tx_community_interests',
		'config' => array (
			'type' => 'text',
			'cols' => '30',
			'rows' => '5',
		)
	),
	'tx_community_favoritemusic' => array (
		'exclude' => 1,
		'label' => 'LLL:EXT:community/lang/locallang_db.xml:fe_users.tx_community_favoritemusic',
		'config' => array (
			'type' => 'text',
			'cols' => '30',
			'rows' => '5',
		)
	),
	'tx_community_favoritetvshows' => array (
		'exclude' => 1,
		'label' => 'LLL:EXT:community/lang/locallang_db.xml:fe_users.tx_community_favoritetvshows',
		'config' => array (
			'type' => 'text',
			'cols' => '30',
			'rows' => '5',
		)
	),
	'tx_community_favoritemovies' => array (
		'exclude' => 1,
		'label' => 'LLL:EXT:community/lang/locallang_db.xml:fe_users.tx_community_favoritemovies',
		'config' => array (
			'type' => 'text',
			'cols' => '30',
			'rows' => '5',
		)
	),
	'tx_community_favoritebooks' => array (
		'exclude' => 1,
		'label' => 'LLL:EXT:community/lang/locallang_db.xml:fe_users.tx_community_favoritebooks',
		'config' => array (
			'type' => 'text',
			'cols' => '30',
			'rows' => '5',
		)
	),
	'tx_community_aboutme' => array (
		'exclude' => 1,
		'label' => 'LLL:EXT:community/lang/locallang_db.xml:fe_users.tx_community_aboutme',
		'config' => array (
			'type' => 'text',
			'cols' => '30',
			'rows' => '5',
		)
	),
);

	// adding additional columns to fe_users
t3lib_div::loadTCA('fe_users');
t3lib_extMgm::addTCAcolumns('fe_users', $feUsersTempColumns, 1);
t3lib_extMgm::addToAllTCAtypes('fe_users', 'tx_community_sex;;;;1-1-1, tx_community_nickname, tx_community_firstname, tx_community_middlename, tx_community_lastname, tx_community_mobilephone, tx_community_instantmessager, tx_community_birthday, tx_community_activities, tx_community_interests, tx_community_favoritemusic, tx_community_favoritetvshows, tx_community_favoritemovies, tx_community_favoritebooks, tx_community_aboutme');

$tempColumns = Array (
	"tx_community_members" => Array (        
        "exclude" => 1,        
        "label" => "LLL:EXT:community/lang/locallang_db.xml:fe_groups.tx_community_members",        
        "config" => Array (
            "type" => "group",    
            "internal_type" => "db",    
            "allowed" => "fe_users",    
            "size" => 5,    
            "minitems" => 0,
            "maxitems" => 100,    
            "MM" => "fe_groups_tx_community_members_mm",
        )
    ),
    'tx_community_admins' => Array (
        'exclude' => 1,
        'label' => 'LLL:EXT:community/lang/locallang_db.xml:fe_groups.tx_community_admins',
        'config' => Array (
            'type' => 'group',
            'internal_type' => 'db',
            'allowed' => 'fe_users',
            'size' => 5,
            'minitems' => 0,
            'maxitems' => 100,
        )
    ),
);


t3lib_div::loadTCA('fe_groups');
t3lib_extMgm::addTCAcolumns('fe_groups', $tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('fe_groups', 'tx_community_members;;;;1-1-1,tx_community_admins;;;;2-2-2');

	// adding the static TS templates
t3lib_extMgm::addStaticFile($_EXTKEY, 'static/community/', 'Community');

	// adding the application / widget selector as plugin content element
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_CommunityApplication'] = 'pi_flexform';
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_CommunityApplication'] = 'layout,select_key,pages,recusive';

t3lib_extMgm::addPiFlexFormValue(
	$_EXTKEY .'_CommunityApplication',
	'FILE:EXT:community/flexforms/flexform_application.xml'
);

t3lib_extMgm::addPlugin(
	array(
		'LLL:EXT:community/lang/locallang_db.xml:tt_content.list_type_communityApplication',
		$_EXTKEY.'_CommunityApplication'
	),
	'list_type'
);


if (TYPO3_MODE == 'BE') {
		// application manager for displaying applications and widgets in flexform
	include_once($PATH_community . 'classes/class.tx_community_applicationmanager.php');
}

?>