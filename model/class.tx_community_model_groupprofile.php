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

require_once(t3lib_extMgm::extPath('community').'model/class.tx_community_model_groupgateway.php');
require_once(t3lib_extMgm::extPath('community').'model/class.tx_community_model_usergateway.php');
require_once(t3lib_extMgm::extPath('community').'model/class.tx_community_model_group.php');
require_once(t3lib_extMgm::extPath('community').'model/class.tx_community_model_abstractprofile.php');
require_once(t3lib_extMgm::extPath('community').'classes/exception/class.tx_community_exception_noprofileid.php');
require_once(t3lib_extMgm::extPath('community').'classes/exception/class.tx_community_exception_unknownprofile.php');
require_once(t3lib_extMgm::extPath('community_logger').'classes/class.tx_communitylogger_logger.php');


/**
 * A community user profile
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_model_GroupProfile extends tx_community_model_AbstractProfile {
	/**
	 * @var tx_community_model_GroupGateway
	 */
	protected $groupGateway;
	/**
	 * @var tx_community_model_UserGateway
	 */
	protected $userGateway;

	/**
	 * @var tx_community_model_User
	 */
	protected $loggedinUser;
	/**
	 * @var tx_community_model_Group
	 */
	protected $group;
	protected $uid = 0;
	protected $request;
	protected $editable = false;
	/**
	 * @var tx_communitylogger_Logger
	 */
	protected $logger;
	
	/**
	 * constructor for class tx_community_model_GroupProfile
	 */
	public function __construct() {
		$this->logger = tx_communitylogger_Logger::getInstance('community');
		$this->logger->info('loaded');

		$this->groupGateway	= t3lib_div::makeInstance('tx_community_model_GroupGateway');
		$this->userGateway	= t3lib_div::makeInstance('tx_community_model_UserGateway');

		$this->request		= t3lib_div::_GP('tx_community');
		$this->uid			= (isset($this->request['group'])) ? intval($this->request['group']) : $this->uid;
		$this->logger->debug("group id: {$this->uid}");

		if ($this->uid == 0) {
			throw new tx_community_exception_NoProfileId();
		}
		$this->group		= $this->groupGateway->findRequestedGroup();
		
		if ($this->group === null) {
		    # @ToDo ganz boese, muss sauber gemacht werden
		    #    $targetURL = $this->communityApplication->pi_getPageLink(
		    #            $this->configuration['pages.']['groupList']
		    #    );
		        Header('HTTP/1.1 303 See Other');
		        Header('Location: ' . t3lib_div::locationHeaderUrl('/index.php?id=38'));
		#		throw new tx_community_exception_UnknownProfile();
		}
		
		$this->loggedinUser	= $this->userGateway->findCurrentlyLoggedInUser();
				
		if ($this->loggedinUser !== null) {
			if ($this->group->isAdmin($this->loggedinUser)) {
				$this->editable = true;
			}
		}
	}
	
	public function isEditable() {
		return $this->editable;
	}
	
	public function getGrouptype() {
		return $this->group->getGrouptype();
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/model/class.tx_community_model_groupprofile.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/model/class.tx_community_model_groupprofile.php']);
}

?>