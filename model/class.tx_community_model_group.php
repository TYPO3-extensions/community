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
require_once($GLOBALS['PATH_community'] . 'classes/class.tx_community_localizationmanager.php');

/**
 * A community group
 *
 * @package TYPO3
 * @subpackage community
 */
class tx_community_model_Group implements tx_community_acl_AclResource {

	const TYPE_OPEN         = 0;
	const TYPE_MEMBERS_ONLY = 1;
	const TYPE_PRIVATE      = 2;
	const TYPE_SECRET       = 3;

	protected $uid        = null;
	protected $data       = array();
	private $originalData = array(); // must not be changed except when saving

	protected $admins         = array();
	protected $members        = array();
	protected $pendingMembers = array();

	protected $addedAdmins           = array();
	protected $removedAdmins         = array();
	protected $addedMembers          = array();
	protected $removedMembers        = array();
	protected $addedPendingMembers   = array();
	protected $removedPendingMembers = array();
	protected $invitedMembers        = array();

	/**
	 * Instance of the user gateway
	 *
	 * @var tx_community_model_UserGateway
	 */
	protected $userGateway;

		// TODO remove as soon as the messages are integrated into the community core
	protected $messageCenterLoaded = false;

	protected $messageQueue = array();

	/**
	 * Instance of the localization manager
	 *
	 * @var tx_community_LocalizationManager
	 */
	protected $localizationManager;

	/**
	 * constructor for class tx_community_model_Group
	 *
	 * @param	array	A tx_community_group database record as array
	 */
	public function __construct(array $data) {
		$this->originalData = $data;
		$this->data = $data;

		if (!empty($data['uid'])) {
			$this->uid = (int) $data['uid'];
		}

		$this->userGateway = t3lib_div::makeInstance('tx_community_model_UserGateway');

		$localizationManagerClass = t3lib_div::makeInstanceClassName('tx_community_LocalizationManager');
		$this->localizationManager = tx_community_LocalizationManager::getInstance(
			'EXT:community/lang/locallang_group.xml',
			$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_community.']
		);

		if (t3lib_extMgm::isLoaded('community_messages')) {
			require_once(t3lib_extMgm::extPath('community_messages') . 'classes/class.tx_communitymessages_api.php');
			$this->messageCenterLoaded = true;
		}
	}

	/**
	 * Method for dynamic handling of getter and setter methods
	 *
	 * @param	string	method name
	 * @param	array	arguments
	 * @return	void|mixed
	 * @author 	Frank Naegler <typo3@naegler.net>
	 */
	public function __call($methodName, $arguments) {
		$property = strtolower(substr($methodName, 3));

		if (substr($methodName, 0, 3) == 'set') {
			$this->data[$property] = $arguments[0]; // FIXME add sanitization
		} else if (substr($methodName, 0, 3) == 'get') {
			$value = null;

			if (array_key_exists($property, $this->data)) {
				$value = $this->data[$property];
			}

			return $value;
		}
	}

