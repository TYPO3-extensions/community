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

require_once($GLOBALS['PATH_community'] . 'classes/class.tx_community_applicationmanager.php');
require_once($GLOBALS['PATH_community'] . 'classes/class.tx_community_localizationmanager.php');
require_once($GLOBALS['PATH_community'] . 'interfaces/interface.tx_community_communityapplicationwidget.php');
require_once($GLOBALS['PATH_community'] . 'interfaces/interface.tx_community_command.php');
require_once($GLOBALS['PATH_community'] . 'view/userprofile/class.tx_community_view_userprofile_profileactions.php');
require_once($GLOBALS['PATH_community'] . 'view/userprofile/class.tx_community_view_userprofile_editrelationship.php');

/**
 * A widget class to add actions to a user profile like "add as friend", "view friends", "send message", ...
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_controller_userprofile_ProfileActionsWidget implements tx_community_CommunityApplicationWidget, tx_community_Command {

	/**
	 * a reference to the parent community application this widget belongs to
	 *
	 * @var tx_community_controller_AbstractCommunityApplication
	 */
	protected $communityApplication;
	protected $configuration;
	protected $data;

	public function initialize($data, $configuration) {
		$this->data = $data;
		$this->configuration = $configuration;
	}

	public function setCommunityApplication(tx_community_controller_AbstractCommunityApplication $communityApplication) {
		$this->communityApplication = $communityApplication;
	}

	/**
	 * returns whether a user is allowed to drag the widget to a different
	 * container or position
	 *
	 * @return	boolean	true if dragging is allowed, false otherwise
	 */
	public function isDragable() {
		return false;
	}

	/**
	 * returns whether the widget can be removed from being displayed
	 *
	 * @return	boolean	true id removing is allowed, false otherwise
	 */
	public function isRemovable() {
		return false;
	}

	/**
	 * return the current layout container the widget is located in
	 *
	 * @return	string
	 */
	public function getLayoutContainer() {
		return 0;
	}

	/**
	 * returns the widget's Id, this is the ID which is used while the widget
	 * gets registerd in ext_localconf.php
	 *
	 * @return	string	the widget's Id
	 */
	public function getId() {
		return 'profileActions';
	}

	/**
	 * gets the position of the widget within its container
	 *
	 * @return	integer	the position within a container
	 */
	public function getPosition() {
		return 2;
	}

	/**
	 * returns the widget's label
	 *
	 * @return	string	the widget's content (HTML, XML, JSON, ...)
	 */
	public function getLabel() {
		return 'ProfileActionWidget';
	}

	/**
	 * returns the widget's CSS class(es)
	 *
	 * @return	string	the widget's CSS class
	 */
	public function getWidgetClass() {
		return '';
	}

	/**
	 * central excution method of this widget, acts as a dispatcher for the
	 * different actions
	 *
	 * @return	string	the result of the called action, usually some form of output/rendered HTML
	 */
	public function execute() {
		$content = '';
		$communityRequest = t3lib_div::_GP('tx_community');

		$applicationManagerClass = t3lib_div::makeInstanceClassName('tx_community_ApplicationManager');
		$applicationManager      = call_user_func(array($applicationManagerClass, 'getInstance'));
		/* @var $applicationManager tx_community_ApplicationManager */

		$widgetConfiguration = $applicationManager->getWidgetConfiguration(
			$this->communityApplication->getName(),
			$this->getId()
		);

			// dispatch
		if (!empty($communityRequest['profileAction'])
			&& method_exists($this, $communityRequest['profileAction'] . 'Action')
			&& in_array($communityRequest['profileAction'], $widgetConfiguration['actions'])
		) {
				// call a specifically requested action
			$actionName = $communityRequest['profileAction'] . 'Action';
			$content = $this->$actionName();
		} else {
				// call the default action
			$defaultActionName = $widgetConfiguration['defaultAction'] . 'Action';
			$content = $this->$defaultActionName();
		}

		return $content;
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
					'profileAction' => 'setRelationships'
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

	}

	public function addAsFriendAction() {
		$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery(
			'tx_community_friend',
			array(
				'pid'    => $this->configuration['pages.']['aclStorage'],
				'tstamp' => $_SERVER['REQUEST_TIME'],
				'crdate' => $_SERVER['REQUEST_TIME'],
				'feuser' => $this->communityApplication->getRequestingUser()->getUid(),
				'friend' => $this->communityApplication->getRequestedUser()->getUid(),
				'role'   => $this->configuration['applications.']['userProfile.']['widgets.']['profileActions.']['addAsFriendDefaultRoleId']
			)
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
				. ' AND feuser = ' . $this->communityApplication->getRequestingUser()->getUid()
				. ' AND friend = ' . $this->communityApplication->getRequestedUser()->getUid()
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

		$profileActions[]['link'] = $this->getAddAsFriendProfileAction();

		if ($this->isFriend($requestingUser, $requestedUser)) {
			$profileActions[]['link'] = $this->getEditRelationshipProfileAction();
			$profileActions[]['link'] = $this->getRemoveAsFriendProfileAction();
		}

		return $profileActions;
	}

	protected function getAddAsFriendProfileAction() {
		$content = '';

		$localizationManagerClass = t3lib_div::makeInstanceClassName('tx_community_LocalizationManager');
		$localizationManager      = call_user_func(
			array($localizationManagerClass, 'getInstance'),
			$GLOBALS['PATH_community'] . 'lang/locallang_userprofile_profileactions.xml',
			array()
		);

		$requestedUser  = $this->communityApplication->getRequestedUser();
		$requestingUser = $this->communityApplication->getRequestingUser();

		if ($requestedUser == $requestingUser) {
				// viewing the own profile
			$content = $localizationManager->getLL('action_thisIsYou');
		} else if ($this->isFriend($requestingUser, $requestedUser)) {
				// the users are already friends
			$content = sprintf(
				$localizationManager->getLL('action_isFriendWith'),
				$requestedUser->getAccount()->getFirstName()
			);
		} else {
				// the users are not friends yet, create a link
			$linkText = sprintf(
				$localizationManager->getLL('action_addAsFriend'),
				$requestedUser->getAccount()->getFirstName()
			);

			$content = $this->communityApplication->pi_linkTP(
				$linkText,
				array(
					'tx_community' => array(
						'user' => $requestedUser->getUid(),
						'profileAction' => 'addAsFriend'
					)
				)
			);
		}

		return $content;
	}

	protected function getRemoveAsFriendProfileAction() {
		$content = '';

		$localizationManagerClass = t3lib_div::makeInstanceClassName('tx_community_LocalizationManager');
		$localizationManager      = call_user_func(
			array($localizationManagerClass, 'getInstance'),
			$GLOBALS['PATH_community'] . 'lang/locallang_userprofile_profileactions.xml',
			array()
		);

		$requestedUser = $this->communityApplication->getRequestedUser();

		$linkText = sprintf(
			$localizationManager->getLL('action_removeAsFriend'),
			$requestedUser->getAccount()->getFirstName()
		);

		$content = $this->communityApplication->pi_linkTP(
			$linkText,
			array(
				'tx_community' => array(
					'user' => $requestedUser->getUid(),
					'profileAction' => 'removeAsFriend'
				)
			)
		);

		return $content;
	}

	protected function getEditRelationshipProfileAction() {
		$content = '';

		$localizationManagerClass = t3lib_div::makeInstanceClassName('tx_community_LocalizationManager');
		$localizationManager      = call_user_func(
			array($localizationManagerClass, 'getInstance'),
			$GLOBALS['PATH_community'] . 'lang/locallang_userprofile_profileactions.xml',
			array()
		);

		$requestedUser = $this->communityApplication->getRequestedUser();
		$linkText = $localizationManager->getLL('action_editRelationship');

		$content = $this->communityApplication->pi_linkTP(
			$linkText,
			array(
				'tx_community' => array(
					'user' => $requestedUser->getUid(),
					'profileAction' => 'editRelationship'
				)
			),
			false,
			$this->configuration['pages.']['relationshipEdit']
		);

		return $content;
	}

	/**
	 * checks whether the requesting user is a friend of the requested user.
	 * This is done by checking whether a record in tx_community_friend exists
	 * as a relation between both.
	 *
	 * @param tx_community_model_User the requested user
	 * @param tx_community_model_User the requesting user, who needs to be checked whether he is a friend
	 */
	protected function isFriend(tx_community_model_User $user, tx_community_model_User $friend) {
			// TODO this schould at some time moved to a more appropriate place like a FriendshipManager or so
			// TODO: Question: I think this is should be a method of the user object: isInRelationTo($user, $role) ?

			// FIXME: a friendship should have a connection from both sides to be a valid friendship connection
		$isFriend = false;

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid',
			'tx_community_friend',
			'feuser = ' . $user->getUid()
				. ' AND friend = ' . $friend->getUid()
		);

		$friendConnectionCount = $GLOBALS['TYPO3_DB']->sql_num_rows($res);

		if ($friendConnectionCount > 0) {
			$isFriend = true;
		}

		return $isFriend;
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
			$relationships[] = $friendRelationshipRow['role'];
		}

		return $relationships;
	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/userprofile/class.tx_community_controller_userprofile_profileactionswidget.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/userprofile/class.tx_community_controller_userprofile_profileactionswidget.php']);
}

?>