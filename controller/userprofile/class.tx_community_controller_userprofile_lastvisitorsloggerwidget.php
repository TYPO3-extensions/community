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



/**
 * A widget to log the last visitors to a user's profile
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_controller_userprofile_LastVisitorsLoggerWidget extends tx_community_controller_AbstractCommunityApplicationWidget {

	/**
	 * @var tx_community_LocalizationManager
	 */
	protected $localizationManager;

	/**
	 * constructor for class tx_community_controller_userprofile_LastVisitorsLoggerWidget
	 *
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function __construct() {
		parent::__construct();

		$this->localizationManager = tx_community_LocalizationManager::getInstance(
			'EXT:community/lang/locallang_userprofile_lastvisitors.xml',
			$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_community.']
		);

		$this->name      = 'lastVisitorsLogger';
		$this->draggable = false;
		$this->removable = false;

		$this->label = $this->localizationManager->getLL('label_LastVisitorsLoggerWidget');
		$this->cssClass = '';
	}

	/**
	 * the default action for this widget, logs the last visitor if the
	 * requesting user is not the same as the requested user into a
	 * round-robbin database
	 *
	 * @return	string		empty string, no output
	 * @author	Ingo Renner <ingo@typo3.org>
	 * @see http://techblog.tilllate.com/2008/06/22/round-robin-data-storage-in-mysql/
	 */
	public function indexAction() {
		$content        = '';
		$requestedUser  = $this->communityApplication->getRequestedUser();
		$requestingUser = $this->communityApplication->getRequestingUser();
		if (!is_null($requestedUser) && !is_null($requestingUser) && $requestedUser != $requestingUser && !$requestingUser->isAnonymous()) {
			$nextSequenceId = $this->getNextSequenceId($requestedUser);

				// TODO try to add a execREPLACEquery to t3lib_db
				// TODO add a ON DUPLICATE KEY option to t3lib_db
			$GLOBALS['TYPO3_DB']->sql_query(
				'INSERT INTO tx_community_profile_visits_log (feuser, sequence_id, last_update, visitor)
				VALUES (' . $requestedUser->getUid() . ', ' . $nextSequenceId . ', NOW(), ' . $requestingUser->getUid() . ')
				ON DUPLICATE KEY UPDATE last_update = NOW(), visitor = ' . $requestingUser->getUid()
			);
		}

		return $content;
	}

	/**
	 * gets the sequence id for the next log entry for the currently shown user
	 * profile
	 *
	 * @param	tx_community_model_User	the requesed user
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	protected function getNextSequenceId(tx_community_model_User $requestedUser) {
		$nextSequenceId = 0;
		$configuration = $this->getConfiguration();
		$numberOfLastVisitorsToLog = $configuration['numberOfLastVisitorsToLog'] ? $configuration['numberOfLastVisitorsToLog'] : 50 ;

		$row = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'(sequence_id + 1) % ' . $numberOfLastVisitorsToLog . ' as next_sequence_id',
			'tx_community_profile_visits_log',
			'feuser = ' . $requestedUser->getUid(),
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


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/userprofile/class.tx_community_controller_userprofile_lastvisitorsloggerwidget.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/userprofile/class.tx_community_controller_userprofile_lastvisitorsloggerwidget.php']);
}

?>