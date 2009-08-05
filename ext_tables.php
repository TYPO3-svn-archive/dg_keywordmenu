<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform'; // for flexform


t3lib_extMgm::addPlugin(array(
	'LLL:EXT:dg_keywordmenu/locallang_db.xml:tt_content.list_type_pi1',
	$_EXTKEY . '_pi1',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');

// NOTE: Be sure to change sampleflex to the correct directory name of your extension!        // for flexform
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:dg_keywordmenu/flexform_pi1.xml');  // for flexform


if (TYPO3_MODE == 'BE') {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_dgkeywordmenu_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_dgkeywordmenu_pi1_wizicon.php';
}

t3lib_extMgm::addStaticFile($_EXTKEY,'static/keywordmenu/', 'Keywordmenu');


t3lib_extMgm::allowTableOnStandardPages('tx_dgkeywordmenu_keywords');


t3lib_extMgm::addToInsertRecords('tx_dgkeywordmenu_keywords');

$TCA['tx_dgkeywordmenu_keywords'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:dg_keywordmenu/locallang_db.xml:tx_dgkeywordmenu_keywords',		
		'label'     => 'keyword',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'versioningWS' => TRUE, 
		'origUid' => 't3_origuid',
		'languageField'            => 'sys_language_uid',	
		'transOrigPointerField'    => 'l10n_parent',	
		'transOrigDiffSourceField' => 'l10n_diffsource',	
		'sortby' => 'sorting',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',	
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_dgkeywordmenu_keywords.gif',
	),
);
?>