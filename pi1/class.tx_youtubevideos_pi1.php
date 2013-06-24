<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Imran Munawar Khan <meimran642@gmail.com>
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
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Plugin 'Youtube Channel Videos Grabbing' for the 'youtubevideos' extension.
 *
 * @author	Imran Munawar Khan <meimran642@gmail.com>
 * @package	TYPO3
 * @subpackage	tx_youtubevideos
 */
class tx_youtubevideos_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_youtubevideos_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_youtubevideos_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'youtubevideos';	// The extension key.
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf) {
		$this->init($conf);
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_USER_INT_obj = 1;	// Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
		$GLOBALS['TSFE']->set_no_cache();
		if($this->pi_Conf['uvideo_display']==1)
		{
			$content = $this->topFive();
		}
		elseif($this->pi_Conf['uvideo_display']==2)
		{
			$content = $this->allVideos();
		}
		return $this->pi_wrapInBaseClass($content);
	}
	function topFive()
	{
		//Includes JS Files
		$js = 'function loadVideo(con) {
				$.post("index.php?id=22&eID=loadv&vid="+con, function(data) {
					$("#dofix").html(data);
				});
			  }';
		$GLOBALS['TSFE']->additionalHeaderData['uvideos_css'] ='<link rel="stylesheet" type="text/css" href="'.t3lib_extMgm::siteRelPath($this->extKey).'res/css/videobox.css" media="all">';
		$GLOBALS['TSFE']->additionalHeaderData['uvideos_jquery'] ='<script language="JavaScript" type="text/javascript" src="'.t3lib_extMgm::siteRelPath($this->extKey).'res/js/jquery-1.6.1.min.js"></script>';				   
		$GLOBALS['TSFE']->additionalHeaderData['uvideos_show'] ='<script language="JavaScript" type="text/javascript" src="'.t3lib_extMgm::siteRelPath($this->extKey).'res/js/show.js "></script>';
		$GLOBALS['TSFE']->additionalHeaderData['uvideos_myscript'] = t3lib_div::wrapJS($js);
		//End of js files
		
		//Display Videos
		$limit = $this->pi_Conf['hnumofrec'];
		$content='<div id="mediaBox">
				    <div class="hd-txt">
			    	<h4>YOUTUBE LATEST VIDEOS</h4>
				    </div>
				 	<div id="innerBox" style="padding-bottom:0px">
				   		<div id="dofix" style="height:290px;width:450px">
			            </div>            
						<div id="videos2"></div>
						<script type="text/javascript" src="http://gdata.youtube.com/feeds/users/'.$this->pi_Conf['userchannel'].'/'.$this->pi_Conf['uvideo_type'].'?alt=json-in-script&callback=showMyVideos2&max-results='.$limit.'&start-index=1">
						</script>
					</div>
			 	 </div>';
		
		return $content;
		
		
		
			
	}
	function allVideos()
	{
		$limit = $this->pi_Conf['numofrec'];	
		$base_url = $GLOBALS['TSFE']->config['config']['baseURL'];
		error_reporting(0);
		$URL="http://gdata.youtube.com/feeds/api/users/".$this->pi_Conf['userchannel']."/uploads";
		$rawFeed = file_get_contents($URL);
		$xml = new SimpleXmlElement($rawFeed);
    	//$feed = tx_div::makeInstance('tx_lib_object');
	    //$xml = simplexml_load_file($URL);		
		if ($xml) {
		  $counts = $xml->children('http://a9.com/-/spec/opensearchrss/1.0/');
		  $pages = ceil(intval($counts->totalResults)/$limit);
		  if(isset($this->piVars['start-index']) && $this->piVars['page']!=1)
		  {
			 $cstrt = $this->piVars['start-index'];
		  }
		  else
		  {
			 $cstrt = 1;
		  }		  
		  if(isset($this->piVars['page']))
		  {
			 $page = $this->piVars['page'];
		  }
		  else
		  {
			 $page = 1;  
		  }
		  $lnk = '';
		  for($i=1; $i <= $pages; $i++)
		  {
			  if($page > 1 && $page < $pages){
			  	
				if($i==1)
				{ 
					$nstrt = $this->piVars['start-index']-$limit;
					$prev = $page-1;
					$lnk .= '<a href="'.$this->getMyURL($prev,$nstrt).'"><span class="'.$cls.'">zurück</span></a>';
				}
				if($i==1){ $nstrt = 1;}else{$nstrt = $nstrt+$limit;}
			  	if($i==1 && $page==1){$cls= 'pg-selected';}elseif($i==$page){$cls= 'pg-selected';}else{$cls= 'pg-normal';}
			  	$lnk .= '<a href="'.$this->getMyURL($i,$nstrt).'"><span class="'.$cls.'">'.$i.'</span></a>';	  
				if($i==$pages)
				{ 
					$nstrt = $this->piVars['start-index']+$limit;
					$next = $page+1;
					$lnk .= '<a href="'.$this->getMyURL($next,$nstrt).'"><span class="'.$cls.'">weiter</span></a>';
				}				
			  
			  }
			  if($page== 1)
			  {
			  	if($i==1)
				{ 
					$nstrt = 1;
					$lnk .= '<span class="pg-selected prev">zurück</span>';
				}
				else
				{
					$nstrt = $nstrt+$limit;
				}
			  	if($i==1 && $page==1){$cls= 'pg-selected';}elseif($i==$page){$cls= 'pg-selected';}else{$cls= 'pg-normal';}
			  	$lnk .= '<a href="'.$this->getMyURL($i,$nstrt).'"><span class="'.$cls.'">'.$i.'</span></a>';	  				  
				if($i==$pages)
				{
					$nstrt = $cstrt+$limit;
					$next = $page+1;
					$lnk .= '<a href="'.$this->getMyURL($next,$nstrt).'"><span class="'.$cls.'">weiter</span></a>';
				}
			  }
			  elseif($this->piVars['page']==$pages)
			  {
				if($i==1)
				{ 
					$nstrt = $this->piVars['start-index']-$limit;
					$prev  =  $page-1;
					$lnk .= '<a href="'.$this->getMyURL($prev,$nstrt).'"><span class="'.$cls.'">zurück</span></a>';
				}
				if($i==1){ $nstrt = 1;}else{$nstrt = $nstrt+$limit;}
			  	if($i==1 && $page==1){$cls= 'pg-selected';}elseif($i==$page){$cls= 'pg-selected';}else{$cls= 'pg-normal';}
			  	$lnk .= '<a href="'.$this->getMyURL($i,$nstrt).'"><span class="'.$cls.'">'.$i.'</span></a>';	  				  
				if($i==$pages)
				{
					$lnk .= '<span class="pg-selected next">weiter</span>';
				}
			  }			  
		  }
		  
		  
		}
		$js = '$(document).ready(function(){
				$(\'.gallery:gt(0) a[rel^="prettyPhoto"]\').prettyPhoto({animation_speed:"fast",slideshow:10000, hideflash: true});
			   });
			   $("document").ready(function(){
				<!-- 
					//If this browser understands the mimeTypes property and recognizes the MIME Type //"application/x-shockwave-flash"...
					
					if (typeof FlashDetect != "undefined" && FlashDetect.versionAtLeast(9) != true)
					{
					//navigator.mimeTypes && navigator.mimeTypes["application/x-shockwave-flash"]){
						$(".flash-help").show(); 
					}
				});';
		$GLOBALS['TSFE']->additionalHeaderData['uvideos_css1'] ='<link rel="stylesheet" type="text/css" href="'.t3lib_extMgm::siteRelPath($this->extKey).'res/css/videobox.css" media="all">';
		$GLOBALS['TSFE']->additionalHeaderData['uvideos_css2'] ='<link rel="stylesheet" type="text/css" href="'.t3lib_extMgm::siteRelPath($this->extKey).'res/css/prettyPhoto.css" media="all">';
		$GLOBALS['TSFE']->additionalHeaderData['uvideos_jquery'] ='<script language="JavaScript" type="text/javascript" src="'.t3lib_extMgm::siteRelPath($this->extKey).'res/js/jquery-1.6.1.min.js"></script>';
		$GLOBALS['TSFE']->additionalHeaderData['uvideos_dispaly'] ='<script language="JavaScript" type="text/javascript" src="'.t3lib_extMgm::siteRelPath($this->extKey).'res/js/display.js"></script>';
		$GLOBALS['TSFE']->additionalHeaderData['uvideos_show'] ='<script language="JavaScript" type="text/javascript" src="'.t3lib_extMgm::siteRelPath($this->extKey).'res/js/jquery.prettyPhoto.js"></script>';		
		$GLOBALS['TSFE']->additionalHeaderData['uvideos_myscript'] = t3lib_div::wrapJS($js);		
		
				   
