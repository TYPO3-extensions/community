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
require_once($GLOBALS['PATH_community'] . 'classes/class.tx_community_applicationmanager.php');
require_once($GLOBALS['PATH_community'] . 'view/editgroup/class.tx_community_view_editgroup_index.php');

/**
 * Edit Group Application Controller
 *
 * @author	Frank Nägler <typo3@naegler.net>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_controller_EditGroupApplication extends tslib_pibase {
	public $prefixId      = 'tx_community_controller_EditGroupApplication';		// Same as class name
	public $scriptRelPath = 'controller/class.tx_community_controller_editgroupapplication.php';	// Path to this script relative to the extension dir.
	public $extKey        = 'community';	// The extension key.

	public $cObj;
	public $conf;
	protected $data;
	protected $name;
	protected $configuration;

	/**
	 * constructor for class tx_community_controller_GroupProfileApplication
	 */
	public function __construct() {
		parent::__construct();

		$this->prefixId = 'tx_community_controller_EditGroupApplication';
		$this->scriptRelPath = 'controller/class.tx_community_controller_editgroupapplication.php';
		$this->name = 'EditGroup';
	}

	public function initialize($configuration) {
		$this->conf = $configuration;
		$this->tslib_pibase();
		$this->pi_setPiVarDefaults();
		$this->pi_USER_INT_obj = 1; // Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
		$this->pi_initPIflexForm();

		$this->conf = t3lib_div::array_merge_recursive_overrule(
			$this->conf,
			$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_community.']
		);
		$this->configuration = $this->conf;
	}

	public function execute($content, $configuration) {
		$content = '';
		$this->initialize($configuration);

		$communityRequest = t3lib_div::GParrayMerged('tx_community');

		$applicationManagerClass = t3lib_div::makeInstanceClassName('tx_community_ApplicationManager');
		$applicationManager      = call_user_func(array($applicationManagerClass, 'getInstance'));
		/* @var $applicationManager tx_community_ApplicationManager */

		$applicationConfiguration = $applicationManager->getApplicationConfiguration(
			$this->getName()
		);

			// dispatch
		if (!empty($communityRequest['editGroupAction'])
			&& method_exists($this, $communityRequest['editGroupAction'] . 'Action')
			&& in_array($communityRequest['editGroupAction'], $applicationConfiguration['actions'])
		) {
				// call a specifically requested action
			$actionName = $communityRequest['editGroupAction'] . 'Action';
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
		$view = t3lib_div::makeInstance('tx_community_view_editGroup_Index');
		/* @var $view tx_community_view_editGroup_Index */
		$view->setTemplateFile($this->configuration['applications.']['editGroup.']['templateFile']);
		$view->setLanguageKey($this->LLkey);

		$formAction = $this->pi_getPageLink(
			$GLOBALS['TSFE']->id,
			'',
			array(
				'tx_community' => array(
					'editGroupAction' => 'saveData'
				)
			)
		);
		$view->setFormAction($formAction);

		return $view->render();
	}
	
	protected function saveDataAction() {
		$communityRequest = t3lib_div::GParrayMerged('tx_community');
		$groupGateway = t3lib_div::makeInstance('tx_community_model_GroupGateway');
		$userGateway = t3lib_div::makeInstance('tx_community_model_UserGateway');
		/**
		 * @var tx_community_model_Group
		 */
		$group = $groupGateway->findCurrentGroup();
		$user  = $userGateway->findCurrentlyLoggedInUser();
		
		$ajaxAction = $communityRequest['ajaxAction'];
		switch ($ajaxAction) {
			case 'saveGeneral':
				$isPublic = (isset($communityRequest['group_public'])) ? 1 : 0;
				
				if ($group->isAdmin($user)) {
					$group->setTitle($communityRequest['group_title']);
					$group->setDescription($communityRequest['group_description']);
					$group->setTX_community_public($isPublic);
					if ($group->save()) {
						$result = "{'status': 'success', 'msg': 'saved'}";
					} else {
						$result = "{'status': 'error', 'msg': 'not saved'}";
					}
				} else {
					$result = "{'status': 'error', 'msg': 'not admin'}";	
				}
			break;
			case 'saveImage':

			break;
			case 'changeMemberStatus':

			break;
			case 'inviteMember':

			break;
			default:
				$result = "{'status': 'error', 'msg': 'no ajax action'}";
			break;
		}		
		echo $result;
		die();
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_editgroupapplication.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_editgroupapplication.php']);
}

?>