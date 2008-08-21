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


/**
 * image widget for a user profile
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_controller_userprofile_ImageWidget implements tx_community_CommunityApplicationWidget, tx_community_Command {

	/**
	 * constructor for class tx_community_controller_userprofile_Imagewidget
	 */
	public function __construct() {

	}

	public function initialize($data, $configuration) {

	}

	public function execute() {
		return 'user profile image widget';
	}

	/**
	 * returns whether a user is allowed to drag the widget to a different
	 * container or position
	 *
	 * @return	boolean	true if dragging is allowed, false otherwise
	 */
	public function isDragable() {
		return true;
	}

	/**
	 * returns whether the widget can be removed from being displayed
	 *
	 * @return	boolean	true id removing is allowed, false otherwise
	 */
	public function isRemovable() {
		return true;
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
	 * sets a new layout container
	 *
	 * @param	string	the ID of the new container
	 */
	public function setLayoutContainer($containerId) {

	}

	public function getID() {
		return 'imageWidget';
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
	 * sets the new position within its layout container
	 *
	 * @param	integer	new position
	 */
	public function setPosition($position) {

	}

	/**
	 * returns the widget's rendered content
	 *
	 * @return	string	the widget's content (HTML, XML, JSON, ...)
	 */
	public function render() {
		return "Ich bin ein Widget";
	}

	/**
	 * returns the widget's label
	 *
	 * @return	string	the widget's content (HTML, XML, JSON, ...)
	 */
	public function getLabel() {
		return "ImageWidget";
	}

	public function getWidgetClass() {
		return '';
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/userprofile/class.tx_community_controller_userprofile_imagewidget.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/userprofile/class.tx_community_controller_userprofile_imagewidget.php']);
}

?>