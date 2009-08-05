<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Dennis Grote <d.grote@dd-medien.de>
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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Plugin 'Keywordmenu' for the 'dg_keywordmenu' extension.
 *
 * @author	Dennis Grote <d.grote@dd-medien.de>
 * @package	TYPO3
 * @subpackage	tx_dgkeywordmenu
 */
class tx_dgkeywordmenu_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_dgkeywordmenu_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_dgkeywordmenu_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'dg_keywordmenu';	// The extension key.
	var $pi_checkCHash = true;
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf) {
		$this->init($conf);
		$content='';

		if ($this->conf['isLoaded']!='yes')
	      return $this->pi_getLL('errorIncludeStatic');
		
		// what will be displayed
		switch ($this->theCode) {
			case 'KEYWORDS':
				$content .= $this->display_list_pages();
				break;

			case 'SINGLE':
				$content .= $this->display_list_menu();
				break;
				
			case 'MENU':
				$content .= $this->display_menu();
				break;
				
			default:
				$content .= '<h1>Select a display type</h1><p>Chose from flexform or type in TS-setup; KEYWORDS, SINGLE or MENU<br />For more information look at the manual</p>';
		}
	
//		t3lib_div::debug($this->conf, FLEXuTS);

		return $this->pi_wrapInBaseClass($content);
	}



	/**
	 * Initialize the Plugin
	 *
	 * @param	array		$conf: The PlugIn configuration
	 * @return	void
	 */
	function init($conf) {

		$this->conf = $conf;				// TypoScript configuration
		$this->pi_setPiVarDefaults();		// GetPost-parameter configuration
		$this->pi_loadLL();					// localized language variables
		$this->pi_initPIflexForm();			// Initialize the FlexForms array
		
		// load flexform in &conf only if TS code is not set
		if (!$this->conf['code']) {
			$piFlexForm = $this->cObj->data['pi_flexform'];
				foreach($piFlexForm['data'] as $sheet => $data) {
				    foreach($data as $lang => $value) {
						foreach($value as $key => $val) {
						    $this->conf[$key] = $this->pi_getFFvalue($piFlexForm, $key, $sheet);
						}
					}
				}
		}
			
		// load template priority on Flexform
		if ($this->conf['template_file']) {
			$this->tmpl = $this->cObj->fileResource($this->conf['template_file']);
		} else {
			$this->tmpl = $this->cObj->fileResource($this->conf['templateFile']);
		}
		
		// from which ID are the entries priority on Flexform
		if ($this->conf['storage_pid']) {
			$this->pidList = $this->conf['storage_pid'];
		} elseif ($this->conf['pidList']) {
			$this->pidList = $this->conf['pidList'];
		} else {
			$this->pidList = $GLOBALS['TSFE']->id;
		}
		
		// put what to display in theCode priority on TS
		$this->theCode = $this->conf['code'] ? $this->conf['code'] : $this->conf['what_to_display'];

		// pid for single with priority on Flexform
		$this->conf['singlePid'] = $this->conf['single_pid'] ? $this->conf['single_pid'] : $this->conf['singlePid'];
		if ($this->theCode == 'SINGLE') $this->conf['singlePid'] = $GLOBALS['TSFE']->id;
		
	}
	
	/**
	 * display keywordlist
	 *
	 * @return	html code of the keywordlist
	 */
	function display_list_pages() {
		// Read in the part of the template file for keyword listing
		$template = $this->cObj->getSubpart($this->tmpl, '###TEMPLATE_KEYWORD_LIST###');
		// Get subpart template
		$subTemplate = $this->cObj->getSubpart($template, '###KEYWORDS###');
		
		// query normal
//		$select = 'keyword, link'; 
//		$from = 'tx_dgkeywordmenu_keywords';
//		$where = 'deleted = 0 AND hidden = 0 AND uid in ('.$this->conf['keywords'].') AND pid = '.$this->pidList.'';
//		$orderBy = 'keyword';
//		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, $orderBy);
		
		// query mit inner join nach der alten schreibweise
		$select = 'T1.keyword, T1.link';
		$from = 'tx_dgkeywordmenu_keywords T1, pages T2';
		$where = 'T1.link=T2.uid AND T1.deleted = 0 AND T1.hidden = 0 AND T2.deleted = 0 AND T2.hidden = 0 AND T1.uid in ('.$this->conf['keywords'].') AND T1.pid = '.$this->pidList.'';
		$groupBy = '';
		$orderBy = 'T1.keyword';
		$limit = '';
		
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);
				
		// so sollte ein inner join eigentlich aussehen
//		SELECT T1.keyword, T1.link, T2.uid, T2.title 
//		FROM tx_dgkeywordmenu_keywords T1 
//		INNER JOIN pages T2 ON T1.link=T2.uid
//		WHERE T2.deleted = 0 AND T2.hidden = 0
//		ORDER BY T1.keyword;
		
		// get keywords from database and put it in marker
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {			
			$typolink_conf=array(
				"title" => $row['keyword'],
				"no_cache" => 0,
				"parameter" => $row['link'],
				"additionalParams" => '');
			
			$listItem = $this->cObj->typolink($row['keyword'], $typolink_conf);
			$subPartContent .= $this->cObj->substituteMarker($subTemplate, '###KEYWORD###', $listItem);
		}
		
		// Substitute subpart
		$content = $this->cObj->substituteSubpart($template, '###KEYWORDS###', $subPartContent);

		return $content;
	}
		
	
	/**
	 * display keywordlist with menu
	 *
	 * @return	The content that is displayed on the website
	 */
	function display_list_menu() {
		
	}
	
	/**
	 * displays the a-z menu
	 *
	 * @return	html code of the a-z menu
	 */
	function display_menu() {
		$menuIndexKeys = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
		
		// Read in the part of the template file for keyword listing
		$template = $this->cObj->getSubpart($this->tmpl, '###TEMPLATE_MENU###');
		// Get subpart template
		$subTemplate = $this->cObj->getSubpart($template, '###LETTERS###');
		
		if ($this->conf['singlePid'] == '') return '<p>Please chose the page where the list with menu are shown, in Flexform or TS</p>';
		
		$currentLetter = htmlspecialchars($this->piVars['letter']);
		
		foreach ($menuIndexKeys as $indexKeys) {
			if ($currentLetter == $indexKeys){
				$menu = $indexKeys;
			} else {
				$menu = $this->pi_linkTP($indexKeys, array($this->prefixId.'[letter]' => $indexKeys), 1, $this->conf['singlePid']);	
			}	
			$subPartContent .= $this->cObj->substituteMarker($subTemplate, '###LETTER###', $menu);
		}
	
		$content = $this->cObj->substituteSubpart($template, '###LETTERS###', $subPartContent);
		
		return $content;
	}
	

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dg_keywordmenu/pi1/class.tx_dgkeywordmenu_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dg_keywordmenu/pi1/class.tx_dgkeywordmenu_pi1.php']);
}

?>