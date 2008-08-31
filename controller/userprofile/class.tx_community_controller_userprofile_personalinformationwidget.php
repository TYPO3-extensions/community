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

require_once($GLOBALS['PATH_community'] . 'classes/class.tx_community_accessmanager.php');
require_once($GLOBALS['PATH_community'] . 'interfaces/interface.tx_community_communityapplicationwidget.php');
require_once($GLOBALS['PATH_community'] . 'interfaces/interface.tx_community_command.php');
require_once($GLOBALS['PATH_community'] . 'interfaces/acl/interface.tx_community_acl_aclresource.php');
require_once($GLOBALS['PATH_community'] . 'view/userprofile/class.tx_community_view_userprofile_personalinformation.php');

/**
 * personal information widget for the user profile community application
 * showing interests, activities, favorites, and about me
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_controller_userprofile_PersonalInformationWidget implements tx_community_CommunityApplicationWidget, tx_community_Command, tx_community_acl_AclResource {

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
		return true;
	}

	/**
	 * returns whether the widget can be removed from being displayed
	 *
	 * @return	boolean	true id removing is allowed, false otherwise
	 */
	public function isRemovable() {
		return true;
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
		return 'personalInformation';
	}

	/**
	 * Returns the string identifier of the Resource
	 *
	 * @return string
	 */
	public function getResourceId() {
		$requestedUser = $this->communityApplication->getRequestedUser();

		$resourceId = $this->communityApplication->getName()
			. '_' . $this->getID()
			. '_' . $requestedUser->getUid();

		return $resourceId;
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
		return 'PersonalInformationWidget';
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
	 * excutes the controller, fetches the user to show the personal
	 * information for, creates a view and return the view's output
	 *
	 * @return	string	the view's output
	 */
	public function execute() {
		$content = '';

		$requestedUser  = $this->communityApplication->getRequestedUser();
		$requestingUser = $this->communityApplication->getRequestingUser();

		$accessManagerClass = t3lib_div::makeInstanceClassName('tx_community_AccessManager');
		$accessManager      = call_user_func(array($accessManagerClass, 'getInstance'));

		$allowed = false;
		if ($requestedUser == $requestingUser) {
			$allowed = true;
		} else {
			$accessManager->addResource($this);
			$allowed = $accessManager->isAllowed($this, $requestingUser);
		}

		if ($allowed) {
			$view = t3lib_div::makeInstance('tx_community_view_userprofile_PersonalInformation');
			$view->setUserModel($requestedUser);
			$view->setTemplateFile($this->configuration['applications.']['userProfile.']['widgets.']['personalInformation.']['templateFile']);
			$view->setLanguageKey($this->communityApplication->LLkey);

			$content =  $view->render();
		}

		return $content;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/userprofile/class.tx_community_controller_userprofile_personalinformationwidget.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/userprofile/class.tx_community_controller_userprofile_personalinformationwidget.php']);
}

?>