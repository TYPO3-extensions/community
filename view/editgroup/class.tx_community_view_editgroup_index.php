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

require_once($GLOBALS['PATH_community'] . 'classes/viewhelper/class.tx_community_viewhelper_lll.php');
require_once($GLOBALS['PATH_community'] . 'model/class.tx_community_model_usergateway.php');


/**
 * index view for the edit group application
 *
 * @author	Frank Nägler <typo3@naegler.net>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_view_editGroup_Index extends tx_community_view_AbstractView {

	protected $formAction;
	protected $image;
	protected $adminActions = array();
	protected $tmpMembersActions = array();
	protected $otherActions = array();
	/**
	 * @var tx_community_model_Group
	 */
	protected $group;
	/**
	 * @var tx_community_model_GroupGateway
	 */
	protected $groupGateway;
	/**
	 * @var tx_community_LocalizationManager
	 */
	protected $llManager;

	public function setFormAction($formAction) {
		$this->formAction = $formAction;
	}

	public function setImage($image) {
		$this->image = $image;
	}

	public function setAdminActions($actions) {
		$this->adminActions = $actions;
	}

	public function setTmpMembersActions($actions) {
		$this->tmpMembersActions = $actions;
	}

	public function setOtherActions($actions) {
		$this->otherActions = $actions;
	}

	public function render() {
		$llMangerClass = t3lib_div::makeInstanceClassName('tx_community_LocalizationManager');
		$this->llManager = call_user_func(array($llMangerClass, 'getInstance'), 'EXT:community/lang/locallang_editgroup.xml',	$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_community.']);

		$this->groupGateway = t3lib_div::makeInstance('tx_community_model_GroupGateway');
		$this->group = $this->groupGateway->findCurrentGroup();

		$templateClass = t3lib_div::makeInstanceClassName('tx_community_Template');
		$template = new $templateClass(
			t3lib_div::makeInstance('tslib_cObj'),
			$this->templateFile,
			'group_settings'
		);
		/* @var $template tx_community_Template */

		$template->addViewHelper(
			'lll',
			'tx_community_viewhelper_Lll',
			array(
				'languageFile' => $GLOBALS['PATH_community'] . 'lang/locallang_editgroup.xml',
				'llKey'        => $this->languageKey
			)
		);

		$template->addSubpart('general_settings', $this->renderGeneralSettings());
		$template->addSubpart('image_settings', $this->renderImageSettings());
		$template->addSubpart('member_settings', $this->renderMemberSettings());
		$template->addSubpart('invite_member', $this->renderInviteMember());
		$template->addVariable('form', array(
			'action' => $this->formAction,
			'group_uid'	=> $this->group->getUid()
		));
		$template->addVariable('msg', array(
			'wait'				=> $this->llManager->getLL('msg_please_wait')
		));

		return $template->render();
	}

	protected function renderGeneralSettings() {
		$templateClass = t3lib_div::makeInstanceClassName('tx_community_Template');
		$template = new $templateClass(
			t3lib_div::makeInstance('tslib_cObj'),
			$this->templateFile,
			'general_settings'
		);
		/* @var $template tx_community_Template */

		$template->addVariable('form', array(
			'action' => $this->formAction,
			'group_uid'	=> $this->group->getUid()
		));

		$template->addVariable('value', array(
			'group_title'		=> $this->group->getTitle(),
			'group_description'	=> $this->group->getDescription(),
			'group_public'		=> 1,
		));

		$template->addVariable('checked', array(
			'public'		=> (($this->group->getTX_community_public()) ? 'checked="checked" ' : '')
		));

		return $template->render();
	}

	protected function renderImageSettings() {
		$templateClass = t3lib_div::makeInstanceClassName('tx_community_Template');
		$template = new $templateClass(
			t3lib_div::makeInstance('tslib_cObj'),
			$this->templateFile,
			'image_settings'
		);
		/* @var $template tx_community_Template */

		$template->addVariable('group', array(
			'image'		=> $this->image
		));

		return $template->render();
	}

	protected function renderMemberSettings() {
		$templateClass = t3lib_div::makeInstanceClassName('tx_community_Template');
		$template = new $templateClass(
			t3lib_div::makeInstance('tslib_cObj'),
			$this->templateFile,
			'member_settings'
		);
		/* @var $template tx_community_Template */
		$template->addVariable('form', array(
			'action' => $this->formAction,
			'group_uid'	=> $this->group->getUid()
		));

		$members = $this->group->getAllTempMembers();
		$loopTempMembers = array();
		foreach ($members as $member) {
			$tmp = array(
				'uid'     => $member->getUid(),
				'name'    => $member->getNickname(),
				'status'  => $this->getMemberStatus($member),
				'actions' => $this->getActionsForMember($member),
			);
			$loopTempMembers[] = $tmp;
		}

		$template->addLoop('tempMembers', 'tempMember', $loopTempMembers);

		$members = $this->group->getAllMembers();
		$loopMembers = array();
		foreach ($members as $member) {
			$tmp = array(
				'uid'     => $member->getUid(),
				'name'    => $member->getNickname(),
				'status'  => $this->getMemberStatus($member),
				'actions' => $this->getActionsForMember($member),
			);
			$loopMembers[] = $tmp;
		}

		$template->addLoop('members', 'member', $loopMembers);

		return $template->render();
	}

	protected function getActionsForMember(tx_community_model_User $member) {
		if ($this->group->isAdmin($member)) {
			$return = implode(' ', $this->adminActions);
		} elseif ($this->group->isTempMember($member)) {
			$return = implode(' ', $this->tmpMembersActions);
		} else {
			$return = implode(' ', $this->otherActions);
		}
		$return = str_replace('%UID%', $member->getUid(), $return);
		return $return;
	}

	protected function getMemberStatus(tx_community_model_User $member) {
		if ($this->group->isAdmin($member)) {
			$return = $this->llManager->getLL('label_status_admin');
		} elseif ($this->group->isTempMember($member)) {
			$return = $this->llManager->getLL('label_status_tmpmember');
		} else {
			$return = $this->llManager->getLL('label_status_member');
		}
		return $return;
	}

	protected function renderInviteMember() {
		$templateClass = t3lib_div::makeInstanceClassName('tx_community_Template');
		$template = new $templateClass(
			t3lib_div::makeInstance('tslib_cObj'),
			$this->templateFile,
			'invite_member'
		);
		/* @var $template tx_community_Template */

		return $template->render();
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/view/editgroup/class.tx_community_view_editgroup_index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/view/editgroup/class.tx_community_view_editgroup_index.php']);
}

?>