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
 * created by Ivan Saranin <ivan@saranin.com>, on 05-Mar-2017, at 11:28:42
 */

namespace SA\Sys;

/*
 * Module for Arr class
 */

class Arr {
	static public function is_set($aArr, $aPath, $aDelimiter = '-') {
		$pathArr = explode($aDelimiter, $aPath);
		$curPath = array_shift($pathArr);
		
		if (!isset($aArr[$curPath])) {
			return false;
		}
		if (count($pathArr) > 0) {
			return self::is_set($aArr[$curPath], implode($aDelimiter, $pathArr), $aDelimiter);
		}
		return true;
	}
	
	static public function value_by_path($aArr, $aPath, $aDelimiter = '-') {
		$pathArr = explode($aDelimiter, $aPath);
		$curPath = array_shift($pathArr);
		
		if (!isset($aArr[$curPath])) {
			return false;
		}
		if (count($pathArr) > 0) {
			return self::value_by_path($aArr[$curPath], implode($aDelimiter, $pathArr), $aDelimiter);
		}
		return $aArr[$curPath];
	}
}
