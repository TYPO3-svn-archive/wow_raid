<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA["tx_wowraid_raids"] = array (
	"ctrl" => $TCA["tx_wowraid_raids"]["ctrl"],
	"interface" => array (
		"showRecordFieldList" => "hidden,fe_group,instance,begin,prepare,participants"
	),
	"feInterface" => $TCA["tx_wowraid_raids"]["feInterface"],
	"columns" => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'fe_group' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.fe_group',
			'config'  => array (
				'type'  => 'select',
				'items' => array (
					array('', 0),
					array('LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2),
					array('LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--')
				),
				'foreign_table' => 'fe_groups'
			)
		),
		"instance" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:wow_raid/locallang_db.xml:tx_wowraid_raids.instance",		
			"config" => Array (
				"type" => "select",
				"items" => Array (
					Array("LLL:EXT:wow_raid/locallang_db.xml:tx_wowraid_raids.instance.I.0", "0"),
				),
				"size" => 1,	
				"maxitems" => 1,
			)
		),
		"begin" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:wow_raid/locallang_db.xml:tx_wowraid_raids.begin",		
			"config" => Array (
				"type"     => "input",
				"size"     => "12",
				"max"      => "20",
				"eval"     => "datetime",
				"checkbox" => "0",
				"default"  => "0"
			)
		),
		"prepare" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:wow_raid/locallang_db.xml:tx_wowraid_raids.prepare",		
			"config" => Array (
				"type"     => "input",
				"size"     => "4",
				"max"      => "4",
				"eval"     => "int",
				"checkbox" => "0",
				"range"    => Array (
					"upper" => "1000",
					"lower" => "10"
				),
				"default" => 0
			)
		),
		"participants" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:wow_raid/locallang_db.xml:tx_wowraid_raids.participants",		
			"config" => Array (
				"type" => "group",	
				"internal_type" => "db",	
				"allowed" => "tx_wowcharacter_characters",	
				"size" => 10,	
				"minitems" => 0,
				"maxitems" => 40,
			)
		),
	),
	"types" => array (
		"0" => array("showitem" => "hidden;;1;;1-1-1, instance, begin, prepare, participants")
	),
	"palettes" => array (
		"1" => array("showitem" => "fe_group")
	)
);



$TCA["tx_wowraid_comments"] = array (
	"ctrl" => $TCA["tx_wowraid_comments"]["ctrl"],
	"interface" => array (
		"showRecordFieldList" => "hidden,raid,author,message"
	),
	"feInterface" => $TCA["tx_wowraid_comments"]["feInterface"],
	"columns" => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		"raid" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:wow_raid/locallang_db.xml:tx_wowraid_comments.raid",		
			"config" => Array (
				"type" => "none",
			)
		),
		"author" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:wow_raid/locallang_db.xml:tx_wowraid_comments.author",		
      "config" => Array (
        "type" => "none",
      )
		),
		"message" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:wow_raid/locallang_db.xml:tx_wowraid_comments.message",		
			"config" => Array (
				"type" => "text",
				"cols" => "30",
				"rows" => "5",
				"wizards" => Array(
					"_PADDING" => 2,
					"RTE" => array(
						"notNewRecords" => 1,
						"RTEonly" => 1,
						"type" => "script",
						"title" => "Full screen Rich Text Editing|Formatteret redigering i hele vinduet",
						"icon" => "wizard_rte2.gif",
						"script" => "wizard_rte.php",
					),
				),
			)
		),
	),
	"types" => array (
		"0" => array("showitem" => "hidden;;1;;1-1-1, raid, author, message;;;richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts]")
	),
	"palettes" => array (
		"1" => array("showitem" => "")
	)
);

if( (TYPO3_MODE=="BE") && (t3lib_div::int_from_ver(TYPO3_version) >= 4001000) ){
  require_once(t3lib_extMgm::extPath('wow_raid').'inc/class.tx_wowraid_labels.php');
  $TCA['tx_wowraid_raids']['ctrl']['label_userFunc'] = "tx_wowraid_labels->getRaidLabel";// list view
  $TCA['tx_wowraid_comments']['ctrl']['label_userFunc'] = "tx_wowraid_labels->getCommentLabel";// list view
  $TCA["tx_wowraid_raids"]["columns"]["instance"]["config"]["itemsProcFunc"] = "tx_wowraid_labels->getRaidList";// edit view
}

?>