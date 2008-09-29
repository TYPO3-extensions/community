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

require_once($GLOBALS['PATH_community'] . 'interfaces/interface.tx_community_view.php');

/**
 * displays the user's image y using a IMAGE content object
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_view_userprofile_ContentObjectImage implements tx_community_View {

	/**
	 * The user model used to render this view
	 *
	 * @var tx_community_model_User
	 */
	protected $userModel;
	protected $imageConfiguration;
	protected $templateFile;
	protected $languageKey;

	public function setUserModel(tx_community_model_User $user) {
		$this->userModel = $user;
	}

	public function setTemplateFile($templateFile) {
		$this->templateFile = $templateFile;
	}

	public function setLanguageKey($languageKey) {
		$this->languageKey = $languageKey;
	}

	public function setImageConfiguration(array $imageConfiguration) {
		$this->imageConfiguration = $imageConfiguration;
	}

	public function render() {
		$this->imageConfiguration['file'] = $this->userModel->getImage();

		$contentObject = t3lib_div::makeInstance('tslib_cObj');
		$image = $contentObject->IMAGE(
			$this->imageConfiguration
		);

		return $image;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/view/userprofile/class.tx_community_view_userprofile_contentobjectimage.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/view/userprofile/class.tx_community_view_userprofile_contentobjectimage.php']);
}

?>