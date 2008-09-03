<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2008 Frank Nägler <typo3@naegler.net>
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

require_once(t3lib_extMgm::extPath('community').'model/class.tx_community_model_group.php');

/**
 * gateway too retrieve groups
 *
 * @author	Frank Nägler <typo3@naegler.net>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_model_GroupGateway {

	/**
	 * constructor for class tx_community_model_GroupGateway
	 */
	public function __construct() {

	}

	/**
	 * find a group by its uid
	 *
	 * @param integer The groups uid
	 * @return	tx_community_model_Group
	 */
	public function findById($uid) {
		$group = null;

		$groupRow = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'*',
			'fe_groups',
			'uid = ' . (int) $uid
		); // TODO restrict to certain part of the tree
		$groupRow = $groupRow[0];

		// TODO first check whether we got exactly one result
		if (is_array($groupRow)) {
			$group = $this->createGroupFromRow($groupRow);
		}

		return $group;
	}

	/**
	 * find current group
	 *
	 * @return	tx_community_model_Group
	 */
	public function findCurrentGroup() {
		$group = null;
		$communityRequest = t3lib_div::_GP('tx_community');
		if (!isset($communityRequest['group'])) {
			return $group;
		}
		
		$groupRow = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'*',
			'fe_groups',
			'uid = ' . (int) $communityRequest['group']
		); // TODO restrict to certain part of the tree
		
		$groupRow = $groupRow[0];
		
		// TODO first check whether we got exactly one result
		if (is_array($groupRow)) {
			$group = $this->createGroupFromRow($groupRow);
		}
		
		return $group;
	}
	
	protected function createGroupFromRow(array $row) {
		/**
		 * @var tx_community_model_Group
		 */
		$groupClass = t3lib_div::makeInstanceClassName('tx_community_model_Group');
		
		/**
		 * @var tx_community_model_Group
		 */
		$group = new $groupClass($row['uid']);
		
		return $group;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/model/class.tx_community_model_groupgateway.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/model/class.tx_community_model_groupgateway.php']);
}

?>