	/**
	 * saves (updates or creates) an usergroup
	 *
	 * @return	boolean|integer	returns boolean true if the group was successfully updated, false if the update failed. An integer is returned as a new group is created and is the group's new uid
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function save() {
		$result = null;

		$this->data['tstamp'] = $_SERVER['REQUEST_TIME'];

		if (is_null($this->uid)) {
				// new group, insert
			$this->data['crdate'] = $_SERVER['REQUEST_TIME'];

			$GLOBALS['TYPO3_DB']->exec_INSERTquery(
				'tx_community_group',
				$this->data
			);
			$this->data['uid'] = $this->uid = $GLOBALS['TYPO3_DB']->sql_insert_id();

				// "resetting"
			$this->originalData = $this->data;

				// works only after we have a uid
			$this->processAdmins();
			$this->processMembers(false);
			$this->processPendingMembers();
			$this->processInvitedMembers();
			$this->sendMessages();

			$changedFields = array_diff_assoc($this->data, $this->originalData);

				// an immediate update is needed to add the member count
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
				'tx_community_group',
				'uid = ' . $this->uid,
				$changedFields
			);

			$result = $this->uid;
		} else {
				// update
			$this->processAdmins();
			$this->processMembers();
			$this->processPendingMembers();
			$this->processInvitedMembers();
			$this->sendMessages();

			$changedFields = array_diff_assoc($this->data, $this->originalData);

			$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
				'tx_community_group',
				'uid = ' . $this->uid,
				$changedFields
			);

				// "resetting"
			$this->originalData = $this->data;

			$result = (boolean) $GLOBALS['TYPO3_DB']->sql_affected_rows();
		}

		return $result;
	}

	/**
	 * deletes the group and all admin, member, and pending member relations
	 *
	 * @return	boolean	true if everything is cleaned up, false otherwise
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function delete() {
		$groupDeleteResource = $GLOBALS['TYPO3_DB']->exec_DELETEquery(
			'tx_community_group',
			'uid = ' . $this->uid
		);
		$groupDeleted = ($GLOBALS['TYPO3_DB']->sql_affected_rows($groupDeleteResource) > 0);

		$groupAdminsDeleteResource = $GLOBALS['TYPO3_DB']->exec_DELETEquery(
			'tx_community_group_admins_mm',
			'uid_local = ' . $this->uid
		);
		$adminRelationsDeleted = ($GLOBALS['TYPO3_DB']->sql_affected_rows($groupAdminsDeleteResource) > 0);

		$groupMembersDeleteResource = $GLOBALS['TYPO3_DB']->exec_DELETEquery(
			'tx_community_group_members_mm',
			'uid_local = ' . $this->uid
		);
		$memberRelationsDeleted = ($GLOBALS['TYPO3_DB']->sql_affected_rows($groupMembersDeleteResource) >= 0);

		$groupPendingMembersDeleteResource = $GLOBALS['TYPO3_DB']->exec_DELETEquery(
			'tx_community_group_pendingmembers_mm',
			'uid_local = ' . $this->uid
		);
		$pendingMemberRelationsDeleted = ($GLOBALS['TYPO3_DB']->sql_affected_rows($groupPendingMembersDeleteResource) >= 0);

		$cleanedUp = (
			$groupDeleted &&
			$adminRelationsDeleted &&
			$memberRelationsDeleted &&
			$pendingMemberRelationsDeleted
		);

		return $cleanedUp;
	}

	/**
	 * sets the unique id for this group, makes sure that it is an integer
	 *
	 * @param	integer	unique id
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function setUid($uid) {
		$uid = (int) $uid;

		$this->data['uid'] = $uid;
		$this->uid = $uid;
	}

	/**
	 * gets the group's image and its path
	 *
	 * @return	string	the group's image and path
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function getImage() {
		if (!empty($this->data['image'])) {
			return 'uploads/tx_community/' . $this->data['image'];
		} else {
			return '';
		}
	}

	/**
	 * returns the Resource identifier
	 *
	 * @return string
	 * @author 	Frank Naegler <typo3@naegler.net>
	 */
	public function getResourceId() {
		return 'tx_community_group_' . $this->uid;
	}

