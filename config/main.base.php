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


// standart path 2014-01-01/12-01-01.jpg
$stdTemplate = '%std-date|date|Y-m-d%/%std-date|date|H-i-s%.%file-format%';
$stdTemplateMessages = '%std-date|date|Y-m%/%std-date|date|d_H-i-s%.%file-format%';
$_CONFIG->templates = [
	'standart' => '%std-maker|replace%/'.$stdTemplate,
	'standart-phone' => 'phone/'.$stdTemplateMessages,
	'standart-whatsapp' => 'whatsapp/'.$stdTemplateMessages,
	'standart-line' => 'line/'.$stdTemplateMessages,
	'standart-viber' => 'viber/'.$stdTemplateMessages,
	'standart-telegram' => 'telegram/'.$stdTemplateMessages,
];