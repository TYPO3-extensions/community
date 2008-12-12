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

require_once($GLOBALS['PATH_community'] . 'view/userprofile/class.tx_community_view_userprofile_lastvisitors.php');

/**
 * A widget to show the last visitors to a user's profile
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_controller_userprofile_LastVisitorsWidget extends tx_community_controller_AbstractCommunityApplicationWidget implements tx_community_acl_AclResource {

	/**
	 * @var tx_community_LocalizationManager
	 */
	protected $localizationManager;

	/**
	 * constructor for class tx_community_controller_userprofile_LastVisitorsWidget
	 *
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function __construct() {
		parent::__construct();

		$this->localizationManager = tx_community_LocalizationManager::getInstance(
			'EXT:community/lang/locallang_userprofile_lastvisitors.xml',
			$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_community.']
		);

		$this->name      = 'lastVisitors';
		$this->draggable = true;
		$this->removable = true;

		$this->label = $this->localizationManager->getLL('label_LastVisitorsWidget');
		$this->cssClass = '';
	}

	/**
	 * the default action for this widget, renders a list of the last visitors
	 * and if the requesting user is not the same as the requested user a visit
	 * is logged in a round-robbin database
	 *
	 * @return	string		the widget's output as HTML
	 * @author	Ingo Renner <ingo@typo3.org>
	 * @see http://techblog.tilllate.com/2008/06/22/round-robin-data-storage-in-mysql/
	 */
	public function indexAction() {
		$content = '';
		$requestedUser  = $this->communityApplication->getRequestedUser();

		$lastVisitorsRows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'visitor',
			'tx_community_profile_visits_log',
			'feuser = ' . $requestedUser->getUid(),
			'',
			'last_update DESC'
		);

		$lastVisitorsUidArray = array();
		foreach($lastVisitorsRows as $row) {
			$lastVisitorsUidArray[] = $row['visitor'];
		}
		$lastVisitorsUidList = implode(',', $lastVisitorsUidArray);
		unset($lastVisitorsUidArray);

		$lastVisitors = $this->communityApplication->getUserGateway()->findByIdList($lastVisitorsUidList);

		$view = t3lib_div::makeInstance('tx_community_view_userprofile_LastVisitors');
		$view->setTemplateFile($this->configuration['applications.']['userProfile.']['widgets.'][$this->name . '.']['templateFile']);
		$view->setLanguageKey($this->communityApplication->LLkey);
		$view->setLastVisitorsModel($lastVisitors);

		$content = $view->render();

		return $content;
	}

	/**
	 * Returns the string identifier of the Resource
	 *
	 * @return string
	 */
	public function getResourceId() {
		$requestedUser = $this->communityApplication->getRequestedUser();

		$resourceId = $this->communityApplication->getName()
			. '_' . $this->name
			. '_' . $this->accessMode
			. '_' . $requestedUser->getUid();

		return $resourceId;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/userprofile/class.tx_community_controller_userprofile_lastvisitorswidget.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/userprofile/class.tx_community_controller_userprofile_lastvisitorswidget.php']);
}

?>