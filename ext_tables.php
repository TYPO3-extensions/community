<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_extMgm::addStaticFile($_EXTKEY,'static/community/', 'community');

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
			'type' => 'input',
			'size' => '30',
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
	'tx_community_instantmessager' => array (
		'exclude' => 1,
		'label' => 'LLL:EXT:community/lang/locallang_db.xml:fe_users.tx_community_instantmessager',
		'config' => array (
			'type' => 'input',
			'size' => '30',
		)
	),
);


t3lib_div::loadTCA('fe_users');
t3lib_extMgm::addTCAcolumns('fe_users', $feUsersTempColumns, 1);
t3lib_extMgm::addToAllTCAtypes('fe_users','tx_community_sex;;;;1-1-1, tx_community_nickname, tx_community_firstname, tx_community_middlename, tx_community_lastname, tx_community_mobilephone, tx_community_birthday, tx_community_activities, tx_community_interests, tx_community_favoritemusic, tx_community_favoritetvshows, tx_community_favoritemovies, tx_community_favoritebooks, tx_community_aboutme, tx_community_instantmessager');




?>