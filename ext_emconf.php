<?php

########################################################################
# Extension Manager/Repository config file for ext: "wow_raid"
#
# Auto generated 01-03-2009 18:31
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'World of Warcraft - Raid',
	'description' => 'Raid Manager for World of Warcraft',
	'category' => 'plugin',
	'author' => 'Jobe',
	'author_email' => 'jobe@jobesoft.de',
	'shy' => '',
	'dependencies' => 'wow_character',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'experimental',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 1,
	'lockType' => '',
	'author_company' => '',
	'version' => '0.0.0',
	'constraints' => array(
		'depends' => array(
			'wow_character' => '0.1.3',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:34:{s:9:"ChangeLog";s:4:"3b4e";s:10:"README.txt";s:4:"ee2d";s:30:"class.tx_wowraid_instances.php";s:4:"2174";s:27:"class.tx_wowraid_labels.php";s:4:"0113";s:12:"ext_icon.gif";s:4:"79f2";s:17:"ext_localconf.php";s:4:"6adc";s:14:"ext_tables.php";s:4:"716c";s:14:"ext_tables.sql";s:4:"8156";s:28:"ext_typoscript_constants.txt";s:4:"0ca3";s:24:"ext_typoscript_setup.txt";s:4:"049e";s:28:"icon_tx_wowraid_comments.gif";s:4:"3031";s:25:"icon_tx_wowraid_raids.gif";s:4:"d3f0";s:13:"locallang.xml";s:4:"e944";s:16:"locallang_db.xml";s:4:"bd7a";s:7:"tca.php";s:4:"c862";s:12:"wow_raid.ppj";s:4:"cc32";s:12:"wow_raid.ppx";s:4:"6896";s:22:"doc/ArmoryDungeons.URL";s:4:"9c67";s:29:"doc/ArmoryDungeonsStrings.URL";s:4:"f6a8";s:120:"doc/Calendar a Javascript class for Mootools that adds accessible and unobtrusive date pickers to your form elements.URL";s:4:"73bc";s:25:"doc/DatePickerControl.URL";s:4:"1e18";s:19:"doc/wizard_form.dat";s:4:"d0f6";s:20:"doc/wizard_form.html";s:4:"543d";s:15:"inc/lua2php.inc";s:4:"cebc";s:14:"pi1/ce_wiz.gif";s:4:"3ff0";s:28:"pi1/class.tx_wowraid_pi1.php";s:4:"26c3";s:36:"pi1/class.tx_wowraid_pi1_wizicon.php";s:4:"f923";s:13:"pi1/clear.gif";s:4:"cc11";s:17:"pi1/locallang.xml";s:4:"8e9a";s:22:"pi1/tx_wowraid_pi1.css";s:4:"3d54";s:23:"pi1/tx_wowraid_pi1.html";s:4:"17ab";s:24:"pi1/static/editorcfg.txt";s:4:"aaa7";s:20:"pi1/static/setup.txt";s:4:"55ff";s:17:"res/gfx/crown.png";s:4:"93f1";}',
);

?>