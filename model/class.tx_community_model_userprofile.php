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

require_once(t3lib_extMgm::extPath('community').'model/class.tx_community_model_usergateway.php');
require_once(t3lib_extMgm::extPath('community').'model/class.tx_community_model_user.php');
require_once(t3lib_extMgm::extPath('community').'model/class.tx_community_model_abstractprofile.php');
require_once(t3lib_extMgm::extPath('community').'classes/exception/class.tx_community_exception_noprofileid.php');
require_once(t3lib_extMgm::extPath('community').'classes/exception/class.tx_community_exception_unknownprofile.php');

/**
 * A community user profile
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_model_UserProfile extends tx_community_model_AbstractProfile {
	/**
	 * @var tx_community_model_UserGateway
	 */
	protected $userGateway;
		/**
	 * @var tx_community_model_User
	 */
	protected $loggedinUser;
	protected $uid = 0;
	protected $request;
	protected $editable = false;

	/**
	 * constructor for class tx_community_model_UserProfile
	 */
	public function __construct() {
		$this->userGateway	= new tx_community_model_UserGateway();
		$this->loggedinUser	= $this->userGateway->findCurrentlyLoggedInUser();
		$this->request		= t3lib_div::_GP('tx_community');

		if ($this->loggedinUser !== null) {
			$this->uid		= $this->loggedinUser->getUid();
		}
		$this->uid			= (isset($this->request['user'])) ? intval($this->request['user']) : $this->uid;

			// hook to overwrite the uid
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tx_community']['tx_community_model_UserProfile']['getProfileUid'])) {
			foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tx_community']['tx_community_model_UserProfile']['getProfileUid'] as $classReference) {
				$hookObject = & t3lib_div::getUserObj($classReference);
				if ($hookObject instanceof tx_community_UserProfileProvider) {
					$this->uid = $hookObject->getProfileUid($this->uid, $this);
				}
			}
		}



		if ($this->uid == 0) {
			throw new tx_community_exception_NoProfileId();
		}

		if ($this->uid > 0) {
			$user = $this->userGateway->findById($this->uid);
			if (is_null($user)) {
				throw new tx_community_exception_UnknownProfile();
			}
		}

		if ($this->loggedinUser !== null) {
			$this->editable	= ($this->loggedinUser->getUid() == $this->uid) ? true : false;
		}
	}

	public function isEditable() {
		return $this->editable;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/model/class.tx_community_model_userprofile.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/model/class.tx_community_model_userprofile.php']);
}

?>