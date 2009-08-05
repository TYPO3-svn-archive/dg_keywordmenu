<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA['tx_dgkeywordmenu_keywords'] = array (
	'ctrl' => $TCA['tx_dgkeywordmenu_keywords']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'sys_language_uid,l10n_parent,l10n_diffsource,hidden,starttime,endtime,keyword,link'
	),
	'feInterface' => $TCA['tx_dgkeywordmenu_keywords']['feInterface'],
	'columns' => array (
		't3ver_label' => array (		
			'label'  => 'LLL:EXT:lang/locallang_general.xml:LGL.versionLabel',
			'config' => array (
				'type' => 'input',
				'size' => '30',
				'max'  => '30',
			)
		),
		'sys_language_uid' => array (		
			'exclude' => 1,
			'label'  => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array (
				'type'                => 'select',
				'foreign_table'       => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0)
				)
			)
		),
		'l10n_parent' => array (		
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude'     => 1,
			'label'       => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config'      => array (
				'type'  => 'select',
				'items' => array (
					array('', 0),
				),
				'foreign_table'       => 'tx_dgkeywordmenu_keywords',
				'foreign_table_where' => 'AND tx_dgkeywordmenu_keywords.pid=###CURRENT_PID### AND tx_dgkeywordmenu_keywords.sys_language_uid IN (-1,0)',
			)
		),
		'l10n_diffsource' => array (		
			'config' => array (
				'type' => 'passthrough'
			)
		),
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'keyword' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:dg_keywordmenu/locallang_db.xml:tx_dgkeywordmenu_keywords.keyword',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'required',
			)
		),
		'link' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:dg_keywordmenu/locallang_db.xml:tx_dgkeywordmenu_keywords.link',		
			'config' => array (
				'type'     => 'input',
				'size'     => '15',
				'max'      => '255',
				'checkbox' => '',
				'eval'     => 'trim',
				'wizards'  => array(
					'_PADDING' => 2,
					'link'     => array(
						'type'         => 'popup',
						'title'        => 'Link',
						'icon'         => 'link_popup.gif',
						'script'       => 'browse_links.php?mode=wizard',
						'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1'
					)
				)
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, keyword, link')
	),
	'palettes' => array (
		'1' => array('showitem' => 'starttime, endtime')
	)
);
?>