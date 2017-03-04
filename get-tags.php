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
 * created by Ivan Saranin <ivan@saranin.com>, on 04-Mar-2017, at 22:12:03
 */

/* 
 * Module get-tags
 */

require_once __DIR__.'/bootstrap.php';

if (php_sapi_name() !== 'cli') {
	echo 'unauthorized';
	exit(0);
}

if (!isset($argv[1])) {
	echo 'first parameter should be filename';
	exit(0);
}

$fileName = $argv[1];


$getID3 = new getID3();
$tags = $getID3->analyze($fileName);

var_dump($tags);