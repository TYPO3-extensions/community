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
 * A community group, uses TYPO3's fe_groups
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @author 	Frank Naegler <typo3@naegler.net>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_model_Group implements tx_community_acl_AclResource {
	protected $uid;
	protected $admins = array();
	protected $members = array();
	protected $data = array();
	/**
	 * @var tslib_cObj
	 */
	protected $cObj;
	/**
	 * @var tx_community_model_UserGateway
	 */
	protected $userGateway;
	

	/**
	 * constructor for class tx_community_model_Group
	 */
	public function __construct($uid = null) {
		$this->uid = (is_null($uid)) ? ($uid) : ((int) $uid);
		$this->init();
	}

	protected function init() {
		$this->cObj = t3lib_div::makeInstance('tslib_cObj');
		if (!is_null($this->uid)) {
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'*',
				'fe_groups',
				'uid = ' . $this->uid . $this->cObj->enableFields('fe_groups')
			);
			if ($GLOBALS['TYPO3_DB']->sql_num_rows($res) > 0) {
				$data = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
				$this->setDataToStore($data);
			}
		}
	}
	
	/**
	 * method to save (update or create) an usergroup
	 *
	 * @return bool|int
	 */
	public function save() {
		$data = $this->getDataForSave();
		if (is_null($this->uid)) {
			// insert
			$GLOBALS['TYPO3_DB']->exec_INSERTquery(
				'fe_groups',
				$data
			);
			$this->uid = $GLOBALS['TYPO3_DB']->sql_insert_id();
			return $this->uid;
		} else {
			// update
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
				'fe_groups',
				'uid = ' . $this->uid,
				$data
			);
			return ($GLOBALS['TYPO3_DB']->sql_affected_rows());
		}
	}

	/**
	 * __call method for dynamic handling of getter and setter methods
	 *
	 * @param string $name
	 * @param array $arguments
	 * @return void|mixted
	 */
	public function __call($name, $arguments) {
		if (substr($name, 0, 3) === 'set') {
			$param = strtolower(substr($name, 3));
			$this->data[$param] = $arguments[0];
		}

		if (substr($name, 0, 3) === 'get') {
			$param = strtolower(substr($name, 3));
			return $this->data[$param];
		}
	}
	
	/**
	 * returns the Resource identifier
	 *
	 * @return string
	 */
	public function getResourceId() {
		return (string) 'tx_community_model_Group' . $this->uid; //TODO replace class name by table name
	}
	
	/**
	 * prepare data for saving
	 *
	 * @return array of data
	 */
	protected function getDataForSave() {
		$tmpData = array();
		foreach ($this->data as $k => $v) {
			switch ($k) {
				case 'tx_community_admins':
					$tmp = array();
					foreach ($this->data['tx_community_admins'] as $admin) {
						if ($admin instanceof tx_community_model_User) {
							$tmp[] = $admin->getUid();
						}
					}
					$tmpData[$k] = implode(',', $tmp);
				break;
				default:
					$tmpData[$k] = $v;
				break;
			}
		}
		return $tmpData;
	}

	/**
	 * prepare data for storing in object
	 *
	 * @param array $data of data
	 */
	protected function setDataToStore($data) {
		foreach ($data as $k => $v) {
			switch ($k) {
				case 'tx_community_admins':
					if (strlen($v) > 0) {
						$uids = t3lib_div::trimExplode(',', $v);
						foreach ($uids as $uid) {
							$admUser = $this->userGateway->findById($uid);
							if (!is_null($admUser)) {
								$this->data['tx_community_admins'][$admUser->getUid()] = $admUser;
							}
						}
					} else {
						$this->data['tx_community_admins'] = array();
					}
				break;
				default:
					$this->data[$k] = $v;
				break;
			}
		}
	}

	public function addAdmin(tx_community_model_User $user) {
		$this->data['tx_community_admins'][$user->getUid()] = $user;
	}

	public function removeAdmin(tx_community_model_User $user) {
		unset($this->data['tx_community_admins'][$user->getUid()]);
	}

	public function isAdmin(tx_community_model_User $user) {
		return isset($this->data['tx_community_admins'][$user->getUid()]);
	}

	public function addMember(tx_community_model_User $user) {
		if (!$this->isMember($user)) {
			$GLOBALS['TYPO3_DB']->exec_INSERTquery(
				'fe_groups_tx_community_members_mm',
				array(
					'uid_local'		=> $this->uid,
					'uid_foreign'	=> $user->getUid()
				)
			);
			if ($GLOBALS['TYPO3_DB']->sql_insert_id()) {
				$this->data['tx_community_members'] = $this->data['tx_community_members'] + 1;
			}
		}
		return $this->save();
	}

	public function removeMember(tx_community_model_User $user) {
		if ($this->isMember($user)) {
			$res = $GLOBALS['TYPO3_DB']->exec_DELETEquery(
				'fe_groups_tx_community_members_mm',
				'uid_local = ' . $this->uid . ' AND uid_foreign = ' . $user->getUid()
			);
			if ($GLOBALS['TYPO3_DB']->sql_affected_rows()) {
				$this->data['tx_community_members'] = $this->data['tx_community_members'] - 1;
			}
		}
		return $this->save();
	}

	public function isMember(tx_community_model_User $user) {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'*',
			'fe_groups_tx_community_members_mm',
			'uid_local = ' . $this->uid . ' AND uid_foreign = ' . $user->getUid()
		);
		return ($GLOBALS['TYPO3_DB']->sql_num_rows() > 0);
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/model/class.tx_community_model_group.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/model/class.tx_community_model_group.php']);
}

?>