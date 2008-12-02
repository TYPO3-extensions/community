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
 * a view displaying a user's status message
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_view_userprofile_StatusMessage extends tx_community_view_AbstractView {

	/**
	 * The user model used to render this view
	 *
	 * @var tx_community_model_User
	 */
	protected $userModel;

	/**
	 * sets the user model for this view
	 *
	 * @param	array	an array of tx_community_model_User objects
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function setUserModel(tx_community_model_User $user) {
		$this->userModel = $user;
	}

	/**
	 * renders this view
	 *
	 * @return	string	the rendered view
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function render() {
		$templateClass = t3lib_div::makeInstanceClassName('tx_community_Template');
		$template = new $templateClass(
			t3lib_div::makeInstance('tslib_cObj'),
			$this->templateFile,
			'status_message'
		);

		$template->addVariable('user', $this->userModel);

		return $template->render();
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/view/userprofile/class.tx_community_view_userprofile_statusmessage.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/view/userprofile/class.tx_community_view_userprofile_statusmessage.php']);
}

?>