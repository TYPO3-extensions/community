<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2008-2009 Frank Naegler <typo3@naegler.net>
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
 * viewhelper class to format unix timestamps as date
 *
 * @author	Frank Naegler <typo3@naegler.net>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_viewhelper_Age implements tx_community_ViewHelper {

	/**
	 * instance of tslib_cObj
	 *
	 * @var tslib_cObj
	 */
	protected $contentObject = null;

	/**
	 * constructor for class tx_community_viewhelper_Date
	 */
	public function __construct(array $arguments = array()) {
	}

	public function execute(array $arguments = array()) {
		return $this->getAge($arguments[0]);
	}

	protected function getAge($date) {
		$year  = date("Y");
		$month = date("m");
		$day   = date("d");

		$year_diff  = $year - date("Y", $date);
		$month_diff = $month - date("m", $date);
		$day_diff   = $day - date("d", $date);
		if ($month_diff < 0 || ($day_diff < 0 && $month_diff == 0)) {
			$year_diff--;
		}
		return $year_diff;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/classes/viewhelper/class.tx_community_viewhelper_age.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/classes/viewhelper/class.tx_community_viewhelper_age.php']);
}

?>