<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008-2009 Ingo Renner <ingo@typo3.org>
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

require_once($GLOBALS['PATH_community'] . 'model/class.tx_community_model_groupgateway.php');
require_once($GLOBALS['PATH_community'] . 'view/userprofile/class.tx_community_view_userprofile_mygroups.php');

/**
 * a widget for the user profile to show the user's group memberships
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_controller_userprofile_MyGroupsWidget extends tx_community_controller_AbstractCommunityApplicationWidget implements tx_community_acl_AclResource  {

	/**
	 * constructor for class tx_community_controller_userprofile_MyGroupsWidget
	 */
	public function __construct() {
		parent::__construct();
		$this->localizationManager = tx_community_LocalizationManager::getInstance('EXT:community/lang/locallang_userprofile_mygroups.xml', $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_community.']);

		$this->name     = 'myGroups';
		$this->label    = $this->localizationManager->getLL('label_MyGroupsWidget');
		$this->draggable = true;
		$this->removable = true;
		$this->cssClass = '';
	}

	public function indexAction() {
		$groupGateway = t3lib_div::makeInstance('tx_community_model_GroupGateway');
		$cObj = t3lib_div::makeInstance('tslib_cObj');
 
		if ($this->getCommunityApplication()->getName() == 'StartPage') {
			$user = $this->getCommunityApplication()->getRequestingUser();
		} else {
			$user = $this->getCommunityApplication()->getRequestedUser();
		}

		$groups = $groupGateway->findGroupsByUser(
			$user
		);

		$view = t3lib_div::makeInstance('tx_community_view_userprofile_MyGroups');
		$view->setTemplateFile($this->configuration['applications.']['userProfile.']['widgets.']['myGroups.']['templateFile']);
		$view->setLanguageKey($this->communityApplication->LLkey);
               foreach ($groups as $group) {
	            if ($group->getGroupType() != tx_community_model_Group::TYPE_SECRET) {
			 $imgConf = $this->configuration['applications.']['userProfile.']['widgets.']['myGroups.']['groupImage.'];
 			 $imgConf['file'] = (strlen($group->getImage()) > 0) ? $group->getImage() : $imgConf['file'];
			 $genImage = $cObj->cObjGetSingle('IMAGE', $imgConf);
			 $group->setHTMLImage($genImage);
			 $listGroupsArray[] = $group;
		    }
		}

		$view->setGroupModel($listGroupsArray);



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


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/userprofile/class.tx_community_controller_userprofile_mygroupswidget.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/userprofile/class.tx_community_controller_userprofile_mygroupswidget.php']);
}

?>