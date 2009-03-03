<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key';


t3lib_extMgm::addPlugin(array('LLL:EXT:wow_raid/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');


t3lib_extMgm::addStaticFile($_EXTKEY,'pi1/static/','World of Warcraft - Raids');


if (TYPO3_MODE=="BE")	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_wowraid_pi1_wizicon"] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_wowraid_pi1_wizicon.php';


$TCA["tx_wowraid_raids"] = array (
	"ctrl" => array (
		'title'     => 'LLL:EXT:wow_raid/locallang_db.xml:tx_wowraid_raids',		
		'label'     => 'instance',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => "ORDER BY crdate",	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',	
			'fe_group' => 'fe_group',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_wowraid_raids.gif',
	),
	"feInterface" => array (
		"fe_admin_fieldList" => "hidden, fe_group, instance, begin, prepare, participants",
	)
);

$TCA["tx_wowraid_comments"] = array (
	"ctrl" => array (
		'title'     => 'LLL:EXT:wow_raid/locallang_db.xml:tx_wowraid_comments',		
		'label'     => 'raid',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => "ORDER BY crdate",	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_wowraid_comments.gif',
	),
	"feInterface" => array (
		"fe_admin_fieldList" => "hidden, raid, author, message",
	)
);
?>