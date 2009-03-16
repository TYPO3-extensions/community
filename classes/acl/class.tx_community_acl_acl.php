<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008-2009 Ingo Renner <ingo@typo3.org>
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

require_once($GLOBALS['PATH_community'] . 'classes/acl/class.tx_community_acl_exception.php');
require_once($GLOBALS['PATH_community'] . 'classes/acl/class.tx_community_acl_roleregistry.php');

/**
 * community ACL handling class
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_acl_Acl {

	/**
	 * Rule type: allow
	 */
	const TYPE_ALLOW = 'TYPE_ALLOW';

	/**
	 * Rule type: deny
	 */
	const TYPE_DENY  = 'TYPE_DENY';

	/**
	 * Rule operation: add
	 */
	const OPERATION_ADD = 'OPERATION_ADD';

	/**
	 * Rule operation: remove
	 */
	const OPERATION_REMOVE = 'OPERATION_REMOVE';

	/**
	 * Role registry
	 *
	 * @var tx_community_acl_RoleRegistry
	 */
	protected $roleRegistry = null;

	/**
	 * Resource tree
	 *
	 * @var array
	 */
	protected $resources = array();

	/**
	 * ACL rules; whitelist (deny everything to all) by default
	 *
	 * @var array
	 */
	protected $rules = array(
		'allResources' => array(
			'allRoles' => array(
				'allPrivileges' => array(
					'type'   => self::TYPE_DENY,
					'assert' => null
				),
				'byPrivilegeId' => array()
			),
			'byRoleId' => array()
		),
		'byResourceId' => array()
	);

	/**
	 * Adds a Role having an identifier unique to the registry
	 *
	 * The $parents parameter may be a reference to, or the string identifier for,
	 * a Role existing in the registry, or $parents may be passed as an array of
	 * these - mixing string identifiers and objects is ok - to indicate the Roles
	 * from which the newly added Role will directly inherit.
	 *
	 * In order to resolve potential ambiguities with conflicting rules inherited
	 * from different parents, the most recently added parent takes precedence over
	 * parents that were previously added. In other words, the first parent added
	 * will have the least priority, and the last parent added will have the
	 * highest priority.
	 *
	 * @param	tx_community_acl_AclRole	$role
	 * @param	tx_community_acl_AclRole|string|array	$parents
	 * @uses	tx_community_acl_AclRoleRegistry::add()
	 * @return	tx_community_acl_Acl	Provides a fluent interface
	 */
	public function addRole(tx_community_acl_AclRole $role, $parents = null) {
		$this->getRoleRegistry()->add($role, $parents);

		return $this;
	}

	/**
	 * Returns the identified Role
	 *
	 * The $role parameter can either be a Role or Role identifier.
	 *
	 * @param	tx_community_acl_AclRole|string	$role
	 * @uses	tx_community_acl_AclRoleRegistry::get()
	 * @return	tx_community_acl_AclRole
	 */
	public function getRole($role) {
		return $this->getRoleRegistry()->get($role);
	}

	/**
	 * Returns true if and only if the Role exists in the registry
	 *
	 * The $role parameter can either be a Role or a Role identifier.
	 *
	 * @param	tx_community_acl_AclRole|string $role
	 * @uses	tx_community_acl_AclRoleRegistry::has()
	 * @return	boolean
	 */
	public function hasRole($role) {
		return $this->getRoleRegistry()->has($role);
	}

	/**
	 * Returns true if and only if $role inherits from $inherit
	 *
	 * Both parameters may be either a Role or a Role identifier. If
	 * $onlyParents is true, then $role must inherit directly from
	 * $inherit in order to return true. By default, this method looks
	 * through the entire inheritance DAG to determine whether $role
	 * inherits from $inherit through its ancestor Roles.
	 *
	 * @param	tx_community_acl_AclRole|string	$role
	 * @param	tx_community_acl_AclRole|string	$inherit
	 * @param	boolean	$onlyParents
	 * @uses	tx_community_acl_AclRoleRegistry::inherits()
	 * @return	boolean
	 */
	public function inheritsRole($role, $inherit, $onlyParents = false) {
		return $this->getRoleRegistry()->inherits($role, $inherit, $onlyParents);
	}

	/**
	 * Removes the Role from the registry
	 *
	 * The $role parameter can either be a Role or a Role identifier.
	 *
	 * @param  tx_community_acl_AclRole|string $role
	 * @uses   tx_community_acl_AclRoleRegistry::remove()
	 * @return tx_community_acl_Acl Provides a fluent interface
	 */
	public function removeRole($role) {
		$this->getRoleRegistry()->remove($role);

		if ($role instanceof tx_community_acl_AclRole) {
			$roleId = $role->getRoleId();
		} else {
			$roleId = $role;
		}

		foreach ($this->rules['allResources']['byRoleId'] as $roleIdCurrent => $rules) {
			if ($roleId === $roleIdCurrent) {
				unset($this->rules['allResources']['byRoleId'][$roleIdCurrent]);
			}
		}

		foreach ($this->rules['byResourceId'] as $resourceIdCurrent => $visitor) {
			foreach ($visitor['byRoleId'] as $roleIdCurrent => $rules) {
				if ($roleId === $roleIdCurrent) {
					unset($this->rules['byResourceId'][$resourceIdCurrent]['byRoleId'][$roleIdCurrent]);
				}
			}
		}

		return $this;
	}

	/**
	 * Removes all Roles from the registry
	 *
	 * @uses   tx_community_acl_AclRoleRegistry::removeAll()
	 * @return tx_community_acl_Acl Provides a fluent interface
	 */
	public function removeRoleAll() {
		$this->getRoleRegistry()->removeAll();

		foreach ($this->rules['allResources']['byRoleId'] as $roleIdCurrent => $rules) {
			unset($this->rules['allResources']['byRoleId'][$roleIdCurrent]);
		}

		foreach ($this->rules['byResourceId'] as $resourceIdCurrent => $visitor) {
			foreach ($visitor['byRoleId'] as $roleIdCurrent => $rules) {
				unset($this->rules['byResourceId'][$resourceIdCurrent]['byRoleId'][$roleIdCurrent]);
			}
		}

		return $this;
	}

	/**
	 * Adds a Resource having an identifier unique to the ACL
	 *
	 * The $parent parameter may be a reference to, or the string identifier for,
	 * the existing Resource from which the newly added Resource will inherit.
	 *
	 * @param  tx_community_acl_AclResource		$resource
	 * @param  tx_community_acl_AclResource|string $parent
	 * @throws tx_community_acl_Exception
	 * @return tx_community_acl_Acl Provides a fluent interface
	 */
	public function add(tx_community_acl_AclResource $resource, $parent = null) {
		$resourceId = $resource->getResourceId();

		if ($this->has($resourceId)) {
			throw new tx_community_acl_Exception("Resource id '$resourceId' already exists in the ACL");
		}

		$resourceParent = null;

		if (null !== $parent) {
			try {
				if ($parent instanceof tx_community_acl_AclResource) {
					$resourceParentId = $parent->getResourceId();
				} else {
					$resourceParentId = $parent;
				}
				$resourceParent = $this->get($resourceParentId);
			} catch (tx_community_acl_Exception $e) {
				throw new tx_community_acl_Exception("Parent Resource id '$resourceParentId' does not exist");
			}
			$this->resources[$resourceParentId]['children'][$resourceId] = $resource;
		}

		$this->resources[$resourceId] = array(
			'instance' => $resource,
			'parent'   => $resourceParent,
			'children' => array()
			);

		return $this;
	}

	/**
	 * Returns the identified Resource
	 *
	 * The $resource parameter can either be a Resource or a Resource identifier.
	 *
	 * @param  tx_community_acl_AclResource|string $resource
	 * @throws tx_community_acl_Exception
	 * @return tx_community_acl_AclResource
	 */
	public function get($resource) {
		if ($resource instanceof tx_community_acl_AclResource) {
			$resourceId = $resource->getResourceId();
		} else {
			$resourceId = (string) $resource;
		}

		if (!$this->has($resource)) {
			throw new tx_community_acl_Exception("Resource '$resourceId' not found");
		}

		return $this->resources[$resourceId]['instance'];
	}

	/**
	 * Returns true if and only if the Resource exists in the ACL
	 *
	 * The $resource parameter can either be a Resource or a Resource identifier.
	 *
	 * @param  tx_community_acl_AclResource|string $resource
	 * @return boolean
	 */
	public function has($resource) {
		if ($resource instanceof tx_community_acl_AclResource) {
			$resourceId = $resource->getResourceId();
		} else {
			$resourceId = (string) $resource;
		}

		return isset($this->resources[$resourceId]);
	}

	/**
	 * Returns true if and only if $resource inherits from $inherit
	 *
	 * Both parameters may be either a Resource or a Resource identifier. If
	 * $onlyParent is true, then $resource must inherit directly from
	 * $inherit in order to return true. By default, this method looks
	 * through the entire inheritance tree to determine whether $resource
	 * inherits from $inherit through its ancestor Resources.
	 *
	 * @param  tx_community_acl_AclResource|string $resource
	 * @param  tx_community_acl_AclResource|string $inherit
	 * @param  boolean							$onlyParent
	 * @throws tx_community_acl_Exception
	 * @return boolean
	 */
	public function inherits($resource, $inherit, $onlyParent = false) {
		try {
			$resourceId	 = $this->get($resource)->getResourceId();
			$inheritId = $this->get($inherit)->getResourceId();
		} catch (tx_community_acl_Exception $e) {
			throw $e;
		}

		if (null !== $this->resources[$resourceId]['parent']) {
			$parentId = $this->resources[$resourceId]['parent']->getResourceId();
			if ($inheritId === $parentId) {
				return true;
			} else if ($onlyParent) {
				return false;
			}
		} else {
			return false;
		}

		while (null !== $this->resources[$parentId]['parent']) {
			$parentId = $this->resources[$parentId]['parent']->getResourceId();
			if ($inheritId === $parentId) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Removes a Resource and all of its children
	 *
	 * The $resource parameter can either be a Resource or a Resource identifier.
	 *
	 * @param  tx_community_acl_AclResource|string $resource
	 * @throws tx_community_acl_Exception
	 * @return tx_community_acl_Acl Provides a fluent interface
	 */
	public function remove($resource) {
		try {
			$resourceId = $this->get($resource)->getResourceId();
		} catch (tx_community_acl_Exception $e) {
			throw $e;
		}

		$resourcesRemoved = array($resourceId);
		if (null !== ($resourceParent = $this->resources[$resourceId]['parent'])) {
			unset($this->resources[$resourceParent->getResourceId()]['children'][$resourceId]);
		}
		foreach ($this->resources[$resourceId]['children'] as $childId => $child) {
			$this->remove($childId);
			$resourcesRemoved[] = $childId;
		}

		foreach ($resourcesRemoved as $resourceIdRemoved) {
			foreach ($this->rules['byResourceId'] as $resourceIdCurrent => $rules) {
				if ($resourceIdRemoved === $resourceIdCurrent) {
					unset($this->rules['byResourceId'][$resourceIdCurrent]);
				}
			}
		}

		unset($this->resources[$resourceId]);

		return $this;
	}

	/**
	 * Removes all Resources
	 *
	 * @return tx_community_acl_Acl Provides a fluent interface
	 */
	public function removeAll() {
		foreach ($this->resources as $resourceId => $resource) {
			foreach ($this->rules['byResourceId'] as $resourceIdCurrent => $rules) {
				if ($resourceId === $resourceIdCurrent) {
					unset($this->rules['byResourceId'][$resourceIdCurrent]);
				}
			}
		}

		$this->resources = array();

		return $this;
	}

	/**
	 * Adds an "allow" rule to the ACL
	 *
	 * @param  tx_community_acl_AclRole|string|array	 $roles
	 * @param  tx_community_acl_AclResource|string|array $resources
	 * @param  string|array							 $privileges
	 * @param  tx_community_acl_Assert				$assert
	 * @uses   tx_community_acl_Acl::setRule()
	 * @return tx_community_acl_Acl Provides a fluent interface
	 */
	public function allow($roles = null, $resources = null, $privileges = null, tx_community_acl_Assert $assert = null) {
		return $this->setRule(self::OPERATION_ADD, self::TYPE_ALLOW, $roles, $resources, $privileges, $assert);
	}

	/**
	 * Adds a "deny" rule to the ACL
	 *
	 * @param  tx_community_acl_AclRole|string|array	 $roles
	 * @param  tx_community_acl_AclResource|string|array $resources
	 * @param  string|array							 $privileges
	 * @param  tx_community_acl_Assert				$assert
	 * @uses   tx_community_acl_Acl::setRule()
	 * @return tx_community_acl_Acl Provides a fluent interface
	 */
	public function deny($roles = null, $resources = null, $privileges = null, tx_community_acl_Assert $assert = null) {
		return $this->setRule(self::OPERATION_ADD, self::TYPE_DENY, $roles, $resources, $privileges, $assert);
	}

	/**
	 * Removes "allow" permissions from the ACL
	 *
	 * @param  tx_community_acl_AclRole|string|array	 $roles
	 * @param  tx_community_acl_AclResource|string|array $resources
	 * @param  string|array							 $privileges
	 * @uses   tx_community_acl_Acl::setRule()
	 * @return tx_community_acl_Acl Provides a fluent interface
	 */
	public function removeAllow($roles = null, $resources = null, $privileges = null) {
		return $this->setRule(self::OPERATION_REMOVE, self::TYPE_ALLOW, $roles, $resources, $privileges);
	}

	/**
	 * Removes "deny" restrictions from the ACL
	 *
	 * @param  tx_community_acl_AclRole|string|array	 $roles
	 * @param  tx_community_acl_AclResource|string|array $resources
	 * @param  string|array							 $privileges
	 * @uses   tx_community_acl_Acl::setRule()
	 * @return tx_community_acl_Acl Provides a fluent interface
	 */
	public function removeDeny($roles = null, $resources = null, $privileges = null) {
		return $this->setRule(self::OPERATION_REMOVE, self::TYPE_DENY, $roles, $resources, $privileges);
	}

	/**
	 * Performs operations on ACL rules
	 *
	 * The $operation parameter may be either OP_ADD or OP_REMOVE, depending on whether the
	 * user wants to add or remove a rule, respectively:
	 *
	 * OP_ADD specifics:
	 *
	 *	  A rule is added that would allow one or more Roles access to [certain $privileges
	 *	  upon] the specified Resource(s).
	 *
	 * OP_REMOVE specifics:
	 *
	 *	  The rule is removed only in the context of the given Roles, Resources, and privileges.
	 *	  Existing rules to which the remove operation does not apply would remain in the
	 *	  ACL.
	 *
	 * The $type parameter may be either TYPE_ALLOW or TYPE_DENY, depending on whether the
	 * rule is intended to allow or deny permission, respectively.
	 *
	 * The $roles and $resources parameters may be references to, or the string identifiers for,
	 * existing Resources/Roles, or they may be passed as arrays of these - mixing string identifiers
	 * and objects is ok - to indicate the Resources and Roles to which the rule applies. If either
	 * $roles or $resources is null, then the rule applies to all Roles or all Resources, respectively.
	 * Both may be null in order to work with the default rule of the ACL.
	 *
	 * The $privileges parameter may be used to further specify that the rule applies only
	 * to certain privileges upon the Resource(s) in question. This may be specified to be a single
	 * privilege with a string, and multiple privileges may be specified as an array of strings.
	 *
	 * If $assert is provided, then its assert() method must return true in order for
	 * the rule to apply. If $assert is provided with $roles, $resources, and $privileges all
	 * equal to null, then a rule having a type of:
	 *
	 *	  TYPE_ALLOW will imply a type of TYPE_DENY, and
	 *
	 *	  TYPE_DENY will imply a type of TYPE_ALLOW
	 *
	 * when the rule's assertion fails. This is because the ACL needs to provide expected
	 * behavior when an assertion upon the default ACL rule fails.
	 *
	 * @param	string	$operation
	 * @param	string	$type
	 * @param	tx_community_acl_AclRole|string|array	$roles
	 * @param	tx_community_acl_AclResource|string|array	$resources
	 * @param	string|array	$privileges
	 * @param	tx_community_acl_Assert	$assert
	 * @throws	tx_community_acl_Exception
	 * @uses	tx_community_acl_AclRoleRegistry::get()
	 * @uses	tx_community_acl_Acl::get()
	 * @return	tx_community_acl_Acl	Provides a fluent interface
	 */
	public function setRule($operation, $type, $roles = null, $resources = null, $privileges = null, tx_community_acl_Assert $assert = null) {
			// ensure that the rule type is valid; normalize input to uppercase
		$type = strtoupper($type);
		if (self::TYPE_ALLOW !== $type && self::TYPE_DENY !== $type) {
			throw new tx_community_acl_Exception(
				"Unsupported rule type; must be either '" . self::TYPE_ALLOW . "' or '" . self::TYPE_DENY . "'"
			); // TODO add timestamp as error code
		}

			// ensure that all specified Roles exist; normalize input to array
			// of Role objects or null
		if (!is_array($roles)) {
			$roles = array($roles);
		} else if (0 === count($roles)) {
			$roles = array(null);
		}
		$rolesTemp = $roles;
		$roles = array();
		foreach ($rolesTemp as $role) {
			if (null !== $role) {
				$roles[] = $this->getRoleRegistry()->get($role);
			} else {
				$roles[] = null;
			}
		}
		unset($rolesTemp);

			// ensure that all specified Resources exist; normalize input to
			// array of Resource objects or null
		if (!is_array($resources)) {
			$resources = array($resources);
		} else if (0 === count($resources)) {
			$resources = array(null);
		}
		$resourcesTemp = $resources;
		$resources = array();
		foreach ($resourcesTemp as $resource) {
			if (null !== $resource) {
				$resources[] = $this->get($resource);
			} else {
				$resources[] = null;
			}
		}
		unset($resourcesTemp);

			// normalize privileges to array
		if (null === $privileges) {
			$privileges = array();
		} else if (!is_array($privileges)) {
			$privileges = array($privileges);
		}

		switch ($operation) {

				// add to the rules
			case self::OPERATION_ADD:
				foreach ($resources as $resource) {
					foreach ($roles as $role) {
						$rules =& $this->getRules($resource, $role, true);
						if (0 === count($privileges)) {
							$rules['allPrivileges']['type']   = $type;
							$rules['allPrivileges']['assert'] = $assert;
							if (!isset($rules['byPrivilegeId'])) {
								$rules['byPrivilegeId'] = array();
							}
						} else {
							foreach ($privileges as $privilege) {
								$rules['byPrivilegeId'][$privilege]['type']   = $type;
								$rules['byPrivilegeId'][$privilege]['assert'] = $assert;
							}
						}
					}
				}
				break;

				// remove from the rules
			case self::OPERATION_REMOVE:
				foreach ($resources as $resource) {
					foreach ($roles as $role) {
						$rules =& $this->getRules($resource, $role);
						if (null === $rules) {
							continue;
						}
						if (0 === count($privileges)) {
							if (null === $resource && null === $role) {
								if ($type === $rules['allPrivileges']['type']) {
									$rules = array(
										'allPrivileges' => array(
											'type'   => self::TYPE_DENY,
											'assert' => null
											),
										'byPrivilegeId' => array()
										);
								}
								continue;
							}
							if ($type === $rules['allPrivileges']['type']) {
								unset($rules['allPrivileges']);
							}
						} else {
							foreach ($privileges as $privilege) {
								if (isset($rules['byPrivilegeId'][$privilege]) &&
									$type === $rules['byPrivilegeId'][$privilege]['type']) {
									unset($rules['byPrivilegeId'][$privilege]);
								}
							}
						}
					}
				}
				break;

			default:
				throw new tx_community_acl_Exception("Unsupported operation; must be either '" . self::OPERATION_ADD . "' or '"
										   . self::OPERATION_REMOVE . "'");
		}

		return $this;
	}

	/**
	 * Returns true if and only if the Role has access to the Resource
	 *
	 * The $role and $resource parameters may be references to, or the string identifiers for,
	 * an existing Resource and Role combination.
	 *
	 * If either $role or $resource is null, then the query applies to all Roles or all Resources,
	 * respectively. Both may be null to query whether the ACL has a "blacklist" rule
	 * (allow everything to all). By default, tx_community_acl_Acl creates a "whitelist" rule (deny
	 * everything to all), and this method would return false unless this default has
	 * been overridden (i.e., by executing $acl->allow()).
	 *
	 * If a $privilege is not provided, then this method returns false if and only if the
	 * Role is denied access to at least one privilege upon the Resource. In other words, this
	 * method returns true if and only if the Role is allowed all privileges on the Resource.
	 *
	 * This method checks Role inheritance using a depth-first traversal of the Role registry.
	 * The highest priority parent (i.e., the parent most recently added) is checked first,
	 * and its respective parents are checked similarly before the lower-priority parents of
	 * the Role are checked.
	 *
	 * @param  tx_community_acl_AclRole|string	 $role
	 * @param  tx_community_acl_AclResource|string $resource
	 * @param  string							 $privilege
	 * @uses   tx_community_acl_Acl::get()
	 * @uses   tx_community_acl_AclRoleRegistry::get()
	 * @return boolean
	 */
	public function isAllowed($role = null, $resource = null, $privilege = null) {
		if (null !== $role) {
			$role = $this->getRoleRegistry()->get($role);
		}

		if (null !== $resource) {
			$resource = $this->get($resource);
		}

		if (null === $privilege) {
			// query on all privileges
			do {
				// depth-first search on $role if it is not 'allRoles' pseudo-parent
				if (null !== $role && null !== ($result = $this->roleDFSAllPrivileges($role, $resource, $privilege))) {
					return $result;
				}

				// look for rule on 'allRoles' psuedo-parent
				if (null !== ($rules = $this->getRules($resource, null))) {
					foreach ($rules['byPrivilegeId'] as $privilege => $rule) {
						if (self::TYPE_DENY === ($ruleTypeOnePrivilege = $this->getRuleType($resource, null, $privilege))) {
							return false;
						}
					}
					if (null !== ($ruleTypeAllPrivileges = $this->getRuleType($resource, null, null))) {
						return self::TYPE_ALLOW === $ruleTypeAllPrivileges;
					}
				}

				// try next Resource
				$resource = $this->resources[$resource->getResourceId()]['parent'];

			} while (true); // loop terminates at 'allResources' pseudo-parent
		} else {
			// query on one privilege
			do {
				// depth-first search on $role if it is not 'allRoles' pseudo-parent
				if (null !== $role && null !== ($result = $this->roleDFSOnePrivilege($role, $resource, $privilege))) {
					return $result;
				}

				// look for rule on 'allRoles' pseudo-parent
				if (null !== ($ruleType = $this->getRuleType($resource, null, $privilege))) {
					return self::TYPE_ALLOW === $ruleType;
				} else if (null !== ($ruleTypeAllPrivileges = $this->getRuleType($resource, null, null))) {
					return self::TYPE_ALLOW === $ruleTypeAllPrivileges;
				}

				// try next Resource
				$resource = $this->resources[$resource->getResourceId()]['parent'];

			} while (true); // loop terminates at 'allResources' pseudo-parent
		}
	}

	/**
	 * Returns the Role registry for this ACL
	 *
	 * If no Role registry has been created yet, a new default Role registry
	 * is created and returned.
	 *
	 * @return tx_community_acl_AclRoleRegistry
	 */
	protected function getRoleRegistry() {
		if (is_null($this->roleRegistry)) {
			$this->roleRegistry = t3lib_div::makeInstance('tx_community_acl_RoleRegistry');
		}

		return $this->roleRegistry;
	}

	/**
	 * Performs a depth-first search of the Role DAG, starting at $role, in order to find a rule
	 * allowing/denying $role access to all privileges upon $resource
	 *
	 * This method returns true if a rule is found and allows access. If a rule exists and denies access,
	 * then this method returns false. If no applicable rule is found, then this method returns null.
	 *
	 * @param  tx_community_acl_AclRole	 $role
	 * @param  tx_community_acl_AclResource $resource
	 * @return boolean|null
	 */
	protected function roleDFSAllPrivileges(tx_community_acl_AclRole $role, tx_community_acl_AclResource $resource = null) {
		$dfs = array(
			'visited' => array(),
			'stack'   => array()
		);

		if (null !== ($result = $this->roleDFSVisitAllPrivileges($role, $resource, $dfs))) {
			return $result;
		}

		while (null !== ($role = array_pop($dfs['stack']))) {
			if (!isset($dfs['visited'][$role->getRoleId()])) {
				if (null !== ($result = $this->roleDFSVisitAllPrivileges($role, $resource, $dfs))) {
					return $result;
				}
			}
		}

		return null;
	}

	/**
	 * Visits an $role in order to look for a rule allowing/denying $role access to all privileges upon $resource
	 *
	 * This method returns true if a rule is found and allows access. If a rule exists and denies access,
	 * then this method returns false. If no applicable rule is found, then this method returns null.
	 *
	 * This method is used by the internal depth-first search algorithm and may modify the DFS data structure.
	 *
	 * @param  tx_community_acl_AclRole	 $role
	 * @param  tx_community_acl_AclResource $resource
	 * @param  array				  $dfs
	 * @return boolean|null
	 * @throws tx_community_acl_Exception
	 */
	protected function roleDFSVisitAllPrivileges(tx_community_acl_AclRole $role, tx_community_acl_AclResource $resource = null, &$dfs = null) {
		if (null === $dfs) {
			/**
			 * @see tx_community_acl_Exception
			 */
			throw new tx_community_acl_Exception('$dfs parameter may not be null');
		}

		if (null !== ($rules = $this->getRules($resource, $role))) {
			foreach ($rules['byPrivilegeId'] as $privilege => $rule) {
				if (self::TYPE_DENY === ($ruleTypeOnePrivilege = $this->getRuleType($resource, $role, $privilege))) {
					return false;
				}
			}
			if (null !== ($ruleTypeAllPrivileges = $this->getRuleType($resource, $role, null))) {
				return self::TYPE_ALLOW === $ruleTypeAllPrivileges;
			}
		}

		$dfs['visited'][$role->getRoleId()] = true;
		foreach ($this->getRoleRegistry()->getParents($role) as $roleParentId => $roleParent) {
			$dfs['stack'][] = $roleParent;
		}

		return null;
	}

	/**
	 * Performs a depth-first search of the Role DAG, starting at $role, in order to find a rule
	 * allowing/denying $role access to a $privilege upon $resource
	 *
	 * This method returns true if a rule is found and allows access. If a rule exists and denies access,
	 * then this method returns false. If no applicable rule is found, then this method returns null.
	 *
	 * @param  tx_community_acl_AclRole	 $role
	 * @param  tx_community_acl_AclResource $resource
	 * @param  string					  $privilege
	 * @return boolean|null
	 * @throws tx_community_acl_Exception
	 */
	protected function roleDFSOnePrivilege(tx_community_acl_AclRole $role, tx_community_acl_AclResource $resource = null, $privilege = null) {
		if (null === $privilege) {
			/**
			 * @see tx_community_acl_Exception
			 */
			throw new tx_community_acl_Exception('$privilege parameter may not be null');
		}

		$dfs = array(
			'visited' => array(),
			'stack'   => array()
			);

		if (null !== ($result = $this->roleDFSVisitOnePrivilege($role, $resource, $privilege, $dfs))) {
			return $result;
		}

		while (null !== ($role = array_pop($dfs['stack']))) {
			if (!isset($dfs['visited'][$role->getRoleId()])) {
				if (null !== ($result = $this->roleDFSVisitOnePrivilege($role, $resource, $privilege, $dfs))) {
					return $result;
				}
			}
		}

		return null;
	}

	/**
	 * Visits an $role in order to look for a rule allowing/denying $role access to a $privilege upon $resource
	 *
	 * This method returns true if a rule is found and allows access. If a rule exists and denies access,
	 * then this method returns false. If no applicable rule is found, then this method returns null.
	 *
	 * This method is used by the internal depth-first search algorithm and may modify the DFS data structure.
	 *
	 * @param  tx_community_acl_AclRole	 $role
	 * @param  tx_community_acl_AclResource $resource
	 * @param  string					  $privilege
	 * @param  array					   $dfs
	 * @return boolean|null
	 * @throws tx_community_acl_Exception
	 */
	protected function roleDFSVisitOnePrivilege(tx_community_acl_AclRole $role, tx_community_acl_AclResource $resource = null, $privilege = null, &$dfs = null) {
		if (null === $privilege) {
			/**
			 * @see tx_community_acl_Exception
			 */
			throw new tx_community_acl_Exception('$privilege parameter may not be null');
		}

		if (null === $dfs) {
			/**
			 * @see tx_community_acl_Exception
			 */
			throw new tx_community_acl_Exception('$dfs parameter may not be null');
		}

		if (null !== ($ruleTypeOnePrivilege = $this->getRuleType($resource, $role, $privilege))) {
			return self::TYPE_ALLOW === $ruleTypeOnePrivilege;
		} else if (null !== ($ruleTypeAllPrivileges = $this->getRuleType($resource, $role, null))) {
			return self::TYPE_ALLOW === $ruleTypeAllPrivileges;
		}

		$dfs['visited'][$role->getRoleId()] = true;
		foreach ($this->getRoleRegistry()->getParents($role) as $roleParentId => $roleParent) {
			$dfs['stack'][] = $roleParent;
		}

		return null;
	}

	/**
	 * Returns the rule type associated with the specified Resource, Role, and privilege
	 * combination.
	 *
	 * If a rule does not exist or its attached assertion fails, which means that
	 * the rule is not applicable, then this method returns null. Otherwise, the
	 * rule type applies and is returned as either TYPE_ALLOW or TYPE_DENY.
	 *
	 * If $resource or $role is null, then this means that the rule must apply to
	 * all Resources or Roles, respectively.
	 *
	 * If $privilege is null, then the rule must apply to all privileges.
	 *
	 * If all three parameters are null, then the default ACL rule type is returned,
	 * based on whether its assertion method passes.
	 *
	 * @param  tx_community_acl_AclResource $resource
	 * @param  tx_community_acl_AclRole	 $role
	 * @param  string					  $privilege
	 * @return string|null
	 */
	protected function getRuleType(tx_community_acl_AclResource $resource = null, tx_community_acl_AclRole $role = null, $privilege = null) {
		// get the rules for the $resource and $role
		if (null === ($rules = $this->getRules($resource, $role))) {
			return null;
		}

		// follow $privilege
		if (null === $privilege) {
			if (isset($rules['allPrivileges'])) {
				$rule = $rules['allPrivileges'];
			} else {
				return null;
			}
		} else if (!isset($rules['byPrivilegeId'][$privilege])) {
			return null;
		} else {
			$rule = $rules['byPrivilegeId'][$privilege];
		}

		// check assertion if necessary
		if (null === $rule['assert'] || $rule['assert']->assert($this, $role, $resource, $privilege)) {
			return $rule['type'];
		} else if (null !== $resource || null !== $role || null !== $privilege) {
			return null;
		} else if (self::TYPE_ALLOW === $rule['type']) {
			return self::TYPE_DENY;
		} else {
			return self::TYPE_ALLOW;
		}
	}

	/**
	 * Returns the rules associated with a Resource and a Role, or null if no such rules exist
	 *
	 * If either $resource or $role is null, this means that the rules returned are for all Resources or all Roles,
	 * respectively. Both can be null to return the default rule set for all Resources and all Roles.
	 *
	 * If the $create parameter is true, then a rule set is first created and then returned to the caller.
	 *
	 * @param  tx_community_acl_AclResource $resource
	 * @param  tx_community_acl_AclRole	 $role
	 * @param  boolean					 $create
	 * @return array|null
	 */
	protected function &getRules(tx_community_acl_AclResource $resource = null, tx_community_acl_AclRole $role = null, $create = false) {
		// create a reference to null
		$null = null;
		$nullRef =& $null;

		// follow $resource
		do {
			if (null === $resource) {
				$visitor =& $this->rules['allResources'];
				break;
			}
			$resourceId = $resource->getResourceId();
			if (!isset($this->rules['byResourceId'][$resourceId])) {
				if (!$create) {
					return $nullRef;
				}
				$this->rules['byResourceId'][$resourceId] = array();
			}
			$visitor =& $this->rules['byResourceId'][$resourceId];
		} while (false);


		// follow $role
		if (null === $role) {
			if (!isset($visitor['allRoles'])) {
				if (!$create) {
					return $nullRef;
				}
				$visitor['allRoles']['byPrivilegeId'] = array();
			}
			return $visitor['allRoles'];
		}
		$roleId = $role->getRoleId();
		if (!isset($visitor['byRoleId'][$roleId])) {
			if (!$create) {
				return $nullRef;
			}
			$visitor['byRoleId'][$roleId]['byPrivilegeId'] = array();
		}
		return $visitor['byRoleId'][$roleId];
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/classes/acl/class.tx_community_acl_acl.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/classes/acl/class.tx_community_acl_acl.php']);
}

?>