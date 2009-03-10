<?php
/***********************************************************************************************************************
put following line at the end of ext_tables.php to enable flexforms:
include(t3lib_extMgm::extPath('wow_raid').'pi1/flexform.inc');
***********************************************************************************************************************/

/* PI1 */
// hide ?,?,startingpoint
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages';
// you add pi_flexform to be renderd when your plugin is shown
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';
// add flexform description file 
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:wow_raid/pi1/flexform.xml');

?>