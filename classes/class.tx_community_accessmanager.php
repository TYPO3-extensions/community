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
require_once($GLOBALS['PATH_community'] . 'classes/acl/class.tx_community_acl_acl.php');
require_once($GLOBALS['PATH_community'] . 'classes/acl/class.tx_community_acl_role.php');
require_once($GLOBALS['PATH_community'] . 'model/class.tx_community_model_usergateway.php');

/**
 * Access Manager, easily handles access to different areas of the community
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_AccessManager {

	const ACTION_CREATE = 'create';
	const ACTION_READ   = 'read';
	const ACTION_UPDATE = 'update';
	const ACTION_DELETE = 'delete';

	/**
	 * @var tx_community_AccessManager
	 */
	private static $instance = null;

	/**
	 * Access Control List
	 *
	 * @var tx_community_acl_Acl
	 */
	protected $acl;

	/**
	 * @var tx_community_model_UserGateway
	 */
	protected $userGateway;

	/**
	 * constructor for class tx_community_AccessManager
	 */
	protected function __construct() {
		$this->acl = t3lib_div::makeInstance('tx_community_acl_Acl');
		$pageSelect = t3lib_div::makeInstance('t3lib_pageSelect');
		$this->userGateway = t3lib_div::makeInstance('tx_community_model_UserGateway');
			// getting all roles
		$roles = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'*',
			'tx_community_acl_role',
			'1 = 1' . $pageSelect->enableFields('tx_community_acl_role'),
			'',
			'sorting',
			'',
			'uid'
		);

		foreach ($roles as $roleId => $role) {
			$aclRoleClass = t3lib_div::makeInstanceClassName('tx_community_acl_Role');
			$aclRole      = new $aclRoleClass($role['name']);

			$this->acl->addRole($aclRole);
		}
	}

	private function __clone() {

	}

	public static function getInstance() {
		if (is_null(self::$instance)) {
			self::$instance = new tx_community_AccessManager();
		}

		return self::$instance;
	}

	/**
	 * adds a resource to the ACL
	 *
	 * @param tx_community_acl_AclResource the resource to protect
	 */
	public function addResource(tx_community_acl_AclResource $resource) {
		$this->acl->add($resource);
	}

	public function isAllowed(tx_community_acl_AclResource $resource, tx_community_model_User $requestingUser = null, $action = self::ACTION_READ) {
		$allowed = false;
		if (is_null($requestingUser)) {
			$requestingUser = $this->userGateway->findCurrentlyLoggedInUser();
		}

			// get all rules for this resource
		$rules = $this->getRulesByResource($resource);

			// add the rules we found to the ACL
		foreach ($rules as $rule) {
			$this->addRule($rule);
		}

			// try to find a user Id
		$resourceId = $resource->getResourceId();
		$resourceIdParts = array_reverse(explode('_', $resourceId));

		$userId = 0;
		if (is_numeric($resourceIdParts[0])) {
				// a user resource
			$userId = $resourceIdParts[0];

				// find out which role the requesting user has towards the requested user's information
			$friendRoles = $this->getRoleFromFriendConnection($userId, $requestingUser->getUid());

				// check whether at least one of the roles allows access
			foreach ($friendRoles as $friendRole) {
				if($this->acl->isAllowed($friendRole['name'], $resource)) {
					$allowed = true;
					break;
				}
			}

		} else {
			// some other resource, not a user
		}

		return $allowed;
	}

	/**
	 * determines whether a user is logged in or whether it is an anonymous guest
	 *
	 * @param	tx_community_model_User	$requestingUser
	 * @return	boolean
	 */
	public function isLoggedIn(tx_community_model_User $requestingUser = null) {
		$userIsLoggedIn = false;

		if (is_null($requestingUser)) {
			$requestingUser = $this->userGateway->findCurrentlyLoggedInUser();
		}

		if ($requestingUser->getUid()) {
			$userIsLoggedIn = true;
		}

		return $userIsLoggedIn;
	}

	/**
	 * gets the role of a friend
	 *
	 * @param	integer	the requested user's Id
	 * @param	integer	the friend's user Id
	 */
	protected function getRoleFromFriendConnection($requestingUserId, $requestedUserId) {
		$pageSelect = t3lib_div::makeInstance('t3lib_pageSelect');
		$anonymousRoleId = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_community.']['accessManagement.']['anonymousRoleId'];

		$roles = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'DISTINCT tx_community_acl_role.name',
			'tx_community_friend, tx_community_acl_role',
			'('
					. '(feuser = ' . $requestingUserId
					. ' AND friend = ' . $requestedUserId
					. ' AND tx_community_acl_role.uid = tx_community_friend.role)'
				. ' OR'
					. '(tx_community_acl_role.uid = ' . $anonymousRoleId . ')'
			. ')'
				. $pageSelect->enableFields('tx_community_acl_role')
		);

		return $roles;
	}

	protected function getRulesByResource(tx_community_acl_AclResource $resource) {
		$pageSelect = t3lib_div::makeInstance('t3lib_pageSelect');

		$GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true;
		$GLOBALS['TYPO3_DB']->debugOutput = true;

		$rules = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'tx_community_acl_rule.*, tx_community_acl_role.name AS role_name',
			'tx_community_acl_rule, tx_community_acl_role',
			'resource = \'' . $resource->getResourceId() . '\''
				. ' AND tx_community_acl_role.uid = tx_community_acl_rule.role'
				. $pageSelect->enableFields('tx_community_acl_role')
				. $pageSelect->enableFields('tx_community_acl_rule'),
			'',
			'',
			'',
			'uid'
		);

		return $rules;
	}

	public function addRule(array $rule) {
		if ($rule['access_mode'] == 1) {
				// allow
			$this->acl->allow($rule['role_name'], $rule['resource']);
		} else {
				// deny
			$this->acl->deny($rule['role_name'], $rule['resource']);
		}
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/classes/class.tx_community_accessmanager.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/classes/class.tx_community_accessmanager.php']);
}

?>