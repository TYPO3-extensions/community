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
 * gateway to retrieve groups
 *
 * @package TYPO3
 * @subpackage community
 */
class tx_community_model_GroupGateway {

	/**
	 * find a group by its uid
	 *
	 * @param integer The groups uid
	 * @return	tx_community_model_Group
	 * @author	Frank Naegler <typo3@naegler.net>
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function findById($uid) {
		$group = null;

		$groupRow = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'*',
			'tx_community_group',
			'uid = ' . (int) $uid
		); // TODO restrict to certain part of the tree, use enablefields

		if (is_array($groupRow[0])) {
			$group = $this->createGroupFromRow($groupRow[0]);
		}

		return $group;
	}

	/**
	 * finds the requested group
	 *
	 * @return	tx_community_model_Group
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function findRequestedGroup() {
		$group = null;
		$communityRequest = t3lib_div::_GP('tx_community');

		if (isset($communityRequest['group'])) {
			$group = $this->findById($communityRequest['group']);
		}

		return $group;
	}

	/**
	 * finds all groups
	 *
	 * @return	array	Array of tx_community_model_Group instances
	 * @author	Frank Naegler <typo3@naegler.net>
	 */
	public function getAllGroups() {
		$groups = array();

		// TODO: restrict to groupType, for example: type = 4 should not be returned by this function
		$groupRows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'*',
			'tx_community_group',
			'1=1'
		); // TODO restrict to certain part of the tree, use enableFields

		foreach ($groupRows as $groupRow) {
			$groups[] = $this->createGroupFromRow($groupRow);
		}

		return $groups;
	}

	/**
	 * finds all groups where the given user is member
	 *
	 * @param	tx_community_model_User	The user for which to find his groups
	 * @return	array	array of tx_community_model_Group entries
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function findGroupsByUser(tx_community_model_User $user,$sorting = ' crdate desc') {
		$groups = array();

		$groupRows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'uid_local',
			'tx_community_group_members_mm,tx_community_group',
			'uid_local = uid and uid_foreign = ' . $user->getUid(),
			'',
			$sorting						
		);

		foreach ($groupRows as $groupRow) {
			$groups[] = $this->findById($groupRow['uid_local']);
		}

		return $groups;
	}

	/**
	 * creates a tx_community_model_Group instance from a database record
	 *
	 * @param	array	database record in the form of an array
	 * @return	tx_community_model_Group	A tx_community_model_Group group
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	protected function createGroupFromRow(array $row) {

		$groupClass = t3lib_div::makeInstanceClassName('tx_community_model_Group');
		$group = new $groupClass($row);

		return $group;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/model/class.tx_community_model_groupgateway.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/model/class.tx_community_model_groupgateway.php']);
}

?>