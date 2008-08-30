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
 * An abstract community application controller
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
abstract class tx_community_controller_AbstractCommunityApplication extends tslib_pibase {

	public $prefixId;
	public $scriptRelPath;
	public $extKey;

	public $conf;
	protected $data;
	protected $name;

	protected $userGateway;

	/**
	 * constructor for class tx_community_controller_AbstractCommunityApplication
	 */
	public function __construct() {
		$this->extKey = 'community';

		$this->userGateway = t3lib_div::makeInstance('tx_community_model_UserGateway');

		parent::tslib_pibase();
	}

	public function initialize($data, $configuration) {
		$this->data = $data;
		$this->conf = $configuration;
	}

	/**
	 * returns the name of this community application
	 *
	 * @return	string	This community application's name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * returns the user that shall be displayed
	 *
	 * @return tx_community_model_User
	 */
	public function getRequestedUser() {
		$communityRequest = t3lib_div::_GP('tx_community');
		$requestedUser = $this->userGateway->findById((int) $communityRequest['user']);

		if (!($requestedUser instanceof tx_community_model_User)) {
			// TODO throw a "user not found exception"
		}

		return $requestedUser;
	}

	/**
	 * returns the user that is looking at a page (if available), if no user is logged in null is returned
	 *
	 * @return tx_community_model_User|null
	 */
	public function getRequestingUser() {
		return $this->userGateway->findCurrentlyLoggedInUser();;
	}

	abstract public function execute();
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_abstractcommunityapplication.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_abstractcommunityapplication.php']);
}

?>