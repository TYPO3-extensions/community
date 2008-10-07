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
		foreach ($GLOBALS['TX_COMMUNITY']['applications'] as $applicationName => $applicationConfiguration) {
			$this->applications[$applicationName] = $applicationConfiguration;
		}
	}

	private function __clone() {
	}

	public static function getInstance() {
		if (is_null(self::$instance)) {
			self::$instance = new tx_community_ApplicationManager();
		}

		return self::$instance;
	}

	/**
	 * gets the application with the given name/id, throws an extension if no
	 * application with that name exists
	 *
	 * @param	string	application name/id
	 * @param	array	data coming from an content object (cObj)
	 * @param	array	additional configuration that is merged on top of the global community configuration environment
	 * @return	tx_community_controller_AbstractCommunityApplication
	 */
	public function getApplication($applicationName, array $data = array(), array $configuration = array()) {
		if (!array_key_exists($applicationName, $this->applications)) {
			// TODO throw an "application not found exception"
		}

		$configuration = t3lib_div::array_merge_recursive_overrule(
			$configuration,
			$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_community.']
		);

		$application = t3lib_div::getUserObj($this->applications[$applicationName]['classReference']);
		$application->initialize($data, $configuration);

		return $application;
	}

	public function getApplicationConfiguration($applicationName) {
		if (!array_key_exists($applicationName, $GLOBALS['TX_COMMUNITY']['applications'])) {
			// TODO throw an "application not found exception"
		}

		return $GLOBALS['TX_COMMUNITY']['applications'][$applicationName];
	}

	/**
	 * gets a widget of an application, the returned widget is initialized and
	 * ready to use
	 *
	 * @param	string	the name of the application the widget belongs to
	 * @param	string	the widget's name
	 * @return	tx_community_controller_AbstractCommunityApplicationWidget	the requested widget
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function getWidget($applicationName, $widgetName) {
		$application = $this->getApplication($applicationName);

		if (!array_key_exists($widgetName, $this->applications[$applicationName]['widgets'])) {
			// TODO throw an "application not found exception"
		}

		$widget = t3lib_div::getUserObj($this->applications[$applicationName]['widgets'][$widgetName]['classReference']);
		$widget->initialize($application->getData(), $application->getConfiguration());
		$widget->setCommunityApplication($application);

		return $widget;
	}

	/**
	 * returns an array of tx_community_model_AbstractCommunityApplication
	 *
	 * @return unknown
	 */
	public function getAllApplications() {
		$applications = array();

		foreach ($this->applications as $applicationName => $applicationConfiguration) {
			$applications[$applicationName] = t3lib_div::getUserObj($applicationConfiguration['classReference']);
		}

		return $applications;
	}

	public function getAllWidgets() {
		$widgets               = array();
		$widgetsConfigurations = $this->getAllWidgetConfigurations();

		foreach ($widgetsConfigurations as $widgetName => $widgetConfiguration) {
			$widgets[$widgetName] = t3lib_div::getUserObj($widgetConfiguration['classReference']);
		}

		return $widgets;
	}

	public function getAllWidgetConfigurations() {
		$widgetsConfigurations = array();

		if (is_array($GLOBALS['TX_COMMUNITY']['applications'])) {
			foreach ($GLOBALS['TX_COMMUNITY']['applications'] as $applicationName => $applicationConfiguration) {
				if (is_array($applicationConfiguration['widgets'])) {
					foreach ($applicationConfiguration['widgets'] as $widgetName => $widgetConfiguration) {
						$widgetsConfigurations[$widgetName] = $widgetConfiguration;
					}
				}
			}
		}

		return $widgetsConfigurations;
	}

	public function getWidgetsByApplication($applicationName) {
		$widgets              = array();
		$widgetConfigurations = $this->getWidgetConfigurationsByApplicationName($applicationName);

		foreach ($widgetConfigurations as $widgetName => $widgetConfiguration) {
			$widgets[$widgetName] = t3lib_div::getUserObj($widgetConfiguration['classReference']);
		}

		return $widgets;
	}

	public function getWidgetConfigurationsByApplicationName($application) {
		$widgetsConfigurations = array();

		if (is_array($GLOBALS['TX_COMMUNITY']['applications'][$application]['widgets'])) {
			foreach ($GLOBALS['TX_COMMUNITY']['applications'][$application]['widgets'] as $widgetName => $widgetConfiguration) {
				$widgetsConfigurations[$widgetName] = $widgetConfiguration;
			}
		}

		return $widgetsConfigurations;
	}

	public function getWidgetConfiguration($applicationName, $widgetName) {
		if (!array_key_exists($applicationName, $GLOBALS['TX_COMMUNITY']['applications'])) {
			// TODO throw an "application not found exception"
		}

		if (!array_key_exists($widgetName, $GLOBALS['TX_COMMUNITY']['applications'][$applicationName]['widgets'])) {
			// TODO throw an "widget not found exception"
		}

		return $GLOBALS['TX_COMMUNITY']['applications'][$applicationName]['widgets'][$widgetName];
	}

	public function registerApplication(tx_community_model_AbstractCommunityApplication $application) {
			// TODO check whether we really need this method as registration is done through a global array
		$this->applications[$application->getId()] = $application;
	}

	public function getFlexformApplicationList(&$params, &$pObj) {
		foreach ($GLOBALS['TX_COMMUNITY']['applications'] as $applicationName => $applicationConfiguration) {

			if (!(isset($applicationConfiguration['excludeFromPluginListing']) && $applicationConfiguration['excludeFromPluginListing'] == true)) {
				$params['items'][] = array(
					$GLOBALS['LANG']->sL($applicationConfiguration['label']),
					$applicationName
				);
			}
		}
	}

	public function getFlexformApplicationWidgetList(&$params, &$pObj) {
		if(!empty($params['row']['pi_flexform'])) {
			$xml = new SimpleXMLElement($params['row']['pi_flexform']);
			$res = $xml->xpath('//field[@index=\'application\']');
				// TODO use flexform methods from core
		}

		if ($res && !empty($res[0]->value)) {
			$selectedApplication = (string) $res[0]->value;
			if (is_array($GLOBALS['TX_COMMUNITY']['applications'][$selectedApplication]['widgets'])) {
				foreach ($GLOBALS['TX_COMMUNITY']['applications'][$selectedApplication]['widgets'] as $widgetName => $widgetConfiguration) {
					$params['items'][] = array(
						$GLOBALS['LANG']->sL($widgetConfiguration['label']),
						$widgetName
					);
				}
			}
		}
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/classes/class.tx_community_applicationmanager.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/classes/class.tx_community_applicationmanager.php']);
}

?>