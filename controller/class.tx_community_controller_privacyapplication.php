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
require_once($GLOBALS['PATH_community'] . 'controller/class.tx_community_controller_abstractcommunityapplication.php');
require_once($GLOBALS['PATH_community'] . 'classes/class.tx_community_accessmanager.php');
require_once($GLOBALS['PATH_community'] . 'model/class.tx_community_model_usergateway.php');
require_once($GLOBALS['PATH_community'] . 'view/privacy/class.tx_community_view_privacy_index.php');

/**
 * privacy management apllication controller
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_controller_PrivacyApplication extends tx_community_controller_AbstractCommunityApplication {

	protected $configuration;

	/**
	 * constructor for class tx_community_controller_PrivacyApplication
	 */
	public function __construct() {
		parent::__construct();

		$this->prefixId = 'tx_community_controller_PrivacyApplication';
		$this->scriptRelPath = 'controller/class.tx_community_controller_privacyapplication.php';
		$this->name = 'Privacy';
	}

	/**
	 * central execution and dispatching method of the privacy application. This
	 * methods decides which action to call.
	 *
	 * @return	string
	 */
	public function execute() {
		$content = '';
		$communityRequest = t3lib_div::GParrayMerged('tx_community');

		$applicationManagerClass = t3lib_div::makeInstanceClassName('tx_community_ApplicationManager');
		$applicationManager      = call_user_func(array($applicationManagerClass, 'getInstance'));
		/* @var $applicationManager tx_community_ApplicationManager */

		$applicationConfiguration = $applicationManager->getApplicationConfiguration(
			$this->getName()
		);

			// dispatch
		if (!empty($communityRequest['privacyAction'])
			&& method_exists($this, $communityRequest['privacyAction'] . 'Action')
			&& in_array($communityRequest['privacyAction'], $applicationConfiguration['actions'])
		) {
				// call a specifically requested action
			$actionName = $communityRequest['privacyAction'] . 'Action';
			$content = $this->$actionName();
		} else {
				// call the default action
			$defaultActionName = $applicationConfiguration['defaultAction'] . 'Action';
			$content = $this->$defaultActionName();
		}

		return $content;
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

		return $view->render();
	}

	public function savePermissionsAction() {
		$communityRequest = t3lib_div::GParrayMerged('tx_community');
debug($communityRequest, 'req');

			// when finished, redirect back to the privacy settings index action
		$privacySettingsPageUrl = $this->pi_getPageLink(
			$GLOBALS['TSFE']->id
		);

//		Header('HTTP/1.1 303 See Other');
//		Header('Location: ' . t3lib_div::locationHeaderUrl($privacySettingsPageUrl));
//		exit;

		return 'savePermissionsAction';
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
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_privacyapplication.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_privacyapplication.php']);
}

?>