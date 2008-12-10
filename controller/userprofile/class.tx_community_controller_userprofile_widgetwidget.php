<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Frank Naegler <typo3@naegler.net>
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

require_once($GLOBALS['PATH_community'] . 'view/userprofile/class.tx_community_view_userprofile_widget.php');

/**
 * widget widget for the user profile community application
 * showing other widgets
 *
 * @author	Frank Naegler <typo3@naegler.net>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_controller_userprofile_WidgetWidget extends tx_community_controller_AbstractCommunityApplicationWidget {
	/**
	 * @var tx_community_LocalizationManager
	 */
	protected $localizationManager;

	protected $accessMode;

	public function __construct() {
		parent::__construct();
		$this->localizationManager = tx_community_LocalizationManager::getInstance('EXT:community/lang/locallang_userprofile_widget.xml', $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_community.']);

			// set default access mode
		$this->accessMode = 'read';

		$this->name     = 'widget';
		$this->label    = $this->localizationManager->getLL('label_WidgetWidget');
		$this->cssClass = '';

		$this->draggable = false;
		$this->removable = false;
		$this->position  = 1;
	}

	/**
	 * the default action for this widget, fetches the user to show the personal
	 * information for, creates a view and returns the view's output
	 *
	 * @return	string	the view's output
	 */
	public function indexAction() {
		$content = '';

		$requestedUser  = $this->communityApplication->getRequestedUser();
		$requestingUser = $this->communityApplication->getRequestingUser();

		$this->label	= str_replace('###NICKNAME###', $requestedUser->getNickname(), $this->label);
		
		$view = t3lib_div::makeInstance('tx_community_view_userprofile_Widget');
		$view->setUserModel($requestedUser);
		$view->setTemplateFile($this->configuration['applications.']['userProfile.']['widgets.']['widget.']['templateFile']);
		$view->setLanguageKey($this->communityApplication->LLkey);

		$content = $view->render();

		return $content;
	}

	protected function checkAccess() {
		// TODO move access checking stuff here
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/userprofile/class.tx_community_controller_userprofile_personalinformationwidget.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/userprofile/class.tx_community_controller_userprofile_personalinformationwidget.php']);
}

?>