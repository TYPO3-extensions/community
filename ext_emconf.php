<?php

########################################################################
# Extension Manager/Repository config file for ext: "community"
#
# Auto generated 07-08-2008 10:10
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Community',
	'description' => 'A flexible and extensible community system.',
	'category' => 'plugin',
	'author' => 'Ingo Renner',
	'author_email' => 'ingo@typo3.org',
	'shy' => '',
	'dependencies' => 'community_jquery',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'alpha',
	'internal' => '',
	'uploadfolder' => 1,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '0.0.6',
	'constraints' => array(
		'depends' => array(
			'community_jquery' => ''
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:7:{s:9:"ChangeLog";s:4:"1ffb";s:12:"ext_icon.gif";s:4:"1bdc";s:14:"ext_tables.php";s:4:"c0f8";s:19:"doc/wizard_form.dat";s:4:"23fa";s:20:"doc/wizard_form.html";s:4:"8344";s:30:"static/community/constants.txt";s:4:"d41d";s:26:"static/community/setup.txt";s:4:"d41d";}',
);

?>