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


/**
 * A viewhelper to render cObj
 *
 * @package TYPO3
 * @subpackage community
 */
class tx_community_viewhelper_GetCObj implements tx_community_ViewHelper {

	/**
	 * constructor for class tx_community_viewhelper_GetUserObject
	 *
	 * @author	Frank Naegler <typo3@naegler.net>
	 */
	public function __construct(array $arguments = array()) {
		// nothing
	}

	/**
	 * execute method of this view helper, retrieves an instance of the
	 * requested user object, sets parameters and eventually executes it
	 *
	 * @param	array	array of arguments, [0] must be the application name followed by a dot and then followed by the widget name. example: userProfile.profileActions; [1] is optional, if set it must be a tx_community_model_User object; [2] optional, a specific action to be executed by the widget
	 * @return	string	the widget's output
	 * @author	Frank Naegler <typo3@naegler.net>
	 */
	public function execute(array $arguments = array()) {
		$TSPath = $arguments[0];
		$first		= $this->getDataFromTSPath($TSPath);
		$second		= $this->getDataFromTSPath($TSPath.'.');
		$cObj = t3lib_div::makeInstance('tslib_cObj');
		return $cObj->cObjGetSingle($first, $second);
	}
	
	protected function getDataFromTSPath($TSPath) {
		$pathExploded = explode('.', trim($TSPath));
		$depth        = count($pathExploded);
		$pathBranch   = $GLOBALS['TSFE']->tmpl->setup;
		$first        = '';

		for($i = 0; $i < $depth; $i++) {
			if ($i < ($depth -1 )) {
				$pathBranch = $pathBranch[$pathExploded[$i] . '.'];
			} elseif(empty($pathExploded[$i])) {
					// It ends with a dot. We return the rest of the array
				$result = $pathBranch;
			} else {
					// It endes without a dot. We return the value.
				$result= $pathBranch[$pathExploded[$i]];
			}
		}
		
		return $result;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/classes/viewhelper/class.tx_community_viewhelper_getcobj.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/classes/viewhelper/class.tx_community_viewhelper_getcobj.php']);
}

?>