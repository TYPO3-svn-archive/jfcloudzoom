<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Juergen Furrer <juergen.furrer@gmail.com>
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
 * @author	Juergen Furrer <juergen.furrer@gmail.com>
 * @package	TYPO3
 * @subpackage	tx_jfcloudzoom
 */
class tx_jfcloudzoom
{
	public $cObj;
	public $type = 'content';

	public function getImageForTTnews($paramArray, $conf)
	{
		$markerArray = $paramArray[0];
		$lConf = $paramArray[1];
		$pObj = &$conf['parentObj']; // make a reference to the parent-object
		$row = $pObj->local_cObj->data;
		if ($row['tx_jfcloudzoom_activate']) {
			$imageConf = "jfcloudzoomSingleImage.";
		} else {
			$imageConf = "image.";
		}
		$imageNum = isset($lConf['imageCount']) ? $lConf['imageCount']:1;
		$imageNum = t3lib_div::intInRange($imageNum, 0, 100);
		$theImgCode = '';
		$imgs = t3lib_div::trimExplode(',', $row['image'], 1);
		$imgsCaptions = explode(chr(10), $row['imagecaption']);
		reset($imgs);
		$cc = 0;
		while (list($key, $val) = each($imgs)) {
			if ($cc == $imageNum) break;
			if ($val) {
				// register some vars
				$GLOBALS['TSFE']->register['image']        = $val;
				$GLOBALS['TSFE']->register['imagecaption'] = $imgsCaptions[$cc];
				// define the file
				if ($row['tx_jfcloudzoom_activate']) {
					$theImgCode .= $pObj->local_cObj->IMAGE($lConf[$imageConf]);
				} else {
					$theImgCode .= $pObj->local_cObj->IMAGE($lConf[$imageConf]).$pObj->local_cObj->stdWrap($imgsCaptions[$cc], $lConf['caption_stdWrap.']);
				}
			}
			$cc ++;
		}
		$markerArray['###NEWS_IMAGE###'] = '';
		if ($cc) {
			$markerArray['###NEWS_IMAGE###'] = $pObj->local_cObj->wrap(trim($theImgCode), $lConf['imageWrapIfAny']);
		}
		return $markerArray;
	}

	public function getZoom($content, $conf)
	{
		// in case of tt_products
		if (t3lib_extMgm::isLoaded('tt_products')) {
			$GP = t3lib_div::_GP('tt_products');
			if ($GP['product']) {
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
					'tx_jfcloudzoom_activate,tx_jfcloudzoom_factor',
					'tt_products',
					'uid='.$GLOBALS['TYPO3_DB']->fullQuoteStr($GP['product'], 'tt_products')
				);
				$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
				$this->cObj->data['tx_jfcloudzoom_activate'] = $row['tx_jfcloudzoom_activate'];
				$this->cObj->data['tx_jfcloudzoom_factor']   = $row['tx_jfcloudzoom_factor'];
			}
		}
		// check if Cloud-Zoom is active ImageInfo
		if ($this->cObj->data['tx_jfcloudzoom_activate'] && count($GLOBALS['TSFE']->lastImageInfo) > 0) {
			if ($conf['type']) {
				$this->type = $this->cObj->stdWrap($conf['type'], $conf['type.']);
			}
			require_once(t3lib_extMgm::extPath('jfcloudzoom') . 'pi1/class.tx_jfcloudzoom_pi1.php');
			$obj = t3lib_div::makeInstance('tx_jfcloudzoom_pi1');
			$obj->setContentKey($obj->extKey . '_' . $this->cObj->data['uid']);
			$obj->conf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_jfcloudzoom_pi1.'];
			// override the width and height of the config
			$obj->conf[$this->type.'.']['imagewidth']  = $GLOBALS['TSFE']->lastImageInfo[0];
			$obj->conf[$this->type.'.']['imageheight'] = $GLOBALS['TSFE']->lastImageInfo[1];
			if ($this->cObj->data['tx_jfcloudzoom_factor'] > 1) {
				$obj->conf[$this->type.'.']['scaleFactor'] = $this->cObj->data['tx_jfcloudzoom_factor'];
			}
			$obj->cObj = $this->cObj;
			$obj->type = $this->type;
			$return_string = $obj->parseTemplate($data, 'uploads/pics/', true);
		}
		return $content;
	}
}


// XCLASS inclusion code
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jfcloudzoom/class.tx_jfcloudzoom.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jfcloudzoom/class.tx_jfcloudzoom.php']);
}
?>