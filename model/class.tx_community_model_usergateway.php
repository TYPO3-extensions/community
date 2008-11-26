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

require_once(PATH_t3lib . 'class.t3lib_page.php');
require_once($GLOBALS['PATH_community'] . 'model/class.tx_community_model_user.php');
require_once($GLOBALS['PATH_community'] . 'model/class.tx_community_model_account.php');

/**
 * gateway to retrieve users from the database
 *
 * @package TYPO3
 * @subpackage community
 */
class tx_community_model_UserGateway {

	static protected $feUsersEnableFields = null;
	static protected $foundUsers = array();

	/**
	 * constructor for class tx_community_model_UserGateway
	 *
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function __construct() {
		if (is_null(self::$feUsersEnableFields)) {
			$pageSelect = t3lib_div::makeInstance('t3lib_pageSelect');
			self::$feUsersEnableFields = $pageSelect->enableFields(
				'fe_users'
			);

				// free up some memory
			unset($pageSelect);
		}
	}

	/**
	 * find a user by its uid
	 *
	 * @param integer The user's uid
	 * @return	tx_community_model_User
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function findById($uid) {
		$user = null;

		if (isset(self::$foundUsers[$uid])) {
			$user = self::$foundUsers[$uid];
		} else {
			$userRow = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
				'*',
				'fe_users',
				'uid = ' . (int) $uid
			); // TODO restrict to certain part of the tree, enablefields
			$userRow = $userRow[0];

				// TODO first check whether we got exactly one result
			if (is_array($userRow)) {
				$user = $this->createUserFromRow($userRow);
			}
		}

		return $user;
	}

	/**
	 * finds multiple users by a list of user IDs (using only one query)
	 *
	 * @param	string	comma separated list of user IDs
	 * @return	array	An array of tx_community_model_User objects
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function findByIdList($uidList) {
		$users = array();

		$cleanUidList = implode(',', t3lib_div::intExplode(',', $uidList));

		$userRows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'*',
			'fe_users',
			'uid IN (' . $cleanUidList . ')'
		); // TODO restrict to certain part of the tree, enablefields

		foreach($userRows as $userRow) {
			$users[] = $this->createUserFromRow($userRow);
		}

		return $users;
	}

	/**
	 * find users by its roles
	 *
	 * @param	tx_community_model_User	the user to find from which connections originate
	 * @param	integer	A role id the connected users must have to be considered as a hit
	 * @return	array of tx_community_model_User
	 */
	public function findConnectedUsersByRole(tx_community_model_User $user, $roleId) {
		$connectedUsers = array();

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'DISTINCT u.*',
			'fe_users AS u, tx_community_friend AS fc', // fc = friend connection
			'fc.feuser = ' . $user->getUid()
				. ' AND fc.role = ' . $roleId
				. ' AND fc.hidden = 0'
				. ' AND u.uid = fc.friend'
		);

		while ($userRow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$connectedUsers[] = $this->createUserFromRow($userRow);
		}

