<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Pi1',
	array(
		'Group' => '',
		'User' => 'image,details,edit,update,interaction,search,update',
		'Relation' => 'listSome,cancel,request,confirm,reject,unconfirmed',
		'AclRole' => 'list,edit,update,new,create'
	),
	array(
		'Group' => '',
		'User' => 'edit,update',
		'Relation' => 'cancel,request,confirm,reject,unconfirmed',
		'AclRole' => 'list,edit,update,new,create'
	)
);

?>