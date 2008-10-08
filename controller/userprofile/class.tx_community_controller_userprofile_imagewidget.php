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

require_once($GLOBALS['PATH_community'] . 'view/userprofile/class.tx_community_view_userprofile_contentobjectimage.php');

/**
 * image widget for a user profile
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_controller_userprofile_ImageWidget extends tx_community_controller_AbstractCommunityApplicationWidget {

	/**
	 * @var tx_community_LocalizationManager
	 */
	protected $localizationManager;

	/**
	 * constructor for class tx_community_controller_userprofile_Imagewidget
	 */
	public function __construct() {
		parent::__construct();
		$this->localizationManager = tx_community_LocalizationManager::getInstance('EXT:community/lang/locallang_userprofile_imagewidget.xml', $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_community.']);

		$this->name = 'image';
		$this->draggable = true;
		$this->removable = true;

		$this->label = $this->localizationManager->getLL('label_ImageWidget');
		$this->cssClass = '';
	}

	public function indexAction() {
		$requestedUser = $this->communityApplication->getRequestedUser();
		$widgetTypoScriptConfiguration = $this->communityApplication->getWidgetTypoScriptConfiguration($this->name);

		$profileImageConfiguration = array(
			$widgetTypoScriptConfiguration['profileImage'],
			$widgetTypoScriptConfiguration['profileImage.']
		);

		$userImage = $requestedUser->getImage();
		if(!empty($userImage)) {
			$profileImageConfiguration[1]['file'] = $userImage;
		}

		$view = t3lib_div::makeInstance('tx_community_view_userprofile_ContentObjectImage');
		$view->setImageConfiguration($profileImageConfiguration);

		return $view->render();
	}

	/**
	 * renders a thumbnail version of the user's image
	 *
	 * @return	string	the thumbnail image as HTML tag
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function thumbnailAction() {
		$requestedUser = $this->communityApplication->getRequestedUser();
		$widgetTypoScriptConfiguration = $this->communityApplication->getWidgetTypoScriptConfiguration($this->name);

		$profileImageConfiguration = array(
			$widgetTypoScriptConfiguration['thumbnail'],
			$widgetTypoScriptConfiguration['thumbnail.']
		);

		$userImage = $requestedUser->getImage();
		if(!empty($userImage)) {
			$profileImageConfiguration[1]['file'] = $userImage;
		}

		$view = t3lib_div::makeInstance('tx_community_view_userprofile_ContentObjectImage');
		$view->setImageConfiguration($profileImageConfiguration);

		return $view->render();
	}

	/**
	 * renders a custom sized user image
	 *
	 * @param array $arguments
	 * @return	string	the thumbnail image as HTML tag
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function customImageAction(array $arguments) {
		$requestedUser = $this->communityApplication->getRequestedUser();
		$widgetTypoScriptConfiguration = $this->communityApplication->getWidgetTypoScriptConfiguration($this->name);

		$profileImageConfiguration = array(
			$widgetTypoScriptConfiguration[$arguments[0]],
			$widgetTypoScriptConfiguration[$arguments[0] . '.']
		);

		$userImage = $requestedUser->getImage();
		if(!empty($userImage)) {
			$profileImageConfiguration[1]['file'] = $userImage;
		}

		$view = t3lib_div::makeInstance('tx_community_view_userprofile_ContentObjectImage');
		$view->setImageConfiguration($profileImageConfiguration);

		return $view->render();
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/userprofile/class.tx_community_controller_userprofile_imagewidget.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/userprofile/class.tx_community_controller_userprofile_imagewidget.php']);
}

?>