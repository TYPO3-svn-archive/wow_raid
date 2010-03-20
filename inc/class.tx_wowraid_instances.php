<?php /* require_once(t3lib_extMgm::extPath('wow_raid').'inc/class.tx_wowraid_instances.php');/*instances*/

DEFINE(TYPO3TEMP,PATH_site.'/typo3temp/');
DEFINE(CACHETIME,2592000);/* = 30 days */

class tx_wowraid_instances{
    
    private $xml1 = null;
    private $xml2 = null;
    private $tmp = null;
    private $local = null;

    public $dungeons = array();
      
    public function tx_wowraid_instances($lang='de-DE'){
      if(!eregi('^([a-z]{2})[-_]{1}([a-z]{2}).*$',$lang,$this->local))throw new Exception('could not read locale');// get system language
      //$dungeon_names = array();
      //$boss_names = array();
      if( !$this->load() && $this->query() ) $this->save();
      foreach( $this->xml2->dungeons->dungeon as $dungeon_num => $dungeon ){
        $this->dungeons[intval($dungeon['id'])] = array(
          'id' => intval($dungeon['id']),
          'key' => strval($dungeon['key']),
          'name' => strval($dungeon['name'])
        );
      }
      // parse names
      /*foreach( $this->xml2->dungeons->dungeon as $dungeon_num => $dungeon ){
        $dungeon_names[intval($dungeon['id'])] = strval($dungeon['name']);
        foreach( $dungeon->boss as $boss_num => $boss ){
          $boss_names[intval($boss['id'])] = strval($boss['name']);
        }
      }
      // parse dungeons
      foreach( $this->xml1->dungeon as $num => $dungeon )if(intval($dungeon['partySize'])>0){
        $this->dungeons[intval($dungeon['id'])] = array(
          'id' => intval($dungeon['id']),
          'key' => strval($dungeon['key']),
          'hasHeroic' => (intval($dungeon['hasHeroic'])>0),
          'raid' => (intval($dungeon['raid'])>0),
          'levelMax' => intval($dungeon['levelMax']),
          'levelMin' => intval($dungeon['levelMin']),
          'partySize' => intval($dungeon['partySize']),
          'name' => ($dungeon_names[intval($dungeon['nameId'])]?$dungeon_names[intval($dungeon['nameId'])]:'['.strval($dungeon['key']).']'),
        );
        foreach( $dungeon->bosses->boss as $boss_num => $boss ){
          $this->dungeons[intval($dungeon['id'])]['boss'][intval($boss['id'])] = array(
            'key' => strval($boss['key']),
            'name' => $boss_names[intval($boss['id'])],
          );
        }
      }*/
    }
    
    private function query(){
      $locale = sprintf('%s_%s',strtolower($this->local[1]),strtolower($this->local[2]));
      libxml_use_internal_errors(false); libxml_clear_errors();
      libxml_set_streams_context(stream_context_create(array('http' => array(
        'user_agent' => sprintf('Mozilla/5.0 (Windows; U; Windows NT 5.1; %s; rv:1.8) Gecko/20051111 Firefox/1.5',$this->local[1]),
        'header' => sprintf('Accept-Language: %s, en',$this->local[1]),
      ))));
      //$url1 = 'http://eu.wowarmory.com/data/dungeons.xml?locale='.$locale;// DEAD :(
      $url2 = 'http://eu.wowarmory.com/data/dungeonStrings.xml?locale='.$locale;
      //$this->xml1 = simplexml_load_file($url1);
      $this->xml2 = simplexml_load_file($url2);
      return( $this->xml2->dungeons->dungeon );
    }
    
    private function load(){
      $file1 = sprintf(TYPO3TEMP.'tx_wowraid_dungeons_%s.xml',strtolower($this->local[1].$this->local[2]));
      $file2 = sprintf(TYPO3TEMP.'tx_wowraid_dungeonStrings_%s.xml',strtolower($this->local[1].$this->local[2]));
      if(!file_exists($file1))return false;
      if(!file_exists($file2))return false;
      if( ( time() - filemtime($file1) ) > CACHETIME )return false;
      if( ( time() - filemtime($file2) ) > CACHETIME )return false;
      //$this->xml1 = simplexml_load_file($file1);
      $this->xml2 = simplexml_load_file($file2);
      return( $this->xml2->dungeons->dungeon );
    }
    
    private function save(){
      $lang = $this->xml2['lang'];
      if(!eregi('^([a-z]{2})_([a-z]{2})$',$lang,$lang))throw new Exception('incorrect language');
      //$file1 = sprintf(TYPO3TEMP.'tx_wowraid_dungeons_%s.xml',strtolower($lang[1].$lang[2]));
      $file2 = sprintf(TYPO3TEMP.'tx_wowraid_dungeonStrings_%s.xml',strtolower($lang[1].$lang[2]));
      //$this->xml1->asXML($file1);
      $this->xml2->asXML($file2);
    }
    
    public function getInstance($id){
      return $this->dungeons[$id];
    }

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wow_raid/inc/class.tx_wowraid_instances.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wow_raid/inc/class.tx_wowraid_instances.php']);
}
?>
