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
	
	private $templateToFileData = [
		'exif-maker-note' => 'jpg-exif-EXIF-MakerNote',
		'exif-date-YYYY' => 'jpg-exif-EXIF-DateTimeOriginal',
		'exif-date-mm' => 'jpg-exif-EXIF-DateTimeOriginal',
		'exif-date-dd' => 'jpg-exif-EXIF-DateTimeOriginal',
		'exif-date-H' => 'jpg-exif-EXIF-DateTimeOriginal',
		'exif-date-i' => 'jpg-exif-EXIF-DateTimeOriginal',
		'exif-date-s' => 'jpg-exif-EXIF-DateTimeOriginal',
		'ifd0-make' => 'jpg-exif-IFD0-Make',
		'ifd0-model' => 'jpg-exif-IFD0-Model',
		'ifd0-date-YYYY' => 'jpg-exif-IFD0-DateTime',
		'ifd0-date-mm' => 'jpg-exif-IFD0-DateTime',
		'ifd0-date-dd' => 'jpg-exif-IFD0-DateTime',
		'ifd0-date-H' => 'jpg-exif-IFD0-DateTime',
		'ifd0-date-i' => 'jpg-exif-IFD0-DateTime',
		'ifd0-date-s' => 'jpg-exif-IFD0-DateTime',
		'file-format' => 'fileformat'
	];
	
	protected function parseExifTime($aExifTime) {
		$date = date_parse_from_format($this->EXIF_TIME_FORMAT, $aExifTime);
		return $date;
	}
	
	protected function getDateValue($value, $path) {
		$res = '';
		$date = $this->parseExifTime($value);
		if ($date !== false && $date['year'] !== 0) {
			if (preg_match('/date-(.*)/', $path, $matches) !== false) {
				switch ($matches[1]) {
					case 'YYYY':
						$res = sprintf('%\'.02d', $date['year']);
						break;
					case 'mm':
						$res = sprintf('%\'.02d', $date['month']);
						break;
					case 'dd':
						$res = sprintf('%\'.02d', $date['day']);
						break;
					case 'H':
						$res = sprintf('%\'.02d', $date['hour']);
						break;
					case 'i':
						$res = sprintf('%\'.02d', $date['minute']);
						break;
					case 's':
						$res = sprintf('%\'.02d', $date['second']);
						break;
				}
					
			}			
		}
		return $res;
	}
	
	protected function fillTemplateFields($aTemplate, $aData) {
		$res = preg_match_all('/%(.*?)%/', $aTemplate, $matches) !== false;
		if ($res) {
			$matches[1] = array_unique($matches[1]);
			foreach($matches[1] as $fieldname) {
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
				
				if (strpos($fieldname, 'date') !== false) {
					$newValue = $this->getDateValue($value, $fieldname);
					if (empty($newValue)) {
						$this->lastError = sprintf('Error data has wrong format %s for field %s', $value, $fieldname);
						return false;
					}
					$value = $newValue;
				}
				
				$aTemplate = str_replace('%'.$fieldname.'%', $value, $aTemplate);
			}
		}
		
		return $aTemplate;
	}
	
	public function makeName($aTemplate, $aTags) {
		if (!isset($aTags['jpg'])) {
			$this->lastError = sprintf('No jpg data found in file %s, file format %s', 
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
