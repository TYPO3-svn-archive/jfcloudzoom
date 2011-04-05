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

if (t3lib_extMgm::isLoaded('t3jquery')) {
	require_once(t3lib_extMgm::extPath('t3jquery').'class.tx_t3jquery.php');
}

/**
 * Plugin 'Cloud-Zoom' for the 'jfcloudzoom' extension.
 *
 * @author	Juergen Furrer <juergen.furrer@gmail.com>
 * @package	TYPO3
 * @subpackage	tx_jfcloudzoom
 */
class tx_jfcloudzoom_pi1 extends tslib_pibase
{
	public $prefixId      = 'tx_jfcloudzoom_pi1';		// Same as class name
	public $scriptRelPath = 'pi1/class.tx_jfcloudzoom_pi1.php';	// Path to this script relative to the extension dir.
	public $extKey        = 'jfcloudzoom';	// The extension key.
	public $pi_checkCHash = true;
	public $images = array();
	public $captions = array();
	public $type = 'normal';
	protected $lConf = array();
	protected $contentKey = null;
	protected $jsFiles = array();
	protected $js = array();
	protected $css = array();
	protected $imageDir = 'uploads/tx_jfcloudzoom/';

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
			// It's a content, al data from flexform
			// Set the Flexform information
			$this->pi_initPIflexForm();
			$piFlexForm = $this->cObj->data['pi_flexform'];
			foreach ($piFlexForm['data'] as $sheet => $data) {
				foreach ($data as $lang => $value) {
					foreach ($value as $key => $val) {
						$this->lConf[$key] = $this->pi_getFFvalue($piFlexForm, $key, $sheet);
					}
				}
			}

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
					$this->setDataDam(false, 'tt_content', $this->cObj->data['uid']);
					break;
				}
				case "dam_catedit" : {
					$this->setDataDam(true, 'tt_content', $this->cObj->data['uid']);
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
			if ($this->lConf['tint']) {
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
			return true;
		}
		return false;
	}

	/**
	 * Set the Information of the images if mode = dam
	 * @return boolean
	 */
	protected function setDataDam($fromCategory=false, $table='tt_content', $uid=0)
	{
		// clear the imageDir
		$this->imageDir = '';
		// get all fields for captions
		$damCaptionFields = t3lib_div::trimExplode(',', $this->conf['damCaptionFields'], true);
		$fields = (count($damCaptionFields) > 0 ? ','.implode(',tx_dam.', $damCaptionFields) : '');
		if ($fromCategory === true) {
			// Get the images from dam category
			$damcategories = $this->getDamcats($this->lConf['damcategories']);
			$res = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
				tx_dam_db::getMetaInfoFieldList() . $fields,
				'tx_dam',
				'tx_dam_mm_cat',
				'tx_dam_cat',
				" AND tx_dam_cat.uid IN (".implode(",", $damcategories).") AND tx_dam.file_mime_type='image' AND tx_dam.sys_language_uid=" . $GLOBALS['TYPO3_DB']->fullQuoteStr($this->sys_language_uid, 'tx_dam'),
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
		return true;
	}

	/**
	 * return all DAM categories including subcategories
	 *
	 * @return	array
	 */
	protected function getDamcats($dam_cat='')
	{
		$damCats = t3lib_div::trimExplode(",", $dam_cat, true);
		if (count($damCats) < 1) {
			return;
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
	public function setContentKey($contentKey=null)
	{
		$this->contentKey = ($contentKey == null ? $this->extKey : $contentKey);
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
	public function parseTemplate($data=array(), $dir='', $onlyJS=false)
	{
		// define the directory of images
		if ($dir == '') {
			$dir = $this->imageDir;
		}

		// Check if $data is array
		if (count($data) == 0 && $onlyJS === false) {
			return false;
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
			$options[] = "tint: '{$this->conf[$this->type.'.']['tint']}'";
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

		// overwrite all options if set
		if (trim($this->conf[$this->type.'.']['options'])) {
			$options = array($this->conf[$this->type.'.']['options']);
		}

		// define the js file
		$this->addJsFile($this->conf['jQueryCloudZoom']);

		// define the css file
		$this->addCssFile($this->conf['cssFile']);

		$this->addJS($jQueryNoConflict);

		// Add the ressources
		$this->addResources();

		$return_string = null;
		$images = null;
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

		if ($onlyJS === true) {
			return true;
		}

		if (count($data) > 0) {
			foreach ($data as $key => $item) {
				$image = null;
				$thumbnail = null;
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
	 * Include all defined resources (JS / CSS)
	 *
	 * @return void
	 */
	protected function addResources()
	{
		// checks if t3jquery is loaded
		if (T3JQUERY === true) {
			tx_t3jquery::addJqJS();
		} else {
			$this->addJsFile($this->conf['jQueryLibrary'], true);
		}
		if (t3lib_div::int_from_ver(TYPO3_version) >= 4003000) {
			$pagerender = $GLOBALS['TSFE']->getPageRenderer();
		}
		// Fix moveJsFromHeaderToFooter (add all scripts to the footer)
		if ($GLOBALS['TSFE']->config['config']['moveJsFromHeaderToFooter']) {
			$allJsInFooter = true;
		} else {
			$allJsInFooter = false;
		}
		// add all defined JS files
		if (count($this->jsFiles) > 0) {
			foreach ($this->jsFiles as $jsToLoad) {
				if (T3JQUERY === true) {
					$conf = array(
						'jsfile' => $jsToLoad,
						'tofooter' => ($this->conf['jsInFooter'] || $allJsInFooter),
						'jsminify' => $this->conf['jsMinify'],
					);
					tx_t3jquery::addJS('', $conf);
				} else {
					$file = $this->getPath($jsToLoad);
					if ($file) {
						if (t3lib_div::int_from_ver(TYPO3_version) >= 4003000) {
							if ($allJsInFooter) {
								$pagerender->addJsFooterFile($file, 'text/javascript', $this->conf['jsMinify']);
							} else {
								$pagerender->addJsFile($file, 'text/javascript', $this->conf['jsMinify']);
							}
						} else {
							$temp_file = '<script type="text/javascript" src="'.$file.'"></script>';
							if ($allJsInFooter) {
								$GLOBALS['TSFE']->additionalFooterData['jsFile_'.$this->extKey.'_'.$file] = $temp_file;
							} else {
								$GLOBALS['TSFE']->additionalHeaderData['jsFile_'.$this->extKey.'_'.$file] = $temp_file;
							}
						}
					} else {
						t3lib_div::devLog("'{$jsToLoad}' does not exists!", $this->extKey, 2);
					}
				}
			}
		}
		// add all defined JS script
		if (count($this->js) > 0) {
			foreach ($this->js as $jsToPut) {
				$temp_js .= $jsToPut;
			}
			$conf = array();
			$conf['jsdata'] = $temp_js;
			if (T3JQUERY === true && t3lib_div::int_from_ver($this->getExtensionVersion('t3jquery')) >= 1002000) {
				$conf['tofooter'] = ($this->conf['jsInFooter'] || $allJsInFooter);
				$conf['jsminify'] = $this->conf['jsMinify'];
				$conf['jsinline'] = $this->conf['jsInline'];
				tx_t3jquery::addJS('', $conf);
			} else {
				// Add script only once
				$hash = md5($temp_js);
				if ($this->conf['jsInline']) {
					$GLOBALS['TSFE']->inlineJS[$hash] = $temp_css;
				} elseif (t3lib_div::int_from_ver(TYPO3_version) >= 4003000) {
					if ($this->conf['jsInFooter'] || $allJsInFooter) {
						$pagerender->addJsFooterInlineCode($hash, $temp_js, $this->conf['jsMinify']);
					} else {
						$pagerender->addJsInlineCode($hash, $temp_js, $this->conf['jsMinify']);
					}
				} else {
					if ($this->conf['jsMinify']) {
						$temp_js = t3lib_div::minifyJavaScript($temp_js);
					}
					if ($this->conf['jsInFooter'] || $allJsInFooter) {
						$GLOBALS['TSFE']->additionalFooterData['js_'.$this->extKey.'_'.$hash] = t3lib_div::wrapJS($temp_js, true);
					} else {
						$GLOBALS['TSFE']->additionalHeaderData['js_'.$this->extKey.'_'.$hash] = t3lib_div::wrapJS($temp_js, true);
					}
				}
			}
		}
		// add all defined CSS files
		if (count($this->cssFiles) > 0) {
			foreach ($this->cssFiles as $cssToLoad) {
				// Add script only once
				$file = $this->getPath($cssToLoad);
				if ($file) {
					if (t3lib_div::int_from_ver(TYPO3_version) >= 4003000) {
						$pagerender->addCssFile($file, 'stylesheet', 'all', '', $this->conf['cssMinify']);
					} else {
						$GLOBALS['TSFE']->additionalHeaderData['cssFile_'.$this->extKey.'_'.$file] = '<link rel="stylesheet" type="text/css" href="'.$file.'" media="all" />'.chr(10);
					}
				} else {
					t3lib_div::devLog("'{$cssToLoad}' does not exists!", $this->extKey, 2);
				}
			}
		}
		// add all defined CSS Script
		if (count($this->css) > 0) {
			foreach ($this->css as $cssToPut) {
				$temp_css .= $cssToPut;
			}
			$hash = md5($temp_css);
			if (t3lib_div::int_from_ver(TYPO3_version) >= 4003000) {
				$pagerender->addCssInlineBlock($hash, $temp_css, $this->conf['cssMinify']);
			} else {
				// addCssInlineBlock
				$GLOBALS['TSFE']->additionalCSS['css_'.$this->extKey.'_'.$hash] .= $temp_css;
			}
		}
	}

	/**
	 * Return the webbased path
	 * 
	 * @param string $path
	 * return string
	 */
	protected function getPath($path="")
	{
		return $GLOBALS['TSFE']->tmpl->getFileName($path);
	}

	/**
	 * Add additional JS file
	 * 
	 * @param string $script
	 * @param boolean $first
	 * @return void
	 */
	protected function addJsFile($script="", $first=false)
	{
		$script = t3lib_div::fixWindowsFilePath($script);
		if ($this->getPath($script) && ! in_array($script, $this->jsFiles)) {
			if ($first === true) {
				$this->jsFiles = array_merge(array($script), $this->jsFiles);
			} else {
				$this->jsFiles[] = $script;
			}
		}
	}

	/**
	 * Add JS to header
	 * 
	 * @param string $script
	 * @return void
	 */
	protected function addJS($script="")
	{
		if (! in_array($script, $this->js)) {
			$this->js[] = $script;
		}
	}

	/**
	 * Add additional CSS file
	 * 
	 * @param string $script
	 * @return void
	 */
	protected function addCssFile($script="")
	{
		$script = t3lib_div::fixWindowsFilePath($script);
		if ($this->getPath($script) && ! in_array($script, $this->cssFiles)) {
			$this->cssFiles[] = $script;
		}
	}

	/**
	 * Add CSS to header
	 * 
	 * @param string $script
	 * @return void
	 */
	protected function addCSS($script="")
	{
		if (! in_array($script, $this->css)) {
			$this->css[] = $script;
		}
	}

	/**
	 * Returns the version of an extension (in 4.4 its possible to this with t3lib_extMgm::getExtensionVersion)
	 * @param string $key
	 * @return string
	 */
	protected function getExtensionVersion($key)
	{
		if (! t3lib_extMgm::isLoaded($key)) {
			return '';
		}
		$_EXTKEY = $key;
		include(t3lib_extMgm::extPath($key) . 'ext_emconf.php');
		return $EM_CONF[$key]['version'];
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jfcloudzoom/pi1/class.tx_jfcloudzoom_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jfcloudzoom/pi1/class.tx_jfcloudzoom_pi1.php']);
}

?>