<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Pascal Jungblut <mail@pascalj.de>
*
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

class Tx_Community_Helper_AccessHelper {

	/**
	 * Check if a $requestingUser has access to $resource of $requestedUser
	 *
	 * @param string $resource
	 * @param Tx_Commonity_Domain_Model_Model $requestingUser
	 * @param Tx_Commonity_Domain_Model_Relation $requestedUser
	 * @param string $resource
	 */
	static public function hasAccess(
		$resource,
		$requestingUser,
		$requestedUser,
		array $roles = NULL,
		array $rules = NULL,
		Tx_Community_Domain_Model_AclRole $userRole = NULL
	) {
		if ($requestedUser instanceof Tx_Community_Domain_Model_User) {
			$defaultRoles = self::getDefaultRoles($requestedUser);
			$roles = array_merge($roles, $defaultRoles);
			$rules = Tx_Community_Helper_RepositoryHelper::getRepository('AclRule')->findByRoles($defaultRoles);
		}

		if (
			($requestingUser instanceof Tx_Community_Domain_Model_User) &&
			($requestedUser instanceof Tx_Community_Domain_Model_User)
		) {
			if ($requestingUser->getUid() == $requestedUser->getUid()) {
				self::getDefaultRoles($requestingUser);
				return true;
			}
		}

		// if a non existing user requests the page, load the anonymous role
		if (!($userRole instanceof Tx_Community_Domain_Model_AclRole)) {
			$userRole = self::getAnonymousRole($requestedUser);
		}

		// zend including stuff
		$zendPath = t3lib_extMgm::extPath('community') . 'Resources/Private/Zend/';
		set_include_path(get_include_path() . PATH_SEPARATOR . $zendPath);
		require_once('Acl.php');
		$acl = new Zend_Acl();

		foreach ($roles as $role) {
			$acl->addRole(new Zend_Acl_Role($role->getUid()));
		}

		foreach ($rules as $rule) {
			$acl = self::addResourceStringAsHierarchy($acl, $rule->getResource());
			$topResource = array_pop(explode('.', $rule->getResource()));
			if ($rule->getAccessMode()) {
				$acl->allow($rule->getRole()->getUid(), $topResource);
			} else {
				$acl->deny($rule->getRole()->getUid(), $topResource);
			}
		}
		$resourceArray = explode('.', $resource);
		$topResource = array_pop($resourceArray);
		while (count($resourceArray) && !$acl->has($topResource)) {
			$topResource = array_pop($resourceArray);
		}

		return $acl->isAllowed($userRole->getUid(), $topResource);
	}

	/**
	 * Get the default roles for user (configured by typoscript)
	 *
	 * @param Tx_Community_Domain_Model_User $user
	 */
	static public function getDefaultRoles(Tx_Community_Domain_Model_User $user) {
		$roles = Tx_Community_Helper_RepositoryHelper::getRepository('AclRole')->findDefault($user);
		if (count($roles) == 0) {
			return self::createDefaultRoles($user);
		} else {
			return $roles;
		}
	}

	/**
	 * Create the default roles
	 *
	 * @param Tx_Community_Domain_Model_User $user
	 */
	static public function createDefaultRoles(Tx_Community_Domain_Model_User $user) {
		$config = Tx_Community_Controller_BaseController::$tsConfig;

		if (is_array($config['roles']) && is_array($config['roles']['default'])) {
			foreach ($config['roles']['default'] as $roleId => $default) {
				$role = new Tx_Community_Domain_Model_AclRole();
				$role->setDefaultRole($roleId);
				$role->setName($default['name']);
				$role->setOwner($user);
				Tx_Community_Helper_RepositoryHelper::getRepository('AclRole')->add($role);
				$role = NULL;
			}
			$persistenceManager = t3lib_div::makeInstance('Tx_Extbase_Persistence_Manager');
			$persistenceManager->persistAll();
			$roles =  Tx_Community_Helper_RepositoryHelper::getRepository('AclRole')->findDefault($user);
			self::createDefaultRules($roles);
			return $roles;
		} else {
			throw new Tx_Community_Exception_TypoScriptException(
				'Could not find the default roles. Please define them in your Typoscript setup.'
			);
		}
	}

