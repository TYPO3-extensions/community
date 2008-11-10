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

require_once($GLOBALS['PATH_community'] . 'view/groupprofile/class.tx_community_view_groupprofile_profileactions.php');


/**
 * A widget class to add actions to a user profile like "add as friend", "view friends", "send message", ...
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_controller_groupprofile_ProfileActionsWidget extends tx_community_controller_AbstractCommunityApplicationWidget {

	/**
	 * @var tx_community_LocalizationManager
	 */
	protected $localizationManager;

	/**
	 * constructor for the group profile actions widget
	 *
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function __construct() {
		parent::__construct();

		$localizationManagerClass = t3lib_div::makeInstanceClassName('tx_community_LocalizationManager');
		$this->localizationManager = call_user_func(
			array($localizationManagerClass, 'getInstance'),
			$GLOBALS['PATH_community'] . 'lang/locallang_groupprofile_profileactions.xml',
			$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_community.']
		);

		$this->name            = 'profileActions';
		$this->label           = $this->localizationManager->getLL('label_ProfileActionWidget');
		$this->position        = 2;
		$this->layoutContainer = 1;
	}

	/**
	 * the controller's default action
	 *
	 * @return	string
	 */
	public function indexAction() {
		$view = t3lib_div::makeInstance('tx_community_view_groupprofile_ProfileActions');
		$view->setTemplateFile($this->configuration['applications.']['groupProfile.']['widgets.']['profileActions.']['templateFile']);
		$view->setLanguageKey($this->communityApplication->LLkey);

		$view->setProfileActionsModel($this->getProfileActions());

		return $view->render();
	}

	/**
	 * adds a member to the group if that is allowed at all
	 *
	 * @return	void
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function joinGroupAction() {
		$requestingUser = $this->communityApplication->getRequestingUser();

			// TODO move this check into some central function
		if ($requestingUser->getUid() !== 0) {
			$requestedGroup = $this->communityApplication->getRequestedGroup();
			$groupType = $requestedGroup->getGroupType();

			switch ($groupType) {
				case tx_community_model_Group::TYPE_OPEN:
				case tx_community_model_Group::TYPE_MEMBERS_ONLY:
					$requestedGroup->addMember($requestingUser);
					break;
				case tx_community_model_Group::TYPE_PRIVATE:
					$requestedGroup->addPendingMember($requestingUser);
					break;
				case tx_community_model_Group::TYPE_SECRET:
						// do nothing, the user needs an invitation, can't invite himself
					break;
			}

			$requestedGroup->save();

				// done, now redirect back to the group profile page
			$profilePageUrl = $this->communityApplication->pi_getPageLink(
				$this->configuration['pages.']['groupProfile'],
				'',
				array(
					'tx_community' => array(
						'group' => $requestedGroup->getUid()
					)
				)
			);

			Header('HTTP/1.1 303 See Other');
			Header('Location: ' . t3lib_div::locationHeaderUrl($profilePageUrl));
			exit;
		}
	}

	/**
	 * removes a user as member from the group
	 *
	 * @author	Ingo Renner <ingo@typo3.org>
	 * @auhtor	Frank Naegler <typo3@naegler.net>
	 */
	public function leaveGroupAction() {
		$requestingUser = $this->communityApplication->getRequestingUser();
		$requestedGroup = $this->communityApplication->getRequestedGroup();

			// TODO move this check into some central function
		if ($requestingUser->getUid() !== 0) {
			if ($requestedGroup->isAdmin($requestingUser) && $requestedGroup->getNumberOfMembers() > 1) {
					// TODO throw an exception instead
				die($this->localizationManager->getLL('msg_leaveGroupIfIsAdminOfGroup'));
			}

			$requestedGroup->removeMember($requestingUser);
			$requestedGroup->save();

			if ($requestedGroup->getNumberOfMembers() == 0) {
					// trying to delete the group
				if ($requestedGroup->delete()) {
					$targetURL = $this->communityApplication->pi_getPageLink(
						$this->configuration['pages.']['groupList']
					);
					Header('HTTP/1.1 303 See Other');
					Header('Location: ' . t3lib_div::locationHeaderUrl($targetURL));
				}
			}

				// do a redirect to the profile page, no output
			$targetURL = $this->communityApplication->pi_getPageLink(
				$this->configuration['pages.']['groupProfile'],
				'',
				array(
					'tx_community' => array(
						'group' => $requestedGroup->getUid()
					)
				)
			);

			Header('HTTP/1.1 303 See Other');
			Header('Location: ' . t3lib_div::locationHeaderUrl($targetURL));
			exit;
		}
	}

	protected function getProfileActions() {
			// TODO make this extensible at some point
		$profileActions = array();

		$profileActions[]['link'] = $this->getJoinLeaveGroupProfileAction();
		$profileActions[]['link'] = $this->getEditGroupProfileAction();

			// hook
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tx_community']['getGroupProfileActions'])) {
			foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tx_community']['getGroupProfileActions'] as $classReference) {
				$hookObject = & t3lib_div::getUserObj($classReference);
				if ($hookObject instanceof tx_community_GroupProfileActionsProvider) {
					$profileActions = $hookObject->getGroupProfileActions($profileActions, $this);
				}

			}
		}

		return $profileActions;
	}

	/**
	 * creates the action links to join a group if the user is not a member of
	 * the group yet or a link to leave the group if he is a member already. In
	 * case there's a pending membership request, this will be shown instead of
	 * a link.
	 *
	 * @return	string	the join or leave profile action link or a notification that there already is a pending membership request
	 * @author	Ingo Renner <ingo@typo3.org>
	 * @author	Frank Naegler <typo3@naegler.net>
	 */
	protected function getJoinLeaveGroupProfileAction() {
		$content = '';

		$requestingUser = $this->communityApplication->getRequestingUser();
		$requestedGroup = $this->communityApplication->getRequestedGroup();

		if ($requestedGroup->isMember($requestingUser)) {
			$content = $this->communityApplication->pi_linkTP(
				$this->localizationManager->getLL('action_leaveGroup'),
				array(
					'tx_community' => array(
						'group' => $requestedGroup->getUid(),
						$this->name . 'Action' => 'leaveGroup'
					)
				)
			);

			if ($requestedGroup->isAdmin($requestingUser)) {
				$content = $this->localizationManager->getLL('action_isAdminOfGroup');
			}
		} else {
			$content = $this->communityApplication->pi_linkTP(
				 $this->localizationManager->getLL('action_joinGroup'),
				array(
					'tx_community' => array(
						'group' => $requestedGroup->getUid(),
						$this->name . 'Action' => 'joinGroup'
					)
				)
			);
		}

		if ($requestedGroup->isPendingMember($requestingUser)) {
			$content = $this->localizationManager->getLL('action_pending_membership');
		}

		return $content;
	}

	/**
	 * If the user is an admin of the group this method returns an "edit group"
	 * link.
	 *
	 * @return	string	an edit group link if the user is admin of the group, an empty string otherwise
	 * @author	Frank Naegler <typo3@naegler.net>
	 */
	protected function getEditGroupProfileAction() {
		$content = '';

		$requestingUser = $this->communityApplication->getRequestingUser();
		$requestedGroup = $this->communityApplication->getRequestedGroup();

		if ($requestedGroup->isAdmin($requestingUser)) {
				// the user is admin
			$content = $this->communityApplication->pi_linkToPage(
				$this->localizationManager->getLL('action_editGroup'),
				$this->configuration['pages.']['groupEdit'],
				'',
				array(
					'tx_community' => array(
						'group' => $requestedGroup->getUid(),
					)
				)
			);
		}

		return $content;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/groupprofile/class.tx_community_controller_groupprofile_profileactionswidget.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/groupprofile/class.tx_community_controller_groupprofile_profileactionswidget.php']);
}

?>