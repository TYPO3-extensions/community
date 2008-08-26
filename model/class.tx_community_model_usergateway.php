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

require_once($GLOBALS['PATH_community'] . 'model/class.tx_community_model_user.php');

/**
 * gateway too retrieve users
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_model_UserGateway {

	/**
	 * constructor for class tx_community_model_UserGateway
	 */
	public function __construct() {

	}

	/**
	 * find a user by its uid
	 *
	 * @param integer The user's uid
	 * @return	tx_community_model_User
	 */
	public function findById($uid) {
		$user = null;

		$userRow = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'*',
			'fe_users',
			'uid = ' . (int) $uid
		); // TODO restrict to certain part of the tree
		$userRow = $userRow[0];

		// TODO first check whether we got exactly one result
		if (is_array($userRow)) {
			$user = $this->createUserFromRow($userRow);
		}

		return $user;
	}

	/**
	 * finds the currently logged in user
	 *
	 * @return	tx_community_model_User
	 */

	public function findCurrentlyLoggedInUser() {
		$loggedInUser = null;

		if ($GLOBALS['TSFE']->loginUser) {
			$loggedInUser = $this->findById($GLOBALS['TSFE']->fe_user->user['uid']);
		}

		return $loggedInUser;
	}

	protected function createUserFromRow(array $row) {
		/**
		 * @var tx_community_model_User
		 */
		$userClass = t3lib_div::makeInstanceClassName('tx_community_model_User');

		/**
		 * @var tx_community_model_User
		 */
		$user = new $userClass($row['uid']);
		$user->setPid($row['pid']);
		$user->setImage($row['image']);
		$user->setNickname($row['tx_community_nickname']);
		return $user;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/model/class.tx_community_model_usergateway.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/model/class.tx_community_model_usergateway.php']);
}

?>