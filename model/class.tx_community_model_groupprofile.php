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
	 * @var tx_community_model_Group
	 */
	protected $group;
	protected $uid = 0;
	protected $request;
	protected $editable = false;
	
	/**
	 * constructor for class tx_community_model_GroupProfile
	 */
	public function __construct() {
		$this->groupGateway	= new tx_community_model_GroupGateway();
		$this->userGateway	= new tx_community_model_UserGateway();
		$this->request		= t3lib_div::_GP('tx_community');
		$this->uid			= (isset($this->request['group'])) ? intval($this->request['group']) : $this->uid;
		
		$this->group		= $this->groupGateway->findById($this->uid);
		$this->loggedinUser	= $this->userGateway->findCurrentlyLoggedInUser();
		
		if ($this->loggedinUser !== null) {
			// @TODO: check if loggedinUser has admin rights on group
		}
	}
	
	public function isEditable() {
		return $this->editable;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/model/class.tx_community_model_groupprofile.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/model/class.tx_community_model_groupprofile.php']);
}

?>