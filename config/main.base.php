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

$_CONFIG->files = (object) [
	'replacement' => $_CONFIG->folders->base.'/replacement.json'
];

$_CONFIG->templates = [
	'standart' => '%std-maker|replace%/%std-date|date|Y%-%std-date|date|m%-%std-date|date|d%/%std-date|date|H%-%std-date|date|i%-%std-date|date|s%.%file-format%',
];