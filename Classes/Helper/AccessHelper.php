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

class Tx_Community_Helper_AccessHelper {

	/**
	 * @var Tx_Community_Domain_Repository_RelationRepository
	 */
	protected $relationRepository;

	/**
	 * Constructor. Sets up everything that is needed.
	 */
	public function __construct() {
		$this->relationRepository = t3lib_div::makeInstance('Tx_Community_Domain_Repository_RelationRepository');
	}

	/**
	 * Check if a $requestingUser has access to $resource of $requestedUser
	 *
	 * @param string $resource
	 * @param Tx_Commonity_Domain_Model_Model $requestingUser
	 * @param Tx_Commonity_Domain_Model_Relation $requestedUser
	 */
	public function hasAccess($resource, Tx_Commonity_Domain_Model_Model $requestingUser, Tx_Commonity_Domain_Model_Relation $requestedUser) {
		$relation = $this->relationRepository->findRelationBetweenUsers($requestingUser, $requestedUser);
		if ($relation instanceof Tx_Commonity_Domain_Model_Relation) {
			if ($relation->getInitiatingUser()->getId() == $requestingUser) {
				$role = $relation->getRequestedRole();
			} else {
				$role = $relation->getInitiatingRole();
			}
		}
	}
}
?>