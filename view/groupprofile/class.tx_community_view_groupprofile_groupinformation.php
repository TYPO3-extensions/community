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

require_once($GLOBALS['PATH_community'] . 'classes/viewhelper/class.tx_community_viewhelper_lll.php');
require_once($GLOBALS['PATH_community'] . 'classes/viewhelper/class.tx_community_viewhelper_widget.php');

/**
 * group information widget view
 *
 * @author	Frank Naegler <typo3@naegler.net>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_view_groupprofile_GroupInformation extends tx_community_view_AbstractView  {

	/**
	 * The group model used to render this view
	 *
	 * @var tx_community_model_Group
	 */
	protected $groupModel;

	public function setGroupModel(tx_community_model_Group $group) {
		$this->groupModel = $group;
	}

	public function render() {
		$templateClass = t3lib_div::makeInstanceClassName('tx_community_Template');
		$template = new $templateClass(
			t3lib_div::makeInstance('tslib_cObj'),
			$this->templateFile,
			'group_information'
		);

		$template->addViewHelper(
			'lll',
			'tx_community_viewhelper_Lll',
			array(
				'languageFile' => $GLOBALS['PATH_community'] . 'lang/locallang_groupprofile_groupinformation.xml',
				'llKey'        => $this->languageKey
			)
		);

		$template->addViewHelper('widget','tx_community_viewhelper_Widget');

		$template->addVariable('group', $this->groupModel);

		return $template->render();
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/view/groupprofile/class.tx_community_view_groupprofile_groupinformation.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/view/groupprofile/class.tx_community_view_groupprofile_groupinformation.php']);
}

?>