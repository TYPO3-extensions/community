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
 * AclRule
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Tx_Community_Domain_Model_AclRule extends Tx_Extbase_DomainObject_AbstractEntity {

	/**
	 * name
	 * @var string
	 * @validate NotEmpty
	 */
	protected $name;

	/**
	 * resource
	 * @var string
	 * @validate NotEmpty
	 */
	protected $resource;

	/**
	 * accessMode
	 * @var integer
	 */
	protected $accessMode;

	/**
	 * role
	 * @var Tx_Community_Domain_Model_AclRole
	 */
	protected $role;



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
	 * Setter for resource
	 *
	 * @param string $resource resource
	 * @return void
	 */
	public function setResource($resource) {
		$this->resource = $resource;
	}

	/**
	 * Getter for resource
	 *
	 * @return string resource
	 */
	public function getResource() {
		return $this->resource;
	}

	/**
	 * Setter for accessMode
	 *
	 * @param integer $accessMode accessMode
	 * @return void
	 */
	public function setAccessMode($accessMode) {
		$this->accessMode = $accessMode;
	}

	/**
	 * Getter for accessMode
	 *
	 * @return integer accessMode
	 */
	public function getAccessMode() {
		return $this->accessMode;
	}

	/**
	 * Setter for role
	 *
	 * @param Tx_Community_Domain_Model_AclRole $role role
	 * @return void
	 */
	public function setRole(Tx_Community_Domain_Model_AclRole $role) {
		$this->role = $role;
	}

	/**
	 * Getter for role
	 *
	 * @return Tx_Community_Domain_Model_AclRole role
	 */
	public function getRole() {
		return $this->role;
	}

}
?>