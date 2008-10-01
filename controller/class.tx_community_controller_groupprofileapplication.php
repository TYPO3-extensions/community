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

require_once($GLOBALS['PATH_community'] . 'model/class.tx_community_model_groupgateway.php');

/**
 * Group Profile Application Controller
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_controller_GroupProfileApplication extends tx_community_controller_AbstractCommunityApplication  {

	protected $groupGateway   = null;
	protected $requestedGroup = null;

	/**
	 * constructor for class tx_community_controller_GroupProfileApplication
	 */
	public function __construct() {
		parent::__construct();

		$this->prefixId = 'tx_community_controller_GroupProfileApplication';
		$this->scriptRelPath = 'controller/class.tx_community_controller_groupprofileapplication.php';
		$this->name = 'groupProfile';

		$this->groupGateway = t3lib_div::makeInstance('tx_community_model_GroupGateway');
	}

	/**
	 * returns the group that shall be displayed
	 *
	 * @return tx_community_model_Group
	 */
	public function getRequestedGroup() {
		$requestedGroup = null;

		if (is_null($this->requestedGroup)) {
			$communityRequest = t3lib_div::GParrayMerged('tx_community');
			$requestedGroup = $this->groupGateway->findById((int) $communityRequest['group']);

			$this->requestedGroup = $requestedGroup;
		} else {
			$requestedGroup = $this->requestedGroup;
		}

		if (!($requestedGroup instanceof tx_community_model_Group)) {
			// TODO throw a "group not found exception"
		}

		return $requestedGroup;
	}

	/**
	 * sets the requested group, usefull for example when the requested group is
	 * different from the one given in the GET parameter or no GET parameter is
	 * available
	 *
	 * @param	tx_community_model_Group	The group to set as requested group
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function setRequestedGroup(tx_community_model_Group $group) {
		$this->requestedGroup = $group;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_groupprofileapplication.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_groupprofileapplication.php']);
}

?>