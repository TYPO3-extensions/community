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

require_once($GLOBALS['PATH_community'] . 'view/groupprofile/class.tx_community_view_groupprofile_contentobjectimage.php');

/**
 * widget to display a group's image
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_controller_groupprofile_ImageWidget extends tx_community_controller_AbstractCommunityApplicationWidget {

	/**
	 * constructor for class tx_community_controller_groupprofile_ImageWidget
	 */
	public function __construct() {
		parent::__construct();

		$this->name      = 'image';
		$this->label     = 'ImageWidget'; // @TODO localize the label
		$this->cssClass  = '';
		$this->draggable = true;
		$this->removable = true;
	}

	public function indexAction() {
		$requestedGroup = $this->communityApplication->getRequestedGroup();
		$widgetTypoScriptConfiguration = $this->communityApplication->getWidgetTypoScriptConfiguration($this->name);

		$groupImageConfiguration = array(
			$widgetTypoScriptConfiguration['groupImage'],
			$widgetTypoScriptConfiguration['groupImage.']
		);
		$groupImageConfiguration[1]['file'] = $requestedGroup->getImage();

		$view = t3lib_div::makeInstance('tx_community_view_groupprofile_ContentObjectImage');
		$view->setImageConfiguration($groupImageConfiguration);

		return $view->render();
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/groupprofile/class.tx_community_controller_groupprofile_imagewidget.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/groupprofile/class.tx_community_controller_groupprofile_imagewidget.php']);
}

?>