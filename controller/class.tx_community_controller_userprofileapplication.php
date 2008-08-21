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
require_once($GLOBALS['PATH_community'] . 'model/class.tx_community_model_usergateway.php');


/**
 * User Profile Application Controller
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_controller_UserProfileApplication extends tx_community_controller_AbstractCommunityApplication  {

	public function __construct() {
		parent::__construct();

		$this->prefixId = 'tx_community_controller_UserProfileApplication';
		$this->scriptRelPath = 'controller/class.tx_community_controller_userprofileapplication.php';
		$this->communityApplicationName = 'UserProfile';
	}

	public function execute() {
		$content = '';

		$applicationManagerClass = t3lib_div::makeInstanceClassName('tx_community_ApplicationManager');
		$applicationManager      = call_user_func(array($applicationManagerClass, 'getInstance'));
		/* @var $applicationManager tx_community_ApplicationManager */

		$widgetName = $this->pi_getFFvalue(
			$this->data['pi_flexform'],
			'widget'
		);

		$widgetConfiguration = $applicationManager->getWidgetConfiguration(
			$this->communityApplicationName,
			$widgetName
		);

		$widget = t3lib_div::getUserObj($widgetConfiguration['classReference']);
		/* @var $widget tx_community_CommunityApplicationWidget */
		$widget->initialize($this->data, $this->conf);
		$widget->setParentCommunityApplication($this);

		$content = $widget->execute();

		return $content;
	}

	public function getRequestedUser() {
		$communityRequest = t3lib_div::_GP('tx_community');

		$userGateway = t3lib_div::makeInstance('tx_community_model_UserGateway');
		$requestedUser = $userGateway->findById((int) $communityRequest['user']);

		if (!($requestedUser instanceof tx_community_model_User)) {
			// TODO throw "user not found exception"
		}

		return $requestedUser;
	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_userprofileapplication.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_userprofileapplication.php']);
}

?>