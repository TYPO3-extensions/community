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

require_once($GLOBALS['PATH_community'] . 'view/userprofile/class.tx_community_view_userprofile_friendsbirthdaylist.php');

/**
 * A user profile widget to display upcoming friend birthdays
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_controller_userprofile_FriendsBirthdayListWidget extends tx_community_controller_AbstractCommunityApplicationWidget {
	/**
	 * @var tx_community_LocalizationManager
	 */
	protected $localizationManager;

	/**
	 * constructor for class tx_community_controller_userprofile_FriendsBirthdayListWidget
	 */
	public function __construct() {
		parent::__construct();
		$this->localizationManager = tx_community_LocalizationManager::getInstance('EXT:community/lang/locallang_userprofile_friendsbirthdaylist.xml', $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_community.']);

		$this->name     = 'friendsBirthdayList';
		$this->label    = $this->localizationManager->getLL('label_FriendsBirthdayListWidget');
		$this->draggable = true;
		$this->removable = true;
		$this->cssClass = '';
	}

	public function indexAction() {
		$widgetTypoScriptConfiguration = $this->communityApplication->getWidgetTypoScriptConfiguration($this->name);
		/* $friends = $this->communityApplication->getUserGateway()->findFriends(
			$this->communityApplication->getRequestedUser()
		);

		$friends = array_slice($friends, 0, $widgetTypoScriptConfiguration['maxNumberOfItemsShown']);
		*/
		$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			"uid, EXTRACT(MONTH FROM FROM_UNIXTIME(tx_community_birthday, '%Y-%m-%d')) AS MXX, DAYOFMONTH(FROM_UNIXTIME(tx_community_birthday, '%Y-%m-%d')) AS DXX",
			'fe_users',
			"
(
	(
		(
			EXTRACT(MONTH FROM FROM_UNIXTIME(tx_community_birthday, '%Y-%m-%d')) = EXTRACT(MONTH FROM CURDATE())
			AND
			DAYOFMONTH(FROM_UNIXTIME(tx_community_birthday, '%Y-%m-%d')) >= DAYOFMONTH(CURDATE())
		)
		OR
		(
			EXTRACT(MONTH FROM SUBDATE(CURDATE(), INTERVAL 30 DAY)) > EXTRACT(MONTH FROM CURDATE())
			AND
			EXTRACT(MONTH FROM FROM_UNIXTIME(tx_community_birthday, '%Y-%m-%d')) = EXTRACT(MONTH FROM SUBDATE(CURDATE(), INTERVAL 30 DAY))
			AND
			DAYOFMONTH(FROM_UNIXTIME(tx_community_birthday, '%Y-%m-%d')) >= DAYOFMONTH(SUBDATE(CURDATE(), INTERVAL 30 DAY))
		)
	)	
	AND
	(
		uid IN (
			SELECT friend
			FROM tx_community_friend
			WHERE feuser = {$this->communityApplication->getRequestingUser()->getUid()}
		)
	)
)			
			",
			'',
			'MXX, DXX DESC'
		);
		
		$view = t3lib_div::makeInstance('tx_community_view_userprofile_FriendsBirthdayList');
		$view->setTemplateFile($this->configuration['applications.']['userProfile.']['widgets.'][$this->name . '.']['templateFile']);
		$view->setLanguageKey($this->communityApplication->LLkey);
		$view->setUserModel($friends);

		return $view->render();
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/userprofile/class.tx_community_controller_userprofile_friendsbirthdaylistwidget.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/userprofile/class.tx_community_controller_userprofile_friendsbirthdaylistwidget.php']);
}

?>