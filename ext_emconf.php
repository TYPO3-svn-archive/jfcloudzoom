<?php

########################################################################
# Extension Manager/Repository config file for ext "jfcloudzoom".
#
# Auto generated 31-07-2010 22:40
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Cloud-Zoom',
	'description' => 'Show images as cloud zoom. Use t3jquery for better integration with other jQuery extensions.',
	'category' => 'plugin',
	'author' => 'Juergen Furrer',
	'author_email' => 'juergen.furrer@gmail.com',
	'shy' => '',
	'dependencies' => 'cms',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'alpha',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => 'uploads/tx_jfcloudzoom',
	'modify_tables' => '',
	'clearCacheOnLoad' => 1,
	'lockType' => '',
	'author_company' => '',
	'version' => '0.0.2',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'php' => '5.0.0-5.3.99',
			'typo3' => '4.1.0-4.4.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:23:{s:12:"ext_icon.gif";s:4:"1eaf";s:17:"ext_localconf.php";s:4:"19e6";s:14:"ext_tables.php";s:4:"6be6";s:15:"flexform_ds.xml";s:4:"a74c";s:13:"locallang.xml";s:4:"ab6c";s:16:"locallang_db.xml";s:4:"276a";s:12:"mode_dam.gif";s:4:"999b";s:15:"mode_damcat.gif";s:4:"2596";s:15:"mode_folder.gif";s:4:"9d05";s:15:"mode_upload.gif";s:4:"fecd";s:12:"t3jquery.txt";s:4:"8543";s:14:"doc/manual.sxw";s:4:"b260";s:39:"lib/class.tx_jfcloudzoom_cms_layout.php";s:4:"346c";s:42:"lib/class.tx_jfcloudzoom_itemsProcFunc.php";s:4:"206b";s:14:"pi1/ce_wiz.gif";s:4:"f039";s:32:"pi1/class.tx_jfcloudzoom_pi1.php";s:4:"690c";s:40:"pi1/class.tx_jfcloudzoom_pi1_wizicon.php";s:4:"fa09";s:17:"pi1/locallang.xml";s:4:"7e03";s:33:"res/jquery/js/jquery-1.4.2.min.js";s:4:"1009";s:39:"res/jquery/js/jquery.cloudzoom-1.0.2.js";s:4:"ced6";s:43:"res/jquery/js/jquery.cloudzoom-1.0.2.min.js";s:4:"428d";s:20:"static/constants.txt";s:4:"5da7";s:16:"static/setup.txt";s:4:"0407";}',
	'suggests' => array(
	),
);

?>