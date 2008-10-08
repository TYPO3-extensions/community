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

require_once($GLOBALS['PATH_community'] . 'view/invite/class.tx_community_view_invite_index.php');
require_once($GLOBALS['PATH_community'] . 'view/invite/class.tx_community_view_invite_message.php');

/**
 * A community application to invite new users
 *
 * @package TYPO3
 * @subpackage community
 */
class tx_community_controller_InviteApplication extends tx_community_controller_AbstractCommunityApplication {

	/**
	 * constructor for class tx_community_controller_InviteApplication
	 *
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function __construct() {
		parent::__construct();

		$this->prefixId = 'tx_community_controller_InviteApplication';
		$this->scriptRelPath = 'controller/class.tx_community_controller_inviteapplication.php';
		$this->name = 'invite';
	}

	public function indexAction() {
		$view = t3lib_div::makeInstance('tx_community_view_invite_Index');
		$view->setTemplateFile($this->configuration['applications.']['invite.']['templateFile']);
		$view->setLanguageKey($this->LLkey);

		$view->setFormAction($this->pi_getPageLink($GLOBALS['TSFE']->id));

		return $view->render();
	}

	public function sendInvitationAction() {
		$communityRequest = t3lib_div::GParrayMerged('tx_community');

		if (empty($communityRequest['invite']['name'])
		|| empty($communityRequest['invite']['email'])
		|| !filter_var(trim($communityRequest['invite']['email']), FILTER_VALIDATE_EMAIL)) {
				// error, bad bad
			$errorMessageUrl = $this->pi_getPageLink(
				$GLOBALS['TSFE']->id,
				'',
				array(
					'tx_community' => array(
						$this->name . 'Action' => 'errorMessage'
					)
				)
			);

			Header('HTTP/1.1 303 See Other');
			Header('Location: ' . t3lib_div::locationHeaderUrl($errorMessageUrl));
			exit;
		} else {
			$requestingUser = $this->getRequestingUser();
			$communityConfiguration = $this->getCommunityTypoScriptConfiguration();
			$localizationManager = tx_community_LocalizationManager::getInstance(
				'EXT:community/lang/locallang_invite.xml',
				$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_community.']
			);

			$recipientName  = filter_var($communityRequest['invite']['name']);
			$recipientEmail = filter_var(
				trim($communityRequest['invite']['email']),
				FILTER_VALIDATE_EMAIL
			);

			$messageSubject = $localizationManager->getLL('invite_email_subject');
			$messageBody    = $localizationManager->getLL('invite_email_body');

			$messageSubject = str_replace(
				array(
					'###COMMUNITY_NAME###',
					'###USER.combinedName###'
				),
				array(
					$communityConfiguration['general.']['communityName'],
					$requestingUser->getCombinedName()
				),
				$messageSubject
			);

			$messageBody = str_replace(
				array(
					'###NAME###',
					'###COMMUNITY_NAME###',
					'###USER.combinedName###',
					'###REGISTRATION_LINK###'
				),
				array(
					$recipientName,
					$communityConfiguration['general.']['communityName'],
					$requestingUser->getCombinedName(),
					t3lib_div::getIndpEnv('TYPO3_SITE_URL') . $this->pi_getPageLink($communityConfiguration['pages.']['userRegistration'])
				),
				$messageBody
			);

			t3lib_div::plainMailEncoded(
				$recipientEmail,
				$messageSubject,
				$messageBody
			);

			$successMessageUrl = $this->pi_getPageLink(
				$GLOBALS['TSFE']->id,
				'',
				array(
					'tx_community' => array(
						$this->name . 'Action' => 'successMessage'
					)
				)
			);

			Header('HTTP/1.1 303 See Other');
			Header('Location: ' . t3lib_div::locationHeaderUrl($successMessageUrl));
			exit;
		}
	}

	public function errorMessageAction() {
		$view = t3lib_div::makeInstance('tx_community_view_invite_Message');
		$view->setTemplateFile($this->configuration['applications.']['invite.']['templateFile']);
		$view->setLanguageKey($this->LLkey);

		$localizationManager = tx_community_LocalizationManager::getInstance(
			'EXT:community/lang/locallang_invite.xml',
			$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_community.']
		);

		$view->setMessage($localizationManager->getLL('invite_message_error'));

		return $view->render();
	}

	public function successMessageAction() {
		$view = t3lib_div::makeInstance('tx_community_view_invite_Message');
		$view->setTemplateFile($this->configuration['applications.']['invite.']['templateFile']);
		$view->setLanguageKey($this->LLkey);

		$localizationManager = tx_community_LocalizationManager::getInstance(
			'EXT:community/lang/locallang_invite.xml',
			$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_community.']
		);

		$view->setMessage($localizationManager->getLL('invite_message_success'));

		return $view->render();
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_inviteapplication.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/controller/class.tx_community_controller_inviteapplication.php']);
}

?>