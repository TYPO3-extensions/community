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

require_once($GLOBALS['PATH_community'] . 'controller/class.tx_community_controller_groupprofileapplication.php');
require_once($GLOBALS['PATH_community'] . 'model/class.tx_community_model_groupgateway.php');
require_once($GLOBALS['PATH_community'] . 'view/editgroup/class.tx_community_view_editgroup_index.php');
require_once($GLOBALS['PATH_community'] . 'classes/class.tx_community_localizationmanager.php');

/**
 * Edit Group Application Controller
 *
 * @author	Frank Naegler <typo3@naegler.net>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_controller_EditGroupApplication extends tx_community_controller_GroupProfileApplication implements tx_community_acl_AclResource {

	protected $messageAPILoaded = false;
	protected $accessManager    = null;
	/**
	 * @var tx_community_LocalizationManager
	 */
	protected $llManager;
	
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
		$this->getRequestedGroup();

		$llMangerClass = t3lib_div::makeInstanceClassName('tx_community_LocalizationManager');
		$this->llManager = call_user_func(array($llMangerClass, 'getInstance'), 'EXT:community/lang/locallang_editgroup.xml',	$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_community.']);
	}

	/**
	 * does an initial access check
	 *
	 * @return	void
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	protected function checkAccess() {
			// TODO should be moved to some central place, should be made extendable
		if (is_null($this->requestedGroup)) {
				// @TODO throw Exception
			die('no group id given');
		}

		if ($this->getRequestingUser()->getUid() === 0) {
				// @TODO throw Exception
			die('no user logged in');
		}

		if (!$this->requestedGroup->isAdmin($this->getRequestingUser())) {
				// @TODO throw Exception
			die('not an admin of this group');
		}
	}

		// TODO refactor this method
	public function indexAction() {
		$this->checkAccess();

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

		$imagePath = (strlen($this->requestedGroup->getImage())) ? $this->requestedGroup->getImage() : $this->configuration['applications.']['editGroup.']['defaultIcon'];
		$imgConf['file'] = $imagePath;
		$cObj = t3lib_div::makeInstance('tslib_cObj');
		$view->setImage($cObj->cObjGetSingle($this->configuration['applications.']['editGroup.']['previewImage'], $imgConf));

			// make actions
		$actions = $this->configuration['applications.']['editGroup.']['memberlist.']['actions.'];

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

		// TODO refactor this method
	public function saveDataAction() {
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
					$result = "{'status': 'success', 'msg': '{$this->llManager->getLL('msg_saved')}'}";
				} else {
					$result = "{'status': 'error', 'msg': '{$this->llManager->getLL('msg_not_saved')}'}";
				}
			break;
			case 'saveVideo':
				if ($this->saveVideo()) {
					$result = "{'status': 'success', 'msg': '{$this->llManager->getLL('msg_saved')}'}";
				} else {
					$result = "{'status': 'error', 'msg': '{$this->llManager->getLL('msg_not_saved')}'}";
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
						$group->setImage($newName);
						if ($group->save()) {
							$imgConf = $this->configuration['applications.']['editGroup.']['previewImage.'];
							$imgConf['file'] = $upPath.$newName;
							$cObj = t3lib_div::makeInstance('tslib_cObj');
							$genImage = $cObj->cObjGetSingle('IMG_RESOURCE', $imgConf);
							list($width,$height) = getimagesize($genImage);
							$result = "{'status': 'success', 'msg': '{$this->llManager->getLL('msg_uploaded')}', 'newImage': '{$genImage}', 'newWidth': '{$width}', 'newHeight': '{$height}'}";
						} else {
							$result = "{'status': 'error', 'msg': '{$this->llManager->getLL('msg_error_while_save')}'}";
						}
					} else {
						$result = "{'status': 'error', 'msg': '{$this->llManager->getLL('msg_error_cant_upload')}'}";
					}
				} else {
					$result = "{'status': 'error', 'msg': '{$this->llManager->getLL('msg_not_admin')}'}";
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
									$result = "{'status': 'success', 'msg': '{$this->llManager->getLL('msg_saved')}'}";
								} else {
									$result = "{'status': 'error', 'msg': '{$this->llManager->getLL('msg_not_saved')}'}";
								}
							}
						break;
						case 'confirmRequest':
							$newMember = $userGateway->findById($communityRequest['memberUid']);
							if ($newMember instanceof tx_community_model_User) {
								if ($group->confirmMember($newMember)) {
									$result = "{'status': 'success', 'msg': '{$this->llManager->getLL('msg_saved')}'}";
								} else {
									$result = "{'status': 'error', 'msg': '{$this->llManager->getLL('msg_not_saved')}'}";
								}
							}
						break;
						case 'rejectRequest':
							$newMember = $userGateway->findById($communityRequest['memberUid']);
							if ($newMember instanceof tx_community_model_User) {
								if ($group->rejectMember($newMember)) {
									$result = "{'status': 'success', 'msg': '{$this->llManager->getLL('msg_saved')}'}";
								} else {
									$result = "{'status': 'error', 'msg': 'not {$this->llManager->getLL('msg_not_saved')}'}";
								}
							}
						break;
						case 'removeMember':
							$member = $userGateway->findById($communityRequest['memberUid']);
							if ($member instanceof tx_community_model_User) {
								if ($group->isMember($member)) {
									$group->removeMember($member);
									if ($group->save()) {
										$result = "{'status': 'success', 'msg': '{$this->llManager->getLL('msg_saved')}'}";
									} else {
										$result = "{'status': 'error', 'msg': '{$this->llManager->getLL('msg_not_saved')}'}";
									}
								} else {
									$result = "{'status': 'error', 'msg': '{$this->llManager->getLL('msg_no_member_of_group')}'}";
								}
							}
						break;
					}
				} else {
					$result = "{'status': 'error', 'msg': '{$this->llManager->getLL('msg_not_admin')}'}";
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
						$valuesToInvite = t3lib_div::trimExplode(';', $communityRequest['inviteUids']);
						if(count($uidsToInvite)<1){
						    $valuesToInvite = t3lib_div::trimExplode(',', $communityRequest['invite_search']);
						}
						foreach ($valuesToInvite as $value) {
						    if(!$value){
							continue;
						    }
							$inviteUser = $this->userGateway->findById($value);
							if (is_null($inviteUser)) {
								$inviteUser = $this->userGateway->findByNickName($value);
								if (is_null($inviteUser)) {
									$status = 'error';
									$message = $this->llManager->getLL('msg_unknown_user');
									break;
								}
							}
							if ($this->accessManager->isFriendOfCurrentlyLoggedInUser($inviteUser)) {
								$recipients[] = $inviteUser;
								$message = $this->llManager->getLL('msg_users_invited');
							} else {
								$status = 'error';
								$message = $this->llManager->getLL('msg_is_not_friend');
								break;
							}
						}
						if ($status == 'success') {
							$inviteHash = md5($requestedGroup->getUid() . $requestedGroup->getCrdate() . $inviteUser->getUid());
							$inviteUrl = t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_getPageLink(
								$this->configuration['pages.']['groupProfile'],
								'',
								array(
									'tx_community' => array(
										'group' => $requestedGroup->getUid(),
										'profileActionsAction' => 'joinGroup',
										'inviteHash' => $inviteHash
									)
								)
							);
							$subject = $this->llManager->getLL('invite_subject');
							$bodytext = $this->llManager->getLL('invite_bodytext');
							$bodytext = str_replace('###ACCEPT_LINK###', $inviteUrl, $bodytext);
							$bodytext = str_replace('###GROUP_NAME###', $requestedGroup->getName(), $bodytext);
							
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
							$result = array(
								'status' => 'success',
								'data' => $returnData
							);
							//echo implode("\n", $returnData) . "\n";
							echo json_encode($result);
						} else {
							$result = array(
								'status' => 'noresults',
								'data' => '|'
							);
							echo json_encode($result);
						}
						die();
					break;
				}
			break;
			default:
				$result = "{'status': 'error', 'msg': '{$this->llManager->getLL('msg_noajax_action')}'}";
			break;
		}
		echo $result;
		die();
	}

	// TODO refactor this method
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
			$group->setName($communityRequest['groupName']);
			$group->setDescription($communityRequest['groupDescription']);
			$group->setGrouptype($communityRequest['groupType']);
			if ($group->save()) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	// TODO refactor this method
	protected function saveVideo() {
		$communityRequest = t3lib_div::GParrayMerged('tx_community');
		$groupGateway = t3lib_div::makeInstance('tx_community_model_GroupGateway');
		$userGateway = t3lib_div::makeInstance('tx_community_model_UserGateway');
		/**
		 * @var tx_community_model_Group
		 */
		$group = $groupGateway->findRequestedGroup();
		$user  = $userGateway->findCurrentlyLoggedInUser();

		if ($group->isAdmin($user)) {
			if ($communityRequest['groupVideotype'] == '') {
				$communityRequest['groupVideo'] = '';
				if (t3lib_extMgm::isLoaded('community_flexiblelayout')) {
					require_once(t3lib_extMgm::extPath('community_flexiblelayout').'classes/class.tx_communityflexiblelayout_layoutmanager.php');
					
					$conf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_communityflexiblelayout.']['controller.']['dashboard.'];
					
					$profile = tx_community_ProfileFactory::createProfile($conf['profileType']);
					
					$layoutManager = new tx_communityflexiblelayout_LayoutManager();
					$layoutManager->putWidgetToClipboard($conf['communityID'], $conf['profileType'], $profile->getUid(), 'communityVideoWidget');
				}
			}
			$group->setVideo($communityRequest['groupVideo']);
			$group->setVideotype($communityRequest['groupVideotype']);
			if ($group->save()) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}	

	/**
	 * returns the Resource identifier
	 *
	 * @return string
	 */
	public function getResourceId() {
		return $this->name . '_update_' . $this->getRequestedGroup()->getUid();
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_editgroupapplication.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_editgroupapplication.php']);
}

?>