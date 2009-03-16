<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008-2009 Ingo Renner <ingo@typo3.org>
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

		$this->communityRequest = t3lib_div::GParrayMerged('tx_community');

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
			// TODO add the search action to the page browser so that this if is not needed anymore
		if (isset($this->communityRequest['searchkey'])) {
			return $this->searchAction();
		}
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
		$searchConfiguration = $this->configuration['applications.']['search.'];
		$whereClauses        = array();
		$cObj = t3lib_div::makeInstance('tslib_cObj');

			// TODO move into new functions (buildWhereClause)
			// TODO check the session for a whereclause then either use if exists or build

		if (!isset($this->communityRequest['searchkey'])) {
			foreach ($this->communityRequest['profileSearch'] as $submittedParameterName => $submittedParameterValue) {
				if (
					!empty($submittedParameterValue)
					&&
					array_key_exists($submittedParameterName . '.', $searchConfiguration['searchFields.'])
				) {
					$filteredInput = $this->filterInput($submittedParameterValue, $searchConfiguration['searchFields.'][$submittedParameterName . '.']);
					$clauseParts = array();
					$searchInColumns = t3lib_div::trimExplode(',', $searchConfiguration['searchFields.'][$submittedParameterName . '.']['searchIn']);

					foreach ($searchInColumns as $columnName) {
						if (!empty($searchConfiguration['searchFields.'][$submittedParameterName . '.']['compareMode'])) {
								// use a custom comparison
							$whereClause = $this->getWhereClause(
								$columnName,
								$filteredInput,
								$searchConfiguration['searchFields.'][$submittedParameterName . '.']['compareMode']
							);
							if (strlen($whereClause) > 0) {
								$clauseParts[] = $whereClause;
							}
						} else {
								// use the default "equal" comparison
							$whereClause = $this->getWhereClause(
								$columnName,
								$filteredInput
							);
							if (strlen($whereClause) > 0) {
								$clauseParts[] = $whereClause;
							}
						}
					}
					if (count($clauseParts) > 0) {
						$whereClauses[] = '(' . implode(' OR ', $clauseParts) . ')';
					}
				}
			}

			$whereClause = '';
			if (count($whereClauses) > 0) {
				$whereClause = implode(' AND ', $whereClauses);
			}
		}

			// look for a searchkey in request and get the searchparams from session.
			// if no searchkey is found, create a new one and put into the session.
			// we need this for smaller URLs in pageBrowser
		if (!isset($this->communityRequest['searchkey'])) {
			$GLOBALS['tx_community_searchkey'] = 'tx_community_searchkey_'.md5(time());
			$GLOBALS['TSFE']->fe_user->setKey("ses", $GLOBALS['tx_community_searchkey'], $whereClause);
		} else {
			$GLOBALS['tx_community_searchkey'] = $this->communityRequest['searchkey'];
			$whereClause = $GLOBALS['TSFE']->fe_user->getKey("ses", $GLOBALS['tx_community_searchkey']);
		}

		if (empty($whereClause)) {
				// nothing to search for, do a redirect to the search form page

			$profilePageUrl = $this->pi_getPageLink(
				$GLOBALS['TSFE']->id
			);

				// TODO user t3lib_div::redirect when TYPO3 4.3 is released
			Header('HTTP/1.1 303 See Other');
			Header('Location: ' . t3lib_div::locationHeaderUrl($profilePageUrl));
			exit;
		}

		$userGateway = t3lib_div::makeInstance('tx_community_model_UserGateway');
		$userCount = $this->userGateway->getEntryCount($whereClause);
		$pageBrowserConfig = $this->configuration['applications.']['searchUsers.']['pageBrowser.'];
		$pageBrowserConfig['numberOfPages'] = ceil($userCount / $pageBrowserConfig['numberOfEntriesPerPage']);
		$pageBrowserConfig['extraQueryString'] = '&tx_community[searchkey]='.$GLOBALS['tx_community_searchkey'];
		$firstGroup = (isset($this->communityRequest['page'])) ? (intval($this->communityRequest['page']+1)*$pageBrowserConfig['numberOfEntriesPerPage']) - $pageBrowserConfig['numberOfEntriesPerPage'] : 0;

		$userGateway = t3lib_div::makeInstance('tx_community_model_UserGateway');
		$foundUsers  = $userGateway->findByWhereClause($whereClause);

		$foundUsers  = $userGateway->findByWhereClause($whereClause, $pageBrowserConfig['numberOfEntriesPerPage'], $firstGroup);

			// now use the user list to present the result
		$userList = $GLOBALS['TX_COMMUNITY']['applicationManager']->getApplication(
			'userList',
			$this->data,
			$this->configuration
		);
		$userList->setUserListModel($foundUsers);
		$userList->setUserCount($userCount);
		$userList->setPageBrowser($cObj->cObjGetSingle($this->configuration['applications.']['listGroups.']['pageBrowser'], $pageBrowserConfig));

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

		if (strlen($value) > 0) {
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
		}

		return $clause;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_searchapplication.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_searchapplication.php']);
}

?>