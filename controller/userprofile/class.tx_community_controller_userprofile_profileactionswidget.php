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
require_once($GLOBALS['PATH_community'] . 'view/userprofile/class.tx_community_view_userprofile_profileactions.php');

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
	 * returns the widget's ID, this is the ID which is used while register the widget in the ext_localconf.php
	 *
	 * @return	string	the widget's CSS class
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

	public function execute() {
		return $this->indexAction();
	}

	public function indexAction() {
		$view = t3lib_div::makeInstance('tx_community_view_userprofile_ProfileActions');
		$view->setTemplateFile($this->configuration['applications.']['userProfile.']['widgets.']['profileActions.']['templateFile']);
		$view->setLanguageKey($this->communityApplication->LLkey);

		$view->setProfileActionsModel($this->getProfileActions());

		return $view->render();
	}

	protected function addAsFriendAction() {

	}

	protected function getProfileActions() {
			// TODO make this extensible at some point
		$profileActions = array();

		$profileActions[]['link'] = $this->getAddAsFriendProfileAction();

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

		if ($this->isFriend($requestedUser, $requestingUser)) {
				// the users are already friends
			$content = sprintf(
				$localizationManager->getLL('action.isFriendWith'),
				$requestedUser->getAccount()->getFirstName()
			);
		} else {
				// the users are not friends yet
			$content = sprintf(
				$localizationManager->getLL('action.addAsFriend'),
				$requestedUser->getAccount()->getFirstName()
			);

			// TODO add the link to actually add the user as a friend
		}

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
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/userprofile/class.tx_community_controller_userprofile_profileactionswidget.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/userprofile/class.tx_community_controller_userprofile_profileactionswidget.php']);
}

?>