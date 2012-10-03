<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

// Static
t3lib_extMgm::addStaticFile($_EXTKEY, 'static/', 'Cloud-Zoom');
t3lib_extMgm::addStaticFile($_EXTKEY, 'static/tt_content/', 'Cloud-Zoom for tt_content');

// CONTENT
$tempColumns = Array (
	"tx_jfcloudzoom_activate" => Array (
		"exclude" => 1,
		"label" => "LLL:EXT:jfcloudzoom/locallang_db.xml:tt_content.tx_jfcloudzoom_activate",
		"config" => Array (
			"type" => "check",
		)
	),
	"tx_jfcloudzoom_factor" => Array (
		"exclude" => 1,
		"label" => "LLL:EXT:jfcloudzoom/locallang_db.xml:tt_content.tx_jfcloudzoom_factor",
		"config" => Array (
			"type" => "input",
			"size" => "5",
			"trim" => "int",
			"default" => "2"
		)
	),
);

// tt_content
t3lib_div::loadTCA('tt_content');
t3lib_extMgm::addTCAcolumns('tt_content', $tempColumns, 1);
$TCA['tt_content']['palettes']['tx_jfcloudzoom'] = array(
	'showitem' => 'tx_jfcloudzoom_activate,tx_jfcloudzoom_factor',
	'canNotCollapse' => 1,
);
t3lib_extMgm::addToAllTCAtypes('tt_content', '--palette--;LLL:EXT:jfcloudzoom/locallang_db.xml:tt_content.tx_jfcloudzoom_title;tx_jfcloudzoom', 'textpic,image', 'before:imagecaption');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1'] = 'layout,select_key,pages';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1'] = 'pi_flexform';

// tt_news
if (t3lib_extMgm::isLoaded('tt_news')) {
	t3lib_extMgm::addStaticFile($_EXTKEY, 'static/tt_news/', 'Cloud-Zoom for tt_news');
	t3lib_div::loadTCA('tt_news');
	t3lib_extMgm::addTCAcolumns('tt_news', $tempColumns, 1);
	$TCA['tt_news']['palettes']['tx_jfcloudzoom'] = array(
		'showitem' => 'tx_jfcloudzoom_activate,tx_jfcloudzoom_factor',
		'canNotCollapse' => 1,
	);
	t3lib_extMgm::addToAllTCAtypes('tt_news', '--palette--;LLL:EXT:jfcloudzoom/locallang_db.xml:tt_content.tx_jfcloudzoom_title;tx_jfcloudzoom', '', 'after:image');
}

// tt_products
if (t3lib_extMgm::isLoaded('tt_products')) {
	t3lib_extMgm::addStaticFile($_EXTKEY, 'static/tt_products/', 'Cloud-Zoom for tt_products');
	t3lib_div::loadTCA('tt_products');
	t3lib_extMgm::addTCAcolumns('tt_products', $tempColumns, 1);
	$TCA['tt_products']['palettes']['tx_jfcloudzoom'] = array(
		'showitem' => 'tx_jfcloudzoom_activate,tx_jfcloudzoom_factor',
		'canNotCollapse' => 1,
	);
	t3lib_extMgm::addToAllTCAtypes('tt_products', '--palette--;LLL:EXT:jfcloudzoom/locallang_db.xml:tt_content.tx_jfcloudzoom_title;tx_jfcloudzoom', '', 'after:image');
}



// ICON
t3lib_extMgm::addPlugin(array(
	'LLL:EXT:jfcloudzoom/locallang_db.xml:tt_content.list_type_pi1',
	$_EXTKEY . '_pi1',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');

t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:'.$_EXTKEY.'/flexform_ds.xml');

if (TYPO3_MODE == 'BE') {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_jfcloudzoom_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_jfcloudzoom_pi1_wizicon.php';
}

require_once(t3lib_extMgm::extPath($_EXTKEY).'lib/class.tx_jfcloudzoom_itemsProcFunc.php');
?>