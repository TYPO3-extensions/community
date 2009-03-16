<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008-2009 Frank Naegler <typo3@naegler.net>
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
 * GroupProfileActionsProvider interface for the group profile actions widget
 *
 * @package TYPO3
 * @subpackage community
 */
interface tx_community_GroupProfileActionsProvider {

	/**
	 * gets a modified array of action links for the group profile actions widget
	 *
	 * @param	array	an array of the original profile actions
	 * @param	tx_community_controller_groupprofile_ProfileActionsWidget	the parent group profile actions widget
	 * @author	Frank Naegler <typo3@naegler.net>
	 */
	public function getGroupProfileActions(array $profileActions, tx_community_controller_groupprofile_ProfileActionsWidget $profileActionsWidget);
}

?>