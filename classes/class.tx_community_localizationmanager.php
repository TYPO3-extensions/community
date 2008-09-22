<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2008 Frank Nägler <typo3@naegler.net>
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
 * A manager to manage localizations
 *
 * @author	Frank Nägler <typo3@naegler.net>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_LocalizationManager {
	/**
	 * @var tx_community_LocalizationManager
	 */
	private static $instances = array();

	protected $llFile;							// the LL-File
	protected $LLkey = 'default';				// Pointer to the language to use.
	protected $altLLkey = '';					// Pointer to alternative fall-back language to use.

	protected $LOCAL_LANG = array();			// Local Language content
	protected $LOCAL_LANG_charset = array();	// Local Language content charset for individual labels (overriding)

	protected $LLtestPrefix = '';				// You can set this during development to some value that makes it easy for you to spot all labels that ARe delivered by the getLL function.
	protected $LLtestPrefixAlt = '';			// Save as LLtestPrefix, but additional prefix for the alternative value in getLL() function calls

	/**
	 * constructor for class tx_community_LocalizationManager
	 */
	protected function __construct($llFile, $TS) {
		$this->llFile = $llFile;
		$this->conf   = $TS;
		if ($GLOBALS['TSFE']->config['config']['language']) {
			$this->LLkey = $GLOBALS['TSFE']->config['config']['language'];
			if ($GLOBALS['TSFE']->config['config']['language_alt']) {
				$this->altLLkey = $GLOBALS['TSFE']->config['config']['language_alt'];
			}
		}

		$this->loadLL();
	}

	private function __clone() {
	}

	/**
	 * returns a LocalizationManager instance
	 *
	 * @param string $llFile
	 * @param array $TS
	 * @return tx_community_LocalizationManager
	 */
	public static function getInstance($llFile, $TS) {
		if (!isset(self::$instances[$llFile])) {
			self::$instances[$llFile] = new tx_community_LocalizationManager($llFile, $TS);
		}
		return self::$instances[$llFile];
	}

	protected function loadLL() {
		$this->LOCAL_LANG = t3lib_div::readLLfile($this->llFile, $this->LLkey, $GLOBALS['TSFE']->renderCharset);
		if ($this->altLLkey) {
			$tempLOCAL_LANG = t3lib_div::readLLfile($this->llFile, $this->altLLkey);
			$this->LOCAL_LANG = array_merge(is_array($this->LOCAL_LANG) ? $this->LOCAL_LANG : array(), $tempLOCAL_LANG);
		}

		// Overlaying labels from TypoScript (including fictitious language keys for non-system languages!):
		if (is_array($this->conf['_LOCAL_LANG.'])) {
			reset($this->conf['_LOCAL_LANG.']);
			while(list($k,$lA)=each($this->conf['_LOCAL_LANG.'])) {
				if (is_array($lA)) {
					$k = substr($k,0,-1);
					foreach($lA as $llK => $llV) {
						if (!is_array($llV)) {
							$this->LOCAL_LANG[$k][$llK] = $llV;
							// For labels coming from the TypoScript (database) the charset is assumed
							// to be "forceCharset" and if that is not set, assumed to be that of the
							// individual system languages
							$this->LOCAL_LANG_charset[$k][$llK] = $GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'] ? $GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'] : $GLOBALS['TSFE']->csConvObj->charSetArray[$k];
						}
					}
				}
			}
		}
	}

	public function getLL($key, $alt='', $hsc = false) {
		// The "from" charset of csConv() is only set for strings from TypoScript via _LOCAL_LANG
		if (isset($this->LOCAL_LANG[$this->LLkey][$key])) {
			$word = $GLOBALS['TSFE']->csConv($this->LOCAL_LANG[$this->LLkey][$key], $this->LOCAL_LANG_charset[$this->LLkey][$key]);
		} elseif ($this->altLLkey && isset($this->LOCAL_LANG[$this->altLLkey][$key])) {
			$word = $GLOBALS['TSFE']->csConv($this->LOCAL_LANG[$this->altLLkey][$key], $this->LOCAL_LANG_charset[$this->altLLkey][$key]);
		} elseif (isset($this->LOCAL_LANG['default'][$key])) {
			// No charset conversion because default is english and thereby ASCII
			$word = $this->LOCAL_LANG['default'][$key];
		} else {
			$word = $this->LLtestPrefixAlt.$alt;
		}

		$output = $this->LLtestPrefix.$word;
		if ($hsc) {
			$output = htmlspecialchars($output);
		}

		return $output;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/classes/class.tx_community_localizationmanager.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/classes/class.tx_community_localizationmanager.php']);
}

?>