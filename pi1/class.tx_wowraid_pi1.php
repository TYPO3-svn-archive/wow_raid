<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Jobe <jobe@jobesoft.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(t3lib_extMgm::extPath('wow_raid').'inc/class.tx_wowraid_instances.php');
require_once(t3lib_extMgm::extPath('wow_raid').'inc/class.tx_wowraid_raids.php');
require_once(t3lib_extMgm::extPath('wow_character').'inc/class.tx_wowcharacter_character.php');

/**
 * Plugin 'WOW Raids' for the 'wow_raid' extension.
 *
 * @author	Jobe <jobe@jobesoft.de>
 * @package	TYPO3
 * @subpackage	tx_wowraid
 */
class tx_wowraid_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_wowraid_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_wowraid_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'wow_raid';	// The extension key.
  var $instances     = null;
	
	/**
	 * Main method of your PlugIn
	 *
	 * @param	string		$content: The content of the PlugIn
	 * @param	array		$conf: The PlugIn Configuration
	 * @return	The content that should be displayed on the website
	 */
	function main($content,$conf)	{
    $this->conf=$conf;    // Setting the TypoScript passed to this function in $this->conf
    $this->pi_setPiVarDefaults();
    $this->pi_loadLL();    // Loading the LOCAL_LANG values
    $this->pi_USER_INT_obj=1;  // Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
    $this->pi_initPIflexForm();
    if(!( $this->conf['pid'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'raids_folder', 'sDEF') )) throw new Exception('No start folder defined!');
    $GLOBALS['TYPO3_DB']->debugOutput = false;
    $this->instances = new tx_wowraid_instances();    
    $GLOBALS['TSFE']->additionalHeaderData[$this->extKey] = '<link rel="stylesheet" href="'.$this->conf['css'].'" type="text/css" />';
    $view = $this->piVars['view'];
    
    //$raids = new tx_wowraid_raids(12);
    
    /* ACTIONS */
    if($this->piVars['create'])$this->actionCreate();
    if($this->piVars['delete'])$this->actionDelete();
    if($this->piVars['uid']){// these need a given raid to work on
      if($this->piVars['edit'])$this->actionEdit();
      if($this->piVars['join'])$this->actionJoin();
      if($this->piVars['comment'])$this->actionComment();
    }  
    /* MARKERS */
    $tpl = $this->cObj->cObjGetSingle($this->conf['template.']['html'],$this->conf['template.']['html.']);// get template
    $LOCAL_LANG = $this->LOCAL_LANG[$this->LLkey];// get label array
    if(!count($LOCAL_LANG))$LOCAL_LANG = $this->LOCAL_LANG['default'];// fallback to default language
    $marker = array();foreach( $LOCAL_LANG as $key => $value )$marker[sprintf('###LLL_%s###',strtoupper($key))] = $value;// build label array
    $marker['###URL###'] = $this->pi_linkTP_keepPIvars_url(array(),0,1);
    $marker['###NEW###'] = $this->pi_linkTP_keepPIvars($marker['###LLL_NEW###'],array('view'=>'create'));
    /* VIEWS */
    switch($view){
      case'detail': $tpl = $this->singleView($tpl); break;
      case'create': $tpl = $this->createView($tpl); break;
      case'edit':   $tpl = $this->editView($tpl); break;
      default:      $tpl = $this->listView($tpl); break;
    }
    return $this->pi_wrapInBaseClass($this->cObj->substituteMarkerArray($tpl,$marker));
	}
  
  /**
  * @desc Substitute markers and subparts in a template. Markers with sub-markers represent subparts.
  * @desc $marker = array( 'MARKER' => 'VALUE', 'SUBPART' => array( 'MARKER' => 'VALUE' ) );
  * @desc Subparts inherit markers from parents.
  */
  function parse($tpl,$marker){
    $submarker = array_filter($marker,'is_array');// extract arrays
    $marker = array_diff_key($marker,$submarker);// filter arrays
    foreach( $submarker as $key => $value )// handle possible subparts
      while( $subtpl = $this->cObj->getSubpart($tpl,$key) )// fetch all subparts in template
        $tpl = $this->cObj->substituteSubpart($tpl,$key,$this->parse($subtpl,array_merge($marker,$value)));// substitute subparts
    $tpl = $this->cObj->substituteMarkerArray($tpl,$marker);// substitute markers
    return $tpl;
  }
  
  /* ACTIONS **********************************************************************************************************/
  
  /**
  * @desc Add character to raid.
  */
  function actionJoin(){
    $uid = $this->piVars['uid'];
    $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_wowraid_raids',sprintf('uid = %d',$uid));
    $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
    $charsCUR = explode(',',$row['participants']);
    $charsUSR = explode(',',$GLOBALS['TSFE']->fe_user->user['tx_wowcharacter_wowchars']);
    $charsNEW = explode(',',$this->piVars['join']);
    $charsOFF = array(array_shift($charsCUR));
    if( in_array($charsOFF[0],$charsUSR) ){// fe_user is officer
      $charsOFF = $charsNEW;
    }else{
      $charsCUR = array_merge(array_diff($charsCUR,$charsUSR),$charsNEW);
    }
    $participants = implode(',',array_merge($charsOFF,$charsCUR));
    $res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_wowraid_raids',sprintf('uid = %d',$uid),array(
      'tstamp' => time(),
      'participants' => $participants
    ));/**/
    unset($this->piVars['join']);
  }

  /**
  * @desc Flag a raid as deleted.
  */
  function actionDelete(){
    $res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_wowraid_raids',sprintf('uid = %d',$this->piVars['delete']),array(
      'tstamp' => time(),
      'deleted' => 1
    ));
    //TODO: notify participants
    //TODO: release participants
    unset($this->piVars['delete']);
  }

  /**
  * @desc Modify a given raid.
  */
  function actionEdit(){
    $res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_wowraid_raids',sprintf('uid = %d',$this->piVars['uid']),array(
      'tstamp' => time(),
      'instance' => $this->piVars['edit']['instance'],
      'begin' => strtotime($this->piVars['edit']['begin']),
      'prepare' => $this->piVars['edit']['prepare']
    ));
    //TODO: notify participants
    //TODO: release participants
    unset($this->piVars['edit']);
  }
  
  /**
  * @desc Create a new raid
  */
  function actionCreate(){
    $res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_wowraid_raids',array(
      'pid' => $this->conf['pid'],
      'tstamp' => time(),
      'crdate' => time(),
      'cruser_id' => 2,
      'instance' => $this->piVars['create']['instance'],
      'begin' => strtotime($this->piVars['create']['begin']),
      'prepare' => $this->piVars['create']['prepare'],
      'participants' => $this->piVars['create']['officer']
    ));
    unset($this->piVars['create']);
  }

  function actionComment(){
    $uid = $this->piVars['uid'];
    $comment = $this->piVars['comment'];
    $author = $GLOBALS['TSFE']->fe_user->user['uid'];
    $res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_wowraid_comments',array(
      'pid' => $this->conf['pid'],
      'tstamp' => time(),
      'crdate' => time(),
      'raid' => $uid,
      'author' => $author,
      'message' => $comment
    ));
    unset($this->piVars['comment']);
  }
  
  /* VIEWS ************************************************************************************************************/
  
	function listView($tpl){
    $tpl = $this->cObj->getSubpart($tpl,'###LISTVIEW###');
    $tpl_row = $this->cObj->getSubpart($tpl,'###RAID###');
    $tpl_empty = $this->cObj->getSubpart($tpl,'###EMPTY###');
    $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_wowraid_raids','hidden = 0 AND deleted = 0','','begin DESC');
    if($GLOBALS['TYPO3_DB']->sql_num_rows($res) > 0){
      $tpl = $this->cObj->substituteSubpart($tpl,'###RAID###',$this->createList($tpl_row,$res));
      $tpl = $this->cObj->substituteSubpart($tpl,'###EMPTY###','');
    }else{
      $tpl = $this->cObj->substituteSubpart($tpl,'###RAID###','');
      $tpl = $this->cObj->substituteSubpart($tpl,'###EMPTY###',$tpl_empty);
    }  
    return $tpl;
	}
  
  function singleView($tpl){
    $tpl = $this->cObj->getSubpart($tpl,'###SINGLEVIEW###' );
    $uid = $this->piVars['uid'];
    if(!$uid)return "NO RAID SELECTED";
    $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_wowraid_raids',sprintf('uid = %d AND hidden = 0 AND deleted = 0',$uid));
    $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
    $tpl = $this->createSingle($tpl,$row);
    return $tpl;
  }

  function createView($tpl){
    $tpl = $this->cObj->getSubpart($tpl,'###CREATE###');
    if(!$GLOBALS['TSFE']->fe_user->user)return "Please login!";
    if(empty($GLOBALS['TSFE']->fe_user->user["tx_wowcharacter_wowchars"]))return "You don't have any characters!";
    $marker['###ID_INSTANCE###']  = "tx_wowraid_pi1[create][instance]";
    $marker['###ID_BEGIN###']     = "tx_wowraid_pi1[create][begin]";
    $marker['###ID_PREPARE###']   = "tx_wowraid_pi1[create][prepare]";
    $marker['###ID_OFFICER###']   = "tx_wowraid_pi1[create][officer]";
    foreach( $this->instances->dungeons as $dungeonID => $dungeon )
      $marker['###OPTIONS_INSTANCE###'] .= sprintf("<option value='%d'>%s</option>\n",$dungeonID,$dungeon['name']);
    $tpl = $this->cObj->substituteMarkerArray($tpl,$marker);
    $tpl = $this->createSingle($tpl);
    return $tpl;
  }

  function editView($tpl){
    $tpl = $this->cObj->getSubpart($tpl,'###EDIT###');
    $uid = $this->piVars['uid'];
    $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_wowraid_raids',sprintf('uid = %d AND hidden = 0 AND deleted = 0',$uid));
    $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
    foreach( $this->instances->dungeons as $dungeonID => $dungeon )
      $marker['###OPTIONS_INSTANCE###'] .= sprintf("<option value='%d' %s>%s</option>\n",$dungeonID,($dungeonID==$row['instance'])?'selected':'',$dungeon['name']);
    $marker['###ID_INSTANCE###']  = "tx_wowraid_pi1[edit][instance]";
    $marker['###ID_BEGIN###']     = "tx_wowraid_pi1[edit][begin]";
    $marker['###ID_PREPARE###']   = "tx_wowraid_pi1[edit][prepare]";
    $marker['###ID_OFFICER###']   = "tx_wowraid_pi1[edit][officer]";
    $tpl = $this->cObj->substituteMarkerArray($tpl,$marker);
    $tpl = $this->createSingle($tpl,$row);
    return $tpl;
  }

  /* CREATE ELEMENTS **************************************************************************************************/
    
  /**
  * @desc Fill a given template with data for a single element.
  */
  function createSingle($tpl,$data=null){
    $marker = array();
    $marker['###HIDDEN###']  = "<input type='hidden' name='tx_wowraid_pi1[view]' value='detail'>\n";
    $marker['###ID_JOIN###'] = "tx_wowraid_pi1[join]";
    $marker['###ID_COMMENT###'] = "tx_wowraid_pi1[comment]";
    if($data){// fill data dependent markers
      $dungeon = $this->instances->getInstance($data['instance']);
      $officer = array_shift(explode(',',$data['participants']));
      $marker['###HIDDEN###'] .= sprintf("<input type='hidden' name='tx_wowraid_pi1[uid]' value='%d'>\n",$data['uid']);
      $marker['###NAME###'] = $dungeon['name'];
      $marker['###NAME->DETAIL###'] = $this->pi_linkTP_keepPIvars($marker['###NAME###'],array('view'=>'detail','uid'=>$data['uid']));
      $marker['###BEGIN###'] = date('d.m.Y H:i',$data['begin']);
      $marker['###DAYS###'] = intval( ( $data['begin'] - time() ) / 60 / 60 / 60 );
      $marker['###PREPARE###'] = $data['prepare'];
      $marker['###INVITE###'] = date('d.m.Y H:i',$data['begin']-($data['prepare']*60));
      $marker['###PARTICIPANTS###'] = count(explode(',',$data['participants']));
      $marker['###ADMIN###'] = $this->createAdmin($data);
      $marker = array_merge($marker,$this->marksParticipants($data['participants']));
      if( $tpl_participant = $this->cObj->getSubpart($tpl,'###PARTICIPANTS_LIST###') ){
        $tmp = $this->createParticipantList($tpl_participant,$data['participants'],$officer);// create list of participants
        $tpl = $this->cObj->substituteSubpart($tpl,'###PARTICIPANTS_LIST###',$tmp);// substitute list
      }
    }
    if( $GLOBALS['TSFE']->fe_user->user && $tpl_feuser = $this->cObj->getSubpart($tpl,'###FE_USER###') ){// fill markers for fe_users
      $wowchars = $GLOBALS['TSFE']->fe_user->user["tx_wowcharacter_wowchars"];
      $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_wowcharacter_characters',sprintf('uid IN (%s) AND hidden = 0 AND deleted = 0',$wowchars));
      while( $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) )
        $marker['###OPTIONS_CHARS###'] .= sprintf("<option value='%d'>%s</option>\n",$row['uid'],$row['name']);
      $tpl_feuser = $this->cObj->substituteMarkerArray($tpl_feuser,$marker);
    }elseif( $tpl_nouser = $this->cObj->getSubpart($tpl,'###NO_USER###') ){
      $tpl_nouser = $this->cObj->substituteMarkerArray($tpl_nouser,$marker);
    }
    if( $tpl_comments = $this->cObj->getSubpart($tpl,'###COMMENTS###') ){
      $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_wowraid_comments',sprintf('raid = %d',$data['uid'])); unset($tmp);
      while( $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ){
        $row['author'] = $this->pi_getRecord('fe_users',$row['author']);
        $row['character'] = array_shift(array_intersect(explode(',',$data['participants']),explode(',',$row['author']['tx_wowcharacter_wowchars'])));
        $row['character'] = $this->pi_getRecord('tx_wowcharacter_characters',$row['character']);
        $row['character'] = $row['character']['name'];
        $tmp .= $this->cObj->substituteMarkerArray($tpl_comments,array(
          '###DATETIME###' => date('m.d.Y H:i',$row['crdate']),
          '###AUTHOR###' => $row['author']['username'],
          '###CHARACTER###' => $row['character'],
          '###MESSAGE###' => $row['message']
        ));
      }
      $tpl_comments = $tmp;
    }
    $tpl = $this->cObj->substituteSubpart($tpl,'###FE_USER###',$tpl_feuser);// subtitute fe_user part
    $tpl = $this->cObj->substituteSubpart($tpl,'###NO_USER###',$tpl_nouser);// subtitute no_user part
    $tpl = $this->cObj->substituteSubpart($tpl,'###COMMENTS###',$tpl_comments);// subtitute comments part
    return $this->cObj->substituteMarkerArray($tpl,$marker);
  }

  /**
  * @desc Fill a given template with a given list of single elements.
  */
  function createList($tpl,$res){
    while( $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ) $tmp .= $this->createSingle($tpl,$row);
    return $tmp;
  }
  
  /**
  * @desc Fill a given template with data for a single participant.
  */
  function createParticipantSingle($tpl,$row,$officer=false){
    $char = new tx_wowcharacter_pi1_character($row['realm'],$row['name']);
    $char = $char->xml->characterInfo;
    $marker = array();
    $marker['###NAME###'] = $row['name'];
    $marker['###REALM###'] = $row['realm'];
    $marker['###CLASS###'] = $char->character['class'];
    $marker['###LEVEL###'] = $char->character['level'];
    $marker['###RACE###'] = $char->character['race'];
    $marker['###GUILD###'] = $char->character['guildName'];
    $marker['###ICON_OFFICER###'] = '';
    if($officer){
      $marker['###ICON_OFFICER###'] = '<img src="typo3conf/ext/wow_raid/res/gfx/crown.png">';
    }
    return $this->cObj->substituteMarkerArray($tpl,$marker);
  }

  /**
  * @desc Fill a given template with a list of single participants
  */
  function createParticipantList($tpl,$participants,$officer){
    $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_wowcharacter_characters','uid IN ('.$participants.')','','name');
    while( $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) )if($row['uid']==$officer)
      $off = $this->createParticipantSingle($tpl,$row,true);
    else  
      $tmp .= $this->createParticipantSingle($tpl,$row);
    return $off.$tmp;
  }
  
  /**
  * @desc Create a list of administrative Buttons.
  */
  function createAdmin($row){
    $FEchars = explode(',',$GLOBALS['TSFE']->fe_user->user["tx_wowcharacter_wowchars"]);
    $officer = array_shift(explode(',',$row['participants']));
    if(array_search($officer,$FEchars)===FALSE)return null;
    $tmp .= $this->pi_linkTP_keepPIvars('<img src="typo3/sysext/t3skin/icons/gfx/edit2.gif">',array('view'=>'edit','uid'=>$row['uid']));
    $tmp .= $this->pi_linkTP_keepPIvars('<img src="typo3/sysext/t3skin/icons/gfx/garbage.gif">',array('delete'=>$row['uid']));
    return $tmp;
  }
  
  function marksParticipants($participants){
    $result = array(
      '###PARTICIPANTS_WR###' => 0,
      '###PARTICIPANTS_WL###' => 0,
      '###PARTICIPANTS_SH###' => 0,
      '###PARTICIPANTS_RO###' => 0,
      '###PARTICIPANTS_PR###' => 0,
      '###PARTICIPANTS_PA###' => 0,
      '###PARTICIPANTS_MA###' => 0,
      '###PARTICIPANTS_HU###' => 0,
      '###PARTICIPANTS_DR###' => 0,
      '###PARTICIPANTS_DK###' => 0,
    );
    $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('realm,name','tx_wowcharacter_characters','uid IN ('.$participants.')','','name');
    while( $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ){
      $char = new tx_wowcharacter_pi1_character($row['realm'],$row['name']);
      $char = $char->xml->characterInfo;
      switch(intval($char->character['classId'])){
        case 9: $result['###PARTICIPANTS_WL###']++; break;
      }
    }
    return $result;
  }
  
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wow_raid/pi1/class.tx_wowraid_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wow_raid/pi1/class.tx_wowraid_pi1.php']);
}

//print('<pre style="text-align:left;position:absolute;">');var_dump($GLOBALS['TSFE']->fe_user->user);print('</pre>');
?>