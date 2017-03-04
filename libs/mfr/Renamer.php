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
 * created by Ivan Saranin <ivan@saranin.com>, on 04-Mar-2017, at 23:07:29
 */

namespace MFR;

/*
 * Module for Renamer class
 */

class Renamer extends SA\Log\ClassWithErrorLog{
	
	protected $template;
	
	protected $inFolder;
	
	protected $outFolder;
	
	protected $getID3;
	
	protected $nameGenerator;
	
	protected $duplicateFolder = '';
	
	protected $wrongTemplateFolder = '';
	
	
	public function __construct($aDuplicateFolder, $aWrongTemplateFolder) {
		$this->getID3 = new \getID3();
		$this->nameGenerator = new File\NameGenerator();
		$this->duplicateFolder = $aDuplicateFolder;
		$this->wrongTemplateFolder = $aWrongTemplateFolder;
	}
	
	protected function moveFile($aSrc, $aDst) {
		mkdir(dirname($aDst));
		$res = rename($aSrc, $aDst);
		if (!$res) {
			$this->epput('Can`t move file from "%s" to "%s"', $aSrc, $aDst);
		}
		return $res;
	}
	
	protected function isFileSame($aSrc, $aDst) {
		return md5_file($aSrc) === md5_file($aDst);
	}
	
	protected function findNotExistIndex($aFilename) {
		$i = 1;
		$pathInfo = pathinfo($aFilename);
		do {
			$newFilename = sprintf('%s/%s-%d.%s',
					$pathInfo['dirname'], 
					$pathInfo['filename'], 
					$i,
					$pathInfo['extension']);
		} while (file_exists($newFilename));
		return $newFilename;
	}
	
	protected function renameFilesInFolder($aFolder) {
		$this->put('Scanning directory: %s', $aFolder);
		$files = scandir($this->inFolder.$aFolder);
		array_shift($files);
		array_shift($files);
		$this->put('Finded files: %d', count($files));
		foreach($files as $file) {
			$partFilename = $aFolder.'/'.$file;
			$fullFilename = $this->inFolder.$partFilename;
			if (is_dir($fullFilename)) {
				$this->put('File "%s" is directory', $fullFilename);
				$this->renameFilesInFolder($partFilename);
				continue;
			}
			
			$this->put('Analyzing file: %s', $fullFilename);
			$tags = $this->getID3->analyze($fullFilename);
			
			$outFileName = $this->nameGenerator->makeJpgName($this->template, $tags);
			
			if ($outFileName === false) {
				$this->epput('Wrong template: %s ', $this->nameGenerator->lastError);
				$this->moveFile($fullFilename, $this->wrongTemplateFolder.$partFilename);
				continue;
			}
			
			if (file_exists($outFileName)) {
				$this->put('Out file exist: %s', $outFileName);
				if ($this->isFileSame($fullFilename, $outFileName)) {
					$this->epput('Duplicate file: %s ', $outFileName);
					$this->moveFile($fullFilename, $this->duplicateFolder.$partFilename);
					continue;
				}
				$outFileName = $this->findNotExistIndex($outFileName);
				$this->put('Making new index: %s', $outFileName);
			}
			$this->put('Move file from "%s" to "%s"', $fullFilename, $outFileName);
			$this->moveFile($fullFilename, $outFileName);
		}
	}
	
	public function rename($aInFolder, $aOutFolder, $aTemplate) {
		$this->template = $aTemplate;
		$this->inFolder = $aInFolder;
		$this->outFolder = $aOutFolder;
		$this->put('Start rename files from "%s" to "%s", template "%s"', 
				$aInFolder, $aOutFolder, $aTemplate);
		return $this->renameFilesInFolder('/');
	}
}
