<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Ingo Renner <ingo@typo3.org>
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


require_once($GLOBALS['PATH_community'] . 'interfaces/acl/interface.tx_community_acl_aclresource.php');

/**
 * ACL resource class
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_acl_Resource implements tx_community_acl_AclResource {

	/**
	 * Unique id of Resource
	 *
	 * @var string
	 */
	protected $resourceId;

	/**
	 * Sets the Resource identifier
	 *
	 * @param  string $resourceId
	 * @return void
	 */
	public function __construct($resourceId) {
		$this->resourceId = (string) $resourceId;
	}

	/**
	 * returns the Resource identifier
	 *
	 * @return string
	 */
	public function getResourceId() {
		return $this->resourceId;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/classes/acl/class.tx_community_acl_resource.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/classes/acl/class.tx_community_acl_resource.php']);
}

?>