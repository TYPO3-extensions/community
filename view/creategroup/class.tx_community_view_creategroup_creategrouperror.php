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
require_once($GLOBALS['PATH_community'] . 'model/class.tx_community_model_usergateway.php');


/**
 * index view for the edit group application
 *
 * @author	Frank Naegler <typo3@naegler.net>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_view_creategroup_CreateGroupError extends tx_community_view_AbstractView {

	/**
	 * @var tx_community_model_Group
	 */
	protected $group;
	protected $message;

	public function setFormAction($formAction) {
		$this->formAction = $formAction;
	}

	public function setMessage($message) {
		$this->message = $message;
	}

	public function render() {
		$llMangerClass = t3lib_div::makeInstanceClassName('tx_community_LocalizationManager');
		$llManager = call_user_func(array($llMangerClass, 'getInstance'), 'EXT:community/lang/locallang_creategroup.xml', $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_community.']);

		$templateClass = t3lib_div::makeInstanceClassName('tx_community_Template');
		$template = new $templateClass(
			t3lib_div::makeInstance('tslib_cObj'),
			$this->templateFile,
			'create_group'
		);
		/* @var $template tx_community_Template */

		$template->addViewHelper(
			'lll',
			'tx_community_viewhelper_Lll',
			array(
				'languageFile' => $GLOBALS['PATH_community'] . 'lang/locallang_creategroup.xml',
				'llKey'        => $this->languageKey
			)
		);

		$template->addVariable('msg', array(
			'message' => $this->message,
		));

		return $template->render();
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/view/creategroup/class.tx_community_view_creategroup_creategrouperror.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/view/creategroup/class.tx_community_view_creategroup_creategrouperror.php']);
}

?>