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
 * AclRole
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Tx_Community_Domain_Model_AclRole extends Tx_Community_Domain_Model_Observer_AbstractObservableEntity {

	/**
	 * defaultRole property if this role is NOT a default
	 *
	 * @var
	 */
	const NOT_DEFAULT_ROLE = 0;

	/**
	 * name
	 * @var string
	 * @validate NotEmpty
	 */
	protected $name;

	/**
	 * the owner of the role (for faster searching)
	 *
	 * @var Tx_Community_Domain_Model_User
	 * @lazy
	 */
	protected $owner;

	/**
	 * Setter for name
	 *
	 * @param string $name name
	 * @return void
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * Getter for name
	 *
	 * @return string name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Set the owner
	 *
	 * @param Tx_Community_Domain_Model_User $owner
	 */
	public function setOwner(Tx_Community_Domain_Model_User $owner) {
		$this->owner = $owner;
	}

	/**
	 * Get the owner
	 *
	 * @return Tx_Community_Domain_Model_User
	 */
	public function getOwner() {
		return $this->owner;
	}

	/**
	 * Set the type of the default role (NOT_DEFAULT_ROLE for userconfigured roles)
	 *
	 * @param integer $role
	 */
	public function setDefaultRole($role) {
		$this->defaultRole = $role;
	}

	/**
	 * Get the tye of the default role
	 */
	public function getDefaultRole() {
		return $this->defaultRole;
	}
}
?>