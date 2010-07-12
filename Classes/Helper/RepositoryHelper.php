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

class Tx_Community_Helper_RepositoryHelper {

	/**
	 * An array of repositories
	 *
	 * @var array
	 */
	static protected $repositories;

	/**
	 * Get the repository for the model $repositoryName (e.g. "user")
	 *
	 * @param string $repositoryName
	 * @return Tx_Extbase_Persistence_Repository
	 */
	static public function getRepository($repositoryName) {
		if (self::$repositories[$repositoryName] === NULL) {
			self::$repositories[$repositoryName] = t3lib_div::makeInstance('Tx_Community_Domain_Repository_' . ucfirst($repositoryName) . 'Repository');
		}
		return self::$repositories[$repositoryName];
	}
}
?>