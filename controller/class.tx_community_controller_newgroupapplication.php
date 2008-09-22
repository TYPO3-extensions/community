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

require_once($GLOBALS['PATH_community'] . 'controller/class.tx_community_controller_abstractcommunityapplication.php');
require_once($GLOBALS['PATH_community'] . 'model/class.tx_community_model_usergateway.php');
require_once($GLOBALS['PATH_community'] . 'view/newgroup/class.tx_community_view_newgroup_index.php');
require_once($GLOBALS['PATH_community'] . 'view/newgroup/class.tx_community_view_newgroup_newgroup.php');

/**
 * Edit Group Application Controller
 *
 * @author	Frank Nägler <typo3@naegler.net>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_controller_NewGroupApplication extends tx_community_controller_AbstractCommunityApplication {

	public $cObj;
	public $conf;
	protected $data;
	protected $name;
	protected $configuration;
	protected $group;
	protected $user;

	/**
	 * constructor for class tx_community_controller_GroupProfileApplication
	 */
	public function __construct() {
		parent::__construct();

		$this->prefixId = 'tx_community_controller_NewGroupApplication';
		$this->scriptRelPath = 'controller/class.tx_community_controller_newgroupapplication.php';
		$this->name = 'NewGroup';
	}

	public function execute() {
		$content = '';

		$applicationConfiguration = $GLOBALS['TX_COMMUNITY']['applicationManager']->getApplicationConfiguration(
			$this->getName()
		);

		$communityRequest = t3lib_div::GParrayMerged('tx_community');

		$userGateway = t3lib_div::makeInstance('tx_community_model_UserGateway');
		/* @var $userGateway tx_community_model_UserGateway */

		$this->user  = $userGateway->findCurrentlyLoggedInUser();
		if (is_null($this->user)) {
			// @TODO throw Exception
			die('no loggedin user');
		}

			// dispatch
		if (!empty($communityRequest['newGroupAction'])
			&& method_exists($this, $communityRequest['newGroupAction'] . 'Action')
			&& in_array($communityRequest['newGroupAction'], $applicationConfiguration['actions'])
		) {
				// call a specifically requested action
			$actionName = $communityRequest['newGroupAction'] . 'Action';
			$content = $this->$actionName();
		} else {
				// call the default action
			$defaultActionName = $applicationConfiguration['defaultAction'] . 'Action';
			$content = $this->$defaultActionName();
		}

		return $content;
	}

	/**
	 * returns the name of this community application
	 *
	 * @return	string	This community application's name
	 */
	public function getName() {
		return $this->name;
	}

	protected function indexAction() {
		$view = t3lib_div::makeInstance('tx_community_view_newGroup_Index');
		/* @var $view tx_community_view_newGroup_Index */
		$view->setTemplateFile($this->configuration['applications.']['newGroup.']['templateFile']);
		$view->setLanguageKey($this->LLkey);

		$formAction = $this->pi_getPageLink(
			$GLOBALS['TSFE']->id,
			'',
			array(
				'tx_community' => array(
					'newGroupAction' => 'newGroup'
				)
			)
		);
		$view->setFormAction($formAction);

		return $view->render();
	}

	protected function newGroupAction() {
		$view = t3lib_div::makeInstance('tx_community_view_newGroup_NewGroup');
		/* @var $view tx_community_view_newGroup_Index */
		$view->setTemplateFile($this->configuration['applications.']['newGroup.']['templateFile']);
		$view->setLanguageKey($this->LLkey);

		$llMangerClass = t3lib_div::makeInstanceClassName('tx_community_LocalizationManager');
		$llManager = call_user_func(array($llMangerClass, 'getInstance'), 'EXT:community/lang/locallang_newgroup.xml',	$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_community.']);

		$communityRequest = t3lib_div::GParrayMerged('tx_community');
		if (isset($communityRequest['group_title'])) {
			$group = t3lib_div::makeInstance('tx_community_model_Group');
			$group->setTitle($communityRequest['group_title']);
			$group->setPid($this->configuration['pages.']['userStorage']);
			if ($group->save()) {
				$group->addMember($this->user);
				$group->addAdmin($this->user);
				$group->save();
				// redirect
				$editGroupUrl = $this->pi_getPageLink(
					$this->configuration['pages.']['groupEdit'],
					'',
					array(
						'tx_community' => array(
							'group' => $group->getUid()
						)
					)
				);

				Header('HTTP/1.1 303 See Other');
				Header('Location: ' . t3lib_div::locationHeaderUrl($editGroupUrl));
				exit;
			} else {
				$view->setMessage($llManager->getLL('msg_create_error'));
			}
		}
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_newgroupapplication.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_newgroupapplication.php']);
}

?>