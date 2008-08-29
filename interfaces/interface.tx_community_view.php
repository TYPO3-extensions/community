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
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/


/**
 * View interface for the MVC pattern
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
interface tx_community_View {

	/**
	 * render method
	 *
	 * @return	string	the output of the view (HTML in most cases)
	 */
	public function render();

	/**
	 * sets the template file for the view
	 *
	 * @param	string	file reference, can start with 'EXT:'
	 */
	public function setTemplateFile($templateFile);

	/**
	 * sets the of the language the out put should be in
	 *
	 * @param	string	TYPO3 language key
	 */
	public function setLanguageKey($languageKey);
}

?>