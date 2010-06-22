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
 * A base controller that implements basic functions that are needed
 * all over the project. Holds the requested and requesting user.
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @author Pascal Jungblut <mail@pascalj.com>
 */
class Tx_Community_Controller_BaseController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * @var Tx_Community_Domain_Repository_UserRepository
	 */
	protected $userRepository;

	/**
	 * @var Tx_Community_Domain_Repository_RelationRepository
	 */
	protected $relationRepository;

	/**
	 * the user who is requested to view
	 *
	 * @var Tx_Community_Domain_Model_User
	 */
	protected $requestedUser = NULL;

	/**
	 * The requesting user. Normally the logged in fe_user
	 *
	 * @var Tx_Community_Domain_Model_User
	 */
	protected $requestingUser = NULL;


	protected function initializeAction() {
		$this->userRepository = t3lib_div::makeInstance('Tx_Community_Domain_Repository_UserRepository');
		$this->relationRepository = t3lib_div::makeInstance('Tx_Community_Domain_Repository_RelationRepository');
	}

	/**
	 * Get the requested user
	 *
	 * @return Tx_Community_Domain_Model_User
	 */
	protected function getRequestedUser() {
		if (!$this->requestedUser) {
			if ($this->request->hasArgument('user')) {
				$this->requestedUser = $this->userRepository->findByUid((int) $this->request->getArgument('user'));
			} else {
				$this->requestedUser = $this->getRequestingUser();
			}
		}
		if (!$this->requestedUser) {
			throw new Tx_Community_Exception_UserNotFoundException('The requested user could not be found.');
		}
		return $this->requestedUser;
	}

	/**
	 * Get the requesting user
	 *
	 * @return Tx_Community_Domain_Model_User
	 */
	protected function getRequestingUser() {
		if (!$this->requestingUser) {
			$this->requestingUser = $this->userRepository->findCurrentUser();
		}
		return $this->requestingUser;
	}

	/**
	 * Translate $key
	 * @param string $key
	 * @param array $arguments
	 */
	protected function _($key, $arguments = array()) {
		$translator = new Tx_Extbase_Utility_Localization();
		return $translator->translate($key,'community', $arguments);
	}
}
?>
