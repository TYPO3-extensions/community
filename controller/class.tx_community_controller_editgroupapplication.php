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

require_once($GLOBALS['PATH_community'] . 'model/class.tx_community_model_usergateway.php');
require_once($GLOBALS['PATH_community'] . 'model/class.tx_community_model_groupgateway.php');
require_once($GLOBALS['PATH_community'] . 'view/editgroup/class.tx_community_view_editgroup_index.php');

/**
 * Edit Group Application Controller
 *
 * @author	Frank Nägler <typo3@naegler.net>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_controller_EditGroupApplication extends tx_community_controller_AbstractCommunityApplication {

	public $cObj;
	public $conf;
	protected $data;
	protected $name;
	protected $configuration;
	protected $group;
	protected $messageAPILoaded = false;
	protected $accessManager;

	/**
	 * constructor for class tx_community_controller_GroupProfileApplication
	 */
	public function __construct() {
		parent::__construct();

		$this->prefixId = 'tx_community_controller_EditGroupApplication';
		$this->scriptRelPath = 'controller/class.tx_community_controller_editgroupapplication.php';
		$this->name = 'editGroup';

		if (t3lib_extMgm::isLoaded('community_messages')) {
			require_once(t3lib_extMgm::extPath('community_messages').'classes/class.tx_communitymessages_api.php');
			$this->messageAPILoaded = true;
		}

		$this->accessManager = tx_community_AccessManager::getInstance();
	}

	public function execute() {
		$content = '';

		$applicationConfiguration = $GLOBALS['TX_COMMUNITY']['applicationManager']->getApplicationConfiguration(
			$this->getName()
		);

		$communityRequest = t3lib_div::GParrayMerged('tx_community');

		$groupGateway = t3lib_div::makeInstance('tx_community_model_GroupGateway');
		/* @var $groupGateway tx_community_model_GroupGateway */

		$userGateway = t3lib_div::makeInstance('tx_community_model_UserGateway');
		/* @var $userGateway tx_community_model_UserGateway */

		$this->group = $groupGateway->findRequestedGroup();
		if (is_null($this->group)) {
			// @TODO throw Exception
			die('no group id');
		}

		$user  = $userGateway->findCurrentlyLoggedInUser();
		if (is_null($user)) {
			// @TODO throw Exception
			die('no loggedin user');
		}

		if (!$this->group->isAdmin($user)) {
			// @TODO throw Exception
			die('not admin');
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
#debug($actions);
		$adminActions = array();
		$tmpMemberActions = array();
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
		foreach ($actions['tmpMembers.'] as $k => $v) {
			switch ($v) {
				case 'TEXT' :
				case 'HTML' :
				case 'IMAGE' :
					$tmpMemberActions[] = $this->cObj->cObjGetSingle($actions['tmpMembers.'][$k], $actions['tmpMembers.'][$k.'.']);
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
		$view->setTmpMembersActions($tmpMemberActions);
		$view->setOtherActions($otherActions);

		return $view->render();
	}

	protected function saveDataAction() {
		// @TODO: localize all messages
		$communityRequest = t3lib_div::GParrayMerged('tx_community');
		$groupGateway = t3lib_div::makeInstance('tx_community_model_GroupGateway');
		$userGateway = t3lib_div::makeInstance('tx_community_model_UserGateway');
		/**
		 * @var tx_community_model_Group
		 */
		$group = $groupGateway->findRequestedGroup();
		$user  = $userGateway->findCurrentlyLoggedInUser();

		$ajaxAction = $communityRequest['ajaxAction'];
		switch ($ajaxAction) {
			case 'saveGeneral':
				if ($this->saveGeneral()) {
					$result = "{'status': 'success', 'msg': 'saved'}";
				} else {
					$result = "{'status': 'error', 'msg': 'not saved'}";
				}
			break;
			case 'saveImage':
				if ($group->isAdmin($user)) {
					$fileFunc = t3lib_div::makeInstance('t3lib_basicFileFunctions');
					$upPath = $this->configuration['applications.']['editGroup.']['uploadPath'];
					$fileName = $_FILES['tx_community']['name']['imageFile'];
					$tmpFile  = $_FILES['tx_community']['tmp_name']['imageFile'];
					$pathInfo = pathinfo($fileName);
					$dir = t3lib_div::getFileAbsFileName($upPath);
					$newName = md5($fileName) .'.'. $pathInfo['extension'];
					if (move_uploaded_file($tmpFile, $dir.$newName)) {
						t3lib_div::fixPermissions($dir.$newName);
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
				} else {
					$result = "{'status': 'error', 'msg': 'not admin'}";
				}
			break;
			case 'changeMemberStatus':
				if ($group->isAdmin($user)) {
					switch($communityRequest['do']) {
						case 'makeAdmin':
							$newAdmin = $userGateway->findById($communityRequest['memberUid']);
							if ($newAdmin instanceof tx_community_model_User) {
								$group->addAdmin($newAdmin);
								$group->removeAdmin($user);
								if ($group->save()) {
									$result = "{'status': 'success', 'msg': 'saved'}";
								} else {
									$result = "{'status': 'error', 'msg': 'not saved'}";
								}
							}
						break;
						case 'confirmRequest':
							$newMember = $userGateway->findById($communityRequest['memberUid']);
							if ($newMember instanceof tx_community_model_User) {
								if ($group->confirmMember($newMember)) {
									$result = "{'status': 'success', 'msg': 'saved'}";
								} else {
									$result = "{'status': 'error', 'msg': 'not saved'}";
								}
							}
						break;
						case 'rejectRequest':
							$newMember = $userGateway->findById($communityRequest['memberUid']);
							if ($newMember instanceof tx_community_model_User) {
								if ($group->rejectMember($newMember)) {
									$result = "{'status': 'success', 'msg': 'saved'}";
								} else {
									$result = "{'status': 'error', 'msg': 'not saved'}";
								}
							}
						break;
						case 'removeMember':
							$member = $userGateway->findById($communityRequest['memberUid']);
							if ($member instanceof tx_community_model_User) {
								if ($group->isMember($member)) {
									$group->removeMember($member);
									if ($group->save()) {
										$result = "{'status': 'success', 'msg': 'saved'}";
									} else {
										$result = "{'status': 'error', 'msg': 'not saved'}";
									}
								} else {
									$result = "{'status': 'error', 'msg': 'not a memeber of this group'}";
								}
							}
						break;
					}
				} else {
					$result = "{'status': 'error', 'msg': 'not admin'}";
				}
			break;
			case 'inviteMember':
				switch($communityRequest['do']) {
					case 'invite':
						$requestedGroup = $groupGateway->findRequestedGroup();
						if (is_null($requestedGroup)) {
							// @TODO: throw exception
							die('no group in request');
						}

						$status = 'success';
						$uidsToInvite = t3lib_div::trimExplode(';', $communityRequest['inviteUids']);
						foreach ($uidsToInvite as $uid) {
							$inviteUser = $this->userGateway->findById($uid);
							if (is_null($inviteUser)) {
								$status = 'error';
								$message = 'unknown user';
								break;
							}
							if ($this->accessManager->isFriendOfCurrentlyLoggedInUser($inviteUser)) {
								$recipients[] = $inviteUser;
								$message = 'users invited';
							} else {
								$status = 'error';
								$message = 'is not a friend';
								break;
							}
						}
						if ($status == 'success') {
							$inviteUrl = t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_getPageLink(
								$this->configuration['pages.']['groupProfile'],
								'',
								array(
									'tx_community' => array(
										'group' => $requestedGroup->getUid(),
										'profileAction' => 'joinGroup'
									)
								)
							);
							$subject = 'invite for group';
							$bodytext = "
								Einladung zur Gruppe.<br/>
								<a href=\"{$inviteUrl}\">Einladung annehmen</a>
							";
							if ($this->messageAPILoaded) {
								tx_communitymessages_API::sendSystemMessage($subject, $bodytext, $recipients);
							}
						}
						$result = "{'status': '{$status}', 'msg': '{$message}'}";
					break;
					case 'search':
					default:
						$searchTerm = t3lib_div::_GP('q');
						$friends = $this->userGateway->findFriends();
						$returnData = array();
						if (count($friends)) {
							foreach ($friends as $friend) {
								if (strpos(strtolower($friend->getNickname()), strtolower($searchTerm)) !== false) {
									$returnData[] = $friend->getNickname().'|'.$friend->getUid();
								}
							}
						}
						if (count($returnData)) {
							echo implode("\n", $returnData) . "\n";
						} else {
							echo '';
						}
						die();
					break;
				}
			break;
			default:
				$result = "{'status': 'error', 'msg': 'no ajax action'}";
			break;
		}
		echo $result;
		die();
	}

	protected function saveGeneral() {
		$communityRequest = t3lib_div::GParrayMerged('tx_community');
		$groupGateway = t3lib_div::makeInstance('tx_community_model_GroupGateway');
		$userGateway = t3lib_div::makeInstance('tx_community_model_UserGateway');
		/**
		 * @var tx_community_model_Group
		 */
		$group = $groupGateway->findRequestedGroup();
		$user  = $userGateway->findCurrentlyLoggedInUser();

		if ($group->isAdmin($user)) {
			$group->setTitle($communityRequest['group_title']);
			$group->setDescription($communityRequest['group_description']);
			$group->setTX_community_public($isPublic);
			if ($group->save()) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_editgroupapplication.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_editgroupapplication.php']);
}

?>