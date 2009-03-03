<?php /* require_once ( t3lib_extMgm::extPath('wow_raid').'class.tx_wowraid_instances.php' ); */

class tx_wowraid_instances{
    
    public $xml1 = null;
    public $xml2 = null;
    public $dungeons = array();
    private $tmp = null;
      
    public function tx_wowraid_instances($tmp,$lang=null){
      $dungeon_names = array();
      $boss_names = array();
      $this->tmp = $tmp;
      if( !$this->load($lang) && $this->query($lang) ) $this->save($lang);
      // parse names
      foreach( $this->xml2->dungeons->dungeon as $dungeon_num => $dungeon ){
        $dungeon_names[intval($dungeon['id'])] = strval($dungeon['name']);
        foreach( $dungeon->boss as $boss_num => $boss ){
          $boss_names[intval($boss['id'])] = strval($boss['name']);
        }
      }
      // parse dungeons
      foreach( $this->xml1->dungeon as $num => $dungeon )if(intval($dungeon['partySize'])>0){
        $this->dungeons[intval($dungeon['id'])] = array(
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
      }
    }
    
    private function query($lang='de-de'){
      libxml_use_internal_errors(false); libxml_clear_errors();
      libxml_set_streams_context(stream_context_create(array('http' => array(
        'user_agent' => sprintf('Mozilla/5.0 (Windows; U; Windows NT 5.1; %s; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6',$lang),
        'header' => sprintf('Accept-language: %s',$lang),
      ))));
      $url1 = 'http://eu.wowarmory.com/data/dungeons.xml';
      $url2 = 'http://eu.wowarmory.com/data/dungeonStrings.xml';
      $this->xml1 = simplexml_load_file($url1);
      $this->xml2 = simplexml_load_file($url2);
      return( $this->xml1->dungeon && $this->xml2->dungeons->dungeon );
    }
    
    private function load(){
      if(!file_exists($this->tmp.'tx_wowraid_dungeons.xml'))return false;
      if(!file_exists($this->tmp.'tx_wowraid_dungeonStrings.xml'))return false;
      $this->xml1 = simplexml_load_file($this->tmp.'tx_wowraid_dungeons.xml');
      $this->xml2 = simplexml_load_file($this->tmp.'tx_wowraid_dungeonStrings.xml');
      return( $this->xml1->dungeon && $this->xml2->dungeons->dungeon );
    }
    
    private function save(){
      var_dump(getcwd());
      $this->xml1->asXML($this->tmp.'tx_wowraid_dungeons.xml');
      $this->xml2->asXML($this->tmp.'tx_wowraid_dungeonStrings.xml');
    }
    
    public function getInstance($id){
      return $this->dungeons[$id];
    }

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wow_raid/class.tx_wowraid_instances.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wow_raid/class.tx_wowraid_instances.php']);
}
?>
