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

require_once($GLOBALS['PATH_community'] . 'model/class.tx_community_model_groupgateway.php');
require_once($GLOBALS['PATH_community'] . 'view/listgroups/class.tx_community_view_listgroups_index.php');

/**
 * Edit Group Application Controller
 *
 * @author	Frank Naegler <typo3@naegler.net>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_controller_ListGroupsApplication extends tx_community_controller_AbstractCommunityApplication {

	public $cObj;
	public $conf;
	protected $name;
	protected $configuration;
	protected $group;
	/**
	 * @var tx_community_model_GroupGateway
	 */
	protected $groupGateway;
	protected $request;
	private $userCount=0;


	/**
	 * constructor for class tx_community_controller_ListGroupsApplication
	 */
	public function __construct() {
		parent::__construct();

		$this->prefixId = 'tx_community_controller_ListGroupsApplication';
		$this->scriptRelPath = 'controller/class.tx_community_controller_listgroupsapplication.php';
		$this->name = 'listGroups';

		$this->groupGateway = t3lib_div::makeInstance('tx_community_model_GroupGateway');
		$this->request 		= t3lib_div::GParrayMerged('tx_community');
	}

	public function execute() {
		$content = '';

		$applicationConfiguration = $GLOBALS['TX_COMMUNITY']['applicationManager']->getApplicationConfiguration(
			$this->getName()
		);

		switch ($this->request['action']) {
			case 'search':
				$content = $this->searchAction();
			break;
			case 'index':
			default:
				$content = $this->indexAction();
			break;
		}



		return $content;
	}

	/**
	 * returns the name of this community application
	 *
	 * @return	string	This community application's name
	 */
	public function getName() {
		return $this->name;
	}

	protected function indexAction() {
		$view = t3lib_div::makeInstance('tx_community_view_listGroups_Index');
		/* @var $view tx_community_view_listGroups_Index */
		$view->setTemplateFile($this->configuration['applications.']['listGroups.']['templateFile']);
		$view->setLanguageKey($this->LLkey);

		switch ($this->configuration['applications.']['listGroups.']['listType']) {
			case 'usersGroups':
				$user = $this->getRequestedUser();
				if (!is_null($user)) {
					$groups = $this->groupGateway->findGroupsByUser($this->getRequestedUser());
				} else {
					$groups = array();
				}
			break;
			case 'allGroups':
			default:
				$groups = $this->groupGateway->getAllGroups(true, $pageBrowserConfig['numberOfEntriesPerPage'], $firstGroup);
			break;
		}

		$pageBrowserConfig = $this->configuration['applications.']['listGroups.']['pageBrowser.'];
		$pageBrowserConfig['numberOfPages'] = ceil(count($groups) / $pageBrowserConfig['numberOfEntriesPerPage']);
		$firstGroup = (isset($this->request['page'])) ? (intval($this->request['page']+1)*$pageBrowserConfig['numberOfEntriesPerPage']) - $pageBrowserConfig['numberOfEntriesPerPage'] : 0;

		$cObj = t3lib_div::makeInstance('tslib_cObj');

		$listGroupsArray = array();
		foreach ($groups as $group) {
			if ($group->getGroupType() != tx_community_model_Group::TYPE_SECRET) {
				$imgConf = $this->configuration['applications.']['listGroups.']['groupImage.'];
				$imgConf['file'] = (strlen($group->getImage()) > 0) ? $group->getImage() : $imgConf['file'];
				$genImage = $cObj->cObjGetSingle('IMAGE', $imgConf);
				$group->setHTMLImage($genImage);
				$listGroupsArray[] = $group;
			}
		}

		/*$tmp = array();
		for ($i=$firstGroup-1; $i<$firstGroup+$pageBrowserConfig['numberOfEntriesPerPage']-1; $i++) {
			if (!is_null($listGroupsArray[$i])) {
				$tmp[] = $listGroupsArray[$i];
			}
		}*/
		//@TODO Check if this can happen at all. Might be no longer needed since we use the limited query.
		foreach($listGroupsArray as $k=>$v) {
			if(is_null($v)) {
				unset($listGroupsArray[$k]);
			}
		}
		$view->setGroups($listGroupsArray);

		$groupsDetailLink = $this->pi_getPageLink(
			$this->configuration['pages.']['groupProfile'],
			'',
			array(
				'tx_community' => array(
					'group' => '%UID%'
				)
			)
		);
		$view->setGroupDetailLink($groupsDetailLink);

		$pageBrowser = $cObj->cObjGetSingle($this->configuration['applications.']['listGroups.']['pageBrowser'], $pageBrowserConfig);

		$view->setPageBrowser($pageBrowser);

		return $view->render();
	}

	protected function searchAction() {
		$view = t3lib_div::makeInstance('tx_community_view_listGroups_Index');
		/* @var $view tx_community_view_listGroups_Index */
		$view->setTemplateFile($this->configuration['applications.']['listGroups.']['templateFile']);
		$view->setLanguageKey($this->LLkey);

		$searchValue = $GLOBALS['TYPO3_DB']->quoteStr($this->request['quicksearch'], 'tx_community_group');
		$whereClause = "name like '%{$searchValue}%'";

		$pageBrowserConfig = $this->configuration['applications.']['listGroups.']['pageBrowser.'];
		$pageBrowserConfig['numberOfPages'] = ceil($this->groupGateway->getEntryCount($whereClause, true) / $pageBrowserConfig['numberOfEntriesPerPage']);
		$pageBrowserConfig['extraQueryString'] = '&tx_community[action]=search&tx_community[quicksearch]=' . $searchValue;
		$firstGroup = (isset($this->request['page'])) ? (intval($this->request['page']+1)*$pageBrowserConfig['numberOfEntriesPerPage']) - $pageBrowserConfig['numberOfEntriesPerPage'] + 1 : 0;

		$groups = $this->groupGateway->findByWhereClause($whereClause, $pageBrowserConfig['numberOfEntriesPerPage'], $firstGroup);

		$cObj = t3lib_div::makeInstance('tslib_cObj');

		$listGroupsArray = array();
		foreach ($groups as $group) {
			if ($group->getGroupType() != tx_community_model_Group::TYPE_SECRET) {
				$imgConf = $this->configuration['applications.']['listGroups.']['groupImage.'];
				$imgConf['file'] = (strlen($group->getImage()) > 0) ? $group->getImage() : $imgConf['file'];
				$genImage = $cObj->cObjGetSingle('IMAGE', $imgConf);
				$group->setHTMLImage($genImage);
				$listGroupsArray[] = $group;
			}
		}

		/*$tmp = array();
		for ($i=$firstGroup-1; $i<$firstGroup+$pageBrowserConfig['numberOfEntriesPerPage']-1; $i++) {
			if (!is_null($listGroupsArray[$i])) {
				$tmp[] = $listGroupsArray[$i];
			}
		}*/
		//@TODO Check if this can happen at all. Might be no longer needed since we use the limited query.
		foreach($listGroupsArray as $k=>$v) {
			if(is_null($v)) {
				unset($listGroupsArray[$k]);
			}
		}

		$view->setGroups($listGroupsArray);

		$groupsDetailLink = $this->pi_getPageLink(
			$this->configuration['pages.']['groupProfile'],
			'',
			array(
				'tx_community' => array(
					'group' => '%UID%'
				)
			)
		);
		$view->setGroupDetailLink($groupsDetailLink);

		$pageBrowser = $cObj->cObjGetSingle($this->configuration['applications.']['listGroups.']['pageBrowser'], $pageBrowserConfig);

		$view->setPageBrowser($pageBrowser);

		return $view->render();
	}

	public function setUserCount(array $userCount) {
		$this->userCount = $userCount;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_listgroupsapplication.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_listgroupsapplication.php']);
}

?>
