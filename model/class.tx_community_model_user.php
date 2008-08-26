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
 * A community user, uses TYPO3's fe_users
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_model_User {

	protected $uid;
	protected $pid;
	protected $crdate;
	protected $disabled;
	protected $deleted;

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

	public function getUid() {
		return $this->uid;
	}

	public function setPid($pageId) {
		$this->pid = (int) $pageId;
	}

	public function setNickname($nickname) {
		$this->nickName = $nickname;
	}

	public function getNickname() {
		return $this->nickName;
	}

	/**
	 * returns the user's image as path + file relative to the TYPO3 site root
	 *
	 */
	public function getImage() {
		t3lib_div::loadTCA('fe_users');
		return $GLOBALS['TCA']['fe_users']['columns']['image']['config']['uploadfolder'] . '/' . $this->image;
	}

	public function setImage($image) {
		$this->image = $image;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/model/class.tx_community_model_user.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/model/class.tx_community_model_user.php']);
}

?>