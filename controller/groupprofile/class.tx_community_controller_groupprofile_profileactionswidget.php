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

require_once($GLOBALS['PATH_community'] . 'classes/class.tx_community_localizationmanager.php');
require_once($GLOBALS['PATH_community'] . 'interfaces/interface.tx_community_communityapplicationwidget.php');
require_once($GLOBALS['PATH_community'] . 'interfaces/interface.tx_community_command.php');
require_once($GLOBALS['PATH_community'] . 'view/groupprofile/class.tx_community_view_groupprofile_profileactions.php');
require_once($GLOBALS['PATH_community'] . 'model/class.tx_community_model_groupgateway.php');


/**
 * A widget class to add actions to a user profile like "add as friend", "view friends", "send message", ...
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_controller_groupprofile_ProfileActionsWidget implements tx_community_CommunityApplicationWidget, tx_community_Command {

	/**
	 * a reference to the parent community application this widget belongs to
	 *
	 * @var tx_community_controller_AbstractCommunityApplication
	 */
	protected $communityApplication;
	/**
	 * a reference to the community application manager
	 *
	 * @var tx_community_ApplicationManager
	 */
	protected $communityApplicationManager;
	protected $configuration;
	protected $data;
	/**
	 * @var tx_community_LocalizationManager
	 */
	protected $localizationManager;
	/**
	 * @var tx_community_model_GroupGateway
	 */
	protected $groupGateway;

	public function initialize($data, $configuration) {
		$this->data = $data;
		$this->configuration = $configuration;

		$localizationManagerClass = t3lib_div::makeInstanceClassName('tx_community_LocalizationManager');
		$this->localizationManager = call_user_func(
			array($localizationManagerClass, 'getInstance'),
			$GLOBALS['PATH_community'] . 'lang/locallang_groupprofile_profileactions.xml',
			array()
		);

		$this->groupGateway = t3lib_div::makeInstance('tx_community_model_GroupGateway');
		$this->communityApplicationManager = tx_community_ApplicationManager::getInstance();
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
		return 1;
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

		$widgetConfiguration = $this->communityApplicationManager->getWidgetConfiguration(
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
		$view = t3lib_div::makeInstance('tx_community_view_groupprofile_ProfileActions');
		$view->setTemplateFile($this->configuration['applications.']['groupProfile.']['widgets.']['profileActions.']['templateFile']);
		$view->setLanguageKey($this->communityApplication->LLkey);

		$view->setProfileActionsModel($this->getProfileActions());

		return $view->render();
	}

	public function joinGroupAction() {
		$requestingUser = $this->communityApplication->getRequestingUser();
		$requestedGroup = $this->groupGateway->findCurrentGroup();

		if (!is_null($requestingUser)) {
			if ($requestedGroup instanceof tx_community_model_Group) {
				if ($requestedGroup->addMember($requestingUser)) {
					// do a redirect to the profile page, no output
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
				} else {
					// TODO throw some exception
				}
			}
		}
	}

	public function leaveGroupAction() {
		$requestingUser = $this->communityApplication->getRequestingUser();
		/**
		 * @var $requestedGroup tx_community_model_Group
		 */
		$requestedGroup = $this->groupGateway->findCurrentGroup();

		if (!is_null($requestingUser)) {
			if ($requestedGroup instanceof tx_community_model_Group) {
				if ($requestedGroup->isAdmin($requestingUser)) {
					$membersOfGroup = $requestedGroup->getAllMembers();
					if (count($membersOfGroup) > 1) {
						// TODO throw some exception
						die($this->localizationManager->getLL('msg_leaveGroupIfIsAdminOfGroup'));
					}
				}

				if ($requestedGroup->removeMember($requestingUser)) {
					$membersOfGroup = $requestedGroup->getAllMembers();
					if (count($membersOfGroup) == 0) {
						// trying to delete the group
						if ($requestedGroup->delete()) {
							$targetURL = $this->communityApplication->pi_getPageLink(
								$this->configuration['pages.']['groupOverview'],
								'',
								array()
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
				} else {
					// TODO throw some exception
				}
			}
		}
	}

	protected function getProfileActions() {
			// TODO make this extensible at some point
		$profileActions = array();

		$profileActions[]['link'] = $this->getJoinLeaveGroupProfileAction();
		$profileActions[]['link'] = $this->getEditGroupProfileAction();

		return $profileActions;
	}

	protected function getJoinLeaveGroupProfileAction() {
		$content = '';

		$requestingUser = $this->communityApplication->getRequestingUser();
		$requestedGroup = $this->groupGateway->findCurrentGroup();

		if ($requestedGroup->isMember($requestingUser)) {
			$linkText = sprintf(
				$this->localizationManager->getLL('action_leaveGroup'),
				$requestingUser->getNickname()
			);

			$content = $this->communityApplication->pi_linkTP(
				$linkText,
				array(
					'tx_community' => array(
						'group' => $requestedGroup->getUid(),
						'profileAction' => 'leaveGroup'
					)
				)
			);

			$membersOfGroup = $requestedGroup->getAllMembers();
			if ($requestedGroup->isAdmin(($requestingUser)) && count($membersOfGroup) > 1) {
				$content = sprintf(
					$this->localizationManager->getLL('action_isAdminOfGroup'),
					$requestingUser->getNickname()
				);
			}

		} else {
			$linkText = sprintf(
				$this->localizationManager->getLL('action_joinGroup'),
				$requestingUser->getNickname()
			);

			$content = $this->communityApplication->pi_linkTP(
				$linkText,
				array(
					'tx_community' => array(
						'group' => $requestedGroup->getUid(),
						'profileAction' => 'joinGroup'
					)
				)
			);
		}

		if ($requestedGroup->isTempMember($requestingUser)) {
			$content = sprintf(
				$this->localizationManager->getLL('action_pending_membership'),
				$requestingUser->getNickname()
			);
		}
		return $content;
	}

	protected function getEditGroupProfileAction() {
		$content = '';

		$requestingUser = $this->communityApplication->getRequestingUser();
		$requestedGroup = $this->groupGateway->findCurrentGroup();

		if ($requestedGroup->isAdmin($requestingUser)) {
			// the user is admin
			$linkText = $this->localizationManager->getLL('action_editGroup');

			$content = $this->communityApplication->pi_linkToPage(
				$linkText,
				$this->configuration['pages.']['groupEdit'],
				'',
				array(
					'tx_community' => array(
						'group' => $requestedGroup->getUid(),
					)
				)
			);
		} else {
			// the user are not admin yet, create no link
		}

		return $content;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/groupprofile/class.tx_community_controller_groupprofile_profileactionswidget.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/groupprofile/class.tx_community_controller_groupprofile_profileactionswidget.php']);
}

?>