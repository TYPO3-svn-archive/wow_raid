<?php
require_once(PATH_t3lib.'class.t3lib_befunc.php');
require_once(t3lib_extMgm::extPath("wow_raid")."inc/class.tx_wowraid_instances.php");

/* append this to your tca.php *****************************************************************************************

if( (TYPO3_MODE=="BE") && (t3lib_div::int_from_ver(TYPO3_version) >= 4001000) ){
  require_once(t3lib_extMgm::extPath('wow_raid').'inc/class.tx_wowraid_labels.php');
  $TCA['tx_wowraid_raids']['ctrl']['label_userFunc'] = "tx_wowraid_labels->getRaidLabel";// list view
  $TCA['tx_wowraid_comments']['ctrl']['label_userFunc'] = "tx_wowraid_labels->getCommentLabel";// list view
  $TCA["tx_wowraid_raids"]["columns"]["instance"]["config"]["itemsProcFunc"] = "tx_wowraid_labels->getRaidList";// edit view
}

***********************************************************************************************************************/

class tx_wowraid_labels{

  private $instances = null;
  
  function tx_wowraid_labels(){
    $this->instances = new tx_wowraid_instances();
  }
  
  function getRaidLabel(&$params, &$pObj) {
    //$item = t3lib_BEfunc::getRecord('tx_meineextension_tabelle2', $id); //uid aus Tabelle holen
    $params['title'] = sprintf(
      '%s (%s)',
      $this->instances->dungeons[$params['row']['instance']]['name'],
      date('d.m.Y H:i',$params['row']['begin'])
    );
  }
  
  function getCommentLabel(&$params, &$pObj) {
    $uid = $params['row'];
    $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_wowraid_raids',sprintf('uid = %d',$uid));
    $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
    $params['title'] = sprintf(
      '%s (%s)',
      $this->instances->dungeons[$row['instance']]['name'],
      date('d.m.Y H:i',$row['begin'])
    );
  }
  
  function getRaidList($config){
    $config['items'] = array();
    foreach( $this->instances->dungeons as $key => $data )$config['items'][] = array( $data['name'], $key );
    return $config;
  }

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wow_raid/inc/class.tx_wowraid_labels.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wow_raid/inc/class.tx_wowraid_labels.php']);
}
?>