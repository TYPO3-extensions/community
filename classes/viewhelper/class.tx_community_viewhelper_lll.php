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


require_once $GLOBALS['PATH_community'] . 'interfaces/interface.tx_community_viewhelper.php';
require_once $GLOBALS['PATH_community'] . 'classes/exception/class.tx_community_exception_languagefileunavailable.php';


/**
 * view helper to replace label markers starting with "LLL:"
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_viewhelper_Lll implements tx_community_ViewHelper {

	protected $languageFile;
	protected $llKey;
	protected $localLang = array();

	/**
	 * constructor for class tx_community_LllViewHelper
	 */
	public function __construct(array $arguments = array()) {

		if (!isset($arguments['languageFile'])) {
			throw new tx_community_exception_LanguageFileUnavailable(
				'No Language File given',
				1216132287
			);
		}
		$this->languageFile = $arguments['languageFile'];
		$this->llKey        = $arguments['llKey'];

		$this->localLang[$arguments['languageFile']] = t3lib_div::readLLfile(
			$arguments['languageFile'],
			$arguments['llKey'],
			$GLOBALS['TSFE']->renderCharset
		);
	}

	public function execute(array $arguments = array()) {
		$label = '';

		if (!strncmp($arguments[0], 'EXT', 3)) {
			// a full path reference...
			$label = $this->resolveFullPathLabel($arguments[0]);
		} else {
			$label = $this->localLang[$this->languageFile][$this->llKey][$arguments[0]];
		}

		return $label;
	}

	protected function resolveFullPathLabel($path) {
		$pathParts = explode(':', $path);

		$labelKey = array_pop($pathParts);
		$path     = implode(':', $pathParts);

		if (!isset($this->localLang[$path])) {
				// do some nice caching
			$this->localLang[$path] = t3lib_div::readLLfile(
				$path,
				$this->llKey,
				$GLOBALS['TSFE']->renderCharset
			);
		}

		return $this->localLang[$path][$this->llKey][$labelKey];
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/classes/viewhelper/class.tx_community_viewhelper_lll.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/classes/viewhelper/class.tx_community_viewhelper_lll.php']);
}

?>