	/**
	 * sets the group's creator and adds him as admin and member
	 *
	 * @param	tx_community_model_User	the group's creator
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function setCreator(tx_community_model_User $user) {
		$this->data['creator'] = $user->getUid();
		$this->addedAdmins[]   = $user;
		$this->addedMembers[]  = $user;
	}

	/**
	 * gets the groups creator as a tx_community_model_User object
	 *
	 * @return	tx_community_model_User	the group's creator
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function getCreator() {
		return $this->userGateway->findById($this->data['creator']);
	}

	/**
	 * adds an admin to the group, doesn't get affective until save() is called
	 *
	 * @param	tx_community_model_User	The user to add as an admin
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function addAdmin(tx_community_model_User $user) {
		if (!$this->isAdmin($user) && $this->isMember($user)) {
			$this->addedAdmins[] = $user;
		}
	}

	/**
	 * removes an admin from the group, doesn't get affective until save() is called
	 *
	 * @param tx_community_model_User $user
	 * @return	void|boolean	returns false if the user coldn't be removed from the list of admins
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function removeAdmin(tx_community_model_User $user) {
		$currentAdminCount = $this->getNumberOfAdmins();
		$remainingAdminCount = $currentAdminCount - count($this->removedAdmins);

			// make sure the user is admin of the group and that at least one
			// admin is left after removing the user as admin
		if ($this->isAdmin($user) && $remainingAdminCount > 1) {
			$this->removedAdmins[] = $user;
		} else {
				// TODO throw an exception instead
			return $false;
		}
	}

	/**
	 * finds the admins for the group
	 *
	 * @return	array	The admins of this group as an array of tx_community_model_User objects
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function getAdmins() {
		$adminUids = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'uid_foreign',
			'tx_community_group_admins_mm',
			'uid_local = ' . $this->uid
		);

		$adminUidList = array();
		foreach($adminUids as $adminUid) {
			$adminUidList[] = $adminUid['uid_foreign'];
		}
		$adminUidList = implode(',', $adminUidList);

		return $this->userGateway->findByIdList($adminUidList);
	}

	/**
	 * checks whether a user is admin of the group
	 *
	 * @param	tx_community_model_User	the user to check whether he is admin of this group
	 * @return	boolean	true if the user is admin of this group, false otherwise
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function isAdmin(tx_community_model_User $user) {
		$isAdmin = false;

		$isAdminRow = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'COUNT(uid_foreign) as isAdmin',
			'tx_community_group_admins_mm',
			'uid_local = ' . $this->uid . ' AND uid_foreign = ' . $user->getUid()
		);

		if (is_array($isAdminRow) && $isAdminRow[0]['isAdmin'] == 1) {
			$isAdmin = true;
		}

		return $isAdmin;
	}

	/**
	 * gets the current number of admins for the group
	 *
	 * @return	integer	current number of admins for this group
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function getNumberOfAdmins() {
		$adminCount = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'admins',
			'tx_community_group',
			'uid = ' . $this->uid
		);

		return (int) $adminCount[0]['admins'];
	}

	/**
	 * adds a member to the group, doesn't get affective until save() is called
	 *
	 * @param	tx_community_model_User	the user to add as a member for this group
	 * @param	boolean add user also to privat groups directly
	 * @todo	think about forceAddUser
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function addMember(tx_community_model_User $user,$forceAddUser = false) {
		$groupType = $this->getGroupType();

		if (!$this->isMember($user)) {
			switch ($groupType) {
				case self::TYPE_OPEN:
				case self::TYPE_MEMBERS_ONLY:
						// for open and members only groups the user joins immediately
					$this->addedMembers[] = $user;
					break;
				case self::TYPE_PRIVATE:
				case self::TYPE_SECRET:
						// for private and secret groups the user needs to be approved,
						// approvals are granted after request or by invitation for private groups
						// secret groups are invitation only
					if ($this->isPendingMember($user) && $this->hasUserBeenApproved($user) || $forceAddUser) {
						$this->addedMembers[] = $user;
						$this->removedPendingMembers[] = $user;
					}
					break;
			}
		}
	}

	/**
	 * removes a user as member, pending member, and/or admin from the group
	 *
	 * @param	tx_community_model_User	The user to remove as a member
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function removeMember(tx_community_model_User $user) {
		if ($this->isMember($user)) {
			$this->removedMembers[] = $user;
		}

		if ($this->isPendingMember($user)) {
			$this->removedPendingMembers[] = $user;
		}

		if ($this->isAdmin($user)) {
			$this->removedAdmins[] = $user;
		}
	}

	/**
	 * gets all members of the group. Use with care, groups may have 'many'
	 * members
	 *
	 * @return	array	array of tx_community_model_User objects
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function getMembers() {
		$memberUids = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'uid_foreign',
			'tx_community_group_members_mm',
			'uid_local = ' . $this->uid
		);

		$memberUidList = array();
		foreach($memberUids as $memberUid) {
			$memberUidList[] = $memberUid['uid_foreign'];
		}
		$memberUidList = implode(',', $memberUidList);

		return $this->userGateway->findByIdList($memberUidList);
	}

	/**
	 * checks whether the given user is a member of this group
	 *
	 * @param	tx_community_model_User	the user to check the membership for
	 * @return	boolean	true if the user is a member of this group, false otherwise
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function isMember(tx_community_model_User $user) {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'*',
			'tx_community_group_members_mm',
			'uid_local = ' . $this->uid . ' AND uid_foreign = ' . $user->getUid()
		);

		return ($GLOBALS['TYPO3_DB']->sql_num_rows($res) > 0);
	}

	/**
	 * gets the current number of members for this group
	 *
	 * @return	integer	current number of members for this group
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function getNumberOfMembers() {
		$adminCount = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'members',
			'tx_community_group',
			'uid = ' . $this->uid
		);

		return (int) $adminCount[0]['members'];
	}

	/**
	 * gets all pending members of this group
	 *
	 * @return	array	an array of tx_community_model_User objects
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function getPendingMembers() {
		$userUids = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'uid_foreign',
			'tx_community_group_pendingmembers_mm',
			'uid_local = ' . $this->uid
		);

		$pendingMemberUidList = array();
		foreach($userUids as $userUid) {
			$pendingMemberUidList[] = $userUid['uid_foreign'];
		}
		$pendingMemberUidList = implode(',', $pendingMemberUidList);

		return $this->userGateway->findByIdList($pendingMemberUidList);
	}

	/**
	 * checks whether the given user is a pending member of this group
	 *
	 * @param	tx_community_model_User	the user to check the membership for
	 * @return	boolean	true if the user is a pending member of this group, false otherwise
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function isPendingMember(tx_community_model_User $user) {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'*',
			'tx_community_group_pendingmembers_mm',
			'uid_local = ' . $this->uid . ' AND uid_foreign = ' . $user->getUid()
		);
		return ($GLOBALS['TYPO3_DB']->sql_num_rows($res) > 0);
	}

	/**
	 * gets the current number of pending members for this group
	 *
	 * @return	integer	current number of pending members for this group
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function getNumberOfPendingMembers() {
		$adminCount = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'pendingmembers',
			'tx_community_group',
			'uid = ' . $this->uid
		);

		return (int) $adminCount[0]['pendingmembers'];
	}

	/**
	 * sends an invitation to join this group to a user. The user at the same
	 * time gets approved and added to the pending members list. You need to
	 * call save() afterwards to make the approval and addition to the pending
	 * members list affective.
	 *
	 * @param	tx_community_model_User	The user to invite to this group
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function sendInvitationToUser(tx_community_model_User $user) {
			// TODO send an invitation message, do not send the message
			// immidiately, only send when calling save


		$this->invitedMembers[] = $user;
	}

	/**
	 * approves a membership request for this group for a pending member, also
	 * sends a message to the user that his membership request has been approved
	 *
	 * @param	tx_community_model_User	The user to approve his membership request for
	 * @param	boolean	Switch to turn of sending of a approval message to the user, sending of messages is on by default
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function approveMembershipRequestForUser(tx_community_model_User $user, $sendApprovalMessage = true) {
		if ($this->isPendingMember($user)) {
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
				'tx_community_group_pendingmembers_mm',
				'uid_local = ' . $this->uid . ' AND uid_foreign = ' . $user->getUid(),
				array('isapproved' => 1)
			);

			if ($sendApprovalMessage) {
				// TODO send a message (by adding a meesage to the message queue)
			}
		}
	}

	/**
	 * declines a user's membership request for this group by simply removing
	 * him from the list of pending members
	 *
	 * @param	tx_community_model_User	The user to decline his membership request
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function declineMembershipRequestForUser(tx_community_model_User $user) {
		$this->removedPendingMembers[] = $user;

			// TODO send a message (by adding a meesage to the message queue
	}

	/**
	 * checks whether a user's membership request for this group has been approved
	 *
	 * @param	tx_community_model_User	The user to check his approval status for
	 * @return	boolean	return true if the user's membership request has been approved, false if it hasn't or the user wasn't found to be a pending member
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function hasUserBeenApproved(tx_community_model_User $user) {
		$memberIsApproved = false;

		$memberRelation = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'isapproved',
			'tx_community_group_pendingmembers_mm',
			'uid_local = ' . $this->uid . ' AND uid_foreign = ' . $user->getUid()
		);

		if (is_array($memberRelation) && $memberRelation[0]['isapproved'] == 1) {
			$memberIsApproved = true;
		}

		return $memberIsApproved;
	}



	public function confirmMember(tx_community_model_User $user) {
		if ($this->isPendingMember($user)) {
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
						$this->prepareForMessage($this->localizationManager->getLL('subject_confirmMember'), $user),
						$this->prepareForMessage($this->localizationManager->getLL('body_confirmMember'), $user)
					);
					return true;
				}
			}
			return false;
		}
		return false;
	}

	public function rejectMember(tx_community_model_User $user) {
		if ($this->isPendingMember($user)) {
			$res = $GLOBALS['TYPO3_DB']->exec_DELETEquery(
				'fe_groups_tx_community_tmpmembers_mm',
				'uid_local = ' . $this->uid . ' AND uid_foreign = ' . $user->getUid()
			);
			if ($GLOBALS['TYPO3_DB']->sql_affected_rows()) {
				$this->sendMessage(
					$user,
					$this->prepareForMessage($this->localizationManager->getLL('subject_rejectMember'), $user),
					$this->prepareForMessage($this->localizationManager->getLL('body_rejectMember'), $user)
				);
				return true;
			}
			return false;
		}
		return false;
	}

	/**
	 * processes admins: actually adds and removes admins from the group
	 *
	 * @return	void
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	protected function processAdmins() {
		foreach ($this->addedAdmins as $addedAdmin) {
			$GLOBALS['TYPO3_DB']->exec_INSERTquery(
				'tx_community_group_admins_mm',
				array(
					'uid_local'   => $this->uid,
					'uid_foreign' => $addedAdmin->getUid()
				)
			);
			$this->data['admins']++;
		}

		$removedAdminList = array();
		foreach ($this->removedAdmins as $removedAdmin) {
			$removedAdminList[] = $removedAdmin->getUid();
			$this->data['admins']--;
		}

		if (!empty($removedAdminList)) {
			$GLOBALS['TYPO3_DB']->exec_DELETEquery(
				'tx_community_group_admins_mm',
				'uid_local = ' . $this->uid . ' AND uid_foreign IN (' . implode(',', $removedAdminList) . ')'
			);
		}
	}

	/**
	 * processes members: actually adds and removes members from the group
	 *
	 * @return	void
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	protected function processMembers($sendMail = true) {
		foreach ($this->addedMembers as $addedMember) {
			$GLOBALS['TYPO3_DB']->exec_INSERTquery(
				'tx_community_group_members_mm',
				array(
					'uid_local'   => $this->uid,
					'uid_foreign' => $addedMember->getUid()
				)
			);
			$this->data['members']++;

				// TODO add the messages to the message queue instead of actually sending them here
			if($sendMail){		
				$this->sendMessageToAdmins(
	    				$this->localizationManager->getLL('subject_memberHasJoined'),
					$this->localizationManager->getLL('body_memberHasJoined'),
					$addedMember
				);
			}
		}

		$removedMemberList = array();
		foreach ($this->removedMembers as $removedMember) {
			$removedMemberList[] = $removedMember->getUid();
			$this->data['members']--;
		}

		if (!empty($removedMemberList)) {
			$GLOBALS['TYPO3_DB']->exec_DELETEquery(
				'tx_community_group_members_mm',
				'uid_local = ' . $this->uid . ' AND uid_foreign IN (' . implode(',', $removedMemberList) . ')'
			);
		}
	}

	/**
	 * processes pending members: actually adds and removes pending members
	 * from the group
	 *
	 * @return	void
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	protected function processPendingMembers() {
		foreach ($this->addedPendingMembers as $addedPendingMember) {
			$GLOBALS['TYPO3_DB']->exec_INSERTquery(
				'tx_community_group_pendingmembers_mm',
				array(
					'uid_local'   => $this->uid,
					'uid_foreign' => $addedPendingMember->getUid()
				)
			);
			$this->data['pendingmembers']++;

				// TODO add the messages to the message queue instead of actually sending them here
			$this->sendMessageToAdmins(
				$this->localizationManager->getLL('subject_confirmationNeeded'),
				$this->localizationManager->getLL('body_confirmationNeeded'),
				$addedMember
			);
		}

		$removedPendingMemberList = array();
		foreach ($this->removedPendingMembers as $removedPendingMember) {
			$removedPendingMemberList[] = $removedPendingMember->getUid();
			$this->data['pendingmembers']--;
		}

		if (!empty($removedPendingMemberList)) {
			$GLOBALS['TYPO3_DB']->exec_DELETEquery(
				'tx_community_group_pendingmembers_mm',
				'uid_local = ' . $this->uid . ' AND uid_foreign IN (' . implode(',', $removedPendingMemberList) . ')'
			);
		}
	}

	/**
	 * processes invited members: actually adds the invited members as pending
	 * members and sets their approval status to approved
	 *
	 * @return	void
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	protected function processInvitedMembers() {
		foreach ($this->invitedMembers as $invitedMember) {
			$GLOBALS['TYPO3_DB']->exec_INSERTquery(
				'tx_community_group_pendingmembers_mm',
				array(
					'uid_local'   => $this->uid,
					'uid_foreign' => $invitedMember->getUid(),
					'isapproved'  => 1
				)
			);
			$this->data['pendingmembers']++;
		}
	}

	/**
	 * sends a message to all admins
	 *
	 * @param	string	The message's subject
	 * @param	string	The message's body text
	 * @param	tx_community_model_User	$user ?
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	protected function sendMessageToAdmins($subject, $message, tx_community_model_User $user) {
		$admins = $this->getAdmins();

		foreach ($admins as $admin) {
			$this->sendMessage(
				$admin,
				$this->prepareMessage($subject, $user, $admin),
				$this->prepareMessage($message, $user, $admin)
			);
		}
	}

	/**
	 * iterates through the message queue and sends the messages
	 *
	 * @return	void
	 */
	protected function sendMessages() {
		// TODO process the message queue and actually send the messages
	}



		// TODO the methods below still need some polishing as soon as the messaging feature is integrated into the community core

	protected function prepareMessage($message, tx_community_model_User $user, $admin = null) {
		$keys = array(
			'%USER.NICKNAME%'	=> $user->getNickname(),
			'%GROUP.TITLE%'		=> $this->getName(),
		);
		if (!is_null($admin)) {
			$keys['%ADMIN.NICKNAME%']	= $admin->getNickname();
		}
		return str_replace(array_keys($keys), array_values($keys), $message);
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
