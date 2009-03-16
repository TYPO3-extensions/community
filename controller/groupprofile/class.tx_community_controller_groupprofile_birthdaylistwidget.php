<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008-2009 Ingo Renner <ingo@typo3.org>
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

require_once($GLOBALS['PATH_community'] . 'view/groupprofile/class.tx_community_view_groupprofile_birthdaylist.php');

/**
 * A roup profile widget to display upcoming member birthdays
 *
 * @author	Frank Nägler <typo3@naegler.net>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_controller_groupprofile_BirthdayListWidget extends tx_community_controller_AbstractCommunityApplicationWidget {
	/**
	 * @var tx_community_LocalizationManager
	 */
	protected $localizationManager;

	/**
	 * constructor for class tx_community_controller_groupprofile_BirthdayListWidget
	 */
	public function __construct() {
		parent::__construct();
		$this->localizationManager = tx_community_LocalizationManager::getInstance('EXT:community/lang/locallang_groupprofile_birthdaylist.xml', $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_community.']);

		$this->name     = 'birthdayList';
		$this->label    = $this->localizationManager->getLL('label_BirthdayListWidget');
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
		$group = $this->communityApplication->getRequestedGroup();
		
		$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			"uid, FROM_UNIXTIME(tx_community_birthday,'%m%d') as MMDD",
			'fe_users',
			"
				uid IN (
					SELECT uid_foreign
					FROM tx_community_group_members_mm
					WHERE uid_local = {$group->getUid()}
				)
			",
			'',
			"IF (MMDD >= DATE_FORMAT(CURDATE(),'%m%d'),0,1), MMDD ASC",
			'10'
		);

		$members = array();
		if (is_array($rows)) {
			foreach ($rows as $row) {
				$members[] = $this->communityApplication->getUserGateway()->findById($row['uid']);
			}
		}

		$view = t3lib_div::makeInstance('tx_community_view_groupprofile_BirthdayList');
		$view->setTemplateFile($this->configuration['applications.']['groupProfile.']['widgets.'][$this->name . '.']['templateFile']);
		$view->setLanguageKey($this->communityApplication->LLkey);
		$view->setUserModel($friends);

		return $view->render();
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/groupprofile/class.tx_community_controller_groupprofile_birthdaylistwidget.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/groupprofile/class.tx_community_controller_groupprofile_birthdaylistwidget.php']);
}

?>
