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

require_once($GLOBALS['PATH_community'] . 'classes/viewhelper/class.tx_community_viewhelper_ts.php');
require_once($GLOBALS['PATH_community'] . 'classes/viewhelper/class.tx_community_viewhelper_link.php');

/**
 * Widget view to friends of an user
 *
 * @author	Frank Naegler <typo3@naegler.net>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_view_userprofile_MyFriends extends tx_community_view_AbstractView {

	protected $friendsModel;

	public function setFriendsModel(array $friendsModel) {
		$this->friendsModel = $friendsModel;
	}

	public function render() {
		$templateClass = t3lib_div::makeInstanceClassName('tx_community_Template');
		$template = new $templateClass(
			t3lib_div::makeInstance('tslib_cObj'),
			$this->templateFile,
			'my_friends'
		);

		$template->addViewHelper(
			'ts',
			'tx_community_viewhelper_Ts'
		);

		$template->addViewHelper(
			'link',
			'tx_community_viewhelper_Link'
		);

		
		$template->addSubpart('roles', $this->renderRoles());

		return $template->render();
	}
	
	protected function renderRoles() {
		$content = '';
		foreach ($this->friendsModel as $role) {
			$templateClass = t3lib_div::makeInstanceClassName('tx_community_Template');
			$template = new $templateClass(
				t3lib_div::makeInstance('tslib_cObj'),
				$this->templateFile,
				'roles'
			);
	
			$template->addViewHelper(
				'ts',
				'tx_community_viewhelper_Ts'
			);
	
			$template->addViewHelper(
				'link',
				'tx_community_viewhelper_Link'
			);
			
#			debug($template);
			$template->addVariable('role', $role);
			if (is_array($role['friends'])) {
				$template->addLoop('friends', 'user', $role['friends']);
			}

			$content .= $template->render();
		}
		
		return $content;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/view/userprofile/class.tx_community_view_userprofile_myfriends.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/view/userprofile/class.tx_community_view_userprofile_myfriends.php']);
}

?>