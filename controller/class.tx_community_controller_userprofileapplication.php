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
 * User Profile Application Controller
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_controller_UserProfileApplication extends tx_community_controller_AbstractCommunityApplication {

	public function __construct() {
		parent::__construct();

		$this->prefixId = 'tx_community_controller_UserProfileApplication';
		$this->scriptRelPath = 'controller/class.tx_community_controller_userprofileapplication.php';
		$this->name = 'userProfile';
	}

	/**
	 * sets the requested user, usefull for example when the requested user is
	 * different from the one given in the GET parameter or no GET parameter is
	 * available
	 *
	 * @param	tx_community_model_User	The user to set as requested user
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function setRequestedUser(tx_community_model_User $user) {
		$this->requestedUser = $user;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_userprofileapplication.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_userprofileapplication.php']);
}

?>