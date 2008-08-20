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
 * A manager to manage community applications that are used on profile pages
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_ApplicationManager {
	/**
	 * @var tx_community_ApplicationManager
	 */
	private static $instance = null;

	/**
	 * @var array of tx_community_model_AbstractCommunityApplication
	 */
	protected $applications = array();

	/**
	 * constructor for class tx_community_ApplicationManager
	 */
	public function __construct() {
		// TODO make constructor protected, enable t3lib_div::makeInstance to detect a getInstance method
	}

	private function __clone() {
	}

	public static function getInstance() {
		if (is_null(self::$instance)) {
				// TODO use t3lib_div ...
			self::$instance = new tx_community_ApplicationManager();
		}

		return self::$instance;
	}

	public function getApplication($applicationName) {
		if (!array_key_exists($applicationName, $this->applications)) {
			// TODO throw an "application not found exception"
		}

		return $this->applications[$applicationName];
	}

	public function getWidget($widgetName) {

	}

	/**
	 * returns an array of tx_community_model_AbstractCommunityApplication
	 *
	 * @return unknown
	 */
	public function getAllApplications() {
		return $this->applications;
	}

	public function getAllWidgets() {
		$widgets               = array();
		$widgetsConfigurations = $this->getAllWidgetConfigurations();

		foreach ($widgetsConfigurations as $widgetName => $widgetConfiguration) {
			$widgets[] = t3lib_div::getUserObj($widgetConfiguration['classReference']);
		}

		return $widgets;
	}

	public function getAllWidgetConfigurations() {
		$widgetsConfigurations = array();

		if (is_array($GLOBALS['TX_COMMUNITY']['applications'])) {
			foreach ($GLOBALS['TX_COMMUNITY']['applications'] as $applicationName => $applicationConfiguration) {
				if (is_array($applicationConfiguration['widgets'])) {
					foreach ($applicationConfiguration['widgets'] as $widgetConfiguration) {
						$widgetsConfigurations[] = $widgetConfiguration;
					}
				}
			}
		}

		return $widgetsConfigurations;
	}

	public function getWidgetsByApplication($application) {
		$widgets              = array();
		$widgetConfigurations = $this->getWidgetConfigurationsByApplication($application);

		foreach ($widgetConfigurations as $widgetName => $widgetConfiguration) {
			$widgets[] = t3lib_div::getUserObj($widgetConfiguration['classReference']);
		}

		return $widgets;
	}

	public function getWidgetConfigurationsByApplication($application) {
		$widgetsConfigurations = array();

		if (is_array($GLOBALS['TX_COMMUNITY']['applications'][$application]['widgets'])) {
			foreach ($GLOBALS['TX_COMMUNITY']['applications'][$application]['widgets'] as $widgetConfiguration) {
				$widgetsConfigurations[] = $widgetConfiguration;
			}
		}

		return $widgetsConfigurations;
	}

	public function registerApplication(tx_community_model_AbstractCommunityApplication $application) {
		// TODO check whether we really need this method as registration is done through a global array
		$this->applications[$application->getId()] = $application;
	}

	public function getFlexformApplicationList(&$params, &$pObj) {
		foreach ($GLOBALS['TX_COMMUNITY']['applications'] as $applicationName => $applicationConfiguration) {
			$params['items'][] = array(
				$applicationConfiguration['label'], // TODO resolve the label
				$applicationName
			);
		}

	}

	public function getFlexformApplicationWidgetList(&$params, &$pObj) {
		$xml = new SimpleXMLElement($params['row']['pi_flexform']);
		$res = $xml->xpath('//field[@index=\'application\']');

		if ($res) {
			$selectedApplication = (string) $res[0]->value;

			foreach ($GLOBALS['TX_COMMUNITY']['applications'][$selectedApplication]['widgets'] as $widgetName => $widgetConfiguration) {
				$params['items'][] = array(
					$widgetConfiguration['label'], // TODO resolve the label
					$widgetName
				);
			}
		}
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/classes/class.tx_community_applicationmanager.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/classes/class.tx_community_applicationmanager.php']);
}

?>