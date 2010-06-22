<?php

/***************************************************************
*  Copyright notice
*
*  (c) 2010 Pascal Jungblut <mail@pascal-jungblut.com>
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
 * Controller for the User object
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @author Pascal Jungblut <mail@pascalj.com>
 */
class Tx_Community_Controller_UserController extends Tx_Community_Controller_BaseController {

	/**
	 * @var Tx_Community_Domain_Repository_UserRepository
	 */
	protected $userRepository;

	/**
	 * @var Tx_Community_Domain_Repository_RelationRepository
	 */
	protected $relationRepository;

	/**
	 * Initializes the current action
	 *
	 * @return void
	 */
	protected function initializeAction() {
		$this->userRepository = t3lib_div::makeInstance('Tx_Community_Domain_Repository_UserRepository');
		$this->relationRepository = t3lib_div::makeInstance('Tx_Community_Domain_Repository_RelationRepository');
	}

	/**
	 * Get a profile image. We simply assign the user to the view and
	 * let a viewhelper do the work.
	 *
	 */
	public function imageAction() {
		$this->view->assign('user', $this->getRequestedUser());
	}


	/**
	 * Show the details like name, contact, homepage and so on.
	 */
	public function detailsAction() {
		$this->view->assign('user', $this->getRequestedUser());
	}

	/**
	 * Interactions on the userprofile. Like adding relations and initiating a message.
	 */
	public function interactionAction() {
		$this->view->assign('requestedUser', $this->getRequestedUser());
		$this->view->assign('requestingUser', $this->getRequestingUser());
	}

	/**
	 * Edit the details of a user.
	 *
	 * @param Tx_Community_Domain_Model_User $user
	 * @dontvalidate $user
	 */
	public function editAction(Tx_Community_Domain_Model_User $user) {
		$this->view->assign('user', $this->getRequestingUser());
	}

	/**
	 * Update the edited user.
	 *
	 * @param Tx_Community_Domain_Model_User $user
	 */
	public function updateAction(Tx_Community_Domain_Model_User $user) {
		$this->userRepository->update($user);
	}
}
?>