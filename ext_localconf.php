<?php
$TYPO3_CONF_VARS['FE']['eID_include']['loadv'] = 'EXT:youtubevideos/eID/loadvideo.php';

if (!defined ('TYPO3_MODE')) {
 	die ('Access denied.');
}

t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_youtubevideos_pi1.php', '_pi1', 'list_type', 0);
?>