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
 */

/**
 * A community user. Should be mapped on fe_users
 *
 * @author pascaljungblut
 */
class Tx_Community_Controller_UserController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * @var Tx_Community_Domain_Repository_UserRepository
	 */
	protected $userRepository;


	/**
	 * The current user
	 *
	 * @var Tx_Community_Domain_Model_User
	 */
	protected $user;

	/**
	 * Initializes the current action
	 *
	 * @return void
	 */
	protected function initializeAction() {
		$this->userRepository = t3lib_div::makeInstance('Tx_Community_Domain_Repository_UserRepository');
		$this->user = $this->userRepository->findCurrentUser();

	}


	/**
	 * Get a profile image. We simply assign the user to the view and
	 * let a viewhelper do the work.
	 *
	 */
	public function imageAction() {
		$this->view->assign('user', $this->user);
	}

}
?>