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

require_once($GLOBALS['PATH_community'] . 'classes/viewhelper/class.tx_community_viewhelper_widget.php');
require_once($GLOBALS['PATH_community'] . 'classes/viewhelper/class.tx_community_viewhelper_ts.php');
require_once($GLOBALS['PATH_community'] . 'classes/viewhelper/class.tx_community_viewhelper_link.php');
require_once($GLOBALS['PATH_community'] . 'classes/viewhelper/class.tx_community_viewhelper_date.php');
require_once($GLOBALS['PATH_community'] . 'classes/viewhelper/class.tx_community_viewhelper_getcobj.php');

/**
 * The user list default view
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_view_userlist_Index extends tx_community_view_AbstractView {

	protected $userModel = array();
	private $pageBrowser='';
	private $userCount=0;

	public function setUserModel(array $userModel) {
		$this->userModel = $userModel;
	}
	
	public function setUserCount($userCount) {
		$this->userCount = $userCount;
	}

  public function setPageBrowser($pageBrowser) {
    $this->pageBrowser=$pageBrowser;
  }

	public function render() {
		$resultCounter = intval($this->userCount);

		$subpart = ($resultCounter > 0) ? 'user_list' : 'no_results';

		$templateClass = t3lib_div::makeInstanceClassName('tx_community_Template');
		$template = new $templateClass(
			t3lib_div::makeInstance('tslib_cObj'),
			$this->templateFile,
			$subpart
		);
		/* @var $template tx_community_Template */

		$template->addViewHelper('ts',     'tx_community_viewhelper_Ts');
		$template->addViewHelper('link',   'tx_community_viewhelper_Link');
		$template->addViewHelper('date',   'tx_community_viewhelper_Date');
		$template->addViewHelper('widget', 'tx_community_viewhelper_Widget');
		$template->addViewHelper('cobj',   'tx_community_viewhelper_GetCObj');

		$template->addLoop('users', 'user', $this->userModel);
		$template->addVariable('result', array(
			'counter'	=> $resultCounter
		));

		$template->addVariable('pagebrowser', array('pagebrowser'=>$this->pageBrowser));
		
		return $template->render();
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/view/userlist/class.tx_community_view_userlist_index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/view/userlist/class.tx_community_view_userlist_index.php']);
}

?>