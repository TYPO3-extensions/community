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

require_once($GLOBALS['PATH_community'] . 'interfaces/interface.tx_community_view.php');
require_once($GLOBALS['PATH_community'] . 'classes/class.tx_community_template.php');

/**
 * An abstract community view, best used for widgets
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
abstract class tx_community_view_AbstractView implements tx_community_View {

	protected $templateFile;
	protected $languageKey;

	public function setTemplateFile($templateFile) {
		$this->templateFile = $templateFile;
	}

	public function setLanguageKey($languageKey) {
		$this->languageKey = $languageKey;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/view/class.tx_community_view_abstractview.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/view/class.tx_community_view_abstractview.php']);
}

?>