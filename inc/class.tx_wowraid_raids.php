<?php /* require_once(t3lib_extMgm::extPath('wow_raid').'inc/class.tx_wowraid_raid.php' );/*raid*/

require_once(t3lib_extMgm::extPath('wow_raid').'inc/class.tx_wowraid_instances.php');/*instances*/

/**********************************************************************************************************************/

class tx_wowraid_raids{
    
    private $raids = null;
      
    public function tx_wowraid_raids($pid){
      $this->query($pid);// query all raids
    }
    
    /**
    * @desc Query DB for single raid or full list.
    */
    private function query($pid){
      $this->raids = array();// clear raid list
      $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid','tx_wowraid_raids',sprintf('pid = %d',$pid));// query db
      while( $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ){
        $this->raids[$row['uid']] = new tx_wowraid_raid($row['uid']);// fill data
      }
    }
    
    /**
    * @desc Return given raid or full list.
    */
    public function get($uid=null){ if($uid) return $this->raids[$uid]; else return $this->raids; }
    
}

/**********************************************************************************************************************/

class tx_wowraid_raid{
  
  private $raid = null;
    
  public function tx_wowraid_raid($uid){
    $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_wowraid_raids',sprintf('uid = %d',$uid));// query db
    $this->raid = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
  }
  
}    

/**********************************************************************************************************************/

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wow_raid/class.tx_wowraid_raids.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wow_raid/class.tx_wowraid_raids.php']);
}

?>
