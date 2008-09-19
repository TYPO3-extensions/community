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
 * criteria for selection of records represented as an object
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage community
 */
class tx_community_model_ModelCriteria {

	const MODE_GREATER_THAN = '>';
	const MODE_GREATER_THAN_EQUAL = '>=';
	const MODE_LESS_THAN = '<';
	const MODE_LESS_THAN_EQUAL = '<=';
	const MODE_EQUAL = '=';
	const MODE_LIKE = 'LIKE';

	protected $table;
	protected $conditions;

	/**
	 * constructor for class tx_community_model_ModelCriteria
	 */
	public function __construct($tableName) {
		$this->table = $tableName;

		t3lib_div::loadTCA($tableName);
	}

	public function add($column, $value, $mode = tx_community_model_ModelCriteria::MODE_EQUAL) {
		if (array_key_exists($column, $GLOBALS['TCA'][$this->table]['columns'])) {
			$columnConfiguration = $GLOBALS['TCA'][$this->table]['columns'][$column];


		}
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/model/class.tx_community_model_modelcriteria.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/community/model/class.tx_community_model_modelcriteria.php']);
}

?>