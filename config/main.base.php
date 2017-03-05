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
 * created by Ivan Saranin <ivan@saranin.com>, on 27-Feb-2017, at 14:52:58
 */

/* 
 * Module main
 * 
 * config file
 */

$_CONFIG = new \stdClass();

$_CONFIG->main = (object)[
    'dev' => true,
	'timeZone' => 'Europe/Moscow'
];

$_CONFIG->folders = (object) [
	'base' => dirname(__DIR__)
];

$_CONFIG->folders->log = $_CONFIG->folders->base.'/.logs';
$_CONFIG->folders->wrongTemplate ='/.wrong-template/';
$_CONFIG->folders->duplicate ='/.duplicate/';

$_CONFIG->templates = [
	'standart-exif' => '%exif-maker-note%/%exif-date-YYYY%-%exif-date-mm%-%exif-date-dd%/%exif-date-YYYY%-%exif-date-mm%-%exif-date-dd%_%exif-date-H%-%exif-date-i%-%exif-date-s%.%file-format%',
	'standart-ifd0' => '%ifd0-make%_%ifd0-model%/%ifd0-date-YYYY%-%ifd0-date-mm%-%ifd0-date-dd%/%ifd0-date-YYYY%-%ifd0-date-mm%-%ifd0-date-dd%_%ifd0-date-H%-%ifd0-date-i%-%ifd0-date-s%.%file-format%'
];