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

/**
 * The controller for the Tx_Community_Domain_Model_AclRole
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @author Pascal Jungblut <mail@pascalj.com>
 */
class Tx_Community_Controller_AclRoleController extends Tx_Community_Controller_BaseController {

	/**
	 * List all roles that a user created.
	 */
	public function listAction() {
		$this->view->assign('roles', Tx_Community_Helper_RepositoryHelper::getRepository('AclRole')->findByOwner($this->getRequestingUser()));
	}

	/**
	 * Edit a role
	 *
	 * @param Tx_Community_Domain_Model_AclRole $role
	 * @dontvalidate $role
	 */
	public function editAction(Tx_Community_Domain_Model_AclRole $role = NULL) {
		$rules = Tx_Community_Helper_RepositoryHelper::getRepository('AclRule')->findByRole($role);
		$this->view->assign('role', $role);
		$this->view->assign('rules', $rules);
	}

	/**
	 * Update a role
	 *
	 * @param Tx_Community_Domain_Model_AclRole $role
	 */
	public function updateAction(Tx_Community_Domain_Model_AclRole $role) {
		Tx_Community_Helper_RepositoryHelper::getRepository('AclRole')->update($role);
		$args = $this->request->getArguments();
		Tx_Community_Helper_AccessHelper::setRulesForRole($role, $args['rules']);
	}

	/**
	 * Show a form to create a new role
	 *
	 * @param Tx_Community_Domain_Model_AclRole $role
	 */
	public function newAction(Tx_Community_Domain_Model_AclRole $role = NULL) {
		$this->view->assign('role', $role);
		$this->view->assign('rules', Tx_Community_Helper_AccessHelper::getDefaultRules());
	}

	/**
	 * Create a new role
	 *
	 * @param Tx_Community_Domain_Model_AclRole $role
	 */
	public function createAction(Tx_Community_Domain_Model_AclRole $role) {
		$role->setOwner($this->getRequestingUser());
		$role->setDefaultRole(Tx_Community_Domain_Model_AclRole::NOT_DEFAULT_ROLE);

		$args = $this->request->getArguments();
		Tx_Community_Helper_AccessHelper::setRulesForRole($role, $args['rules']);

		Tx_Community_Helper_RepositoryHelper::getRepository('AclRole')->add($role);
	}

	/**
	 * Set the role for a user
	 *
	 * @param Tx_Community_Domain_Model_Relation $relation
	 * @param unknown_type $role
	 */
	public function assignAction() {
		$relation = Tx_Community_Helper_RepositoryHelper::getRepository('Relation')->findRelationBetweenUsers(
				$this->getRequestingUser(),
				$this->getRequestedUser()
			);
		$relation->setInitiatingRole(Tx_Community_Helper_RepositoryHelper::getRepository('AclRole')->findByUid(7));
		Tx_Community_Helper_RepositoryHelper::getRepository('Relation')->update($relation);
		return 'test';

		if (!$this->request->hasArgument('role')) {
			$this->view->assign('requestedUser', $this->getRequestedUser());
			$this->view->assign('roles', Tx_Community_Helper_RepositoryHelper::getRepository('AclRole')->findByOwner($this->getRequestingUser()));
		} else {
			$role = Tx_Community_Helper_RepositoryHelper::getRepository('AclRole')->findByUid($this->request->getArgument('role'));
			$relation = Tx_Community_Helper_RepositoryHelper::getRepository('Relation')->findRelationBetweenUsers(
				$this->getRequestingUser(),
				$this->getRequestedUser()
			);
			$relation = Tx_Community_Helper_RelationHelper::setAclRole($relation, $this->getRequestedUser(), $role);
			Tx_Community_Helper_RepositoryHelper::getRepository('Relation')->update($relation);

			$persistenceManager = t3lib_div::makeInstance('Tx_Extbase_Persistence_Manager');
			$persistenceManager->persistAll();
		}
	}
}
?>