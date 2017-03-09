<?

/*
 * Media file renamer
 * https://github.com/isaranin/media-file-renamer
 *  
 * Rename media files photos/video/mp3 by it tags
 *  
 * repository		git@github.com:isaranin/media-file-renamer.git
 *  
 * author		Ivan Saranin <ivan@saranin.com>
 * company		saranin.com
 * url			http://www.saranin.com
 * copyright		(c) 2017, saranin.com
 *  
 *  
 * created by Ivan Saranin <ivan@saranin.com>, on 04-Mar-2017, at 11:45:18
 */

namespace MFR\File;

/*
 * Module for NameGenerator class
 */

class NameGenerator {
	
	public $lastError = '';
	
	private $EXIF_TIME_FORMAT = 'Y:m:d H:i:s';
	
	private $STANDART_TIME_FORMAT = 'Y:m:d H:i:s';
	
	private $templateToFileData = [
		'exif-maker-note' => 'jpg-exif-EXIF-MakerNote',
		'exif-date' => 'jpg-exif-EXIF-DateTimeOriginal',
		'ifd0-make' => 'jpg-exif-IFD0-Make',
		'ifd0-model' => 'jpg-exif-IFD0-Model',
		'ifd0-date' => 'jpg-exif-IFD0-DateTime',
		'std-maker' => 'standart-maker',
		'std-date' => 'standart-datetime',
		'file-format' => 'fileformat'
	];
	
	protected $SUPPORT_FORMATS = [
		'jpg', 'mp4', 'avi'
	];
	
	public $replacement = [];
		
	protected function parseExifTime($aExifTime) {
		$date = date_parse_from_format($this->EXIF_TIME_FORMAT, $aExifTime);
		return $date;
	}
	
	protected function getTypedValue($value, $type, $path, $params) {
		$res = '';
		switch ($type) {
			case 'date':
				$date = \DateTime::createFromFormat($this->STANDART_TIME_FORMAT, $value);
				if ($date !== false && isset($params[0])) {
					$res = $date->format($params[0]);
				}
				break;
			case 'replace':
				if (!isset($this->replacement[$path][$value])) {
					$this->replacement[$path][$value] = $value;
				}
				$res = $this->replacement[$path][$value];
				break;
			default:
				$res = $value;
				break;
		}
		return $res;
	}
	
	protected function fillTemplateFields($aTemplate, $aData) {
		$res = preg_match_all('/%(.*?)%/', $aTemplate, $matches) !== false;
		if ($res) {
			$matches[1] = array_unique($matches[1]);
			foreach($matches[1] as $fieldnamefull) {
				$fieldParams = explode('|', $fieldnamefull);
				$fieldname = array_shift($fieldParams);
				if (!isset($this->templateToFileData[$fieldname])) {
					$this->lastError = sprintf('Field "%s" unknown',
							$fieldname);
					$res = false;
					break;
				}
				$path = $this->templateToFileData[$fieldname];
				$value = \SA\Sys\Arr::value_by_path($aData, $path);
				if ($value === false) {
					$this->lastError = sprintf('Field "%s" not found in data, path "%s"',
							$fieldname, $path);
					$res = false;
					break;
				}
				$value = trim($value);
				
				if (count($fieldParams) > 0) {
					$type = array_shift($fieldParams);
					$newValue = $this->getTypedValue($value, $type, $fieldname, $fieldParams);
					if (empty($newValue)) {
						$this->lastError = sprintf('Error data has wrong format %s for field %s type %s', $value, $fieldname, $matches[1]);
						return false;
					}
					$value = $newValue;
				}
				
				$aTemplate = str_replace('%'.$fieldnamefull.'%', $value, $aTemplate);
			}
		}
		
		return $aTemplate;
	}
	
	public function makeName($aTemplate, $aTags) {
		
		if (!isset($aTags['fileformat']) || !in_array($aTags['fileformat'], $this->SUPPORT_FORMATS)) {
			$this->lastError = sprintf('Format unsupported for file %s, file format %s', 
					$aTags['filenamepath'],
					$aTags['fileformat']
					);
			return false;
		}		
		
		$outFilename = $this->fillTemplateFields($aTemplate, $aTags);
		
		if (preg_match_all('/%(.*?)%/', $outFilename, $matches) > 0) {
			$this->lastError = sprintf('Can`t find fields for templates %s', 
					implode(', ', $matches[0]));
			return false;
		}
		
		return $outFilename;		
	}
}
