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

require_once($GLOBALS['PATH_community'] . 'classes/class.tx_community_localizationmanager.php');
require_once($GLOBALS['PATH_community'] . 'classes/viewhelper/class.tx_community_viewhelper_lll.php');
require_once($GLOBALS['PATH_community'] . 'model/class.tx_community_model_usergateway.php');


/**
 * index view for the edit group application
 *
 * @author	Frank Nägler <typo3@naegler.net>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_view_listGroups_Index extends tx_community_view_AbstractView {

	protected $groups;
	/**
	 * @var tx_community_model_Group
	 */
	protected $group;
	/**
	 * @var tx_community_LocalizationManager
	 */
	protected $llManager;
	protected $groupLink;

	public function setGroups($groups) {
		$this->groups = $groups;
	}

	public function setGroupDetailLink($link) {
		$this->groupLink = $link;
	}

	public function render() {
		$llMangerClass = t3lib_div::makeInstanceClassName('tx_community_LocalizationManager');
		$this->llManager = call_user_func(array($llMangerClass, 'getInstance'), 'EXT:community/lang/locallang_listgroups.xml',	$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_community.']);

		$templateClass = t3lib_div::makeInstanceClassName('tx_community_Template');
		$template = new $templateClass(
			t3lib_div::makeInstance('tslib_cObj'),
			$this->templateFile,
			'list_groups'
		);
		/* @var $template tx_community_Template */

		$template->addViewHelper(
			'lll',
			'tx_community_viewhelper_Lll',
			array(
				'languageFile' => $GLOBALS['PATH_community'] . 'lang/locallang_listgroups.xml',
				'llKey'        => $this->languageKey
			)
		);

		foreach ($this->groups as $group) {
			$tmp = array(
				'uid'         => $group->getUid(),
				'title'       => $group->getTitle(),
				'description' => $group->getDescription(),
				'url'         => $this->getGroupDetailLink($group->getUid()),
				'image'		  => $group->getHtmlImage()
			);
			$loopGroups[] = $tmp;
		}

		$template->addLoop('groups', 'group', $loopGroups);

		return $template->render();
	}

	protected function getGroupDetailLink($uid) {
		return str_replace('%25UID%25', $uid, $this->groupLink);
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/view/listgroups/class.tx_community_view_listgroups_index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/view/listgroups/class.tx_community_view_listgroups_index.php']);
}

?>