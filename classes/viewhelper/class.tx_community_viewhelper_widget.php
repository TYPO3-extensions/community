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
 * A viewhelper to render widgets
 *
 * @package TYPO3
 * @subpackage community
 */
class tx_community_viewhelper_Widget implements tx_community_ViewHelper {

	/**
	 * constructor for class tx_community_viewhelper_Widget
	 *
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function __construct(array $arguments = array()) {
			// nothing
	}

	/**
	 * execute method of this view helper, retrieves an instance of the
	 * requested widget, sets parameters and eventually executes it
	 *
	 * @param	array	array of arguments, [0] must be the application name in uppercase letters, words separated by underscore followed by a dot and then followed by the widget name in uppercase, words separated by underscore. example: USER_PROFILE.PROFILE_ACTIONS; [1] is optional, if set it must be a tx_community_model_User object; [2] optional, a specific action to be executed by the widget
	 * @return	string	the widget's output
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function execute(array $arguments = array()) {
		list($applicationName, $widgetName) = explode('.', $arguments[0]);
		$applicationName = $this->lcfirst($this->camelize(strtolower($applicationName)));
		$widgetName      = $this->lcfirst($this->camelize(strtolower($widgetName)));

		$widget = $GLOBALS['TX_COMMUNITY']['applicationManager']->getWidget($applicationName, $widgetName);
		/* @var $widget tx_community_controller_AbstractCommunityApplicationWidget */

		if ($arguments[1] instanceof tx_community_model_User) {
			$widget->getCommunityApplication()->setRequestedUser($arguments[1]);
		}

		$widgetAction = '';
		if (empty($arguments[2])) {
				// no specific action requested, call the default action
			$widgetAction = $GLOBALS['TX_COMMUNITY']['applications'][$applicationName]['widgets'][$widgetName]['defaultAction'];
		} else {
			$widgetAction = $arguments[2];
		}
		$widgetAction = $widgetAction . 'Action';

		return $widget->$widgetAction();
	}

	/**
	 * Returns given word as CamelCased
	 *
	 * Converts a word like "send_email" to "SendEmail". It
	 * will remove non alphanumeric characters from the word, so
	 * "who's online" will be converted to "WhoSOnline"
	 *
	 * @param	string	Word to convert to camel case
	 * @return	string	UpperCamelCasedWord
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	protected function camelize($word)
	{
		return str_replace(' ', '', ucwords(preg_replace('![^A-Z^a-z^0-9]+!', ' ', $word)));
	}

	/**
	 * counter function to ucfirst (only available in PHP > 5.3)
	 *
	 * @param	string	the string to turn its first character into lower case
	 * @return	string	the string with its first character in lower case
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	protected function lcfirst($string) {
		$string[0] = strtolower($string[0]);

		return $string;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/classes/viewhelper/class.tx_community_viewhelper_widget.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/classes/viewhelper/class.tx_community_viewhelper_widget.php']);
}

?>