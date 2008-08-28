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


require_once(t3lib_extMgm::extPath('community').'model/class.tx_community_model_userprofile.php');
require_once(t3lib_extMgm::extPath('community').'model/class.tx_community_model_groupprofile.php');
require_once(t3lib_extMgm::extPath('community').'classes/exception/class.tx_community_exception_unknownprofiletype.php');


/**
 * A manager to manage community applications that are used on profile pages
 *
 * @author	Frank Nägler <typo3@naegler.net>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_ProfileFactory {
	static public function createProfile($type) {
		try {
			switch (strtolower($type)) {
				case 'userprofile' :
					$profile = new tx_community_model_UserProfile();
				break;
				case 'groupprofile' :
					$profile = new tx_community_model_GroupProfile();
				break;
				case 'startpage' :
					$profile = new tx_community_model_UserProfile();
				break;
				default :
					throw new tx_community_exception_UnknownProfileType();
				break;
			}
			return $profile;
		} catch (Exception $exception) {
			throw $exception;
		}
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/classes/class.tx_community_profilefactory.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/classes/class.tx_community_profilefactory.php']);
}

?>