/*		$js  = '$(document).ready(function(){
				$(".gallery:gt(0) a[rel^=\'prettyPhoto\']").prettyPhoto({animation_speed:\'fast\',slideshow:10000, hideflash: true});
			});';
		$GLOBALS['TSFE']->additionalHeaderData['mein_profile_js'] = t3lib_div::wrapJS($js);	*/				
		
		$content .= "<div class='fleft' style='display:none;'>
						<ul class='gallery clearfix'>
							<li><a href='http://www.adobe.com/products/flashplayer/include/marquee/design.swf?width=792&amp;height=294' rel='prettyPhoto[flash]' title='Flash 10 demo'><img src='images/thumbnails/flash-logo.png' width='60' alt='Flash 10 demo' /></a></li>
						</ul>
					 </div>
					 <div id='mediaBox' class='tx_youtubevideos-mediaBox'>
						<div class='hd-txt'>
							<h4>SHOW CHANNEL ALL VIDEOS</h4>
						</div>		
						<div class='flash-help' style='display:none'>
							<p>".$this->pi_getLL('flash_help')."</p>
							<a href='http://www.adobe.com/go/getflashplayer/'><img src='".t3lib_extMgm::siteRelPath($this->extKey)."res/get_flash.jpg' alt='Flash player' /></a>
							</div>
						<div class='clr'></div>
						<div id='innerBox'>
							<script type='text/javascript' src='http://gdata.youtube.com/feeds/users/".$this->pi_Conf['userchannel']."/".$this->pi_Conf['uvideo_type']."?alt=json-in-script&callback=showMyVideos2&max-results=".$limit."&start-index=".$cstrt."'>
							</script>
						</div>
					 </div>
					<div class='clr'></div>
					<div id='pageNavPosition' class='pagination' style='display:inline'>".$lnk."</div>";
	
		return $content;
	}
	function getMyURL($pg,$sind)
	{
		$configuration = array();
		$configuration['parameter'] = $GLOBALS['TSFE']->id; // target page id or external
		$configuration['additionalParams'] = '&'.$this->prefixId.'[start-index]='.$sind.'&'.$this->prefixId.'[page]='.$pg;
		$configuration['returnLast'] = 'url'; // return the URL of the link 
		$purl = $this->cObj->typolink("NULL", $configuration);				
		unset($configuration);		
	return $purl;
	}	
	function init($conf)
	{
		$this->pi_initPIflexForm(); // Init and get the flexform data of the plugin
		$this->pi_Conf = array(); // Setup our storage array...
		// Assign the flexform data to a local variable for easier access

		$piFlexForm = $this->cObj->data['pi_flexform'];
		// Traverse the entire array based on the language...
		// and assign each configuration option to $this->lConf array...
		if(!empty($piFlexForm) && is_array($piFlexForm))
		{
			foreach ( $piFlexForm['data'] as $sheet => $data ) {
				foreach ( $data as $lang => $value ) {
					foreach ( $value as $key => $val ) {
						$this->pi_Conf[$key] = $this->pi_getFFvalue($piFlexForm, $key, $sheet);
					}
				}
			}
		}
		$this->_res_path = 'typo3conf/ext/'.$this->extKey.'/res/';
	} //end of init()		
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/youtubevideos/pi1/class.tx_youtubevideos_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/youtubevideos/pi1/class.tx_youtubevideos_pi1.php']);
}

?>