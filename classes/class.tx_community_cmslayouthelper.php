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


/**
 * Helper class to display a summary in the page module about what the
 * extension is showing in the frontend
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_CmsLayoutHelper {

	/**
	 * Returns information about which application and which widget is selected
	 *
	 * @param	array		Parameters coming from the hook
	 * @param	tx_cms_layout		Reference to the calling tx_cms_layout object
	 * @return	string		Information about pi1 plugin
	 */
	public function getExtensionSummary(array $parameters, &$pageModule) {
		$content = '';
		$data    = t3lib_div::xml2array($parameters['row']['pi_flexform']);

		$application = $data['data']['sDEF']['lDEF']['application']['vDEF'];
		$widget      = $data['data']['sDEF']['lDEF']['widget']['vDEF'];

		$applicationLabel = $GLOBALS['LANG']->sL($GLOBALS['TX_COMMUNITY']['applications'][$application]['label']);

		$widgetLabel = '-';
		if ($widget) {
			$widgetLabel = $GLOBALS['LANG']->sL($GLOBALS['TX_COMMUNITY']['applications'][$application]['widgets'][$widget]['label']);
		}

		$content .= 'Application: ' . $applicationLabel . '<br />';
		$content .= 'Widget: ' . $widgetLabel;

		return $content;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/classes/class.tx_community_cmslayouthelper.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/classes/class.tx_community_cmslayouthelper.php']);
}

?>