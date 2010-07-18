<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

require_once(t3lib_extMgm::extPath('community') . 'Classes/Dispatcher.php');

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Pi1',
	array(
		'User' => 'image,details,edit,update,interaction,search,update',
		'Relation' => 'listSome,cancel,request,confirm,reject,unconfirmed',
		'AclRole' => 'list,edit,update,new,create',
		'Group' => '',
	),
	array(

		'User' => 'image,details,edit,update,interaction,search,update',
		'Relation' => 'cancel,request,confirm,reject,unconfirmed',
		'AclRole' => 'list,edit,update,new,create',
		'Group' => '',
	)
);

?>