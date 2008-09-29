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


/**
 * Group Profile Application Controller
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_controller_GroupProfileApplication extends tx_community_controller_AbstractCommunityApplication  {
	public $prefixId      = 'tx_community_controller_GroupProfileApplication';		// Same as class name
	public $scriptRelPath = 'controller/class.tx_community_controller_groupprofileapplication.php';	// Path to this script relative to the extension dir.
	public $extKey        = 'community';	// The extension key.

	public $cObj;

	/**
	 * constructor for class tx_community_controller_GroupProfileApplication
	 */
	public function __construct() {
		parent::__construct();

		$this->prefixId = 'tx_community_controller_GroupProfileApplication';
		$this->scriptRelPath = 'controller/class.tx_community_controller_groupprofileapplication.php';
		$this->name = 'groupProfile';
	}

	public function execute() {
		$content = '';

		$widgetName = $this->pi_getFFvalue(
			$this->data['pi_flexform'],
			'widget'
		);

		$widgetConfiguration = $GLOBALS['TX_COMMUNITY']['applicationManager']->getWidgetConfiguration(
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
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_groupprofileapplication.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_groupprofileapplication.php']);
}

?>