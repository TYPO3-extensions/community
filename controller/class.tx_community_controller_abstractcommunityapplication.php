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

require_once($GLOBALS['PATH_community'] . 'classes/class.tx_community_accessmanager.php');
require_once($GLOBALS['PATH_community'] . 'model/class.tx_community_model_usergateway.php');

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
	protected $configuration;
	protected $data;
	protected $name;

	protected $requestedUser; // the user someone is viewing
	protected $requestingUser; // the currently logged in user

	/**
	 * @var tx_community_model_UserGateway
	 */
	protected $userGateway;

	// FIXME create an abstract community application widget that includes the properties, common implementations for the interfaces

	// TODO add a way to have controller plugins like Zend Framework does

	// TODO add a controller plugin to handle ACL stuff
	// TODO @see http://www.longshadow.com.au/tying-the-acl-to-controllersaction-in-zend-framework-15/
	// TODO @see http://devzone.zend.com/article/3509-Zend_Acl-and-MVC-Integration-Part-I-Basic-Use

	/**
	 * constructor for class tx_community_controller_AbstractCommunityApplication
	 */
	public function __construct() {
		$this->extKey = 'community';
		$this->cObj = t3lib_div::makeInstance('tslib_cObj');

		$this->requestedUser  = null;
		$this->requestingUser = null;

		$this->userGateway = t3lib_div::makeInstance('tx_community_model_UserGateway');

		parent::tslib_pibase();
	}

	public function initialize($data, $configuration) {
		$this->data = $data;
			// do not use $this->conf, but use $this->configuration
		$this->conf = $configuration; // TODO check whether we really need this one

		$this->configuration = $configuration;
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
		$requestedUser = null;

		if (is_null($this->requestedUser)) {
			$communityRequest = t3lib_div::GParrayMerged('tx_community');
			$requestedUser = $this->userGateway->findById((int) $communityRequest['user']);

			$this->requestedUser = $requestedUser;
		} else {
			$requestedUser = $this->requestedUser;
		}

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
		$requestingUser = null;

		if (is_null($this->requestingUser)) {
			$requestingUser = $this->userGateway->findCurrentlyLoggedInUser();
			$this->requestingUser = $requestingUser;
		} else {
			$requestingUser = $this->requestingUser;
		}


		return $requestingUser;
	}

	/**
	 * returns the configuration for the whole community environment
	 *
	 * @return	array	configuration for the community environment
	 */
	public function getCommunityTypoScriptConfiguration() {
		return $this->configuration;
	}

	/**
	 * returns the application's TypoScript configuration
	 *
	 * @return	array	The application's TypoScript configuration
	 */
	public function getApplicationTypoScriptConfiguration() {
		return $this->configuration['applications.'][$this->name . '.'];
	}

	abstract public function execute();
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_abstractcommunityapplication.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_abstractcommunityapplication.php']);
}

?>