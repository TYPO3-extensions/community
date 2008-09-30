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

require_once(PATH_t3lib . 'class.t3lib_page.php');
require_once($GLOBALS['PATH_community'] . 'interfaces/acl/interface.tx_community_acl_aclresource.php');
require_once($GLOBALS['PATH_community'] . 'classes/class.tx_community_localizationmanager.php');

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
	protected $admins  = array();
	protected $members = array();
	protected $data    = array();

	/**
	 * @var tx_community_model_UserGateway
	 */
	protected $userGateway;

		// FIXME (most likely) does not need to have a reference to the message center here
	protected $messageCenterLoaded = false;

	/**
	 * FIXME rename to localizationManager
	 *
	 * @var tx_community_LocalizationManager
	 */
	protected $llManager;
	
	protected $htmlImage = 'no image';


	/**
	 * constructor for class tx_community_model_Group
	 */
	public function __construct($uid = null) {
		$this->uid = $uid;

		$this->userGateway = t3lib_div::makeInstance('tx_community_model_UserGateway');

		$llMangerClass = t3lib_div::makeInstanceClassName('tx_community_LocalizationManager');
		$this->llManager = call_user_func(array($llMangerClass, 'getInstance'), 'EXT:community/lang/locallang_group.xml',	$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_community.']);

			// FIXME must be done in the group gateway
		if (!is_null($this->uid)) {
			$pageSelect = t3lib_div::makeInstance('t3lib_pageSelect');

			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'*',
				'fe_groups',
				'uid = ' . $this->uid . $pageSelect->enableFields('fe_groups')
			);
			if ($GLOBALS['TYPO3_DB']->sql_num_rows($res) > 0) {
				$data = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
				$this->setDataToStore($data);
			}
		}

		if (t3lib_extMgm::isLoaded('community_messages')) {
			require_once(t3lib_extMgm::extPath('community_messages') . 'classes/class.tx_communitymessages_api.php');
			$this->messageCenterLoaded = true;
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
			$this->data['uid'] = $this->uid;
			return $this->uid;
		} else {
			// update
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
				'fe_groups',
				'uid = ' . $this->uid,
				$data
			);
			return $GLOBALS['TYPO3_DB']->sql_affected_rows();
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

	public function getImage() {
		if (strlen($this->data['tx_community_image'])) {
			return 'uploads/tx_community/' . $this->data['tx_community_image'];	
		} else {
			return '';
		}
		
	}

	public function getHtmlImage() {
		return $this->htmlImage;
	}
	
	public function setHtmlImage($htmlcode) {
		$this->htmlImage = $htmlcode;
	}
	
	public function getAdmin() {
		$admins = array();
		foreach ($this->data['tx_community_admins'] as $admin) {
			$admins[] = $admin->getNickname();
		}
		return implode(', ', $admins);
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
					$tmpData[$k] = $GLOBALS['TYPO3_DB']->quoteStr($v, 'fe_groups');
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
		$table = ($this->getTX_community_public()) ? 'fe_groups_tx_community_members_mm' : 'fe_groups_tx_community_tmpmembers_mm';
		$field = ($this->getTX_community_public()) ? 'tx_community_members' : 'tx_community_tmpmembers';
		if (!$this->isMember($user) && !$this->isTempMember($user)) {
			$GLOBALS['TYPO3_DB']->exec_INSERTquery(
				$table,
				array(
					'uid_local'		=> $this->uid,
					'uid_foreign'	=> $user->getUid()
				)
			);
			if ($GLOBALS['TYPO3_DB']->sql_affected_rows()) {
				$this->data[$field] = $this->data[$field] + 1;
			}
		}
		if ($this->save()) {
			if ($this->getTX_community_public()) {
				if (is_array($this->data['tx_community_admins'])) {
					foreach ($this->data['tx_community_admins'] as $uid => $admin) {
						$this->sendMessage(
							$admin,
							$this->prepareForMessage($this->llManager->getLL('subject_memberHasJoined'), $user, $admin),
							$this->prepareForMessage($this->llManager->getLL('body_memberHasJoined'), $user, $admin)
						);
					}
				}
			} else {
				if (is_array($this->data['tx_community_admins'])) {
					foreach ($this->data['tx_community_admins'] as $uid => $admin) {
						$this->sendMessage(
							$admin,
							$this->prepareForMessage($this->llManager->getLL('subject_confirmationNeeded'), $user, $admin),
							$this->prepareForMessage($this->llManager->getLL('body_confirmationNeeded'), $user, $admin)
						);
					}
				}
			}
			return true;
		}
		return false;
	}

	public function removeMember(tx_community_model_User $user) {
		if ($this->isMember($user)) {
			$res = $GLOBALS['TYPO3_DB']->exec_DELETEquery(
				'fe_groups_tx_community_members_mm',
				'uid_local = ' . $this->uid . ' AND uid_foreign = ' . $user->getUid()
			);
			if ($GLOBALS['TYPO3_DB']->sql_affected_rows()) {
				$this->data['tx_community_members'] = intval($this->data['tx_community_members']) - 1;
				foreach ($this->data['tx_community_admins'] as $uid => $admin) {
					$this->sendMessage(
						$admin,
						$this->prepareForMessage($this->llManager->getLL('subject_memberLeaveGroup'), $user, $admin),
						$this->prepareForMessage($this->llManager->getLL('body_memberLeaveGroup'), $user, $admin)
					);
				}
				return true;
			}
		}
		return false;
	}

	public function confirmMember(tx_community_model_User $user) {
		if ($this->isTempMember($user)) {
			$res = $GLOBALS['TYPO3_DB']->exec_DELETEquery(
				'fe_groups_tx_community_tmpmembers_mm',
				'uid_local = ' . $this->uid . ' AND uid_foreign = ' . $user->getUid()
			);
			if ($GLOBALS['TYPO3_DB']->sql_affected_rows()) {
				$this->data['tx_community_tmpmembers'] = intval($this->data['tx_community_tmpmembers']) - 1;
				$this->save();
				$GLOBALS['TYPO3_DB']->exec_INSERTquery(
					'fe_groups_tx_community_members_mm',
					array(
						'uid_local'		=> $this->uid,
						'uid_foreign'	=> $user->getUid()
					)
				);
				if ($GLOBALS['TYPO3_DB']->sql_affected_rows()) {
					$this->data['tx_community_members'] = $this->data['tx_community_members'] + 1;
					$this->sendMessage(
						$user,
						$this->prepareForMessage($this->llManager->getLL('subject_confirmMember'), $user),
						$this->prepareForMessage($this->llManager->getLL('body_confirmMember'), $user)
					);
					return true;
				}
			}
			return false;
		}
		return false;
	}

	public function rejectMember(tx_community_model_User $user) {
		if ($this->isTempMember($user)) {
			$res = $GLOBALS['TYPO3_DB']->exec_DELETEquery(
				'fe_groups_tx_community_tmpmembers_mm',
				'uid_local = ' . $this->uid . ' AND uid_foreign = ' . $user->getUid()
			);
			if ($GLOBALS['TYPO3_DB']->sql_affected_rows()) {
				$this->sendMessage(
					$user,
					$this->prepareForMessage($this->llManager->getLL('subject_rejectMember'), $user),
					$this->prepareForMessage($this->llManager->getLL('body_rejectMember'), $user)
				);
				return true;
			}
			return false;
		}
		return false;
	}

	public function getAllMembers() {
		$returnUser = array();

		$users = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'uid_foreign',
			'fe_groups_tx_community_members_mm',
			'uid_local = ' . $this->uid
		);
		foreach ($users as $user) {
			$tmpUser = $this->userGateway->findById($user['uid_foreign']);
			if (!is_null($tmpUser)) {
				$returnUser[] = $tmpUser;
			}
		}
		return $returnUser;
	}

	public function isMember(tx_community_model_User $user) {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'*',
			'fe_groups_tx_community_members_mm',
			'uid_local = ' . $this->uid . ' AND uid_foreign = ' . $user->getUid()
		);
		return ($GLOBALS['TYPO3_DB']->sql_num_rows($res) > 0);
	}


	public function getAllTempMembers() {
		$returnUser = array();

		$users = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'uid_foreign',
			'fe_groups_tx_community_tmpmembers_mm',
			'uid_local = ' . $this->uid
		);
		foreach ($users as $user) {
			$tmpUser = $this->userGateway->findById($user['uid_foreign']);
			if (!is_null($tmpUser)) {
				$returnUser[] = $tmpUser;
			}
		}
		return $returnUser;
	}

	public function isTempMember(tx_community_model_User $user) {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'*',
			'fe_groups_tx_community_tmpmembers_mm',
			'uid_local = ' . $this->uid . ' AND uid_foreign = ' . $user->getUid()
		);
		return ($GLOBALS['TYPO3_DB']->sql_num_rows($res) > 0);
	}
	
	public function delete() {
		$res = $GLOBALS['TYPO3_DB']->exec_DELETEquery(
			'fe_groups',
			'uid = ' . $this->uid
		);
		return ($GLOBALS['TYPO3_DB']->sql_num_rows($res) > 0);
	}

	protected function prepareForMessage($txt, $user, $admin = null) {
		$keys = array(
			'%USER.NICKNAME%'	=> $user->getNickname(),
			'%GROUP.TITLE%'		=> $this->getTitle(),
		);
		if (!is_null($admin)) {
			$keys['%ADMIN.NICKNAME%']	= $admin->getNickname();
		}
		return str_replace(array_keys($keys), array_values($keys), $txt);
	}

	protected function sendMessage(tx_community_model_User $toUser, $subject, $message) {
		if ($this->messageCenterLoaded) {
			tx_communitymessages_API::sendSystemMessage($subject, $message, array($toUser));
		}
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/model/class.tx_community_model_group.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/model/class.tx_community_model_group.php']);
}

?>