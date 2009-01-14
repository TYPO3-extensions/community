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

require_once($GLOBALS['PATH_community'] . 'view/userprofile/class.tx_community_view_userprofile_profileactions.php');
require_once($GLOBALS['PATH_community'] . 'view/userprofile/class.tx_community_view_userprofile_editrelationship.php');

/**
 * A widget class to add actions to a user profile like "add as friend", "view friends", "send message", ...
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_controller_userprofile_ProfileActionsWidget extends tx_community_controller_AbstractCommunityApplicationWidget {

	public function __construct() {
		parent::__construct();
		$this->localizationManager = tx_community_LocalizationManager::getInstance('EXT:community/lang/locallang_userprofile_profileactions.xml', $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_community.']);

		$this->name     	= 'profileActions';
		$this->label    	= $this->localizationManager->getLL('label_ProfileActionWidget');
		$this->draggable	= false;
		$this->removable	= false;
		$this->cssClass		= '';
	}

	/**
	 * the controller's default action
	 *
	 * @return	string
	 */
	public function indexAction() {
		$view = t3lib_div::makeInstance('tx_community_view_userprofile_ProfileActions');
		$view->setTemplateFile($this->configuration['applications.']['userProfile.']['widgets.']['profileActions.']['templateFile']);
		$view->setLanguageKey($this->communityApplication->LLkey);

		$view->setProfileActionsModel($this->getProfileActions());

		return $view->render();
	}

	/**
	 * Displays a form to edit the relationship of the requested user to the
	 * requesting user. The requesting user can add the requested user to
	 * different roles.
	 *
	 * @return	string
	 */
	public function editRelationshipAction() {
		$view = t3lib_div::makeInstance('tx_community_view_userprofile_EditRelationship');
		$view->setTemplateFile($this->configuration['applications.']['userProfile.']['widgets.']['profileActions.']['templateFile']);
		$view->setLanguageKey($this->communityApplication->LLkey);

		$view->setFriendUser($this->communityApplication->getRequestedUser());
		$view->setRelationshipOptions($this->getRelationshipOptions());

		$formAction = $this->communityApplication->pi_getPageLink(
			$GLOBALS['TSFE']->id,
			'',
			array(
				'tx_community' => array(
					'user' => $this->communityApplication->getRequestedUser()->getUid(),
					$this->name . 'Action' => 'setRelationships'
				)
			)
		);
		$view->setFormAction($formAction);

		return $view->render();
	}

	protected function getRelationshipOptions() {
		$relationshipOptions   = array();
		$availableFriendRoles  = $this->getPublicFriendRoles();
		$existingRelationships = $this->getRelationshipsToRequestedUser();

		foreach ($availableFriendRoles as $roleId => $role) {
			$checked = '';
			if (in_array($roleId, $existingRelationships)) {
				$checked = 'checked="checked"';
			}

			$relationshipOptions[] = array(
				'field_name' => 'tx_community[relationship][' . $roleId . ']',
				'field_id' => 'tx_community_relationship_' . $roleId,
				'field_checked' => $checked,
				'label' => $role['name']
			);
		}

		return $relationshipOptions;
	}

	public function setRelationshipsAction() {
		$communityRequest = t3lib_div::GParrayMerged('tx_community');
		$existingRelationships = $this->getRelationshipsToRequestedUser();
		$setRelationships = array();
		foreach ($communityRequest['relationship'] as $roleId => $activated) {
			if ($activated) {
				$setRelationships[] = $roleId;
			}
		}
		$newRelationshipsToAdd = array_diff($setRelationships, $existingRelationships);
		$oldRelationshipsToRemove = array_diff($existingRelationships, $setRelationships);

		foreach ($newRelationshipsToAdd as $roleIdToAdd) {
			$this->addRelationship($roleIdToAdd);
		}

		foreach ($oldRelationshipsToRemove as $roleIdRemove) {
			$this->removeRelationship($roleIdRemove);
		}

		$profilePageUrl = $this->communityApplication->pi_getPageLink(
			$this->configuration['pages.']['userProfile'],
			'',
			array(
				'tx_community' => array(
					'user' => $this->communityApplication->getRequestedUser()->getUid()
				)
			)
		);

		Header('HTTP/1.1 303 See Other');
		Header('Location: ' . t3lib_div::locationHeaderUrl($profilePageUrl));
		exit;
	}

	/**
	 * adds a relationship between the requested user and the requesting user
	 * with the given role
	 *
	 * @param	integer	$roleId
	 */
	protected function addRelationship($roleId) {
		$success = false;
		$communityRequest = t3lib_div::GParrayMerged('tx_community');

		$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery(
			'tx_community_friend',
			array(
				'pid'    => $this->configuration['pages.']['aclStorage'],
				'tstamp' => $_SERVER['REQUEST_TIME'],
				'crdate' => $_SERVER['REQUEST_TIME'],
				'feuser' => $this->communityApplication->getRequestingUser()->getUid(),
				'friend' => $this->communityApplication->getRequestedUser()->getUid(),
				'role'   => $roleId
			)
		);

		if ($GLOBALS['TYPO3_DB']->sql_affected_rows($res)) {
			$success = true;
		}

		// @TODO: if $success send message to user with a hint to confirm the request
		//        if it is a confirmation, send a message to the first requesting user
		//        use community_messages, here is an example:
		/*
			$isLoaded = (t3lib_extMgm::isLoaded('community_messages')) ? 'true' : 'false';
			if ($isLoaded === 'true') {
				$userGateway = new tx_community_model_UserGateway();
				$user = $userGateway->findCurrentlyLoggedInUser();
				if ($user !== null) {
					$subject = $this->localizationManager->getLL('systemmessage_subject');
					$bodytext = $this->localizationManager->getLL('systemmessage_bodytext');
					$recipients = array($user);
					tx_communitymessages_API::sendSystemMessage($subject, $bodytext, $recipients);
				}
			}
		*/
		return $success;
	}

	protected function removeRelationship($roleId) {
		$GLOBALS['TYPO3_DB']->exec_DELETEquery(
			'tx_community_friend',
			'feuser = ' . $this->communityApplication->getRequestingUser()->getUid()
				. ' AND friend = ' . $this->communityApplication->getRequestedUser()->getUid()
				. ' AND role = ' . $roleId
		);

		// @TODO: if $success send message to user with a hint that the relationship was canceled
		//        use community_messages, here is an example:
		/*
			$isLoaded = (t3lib_extMgm::isLoaded('community_messages')) ? 'true' : 'false';
			if ($isLoaded === 'true') {
				$userGateway = new tx_community_model_UserGateway();
				$user = $userGateway->findCurrentlyLoggedInUser();
				if ($user !== null) {
					$subject = $this->localizationManager->getLL('systemmessage_subject');
					$bodytext = $this->localizationManager->getLL('systemmessage_bodytext');
					$recipients = array($user);
					tx_communitymessages_API::sendSystemMessage($subject, $bodytext, $recipients);
				}
			}
		*/

		// TODO check for errors, throw exceptions, add pid to where clause
	}

	public function addAsFriendAction() {
		$communityRequest = t3lib_div::GParrayMerged('tx_community');

		$roleId	= $this->configuration['accessManagement.']['addAsFriendDefaultRoleId'];

		if ($requestRoleId = intval($communityRequest['roleId'])) {
			$roleId = $requestRoleId;
		}

		$friendAdded = $this->addRelationship($roleId);

		if ($friendAdded) {
				// do a redirect to the profile page, no output

			$profilePageUrl = $this->communityApplication->pi_getPageLink(
				$this->configuration['pages.']['userProfile'],
				'',
				array(
					'tx_community' => array(
						'user' => $this->communityApplication->getRequestedUser()->getUid()
					)
				)
			);

				// TODO user t3lib_div::redirect when TYPO3 4.3 is released
			Header('HTTP/1.1 303 See Other');
			Header('Location: ' . t3lib_div::locationHeaderUrl($profilePageUrl));
			exit;
		} else {
			// TODO throw some exception
		}
	}

	/**
	 * removes all friend connections from the requesting user to the requested
	 * user. However, existing connections from the other direction are not
	 * affected.
	 *
	 */
	public function removeAsFriendAction() {
		$res = $GLOBALS['TYPO3_DB']->exec_DELETEquery(
			'tx_community_friend',
			'pid = ' . $this->configuration['pages.']['aclStorage']
				. ' AND ('
					. ' (feuser = ' . $this->communityApplication->getRequestingUser()->getUid() . ' AND friend = ' . $this->communityApplication->getRequestedUser()->getUid() . ')'
					. ' OR '
					. ' (friend = ' . $this->communityApplication->getRequestingUser()->getUid() . ' AND feuser = ' . $this->communityApplication->getRequestedUser()->getUid() . ')'
				. ')'
		);

		if ($GLOBALS['TYPO3_DB']->sql_affected_rows($res)) {
				// do a redirect to the profile page, no output

			$profilePageUrl = $this->communityApplication->pi_getPageLink(
				$this->configuration['pages.']['userProfile'],
				'',
				array(
					'tx_community' => array(
						'user' => $this->communityApplication->getRequestedUser()->getUid()
					)
				)
			);
				// TODO user t3lib_div::redirect when TYPO3 4.3 is released
			Header('HTTP/1.1 303 See Other');
			Header('Location: ' . t3lib_div::locationHeaderUrl($profilePageUrl));
			exit;
		} else {
			// TODO throw some exception
		}
	}

	protected function getProfileActions() {
			// TODO make this function extensible at some point
		$profileActions = array();

		$requestedUser  = $this->communityApplication->getRequestedUser();
		$requestingUser = $this->communityApplication->getRequestingUser();

		$accessManagerClass = t3lib_div::makeInstanceClassName('tx_community_AccessManager');
		$accessManager      = call_user_func(array($accessManagerClass, 'getInstance'));

		if ($accessManager->isLoggedIn()) {
			$profileActions[] = array('link' => $this->getAddAsFriendProfileAction(), 'class' => 'addAsFriend');
		}

		if ($this->isFriend($requestingUser, $requestedUser)) {
			$profileActions[] = array('link' => $this->getEditRelationshipProfileAction(), 'class' => 'editRelationship');
			$profileActions[] = array('link' => $this->getRemoveAsFriendProfileAction(), 'class' => 'removeAsFriend');
		}

			// hook
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tx_community']['getUserProfileActions'])) {
			foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tx_community']['getUserProfileActions'] as $classReference) {
				$hookObject = & t3lib_div::getUserObj($classReference);
				if ($hookObject instanceof tx_community_UserProfileActionsProvider) {
					$profileActions = $hookObject->getUserProfileActions($profileActions, $this);
				}

			}
		}

		return $profileActions;
	}

	protected function getAddAsFriendProfileAction() {
		$content = '';

		$requestedUser  = $this->communityApplication->getRequestedUser();
		$requestingUser = $this->communityApplication->getRequestingUser();

		if ($requestedUser == $requestingUser) {
				// viewing the own profile
			$content = $this->localizationManager->getLL('action_thisIsYou');
		} else if ($this->isFriend($requestingUser, $requestedUser)) {
				// the users are already friends
			$content = sprintf(
				$this->localizationManager->getLL('action_isFriendWith'),
				$requestedUser->getNickname()
			);
		} else if($this->isFriendshipRequestSend($requestingUser, $requestedUser)) {
				// a friendship request has been sent already, but is not approved yet
			$content = sprintf(
				$this->localizationManager->getLL('action_friendshipRequestSent'),
				$requestedUser->getNickname()
			);
		} else {
				// the users are not friends yet, create a link
			$linkText = sprintf(
				$this->localizationManager->getLL('action_addAsFriend'),
				$requestedUser->getNickname()
			);
			$content = $this->communityApplication->pi_linkToPage(
				$linkText,
				$this->configuration['pages.']['userProfile'],
				'',
				array(
					'tx_community' => array(
						'user' => $requestedUser->getUid(),
						$this->name . 'Action' => 'addAsFriend'
					)
				)
			);
		}

		return $content;
	}

	protected function getRemoveAsFriendProfileAction() {
		$content = '';

		$requestedUser = $this->communityApplication->getRequestedUser();

		$linkText = sprintf(
			$this->localizationManager->getLL('action_removeAsFriend'),
			$requestedUser->getAccount()->getFirstName()
		);

		$content = $this->communityApplication->pi_linkToPage(
			$linkText,
			$this->configuration['pages.']['userProfile'],
			'',
			array(
				'tx_community' => array(
					'user' => $requestedUser->getUid(),
					$this->name . 'Action' => 'removeAsFriend'
				)
			)
		);

		return $content;
	}

	protected function getEditRelationshipProfileAction() {
		$content = '';

		$requestedUser = $this->communityApplication->getRequestedUser();
		$linkText = $this->localizationManager->getLL('action_editRelationship');

		$content = $this->communityApplication->pi_linkToPage(
			$linkText,
			$this->configuration['pages.']['relationshipEdit'],
			'',
			array(
				'tx_community' => array(
					'user' => $requestedUser->getUid(),
					$this->name . 'Action' => 'editRelationship'
				)
			)
		);

		return $content;
	}

	/**
	 * checks whether the requesting user is a friend of the requested user.
	 * This is done by checking whether a record in tx_community_friend exists
	 * as a relation between both.
	 *
	 * @param tx_community_model_User the requesting user
	 * @param tx_community_model_User the requested user, who needs to be checked whether he is a friend
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	protected function isFriend(tx_community_model_User $user, tx_community_model_User $friend) {
			// TODO this schould at some point be moved to a more appropriate place like a FriendshipManager or so
			// TODO: Question: I think this should be a method of the user object: isInRelationTo($user, $role) ?
		$isFriend = false;

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'f1.uid',
			'tx_community_friend AS f1 JOIN tx_community_friend AS f2
				ON f1.feuser = f2.friend AND f1.friend = f2.feuser
				AND f1.feuser = ' . $user->getUid()
			. ' AND f1.friend = ' . $friend->getUid(),
			''
		);

		$friendConnectionCount = $GLOBALS['TYPO3_DB']->sql_num_rows($res);
		$GLOBALS['TYPO3_DB']->sql_free_result($res);

		if ($friendConnectionCount > 0) {
			$isFriend = true;
		}

		return $isFriend;
	}

	/**
	 * checks whether a one-way relation between two users is present. This
	 * happens when a user sent a friendship request, but that request has
	 * not been confirmed by the to be friend yet
	 *
	 * @param tx_community_model_User the requesting user
	 * @param tx_community_model_User the requested user, who needs to be checked whether there's a friend request already
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	protected function isFriendshipRequestSend(tx_community_model_User $user, tx_community_model_User $friend) {
			// TODO maybe we can merge these two queries into one

			// TODO use page id restrctions, do not hardcode enablefields
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid',
			'tx_community_friend',
			'feuser = ' . $user->getUid()
				. ' AND friend = ' . $friend->getUid()
				. ' AND hidden = 0'
		);
		$userToFriendRelationExists = (boolean) $GLOBALS['TYPO3_DB']->sql_num_rows($res);
		$GLOBALS['TYPO3_DB']->sql_free_result($res);

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid',
			'tx_community_friend',
			'feuser = ' . $friend->getUid()
				. ' AND friend = ' . $user->getUid()
				. ' AND hidden = 0'
		);
		$friendToUserRelationExists = (boolean) $GLOBALS['TYPO3_DB']->sql_num_rows($res);
		$GLOBALS['TYPO3_DB']->sql_free_result($res);

		return ($userToFriendRelationExists && !$friendToUserRelationExists);
	}

	/**
	 * gets the roles a user can add his friends to (like categorization of
	 * friends)
	 *
	 * @return	array
	 */
	protected function getPublicFriendRoles() {

			// FIXME enablefields must not be hardcoded
			// TODO add a page id restriction
		$publicFriendRoles = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'uid, name',
			'tx_community_acl_role',
			'hidden = 0'
				. ' AND deleted = 0'
				. ' AND is_public = 1'
				. ' AND is_friend_role = 1',
			'',
			'',
			'',
			'uid'
		);

		return $publicFriendRoles;
	}

	protected function getRelationshipsToRequestedUser() {
		$relationships = array();

		$requestedUser  = $this->communityApplication->getRequestedUser();
		$requestingUser = $this->communityApplication->getRequestingUser();

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'DISTINCT role',
			'tx_community_friend',
			'feuser = ' . $requestingUser->getUid()
				. ' AND friend = ' . $requestedUser->getUid()
		);

		while ($friendRelationshipRow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$relationships[] = (int) $friendRelationshipRow['role'];
		}

		return $relationships;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/userprofile/class.tx_community_controller_userprofile_profileactionswidget.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/userprofile/class.tx_community_controller_userprofile_profileactionswidget.php']);
}

?>
