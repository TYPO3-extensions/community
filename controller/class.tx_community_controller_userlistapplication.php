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

require_once($GLOBALS['PATH_community'] . 'controller/class.tx_community_controller_abstractcommunityapplication.php');
require_once($GLOBALS['PATH_community'] . 'view/userlist/class.tx_community_view_userlist_index.php');

/**
 * An application to create lists of users
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_controller_UserListApplication extends tx_community_controller_AbstractCommunityApplication {

	protected $userListModel = array();
	protected $templateFileReference;

	/**
	 * constructor for class tx_community_controller_UserListApplication
	 */
	public function __construct() {
		parent::__construct();

		$this->prefixId = 'tx_community_controller_UserListApplication';
		$this->scriptRelPath = 'controller/class.tx_community_controller_userlistapplication.php';
		$this->name = 'UserList';
	}

	public function execute() {
			// TODO add dispatching of the correct action
		return $this->indexAction();
	}

	public function executeAction($actionName) {
		// TODO implement a (this) method to call a specific action
	}

	public function indexAction() {
		$view = t3lib_div::makeInstance('tx_community_view_userlist_Index');
		/* @var $view tx_community_view_privacy_Index */
		$view->setTemplateFile($this->configuration['applications.']['userList.']['templateFile']);
		$view->setLanguageKey($this->LLkey);

		$view->setUserModel($this->userListModel);

		return $view->render() . ', user list';
	}

	public function setUserListModel(array $userListModel) {
		$this->userListModel = $userListModel;
	}

	public function setTemplateFile($templateFileReference) {
		$this->templateFileReference = $templateFileReference;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_userlistapplication.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_userlistapplication.php']);
}

?>