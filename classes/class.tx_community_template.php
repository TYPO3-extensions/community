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

require_once(PATH_t3lib . 'class.t3lib_parsehtml.php');
require_once $GLOBALS['PATH_community'] . 'interfaces/interface.tx_community_viewhelper.php';

/**
 * templating class
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_Template {

	protected $prefix;
	protected $cObj;
	protected $templateFile;
	protected $template;
	protected $workOnSubpart;
	protected $viewHelperIncludePath;
	protected $helpers   = array();
	protected $variables = array();
	protected $markers   = array();
	protected $subparts  = array();
	protected $loops     = array();

	/**
	 * constructor for the html marker template view
	 *
	 * @param	tslib_cObj	content object
	 * @param	string	path to the template file
	 * @param	string	name of the subpart to work on
	 */
	public function __construct(tslib_cObj $contentObject, $templateFile, $subpart) {
		$this->cObj = $contentObject;
		$this->templateFile = $templateFile;

		$this->loadHtmlFile($templateFile);
		$this->workOnSubpart($subpart);
	}

	/**
	 * loads the content of a html template file. Resolves paths beginning with EXT:
	 *
	 * @param	string	path to html template file
	 */
	public function loadHtmlFile($htmlFile) {
		$this->template = $this->cObj->fileResource($htmlFile);
	}

	public function setViewHelperIncludePath($path) {
		$this->viewHelperIncludePath = $path;
	}

	/**
	 * adds a view helper
	 *
	 * @param	string	view helper name
	 * @param	string	view helper class name
	 * @param	array	optional array of arguments
	 */
	public function addViewHelper($helper, $helperClassName, array $arguments = array()) {

		if (!isset($this->helpers[$helper])) {
			/*

			// TODO an autoloader could take care of loading

			$className = 'tx_community_' . ucfirst(strtolower($helper)) . 'ViewHelper';
			$fileName   = $this->viewHelperIncludePath . 'class.' . $className . '.php';

			if (!file_exists($fileName)) {
				return false;
			}

			include_once($fileName);
			*/
			$helperClass    = t3lib_div::makeInstanceClassName($helperClassName);
			$helperInstance = new $helperClass($arguments);

			if (!$helperInstance instanceof tx_community_ViewHelper) {
				return false;
			}
			$this->helpers[$helper] = $helperInstance;
		}
	}

	/**
	 * renders the template and fills its markers
	 *
	 * @return	string the rendered html template with markers replaced with their content
	 */
	public function render() {

			// process loops
		foreach ($this->loops as $key => $loopVariables) {
			$this->renderLoop($key);
		}

			// process variables
		foreach ($this->variables as $variableKey => $variable) {
			$variableKey     = strtoupper($variableKey);
			$variableMarkers = $this->getVariableMarkers($variableKey, $this->workOnSubpart);

			$resolvedMarkers = $this->resolveVariableMarkers($variableMarkers, $variable);

			$this->workOnSubpart = t3lib_parsehtml::substituteMarkerArray(
				$this->workOnSubpart,
				$resolvedMarkers,
				'###|###'
			);
		}

			// process markers
		$this->workOnSubpart = t3lib_parsehtml::substituteMarkerArray(
			$this->workOnSubpart,
			$this->markers
		);

			// process subparts
		foreach ($this->subparts as $subpart => $content) {
			$this->workOnSubpart = t3lib_parsehtml::substituteSubpart(
				$this->workOnSubpart,
				$subpart,
				$content
			);
		}

			// process view helpers, they need to be the last objects processing the template
		$this->workOnSubpart = $this->processViewHelpers($this->workOnSubpart);

		return $this->workOnSubpart;
	}

	/**
	 * processes view helper, hands variables over if needed
	 *
	 * @param	string	the content to process by view helpers
	 * @return	string	the view helper processed content
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	protected function processViewHelpers($content) {
		foreach ($this->helpers as $helperKey => $helper) {
			$helperKey     = strtoupper($helperKey);
			$helperMarkers = $this->getHelperMarkers($helperKey, $content);

			foreach ($helperMarkers as $marker) {
				$helperArguments = explode('|', $marker);
					// TODO check whether on of the parameters is a Helper itself, if so resolve it before handing it of to the actual helper, this way the order in which viewhelpers get added to the template do not matter anymore

					// checking whether any of the helper arguments should be
					// replaced by a variable available to the template
				foreach ($helperArguments as $i => $helperArgument) {
					$lowercaseHelperArgument = strtolower($helperArgument);
					if (array_key_exists($lowercaseHelperArgument, $this->variables)) {
						$helperArguments[$i] = $this->variables[$lowercaseHelperArgument];
					}
				}

				$viewHelperContent = $helper->execute($helperArguments);

				$content = t3lib_parsehtml::substituteMarker(
					$content,
					'###' . $helperKey . ':' . $marker . '###',
					$viewHelperContent
				);
			}
		}

		return $content;
	}

	protected function renderLoop($loopName) {
		$loopContent    = '';
		$loopTemplate   = $this->getSubpart('LOOP:' . $loopName);
		$loopSingleItem = $this->getSubpart('loop_content', $loopTemplate);
		$loopMarker     = $this->loops[$loopName]['marker'];
		$loopVariables  = $this->loops[$loopName]['data'];
		$foundMarkers   = $this->getMarkersFromTemplate($loopSingleItem, $loopMarker . '\.');

		foreach ($loopVariables as $value) {
			$resolvedMarkers = $this->resolveVariableMarkers($foundMarkers, $value);

			$currentIterationContent = t3lib_parsehtml::substituteMarkerArray(
				$loopSingleItem,
				$resolvedMarkers,
				'###|###'
			);

			$processInLoopMarkers = $this->getMarkersFromTemplate(
				$currentIterationContent,
				'LOOP:',
				false
			);

			$currentIterationContent = $this->processInLoopMarkers(
				$currentIterationContent,
				$loopName,
				$processInLoopMarkers,
				$value
			);

			$loopContent .= $currentIterationContent;
		}

		$this->workOnSubpart = t3lib_parsehtml::substituteSubpart(
			$this->workOnSubpart,
			'###LOOP:' . strtoupper($loopName) . '###',
			$loopContent
		);
	}

	/**
	 * processes marker in a loop that start with LOOP:, this is useful
	 * especially for calling view helper with the current iteration's value
	 * as a parameter
	 *
	 * @param unknown_type $content
	 * @param unknown_type $loopName
	 * @param array $markers
	 * @param unknown_type $currentIterationValue
	 * @return unknown
	 */
	protected function processInLoopMarkers($content, $loopName, array $markers, $currentIterationValue) {

		foreach ($markers as $marker) {
			list($helperName, $helperArguments) = explode(':', $marker);

			$helperName      = strtolower($helperName);
			$helperArguments = explode('|', $helperArguments);

				// checking whether any of the helper arguments should be
				// replaced by the current iteration's value
			if (isset($this->loops[$loopName])) {
				foreach ($helperArguments as $i => $helperArgument) {
					if (strtoupper($this->loops[$loopName]['marker']) == strtoupper($helperArgument)) {
						$helperArguments[$i] = $currentIterationValue;
					}
				}
			}

			if (array_key_exists($helperName, $this->helpers)) {
				$markerContent = $this->helpers[$helperName]->execute($helperArguments);
			} else {
					// TODO turn this into an exception
				$markerContent = 'no matching view helper found for marker "' . $marker . '"';
			}

			$content = str_replace('###LOOP:' . $marker . '###', $markerContent, $content);
		}

		return $content;
	}

	protected function resolveVariableMarkers(array $markers, $variableValue) {
		$resolvedMarkers = array();

		foreach ($markers as $marker) {
			$dotPosition = strpos($marker, '.');

			if ($dotPosition !== false) {
					// the marker contains a dot, thus we have to resolve the second part of the marker
				$valueSelector = strtolower(substr($marker, $dotPosition + 1));

				if (is_array($variableValue)) {
					$resolvedValue = $variableValue[$valueSelector];
				} else if (is_object($variableValue)) {
					$resolveMethod = 'get' . $this->camelize($valueSelector);
					$resolvedValue = $variableValue->$resolveMethod();
				}
			} else {
				$resolvedValue = $variableValue[strtolower($marker)];
			}

			if (is_null($resolvedValue)) {
				if (t3lib_extMgm::isLoaded('community_logger')) {
					require_once(t3lib_extMgm::extPath('community_logger').'classes/class.tx_communitylogger_logger.php');
					$logger = tx_communitylogger_Logger::getInstance('community');
					$logger->debug('!!!Marker "' . $marker . '" could not be resolved.');
				}
				$resolvedValue = '';
			}

			$resolvedMarkers[$marker] = $resolvedValue;
		}

		return $resolvedMarkers;
	}

	public function workOnSubpart($subpart) {
		$this->workOnSubpart = $this->getSubpart($subpart, $this->template);
	}

	/**
	 * retrievs a supart from the given html template
	 *
	 * @param	string	subpart marker name, can be lowercase, doesn't need the ### delimiters
	 * @return	string	the html subpart
	 */
	public function getSubpart($subpartName, $alternativeTemplate = '') {
		$template = $this->workOnSubpart;

			// set altenative template to work on
		if (!empty($alternativeTemplate)) {
			$template = $alternativeTemplate;
		}

		$subpart = t3lib_parsehtml::getSubpart(
			$template,
			'###' . strtoupper($subpartName) . '###'
		);

		return $subpart;
	}

	/**
	 * sets a marker's value
	 *
	 * @param	string	marker name, can be lower case, doesn't need the ### delimiters
	 * @param	string	the marker's value
	 */
	public function addMarker($marker, $content) {
		$this->markers['###' . strtoupper($marker) . '###'] = $content;
	}

	public function addMarkerArray(array $markers) {
		foreach ($markers as $marker => $content) {
			$this->addMarker($marker, $content);
		}
	}

	/**
	 * sets a subpart's value
	 *
	 * @param	string	subpart name, can be lower case, doesn't need the ### delimiters
	 * @param	string	the subpart's value
	 */
	public function addSubpart($subpartMarker, $content) {
		$this->subparts['###' . strtoupper($subpartMarker) . '###'] = $content;
	}

	/**
	 * assigns a variable to the html template.
	 * Simple variables can be used like regular markers or in the form VAR:"VARIABLE_NAME" (without the quotes).
	 * Objects can be used in the form VAR:"OBJECT_NAME"."PROPERTY_NAME" (without the quotes)
	 *
	 * @param	string	variable key
	 * @param	mixed	variable value
	 */
	public function addVariable($key, $value) {
		$key = strtolower($key);

		if (array_key_exists($key, $this->variables)) {
				// TODO throw an exception
		} else {
			$this->variables[$key] = $value;
		}
	}

	public function addLoop($loopName, $markerName, array $variables) {
			// TODO make loops objects so that they can be nested
		$this->loops[$loopName] = array(
			'marker' => $markerName,
			'data'   => $variables
		);

		// use foreach with an "Iterator" to run through $variables

	}

	public function getMarkersFromTemplate($template, $markerPrefix = '', $capturePrefix = true) {
		$regex = '!###([A-Z0-9_-|:.]*)\###!is';

		if (!empty($markerPrefix)) {
			if ($capturePrefix) {
				$regex = '!###(' . strtoupper($markerPrefix) . '[A-Z0-9_-|:.]*)\###!is';
			} else {
				$regex = '!###' . strtoupper($markerPrefix) . '([A-Z0-9_-|:.]*)\###!is';
			}
		}

		preg_match_all($regex, $template, $match);
		$markers = array_unique($match[1]);

		return $markers;
	}

	public function getHelperMarkers($helperMarker, $subpart) {
			// '!###' . $helperMarker . ':([A-Z0-9_-|.]*)\###!is'
		preg_match_all(
			'!###' . $helperMarker . ':(.*?)\###!is',
			$subpart,
			$match
		);
		$markers = array_unique($match[1]);

		return $markers;
	}

	public function getVariableMarkers($variableMarker, $subpart) {
		preg_match_all(
			'!###(' . $variableMarker . '\.[A-Z0-9_-]*)\###!is',
			$subpart,
			$match
		);
		$markers = array_unique($match[1]);

		return $markers;
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
	 */
	protected function camelize($word)
	{
		return str_replace(' ', '', ucwords(preg_replace('![^A-Z^a-z^0-9]+!', ' ', $word)));
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/classes/class.tx_community_template.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/classes/class.tx_community_template.php']);
}

?>