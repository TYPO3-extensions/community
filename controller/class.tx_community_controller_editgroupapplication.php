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
	protected $group;

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

		$applicationManagerClass = t3lib_div::makeInstanceClassName('tx_community_ApplicationManager');
		$applicationManager      = call_user_func(array($applicationManagerClass, 'getInstance'));
		/* @var $applicationManager tx_community_ApplicationManager */
		
		$applicationConfiguration = $applicationManager->getApplicationConfiguration(
			$this->getName()
		);
		
		$communityRequest = t3lib_div::GParrayMerged('tx_community');

		$groupGateway = t3lib_div::makeInstance('tx_community_model_GroupGateway');
		/* @var $groupGateway tx_community_model_GroupGateway */
		
		$this->group = $groupGateway->findCurrentGroup();
		if (is_null($this->group)) {
			// @TODO throw Exception
			die('no group id');
		}
		
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
		
		$imgConf = $this->configuration['applications.']['editGroup.']['previewImage.'];
		
		$imagePath = (strlen($this->group->getTX_community_image())) ? $this->configuration['applications.']['editGroup.']['uploadPath'] . $this->group->getTX_community_image() : $this->configuration['applications.']['editGroup.']['defaultIcon'];
		$imgConf['file'] = $imagePath;
		$cObj = t3lib_div::makeInstance('tslib_cObj');
		$view->setImage($cObj->cObjGetSingle($this->configuration['applications.']['editGroup.']['previewImage'], $imgConf));
		
		// make actions
		$actions = $this->configuration['applications.']['editGroup.']['memberlist.']['actions.'];
		debug($actions);
		$adminActions = array();
		$otherActions = array();
		foreach ($actions['admins.'] as $k => $v) {
			switch ($v) {
				case 'TEXT' :
				case 'HTML' :
				case 'IMAGE' :
					$adminActions[] = $this->cObj->cObjGetSingle($actions['admins.'][$k], $actions['admins.'][$k.'.']);
				break;
			}
		}
		foreach ($actions['other.'] as $k => $v) {
			switch ($v) {
				case 'TEXT' :
				case 'HTML' :
				case 'IMAGE' :
					$otherActions[] = $this->cObj->cObjGetSingle($actions['other.'][$k], $actions['other.'][$k.'.']);
				break;
			}
		}
		$view->setAdminActions($adminActions);
		$view->setOtherActions($otherActions);
		
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
				$fileFunc = t3lib_div::makeInstance('t3lib_basicFileFunctions');
				$upPath = $this->configuration['applications.']['editGroup.']['uploadPath'];
				$fileName = $_FILES['tx_community']['name']['imageFile'];
				$tmpFile  = $_FILES['tx_community']['tmp_name']['imageFile'];
				$pathInfo = pathinfo($fileName);
				$dir = t3lib_div::getFileAbsFileName($upPath);
				$newName = md5($fileName) .'.'. $pathInfo['extension'];
				if (move_uploaded_file($tmpFile, $dir.$newName)) {
					$group->setTX_community_image($newName);
					if ($group->save()) {
						$imgConf = $this->configuration['applications.']['editGroup.']['previewImage.'];
						$imgConf['file'] = $upPath.$newName;
						$cObj = t3lib_div::makeInstance('tslib_cObj');
						$genImage = $cObj->cObjGetSingle('IMG_RESOURCE', $imgConf);
						list($width,$height) = getimagesize($genImage);
						$result = "{'status': 'success', 'msg': 'image uploaded', 'newImage': '{$genImage}', 'newWidth': '{$width}', 'newHeight': '{$height}'}";
					} else {
						$result = "{'status': 'error', 'msg': 'error while save'}";
					}
				} else {
					$result = "{'status': 'error', 'msg': 'can't upload file'}";
				}
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