<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Juergen Furrer <juergen.furrer@gmail.com>
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
require_once(t3lib_extMgm::extPath('jfcloudzoom').'lib/class.tx_jfcloudzoom_pagerenderer.php');

/**
 * Plugin 'Cloud-Zoom' for the 'jfcloudzoom' extension.
 *
 * @author	Juergen Furrer <juergen.furrer@gmail.com>
 * @package	TYPO3
 * @subpackage	tx_jfcloudzoom
 */
class tx_jfcloudzoom_pi1 extends tslib_pibase
{
	public $prefixId      = 'tx_jfcloudzoom_pi1';
	public $scriptRelPath = 'pi1/class.tx_jfcloudzoom_pi1.php';
	public $extKey        = 'jfcloudzoom';
	public $pi_checkCHash = TRUE;
	public $images = array();
	public $captions = array();
	public $type = 'normal';
	protected $lConf = array();
	protected $contentKey = NULL;
	protected $jsFiles = array();
	protected $js = array();
	protected $css = array();
	protected $piFlexForm = array();
	protected $imageDir = 'uploads/tx_jfcloudzoom/';
	protected $pagerenderer = NULL;

	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	public function main($content, $conf)
	{
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();

		// define the key of the element
		$this->setContentKey("jfcloudzoom");

		// set the system language
		$this->sys_language_uid = $GLOBALS['TSFE']->sys_language_content;

		if ($this->cObj->data['list_type'] == $this->extKey.'_pi1') {
			$this->type = 'normal';

			// It's a content, all data from flexform

			$this->lConf['mode']          = $this->getFlexformData('general', 'mode');
			$this->lConf['images']        = $this->getFlexformData('general', 'images', ($this->lConf['mode'] == 'upload'));
			$this->lConf['captions']      = $this->getFlexformData('general', 'captions', ($this->lConf['mode'] == 'upload'));
			$this->lConf['damimages']     = $this->getFlexformData('general', 'damimages', ($this->lConf['mode'] == 'dam'));
			$this->lConf['damcategories'] = $this->getFlexformData('general', 'damcategories', ($this->lConf['mode'] == 'dam_catedit'));

			$this->lConf['imagewidth']      = $this->getFlexformData('settings', 'imagewidth');
			$this->lConf['imageheight']     = $this->getFlexformData('settings', 'imageheight');
			$this->lConf['thumbnailwidth']  = $this->getFlexformData('settings', 'thumbnailwidth');
			$this->lConf['thumbnailheight'] = $this->getFlexformData('settings', 'thumbnailheight');
			$this->lConf['scaleFactor']     = $this->getFlexformData('settings', 'scaleFactor');
			$this->lConf['smoothMove']      = $this->getFlexformData('settings', 'smoothMove');
			$this->lConf['position']        = $this->getFlexformData('settings', 'position');
			$this->lConf['zoomWidth']       = $this->getFlexformData('settings', 'zoomWidth');
			$this->lConf['zoomHeight']      = $this->getFlexformData('settings', 'zoomHeight');
			$this->lConf['adjustX']         = $this->getFlexformData('settings', 'adjustX');
			$this->lConf['adjustY']         = $this->getFlexformData('settings', 'adjustY');
			$this->lConf['showTitle']       = $this->getFlexformData('settings', 'showTitle');
			$this->lConf['softFocus']       = $this->getFlexformData('settings', 'softFocus');
			$this->lConf['useThumbnails']   = $this->getFlexformData('settings', 'useThumbnails');
			$this->lConf['maxZindex']       = $this->getFlexformData('settings', 'maxZindex');
			

			$this->lConf['tint']         = $this->getFlexformData('color', 'tint');
			$this->lConf['tintOpacity']  = $this->getFlexformData('color', 'tintOpacity');
			$this->lConf['lensOpacity']  = $this->getFlexformData('color', 'lensOpacity');
			$this->lConf['titleOpacity'] = $this->getFlexformData('color', 'titleOpacity');

			$this->lConf['options'] = $this->getFlexformData('special', 'options');

			// define the key of the element
			$this->setContentKey("jfcloudzoom_c" . $this->cObj->data['uid']);

			// define the images
			switch ($this->lConf['mode']) {
				case "" : {}
				case "folder" : {}
				case "upload" : {
					$this->setDataUpload();
					break;
				}
				case "dam" : {
					$this->setDataDam(FALSE, 'tt_content', $this->cObj->data['uid']);
					break;
				}
				case "dam_catedit" : {
					$this->setDataDam(TRUE, 'tt_content', $this->cObj->data['uid']);
					break;
				}
			}

			// Override the config with flexform data
			if ($this->lConf['imagewidth']) {
				$this->conf[$this->type.'.']['imagewidth'] = $this->lConf['imagewidth'];
			}
			if ($this->lConf['imageheight']) {
				$this->conf[$this->type.'.']['imageheight'] = $this->lConf['imageheight'];
			}
			if ($this->lConf['thumbnailwidth']) {
				$this->conf[$this->type.'.']['thumbnailwidth'] = $this->lConf['thumbnailwidth'];
			}
			if ($this->lConf['thumbnailheight']) {
				$this->conf[$this->type.'.']['thumbnailheight'] = $this->lConf['thumbnailheight'];
			}
			if ($this->lConf['scaleFactor'] > 0) {
				$this->conf[$this->type.'.']['scaleFactor'] = $this->lConf['scaleFactor'];
			}
			if ($this->lConf['position']) {
				$this->conf[$this->type.'.']['position'] = $this->lConf['position'];
			}
			if ($this->lConf['zoomWidth']) {
				$this->conf[$this->type.'.']['zoomWidth'] = $this->lConf['zoomWidth'];
			}
			if ($this->lConf['zoomHeight']) {
				$this->conf[$this->type.'.']['zoomHeight'] = $this->lConf['zoomHeight'];
			}
			if ($this->lConf['adjustX'] != '') {
				$this->conf[$this->type.'.']['adjustX'] = $this->lConf['adjustX'];
			}
			if ($this->lConf['adjustY'] != '') {
				$this->conf[$this->type.'.']['adjustY'] = $this->lConf['adjustY'];
			}
			if ($this->lConf['smoothMove']) {
				$this->conf[$this->type.'.']['smoothMove'] = $this->lConf['smoothMove'];
			}
			if (strlen($this->lConf['tint']) == 6) {
				$this->conf[$this->type.'.']['tint'] = $this->lConf['tint'];
			}
			if ($this->lConf['tintOpacity']) {
				$this->conf[$this->type.'.']['tintOpacity'] = $this->lConf['tintOpacity'];
			}
			if ($this->lConf['lensOpacity']) {
				$this->conf[$this->type.'.']['lensOpacity'] = $this->lConf['lensOpacity'];
			}
			if ($this->lConf['titleOpacity']) {
				$this->conf[$this->type.'.']['titleOpacity'] = $this->lConf['titleOpacity'];
			}
			// Will be overridden, if not "from TS"
			if ($this->lConf['softFocus'] < 2) {
				$this->conf[$this->type.'.']['softFocus'] = $this->lConf['softFocus'];
			}
			if ($this->lConf['showTitle'] < 2) {
				$this->conf[$this->type.'.']['showTitle'] = $this->lConf['showTitle'];
			}
			if ($this->lConf['useThumbnails'] < 2) {
				$this->conf[$this->type.'.']['useThumbnails'] = $this->lConf['useThumbnails'];
			}
			if ($this->lConf['maxZindex']) {
				$this->conf[$this->type.'.']['maxZindex'] = $this->lConf['maxZindex'];
			}
			$this->conf[$this->type.'.']['options']       = $this->lConf['options'];
		} else {
			$this->type = 'header';
			// It's the header
			$this->images[] = 1;
			$this->captions[] = 1;
		}

		$data = array();
		foreach ($this->images as $key => $image) {
			$data[$key]['image']   = $image;
			$data[$key]['caption'] = $this->captions[$key];
		}

		return $this->pi_wrapInBaseClass($this->parseTemplate($data));
	}

