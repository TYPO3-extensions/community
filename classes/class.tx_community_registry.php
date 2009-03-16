<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008-2009 Frank Naegler <typo3@naegler.net>
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
 * registry class to store objects and make them available across the layers
 * of the MVC Pattern
 *
 * @author	Frank Naegler <typo3@naegler.net>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_Registry {

	protected static $instances = array();
	protected $extKey;
	protected $configuration = null;
	protected $llManager = null;

	/**
	 * singleton instance access method for the Registry class
	 *
	 * @return tx_community_Registry
	 */
	public function getInstance($extKey = 'tx_community') {
		if (!isset(self::$instances[$extKey])) {
			self::$instances[$extKey] = new tx_community_Registry($extKey);
		}
		return self::$instances[$extKey];
	}

	/**
	 * constructor for class tx_community_Registry
	 *
	 */
	protected function __construct($extKey) {
		$this->extKey = $extKey;
	}

	/**
	 * clone interceptor method, declared private to implement the singleton pattern
	 *
	 */
	private function __clone() {}

	/**
	 * sets the configuration arrray in the registry
	 *
	 * @param	array	configuration array to store
	 */
	public function setConfiguration(array $configuration) {
		$this->configuration = $configuration;
	}

	/**
	 * gets the configuration array stored in the registry.
	 * returns null if no configuration is available for the given key
	 *
	 * @return	mixed
	 */
	public function getConfiguration() {
		return $this->configuration;
	}

	/**
	 * sets the LocalizationManager object in the registry
	 *
	 * @param tx_community_LocalizationManager
	 */
	public function setLocalizationManager(tx_community_LocalizationManager $llManager) {
		$this->llManager = $llManager;
	}

	/**
	 * gets the LocalizationManager object stored in the registry.
	 * returns null if no object is available for the given key
	 *
	 * @return	tx_community_LocalizationManager
	 */
	public function getLocalizationManager() {
		return $this->llManager;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/classes/class.tx_community_registry.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/classes/class.tx_community_registry.php']);
}

?>