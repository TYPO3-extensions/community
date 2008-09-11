<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Ingo Renner <ingo@typo3.org>
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


require_once($GLOBALS['PATH_community'] . 'interfaces/interface.tx_community_view.php');
require_once($GLOBALS['PATH_community'] . 'classes/class.tx_community_template.php');
require_once($GLOBALS['PATH_community'] . 'classes/viewhelper/class.tx_community_viewhelper_lll.php');


/**
 * the edit relationship view for the accoring user profile action
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_view_userprofile_EditRelationship implements tx_community_View {

	protected $templateFile;
	protected $languageKey;
	protected $friendUser;
	protected $relationshipOptions;
	protected $formAction;

	public function setTemplateFile($templateFile) {
		$this->templateFile = $templateFile;
	}

	public function setLanguageKey($languageKey) {
		$this->languageKey = $languageKey;
	}

	public function setFriendUser(tx_community_model_User $friendUser) {
		$this->friendUser = $friendUser;
	}

	public function setRelationshipOptions(array $relationshipOptions) {
		$this->relationshipOptions = $relationshipOptions;
	}

	public function setFormAction($formAction) {
		$this->formAction = $formAction;
	}

	/**
	 * Enter description here...
	 *
	 * @param	array	array of role IDs the friend is assigned to already
	 */
	public function setRelationships(array $relationships) {
		$this->friendRelationships = $relationships;
	}

	public function render() {
		$templateClass = t3lib_div::makeInstanceClassName('tx_community_Template');
		$template = new $templateClass(
			t3lib_div::makeInstance('tslib_cObj'),
			$this->templateFile,
			'edit_relationship'
		);

		$template->addVariable('friend', $this->friendUser);
		$template->addVariable('form', array('action' => $this->formAction));
		$template->addLoop('friendroles', $this->relationshipOptions);

		$template->addViewHelper(
			'lll',
			'tx_community_viewhelper_Lll',
			array(
				'languageFile' => $GLOBALS['PATH_community'] . 'lang/locallang_userprofile_profileactions.xml',
				'llKey'        => $this->languageKey
			)
		);

		return $template->render();
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/view/userprofile/class.tx_community_view_userprofile_editrelationship.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/view/userprofile/class.tx_community_view_userprofile_editrelationship.php']);
}

?>