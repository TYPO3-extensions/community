<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008-2009 Frank Naegler <typo3@naegler.net>
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

require_once($GLOBALS['PATH_community'] . 'model/class.tx_community_model_group.php');
require_once($GLOBALS['PATH_community'] . 'view/creategroup/class.tx_community_view_creategroup_index.php');
require_once($GLOBALS['PATH_community'] . 'view/creategroup/class.tx_community_view_creategroup_creategrouperror.php');

/**
 * Create Group Application Controller
 *
 * @author	Frank Naegler <typo3@naegler.net>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_controller_CreateGroupApplication extends tx_community_controller_AbstractCommunityApplication {

	protected $group;

	/**
	 * constructor for class tx_community_controller_GroupProfileApplication
	 */
	public function __construct() {
		parent::__construct();

		$this->prefixId = 'tx_community_controller_CreateGroupApplication';
		$this->scriptRelPath = 'controller/class.tx_community_controller_creategroupapplication.php';
		$this->name = 'createGroup';
	}

	public function indexAction() {
		$view = t3lib_div::makeInstance('tx_community_view_creategroup_Index');
		/* @var $view tx_community_view_creategroup_Index */
		$view->setTemplateFile($this->configuration['applications.']['createGroup.']['templateFile']);
		$view->setLanguageKey($this->LLkey);

		$view->setFormAction($this->pi_getPageLink($GLOBALS['TSFE']->id));

		return $view->render();
	}

	public function createGroupAction() {
		$communityRequest = t3lib_div::GParrayMerged('tx_community');

		if (isset($communityRequest['groupName'])) {
			$groupClass = t3lib_div::makeInstanceClassName('tx_community_model_Group');
			$groupData  = array(
				'name'        => $communityRequest['groupName'],
				'description' => $communityRequest['groupDescription'],
				'grouptype'   => $communityRequest['groupType'],
				'pid'         => $this->configuration['pages.']['groupStorage']
			);
			$group = new $groupClass($groupData);

			$requestingUser = $this->getRequestingUser();

			$group->setCreator($requestingUser);
			$groupUid = $group->save();

			if ($groupUid) {
					// success, redirect
				$editGroupUrl = $this->pi_getPageLink(
					$this->configuration['pages.']['groupEdit'],
					'',
					array(
						'tx_community' => array(
							'group' => $groupUid
						)
					)
				);

				if ($this->configuration['pages.']['groupEdit.']['locationHash']) {
					$editGroupUrl .= $this->configuration['pages.']['groupEdit.']['locationHash'];
				}

				Header('HTTP/1.1 303 See Other');
				Header('Location: ' . t3lib_div::locationHeaderUrl($editGroupUrl));
				exit;
			} else {
				$view = t3lib_div::makeInstance('tx_community_view_creategroup_CreateGroupError');
				/* @var $view tx_community_view_creategroup_CreateGroupError */
				$view->setTemplateFile($this->configuration['applications.']['createGroup.']['templateFile']);
				$view->setLanguageKey($this->LLkey);

				$llMangerClass = t3lib_div::makeInstanceClassName('tx_community_LocalizationManager');
				$llManager = call_user_func(array($llMangerClass, 'getInstance'), 'EXT:community/lang/locallang_creategroup.xml',	$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_community.']);

				$view->setMessage($llManager->getLL('creategroup_errorMsg'));

				return $view->render();
			}
		}
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_creategroupapplication.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_creategroupapplication.php']);
}

?>