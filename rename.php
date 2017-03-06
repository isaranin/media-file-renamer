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
 * created by Ivan Saranin <ivan@saranin.com>, on 05-Mar-2017, at 00:17:42
 */

/* 
 * Module rename
 */

require_once __DIR__.'/bootstrap.php';

if (php_sapi_name() !== 'cli') {
	echo 'unauthorized';
	exit(0);
}

if (count($argv) < 3) {
	echo 'First parameter should be input folder, second output folder, third template name';
	exit(0);
}

$inFolder = $argv[1];
$outFolder = $argv[2];
$template = $argv[3];

if (!isset($_CONFIG->templates[$template])) {
	echo sprintf('Can`t find template %s', $template);
	exit(0);
}
$template = $_CONFIG->templates[$template];

$renamer = new MFR\Renamer($_CONFIG->folders->duplicate, 
		$_CONFIG->folders->wrongTemplate, $_CONFIG->files->replacement);
$renamer->log =  new SA\Log\File($_CONFIG->folders->log.'/'.'status.log');
$renamer->errorLog = new SA\Log\File($_CONFIG->folders->log.'/'.'error.log');

$renamer->rename($inFolder, $outFolder, $template);