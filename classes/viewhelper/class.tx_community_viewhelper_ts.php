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
 * A viewhelper to retrieve TS values and/or objects
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_viewhelper_Ts implements tx_community_ViewHelper {

	/**
	 * constructor for class tx_community_viewhelper_Ts
	 */
	public function __construct(array $arguments = array()) {

	}

	public function execute(array $arguments = array()) {
			// TODO add a feature to resolve content objects
		return $this->resolveTypoScriptPath($arguments[0]);
	}

	/**
	 * resolves a TS path and returns its value
	 *
	 * @param	string	a TS path, separated with dots
	 * @return	string
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	protected function resolveTypoScriptPath($path) {
		$pathExploded = explode('.', trim($path));
		$depth        = count($pathExploded);
		$pathBranch   = $GLOBALS['TSFE']->tmpl->setup;
		$value        = '';

		for($i = 0; $i < $depth; $i++) {
			if ($i < ($depth -1 )) {
				$pathBranch = $pathBranch[$pathExploded[$i] . '.'];
			} elseif(empty($pathExploded[$i])) {
					// It ends with a dot. We return the rest of the array
				$value = $array;
			} else {
					// It endes without a dot. We return the value.
				$value = $pathBranch[$pathExploded[$i]];
			}
		}

		return $value;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/classes/viewhelper/class.tx_community_viewhelper_ts.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/classes/viewhelper/class.tx_community_viewhelper_ts.php']);
}

?>