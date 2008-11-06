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
 * A manager to manage localizations
 *
 * @author	Frank Naegler <typo3@naegler.net>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_LocalizationManager {

	/**
	 * @var tx_community_LocalizationManager
	 */
	static private $instances              = array();

	protected $configuration               = array();

		// the LL-File
	protected $localLanguageFile;

		// Pointer to the language to use.
	protected $localLanguageKey            = 'default';

		// Pointer to alternative fall-back language to use.
	protected $alternativeLocalLanguageKey = '';

		// Local Language content
	protected $localLanguageLabels         = array();

		// Local Language content charset for individual labels (overriding)
	protected $localLanguageLabelsCharset  = array();

		// You can set this during development to some value that makes it easy for you to spot all labels that ARe delivered by the getLL function.
	protected $localLanguageTestPrefix            = '';

		// Save as LLtestPrefix, but additional prefix for the alternative value in getLL() function calls
	protected $alternativeLocalLanguageTestPrefix = '';

	/**
	 * constructor for class tx_community_LocalizationManager
	 */
	protected function __construct($localLanguageFile, array $configuration) {
		$this->localLanguageFile = $localLanguageFile;
		$this->configuration     = $configuration;

		if ($GLOBALS['TSFE']->config['config']['language']) {
			$this->localLanguageKey = $GLOBALS['TSFE']->config['config']['language'];

			if ($GLOBALS['TSFE']->config['config']['language_alt']) {
				$this->alternativeLocalLanguageKey = $GLOBALS['TSFE']->config['config']['language_alt'];
			}
		}

		$this->loadLL();
	}

	private function __clone() {}

	/**
	 * returns a LocalizationManager instance
	 *
	 * @param	string	local language file path
	 * @param	array	TypoScript configuration
	 * @return	tx_community_LocalizationManager	instance of the localization manager for the given LLL file
	 */
	public static function getInstance($localLanguageFile, array $configuration) {
		if (!isset(self::$instances[$localLanguageFile])) {
			self::$instances[$localLanguageFile] = new tx_community_LocalizationManager($localLanguageFile, $configuration);
		}

		return self::$instances[$localLanguageFile];
	}

	/**
	 * loads the labels from a local language file
	 *
	 * @author	Ingo Renner <ingo@typo3.org>
	 * @author	Frank Naegler <typo3@naegler.net>
	 */
	protected function loadLL() {
		$this->localLanguageLabels = t3lib_div::readLLfile(
			$this->localLanguageFile,
			$this->localLanguageKey,
			$GLOBALS['TSFE']->renderCharset
		);

		if ($this->alternativeLocalLanguageKey) {
			$tempLocalLangueLabels = t3lib_div::readLLfile(
				$this->localLanguageFile,
				$this->alternativeLocalLanguageKey
			);

			$this->localLanguageLabels = array_merge(
				is_array($this->localLanguageLabels) ?
					$this->localLanguageLabels :
					array(),
				$tempLocalLangueLabels
			);
		}

			// Overlaying labels from TypoScript (including fictitious language keys for non-system languages!):
		if (is_array($this->configuration['_LOCAL_LANG.'])) {

			foreach ($this->configuration['_LOCAL_LANG.'] as $language => $overideLabels) {
				if (is_array($overideLabels)) {
					foreach ($overideLabels as $labelKey => $overideLabel) {
						if (!is_array($overideLabel)) {
							$this->localLanguageLabels[$language][$labelKey] = $overideLabel;

								// For labels coming from the TypoScript (database) the charset is assumed
								// to be "forceCharset" and if that is not set, assumed to be that of the
								// individual system languages
							$this->localLanguageLabelsCharset[$language][$labelKey] = $GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'] ?
								$GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'] :
								$GLOBALS['TSFE']->csConvObj->charSetArray[$language];
						}
					}
				}
			}
		}
	}

	public function getLL($labelKey, $alternativeLabel = '', $hsc = false) {
			// The "from" charset of csConv() is only set for strings from TypoScript via _LOCAL_LANG
		if (isset($this->localLanguageLabels[$this->localLanguageKey][$labelKey])) {
			$word = $GLOBALS['TSFE']->csConv(
				$this->localLanguageLabels[$this->localLanguageKey][$labelKey],
				$this->localLanguageLabelsCharset[$this->localLanguageKey][$labelKey]
			);
		} elseif ($this->alternativeLocalLanguageKey && isset($this->localLanguageLabels[$this->alternativeLocalLanguageKey][$labelKey])) {
			$word = $GLOBALS['TSFE']->csConv(
				$this->localLanguageLabels[$this->alternativeLocalLanguageKey][$labelKey],
				$this->localLanguageLabelsCharset[$this->alternativeLocalLanguageKey][$labelKey]
			);
		} elseif (isset($this->localLanguageLabels['default'][$labelKey])) {
				// No charset conversion because default is english and thereby ASCII
			$word = $this->localLanguageLabels['default'][$labelKey];
		} else {
			$word = $this->alternativeLocalLanguageTestPrefix . $alternativeLabel;
		}

		$output = $this->localLanguageTestPrefix . $word;

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