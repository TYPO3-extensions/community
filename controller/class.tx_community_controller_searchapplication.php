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

require_once($GLOBALS['PATH_community'] . 'controller/class.tx_community_controller_abstractcommunityapplication.php');
require_once($GLOBALS['PATH_community'] . 'view/search/class.tx_community_view_search_index.php');

/**
 * User search application
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_controller_SearchApplication extends tx_community_controller_AbstractCommunityApplication {

	/**
	 * constructor for class tx_community_controller_SearchApplication
	 */
	public function __construct() {
		parent::__construct();

		$this->prefixId = 'tx_community_controller_SearchApplication';
		$this->scriptRelPath = 'controller/class.tx_community_controller_searchapplication.php';
		$this->name = 'Search';
	}

	public function execute() {
		$content = '';

		$applicationManagerClass = t3lib_div::makeInstanceClassName('tx_community_ApplicationManager');
		$applicationManager      = call_user_func(array($applicationManagerClass, 'getInstance'));
		/* @var $applicationManager tx_community_ApplicationManager */

		$widgetName = $this->pi_getFFvalue(
			$this->data['pi_flexform'],
			'widget'
		);

		if (!empty($widgetName)) {
			$content = $this->executeWidget(
				$applicationManager,
				$widgetName
			);
		} else {
			$content = $this->executeApllicationAction($applicationManager);
		}

		return $content;
	}

	protected function executeWidget(tx_community_ApplicationManager $applicationManager, $widgetName) {
		$content = '';

		$widgetConfiguration = $applicationManager->getWidgetConfiguration(
			$this->name,
			$widgetName
		);

		$widget = t3lib_div::getUserObj($widgetConfiguration['classReference']);
		/* @var $widget tx_community_CommunityApplicationWidget */
		$widget->initialize($this->data, $this->conf);
		$widget->setCommunityApplication($this);

		$content = $widget->execute();

		return $content;
	}

	protected function executeApllicationAction(tx_community_ApplicationManager $applicationManager) {
		$content = '';
		$communityRequest = t3lib_div::GParrayMerged('tx_community');

		$applicationConfiguration = $applicationManager->getApplicationConfiguration(
			$this->getName()
		);

			// dispatch
		if (!empty($communityRequest['searchAction'])
			&& method_exists($this, $communityRequest['searchAction'] . 'Action')
			&& in_array($communityRequest['searchAction'], $applicationConfiguration['actions'])
		) {
				// call a specifically requested action
			$actionName = $communityRequest['searchAction'] . 'Action';
			$content = $this->$actionName();
		} else {
				// call the default action
			$defaultActionName = $applicationConfiguration['defaultAction'] . 'Action';
			$content = $this->$defaultActionName();
		}

		return $content;
	}

	/**
	 * renders an advanced search input form
	 *
	 * @return unknown
	 */
	public function indexAction() {
		$view = t3lib_div::makeInstance('tx_community_view_search_Index');
		/* @var $view tx_community_view_privacy_Index */
		$view->setTemplateFile($this->configuration['applications.']['search.']['templateFile']);
		$view->setLanguageKey($this->LLkey);

		$formAction = $this->pi_getPageLink(
			$this->configuration['pages.']['searchResults']
		);
		$view->setFormAction($formAction);

		$searchFormModel = $this->getSearchFormModel();
		$view->setFormModel($searchFormModel);

		return $view->render();
	}

	protected function getSearchFormModel() {
		$formModel = array();
		$searchApplicationConfiguration = $this->conf['applications.']['search.'];

		foreach ($searchApplicationConfiguration['searchFieldGroups.'] as $searchFieldGroupConfiguration) {
			$groupFields = array();

			$groupFieldNames = t3lib_div::trimExplode(',', $searchFieldGroupConfiguration['fields'], true);
			foreach ($groupFieldNames as $groupFieldName) {
				$groupFields[$groupFieldName] = $searchApplicationConfiguration['searchFields.'][$groupFieldName . '.'];
			}


			$formModel[] = array(
				'label' => $searchFieldGroupConfiguration['label'],
				'fields' => $groupFields
			);
		}

		return $formModel;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_searchapplication.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_searchapplication.php']);
}

?>