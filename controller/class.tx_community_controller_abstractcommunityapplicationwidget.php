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
require_once($GLOBALS['PATH_community'] . 'interfaces/acl/interface.tx_community_acl_aclresource.php');

/**
 * An abstract community application widget, that can be used as a base for other community application widgets
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
abstract class tx_community_controller_AbstractCommunityApplicationWidget implements tx_community_CommunityApplicationWidget, tx_community_Command {

	/**
	 * a reference to the parent community application this widget belongs to
	 *
	 * @var tx_community_controller_AbstractCommunityApplication
	 */
	protected $communityApplication;
	protected $configuration;
	protected $data;

	protected $name     = null;
	protected $label    = null;
	protected $cssClass = null;

	protected $dragable;
	protected $removable;
	protected $layoutContainer;
	protected $position;

	public function __construct() {
		$this->dragable        = true;
		$this->removable       = true;
		$this->layoutContainer = 0;
		$this->position        = 0;
	}

	public function initialize($data, $configuration) {
		$this->data = $data;
		$this->configuration = $configuration;
	}

	public function setCommunityApplication(tx_community_controller_AbstractCommunityApplication $communityApplication) {
		$this->communityApplication = $communityApplication;
	}

	/**
	 * central excution method for the widget, acts as a dispatcher for the
	 * different actions
	 *
	 * @return	string	the result of the called action, usually some form of output/rendered HTML
	 */
	public function execute() {
		$content = '';
		$communityRequest = t3lib_div::GParrayMerged('tx_community');;

		$widgetConfiguration = $GLOBALS['TX_COMMUNITY']['applicationManager']->getWidgetConfiguration(
			$this->communityApplication->getName(),
			$this->getName()
		);

			// dispatch
		if (!empty($communityRequest[$this->name . 'Action'])
			&& method_exists($this, $communityRequest[$this->name . 'Action'] . 'Action')
			&& in_array($communityRequest[$this->name . 'Action'], $widgetConfiguration['actions'])
		) {
				// call a specifically requested action
			$actionName = $communityRequest[$this->name . 'Action'] . 'Action';
			$content = $this->$actionName();
		} else {
				// call the default action
			$defaultActionName = $widgetConfiguration['defaultAction'] . 'Action';
			$content = $this->$defaultActionName();
		}

		return $content;
	}

	/**
	 * gets the widget's name
	 *
	 * @return	string	the widget's name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * returns whether a user is allowed to drag the widget to a different
	 * container or position
	 *
	 * @return	boolean	true if dragging is allowed, false otherwise
	 */
	public function isDragable() {
		return $this->dragable;
	}

	/**
	 * returns whether the widget can be removed from being displayed
	 *
	 * @return	boolean	true id removing is allowed, false otherwise
	 */
	public function isRemovable() {
		return $this->removable;
	}

	/**
	 * return the current layout container the widget is located in
	 *
	 * @return	string
	 */
	public function getLayoutContainer() {
		return $this->layoutContainer;
	}

	/**
	 * gets the position of the widget within its container
	 *
	 * @return	integer	the position within a container
	 */
	public function getPosition() {
		return $this->position;
	}

	public function getLabel() {
		return $this->label;
	}

	public function getCssClass() {
		return $this->cssClass;
	}

	abstract public function indexAction();
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_abstractcommunityapplicationwidget.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_abstractcommunityapplicationwidget.php']);
}

?>