	/**
	 * Set the Information of the images if mode = upload
	 * @return boolean
	 */
	protected function setDataUpload()
	{
		if ($this->lConf['images']) {
			// define the images
			$this->images = t3lib_div::trimExplode(',', $this->lConf['images']);
			// define the captions
			if ($this->lConf['captions']) {
				$this->captions = t3lib_div::trimExplode(chr(10), $this->lConf['captions']);
			}
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Set the Information of the images if mode = dam
	 * @return boolean
	 */
	protected function setDataDam($fromCategory=FALSE, $table='tt_content', $uid=0)
	{
		// clear the imageDir
		$this->imageDir = '';
		// get all fields for captions
		$fieldsArray = array_merge(
			t3lib_div::trimExplode(',', $this->conf['damCaptionFields'], TRUE)
		);
		$fields = NULL;
		$damCaptionFields = array();
		if (count($fieldsArray) > 0) {
			foreach ($fieldsArray as $field) {
				$fields .= ',tx_dam.' . $field;
				$damCaptionFields[] = $field;
			}
		}
		if ($fromCategory === TRUE) {
			// Get the images from dam category
			$damcategories = $this->getDamcats($this->lConf['damcategories']);
			$res = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
				tx_dam_db::getMetaInfoFieldList() . $fields,
				'tx_dam',
				'tx_dam_mm_cat',
				'tx_dam_cat',
				" AND tx_dam_cat.uid IN (".implode(",", $damcategories).") AND tx_dam.file_mime_type='image'",
				'',
				'tx_dam.sorting',
				''
			);
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$images['rows'][] = $row;
			}
		} else {
			// Get the images from dam
			$images = tx_dam_db::getReferencedFiles(
				$table,
				$uid,
				'jfcloudzoom',
				'tx_dam_mm_ref',
				tx_dam_db::getMetaInfoFieldList() . $fields,
				"tx_dam.file_mime_type = 'image'"
			);
		}
		if (count($images['rows']) > 0) {
			// overlay the translation
			$conf = array(
				'sys_language_uid' => $this->sys_language_uid,
				'lovl_mode' => ''
			);
			// add image
			foreach ($images['rows'] as $key => $row) {
				$row = tx_dam_db::getRecordOverlay('tx_dam', $row, $conf);
				// set the data
				$this->images[] = $row['file_path'].$row['file_name'];$
				// set the caption
				$caption = '';
				unset($caption);
				if (count($damCaptionFields) > 0) {
					foreach ($damCaptionFields as $damCaptionField) {
						if (! isset($caption) && trim($row[$damCaptionField])) {
							$caption = $row[$damCaptionField];
							break;
						}
					}
				}
				$this->captions[] = $caption;
			}
		}
		return TRUE;
	}

