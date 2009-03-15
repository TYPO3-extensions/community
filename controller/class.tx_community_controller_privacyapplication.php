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

require_once(PATH_t3lib . 'class.t3lib_page.php');
require_once($GLOBALS['PATH_community'] . 'classes/class.tx_community_accessmanager.php');
require_once($GLOBALS['PATH_community'] . 'view/privacy/class.tx_community_view_privacy_index.php');

/**
 * privacy management apllication controller
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_controller_PrivacyApplication extends tx_community_controller_AbstractCommunityApplication {

	protected $dataSaved = false;

	/**
	 * constructor for class tx_community_controller_PrivacyApplication
	 */
	public function __construct() {
		parent::__construct();

		$this->prefixId = 'tx_community_controller_PrivacyApplication';
		$this->scriptRelPath = 'controller/class.tx_community_controller_privacyapplication.php';
		$this->name = 'privacy';
	}

	public function indexAction() {
		$view = t3lib_div::makeInstance('tx_community_view_privacy_Index');
		/* @var $view tx_community_view_privacy_Index */
		$view->setTemplateFile($this->configuration['applications.']['privacy.']['templateFile']);
		$view->setLanguageKey($this->LLkey);

		$accessControlModel = $this->getAccessControlModel();
		$publicRoles        = $this->getPublicRoles();
		$allowedRules       = $this->getAllowedRules($accessControlModel);

		$formAction = $this->pi_getPageLink(
			$GLOBALS['TSFE']->id,
			'',
			array(
				'tx_community' => array(
					'privacyAction' => 'savePermissions'
				)
			)
		);

		$view->setAccessControlModel($accessControlModel);
		$view->setRoles($publicRoles);
		$view->setAllowedRules($allowedRules);
		$view->setFormAction($formAction);
		$view->setIsSaved($this->dataSaved);

		return $view->render();
	}

	public function savePermissionsAction() {
		$pageSelect = t3lib_div::makeInstance('t3lib_pageSelect');
		$communityRequest = t3lib_div::GParrayMerged('tx_community');
		$resources = array();
		$oldRules = array();
		$newRules = array();

			// first prepare a list of resources we can than compare the user's settings against
		foreach ($communityRequest['privacy'] as $applicationName => $resourceAction) {
			foreach ($resourceAction as $resourceActionName => $roles) {
				$joinedResourceName = $applicationName . '_' . $resourceActionName . '_' . $this->getRequestingUser()->getUid();
				$resources[] = $joinedResourceName;

				foreach ($roles as $roleId => $roleAccessMode) {
					if ($roleAccessMode) {
						$newRules[] = $joinedResourceName . ':' . $roleId;
					}
				}
			}
		}

			// get the existing rules for this user
		$existingRules = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'uid, resource, role',
			'tx_community_acl_rule',
			'access_mode = 1'
				. ' AND resource IN(\'' . implode('\', \'', $resources) . '\')'
				. $pageSelect->enableFields('tx_community_acl_rule')
		);

		foreach ($existingRules as $rule) {
			$oldRules[] = $rule['resource'] . ':' . $rule['role'];
		}

			// determine what rules to add and which to remove
		$newRulesToAdd    = array_diff($newRules, $oldRules);
		$oldRulesToRemove = array_diff($oldRules, $newRules);

			// add
		foreach ($newRulesToAdd as $newRuleToAdd) {
			list($resource, $role) = explode(':', $newRuleToAdd);
			$this->addRule($resource, $role);
		}

			// remove
		foreach ($oldRulesToRemove as $oldRuleToRemove) {
			list($resource, $role) = explode(':', $oldRuleToRemove);
			$this->removeRule($resource, $role);
		}

		$this->dataSaved = true;
		return $this->indexAction();
	}

	protected function getAccessControlModel() {
			// TODO add a method to the application manager to retrieve all application configurations
		$accessControlModel = array();

		foreach ($GLOBALS['TX_COMMUNITY']['applications'] as $applicationId => $application) {
			if (is_array($application['accessControl']) && !empty($application['accessControl'])) {
					// add access control for community applications
				foreach ($application['accessControl'] as $applicationControlKey => $applicationControlLabel) {
					$accessControlModel[$applicationId][$applicationControlKey] = $applicationControlLabel;
				}
			}

			if (is_array($application['widgets']) && !empty($application['widgets'])) {
				foreach ($application['widgets'] as $widgetId => $widget) {
					if (is_array($widget['accessControl']) && !empty($widget['accessControl'])) {
							// add access control for the application's widgets
						foreach ($widget['accessControl'] as $widgetControlKey => $widgetControlLabel) {
							$accessControlModel[$applicationId][$widgetId . '_' . $widgetControlKey] = $widgetControlLabel;
						}
					}
				}
			}
		}

		return $accessControlModel;
	}

	protected function getPublicRoles() {
		$pageSelect = t3lib_div::makeInstance('t3lib_pageSelect');

		$roles = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'*',
			'tx_community_acl_role',
			'is_public = 1' . $pageSelect->enableFields('tx_community_acl_role'),
			'',
			'sorting',
			'',
			'uid'
		);

		return $roles;
	}

	protected function getAllowedRules($accessControlModel) {
		$allowedRules = array();
		$pageSelect   = t3lib_div::makeInstance('t3lib_pageSelect');

		foreach ($accessControlModel as $applicationName => $resourceAction) {
			foreach ($resourceAction as $resourceActionName => $label) {
				$rules = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
					'uid, role',
					'tx_community_acl_rule',
					'access_mode = 1'
						. ' AND resource = \'' . $applicationName . '_' . $resourceActionName . '_' . $this->getRequestingUser()->getUid() . '\''
						. $pageSelect->enableFields('tx_community_acl_rule'),
					'',
					'sorting',
					'',
					'uid'
				);

				foreach ($rules as $rule) {
					$allowedRules[$applicationName][$resourceActionName][] = $rule['role'];
				}
			}
		}

		return $allowedRules;
	}

	protected function addRule($resource, $role) {
		$GLOBALS['TYPO3_DB']->exec_INSERTquery(
			'tx_community_acl_rule',
			array(
				'pid' => $this->configuration['pages.']['aclStorage'],
				'tstamp' => $_SERVER['REQUEST_TIME'],
				'crdate' => $_SERVER['REQUEST_TIME'],
				'name' => $resource,
				'resource' => $resource,
				'role' => $role,
				'access_mode' => 1
			)
		);

		// TODO check for errors, throw exception
	}

	protected function removeRule($resource, $role) {
		$GLOBALS['TYPO3_DB']->exec_DELETEquery(
			'tx_community_acl_rule',
			'resource = \'' . $resource . '\''
				. ' AND role = ' . $role
		);

		// TODO check for errors, throw exceptions, add pid to where clause
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_privacyapplication.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_privacyapplication.php']);
}

?>