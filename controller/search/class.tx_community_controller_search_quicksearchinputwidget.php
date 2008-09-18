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

require_once($GLOBALS['PATH_community'] . 'interfaces/interface.tx_community_communityapplicationwidget.php');
require_once($GLOBALS['PATH_community'] . 'interfaces/interface.tx_community_command.php');
require_once($GLOBALS['PATH_community'] . 'view/search/class.tx_community_view_search_quicksearchinput.php');

/**
 * Search input field widget to do a quick search by name
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_controller_search_QuickSearchInputWidget implements tx_community_CommunityApplicationWidget, tx_community_Command {

	/**
	 * a reference to the parent community application this widget belongs to
	 *
	 * @var tx_community_controller_AbstractCommunityApplication
	 */
	protected $communityApplication;
	protected $configuration;
	protected $data;

	public function initialize($data, $configuration) {
		$this->data = $data;
		$this->configuration = $configuration;
	}

	public function setCommunityApplication(tx_community_controller_AbstractCommunityApplication $communityApplication) {
		$this->communityApplication = $communityApplication;
	}

	/**
	 * returns whether a user is allowed to drag the widget to a different
	 * container or position
	 *
	 * @return	boolean	true if dragging is allowed, false otherwise
	 */
	public function isDragable() {
		return false;
	}

	/**
	 * returns whether the widget can be removed from being displayed
	 *
	 * @return	boolean	true id removing is allowed, false otherwise
	 */
	public function isRemovable() {
		return false;
	}

	/**
	 * return the current layout container the widget is located in
	 *
	 * @return	string
	 */
	public function getLayoutContainer() {
		return 0;
	}

	/**
	 * returns the widget's Id, this is the ID which is used while the widget
	 * gets registerd in ext_localconf.php
	 *
	 * @return	string	the widget's Id
	 */
	public function getId() {
		return 'quickSearchInput';
	}

	/**
	 * gets the position of the widget within its container
	 *
	 * @return	integer	the position within a container
	 */
	public function getPosition() {
		return 2;
	}

	/**
	 * returns the widget's label
	 *
	 * @return	string	the widget's content (HTML, XML, JSON, ...)
	 */
	public function getLabel() {
		return 'QuickSearchInputWidget';
	}

	/**
	 * returns the widget's CSS class(es)
	 *
	 * @return	string	the widget's CSS class
	 */
	public function getWidgetClass() {
		return '';
	}

	public function execute() {
		$content = '';
		$communityRequest = t3lib_div::GParrayMerged('tx_community');;

		$applicationManagerClass = t3lib_div::makeInstanceClassName('tx_community_ApplicationManager');
		$applicationManager      = call_user_func(array($applicationManagerClass, 'getInstance'));
		/* @var $applicationManager tx_community_ApplicationManager */

		$widgetConfiguration = $applicationManager->getWidgetConfiguration(
			$this->communityApplication->getName(),
			$this->getId()
		);

			// dispatch
		if (!empty($communityRequest['quickSearchInputAction'])
			&& method_exists($this, $communityRequest['quickSearchInputAction'] . 'Action')
			&& in_array($communityRequest['quickSearchInputAction'], $widgetConfiguration['actions'])
		) {
				// call a specifically requested action
			$actionName = $communityRequest['quickSearchInputAction'] . 'Action';
			$content = $this->$actionName();
		} else {
				// call the default action
			$defaultActionName = $widgetConfiguration['defaultAction'] . 'Action';
			$content = $this->$defaultActionName();
		}

		return $content;
	}

	public function indexAction() {
		$view = t3lib_div::makeInstance('tx_community_view_search_QuickSearchInput');
		/* @var $view tx_community_view_search_QuickSearchInput */
		$view->setTemplateFile($this->configuration['applications.']['search.']['widgets.']['quickSearchInput.']['templateFile']);
		$view->setLanguageKey($this->communityApplication->LLkey);

		$formAction = $this->communityApplication->pi_getPageLink(
			$this->configuration['pages.']['searchResults']
		);

		$view->setFormAction($formAction);
		$view->setInputFieldProperties(array(
			'name' => 'tx_community[profileSearch][name]',
			'id'   => 'tx_community-profileSearch-' . $this->getId()
		));

		return $view->render();
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/search/class.tx_community_controller_search_quicksearchinputwidget.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/search/class.tx_community_controller_search_quicksearchinputwidget.php']);
}

?>