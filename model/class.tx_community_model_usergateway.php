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
require_once($GLOBALS['PATH_community'] . 'model/class.tx_community_model_account.php');

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

			// TODO cache the users to save queries

		return $user;
	}

	/**
	 * find users by its roles
	 *
	 * @param array The roles objects
	 * @return	array of tx_community_model_User
	 */
	public function findByRoles($roles) {
		// @TODO find users by its role
	}
	
	/**
	 * find users by custom criteria
	 * for example: $criteria = array(
	 * 	'lastname'	=> 'Meyer',
	 * 	'firstname'	=> 'Franz',
	 * 	'roles'		=> array(new tx_community_acl_Role(1), new tx_community_acl_Role(2))
	 * );
	 * 
	 * this method is neede for a user search and invite functionality
	 *
	 * @param array $criteria
	 */
	public function findByCriteria($criteria) {
		
	}

	/**
	 * find users friends
	 *
	 * @param tx_community_model_User $user
	 * @return	array of tx_community_model_User
	 */
	public function findFriends($user = null) {
		$friends = array();
		if (is_null($user)) {
			$user = $this->findCurrentlyLoggedInUser();
		}
		if (!is_null($user)) {
			$userRows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
				'friend',	// FIXME friend role name must not be hardcoded!
				'tx_community_friend',
				'feuser = ' . $user->getUid()
			);
			if (is_array($userRows)) {
				foreach($userRows as $userRow) {
					$friends[] = $this->findById($userRow['friend']);
				}
			}
		}

		return $friends;
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
		$userClass = t3lib_div::makeInstanceClassName('tx_community_model_User');

		$user = new $userClass($row['uid']);
		/* @var $user tx_community_model_User */
		$user->setPid($row['pid']);
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

		return $user;
	}

	/**
	 * creates a user's account object
	 *
	 * @param array A row of user data
	 * @return	tx_community_model_Account	A user account object
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