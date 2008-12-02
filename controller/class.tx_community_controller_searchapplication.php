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
		$this->name = 'search';
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

	public function searchAction() {
		$feUserTcaColumns    = $GLOBALS['TCA']['fe_users']['columns'];
		$communityRequest    = t3lib_div::GParrayMerged('tx_community');
		$searchConfiguration = $this->configuration['applications.']['search.'];
		$whereClauses        = array();

		foreach ($communityRequest['profileSearch'] as $submittedParameterName => $submittedParameterValue) {
			if (!empty($submittedParameterValue)
				&& array_key_exists($submittedParameterName . '.', $searchConfiguration['searchFields.'])
			) {
				$filteredInput = $this->filterInput($submittedParameterValue, $searchConfiguration['searchFields.'][$submittedParameterName . '.']);

				$clauseParts = array();
				$searchInColumns = t3lib_div::trimExplode(',', $searchConfiguration['searchFields.'][$submittedParameterName . '.']['searchIn']);
				foreach ($searchInColumns as $columnName) {
					if (!empty($searchConfiguration['searchFields.'][$submittedParameterName . '.']['compareMode'])) {
							// use a custom comparison
						$clauseParts[] = $this->getWhereClause(
							$columnName,
							$filteredInput,
							$searchConfiguration['searchFields.'][$submittedParameterName . '.']['compareMode']
						);
					} else {
							// use the default "equal" comparison
						$clauseParts[] = $this->getWhereClause(
							$columnName,
							$filteredInput
						);
					}
				}

				$whereClauses[] = '(' . implode(' OR ', $clauseParts) . ')';
			}
		}
		$whereClause = '';
		if (count($whereClauses) > 0) {
			$whereClause = implode(' AND ', $whereClauses);
		}

		$userGateway = t3lib_div::makeInstance('tx_community_model_UserGateway');
		$foundUsers  = $userGateway->findByWhereClause($whereClause);

			// now use the user list to present the result
		$userList = $GLOBALS['TX_COMMUNITY']['applicationManager']->getApplication(
			'userList',
			$this->data,
			$this->configuration
		);
		$userList->setUserListModel($foundUsers);

		return $userList->execute();
	}

	protected function filterInput($content, array $filterConfiguration = array()) {
			// first apply a default string filter (FILTER_SANITIZE_STRING)
		$content = filter_var($content);

		if (!empty($filterConfiguration['validate'])) {
			$filters = t3lib_div::trimExplode(',', $filterConfiguration['validate']);

			foreach ($filters as $filter) {
					// put this in a separate method if we get more filters
				switch ($filter) {
					case 'email':
						$content = filter_var($content, FILTER_VALIDATE_EMAIL);
						break;
				}
			}
		}

		return $content;
	}

	protected function getWhereClause($columnName, $value, $compareMode = 'equal') {
		$clause = '';

		switch ($compareMode) {
			case 'equal':
				if (is_string($value)) {
					$value = '\'' . $value . '\'';
				}

				$clause = $columnName . ' = ' . $value;
				break;
			case 'like':
				$clause = $columnName . ' LIKE \'%' . $value . '%\'';
				break;
			default:
				// TODO throw an unknown column exception
		}

		return $clause;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_searchapplication.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_searchapplication.php']);
}

?>