	/**
	 * return all DAM categories including subcategories
	 *
	 * @return	array
	 */
	protected function getDamcats($dam_cat='')
	{
		$damCats = t3lib_div::trimExplode(",", $dam_cat, TRUE);
		if (count($damCats) < 1) {
			return array();
		} else {
			// select subcategories
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'uid, parent_id',
				'tx_dam_cat',
				'parent_id IN ('.implode(",", $damCats).') '.$this->cObj->enableFields('tx_dam_cat'),
				'',
				'parent_id',
				''
			);
			$subcats = array();
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$damCats[] = $row['uid'];
			}
		}
		return $damCats;
	}

	/**
	 * Set the contentKey
	 * @param string $contentKey
	 */
	public function setContentKey($contentKey=NULL)
	{
		$this->contentKey = ($contentKey == NULL ? $this->extKey : $contentKey);
	}

	/**
	 * Get the contentKey
	 * @return string
	 */
	public function getContentKey()
	{
		return $this->contentKey;
	}

	/**
	 * Parse all images into the template
	 * @param $data
	 * @return string
	 */
	public function parseTemplate($data=array(), $dir='', $onlyJS=FALSE)
	{
		$this->pagerenderer = t3lib_div::makeInstance('tx_jfcloudzoom_pagerenderer');
		$this->pagerenderer->setConf($this->conf);

		// define the directory of images
		if ($dir == '') {
			$dir = $this->imageDir;
		}

		// Check if $data is array
		if (count($data) == 0 && $onlyJS === FALSE) {
			return FALSE;
		}

		// define the contentKey if not exist
		if ($this->getContentKey() == '') {
			$this->setContentKey("jfcloudzoom_key");
		}

		// define the jQuery mode and function
		if ($this->conf['jQueryNoConflict']) {
			$jQueryNoConflict = "jQuery.noConflict();";
		} else {
			$jQueryNoConflict = "";
		}

		$options = array();

		if (! $this->conf[$this->type.'.']['imagewidth']) {
			$this->conf[$this->type.'.']['imagewidth'] = ($this->conf[$this->type.'.']['imagewidth'] ? $this->conf[$this->type.'.']['imagewidth'] : "200c");
		}
		if (! $this->conf[$this->type.'.']['imageheight']) {
			$this->conf[$this->type.'.']['imageheight'] = ($this->conf[$this->type.'.']['imageheight'] ? $this->conf[$this->type.'.']['imageheight'] : "200c");
		}
		if ($this->conf[$this->type.'.']['scaleFactor'] < 2) {
			$this->conf[$this->type.'.']['scaleFactor'] = 2;
		}
		if ($this->conf[$this->type.'.']['position']) {
			$options[] = "position: '{$this->conf[$this->type.'.']['position']}'";
		}
		if (is_numeric($this->conf[$this->type.'.']['zoomWidth']) || strtolower($this->conf[$this->type.'.']['zoomWidth']) == 'auto') {
			$options[] = "zoomWidth: '{$this->conf[$this->type.'.']['zoomWidth']}'";
		}
		if (is_numeric($this->conf[$this->type.'.']['zoomHeight']) || strtolower($this->conf[$this->type.'.']['zoomHeight']) == 'auto') {
			$options[] = "zoomHeight: '{$this->conf[$this->type.'.']['zoomHeight']}'";
		}
		if (is_numeric($this->conf[$this->type.'.']['adjustX'])) {
			$options[] = "adjustX: {$this->conf[$this->type.'.']['adjustX']}";
		}
		if (is_numeric($this->conf[$this->type.'.']['adjustY'])) {
			$options[] = "adjustY: {$this->conf[$this->type.'.']['adjustY']}";
		}
		if (is_numeric($this->conf[$this->type.'.']['smoothMove'])) {
			$options[] = "smoothMove: {$this->conf[$this->type.'.']['smoothMove']}";
		}
		if ($this->conf[$this->type.'.']['tint']) {
			$color = "#" . str_replace('#', '', $this->conf[$this->type.'.']['tint']);
			$options[] = "tint: '{$color}'";
		}
		if (is_numeric($this->conf[$this->type.'.']['tintOpacity'])) {
			$options[] = "tintOpacity: {$this->conf[$this->type.'.']['tintOpacity']}";
		}
		if (is_numeric($this->conf[$this->type.'.']['lensOpacity'])) {
			$options[] = "lensOpacity: {$this->conf[$this->type.'.']['lensOpacity']}";
		}
		if (is_numeric($this->conf[$this->type.'.']['titleOpacity'])) {
			$options[] = "titleOpacity: {$this->conf[$this->type.'.']['titleOpacity']}";
		}
		$options[] = "softFocus: ".($this->conf[$this->type.'.']['softFocus'] ? 'true' : 'false');
		$options[] = "showTitle: ".($this->conf[$this->type.'.']['showTitle'] ? 'true' : 'false');
		if (is_numeric($this->conf[$this->type.'.']['maxZindex'])) {
			$options[] = "maxZindex: ".$this->conf[$this->type.'.']['maxZindex'];
		}

		// overwrite all options if set
		if (trim($this->conf[$this->type.'.']['options'])) {
			$options = array($this->conf[$this->type.'.']['options']);
		}

		// define the js file
		$this->pagerenderer->addJsFile($this->conf['jQueryCloudZoom']);

		// define the css file
		$this->pagerenderer->addCssFile($this->conf['cssFile']);

		$this->pagerenderer->addJS($jQueryNoConflict);

		// checks if t3jquery is loaded
		if (T3JQUERY === TRUE) {
			tx_t3jquery::addJqJS();
		} else {
			$this->pagerenderer->addJsFile($this->conf['jQueryLibrary'], TRUE);
			$this->pagerenderer->addJsFile($this->conf['jQueryEasing']);
		}

		// Add the ressources
		$this->pagerenderer->addResources();

		$return_string = NULL;
		$images = NULL;
		$GLOBALS['TSFE']->register['key'] = $this->getContentKey();
		$GLOBALS['TSFE']->register['imagewidth']  = $this->conf[$this->type.'.']['imagewidth'];
		$GLOBALS['TSFE']->register['imageheight'] = $this->conf[$this->type.'.']['imageheight'];
		$GLOBALS['TSFE']->register['thumbnailwidth']  = $this->conf[$this->type.'.']['thumbnailwidth'];
		$GLOBALS['TSFE']->register['thumbnailheight'] = $this->conf[$this->type.'.']['thumbnailheight'];
		// scaled image width
		if (preg_match("/([0-9]*)(.*)/i", $this->conf[$this->type.'.']['imagewidth'], $reg)) {
			$GLOBALS['TSFE']->register['scaledimagewidth'] = ($reg[1] * $this->conf[$this->type.'.']['scaleFactor']).$reg[2];
		}
		if (preg_match("/([0-9]*)(.*)/i", $this->conf[$this->type.'.']['imageheight'], $reg)) {
			$GLOBALS['TSFE']->register['scaledimageheight'] = ($reg[1] * $this->conf[$this->type.'.']['scaleFactor']).$reg[2];
		}
		$GLOBALS['TSFE']->register['options'] = implode(", ", $options);
		$GLOBALS['TSFE']->register['IMAGE_NUM_CURRENT'] = 0;

		if ($onlyJS === TRUE) {
			return TRUE;
		}

		if (count($data) > 0) {
			foreach ($data as $key => $item) {
				$image = NULL;
				$thumbnail = NULL;
				$totalImagePath = $dir . $item['image'];
				$GLOBALS['TSFE']->register['file']    = $totalImagePath;
				$GLOBALS['TSFE']->register['caption'] = $item['caption'];
				$image = trim($this->cObj->cObjGetSingle($this->conf[$this->type.'.']['image'], $this->conf[$this->type.'.']['image.']));
				$image = $this->cObj->stdWrap($image, $this->conf[$this->type.'.']['itemWrap.']);
				$GLOBALS['TSFE']->register['smallImageURL'] = $GLOBALS['TSFE']->lastImageInfo[3];
				if ($this->conf[$this->type.'.']['useThumbnails']) {
					// small images will be rendered
					$thumbnail = trim($this->cObj->cObjGetSingle($this->conf[$this->type.'.']['thumbnail'], $this->conf[$this->type.'.']['thumbnail.']));
					$thumbnail = $this->cObj->stdWrap($thumbnail, $this->conf[$this->type.'.']['thumbnailWrap.']);
					$thumbnails .= $thumbnail;
					if (! $images) {
						$images = $image;
					}
				} else {
					$images .= $image;
				}
				$GLOBALS['TSFE']->register['IMAGE_NUM_CURRENT'] ++;
			}
			$markerArray = array();
			$markerArray['THUMBNAILS'] = $this->cObj->stdWrap($thumbnails, $this->conf[$this->type.'.']['thumbnailsWrap.']);
			$images = $this->cObj->stdWrap($images, $this->conf[$this->type.'.']['stdWrap.']);
			$return_string = $this->cObj->substituteMarkerArray($images, $markerArray, '###|###', 0);
		}
		return $return_string;
	}

	/**
	* Set the piFlexform data
	*
	* @return void
	*/
	protected function setFlexFormData()
	{
		if (! count($this->piFlexForm)) {
			$this->pi_initPIflexForm();
			$this->piFlexForm = $this->cObj->data['pi_flexform'];
		}
	}

	/**
	 * Extract the requested information from flexform
	 * @param string $sheet
	 * @param string $name
	 * @param boolean $devlog
	 * @return string
	 */
	protected function getFlexformData($sheet='', $name='', $devlog=TRUE)
	{
		$this->setFlexFormData();
		if (! isset($this->piFlexForm['data'])) {
			if ($devlog === TRUE) {
				t3lib_div::devLog("Flexform Data not set", $this->extKey, 1);
			}
			return NULL;
		}
		if (! isset($this->piFlexForm['data'][$sheet])) {
			if ($devlog === TRUE) {
				t3lib_div::devLog("Flexform sheet '{$sheet}' not defined", $this->extKey, 1);
			}
			return NULL;
		}
		if (! isset($this->piFlexForm['data'][$sheet]['lDEF'][$name])) {
			if ($devlog === TRUE) {
				t3lib_div::devLog("Flexform Data [{$sheet}][{$name}] does not exist", $this->extKey, 1);
			}
			return NULL;
		}
		if (isset($this->piFlexForm['data'][$sheet]['lDEF'][$name]['vDEF'])) {
			return $this->pi_getFFvalue($this->piFlexForm, $name, $sheet);
		} else {
			return $this->piFlexForm['data'][$sheet]['lDEF'][$name];
		}
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jfcloudzoom/pi1/class.tx_jfcloudzoom_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jfcloudzoom/pi1/class.tx_jfcloudzoom_pi1.php']);
}

?>