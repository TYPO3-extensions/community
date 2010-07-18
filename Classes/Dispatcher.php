<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Pascal Jungblut <mail@pascalj.de>
*
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

class Tx_Community_Dispatcher extends Tx_Extbase_Dispatcher {

	/**
	 * @var array
	 */
	protected $settings = array();


	/**
	 * Creates a request an dispatches it to a controller.
	 *
	 * @param string $content The content
	 * @param array $configuration The TS configuration array
	 * @return string $content The processed content
	 */
	public function dispatch($content, $configuration) {

		$this->timeTrackPush('Extbase is called.','');
		$this->timeTrackPush('Extbase gets initialized.','');

		if (!is_array($configuration)) {
			t3lib_div::sysLog('Extbase was not able to dispatch the request. No configuration.', 'extbase', t3lib_div::SYSLOG_SEVERITY_ERROR);
			return $content;
		}

		$this->initializeConfigurationManagerAndFrameworkConfiguration($configuration);
		$this->settings = is_array(self::$extbaseFrameworkConfiguration['settings']) ? self::$extbaseFrameworkConfiguration['settings'] : array();
		$requestBuilder = t3lib_div::makeInstance('Tx_Extbase_MVC_Web_RequestBuilder');
		$request = $requestBuilder->initialize(self::$extbaseFrameworkConfiguration);
		$request = $requestBuilder->build();
		if (isset($this->cObj->data) && is_array($this->cObj->data)) {
			// we need to check the above conditions as cObj is not available in Backend.
			$request->setContentObjectData($this->cObj->data);
			$request->setIsCached(false);//$this->cObj->getUserObjectType() == tslib_cObj::OBJECTTYPE_USER);
		}
		$response = t3lib_div::makeInstance('Tx_Extbase_MVC_Web_Response');

		// Request hash service
		$requestHashService = t3lib_div::makeInstance('Tx_Extbase_Security_Channel_RequestHashService'); // singleton
		$requestHashService->verifyRequest($request);

		$persistenceManager = self::getPersistenceManager();

		$this->timeTrackPull();

		$this->timeTrackPush('Extbase dispatches request.','');
		$dispatchLoopCount = 0;
		while (!$request->isDispatched()) {
			if ($dispatchLoopCount++ > 99) throw new Tx_Extbase_MVC_Exception_InfiniteLoop('Could not ultimately dispatch the request after '  . $dispatchLoopCount . ' iterations.', 1217839467);
			$controller = $this->getPreparedController($request);
			try {
				if (
					($controller instanceof Tx_Community_Controller_Cacheable_ControllerInterface) &&
					$this->isCachableAction($request->getControllerName(), $request->getControllerActionName())
				) {
					$content = $this->getCacheEntry($controller, $request);
					if ($content) {
						$response->appendContent($content);
						$request->setDispatched(TRUE);
						continue;
					}
				}

				$controller->processRequest($request, $response);

				if (
					($controller instanceof Tx_Community_Controller_Cacheable_ControllerInterface) &&
					$this->isCachableAction($request->getControllerName(), $request->getControllerActionName())
				) {					$content = $response->getContent();
					$tags = $controller->getTags();
					$identifier = $controller->getIdentifier($request);
					$this->setCacheEntry($content, $identifier, $tags);
				}
			} catch (Tx_Extbase_MVC_Exception_StopAction $ignoredException) {
			}
		}
		$this->timeTrackPull();

		$this->timeTrackPush('Extbase persists all changes.','');
		$flashMessages = t3lib_div::makeInstance('Tx_Extbase_MVC_Controller_FlashMessages'); // singleton
		$flashMessages->persist();
		$persistenceManager->persistAll();
		$this->timeTrackPull();

		self::$reflectionService->shutdown();

		if (count($response->getAdditionalHeaderData()) > 0) {
			$GLOBALS['TSFE']->additionalHeaderData[$request->getControllerExtensionName()] = implode("\n", $response->getAdditionalHeaderData());
		}
		$response->sendHeaders();
		$this->timeTrackPull();
		return $response->getContent();
	}

	protected function getCacheEntry(Tx_Community_Controller_Cacheable_ControllerInterface $controller, $request) {
		$id = $controller->getIdentifier($request);
		$cacheHandler = t3lib_div::makeInstance('tx_enetcache');
		return $cacheHandler->get($id);
	}

	protected function setCacheEntry($content, array $identifier, array $tags) {

		t3lib_div::makeInstance('tx_enetcache')->set($identifier, $content, $tags);
	}

	protected function isCachableAction($controller, $action) {
		if (
			is_array($this->settings['caching']) &&
			$this->settings['caching'][$controller]
		) {
			return in_array($action, explode(',', $this->settings['caching'][$controller]));
		} else {
			return false;
		}
	}
}
?>