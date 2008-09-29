<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Ingo Renner <ingo@typo3.org>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once(PATH_tslib.'class.tslib_pibase.php');
require_once($GLOBALS['PATH_community'] . 'classes/class.tx_community_applicationmanager.php');

/**
 * central application controller for the community extension
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_controller_CommunityApplication extends tslib_pibase {
	public $prefixId      = 'tx_community_controller_CommunityApplication';		// Same as class name
	public $scriptRelPath = 'controller/class.tx_community_controller_communityapplication.php';	// Path to this script relative to the extension dir.
	public $extKey        = 'community';	// The extension key.

	public $conf;

	/**
	 * constructor for class tx_community_controller_CommunityApplication
	 */
	public function __construct() {

			// make the application manager available to the global scope when the plugin is executed
		$applicationManagerClass = t3lib_div::makeInstanceClassName('tx_community_ApplicationManager');
		$applicationManager      = call_user_func(array($applicationManagerClass, 'getInstance'));
		$GLOBALS['TX_COMMUNITY']['applicationManager'] = $applicationManager;
	}

	public function initialize($configuration) {
		$this->conf = $configuration;
		$this->tslib_pibase();
		$this->pi_setPiVarDefaults();
		$this->pi_USER_INT_obj = 1; // Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
		$this->pi_initPIflexForm();

		$this->conf = t3lib_div::array_merge_recursive_overrule(
			$this->conf,
			$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_community.']
		);
	}

	public function execute($content, $configuration) {
		$content = '';
		$this->initialize($configuration);

		$applicationName = $this->pi_getFFvalue(
			$this->cObj->data['pi_flexform'],
			'application'
		);

		$application = $GLOBALS['TX_COMMUNITY']['applicationManager']->getApplication($applicationName, $this->cObj->data, $this->conf);
		$content = $application->execute();

		return $this->pi_wrapInBaseClass($content);
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_communityapplication.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_communityapplication.php']);
}

?>