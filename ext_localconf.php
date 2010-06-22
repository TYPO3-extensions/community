<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Pi1',
	array(
		'Group' => '',
		'User' => 'image,details,edit,update,interaction',
		'Relation' => 'listSome,cancel,request,confirm,reject'
	),
	array(
		'Group' => '',
		'User' => 'edit,update',
		'Relation' => 'cancel,request,confirm,reject',
	)
);

?>