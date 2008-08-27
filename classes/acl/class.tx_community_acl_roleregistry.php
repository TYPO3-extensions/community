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

require_once($GLOBALS['PATH_community'] . 'classes/acl/class.tx_community_acl_roleregistryexception.php');

/**
 * ACL role registry
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_acl_RoleRegistry {

	/**
     * Internal Role registry data storage
     *
     * @var array
     */
    protected $roles = array();

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
     * @param  tx_community_acl_AclRole              $role
     * @param  tx_community_acl_AclRole|string|array $parents
     * @throws tx_communitty_acl_RoleRegistryException
     * @return tx_community_acl_RoleRegistry Provides a fluent interface
     */
    public function add(tx_community_acl_AclRole $role, $parents = null)
    {
        $roleId = $role->getRoleId();

        if ($this->has($roleId)) {
            /**
             * @see tx_communitty_acl_RoleRegistryException
             */
            throw new tx_communitty_acl_RoleRegistryException("Role id '$roleId' already exists in the registry");
        }

        $roleParents = array();

        if (null !== $parents) {
            if (!is_array($parents)) {
                $parents = array($parents);
            }
            /**
             * @see tx_communitty_acl_RoleRegistryException
             */
            foreach ($parents as $parent) {
                try {
                    if ($parent instanceof tx_community_acl_AclRole) {
                        $roleParentId = $parent->getRoleId();
                    } else {
                        $roleParentId = $parent;
                    }
                    $roleParent = $this->get($roleParentId);
                } catch (tx_communitty_acl_RoleRegistryException $e) {
                    throw new tx_communitty_acl_RoleRegistryException("Parent Role id '$roleParentId' does not exist");
                }
                $roleParents[$roleParentId] = $roleParent;
                $this->roles[$roleParentId]['children'][$roleId] = $role;
            }
        }

        $this->roles[$roleId] = array(
            'instance' => $role,
            'parents'  => $roleParents,
            'children' => array()
            );

        return $this;
    }

    /**
     * Returns the identified Role
     *
     * The $role parameter can either be a Role or a Role identifier.
     *
     * @param  tx_community_acl_AclRole|string $role
     * @throws tx_communitty_acl_RoleRegistryException
     * @return tx_community_acl_AclRole
     */
    public function get($role)
    {
        if ($role instanceof tx_community_acl_AclRole) {
            $roleId = $role->getRoleId();
        } else {
            $roleId = (string) $role;
        }

        if (!$this->has($role)) {
            /**
             * @see tx_communitty_acl_RoleRegistryException
             */
            throw new tx_communitty_acl_RoleRegistryException("Role '$roleId' not found");
        }

        return $this->roles[$roleId]['instance'];
    }

    /**
     * Returns true if and only if the Role exists in the registry
     *
     * The $role parameter can either be a Role or a Role identifier.
     *
     * @param  tx_community_acl_AclRole|string $role
     * @return boolean
     */
    public function has($role)
    {
        if ($role instanceof tx_community_acl_AclRole) {
            $roleId = $role->getRoleId();
        } else {
            $roleId = (string) $role;
        }

        return isset($this->roles[$roleId]);
    }

    /**
     * Returns an array of an existing Role's parents
     *
     * The array keys are the identifiers of the parent Roles, and the values are
     * the parent Role instances. The parent Roles are ordered in this array by
     * ascending priority. The highest priority parent Role, last in the array,
     * corresponds with the parent Role most recently added.
     *
     * If the Role does not have any parents, then an empty array is returned.
     *
     * @param  tx_community_acl_AclRole|string $role
     * @uses   tx_community_acl_RoleRegistry::get()
     * @return array
     */
    public function getParents($role)
    {
        $roleId = $this->get($role)->getRoleId();

        return $this->roles[$roleId]['parents'];
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
     * @param  tx_community_acl_AclRole|string $role
     * @param  tx_community_acl_AclRole|string $inherit
     * @param  boolean                        $onlyParents
     * @throws tx_communitty_acl_RoleRegistryException
     * @return boolean
     */
    public function inherits($role, $inherit, $onlyParents = false)
    {
        /**
         * @see tx_communitty_acl_RoleRegistryException
         */
        try {
            $roleId     = $this->get($role)->getRoleId();
            $inheritId = $this->get($inherit)->getRoleId();
        } catch (tx_communitty_acl_RoleRegistryException $e) {
            throw $e;
        }

        $inherits = isset($this->roles[$roleId]['parents'][$inheritId]);

        if ($inherits || $onlyParents) {
            return $inherits;
        }

        foreach ($this->roles[$roleId]['parents'] as $parentId => $parent) {
            if ($this->inherits($parentId, $inheritId)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Removes the Role from the registry
     *
     * The $role parameter can either be a Role or a Role identifier.
     *
     * @param  tx_community_acl_AclRole|string $role
     * @throws tx_communitty_acl_RoleRegistryException
     * @return tx_community_acl_RoleRegistry Provides a fluent interface
     */
    public function remove($role)
    {
        /**
         * @see tx_communitty_acl_RoleRegistryException
         */
        try {
            $roleId = $this->get($role)->getRoleId();
        } catch (tx_communitty_acl_RoleRegistryException $e) {
            throw $e;
        }

        foreach ($this->roles[$roleId]['children'] as $childId => $child) {
            unset($this->roles[$childId]['parents'][$roleId]);
        }
        foreach ($this->roles[$roleId]['parents'] as $parentId => $parent) {
            unset($this->roles[$parentId]['children'][$roleId]);
        }

        unset($this->roles[$roleId]);

        return $this;
    }

    /**
     * Removes all Roles from the registry
     *
     * @return tx_community_acl_RoleRegistry Provides a fluent interface
     */
    public function removeAll()
    {
        $this->roles = array();

        return $this;
    }
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/classes/acl/class.tx_community_acl_roleregistry.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/classes/acl/class.tx_community_acl_roleregistry.php']);
}

?>