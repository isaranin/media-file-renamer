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
 * created by Ivan Saranin <ivan@saranin.com>, on 27-Feb-2017, at 14:56:30
 */

/* 
 * Module bootstrap
 */

require_once __DIR__.'/config/main.base.php';
require_once __DIR__.'/libs/autoload.php';

error_reporting($_CONFIG->main->dev?E_ALL:0 );

// устанавливаем временную зоу
date_default_timezone_set($_CONFIG->main->timeZone);

// включаем вывод ошибок на экран
ini_set('display_errors', ($_CONFIG->main->dev?'1':'0'));