	/**
	 * Create the default rules form typoscript
	 *
	 * @param array $roles
	 */
	protected function createDefaultRules(array $roles) {
		$config = Tx_Community_Controller_BaseController::$tsConfig;
		$rolesConfig = $config['roles']['default'];

		foreach ($roles as $role) {
			$rules = array();
			if (is_array($rolesConfig[$role->getDefaultRole()])) {
				if (is_array($rolesConfig[$role->getDefaultRole()]['rules'])) {
					foreach ($rolesConfig[$role->getDefaultRole()]['rules'] as $rule) {
						$rules[$rule['name']] = ($rule['access'] == 'allow') ? 1 : 0;
					}
				} else {
					throw new Tx_Community_Exception_TypoScriptException(
						'Could not find rules for the default roles. Please add them to your TypoScript setup.'
					);
				}
			} else {
				throw new Tx_Community_Exception_TypoScriptException(
					'Could not find rules for the default roles. Please add them to your TypoScript setup.'
				);
			}

			self::setRulesForRole($role, $rules);
		}
	}

	/**
	 * Set the rules for a role
	 *
	 * @param Tx_Community_Domain_Model_AclRole $role
	 * @param array $rules
	 */
	static public function setRulesForRole(
		Tx_Community_Domain_Model_AclRole $role,
		array $rules
	) {

		$repo = Tx_Community_Helper_RepositoryHelper::getRepository('AclRule');
		foreach ($rules as $ruleName => $hasAccess) {

			$knownRules = $repo->findForResource($ruleName, $role);

			if (count($knownRules) >= 1) {
				$rule = $knownRules[0];
				$rule->setAccessMode($hasAccess);
				$repo->update($rule);
				continue;
			} else {
				$rule = new Tx_Community_Domain_Model_AclRule();
				$rule->setResource($ruleName);
				$rule->setAccessMode($hasAccess);
				$rule->setRole($role);
				$repo->add($rule);
			}
		}
		$persistenceManager = t3lib_div::makeInstance('Tx_Extbase_Persistence_Manager');
		$persistenceManager->persistAll();
	}

	static public function getAnonymousRole(Tx_Community_Domain_Model_User $user) {
		$roleArr = Tx_Community_Helper_RepositoryHelper::getRepository('AclRole')->findDefault($user, 1);
		return $roleArr[0];
	}

	static public function getAnyoneRole(Tx_Community_Domain_Model_User $user) {
		$roleArr = Tx_Community_Helper_RepositoryHelper::getRepository('AclRole')->findDefault($user, 2);
		return $roleArr[0];
	}

	static public function getFriendRole(Tx_Community_Domain_Model_User $user) {
		$roleArr = Tx_Community_Helper_RepositoryHelper::getRepository('AclRole')->findDefault($user, 3);
		return $roleArr[0];
	}

	static protected function addResourceStringAsHierarchy(Zend_Acl $acl, $resourceString) {
		$resources = explode('.', $resourceString);
		foreach ($resources as $resourceNumber => $resourceName) {
			if (!$acl->has($resourceName)) {
				if ($resouceNumber != 0) {
					$acl->addResource($resourceName);
				} else {
					$acl->addResource($resourceName, $resources[$resourceNumber-1]);
				}
			}
		}
		return $acl;
	}

	static public function getDefaultRules() {
		$tsConf = Tx_Community_Controller_BaseController::$tsConfig;
		$rulesArr = array();
		if (is_array($tsConf['rules']) && is_array($tsConf['rules']['default'])) {
			foreach ($tsConf['rules']['default'] as $rule) {
				$newRule = new Tx_Community_Domain_Model_AclRule();
				$newRule->setResource($rule['name']);
				$newRule->setAccessMode($rule['access'] == 'allow' ? 1 : 0);
				$rulesArr[] = $newRule;
			}
		}
		return $rulesArr;

	}
}
?>