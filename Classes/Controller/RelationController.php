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
 * The relation controller.
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @author Pascal Jungblut <mail@pascalj.com>
 */
class Tx_Community_Controller_RelationController extends Tx_Community_Controller_BaseController {

	/**
	 * @var Tx_Commnity_Domain_Repository_UserRepository
	 */
	protected $userRepository;

	/**
	 * initialize action: called before any other action
	 *
	 * @see Classes/Controller/Tx_Community_Controller_BaseController#initializeAction()
	 */
	protected function initializeAction() {
		parent::initializeAction();
	}

	/**
	 * @param Tx_Community_Domain_Model_User $user
	 */
	public function listSomeAction() {
		$relations = $this->relationRepository->findRelationsForUser($this->getRequestedUser());
		$users = array();
		foreach($relations as $relation) {
			if ($relation->getRequestedUser()->getUid() == $this->getRequestedUser()->getUid()) {
				$users[] = $relation->getInitiatingUser();
			} else {
				$users[] = $relation->getRequestedUser();
			}
		}
		$this->view->assign('relations', $users);
	}

	/**
	 * Requests a relation between two users. It will set the status to NEW.
	 *
	 * @param Tx_Community_Domain_Model_User $user
	 * @see Tx_Community_Domain_Model_Relation
	 */
	public function requestAction(Tx_Community_Domain_Model_User $user) {
		$relation = $this->relationRepository->findRelationBetweenUsers($user, $this->getRequestingUser());
		if ($relation === NULL) {
			$relation = new Tx_Community_Domain_Model_Relation();

			// set the details for the relation
			$relation->setInitiatingUser($this->getRequestingUser());
			$relation->setRequestedUser($user);
			$requestedRole = Tx_Community_Helper_AccessHelper::getFriendRole($user);
			$requestingRole = Tx_Community_Helper_AccessHelper::getFriendRole($this->getRequestingUser());
			$relation->setStatus(Tx_Community_Domain_Model_Relation::RELATION_STATUS_NEW);
			$this->relationRepository->add($relation);
		} elseif ($relation instanceof Tx_Community_Domain_Model_Relation) {
			if($relation->getStatus() == Tx_Community_Domain_Model_Relation::RELATION_STATUS_REJECTED) {
				if($relation->getRequestedUser() == $user) {
					$this->flashMessages->add($this->_('relation.request.allreadyRejected'));

					// TODO what should happen?
					return;
				} else {
					$requestedUser = $relation->getRequestedUser();
					$relation->setRequestedUser($relation->getInitiatingUser());
					$relation->setInitiatingUser($requestedUser);
					$relation->setStatus(Tx_Community_Domain_Model_Relation::RELATION_STATUS_NEW);
					$this->relationRepository->update($relation);
				}
			}
		} else {
			// more than one relation? something is wrong.
			throw new Tx_Community_Exception_UnexpectedException(
				'There are more than one relations between user ' . $user->getUid() . ' and user ' . $this->getRequestingUser()->getUid()
			);
		}

		// TODO send mails on request
	}

	/**
	 * Confirm a relation
	 *
	 * @param Tx_Community_Domain_Model_User $relation
	 */
	public function confirmAction(Tx_Community_Domain_Model_Relation $relation) {
		if($this->getRequestingUser() instanceof Tx_Community_Domain_Model_User) {
			if ($relation->getRequestedUser()->getUid() == $this->getRequestingUser()->getUid()) {
				$this->confirmRelation($relation);
			}
		} else {
			throw new Tx_Community_Exception_UserNotFoundException('No one is logged in.');
		}
	}


	/**
	 * Reject a relation.
	 *
	 * @param Tx_Community_Domain_Model_Relation $relation
	 */
	public function rejectAction(Tx_Community_Domain_Model_Relation $relation) {
		if ($this->getRequestingUser() instanceof Tx_Community_Domain_Model_User) {
			if ($relation->getRequestedUser()->getUid() == $this->getRequestingUser()->getUid()) {
				$this->rejectRelation($relation);
			}
		} else {
			throw new Tx_Community_Exception_UserNotFoundException('No one is logged in.');
		}
	}

	/**
	 * List all unconfirmed relations.
	 */
	public function unconfirmedAction() {
		if ($this->ownProfile()) {
			$this->view->assign('unconfirmedRelations', $this->relationRepository->findUnconfirmedForUser(
				$this->getRequestingUser())
			);
		} else {
			$this->view->assign('unconfirmedRelations', array());
		}
	}

	/**
	 * Cancel a relation that is allready accepted.
	 *
	 * @param Tx_Community_Domain_Model_Relation $relation
	 */
	public function cancelAction(Tx_Community_Domain_Model_Relation $relation) {
		if ($this->getRequestingUser() instanceof Tx_Community_Domain_Model_User) {
			$this->cancelRelation($relation);
		} else {
			throw new Tx_Community_Exception_UserNotFoundException('No one is logged in.');
		}
	}

	/**
	 * Confirm a relation and notify the initiating user
	 *
	 * @param Tx_Community_Domain_Model_Relation $relation
	 */
	protected function confirmRelation(Tx_Community_Domain_Model_Relation $relation) {
		$relation->setStatus(Tx_Community_Domain_Model_Relation::RELATION_STATUS_CONFIRMED);
		$this->relationRepository->update($relation);
		// TODO send mails on confirmation
	}

	/**
	 * Reject a relation and notify the initiating user
	 *
	 * @param Tx_Community_Domain_Model_Relation $relation
	 */
	protected function rejectRelation(Tx_Community_Domain_Model_Relation $relation) {
		$relation->setStatus(Tx_Community_Domain_Model_Relation::RELATION_STATUS_REJECTED);
		$this->relationRepository->update($relation);
		// TODO send mails on rejection
	}

	/**
	 * Cancel a relation. Happens when an initiating user cancels the request _or_ if
	 * an accepted relation gets cancelled by one of the users.
	 *
	 * @param Tx_Community_Domain_Model_Relation $relation
	 */
	protected function cancelRelation(Tx_Community_Domain_Model_Relation $relation) {
		$relation->setStatus(Tx_Community_Domain_Model_Relation::RELATION_STATUS_CANCELLED);
		$this->relationRepository->remove($relation);
		// TODO send mails on rejection
	}

	protected function setDefaultRole() {

	}
}
?>