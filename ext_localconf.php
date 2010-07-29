<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

// Page module hook
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info']['jfcloudzoom_pi1']['jfcloudzoom'] = 'EXT:jfcloudzoom/lib/class.tx_jfcloudzoom_cms_layout.php:tx_jfcloudzoom_cms_layout->getExtensionSummary';

t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_jfcloudzoom_pi1.php', '_pi1', 'list_type', 1);

?>