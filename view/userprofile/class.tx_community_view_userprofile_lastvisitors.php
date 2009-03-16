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

require_once($GLOBALS['PATH_community'] . 'classes/viewhelper/class.tx_community_viewhelper_lll.php');
require_once($GLOBALS['PATH_community'] . 'classes/viewhelper/class.tx_community_viewhelper_ts.php');
require_once($GLOBALS['PATH_community'] . 'classes/viewhelper/class.tx_community_viewhelper_link.php');

/**
 * View for the last visitors widget
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_view_userprofile_LastVisitors extends tx_community_view_AbstractView {

	protected $lastVisitorsModel;

	/**
	 * constructor for class tx_community_view_userprofile_LastVisitors
	 */
	public function __construct() {

	}

	/**
	 * sets the array of last visitors
	 *
	 * @param	array	An array of user objects that last visited a user's profile
	 */
	public function setLastVisitorsModel(array $lastVisitors) {
		$this->lastVisitorsModel = $lastVisitors;
	}

	public function render() {
		$templateClass = t3lib_div::makeInstanceClassName('tx_community_Template');
		$template = new $templateClass(
			t3lib_div::makeInstance('tslib_cObj'),
			$this->templateFile,
			count($this->lastVisitorsModel) ? 'last_visitors' : 'last_visitors_error'
		);

		$template->addViewHelper(
			'lll',
			'tx_community_viewhelper_Lll',
			array(
				'languageFile' => $GLOBALS['PATH_community'] . 'lang/locallang_userprofile_lastvisitors.xml',
				'llKey'        => $this->languageKey
			)
		);

		$template->addViewHelper(
			'ts',
			'tx_community_viewhelper_Ts'
		);

		$template->addViewHelper(
			'link',
			'tx_community_viewhelper_Link'
		);

		$template->addLoop('visitors', 'user', $this->lastVisitorsModel);

		return $template->render();
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/view/userprofile/class.tx_community_view_userprofile_lastvisitors.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/view/userprofile/class.tx_community_view_userprofile_lastvisitors.php']);
}

?>