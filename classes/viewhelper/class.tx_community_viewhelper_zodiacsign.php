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
 * viewhelper class to format unix timestamps as date
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_viewhelper_ZodiacSign implements tx_community_ViewHelper {

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
		return $this->getZodiacSign($arguments[0]);
	}

	protected function getZodiacSign($date) {
		$day	= intval(strftime('%e' ,$date));
		$month	= intval(strftime('%m' ,$date));
		// @TODO: localize strings
		if ($day > 20 && $month == 3 || $day < 21 && $month == 4) {
			$zodiacSign = "Widder";
		}
		if ($day > 20 && $month == 4 || $day < 21 && $month == 5) {
			$zodiacSign = "Stier";
		}
		if ($day > 20 && $month == 5 || $day < 22 && $month == 6) {
			$zodiacSign = "Zwillinge";
		}
		if ($day > 21 && $month == 6 || $day < 23 && $month == 7) {
			$zodiacSign = "Krebs";
		}
		if ($day > 22 && $month == 7 || $day < 24 && $month == 8) {
			$zodiacSign = "Löwe";
		}
		if ($day > 23 && $month == 8 || $day < 24 && $month == 9) {
			$zodiacSign = "Jungfrau";
		}
		if ($day > 23 && $month == 9 || $day < 24 && $month == 10) {
			$zodiacSign = "Waage";
		}
		if ($day > 23 && $month == 10 || $day < 23 && $month == 11) {
			$zodiacSign = "Skorpion";
		}
		if ($day > 22 && $month == 11 || $day < 22 && $month == 12) {
			$zodiacSign = "Schütze";
		}
		if ($day > 21 && $month == 12 || $day < 21 && $month == 1) {
			$zodiacSign = "Steinbock";
		}
		if ($day > 20 && $month == 1 || $day < 20 && $month == 2) {
			$zodiacSign = "Wassermann";
		}
		if ($day > 19 && $month == 2 || $day < 21 && $month == 3) {
			$zodiacSign = "Fische";
		}
		return $zodiacSign;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/classes/viewhelper/class.tx_community_viewhelper_date.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/classes/viewhelper/class.tx_community_viewhelper_date.php']);
}

?>