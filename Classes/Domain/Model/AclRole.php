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
class Tx_Community_Domain_Model_AclRole extends Tx_Extbase_DomainObject_AbstractValueObject {
	
	/**
	 * name
	 * @var string
	 * @validate NotEmpty
	 */
	protected $name;
	
	/**
	 * isPublic
	 * @var boolean
	 */
	protected $is_public;
	
	/**
	 * isFriend
	 * @var boolean
	 */
	protected $is_friend;
	
	
	
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
	 * Setter for isPublic
	 *
	 * @param boolean $isPublic isPublic
	 * @return void
	 */
	public function setIsPublic($isPublic) {
		$this->isPublic = $isPublic;
	}

	/**
	 * Getter for isPublic
	 *
	 * @return boolean isPublic
	 */
	public function getIsPublic() {
		return $this->isPublic;
	}
	
	/**
	 * Returns the boolean state of isPublic
	 *
	 * @return bool The state of isPublic
	 */
	public function isIsPublic() {
		$this->getIsPublic();
	}
	
	/**
	 * Setter for isFriend
	 *
	 * @param boolean $isFriend isFriend
	 * @return void
	 */
	public function setIsFriend($isFriend) {
		$this->isFriend = $isFriend;
	}

	/**
	 * Getter for isFriend
	 *
	 * @return boolean isFriend
	 */
	public function getIsFriend() {
		return $this->isFriend;
	}
	
	/**
	 * Returns the boolean state of isFriend
	 *
	 * @return bool The state of isFriend
	 */
	public function isIsFriend() {
		$this->getIsFriend();
	}
	
}
?>