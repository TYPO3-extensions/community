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
class tx_community_controller_userprofile_LastVisitorsWidget extends tx_community_controller_AbstractCommunityApplicationWidget {

	/**
	 * @var tx_community_LocalizationManager
	 */
	protected $localizationManager;

	/**
	 * constructor for class tx_community_controller_userprofile_LastVisitorsWidget
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

	public function indexAction() {
		$content = '';
		$requestedUser  = $this->communityApplication->getRequestedUser();
		$requestingUser = $this->communityApplication->getRequestingUser();

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
debug($lastVisitorsUidList);

		$view = t3lib_div::makeInstance('tx_community_view_userprofile_LastVisitors');
		$view->setTemplateFile($this->configuration['applications.']['userProfile.']['widgets.'][$this->name . '.']['templateFile']);
		$view->setLanguageKey($this->communityApplication->LLkey);
		$view->setLastVisitorsModel($lastVisitors);

		$content = $view->render();

		if ($requestedUser != $requestingUser) {
			$this->logVisit($requestedUser, $requestingUser);
		}

		return $content;
	}

	protected function logVisit(tx_community_model_User $requestedUser, tx_community_model_User $requestingUser) {
		$nextSequenceId = $this->getNextSequenceId($requestedUser);

			// TODO try to add a execREPLACEquery to t3lib_db
			// TODO add a ON DUPLICATE KEY option to t3lib_db
		$GLOBALS['TYPO3_DB']->sql_query(
			'INSERT INTO tx_community_profile_visits_log (feuser, sequence_id, last_update, visitor)
			VALUES (' . $requestedUser->getUid() . ', ' . $nextSequenceId . ', NOW(), ' . $requestingUser->getUid() . ')
			ON DUPLICATE KEY UPDATE last_update = NOW(), visitor = ' . $requestingUser->getUid()
		);
	}

	protected function getNextSequenceId(tx_community_model_User $feuser) {
		$nextSequenceId = 0;

			// TODO add support for the configuration option to set the amount of logged visitors
		$row = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'(sequence_id + 1) % 5 as next_sequence_id',
			'tx_community_profile_visits_log',
			'feuser = ' . $feuser->getUid(),
			'',
			'last_update DESC',
			1
		);

		if (!empty($row)) {
			$nextSequenceId = $row[0]['next_sequence_id'];
		}

		return $nextSequenceId;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/userprofile/class.tx_community_controller_userprofile_lastvisitorswidget.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/userprofile/class.tx_community_controller_userprofile_lastvisitorswidget.php']);
}

?>