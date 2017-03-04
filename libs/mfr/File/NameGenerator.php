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

/*
 * Module for NameGenerator class
 */

class NameGenerator {
	
	public $lastError = '';
	
	private $EXIF_TIME_FORMAT = 'Y:m:d H:i:s';
	
	private $templateToFileData = [
		'exif-maker-note' => 'MakerNote',
		'exif-date-YYYY' => 'DateTimeOriginal',
		'exif-date-mm' => 'DateTimeOriginal',
		'exif-date-dd' => 'DateTimeOriginal',
		'exif-date-H' => 'DateTimeOriginal',
		'exif-date-i' => 'DateTimeOriginal',
		'exif-date-s' => 'DateTimeOriginal',
	];
	
	protected function parseExifTime($aExifTime) {
		$date = date_parse_from_format(self::$EXIF_TIME_FORMAT, $aExifTime);
		return $date;
	}
	
	protected function checkTemplateAndData($aTemplate, $aData) {
		$res = true;
		foreach(self::$templateToFileData as $template => $field) {
			if (preg_match(sprintf('/\%%s/i',$template), $aTemplate) === 1) {
				$res = isset($aData[$field]);
				if (!$res) {
					$this->lastError = sprintf('Field %s not found in data, but exist in template %s, %s',
							$field, $template, $aTemplate);
					break;
				}
			}
		}
		return $res;
	}
	
	protected function fillExifNames($aTemplate, $aExifData) {
		
		$originalTime = $this->parseExifTime(isset($aExifData['DateTimeOriginal'])?$aExifData['DateTimeOriginal']:'');
		if (!$originalTime || $originalTime['year'] === '0000') {
			$this->lastError = sprintf('Error exif data, has wrong format %s', $aExifData['DateTimeOriginal']);
			return false;
		}
		
		$replace = [
			'%exif-maker-note%' => $aExifData['MakerNote'],
			'%exif-date-YYYY%' => $originalTime['year'],
			'%exif-date-mm%' => $originalTime['month'],
			'%exif-date-dd%' => $originalTime['day'],
			'%exif-date-H%' => $originalTime['hour'],
			'%exif-date-i%' => $originalTime['minute'],
			'%exif-date-s%' => $originalTime['second'],
		];
				
		$res = strtr($aTemplate, $replace);
		return $res;
	}
	
	public function makeJpgName($aTemplate, $aTags) {
		if (!isset($aTags['jpg'])) {
			$this->lastError = sptrinf('No jpg data found in file %s, file format %s', 
					$aTags['filenamepath'],
					$aTags['fileformat']
					);
			return false;
		}
		if (isset($aTags['jpg']['exif']) && isset($aTags['jpg']['exif']['EXIF'])) {
			$outFilename = $this->fillExifNames($aTemplate, $aTags['exif']['EXIF']);
		}
		
		if (preg_match_all('/%(.*?)%/', $outFilename, $matches) > 0) {
			$this->lastError = sprintf('Can`t find fields for templates %s', 
					implode(', ', $matches[0]));
			return false;
		}
		
		return $outFilename;		
	}
}
