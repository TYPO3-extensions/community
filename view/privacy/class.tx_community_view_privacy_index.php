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

require_once($GLOBALS['PATH_community'] . 'classes/class.tx_community_template.php');
require_once($GLOBALS['PATH_community'] . 'classes/viewhelper/class.tx_community_viewhelper_lll.php');

/**
 * index view for the privacy community application
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_view_privacy_Index implements tx_community_View {

	protected $roles;
	protected $accessControlModel;
	protected $allowedRules;
	protected $formAction;

	public function setRoles(array $roles) {
		$this->roles = $roles;
	}

	public function setAccessControlModel(array $accessControlModel) {
		$this->accessControlModel = $accessControlModel;
	}

	public function setAllowedRules($allowedRules) {
		$this->allowedRules = $allowedRules;
	}

	public function setFormAction($formAction) {
		$this->formAction = $formAction;
	}

	public function render() {
		$templateClass = t3lib_div::makeInstanceClassName('tx_community_Template');
		$template = new $templateClass(
			t3lib_div::makeInstance('tslib_cObj'),
			$this->templateFile,
			'privacy_settings'
		);
		/* @var $template tx_community_Template */

		$template->addViewHelper(
			'lll',
			'tx_community_viewhelper_Lll',
			array(
				'languageFile' => $GLOBALS['PATH_community'] . 'lang/locallang_privacy.xml',
				'llKey'        => $this->languageKey
			)
		);

		$template->addSubpart('setting_groups', $this->renderSettingGroups());
		$template->addVariable('form', array('action' => $this->formAction));

		return $template->render();
	}

	protected function renderSettingGroups() {
		$settingGroups = '';
		$templateClass = t3lib_div::makeInstanceClassName('tx_community_Template');

		foreach($this->accessControlModel as $applicationName => $applicationAccessSettings) {
			$template = new $templateClass(
				t3lib_div::makeInstance('tslib_cObj'),
				$this->templateFile,
				'setting_groups'
			);
			/* @var $template tx_community_Template */

			$template->addVariable('setting_group', array('name' => $applicationName));
			$template->addSubpart(
				'settings',
				$this->renderApplicationSettings($applicationName)
			);


			$settingGroups .= $template->render();
		}

		return $settingGroups;
	}

	protected function renderApplicationSettings($applicationName) {
		$applicationSettings = '';
		$templateClass = t3lib_div::makeInstanceClassName('tx_community_Template');

		foreach ($this->accessControlModel[$applicationName] as $resourceActionControl => $label) {
			$template = new $templateClass(
				t3lib_div::makeInstance('tslib_cObj'),
				$this->templateFile,
				'settings'
			);
			/* @var $template tx_community_Template */

			$template->addVariable('setting', array('description' => $label));
			$template->addSubpart(
				'setting_options',
				$this->renderSettingOptions(
					$applicationName,
					$resourceActionControl
				)
			);

			$applicationSettings .= $template->render();
		}

		return $applicationSettings;
	}

	protected function renderSettingOptions($applicationName, $controlName) {
		$templateClass = t3lib_div::makeInstanceClassName('tx_community_Template');
		$template = new $templateClass(
			t3lib_div::makeInstance('tslib_cObj'),
			$this->templateFile,
			'setting_options'
		);
		/* @var $template tx_community_Template */

		$roleOptions = array();
		foreach ($this->roles as $roleId => $roleRow) {

			$checked = '';
			if (
				isset($this->allowedRules[$applicationName][$controlName]) &&
				in_array($roleRow['uid'], $this->allowedRules[$applicationName][$controlName])
			) {
				$checked = 'checked="checked"';
			}

			$roleOptions[] = array(
				'field_name' => 'tx_community[privacy][' . $applicationName . '][' . $controlName . '][' .$roleId . ']',
				'field_id' => 'tx_community_' . $applicationName . '_' . $controlName . '_' . $roleId,
				'field_checked' => $checked,
				'label' => $roleRow['name']
			);
		}

		$template->addLoop('setting_options', 'setting_option', $roleOptions);
		$applicationSettings .= $template->render();

		return $applicationSettings;
	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/view/privacy/class.tx_community_view_privacy_index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/view/privacy/class.tx_community_view_privacy_index.php']);
}

?>