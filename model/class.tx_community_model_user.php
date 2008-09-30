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

require_once($GLOBALS['PATH_community'] . 'interfaces/acl/interface.tx_community_acl_aclresource.php');
require_once($GLOBALS['PATH_community'] . 'interfaces/acl/interface.tx_community_acl_aclrole.php');

/**
 * A community user, uses TYPO3's fe_users
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_model_User implements tx_community_acl_AclResource, tx_community_acl_AclRole {

	protected $uid;
	protected $pid;
	protected $crdate;
	protected $disabled;
	protected $deleted;

	/**
	 * the user's account
	 *
	 * @var tx_community_model_Account
	 */
	protected $account;
	protected $nickName;

	protected $sex;
	protected $birthday;

	protected $address;
	protected $zip;
	protected $city;
	protected $phone;
	protected $mobilePhone;
	protected $website;

	protected $activities;
	protected $interests;
	protected $favoriteMusic;
	protected $favoriteTvShows;
	protected $favoriteMovies;
	protected $favoriteBooks;
	protected $aboutMe;

	protected $image;
	protected $htmlImage;

	protected $userDetailLink;

	/**
	 * constructor for class tx_community_model_User
	 */
	public function __construct($uid = null) {
		$this->uid      = $uid;
		$this->disabled = false;
		$this->deleted  = false;
	}

	public function save() {
		$timestamp = $_SERVER['REQUEST_TIME'];

		// TODO validate data
		// if validation fails throw exception, do not save, controller can then handle the exception and f.e. show an error message

		if (is_null($this->uid)) {
			// insert
		} else {
			// update
		}
	}

	public function delete() {
		// set deleted to true, then save
	}

	public function isAnonymous() {
		$isAnonymous = false;

		if($this->uid === 0) {
			$isAnonymous = true;
		}

		return $isAnonymous;
	}

	/**
	 * returns the Resource identifier
	 *
	 * @return string
	 */
	public function getResourceId() {
		return (string) 'fe_user_' . $this->uid;
	}

	/**
	 * returns the Role identifier
	 *
	 * @return string
	 */
	public function getRoleId() {
		return (string) 'fe_user_' . $this->uid;
	}

	public function setAccount(tx_community_model_Account $account) {
		$this->account = $account;
	}

	/**
	 * returns the user's account
	 *
	 * @return	tx_community_model_Account	The user's account
	 */
	public function getAccount() {
		return $this->account;
	}

	public function getPid() {
		return $this->pid;
	}

	public function setUid($uid) {
		$this->uid = $uid;
	}

	public function getUid() {
		return $this->uid;
	}

	public function setPid($pageId) {
		$this->pid = (int) $pageId;
	}

	public function setSex($sex) {
		$this->sex = $sex;
	}

	public function getSex() {
		return $this->sex;
	}

	public function setNickname($nickname) {
		$this->nickName = $nickname;
	}

	public function getNickname() {
		return $this->nickName;
	}

	public function getCombinedName() {
		return $this->getFirstName() . ' ' . $this->getLastName();
	}

	public function getFirstName() {
		return $this->account->getFirstName();
	}

	public function getLastName() {
		return $this->account->getLastName();
	}

	public function setBirthday($birthday) {
		$this->birthday = (int) $birthday;
	}

	public function getBirthday() {
		return $this->birthday;
	}

	/**
	 * returns the user's image as path + file relative to the TYPO3 site root
	 *
	 */
	public function getImage() {
		if (strlen($this->image)) {
			return 'uploads/pics/' . $this->image;
		} else {
			return '';
		}
	}

	public function setImage($image) {
		$this->image = $image;
	}

	public function getHtmlImage() {
		return $this->htmlImage;
	}

	public function setHtmlImage($htmlcode) {
		$this->htmlImage = $htmlcode;
	}

	public function getUserDetailLink() {
		return $this->userDetailLink;
	}

	public function setUserDetailLink($userDetailLink) {
		$this->userDetailLink = $userDetailLink;
	}

	public function getActivities() {
		return $this->activities;
	}

	public function setActivities($activities) {
		$this->activities = $activities;
	}

	public function getInterests() {
		return $this->interests;
	}

	public function setInterests($interests) {
		$this->interests = $interests;
	}

	public function getFavoriteMusic() {
		return $this->favoriteMusic;
	}

	public function setFavoriteMusic($favoriteMusic) {
		$this->favoriteMusic = $favoriteMusic;
	}

	public function getFavoriteTvShows() {
		return $this->favoriteTvShows;
	}

	public function setFavoriteTvShows($tvShows) {
		$this->favoriteTvShows = $tvShows;
	}

	public function getFavoriteMovies() {
		return $this->favoriteMovies;
	}

	public function setFavoriteMovies($movies) {
		$this->favoriteMovies = $movies;
	}

	public function getFavoriteBooks() {
		return $this->favoriteBooks;
	}

	public function setFavoriteBooks($books) {
		$this->favoriteBooks = $books;
	}

	public function getAboutMe() {
		return $this->aboutMe;
	}

	public function setAboutMe($aboutMe) {
		$this->aboutMe = $aboutMe;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/model/class.tx_community_model_user.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/model/class.tx_community_model_user.php']);
}

?>