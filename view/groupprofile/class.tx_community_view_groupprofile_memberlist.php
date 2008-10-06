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
require_once($GLOBALS['PATH_community'] . 'classes/viewhelper/class.tx_community_viewhelper_ts.php');
require_once($GLOBALS['PATH_community'] . 'classes/viewhelper/class.tx_community_viewhelper_link.php');

/**
 * member list widget view
 *
 * @author	Frank Nägler <typo3@naegler.net>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_view_groupprofile_MemberList extends tx_community_view_AbstractView {

	/**
	 * The group model used to render this view
	 *
	 * @var tx_community_model_Group
	 */
	protected $groupModel;
	protected $configuration;
	protected $userDetailLink;

	public function setGroupModel(tx_community_model_Group $group) {
		$this->groupModel = $group;
	}

	public function setConfiguration($configuration) {
		$this->configuration = $configuration;
	}

	public function render() {
		$templateClass = t3lib_div::makeInstanceClassName('tx_community_Template');
		$template = new $templateClass(
			t3lib_div::makeInstance('tslib_cObj'),
			$this->templateFile,
			'member_list'
		);

		$template->addViewHelper(
			'lll',
			'tx_community_viewhelper_Lll',
			array(
				'languageFile' => $GLOBALS['PATH_community'] . 'lang/locallang_groupprofile_groupinformation.xml',
				'llKey'        => $this->languageKey
			)
		);

		$template->addViewHelper(
			'ts',
			'tx_community_viewhelper_Ts'
		);

		$template->addViewHelper(
			'link',
			'tx_community_viewhelper_Link'
		);

		$template->addVariable('group', $this->groupModel);

		$members = $this->groupModel->getMembers();
		debug($members);
		foreach ($members as $member) {
			$imgConf = $this->configuration['applications.']['groupProfile.']['widgets.']['memberList.']['userImage.'];
			$imgConf['file'] = (strlen($member->getImage()) > 0) ? $member->getImage() : $imgConf['file'];
			$cObj = t3lib_div::makeInstance('tslib_cObj');
			$genImage = $cObj->cObjGetSingle('IMAGE', $imgConf);
			$member->setHTMLImage($genImage);
		}
		$template->addLoop('members', 'member', $members);

		return $template->render();
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/view/groupprofile/class.tx_community_view_groupprofile_memberlist.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/view/groupprofile/class.tx_community_view_groupprofile_memberlist.php']);
}

?>