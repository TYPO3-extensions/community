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

require_once($GLOBALS['PATH_community'] . 'view/userprofile/class.tx_community_view_userprofile_statusmessage.php');

/**
 * A widget to display and edit a user's status message
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_controller_userprofile_StatusMessageWidget extends tx_community_controller_AbstractCommunityApplicationWidget {

	/**
	 * constructor for class tx_community_controller_userprofile_StatusMessageWidget
	 */
	public function __construct() {
		parent::__construct();

		$this->name      = 'statusMessage';
		$this->draggable = true;
		$this->removable = true;

		$this->label = 'Status Message';
		$this->cssClass = '';
	}

	/**
	 * default action for this widget, simply displays the user's status message
	 *
	 * @return	string		the widget's output as HTML
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function indexAction() {
		$content = '';

		$view = t3lib_div::makeInstance('tx_community_view_userprofile_StatusMessage');
		$view->setTemplateFile($this->configuration['applications.']['userProfile.']['widgets.'][$this->name . '.']['templateFile']);
		$view->setUserModel($this->communityApplication->getRequestedUser());

		$content = $view->render();

		return $content;
	}

	public function updateStatusMessageAction() {

	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/userprofile/class.tx_community_controller_userprofile_statusmessagewidget.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/userprofile/class.tx_community_controller_userprofile_statusmessagewidget.php']);
}

?>