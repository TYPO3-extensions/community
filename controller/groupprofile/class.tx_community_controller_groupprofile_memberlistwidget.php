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

require_once($GLOBALS['PATH_community'] . 'view/groupprofile/class.tx_community_view_groupprofile_memberlist.php');

/**
 * meber list widget for the group profile community application
 *
 * @author	Frank Nägler <typo3@naegler.net>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_controller_groupprofile_MemberListWidget extends tx_community_controller_AbstractCommunityApplicationWidget implements tx_community_acl_AclResource {

	protected $accessMode;
	/**
	 * @var tx_community_model_GroupGateway
	 */
	protected $groupGateway;
	
	public function __construct() {
		parent::__construct();

			// set default access mode
		$this->accessMode = 'read';

		$this->name     = 'memberList';
		$this->label    = 'MemberListWidget';
		$this->cssClass = '';

		$this->dragable  = true;
		$this->removable = true;
		$this->position  = 2;

		$this->groupGateway = t3lib_div::makeInstance('tx_community_model_GroupGateway');
	}

	/**
	 * Returns the string identifier of the Resource
	 *
	 * @return string
	 */
	public function getResourceId() {
		$requestedGroup = $this->groupGateway->findCurrentGroup();

		$resourceId = $this->communityApplication->getName()
			. '_' . $this->name
			. '_' . $this->accessMode
			. '_' . $requestedGroup->getUid();

		return $resourceId;
	}

	/**
	 * the default action for this widget, fetches the group to show the group
	 * information for, creates a view and returns the view's output
	 *
	 * @return	string	the view's output
	 */
	public function indexAction() {
		$content = '';

		$requestingUser = $this->communityApplication->getRequestingUser();
		$requestedGroup = $this->groupGateway->findCurrentGroup();
		
		$accessManagerClass = t3lib_div::makeInstanceClassName('tx_community_AccessManager');
		$accessManager      = call_user_func(array($accessManagerClass, 'getInstance'));

 		$allowed = false;
		if ($requestedGroup->isAdmin($requestingUser)) {
			$allowed = true;
		} else {
			$accessManager->addResource($this);
			$allowed = $accessManager->isAllowed($this);
		}

		/*
 		 * no access maner needed here?
 		 * we overwrite it
 		 */
		$allowed = true;
		if ($allowed) {
			$view = t3lib_div::makeInstance('tx_community_view_groupprofile_MemberList');
			$view->setGroupModel($requestedGroup);
			$view->setTemplateFile($this->configuration['applications.']['groupProfile.']['widgets.']['memberList.']['templateFile']);
			$view->setConfiguration($this->configuration);
			$view->setLanguageKey($this->communityApplication->LLkey);

			$content = $view->render();
		}

		return $content;
	}

	protected function checkAccess() {
		// TODO move access checking stuff here
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/groupprofile/class.tx_community_controller_userprofile_memberwidget.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/groupprofile/class.tx_community_controller_userprofile_memberlistwidget.php']);
}

?>