		return $connectedUsers;
	}

	/**
	 * finds users by a custom where clause
	 *
	 * @param	string	where clause
	 * @return	array	An array of tx_community_model_User objects
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function findByWhereClause($whereClause) {
		$foundUsers = array();

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'DISTINCT fe_users.*',
			'fe_users',
			'(' . $whereClause . ')'
				. self::$feUsersEnableFields
		);

		if ($GLOBALS['TYPO3_DB']->sql_num_rows($res) > 0) {
			while ($userRow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$foundUsers[] = $this->createUserFromRow($userRow);
			}
		}

		return $foundUsers;
	}

	/**
	 * finds a user's friends
	 *
	 * @param	tx_community_model_User	The user to find the friends for, optional, if not set or set to null the currently logged in user will be taken
	 * @return	array	The user's friends as an array of tx_community_model_User objects
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function findFriends($user = null) {
		$friends = array();
		if (is_null($user)) {
			$user = $this->findCurrentlyLoggedInUser();
		}

		$userRows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'DISTINCT f1.friend',
			'tx_community_friend as f1 JOIN tx_community_friend AS f2'
			. ' ON f1.feuser = f2.friend
				AND f1.friend = f2.feuser
				AND f1.feuser = ' . $user->getUid(),
			''
		);

		if (is_array($userRows)) {
			$friendUidList = array();
			foreach($userRows as $userRow) {
				$friendUidList[] = $userRow['friend'];
			}
			$friends = $this->findByIdList(implode(',', $friendUidList));
		}

		return $friends;
	}

	/**
	 * finds friends of a user that are currently online. The timespan of what
	 * counts as "online" can be adjusted.
	 *
	 * @param	tx_community_model_User	The user to find the online friends for, optional, if not set or set to null the currently logged in user will be taken
	 * @param	integer	sets the timeout for a user being counted as online in seconds, defaults to 3600
	 * @return	array	An array of tx_community_model_User objects representing the online friends
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function findOnlineFriends($user = null, $onlineTimeout = 3600) {
		$onlineFriends = array();

		if (is_null($user)) {
			$user = $this->findCurrentlyLoggedInUser();
		}

		$userRows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'DISTINCT f1.friend',
			'tx_community_friend as f1 JOIN tx_community_friend AS f2'
			. ' ON f1.feuser = f2.friend
				AND f1.friend = f2.feuser
				AND f1.feuser = ' . $user->getUid()
				. ' JOIN fe_sessions AS fses ON fses.ses_userid = f1.friend
					AND fses.ses_tstamp >= ' . ($_SERVER['REQUEST_TIME'] - $onlineTimeout),
			''
		);

		if (is_array($userRows)) {
			$onlineFriendUidList = array();
			foreach($userRows as $userRow) {
				$onlineFriendUidList[] = $userRow['friend'];
			}
			$onlineFriends = $this->findByIdList(implode(',', $onlineFriendUidList));
		}


		return $onlineFriends;
	}

	/**
	 * finds the currently logged in user
	 *
	 * @return	tx_community_model_User
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function findCurrentlyLoggedInUser() {
		$loggedInUser = null;

		if ($GLOBALS['TSFE']->loginUser) {
			$loggedInUser = $this->findById($GLOBALS['TSFE']->fe_user->user['uid']);
		} else {
			$userClass = t3lib_div::makeInstanceClassName('tx_community_model_User');
			$loggedInUser = new $userClass(0);
		}

		return $loggedInUser;
	}

	/**
	 * creates a tx_community_model_User object from a database record
	 *
	 * @param	array	the database record as an array
	 * @return	tx_community_model_User
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	protected function createUserFromRow(array $row) {
		$user = null;
		$userClass = t3lib_div::makeInstanceClassName('tx_community_model_User');

		if (isset(self::$foundUsers[$row['uid']])) {
			$user = self::$foundUsers[$row['uid']];
		} else {
			$user = new $userClass($row['uid']);
			/* @var $user tx_community_model_User */
			$user->setPid($row['pid']);
			$user->setCrdate($row['crdate']);
			$user->setStatusMessage($row['tx_community_statusmessage']);
			$user->setSex($row['tx_community_sex']);
			$user->setBirthday($row['tx_community_birthday']);
			$user->setCity($row['city']);
			$user->setImage($row['image']);
			$user->setNickname($row['tx_community_nickname']);
			$user->setActivities($row['tx_community_activities']);
			$user->setInterests($row['tx_community_interests']);
			$user->setFavoriteMusic($row['tx_community_favoritemusic']);
			$user->setFavoriteTvShows($row['tx_community_favoritetvshows']);
			$user->setFavoriteMovies($row['tx_community_favoritemovies']);
			$user->setFavoriteBooks($row['tx_community_favoritebooks']);
			$user->setAboutMe($row['tx_community_aboutme']);

			$user->setAccount($this->createUserAccountFromRow($row));

			self::$foundUsers[$row['uid']] = $user;
		}

		return $user;
	}

	/**
	 * creates a user's account object
	 *
	 * @param array A row of user data
	 * @return	tx_community_model_Account	A user account object
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	protected function createUserAccountFromRow(array $row) {
		$account = t3lib_div::makeInstance('tx_community_model_Account');
		/* @var $account tx_community_model_Account*/

		$account->setFirstName($row['tx_community_firstname']);
		$account->setMiddleName($row['tx_community_middlename']);
		$account->setLastName($row['tx_community_lastname']);

		$account->setEmail($row['email']);

		$account->setUserName($row['username']);
		$account->setPassword($row['password']);

		return $account;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/model/class.tx_community_model_usergateway.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/model/class.tx_community_model_usergateway.php']);
}

?>