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

require_once($GLOBALS['PATH_community'] . 'controller/class.tx_community_controller_abstractcommunityapplication.php');
require_once($GLOBALS['PATH_community'] . 'classes/class.tx_community_accessmanager.php');
require_once($GLOBALS['PATH_community'] . 'model/class.tx_community_model_usergateway.php');

/**
 * privacy management apllication controller
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_controller_PrivacyApplication extends tx_community_controller_AbstractCommunityApplication {

	/**
	 * constructor for class tx_community_controller_PrivacyApplication
	 */
	public function __construct() {
		parent::__construct();

		$this->prefixId = 'tx_community_controller_PrivacyApplication';
		$this->scriptRelPath = 'controller/class.tx_community_controller_privacyapplication.php';
		$this->communityApplicationName = 'Privacy';
	}

	public function execute() {
		$content = '';

		$userGateway = t3lib_div::makeInstance('tx_community_model_UserGateway');
		$currentlyLoggedInUser = $userGateway->findCurrentlyLoggedInUser();
		/* @var $currentlyLoggedInUser tx_community_model_User */

		$accessManagerClass = t3lib_div::makeInstanceClassName('tx_community_AccessManager');
		$accessManager      = call_user_func(array($accessManagerClass, 'getInstance'));

		if (!is_null($currentlyLoggedInUser)) {
			$content = 'User: ' . $currentlyLoggedInUser->getUid();


		} else {
			$content = 'No user logged in!';
		}

		return $content;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_privacyapplication.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_privacyapplication.php']);
}

?>