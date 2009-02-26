<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2008 Frank Naegler <typo3@naegler.net>
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

require_once($GLOBALS['PATH_community'] . 'model/class.tx_community_model_usergateway.php');
require_once($GLOBALS['PATH_community'] . 'view/userprofile/class.tx_community_view_userprofile_myfriends.php');

/**
 * a widget for the user profile to show the user's friends
 *
 * @author	Frank Naegler <typo3@naegler.net>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_controller_userprofile_MyFriendsWidget extends tx_community_controller_AbstractCommunityApplicationWidget implements tx_community_acl_AclResource {

	/**
	 * constructor for class tx_community_controller_userprofile_MyFriendsWidget
	 */
	public function __construct() {
		parent::__construct();
		$this->localizationManager = tx_community_LocalizationManager::getInstance('EXT:community/lang/locallang_userprofile_myfriends.xml', $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_community.']);

		$this->name     = 'myFriends';
		$this->label    = $this->localizationManager->getLL('label_MyFriendsWidget');
		$this->draggable = true;
		$this->removable = true;
		$this->cssClass = '';
		$this->request 		= t3lib_div::GParrayMerged('tx_community_myfriends');
	}

	public function indexAction() {
		$userGateway = t3lib_div::makeInstance('tx_community_model_UserGateway');
		$cObj = t3lib_div::makeInstance('tslib_cObj');

		$pageBrowserConfig = $this->configuration['applications.']['listUsers.']['pageBrowser.'];

		if ($this->communityApplication->getName() == 'StartPage') {
			$user = $this->communityApplication->getRequestingUser();
		} else {
			$user = $this->communityApplication->getRequestedUser();
		}
		
		$roleData = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'uid, name',
			'tx_community_acl_role',
			'is_friend_role = 1 AND deleted = 0 AND hidden = 0' 
			);
			$friendsByRole = array();
			foreach ($roleData as $role) {
				$firstGroup = (isset($this->request['page_'.$role['uid']])) ? (intval($this->request['page_'.$role['uid']]+1)*$pageBrowserConfig['numberOfEntriesPerPage']) - $pageBrowserConfig['numberOfEntriesPerPage'] : 0;
				$pageBrowserConfig['numberOfPages'] = ceil($userGateway->findConnectedUsersByRoleCount($user, $role['uid']) / max($pageBrowserConfig['numberOfEntriesPerPage'],1));
				$pageBrowserConfig['pageParameterName'] = 'tx_community_myfriends|page_'.$role['uid'];
				$friendsByRole[] = array(
				'uid' => $role['uid'],
				'name' => $role['name'],
				'friends' => $userGateway->findConnectedUsersByRole($user, $role['uid'], $pageBrowserConfig['numberOfEntriesPerPage'], $firstGroup),
				'pagebrowser' => $cObj->cObjGetSingle($this->configuration['applications.']['listGroups.']['pageBrowser'], $pageBrowserConfig),
				);
			}

			$view = t3lib_div::makeInstance('tx_community_view_userprofile_Myfriends');
			$view->setTemplateFile($this->configuration['applications.']['userProfile.']['widgets.']['myFriends.']['templateFile']);
			$view->setLanguageKey($this->communityApplication->LLkey);
			$view->setFriendsModel($friendsByRole);

			return $view->render();
	}



	/**
	 * Returns the string identifier of the Resource
	 *
	 * @return string
	 */
	public function getResourceId() {
		$requestedUser = $this->communityApplication->getRequestedUser();

		$resourceId = $this->communityApplication->getName()
		. '_' . $this->name
		. '_' . $this->accessMode
		. '_' . $requestedUser->getUid();

		return $resourceId;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/userprofile/class.tx_community_controller_userprofile_myfriendswidget.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/userprofile/class.tx_community_controller_userprofile_myfriendswidget.php']);
}

?>
