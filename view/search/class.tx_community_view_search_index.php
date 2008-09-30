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
 * profile search advanced form view (index)
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_view_search_Index implements tx_community_View {

	protected $formAction;
	protected $formModel;

	public function setFormAction($formAction) {
		$this->formAction = $formAction;
	}

	public function setFormModel(array $formModel) {
		$this->formModel = $formModel;
	}

	public function render() {
		$templateClass = t3lib_div::makeInstanceClassName('tx_community_Template');
		$template = new $templateClass(
			t3lib_div::makeInstance('tslib_cObj'),
			$this->templateFile,
			'advanced_search_form'
		);
		/* @var $template tx_community_Template */

		$template->addViewHelper(
			'lll',
			'tx_community_viewhelper_Lll',
			array(
				'languageFile' => $GLOBALS['PATH_community'] . 'lang/locallang_search.xml',
				'llKey'        => $this->languageKey
			)
		);

		$template->addSubpart('search_field_group', $this->renderSearchFieldGroups());
		$template->addVariable('form', array('action' => $this->formAction));

		return $template->render();
	}

	protected function renderSearchFieldGroups() {
		$fieldGroups = '';
		$templateClass = t3lib_div::makeInstanceClassName('tx_community_Template');

		foreach ($this->formModel as $groupModel) {
			$template = new $templateClass(
				t3lib_div::makeInstance('tslib_cObj'),
				$this->templateFile,
				'search_field_group'
			);

			$template->addMarker('group_label', $groupModel['label']);
			$template->addSubpart('search_fields', $this->renderSearchFields($groupModel));

			$fieldGroups .= $template->render();
		}

		return $fieldGroups;
	}

	protected function renderSearchFields(array $groupModel) {
		$searchFields = '';
		$templateClass = t3lib_div::makeInstanceClassName('tx_community_Template');

		foreach ($groupModel['fields'] as $fieldName => $fieldConfiguration) {
			$template = new $templateClass(
				t3lib_div::makeInstance('tslib_cObj'),
				$this->templateFile,
				'search_field'
			);

			$searchFieldMarkers = array(
				'id' => 'tx_community-profileSearch-' . $fieldName,
				'name' => 'tx_community[profileSearch][' . $fieldName . ']',
				'label' => $fieldConfiguration['label']
			);

			if ($fieldConfiguration['type'] == 'text') {
				$template->addSubpart('search_field_select', '');
			} else if ($fieldConfiguration['type'] == 'select') {
				$selectFieldOptions = '';

				foreach ($fieldConfiguration['selectOptions.'] as $option) {
					list($optionValue, $optionLabel) = explode('|', $option);
					$selectFieldOptions .= '<option value="' . $optionValue . '">###LLL:' . $optionLabel . '###</option>';
				}

				$searchFieldMarkers['options'] = $selectFieldOptions;
				$template->addSubpart('search_field_text', '');
			}

			$template->addVariable(
				'search_field',
				$searchFieldMarkers
			);

			$searchFields .= $template->render();
		}

		return $searchFields;

	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/view/search/class.tx_community_view_search_index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/view/search/class.tx_community_view_search_index.php']);
}

?>